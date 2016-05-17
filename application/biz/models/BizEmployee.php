<?php
require_once (APPPATH . "models/Employee.php");
class BizEmployee extends Employee
{
	function getEmployeesByCurrentLocation()
	{
		$this->db->select('employees.*');
		$this->db->from('employees');
		$this->db->join('employees_locations', 'employees_locations.employee_id = employees.person_id');
		$this->db->where('location_id', $this->get_logged_in_employee_current_location_id());
		return $this->db->get()->result_array();
	}
}
?>
