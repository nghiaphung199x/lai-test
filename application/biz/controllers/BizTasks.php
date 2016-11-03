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

        // load helper
        $this->load->helper('filterext');
	}
	
	public function index() {
		$this->load->library('MY_System_Info');
		$info 					  = new MY_System_Info();
		$user_info = $info->getInfo();
		if(!in_array('tasks_view', $user_info['task_permission']))
			redirect('/no_access/tasks');

		$this->load->view('tasks/index_view', $this->_data);
	}
	
	public function danhsach() {
		$this->_paginator['per_page'] 		  = 5;
		$this->_data['arrParam']['paginator'] = $this->_paginator;
		
		$this->load->model('MTasks');
		$config['total_rows'] = $this->MTasks->countItem($this->_data['arrParam']);
		
		$config['per_page'] = $this->_paginator['per_page'];
		$config['uri_segment'] = $this->_paginator['uri_segment'];
		$config['use_page_numbers'] = TRUE;
		
		$this->load->library("pagination");
		$this->pagination->initialize($config);
		$this->pagination->createConfig('front-end');
		
		$pagination = $this->pagination->create_ajax();
		
		$this->_data['arrParam']['start'] = $this->uri->segment(3);
		
		$ketqua = $this->MTasks->listItem($this->_data['arrParam']);
	
		$result = array('ketqua'=>$ketqua['ketqua'], 'deny'=>$ketqua['deny'], 'drag_task'=>$ketqua['drag_task'], 'links'=>array());
		if(!empty($ketqua['ketqua'])) {
			$this->load->model('MTasksLinks');
			$arrParams['task_ids'] = array_keys($ketqua['ketqua']);
			$links = $this->MTasksLinks->listItem($arrParams, array('task'=>'by-source'));
			$result['links'] = $links;
		}
		
		$result['count']      = $config['total_rows'];
		$result['pagination'] = $pagination;

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

    public function trangthaiList() {
        $post = $this->input->post();
        if(!empty($post)) {
            $keywords       = trim($post['keywords']);
            $task_trangthai = lang('task_trangthai');
            $task_trangthai[5] = 'Chậm tiến độ';
            $task_trangthai[6] = 'Đã hoàn thành nhưng chậm tiến độ';
            $result = array();
            foreach($task_trangthai as $id => $name) {
                $re_keywords = rewriteUrl($keywords, 'low');
                $re_name     = rewriteUrl($name, 'low');
                if (mb_strpos($re_name, $re_keywords) !== false) {
                    $result[] = array(
                        'id' => $id, 'name' => $name
                    );
                }
            }

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
                if(isset($errors['date_start']) && !isset($errors['date_end']))
                    $errors['date_end'] = '.';

                if(!isset($errors['date_start']) && isset($errors['date_end']))
                    $errors['date_start'] = '.';

				$flagError = true;
			}else {
				// time valid
				$arrParam['date_start'] = date('Y-m-d', strtotime($arrParam['date_start']));

				$arrParam['date_end']   = date('Y-m-d', strtotime($arrParam['date_end']));

				$datediff = strtotime($arrParam['date_end']) - strtotime($arrParam['date_start']);
				$arrParam['duration'] = floor($datediff/(60*60*24));
				if($arrParam['duration'] <= 0) {
					$flagError = true;
					$errors['date_start'] = 'Ngày kết thúc phải sau ngày bắt đầu.';
					$errors['date_end']   = '.';
				}else {
                    if($arrParam['parent'] > 0) {
                        $error_date = $this->validate_min_max_date($arrParam['date_start'], $arrParam['date_end'], $parent_item['date_start'], $parent_item['date_end']);
                        if(!empty($error_date)) {
                            $flagError = true;
                            $errors['date_start'] = $error_date;
                            $errors['date_end']   = '.';
                        }
                    }
                }

                // max percent valid
				if($arrParam['parent'] > 0) {
					$max_percent = $this->MTasks->getMaxPercent($arrParam['parent'], $arrParam['project_id']);
					if($arrParam['percent'] > $max_percent) {
						$flagError = true;
						$errors['percent'] = 'Tỷ lệ không được quá ' . $max_percent . '%';
					}
				}
			}

			if($flagError == false) {
				// check permission
				$is_pheduyet = $is_implement = array();
				if(!empty($project_relation)) {
					foreach($project_relation as $val){
						if($val['is_pheduyet'] == 1)
							$is_pheduyet[] = $val['user_id'];
						
						if($val['is_implement'] == 1)
							$is_implement[] = $val['user_id'];
					}
				}

				$arrParam['pheduyet'] = -1;
				if(in_array('update_project', $task_permission))
					$arrParam['pheduyet'] = 2;
				elseif(in_array($user_info['id'], $is_implement) && in_array('update_brand_task', $task_permission))
					$arrParam['pheduyet'] = 2;
				elseif(count($is_pheduyet) == 0)
					$arrParam['pheduyet'] = 2;
				
				// covert status and progress
				$arrParam = $this->convert_progress_task($arrParam);
				$last_id = $this->MTasks->saveItem($arrParam, array('task'=>'add'));

                //update first progress
                $params = array(
                    'task_id'  => $last_id               , 'trangthai' => $arrParam['trangthai'],
                    'prioty'   => $arrParam['prioty']    , 'progress'  => $arrParam['progress'],
                    'pheduyet' => $arrParam['pheduyet']  , 'note'      => '', 'key' => 'plus',
                    'date_pheduyet' =>  @date("Y-m-d H:i:s"),
                );

                $this->MTaskProgress->saveItem($params, array('task'=>'add'));

				//add template task
				if($arrParam['task_template'] > 0) {
                    $arrParam['last_id'] = $last_id;
					$this->add_template_for_task($arrParam);
				}

				// nếu là công việc con
 				if($arrParam['parent'] > 0){
 					//nếu không phải dự án thì update lại progress item : progress => -1
 					$this->MTaskProgress->saveItem(array('task_ids'=>$task_ids), array('task'=>'progress-1'));

 					//update lại tiến đô + lịch sử
 					$arrParam['key'] = 'plus';
 					$this->MTaskProgress->solve($arrParam);
 				}

                $respon = array('flag'=>'true');
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

	protected function convert_progress_task($arrParam) {
        if($arrParam['pheduyet'] == -1) {
            $arrParam['trangthai'] = 0;
            $arrParam['progress'] = 0;
        }else {
            if($arrParam['trangthai'] == 2 || $arrParam['progress'] == 100) {
                $arrParam['trangthai'] = 2;
                $arrParam['progress'] = 100;
            }elseif($arrParam['progress'] > 0 && $arrParam['trangthai'] == 0) {
                $arrParam['trangthai'] = 1;
            }

            if($arrParam['task_template'] > 0) {
                $arrParam['trangthai'] = 0;
                $arrParam['progress']  = 0;
            }
        }

		return $arrParam;
	}

	protected function add_template_for_task($arrParam) {
        $arrParam   = $this->_data['arrParam'];
        $info 		    = new MY_System_Info();
        $user_info      = $info->getInfo();

        $this->load->model('MTaskTemplate');
        if($arrParam['parent'] == 0)
            $arrParam['project_id'] = $arrParam['last_id'];

		$template_task_items = $this->MTaskTemplate->listItem(array('template_id'=>$arrParam['task_template']), array('task'=>'by-template'));
		$parentArray = array();

		foreach($template_task_items as $key => $val) {
			$params = array();
			$params['name'] = $val['name'];
			if($val['level'] == 1) {
				$params['parent'] = $arrParam['last_id'];
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
            $params['pheduyet']   = $arrParam['pheduyet'];
			$params['trangthai']  = $arrParam['trangthai'];
			$params['prioty']     = $arrParam['prioty'];

			$parentArray[$val['id']] = $this->MTasks->saveItem($params, array('task'=>'add'));

            $items[] = array(
                'task_id'            => $parentArray[$val['id']],
                'trangthai'          => 0,
                'prioty'             => $params['prioty'],
                'progress'           => 0,
                'pheduyet'           => $params['pheduyet'],
                'note'               => '',
                'reply'              => '',
                'created'            => @date("Y-m-d H:i:s"),
                'created_by'         => $user_info['id'],
                'user_pheduyet'      => 0,
                'date_pheduyet'      => @date("Y-m-d H:i:s"),
                'user_pheduyet_name' => '',
                'key'                => 'plus',
            );
		}
        if(!empty($items)) {
            $this->MTaskProgress->saveItem(array('items'=>$items), array('task'=>'multi-add'));
        }
	}

    protected function update_time_for_tasks_child($task_items, $date_start, $date_end) {
        $this->load->model('MTasks');
        foreach($task_items as $val) {
            $params = array();
            $fields = array();
            $params['id'] = $val['id'];
            $datediff_start    		= strtotime($val['date_start']) - strtotime($date_start);
            if($datediff_start > 0)
                $fields['date_start'] = $val['date_start'];
            else
                $fields['date_start'] = $date_start;

            $datediff_end    		= strtotime($val['date_end']) - strtotime($date_end);
            if($datediff_end > 0)
                $fields['date_end'] = $date_end;
            else
                $fields['date_end'] = $val['date_end'];

            $datediff = strtotime($fields['date_end']) - strtotime($fields['date_start']);
            $fields['duration'] = floor($datediff/(60*60*24)) + 1;

            $params['fields'] = $fields;
            $this->MTasks->saveItem($params, array('task'=>'custom'));
        }
    }

	public function editcongviec() {
		$arrParam   = $this->_data['arrParam'];

		$post 	    = $this->input->post();
		$this->load->library('MY_System_Info');
		$info 		= new MY_System_Info();
		$this->_data['user_info'] = $user_info = $info->getInfo();

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
			if($item['parent'] > 0)
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
				}else {
                    if($item['parent'] > 0) {
                        $parent_item = $this->MTasks->getItem(array('id'=>$item['parent']), array('task'=>'information'));
                        $error_date = $this->validate_min_max_date($arrParam['date_start'], $arrParam['date_end'], $parent_item['date_start'], $parent_item['date_end']);
                        if(!empty($error_date)) {
                            $flagError = true;
                            $errors['date_start'] = $error_date;
                            $errors['date_end']   = '.';
                        }
                    }
                }

				if($flagError == false) {
					$max_percent = $this->MTasks->getMaxPercent($arrParam['parent'], $arrParam['project_id'], $arrParam['id']);
					// valid percent
					if($arrParam['percent'] > $max_percent) {
						$flagError = true;
						$errors['percent'] = 'Tỷ lệ không được quá ' . $max_percent . '%';
					}
				}
			}

			if($flagError == false) {
				// covert trangthai and progress
				if($arrParam['trangthai'] == 2 || $arrParam['progress'] == 100) {
					$arrParam['trangthai'] = 2;
					$arrParam['progress'] = 100;
				}elseif($arrParam['progress'] > 0 && $arrParam['trangthai'] == 0) {
					$arrParam['trangthai'] = 1;
				}
				
				$arrParam['created_by'] = $item['created_by'];

				$this->MTasks->saveItem($arrParam, array('task'=>'edit'));

                //update time for tasks child

                $params = $item;
                $params['date_start'] = $arrParam['date_start'];
                $params['date_end']   = $arrParam['date_end'];

                $task_items = $this->MTasks->getItems($params, array('task'=>'update-task'));

                if(!empty($task_items)) {
                   $this->update_time_for_tasks_child($task_items, $arrParam['date_start'], $arrParam['date_end']);
                }

				// update progress
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
						$is_create_task_parent[] = $val['id'];
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
			if($item['parent'] == 0) { // project
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

			}else { // tasks
                if(in_array('update_all_task', $task_permission)){
                        $view = 'tasks/editform_view';
                }elseif(in_array($user_info['id'], $is_implement) && in_array('update_brand_task', $task_permission)) {
                        $view = 'tasks/editform_view';
                }elseif(in_array($user_info['id'], $is_create_task_parent)){
                        $view = 'tasks/editform_view';

                }elseif(in_array($user_info['id'], $is_implement)) {
                        $view = 'tasks/quickupdate_view';
                }
                elseif(in_array($user_info['id'], $is_xem) || in_array($user_info['id'], $is_pheduyet_parent)) {
                    $this->_data['no_comment'] = true;
                    $this->_data['is_xem'] 	   = true;
                    $view = 'tasks/detail_view';
                }
			}

			//project/task brands
			$this->_data['slbTasks'] = $this->MTasks->itemSelectBox(array('project_id'=>$item['project_id'], 'lft'=>$item['lft'], 'rgt'=>$item['rgt']));
			if(!empty($view))
				$this->load->view($view,$this->_data);
		}
	}

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
			if($item['pheduyet'] != 1 && $item['pheduyet'] != 2) {
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
						
					$arrParam['pheduyet'] = -1;
					if(in_array('update_project', $task_permission))
						$arrParam['pheduyet'] = 2;
					elseif(in_array($user_info['id'], $is_implement) && in_array('update_brand_task', $task_permission))
						$arrParam['pheduyet'] = 2;
					elseif(count($is_progress) == 0)
						$arrParam['pheduyet'] = 2;

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


		if(!empty($post)) {
			$arrParam = $this->_data['arrParam'];
			$this->load->library("form_validation");
			$this->form_validation->set_rules('name', 'Tên tài liệu', 'required|max_length[255]|is_unique[task_files.name]');
			$this->form_validation->set_rules('file_name', 'Tên file', 'required|max_length[255]');

			if($this->form_validation->run($this) == FALSE){
				$errors = $this->form_validation->error_array();
				$flagError = true;
			}else {
				if($_FILES["file_upload"]['name'] != ""){
					$upload_dir = FILE_TASK_PATH;
                    $ext = pathinfo($_FILES["file_upload"]['name'], PATHINFO_EXTENSION);
                    $file_name = rewriteUrl($post['file_name']);

					$config['upload_path'] = $upload_dir;
					$config['allowed_types'] = 'jpg|png|pdf|docx|doc|xls|xlsx|zip|zar';
					$config['max_size']	= '10240';
					$config['encrypt_name'] = FALSE;

					$config['file_name'] = $file_name . '.' . $ext;
                    if (file_exists($upload_dir . $file_name . '.' . $ext)) {
                        $config['file_name'] = $file_name . time() . '.' . $ext;
                    }

					$this->load->library('upload', $config);
	
					if($this->upload->do_upload("file_upload")){
						$file_info             = $this->upload->data();
                        $arrParam['size']      = $_FILES['file_upload']['size'];
                        $arrParam['extension'] = $ext;
                        $arrParam['file_name'] = $config['file_name'];

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

		$this->load->model('MTaskFiles');
		$item		 = $this->MTaskFiles->getItem($this->_data['arrParam'], array('task'=>'public-info'));

		if(!empty($post)) {
			$arrParam 			   = $this->_data['arrParam'];
            $arrParam['file_name'] = trim($arrParam['file_name']);
			$arrParam['task_id']   = $item['task_id'];

			$this->load->library("form_validation");
			$flagError = false;
            $upload_dir = FILE_TASK_PATH;

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

				if($flagError == false) {
                    $ext        = pathinfo($_FILES["file_upload"]['name'], PATHINFO_EXTENSION);
                    $file_name  = rewriteUrl($post['file_name']);

					// remove file cũ
					@unlink($upload_dir . $item['file_name']);
					
					$config['upload_path'] = $upload_dir;
					$config['allowed_types'] = 'jpg|png|pdf|docx|doc|xls|xlsx|zip|zar';
					$config['max_size']	= '10240';
					$config['encrypt_name'] = FALSE;
					$config['file_name'] = $file_name . '.' . $ext;

                    if (file_exists($upload_dir . $file_name . '.' . $ext)) {
                        $config['file_name'] = $file_name . time() . '.' . $ext;
                    }

					$this->load->library('upload', $config);

					if($this->upload->do_upload("file_upload")){
						$file_info = $this->upload->data();
						$arrParam['size']       = $_FILES['file_upload']['size'];
                        $arrParam['extension']  = $ext;
                        $arrParam['file_name']  = $config['file_name'];

					}else{
						$flagError = true;
						$err = $this->upload->display_errors();
						$errors['file_upload'] = $fileError[$err];
					}
				}
					
			}else {
				$this->form_validation->set_rules('name', 'Tên tài liệu', 'required|max_length[255]');
                $this->form_validation->set_rules('file_name', 'Tên file', 'required|max_length[255]');
	
				if($this->form_validation->run($this) == FALSE){
                    $flagError = true;
					$errors    = $this->form_validation->error_array();
				}

                $new_file_name = rewriteUrl($arrParam['file_name']) . '.' . $item['extension'];

                if(!isset($errors['file_name']) && $new_file_name != $item['file_name']) {
                    if (file_exists($upload_dir . $new_file_name)) {
                        $flagError           = true;
                        $errors['file_name'] = 'Tên File đã được sử dụng';
                    }else
                        $item['file_name'] = $new_file_name;
                }

				if($flagError == false) {
					$arrParam['file_name']       = $item['file_name'];
                    $arrParam['extension']       = $item['extension'];
					$arrParam['size'] 	         = $item['size'];
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
                $type   = 'content';
	
				$response = array('flag'=>'false', 'msg'=>current($errors), 'type' => $type);
			}else {
				$this->MTaskComment->saveItem($arrParam, array('task'=>'add'));
				$response = array('flag'=>'true', 'msg'=>'Bình luận thành công', 'task_id'=>$arrParam['task_id']);
			}
				
			echo json_encode($response);
		}
	}
	
	public function link()  {
		$this->load->model('MTasksLinks');
        $this->load->model('MTasks');
		$post  = $this->input->post();

		$this->load->library('MY_System_Info');
		$info 		= new MY_System_Info();
		$user_info = $info->getInfo();

		//user permission
		$task_permission = $user_info['task_permission'];

		if(!empty($post)) {
			$item = $this->MTasks->getItem(array('id'=>$post['source']), array('task'=>'public-info', 'brand'=>'full'));

            $is_create_task_parent  = $is_implement = array();
			if(!empty($item['is_create_task'])) {
				foreach($item['is_create_task'] as $key => $val){
					$keyArr = explode('-', $key);
					if($keyArr[0] != $post['source'])
						$is_create_task_parent[] = $val['id'];
				}

				$is_create_task_parent = array_unique($is_create_task_parent);
			}


            if(!empty($item['is_implement'])) {
                foreach($item['is_implement'] as $val)
                    $is_implement[] = $val['id'];

                $is_implement = array_unique($is_implement);
            }

			$flag = 'false';
            if(in_array('update_all_task', $task_permission)){
                $flag = 'true';
            }elseif(in_array($user_info['id'], $is_implement) && in_array('update_brand_task', $task_permission)) {
                $flag = 'true';
            }elseif(in_array($user_info['id'], $is_create_task_parent)){
                $flag = 'true';
            }

            if($flag == 'true') {
				$arrParam = $post;
				$arrParam['user_info'] = $user_info;
				$this->MTasksLinks->saveItem($arrParam, array('task'=>'add'));

                $msg = 'Thực hiện tác vụ thành công';
            }else
            	$msg = 'Bạn không có quyền thực hiện chức năng này.';

			$response = array('flag'=>$flag, 'msg'=>$msg);
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
		$this->load->model('MTasks');
        $this->load->model('MTaskProgress');
		$post     = $this->input->post();
		$arrParam = $this->_data['arrParam'];
		$item = $this->MTasks->getItem(array('id'=>$arrParam['task_id']), array('task'=>'public-info', 'brand'=>'full'));

		$this->load->library('MY_System_Info');
		$info 			= new MY_System_Info();
		$user_info 		= $info->getInfo();

		if(!empty($post)) {
            $is_pheduyet_parent = $is_pheduyet = array();
			if(!empty($item['is_pheduyet'])) {
				foreach($item['is_pheduyet'] as $key => $val){
					$is_pheduyet[] = $val['id'];

					$keyArr = explode('-', $key);
					if($keyArr[0] != $arrParam['id'])
						$is_pheduyet_parent[] = $val['id'];
				}
					
				$is_pheduyet_parent = array_unique($is_pheduyet_parent);
			}

            $flag = 'true';
            if(empty($item)) {
                $flag = 'false';
                $msg = 'Công việc không tồn tại.';
            }else {
                if(!in_array($user_info['id'], $is_pheduyet_parent) || $item['pheduyet'] != -1) {
                    $flag = 'fasle';
                    $msg = 'Không thực hiện được tác vụ';
                }

                if($flag == 'true') {
                    $check = $this->MTasks->check_parent_appoval($item);
                    if($check == true) {
                        $flag = 'false';
                        $msg = 'Không thực hiện được tác vụ vì công việc cha chưa hoặc không được phê duyệt';
                    }
                }
            }

            if($flag == 'true') {
                // update pheduyet
                $arrParam['id'] = $arrParam['task_id'];
                $this->MTasks->saveItem($arrParam, array('task'=>'pheduyet'));

                // if the task is not approval
                if($arrParam['pheduyet_select'] == 0) {
                    // percent = 0
                    $arrParam['fields'] = array('percent'=>0);
                    $this->MTasks->saveItem($arrParam, array('task'=>'custom'));

                    // remove progress
                    $this->MTaskProgress->deleteItem(array('task_ids'=>array($arrParam['id'])), array('task'=>'delete-multi-by-task'));
                }
            }

            $response = array('flag'=>$flag, 'msg'=>$msg);

			echo json_encode($response);
		}else {
            $this->load->view('tasks/pheduyet_view',$this->_data);
        }
	}
	
	public function quickupdate() {
		$post 	  = $this->input->post();
		$arrParam = $this->_data['arrParam'];
		$this->load->model('MTasks');
		if(!empty($post)) {
            $flag = 'true';
            $item = $this->MTasks->getItem($arrParam, array('task'=>'information'));
            if(empty($item)) {
                $flag = 'false';
                $msg = 'Dự án/ Công việc này không tồn tại.';
            }else {
				if($item['parent'] > 0) {
					$parent_item = $this->MTasks->getItem(array('id'=>$item['parent']), array('task'=>'information'));
					$error_date = $this->validate_min_max_date($arrParam['date_start'], $arrParam['date_end'], $parent_item['date_start'], $parent_item['date_end']);
					if(!empty($error_date)) {
						$flag = 'false';
						$msg  = $error_date;
					}	
				}
            }
			
			if($flag == 'true') {
                // update the task
                $date_start = str_replace('/', '-', $arrParam['date_start']);
                $arrParam['date_start'] = date('Y-m-d', strtotime($date_start));

                $date_end = str_replace('/', '-', $arrParam['date_end']);
                $arrParam['date_end'] = date('Y-m-d', strtotime($date_end));

                $datediff = strtotime($arrParam['date_end']) - strtotime($arrParam['date_start']);
                $arrParam['duration'] = floor($datediff/(60*60*24)) + 1;

                $this->MTasks->saveItem($arrParam, array('task'=>'quick-update'));

                $msg = 'Cập nhật thành công.';
			}

            $response = array('flag'=>$flag, 'msg'=>$msg);
            echo json_encode($response);
		}
	}
	
	public function deletecv() {
		$post 	  = $this->input->post();
		$this->load->model('MTasks');
		$this->load->model('MTaskProgress');
        $this->load->model('MTaskFiles');
        $this->load->model('MTaskComment');
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
            // delete files tasks
            $this->MTaskFiles->deleteItem(array('task_ids'=>$arrParam['ids']), array('task'=>'delete-by-tasks'));

            // delete comment
            $this->MTaskComment->deleteItem(array('task_ids'=>$arrParam['ids']), array('task'=>'delete-multi-by-task'));

            // delete progress
            $this->MTaskProgress->deleteItem(array('task_ids'=>$arrParam['ids']), array('task'=>'delete-multi-by-task'));

        }
	}
	
	public function template() { 
		$this->load->view('tasks/template_view',$this->_data);
	}
	
	public function templatelist() {
		$this->load->model('MTaskTemplate');
		$this->_paginator['per_page'] 	   	  = 10;
		$this->_data['arrParam']['paginator'] = $this->_paginator;
		
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
	
	public function editTemplate() {
		$id = $this->uri->segment(3);
		$this->load->view('tasks/editTemplate_view',$this->_data);
	}
	
	public function deleteTemplate() {
		$post  = $this->input->post();
		if(!empty($post)) {
			$this->load->model('MTaskTemplate');
			$this->MTaskTemplate->deleteItem($this->_data['arrParam'], array('task'=>'delete'));
		}
	}
	
	public function addcvtemplate() {
		$this->load->view('tasks/addcvtemplate_view',$this->_data);
	}
	
	public function project() {
		$this->load->view('tasks/project_view',$this->_data);
	}
	
	public function addProject() {
		$this->load->view('tasks/projectAdd_view',$this->_data);
	}
	
	public function projectlist() {
		$this->load->model('MTasks');
		$this->_paginator['per_page']    	  = 20;
		$this->_data['arrParam']['paginator'] = $this->_paginator;
		$post  = $this->input->post();
	
		if(!empty($post)) {
			$config['base_url'] = base_url() . 'tasks/projectlist';
			$config['total_rows'] = $this->MTasks->countItem($this->_data['arrParam'], array('task'=>'public-list'));
	
			$config['per_page'] = $this->_paginator['per_page'];
			$config['uri_segment'] = $this->_paginator['uri_segment'];
			$config['use_page_numbers'] = TRUE;
	
			$this->load->library("pagination");
			$this->pagination->initialize($config);
			$this->pagination->createConfig('front-end');
	
			$pagination = $this->pagination->create_ajax();
	
			$this->_data['arrParam']['start'] = $this->uri->segment(3);
			$items = $this->MTasks->listItem($this->_data['arrParam'], array('task'=>'public-list'));
			
			$result = array('count'=> $config['total_rows'], 'items'=>$items, 'pagination'=>$pagination);
			echo json_encode($result);
		}
	}
	
	public function sort() {
		$this->load->model('MTasks');
		$post  = $this->input->post();
		if(!empty($post)) {
			$checkExist = $this->MTasks->checkItemExist($this->_data['arrParam']['id']);
			if($checkExist) {
				$this->MTasks->sort($this->_data['arrParam']);
				$resonse = array('flag'=>'true', 'msg'=>'Cập nhật thành công.');
			}else
				$resonse = array('flag'=>'false', 'msg'=>'Dự án/ Công việc không tồn tại.');
			
			echo json_encode($resonse);
		}
	}
	
	public function projectGridList() {
		$this->load->model('MTasks');
		$post  = $this->input->post();

		if(!empty($post)) {
			$config['base_url'] = base_url() . 'tasks/gridList';
			$config['total_rows'] = $this->MTasks->countItem($this->_data['arrParam'], array('task'=>'grid-project'));

			$config['per_page'] = $this->_paginator['per_page'];
			$config['uri_segment'] = $this->_paginator['uri_segment'];
			$config['use_page_numbers'] = TRUE;
		
			$this->load->library("pagination");
			$this->pagination->initialize($config);
			$this->pagination->createConfig('front-end');
		
			$pagination = $this->pagination->create_ajax();
		
			$this->_data['arrParam']['start'] = $this->uri->segment(3);
			$items = $this->MTasks->listItem($this->_data['arrParam'], array('task'=>'grid-project'));

			$result = array('count'=> $config['total_rows'], 'items'=>$items, 'pagination'=>$pagination);
			echo json_encode($result);
		}
	}

    public function tasks_child_statistic() {
        $post  = $this->input->post();
        $this->load->model('MTasks');
        if(!empty($post)) {
            $all              = $this->MTasks->statistic($this->_data['arrParam'], array('task'=>'task-by-project'));
            $implement        = $this->MTasks->statistic($this->_data['arrParam'], array('task'=>'task-by-project-implement'));
            $xem              = $this->MTasks->statistic($this->_data['arrParam'], array('task'=>'task-by-project-cc'));
            $cancel           = $this->MTasks->statistic($this->_data['arrParam'], array('task'=>'task-by-project-trangthai', 'type'=>'cancel'));
            $not_done         = $this->MTasks->statistic($this->_data['arrParam'], array('task'=>'task-by-project-trangthai', 'type'=>'not-done'));
            $unfulfilled      = $this->MTasks->statistic($this->_data['arrParam'], array('task'=>'task-by-project-trangthai', 'type'=>'unfulfilled'));
            $processing       = $this->MTasks->statistic($this->_data['arrParam'], array('task'=>'task-by-project-trangthai', 'type'=>'processing'));
            $slow_proccessing = $this->MTasks->statistic($this->_data['arrParam'], array('task'=>'task-by-project-trangthai', 'type'=>'slow_proccessing'));
            $finish           = $this->MTasks->statistic($this->_data['arrParam'], array('task'=>'task-by-project-trangthai', 'type'=>'finish'));
            $slow_finish      = $this->MTasks->statistic($this->_data['arrParam'], array('task'=>'task-by-project-trangthai', 'type'=>'slow-finish'));

            $data = array(
                'all'              => $all,
                'implement'        => $implement,
                'xem'              => $xem,
                'cancel'           => $cancel,
                'not_done'         => $not_done,
                'unfulfilled'      => $unfulfilled,
                'processing'       => $processing,
                'slow_proccessing' => $slow_proccessing,
                'finish'           => $finish,
                'slow_finish'      => $slow_finish,
            );

            echo json_encode($data);
        }
    }

	public function taskByProjectList() {
		$this->load->model('MTasks');
		$post  = $this->input->post();

		if(!empty($post)) {
			$project_id = $this->_data['arrParam']['project_id'];

			$result  = $this->MTasks->listItem($this->_data['arrParam'], array('task'=>'task-by-project'));

			$project = $result['project'];
			$items   = $result['ketqua'];

			$items   = array_merge($items, array());
            $items   = (!empty($items)) ? $items : array();

			$result = array('items'=>$items, 'project'=>$project);

			echo json_encode($result);
		}
	}

	public function grid() {
        $this->load->library('MY_System_Info');
        $info 			 = new MY_System_Info();
        $user_info 		 = $info->getInfo();
        $this->_data['user_info'] = $user_info;

		$this->load->view('tasks/grid_view', $this->_data);
	}

    public function add_personal() {
        $arrParam   = $this->_data['arrParam'];
		$this->load->model('MTaskPersonal');
		$this->load->model('MTaskPersonalProgress');
        $post  = $this->input->post();
        if(!empty($post)) {
			$this->load->library("form_validation");
			$this->form_validation->set_rules('name', 'Tiêu đề', 'required|max_length[255]');
			$this->form_validation->set_rules('progress', 'Tiến độ', 'required|greater_than[-1]|less_than[101]');
			$this->form_validation->set_rules('date_start', 'Bắt đầu', 'required');
			$this->form_validation->set_rules('date_end', 'Kết thúc', 'required');
			
			$flagError = false;
	
			if($this->form_validation->run($this) == FALSE){
				$errors = $this->form_validation->error_array();
                if(isset($errors['date_start']) && !isset($errors['date_end']))
                    $errors['date_end'] = '.';

                if(!isset($errors['date_start']) && isset($errors['date_end']))
                    $errors['date_start'] = '.';

                $flagError = true;
			}else {
				// time valid
				$arrParam['date_start'] = date('Y-m-d', strtotime($arrParam['date_start']));

				$arrParam['date_end']   = date('Y-m-d', strtotime($arrParam['date_end']));

				$datediff = strtotime($arrParam['date_end']) - strtotime($arrParam['date_start']);
				$arrParam['duration'] = floor($datediff/(60*60*24));
				if($arrParam['duration'] <= 0) {
					$flagError = true;
					$errors['date_start'] = 'Ngày kết thúc phải sau ngày bắt đầu.';
					$errors['date_end']   = '.';
				}
			}
			if($flagError == false) {
				// covert status and progress
				$arrParam = $this->convert_progress_task($arrParam);
				$last_id = $this->MTaskPersonal->saveItem($arrParam, array('task'=>'add'));
				
				 //update first progress
                $params = array(
                    'task_id'  => $last_id               , 'trangthai' => $arrParam['trangthai'],
                    'prioty'   => $arrParam['prioty']    , 'progress'  => $arrParam['progress'],
					'note'      => '', 
                );

                $this->MTaskPersonalProgress->saveItem($params, array('task'=>'add'));
				 
				$response = array('flag'=>'true', 'msg'=>'Cập nhật thành công');
			}else {
				$response = array('flag'=>'false', 'errors'=>$errors);
			}
			
			echo json_encode($response);
			
        }else {
            $this->load->library('MY_System_Info');
            $info 			 = new MY_System_Info();
            $user_info 		 = $info->getInfo();
            $this->_data['user_info'] = $user_info;

            $this->load->view('tasks/add_personal_form_view', $this->_data);
        }
    }

    public function edit_personal() {
		$this->load->model('MTaskPersonal');
		
		$arrParam = $this->_data['arrParam'];
		$post 	    = $this->input->post();
		$item = $this->MTaskPersonal->getItem(array('id'=>$arrParam['id']), array('task'=>'public-info'));

		if(!empty($post)) {
			$this->load->library("form_validation");
			$this->form_validation->set_rules('name', 'Tiêu đề', 'required|max_length[255]');
			$this->form_validation->set_rules('progress', 'Tiến độ', 'required|greater_than[-1]|less_than[101]');
			$this->form_validation->set_rules('date_start', 'Bắt đầu', 'required');
			$this->form_validation->set_rules('date_end', 'Kết thúc', 'required');

			$flagError = false;
	
			if($this->form_validation->run($this) == FALSE){
				$errors = $this->form_validation->error_array();
                if(isset($errors['date_start']) && !isset($errors['date_end']))
                    $errors['date_end'] = '.';

                if(!isset($errors['date_start']) && isset($errors['date_end']))
                    $errors['date_start'] = '.';

                $flagError = true;
			}else {
				// time valid
				$arrParam['date_start'] = date('Y-m-d', strtotime($arrParam['date_start']));

				$arrParam['date_end']   = date('Y-m-d', strtotime($arrParam['date_end']));

				$datediff = strtotime($arrParam['date_end']) - strtotime($arrParam['date_start']);
				$arrParam['duration'] = floor($datediff/(60*60*24));
				if($arrParam['duration'] <= 0) {
					$flagError = true;
					$errors['date_start'] = 'Ngày kết thúc phải sau ngày bắt đầu.';
					$errors['date_end']   = '.';
				}
			}

			if($flagError == false) {
				// covert status and progress
				$arrParam = $this->convert_progress_task($arrParam);
				$last_id = $this->MTaskPersonal->saveItem($arrParam, array('task'=>'edit'));
				
				$response = array('flag'=>'true', 'msg'=>'Cập nhật thành công');
			}else {
				$response = array('flag'=>'false', 'errors'=>$errors);
			}

		}else {
            $this->load->library('MY_System_Info');
            $info 			 = new MY_System_Info();
            $user_info 		 = $info->getInfo();
			$id_admin = $user_info['id'];
			
			if($id_admin == $item['created_by'])
				$view = 'tasks/edit_personal_form_view';
			elseif(in_array($id_admin, $item['implement_ids']) || in_array($id_admin, $item['xem_ids']))
				$view = 'tasks/quickupdate_personal_view';

            $this->_data['item'] = $item;
			if(!empty($view))
				$this->load->view($view, $this->_data);
		}
    }

    public function delete_personal() {
        $post  = $this->input->post();
        if(!empty($post)) {
            $cid = $this->_data['arrParam']['ids'];
            $this->load->model('MTaskPersonal');
            $this->load->model('MTaskPersonalProgress');
            $this->load->model('MTaskPersonalFiles');
            $this->load->model('MTaskPersonalComment');

            $this->MTaskPersonal->deleteItem(array('cid'=>$cid), array('task'=>'delete-multi'));
            $this->MTaskPersonalProgress->deleteItem(array('cid'=>$cid), array('task'=>'delete-multi-by-task'));
            $this->MTaskPersonalFiles->deleteItem(array('cid'=>$cid), array('task'=>'delete-by-tasks'));
            $this->MTaskPersonalComment->deleteItem(array('cid'=>$cid), array('task'=>'delete-multi-by-task'));
        }
    }

    public function personal() {
        $this->load->view('tasks/personal_grid_view', $this->_data);
    }

    public function personalList() {
        $this->load->model('MTaskPersonal');
        $this->_paginator['per_page']    	  = 20;
        $this->_data['arrParam']['paginator'] = $this->_paginator;
        $post  = $this->input->post();

        if(!empty($post)) {
            $config['base_url'] = base_url() . 'tasks/personalList';
            $config['total_rows'] = $this->MTaskPersonal->countItem($this->_data['arrParam']);

            $config['per_page'] = $this->_paginator['per_page'];
            $config['uri_segment'] = $this->_paginator['uri_segment'];
            $config['use_page_numbers'] = TRUE;

            $this->load->library("pagination");
            $this->pagination->initialize($config);
            $this->pagination->createConfig('front-end');

            $pagination = $this->pagination->create_ajax();

            $this->_data['arrParam']['start'] = $this->uri->segment(3);
            $items = $this->MTaskPersonal->listItem($this->_data['arrParam']);

            $result = array('count'=> $config['total_rows'], 'items'=>$items, 'pagination'=>$pagination);
            echo json_encode($result);
       }
    }

    public function add_personal_tiendo() {
        $this->load->model('MTaskPersonalProgress');
        $this->load->model('MTaskPersonal');
        $post  = $this->input->post();
        $arrParam = $this->_data['arrParam'];

        $item = $this->MTaskPersonal->getItem(array('id'=>$arrParam['task_id']), array('task'=>'information'));
        if(!empty($post)) {
            $flag = 'true';
            if(empty($item)){
                $flag = 'false';
                $msg  = 'Công việc không tồn tại';
            }else {
                $this->form_validation->set_rules('progress', 'Tiến độ', 'required|greater_than[-1]|less_than[101]');
                if($this->form_validation->run($this) == FALSE){
                    $errors = $this->form_validation->error_array();
                    $flag = 'false';
                    $msg = current($errors);
                }
            }

            if($flag == 'true') {
				$arrParam = $this->convert_progress_task($arrParam);
				$params = array(
					'id' => $arrParam['task_id'],
					'trangthai' => $arrParam['trangthai'],
					'progress' => $arrParam['progress']
				);
                $this->MTaskPersonalProgress->saveItem($arrParam, array('task'=>'add'));
				$this->MTaskPersonal->saveItem($params, array('task'=>'update-progress'));
				
				$msg = 'Cập nhật thành công.';
                
            }
			
			$response = array('flag'=>$flag, 'msg'=>$msg);
			
			echo json_encode($response);
        }else {
            $this->_data['item'] = $item;
            $this->load->view('tasks/add_personal_tiendo_view', $this->_data);
        }
    }

    public function personal_progress_list() {
        $this->load->model('MTaskPersonalProgress');
        $post  = $this->input->post();

        if(!empty($post)) {
            $config['base_url'] = base_url() . 'tasks/personal_progress_list';
            $config['total_rows'] = $this->MTaskPersonalProgress->countItem($this->_data['arrParam'], array('task'=>'public-list'));

            $config['per_page'] = $this->_paginator['per_page'];
            $config['uri_segment'] = $this->_paginator['uri_segment'];
            $config['use_page_numbers'] = TRUE;

            $this->load->library("pagination");
            $this->pagination->initialize($config);
            $this->pagination->createConfig('front-end');

            $pagination = $this->pagination->create_ajax();

            $this->_data['arrParam']['start'] = $this->uri->segment(3);
            $items = $this->MTaskPersonalProgress->listItem($this->_data['arrParam'], array('task'=>'public-list'));

            $result = array('count'=> $config['total_rows'], 'items'=>$items, 'pagination'=>$pagination);

            echo json_encode($result);
        }
    }

    public function add_personal_file() {
		$fileError = array(
				'<p>The filetype you are attempting to upload is not allowed.</p>'=>'File tải lên phải có định dạng jpg|png|pdf|docx|doc|xls|xlsx|zip|zar',
				'<p>The file you are attempting to upload is larger than the permitted size.</p>' => 'File tải lên không được quá 10 Mb'
		);
		$post  = $this->input->post();

		if(!empty($post)) {
			$arrParam = $this->_data['arrParam'];
			$this->load->library("form_validation");
			$this->form_validation->set_rules('name', 'Tên tài liệu', 'required|max_length[255]|is_unique[task_files.name]');
			$this->form_validation->set_rules('file_name', 'Tên file', 'required|max_length[255]|is_unique[task_files.file_name]');
	
			if($this->form_validation->run($this) == FALSE){
				$errors = $this->form_validation->error_array();

				$flagError = true;
			}else {
				if($_FILES["file_upload"]['name'] != ""){
					$upload_dir = FILE_TASK_PATH;
                    $ext = pathinfo($_FILES["file_upload"]['name'], PATHINFO_EXTENSION);
                    $file_name = rewriteUrl($post['file_name']);

					$config['upload_path'] = $upload_dir;
					$config['allowed_types'] = 'jpg|png|pdf|docx|doc|xls|xlsx|zip|zar';
					$config['max_size']	= '10240';
					$config['encrypt_name'] = FALSE;

					$config['file_name'] = $file_name . '.' . $ext;
                    if (file_exists($upload_dir . $file_name . '.' . $ext)) {
                        $config['file_name'] = $file_name . time() . '.' . $ext;
                    }

					$this->load->library('upload', $config);
	
					if($this->upload->do_upload("file_upload")){
						$file_info             = $this->upload->data();
                        $arrParam['size']      = $_FILES['file_upload']['size'];
                        $arrParam['extension'] = $ext;
                        $arrParam['file_name'] = $config['file_name'];

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
				$this->load->model('MTaskPersonalFiles');
				$this->MTaskPersonalFiles->saveItem($arrParam, array('task'=>'add'));
	
				$respon = array('flag'=>'true', 'message'=>'Cập nhật thành công');
			}
			
			echo json_encode($respon);
		}else
			$this->load->view('tasks/add_personal_file_view',$this->_data);

    }

    public function edit_personal_file() {
        $this->load->model('MTaskPersonalFiles');
		$fileError = array(
				'<p>The filetype you are attempting to upload is not allowed.</p>'=>'File tải lên phải có định dạng jpg|png|pdf|docx|doc|xls|xlsx|zip|zar',
				'<p>The file you are attempting to upload is larger than the permitted size.</p>' => 'File tải lên không được quá 10 Mb'
		);

		$item   = $this->MTaskPersonalFiles->getItem($this->_data['arrParam'], array('task'=>'public-info'));
        $post  	= $this->input->post();
        if(!empty($post)) {
			$arrParam 			   = $this->_data['arrParam'];
            $arrParam['file_name'] = trim($arrParam['file_name']);
			$arrParam['task_id']   = $item['task_id'];

			$this->load->library("form_validation");
			$flagError = false;
            $upload_dir = FILE_TASK_PATH;

            if($_FILES["file_upload"]['name'] != ""){
				$this->form_validation->set_rules('name', 'Tên tài liệu', 'required|max_length[255]');
				$this->form_validation->set_rules('file_name', 'Tên file', 'required|max_length[255]');

				if($this->form_validation->run($this) == FALSE){
					$errors = $this->form_validation->error_array();
					$flagError = true;
				}
					
				if($flagError == false){
					$flagError = $this->MTaskPersonalFiles->validate($arrParam['name'], 'name', $arrParam['id']);
					if($flagError == true)
						$errors['name'] = 'Tên tài liệu đã tồn tại.';
				}

				if($flagError == false) {
                    $ext        = pathinfo($_FILES["file_upload"]['name'], PATHINFO_EXTENSION);
                    $file_name  = rewriteUrl($post['file_name']);

					// remove file cũ
					@unlink($upload_dir . $item['file_name']);
					
					$config['upload_path'] = $upload_dir;
					$config['allowed_types'] = 'jpg|png|pdf|docx|doc|xls|xlsx|zip|zar';
					$config['max_size']	= '10240';
					$config['encrypt_name'] = FALSE;
					$config['file_name'] = $file_name . '.' . $ext;

                    if (file_exists($upload_dir . $file_name . '.' . $ext)) {
                        $config['file_name'] = $file_name . time() . '.' . $ext;
                    }

					$this->load->library('upload', $config);

					if($this->upload->do_upload("file_upload")){
						$file_info = $this->upload->data();
						$arrParam['size']       = $_FILES['file_upload']['size'];
                        $arrParam['extension']  = $ext;
                        $arrParam['file_name']  = $config['file_name'];

					}else{
						$flagError = true;
						$err = $this->upload->display_errors();
						$errors['file_upload'] = $fileError[$err];
					}
				}

            }else {
				$this->form_validation->set_rules('name', 'Tên tài liệu', 'required|max_length[255]');
                $this->form_validation->set_rules('file_name', 'Tên file', 'required|max_length[255]');
	
				if($this->form_validation->run($this) == FALSE){
                    $flagError = true;
					$errors    = $this->form_validation->error_array();
				}

                $new_file_name = rewriteUrl($arrParam['file_name']) . '.' . $item['extension'];

                if(!isset($errors['file_name']) && $new_file_name != $item['file_name']) {
                    if (file_exists($upload_dir . $new_file_name)) {
                        $flagError           = true;
                        $errors['file_name'] = 'Tên File đã được sử dụng';
                    }else
                        $item['file_name'] = $new_file_name;
                }

				if($flagError == false) {
					$arrParam['file_name']       = $item['file_name'];
                    $arrParam['extension']       = $item['extension'];
					$arrParam['size'] 	         = $item['size'];
				}
            }

           	if($flagError == true) {
				$respon = array('flag'=>'false', 'errors'=>$errors);
			}else {
				$this->MTaskPersonalFiles->saveItem($arrParam, array('task'=>'edit'));
	
				$respon = array('flag'=>'true', 'message'=>'Cập nhật thành công');
			}
	
			echo json_encode($respon);

        }else {
        	$this->_data['item'] = $item;            
        	$this->load->view('tasks/edit_personal_file_view',$this->_data);
        }
    }

    public function delete_personal_file() {
		$post  = $this->input->post();
	
		if(!empty($post)) {
			$this->load->model('MTaskPersonalFiles');
			$cid = $this->_data['arrParam']['file_ids'];

            $this->MTaskPersonalFiles->deleteItem(array('cid'=>$cid), array('task'=>'delete-multi'));
		}
    }
     
    public function personel_file_list() {
		$this->load->model('MTaskPersonalFiles');
		$post  = $this->input->post();
	
		if(!empty($post)) {
			$config['base_url'] = base_url() . 'tasks/personel_file_list';
            $config['total_rows'] = $this->MTaskPersonalFiles->countItem($this->_data['arrParam'], array('task'=>'public-list'));

            $config['per_page'] = $this->_paginator['per_page'];
			$config['uri_segment'] = $this->_paginator['uri_segment'];
			$config['use_page_numbers'] = TRUE;
				
			$this->load->library("pagination");
			$this->pagination->initialize($config);
			$this->pagination->createConfig('front-end');
				
			$pagination = $this->pagination->create_ajax();
	
			$this->_data['arrParam']['start'] = $this->uri->segment(3);
			$items = $this->MTaskPersonalFiles->listItem($this->_data['arrParam'], array('task'=>'public-list'));
	
			$result = array('count'=> $config['total_rows'], 'items'=>$items, 'pagination'=>$pagination);
				
			echo json_encode($result);
		}
    }

    public function add_personal_comment() {
		$this->load->model('MTaskPersonalComment');
		$post  	  = $this->input->post();
		$arrParam = $this->_data['arrParam'];
	
		if(!empty($post)) {
			$this->form_validation->set_rules('content', 'Nội dung', 'required');
				
			if($this->form_validation->run($this) == FALSE){
				$errors = $this->form_validation->error_array();
                $type   = 'content';
	
				$response = array('flag'=>'false', 'msg'=>current($errors), 'type' => $type);
			}else {
				$this->MTaskPersonalComment->saveItem($arrParam, array('task'=>'add'));
				$response = array('flag'=>'true', 'msg'=>'Bình luận thành công', 'task_id'=>$arrParam['task_id']);
			}
				
			echo json_encode($response);
		}
    }

    public function personal_comment_list() {
		$this->load->model('MTaskPersonalComment');
		$post  = $this->input->post();
		if(!empty($post)) {
			$config['base_url'] = base_url() . 'tasks/personal_comment_list';
			$config['total_rows'] = $this->MTaskPersonalComment->countItem($this->_data['arrParam'], array('task'=>'public-list'));
			$config['per_page'] = $this->_paginator['per_page'];
			$config['uri_segment'] = $this->_paginator['uri_segment'];
			$config['use_page_numbers'] = TRUE;
				
			$this->load->library("pagination");
			$this->pagination->initialize($config);
			$this->pagination->createConfig('front-end');
				
			$pagination = $this->pagination->create_ajax();
	
			$this->_data['arrParam']['start'] = $this->uri->segment(3);
			$items = $this->MTaskPersonalComment->listItem($this->_data['arrParam'], array('task'=>'public-list'));
	
			$result = array('items'=>$items, 'pagination'=>$pagination);
				
			echo json_encode($result);
		}
    }

	public function test() {
//		$this->load->model('MTasks');
//		$this->MTasks->test();

        echo $ext        = pathinfo('tai liệu.doc', PATHINFO_EXTENSION);
        //$this->load->view('tasks/test_view', $this->_data);
	}
	
	public function valid_date($str){
		$regular_string = '/^(0[1-9]|[1-2][0-9]|3[0-1])-(0[1-9]|1[0-2])-[0-9]{4})$/';
		if (preg_match($regular_string, $str))
		{
			return true;
		}else{
			
			$this->form_validation->set_message('valid_date', '%s phải mang định dạng m-y-D');
			return false;
		}
	}
	
	public function validate_min_max_date($date_start, $date_end, $date_start_limit, $date_end_limit) {
		$error = '';
        $datediff_start    		= strtotime($date_start) - strtotime($date_start_limit);
        $datediff_end      		= strtotime($date_end) - strtotime($date_end_limit);

        $date_start_limit = date('d-m-Y', strtotime($date_start_limit));
        $date_end_limit   = date('d-m-Y', strtotime($date_end_limit));

        if($datediff_start < 0 || $datediff_end > 0) {
           $error = 'Thời gian chỉ trong khoảng từ ' . $date_start_limit . ' đến ' . $date_end_limit;
        }

		return $error;
	}
}