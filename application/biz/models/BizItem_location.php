<?php
require_once (APPPATH . "models/Item_location.php");
class BizItem_location extends Item_location
{	
	function getQtyOfEachLocation($item_id)
	{
		$this->db->select('items.name as item_name, locations.name as location_name, location_items.quantity');
		$this->db->from('location_items');
		$this->db->join('locations', 'locations.location_id = location_items.location_id');
		$this->db->join('items', 'items.item_id = location_items.item_id');
		$this->db->where('items.item_id', $item_id);
		$this->db->where('locations.deleted', 0);
		return $this->db->get()->result_array();
	}
}
?>
