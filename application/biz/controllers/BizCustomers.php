<?php
require_once (APPPATH . "controllers/Customers.php");

class BizCustomers extends Customers 
{
	protected $_scopeOfView = 'view_scope_owner';
	function __construct()
	{
		parent::__construct();
		$this->lang->load('quotes_contract');
		if( $this->Employee->has_module_action_permission(
			$this->module_id,
			'view_scope_location',
			$this->Employee->get_logged_in_employee_info()->person_id)
		)
		{
			$this->_scopeOfView = 'view_scope_location';
		} elseif( $this->Employee->has_module_action_permission(
			$this->module_id,
			'view_scope_all',
			$this->Employee->get_logged_in_employee_info()->person_id)
		) {
			$this->_scopeOfView = 'view_scope_all';
		}
                $this->load->helper('my_table_helper');
	}
	function index($offset=0)
	{
		$params = $this->session->userdata('customers_search_data') ? $this->session->userdata('customers_search_data') : array('offset' => 0, 'order_col' => 'last_name', 'order_dir' => 'asc', 'search' => FALSE);
		if ($offset!=$params['offset'])
		{
		   redirect('customers/index/'.$params['offset']);
		}
		$this->check_action_permission('search');
		$config['base_url'] = site_url('customers/sorting');
		$config['per_page'] = $this->config->item('number_of_items_per_page') ? (int)$this->config->item('number_of_items_per_page') : 20; 
		
		$data['controller_name']=$this->_controller_name;
		$data['per_page'] = $config['per_page'];
		$data['search'] = $params['search'] ? $params['search'] : "";
		if ($data['search'])
		{
			$config['total_rows'] = $this->Customer->search_count_all($data['search']);
			$table_data = $this->Customer->search($data['search'],$data['per_page'],$params['offset'],$params['order_col'],$params['order_dir']);
		}
		else
		{
			$extra['scope_of_view'] = $this->_scopeOfView;
			$config['total_rows'] = $this->Customer->count_all($extra);
			$table_data = $this->Customer->get_all($data['per_page'],$params['offset'],$params['order_col'],$params['order_dir'], $extra);
		}
		$this->load->library('pagination');$this->pagination->initialize($config);
		$data['pagination'] = $this->pagination->create_links();
		$data['order_col'] = $params['order_col'];
		$data['order_dir'] = $params['order_dir'];
		
		$data['manage_table']=get_people_manage_table($table_data,$this);
		$data['total_rows'] = $config['total_rows'];

		$tiers = array();
		$tiers_result = $this->Tier->get_all()->result_array();
		if (count($tiers_result) > 0)
		{
			$tiers[0] = lang('common_none');
			foreach($tiers_result as $tier)
			{
				$tiers[$tier['id']]=$tier['name'];
			}
			$tiers['all'] = 'Tất cả';
		}

		$data['tiers']=$tiers;
		$data['selected_tier']='all';

		$employees = $this->Employee->getEmployeesByCurrentLocation();
		$data['selected_employee']='all';
		$employeesDropbox = array();
		foreach ($employees as $employee) {
			$employeesDropbox["".$employee['person_id']] = $employee['username'];
		}
		$employeesDropbox['all'] = 'Tất cả';
		$data['employees'] = $employeesDropbox;
		$data['type'] = 'customer';

		$this->load->view('people/manage',$data);
	}

	/*
	Returns customer table data rows. This will be called with AJAX.
	*/
	function search()
	{
		$this->check_action_permission('search');
		$searchText=$this->input->post('search');
		$tierId=$this->input->post('tier_id', 'all');
		$employeeId=$this->input->post('created_by', 'all');

		$offset = $this->input->post('offset') ? $this->input->post('offset') : 0;
		$order_col = $this->input->post('order_col') ? $this->input->post('order_col') : 'last_name';
		$order_dir = $this->input->post('order_dir') ? $this->input->post('order_dir'): 'asc';

		$customers_search_data = array('offset' => $offset, 'order_col' => $order_col, 'order_dir' => $order_dir, 'search' => $searchText);
		$this->session->set_userdata("customers_search_data",$customers_search_data);
		$per_page=$this->config->item('number_of_items_per_page') ? (int)$this->config->item('number_of_items_per_page') : 20;
		
		$searchParams['search_text'] = $searchText;
		$searchParams['tier_id'] = $tierId;
		$searchParams['employee_id'] = $employeeId;
		$searchParams['scope_of_view'] = $this->_scopeOfView;
		
		$search_data=$this->Customer->search($searchParams,$per_page,$offset, $order_col ,$order_dir);
		$config['base_url'] = site_url('customers/search');
		$config['total_rows'] = $this->Customer->search_count_all($searchParams);
		$config['per_page'] = $per_page ;
		
		$this->load->library('pagination');$this->pagination->initialize($config);				
		$data['pagination'] = $this->pagination->create_links();
		$data['total_rows'] = $this->Customer->search_count_all($searchText);
		$data['manage_table']=get_people_manage_table_data_rows($search_data,$this);
		echo json_encode(array('manage_table' => $data['manage_table'], 'pagination' => $data['pagination']));
	}

	/*
	Inserts/updates a customer
	*/
	function save($customer_id=-1)
	{
		$this->check_action_permission('add_update');
		$person_data = array(
			'first_name'=>$this->input->post('first_name'),
			'last_name'=>$this->input->post('last_name'),
			'email'=>$this->input->post('email'),
			'phone_number'=>$this->input->post('phone_number'),
			'address_1'=>$this->input->post('address_1'),
			'address_2'=>$this->input->post('address_2'),
			'city'=>$this->input->post('city'),
			'state'=>$this->input->post('state'),
			'zip'=>$this->input->post('zip'),
			'country'=>$this->input->post('country'),
			'comments'=>$this->input->post('comments'),
			'birth_date' => date('Y-m-d', strtotime($this->input->post('birth_date'))),
		);
		
		
		$customer_data=array(
			'company_name' => $this->input->post('company_name'),
			'tier_id' => $this->input->post('tier_id') ? $this->input->post('tier_id') : NULL,
			'account_number'=>$this->input->post('account_number')=='' ? null:$this->input->post('account_number'),
			'taxable'=>$this->input->post('taxable')=='' ? 0:1,
			'tax_certificate' => $this->input->post('tax_certificate'),
			'override_default_tax'=> $this->input->post('override_default_tax') ? $this->input->post('override_default_tax') : 0,
			
			'type_customer'=> $this->input->post('customer_type') ? $this->input->post('customer_type') : 0,
			'position'=> $this->input->post('position'),
			'sex'=> $this->input->post('sex') ? $this->input->post('sex') : 1,
			'family_info'=> $this->input->post('family_info'),
			'company_birth_date' => date('Y-m-d', strtotime($this->input->post('company_birth_date'))),
			'company_manage_name' => $this->input->post('company_manage_name'),
			'code_tax' => $this->input->post('code_tax'),
		);

		if($customer_id == -1)
		{
			$customer_data['created_by'] = $this->Employee->get_logged_in_employee_info()->person_id;
		}
		
		
		if ($this->config->item('enable_customer_loyalty_system') && $this->config->item('loyalty_option') == 'advanced' &&  count(explode(":",$this->config->item('spend_to_point_ratio'),2)) == 2)
		{
      	list($spend_amount_for_points, $points_to_earn) = explode(":",$this->config->item('spend_to_point_ratio'),2);
			$customer_data['current_spend_for_points'] = $spend_amount_for_points - $this->input->post('amount_to_spend_for_next_point');
		}
		elseif ($this->config->item('enable_customer_loyalty_system') && $this->config->item('loyalty_option') == 'simple')
		{
			$number_of_sales_for_discount = $this->config->item('number_of_sales_for_discount'); 
			$customer_data['current_sales_for_discount'] = $number_of_sales_for_discount - (float)$this->input->post('sales_until_discount');			
		}
		
		if ($this->input->post('balance')!== NULL && is_numeric($this->input->post('balance')))
		{
			$customer_data['balance'] = $this->input->post('balance');
		}

		if ($this->input->post('credit_limit')!== NULL && is_numeric($this->input->post('credit_limit')))
		{
			$customer_data['credit_limit'] = $this->input->post('credit_limit');
		}
		elseif($this->input->post('credit_limit') === '')
		{
			$customer_data['credit_limit'] = NULL;
		}
		
		if ($this->input->post('points')!== NULL && is_numeric($this->input->post('points')))
		{
			$customer_data['points'] = $this->input->post('points');
		}
		
		$redirect_code=$this->input->post('redirect_code');
		if ($this->input->post('delete_cc_info'))
		{
			$customer_data['cc_token'] = NULL;
			$customer_data['cc_preview'] = NULL;
			$customer_data['card_issuer'] = NULL;			
		}
		
		if($this->Customer->save_customer($person_data,$customer_data,$customer_id))
		{
			if ($this->Location->get_info_for_key('mailchimp_api_key'))
			{
				$this->Person->update_mailchimp_subscriptions($this->input->post('email'), $this->input->post('first_name'), $this->input->post('last_name'), $this->input->post('mailing_lists'));
			}
	

			$success_message = '';
			
			//New customer
			if($customer_id==-1)
			{
				$success_message = lang('customers_successful_adding').' '.$person_data['first_name'].' '.$person_data['last_name'];
				echo json_encode(array('success'=>true,'message'=> $success_message,'person_id'=>$customer_data['person_id'],'redirect_code'=>$redirect_code));
				$customer_id = $customer_data['person_id'];
				
			}
			else //previous customer
			{
				$success_message = lang('customers_successful_updating').' '.$person_data['first_name'].' '.$person_data['last_name'];
				$this->session->set_flashdata('manage_success_message', $success_message);
				echo json_encode(array('success'=>true,'message'=>$success_message,'person_id'=>$customer_id,'redirect_code'=>$redirect_code));
			}
			
			$customers_taxes_data = array();
			$tax_names = $this->input->post('tax_names');
			$tax_percents = $this->input->post('tax_percents');
			$tax_cumulatives = $this->input->post('tax_cumulatives');
			for($k=0;$k<count($tax_percents);$k++)
			{
				if (is_numeric($tax_percents[$k]))
				{
					$customers_taxes_data[] = array('name'=>$tax_names[$k], 'percent'=>$tax_percents[$k], 'cumulative' => isset($tax_cumulatives[$k]) ? $tax_cumulatives[$k] : '0' );
				}
			}
			$this->load->model('Customer_taxes');
			$this->Customer_taxes->save($customers_taxes_data, $customer_id);
			
						
				//Delete Image
				if($this->input->post('del_image') && $customer_id != -1)
				{
					$customer_info = $this->Customer->get_info($customer_id);
				    if($customer_info->image_id != null)
				    {
						$this->Person->update_image(NULL,$customer_id);
						$this->load->model('Appfile');
						$this->Appfile->delete($customer_info->image_id);
				    }
				}

				//Save Image File
				if(!empty($_FILES["image_id"]) && $_FILES["image_id"]["error"] == UPLOAD_ERR_OK)
				{			    

				    $allowed_extensions = array('png', 'jpg', 'jpeg', 'gif');
					$extension = strtolower(pathinfo($_FILES["image_id"]["name"], PATHINFO_EXTENSION));
				    if (in_array($extension, $allowed_extensions))
				    {
					    $config['image_library'] = 'gd2';
					    $config['source_image']	= $_FILES["image_id"]["tmp_name"];
					    $config['create_thumb'] = FALSE;
					    $config['maintain_ratio'] = TRUE;
					    $config['width']	 = 400;
					    $config['height']	= 300;
					    $this->load->library('image_lib', $config); 
					    $this->image_lib->resize();
						 $this->load->model('Appfile');
					    $image_file_id = $this->Appfile->save($_FILES["image_id"]["name"], file_get_contents($_FILES["image_id"]["tmp_name"]));
				    }
					
					if($customer_id==-1)
					{
		    			$this->Person->update_image($image_file_id,$customer_data['person_id']);
					}
					else
					{
						$this->Person->update_image($image_file_id,$customer_id);
	    			
					}
				}
		}
		else//failure
		{	
			echo json_encode(array('success'=>false,'message'=>lang('customers_error_adding_updating').' '.
			$person_data['first_name'].' '.$person_data['last_name'],'person_id'=>-1));
		}
	}
	
	/*
	 get the width for the add/edit form
	 */
	
	function get_form_width() {
		return 750;
	}
	
	/**
	 * SMS Brandname
	 */
	function manage_sms() {
		$config['total_rows'] = $this->Customer->count_all_sms();
		
		$config['per_page'] = $this->config->item('number_of_items_per_page') ? (int) $this->config->item('number_of_items_per_page') : 20;
		$config['base_url'] = site_url('customers/sorting_sms');
		$this->pagination->initialize($config);
		$data['pagination'] = $this->pagination->create_links();
		$data['controller_name'] = $this->_controller_name;
		$data['form_width'] = $this->get_form_width();
		$data['per_page'] = $config['per_page'];
		$data['total_rows'] = $config['total_rows'];
		$data['manage_table'] = get_sms_manage_table($this->Customer->get_all_sms($data['per_page']), $this);
		
		$this->load->view("customers/manage_sms", $data);
	}
	
	function sorting_sms() {
		$per_page = $this->config->item('number_of_items_per_page') ? (int) $this->config->item('number_of_items_per_page') : 20;
		$config['total_rows'] = $this->Customer->count_all_sms();
		$table_data = $this->Customer->get_all_sms($per_page, $this->input->post('offset') ? $this->input->post('offset') : 0, $this->input->post('order_col') ? $this->input->post('order_col') : 'id', $this->input->post('order_dir') ? $this->input->post('order_dir') : 'DESC');
	
		$config['base_url'] = site_url('customers/sorting_sms');
		$config['per_page'] = $per_page;
		$this->pagination->initialize($config);
		$data['pagination'] = $this->pagination->create_links();
		$data['manage_table'] = get_sms_manage_table_data_rows($table_data, $this);
		$data['total_rows'] = $config['total_rows'];
		echo json_encode(array('manage_table' => $data['manage_table'], 'pagination' => $data['pagination']));
	}
	
	function search_sms() {
		$this->check_action_permission('search');
		$search = $this->input->post('search');
		$per_page = $this->config->item('number_of_items_per_page') ? (int) $this->config->item('number_of_items_per_page') : 20;
		$search_data = $this->Customer->search_sms($search, $per_page, $this->input->post('offset') ? $this->input->post('offset') : 0, $this->input->post('order_col') ? $this->input->post('order_col') : 'id', $this->input->post('order_dir') ? $this->input->post('order_dir') : 'desc');
		$config['base_url'] = site_url("customers/search_sms");
		$config['total_rows'] = $this->Customer->search_count_sms($search);
		$config['per_page'] = $per_page;
		$this->pagination->initialize($config);
		$data['pagination'] = $this->pagination->create_links();
		$data['manage_table'] = get_sms_manage_table_data_rows($search_data, $this);
		$data['total_rows'] = $config['total_rows'];
		echo json_encode(array('manage_table' => $data['manage_table'], 'pagination' => $data['pagination']));
	}
	
	function view_sms($id = -1,$redirect=0) {
		$data['info_sms'] = $this->Customer->get_info_sms($id);
		$data['redirect']= $redirect;
		$this->load->view("customers/form_sms", $data);
	}
	
	function save_sms($id = -1) {
		$sms_data = array(
				'title' => $this->input->post('sms_title'),
				'message' => $this->input->post('sms_message'),
				'number_char' => $this->input->post('sms_num_char'),
				'number_message' => $this->input->post('sms_num_mess'),
		);
		$redirect=$this->input->post('redirect');
		
		if ($this->Customer->save_sms($sms_data, $id)) {
			if ($id == -1) {
				echo (json_encode(array('success' => true, 'message' => lang('customers_sms_msg_new'). ' (' . $sms_data['title'] . ')'. lang('customers_sms_msg_success') .' !', 'id' => $sms_data['id'],'redirect'=> $redirect)));	
			} else { //previous customer
				echo (json_encode(array('success' => true, 'message' => lang('customers_sms_msg_update') . ' (' . $sms_data['title'] . ') '. lang('customers_sms_msg_success') .' !', 'id' => $sms_data['id'],'redirect'=> $redirect)));
			}
		} else {//failure
			echo json_encode(array('success' => false, 'message' => lang('customers_sms_msg_error'), 'id' => -1));
		}
	}
	
	function delete_sms() {
		$sms_to_delete = $this->input->post('ids');
		if ($this->Customer->delete_sms_list($sms_to_delete)) {
			echo json_encode(array('success' => true, 'message' => lang('customers_sms_delete_msg_frs').' ' . count($sms_to_delete) . ' ' . lang('customers_sms_delete_msg_ed')));
		} else {
			echo json_encode(array('success' => false, 'message' => lang('customers_sms_delete_error')));
		}
	}
	
	function suggest_sms() {
		$suggestions = $this->Customer->get_search_suggestions_sms($this->input->get('term'), 100);
		echo json_encode($suggestions);
	}
	function clear_state_sms()
	{
		redirect('customers/manage_sms');
	}
	
	function send_sms() {
		$sms_to_send = $this->input->post('ids');
		$data['list_sms'] = $this->Customer->get_all_sms();
		$this->load->view("customers/send_sms", $data);
	}
	
	function get_number_sms(){
		$max_id_sms = $this->Customer->get_table_number_sms();
		$sms = $this->Customer->get_info_id_max_of_table_number_sms($max_id_sms['id']);
		echo json_encode(array("quantity_sms" => $sms['quantity_sms']));
	}
	
	function do_send_sms()
	{
        $check = $this->input->get("type_send");
		$customer_ids = $this->input->post('customer_ids');
		$sms_id = $this->input->post('sms_id');
		$info_sms = $this->Customer->get_info_sms($sms_id);
		$message = $info_sms->message;
		
		$max_id_table_number_sms = $this->Customer->get_table_number_sms();
		$info_max_id = $this->Customer->get_info_id_max_of_table_number_sms($max_id_table_number_sms['id']);
		if($info_max_id['quantity_sms'] > 0){
                    if($check >0){
                        if(isset($_SESSION['sms_tmp'])&&$_SESSION['sms_tmp'] !=NULL){
                            $this->update_info_list_tmp($_SESSION['sms_tmp']);
                            foreach ($_SESSION['sms_tmp'] as $person_data) {
                                $id_cus = $person_data['person_id'];
                                $number_sms++;
                                $new_keyword = rand(100000, 999999);
                                 $new_message = preg_replace('/\[[a-zA-Z]{2,6}\]/', $new_keyword, $message);
                                $info_cus = $this->Customer->get_info_person_by_id($id_cus);
                                //check numberfone
                                if(!isset($info_cus['phone_number'])||$info_cus['phone_number']=='')continue;
                                $mobile = '84' . substr($info_cus['phone_number'], 1, strlen($info_cus['phone_number']));

                                $getdata = http_build_query(array(
                                                'username' => $this->config->item('config_sms_user'),
                                                'password' => $this->config->item('config_sms_pass'),
                                                'source_addr' => $this->config->item('config_sms_brand_name'),
                                                'dest_addr' => $mobile,
                                                'message' => $new_message,
                                ));
                                $opts = array(
                                                'http' => array(
                                                                'method' => 'GET',
                                                                'content' => $getdata
                                                )
                                );
                                $context = stream_context_create($opts);
                                $result = file_get_contents('http://sms.vnet.vn:8082/api/sent?' . $getdata, false, $context);
                                sleep(1);
                                if ($result) {
                                        $data_insert = array(
                                            'id_cus' => $id_cus,
                                            'mobile' => $mobile,
                                            'content_message' => $new_message,
                                            'equals' => $result,
                                            'date_send' => date('Y-m-d H:i:s'),
                                        );
                                        $this->Customer->save_message($data_insert);
                                        if($result > 0){
                                                echo json_encode(array("success" => true, "message" => 'Đã gửi thành công khác hàng'));
                                                $this->delete_customer_from_list_sms($id_cus);
                                                $data_update_table_number_sms = array(
                                                                'quantity_sms' => ($info_max_id['quantity_sms'] - $info_sms->number_message),
                                                );
                                                $this->Customer->update_number_sms($max_id_table_number_sms['id'],$data_update_table_number_sms);
                                                $this->Customer->update_month_sms($month_data);
                                        }
                                        $is_success = true;
                                }
                                else echo 'error message';
                            }
                        }
                    }else{
			foreach ($customer_ids as $id_cus) {
				$info_cus = $this->Customer->get_info($id_cus);
				$mobile = '84' . substr($info_cus->phone_number, 1, strlen($info_cus->phone_number));
				$getdata = http_build_query(array(
						'username' => $this->config->item('config_sms_user'),
						'password' => $this->config->item('config_sms_pass'),
						'source_addr' => $this->config->item('config_sms_brand_name'),
						'dest_addr' => $mobile,
						'message' => $message,
				));
				$opts = array(
						'http' => array(
								'method' => 'GET',
								'content' => $getdata
						)
				);
				$context = stream_context_create($opts);
				$result = file_get_contents('http://sms.vnet.vn:8082/api/sent?' . $getdata, false, $context);
				if ($result) {
					$data_insert = array(
							'id_cus' => $id_cus,
							'mobile' => $mobile,
							'content_message' => $message,
							'equals' => $result,
							'date_send' => date('Y-m-d H:i:s'),
					);
					$this->Customer->save_message($data_insert);
					if($result > 0){
						$data_update_table_number_sms = array(
								'quantity_sms' => ($info_max_id['quantity_sms'] - $info_sms->number_message),
						);
						$this->Customer->update_number_sms($max_id_table_number_sms['id'],$data_update_table_number_sms);
					}
					echo json_encode(array("success" => true, "message" => lang('customers_sms_send_sms_success')));
				} else {
					echo json_encode(array("success" => false, "message" => lang('customers_sms_send_sms_unsuccess')));
				}
			}
                    }
		}else{
			echo json_encode(array("success" => false, "message" => lang('customers_sms_send_sms_not_enough')));
		}
		
	}
        
        function manage_sms_tmp(){
                $config['total_rows'] = count($_SESSION['sms_tmp']);
		$config['per_page'] = $this->config->item('number_of_items_per_page') ? (int) $this->config->item('number_of_items_per_page') : 20;
		$config['base_url'] = site_url('customers/sorting_sms');
		$this->pagination->initialize($config);
		$data['pagination'] = $this->pagination->create_links();
		$data['controller_name'] = $this->_controller_name;
		$data['form_width'] = $this->get_form_width();
		$data['per_page'] = $config['per_page'];
                $data['manage_table'] = get_customer_manage_table($_SESSION['sms_tmp'], $this);
		$this->load->view("customers/manage_sms_tmp", $data);
        }
        
        function delete_sms_tmp_all(){
                unset($_SESSION['sms_tmp']);
                echo json_encode(array('success' => true, 'message' => ' Đã xóa! SMS!'));
        }
        
        function delete_sms_tmp_id(){
            $sms_to_delete = $this->input->post('ids');
            if(in_array($sms_to_delete, $_SESSION['sms_tmp'][$sms_to_delete])){
                unset($_SESSION['sms_tmp'][$sms_to_delete]);
                echo json_encode(array('success' => true, 'message' => ' Xóa khỏi danh sách tạm thành công!'));
            }else{
                echo json_encode(array('success' => false, 'message' => 'Lỗi! Không xóa được, vui lòng thử lại!'));
            }
        }
        
        function send_sms_list(){
            $data['list_sms'] = $this->Customer->get_all_sms();
            $this->load->view("customers/send_sms_list",$data);
        }
        
        function save_list_send_sms($item_ids="") {
		$item_ids = explode('~', $item_ids);
//                var_dump($item_ids);die;
		foreach ($item_ids as $item) {
			$info_cus = $this->Customer->get_info_person_by_id($item);
			if (isset($_SESSION['sms_tmp'][$item])) {
                            echo '1';
				continue;
			} else {
                            echo '2';
				$_SESSION['sms_tmp'][$info_cus['person_id']] = array(
						'person_id' => $item,
						'name' => $info_cus['first_name'] . " " . $info_cus['last_name'],
						'phone_number' => $info_cus['phone_number'],
				);
			}
		}
		redirect('customers');
	}
        
        function update_info_list_tmp(&$list_customer=array()){
            $info = '';
            if(isset($list_customer)&&count($list_customer)>0){
                foreach ($list_customer as $person_id => $person_info){
                    $info = $this->Customer->get_info_person_by_id($person_id);
                    if($info['first_name'] . " " . $info['last_name'] != $person_info['name'])
                            $list_customer[$person_id]['name'] = $info_cus['first_name'] . " " . $info_cus['last_name'];
                    
                    if($info['phone_number']!=$person_info['phone_number'])
                         $list_customer[$person_id]['phone_number'] = $info['phone_number'];
                }
            }
        }
        
        function delete_customer_from_list_sms($id){
            if($id>0){
                unset($_SESSION['sms_tmp'][$id]);
                return $id;
            }
            return 0;
        }
	
	function quotes_contract()
	{
		$config['base_url'] = site_url('customers/quotes_contract_sorting');
		$config['total_rows'] = $this->Customer->count_all_quotes_contract();
		$config['per_page'] = $this->config->item('number_of_items_per_page') ? (int) $this->config->item('number_of_items_per_page') : 20;
		$this->pagination->initialize($config);
		$data['pagination'] = $this->pagination->create_links();
		$data['controller_name'] = $this->_controller_name;
		$data['per_page'] = $config['per_page'];
		$data['total_rows'] = $config['total_rows'];
		
		$data['manage_table'] = get_quotes_contract_manage_table($this->Customer->get_all_quotes_contract($data['per_page']), $this);
		$this->load->view('customers/manage_quotes_contract', $data);
	}
	
	function quotes_contract_sorting()
	{
		$this->check_action_permission('search');
		$search = $this->input->post('search');
		$cat = $this->input->post('cat');
		$per_page = $this->config->item('number_of_items_per_page') ? (int) $this->config->item('number_of_items_per_page') : 20;
		if ($search || $cat) {
			$config['total_rows'] = $this->Customer->search_count_all_quotes_contract($search, $cat);
			$table_data = $this->Customer->search_quotes_contract($search, $cat, $per_page, $this->input->post('offset') ? $this->input->post('offset') : 0, $this->input->post('order_col') ? $this->input->post('order_col') : 'id_quotes_contract', $this->input->post('order_dir') ? $this->input->post('order_dir') : 'desc');
		} else {
			$config['total_rows'] = $this->Customer->count_all_quotes_contract();
			$table_data = $this->Customer->get_all_quotes_contract($per_page, $this->input->post('offset') ? $this->input->post('offset') : 0, $this->input->post('order_col') ? $this->input->post('order_col') : 'id_quotes_contract', $this->input->post('order_dir') ? $this->input->post('order_dir') : 'desc');
		}
		$config['base_url'] = site_url('customers/quotes_contract_sorting');
		$config['per_page'] = $per_page;
		$this->pagination->initialize($config);
		$data['pagination'] = $this->pagination->create_links();
		$data['manage_table'] = get_quotes_contract_manage_table_data_rows($table_data, $this);
		$data['total_rows'] = $config['total_rows'];
		echo json_encode(array('manage_table' => $data['manage_table'], 'pagination' => $data['pagination']));
	}
	
	function quotes_contract_delete() {
		$this->check_action_permission('delete');
		$id = $this->input->post("ids");
		if ($this->Customer->delete_list_quotes_contract($id)) {
			echo json_encode(array('success' => true, 'message' => lang('common_delete_success'). ' ' . count($id) . '' . lang('customers_quotes_contract_menu_link') ));
		} else {
			echo json_encode(array('success' => false, 'message' => lang('common_error')));
		}
	}
	
	function clear_state_quotes_contract()
	{
		redirect('customers/quotes_contract');
	}
	
	function quotes_contract_search() {
		$this->check_action_permission('search');
		$search = $this->input->post('search');
		$cat = $this->input->post('cat');
		$per_page = $this->config->item('number_of_items_per_page') ? (int) $this->config->item('number_of_items_per_page') : 20;
		$search_data = $this->Customer->search_quotes_contract($search, $cat, $per_page, $this->input->post('offset') ? $this->input->post('offset') : 0, $this->input->post('order_col') ? $this->input->post('order_col') : 'id_quotes_contract', $this->input->post('order_dir') ? $this->input->post('order_dir') : 'desc');
		$config['base_url'] = site_url('quotes_contract/search');
		$config['total_rows'] = $this->Customer->search_count_all_quotes_contract($search, $cat);
		$config['per_page'] = $per_page;
		$this->pagination->initialize($config);
		$data['pagination'] = $this->pagination->create_links();
		$data['total_rows'] = $config['total_rows'];
		$data['manage_table'] = get_quotes_contract_manage_table_data_rows($search_data, $this);
	
		echo json_encode(array('manage_table' => $data['manage_table'], 'pagination' => $data['pagination']));
	}
	
	function quotes_contract_view($id = -1) {
		$this->check_action_permission('add_update');
		$data = array();
		$data['info_quotes_contract'] = $this->Customer->get_info_quotes_contract($id);
		$this->load->view("customers/form_quotes_contract", $data);
	}
	
	function quotes_contract_save($id = -1) {
		$title = $this->input->post("title_quotes_contract");
		$cat = $this->input->post("cat_quotes_contract");
		$content = $this->input->post("content_quotes_contract");
		$data = array(
				"title_quotes_contract" => $title,
				"content_quotes_contract" => $content,
				"cat_quotes_contract" => $cat
		);
		
		if ($this->Customer->save_quotes_contract($data, $id)) {
			if ($id == -1) {
				echo json_encode(array('success' => true, 'message' => lang('common_add_success'), 'id' => $title));
			} else { //previous item
				echo json_encode(array('success' => true, 'message' => lang('common_update_success'), 'id' => $title));
			}
		} else {//failure
			echo json_encode(array('success' => false, 'message' => lang('common_error')));
		}
	}
	
	function quotes_contract_suggest() {
		$suggestions = $this->Customer->get_search_suggestions_quotes_contract($this->input->get('term'), 100);
		echo json_encode($suggestions);
	}
	
	/*
	 Loads the customer edit form
	 */
	function view($customer_id=-1,$redirect_code=0)
	{
		$this->check_action_permission('add_update');
		$this->load->model('Tier');
		$tiers = array();
		$tiers_result = $this->Tier->get_all()->result_array();
	
		if (count($tiers_result) > 0)
		{
			$tiers[0] = lang('common_none');
			foreach($tiers_result as $tier)
			{
				$tiers[$tier['id']]=$tier['name'];
			}
		}
	
		$data['controller_name']=$this->_controller_name;
		$data['tiers']=$tiers;
		$data['person_info']=$this->Customer->get_info($customer_id);
		$this->load->model('Customer_taxes');
		$data['customer_tax_info']=$this->Customer_taxes->get_info($customer_id);
		
		$customer_typers = array();
		$customer_typers_result = $data['type_customers'] = $this->Customer->get_Customer_type();
		
		if (count($customer_typers_result) > 0)
		{
			$customer_typers[0] = lang('common_none');
			foreach($customer_typers_result as $type)
			{
				$customer_typers[$type['customer_type_id']] = $type['name'];
			}
		}
		$data['type_customers'] = $customer_typers;
		$data['sex'] = array('1'=>'Nam', '2'=>'Nữ');
		
		$data['redirect_code']=$redirect_code;
		$this->load->view("customers/form",$data);
	}
	
	function manage_mail() {
	
		$config['total_rows'] = $this->Customer->count_all_mail();
		$config['per_page'] = $this->config->item('number_of_items_per_page') ? (int) $this->config->item('number_of_items_per_page') : 20;
		$config['base_url'] = site_url('customers/sorting_mail');
		$this->pagination->initialize($config);
		$data['pagination'] = $this->pagination->create_links();
		$data['controller_name'] = $this->_controller_name;
		$data['per_page'] = $config['per_page'];
		$data['total_rows'] = $config['total_rows'];
		
		$data['manage_table'] = get_mail_manage_table($this->Customer->get_all_mail($data['per_page']), $this);
		$this->load->view("customers/manage_mail", $data);
	}
	
	function sorting_mail() {
		$per_page = $this->config->item('number_of_items_per_page') ? (int) $this->config->item('number_of_items_per_page') : 20;
	
		$config['total_rows'] = $this->Customer->count_all_mail();
		$table_data = $this->Customer->get_all_mail($per_page, $this->input->post('offset') ? $this->input->post('offset') : 0, $this->input->post('order_col') ? $this->input->post('order_col') : 'mail_title', $this->input->post('order_dir') ? $this->input->post('order_dir') : 'asc');
	
		$config['base_url'] = site_url('customers/sorting_mail');
		$config['per_page'] = $per_page;
		$this->pagination->initialize($config);
		$data['pagination'] = $this->pagination->create_links();
		$data['manage_table'] = get_mail_manage_table_data_rows($table_data, $this);
		echo json_encode(array('manage_table' => $data['manage_table'], 'pagination' => $data['pagination']));
	}
	
	function create_mail() {
		$this->load->helper('ckeditor');
		$this->load->view("customers/create_mail", $data);
	}
	
	/**
	 * Function edit/add mail template
	 */
	function view_mail($mail_id = -1) {
		$config['global_xss_filtering'] = FALSE;
		$this->form_validation->set_rules('inhoud', 'inhoud', 'xss|clean');
		$data['mail_info'] = $this->Customer->get_info_mail($mail_id);
		$this->load->helper('ckeditor');
	
		//Ckeditor's configuration
		$data['ckeditor'] = array(
				//ID of the textarea that will be replaced
				'id' => 'mail_content',
				'path' => 'assets/js/biz/ckeditor/',
				'value' => isset($_POST['mail_content']) ? $_POST['mail_content'] : '',
				//Optionnal values
				'config' => array(
						'toolbar' => "Full", //Using the Full toolbar
						'width' => "100%", //Setting a custom width
						'height' => '200px', //Setting a custom height
				),
				//Replacing styles from the "Styles tool"
				'styles' => array(
						//Creating a new style named "style 1"
						'style 1' => array(
								'name' => 'Blue Title',
								'element' => 'h2',
								'styles' => array(
										'color' => 'Blue',
										'font-weight' => 'bold'
								)
						),
						//Creating a new style named "style 2"
						'style 2' => array(
								'name' => 'Red Title',
								'element' => 'h2',
								'styles' => array(
										'color' => 'Red',
										'font-weight' => 'bold',
										'text-decoration' => 'underline'
								)
						)
				)
		);
	
	
		$this->load->view("customers/create_mail", $data);
	}
	
	function save_mail($mail_id = -1) {
	
		$mail_data = array(
				'mail_title' => $this->input->post('mail_title'),
				'mail_content' => $this->input->post('mail_content')
		);
		//print_r($mail_data);die;
		if ($this->Customer->save_mail($mail_data, $mail_id)) {
			if ($mail_id == -1) {
				echo json_encode(array('success' => true, 'message' => lang('common_add_success') .
						$mail_data['mail_title'], 'mail_title' => $mail_data['mail_title']));
			} else { //previous customer
				echo json_encode(array('success' => true, 'message' => lang('common_update_success') .
						$mail_data['mail_title'], 'mail_title' => $mail_data['mail_title']));
			}
		} else {//failure
			echo json_encode(array('success' => false, 'message' => lang('common_error') , 'mail_id' => -1));
		}
	}
	
	function delete_mail() {
		$check = true;
		$mails_to_delete = $this->input->post('ids');
		$list_mail_template = array();
		$list_mail_template[] = $this->config->item('mail_template_birthday');
		$list_mail_template[] = $this->config->item('mail_template_contact');
		$list_mail_template[] = $this->config->item('mail_template_calendar');
		$title_mail = array();
		foreach ($list_mail_template as $key => $value) {
			$info_mail = $this->Customer->get_info_mail($value);
			$title_mail[] = $info_mail->mail_title;
			foreach ($mails_to_delete as $key1 => $value1) {
				if ($value == $value1) {
					$check = false;
				}
			}
		}
		if ($check) {
			if ($this->Customer->delete_mail_list($mails_to_delete)) {
				echo json_encode(array('success' => true, 'message' => lang('common_detach') . count($mails_to_delete) . ' email!'));
			} else {
				echo json_encode(array('success' => false, 'message' => lang('common_error')));
			}
		} else {
			$msg = "<br>(";
			for ($i = 0; $i < count($title_mail); $i++) {
				$msg .= $title_mail[$i] . "), ";
			}
			echo json_encode(array('success' => false, 'message' => lang('customers_mail_delete_err_mail_auto') . substr($msg, 0, strlen($msg) - 2) . ")"));
		}
	}
	
	function send_mail() {
		$data['list_mail'] = $this->Customer->get_all_mail();
		$this->load->view("customers/send_mail", $data);
	}
	
	/**
	 * Function send email
	 */
	function do_send_mail() {
		$check = $this->input->post("type_send");
		$customer_ids = $this->input->post('customer_ids');
		$mail_id = $this->input->post('mail_id');
		$mail_info = $this->Customer->get_info_mail($mail_id);
		$info_emp = $this->Employee->get_info($this->session->userdata('person_id'));
		$list_email = array();
		$send_success = array();
		$send_fail = array();
		$config = Array(
				'protocol' => 'smtp',
				'smtp_host' => 'ssl://smtp.googlemail.com',
				'smtp_port' => 465,
				'smtp_user' => $this->config->item('config_email_account'),
				'smtp_pass' => $this->config->item('config_email_pass'),
				'charset' => 'utf-8',
				'mailtype' => 'html'
		);
		$this->load->library('email', $config);
		$this->email->set_newline("\r\n");
		$this->email->from($this->config->item('email'), $this->config->item('company'));
		$this->email->subject($mail_info->mail_title);
		
		if ($check == 1) {
			if (isset($_SESSION['mail']) && $_SESSION['mail'] != NULL) {
				foreach ($_SESSION['mail'] as $mail) {
					$list_email[] = $mail['email'];
					if ($mail['email'] != "") {
// 						$user_info = $this->Customer->get_info_person_by_id($mail['person_id']);
						$user_info = $this->Customer->get_info($mail['person_id']);
						
						$user_info = (array) $user_info;
						$user_info = get_object_vars($user_info);
						
// 						$info_contraccustomer = $this->Contractcustomers->get_info_contraccustomer_by_customer($mail['person_id']);
						$info_contraccustomer = false;
						$content = $mail_info->mail_content;
						//Thong tin khach hang duoc gui mail
						$content = str_replace('__FIRST_NAME__', $user_info['first_name'], $content);
						$content = str_replace('__LAST_NAME__', $user_info['last_name'], $content);
						$content = str_replace('__PHONE_NUMBER__', $user_info['phone_number'], $content);
						$content = str_replace('__EMAIL__', $user_info['email'], $content);
						$content = str_replace('__COMPANY_CUSTOMER__', $user_info['company_name'], $content);
						//Thong tin chu ky cong ty gui mail
						$content = str_replace('__NAME_COMPANY__', '<b>' . $this->config->item('company') . '</b>', $content);
						$content = str_replace('__ADDRESS_COMPANY__', $this->config->item('address'), $content);
						$content = str_replace('__EMAIL_COMPANY__', $this->config->item('email'), $content);
						$content = str_replace('__FAX_COMPANY__', $this->config->item('fax'), $content);
						$content = str_replace('__WEBSITE_COMPANY__', $this->config->item('website'), $content);
						//Thong tin nhan vien
						$content = str_replace('__FIRST_NAME_EMPLOYEE__', '<b>' . $info_emp->first_name . '</b>', $content);
						$content = str_replace('__LAST_NAME_EMPLOYEE__', $info_emp->last_name, $content);
						$content = str_replace('__PHONE_NUMBER_EMPLOYEE__', $info_emp->phone_number, $content);
						$content = str_replace('__EMAIL_EMPLOYEE__', $info_emp->email, $content);
						//Thong tin hop dong
						if ($info_contraccustomer) {
							$content = str_replace('__NAME_CONTRACT__', '<b>' . $info_contraccustomer['name'] . '</b>', $content);
							$content = str_replace('__NUMBER_CONTRACT__', $info_contraccustomer['number_contract'], $content);
							$content = str_replace('__START_DATE__', date('d-m-Y', strtotime($info_contraccustomer['start_date'])), $content);
							$content = str_replace('__EXPIRATION_DATE__', date('d-m-Y', strtotime($info_contraccustomer['end_date'])), $content);
						} else {
							$content = str_replace('__NAME_CONTRACT__', '', $content);
							$content = str_replace('__NUMBER_CONTRACT__', '', $content);
							$content = str_replace('__START_DATE__', '', $content);
							$content = str_replace('__EXPIRATION_DATE__', '', $content);
						}
						$this->email->message($content);
						$this->email->to($mail['email']);
						if ($this->email->send()) {
							$send_success[] = $mail['email'];
							$data_history = array(
									'person_id' => $mail['person_id'],
									'employee_id' => $this->session->userdata('person_id'),
									'title' => $mail_info->mail_title,
									'content' => $content,
									'time' => date('Y-m-d H:i:s'),
									'status' => 1,
							);
							$this->Customer->add_mail_history($data_history);
							unset($_SESSION['mail'][$mail['person_id']]);
						} else {
							$send_fail[] = $mail['email'];
							$data_history = array(
									'person_id' => $mail['person_id'],
									'employee_id' => $this->session->userdata('person_id'),
									'title' => $mail_info->mail_title,
									'content' => $content,
									'time' => date('Y-m-d H:i:s'),
									'status' => 0,
							);
							$this->Customer->add_mail_history($data_history);
							show_error($this->email->print_debugger());
							unset($_SESSION['mail'][$mail['person_id']]);
							unset($_SESSION['mail_total']);
						}
					}
				}
			}
		} else {
			foreach ($customer_ids as $cust) {
				$info_cus = $this->Customer->get_info($cust);
// 				$info_contraccustomer = $this->Contractcustomers->get_info_contraccustomer_by_customer($info_cus->person_id);
				$info_contraccustomer = false;
				if ($info_cus->email != "") {
					$this->email->message($mail_info->mail_content);
					$content = $mail_info->mail_content;
					//Thong tin khach hang duoc gui mail
					$content = str_replace('__FIRST_NAME__', $info_cus->first_name, $content);
					$content = str_replace('__LAST_NAME__', $info_cus->last_name, $content);
					$content = str_replace('__PHONE_NUMBER__', $info_cus->phone_number, $content);
					$content = str_replace('__EMAIL__', $info_cus->email, $content);
					$content = str_replace('__COMPANY_CUSTOMER__', $info_cus->company_name, $content);
					//Thong tin chu ky cong ty gui mail
					$content = str_replace('__NAME_COMPANY__', '<b>' . $this->config->item('company') . '</b>', $content);
					$content = str_replace('__ADDRESS_COMPANY__', $this->config->item('address'), $content);
					$content = str_replace('__EMAIL_COMPANY__', $this->config->item('email'), $content);
					$content = str_replace('__FAX_COMPANY__', $this->config->item('fax'), $content);
					$content = str_replace('__WEBSITE_COMPANY__', $this->config->item('website'), $content);
					//Thong tin nhan vien
					$content = str_replace('__FIRST_NAME_EMPLOYEE__', '<b>' . $info_emp->first_name . '</b>', $content);
					$content = str_replace('__LAST_NAME_EMPLOYEE__', $info_emp->last_name, $content);
					$content = str_replace('__PHONE_NUMBER_EMPLOYEE__', $info_emp->phone_number, $content);
					$content = str_replace('__EMAIL_EMPLOYEE__', $info_emp->email, $content);
					//Thong tin hop dong
					if ($info_contraccustomer) {
						$content = str_replace('__NAME_CONTRACT__', '<b>' . $info_contraccustomer['name'] . '</b>', $content);
						$content = str_replace('__NUMBER_CONTRACT__', $info_contraccustomer['number_contract'], $content);
						$content = str_replace('__START_DATE__', date('d-m-Y', strtotime($info_contraccustomer['start_date'])), $content);
						$content = str_replace('__EXPIRATION_DATE__', date('d-m-Y', strtotime($info_contraccustomer['end_date'])), $content);
					} else {
						$content = str_replace('__NAME_CONTRACT__', '', $content);
						$content = str_replace('__NUMBER_CONTRACT__', '', $content);
						$content = str_replace('__START_DATE__', '', $content);
						$content = str_replace('__EXPIRATION_DATE__', '', $content);
					}
					$this->email->message($content);
					$this->email->to($info_cus->email);
					if ($this->email->send()) {
						$send_success[] = $info_cus->email;
						$data_history = array(
								'person_id' => $cust,
								'employee_id' => $this->session->userdata('person_id'),
								'title' => $mail_info->mail_title,
								'content' => $content,
								'time' => date('Y-m-d H:i:s'),
								'status' => 1,
						);
						$this->Customer->add_mail_history($data_history);
					} else {
						$send_fail[] = $info_cus->email;
						$data_history = array(
								'person_id' => $cust,
								'employee_id' => $this->session->userdata('person_id'),
								'title' => $mail_info->mail_title,
								'content' => $content,
								'time' => date('Y-m-d H:i:s'),
								'status' => 0,
						);
						$this->Customer->add_mail_history($data_history);
						show_error($this->email->print_debugger());
					}
				}
			}
		}
	
		if (empty($send_success)) {
			echo json_encode(array('success' => false, 'message' => lang('customers_mail_not_send: ')));
		} else if (empty($send_fail)) {
			echo json_encode(array(
					'success' => true,
					'message' => lang('customers_mail_send_success')));
		} else {
			$list_success = '';
			foreach ($send_success as $s) {
				$list .= $s . ', ';
			}
			$list_fail = '';
			foreach ($send_fail as $s) {
				$list .= $s . ', ';
			}
			echo json_encode(array('success' => true, 'message' => lang('customers_mail_send_success') . $list_success)) .
			json_encode(array('success' => false, 'message' => lang('customers_mail_not_send: ') . $list_fail));
		}
	}
	
	function save_list_send_mail($item_ids) {
		$item_ids = explode('~', $item_ids);
		$_SESSION['mail_total'] = count($item_ids);
		foreach ($item_ids as $item) {
			$info_cus = $this->Customer->get_info_person_by_id($item);
			if (isset($_SESSION['mail'][$item])) {
				continue;
			} else {
				$_SESSION['mail'][$info_cus['person_id']] = array(
						'person_id' => $item,
						'name' => $info_cus['first_name'] . " " . $info_cus['last_name'],
						'email' => $info_cus['email'],
				);
			}
		}
		redirect('customers');
	}
	
	function manage_mail_temp() {
		$mailData = isset($_SESSION['mail']) ? $_SESSION['mail'] : array();
		$config['total_rows'] = count($mailData);
		$config['per_page'] = $this->config->item('number_of_items_per_page') ? (int) $this->config->item('number_of_items_per_page') : 20;
		$config['base_url'] = site_url('customers/sorting_mail');
		$this->pagination->initialize($config);
		$data['pagination'] = $this->pagination->create_links();
		$data['controller_name'] = $this->_controller_name;
		$data['per_page'] = $config['per_page'];
		$data['total_rows'] = $config['total_rows'];

		$data['manage_table'] = get_mail_manage_table_temp($mailData, $this);
		$this->load->view("customers/manage_email_temp", $data);
	}
	
	function remove_mail_list() {
        $person_id = isset($_POST['ids']) ? $_POST['ids'] : '0';
        if ($person_id == 0) {
            unset($_SESSION['mail']);
            unset($_SESSION['mail_total']);
        } else {
            unset($_SESSION['mail'][$person_id]);
            $_SESSION['mail_total'] = count($_SESSION['mail_total']) - 1;
            echo count($_SESSION['mail']);
        }
    }
}
?>

