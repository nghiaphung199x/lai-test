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
?>