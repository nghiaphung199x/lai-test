<?php
function is_sale_integrated_cc_processing()
{
	$CI =& get_instance();
	$cc_payment_amount = $CI->sale_lib->get_payment_amount(lang('common_credit'));
	return $CI->Location->get_info_for_key('enable_credit_card_processing') && $cc_payment_amount != 0;
}

function is_credit_card_sale()
{
	$CI =& get_instance();
	$cc_payment_amount = $CI->sale_lib->get_payment_amount(lang('common_credit'));
	return $cc_payment_amount != 0;
}

function is_store_account_sale()
{
	$CI =& get_instance();
	$store_account_amount = $CI->sale_lib->get_payment_amount(lang('common_store_account'));
	return $store_account_amount != 0;
}


function sale_has_partial_credit_card_payment()
{
	$CI =& get_instance();
	$cc_partial_payment_amount = $CI->sale_lib->get_payment_amount(lang('sales_partial_credit'));
	return $cc_partial_payment_amount != 0;
}

function getStatusOfDelivery($delivery_date)
{
	$CI =& get_instance();
	$warning_days_level1 = (int) $CI->config->item('day_warning_level1');
	$warning_days_level2 = (int) $CI->config->item('day_warning_level2');
	$warning_days_level3 = (int) $CI->config->item('day_warning_level3');
	
	if( strlen($delivery_date) ) {
		$current = strtotime(date('Y-m-d'));
		$delivery_datetime = strtotime($delivery_date);
		
		$diff = $delivery_datetime - $current;
		if ($diff <= ($warning_days_level1 * 24 * 60 * 60)) {
			$status = 'delivery_warning_lv1';
		} elseif ($diff <= ($warning_days_level2 * 24 * 60 * 60)) {
			$status = 'delivery_warning_lv2';
		} elseif ($diff <= ($warning_days_level3 * 24 * 60 * 60)) {
			$status = 'delivery_warning_lv3';
		}
	}
	return $status;
}
?>