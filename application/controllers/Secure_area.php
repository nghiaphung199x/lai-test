<?php
class Secure_area extends MY_Controller 
{
	var $module_id;

	protected $_controller_name = '';
	
	/*
	Controllers that are considered secure extend Secure_area, optionally a $module_id can
	be set to also check if a user can access a particular module in the system.
	*/
	function __construct($module_id=null)
	{
		$this->_controller_name = str_replace(BIZ_PREFIX, '', strtolower(get_class($this)));

		parent::__construct();
		$this->module_id = $module_id;	
		$this->load->model('Employee');
		$this->load->model('Location');
		if(!$this->Employee->is_logged_in())
		{
			redirect('login');
		}
		
		if(!$this->Employee->has_module_permission($this->module_id,$this->Employee->get_logged_in_employee_info()->person_id))
		{
			redirect('no_access/'.$this->module_id);
		}
		
		//load up global data
		$logged_in_employee_info=$this->Employee->get_logged_in_employee_info();
		$data['allowed_modules']=$this->Module->get_allowed_modules($logged_in_employee_info->person_id);
		$data['user_info']=$logged_in_employee_info;
		$data['new_message_count']=$this->Employee->get_unread_messages_count();;
		
		$locations_list=$this->Location->get_all();
		
		$authenticated_locations = $this->Employee->get_authenticated_location_ids($logged_in_employee_info->person_id);
		$locations = array();
		$total_locations_in_system = 0;
		foreach($locations_list->result() as $row)
		{
			if(in_array($row->location_id, $authenticated_locations))
			{
				$locations[$row->location_id] =$row->name;
			}
			
			$total_locations_in_system++;
		}
		
		$data['total_locations_in_system'] = $total_locations_in_system;
		$data['authenticated_locations'] = $locations;
		
		$location_id = $this->Employee->get_logged_in_employee_current_location_id();
		$loc_info = $this->Location->get_info($location_id);
		
		$data['current_logged_in_location_id'] = $location_id;
		$data['current_employee_location_info'] = $loc_info;
		$data['location_color'] = $loc_info->color;
		$this->load->vars($data);
	}
	
	function check_action_permission($action_id)
	{
		if (!$this->Employee->has_module_action_permission($this->module_id, $action_id, $this->Employee->get_logged_in_employee_info()->person_id))
		{
			redirect('no_access/'.$this->module_id);
		}
	}	
}
?>