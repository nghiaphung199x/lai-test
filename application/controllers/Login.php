<?php
class Login extends MY_Controller 
{
	protected $_controller_name = 'login';

	function __construct()
	{
		parent::__construct();
		$this->lang->load('login');
	}
	
	function index()
	{
		$data = array();
		$this->load->helper('demo');
		$data['username'] = is_on_demo_host() ? 'admin' : '';
		$data['password'] = is_on_demo_host() ? 'pointofsale' : '';
		if ($this->agent->browser() == 'Internet Explorer' && $this->agent->version() < 11)
		{
			$data['ie_browser_warning'] = TRUE;
		}
		else
		{
			$data['ie_browser_warning'] = FALSE;
		}
		if(APPLICATION_VERSION==$this->config->item('version'))
		{
			$data['application_mismatch']=false;
		}
		else
		{
			$data['application_mismatch']=lang('login_application_mismatch');
		}
		
		if($this->Employee->is_logged_in())
		{
			redirect('home');
		}
		else
		{
			$this->form_validation->set_rules('username', 'lang:login_username', 'required|callback_employee_location_check|callback_login_check');
			$this->form_validation->set_message('required', lang('login_invalid_username_and_password'));
    	   $this->form_validation->set_error_delimiters('<div class="error">', '</div>');
			if($this->form_validation->run() == FALSE)
			{
				//Only set the username when we have a non false value (not '' or FALSE)
				if ($this->input->post('username'))
				{					
					$data['username'] = $this->input->post('username');
				}
				include APPPATH.'config/database.php';
				
				//If we have a site configuration check to make sure the user has not cancelled
				if (isset($db['site']))
				{
					$site_db = $this->load->database('site', TRUE);
					
					if ($this->_is_subscription_cancelled($site_db) || $this->_is_subscription_failed($site_db))
					{
						if ($this->_is_subscription_failed($site_db))
						{
							$data['subscription_payment_failed']  = TRUE;
							$this->load->view('login/login',$data);							
						}
						elseif ($this->_is_subscription_cancelled_within_5_days($site_db))
						{
							$data['subscription_cancelled_within_5_days']  = TRUE;
							$this->load->view('login/login',$data);
						}
						else
						{
							$this->load->view('login/subscription_cancelled');
						}
					}
					else
					{
						$this->load->view('login/login', $data);
					}
				}
				else
				{
					$this->load->view('login/login',$data);
				}
			}
			else
			{
				
				$logged_in_employee_info=$this->Employee->get_logged_in_employee_info();
				
				if ($logged_in_employee_info->force_password_change)
				{
					$this->Employee->logout(false);
					$data['username'] = $logged_in_employee_info->username;
					//Create key on the fly
					$data['key'] = $this->generate_reset_key($logged_in_employee_info->person_id);
					$data['force_password_change'] = TRUE;
					$this->load->view('login/reset_password_enter_password', $data);			
				}
				else
				{
					$number_of_locations = count($this->Employee->get_authenticated_location_ids($logged_in_employee_info->person_id));
					redirect('home/index/'.($number_of_locations > 1 ? '1' : '0'));
				}
			}
		}
	}
	
	function _is_subscription_cancelled($site_db)
	{
		$phppos_client_name = substr($_SERVER['HTTP_HOST'], 0, strpos($_SERVER['HTTP_HOST'], '.'));
		$site_db->select('subscr_status');	
		$site_db->from('subscriptions');	
		$site_db->where('username',$phppos_client_name);
		$site_db->where('subscr_status','cancelled');
		$query = $site_db->get();
		return ($query->num_rows() >= 1);
	}
	
	function _is_subscription_cancelled_within_5_days($site_db)
	{
		$phppos_client_name = substr($_SERVER['HTTP_HOST'], 0, strpos($_SERVER['HTTP_HOST'], '.'));
		$five_days_ago = date('Y-m-d H:i:s', strtotime("now -5 days"));
		$site_db->select('subscr_status');	
		$site_db->from('subscriptions');	
		$site_db->where('username',$phppos_client_name);
		$site_db->where('subscr_status','cancelled');
		$site_db->where('cancel_date >', $five_days_ago);
		$query = $site_db->get();
		return ($query->num_rows() >= 1);
	}
	
	function _is_subscription_failed($site_db)
	{
		$phppos_client_name = substr($_SERVER['HTTP_HOST'], 0, strpos($_SERVER['HTTP_HOST'], '.'));
		$site_db->select('subscr_status');	
		$site_db->from('subscriptions');	
		$site_db->where('username',$phppos_client_name);
		$site_db->where('subscr_status','failed');
		$query = $site_db->get();
		return ($query->num_rows() >= 1);
	}
	
	function login_check($username)
	{
		include APPPATH.'config/database.php';
		//If we have a site configuration check to make sure the user has not cancelled. We want to block login for cancelled users
		if (isset($db['site']))
		{
			$site_db = $this->load->database('site', TRUE);
		
			if ($this->_is_subscription_cancelled($site_db))
			{
				//If we are not cancelled within 5 days; block login
				if (!$this->_is_subscription_cancelled_within_5_days($site_db))
				{
					$this->form_validation->set_message('login_check', lang('login_invalid_username_and_password'));
					return false;
				}
			}
		}
		$password = $this->input->post("password");	
		
		if(!$this->Employee->login($username,$password))
		{
			$this->form_validation->set_message('login_check', lang('login_invalid_username_and_password'));
			return false;
		}
		return true;		
	}
	
	function employee_location_check($username)
	{		
		$employee_id = $this->Employee->get_employee_id($username);
		
		if ($employee_id)
		{
			$employee_location_count = count($this->Employee->get_authenticated_location_ids($employee_id));

			if ($employee_location_count < 1)
			{
				$this->form_validation->set_message('employee_location_check', lang('login_employee_is_not_assigned_to_any_locations'));
				return false;
			}
		}
		
		//Didn't find an employee, we can pass validation
		return true;
	}
		
	function switch_user($reload = 0)
	{
		
		if ($this->config->item('fast_user_switching')) 
		{
			if($this->input->post('username_or_account_number') || $this->input->post('username'))
			{
				if ($this->input->post('username_or_account_number'))
				{
					if (!$this->Employee->login_no_password($this->input->post('username_or_account_number')))
					{
						echo json_encode(array('success'=>false,'message'=>lang('login_invalid_username_and_password')));
					}
					else
					{
						if (!$this->config->item('keep_same_location_after_switching_employee') && $reload == 1)
						{
							//Unset location in case the user doesn't have access to currently set location
							$this->session->unset_userdata('employee_current_location_id');							
						}
						
						$emp_info = $this->Employee->get_logged_in_employee_info();
						$name = $emp_info->first_name. ' '.$emp_info->last_name;
						$avatar = $emp_info->image_id ?  site_url('app_files/view/'.$emp_info->image_id) : base_url('assets/assets/images/avatar-default.jpg');
						$is_clocked_in_or_timeclock_disabled = $this->Employee->is_clocked_in() || !$this->config->item('timeclock');
						echo json_encode(array('success'=>true,'reload' => $reload,'name' => $name,'avatar' => $avatar,'is_clocked_in_or_timeclock_disabled' => $is_clocked_in_or_timeclock_disabled));
					}
				}
				else
				{
					if (!$this->Employee->login_no_password($this->input->post('username')))
					{
						echo json_encode(array('success'=>false,'message'=>lang('login_invalid_username_and_password')));
					}
					else
					{
						if (!$this->config->item('keep_same_location_after_switching_employee') && $reload == 1)
						{
							//Unset location in case the user doesn't have access to currently set location
							$this->session->unset_userdata('employee_current_location_id');							
						}
						
						$is_clocked_in_or_timeclock_disabled = $this->Employee->is_clocked_in() || !$this->config->item('timeclock');
						
						$emp_info = $this->Employee->get_logged_in_employee_info();
						$name = $emp_info->first_name. ' '.$emp_info->last_name;
						$avatar = $emp_info->image_id ?  site_url('app_files/view/'.$emp_info->image_id) : base_url('assets/assets/images/avatar-default.jpg');

						echo json_encode(array('success'=>true,'reload' => $reload, 'name' => $name,'avatar' => $avatar,'is_clocked_in_or_timeclock_disabled' => $is_clocked_in_or_timeclock_disabled));
					}
				}
			}
			else
			{
				foreach($this->Employee->get_all()->result_array() as $row)
				{
					$employees[$row['username']] = $row['first_name'] .' '. $row['last_name'];
				}
				$data['employees']=$employees;
				$data['reload'] = $reload;
				$this->load->view('login/switch_user',$data);
			}
		}
		else
		{
		
			if($this->input->post('password'))
			{
				if(!$this->Employee->login($this->input->post('username'),$this->input->post('password')))
				{
					echo json_encode(array('success'=>false,'message'=>lang('login_invalid_username_and_password')));
				}
				else
				{
					if (!$this->config->item('keep_same_location_after_switching_employee') && $reload == 1)
					{
						//Unset location in case the user doesn't have access to currently set location
						$this->session->unset_userdata('employee_current_location_id');							
					}
					
					$is_clocked_in_or_timeclock_disabled = $this->Employee->is_clocked_in() || !$this->config->item('timeclock');
					
					$emp_info = $this->Employee->get_logged_in_employee_info();
					$name = $emp_info->first_name. ' '.$emp_info->last_name;
					$avatar = $emp_info->image_id ?  site_url('app_files/view/'.$emp_info->image_id) : base_url('assets/assets/images/avatar-default.jpg');

					
					echo json_encode(array('success'=>true,'reload' => $reload, 'name'=> $name,'avatar' => $avatar,'is_clocked_in_or_timeclock_disabled' => $is_clocked_in_or_timeclock_disabled));
				}
			}
			else
			{
				foreach($this->Employee->get_all()->result_array() as $row)
				{
					$employees[$row['username']] = $row['first_name'] .' '. $row['last_name'];
				}
				$data['employees']=$employees;
				$data['reload'] = $reload;
				$this->load->view('login/switch_user',$data);
			
			}
		}
	}
	
	function edit_profile()
	{
		$data = array();
		$employee_person_id = $this->Employee->get_logged_in_employee_info()->person_id;
		$data['person_info']=$this->Employee->get_info($employee_person_id);
		$data['controller_name']=$this->_controller_name;
		
		$this->load->view('login/edit_profile', $data);
		
	}
	
	function do_edit_profile()
	{
		$employee_id = $this->Employee->get_logged_in_employee_info()->person_id;
		
		$person_data = array(
		'first_name'=>$this->input->post('first_name'),
		'last_name'=>$this->input->post('last_name'),
		'email'=>$this->input->post('email'),
		'phone_number'=>$this->input->post('phone_number'),
		'address_1'=>$this->input->post('address_1'),
		'address_2'=>$this->input->post('address_2'),
		'city'=>$this->input->post('city'),
		'state'=>$this->input->post('state'),
		'zip'=>$this->input->post('zip'),
		'country'=>$this->input->post('country'),
		'comments'=>$this->input->post('comments')
		);
		//Password has been changed OR first time password set
		if($this->input->post('password')!='')
		{
			$employee_data=array(
			'username'=>$this->input->post('username'),
			'password'=>md5($this->input->post('password'))
			);
		}
		else //Password not changed
		{
			$employee_data=array('username'=>$this->input->post('username'));
		}
		
		
		$this->load->helper('directory');
		
		$valid_languages = str_replace(DIRECTORY_SEPARATOR,'',directory_map(APPPATH.'language/', 1));
		$employee_data=array_merge($employee_data,array('language'=>in_array($this->input->post('language'), $valid_languages) ? $this->input->post('language') : 'english'));
		$this->load->helper('demo');
		if ( (is_on_demo_host()) && $employee_id == 1)
		{
			//failure
			echo json_encode(array('success'=>false,'message'=>lang('common_employees_error_updating_demo_admin'),'person_id'=>-1));
		}
		elseif($this->Employee->save_profile($person_data,$employee_data, $employee_id))
		{
			$success_message = '';
			
			//New employee
			if($employee_id==-1)
			{
				$success_message = lang('common_employees_successful_adding').' '.$person_data['first_name'].' '.$person_data['last_name'];
				echo json_encode(array('success'=>true,'message'=>$success_message,'person_id'=>$employee_data['person_id']));
			}
			else //previous employee
			{
				$success_message = lang('common_employees_successful_updating').' '.$person_data['first_name'].' '.$person_data['last_name'];
				$this->session->set_flashdata('manage_success_message', $success_message);
				echo json_encode(array('success'=>true,'message'=>$success_message,'person_id'=>$employee_id));
			}
			
			
			//Delete Image
			if($this->input->post('del_image') && $employee_id != -1)
			{
				$employee_info = $this->Employee->get_info($employee_id);
			    if($employee_info->image_id != null)
			    {
			 		$this->load->model('Appfile');
					$this->Person->update_image(NULL,$employee_id);
					$this->Appfile->delete($employee_info->image_id);
			    }
			}

			//Save Image File
			if(!empty($_FILES["image_id"]) && $_FILES["image_id"]["error"] == UPLOAD_ERR_OK)
			{
			    $allowed_extensions = array('png', 'jpg', 'jpeg', 'gif');
				$extension = strtolower(pathinfo($_FILES["image_id"]["name"], PATHINFO_EXTENSION));
			    if (in_array($extension, $allowed_extensions))
			    {
				    $config['image_library'] = 'gd2';
				    $config['source_image']	= $_FILES["image_id"]["tmp_name"];
				    $config['create_thumb'] = FALSE;
				    $config['maintain_ratio'] = TRUE;
				    $config['width']	 = 400;
				    $config['height']	= 300;
				    $this->load->library('image_lib', $config); 
				    $this->image_lib->resize();
					 $this->load->model('Appfile');
				    $image_file_id = $this->Appfile->save($_FILES["image_id"]["name"], file_get_contents($_FILES["image_id"]["tmp_name"]));
			    }
						if($employee_id==-1)
						{
			    			$this->Person->update_image($image_file_id,$employee_data['person_id']);
						}
						else
						{
							$this->Person->update_image($image_file_id,$employee_id);
		    			
						}
			}
		}
		else//failure
		{	
			echo json_encode(array('success'=>false,'message'=>lang('common_employees_error_adding_updating').' '.
			$person_data['first_name'].' '.$person_data['last_name'],'person_id'=>-1));
		}
	}
	
	function reset_password()
	{
		$this->load->view('login/reset_password');
	}
	
	function do_reset_password_notify()
	{	
		if($this->input->post('username_or_email'))
		{
			$employee = $this->Employee->get_employee_by_username_or_email($this->input->post('username_or_email'));
			if ($employee)
			{
				$data = array();
				$data['employee'] = $employee;
			   $data['reset_key'] = $this->generate_reset_key($employee->person_id);
			
				$this->load->library('email');
				$config['mailtype'] = 'html';
				$this->email->initialize($config);
				$this->email->from('no-reply@mg.4biz.vn', $this->config->item('company'));
				$this->email->to($employee->email); 

				$this->email->subject(lang('login_reset_password'));
				$this->email->message($this->load->view("login/reset_password_email",$data, true));	
				$this->email->send();
			
				$data['success']=lang('login_password_reset_has_been_sent');
				$this->load->view('login/reset_password',$data);
			}
			else 
			{
				$data['error']=lang('login_username_or_email_does_not_exist');
				$this->load->view('login/reset_password',$data);
			}
		}
		else
		{
			$data['error']= lang('common_field_cannot_be_empty');
			$this->load->view('login/reset_password',$data);
		}
	}
	
	function reset_password_enter_password($key=false)
	{
		if ($key)
		{
			$data = array();
			$reset_info = $this->get_reset_info($key);
			
			if ($reset_info)
			{
				$employee_id = $reset_info->employee_id;
				$expire = strtotime($reset_info->expire);
						 
				if ($employee_id && $expire && $expire > time())
				{
					$employee = $this->Employee->get_info($employee_id);
					$data['username'] = $employee->username;
					$data['key'] = $key;
					$this->load->view('login/reset_password_enter_password', $data);			
				}
			}
		}
	}
	
	function get_reset_info($key)
	{
		$this->db->from('employees_reset_password');
		$this->db->where('key',$key);
		$query = $this->db->get();

		if($query->num_rows()==1)
		{
			return $query->row();
		}
		
		return FALSE;
	}
	
	function generate_reset_key($employee_id)
	{
		if (function_exists('openssl_random_pseudo_bytes'))
		{
			$key = bin2hex(openssl_random_pseudo_bytes(16));
		}
		else
		{
			$key = md5(rand());
		}
		if($this->db->insert('employees_reset_password',
		array(
			'employee_id' => $employee_id, 
			'key' => $key, 
			'expire' => date('Y-m-d H:i:s', strtotime("+3 day")))))
		{
			return $key;
		}
		
		return FALSE;
	}
	
	function delete_reset_key($key)
	{
		return $this->db->delete('employees_reset_password', array('key' => $key)); 
	}
	
	function do_reset_password($key=false)
	{
		if ($key)
		{
			$reset_info = $this->get_reset_info($key);
			
			if ($reset_info)
			{
				$employee_id = $reset_info->employee_id;
				$expire = strtotime($reset_info->expire);
				
				if ($employee_id && $expire && $expire > time())
				{
					$password = $this->input->post('password');
					$confirm_password = $this->input->post('confirm_password');
			
					if (($password == $confirm_password) && strlen($password) >=8)
					{
						if ($this->Employee->update_employee_password($employee_id, md5($password)))
						{
							$this->delete_reset_key($key);
							$this->load->view('login/do_reset_password');	
						}
					}
					else
					{
						$data = array();
						$employee = $this->Employee->get_info($employee_id);
						$data['username'] = $employee->username;
						$data['key'] = $key;
						$data['force_password_change'] = $this->input->post('force_password_change') ? TRUE : FALSE;
						$data['error_message'] = lang('login_passwords_must_match_and_be_at_least_8_characters');
						$this->load->view('login/reset_password_enter_password', $data);
					}
				}
			}
		}
	}
	
	function is_update_available()
	{
		session_write_close();
		$this->load->helper('update');
		echo json_encode(is_phppos_update_available());
	}
}
?>