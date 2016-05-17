<?php
require_once (APPPATH . "controllers/Config.php");
class BizConfig extends Config 
{
	function save()
	{
		$this->load->helper('demo');
		$this->load->model('Appfile');
		
		if(!empty($_FILES["company_logo"]) && $_FILES["company_logo"]["error"] == UPLOAD_ERR_OK && !is_on_demo_host())
		{
			$allowed_extensions = array('png', 'jpg', 'jpeg', 'gif');
			$extension = strtolower(pathinfo($_FILES["company_logo"]["name"], PATHINFO_EXTENSION));
			
			if (in_array($extension, $allowed_extensions))
			{
				$config['image_library'] = 'gd2';
				$config['source_image']	= $_FILES["company_logo"]["tmp_name"];
				$config['create_thumb'] = FALSE;
				$config['maintain_ratio'] = TRUE;
				$config['width']	 = 255;
				$config['height']	= 90;
				$this->load->library('image_lib', $config); 
				$this->image_lib->resize();
				$company_logo = $this->Appfile->save($_FILES["company_logo"]["name"], file_get_contents($_FILES["company_logo"]["tmp_name"]), $this->config->item('company_logo'));
			}
		}
		elseif($this->input->post('delete_logo'))
		{
			$this->Appfile->delete($this->config->item('company_logo'));
		}
		
		//Catch an error if our company name is NOT set. This can happen if logo uploaded is larger than post size
		if (!$this->input->post('company'))
		{
			echo json_encode(array('success'=>false,'message'=>lang('config_saved_unsuccessfully')));
			exit;
		}
		
		$this->load->helper('directory');
		$valid_languages = str_replace(DIRECTORY_SEPARATOR,'',directory_map(APPPATH.'language/', 1));
		$batch_save_data=array(
			'company'=>$this->input->post('company'),
			'sale_prefix'=>$this->input->post('sale_prefix') ? $this->input->post('sale_prefix') : 'POS',
			'website'=>$this->input->post('website'),
			'prices_include_tax' => $this->input->post('prices_include_tax') ? 1 : 0,
			'default_tax_1_rate'=>$this->input->post('default_tax_1_rate'),		
			'default_tax_1_name'=>$this->input->post('default_tax_1_name'),		
			'default_tax_2_rate'=>$this->input->post('default_tax_2_rate'),	
			'default_tax_2_name'=>$this->input->post('default_tax_2_name'),
			'default_tax_2_cumulative' => $this->input->post('default_tax_2_cumulative') ? 1 : 0,
			'default_tax_3_rate'=>$this->input->post('default_tax_3_rate'),	
			'default_tax_3_name'=>$this->input->post('default_tax_3_name'),
			'default_tax_4_rate'=>$this->input->post('default_tax_4_rate'),	
			'default_tax_4_name'=>$this->input->post('default_tax_4_name'),
			'default_tax_5_rate'=>$this->input->post('default_tax_5_rate'),	
			'default_tax_5_name'=>$this->input->post('default_tax_5_name'),
			'currency_symbol'=>$this->input->post('currency_symbol'),
			'language'=>in_array($this->input->post('language'), $valid_languages) ? $this->input->post('language') : 'english',
			'date_format'=>$this->input->post('date_format'),
			'time_format'=>$this->input->post('time_format'),
			'print_after_sale'=>$this->input->post('print_after_sale') ? 1 : 0,
			'print_after_receiving'=>$this->input->post('print_after_receiving') ? 1 : 0,
			'round_cash_on_sales'=>$this->input->post('round_cash_on_sales') ? 1 : 0,
			'automatically_email_receipt'=>$this->input->post('automatically_email_receipt') ? 1 : 0,
			'automatically_show_comments_on_receipt' => $this->input->post('automatically_show_comments_on_receipt') ? 1 : 0,
			'id_to_show_on_sale_interface' => $this->input->post('id_to_show_on_sale_interface'),
			'auto_focus_on_item_after_sale_and_receiving' => $this->input->post('auto_focus_on_item_after_sale_and_receiving') ? 1 : 0,
			'barcode_price_include_tax'=>$this->input->post('barcode_price_include_tax') ? 1 : 0,
			'hide_signature'=>$this->input->post('hide_signature') ? 1 : 0,
			'hide_customer_recent_sales'=>$this->input->post('hide_customer_recent_sales') ? 1 : 0,
			'disable_confirmation_sale'=>$this->input->post('disable_confirmation_sale') ? 1 : 0,
			'track_cash' => $this->input->post('track_cash') ? 1 : 0,
			'number_of_items_per_page'=>$this->input->post('number_of_items_per_page'),
			'additional_payment_types' => $this->input->post('additional_payment_types'),
			'hide_layaways_sales_in_reports' => $this->input->post('hide_layaways_sales_in_reports') ? 1 : 0,
			'hide_store_account_payments_in_reports' => $this->input->post('hide_store_account_payments_in_reports') ? 1 : 0,
			'change_sale_date_when_suspending' => $this->input->post('change_sale_date_when_suspending') ? 1 : 0,
			'change_sale_date_when_completing_suspended_sale' => $this->input->post('change_sale_date_when_completing_suspended_sale') ? 1 : 0,
			'show_receipt_after_suspending_sale' => $this->input->post('show_receipt_after_suspending_sale') ? 1 : 0,
			'customers_store_accounts' => $this->input->post('customers_store_accounts') ? 1 : 0,
			'calculate_average_cost_price_from_receivings' => $this->input->post('calculate_average_cost_price_from_receivings') ? 1 : 0,
			'averaging_method' => $this->input->post('averaging_method'),
			'hide_dashboard_statistics' => $this->input->post('hide_dashboard_statistics') ? 1 : 0,
			'show_language_switcher' => $this->input->post('show_language_switcher') ? 1 : 0,
			'show_clock_on_header' => $this->input->post('show_clock_on_header') ? 1 : 0,
			'disable_giftcard_detection' => $this->input->post('disable_giftcard_detection') ? 1 : 0,
			'always_show_item_grid' => $this->input->post('always_show_item_grid') ? 1 : 0,
			'hide_out_of_stock_grid' => $this->input->post('hide_out_of_stock_grid') ? 1 : 0,
			'default_payment_type'=> $this->input->post('default_payment_type'),
			'return_policy'=>$this->input->post('return_policy'),
			'announcement_special'=>$this->input->post('announcement_special'),
			'spreadsheet_format' => $this->input->post('spreadsheet_format'),
			'legacy_detailed_report_export' => $this->input->post('legacy_detailed_report_export') ? 1 : 0,
			'hide_barcode_on_sales_and_recv_receipt' => $this->input->post('hide_barcode_on_sales_and_recv_receipt') ? 1 : 0,
			'round_tier_prices_to_2_decimals' => $this->input->post('round_tier_prices_to_2_decimals') ? 1 : 0,
			'group_all_taxes_on_receipt' => $this->input->post('group_all_taxes_on_receipt') ? 1 : 0,
			'receipt_text_size' => $this->input->post('receipt_text_size'),
			'select_sales_person_during_sale' => $this->input->post('select_sales_person_during_sale') ? 1 : 0,
			'default_sales_person' => $this->input->post('default_sales_person'),
			'require_customer_for_sale' => $this->input->post('require_customer_for_sale') ? 1 : 0,
			'commission_default_rate' => (float)$this->input->post('commission_default_rate'),
			'hide_store_account_payments_from_report_totals' => $this->input->post('hide_store_account_payments_from_report_totals') ? 1 : 0,
			'disable_sale_notifications' => $this->input->post('disable_sale_notifications') ? 1 : 0,
			'change_sale_date_for_new_sale' => $this->input->post('change_sale_date_for_new_sale') ? 1 : 0,
			'id_to_show_on_barcode' => $this->input->post('id_to_show_on_barcode'),
			'timeclock' => $this->input->post('timeclock') ? 1 : 0,
			'number_of_recent_sales' => $this->input->post('number_of_recent_sales'),
			'hide_suspended_recv_in_reports' => $this->input->post('hide_suspended_recv_in_reports') ? 1 : 0,
			'calculate_profit_for_giftcard_when' => $this->input->post('calculate_profit_for_giftcard_when'),
			'remove_customer_contact_info_from_receipt' => $this->input->post('remove_customer_contact_info_from_receipt') ? 1 : 0,
			'speed_up_search_queries' => $this->input->post('speed_up_search_queries') ? 1 : 0,
			'redirect_to_sale_or_recv_screen_after_printing_receipt' => $this->input->post('redirect_to_sale_or_recv_screen_after_printing_receipt') ? 1 : 0,
			'enable_sounds' => $this->input->post('enable_sounds') ? 1 : 0,
			'charge_tax_on_recv' => $this->input->post('charge_tax_on_recv') ? 1 : 0,
			'report_sort_order' => $this->input->post('report_sort_order'),
			'do_not_group_same_items' => $this->input->post('do_not_group_same_items') ? 1 : 0,
			'show_item_id_on_receipt' => $this->input->post('show_item_id_on_receipt') ? 1: 0,
			'do_not_allow_out_of_stock_items_to_be_sold' => $this->input->post('do_not_allow_out_of_stock_items_to_be_sold') ? 1: 0,
			'number_of_items_in_grid' => $this->input->post('number_of_items_in_grid'),
			'edit_item_price_if_zero_after_adding' => $this->input->post('edit_item_price_if_zero_after_adding') ? 1 : 0,
			'override_receipt_title' => $this->input->post('override_receipt_title'),
			'automatically_print_duplicate_receipt_for_cc_transactions' => $this->input->post('automatically_print_duplicate_receipt_for_cc_transactions') ? 1: 0,
			'default_type_for_grid' => $this->input->post('default_type_for_grid'),
			'disable_quick_complete_sale' => $this->input->post('disable_quick_complete_sale') ? 1 : 0,
			'fast_user_switching' => $this->input->post('fast_user_switching') ? 1 : 0,
			'require_employee_login_before_each_sale' => $this->input->post('require_employee_login_before_each_sale') ? 1 : 0,
			'keep_same_location_after_switching_employee' => $this->input->post('keep_same_location_after_switching_employee') ? 1 : 0,
			'number_of_decimals' => $this->input->post('number_of_decimals'),
			'thousands_separator' => $this->input->post('thousands_separator'),
			'decimal_point' => $this->input->post('decimal_point'),
			'legacy_search_method' => $this->input->post('legacy_search_method') ? 1 : 0,
			'hide_store_account_balance_on_receipt' => $this->input->post('hide_store_account_balance_on_receipt') ? 1 : 0,
			'deleted_payment_types' => $this->input->post('deleted_payment_types') ? $this->input->post('deleted_payment_types') : '',
			'commission_percent_type' => $this->input->post('commission_percent_type'),
			'highlight_low_inventory_items_in_items_module' => $this->input->post('highlight_low_inventory_items_in_items_module') ? 1 : 0,
			'enable_customer_loyalty_system' => $this->input->post('enable_customer_loyalty_system') ? 1 : 0,
			'loyalty_option' =>$this->input->post('loyalty_option'),
			'number_of_sales_for_discount' => $this->input->post('number_of_sales_for_discount'),
			'discount_percent_earned' => (float)$this->input->post('discount_percent_earned'),
			'hide_sales_to_discount_on_receipt' => $this->input->post('hide_sales_to_discount_on_receipt') ? 1 : 0,
			'point_value' => $this->input->post('point_value'),
			'spend_to_point_ratio' => $this->input->post('spend_amount_for_points') && $this->input->post('points_to_earn') && is_numeric($this->input->post('spend_amount_for_points')) && is_numeric($this->input->post('points_to_earn')) ? $this->input->post('spend_amount_for_points').':'.$this->input->post('points_to_earn') : '',
			'hide_price_on_barcodes' => $this->input->post('hide_price_on_barcodes') ? 1 : 0,
			'always_use_average_cost_method' => $this->input->post('always_use_average_cost_method') ? 1 : 0,
			'test_mode' => $this->input->post('test_mode') ? 1 : 0,
			'require_customer_for_suspended_sale' => $this->input->post('require_customer_for_suspended_sale') ? 1 : 0,
			'default_new_items_to_service' => $this->input->post('default_new_items_to_service') ? 1 : 0,
			'prompt_for_ccv_swipe' => $this->input->post('prompt_for_ccv_swipe') ? 1 : 0,
			'disable_store_account_when_over_credit_limit' => $this->input->post('disable_store_account_when_over_credit_limit') ? 1 : 0,
			'mailing_labels_type' => $this->input->post('mailing_labels_type'),
			'phppos_session_expiration' => ($this->input->post('phppos_session_expiration') == 0 || ($this->input->post('phppos_session_expiration') >= (1*60*60) && $this->input->post('phppos_session_expiration') <= (24*60*60))) ? $this->input->post('phppos_session_expiration') : 0,
			'do_not_allow_below_cost' => $this->input->post('do_not_allow_below_cost') ? 1 : 0,
			'store_account_statement_message' => $this->input->post('store_account_statement_message'),
			'hide_points_on_receipt' => $this->input->post('hide_points_on_receipt') ? 1 : 0,
			'disable_margin_calculator' => $this->input->post('disable_margin_calculator') ? 1 : 0,
			'disable_quick_edit' => $this->input->post('disable_quick_edit')  ? 1 : 0,

			'config_sales_receipt_pdf_size' => $this->input->post('config_sales_receipt_pdf_size'),
		);
	
		
		if (isset($company_logo))
		{
			$batch_save_data['company_logo'] = $company_logo;
		}
		elseif($this->input->post('delete_logo'))
		{
			$batch_save_data['company_logo'] = 0;
		}
		
		if (is_on_demo_host())
		{
			$batch_save_data['language'] = 'english';
			$batch_save_data['currency_symbol'] = '$';
			$batch_save_data['number_of_decimals'] = '';
			$batch_save_data['thousands_separator'] =',';
			$batch_save_data['decimal_point'] ='.';
			$batch_save_data['company_logo'] = 0;
			$batch_save_data['company'] = '4Biz by LifeTek, Inc';
			$batch_save_data['test_mode'] = 0;
		}
		
		if($this->Appconfig->batch_save($batch_save_data) 
			&& $this->save_tiers($this->input->post('tiers_to_edit'), $this->input->post('tiers_to_add'), $this->input->post('tiers_to_delete'))
			&& $this->Register->save_register_currency_denominations($this->input->post('currency_denoms_name'), $this->input->post('currency_denoms_value')))
		{
			echo json_encode(array('success'=>true,'message'=>lang('config_saved_successfully')));
		}
		else
		{
			echo json_encode(array('success'=>false,'message'=>lang('config_saved_unsuccessfully')));
		}
	}
}
?>