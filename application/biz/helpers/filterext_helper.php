<?php
function rewriteUrl($value){

	/*a à ả ã á ạ ă ằ ẳ ẵ ắ ặ â ầ ẩ ẫ ấ ậ b c d đ e è ẻ ẽ é ẹ ê ề ể ễ ế ệ
		f g h i ì ỉ ĩ í ị j k l m n o ò ỏ õ ó ọ ô ồ ổ ỗ ố ộ ơ ờ ở ỡ ớ ợ
	p q r s t u ù ủ ũ ú ụ ư ừ ử ữ ứ ự v w x y ỳ ỷ ỹ ý ỵ z*/
	$value = html_entity_decode ($value);
	$charaterA = '#(à|ả|ã|á|ạ|ă|ằ|ẳ|ẵ|ắ|ặ|â|ầ|ẩ|ẫ|ấ|ậ)#imsU';
	$repleceCharaterA = 'a';
	$value = preg_replace($charaterA,$repleceCharaterA,$value);

	$charaterD = '#(đ)#imsU';
	$replaceCharaterD = 'd';
	$value = preg_replace($charaterD,$replaceCharaterD,$value);

	$charaterD = '#(è|ẻ|ẽ|é|ẹ|ê|ề|ể|ễ|ế|ệ)#imsU';
	$replaceCharaterD = 'e';
	$value = preg_replace($charaterD,$replaceCharaterD,$value);

	$charaterI = '#(ì|ỉ|ĩ|í|ị)#imsU';
	$replaceCharaterI = 'i';
	$value = preg_replace($charaterI,$replaceCharaterI,$value);

	$charaterO = '#(ò|ỏ|õ|ó|ọ|ô|ồ|ổ|ỗ|ố|ộ|ơ|ờ|ở|ỡ|ớ|ợ)#imsU';
	$replaceCharaterO = 'o';
	$value = preg_replace($charaterO,$replaceCharaterO,$value);

	$charaterU = '#(ù|ủ|ũ|ú|ụ|ư|ừ|ử|ữ|ứ|ự)#imsU';
	$replaceCharaterU = 'u';
	$value = preg_replace($charaterU,$replaceCharaterU,$value);

	$charaterY = '#(ỳ|ỷ|ỹ|ý)#imsU';
	$replaceCharaterY = 'y';
	$value = preg_replace($charaterY,$replaceCharaterY,$value);

	$value = trim(mb_strtolower(url_title($value), 'UTF-8'));
	return $value;
}