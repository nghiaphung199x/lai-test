<?php
require_once (APPPATH . "controllers/Item_kits.php");
class BizItem_kits extends Item_kits
{
	public function count_available_kits()
	{
		$items = $this->input->post('items');
		echo json_encode(array('success' => 1, 'available_kits' => $this->Item_kit->countAvailableKits($items)));
	}
	
	function item_search()
	{
		$this->load->model('Item');
		//allow parallel searchs to improve performance.
		session_write_close();
		$suggestions = $this->Item->get_item_search_suggestions($this->input->get('term'),100);
		$this->load->model('Item_location');
		foreach ($suggestions as &$item) {
			$item['qty'] = (int) $this->Item_location->get_location_quantity($item['value']);
		}
		
		echo json_encode($suggestions);
	}
	
	function view($item_kit_id=-1,$redirect=0)
	{
		$this->load->model('Item_kit_items');
		$this->load->model('Item_kit_taxes');
		$this->load->model('Tier');
		$this->load->model('Item');
		$this->load->model('Item_kit_location');
		$this->load->model('Item_kit_location_taxes');
		$this->load->model('Supplier');
		$this->load->model('Item_kit_taxes_finder');
		$this->load->model('Item_location');
	
		$this->check_action_permission('add_update');
		$data = $this->_get_item_kit_data($item_kit_id);
		$data['redirect']=$redirect;
		
		$this->load->view("item_kits/form",$data);
	}
}
?>
