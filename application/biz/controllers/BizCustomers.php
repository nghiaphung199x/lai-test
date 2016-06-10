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
			'comments'=>$this->input->post('comments')
		);
		
		
		$customer_data=array(
			'company_name' => $this->input->post('company_name'),
			'tier_id' => $this->input->post('tier_id') ? $this->input->post('tier_id') : NULL,
			'account_number'=>$this->input->post('account_number')=='' ? null:$this->input->post('account_number'),
			'taxable'=>$this->input->post('taxable')=='' ? 0:1,
			'tax_certificate' => $this->input->post('tax_certificate'),
			'override_default_tax'=> $this->input->post('override_default_tax') ? $this->input->post('override_default_tax') : 0,
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
                                                'username' => $this->config->item('config_user_sms'),
                                                'password' => $this->config->item('config_user_pass'),
                                                'source_addr' => $this->config->item('config_brand_name'),
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
						'username' => $this->config->item('user_sms'),
						'password' => $this->config->item('pass_sms'),
						'source_addr' => $this->config->item('brandname'),
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
        
}
?>
