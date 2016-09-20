<?php 
class MTaskProgress extends CI_Model{
   
	protected $_table       = 'task_progress';
	protected $_items       = null;
	protected $_task_ids    = null;
	protected $_is_progress = null;
	protected $_id_admin    = null;
	protected $_admin_name  = null;
	
	public function __construct(){
		parent::__construct();
		
		$this->load->library('MY_System_Info');
		$info 			 = new MY_System_Info();
		$user_info 		 = $info->getInfo();
		
		$this->_id_admin 	    = $user_info['id'];
		$this->_admin_name 		= $user_info['username'];
		$this->_task_permission = $user_info['task_permission'];
	}
	
	public function getItem($arrParam = null, $options = null) {
		if($options['task'] == 'public-info') {
			$this->db->select('p.*')
					 ->from($this->_table . ' AS p')
					 ->where('p.id',$arrParam['id']);
			
			$query = $this->db->get();
			$result = $query->row_array();

			$this->db->flush_cache();
		}
		
		return $result;
	}
	
	public function countItem($arrParam = null, $options = null){
		$ssFilter  = $arrParam['ssFilter'];
			
		$taskTable = $this->model_load_model('MTasks');
			
		$item = $taskTable->getItem(array('id'=>$arrParam['task_id']), array('task'=>'public-info', 'brand'=>'full'));
			
		$task_ids = $taskTable->getIds(array('lft'=>$item['lft'], 'rgt'=>$item['rgt'], 'project_id'=>$item['project_id']));
		
		$this->_task_ids = $task_ids;

		// user có quyền phê duyệt
		$is_progress  = array();
		
		if(!empty($item['is_progress'])) {
			foreach($item['is_progress'] as $key => $val){
				$is_progress[] = $val['id'];
			}
				
			$is_progress 		= array_unique($is_progress);
		}
		
		$this->_is_progress = $is_progress;

		if($options['task'] == 'public-list'){
			$this->db -> select('COUNT(p.id) AS totalItem')
					  -> from($this->_table . ' AS p')
					  -> where('p.task_id IN ('.implode(', ', $this->_task_ids).')')
					  -> where('p.pheduyet IN (1, 3)');
			
			$query = $this->db->get();
			
			$result = $query->row()->totalItem;
			
			$this->db->flush_cache();
		}elseif($options['task'] == 'request-list') {
			$this->db -> select('COUNT(p.id) AS totalItem')
					  -> from($this->_table . ' AS p')
					  -> where('p.task_id IN ('.implode(', ', $this->_task_ids).')')
					  -> where('p.pheduyet IN (0, 1, 2)')
					  -> where('p.created_by', $this->_id_admin);

			$query = $this->db->get();
			$result = $query->row()->totalItem;
				
			$this->db->flush_cache();
		}elseif($options['task'] == 'pheduyet-list') {
			if(in_array($this->_id_admin, $is_progress)) {
				$this->db -> select('COUNT(p.id) AS totalItem')
						  -> from($this->_table . ' AS p')
						  -> where('p.task_id IN ('.implode(', ', $this->_task_ids).')')
						  -> where('p.pheduyet = 2')
						  -> or_where('p.pheduyet IN (0, 1) AND p.user_pheduyet = ' . $this->_id_admin);
					
				$query = $this->db->get();
				$result = $query->row()->totalItem;
				
				$this->db->flush_cache();
			}else
				$result = 0;
			
		}
		return $result;
	}
	
	public function saveItem($arrParam = null, $options = null) {
		if($options['task'] == 'add') {
			$data['task_id'] 			= $arrParam['task_id'];
			$data['trangthai'] 			= $arrParam['trangthai'];
			$data['prioty'] 			= $arrParam['prioty'];
			$data['progress'] 			= $arrParam['progress'] / 100;
			$data['pheduyet'] 			= $arrParam['pheduyet'];
			$data['note']				= stripslashes($arrParam['note']);
			$data['reply']				= '';		
			$data['created']			= @date("Y-m-d H:i:s");
			$data['created_by']     	= $arrParam['adminInfo']['id'];
			$data['user_pheduyet']		= 0;
			$data['date_pheduyet']		= $arrParam['date_pheduyet'];
			$data['user_pheduyet_name'] = '';
		
			$data['key']				= $arrParam['key'];
			
			$this->db->insert($this->_table,$data);
			$lastId = $this->db->insert_id();
				
			$this->db->flush_cache();
			
			return $lastId;
		}elseif($options['task'] == 'update-pheduyet') {
			$this->db->where("id",$arrParam['id']);
			
			$data['pheduyet'] 			= $arrParam['pheduyet'];
			$data['reply']				= stripslashes($arrParam['reply']);
			$data['date_pheduyet']		= @date("Y-m-d H:i:s");
			$data['user_pheduyet']		= $this->_id_admin;
			$data['user_pheduyet_name']	= $this->_admin_name;
			
			$this->db->update($this->_table,$data);
				
			$this->db->flush_cache();
		}elseif($options['task'] == 'progress-1') {
			$task_ids = implode(', ', $arrParam['task_ids']);
			$sqlString   = 'UPDATE ' .$this->db->dbprefix($this->_table)
						  . ' SET progress = -1'
						  . ' WHERE task_ids IN ('.$task_ids.')';
			
			$this->db->query($sqlString);
		}
	}
	
	public function listItem($arrParam = null, $options = null){
		$prioty_arr    = array('Rất cao', 'Cao', 'Trung bình', 'Thấp', 'Rất thấp');
		$ssFilter  = $arrParam['ssFilter'];
		if($options['task'] == 'public-list'){
			$paginator = $arrParam['paginator'];
			$this->db->select("DATE_FORMAT(p.date_pheduyet, '%d/%m/%Y %H:%i:%s') as created", FALSE);
			$this->db -> select('p.id, p.created_by, p.trangthai, t.name as task_name,p.progress, p.pheduyet, p.key, e.username, p.prioty')
					  -> from($this->_table . ' AS p')
					  -> join('tasks as t', 't.id = p.task_id', 'left')
					  -> join('employees AS e', 'e.id = p.created_by', 'left')
					  -> where('p.task_id IN ('.implode(', ', $this->_task_ids).')')
					  -> where('p.pheduyet IN (1, 3)')
					  -> order_by('p.date_pheduyet', 'DESC')
					  -> order_by('p.id', 'DESC');
	
			$page = (empty($arrParam['start'])) ? 1 : $arrParam['start'];
			$this->db->limit($paginator['per_page'],($page - 1)*$paginator['per_page']);
				
			if(!empty($ssFilter['col']) && !empty($ssFilter['order'])){
				$this->db->order_by($ssFilter['col'],$ssFilter['order']);
			}

			$query = $this->db->get();

			$result = $query->result_array();
			$this->db->flush_cache();
			
			if(!empty($result)) {
				$trangthai_arr = array('-1'=>'_','0'=>'Chưa thực hiện', '1'=>'Đang thực hiện', '2'=>'Hoàn thành', '3'=>'Đóng/dừng', '4'=>'Không thực hiện');
				foreach($result as &$val) {
					$val['trangthai'] = $trangthai_arr[$val['trangthai']];
					$val['progress'] = $val['progress'] * 100 . '%';
						
					$val['prioty'] = $prioty_arr[$val['prioty']];
				}
			}
		
		}elseif($options['task'] == 'request-list') {
			$paginator = $arrParam['paginator'];
			$this->db->select("DATE_FORMAT(p.date_pheduyet, '%d/%m/%Y %H:%i:%s') as date_pheduyet", FALSE);
			$this->db->select("DATE_FORMAT(p.created, '%d/%m/%Y %H:%i:%s') as created", FALSE);
			$this->db -> select('p.id, p.created_by, p.trangthai, t.name as task_name,p.progress, p.pheduyet, p.key, p.prioty, p.user_pheduyet_name')
					-> from($this->_table . ' AS p')
					-> join('tasks as t', 't.id = p.task_id', 'left')
					-> where('p.task_id IN ('.implode(', ', $this->_task_ids).')')
					-> where('p.pheduyet IN (0, 1, 2)')
					-> where('p.created_by', $this->_id_admin)
					-> order_by('p.created', 'DESC');
			
			$page = (empty($arrParam['start'])) ? 1 : $arrParam['start'];
			$this->db->limit($paginator['per_page'],($page - 1)*$paginator['per_page']);
			
			if(!empty($ssFilter['col']) && !empty($ssFilter['order'])){
				$this->db->order_by($ssFilter['col'],$ssFilter['order']);
			}
			
			$query = $this->db->get();
			
			$result = $query->result_array();
			$this->db->flush_cache();
			
			if(!empty($result)) {
				$trangthai_arr = array('-1'=>'_','0'=>'Chưa thực hiện', '1'=>'Đang thực hiện', '2'=>'Hoàn thành', '3'=>'Đóng/dừng', '4'=>'Không thực hiện');
				foreach($result as &$val) {
					$val['trangthai'] = $trangthai_arr[$val['trangthai']];
					$val['progress'] = $val['progress'] * 100 . '%';
			
					$val['prioty'] = $prioty_arr[$val['prioty']];
					$val['date_pheduyet'] = ($val['date_pheduyet'] == '00/00/0000 00:00:00') ? '' : $val['date_pheduyet'];
				}
			}
		}elseif($options['task'] == 'pheduyet-list') {
			$result = array();
			if(in_array($this->_id_admin, $this->_is_progress)) {
				$this->db->select("DATE_FORMAT(p.date_pheduyet, '%d/%m/%Y %H:%i:%s') as date_pheduyet", FALSE);
				$this->db->select("DATE_FORMAT(p.created, '%d/%m/%Y %H:%i:%s') as created", FALSE);
				$this->db -> select('p.id,t.name as task_name, p.progress, p.trangthai, p.prioty, e.username, p.pheduyet')
						  -> from($this->_table . ' AS p')
						  -> join('tasks as t', 't.id = p.task_id', 'left')
						  -> join('employees as e', 'e.id = p.created_by', 'left')
						  -> where('p.task_id IN ('.implode(', ', $this->_task_ids).')')
						  -> where('p.pheduyet = 2')
						  -> or_where('p.pheduyet IN (0, 1) AND p.user_pheduyet = ' . $this->_id_admin);
					
				$query = $this->db->get();
				$result = $query->result_array();

				$this->db->flush_cache();
				
				if(!empty($result)) {
					foreach($result as &$val) {
						$trangthai_arr = array('-1'=>'_','0'=>'Chưa thực hiện', '1'=>'Đang thực hiện', '2'=>'Hoàn thành', '3'=>'Đóng/dừng', '4'=>'Không thực hiện');
						foreach($result as &$val) {
							$val['trangthai'] = $trangthai_arr[$val['trangthai']];
							$val['progress'] = $val['progress'] * 100 . '%';
								
							$val['prioty'] = $prioty_arr[$val['prioty']];
							if($val['date_pheduyet'] == '00/00/0000 00:00:00') {
								$val['date_pheduyet'] = '';
								$val['is_xuly'] = true;
							}else {
								$val['is_xuly'] = false;
							}

							if($val['pheduyet'] == 2)
								$val['pheduyet'] = '<i class="fa fa-clock-o" aria-hidden="true"></i>';
							elseif($val['pheduyet'] == 0)
								$val['pheduyet'] = '<i class="fa fa-times"></i>';
							elseif($val['pheduyet'] == 1)
								$val['pheduyet'] = '<i class="fa fa-check"></i>';

						}
					}
				}
			}
		}
		
		return $result;
	}
	
	function do_progress($level, $options) {
		$taskTable = $this->model_load_model('MTasks');
		if(!empty($level)) {
			$last_level = end($level);
			array_pop($level );

			$parent_id = $last_level[0]['parent'];
			if($parent_id > 0) {
				$new_parent_progress = 0;
				foreach($last_level as $task) {
					$new_parent_progress = $new_parent_progress + $task['percent'] * $task['progress'];
				}
				
				$new_parent_progress = $new_parent_progress * 100;

				//update parent
				$taskTable->saveItem(array('id'=>$parent_id, 'progress'=>$new_parent_progress), array('task'=>'update-progress'));
	
				// cập nhật lại parent trong level
				if(!empty($level)) {
					foreach($level as &$l) {
						foreach($l as &$task) {
							if($parent_id == $task[id]) { 
								$task['progress'] = $new_parent_progress / 100;
								$parent_item = $task;
							}
						}
					}
				}
				
				// progress data
				if($options['task'] != 'progress') {
					$created_by 		= $options['created_by'];
					$user_pheduyet 		= $this->_id_admin;
					$user_pheduyet_name = $this->_admin_name;
				}else {
					$created_by 		= $this->_id_admin;
					$user_pheduyet 		= 0;
					$user_pheduyet_name = '';
				}
					
				$progressTmp = array(
						'task_id' 			 => $parent_item['id'],
						'trangthai' 		 => $parent_item['trangthai'],
						'prioty' 			 => $parent_item['prioty'],
						'progress' 			 => $parent_item['progress'],
						'pheduyet'			 => 3,
						'note' 				 => '',
						'reply' 			 => '',
						'created'			 => @date("Y-m-d H:i:s"),
						'created_by'		 => $created_by,
						'user_pheduyet'		 => $user_pheduyet,
						'user_pheduyet_name' => $user_pheduyet_name,	
						'date_pheduyet'	     => @date("Y-m-d H:i:s"),
						'key' 			 	 => '',
						);
				
				$this->_items[] = $progressTmp;

				$this->do_progress($level);	
			}
		}
	}

	public function solve($arrParam) {
		$taskTable 		= $this->model_load_model('MTasks');
		$arrParam['id'] = $arrParam['last_id'];
		$task 			= $arrParam;
		$task_items 	= $taskTable->getItems(array('project_id'=>$task['project_id']), array('task'=>'by-project'));
		
		foreach($task_items as $task_id => $task) {
			if($task_id == $task['id']){
				$task['progress']  = $task['progress'] / 100;
				
					
				if($options != null) {
					$progressTmp = array(
							'task_id' 			 => $progress_item['task_id'],
							'trangthai' 		 => $progress_item['trangthai'],
							'prioty' 			 => $progress_item['prioty'],
							'progress' 			 => $progress_item['progress'],
							'pheduyet'			 => $arrParam['pheduyet'],
							'note' 				 => '',
							'reply' 			 => '',
							'created'			 => @date("Y-m-d H:i:s"),
							'created_by'		 => $this->_id_admin,
							'user_pheduyet'		 => 0,
							'user_pheduyet_name' => '',
							'date_pheduyet'	     => @date("Y-m-d H:i:s"),
							'key' 			 	 => 'add',
					);
						
					$this->_items[] = $progressTmp;
				}else{
					$options['created_by'] = $progress_item['created_by'];
				}
			}
			$level[$task['level']][] = $task;
		}
	}

	function handling($arrParam = null, $options = null) {
		if($options == null)
			$progress_item = $this->getItem(array('id'=>$arrParam['id']), array('task'=>'public-info'));
		elseif($options['task'] == 'progress'){
			$progress_item 			   = $arrParam;
			$progress_item['progress'] = $progress_item['progress'] / 100;
		}
		$taskTable = $this->model_load_model('MTasks');
		$task = $taskTable->getItem(array('id'=>$progress_item['task_id']), array('task'=>'public-info'));
		
		if($progress_item['progress'] == -1) { // chỉ cập nhật trạng thái
			$arrParam['id'] 	   = $progress_item['task_id'];
			$arrParam['prioty']    = $progress_item['prioty'];
			$arrParam['trangthai'] = $progress_item['trangthai'];
			
			$taskTable->saveItem($arrParam, array('task'=>'update-tiendo'));
			
		}else {
			$task_items = $taskTable->getItems(array('project_id'=>$task['project_id']), array('task'=>'by-project'));
			
			foreach($task_items as $task_id => $task) {
				if($task_id == $progress_item['task_id']){
					$task['progress']  = $progress_item['progress'] * 100;
					$task['prioty']    = $progress_item['prioty'];
					$task['trangthai'] = $progress_item['trangthai'];
			
					// cập nhật tiến độ task
					$taskTable->saveItem($task, array('task'=>'update-tiendo'));
			
					$task['progress']  = $progress_item['progress'];
					
					if($options != null) {
						$progressTmp = array(
								'task_id' 			 => $progress_item['task_id'],
								'trangthai' 		 => $progress_item['trangthai'],
								'prioty' 			 => $progress_item['prioty'],
								'progress' 			 => $progress_item['progress'],
								'pheduyet'			 => $arrParam['pheduyet'],
								'note' 				 => '',
								'reply' 			 => '',
								'created'			 => @date("Y-m-d H:i:s"),
								'created_by'		 => $this->_id_admin,
								'user_pheduyet'		 => 0,
								'user_pheduyet_name' => '',
								'date_pheduyet'	     => @date("Y-m-d H:i:s"),
								'key' 			 	 => '',
						);
							
						$this->_items[] = $progressTmp;
					}else{
						$options['created_by'] = $progress_item['created_by'];
					}
				}
				$level[$task['level']][] = $task;
			}

			$this->do_progress($level, $options);
			
			// cập nhật progress
			if(!empty($this->_items))
				$this->db->insert_batch($this->_table, $this->_items);
			
		}	
	}
	
	function model_load_model($model_name)
	{
		$CI =& get_instance();
		$CI->load->model($model_name);
		return $CI->$model_name;
	}
	
}