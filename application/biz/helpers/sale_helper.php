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
	$status = '';
	if( strlen($delivery_date) ) {
		$current = strtotime(date('Y-m-d'));
		$delivery_datetime = strtotime($delivery_date);
		
		$diff = $delivery_datetime - $current;
		
		if( $diff <= 0 )
		{
			$status = 'delivery_expire';
		} elseif ($diff <= (24 * 60 * 60)) {
			$status = 'delivery_warning_lv1';
		} elseif ($diff <= (7 * 24 * 60 * 60)) {
			$status = 'delivery_warning_lv2';
		}
	}
	return $status;
}
?>