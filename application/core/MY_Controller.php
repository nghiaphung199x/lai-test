<?php
function force_http_if_needed()
{
	if (is_https())
	{
	    $CI =& get_instance();	
		//If we have setup Mercury....or if it is not set then default to Mercury
		if ($CI->Location->get_info_for_key('enable_credit_card_processing') && ($CI->Location->get_info_for_key('credit_card_processor') == 'mercury' || !$CI->Location->get_info_for_key('credit_card_processor')))
		{	
			//EMV
			if ($CI->Location->get_info_for_key('emv_merchant_id') && $CI->Location->get_info_for_key('com_port') && $CI->Location->get_info_for_key('listener_port'))
			{
				$full_url = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
				header('HTTP/1.1 307 Temporary Redirect');
				header('Cache-Control: no-cache, no-store, must-revalidate'); // HTTP 1.1.
				header('Pragma: no-cache'); // HTTP 1.0.
				header('Expires: 0'); // Proxies.
				//Redirect to new codebase (temporary)
				header("Location: $full_url",TRUE,307);
				exit();
			}
		}	
	}
}

function checkServiceDisabled()
{
	if (SERVICE_DISABLED) {
		$htmlContent = <<<EOD
<html>
	<body>
			<div style="text-align: center; padding-top: 50px;">
				<h2>DỊCH VỤ ĐÃ BỊ TẠM DỪNG</h2>
				<p>Hãy liên hệ với chúng tôi để biết thêm chi tiết.</p>
			</div>
	</body>
</html
EOD;
		echo $htmlContent;
		exit;
	}
}

$lazy_load = (!defined("LAZY_LOAD") or LAZY_LOAD == TRUE);

if (!$lazy_load)
{
	class MY_Controller extends CI_Controller 
	{
		public function __construct()
		{
			parent::__construct();
			force_http_if_needed();
			checkServiceDisabled();
		}
	}
}
else
{
	class MY_Controller extends CI_Controller 
	{	
		//Lazy loading based on http://stackoverflow.com/questions/17579449/model-library-lazy-load-in-codeigniter
		public function __construct()
		{
			foreach (is_loaded() as $var => $class)
			{
			     $this->$var = '';
			}

			$this->load = '';
			parent::__construct();
			force_http_if_needed();
			checkServiceDisabled();
		}
	
	
		// Lazy load models + libraries....If we can't load a model that we have; then we will try to load library $name
		public function __get($name)
		{
			//Cache models so we only scan model dir once

			static $models = FALSE;
			$this->load->helper('file');

			if (!$models)
			{
				$bizModelFiles = get_filenames(BIZ_MODEL_PATH, TRUE);
				foreach($bizModelFiles as $bizModelFile) {
					$model_relative_name = str_replace('.php','',substr($bizModelFile,strlen(BIZ_MODEL_PATH)));
					$model_folder = strpos($model_relative_name, DIRECTORY_SEPARATOR) !== FALSE ? substr($model_relative_name,0,strrpos($model_relative_name,DIRECTORY_SEPARATOR)) : '';
					$model_name = str_replace($model_folder.DIRECTORY_SEPARATOR, '',$model_relative_name);
					$model_name = str_replace(ucfirst(BIZ_PREFIX), '',$model_name);
					$models[$model_name] = $model_folder.'/'.$model_name;
				}
				
				$model_files = get_filenames(APPPATH.'models', TRUE);
				foreach($model_files as $model_file)
				{
					$model_relative_name = str_replace('.php','',substr($model_file,strlen(APPPATH.'models'.DIRECTORY_SEPARATOR)));
					$model_folder = strpos($model_relative_name, DIRECTORY_SEPARATOR) !== FALSE ? substr($model_relative_name,0,strrpos($model_relative_name,DIRECTORY_SEPARATOR)) : '';
					$model_name = str_replace($model_folder.DIRECTORY_SEPARATOR, '',$model_relative_name);
					if (!isset($models[$model_name])) {
						$models[$model_name] = $model_folder.'/'.$model_name;
					}
				}
			}

			if (isset($models[$name]))
			{
				$this->load->model($models[$name]);
				$log_message = "Lazy Loaded model $name CURRENT_URL: ".current_url().' REQUEST '.var_export($_REQUEST, TRUE);
				log_message('error', $log_message);
				return $this->$name;
			}
			else //Try a library if we cannot load a model
			{
				$this->load->library($name);
				$log_message = "Lazy Loaded library $name CURRENT_URL: ".current_url().' REQUEST '.var_export($_REQUEST, TRUE);
				log_message('error', $log_message);
				return $this->$name;
			}
		
			return NULL;
		}
	}
}