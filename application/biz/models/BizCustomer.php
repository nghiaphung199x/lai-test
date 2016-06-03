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
	
	function delete_sms_list($sms_ids) {
		$this->db->where_in('id', $sms_ids);
		return $this->db->update('sms', array('deleted' => 1));
	}
	
	function get_search_suggestions_sms($search,$limit = 25){
		$this->db->from('sms');
		$this->db->where('deleted', 0);
		$this->db->like('title', $search);
		$this->db->order_by("id", "asc");
		$sms = $this->db->get();
	
		foreach ($sms->result() as $row) {
			$suggestions[] = array('label' => $row->title);
		}
		//only return $limit suggestions
		if (count($suggestions > $limit)) {
			$suggestions = array_slice($suggestions, 0, $limit);
		}
		return $suggestions;
	}
	
	function search_sms($search, $limit = 20, $offset = 0, $column = 'id', $orderby = 'desc') {
		$this->db->from('sms');
		$this->db->like('title', $search);
		$this->db->where('deleted',0);
		$this->db->order_by($column, $orderby);
		$this->db->limit($limit);
		$this->db->offset($offset);
		return $this->db->get();
	}
	
	function search_count_sms($search, $limit = 10000) {
		$this->db->from('sms');
		$this->db->like('title', $search);
		$this->db->where('deleted',0);
		$result = $this->db->get();
		return $result->num_rows();
	}
	
	function get_table_number_sms(){
		$this->db->select_max("id");
		$query = $this->db->get("number_sms");
		return $query->row_array();
	}
	
	function get_info_id_max_of_table_number_sms($id_max){
		$this->db->where("id",$id_max);
		$query = $this->db->get("number_sms");
		return $query->row_array();
	}
	
	function save_message($data){
		$this->db->insert("message",$data);
	}
	
	function update_number_sms($id,$data){
		$this->db->where('id',$id);
		$this->db->update("number_sms",$data);
	}
	
	function get_all_quotes_contract($limit = 100, $offset = 0, $col = 'id_quotes_contract', $order = 'desc') {
		$this->db->from('quotes_contract');
		$this->db->order_by($col, $order);
		$this->db->limit($limit);
		$this->db->offset($offset);
		return $this->db->get();
	}
	
	function count_all_quotes_contract() {
		$this->db->from('quotes_contract');
		return $this->db->count_all_results();
	}
	
	function get_info_quotes_contract($id) {
		$this->db->where('id_quotes_contract', $id);
		$query = $this->db->get('quotes_contract');
	
		if ($query->num_rows() == 1) {
			return $query->row();
		} else {
			//Get empty base parent object, as $item_id is NOT an item
			$item_obj = new stdClass();
	
			//Get all the fields from items table
			$fields = $this->db->list_fields('quotes_contract');
	
			foreach ($fields as $field) {
				$item_obj->$field = '';
			}
			return $item_obj;
		}
	}
	
	function exists_quotes_contract($id) {
		$this->db->from('quotes_contract');
		$this->db->where('id_quotes_contract', $id);
		$query = $this->db->get();
		return ($query->num_rows() == 1);
	}
	
	function save_quotes_contract(&$data, $id = false) {
		if (!$id or ! $this->exists($id)) {
			if ($this->db->insert('quotes_contract', $data)) {
				$data['id_quotes_contract'] = $this->db->insert_id();
				return true;
			}
			return false;
		} else {
			$this->db->where('id_quotes_contract', $id);
			return $this->db->update('quotes_contract', $data);
		}
	}
	
	function delete_list_quotes_contract($id) {
		$this->db->where_in('id_quotes_contract', $id);
		return $this->db->delete('quotes_contract');
	}
	
	function get_search_suggestions_quotes_contract($search, $limit = 25) {
		$suggestions = array();
	
		$this->db->from('quotes_contract');
		$this->db->like('title_quotes_contract', $search);
		$this->db->order_by("title_quotes_contract", "asc");
		$by_name = $this->db->get();
		foreach ($by_name->result() as $row) {
			$suggestions[] = array('label' => $row->title_quotes_contract);
		}
	
		$this->db->from('quotes_contract');
		$this->db->like('id_quotes_contract', $search);
		$this->db->order_by("id_quotes_contract", "asc");
		$by_id = $this->db->get();
		foreach ($by_id->result() as $row) {
			$suggestions[] = array('label' => $row->id_quotes_contract);
		}
	
		//only return $limit suggestions
		if (count($suggestions > $limit)) {
			$suggestions = array_slice($suggestions, 0, $limit);
		}
		return $suggestions;
	}
	
	function search_quotes_contract($search, $cat = '', $limit = 20, $offset = 0, $column = 'id_quotes_contract', $orderby = 'desc') {
		$this->db->from('quotes_contract');
		if ($cat) {
			$this->db->where("cat_quotes_contract", $cat);
		}
		$this->db->where("(title_quotes_contract LIKE '%" . $search . "%' OR id_quotes_contract LIKE '%" . $search . "%')");
		$this->db->order_by($column, $orderby);
		$this->db->limit($limit);
		$this->db->offset($offset);
		return $this->db->get();
	}
	
	function search_count_all_quotes_contract($search, $cat = '') {
		$this->db->from('quotes_contract');
		if ($cat) {
			$this->db->where("cat_quotes_contract", $cat);
		}
		$this->db->where("(title_quotes_contract LIKE '%" . $search . "%' OR id_quotes_contract LIKE '%" . $search . "%')");
		$result = $this->db->get();
		return $result->num_rows();
	}
	
	function get_list_template_quotes_contract($cat = '') {
		if ($cat) {
			$this->db->where("cat_quotes_contract", $cat);
		}
		$query = $this->db->get("quotes_contract");
		return $query->result();
	}
	function get_info_person_by_id($id) {
            $this->db->where('person_id', $id);
            $query = $this->db->get('people');
            return $query->row_array();
        }

}
?>
