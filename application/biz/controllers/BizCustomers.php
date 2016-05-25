<?php
require_once (APPPATH . "controllers/Customers.php");

class BizCustomers extends Customers 
{
	protected $_scopeOfView = 'view_scope_owner';
	function __construct()
	{
		parent::__construct();

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
		echo json_encode(array('manage_table' => $data['manage_table'], 'pagination' => $data['pagination']));
	}
	
	function view_sms($id = -1) {
		$data['info_sms'] = $this->Customer->get_info_sms($id);
		$this->load->view("customers/form_sms", $data);
	}
	
	function save_sms($id = -1) {
		$sms_data = array(
				'title' => $this->input->post('sms_title'),
				'message' => $this->input->post('sms_message'),
				'number_char' => $this->input->post('sms_num_char'),
				'number_message' => $this->input->post('sms_num_mess'),
		);
		if ($this->Customer->save_sms($sms_data, $id)) {
			if ($id == -1) {
				echo json_encode(array('success' => true, 'message' => lang('customers_sms_msg_new'). ' (' . $sms_data['title'] . ')'. lang('customers_sms_msg_success') .' !', 'id' => $sms_data['id'],'redirect_code'=> 'customers/manage_sms'));
			} else { //previous customer
				echo json_encode(array('success' => true, 'message' => lang('customers_sms_msg_update') . ' (' . $sms_data['title'] . ') '. lang('customers_sms_msg_success') .' !', 'id' => $sms_data['id'],'redirect_code'=> 'customers/manage_sms'));
			}
		} else {//failure
			echo json_encode(array('success' => false, 'message' => lang('customers_sms_msg_error'), 'id' => -1));
		}
	}
	
	function delete_sms() {
		$sms_to_delete = $this->input->post('ids');
		if ($this->Customer->delete_sms_list($sms_to_delete)) {
			echo json_encode(array('success' => true, 'message' => ' Đã xóa!' . count($sms_to_delete) . ' SMS!'));
		} else {
			echo json_encode(array('success' => false, 'message' => 'Lỗi! Không xóa được, vui lòng thử lại!'));
		}
	}
	
	function suggest_sms() {
		$suggestions = $this->Customer->get_search_suggestions_sms($this->input->get('term'), 100);
		echo json_encode($suggestions);
	}
}
?>
