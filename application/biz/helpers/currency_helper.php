<?php
function to_currency($number, $decimals = 2)
{
	$CI =& get_instance();
	
	$decimals_system_decide = true;
	
	if ($CI->config->item('number_of_decimals') !== NULL && $CI->config->item('number_of_decimals')!= '')
	{
		$decimals = (int)$CI->config->item('number_of_decimals');
		$decimals_system_decide = false;
	}
	
	$thousands_separator = $CI->config->item('thousands_separator') ? $CI->config->item('thousands_separator') : ',';
	$decimal_point = $CI->config->item('decimal_point') ? $CI->config->item('decimal_point') : '.';
	
	$currency_symbol = $CI->config->item('currency_symbol') ? $CI->config->item('currency_symbol') : '$';
	
	if($number >= 0)
	{
		$ret = number_format($number, $decimals, $decimal_point, $thousands_separator) . ' ' . $currency_symbol;
   }
   else
   {
		$ret = '<span style="white-space:nowrap;">-</span>'.number_format(abs($number), $decimals, $decimal_point, $thousands_separator) . ' ' .$currency_symbol;
   }

	 if ($decimals_system_decide && $decimals >=2)
	 {
		 return preg_replace('/(?<=\d{2})0+$/', '', $ret);
	 }
	 else
	 {
		 return $ret;
	 }
	
}

function to_currency_abs($number, $decimals = 2)
{
	$number = abs($number);

	$CI =& get_instance();
	
	$decimals_system_decide = true;
	
	if ($CI->config->item('number_of_decimals') !== NULL && $CI->config->item('number_of_decimals')!= '')
	{
		$decimals = (int)$CI->config->item('number_of_decimals');
		$decimals_system_decide = false;
	}
	
	$thousands_separator = $CI->config->item('thousands_separator') ? $CI->config->item('thousands_separator') : ',';
	$decimal_point = $CI->config->item('decimal_point') ? $CI->config->item('decimal_point') : '.';
	
	$currency_symbol = $CI->config->item('currency_symbol') ? $CI->config->item('currency_symbol') : '$';
	
	if($number >= 0)
	{
		$ret = number_format($number, $decimals, $decimal_point, $thousands_separator) . ' ' . $currency_symbol;
   }
   else
   {
		$ret = '<span style="white-space:nowrap;">-</span>'.number_format(abs($number), $decimals, $decimal_point, $thousands_separator) . ' ' .$currency_symbol;
   }

	 if ($decimals_system_decide && $decimals >=2)
	 {
		 return preg_replace('/(?<=\d{2})0+$/', '', $ret);
	 }
	 else
	 {
		 return $ret;
	 }
	
}

function round_to_nearest_05($amount)
{
	return round($amount * 2, 1) / 2;
}

function to_currency_no_money($number, $decimals = 2)
{
	$CI =& get_instance();
	
	$decimals_system_decide = true;
	
	//Only use override if decimals passed in is less than 5 and we have configured a decimal override
	if ($decimals <=5 && $CI->config->item('number_of_decimals') !== NULL && $CI->config->item('number_of_decimals')!= '')
	{
		$decimals = (int)$CI->config->item('number_of_decimals');
		$decimals_system_decide = false;
	}
	
	 $ret = number_format($number, $decimals, '.', '');
	 
	 if ($decimals_system_decide && $decimals >=2)
	 {
		 return preg_replace('/(?<=\d{2})0+$/', '', $ret);
	 }
	 else
	 {
		 return $ret;
	 }
		 
}

function to_quantity($val, $show_not_set = TRUE)
{
	if ($val !== NULL)
	{
		return $val == (int)$val ? (int)$val : rtrim($val, '0');		
	}
	
	if ($show_not_set)
	{
		return lang('common_not_set');
	}
	
	return '';
	
}

function to_quantity_abs($val, $show_not_set = TRUE)
{
	$val = abs($val);

	if ($val !== NULL)
	{
		return $val == (int)$val ? (int)$val : rtrim($val, '0');		
	}
	
	if ($show_not_set)
	{
		return lang('common_not_set');
	}
	
	return '';
	
}

function getStringNumber($amount)
	{
		 if($amount <=0)
        {
            return $textnumber="Tiền phải là số nguyên dương lớn hơn số 0";
        }
        $Text=array("không", "một", "hai", "ba", "bốn", "năm", "sáu", "bảy", "tám", "chín");
        $TextLuythua =array("","nghìn", "triệu", "tỷ", "ngàn tỷ", "triệu tỷ", "tỷ tỷ");
        $textnumber = "";
        $length = strlen($amount);
       
        for ($i = 0; $i < $length; $i++)
        $unread[$i] = 0;
       
        for ($i = 0; $i < $length; $i++)
        {              
            $so = substr($amount, $length - $i -1 , 1);               
           
            if ( ($so == 0) && ($i % 3 == 0) && ($unread[$i] == 0)){
                for ($j = $i+1 ; $j < $length ; $j ++)
                {
                    $so1 = substr($amount,$length - $j -1, 1);
                    if ($so1 != 0)
                        break;
                }                      
                      
                if (intval(($j - $i )/3) > 0){
                    for ($k = $i ; $k <intval(($j-$i)/3)*3 + $i; $k++)
                        $unread[$k] =1;
                }
            }
        }
       
        for ($i = 0; $i < $length; $i++)
        {       
            $so = substr($amount,$length - $i -1, 1);      
            if ($unread[$i] ==1)
            continue;
           
            if ( ($i% 3 == 0) && ($i > 0))
            $textnumber = $TextLuythua[$i/3] ." ". $textnumber;    
           
            if ($i % 3 == 2 )
            $textnumber = 'trăm ' . $textnumber;
           
            if ($i % 3 == 1)
            $textnumber = 'mươi ' . $textnumber;
           
           
            $textnumber = $Text[$so] ." ". $textnumber;
        }
       
        //Phai de cac ham replace theo dung thu tu nhu the nay
        $textnumber = str_replace("không mươi", "lẻ", $textnumber);
        $textnumber = str_replace("lẻ không", "", $textnumber);
        $textnumber = str_replace("mươi không", "mươi", $textnumber);
        $textnumber = str_replace("một mươi", "mười", $textnumber);
        $textnumber = str_replace("mươi năm", "mươi lăm", $textnumber);
        $textnumber = str_replace("mươi một", "mươi mốt", $textnumber);
        $textnumber = str_replace("mười năm", "mười lăm", $textnumber);
        return ucfirst($textnumber."đồng chẵn");
}

function NumberFormatToCurrency($number){
    $number = abs($number);
    $CI =& get_instance();
    $decimals_system_decide = true;
    if ($CI->config->item('number_of_decimals') !== NULL && $CI->config->item('number_of_decimals')!= ''){
        $decimals = (int)$CI->config->item('number_of_decimals');
        $decimals_system_decide = false;
    }
    $thousands_separator = $CI->config->item('thousands_separator') ? $CI->config->item('thousands_separator') : ',';
    $decimal_point = $CI->config->item('decimal_point') ? $CI->config->item('decimal_point') : '.';
    if($number >= 0){
        $ret = number_format($number, $decimals, $decimal_point, $thousands_separator);
   }
   else{
        $ret = '<span style="white-space:nowrap;">-</span>'.number_format(abs($number), $decimals, $decimal_point, $thousands_separator);
   }
    if ($decimals_system_decide && $decimals >=2){
       return preg_replace('/(?<=\d{2})0+$/', '', $ret);
    }
    else  return $ret;
}


?>