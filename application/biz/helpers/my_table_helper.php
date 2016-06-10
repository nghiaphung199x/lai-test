<?php

function get_customer_manage_table($customer, $controller) 
{
	$CI = & get_instance();
	$controller_name = str_replace(BIZ_PREFIX, '', strtolower(get_class($CI)));	
	$table = '<table class="tablesorter table table-hover" id="sortable_table">';
	$headers = array(
			lang('customers_sms_tmp_code'),
			lang('customers_sms_tmp_name'),
			lang('customers_sms_tmp_phonenumber'),
			'&nbsp'
	);
	$table.='<thead><tr>';

	$count = 0;
	foreach ($headers as $header) {
		$count++;

		if ($count == 1) {
			$table.="<th class='leftmost'>$header</th>";
		} elseif ($count == count($headers)) {
			$table.="<th class='rightmost'>$header</th>";
		} else {
			$table.="<th>$header</th>";
		}
	}
	$table.='</tr></thead><tbody>';
        
	$table.=get_customer_manage_table_data_rows($customer, $controller);
        $table.='<tr><td colspan="2">'.anchor("$controller_name/send_sms_list", lang('customers_sms_send_list'), array(
            'title' => $customer_id,
            'id'=>'sendsms_list',
            'class' => 'bulk_edit_inactive btn btn-primary btn-lg',
            'data-id'=>$customer_id,
            'data-toggle'=> "modal",
            'data-target'=>"#myModal")).'</td>';
        $table.='<td class="delete_all_sms_tmp a-menu">'.anchor("$controller_name/manage_sms_tmp", lang('customers_sms_del_list'), array('title' => $customer_id, 'class' => 'btn btn-primary btn-lg delete_all_sms_tmp','data-id'=>$customer_id)).'</td></tr>';
	$table.='</tbody></table>';
	return $table;
}

function get_customer_manage_table_data_rows($customer, $controller) 
{
	$CI = & get_instance();
	$table_data_rows = '';

	foreach ($customer as $key =>$value) {
		$table_data_rows.=get_customer_data_row($key, $value, $controller);
	}
	if (count($customer) == 0) {
		$table_data_rows.="<tr><td colspan='11'><span class='col-md-12 text-center text-warning' >".lang('customers_no_sms')."</span></td></tr>";
	}
         
	return $table_data_rows;
}

function get_customer_data_row($customer_id, $customer_info, $controller) 
{
	$CI = & get_instance();
	$controller_name = str_replace(BIZ_PREFIX, '', strtolower(get_class($CI)));

	$table_data_row = '<tr>';
	$table_data_row.="<td>" . $customer_id . "</td>";
	$table_data_row.='<td>' . H($customer_info['name']) . '</a></td>';
	$table_data_row.='<td>' . H($customer_info['phone_number']) . '</td>';
	$table_data_row.='<td class="rightmost">' . anchor($controller_name . "/del_customer/$customer_id/2", lang('common_delete'), array('title' => $customer_id, 'class' => 'delete_sms_tmp','data-id'=>$customer_id)) . '</td>';
	$table_data_row.='</tr>';
	return $table_data_row;
}

?>