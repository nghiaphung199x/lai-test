<?php
require_once (APPPATH . "models/Customer.php");
class BizCustomer extends Customer
{
	/*
	Returns all the customers
	*/
	function get_all($limit=10000, $offset=0,$col='last_name',$order='asc', $extra= array())
	{
		$order_by = '';
		
		if (!$this->config->item('speed_up_search_queries'))
		{
			$order_by=" ORDER BY ".$col." ". $order;
		}
		
		$people=$this->db->dbprefix('people');
		$customers=$this->db->dbprefix('customers');
		$employees_locations=$this->db->dbprefix('employees_locations');

		$query = "SELECT * 
						FROM ".$people."
						STRAIGHT_JOIN ".$customers." ON 										                       
						".$people.".person_id = ".$customers.".person_id
						WHERE deleted =0 $order_by 
						LIMIT  ".$offset.",".$limit;

		if($extra['scope_of_view'] == 'view_scope_owner') {
			$query = "SELECT *  FROM ".$people." STRAIGHT_JOIN ".$customers." ON ".$people.".person_id = ".$customers.".person_id
						WHERE deleted = 0 AND ". $customers .".created_by = ". $this->Employee->get_logged_in_employee_info()->person_id ."
						$order_by LIMIT  ".$offset.",".$limit;
		} elseif($extra['scope_of_view'] == 'view_scope_location') {
			$query = "SELECT *  FROM ".$people." STRAIGHT_JOIN ".$customers." ON ".$people.".person_id = ".$customers.".person_id
						WHERE deleted = 0 AND ". $customers .".created_location_id = ". $this->Employee->get_logged_in_employee_current_location_id() ."
						$order_by LIMIT  ".$offset.",".$limit;
		}
		$data=$this->db->query($query);
		return $data;
	}
	
	function count_all($extra=array())
	{
		$this->db->from('customers');
		$this->db->where('deleted',0);
		if(isset($extra['scope_of_view']) && $extra['scope_of_view'] == 'view_scope_owner') {
			$this->db->where('created_by', $this->Employee->get_logged_in_employee_info()->person_id);
		} elseif(isset($extra['scope_of_view']) && $extra['scope_of_view'] == 'view_scope_location') {
			$this->db->where('created_location_id', $this->Employee->get_logged_in_employee_current_location_id());
		}

		return $this->db->count_all_results();
	}

	function search_count_all($searchParams, $limit=10000)
	{
		$searchText = is_array($searchParams) ? $searchParams['search_text'] : $searchParams;

		$this->db->from('customers');
		$this->db->join('people','customers.person_id=people.person_id');		

		if ($searchText)
		{
			if($this->config->item('supports_full_text') && !$this->config->item('legacy_search_method'))
			{
				$this->db->where("(MATCH (first_name, last_name, email, phone_number) AGAINST (".$this->db->escape(escape_full_text_boolean_search($searchText).'*')." IN BOOLEAN MODE".") or MATCH(account_number, company_name, tax_certificate) AGAINST (".$this->db->escape(escape_full_text_boolean_search($searchText).'*')." IN BOOLEAN MODE"."))and ".$this->db->dbprefix('customers'). ".deleted=0", NULL, FALSE);		
			}
			else
			{
				$this->db->where("(first_name LIKE '%".$this->db->escape_like_str($searchText)."%' or 
				last_name LIKE '%".$this->db->escape_like_str($searchText)."%' or 
				email LIKE '%".$this->db->escape_like_str($searchText)."%' or 
				phone_number LIKE '%".$this->db->escape_like_str($searchText)."%' or 
				account_number LIKE '%".$this->db->escape_like_str($searchText)."%' or 
				company_name LIKE '%".$this->db->escape_like_str($searchText)."%' or 
				CONCAT(`first_name`,' ',`last_name`) LIKE '%".$this->db->escape_like_str($searchText)."%') and deleted=0");		
			}
		}
		else
		{
			$this->db->where('deleted',0);
		}

		if(isset($searchParams['tier_id']) && $searchParams['tier_id'] != 'all')
		{
			if($searchParams['tier_id'] == 0)
			{
				$this->db->where('tier_id IS NULL');
			} else {
				$this->db->where('tier_id', $searchParams['tier_id']);
			}
			
		}

		if(isset($searchParams['employee_id']) && $searchParams['employee_id'] != 'all')
		{
			$this->db->where('created_by', $searchParams['employee_id']);
		}

		if(isset($searchParams['scope_of_view']) && $searchParams['scope_of_view'] == 'view_scope_owner') {
			$this->db->where('created_by', $this->Employee->get_logged_in_employee_info()->person_id);
		} elseif(isset($searchParams['scope_of_view']) && $searchParams['scope_of_view'] == 'view_scope_location') {
			$this->db->where('created_location_id', $this->Employee->get_logged_in_employee_current_location_id());
		}

		$this->db->limit($limit);
		$result=$this->db->get();
		return $result->num_rows();
	}

	/*
	Preform a search on customers
	*/
	function search($searchParams, $limit=20,$offset=0,$column='last_name',$orderby='asc')
	{
		$searchText = is_array($searchParams) ? $searchParams['search_text'] : $searchParams;

		$this->db->from('customers');
		$this->db->join('people','customers.person_id=people.person_id');	
		
		if ($searchText)
		{
			if($this->config->item('supports_full_text') && !$this->config->item('legacy_search_method'))
			{
				$this->db->where("(MATCH (first_name, last_name, email, phone_number) AGAINST (".$this->db->escape(escape_full_text_boolean_search($searchText).'*')." IN BOOLEAN MODE".") or MATCH(account_number, company_name, tax_certificate) AGAINST (".$this->db->escape(escape_full_text_boolean_search($searchText).'*')." IN BOOLEAN MODE"."))and ".$this->db->dbprefix('customers'). ".deleted=0", NULL, FALSE);		
			}
			else
			{
				$this->db->where("(first_name LIKE '%".$this->db->escape_like_str($searchText)."%' or 
				last_name LIKE '%".$this->db->escape_like_str($searchText)."%' or 
				email LIKE '%".$this->db->escape_like_str($searchText)."%' or 
				phone_number LIKE '%".$this->db->escape_like_str($searchText)."%' or 
				account_number LIKE '%".$this->db->escape_like_str($searchText)."%' or 
				company_name LIKE '%".$this->db->escape_like_str($searchText)."%' or 
				CONCAT(`first_name`,' ',`last_name`) LIKE '%".$this->db->escape_like_str($searchText)."%' or 
				CONCAT(`last_name`,', ',`first_name`) LIKE '%".$this->db->escape_like_str($searchText)."%' or 
				CONCAT(`last_name`,', ',`first_name`, ' (',".$this->db->dbprefix('customers').".person_id,')') LIKE '%".$this->db->escape_like_str($searchText)."%'
				) and deleted=0");		
			}
		}
		else
		{
			$this->db->where('deleted',0);
		}

		if(isset($searchParams['tier_id']) && $searchParams['tier_id'] != 'all')
		{
			if($searchParams['tier_id'] == 0)
			{
				$this->db->where('tier_id IS NULL');
			} else {
				$this->db->where('tier_id', $searchParams['tier_id']);
			}
			
		}

		if(isset($searchParams['employee_id']) && $searchParams['employee_id'] != 'all')
		{
			$this->db->where('created_by', $searchParams['employee_id']);
		}
		
		if(isset($searchParams['scope_of_view']) && $searchParams['scope_of_view'] == 'view_scope_owner') {
			$this->db->where('created_by', $this->Employee->get_logged_in_employee_info()->person_id);
		} elseif(isset($searchParams['scope_of_view']) && $searchParams['scope_of_view'] == 'view_scope_location') {
			$this->db->where('created_location_id', $this->Employee->get_logged_in_employee_current_location_id());
		}

		if (!$this->config->item('speed_up_search_queries'))
		{
			$this->db->order_by($column,$orderby);
		}
		$this->db->limit($limit);
		$this->db->offset($offset);
		return $this->db->get();
	}
	
	function count_all_sms() {
		$this->db->from('sms');
		$this->db->where('deleted',0);
		return $this->db->count_all_results();
	}
	
	function get_all_sms($limit = 10000, $offset = 0, $col = 'id', $order = 'DESC'){
		$this->db->from('sms');
		$this->db->where('deleted', 0);
		$this->db->order_by($col, $order);
		$this->db->limit($limit);
		$this->db->offset($offset);
		return $this->db->get();
	}
	
	function get_info_sms($id){
		$this->db->from('sms');
		$this->db->where('id',$id);
		$query = $this->db->get();
		if ($query->num_rows() == 1) {
			return $query->row();
		} else {
			//Get empty base parent object, as $customer_id is NOT an customer
			$person_obj = parent::get_info(-1);
			//Get all the fields from customer table
			$fields = $this->db->list_fields('sms');
			//append those fields to base parent object, we we have a complete empty object
			foreach ($fields as $field) {
				$person_obj->$field = '';
			}
			return $person_obj;
		}
	}
	
	function exists_sms($id){
		$this->db->where('id',$id);
		$query = $this->db->get("sms");
		return ($query->num_rows() == 1);
	}
	function save_sms(&$sms_data, $id = false){
		if(!$id or !$this->exists_sms($id)){
			if ($this->db->insert('sms', $sms_data)) {
				$sms_data['id'] = $this->db->insert_id();
				return true;
			}
			return false;
		}
		$this->db->where('id', $id);
		return $this->db->update('sms', $sms_data);
	}
}
?>
