<?php

function setup_mysql()
{
	$CI =& get_instance();
	
	//Makes sure we have a simple mode that doesn't have strict restrictions
	$CI->db->query('SET SESSION sql_mode="NO_AUTO_CREATE_USER"');
	
	//Needed to prevent deadlock http://stackoverflow.com/questions/23768456/mysql-create-temporary-table-transaction-causes-deadlock
	$CI->db->query('SET SESSION TRANSACTION ISOLATION LEVEL READ COMMITTED');
}

//Loads configuration from database into global CI config
function load_config()
{	
	$CI =& get_instance();
	
	foreach($CI->Appconfig->get_all()->result() as $app_config)
	{
		$CI->config->set_item($app_config->key,$app_config->value);
		
		if ($app_config->key == 'number_of_items_per_page' && $CI->agent->is_mobile())
		{
			$CI->config->set_item($app_config->key,20);			
		}
	}
	
	if($CI->Employee->is_logged_in() and $CI->Employee->get_logged_in_employee_info()->language)
	{
		$CI->lang->switch_to($CI->Employee->get_logged_in_employee_info()->language);
	}
	else if ($CI->config->item('language'))
	{
		$CI->lang->switch_to($CI->config->item('language'));
	}
	
	if ($CI->Location->get_info_for_key('timezone'))
	{
		date_default_timezone_set($CI->Location->get_info_for_key('timezone'));
	}
	else
	{
		$timezone = $CI->Location->get_info_for_key('timezone',1);
		if (!$timezone)
		{
			$timezone = 'America/New_York';
		}
		
		date_default_timezone_set($timezone);
	}
}
?>