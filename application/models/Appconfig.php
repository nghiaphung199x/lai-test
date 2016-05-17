<?php
class Appconfig extends CI_Model 
{
	
	function exists($key)
	{
		$this->db->from('app_config');	
		$this->db->where('app_config.key',$key);
		$query = $this->db->get();
		
		return ($query->num_rows()==1);
	}
	
	function get_all()
	{
		$this->db->from('app_config');
		$this->db->order_by("key", "asc");
		return $this->db->get();		
	}
	
	function get($key)
	{
		return $this->config->item($key);
	}
	
	function save($key,$value)
	{
		$config_data=array(
		'key'=>$key,
		'value'=>$value
		);
		return $this->db->replace('app_config', $config_data);
	}
	
	function get_raw_number_of_decimals()
	{
		$this->db->from('app_config');
		$this->db->where("key", "number_of_decimals");
		$row = $this->db->get()->row_array();
		if (!empty($row))
		{
			return $row['value'];
		}
		return 2;	
	}
	
	function get_raw_language_value()
	{
		$this->db->from('app_config');
		$this->db->where("key", "language");
		$row = $this->db->get()->row_array();
		if (!empty($row))
		{
			return $row['value'];
		}
		return '';	
	}
	
	function get_raw_phppos_session_expiration()
	{
		$this->db->from('app_config');
		$this->db->where("key", "phppos_session_expiration");
		$row = $this->db->get()->row_array();
		if (!empty($row))
		{
			if (is_numeric($row['value']))
			{
				return (int)$row['value'];
			}
			
		}
		return NULL;	
	}
	
	function batch_save($data)
	{
		//Check for duplicate taxes
		for($k = 1;$k<=5;$k++)
		{
			$current_tax = $data["default_tax_${k}_name"].$data["default_tax_${k}_rate"];
			
			for ($j = 1;$j<=5;$j++)
			{
				$check_tax = $data["default_tax_${j}_name"].$data["default_tax_${j}_rate"];
				if ($j!=$k && $current_tax != '' && $check_tax != '')
				{
					if ($current_tax == $check_tax)
					{
						return FALSE;
					}
				}
			}
		}
		
		$success = true;
		
		//Run these queries as a transaction, we want to make sure we do all or nothing
		$this->db->trans_start();
		foreach($data as $key=>$value)
		{
			if(!$this->save($key, $value))
			{
				$success=false;
				break;
			}
		}
		
		$this->db->trans_complete();		
		return $success;
		
	}
		
	function get_logo_image()
	{
		if ($this->config->item('company_logo'))
		{
			return site_url('app_files/view/'.$this->get('company_logo'));
		}
		return  base_url().'assets/img/header_logo.png';
	}
		
	function get_additional_payment_types()
	{
		$return = array();
		$payment_types = $this->get('additional_payment_types');
		
		if ($payment_types)
		{
			$return = array_map('trim', explode(',',$payment_types));
		}
		
		return $return;
	}
	
	function mark_mercury_activate($mercury_activate_seen = true)
	{
		$this->db->query('REPLACE INTO '.$this->db->dbprefix('app_config').' (`key`, `value`) VALUES ("mercury_activate_seen", "'.($mercury_activate_seen ? 1 : 0).'")');
	}
}

?>