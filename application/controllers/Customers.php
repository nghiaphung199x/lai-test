<?php
require_once ("Person_controller.php");
class Customers extends Person_controller
{
	function __construct()
	{
		parent::__construct('customers');
		$this->lang->load('customers');
		$this->lang->load('module');
		$this->load->model('Customer');
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
			$config['total_rows'] = $this->Customer->count_all();
			$table_data = $this->Customer->get_all($data['per_page'],$params['offset'],$params['order_col'],$params['order_dir']);
		}
		$this->load->library('pagination');$this->pagination->initialize($config);
		$data['pagination'] = $this->pagination->create_links();
		$data['order_col'] = $params['order_col'];
		$data['order_dir'] = $params['order_dir'];
		
		$data['manage_table']=get_people_manage_table($table_data,$this);
		$data['total_rows'] = $config['total_rows'];
		$this->load->view('people/manage',$data);
	}

	function sorting()
	{
		$this->check_action_permission('search');
		$search=$this->input->post('search') ? $this->input->post('search') : "";
		$per_page=$this->config->item('number_of_items_per_page') ? (int)$this->config->item('number_of_items_per_page') : 20;
		
		$offset = $this->input->post('offset') ? $this->input->post('offset') : 0;
		$order_col = $this->input->post('order_col') ? $this->input->post('order_col') : 'last_name';
		$order_dir = $this->input->post('order_dir') ? $this->input->post('order_dir'): 'asc';

		$customers_search_data = array('offset' => $offset, 'order_col' => $order_col, 'order_dir' => $order_dir, 'search' => $search);
		$this->session->set_userdata("customers_search_data",$customers_search_data);
		
		
		if ($search)
		{
			$config['total_rows'] = $this->Customer->search_count_all($search);
			$table_data = $this->Customer->search($search,$per_page,$this->input->post('offset') ? $this->input->post('offset') : 0, $this->input->post('order_col') ? $this->input->post('order_col') : 'last_name' ,$this->input->post('order_dir') ? $this->input->post('order_dir'): 'asc');
		}
		else
		{
			$config['total_rows'] = $this->Customer->count_all();
			$table_data = $this->Customer->get_all($per_page,$this->input->post('offset') ? $this->input->post('offset') : 0, $this->input->post('order_col') ? $this->input->post('order_col') : 'last_name' ,$this->input->post('order_dir') ? $this->input->post('order_dir'): 'asc');
		}
		$config['base_url'] = site_url('customers/sorting');
		$config['per_page'] = $per_page; 
		$this->load->library('pagination');$this->pagination->initialize($config);
		$data['pagination'] = $this->pagination->create_links();
		$data['manage_table']=get_people_manage_table_data_rows($table_data,$this);
		echo json_encode(array('manage_table' => $data['manage_table'], 'pagination' => $data['pagination']));	
		
	}
	
	/*
	Returns customer table data rows. This will be called with AJAX.
	*/
	function search()
	{
		$this->check_action_permission('search');
		$search=$this->input->post('search');
		$offset = $this->input->post('offset') ? $this->input->post('offset') : 0;
		$order_col = $this->input->post('order_col') ? $this->input->post('order_col') : 'last_name';
		$order_dir = $this->input->post('order_dir') ? $this->input->post('order_dir'): 'asc';

		$customers_search_data = array('offset' => $offset, 'order_col' => $order_col, 'order_dir' => $order_dir, 'search' => $search);
		$this->session->set_userdata("customers_search_data",$customers_search_data);
		$per_page=$this->config->item('number_of_items_per_page') ? (int)$this->config->item('number_of_items_per_page') : 20;
		$search_data=$this->Customer->search($search,$per_page,$offset, $order_col ,$order_dir);
		$config['base_url'] = site_url('customers/search');
		$config['total_rows'] = $this->Customer->search_count_all($search);
		$config['per_page'] = $per_page ;
		
		$this->load->library('pagination');$this->pagination->initialize($config);				
		$data['pagination'] = $this->pagination->create_links();
		$data['total_rows'] = $this->Customer->search_count_all($search);
		$data['manage_table']=get_people_manage_table_data_rows($search_data,$this);
		echo json_encode(array('manage_table' => $data['manage_table'], 'pagination' => $data['pagination']));
	}
	
	function mailing_label_from_summary_customers_report($start_date, $end_date, $sale_type, $total_spent_condition = 'any', $total_spent_amount = 0)
	{
		$start_date=rawurldecode($start_date);
		$end_date=rawurldecode($end_date);

		$this->load->model('Sale');
		$this->load->model('reports/Summary_customers');
		$model = $this->Summary_customers;
		$model->setParams(array('start_date'=>$start_date, 'end_date'=>$end_date, 'sale_type' => $sale_type, 'offset' => 0, 'export_excel' => 1, 'total_spent_condition' => $total_spent_condition, 'total_spent_amount' => $total_spent_amount));

		$this->Sale->create_sales_items_temp_table(array('start_date'=>$start_date, 'end_date'=>$end_date, 'sale_type' => $sale_type));
		
		$report_data = $model->getData();
		
		$customer_ids = array();
		foreach($report_data as $row)
		{
			$customer_ids[] = $row['customer_id'];
		}
		
		foreach($customer_ids as $customer_id)
		{			
			$customer_info = $this->Customer->get_info($customer_id);
			
			$label = array();
			$label['name'] = $customer_info->first_name.' '.$customer_info->last_name;
			$label['address_1'] = $customer_info->address_1;
			$label['address_2'] = $customer_info->address_2;
			$label['city'] = $customer_info->city;
			$label['state'] = $customer_info->state;
			$label['zip'] = $customer_info->zip;
			$label['country'] = $customer_info->country;
			
			$data['mailing_labels'][] = $label;
			
		}
		
		$data['type'] = $this->config->item('mailing_labels_type') == 'excel' ? 'excel' : 'pdf';
		
		$this->load->view("mailing_labels", $data);	
		
	}
	
	function mailing_labels($customer_ids)
	{
		$data['mailing_labels'] = array();
		
		foreach(explode('~', $customer_ids) as $customer_id)
		{			
			$customer_info = $this->Customer->get_info($customer_id);
			
			$label = array();
			$label['name'] = $customer_info->first_name.' '.$customer_info->last_name;
			$label['address_1'] = $customer_info->address_1;
			$label['address_2'] = $customer_info->address_2;
			$label['city'] = $customer_info->city;
			$label['state'] = $customer_info->state;
			$label['zip'] = $customer_info->zip;
			$label['country'] = $customer_info->country;
			
			$data['mailing_labels'][] = $label;
			
		}
		$data['type'] = $this->config->item('mailing_labels_type') == 'excel' ? 'excel' : 'pdf';
		$this->load->view("mailing_labels", $data);	
	}
	
	/*
	Gives search suggestions based on what is being searched for
	*/
	function suggest()
	{
		//allow parallel searchs to improve performance.
		session_write_close();
		$suggestions = $this->Customer->get_customer_search_suggestions($this->input->get('term'),100);
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
		
		$data['redirect_code']=$redirect_code;
		$this->load->view("customers/form",$data);
	}
	
	function account_number_exists()
	{
		if($this->Customer->account_number_exists($this->input->post('account_number')))
		echo 'false';
		else
		echo 'true';
		
	}

	function clear_state()
	{
		$this->session->unset_userdata('customers_search_data');
		redirect('customers');
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
	This deletes customers from the customers table
	*/
	function delete()
	{
		$this->check_action_permission('delete');
		$customers_to_delete=$this->input->post('ids');
		
		if($this->Customer->delete_list($customers_to_delete))
		{
			echo json_encode(array('success'=>true,'message'=>lang('customers_successful_deleted').' '.
			count($customers_to_delete).' '.lang('customers_one_or_multiple')));
		}
		else
		{
			echo json_encode(array('success'=>false,'message'=>lang('customers_cannot_be_deleted')));
		}
	}
	
	function _excel_get_header_row()
	{
		$return = array(lang('common_first_name'),lang('common_last_name'),lang('common_email'),lang('common_phone_number'),lang('common_address_1'),lang('common_address_2'),lang('common_city'),	lang('common_state'),lang('common_zip'),lang('common_country'),lang('common_comments'),lang('customers_account_number'),lang('customers_taxable'),lang('customers_tax_certificate'), lang('customers_company_name'),lang('common_tier_name'));
	
		if ($this->config->item('customers_store_accounts'))
		{
			$return[] = lang('common_balance');
		}
		
		return $return;
	}
		
	function excel()
	{
		$this->load->helper('download');
		$this->load->helper('report');
		$header_row = $this->_excel_get_header_row();
		
		$this->load->helper('spreadsheet');
		$content = array_to_spreadsheet(array($header_row));
		force_download('import_customers.'.($this->config->item('spreadsheet_format') == 'XLSX' ? 'xlsx' : 'csv'), $content);
	}
	
	function excel_import()
	{
		$this->check_action_permission('add_update');
		$this->load->view("customers/excel_import", null);
	}
	
	function check_duplicate()
	{
		echo json_encode(array('duplicate'=>$this->Customer->check_duplicate($this->input->post('name'),$this->input->post('email'),$this->input->post('phone_number'))));
	}
	/* added for excel expert */
	function excel_export() {
		
		$this->load->helper('download');
		set_time_limit(0);
		
		$tiers = array();
		$this->load->model('Tier');
		foreach($this->Tier->get_all()->result_array() as $tier)
		{
			$tiers[$tier['id']] = $tier['name'];
		}
		
		$data = $this->Customer->get_all($this->Customer->count_all())->result_object();
		$this->load->helper('report');
		$rows = array();
		
		$header_row = $this->_excel_get_header_row();
		$header_row[] = lang('customers_customer_id');
		$rows[] = $header_row;
		
		foreach ($data as $r) {
			$row = array(
				$r->first_name,
				$r->last_name,
				$r->email,
				$r->phone_number,
				$r->address_1,
				$r->address_2,
				$r->city,
				$r->state,
				$r->zip,
				$r->country,
				$r->comments,
				$r->account_number,
				$r->taxable ? 'y' : 'n',
				$r->tax_certificate,
				$r->company_name,
				isset($tiers[$r->tier_id]) ?  $tiers[$r->tier_id] : '',
			);
			
			if ($this->config->item('customers_store_accounts'))
			{
				$row[] = $r->balance ? to_currency_no_money($r->balance) : '';
			}
			
			$row[] = $r->person_id;
			
			$rows[] = $row;
		}
		
		$this->load->helper('spreadsheet');
		$content = array_to_spreadsheet($rows);
		force_download('customers_export.'.($this->config->item('spreadsheet_format') == 'XLSX' ? 'xlsx' : 'csv'), $content);
		exit;
	}

    /*
	function do_excel_import()
	{
		
		$tiers = array();
		$this->load->model('Tier');
		foreach($this->Tier->get_all()->result_array() as $tier)
		{
			$tiers[$tier['name']] = $tier['id'];
		}
		
		$this->load->helper('demo');
		if (is_on_demo_host())
		{
			$msg = lang('common_excel_import_disabled_on_demo');
			echo json_encode( array('success'=>false,'message'=>$msg) );
			return;
		}

		$file_info = pathinfo($_FILES['file_path']['name']);
		if($file_info['extension'] != 'xlsx' && $file_info['extension'] != 'csv')
		{
			echo json_encode(array('success'=>false,'message'=>lang('common_upload_file_not_supported_format')));
			return;
		}
		
		set_time_limit(0);
		$this->check_action_permission('add_update');
		$this->db->trans_start();
				
		$msg = 'do_excel_import';
		$failCodes = array();
		if ($_FILES['file_path']['error']!=UPLOAD_ERR_OK)
		{
			$msg = lang('common_excel_import_failed');
			echo json_encode( array('success'=>false,'message'=>$msg) );
			return;
		}
		else
		{
			if (($handle = fopen($_FILES['file_path']['tmp_name'], "r")) !== FALSE)
			{
				$this->load->helper('spreadsheet');
				$objPHPExcel = file_to_obj_php_excel($_FILES['file_path']['tmp_name']);
				$sheet = $objPHPExcel->getActiveSheet();
				$num_rows = $objPHPExcel->setActiveSheetIndex(0)->getHighestRow();
				
				//Loop through rows, skip header row
				for($k = 2;$k<=$num_rows; $k++)
				{					
					$first_name = $sheet->getCellByColumnAndRow(0, $k)->getValue();
					if (!$first_name)
					{
						$first_name = '';
					}
					
					$last_name = $sheet->getCellByColumnAndRow(1, $k)->getValue();
					if (!$last_name)
					{
						$last_name = '';
					}

					$email = $sheet->getCellByColumnAndRow(2, $k)->getValue();
					if (!$email)
					{
						$email = '';
					}

					$phone_number = $sheet->getCellByColumnAndRow(3, $k)->getValue();
					if (!$phone_number)
					{
						$phone_number = '';
					}

					$address_1 = $sheet->getCellByColumnAndRow(4, $k)->getValue();
					if (!$address_1)
					{
						$address_1 = '';
					}

					$address_2 = $sheet->getCellByColumnAndRow(5, $k)->getValue();
					if (!$address_2)
					{
						$address_2 = '';
					}

					$city = $sheet->getCellByColumnAndRow(6, $k)->getValue();
					if (!$city)
					{
						$city = '';
					}

					$state = $sheet->getCellByColumnAndRow(7, $k)->getValue();
					if (!$state)
					{
						$state = '';
					}

					$zip = $sheet->getCellByColumnAndRow(8, $k)->getValue();
					if (!$zip)
					{
						$zip = '';
					}

					$country = $sheet->getCellByColumnAndRow(9, $k)->getValue();
					if (!$country)
					{
						$country = '';
					}

					$comments = $sheet->getCellByColumnAndRow(10, $k)->getValue();
					if (!$comments)
					{
						$comments = '';
					}

					$account_number = $sheet->getCellByColumnAndRow(11, $k)->getValue();
					if (!$account_number)
					{
						$account_number = NULL;
					}

					$taxable = $sheet->getCellByColumnAndRow(12, $k)->getValue();
					
					$tax_certificate = $sheet->getCellByColumnAndRow(13, $k)->getValue();
					if (!$tax_certificate)
					{
						$tax_certificate = '';
					}
					
					$company_name = $sheet->getCellByColumnAndRow(14, $k)->getValue();
					if (!$company_name)
					{
						$company_name = '';
					}
					
					$tier_name = $sheet->getCellByColumnAndRow(15, $k)->getValue();
					if (!$tier_name)
					{
						$tier_id = NULL;
					}
					else
					{
						$tier_id = isset($tiers[$tier_name]) ? $tiers[$tier_name] : NULL;
					}
					
					
					if ($this->config->item('customers_store_accounts'))
					{
						$balance = $sheet->getCellByColumnAndRow(16, $k)->getValue();
						if (!$balance)
						{
							$balance = 0;
						}
						$person_id = $sheet->getCellByColumnAndRow(17, $k)->getValue();
					}
					else
					{
						$balance = 0;
						$person_id = $sheet->getCellByColumnAndRow(16, $k)->getValue();
					}
					
					//If we don't have a first name skip the import
					if (!$first_name)
					{
						continue;
					}
					
					
					$person_data = array(
					'first_name'=>$first_name,
					'last_name'=>$last_name,
					'email'=>$email,
					'phone_number'=>$phone_number,
					'address_1'=>$address_1,
					'address_2'=>$address_2,
					'city'=>$city,
					'state'=>$state,
					'zip'=>$zip,
					'country'=>$country,
					'comments'=>$comments
					);
					
					$customer_data=array(
					'account_number'=>$account_number,
					'taxable'=> $taxable == 'n' || $taxable == 'no' ? 0 : 1,
					'tax_certificate' => $tax_certificate,
					'company_name' => $company_name,
					'balance' => $balance,
					'tier_id' => $tier_id,
					);
					
					if(!$this->Customer->save_customer($person_data,$customer_data, $person_id ? $person_id : FALSE))
					{	
						echo json_encode( array('success'=>false,'message'=>lang('customers_duplicate_account_id')));
						return;
					}
				}
			}
			else 
			{
				echo json_encode( array('success'=>false,'message'=>lang('common_upload_file_not_supported_format')));
				return;
			}
		}
		$this->db->trans_complete();
		echo json_encode(array('success'=>true,'message'=>lang('customers_import_successfull')));
	}
    */

    /**
     * @Loads the form for excel import
     */
    function do_excel_import()
    {
        $this->check_action_permission('add_update');
        $this->load->helper('demo');
        if (is_on_demo_host()) {
            $msg = lang('common_excel_import_disabled_on_demo');
            echo json_encode(array('success' => false, 'message' => $msg));
            return;
        }
        if ($_FILES['file_path']['error'] != UPLOAD_ERR_OK) {
            $msg = lang('common_excel_import_failed');
            echo json_encode(array('success' => false, 'message' => $msg));
            return;
        } else {
            if (($handle = fopen($_FILES['file_path']['tmp_name'], "r")) !== FALSE) {
                $this->load->helper('spreadsheet');
                $objPHPExcel = file_to_obj_php_excel($_FILES['file_path']['tmp_name']);
                $end_column = $objPHPExcel->setActiveSheetIndex(0)->getHighestColumn();
                $this->load->model('Attribute_set');
                $data['attribute_sets'] = $this->Attribute_set->get_all()->result();
                $data['sheet'] = $objPHPExcel->getActiveSheet();
                $data['num_rows'] = $objPHPExcel->setActiveSheetIndex(0)->getHighestRow();
                $data['columns'] = range('A', $end_column);
                $data['fields'] = $this->Customer->get_import_fields();
                $data['person_fields'] = $this->Customer->get_person_import_fields();
                $html = $this->load->view('customers/import/result', $data, true);
                $result = array('success' => true, 'message' => lang('common_import_success'), 'html' => $html);
                echo json_encode($result);
                return;
            } else {
                echo json_encode(array('success' => false, 'message' => lang('common_upload_file_not_supported_format')));
                return;
            }
        }
        $result = array('success' => true, 'message' => lang('common_import_success'));
        echo json_encode($result);
    }

    /**
     * Import Real Data
     **/
    public function action_import_data()
    {
        $this->check_action_permission('add_update');
        $this->load->helper('demo');
        if (is_on_demo_host()) {
            $msg = lang('common_excel_import_disabled_on_demo');
            echo json_encode(array('success' => false, 'message' => $msg));
            return;
        }
        $this->load->model('Attribute');
        $entity_type = 'customers';
        $person_entity_type = 'people';
        $check_duplicate_field = $this->input->post('check_duplicate_field');
        $field_parts = explode(':', $check_duplicate_field);
        if (count($field_parts) == 2) {
            $check_duplicate_field_type = $field_parts[0];
            $check_duplicate_field_name = $field_parts[1];
        }
        $attribute_set_id = $this->input->post('attribute_set_id');
        $columns = $this->input->post('columns');
        $rows = $this->input->post('rows');
        $selected_rows = $this->input->post('selected_rows');
        $stored_rows = 0;
        $person_import_fields = $this->Customer->get_person_import_fields();
        if (empty($rows) || empty($selected_rows)) {
            $msg = lang('common_error');
            echo json_encode(array('success' => true, 'message' => $msg));
            return;
        }
        foreach ($rows as $index => $row) {
            if (!isset($selected_rows[$index])) {
                continue;
            }
            $data = array('attribute_set_id' => $attribute_set_id);
            $person_data = $extend_data = $extend_rows = array();
            foreach ($columns as $excel_column => $field_column) {
                if (!empty($field_column) && !empty($row[$excel_column])) {
                    $field_parts = explode(':', $field_column);

                    /* Set Basic Attributes */
                    if (count($field_parts) == 2) {
                        switch ($field_parts[0]) {
                            case 'person':
                                $person_data[$field_parts[1]] = $row[$excel_column];
                                break;
                            case 'basic':
                                $data[$field_parts[1]] = $row[$excel_column];
                                break;
                            case 'extend':
                                $extend_data = array(
                                    'entity_type' => $entity_type,
                                    'attribute_id' => $field_parts[1],
                                    'entity_value' => $row[$excel_column],
                                );
                                $extend_rows[] = $extend_data;
                                break;
                            default:
                                $data[$field_parts[1]] = $row[$excel_column];
                                break;
                        }
                    }
                }
            }
            try {
                /* Check duplicate item */
                $exists_row = false;
                if (isset($check_duplicate_field_type) && isset($check_duplicate_field_name)) {
                    switch ($check_duplicate_field_type) {
                        case 'person':
                            if (!empty($person_data[$check_duplicate_field_name])) {
                                $exists_row = $this->Person->exists_by_field($person_entity_type, $check_duplicate_field_name, $person_data[$check_duplicate_field_name], false, false);
                            }
                            break;
                        case 'basic':
                            if (!empty($data[$check_duplicate_field_name])) {
                                $exists_row = $this->Customer->exists_by_field($entity_type, $check_duplicate_field_name, $data[$check_duplicate_field_name]);
                            }
                            break;
                        case 'extend':
                            if (!empty($extend_data['entity_value'])) {
                                $exists_row = $this->Attribute->exists_by_value($entity_type, $extend_data['attribute_id'], $extend_data['entity_value']);
                            }
                            break;
                        default:
                            if (!empty($data[$check_duplicate_field_name])) {
                                $exists_row = $this->Customer->exists_by_field($entity_type, $check_duplicate_field_name, $data[$check_duplicate_field_name]);
                            }
                            break;
                    }
                }
                if (!$exists_row) {
                    /* Auto fill empty person fields */
                    foreach ($person_import_fields as $person_import_field) {
                        if (!isset($person_data[$person_import_field])) {
                            $person_data[$person_import_field] = '';
                        }
                    }
                    $customer_id = $this->Customer->save_customer($person_data, $data, null);
                    if (!empty($customer_id)) {
                        $stored_rows++;
                        /* Set extended attributes */
                        if (!empty($extend_rows)) {
                            foreach ($extend_rows as $extend_data) {
                                $extend_data['entity_id'] = $customer_id;
                                $this->Attribute->set_attributes($extend_data);
                            }
                        }
                    }
                }
            } catch (Exception $ex) {
                continue;
            }
        }
        if (!empty($stored_rows)) {
            $msg = $stored_rows . ' ' . lang('common_record_stored');
            echo json_encode(array('success' => true, 'message' => $msg));
            return;
        }
        $msg = $stored_rows . ' ' . lang('common_record_stored');
        echo json_encode(array('success' => false, 'message' => $msg));
    }
		
	function cleanup()
	{
		$this->Customer->cleanup();
		echo json_encode(array('success'=>true,'message'=>lang('customers_cleanup_sucessful')));
	}
		
	function pay_now($customer_id)
	{
		$this->load->model('Sale');
		$this->load->model('Customer');
		$this->load->model('Tier');
		$this->load->model('Category');
		$this->load->model('Giftcard');
		$this->load->model('Tag');
		$this->load->model('Item');
		$this->load->model('Item_location');
		$this->load->model('Item_kit_location');
		$this->load->model('Item_kit_location_taxes');
		$this->load->model('Item_kit');
		$this->load->model('Item_kit_items');
		$this->load->model('Item_kit_taxes');
		$this->load->model('Item_location_taxes');
		$this->load->model('Item_taxes');
		$this->load->model('Item_taxes_finder');
		$this->load->model('Item_kit_taxes_finder');
		$this->load->library('sale_lib');
    	$this->sale_lib->clear_all();
		$this->sale_lib->set_customer($customer_id);
		$this->sale_lib->set_mode('store_account_payment');
		$store_account_payment_item_id = $this->Item->create_or_update_store_account_item();
		$this->sale_lib->add_item($store_account_payment_item_id,1);
		redirect('sales');
	}
}
?>