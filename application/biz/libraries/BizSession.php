<?php

class BizSession {
	var $CI;
	
	function __construct()
	{
		$this->CI =& get_instance();
	}
	
	function setValue($key, $value)
	{
		$this->CI->session->set_userdata($key, $value);
	}
	
	function getValue($key)
	{
		if($this->CI->session->userdata($key) === NULL)
		{
			$this->setValue($key, null);
			return null;
		}
		return $this->CI->session->userdata($key);
	}
}
