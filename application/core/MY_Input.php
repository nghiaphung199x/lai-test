<?php
class MY_Input extends CI_Input 
{
   function __construct()
	{
		parent::__construct();
   }
	
	function request($index)
	{
		if (isset($_REQUEST[$index]))
		{
			return $_REQUEST[$index];
		}
		
		return NULL;
	}
}