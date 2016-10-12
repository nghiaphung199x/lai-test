<?php
require_once ("Secure_area.php");
class Timeclocks extends Secure_area 
{
	function __construct()
	{
		parent::__construct();	
		$this->lang->load('timeclocks');
		$this->lang->load('module');		
		
	}
	
	function index()
	{
		$data = array();
		$data['is_clocked_in'] = $this->Employee->is_clocked_in();
		$this->load->view("timeclocks/manage",$data);
	}
	
	function in()
	{
		if (!$this->Employee->is_clocked_in() && $this->Employee->clock_in($this->input->post('comment')))
		{
			echo json_encode(array('success'=>true,'message'=>lang('timeclocks_clock_in_success')));
		}
		else
		{
			echo json_encode(array('success'=>false,'message'=>lang('timeclocks_clock_in_failure')));			
		}
	}
	
	function out()
	{
		if ($this->Employee->clock_out($this->input->post('comment')))
		{
			echo json_encode(array('success'=>true,'message'=>lang('timeclocks_clock_out_success')));
		}
		else
		{
			echo json_encode(array('success'=>false,'message'=>lang('timeclocks_clock_out_failure')));			
		}
	}
	
	function view($id, $start_date, $end_date, $employee_id_report)
	{
		if ($this->Employee->has_module_action_permission('reports', 'view_timeclock', $this->Employee->get_logged_in_employee_info()->person_id))
		{
			$data = array();
			
			$timeclock_entry = $this->Employee->get_timeclock($id);
			
			$data['id'] = $timeclock_entry->id ? $timeclock_entry->id : -1;
			$data['start_date'] = $start_date;
			$data['end_date'] = $end_date;
			$data['employee_id'] = $timeclock_entry->employee_id;
			$data['location_id'] = $timeclock_entry->location_id ? $timeclock_entry->location_id : $this->Employee->get_logged_in_employee_current_location_id();
			$data['employee_id_report'] = $employee_id_report;
			$data['hourly_pay_rate'] = $timeclock_entry->hourly_pay_rate;
			
			$data['employees'] = array();
			foreach ($this->Employee->get_all()->result() as $employee)
			{
				$data['employees'][$employee->person_id] = $employee->first_name . ' '. $employee->last_name;
			}
			
			$data['in'] = $timeclock_entry->clock_in ? $timeclock_entry->clock_in :  date('Y-m-d H:i:s');
			$data['out'] = $timeclock_entry->clock_out ? $timeclock_entry->clock_out :  date('Y-m-d H:i:s');
			$data['in_comment'] = $timeclock_entry->clock_in_comment;
			$data['out_comment'] = $timeclock_entry->clock_out_comment;
			$this->load->view('timeclocks/form',$data);
		}
	}
	
	function save($id, $start_date, $end_date, $employee_id_report)
	{
		$employee_id = $this->input->post('employee_id');
		$location_id = $this->input->post('location_id');
		$clock_in = $this->input->post('clock_in');
		$clock_out = $this->input->post('clock_out');
		$clock_in_comment = $this->input->post('clock_in_comment');
		$clock_out_comment = $this->input->post('clock_out_comment');
		$hourly_pay_rate = (float)$this->input->post('hourly_pay_rate');
		
		$this->Employee->save_timeclock(array(
			'id'=> $id,
			'employee_id' => $employee_id,
			'location_id' => $location_id,
			'clock_in'=> $clock_in,
			'clock_out'=> $clock_out,
			'clock_in_comment'=> $clock_in_comment,
			'clock_out_comment'=> $clock_out_comment,	
			'hourly_pay_rate' => $hourly_pay_rate,		
		));
		redirect('reports/detailed_timeclock/'.$start_date.'/'.$end_date.'/'.$employee_id_report);

	}
	
	function delete($id, $start_date, $end_date, $employee_id_report)
	{
		if ($this->Employee->has_module_action_permission('reports', 'view_timeclock', $this->Employee->get_logged_in_employee_info()->person_id))
		{
			$this->Employee->delete_timeclock($id);
		}
		
		redirect('reports/detailed_timeclock/'.$start_date.'/'.$end_date.'/'.$employee_id_report);
	}
}
?>