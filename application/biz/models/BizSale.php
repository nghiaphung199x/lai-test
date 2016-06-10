<?php
require_once (APPPATH . "models/Sale.php");
class BizSale extends Sale
{
	function get_all_materials() {
		$this->db->from('sales');
		$this->db->where('deleted', 0);
		$this->db->where('quotes_contract', 1);
		$this->db->order_by('sale_id', 'desc');
		return $this->db->get();
	}
}
?>
