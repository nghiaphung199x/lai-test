<?php 
class MTaskCustomers extends CI_Model{
	protected $_table="customers";
	public function __construct(){
		parent::__construct();
	}
	
	public function getItems($arrParams = null, $options = null) {
		if($options == null) {
			$this->db->select("CONCAT_WS(' ', p.first_name, p.last_name) AS name", FALSE);
			$this->db->select("c.id")
					->from($this->_table . ' AS c')
					->join('people as p', 'c.person_id = p.person_id', 'left')
					->where('id IN ('.implode(',', $arrParams['cid']).')')
					->where('c.deleted = 0')
					->order_by("c.id",'DESC');
				
			$query = $this->db->get();
	
			$result = $query->result_array();
			$this->db->flush_cache();
		}
	
		return $result;
	}
}