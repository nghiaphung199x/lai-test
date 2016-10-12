<?php
$lang['config_info']='Store Configuration Information';

$lang['config_address']='Company Address';
$lang['config_phone']='Company Phone';
$lang['config_prefix']='Sale ID Prefix';
$lang['config_website']='Website';
$lang['config_fax']='Fax';
$lang['config_default_tax_rate']='Default Tax Rate %';


$lang['config_company_required']='Company name is a required field';

$lang['config_phone_required']='Company phone is a required field';
$lang['config_sale_prefix_required']='Sale ID prefix is a required field';
$lang['config_default_tax_rate_required']='The default tax rate is a required field';
$lang['config_default_tax_rate_number']='The default tax rate must be a number';
$lang['config_company_website_url']='Company website is not a valid URL (http://...)';
$lang['config_saved_successfully']='Configuration saved successfully';
$lang['config_saved_unsuccessfully']='Failed to save configuration. Configuration changes are not allowed in demo mode or taxes weren\'t saved correctly';
$lang['config_return_policy_required']='Return policy is a required field';
$lang['config_print_after_sale']='Print receipt after sale';
$lang['config_automatically_email_receipt']='Automatically Email receipt';
$lang['config_barcode_price_include_tax']='Include tax on barcode?';
$lang['disable_confirmation_sale']='Disable confirmation for complete sale';


$lang['config_currency_symbol'] = 'Currency Symbol';
$lang['config_backup_database'] = 'Backup Database';
$lang['config_restore_database'] = 'Restore Database';

$lang['config_number_of_items_per_page'] = 'Number Of Items Per Page';
$lang['config_date_format'] = 'Date Format';
$lang['config_time_format'] = 'Time Format';
$lang['config_company_logo'] = 'Company Logo';
$lang['config_delete_logo'] = 'Delete Logo';

$lang['config_optimize_database'] = 'Optimize Database';
$lang['config_database_optimize_successfully'] = 'Optimized Database Successfully';
$lang['config_payment_types'] = 'Payment Types';
$lang['select_sql_file'] = 'select .sql file';

$lang['restore_heading'] = 'This allows you to restore your database';

$lang['type_file'] = 'select .sql file from your computer';

$lang['restore'] = 'restore';

$lang['required_sql_file'] = 'No sql file is selected';

$lang['restore_db_success'] = 'DataBase is restored successfully';

$lang['db_first_alert'] = 'Are you sure of restoring the database?';
$lang['db_second_alert'] = 'Present data will be lost , continue?';
$lang['password_error'] = 'Password incorrect';
$lang['password_required'] = 'Password field cannot be blank';
$lang['restore_database_title'] = 'Restore Database';



$lang['config_environment'] = 'Environment';


$lang['config_sandbox'] = 'Sandbox';
$lang['config_production'] = 'Production';

$lang['config_default_payment_type'] = 'Default Payment Type';
$lang['config_speed_up_note'] = 'Only recommend if you have more than 10,000 items or customers';
$lang['config_hide_signature'] = 'Hide Signature';
$lang['config_round_cash_on_sales'] = 'Round to nearest .05 on receipt';
$lang['config_customers_store_accounts'] = 'Customers Store Accounts';
$lang['config_change_sale_date_when_suspending'] = 'Change sale date when suspending sale';
$lang['config_change_sale_date_when_completing_suspended_sale'] = 'Change sale date when completing suspended sale';
$lang['config_price_tiers'] = 'Price tiers';
$lang['config_add_tier'] = 'Add tier';
$lang['config_show_receipt_after_suspending_sale'] = 'Show receipt after suspending sale';
$lang['config_backup_overview'] = 'Backup Overview';
$lang['config_backup_overview_desc'] = 'Backing up your data is very important, but can be troublesome with large amount of data. If you have lots of images, items, and sales this can increase the size of your database.';
$lang['config_backup_options'] = 'We offer many options for backup to help you decide how to proceed';
$lang['config_backup_simple_option'] = 'Clicking "Backup database". This will attempt to download your whole database to a file. If you get a blank screen or can\'t download the file, try one of the other options.';
$lang['config_backup_phpmyadmin_1'] = 'PHPMyAdmin is a popular tool for managing your databases. If you are using the download version with installer, it can be accessed by going to';
$lang['config_backup_phpmyadmin_2'] = 'Your username is root and password is what you used during initial installation of PHP POS. Once logged in select your database from the panel on the left. Then select export and then submit the form.';
$lang['config_backup_control_panel'] = 'If you have installed on your own server that has a control panel such as cpanel, look for the backup module which will often let you download backups of your database.';
$lang['config_backup_mysqldump'] = 'If you have access to the shell and mysqldump on your server, you can try to execute it by clicking the below link. Otherwise  you will need to try other options.';
$lang['config_mysqldump_failed'] = 'mysqldump backup has failed. This could be due to a server restriction or the command might not be available. Please try another backup method';



$lang['config_looking_for_location_settings'] = 'Looking for other configuration options? Go to';
$lang['config_module'] = 'Module';
$lang['config_automatically_calculate_average_cost_price_from_receivings'] = 'Calculate Average Cost Price from Receivings';
$lang['config_averaging_method'] = 'Averaging Method';
$lang['config_historical_average'] = 'Historical Average';
$lang['config_moving_average'] = 'Moving Average';

$lang['config_hide_dashboard_statistics'] = 'Hide Dashboard Statistics';
$lang['config_hide_store_account_payments_in_reports'] = 'Hide Store Account Payments In Reports';
$lang['config_id_to_show_on_sale_interface'] = 'Item ID to Show on Sales Interface';
$lang['config_auto_focus_on_item_after_sale_and_receiving'] = 'Auto Focus On Item Field When using Sales/Receivings Interfaces';
$lang['config_automatically_show_comments_on_receipt'] = 'Automatically Show Comments on Receipt';
$lang['config_hide_customer_recent_sales'] = 'Hide Recent Sales for Customer';
$lang['config_spreadsheet_format'] = 'Spreadsheet Format';
$lang['config_csv'] = 'CSV';
$lang['config_xlsx'] = 'XLSX';
$lang['config_disable_giftcard_detection'] = 'Disable Giftcard Detection';
$lang['config_disable_subtraction_of_giftcard_amount_from_sales'] = 'Disable giftcard subtraction when using giftcard during sale';
$lang['config_always_show_item_grid'] = 'Always Show Item Grid';
$lang['config_legacy_detailed_report_export'] = 'Legacy Detailed Report Excel Export';
$lang['config_print_after_receiving'] = 'Print receipt after receiving';
$lang['config_company_info'] = 'Company Information';
$lang['config_tax_currency_info'] = 'Taxes & Currency';
$lang['config_sales_receipt_info'] = 'Sales & Receipt';
$lang['config_suspended_sales_layaways_info'] = 'Suspended Sales/Layaways';
$lang['config_application_settings_info'] = 'Application Settings';
$lang['config_hide_barcode_on_sales_and_recv_receipt'] = 'Hide barcode on receipts';
$lang['config_round_tier_prices_to_2_decimals'] = 'Round tier Prices to 2 decimals';
$lang['config_group_all_taxes_on_receipt'] = 'Group all taxes on receipt';
$lang['config_receipt_text_size'] = 'Receipt text size';
$lang['config_small'] = 'Small';
$lang['config_medium'] = 'Medium';
$lang['config_large'] = 'Large';
$lang['config_extra_large'] = 'Extra large';
$lang['config_select_sales_person_during_sale'] = 'Select sales person during sale';
$lang['config_default_sales_person'] = 'Default sales person';
$lang['config_require_customer_for_sale'] = 'Require customer for sale';

$lang['config_hide_store_account_payments_from_report_totals'] = 'Hide store account payments from report totals';
$lang['config_disable_sale_notifications'] = 'Disable sale notifications';
$lang['config_id_to_show_on_barcode'] = 'ID to show on barcode';
$lang['config_currency_denoms'] = 'Currency Denominations';
$lang['config_currency_value'] = 'Currency Value';
$lang['config_add_currency_denom'] = 'Add currency denomination';
$lang['config_enable_timeclock'] = 'Enable Time Clock';
$lang['config_change_sale_date_for_new_sale'] = 'Change Sale Date For New Sale';
$lang['config_dont_average_use_current_recv_price'] = 'Don\'t average, use current received price';
$lang['config_number_of_recent_sales'] = 'Number of recent sales by customer to show';
$lang['config_hide_suspended_recv_in_reports'] = 'Hide suspended Receivings in reports';
$lang['config_calculate_profit_for_giftcard_when'] = 'Calculate Gift Card Profit When';
$lang['config_selling_giftcard'] = 'Selling Gift Card';
$lang['config_redeeming_giftcard'] = 'Redeeming Gift Card';
$lang['config_remove_customer_contact_info_from_receipt'] = 'Remove customer contact info from receipt';
$lang['config_speed_up_search_queries'] = 'Speed up search queries?';




$lang['config_redirect_to_sale_or_recv_screen_after_printing_receipt'] = 'Redirect to sale or receiving screen after printing receipt';
$lang['config_enable_sounds'] = 'Enable sounds for status messages';
$lang['config_charge_tax_on_recv'] = 'Charge tax on receivings';
$lang['config_report_sort_order'] = 'Report Sort Order';
$lang['config_asc'] = 'Oldest first';
$lang['config_desc'] = 'Newest first';
$lang['config_do_not_group_same_items'] = 'Do NOT group items that are the same';
$lang['config_show_item_id_on_receipt'] = 'Show item id on receipt';
$lang['config_show_language_switcher'] = 'Show Language Switcher';
$lang['config_do_not_allow_out_of_stock_items_to_be_sold'] = 'Do not allow out of stock items to be sold';
$lang['config_number_of_items_in_grid'] = 'Number of items per page in grid';
$lang['config_edit_item_price_if_zero_after_adding'] = 'Edit item price if 0 after adding to sale';
$lang['config_override_receipt_title'] = 'Override receipt title';
$lang['config_automatically_print_duplicate_receipt_for_cc_transactions'] = 'Automatically print duplicate receipt for credit card transactions';






$lang['config_default_type_for_grid'] = 'Default type for Grid';
$lang['config_billing_is_managed_through_paypal'] = 'Billing is managed through  <a target="_blank" href="http://paypal.com">Paypal</a>. You can cancel your subscription by clicking <a target="_blank" href="https://www.paypal.com/cgi-bin/webscr?cmd=_subscr-find&alias=BNTRX72M8UZ2E">here</a>. You can <a href="http://4biz.vn/update_billing.php" target="_blank">update billing here</a>.';
$lang['config_cannot_change_language'] = 'Language cannot be changed on the demo. To try another language create a new employee and assign them a language of your choosing';
$lang['disable_quick_complete_sale'] = 'Disable sale quick complete';
$lang['config_fast_user_switching'] = 'Enable fast user switching (password not required)';
$lang['config_require_employee_login_before_each_sale'] = 'Require employee login before each sale';
$lang['config_keep_same_location_after_switching_employee'] = 'Keep same location after switching employee';
$lang['config_number_of_decimals'] = 'Number of decimals';
$lang['config_let_system_decide'] = 'Let system decide (Recommended)';
$lang['config_thousands_separator'] = 'Thousands Separator';
$lang['config_legacy_search_method'] = 'Legacy Search Method';
$lang['config_hide_store_account_balance_on_receipt'] = 'Hide store account balance on receipt';
$lang['config_decimal_point'] = 'Decimal Point';
$lang['config_hide_out_of_stock_grid'] = 'Hide out of stock items in grid';
$lang['config_highlight_low_inventory_items_in_items_module'] = 'Highlight low inventory items in items module';
$lang['config_sort'] = 'Sort';
$lang['config_enable_customer_loyalty_system'] = 'Enable Customer Loyalty system';
$lang['config_spend_to_point_ratio'] = 'Spend amount to point ratio';
$lang['config_point_value'] = 'Point Value';
$lang['config_hide_points_on_receipt'] = 'Hide Points On Receipt';
$lang['config_show_clock_on_header'] = 'Show Clock in Header';
$lang['config_show_clock_on_header_help_text'] = 'This is visible only on wide screens';
$lang['config_loyalty_explained_spend_amount'] = 'Enter the amount to spend';
$lang['config_loyalty_explained_points_to_earn'] = 'Enter points to be earned';
$lang['config_simple'] = 'Simple';
$lang['config_advanced'] = 'Advanded';
$lang['config_loyalty_option'] = 'Loyalty Program Option';
$lang['config_number_of_sales_for_discount'] = 'Number of sales for discount';
$lang['config_discount_percent_earned'] = 'Discount percent earned when reaching sales';
$lang['hide_sales_to_discount_on_receipt'] = 'Hide sales to discount on receipt';
$lang['config_hide_price_on_barcodes'] = 'Hide price on barcodes';
$lang['config_always_use_average_cost_method'] = 'Always Use Global Average Cost Price For A Sale Item\'s Cost Price';
$lang['config_test_mode'] = 'Test mode';
$lang['config_test_mode_help'] = 'Sales NOT saved';
$lang['config_require_customer_for_suspended_sale'] = 'Require customer for suspended sale';
$lang['config_default_new_items_to_service'] = 'Default New Items as service items';






$lang['config_prompt_for_ccv_swipe'] = 'Prompt for CCV when swiping credit card';
$lang['config_disable_store_account_when_over_credit_limit'] = 'Disable store account when over credit limit';
$lang['config_mailing_labels_type'] = 'Mailing Labels Format';
$lang['config_phppos_session_expiration'] = 'Session expiration';
$lang['config_hours'] = 'Hours';
$lang['config_never'] = 'Never';
$lang['config_on_browser_close'] = 'On Browser Close';
$lang['config_do_not_allow_below_cost'] = 'Do NOT allow items to be sold below cost price';
$lang['config_store_account_statement_message'] = 'Store Account Statement Message';
$lang['config_disable_margin_calculator'] = 'Disable price margin calculator';
$lang['config_disable_quick_edit'] = 'Disable quick edit on manage pages';
?>