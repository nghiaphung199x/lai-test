<?php
class BizItemMeasures extends CI_Model
{
	/*
	 Inserts or updates a item
	 */
	function save(&$itemMeasureData,$id=false)
	{
		if($this->db->insert('item_measures',$itemMeasureData))
		{
			$itemMeasureData['id']=$this->db->insert_id();
			return true;
		}
		return false;
	}
	
	function deleteByItemId($itemId=false)
	{
		if($itemId) {
			$this->db->delete('item_measures', array('item_id' => $itemId));
		}
	}
	
	public function getMeasuresByItemId($itemId=false) {
		if($itemId) {
			$this->db->from('item_measures');
			$this->db->where('item_id', $itemId);
			return $this->db->get()->result_array();
		}
		return array();
	}
}