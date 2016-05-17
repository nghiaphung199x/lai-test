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
		$this->load->model('Measure');
	}
	
	function _get_item_data($item_id)
	{
		$this->load->helper('report');
	
		$data = array();
		$data['controller_name']=$this->_controller_name;
	
		$data['item_info']=$this->Item->get_info($item_id);
	
		$data['categories'][''] = lang('common_select_category');
	
		$categories = $this->Category->sort_categories_and_sub_categories($this->Category->get_all_categories_and_sub_categories());
		foreach($categories as $key=>$value)
		{
			$name = str_repeat('&nbsp;&nbsp;', $value['depth']).$value['name'];
			$data['categories'][$key] = $name;
		}
	
		$data['tags'] = implode(',',$this->Tag->get_tags_for_item($item_id));
	
		$data['measures'] = array();
		$measures = $this->Measure->get_all();
		foreach($measures as $key=>$measure)
		{
			$data['measures'][$key] = $measure['name'];
		}
	
		$data['item_tax_info']=$this->Item_taxes->get_info($item_id);
		$data['tiers']=$this->Tier->get_all()->result();
		$data['locations'] = array();
		$data['location_tier_prices'] = array();
		$data['additional_item_numbers'] = $this->Additional_item_numbers->get_item_numbers($item_id);
	
		if ($item_id != -1)
		{
			$data['next_item_id'] = $this->Item->get_next_id($item_id);
			$data['prev_item_id'] = $this->Item->get_prev_id($item_id);;
		}
			
		foreach($this->Location->get_all()->result() as $location)
		{
			if($this->Employee->is_location_authenticated($location->location_id))
			{
				$data['locations'][] = $location;
				$data['location_items'][$location->location_id] = $this->Item_location->get_info($item_id,$location->location_id);
				$data['location_taxes'][$location->location_id] = $this->Item_location_taxes->get_info($item_id, $location->location_id);
	
				foreach($data['tiers'] as $tier)
				{
					$tier_prices = $this->Item_location->get_tier_price_row($tier->id,$data['item_info']->item_id, $location->location_id);
					if (!empty($tier_prices))
					{
						$data['location_tier_prices'][$location->location_id][$tier->id] = $tier_prices;
					}
					else
					{
						$data['location_tier_prices'][$location->location_id][$tier->id] = FALSE;
					}
				}
			}
	
		}
	
	
		if ($item_id == -1)
		{
			$suppliers = array(''=> lang('common_not_set'), '-1' => lang('common_none'));
		}
		else
		{
			$suppliers = array('-1' => lang('common_none'));
		}
		foreach($this->Supplier->get_all()->result_array() as $row)
		{
			$suppliers[$row['person_id']] = $row['company_name'] .' ('.$row['first_name'] .' '. $row['last_name'].')';
		}
	
		$data['tier_prices'] = array();
		$data['tier_type_options'] = array('unit_price' => lang('common_fixed_price'), 'percent_off' => lang('common_percent_off'));
		foreach($data['tiers'] as $tier)
		{
			$tier_prices = $this->Item->get_tier_price_row($tier->id,$data['item_info']->item_id);
	
			if (!empty($tier_prices))
			{
				$data['tier_prices'][$tier->id] = $tier_prices;
			}
			else
			{
				$data['tier_prices'][$tier->id] = FALSE;
			}
		}
	
		$data['suppliers']=$suppliers;
		$data['selected_supplier'] = $this->Item->get_info($item_id)->supplier_id;
	
		$decimals = $this->Appconfig->get_raw_number_of_decimals();
		$decimals = $decimals !== NULL && $decimals!= '' ? $decimals : 2;
		$data['decimals'] = $decimals;
	
		return $data;
	}
	
	function manage_measures()
	{
		// $this->check_action_permission('manage_measures');
		$measures = $this->Measure->get_all();
		$data = array('measures' => $measures, 'measure_list' => $this->_measureList());
		$this->load->view('items/measures',$data);
	}
	
	function _measureList()
	{
		$measures = $this->Measure->get_all();
		$return = '<ul>';
		foreach($measures as $measureId => $measure)
		{
			$return .='<li>'.$measure['name'].
			'<a href="javascript:void(0);" class="edit_measure" data-name = "'.H($measure['name']).'" data-measure_id="'.$measureId.'">['.lang('common_edit').']</a> '.
			'<a href="javascript:void(0);" class="delete_measure" data-measure_id="'.$measureId.'">['.lang('common_delete').']</a> ';
			$return .='</li>';
		}
		$return .='</ul>';
	
		return $return;
	}
	
	function saveMeasure($measureId = FALSE)
	{
		// $this->check_action_permission('manage_tags');
		$measureName = $this->input->post('measure_name');
	
		if ($this->Measure->save($measureName, $measureId))
		{
			echo json_encode(array('success'=>true,'message'=>lang('items_tag_successful_adding').' '.$measureName));
		}
		else
		{
			echo json_encode(array('success'=>false,'message'=>lang('items_tag_successful_error')));
		}
	}
	
	function deleteMeasure()
	{
		// $this->check_action_permission('manage_tags');
		$measureId = $this->input->post('measure_id');
		if($this->Measure->delete($measureId))
		{
			echo json_encode(array('success'=>true,'message'=>lang('items_successful_deleted')));
		}
		else
		{
			echo json_encode(array('success'=>false,'message'=>lang('items_cannot_be_deleted')));
		}
	}
	
	function measureList()
	{
		echo $this->_measureList();
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
