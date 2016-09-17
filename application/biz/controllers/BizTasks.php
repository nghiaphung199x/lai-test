<?php
require_once (APPPATH . "controllers/Secure_area.php");
class BizTasks extends Secure_area 
{
	protected $_data;
	function __construct($module_id=null)
	{
		parent::__construct();
		$get = $this->input->get();
		if(empty($get))
			$get = array();
		
		$post = $this->input->post();
		if(empty($post))
			$post = array();
		
		$this->_data['arrParam'] = array_merge($get, $post);
	}
	
	function index() {
		$this->load->view('tasks/index_view', $this->_data);
	}
	
	function danhsach() {
		$this->load->model('MTasks');
		$ketqua = $this->MTasks->listItem();
	
		$result = array('ketqua'=>$ketqua['ketqua'], 'deny'=>$ketqua['deny'], 'drag_task'=>$ketqua['drag_task'], 'links'=>array());
		if(!empty($ketqua['ketqua'])) {
			$this->load->model('MTasksLinks');
			$arrParams['task_ids'] = array_keys($ketqua['ketqua']);
			$links = $this->MTasksLinks->listItem($arrParams, array('task'=>'by-source'));
			$result['links'] = $links;
		}
		
		echo json_encode($result);
	}
	
	public function editcongviec() {
		$post = $this->input->post();
		$get  = $this->input->get();
		$this->load->library('MY_System_Info');
		$info 		= new MY_System_Info();
		$this->_data['user_info'] = $user_info = $info->getInfo();
	
		if(empty($user_info)) {
			$response = array('flag'=>'error', 'message'=>'Bạn cần phải đăng nhập để thực hiện tác vụ.');
			echo json_encode($response);
		}else {
			//quyền chung của user
			$task_permission = array();
			if(!empty($user_info['task_permission'])) {
				$task_permission = $user_info['task_permission'];
				$task_permission = explode(',', $task_permission);
			}
				
			$this->load->model('MTasks');
			$this->load->model('MTasksRelation');
				
			if(!empty($post)) {
				$arrParam = $post;
	
				$this->load->library("form_validation");
				$this->form_validation->set_rules('name', 'Tiêu đề', 'required|max_length[255]');
				$this->form_validation->set_rules('date_start', 'Bắt đầu', 'required');
				$this->form_validation->set_rules('date_end', 'Kết thúc', 'required');
	
				if($this->form_validation->run($this) == FALSE){
					$errors = $this->form_validation->error_array();
					$flagError = true;
				}else {
					// kiểm tra time
					$date_start = str_replace('/', '-', $arrParam['date_start']);
					$arrParam['date_start'] = date('Y-m-d', strtotime($date_start));
						
					$date_end = str_replace('/', '-', $arrParam['date_end']);
					$arrParam['date_end'] = date('Y-m-d', strtotime($date_end));
	
					$datediff = strtotime($arrParam['date_end']) - strtotime($arrParam['date_start']);
					$arrParam['duration'] = floor($datediff/(60*60*24)) + 1;
					if($arrParam['duration'] < 0) {
						$flagError = true;
						$errors['date'] = 'Ngày kết thúc phải sau ngày bắt đầu.';
					}
				}
	
	
				if($flagError == false) {
					$this->MTasks->saveItem($arrParam, array('task'=>'edit'));
					$respon = array('flag'=>'true');
				}else {
					$respon = array('flag'=>'false', 'message'=>current($errors));
				}
	
				echo json_encode($respon);
			}else {
				$item = $this->MTasks->getItem(array('id'=>$get['id']), array('task'=>'public-info', 'brand'=>'detail'));
				$is_xem 	  = $is_implement = $is_create_task = $is_pheduyet = $is_progress = array();
				$is_create_task_parent = $is_pheduyet_parent = $is_progress_parent = array();
				if(!empty($item['is_xem'])) {
					foreach($item['is_xem'] as $val)
						$is_xem[] = $val['user_id'];
						
					$is_xem = array_unique($is_xem);
				}
	
				if(!empty($item['is_implement'])) {
					foreach($item['is_implement'] as $val)
						$is_implement[] = $val['user_id'];
						
					$is_implement = array_unique($is_implement);
				}
	
				if(!empty($item['is_create_task'])) {
					foreach($item['is_create_task'] as $key => $val){
						$is_create_task[] = $val['user_id'];
						$keyArr = explode('-', $key);
						if($keyArr[0] != $get['id'])
							$is_create_task_parent[] = $val['user_id'];
					}
	
					$is_create_task_parent = array_unique($is_create_task_parent);
					$is_create_task 	   = array_unique($is_create_task);
				}
	
				if(!empty($item['is_pheduyet'])) {
					foreach($item['is_pheduyet'] as $key => $val){
						$is_pheduyet[] = $val['user_id'];
	
						$keyArr = explode('-', $key);
						if($keyArr[0] != $get['id'])
							$is_pheduyet_parent[] = $val['user_id'];
					}
						
					$is_pheduyet_parent = array_unique($is_pheduyet_parent);
					$is_pheduyet 		= array_unique($is_pheduyet);
				}
	
				$item['is_pheduyet_parent'] = $is_pheduyet_parent;
	
				if(!empty($item['is_progress'])) {
					foreach($item['is_progress'] as $key => $val){
						$is_progress[] = $val['user_id'];
	
						$keyArr = explode('-', $key);
						if($keyArr[0] != $get['id'])
							$is_progress_parent[] = $val['user_id'];
					}
						
					$is_progress_parent = array_unique($is_progress_parent);
					$is_progress 		= array_unique($is_progress);
				}
	
				$item['is_pheduyet_parent'] = $is_pheduyet_parent;
	
				if($item['parent'] > 0){
					$cid 						 = array($item['parent'], $item['project_id']);
					$items 						 = $this->MTasks->getItems(array('cid'=>$cid), array('task'=>'public-info'));
					$this->_data['project_item'] = $items[$item['project_id']];
					$this->_data['parent_item']  = $items[$item['parent']];
						
					$items 		= $this->MTasks->getInfo(array('lft'=>$item['lft'], 'rgt'=>$item['rgt'], 'project_id'=>$item['project_id']), array('task'=>'create-task'));
					$task_ids 	= $items['task_ids'];
						
					$project_relation 	  = $this->MTasksRelation->getItems(array('task_ids'=>$task_ids), array('task'=>'by-multi-task'));
						
					$this->_data['project_relation'] = $project_relation;
				}
	
				$this->_data['item'] = $item;
	
				if($item['parent'] == 0) { // dự án
					if(in_array('update_project', $task_permission))
						$this->load->view('index/editform_view',$this->_data);
					elseif(in_array($user_info['id'], $is_implement) && in_array('update_brand_task', $task_permission))
						$this->load->view('index/editform_view',$this->_data);
					elseif(in_array($user_info['id'], $is_implement))
						$this->load->view('index/quickupdate_view',$this->_data);
					elseif(in_array($user_info['id'], $is_xem)) {
						$this->_data['no_comment'] = $this->_data['no_update'] = true;
						$this->load->view('index/detail_view',$this->_data);
					}
	
				}else { // công việc thuộc dự án
					if(in_array('update_all_task', $task_permission))
						$this->load->view('index/editform_view',$this->_data);
					elseif(in_array($user_info['id'], $is_implement) && in_array('update_brand_task', $task_permission))
						$this->load->view('index/editform_view',$this->_data);
					elseif(in_array($user_info['id'], $is_create_task_parent)){
						$this->load->view('index/editform_view',$this->_data);
					}elseif(in_array($user_info['id'], $is_implement))
						$this->load->view('index/quickupdate_view',$this->_data);
					elseif(in_array($user_info['id'], $is_xem) || in_array($user_info['id'], $is_pheduyet_parent)) {
						$this->_data['no_comment'] = $this->_data['no_update'] = true;
						$this->load->view('index/detail_view',$this->_data);
					}
				}
	
			}
		}
	}
	
	public function quickupdate() {
		$post = $this->input->post();
		$this->load->model('MTasks');
		if(!empty($post)) {
			$arrParam = $post;
				
			$this->load->library("form_validation");
			$this->form_validation->set_rules('date_start', 'Bắt đầu', 'required');
			$this->form_validation->set_rules('date_end', 'Kết thúc', 'required');
			if(isset($post['trangthai'])) {
				$this->form_validation->set_rules('progress', 'Tiến độ', 'required|greater_than[-1]|less_than[101]');
			}
				
			$flagError = false;
				
			if($this->form_validation->run($this) == FALSE){
				$errors = $this->form_validation->error_array();
				$flagError = true;
			}else {
				// kiểm tra time
				$date_start = str_replace('/', '-', $arrParam['date_start']);
				$arrParam['date_start'] = date('Y-m-d', strtotime($date_start));
					
				$date_end = str_replace('/', '-', $arrParam['date_end']);
				$arrParam['date_end'] = date('Y-m-d', strtotime($date_end));
					
				$datediff = strtotime($arrParam['date_end']) - strtotime($arrParam['date_start']);
				$arrParam['duration'] = floor($datediff/(60*60*24)) + 1;
				if($arrParam['duration'] < 0) {
					$flagError = true;
					$errors['date'] = 'Ngày kết thúc phải sau ngày bắt đầu.';
				}
			}
	
			if($flagError == false) {
				$this->MTasks->saveItem($arrParam, array('task'=>'quick-update'));
				$respon = array('flag'=>'true');
			}else {
				$respon = array('flag'=>'false', 'message'=>current($errors));
			}
				
			echo json_encode($respon);
		}
	}
	
	public function addcongviec() {
		$post = $this->input->post();
		$get  = $this->input->get();
		$this->load->library('MY_System_Info');
		$info 		= new MY_System_Info();
		$this->_data['user_info'] = $user_info = $info->getInfo();
	
		$this->load->model('MTasks');
		$this->load->model('MTasksRelation');
	
		if($get['parent'] > 0) {
			$parent_item 	= $this->MTasks->getItem(array('id'=>$get['parent']), array('task'=>'public-info'));
			$parents 		= $this->MTasks->getInfo(array('lft'=>$parent_item['lft'], 'rgt'=>$parent_item['rgt'], 'project_id'=>$parent_item['project_id']), array('task'=>'create-task'));
			$task_ids 		= $parents['task_ids'];
				
			$project_relation 	  = $this->MTasksRelation->getItems(array('task_ids'=>$task_ids), array('task'=>'by-multi-task'));
		}
	
		if(!empty($post)) {
			if(empty($user_info)) {
				$respon = array('flag'=>'false', 'message'=>'Bạn phải đăng nhập.');
			}else {
				$arrParam = $post;
				$arrParam['user_info'] = $this->_data['user_info'];
	
				$this->load->library("form_validation");
				$this->form_validation->set_rules('name', 'Tiêu đề', 'required|max_length[255]');
				$this->form_validation->set_rules('progress', 'Tiến độ', 'required|greater_than[-1]|less_than[101]');
				$this->form_validation->set_rules('date_start', 'Bắt đầu', 'required');
				$this->form_validation->set_rules('date_end', 'Kết thúc', 'required');
	
				$flagError = false;
				$task_permission = array();

				$task_permission = $user_info['task_permission'];
					
				if($this->form_validation->run($this) == FALSE){
					$errors = $this->form_validation->error_array();
					$flagError = true;
				}else {
					// kiểm tra time
					$date_start = str_replace('/', '-', $arrParam['date_start']);
					$arrParam['date_start'] = date('Y-m-d', strtotime($date_start));
						
					$date_end = str_replace('/', '-', $arrParam['date_end']);
					$arrParam['date_end'] = date('Y-m-d', strtotime($date_end));
	
					$datediff = strtotime($arrParam['date_end']) - strtotime($arrParam['date_start']);
					$arrParam['duration'] = floor($datediff/(60*60*24));
					if($arrParam['duration'] < 0) {
						$flagError = false;
						$errors['date'] = 'Ngày kết thúc phải sau ngày bắt đầu.';
					}
				}
	
				if($flagError == false) {
					$arrParam['pheduyet'] = 1;
					$this->MTasks->saveItem($arrParam, array('task'=>'add'));
					$respon = array('flag'=>'true');
				}else {
					$respon = array('flag'=>'false', 'message'=>current($errors));
				}
			}
	
			echo json_encode($respon);
	
		}else {
			$this->_data['parent'] 				= $get['parent'];
			$this->_data['parent_item'] 		= $parent_item;
			$this->_data['project_relation'] 	= $project_relation;
	
			$this->load->view('tasks/addform_view',$this->_data);
		}
	}
}