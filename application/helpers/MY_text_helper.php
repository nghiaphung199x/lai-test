<?php
function character_limiter($str, $n = 500, $end_char = '&#8230;')
{
	if (strlen($str) < $n)
	{
		return $str;
	}

	if (function_exists('mb_substr'))
	{
		return mb_substr($str,0, $n).$end_char;
	}
	
	return substr($str,0, $n).$end_char;
}

function replace_newline($string) 
{
	return (string)str_replace(array("\r", "\r\n", "\n"), '', $string);
}

function number_pad($number,$n) 
{
	return str_pad((int) $number,$n,"0",STR_PAD_LEFT);
}

function H($input)
{
	return htmlentities($input, ENT_QUOTES, 'UTF-8', false);
}

//From http://stackoverflow.com/a/26537463/627473
function escape_full_text_boolean_search($search)
{
	$return = preg_replace('/[+\-><\(\)~*\"@]+/', ' ', $search);
	if(trim($return) == "")
	{
		//If we have no search return a bar character is this prevents fatal error
		$return = '|';
	}
	return $return;
}

function does_contain_only_digits($string)
{
	return (preg_match('/^[0-9]+$/', $string));
}
?>