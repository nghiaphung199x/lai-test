<?php
require_once (APPPATH . "controllers/Secure_area.php");
class BizTasks extends Secure_area 
{
	protected $_data;
	protected $_paginator = array(
			'per_page' => 10,
			'uri_segment' => 3
	);
	
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
		
		$this->_data['arrParam']['paginator'] = $this->_paginator;
		
		//định nghĩa lại ngông ngữ báo lỗi
		$this->load->library("form_validation");
		$this->form_validation->set_message('required', '%s không được rỗng.');
		$this->form_validation->set_message('isset', 'Trường %s phải có giá trị.');
		$this->form_validation->set_message('valid_email', '%s không phải là địa chỉ email.');
		$this->form_validation->set_message('valid_url', '%s không phải là URL.');
		$this->form_validation->set_message('valid_ip', '%s không phải là địa chỉ IP.');
		$this->form_validation->set_message('min_length', '%s phải có ít nhất là %s kí tự.');
		$this->form_validation->set_message('max_length', '%s phải có tối đa là %s kí tự.');
		$this->form_validation->set_message('greater_than', '%s không được nhỏ hơn hoặc bằng %s');
		$this->form_validation->set_message('less_than', '%s không được lớn hơn hoặc bằng %s');
		$this->form_validation->set_message('exact_length', '%s phải có chính xác là %s kí tự.');
		$this->form_validation->set_message('alpha', '%s chỉ được chứa kí tự chữ cái.');
		$this->form_validation->set_message('alpha_numeric', '%s chỉ chứa các kí tự chữ cái và số nguyên.');
		$this->form_validation->set_message('alpha_dash', '%s chỉ chứa các kí tự chữ cái, số nguyên, dấu gạch dưới và dấu gạch ngang.');
		$this->form_validation->set_message('numeric', '%s chỉ chứa số.');
		$this->form_validation->set_message('is_numeric', '%s chỉ chứa kí tự số.');
		$this->form_validation->set_message('integer', '%s phải có kiểu số nguyên.');
		$this->form_validation->set_message('regex_match', '%s không khớp với định dạng.');
		$this->form_validation->set_message('is_unique', '%s đã tồn tại.');
	}
	
	function index() {
		$this->load->view('tasks/index_view', $this->_data);
		
		$this->load->library('MY_System_Info');
		$info 					  = new MY_System_Info();
		$user_info = $info->getInfo();
		if(!in_array('tasks_view', $user_info['task_permission']))
			redirect('/no_access/tasks');
		
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
	
	public function customerList() {
		$post = $this->input->post();
		if(!empty($post)) {
			$this->load->model('MTaskCustomers');
			$result = $this->MTaskCustomers->listItem($this->_data['arrParam']);
			
			echo json_encode($result);
		}
	}
	
	public function userList() {
		$post = $this->input->post();
		if(!empty($post)) {
			$this->load->model('MTaskUser');
			$result = $this->MTaskUser->listItem($this->_data['arrParam']);
				
			echo json_encode($result);
		}
	}

	public function addcongviec() {
		$post = $this->input->post();
		$this->load->library('MY_System_Info');
		$info 					  = new MY_System_Info();
		$this->_data['user_info'] = $user_info = $info->getInfo();
	
		$this->load->model('MTasks');
		$this->load->model('MTasksRelation');
		$this->load->model('MTaskProgress');
		$this->load->model('MTaskTemplate');
		
		$arrParam = $this->_data['arrParam'];

		if($arrParam['parent'] > 0) {
			$parent_item 		 = $this->MTasks->getItem(array('id'=>$arrParam['parent']), array('task'=>'public-info'));
			$parents 			 = $this->MTasks->getInfo(array('lft'=>$parent_item['lft'], 'rgt'=>$parent_item['rgt'], 'project_id'=>$parent_item['project_id']), array('task'=>'create-task'));

			$task_ids 			 = $parents['task_ids'];
			$project_relation 	 = $this->MTasksRelation->getItems(array('task_ids'=>$task_ids), array('task'=>'by-multi-task'));
		}
	
		if(!empty($post)) {
			$arrParam['user_info'] = $this->_data['user_info'];

			$this->load->library("form_validation");
			$this->form_validation->set_rules('name', 'Tiêu đề', 'required|max_length[255]');
			$this->form_validation->set_rules('progress', 'Tiến độ', 'required|greater_than[-1]|less_than[101]');
			$this->form_validation->set_rules('color', 'Màu', 'required');
			$this->form_validation->set_rules('date_start', 'Bắt đầu', 'required');
			$this->form_validation->set_rules('date_end', 'Kết thúc', 'required');
			if($arrParam['parent'] > 0)
				$this->form_validation->set_rules('percent', 'Tỷ lệ', 'required|greater_than[-1]|less_than[101]');
			
			if($arrParam['template_task'] > 0)
				$this->form_validation->set_rules('task_template', 'Template', 'is_unique[task_template.id]');

			$flagError = false;
			$task_permission = array();

			$task_permission = $user_info['task_permission'];
				
			if($this->form_validation->run($this) == FALSE){
				$errors = $this->form_validation->error_array();
				$flagError = true;
			}else {
				// kiểm tra time
				$arrParam['date_start'] = date('Y-m-d', strtotime($arrParam['date_start']));

				$arrParam['date_end']   = date('Y-m-d', strtotime($arrParam['date_end']));

				$datediff = strtotime($arrParam['date_end']) - strtotime($arrParam['date_start']);
				$arrParam['duration'] = floor($datediff/(60*60*24));
				if($arrParam['duration'] <= 0) {
					$flagError = true;
					$errors['date_start'] = 'Ngày kết thúc phải sau ngày bắt đầu.';
					$errors['date_end']   = '.';
				}
				
				if($flagError == false && $arrParam['parent'] > 0) {
					$max_percent = $this->MTasks->getMaxPercent($arrParam['parent'], $arrParam['project_id']);
					// kiểm tra percent
					if($arrParam['percent'] > $max_percent) {
						$flagError = true;
						$errors['percent'] = 'Tỷ lệ không được quá ' . $max_percent . '%';
					}
				}
			}

			if($flagError == false) {
				// kiểm tra quyền
				$is_pheduyet = $is_implement = array();
				if(!empty($project_relation)) {
					foreach($project_relation as $val){
						if($val['is_pheduyet'] == 1)
							$is_pheduyet[] = $val['user_id'];
						
						if($val['is_implement'] == 1)
							$is_implement[] = $val['user_id'];
					}
				}

				$arrParam['pheduyet'] = 0;
				if(in_array('update_project', $task_permission))
					$arrParam['pheduyet'] = 1;
				elseif(in_array($user_info['id'], $is_implement) && in_array('update_brand_task', $task_permission))
					$arrParam['pheduyet'] = 1;
				elseif(count($is_pheduyet) == 0)
					$arrParam['pheduyet'] = 1;
				
				// ấy lại trạng thái và tiến độ
				if($arrParam['trangthai'] == 2 || $arrParam['progress'] == 100) {
					$arrParam['trangthai'] = 2;
					$arrParam['progress'] = 100;
				}elseif($arrParam['progress'] > 0 && $arrParam['trangthai'] == 0) {
					$arrParam['trangthai'] = 1;
				}
				
				if($arrParam['template_task'] > 0) {
					$arrParam['trangthai'] = 0;
					$arrParam['progress']  = 0;
				}

				$last_id = $this->MTasks->saveItem($arrParam, array('task'=>'add'));
				$respon = array('flag'=>'true');
				//add template task
				if($arrParam['task_template'] > 0) {
					$template_task_items = $this->MTaskTemplate->listItem(array('template_id'=>$arrParam['task_template']), array('task'=>'by-template'));
					$parentArray = array();
					foreach($template_task_items as $key => $val) {
						$params = array();
						$params['name'] = $val['name'];
						if($val['level'] == 1) {
							$params['parent'] = $last_id;
						}else
							$params['parent'] = $parentArray[$val['parent']];
						
						$params['color']      = $arrParam['color'];
						$params['detail']     = '';
						$params['percent']    = 0;
						$params['progress']   = 0;
						$params['project_id'] = $arrParam['project_id'];
						$params['date_start'] = $arrParam['date_start'];
						$params['date_end']   = $arrParam['date_end'];
						$params['duration']   = $arrParam['duration'];
						if($arrParam['parent'] == 0)
							$params['pheduyet']   = 1;
						else
							$params['pheduyet']   = $arrParam['pheduyet'];
						
						$params['trangthai']   	  = 0;
						$params['prioty']         = $arrParam['prioty'];
						$params['type']           = $arrParam['type'];
			
						$parentArray[$val['id']] = $this->MTasks->saveItem($params, array('task'=>'add'));
					}
				}

				// nếu là công việc con
// 				if($arrParam['parent'] > 0){
// 					//nếu không phải dự án thì update lại progress item : progress => -1
// 					$this->MTaskProgress->saveItem(array('task_ids'=>$task_ids), array('task'=>'progress-1'));
// 					//update lại tiến đô + lịch sử
// 					$arrParam['key'] = 'plus';
// 					$this->MTaskProgress->solve($arrParam); 
// 				}

			}else {
				$respon = array('flag'=>'false', 'errors'=>$errors);
			}
	
			echo json_encode($respon);
	
		}else {
			$this->load->model('MTaskTemplate');
			$task_template = $this->MTaskTemplate->itemSelectbox();
			$max_percent   = $this->MTasks->getMaxPercent($arrParam['parent'], $parent_item['project_id']);

			$this->_data['percent'] 			= $max_percent;
			$this->_data['parent'] 				= $arrParam['parent'];
			$this->_data['parent_item'] 		= $parent_item;
			$this->_data['project_relation'] 	= $project_relation;
			$this->_data['task_template'] 	    = $task_template;

			//view
			$this->load->view('tasks/addform_view',$this->_data);
		}

	}

	public function editcongviec() {
		$post = $this->input->post();
		$this->load->library('MY_System_Info');
		$info 		= new MY_System_Info();
		$this->_data['user_info'] = $user_info = $info->getInfo();
	
		//quyền chung của user
		$task_permission = $user_info['task_permission'];
			
		$this->load->model('MTasks');
		$this->load->model('MTasksRelation');
		$this->load->model('MTaskProgress');
			
		$arrParam = $this->_data['arrParam'];
		$item = $this->MTasks->getItem(array('id'=>$arrParam['id']), array('task'=>'public-info', 'brand'=>'full'));

		if(!empty($post)) {
			$this->load->library("form_validation");
			$this->form_validation->set_rules('name', 'Tiêu đề', 'required|max_length[255]');
			$this->form_validation->set_rules('color', 'Màu', 'required');
			$this->form_validation->set_rules('date_start', 'Bắt đầu', 'required');
			$this->form_validation->set_rules('date_end', 'Kết thúc', 'required');
			if($arrParam['parent'] > 0)
				$this->form_validation->set_rules('percent', 'Tỷ lệ', 'required|greater_than[-1]|less_than[101]');

			if($this->form_validation->run($this) == FALSE){
				$errors = $this->form_validation->error_array();
				$flagError = true;
			}else {
				// kiểm tra time
				$arrParam['date_start'] = date('Y-m-d', strtotime($arrParam['date_start']));
				$arrParam['date_end']   = date('Y-m-d', strtotime($arrParam['date_end']));

				$datediff = strtotime($arrParam['date_end']) - strtotime($arrParam['date_start']);
				$arrParam['duration'] = floor($datediff/(60*60*24)) + 1;
				if($arrParam['duration'] < 0) {
					$flagError = true;
					$errors['date_start'] = 'Ngày kết thúc phải sau ngày bắt đầu.';
					$errors['date_end']   = '.';
				}
				
				if($flagError == false) {
					$max_percent = $this->MTasks->getMaxPercent($arrParam['parent'], $arrParam['project_id'], $arrParam['id']);
					// kiểm tra percent
					if($arrParam['percent'] > $max_percent) {
						$flagError = true;
						$errors['percent'] = 'Tỷ lệ không được quá ' . $max_percent . '%';
					}
				}
			}

			if($flagError == false) {
				// ấy lại trạng thái và tiến độ
				if($arrParam['trangthai'] == 2 || $arrParam['progress'] == 100) {
					$arrParam['trangthai'] = 2;
					$arrParam['progress'] = 100;
				}elseif($arrParam['progress'] > 0 && $arrParam['trangthai'] == 0) {
					$arrParam['trangthai'] = 1;
				}

				$this->MTasks->saveItem($arrParam, array('task'=>'edit'));

				// cập nhật lại tiến độ
				if($arrParam['percent'] != $item['percent'] * 100) {
					$arrParam['key']   = 'pencil-square-o';
					$arrParam['level'] = $item['level'];

					$this->MTaskProgress->solve($arrParam, array('task'=>'edit'));
				}

				$respon = array('flag'=>'true');
			}else {
				$respon = array('flag'=>'false', 'errors'=>$errors);
			}

			echo json_encode($respon);
		}else {
			$is_xem 	  = $is_implement = $is_create_task = $is_pheduyet = $is_progress = array();
			$is_create_task_parent = $is_pheduyet_parent = $is_progress_parent = array();

			if(!empty($item['is_xem'])) {
				foreach($item['is_xem'] as $val)
					$is_xem[] = $val['id'];
					
				$is_xem = array_unique($is_xem);
			}

			if(!empty($item['is_implement'])) {
				foreach($item['is_implement'] as $val)
					$is_implement[] = $val['id'];
					
				$is_implement = array_unique($is_implement);
			}

			if(!empty($item['is_create_task'])) {
				foreach($item['is_create_task'] as $key => $val){
					$is_create_task[] = $val['id'];
					$keyArr = explode('-', $key);
					if($keyArr[0] != $arrParam['id'])
						$is_create_task_parent[] = $val['user_id'];
				}

				$is_create_task_parent = array_unique($is_create_task_parent);
				$is_create_task 	   = array_unique($is_create_task);
			}

			if(!empty($item['is_pheduyet'])) {
				foreach($item['is_pheduyet'] as $key => $val){
					$is_pheduyet[] = $val['id'];

					$keyArr = explode('-', $key);
					if($keyArr[0] != $arrParam['id'])
						$is_pheduyet_parent[] = $val['id'];
				}
					
				$is_pheduyet_parent = array_unique($is_pheduyet_parent);
				$is_pheduyet 		= array_unique($is_pheduyet);
			}

			$item['is_pheduyet_parent'] = $is_pheduyet_parent;

			if(!empty($item['is_progress'])) {
				foreach($item['is_progress'] as $key => $val){
					$is_progress[] = $val['id'];

					$keyArr = explode('-', $key);
					if($keyArr[0] != $arrParam['id'])
						$is_progress_parent[] = $val['id'];
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
					$view = 'tasks/editform_view';
				elseif(in_array($user_info['id'], $is_implement) && in_array('update_brand_task', $task_permission))
					$view = 'tasks/editform_view';
				elseif(in_array($user_info['id'], $is_implement))
					$view = 'tasks/quickupdate_view';
				elseif(in_array($user_info['id'], $is_xem)) {
					$this->_data['no_comment'] = $this->_data['no_update'] = true;
					$view = 'tasks/detail_view';
				}

			}else { // công việc thuộc dự án
				if(in_array('update_all_task', $task_permission))
					$view = 'tasks/editform_view';
				elseif(in_array($user_info['id'], $is_implement) && in_array('update_brand_task', $task_permission))
					$view = 'tasks/editform_view';
				elseif(in_array($user_info['id'], $is_create_task_parent)){
					$view = 'tasks/editform_view';
				}elseif(in_array($user_info['id'], $is_implement))
					$view = 'tasks/quickupdate_view';
				elseif(in_array($user_info['id'], $is_xem) || in_array($user_info['id'], $is_pheduyet_parent)) {
					$this->_data['no_comment'] = true;	
					$this->_data['is_xem'] 	   = true;
					$view = 'tasks/detail_view';
				}
			}

			//nhánh dự án/ công việc
			$this->_data['slbTasks'] = $this->MTasks->itemSelectBox(array('project_id'=>$item['project_id'], 'lft'=>$item['lft'], 'rgt'=>$item['rgt']));
			
			if(!empty($view))
				$this->load->view($view,$this->_data);
		}
	}
	//labeaute1212@gmail.com : labeaute
	public function progresslist() {
		$this->load->model('MTaskProgress');
		$post  = $this->input->post();
		
		if(!empty($post)) {
			$config['base_url'] = base_url() . 'tasks/progresslist';
			$config['total_rows'] = $this->MTaskProgress->countItem($this->_data['arrParam'], array('task'=>'public-list'));

			$config['per_page'] = $this->_paginator['per_page'];
			$config['uri_segment'] = $this->_paginator['uri_segment'];
			$config['use_page_numbers'] = TRUE;
	
			$this->load->library("pagination");
			$this->pagination->initialize($config);
			$this->pagination->createConfig('front-end');
	
			$pagination = $this->pagination->create_ajax();
				
			$this->_data['arrParam']['start'] = $this->uri->segment(3);
			$items = $this->MTaskProgress->listItem($this->_data['arrParam'], array('task'=>'public-list'));
				
			$result = array('count'=> $config['total_rows'], 'items'=>$items, 'pagination'=>$pagination);
	
			echo json_encode($result);
		}
	}
	
	public function countTiendo() {
		$this->load->model('MTaskProgress');
		$post  = $this->input->post();
		if(!empty($post)) {
			$result['tiendo_total']    = $this->MTaskProgress->countItem($this->_data['arrParam'], array('task'=>'public-list'));
			$result['request_total']   = $this->MTaskProgress->countItem($this->_data['arrParam'], array('task'=>'request-list'));
			$result['pheduyet_total']  = $this->MTaskProgress->countItem($this->_data['arrParam'], array('task'=>'pheduyet-list'));
				
			echo json_encode($result);
		}
	}
	
	public function filelist() {
		$this->load->model('MTaskFiles');
		$post  = $this->input->post();
	
		if(!empty($post)) {
			$config['base_url'] = base_url() . 'tasks/filelist';
			$config['total_rows'] = $this->MTaskFiles->countItem($this->_data['arrParam'], array('task'=>'public-list'));
			$config['per_page'] = $this->_paginator['per_page'];
			$config['uri_segment'] = $this->_paginator['uri_segment'];
			$config['use_page_numbers'] = TRUE;
				
			$this->load->library("pagination");
			$this->pagination->initialize($config);
			$this->pagination->createConfig('front-end');
				
			$pagination = $this->pagination->create_ajax();
	
			$this->_data['arrParam']['start'] = $this->uri->segment(3);
			$items = $this->MTaskFiles->listItem($this->_data['arrParam'], array('task'=>'public-list'));
	
			$result = array('count'=> $config['total_rows'], 'items'=>$items, 'pagination'=>$pagination);
				
			echo json_encode($result);
		}
	}
	
	public function requestlist() {
		$this->load->model('MTaskProgress');
		$post  = $this->input->post();
		if(!empty($post)) {
			$config['base_url'] = base_url() . 'tasks/progresslist';
			$config['total_rows'] = $this->MTaskProgress->countItem($this->_data['arrParam'], array('task'=>'request-list'));
			$config['per_page'] = $this->_paginator['per_page'];
			$config['uri_segment'] = $this->_paginator['uri_segment'];
			$config['use_page_numbers'] = TRUE;
	
			$this->load->library("pagination");
			$this->pagination->initialize($config);
			$this->pagination->createConfig('front-end');
	
			$pagination = $this->pagination->create_ajax();
	
			$this->_data['arrParam']['start'] = $this->uri->segment(3);
			$items = $this->MTaskProgress->listItem($this->_data['arrParam'], array('task'=>'request-list'));
	
			$result = array('count'=> $config['total_rows'], 'items'=>$items, 'pagination'=>$pagination);
	
			echo json_encode($result);
		}
	}
	
	public function pheduyetlist() {
		$this->load->model('MTaskProgress');
		$post  = $this->input->post();
		if(!empty($post)) {
			$config['base_url'] = base_url() . 'tasks/pheduyetlist';
			$config['total_rows'] = $this->MTaskProgress->countItem($this->_data['arrParam'], array('task'=>'pheduyet-list'));
			$config['per_page'] = $this->_paginator['per_page'];
			$config['uri_segment'] = $this->_paginator['uri_segment'];
			$config['use_page_numbers'] = TRUE;
	
			$this->load->library("pagination");
			$this->pagination->initialize($config);
			$this->pagination->createConfig('front-end');
	
			$pagination = $this->pagination->create_ajax();
	
			$this->_data['arrParam']['start'] = $this->uri->segment(3);
			$items = $this->MTaskProgress->listItem($this->_data['arrParam'], array('task'=>'pheduyet-list'));
	
			$result = array('count'=> $config['total_rows'], 'items'=>$items, 'pagination'=>$pagination);
	
			echo json_encode($result);
		}
	}
	
	public function addtiendo() {
		$this->load->model('MTasks');
		$this->load->model('MTaskProgress');
		$post  = $this->input->post();
		$arrParam = $this->_data['arrParam'];
	
		$this->load->library('MY_System_Info');
		$info 		= new MY_System_Info();
		$arrParam['adminInfo'] = $user_info = $info->getInfo();
		
		if(!empty($post)) {
			$item = $this->MTasks->getItem(array('id'=>$this->_data['arrParam']['task_id']), array('task'=>'public-info', 'brand'=>'full'));
			if($item['pheduyet'] == 0) {
				$respon = array('flag'=>'false', 'message'=>'Công việc chưa được phê duyệt.');
			}else {
				$flagError = false;
				// check cv cuối
				if($item['lft'] == $item['rgt'] + 1)
						$arrParam['progress'] = -1;

				if($arrParam['progress'] != -1) {
					$this->form_validation->set_rules('progress', 'Tiến độ', 'required|greater_than[-1]|less_than[101]');
					
					if($this->form_validation->run($this) == FALSE){
						$errors = $this->form_validation->error_array();
						$flagError = true;
					}
				}

				if($flagError == false) {
					$is_progress = $is_progress_parent = $is_implement = array();
						
					if(!empty($item['is_implement'])) {
						foreach($item['is_implement'] as $val)
							$is_implement[] = $val['user_id'];
					
						$is_implement = array_unique($is_implement);
					}
					
					if(!empty($item['is_progress'])) {
						foreach($item['is_progress'] as $key => $val){
							$is_progress[] = $val['user_id'];
						}
					
						$is_progress 		= array_unique($is_progress);
					}
					
					$task_permission = $user_info['task_permission'];
						
					$arrParam['pheduyet'] = 2;
					if(in_array('update_project', $task_permission))
						$arrParam['pheduyet'] = 3;
					elseif(in_array($user_info['id'], $is_implement) && in_array('update_brand_task', $task_permission))
						$arrParam['pheduyet'] = 3;
					elseif(count($is_progress) == 0)
						$arrParam['pheduyet'] = 3;

					if($arrParam['trangthai'] == 2 || $arrParam['progress'] == 100) {
						$arrParam['trangthai'] = 2;
						$arrParam['progress']  = 100;
					}elseif($arrParam['progress'] > 0 && $arrParam['trangthai'] == 0) {
						$arrParam['trangthai'] = 1;
					}	


					if($arrParam['pheduyet'] == 3) { // không cần phải gửi request
						// cập nhật tiến độ cho task
						// nếu proress == -1 thì chỉ cập nhật trạng thái + progress, ngược lại handling
						$params 	  = $arrParam;
						$params['id'] = $arrParam['task_id'];
					
						if($arrParam['progress'] == -1) {
							$arrParam['progress'] = $item['progress'] * 100; // progress mới nhất
							$this->MTasks->saveItem($params, array('task'=>'update-tiendo'));
							
							$arrParam['key'] = '';
							$arrParam['date_pheduyet'] = @date("Y-m-d H:i:s");
								
							$this->MTaskProgress->saveItem($arrParam, array('task'=>'add'));
						}else {
							$this->MTasks->saveItem($params, array('task'=>'update-tiendo'));
							$this->MTaskProgress->handling($arrParam, array('task'=>'progress'));
						}
					
						$respon = array('flag'=>'true', 'message'=>'Cập nhật thành công', 'reload'=>'true');
					}else {
						// cập nhật task progress. ko cập nhật task
						$arrParam['key'] = '';
						$arrParam['date_pheduyet'] = '0000-00-00 00:00:00';
					
						$this->MTaskProgress->saveItem($arrParam, array('task'=>'add'));
						$respon = array('flag'=>'true', 'message'=>'Yêu cầu đang được phê duyệt.');
					}
				}else
					$respon = array('flag'=>'false', 'message'=>current($errors));
			}

			echo json_encode($respon);
		}else {
			$this->_data['item'] = $item = $this->MTasks->getItem(array('id'=>$this->_data['arrParam']['task_id']), array('task'=>'public-info'));
			$this->load->view('tasks/addtiendo_view',$this->_data);
		}
	}
	
	public function addfile() {
		$fileError = array(
				'<p>The filetype you are attempting to upload is not allowed.</p>'=>'File tải lên phải có định dạng jpg|png|pdf|docx|doc|xls|xlsx|zip|zar',
				'<p>The file you are attempting to upload is larger than the permitted size.</p>' => 'File tải lên không được quá 10 Mb'
		);
		$post  = $this->input->post();
	
		$this->load->library('MY_System_Info');
		$info 		= new MY_System_Info();

		if(!empty($post)) {
			$arrParam = $this->_data['arrParam'];
			$arrParam['adminInfo'] = $user_info = $info->getInfo();
			$this->load->library("form_validation");
			$this->form_validation->set_rules('name', 'Tên tài liệu', 'required|max_length[255]|is_unique[task_files.name]');
			$this->form_validation->set_rules('file_name', 'Tên file', 'required|max_length[255]|is_unique[task_files.file_name]');
	
			if($this->form_validation->run($this) == FALSE){
				$errors = $this->form_validation->error_array();
				$flagError = true;
			}else {
				if($_FILES["file_upload"]['name'] != ""){
					$upload_dir = FILE_PATH;
					$config['upload_path'] = $upload_dir;
					$config['allowed_types'] = 'jpg|png|pdf|docx|doc|xls|xlsx|zip|zar';
					$config['max_size']	= '10240';
					$config['encrypt_name'] = TRUE;
					$config['file_name'] = 'test-1.docx';
						
					$this->load->library('upload', $config);
	
					if($this->upload->do_upload("file_upload")){
						// đổi tên file vì config file_name ko hoạt động
						$file_info = $this->upload->data();
						$old_file_name = $file_info['file_name'];
						rename($upload_dir . $old_file_name, $upload_dir . $post['file_name']);
	
						$arrParam['size'] = $_FILES['file_upload']['size'];
	
					}else{
						$flagError = true;
						$err = $this->upload->display_errors();
						$errors['file_upload'] = $fileError[$err];
					}
				}else {
					$flagError = true;
					$errors['file_upload'] = 'Phải tải file lên.';
				}
			}
	
			if($flagError == true) {
				$respon = array('flag'=>'false', 'errors'=>$errors);
			}else {
				$this->load->model('MTaskFiles');
				$this->MTaskFiles->saveItem($arrParam, array('task'=>'add'));
	
				$respon = array('flag'=>'true', 'message'=>'Cập nhật thành công');
			}
			
			echo json_encode($respon);
		}else
			$this->load->view('tasks/addfile_view',$this->_data);
	}
	
	public function editfile() {
		$fileError = array(
				'<p>The filetype you are attempting to upload is not allowed.</p>'=>'File tải lên phải có định dạng jpg|png|pdf|docx|doc|xls|xlsx|zip|zar',
				'<p>The file you are attempting to upload is larger than the permitted size.</p>' => 'File tải lên không được quá 10 Mb'
		);
		$post  = $this->input->post();
	
		$this->load->library('MY_System_Info');
		$info 		= new MY_System_Info();
		$this->load->model('MTaskFiles');
		$item		= $this->MTaskFiles->getItem($this->_data['arrParam'], array('task'=>'public-info'));
		if(!empty($post)) {
			$arrParam 			   = $this->_data['arrParam'];
			$arrParam['task_id']   = $item['task_id'];
			$arrParam['adminInfo'] = $user_info = $info->getInfo();
	
			$this->load->library("form_validation");
			$flagError = false;

			if($_FILES["file_upload"]['name'] != ""){
				$this->form_validation->set_rules('name', 'Tên tài liệu', 'required|max_length[255]');
				$this->form_validation->set_rules('file_name', 'Tên file', 'required|max_length[255]');
				
				if($this->form_validation->run($this) == FALSE){
					$errors = $this->form_validation->error_array();
					$flagError = true;
				}
					
				if($flagError == false){
					$flagError = $this->MTaskFiles->validate($arrParam['name'], 'name', $arrParam['id']);
					if($flagError == true)
						$errors['name'] = 'Tên tài liệu đã tồn tại.';
				}
				
				if($flagError == false){
					$flagError = $this->MTaskFiles->validate($arrParam['file_name'], 'file_name', $arrParam['id']);
					if($flagError == true)
						$errors['file_name'] = 'Tên tài file đã tồn tại.';
				}

				if($flagError == false) {
					$upload_dir = FILE_PATH;
					// remove file cũ
					@unlink($upload_dir . $item['file_name']);
					
					$config['upload_path'] = $upload_dir;
					$config['allowed_types'] = 'jpg|png|pdf|docx|doc|xls|xlsx|zip|zar';
					$config['max_size']	= '10240';
					$config['encrypt_name'] = TRUE;
					$config['file_name'] = 'test-1.docx';
					
					$this->load->library('upload', $config);
					
					if($this->upload->do_upload("file_upload")){
						// đổi tên file vì config file_name không hoạt động
						$file_info = $this->upload->data();
						$old_file_name = $file_info['file_name'];
						rename($upload_dir . $old_file_name, $upload_dir . $post['file_name']);
							
						$arrParam['size'] = $_FILES['file_upload']['size'];
					}else{
						$flagError = true;
						$err = $this->upload->display_errors();
						$errors['file_upload'] = $fileError[$err];
					}
				}
					
			}else {
				$this->form_validation->set_rules('name', 'Tên tài liệu', 'required|max_length[255]');
	
				if($this->form_validation->run($this) == FALSE){
					$errors = $this->form_validation->error_array();
					$flagError = true;
				}
		
				if($flagError == false){
					$flagError = $this->MTaskFiles->validate($arrParam['name'], 'name', $arrParam['id']);
					if($flagError == true)
						$errors['file_name'] = 'Tên tài liệu đã tồn tại.';
				}
				
				if($flagError == false) {
					$arrParam['file_name'] = $item['file_name'];
					$arrParam['size'] 	   = $item['size'];
				}
			}

			if($flagError == true) {
				$respon = array('flag'=>'false', 'errors'=>$errors);
			}else {
				$this->load->model('MTaskFiles');
				$this->MTaskFiles->saveItem($arrParam, array('task'=>'edit'));
	
				$respon = array('flag'=>'true', 'message'=>'Cập nhật thành công');
			}
	
			echo json_encode($respon);
				
		}else {
			$this->_data['item'] = $item;
	
			$this->load->view('tasks/editfile_view',$this->_data);
		}
	}
	
	public function deletefile() {
		$post  = $this->input->post();
	
		if(!empty($post)) {
			$this->load->model('MTaskFiles');
			$this->_data['arrParam']['cid'] = $this->_data['arrParam']['file_ids'];
				
			$this->MTaskFiles->deleteItem($this->_data['arrParam'], array('task'=>'delete-multi'));
		}
	}
	
	public function note() {
		$post  = $this->input->post();
		if(!empty($post)) {
			$this->load->model('MTaskProgress');
			$item = $this->MTaskProgress->getItem($this->_data['arrParam'], array('task'=>'public-info'));
			$this->_data['item'] = $item;
			$this->load->view('tasks/note_view',$this->_data);
		}
	}
	
	public function xulytiendo() {
		$post  = $this->input->post();
		if(!empty($post)) {
			$arrParam = $this->_data['arrParam'];
			$this->load->model('MTaskProgress');
			$this->MTaskProgress->saveItem($arrParam, array('task'=>'update-pheduyet'));
			
			if($arrParam['pheduyet'] == 1){
				$this->MTaskProgress->handling($arrParam);
				$respon = array('flag'=>'true', 'message'=>'Cập nhật thành công', 'reload'=>'true');
			}else {
				$respon = array('flag'=>'true', 'message'=>'Cập nhật thành công');
			}
	
			echo json_encode($respon);
		}else
			$this->load->view('tasks/xulytiendo_view',$this->_data);
	}
	

	public function detail() {
		$post  = $this->input->post();
		if(!empty($post)) {
			$arrParam = $this->_data['arrParam'];
			$this->load->library('MY_System_Info');
			$info 		= new MY_System_Info();
			$this->_data['user_info'] = $user_info = $info->getInfo();
				
			$this->load->model('MTasks');
			$item = $this->MTasks->getItem(array('id'=>$arrParam['id']), array('task'=>'public-info', 'brand'=>'full'));
				
			if($item['parent'] > 0){
				$cid 						 = array($item['parent'], $item['project_id']);
				$items 						 = $this->MTasks->getItems(array('cid'=>$cid), array('task'=>'public-info'));
				$this->_data['project_item'] = $items[$item['project_id']];
				$this->_data['parent_item']  = $items[$item['parent']];
	
				$is_pheduyet_parent = array();
				if(!empty($item['is_pheduyet'])) {
					foreach($item['is_pheduyet'] as $key => $val){
						$is_pheduyet[] = $val['id'];
	
						$keyArr = explode('-', $key);
						if($keyArr[0] != $arrParam['id'])
							$is_pheduyet_parent[] = $val['id'];
					}
						
					$is_pheduyet_parent = array_unique($is_pheduyet_parent);
					$is_pheduyet 		= array_unique($is_pheduyet);
				}
	
				$item['is_pheduyet_parent'] = $is_pheduyet_parent;
			}
	
			$this->_data['item'] = $item;
			$this->load->view('tasks/detail_view',$this->_data);
		}
	}
	
	public function commentlist() {
		$this->load->model('MTaskComment');
		$post  = $this->input->post();
		if(!empty($post)) {
			$config['base_url'] = base_url() . 'tasks/commentlist';
			$config['total_rows'] = $this->MTaskComment->countItem($this->_data['arrParam'], array('task'=>'public-list'));
			$config['per_page'] = $this->_paginator['per_page'];
			$config['uri_segment'] = $this->_paginator['uri_segment'];
			$config['use_page_numbers'] = TRUE;
				
			$this->load->library("pagination");
			$this->pagination->initialize($config);
			$this->pagination->createConfig('front-end');
				
			$pagination = $this->pagination->create_ajax();
	
			$this->_data['arrParam']['start'] = $this->uri->segment(3);
			$items = $this->MTaskComment->listItem($this->_data['arrParam'], array('task'=>'public-list'));
	
			$result = array('items'=>$items, 'pagination'=>$pagination);
				
			echo json_encode($result);
		}
	}
	
	public function addcomment() {
		$this->load->model('MTaskComment');
		$post  	  = $this->input->post();
		$arrParam = $this->_data['arrParam'];
	
		if(!empty($post)) {
			$this->form_validation->set_rules('content', 'Nội dung', 'required');
				
			if($this->form_validation->run($this) == FALSE){
				$errors = $this->form_validation->error_array();
	
				$response = array('flag'=>'false', 'msg'=>current($errors));
			}else {
				$this->MTaskComment->saveItem($arrParam, array('task'=>'add'));
				$response = array('flag'=>'true', 'msg'=>'Bình luận thành công', 'task_id'=>$arrParam['task_id']);
			}
				
			echo json_encode($response);
		}
	}
	
	public function link()  {
		$post  = $this->input->post();
		$this->load->library('MY_System_Info');
		$info 		= new MY_System_Info();
		$user_info = $info->getInfo();
	
		if(!empty($post)) {
			$this->load->model('MTasksLinks');
	
			$arrParam = $post;
			$arrParam['user_info'] = $user_info;
			$this->MTasksLinks->saveItem($arrParam, array('task'=>'add'));
				
			$response = array('flag'=>'true');
			echo json_encode($response);
		}
	}
	
	public function delete() {
		$post  = $this->input->post();
		if(!empty($post)) {
			$this->load->model('MTasksLinks');
	
			$arrParam['id'] = $post['link_id'];
			$this->MTasksLinks->deleteItem($arrParam, array('task'=>'delete'));
		}
	}
	
	public function pheduyet() {
		$post     = $this->input->post();
		$arrParam = $this->_data['arrParam'];
		if(!empty($post)) {
			$this->load->model('MTasks');
			$this->MTasks->saveItem(array('id'=>$arrParam['id']), array('task'=>'pheduyet'));
				
			$response = array('flag'=>'true');
			echo json_encode($response);
		}
	}
	
	public function quickupdate() {
		$post 	  = $this->input->post();
		$arrParam = $this->_data['arrParam'];
		$this->load->model('MTasks');
		if(!empty($post)) {
			// kiểm tra time
			$date_start = str_replace('/', '-', $arrParam['date_start']);
			$arrParam['date_start'] = date('Y-m-d', strtotime($date_start));
				
			$date_end = str_replace('/', '-', $arrParam['date_end']);
			$arrParam['date_end'] = date('Y-m-d', strtotime($date_end));
				
			$datediff = strtotime($arrParam['date_end']) - strtotime($arrParam['date_start']);
			$arrParam['duration'] = floor($datediff/(60*60*24)) + 1;

			$this->MTasks->saveItem($arrParam, array('task'=>'quick-update'));
		}
	}
	
	public function deletecv() {
		$post 	  = $this->input->post();
		$this->load->model('MTasks');
		$this->load->model('MTaskProgress');
		$arrParam = $this->_data['arrParam'];
		if(!empty($post)) {
			$items = $this->MTasks->getItems(array('cid'=>$arrParam['ids']), array('task'=>'public-info'));

			foreach($arrParam['ids'] as $id){
				$this->MTasks->deleteItem($id);
				$item = $items[$id];
	
				if($item['parent'] > 0) {
					$params 	   = $item;
					$params['key'] = 'trash-o';
					
					$this->MTaskProgress->solve($params, array('task'=>'remove'));
				}
			}
		}
	}
	
	public function template() { 
		$this->load->view('tasks/template_view',$this->_data);
	}
	
	public function templatelist() {
		$this->load->model('MTaskTemplate');
		$this->_paginator['per_page'] = 20;
		$post  = $this->input->post();
		
		if(!empty($post)) {
			$config['base_url'] = base_url() . 'tasks/templatelist';
			$config['total_rows'] = $this->MTaskTemplate->countItem($this->_data['arrParam'], array('task'=>'template-list'));

			$config['per_page'] = $this->_paginator['per_page'];
			$config['uri_segment'] = $this->_paginator['uri_segment'];
			$config['use_page_numbers'] = TRUE;
	
			$this->load->library("pagination");
			$this->pagination->initialize($config);
			$this->pagination->createConfig('front-end');
	
			$pagination = $this->pagination->create_ajax();
				
			$this->_data['arrParam']['start'] = $this->uri->segment(3);
			$items = $this->MTaskTemplate->listItem($this->_data['arrParam'], array('task'=>'template-list'));
				
			$result = array('count'=> $config['total_rows'], 'items'=>$items, 'pagination'=>$pagination);
	
			echo json_encode($result);
		}
	}
	
	public function listTemplateTask() {
		$arrParam = $this->_data['arrParam'];
		if(isset($arrParam['tasks'])) {
			$tasks = $tasksTmp = array();
			foreach($arrParam['tasks'] as $val) {
				if($val['parent'] == 'root') {
					$val['parent'] = 0;
					$val['level']  = 1;
				}
				$tasksTmp[$val['id']] = $val;
			}

			foreach($tasksTmp as &$val) {
				if($val['parent'] != '0'){
					$val['level'] = $tasksTmp[$val['parent']]['level'] + 1;
				}
				
				$tasks[] = $val;
			}

			$orderings = array();
			foreach($tasks as $val){
				$orderings[$val['parent']][] = $val['id'];
			}

			$this->_data['items']     = $tasks;
			$this->_data['orderings'] = $orderings;
		}
		$this->load->view('tasks/listTemplateTask_view',$this->_data);
	}
	
	public function templateAdd() {
		$this->load->model('MTaskTemplate');
		$post  = $this->input->post();
		if(!empty($post)) {
			$this->load->library("form_validation");
			$this->form_validation->set_rules('template_name', 'Tên', 'required|max_length[300]|is_unique[task_template.name]');
			if($this->form_validation->run($this) == FALSE){
				$errors = $this->form_validation->error_array();
				$flagError = true;
			}else {
				if(!isset($this->_data['arrParam']['tasks'])) {
					$flagError = true;
					$errors[] = 'Phải thêm công việc cho template.';
				}
			}

			if($flagError == false) {
				// + template
				$params['name']   = $this->_data['arrParam']['template_name'];
				$params['parent'] = 0;
				$last_id = $this->MTaskTemplate->saveItem($params, array('task'=>'add'));
	
				// + task for template
				$arrayParent = array();
				foreach($this->_data['arrParam']['tasks'] as $key => $params) {
					if($params['parent'] == 'root')
						$params['parent'] = $last_id;
					else
						$params['parent'] = $arrayParent[$params['parent']];
						
					$params['template_id'] = $last_id;

					$arrayParent[$params['id']] = $this->MTaskTemplate->saveItem($params, array('task'=>'add'));
				}
	
				$respon = array('flag'=>'true', 'msg'=>'Cập nhật thành công.');
			}else {
				$respon = array('flag'=>'false', 'msg'=>current($errors));
			}
			
			echo json_encode($respon);
		}else
		  $this->load->view('tasks/templateAdd_view',$this->_data);
	}
	
	public function addcvtemplate() {
		$this->load->view('tasks/addcvtemplate_view',$this->_data);
	}
	
	public function test() {
// 		$this->load->model('MTasks');
// 		$this->MTasks->test();
		$date = date('Y-m-d h:i:s', time());
		
	
	}
}