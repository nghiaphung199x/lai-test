<?php
class BizKitBom extends CI_Model
{
	function save(&$item_kit_boms_data, $item_kit_id)
	{
		//Run these queries as a transaction, we want to make sure we do all or nothing
		$this->db->trans_start();
		$this->db->delete('item_kit_boms', array('item_kit_id' => $item_kit_id));
		foreach ($item_kit_boms_data as $row)
		{
			$row['item_kit_id'] = $item_kit_id;
			$this->db->insert('item_kit_boms',$row);		
		}
		$this->db->trans_complete();
		return true;
	}
}