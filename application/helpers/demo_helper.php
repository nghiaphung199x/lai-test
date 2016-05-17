<?php
function is_on_demo_host()
{
	return $_SERVER['HTTP_HOST'] == 'demo.4biz.vn' || $_SERVER['HTTP_HOST'] == 'demo.phppointofsalestaging.com';
}
?>