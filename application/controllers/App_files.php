<?php
class App_files extends MY_Controller 
{
	function __construct()
	{
		parent::__construct();	
	}
	
	function view($file_id)
	{ 
		//Don't allow images to cause hangups with session
		session_write_close();
		$this->load->model('Appfile');
		$file = $this->Appfile->get($file_id);
		$this->load->helper('file');
		header("Content-type: ".get_mime_by_extension($file->file_name));
		echo $file->file_data;
	}
}
?>