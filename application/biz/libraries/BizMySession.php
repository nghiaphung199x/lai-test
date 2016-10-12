<?php

class BizMySession {
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
		return $this->CI->session->userdata($key);
	}
}
