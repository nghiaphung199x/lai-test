<?php 
include_once('mnested2.php');
class MTasks extends MNested2{
   
	protected $_table 			= 'tasks';
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
		
		$this->_fields 	 =  array(
							'name' 	 		=> 't.name',
							'prioty' 	 	=> 't.prioty',
							'modified' 	 	 => 't.modified',
							'username' 		 => 'e.username',
					  );
	}
	
	
	public function countItem($arrParams = null, $options = null) {
		if($options == null || $options['task'] == 'grid-list' || $options['task'] == 'grid-project') {
			$user_ids = array();
			$flagAll = $this->checkAllPermission();
			
			if($flagAll == false) {
				//project liên quan
				$sql = 'SELECT COUNT(t.id) AS total_item
						FROM ' . $this->db->dbprefix($this->_table).' AS t
						WHERE t.id IN (SELECT task_id FROM '.$this->db->dbprefix(task_user_relations).' WHERE user_id = '.$this->_id_admin.')
						AND t.parent = 0';
				
				if(!empty($arrParams['keywords'])) {
					$sql = $sql . ' AND t.name LIKE \'%'.$arrParams['keywords'].'%\'';
				}

				$query = $this->db->query($sql);
				$result = $query->row()->totalItem;
			}else {
				$this->db -> select('COUNT(t.id) AS totalItem')
						  -> from($this->_table . ' AS t');
				
				if(!empty($arrParams['keywords'])) {
					$this->db->where('t.name LIKE \'%'.$arrParams['keywords'].'%\'');
				}
					
				$query = $this->db->get();
				
				$result = $query->row()->totalItem;
			}
		}elseif ($options['task'] == 'public-list') {
			$this->db -> select('COUNT(t.id) AS totalItem')
					  -> from($this->_table . ' AS t')
			  		  -> where('t.parent = 0');
			
			if(!empty($arrParams['keywords'])) {
				$this->db->where('t.name LIKE \'%'.$arrParams['keywords'].'%\'');
			}
			
			$query = $this->db->get();
				
			$result = $query->row()->totalItem;
				
			$this->db->flush_cache();
		}
		return $result;

	}
	
	public function saveItem($arrParam = null, $options = null){
		if($options['task'] == 'add'){
			if(isset($arrParam['customer'])) {
				$customer_ids = implode(',', $arrParam['customer']);
			}
			
			if($arrParam['progress'] == 100)
				$date_finish = @date("Y-m-d H:i:s");
			else
				$date_finish = '0000/00/00 00:00:00';
				
			if($arrParam['parent'] == 0) {
				$data['name']  					= 		stripslashes($arrParam['name']);
				$data['detail'] 				= 		stripslashes($arrParam['detail']);
				$data['percent']				= 		1;
				$data['progress']				= 		$arrParam['progress'] / 100;
				$data['lft']					= 		0;
				$data['rgt']					= 		1;
				$data['level']					= 		0;
				$data['parent']					= 		$arrParam['parent'];
				$data['project_id']				= 		$arrParam['project_id'];
				$data['date_start']				= 		$arrParam['date_start'];
				$data['date_end']				= 		$arrParam['date_end'];
				$data['date_finish']			= 		$date_finish;
				$data['duration']				= 		$arrParam['duration'];
				$data['created']				= 		@date("Y-m-d H:i:s");
				$data['created_by']				= 		$this->_id_admin;
				$data['modified']				= 		@date("Y-m-d H:i:s");
				$data['modified_by']			= 		$this->_id_admin;
				$data['trangthai']				= 		$arrParam['trangthai'];
				$data['prioty']					= 		$arrParam['prioty'];
				$data['pheduyet']				= 		1;
				$data['type']					= 		$arrParam['type'];
				$data['project_id']				= 		0;
				$data['customer_ids']			= 		$customer_ids;
				$data['color']					= 		$arrParam['color'];

				$this->db->insert($this->_table,$data);
				$lastId = $this->db->insert_id();
				
				if($lastId > 0) {
					$this->db->where("id",$lastId);
					$data['project_id']	 		= 			$lastId;
					
					$this->db->update($this->_table,$data);
				}
				
			}else {
				$data['name']  					= 		stripslashes($arrParam['name']);
				$data['detail'] 				= 		stripslashes($arrParam['detail']);
				$data['percent']				= 		$arrParam['percent'] / 100;
				$data['progress']				= 		$arrParam['progress'] / 100;
				$data['date_start']				= 		$arrParam['date_start'];
				$data['date_end']				= 		$arrParam['date_end'];
				$data['duration']				= 		$arrParam['duration'];
				$data['date_finish']			= 		$date_finish;
				$data['created']				= 		@date("Y-m-d H:i:s");
				$data['created_by']				= 		$this->_id_admin;
				$data['modified']				= 		@date("Y-m-d H:i:s");
				$data['modified_by']			= 		$this->_id_admin;
				$data['trangthai']				= 		$arrParam['trangthai'];
				$data['prioty']					= 		$arrParam['prioty'];
				$data['pheduyet']				= 		$arrParam['pheduyet'];
				$data['type']					= 		$arrParam['type'];
				$data['project_id']				= 		$arrParam['project_id'];
				$data['customer_ids']			= 		$customer_ids;
				$data['color']					= 		$arrParam['color'];

				$lastId = $this->insertNode($data,$arrParam['parent'], $arrParam['project_id']);
			}
			
			if($lastId > 0) {
				$xemArr = array();
				if(isset($arrParam['xem'])) 
					$xemArr = $arrParam['xem'];

				$implementArr = array();
				if(isset($arrParam['implement'])) 
					$implementArr = $arrParam['implement'];
				
				$create_taskArr = array();
				if(isset($arrParam['create_task']))
					$create_taskArr = $arrParam['create_task'];
				
				$pheduyet_taskArr = array();
				if(isset($arrParam['pheduyet_task']))
					$pheduyet_taskArr = $arrParam['pheduyet_task'];
				
				$progress_taskArr = array();
				if(isset($arrParam['progress_task']))
					$progress_taskArr = $arrParam['progress_task'];

				$created_byArr = array($this->_id_admin);

				$this->do_relation_information($lastId, $xemArr, $implementArr, $create_taskArr, $pheduyet_taskArr, $progress_taskArr, $created_byArr);

				if(!empty($array)) {
					$this->db->insert_batch('task_user_relations', $array);
				}
			}

			$this->db->flush_cache();
		}elseif($options['task'] == 'edit') {
			$lastId = $arrParam['id'];
			if(isset($arrParam['customer'])) {
				$customer_ids = implode(',', $arrParam['customer']);
			}

			$this->db->where("id",$arrParam['id']);

			if($arrParam['progress'] == 100)
				$date_finish = @date("Y-m-d H:i:s");
			else
				$date_finish = '0000/00/00 00:00:00';

			$data['name']  					= 		stripslashes($arrParam['name']);
			$data['detail'] 				= 		stripslashes($arrParam['detail']);
			$data['date_start']				= 		$arrParam['date_start'];
			$data['date_end']				= 		$arrParam['date_end'];
			$data['date_finish']			= 		$date_finish;
			$data['duration']				= 		$arrParam['duration'];
			$data['modified']				= 		@date("Y-m-d H:i:s");
			$data['modified_by']			= 		$arrParam['user_info']['id'];
			$data['trangthai']				= 		$arrParam['trangthai'];
			$data['type']					= 		$arrParam['type'];
			$data['color']					= 		$arrParam['color'];
			$data['customer_ids']			= 		$customer_ids;
			if($arrParam['parent'] != 0)
				$data['percent']				= 		$arrParam['percent'] / 100;
			
			$this->db->update($this->_table,$data);
			$this->db->flush_cache();
			
			$tblRelation = $this->model_load_model('MTasksRelation');
			$tblRelation->deleteItem(array('cid'=>array($arrParam['id'])), array('task'=>'delete-multi'));

			$xemArr = array();
			if(isset($arrParam['xem']))
				$xemArr = $arrParam['xem'];
		
			$implementArr = array();
			if(isset($arrParam['implement']))
				$implementArr = $arrParam['implement'];
		
			$create_taskArr = array();
			if(isset($arrParam['create_task']))
				$create_taskArr = $arrParam['create_task'];
		
			$pheduyet_taskArr = array();
			if(isset($arrParam['pheduyet_task']))
				$pheduyet_taskArr = $arrParam['pheduyet_task'];
			
			$progress_taskArr = array();
			if(isset($arrParam['progress_task']))
				$progress_taskArr = $arrParam['progress_task'];

			$created_byArr = array($arrParam['created_by']);

			$array = $this->do_relation_information($lastId, $xemArr, $implementArr, $create_taskArr, $pheduyet_taskArr, $progress_taskArr, $created_byArr);

			if(!empty($array)) {
				$this->db->insert_batch('task_user_relations', $array);
			}

			$this->db->flush_cache();
			
		}elseif($options['task'] == 'quick-update') {
			$this->db->where("id",$arrParam['id']);
			$data['date_start']			= 		$arrParam['date_start'];
			$data['date_end']			= 		$arrParam['date_end'];
			$data['duration']			= 		$arrParam['duration'];
				
			$this->db->update($this->_table,$data);
				
			$this->db->flush_cache();
				
			$lastId = $arrParam['id'];
		}elseif($options['task'] == 'pheduyet') {
			$this->db->where("id",$arrParam['id']);
			$data['pheduyet']			= 		1;
				
			$this->db->update($this->_table,$data);
				
			$this->db->flush_cache();
				
			$lastId = $arrParam['id'];
		}elseif($options['task'] == 'update-tiendo') {
			$this->db->where("id",$arrParam['id']);

			$data['trangthai']			= 		$arrParam['trangthai'];
			$data['prioty']				= 		$arrParam['prioty'];
			if($arrParam['progress'] != -1) 
				$data['progress']			= 		$arrParam['progress'] / 100;

			if($arrParam['progress'] == 100)
				$data['date_finish'] = @date("Y-m-d H:i:s");
			else
				$data['date_finish'] = '0000/00/00 00:00:00';

			$this->db->update($this->_table,$data);
				
			$this->db->flush_cache();
				
			$lastId = $arrParam['id'];

		}elseif($options['task'] == 'update-progress') {
			$this->db->where("id",$arrParam['id']);

			$data['progress']			= 		$arrParam['progress'] / 100;

			if($arrParam['progress'] == 100)
				$data['date_finish'] = @date("Y-m-d H:i:s");
			else
				$data['date_finish'] = '0000/00/00 00:00:00';

			$this->db->update($this->_table,$data);
				
			$this->db->flush_cache();
				
			$lastId = $arrParam['id'];
		}

		return $lastId;
	}
	
	public function itemSelectBox($arrParams = null, $options = null) {
		if($options == null) {
			$this->db->select("t.id, t.name, t.level")
					->from('tasks as t')
					->where('t.lft >= ' . $arrParams['lft'] . ' AND rgt <= ' . $arrParams['rgt'])
					->where('t.project_id', $arrParams['project_id']);
			
			$query = $this->db->get();
			$result = $query->result_array();
			$this->db->flush_cache();
			if(!empty($result)) {
				foreach($result as &$val) {
					$val['name'] = str_repeat('-', $val['level']) . ' ' . $val['name'];
				}
			}
		}
		
		return $result;
	}

	public function listItem($arrParams = null, $options = null) {
		$paginator = $arrParams['paginator'];
		if($options == null) {
			$flagAll = $this->checkAllPermission();
			
			// không có toàn quyền
			if($flagAll == false) {
				$project_ids = $this->getProjectRelation();	
			}
			
			$this->db->select("id")
				     ->from($this->_table)
					 ->where('parent = 0')
					 ->order_by("prioty",'ASC')
				     ->order_by('sort', 'ASC');
			
			if(!empty($project_ids))
				$this->where('project_id IN ('.implode(', ', $project_ids).')');
			
			if(!empty($arrParams['keywords'])) {
				$this->db->where('name LIKE \'%'.$arrParams['keywords'].'%\'');
			}
			
			$page = (empty($arrParams['start'])) ? 1 : $arrParams['start'];
			$this->db->limit($paginator['per_page'],($page - 1)*$paginator['per_page']);

			$query = $this->db->get();
			
			$project_ids = array();
			
			$resultTmp = $query->result_array();

			$this->db->flush_cache();
			
			if(!empty($resultTmp)) {
				foreach($resultTmp as $val)
					$project_ids[] = $val['id'];
			}
		
			if(!empty($project_ids)) {
				//danh sách tasks
				$project_ids = array_unique($project_ids);
				if(!empty($project_ids)) {
					$this->db->select("DATE_FORMAT(date_start, '%d-%m-%Y') as start_date", FALSE);
					$this->db->select("DATE_FORMAT(date_end, '%d-%m-%Y') as end_date", FALSE);
					$this->db->select("DATE_FORMAT(date_finish, '%d-%m-%Y') as finish_date", FALSE);
					$this->db->select("id, name as text, name, duration, percent, progress, level, parent, type, project_id, lft, rgt, created, pheduyet, color, prioty, trangthai")
							 ->from($this->_table)
							 ->where('project_id IN ('.implode(', ', $project_ids).')')
							 ->order_by("lft",'ASC');
					
					$query 		   = $this->db->get();
					
					$res_tmp     = $query->result_array();
					
					$this->db->flush_cache();

					$task_list_tmp = array();
	
					if(!empty($res_tmp)) {
						foreach($res_tmp as $val){
							$resultTmp[$val['id']] = $val;
						}

						$stt = 1;
						foreach($project_ids as $project_id) {
							$taskTmp 	     = $resultTmp[$project_id];
							$taskTmp['order'] = $stt;
							
							$task_list_tmp[$project_id] = $taskTmp;
							$task_ids[] 			    = $val['id'];
							$stt = $stt + 1;

							unset($resultTmp[$project_id]);
							if(!empty($resultTmp)) {
								foreach($resultTmp as $val) {
									if($val['project_id'] == $project_id){
										$taskTmp	     = $resultTmp[$val['id']];
										$taskTmp['order'] = $stt;
										
										$task_list_tmp[$val['id']] = $taskTmp;
										$task_ids[] 			   = $val['id'];
										
										unset($resultTmp[$val['id']]);
										$stt = $stt + 1;
									}
								}	
							}
						}
					}
				}else
					$task_list_tmp = array();
			}

			$task_list = array();
			if(!empty($task_list_tmp)) {
				// task replation
				$resultTmp = $this->getUsersRelation($task_ids);
		
				$implement_ids = $create_task_ids = $is_xem_ids = $task_implements = array();
				if(!empty($resultTmp)) {
					foreach($resultTmp as $val) {
						if($val['is_implement'] == 1) {
							$implement_ids[] = $val['task_id'];
							$task_implements[$val['task_id']][] = $val['user_id'];
						}

						if($val['is_create_task'] == 1)
							$create_task_ids[] = $val['task_id'];
				
						if($val['is_xem'] == 1)
							$is_xem_ids[] = $val['task_id'];
				
						$user_ids[] = $val['user_id'];
					}
				}

				//danh sách users
				if(!empty($user_ids)) {
					$userTable = $this->model_load_model('MTaskUser');
					$usersInfo = $userTable->getItems(array('user_ids'=>$user_ids));
				}
				
				//
				$implement = array();

				foreach($project_ids as $project_id) {
					$task_list[$project_id] = $task_list_tmp[$project_id];
					unset($task_list_tmp[$project_id]);
					
					foreach($task_list_tmp as $val) {
						if($val['project_id'] == $project_id) {
							$task_list[$val['id']] = $val;
							unset($task_list_tmp[$val['id']]);
						}	
					}
				}
	
				foreach($task_list as &$val) {
					$implement_origin = array();
					if($val['level'] == 0 || $val['level'] == 1) {
						$val['open'] = true;
					}
					
					$val['text'] = $val['text'] . ' ('.($val['progress'] * 100).'%)';
					if($val['pheduyet'] == 0)
						$val['text'] = $val['text'] . ' - Chưa phê duyệt';
					
					if(isset($task_implements[$val['id']]))
						$implement_origin = $task_implements[$val['id']];
					
					$implement 		  = $implement_origin;
	
					if($val['parent'] > 0)
						$implement = array_merge($implement, $task_list[$val['parent']]['implement_ids']);
				
					$implement = array_unique($implement);

					$val['implement_ids'] = $implement;
					if(!empty($val['implement_ids'])){
						foreach($val['implement_ids'] as $user_id) {
							if(in_array($user_id, $implement_origin))
								$val['implement'][] = '<strong>'.$usersInfo[$user_id]['username'].'</strong>';
							else
								$val['implement'][] = $usersInfo[$user_id]['username'];
						}
						
						$val['implement'] = implode(', ', $val['implement']);
					}
					
					//tooltip information
					$tyle = '';
					if($val['parent'] > 0)
						$tyle = ($val['percent'] * 100) . '% <strong> '.$task_list[$val['parent']]['name'].'</strong>';
					
					$date_time   = $val['start_date'] . ' đến ' . $val['end_date'];
					$date_finish = '';
					if($val['trangthai'] == 0 || $val['trangthai'] == 1){	// chưa thực hiên + đang thực hiện
						$now        = date('Y-m-d', strtotime(date("d-m-Y")));
						$date_end   = date('Y-m-d', strtotime($val['end_date']));

						$datediff 	= strtotime($now) - strtotime($date_end);
						$duration 	= floor($datediff/(60*60*24));
						if($duration <= 0)
							$date_time = $date_time . ' (Còn '.abs($duration).' ngày)';
						else{
							$date_time 	  = $date_time . ' (Quá '.abs($duration).' ngày)';
							$val['color'] = '#c90d2f';
						}
								
					}elseif($val['trangthai'] == 2) {// hoàn thành
						$end_date      = date('Y-m-d', strtotime($val['end_date']));
						$finish_date   = date('Y-m-d', strtotime($val['finish_date']));

						$datediff 	= strtotime($end_date) - strtotime($finish_date);
						$duration 	= floor($datediff/(60*60*24));
						
						if($duration < 0){
							$date_finish  = $val['finish_date'] . ' (Trễ '.abs($duration).' ngày)';
							$val['color'] = '#516e47';
						}elseif($duration > 0){
							$val['color'] = '#12e841';
							$date_finish  = $val['finish_date'] . ' (Sớm '.abs($duration).' ngày)';
						}else{
							$val['color'] = '#12e841';
							$date_finish  = $val['finish_date'];
						}

					}elseif($val['trangthai'] == 3) {
						$val['color'] = '#e0d91c';
						$date_time    = $date_time . ' (Đóng/ Dừng)';
					}elseif($val['trangthai'] == 4) {
						$val['color'] = '#303020';
						$date_time    = $date_time . ' (Không thực hiện)';
					}

					$tooltip   = array();
					if(!empty($tyle))
						$tooltip[] = '<strong>Tỷ lệ: </strong>'.$tyle;
					$tooltip[] = '<strong>Thời gian: </strong>'.$date_time;
					
					if(!empty($date_finish))
						$tooltip[] = '<strong>Hoàn thành: </strong>'.$date_finish;
					
					$tooltip[] = '<strong>Phụ trách</strong>: '.$val['implement'];
					$val['tooltip'] = implode('<br />', $tooltip);
				}
			}
			if(!empty($task_list)) {
				//allow task
				$allow_tasks = $deny_task = $drag_task = $click_task = array();
				if($flagAll == true) {
					$allow_tasks = $drag_task = $click_task = array_keys($task_list);
				}else {
					// cập nhật project
					if(in_array('update_project', $this->_task_permission)) {
						$allow_tasks = $drag_task = $click_task = $project_ids;
					}else
						$deny_task[] = "0";
					
					// quyền cập nhật tất cả các task
					if(in_array('update_all_task', $this->_task_permission)) {
						$tmp = array();
						foreach($resultTmp as $val)	{
							if($val['parent'] != 0 && $val['type'] == 1)
								$tmp[] = $val['id'];
						}
					
						$allow_tasks =  array_merge($allow_tasks, $tmp);
						$drag_task 	 =  array_merge($drag_task, $tmp);
						$click_task  =  array_merge($click_task, $tmp);
					}
					
					// quyền cập nhật trên nhánh
					if(!empty($implement_ids)) {
						foreach($task_list as $task_id => $task_detail) {
							foreach($implement_ids as $t_id) {
								if($task_detail['lft'] >=  $task_list[$t_id]['lft'] && $task_detail['rgt'] <=  $task_list[$t_id]['rgt']){
									if(in_array('update_brand_task', $this->_task_permission)) 
										$allow_tasks[] = $task_detail['id'];
									
									$click_task[] = $task_detail['id'];
								}
							}
						}
					}

					// create_task
					if(!empty($create_task_ids)) {
						foreach($task_list as $task_id => $task_detail) {
							foreach($create_task_ids as $t_id) {
								if($task_detail['lft'] >=  $task_list[$t_id]['lft'] && $task_detail['rgt'] <=  $task_list[$t_id]['rgt']){
									$allow_tasks[] = $task_detail['id'];
								}
							}
						}
					}
					
					// is xem
					if(!empty($is_xem_ids)) {
						foreach($task_list as $task_id => $task_detail) {
							foreach($is_xem_ids as $t_id) {
								if($task_detail['lft'] >=  $task_list[$t_id]['lft'] && $task_detail['rgt'] <=  $task_list[$t_id]['rgt']){
									$click_task[] = $task_detail['id'];
								}
							}
						}
					}
		
					foreach($task_list as $value) {
						if(!in_array($value['id'], $allow_tasks))
							$deny_task[] = $value['id'];
					}

					$drag_task = array_unique($drag_task);
				}
	
				foreach($task_list as &$val){
					if(!in_array($val['id'], $click_task))
						$val['color'] = '#cccccc';
				}	
				$task_list = array_merge($task_list, array());
				
				$result = array('ketqua'=>$task_list, 'deny'=>$deny_task, 'drag_task'=>$drag_task);
			}else {
				$deny_task = array();
				if(!in_array('update_project', $this->_task_permission))
					$deny_task[] = "0";
				
				$result = array('ketqua'=>array(), 'deny'=>$deny_task, 'drag_task'=>array());
			}
			
		}elseif($options['task'] == 'public-list') {
			$prioty_arr    = array('Rất cao', 'Cao', 'Trung bình', 'Thấp', 'Rất thấp');
			
			$this->db->select("DATE_FORMAT(t.modified, '%d/%m/%Y %H:%i:%s') as modified", FALSE);
			$this->db->select("t.*, e.username")
				     ->from($this->_table . ' AS t')
				     -> join('employees AS e', 'e.id = t.modified_by', 'left')
					 ->where('t.parent = 0');

			$page = (empty($arrParams['start'])) ? 1 : $arrParams['start'];
			$this->db->limit($paginator['per_page'],($page - 1)*$paginator['per_page']);
			
			if(!empty($arrParams['keywords'])) {
				$this->db->where('t.name LIKE \'%'.$arrParams['keywords'].'%\'');
			}
			
			if(!empty($arrParams['col']) && !empty($arrParams['order'])){
				$col   = $this->_fields[$arrParams['col']];
				$order = $arrParams['order'];
			
				$this->db->order_by($col, $order)
						 ->order_by('t.sort', 'ASC');
			}else {
				$this->db->order_by('t.prioty', 'ASC')
						 ->order_by('t.sort', 'ASC');
			} 
			
			$query = $this->db->get();
			
			$result = $query->result_array();
			if(!empty($result)) {
				foreach($result as &$val)
					$val['prioty'] = $prioty_arr[$val['prioty']];
			}
			$this->db->flush_cache();
		}elseif($options['task'] == 'grid-project') {
			$user_ids = array();
			$flagAll = $this->checkAllPermission();
			if($flagAll == false) {
				//project liên quan
				$project_ids = $this->getProjectRelation();	
			}

			$this->db->select("DATE_FORMAT(date_start, '%d-%m-%Y') as start_date", FALSE);
			$this->db->select("DATE_FORMAT(date_end, '%d-%m-%Y') as end_date", FALSE);
			$this->db->select("DATE_FORMAT(date_finish, '%d-%m-%Y') as finish_date", FALSE);
			$this->db->select("id, name as text, name, duration, percent, progress, level, parent, type, project_id, lft, rgt, created, pheduyet, color, prioty, trangthai")
					 ->from($this->_table)
					 ->where('parent = 0');

			if(!empty($arrParams['col']) && !empty($arrParams['order'])){
					$col   = $this->_fields[$arrParams['col']];
					$order = $arrParams['order'];
						
					$this->db->order_by($col, $order);
			}else {
					$this->db ->order_by("prioty",'ASC')
					 		  ->order_by('sort', 'ASC');
			}

			if(!empty($project_ids))
				$this->where('project_id IN ('.implode(', ', $project_ids).')');
			
			if(!empty($arrParams['keywords'])) {
				$this->db->where('name LIKE \'%'.$arrParams['keywords'].'%\'');
			}

			$page = (empty($arrParams['start'])) ? 1 : $arrParams['start'];
			$this->db->limit($paginator['per_page'],($page - 1)*$paginator['per_page']);
			
			$query = $this->db->get();

			$result = $query->result_array();
			if(!empty($result)) {	
				foreach($result as $val)
					$task_ids[] = $val['id'];

				// lấy ra user liên quan
				$resultTmp = $this->getUsersRelation($task_ids); 

				$task_implements = $user_ids = array();
				if(!empty($resultTmp)) {
					foreach($resultTmp as $val) {
						if($val['is_implement'] == 1) {
							$implement_ids[] = $val['task_id'];
							$task_implements[$val['task_id']][] = $val['user_id'];
						}

						$user_ids[] = $val['user_id'];
					}
				}

				//danh sách users
				if(!empty($user_ids)) {
					$userTable = $this->model_load_model('MTaskUser');
					$usersInfo = $userTable->getItems(array('user_ids'=>$user_ids));
				}



				foreach($result as &$val) {
					$val['implement_ids'] = $task_implements[$val['id']];
					if(!empty($val['implement_ids'])){
						foreach($val['implement_ids'] as $user_id){

							$val['implement'][] = '<strong>'.$usersInfo[$user_id]['username'].'</strong>';
						}

						$val['implement'] = implode(', ', $val['implement']);
					}

				}
			}

		}elseif($options['task'] == 'grid-list') {
			$user_ids = array();
			$flagAll = $this->checkAllPermission();
			if($flagAll == false) {
				//project liên quan
				$project_ids = $this->getProjectRelation();	
			}
			
			$this->db->select("DATE_FORMAT(date_start, '%d-%m-%Y') as start_date", FALSE);
			$this->db->select("DATE_FORMAT(date_end, '%d-%m-%Y') as end_date", FALSE);
			$this->db->select("DATE_FORMAT(date_finish, '%d-%m-%Y') as finish_date", FALSE);
			$this->db->select("id, name as text, name, duration, percent, progress, level, parent, type, project_id, lft, rgt, created, pheduyet, color, prioty, trangthai")
					 ->from($this->_table)
					 ->where('parent = 0');

			if(!empty($arrParams['col']) && !empty($arrParams['order'])){
					$col   = $this->_fields[$arrParams['col']];
					$order = $arrParams['order'];
						
					$this->db->order_by($col, $order);
			}else {
					$this->db ->order_by("prioty",'ASC')
					 		  ->order_by('sort', 'ASC');
			}
			
			if(!empty($project_ids))
				$this->where('project_id IN ('.implode(', ', $project_ids).')');
			
			if(!empty($arrParams['project_keywords'])) {
				$this->db->where('name LIKE \'%'.$arrParams['project_keywords'].'%\'');
			}
			
			$page = (empty($arrParams['start'])) ? 1 : $arrParams['start'];
			$this->db->limit($paginator['per_page'],($page - 1)*$paginator['per_page']);
			
			$query = $this->db->get();
				
			$project_ids = array();
				
			$resultTmp = $query->result_array();
			
			$this->db->flush_cache();
			
			$project_items = array();
			if(!empty($resultTmp)) {
				foreach($resultTmp as $val){
					$project_ids[]  		   = $val['id'];
					$project_items[$val['id']] = $val;
					$task_ids[] 			   = $val['id'];
				}	
			}

			$result = array();
			if(!empty($project_ids)) {
				foreach($project_ids as $project_id) {
					$result[$project_id] = $project_items[$project_id];
					$this->db->select("DATE_FORMAT(t.date_start, '%d-%m-%Y') as start_date", FALSE);
					$this->db->select("DATE_FORMAT(t.date_end, '%d-%m-%Y') as end_date", FALSE);
					$this->db->select("DATE_FORMAT(t.date_finish, '%d-%m-%Y') as finish_date", FALSE);
					$this->db->select("t.id, t.name, t.name, t.duration, t.percent, t.progress, t.level, t.parent, t.type, t.project_id, t.lft, t.rgt, t.created, t.pheduyet, t.color, t.prioty, t.trangthai")
							 ->from($this->_table . ' AS t');	
					
					if($flagAll == false) {
						$this->db->join('task_user_relations as r', 't.id = r.task_id AND r.user_id = ' . $this->_id_admin);
					}
					
					$this->db->where('t.project_id', $project_id)
							 ->where('t.parent != 0')
							 ->order_by("t.lft",'ASC');

					$query 		   = $this->db->get();
					$resultTmp     = $query->result_array();	
					$this->db->flush_cache();

					if(!empty($resultTmp)) {
						foreach($resultTmp as $val){
							$result[$val['id']] 	   = $val;
							$task_ids[] 			   = $val['id'];
						}
							
					}
				}
			}

			if(!empty($result)) {
				// lấy ra user liên quan
				$resultTmp = $this->getUsersRelation($task_ids);

				$task_implements = array();
				if(!empty($resultTmp)) {
					foreach($resultTmp as $val) {
						if($val['is_implement'] == 1) {
							$implement_ids[] = $val['task_id'];
							$task_implements[$val['task_id']][] = $val['user_id'];
						}

						$user_ids[] = $val['user_id'];
					}
				}

				//danh sách users
				if(!empty($user_ids)) {
					$userTable = $this->model_load_model('MTaskUser');
					$usersInfo = $userTable->getItems(array('user_ids'=>$user_ids));
				}

				foreach($result as &$val) {
					$implement_origin = array();

					if(isset($task_implements[$val['id']]))
						$implement_origin = $task_implements[$val['id']];
					
					$implement 		  = $implement_origin;

					if($val['parent'] > 0)
						$implement = array_merge($implement, $result[$val['parent']]['implement_ids']);
				
					$implement = array_unique($implement);
					$val['implement_ids'] = $implement;
					if(!empty($val['implement_ids'])){
						foreach($val['implement_ids'] as $user_id) {
							if(in_array($user_id, $implement_origin))
								$val['implement'][] = '<strong>'.$usersInfo[$user_id]['username'].'</strong>';
							else
								$val['implement'][] = $usersInfo[$user_id]['username'];
						}
						
						$val['implement'] = implode(', ', $val['implement']);
					}

					if($val['trangthai'] == 0 || $val['trangthai'] == 1){	// chưa thực hiên + đang thực hiện
						$now        = date('Y-m-d', strtotime(date("d-m-Y")));
						$date_end   = date('Y-m-d', strtotime($val['end_date']));

						$datediff 	= strtotime($now) - strtotime($date_end);
						$duration 	= floor($datediff/(60*60*24));
						if($duration <= 0)
							$val['note'] = 'Còn '.abs($duration).' ngày';
						else{
							$val['note']  =  'Quá '.abs($duration).' ngày';
							$val['color'] = '#c90d2f';
						}
								
					}elseif($val['trangthai'] == 2) {// hoàn thành
						$end_date      = date('Y-m-d', strtotime($val['end_date']));
						$finish_date   = date('Y-m-d', strtotime($val['finish_date']));

						$datediff 	= strtotime($end_date) - strtotime($finish_date);
						$duration 	= floor($datediff/(60*60*24));
						
						if($duration < 0){
							$val['note']  = 'Trễ '.abs($duration).' ngày';
							$val['color'] = '#516e47';
						}elseif($duration > 0){
							$val['color'] = '#12e841';
							$val['note']  = 'Sớm '.abs($duration).' ngày';
						}else{
							$val['color'] = '#12e841';
						}

					}elseif($val['trangthai'] == 3) {
						$val['color'] = '#e0d91c';
					}elseif($val['trangthai'] == 4) {
						$val['color'] = '#303020';
					}
				}
			}
		}
		
		return $result;
	}
	
	public function getInfo($arrParam = null, $options = null) {
		if($options['task'] == 'create-task') {
			$this->db->select("t.id, t.created")
					->from($this->_table . ' as t')
					->where('t.lft <= ' . $arrParam['lft'] . ' AND rgt >= ' . $arrParam['rgt'])
					->where('t.project_id',$arrParam['project_id']);
			
			$query = $this->db->get();
			$resultTmp =  $query->result_array();
			$this->db->flush_cache();
			
			if(!empty($resultTmp)) {
				$task_ids = $created = array();
				foreach($resultTmp as $val) {
					$task_ids[] = $val['id'];
					$created[]  = $val['created'];
				}

				$result['task_ids'] = $task_ids;
				$result['created']  = $created;
			}else
				$result = array();

		}
		
		return $result;
	}
	
	public function getIds($arrParam = null, $options = null) {
		if($options == null) {
			$this->db->select("t.id")
					->from($this->_table . ' as t')
					->where('t.lft >= ' . $arrParam['lft'] . ' AND t.rgt <= '.$arrParam['rgt'] )
					->where('t.project_id', $arrParam['project_id']);
			
			$query = $this->db->get();	
			$resultTmp =  $query->result_array();
			

		}elseif($options['task'] == 'up-branch') {
			$this->db->select("t.id")
					 ->from($this->_table . ' as t')
					 ->where('t.lft <= ' . $arrParam['lft'] . ' AND t.rgt >= '.$arrParam['rgt'] )
					 ->where('t.project_id', $arrParam['project_id']);
				
			$query = $this->db->get();
			$resultTmp =  $query->result_array();
		}
		
		$result = array();
		if(!empty($resultTmp)) {
			foreach($resultTmp as $val)
				$result[] = $val['id'];
		}
		
		return $result;
	}
	
	public function getItems($arrParam = null, $options = null) {
		if($options['task'] == 'public-info') {
			$this->db->select("t.*")
					->from($this->_table . ' as t')
					->where('t.id IN ('.implode(', ', $arrParam['cid']).')');
				
			$this->db->select("DATE_FORMAT(t.date_start, '%d/%m/%Y') as date_start", FALSE);
			$this->db->select("DATE_FORMAT(t.date_end, '%d/%m/%Y') as date_end", FALSE);
			
			$query = $this->db->get();
			
			$resultTmp =  $query->result_array();
			$this->db->flush_cache();
			

		}elseif($options['task'] == 'by-project') {
			$this->db->select("DATE_FORMAT(t.date_start, '%d/%m/%Y') as date_start", FALSE);
			$this->db->select("DATE_FORMAT(t.date_end, '%d/%m/%Y') as date_end", FALSE);
			$this->db->select("t.*")
						->from($this->_table . ' as t')
						->where('t.project_id', $arrParam['project_id']);
			
			if(!empty($arrParam['level']))
				$this->db->where('t.level <= ' . $arrParam['level']);

			$query = $this->db->get();
				
			$resultTmp =  $query->result_array();
			$this->db->flush_cache();	
		}
		
		$result = array();
		if(!empty($resultTmp)) {
			foreach($resultTmp as $val)
				$result[$val['id']] = $val;
		}
		
		return $result;
	}

	public function getItem($arrParam = null, $options = null){
		if($options['task'] == 'public-info') {
			$this->db->select("t.*")
					 ->from($this->_table . ' as t')
					 ->where('t.id',$arrParam['id']);
			
			$this->db->select("DATE_FORMAT(t.date_start, '%d-%m-%Y') as date_start", FALSE);
			$this->db->select("DATE_FORMAT(t.date_end, '%d-%m-%Y') as date_end", FALSE);

			$query = $this->db->get();
				
			$result =  $query->row_array();
			$this->db->flush_cache();
			if($options['brand'] == 'detail' || $options['brand'] == 'full') {
				if(!empty($result)) {
					// tất cả task bao gồm task ở bên trên
					if($options['brand'] == 'full')
						$task_ids = $this->getIds(array('lft'=>$result['lft'], 'rgt'=>$result['rgt'], 'project_id'=>$result['project_id']), array('task'=>'up-branch'));
					elseif($options['brand'] == 'detail')
						$task_ids = $this->getIds(array('lft'=>$result['lft'], 'rgt'=>$result['rgt'], 'project_id'=>$result['project_id']));
				

					// file list
					$this->db->select('f.*')
							->from('task_files as f')
							->where('f.task_id IN ('.implode(',', $task_ids).')')
							->order_by('f.modified', 'DESC');
					
					$query = $this->db->get();
					$result['files'] = $query->result_array();
					
					$this->db->flush_cache();
					// end file list
					if(!empty($result['customer_ids'])) {
						$cid 		  = explode(',', $result['customer_ids']);
	
						$this->load->model('MTaskCustomers', 'MTaskCustomers');
						$result['customers'] = $this->MTaskCustomers->getItems(array('cid'=>$cid));
					}

					$this->db->select('r.*')
							->from('task_user_relations as r')
							->where('r.task_id IN ('.implode(',', $task_ids).')')
							->order_by('r.user_id', 'ASC');

					$query = $this->db->get();
					$resultTmp = $query->result_array();

					$this->db->flush_cache();
					$user_ids = array();
						
					$user_ids = array($result['created_by']);

					if(!empty($resultTmp)) {
						foreach($resultTmp as $val)
							$user_ids[] = $val['user_id'];
						
						$user_ids = array_unique($user_ids);
						$this->load->model('MTaskUser', 'MTaskUser');
						$tblUser  = $this->MTaskUser;
						$users 	  = $tblUser->getItems(array('user_ids'=>$user_ids));

						$result['created_by_name'] = $users[$result['created_by']]['username'];

						foreach($resultTmp as $val) {	
							$user_id  = $val['user_id'];
							$keywords = $val['task_id'] . '-' . $val['user_id'];
							
							if(isset($users[$user_id])) {
								if($val['is_xem'] == 1)
									$result['is_xem'][$keywords] = $users[$user_id];
								
								if($val['is_implement'] == 1)
									$result['is_implement'][$keywords] = $users[$user_id];
								
								if($val['is_create_task'] == 1)
									$result['is_create_task'][$keywords] = $users[$user_id];
								
								if($val['is_pheduyet'] == 1)
									$result['is_pheduyet'][$keywords] = $users[$user_id];
									
								if($val['is_progress'] == 1)
									$result['is_progress'][$keywords] = $users[$user_id];	
							}
						}
					}else {
						$this->load->model('MTaskUser', 'MTaskUser');
						$tblUser  = $this->MTaskUser;
						$users 	  = $tblUser->getItems(array('user_ids'=>$user_ids));
						
						$result['created_by_name'] = $users[$result['created_by']]['username'];
					}
				}
			}
		}
	
		return $result;
	}
	
	public function sort($arrParam = null, $options = null) {
		if($options == null) {
			$this->db->where("id",$arrParam['id']);
			
			$data['sort']			= 		(int)$arrParam['sort'];
			
			$this->db->update($this->_table,$data);
			
			$this->db->flush_cache();
		}
	}
	
	public function checkItemExist($id) {
		$this->db -> select('COUNT(t.id) AS totalItem')
			  	  -> from($this->_table . ' AS t')
				  -> where('t.id', $id);
			
		$query  = $this->db->get();
		$result = $query->row()->totalItem;
		$this->db->flush_cache();
		
		return $result;
	}
	
	public function getMaxPercent($parent_id, $project_id, $id = null) {
		$this->db->select("t.percent")
				 ->from($this->_table . ' as t')
				 ->where('t.parent', $parent_id)
				 ->where('t.project_id', $project_id);
		
		if($id > 0)
			$this->db->where('t.id != ' . $id);
		
		$query = $this->db->get();
		$result =  $query->result_array();
		$this->db->flush_cache();

		if(empty($result))
			$percent = 0;
		else {
			foreach($result as $value)
				$percent = $percent + 100 * $value['percent']; 
		}
		
		$percent = 100 - $percent;
		
		return $percent;
	}

	public function deleteItem($id) {
		$this->removeNode($id);
	}
	
	protected function getUsersRelation($task_ids) {
		// task replation
		$this->db->select("r.task_id, r.is_implement, r.is_create_task, r.is_pheduyet, r.is_progress, r.is_xem, r.user_id")
				 ->from('task_user_relations as r')
				 ->where('r.task_id IN ('.implode(', ', $task_ids).')');
		
		$query = $this->db->get();
		
		$resultTmp = $query->result_array();
		
		$this->db->flush_cache();
		
		return $resultTmp;
	}
	
	// support function
	protected function getProjectRelation() {
		//project liên quan
		$sql = 'SELECT t.id, t.project_id
				FROM ' . $this->db->dbprefix($this->_table).' AS t
				WHERE t.id IN (SELECT task_id FROM '.$this->db->dbprefix(task_user_relations).' WHERE user_id = '.$this->_id_admin.')'
			 .' ORDER BY t.prioty ASC, t.id DESC';
		
		$query = $this->db->query($sql);
		$resultTmp = $query->result_array();
		$project_ids = array();
		if(!empty($resultTmp)) {
			foreach($resultTmp as $val)
				$project_ids[] = $val['project_id'];
		}
			
		$this->db->flush_cache();
		
		return $project_ids;
	}
	protected function checkAllPermission() {
		$flagAll = true;
		if(!(in_array('update_project', $this->_task_permission) && in_array('update_all_task', $this->_task_permission)))
			$flagAll = false;
		
		return $flagAll;
	}
	
	protected function do_relation_information($lastId, $xemArr, $implementArr, $create_taskArr, $pheduyet_taskArr, $progress_taskArr, $created_byArr) {
		$array = array();
		if(isset($xemArr)) {
			foreach($xemArr as $user_id) {
				$tmp = array();
				$tmp['task_id'] = $lastId;
				$tmp['user_id'] = $user_id;
		
				$tmp['is_xem'] = 1;
				if(($key = array_search($user_id, $xemArr)) !== false) {
					unset($xemArr[$key]);
				}
		
				if(in_array($user_id, $implementArr)){
					$tmp['is_implement'] = 1;
					if(($key = array_search($user_id, $implementArr)) !== false) {
						unset($implementArr[$key]);
					}
				}else
					$tmp['is_implement'] = 0;
		
				if(in_array($user_id, $create_taskArr)){
					$tmp['is_create_task'] = 1;
					if(($key = array_search($user_id, $create_taskArr)) !== false) {
						unset($create_taskArr[$key]);
					}
				}else
					$tmp['is_create_task'] = 0;
		
		
				if(in_array($user_id, $pheduyet_taskArr)){
					$tmp['is_pheduyet'] = 1;
					if(($key = array_search($user_id, $pheduyet_taskArr)) !== false) {
						unset($pheduyet_taskArr[$key]);
					}
				}else
					$tmp['is_pheduyet'] = 0;
					
				if(in_array($user_id, $progress_taskArr)){
					$tmp['is_progress'] = 1;
					if(($key = array_search($user_id, $progress_taskArr)) !== false) {
						unset($progress_taskArr[$key]);
					}
				}else
					$tmp['is_progress'] = 0;
		
		
				if(in_array($user_id, $created_byArr)){
					$tmp['is_created'] = 1;
					if(($key = array_search($user_id, $created_byArr)) !== false) {
						unset($created_byArr[$key]);
					}
				}else
					$tmp['is_created'] = 0;
		
				$tmp['created']				= 		@date("Y-m-d H:i:s");
		
				$array[] = $tmp;
			}
		}
		
		if(!empty($implementArr)) {
			foreach($implementArr as $user_id) {
				$tmp = array();
				$tmp['task_id'] = $lastId;
				$tmp['user_id'] = $user_id;
		
				if(in_array($user_id, $xemArr)){
					$tmp['is_xem'] = 1;
					if(($key = array_search($user_id, $xemArr)) !== false) {
						unset($xemArr[$key]);
					}
				}else
					$tmp['is_xem'] = 0;
		
				$tmp['is_implement'] = 1;
				if(($key = array_search($user_id, $implementArr)) !== false) {
					unset($implementArr[$key]);
				}
		
				if(in_array($user_id, $create_taskArr)){
					$tmp['is_create_task'] = 1;
					if(($key = array_search($user_id, $create_taskArr)) !== false) {
						unset($create_taskArr[$key]);
					}
				}else
					$tmp['is_create_task'] = 0;
		
		
				if(in_array($user_id, $pheduyet_taskArr)){
					$tmp['is_pheduyet'] = 1;
					if(($key = array_search($user_id, $pheduyet_taskArr)) !== false) {
						unset($pheduyet_taskArr[$key]);
					}
				}else
					$tmp['is_pheduyet'] = 0;
					
				if(in_array($user_id, $progress_taskArr)){
					$tmp['is_progress'] = 1;
					if(($key = array_search($user_id, $progress_taskArr)) !== false) {
						unset($progress_taskArr[$key]);
					}
				}else
					$tmp['is_progress'] = 0;
		
		
				if(in_array($user_id, $created_byArr)){
					$tmp['is_created'] = 1;
					if(($key = array_search($user_id, $created_byArr)) !== false) {
						unset($created_byArr[$key]);
					}
				}else
					$tmp['is_created'] = 0;
		
				$tmp['created']				= 		@date("Y-m-d H:i:s");
		
				$array[] = $tmp;
			}
		}
		
		if(!empty($create_taskArr)) {
			foreach($create_taskArr as $user_id) {
				$tmp = array();
				$tmp['task_id'] = $lastId;
				$tmp['user_id'] = $user_id;
		
				if(in_array($user_id, $xemArr)){
					$tmp['is_xem'] = 1;
					if(($key = array_search($user_id, $xemArr)) !== false) {
						unset($xemArr[$key]);
					}
				}else
					$tmp['is_xem'] = 0;
		
		
				if(in_array($user_id, $implementArr)){
					$tmp['is_implement'] = 1;
					if(($key = array_search($user_id, $implementArr)) !== false) {
						unset($implementArr[$key]);
					}
				}else
					$tmp['is_implement'] = 0;
		
				$tmp['is_create_task'] = 1;
				if(($key = array_search($user_id, $create_taskArr)) !== false) {
					unset($create_taskArr[$key]);
				}
		
				if(in_array($user_id, $pheduyet_taskArr)){
					$tmp['is_pheduyet'] = 1;
					if(($key = array_search($user_id, $pheduyet_taskArr)) !== false) {
						unset($pheduyet_taskArr[$key]);
					}
				}else
					$tmp['is_pheduyet'] = 0;
					
				if(in_array($user_id, $progress_taskArr)){
					$tmp['is_progress'] = 1;
					if(($key = array_search($user_id, $progress_taskArr)) !== false) {
						unset($progress_taskArr[$key]);
					}
				}else
					$tmp['is_progress'] = 0;
		
		
				if(in_array($user_id, $created_byArr)){
					$tmp['is_created'] = 1;
					if(($key = array_search($user_id, $created_byArr)) !== false) {
						unset($created_byArr[$key]);
					}
				}else
					$tmp['is_created'] = 0;
		
				$tmp['created']				= 		@date("Y-m-d H:i:s");
		
				$array[] = $tmp;
			}
		}
		
		if(!empty($pheduyet_taskArr)) {
			foreach($pheduyet_taskArr as $user_id) {
				$tmp = array();
				$tmp['task_id'] = $lastId;
				$tmp['user_id'] = $user_id;
		
				if(in_array($user_id, $xemArr)){
					$tmp['is_xem'] = 1;
					if(($key = array_search($user_id, $xemArr)) !== false) {
						unset($xemArr[$key]);
					}
				}else
					$tmp['is_xem'] = 0;
		
		
				if(in_array($user_id, $implementArr)){
					$tmp['is_implement'] = 1;
					if(($key = array_search($user_id, $implementArr)) !== false) {
						unset($implementArr[$key]);
					}
				}else
					$tmp['is_implement'] = 0;
		
				if(in_array($user_id, $create_taskArr)){
					$tmp['is_create_task'] = 1;
					if(($key = array_search($user_id, $create_taskArr)) !== false) {
						unset($create_taskArr[$key]);
					}
				}else
					$tmp['is_create_task'] = 0;
		
				$tmp['is_pheduyet'] = 1;
				if(($key = array_search($user_id, $pheduyet_taskArr)) !== false) {
					unset($pheduyet_taskArr[$key]);
				}
					
				if(in_array($user_id, $progress_taskArr)){
					$tmp['is_progress'] = 1;
					if(($key = array_search($user_id, $progress_taskArr)) !== false) {
						unset($progress_taskArr[$key]);
					}
				}else
					$tmp['is_progress'] = 0;
		
		
				if(in_array($user_id, $created_byArr)){
					$tmp['is_created'] = 1;
					if(($key = array_search($user_id, $created_byArr)) !== false) {
						unset($created_byArr[$key]);
					}
				}else
					$tmp['is_created'] = 0;
		
				$tmp['created']				= 		@date("Y-m-d H:i:s");
					
				$array[] = $tmp;
			}
		}
			
		if(!empty($progress_taskArr)) {
			foreach($progress_taskArr as $user_id) {
				$tmp = array();
				$tmp['task_id'] = $lastId;
				$tmp['user_id'] = $user_id;
					
				if(in_array($user_id, $xemArr)){
					$tmp['is_xem'] = 1;
					if(($key = array_search($user_id, $xemArr)) !== false) {
						unset($xemArr[$key]);
					}
				}else
					$tmp['is_xem'] = 0;
					
					
				if(in_array($user_id, $implementArr)){
					$tmp['is_implement'] = 1;
					if(($key = array_search($user_id, $implementArr)) !== false) {
						unset($implementArr[$key]);
					}
				}else
					$tmp['is_implement'] = 0;
					
				if(in_array($user_id, $create_taskArr)){
					$tmp['is_create_task'] = 1;
					if(($key = array_search($user_id, $create_taskArr)) !== false) {
						unset($create_taskArr[$key]);
					}
				}else
					$tmp['is_create_task'] = 0;
					
				if(in_array($user_id, $pheduyet_taskArr)){
					$tmp['is_pheduyet'] = 1;
					if(($key = array_search($user_id, $pheduyet_taskArr)) !== false) {
						unset($pheduyet_taskArr[$key]);
					}
				}else
					$tmp['is_pheduyet'] = 0;
					
				$tmp['is_progress'] = 1;
					
				if(($key = array_search($user_id, $progress_taskArr)) !== false) {
					unset($progress_taskArr[$key]);
				}
		
		
				if(in_array($user_id, $created_byArr)){
					$tmp['is_created'] = 1;
					if(($key = array_search($user_id, $created_byArr)) !== false) {
						unset($created_byArr[$key]);
					}
				}else
					$tmp['is_created'] = 0;
		
				$tmp['created']				= 		@date("Y-m-d H:i:s");
					
				$array[] = $tmp;
			}
		}
		
		if(!empty($created_byArr)) {
			foreach($created_byArr as $user_id) {
				$tmp = array();
				$tmp['task_id'] = $lastId;
				$tmp['user_id'] = $user_id;
					
				if(in_array($user_id, $xemArr)){
					$tmp['is_xem'] = 1;
					if(($key = array_search($user_id, $xemArr)) !== false) {
						unset($xemArr[$key]);
					}
				}else
					$tmp['is_xem'] = 0;
					
					
				if(in_array($user_id, $implementArr)){
					$tmp['is_implement'] = 1;
					if(($key = array_search($user_id, $implementArr)) !== false) {
						unset($implementArr[$key]);
					}
				}else
					$tmp['is_implement'] = 0;
					
				if(in_array($user_id, $create_taskArr)){
					$tmp['is_create_task'] = 1;
					if(($key = array_search($user_id, $create_taskArr)) !== false) {
						unset($create_taskArr[$key]);
					}
				}else
					$tmp['is_create_task'] = 0;
					
				if(in_array($user_id, $pheduyet_taskArr)){
					$tmp['is_pheduyet'] = 1;
					if(($key = array_search($user_id, $pheduyet_taskArr)) !== false) {
						unset($pheduyet_taskArr[$key]);
					}
				}else
					$tmp['is_pheduyet'] = 0;
		
				if(in_array($user_id, $progress_taskArr)){
					$tmp['is_progress'] = 1;
					if(($key = array_search($user_id, $progress_taskArr)) !== false) {
						unset($progress_taskArr[$key]);
					}
				}else
					$tmp['is_progress'] = 0;
		
					
				$tmp['is_created'] = 1;
					
				if(($key = array_search($user_id, $created_byArr)) !== false) {
					unset($created_byArr[$key]);
				}
		
		
				$tmp['created']				= 		@date("Y-m-d H:i:s");
					
				$array[] = $tmp;
			}
		}
		
		return $array;
	}
	
	function model_load_model($model_name)
	{
		$CI =& get_instance();
		$CI->load->model($model_name);
		return $CI->$model_name;
	}
	
	public function test() {
		$sql = 'DELETE FROM phppos_tasks WHERE id != 1';
		$this->db->query($sql);
		
		echo '<br />'.$sql = 'DELETE FROM phppos_task_user_relations WHERE task_id != 1';
		$this->db->query($sql);
		
		echo '<br />'.$sql = 'DELETE FROM phppos_task_progress';
		$this->db->query($sql);
		
		echo '<br />'.$sql = 'UPDATE phppos_tasks SET progress = 0';
		$this->db->query($sql);
	}
}