<?php
require_once (APPPATH . "controllers/Items.php");

class BizItems extends Items 
{
	function __construct()
	{
		parent::__construct();
		$this->load->model('Receiving');
		$this->load->model('Item_location');
		$this->load->library('BizSession');
	}
	
	public function showNotAudit() {
		$response = array('success' => 1);
		$data = array();
		$count_id = $this->input->post('count_id', 0);;
		$data['audit_items'] = $this->Inventory->get_items_counted($count_id, NULL,NULL);
		$auditedIds = array_map(function($item) {
			return $item['item_id'];
		}, $data['audit_items']);
		$extra['category_id'] = (int) $this->bizsession->getValue('AUDIT_CATEGORY');
		$data['notAuditedItems'] = $this->Item->getNotAuditedInLocation($auditedIds, $extra);
		$response['html'] = $this->load->view('items/partials/not_audited', $data, TRUE);
		echo json_encode($response);
	}
	
	function item_search()
	{
		//allow parallel searchs to improve performance.
		$extra['category_id'] = (int) $this->bizsession->getValue('AUDIT_CATEGORY');
		$extra['by_current_location'] = true;
		session_write_close();
		$suggestions = $this->Item->get_item_search_suggestions($this->input->get('term'),100, $extra);
		echo json_encode($suggestions);
	}
	
	function do_count($count_id, $offset = 0)
	{
		$this->check_action_permission('count_inventory');
		$this->session->set_userdata('current_count_id',$count_id);
	
		$data = array();
		$config = array();
		$config['base_url'] = site_url("items/do_count/$count_id");
		$config['per_page'] = $this->config->item('number_of_items_per_page') ? (int)$this->config->item('number_of_items_per_page') : 20;
		$config['total_rows'] = $this->Inventory->get_number_of_items_counted($count_id);
		$config['uri_segment'] = 4;
		$data['per_page'] = $config['per_page'];
		$data['count_id'] = $count_id;
	
		$data['total_rows'] = $config['total_rows'];
		$this->load->library('pagination');$this->pagination->initialize($config);
		$data['pagination'] = $this->pagination->create_links();
		$data['count_info'] = $this->Inventory->get_count_info($count_id);
	
		$data['items_counted'] = $this->Inventory->get_items_counted($count_id,$config['per_page'], $offset);
		$data['mode'] = $this->session->userdata('count_mode') ? $this->session->userdata('count_mode') : 'scan_and_set';
		$data['modes'] = array('scan_and_set' => lang('items_scan_and_set'), 'scan_and_add' => lang('items_scan_and_add') );
		
		$categories = $this->Category->get_all_categories_and_sub_categories();
		
		$data['categories'] = array_map(function($item){
			return $item['name'];
		}, $categories);
		$data['categories']['all'] = 'Tất cả';
		$data['selected_category'] = 'all';
		$this->load->view('items/do_count', $data);
	}
	
	public function setCategory()
	{
		$response = array('success' => 1);
		$categoryId = $this->input->post('category_id', 0);
		$this->bizsession->setValue('AUDIT_CATEGORY', $categoryId);
		echo json_encode($response);
	}
	
	function index($offset=0)
	{
		$params = $this->session->userdata('item_search_data') ? $this->session->userdata('item_search_data') : array('offset' => 0, 'order_col' => 'name', 'order_dir' => 'asc', 'search' => FALSE, 'category_id' => FALSE, 'fields' => 'all');
		if ($offset!=$params['offset'])
		{
			redirect('items/index/'.$params['offset']);
		}
	
		$this->check_action_permission('search');
		$config['base_url'] = site_url('items/sorting');
		$config['per_page'] = $this->config->item('number_of_items_per_page') ? (int)$this->config->item('number_of_items_per_page') : 20;
	
		$data['controller_name']=$this->_controller_name;
		$data['per_page'] = $config['per_page'];
		$data['search'] = $params['search'] ? $params['search'] : "";
		$data['category_id'] = $params['category_id'] ? $params['category_id'] : "";
		$data['categories'][''] = lang('common_all');
		$categories = $this->Category->sort_categories_and_sub_categories($this->Category->get_all_categories_and_sub_categories());
		foreach($categories as $key=>$value)
		{
			$name = str_repeat('&nbsp;&nbsp;', $value['depth']).$value['name'];
			$data['categories'][$key] = $name;
		}
	
		$data['fields'] = $params['fields'] ? $params['fields'] : "all";
	
		if ($data['search'] || $data['category_id'])
		{
			$config['total_rows'] = $this->Item->search_count_all($data['search'], $data['category_id'],10000, $data['fields']);
			$table_data = $this->Item->search($data['search'],$data['category_id'],$data['per_page'],$params['offset'],$params['order_col'],$params['order_dir'], $data['fields']);
		}
		else
		{
			$config['total_rows'] = $this->Item->count_all();
			$table_data = $this->Item->get_all($data['per_page'],$params['offset'],$params['order_col'],$params['order_dir']);
		}
		
		$data['total_rows'] = $config['total_rows'];
		$this->load->library('pagination');$this->pagination->initialize($config);
		$data['pagination'] = $this->pagination->create_links();
		$data['order_col'] = $params['order_col'];
		$data['order_dir'] = $params['order_dir'];
		$data['manage_table']=get_items_manage_table($table_data,$this);
		$this->load->view('items/manage',$data);
	}
	
	public function qty_location()
	{
		$response = array('success' => 1);
		$data = array();
		$itemId = $this->input->post('item_id', 0);

		$data['qty_locations'] = $this->Item_location->getQtyOfEachLocation($itemId);
		$response['html'] = $this->load->view('items/partials/qty_location', $data, TRUE);
		echo json_encode($response);
	}

	public function transfer_pending()
	{
		$data = array();
		$data['transferings'] = $this->Receiving->getAllTransferings();
		$this->load->view('items/transferings', $data);
	}
	
	public function delete_transfer()
	{
		$response = array('success' => 1);
		$recId = $this->input->post('rec_id', 0);
		$this->Receiving->removeTransferPending($recId, $this->Employee->get_logged_in_employee_info()->person_id);
		echo json_encode($response);
	}
	
	public function approve_transfer()
	{
		$response = array('success' => 1);
	
		$recId = $this->input->post('rec_id', 0);
		$this->receiving_lib->clear_all();
		$this->receiving_lib->copy_entire_receiving($recId);
	
		$recInfo = $this->Receiving->get_info($recId)->row_array();
	
		$data['cart']=$this->receiving_lib->get_cart();
		if (empty($data['cart']))
		{
			$response['success'] = 0;
		}
	
		$supplier_id=$recInfo['supplier_id'];
		$location_to_id=$recInfo['transfer_to_location_id'];
		$location_from_id=$recInfo['location_id'];
		$employee_id=$recInfo['employee_id'];
		$comment = $recInfo['comment'];
		$payment_type = $recInfo['payment_type'];
	
		$recId = $this->Receiving->approvedTransfer(
				$data['cart'],
				$supplier_id,
				$employee_id,
				$comment,
				$payment_type,
				$recId,
				$recInfo['receiving_time'],
				0,
				$location_from_id
				);
	
		if($supplier_id!=-1)
		{
			$suppl_info=$this->Supplier->get_info($supplier_id);
		}
	
		if($recId > 0 && $this->receiving_lib->get_email_receipt() && !empty($suppl_info->email))
		{
			$this->load->library('email');
			$config['mailtype'] = 'html';
			$this->email->initialize($config);
			$this->email->from($this->Location->get_info_for_key('email') ? $this->Location->get_info_for_key('email') : 'no-reply@mg.4biz.vn', $this->config->item('company'));
			$this->email->to($suppl_info->email);
	
			$this->email->subject(lang('receivings_receipt'));
			$this->email->message($this->load->view("receivings/receipt_email",$data, true));
			$this->email->send();
		}
		$this->receiving_lib->clear_all();
		echo json_encode($response);
	}
	
	function finish_count($update_inventory = 0)
	{
		$this->check_action_permission('count_inventory');
	
		$count_id = $this->session->userdata('current_count_id');
	
		if ($update_inventory && $this->Employee->has_module_action_permission('items','edit_quantity', $this->Employee->get_logged_in_employee_info()->person_id))
		{
			$this->Inventory->update_inventory_from_count($count_id);
			
			$data['audit_items'] = $this->Inventory->get_items_counted($count_id, NULL,NULL);
			$data['create_datetime'] = date(get_date_format().' '.get_time_format(), strtotime());
			$data['count_id'] = $count_id;
			$this->load->view("items/audit",$data);
		} else {
			$this->Inventory->set_count($count_id, 'closed');
			redirect('items/count');
		}
	}
}
?>
