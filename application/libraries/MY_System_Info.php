<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 
class MY_System_Info{
	protected $_CI; 
	protected $_id;
	protected $_group_id;
	
	public function __construct(){
		session_start();
		$this->_CI= &get_instance();
	}
	
	public function getInfo() {
		$task_permission = array();
		$person_id = $_SESSION['person_id'];
		$this->_CI->db->select('*')
				       ->where('person_id', (int)$person_id);

		$result =  $this->_CI->db->get('employees')->row_array();
		$this->_CI->db->flush_cache();
		
		$group_id = $result['group_id'];
		
		$this->_CI->db->select('*')
					  ->from('group_permissions_actions')
			  		  ->where('group_id', $group_id)
					  ->where('module_id', 'tasks');
		
		$query = $this->_CI->db->get();
		
		$resultTmp = $query->result_array();

		$this->_CI->db->flush_cache();
		if(!empty($resultTmp)) {
			foreach($resultTmp as $val)
				$task_permission[] = $val['action_id'];
		}

		$this->_CI->db->select('*')
					->from('permissions_actions')
					->where('person_id', $person_id);
		
		$query = $this->_CI->db->get();
		
		$resultTmp = $query->result_array();
		
		$this->_CI->db->flush_cache();
		
		if(!empty($resultTmp)) {
			foreach($resultTmp as $val)
				$task_permission[] = $val['action_id'];
		}
		
		$array['task_permission'] = $task_permission;
		
		return $array;
	} 
	
}