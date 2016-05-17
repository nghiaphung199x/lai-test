<?php
require_once (APPPATH . "models/Item.php");
class BizItem extends Item
{
	function getNotAuditedInLocation($auditedIds = array(), $extra = array())
	{
		$this->db->select('items.*, categories.name as category, location_items.quantity as location_quantity');
		$this->db->from('items');
		$this->db->join('categories', 'categories.id = items.category_id');
		$this->db->join('location_items', 'location_items.item_id = items.item_id');
		$this->db->where('location_items.location_id', $this->Employee->get_logged_in_employee_current_location_id());
		if(!empty($auditedIds))
		{
			$this->db->where_not_in('items.item_id', $auditedIds);
		}
		
		if(isset($extra['category_id']) && $extra['category_id']) {
			$this->db->where('categories.id', $extra['category_id']);
		}
		return $this->db->get()->result_array();
	}
	
	function getTotalInAllLocation($item_id)
	{
		$location_items=$this->db->dbprefix('location_items');
		$items=$this->db->dbprefix('items');
		$locations=$this->db->dbprefix('locations');	
		$query = "select SUM(". $location_items .".quantity) as total_quantity from ". $location_items ." JOIN ". $locations ." ON ". $locations .".location_id = ". $location_items .".location_id where ". $location_items .".item_id = " . $item_id . " AND " . $locations . ".deleted =0";
		$result=$this->db->query($query);
		if($result->num_rows() > 0)
		{
			$row = $result->result();
			return $row[0]->total_quantity;
		}
		return null;
	}
	
	function get_item_search_suggestions($search,$limit=25, $extra=array())
	{
		if (!trim($search))
		{
			return array();
		}
	
		$suggestions = array();
	
		if($this->config->item('supports_full_text') && !$this->config->item('legacy_search_method'))
		{
			$this->db->select("items.*,categories.name as category, MATCH (".$this->db->dbprefix('items').".name) AGAINST (".$this->db->escape(escape_full_text_boolean_search($search).'*')." IN BOOLEAN MODE) as rel", false);
			$this->db->from('items');
			$this->db->join('categories', 'categories.id = items.category_id','left');
			$this->db->where('items.deleted',0);
			$this->db->where("MATCH (".$this->db->dbprefix('items').".name) AGAINST (".$this->db->escape(escape_full_text_boolean_search($search).'*')." IN BOOLEAN MODE)", NULL, FALSE);
			$this->db->limit($limit);
			
			if(isset($extra['category_id']) && $extra['category_id']) {
				$this->db->where('categories.id', $extra['category_id']);
			}
			if(isset($extra['by_current_location']) && $extra['by_current_location']) {
				$this->db->join('location_items', 'location_items.item_id = items.item_id');
				$this->db->where('location_items.location_id', $this->Employee->get_logged_in_employee_current_location_id());
			}
			
			$this->db->order_by('rel DESC');
			$by_name = $this->db->get();
	
			$temp_suggestions = array();
	
			foreach($by_name->result() as $row)
			{
				$data = array(
						'image' => $row->image_id ?  site_url('app_files/view/'.$row->image_id) : base_url()."assets/img/item.png" ,
						'category' => $row->category,
						'item_number' => $row->item_number,
				);
	
				if ($row->category && $row->size)
				{
					$data['label'] = $row->name . ' ('.$row->category.', '.$row->size.')';
	
					$temp_suggestions[$row->item_id] = $data;
				}
				elseif ($row->category)
				{
					$data['label'] = $row->name . ' ('.$row->category.')';
	
					$temp_suggestions[$row->item_id] =  $data;
				}
				elseif ($row->size)
				{
					$data['label'] = $row->name . ' ('.$row->size.')';
	
					$temp_suggestions[$row->item_id] =  $data;
				}
				else
				{
					$data['label'] = $row->name;
	
					$temp_suggestions[$row->item_id] = $data;
				}
					
			}
	
			foreach($temp_suggestions as $key => $value)
			{
				$suggestions[]=array('value'=> $key, 'label' => $value['label'], 'image' => $value['image'], 'category' => $value['category'], 'item_number' => $value['item_number']);
			}
	
			$this->db->select("items.*,categories.name as category, MATCH (item_number) AGAINST (".$this->db->escape(escape_full_text_boolean_search($search).'*')." IN BOOLEAN MODE) as rel", false);
			$this->db->from('items');
			$this->db->join('categories', 'categories.id = items.category_id','left');
			$this->db->where('items.deleted',0);
			$this->db->where("MATCH (item_number) AGAINST (".$this->db->escape(escape_full_text_boolean_search($search).'*')." IN BOOLEAN MODE)", NULL, FALSE);
			$this->db->limit($limit);
			$this->db->order_by('rel DESC');
			if(isset($extra['category_id']) && $extra['category_id']) {
				$this->db->where('categories.id', $extra['category_id']);
			}
			if(isset($extra['by_current_location']) && $extra['by_current_location']) {
				$this->db->join('location_items', 'location_items.item_id = items.item_id');
				$this->db->where('location_items.location_id', $this->Employee->get_logged_in_employee_current_location_id());
			}
			
			$by_item_number = $this->db->get();
	
			$temp_suggestions = array();
	
			foreach($by_item_number->result() as $row)
			{
				$data = array(
						'label' => $row->item_number.' ('.$row->name.')',
						'image' => $row->image_id ?  site_url('app_files/view/'.$row->image_id) : base_url()."assets/img/item.png" ,
						'category' => $row->category,
						'item_number' => $row->item_number,
				);
	
				$temp_suggestions[$row->item_id] = $data;
			}
	
			foreach($temp_suggestions as $key => $value)
			{
				$suggestions[]=array('value'=> $key, 'label' => $value['label'], 'image' => $value['image'], 'category' => $value['category'], 'item_number' => $value['item_number']);
			}
	
			$this->db->select("items.*,categories.name as category,MATCH (product_id) AGAINST (".$this->db->escape(escape_full_text_boolean_search($search).'*')." IN BOOLEAN MODE) as rel", false);
			$this->db->from('items');
			$this->db->join('categories', 'categories.id = items.category_id','left');
			$this->db->where("MATCH (product_id) AGAINST (".$this->db->escape(escape_full_text_boolean_search($search).'*')." IN BOOLEAN MODE)", NULL, FALSE);
			$this->db->where('items.deleted',0);
			$this->db->limit($limit);
			$this->db->order_by('rel DESC');
			if(isset($extra['category_id']) && $extra['category_id']) {
				$this->db->where('categories.id', $extra['category_id']);
			}
			if(isset($extra['by_current_location']) && $extra['by_current_location']) {
				$this->db->join('location_items', 'location_items.item_id = items.item_id');
				$this->db->where('location_items.location_id', $this->Employee->get_logged_in_employee_current_location_id());
			}
			
			$by_product_id = $this->db->get();
	
			$temp_suggestions = array();
	
			foreach($by_product_id->result() as $row)
			{
				$data = array(
						'label' => $row->product_id.' ('.$row->name.')',
						'image' => $row->image_id ?  site_url('app_files/view/'.$row->image_id) : base_url()."assets/img/item.png" ,
						'category' => $row->category,
						'item_number' => $row->item_number,
				);
	
				$temp_suggestions[$row->item_id] = $data;
			}
	
				
			foreach($temp_suggestions as $key => $value)
			{
				$suggestions[]=array('value'=> $key, 'label' => $value['label'], 'image' => $value['image'], 'category' => $value['category'], 'item_number' => $value['item_number']);
			}
	
			$this->db->select("additional_item_numbers.*, items.image_id, categories.name as category, MATCH (".$this->db->dbprefix('additional_item_numbers').".item_number) AGAINST (".$this->db->escape(escape_full_text_boolean_search($search).'*')." IN BOOLEAN MODE) as rel", false);
			$this->db->from('additional_item_numbers');
			$this->db->join('items', 'additional_item_numbers.item_id = items.item_id');
			$this->db->join('categories', 'categories.id = items.category_id','left');
			$this->db->where("MATCH (".$this->db->dbprefix('additional_item_numbers').".item_number) AGAINST (".$this->db->escape(escape_full_text_boolean_search($search).'*')." IN BOOLEAN MODE)", NULL, FALSE);
			$this->db->limit($limit);
			$this->db->order_by('rel DESC');
			if(isset($extra['category_id']) && $extra['category_id']) {
				$this->db->where('categories.id', $extra['category_id']);
			}
			if(isset($extra['by_current_location']) && $extra['by_current_location']) {
				$this->db->join('location_items', 'location_items.item_id = items.item_id');
				$this->db->where('location_items.location_id', $this->Employee->get_logged_in_employee_current_location_id());
			}
			
			$by_additional_item_numbers = $this->db->get();
			$temp_suggestions = array();
			foreach($by_additional_item_numbers->result() as $row)
			{
				$data = array(
						'label' => $row->item_number,
						'image' => $row->image_id ?  site_url('app_files/view/'.$row->image_id) : base_url()."assets/img/item.png" ,
						'category' => $row->category,
						'item_number' => $row->item_number,
				);
	
				$temp_suggestions[$row->item_id] = $data;
			}
	
			foreach($temp_suggestions as $key => $value)
			{
				$suggestions[]=array('value'=> $key, 'label' => $value['label'], 'image' => $value['image'], 'category' => $value['category'], 'item_number' => $value['item_number']);
	
			}
		}
		else
		{
			$this->db->select("items.*,categories.name as category", false);
			$this->db->from('items');
			$this->db->join('categories', 'categories.id = items.category_id','left');
			$this->db->where('items.deleted',0);
			$this->db->like($this->db->dbprefix('items').'.name', $search);
			$this->db->limit($limit);
			
			if(isset($extra['category_id']) && $extra['category_id']) {
				$this->db->where('categories.id', $extra['category_id']);
			}
			if(isset($extra['by_current_location']) && $extra['by_current_location']) {
				$this->db->join('location_items', 'location_items.item_id = items.item_id');
				$this->db->where('location_items.location_id', $this->Employee->get_logged_in_employee_current_location_id());
			}
			
			$by_name = $this->db->get();
	
			$temp_suggestions = array();
	
			foreach($by_name->result() as $row)
			{
				$data = array(
						'image' => $row->image_id ?  site_url('app_files/view/'.$row->image_id) : base_url()."assets/img/item.png" ,
						'category' => $row->category,
						'item_number' => $row->item_number,
				);
	
				if ($row->category && $row->size)
				{
					$data['label'] = $row->name . ' ('.$row->category.', '.$row->size.')';
	
					$temp_suggestions[$row->item_id] = $data;
				}
				elseif ($row->category)
				{
					$data['label'] = $row->name . ' ('.$row->category.')';
	
					$temp_suggestions[$row->item_id] =  $data;
				}
				elseif ($row->size)
				{
					$data['label'] = $row->name . ' ('.$row->size.')';
	
					$temp_suggestions[$row->item_id] =  $data;
				}
				else
				{
					$data['label'] = $row->name;
	
					$temp_suggestions[$row->item_id] = $data;
				}
					
			}
			
			$this->load->helper('array');
			uasort($temp_suggestions, 'sort_assoc_array_by_label');
	
			foreach($temp_suggestions as $key => $value)
			{
				$suggestions[]=array('value'=> $key, 'label' => $value['label'], 'image' => $value['image'], 'category' => $value['category'], 'item_number' => $value['item_number']);
			}
			$this->db->select("items.*,categories.name as category", false);
			$this->db->from('items');
			$this->db->join('categories', 'categories.id = items.category_id','left');
			$this->db->where('items.deleted',0);
			$this->db->like($this->db->dbprefix('items').'.item_number', $search);
			$this->db->limit($limit);
			if(isset($extra['category_id']) && $extra['category_id']) {
				$this->db->where('categories.id', $extra['category_id']);
			}
			if(isset($extra['by_current_location']) && $extra['by_current_location']) {
				$this->db->join('location_items', 'location_items.item_id = items.item_id');
				$this->db->where('location_items.location_id', $this->Employee->get_logged_in_employee_current_location_id());
			}
			
			$by_item_number = $this->db->get();
	
			$temp_suggestions = array();
	
			foreach($by_item_number->result() as $row)
			{
				$data = array(
						'label' => $row->item_number.' ('.$row->name.')',
						'image' => $row->image_id ?  site_url('app_files/view/'.$row->image_id) : base_url()."assets/img/item.png" ,
						'category' => $row->category,
						'item_number' => $row->item_number,
				);
	
				$temp_suggestions[$row->item_id] = $data;
			}
				
			uasort($temp_suggestions, 'sort_assoc_array_by_label');
	
			foreach($temp_suggestions as $key => $value)
			{
				$suggestions[]=array('value'=> $key, 'label' => $value['label'], 'image' => $value['image'], 'category' => $value['category'], 'item_number' => $value['item_number']);
			}
			
			$this->db->select("items.*,categories.name as category", false);
			$this->db->from('items');
			$this->db->join('categories', 'categories.id = items.category_id','left');
			$this->db->where('items.deleted',0);
			$this->db->like($this->db->dbprefix('items').'.product_id', $search);
			$this->db->limit($limit);
			if(isset($extra['category_id']) && $extra['category_id']) {
				$this->db->where('categories.id', $extra['category_id']);
			}
			if(isset($extra['by_current_location']) && $extra['by_current_location']) {
				$this->db->join('location_items', 'location_items.item_id = items.item_id');
				$this->db->where('location_items.location_id', $this->Employee->get_logged_in_employee_current_location_id());
			}
			
			$by_product_id = $this->db->get();
	
			$temp_suggestions = array();
	
			foreach($by_product_id->result() as $row)
			{
				$data = array(
						'label' => $row->product_id.' ('.$row->name.')',
						'image' => $row->image_id ?  site_url('app_files/view/'.$row->image_id) : base_url()."assets/img/item.png" ,
						'category' => $row->category,
						'item_number' => $row->item_number,
				);
	
				$temp_suggestions[$row->item_id] = $data;
			}
				
			uasort($temp_suggestions, 'sort_assoc_array_by_label');
	
			foreach($temp_suggestions as $key => $value)
			{
				$suggestions[]=array('value'=> $key, 'label' => $value['label'], 'image' => $value['image'], 'category' => $value['category'], 'item_number' => $value['item_number']);
			}
	
			$this->db->select("additional_item_numbers.*, items.image_id, categories.name as category", false);
			$this->db->from('additional_item_numbers');
			$this->db->join('items', 'additional_item_numbers.item_id = items.item_id');
			$this->db->join('categories', 'categories.id = items.category_id','left');
			$this->db->like($this->db->dbprefix('additional_item_numbers').'.item_number', $search);
				
			$this->db->limit($limit);
			if(isset($extra['category_id']) && $extra['category_id']) {
				$this->db->where('categories.id', $extra['category_id']);
			}
			if(isset($extra['by_current_location']) && $extra['by_current_location']) {
				$this->db->join('location_items', 'location_items.item_id = items.item_id');
				$this->db->where('location_items.location_id', $this->Employee->get_logged_in_employee_current_location_id());
			}
			
			$by_additional_item_numbers = $this->db->get();
			$temp_suggestions = array();
			foreach($by_additional_item_numbers->result() as $row)
			{
				$data = array(
						'label' => $row->item_number,
						'image' => $row->image_id ?  site_url('app_files/view/'.$row->image_id) : base_url()."assets/img/item.png" ,
						'category' => $row->category,
						'item_number' => $row->item_number,
				);
	
				$temp_suggestions[$row->item_id] = $data;
			}
				
			uasort($temp_suggestions, 'sort_assoc_array_by_label');
				
			foreach($temp_suggestions as $key => $value)
			{
				$suggestions[]=array('value'=> $key, 'label' => $value['label'], 'image' => $value['image'], 'category' => $value['category'], 'item_number' => $value['item_number']);
			}
		}
	
		for($k=count($suggestions)-1;$k>=0;$k--)
		{
			if (!$suggestions[$k]['label'])
			{
				unset($suggestions[$k]);
			}
		}
	
		$suggestions = array_values($suggestions);
	
		//only return $limit suggestions
		if(count($suggestions > $limit))
		{
			$suggestions = array_slice($suggestions, 0,$limit);
		}
		
		return $suggestions;
	
	}
}
?>
