<?php 
include_once('mnestedtemplate.php');
class MTaskTemplate extends MNestedTemplate{
   
	protected $_table 			= 'task_template';
	protected $_id_admin 		= null;
	protected $_task_permission = null;
	protected $_fields 		    = array();
	
	public function __construct(){
		parent::__construct();
		$this->load->library('MY_System_Info');
		$info 			 = new MY_System_Info();
		$user_info 		 = $info->getInfo();

		$this->_id_admin = $user_info['id'];
		$this->_task_permission  = $user_info['task_permission'];
		
		$this->_fields 			= array(
									'name' 	 		=> 't.name',
									'modified' 	 	 => 't.modified',
									'username' 		 => 'e.username',
							);
	}
	
	public function countItem($arrParam = null, $options = null){
		if($options['task'] == 'template-list'){
			$this->db -> select('COUNT(t.id) AS totalItem')
					  -> from($this->_table . ' AS t')
			  		  -> where('t.parent = 0');
			
			if(!empty($arrParam['keywords'])) {
				$this->db->where('t.name LIKE \''.$arrParam['keywords'].'\'');
			}
			
			$query = $this->db->get();
				
			$result = $query->row()->totalItem;
				
			$this->db->flush_cache();
		}
		
		return $result;
	}
	
	public function saveItem($arrParam = null, $options = null){
		if($options['task'] == 'add'){
			if($arrParam['parent'] == 0) {
				$data['name']  					= 		stripslashes($arrParam['name']);
				$data['parent']					= 		$arrParam['parent'];
				$data['lft']					= 		0;
				$data['rgt']					= 		1;
				$data['template_id']			= 		0;
				$data['created']				= 		@date("Y-m-d H:i:s");
				$data['created_by']				= 		$this->_id_admin;
				$data['modified']				= 		@date("Y-m-d H:i:s");
				$data['modified_by']			= 		$this->_id_admin;
				
				$this->db->insert($this->_table,$data);
				$lastId = $this->db->insert_id();
				if($lastId > 0) {
					$this->db->where("id",$lastId);
					$data['template_id']	 		= 			$lastId;
						
					$this->db->update($this->_table,$data);
				}
			}else {
				$data['name']  					= 		stripslashes($arrParam['name']);
				$data['parent']					= 		$arrParam['parent'];
				$data['template_id']			= 		0;
				$data['created']				= 		@date("Y-m-d H:i:s");
				$data['created_by']				= 		$this->_id_admin;
				$data['modified']				= 		@date("Y-m-d H:i:s");
				$data['modified_by']			= 		$this->_id_admin;
				
				$lastId = $this->insertNode($data,$arrParam['parent'], $arrParam['template_id']);
			}
		}
		
		return $lastId;
		
	}
	
	public function listItem($arrParam = null, $options = null){
		if($options['task'] == 'template-list'){
			$paginator = $arrParam['paginator'];
			$this->db->select("DATE_FORMAT(t.modified, '%d/%m/%Y %H:%i:%s') as modified", FALSE);
			$this->db -> select('t.id, t.name, e.username')
					  -> from($this->_table . ' AS t')
					  -> join('employees AS e', 'e.id = t.modified_by', 'left')
					  -> where('t.parent = 0');
			
			$page = (empty($arrParam['start'])) ? 1 : $arrParam['start'];
			$this->db->limit($paginator['per_page'],($page - 1)*$paginator['per_page']);
			
			if(!empty($arrParam['keywords'])) {
				$this->db->where('t.name LIKE \''.$arrParam['keywords'].'\'');
			}
			
			if(!empty($arrParam['col']) && !empty($arrParam['order'])){
				$col   = $this->_fields[$arrParam['col']];
				$order = $arrParam['order'];
			
				$this->db->order_by($col, $order);
			}else {
				$this->db->order_by('t.id', 'DESC');
			}
			
			$query = $this->db->get();

			$result = $query->result_array();
			$this->db->flush_cache();
		}
		
		return $result;
	}
	
}