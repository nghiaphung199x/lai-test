<?php
class BizMeasure extends CI_Model
{
	function count_all()
	{
		$this->db->from('measures');
		return $this->db->count_all_results();
	}
	
	
	function get_all($limit=10000, $offset=0,$col='name',$order='asc')
	{
		$this->db->from('measures');
		$this->db->where('deleted',0);
		if (!$this->config->item('speed_up_search_queries'))
		{
			$this->db->order_by($col, $order);
		}
		
		$this->db->limit($limit);
		$this->db->offset($offset);
		
		$return = array();
		
		foreach($this->db->get()->result_array() as $result)
		{
			$return[$result['id']] = array('name' => $result['name']);
		}
		
		return $return;
	}
	
	function save($measureName, $measureId = FALSE)
	{
		if ($measureId == FALSE)
		{
			if ($measureName)
			{
				if($this->db->insert('measures',array('name' => $measureName)))
				{
					return $this->db->insert_id();
				}
			}
			
			return FALSE;
		}
		else
		{
			$this->db->where('id', $measureId);
			if ($this->db->update('measures',array('name' => $measureName)))
			{
				return $measureId;
			}
		}
		return FALSE;
	}
	
	/*
	Deletes one tag
	*/
	function delete($measureId)
	{		
		$this->db->where('id', $measureId);
		return $this->db->update('measures', array('deleted' => 1, 'name' => NULL));
	}
}