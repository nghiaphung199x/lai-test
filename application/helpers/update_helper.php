<?php
function is_phppos_update_available()
{
	$url = (!defined("ENVIRONMENT") or ENVIRONMENT == 'development') ? 'http://phppointofsalestaging.com/current_version.php': 'http://4biz.vn/current_version.php';
	
   $ch = curl_init($url);
  	curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
  	$current_version = curl_exec($ch);
  	curl_close($ch);

	return ($current_version != '' && (APPLICATION_VERSION != $current_version));
}

function is_on_phppos_host()
{
	return strpos($_SERVER['HTTP_HOST'],'4biz.vn') !== FALSE || strpos($_SERVER['HTTP_HOST'],'phppointofsalestaging.com') !== FALSE;
}
?>
