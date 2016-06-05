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
			$this->db->join('measures', 'measures.id = item_measures.measure_converted_id', 'left');
			$this->db->where('item_id', $itemId);
			return $this->db->get()->result_array();
		}
		return array();
	}
	
	public function getConvertedValue($itemId = 0, $measureConvertedId = 0) {
		$itemInfo = $this->Item->get_info($itemId);
		if ($itemInfo) {
			$this->db->from('item_measures');
			$this->db->where('item_id', $itemId);
			$this->db->where('measure_id', $itemInfo->measure_id);
			$this->db->where('measure_converted_id', $measureConvertedId);
			$result = $this->db->get();
			if($result->num_rows() > 0)
			{
				$row = $result->result();
				return $row[0];
			}
		}
		return FALSE;
	}
}