<?php
/*
Gets the html table to manage people.
*/
function get_people_manage_table($people,$controller)
{
	$CI =& get_instance();
	$table='<table class="tablesorter table  table-hover" id="sortable_table">';	
	$controller_name=str_replace(BIZ_PREFIX, '', strtolower(get_class($CI)));

	if($controller_name=='customers')
	{
		$headers = array('<input type="checkbox" id="select_all" /><label for="select_all"><span></span></label>', 
		lang('common_person_id'),
		lang('common_name'),
		lang('common_email'),
		lang('common_phone_number'));
		
		if ($CI->config->item('enable_customer_loyalty_system'))
		{
			if ($CI->config->item('loyalty_option') == 'simple')
			{
				$headers[] = lang('common_sales_until_discount');							
			}
			elseif ($CI->config->item('loyalty_option') == 'advanced')
			{
				$headers[] = lang('common_points').' / '.lang('customers_amount_to_spend_for_next_point');;							
			}
		}
		
		if ($CI->config->item('customers_store_accounts'))
		{
			$headers[] = lang('common_balance');
			$headers[] = '&nbsp;';
		}
		
		$headers[] = '&nbsp;';
	
	}
	elseif($controller_name=='employees')
	{
		$headers = array('<input type="checkbox" id="select_all" /><label for="select_all"><span></span></label>', 
		lang('common_person_id'),
		lang('common_name'),
		lang('common_email'),
		lang('common_phone_number'),
		lang('common_clone'),
		'&nbsp;',
		'&nbsp;');
	} 
	else
	{	
		$headers = array('<input type="checkbox" id="select_all" /><label for="select_all"><span></span></label>', 
		lang('common_person_id'),
		lang('common_name'),
		lang('common_email'),
		lang('common_phone_number'),
		'&nbsp;');
	}
	$table.='<thead><tr>';
	
	$count = 0;
	foreach($headers as $header)
	{
		$count++;
		
		if ($count == 1)
		{
			$table.="<th class='leftmost'>$header</th>";
		}
		elseif ($count == count($headers))
		{
			$table.="<th class='rightmost'>$header</th>";
		}
		else
		{
			$table.="<th>$header</th>";		
		}
	}
	$table.='</tr></thead><tbody>';
	$table.=get_people_manage_table_data_rows($people,$controller);
	$table.='</tbody></table>';
	return $table;
}

/*
Gets the html data rows for the people.
*/
function get_people_manage_table_data_rows($people,$controller)
{
	$CI =& get_instance();
	$table_data_rows='';
	
	foreach($people->result() as $person)
	{
		$table_data_rows.=get_person_data_row($person,$controller);
	}
	
	if($people->num_rows()==0)
	{
		$table_data_rows.="<tr><td colspan='10'><span class='col-md-12 text-center text-warning' >".lang('common_no_persons_to_display')."</span></tr>";
	}
	
	return $table_data_rows;
}

function get_person_data_row($person,$controller)
{
	static $has_send_message_permission;	
	$CI =& get_instance();
	if (!$has_send_message_permission)
	{
		$has_send_message_permission = $CI->Employee->has_module_action_permission('messages','send_message', $CI->Employee->get_logged_in_employee_info()->person_id);
	}
	
	$controller_name=str_replace(BIZ_PREFIX, '', strtolower(get_class($CI)));
	$avatar_url=$person->image_id ?  site_url('app_files/view/'.$person->image_id) : base_url('assets/assets/images/avatar-default.jpg');
	$six_months_ago = date('Y-m-d', strtotime('-6 months'));
	
	$today = date('Y-m-d').'%2023:59:59';	
	$link = site_url('reports/specific_'.($controller_name == 'customers' ? 'customer' : 'employee').'/'.$six_months_ago.'/'.$today.'/'.$person->person_id.'/all/0');
	$table_data_row='<tr>';	
	$table_data_row.="<td><input type='checkbox' name='person_$person->person_id' id='person_$person->person_id' value='".$person->person_id."'/><label for='person_$person->person_id'><span></span></label></td>";
	
	$table_data_row.='<td>'.$person->person_id.'</td>';
	$table_data_row.='<td ><a href="'.$link.'" class="underline">'.H($person->first_name).' '.H($person->last_name).'</a></td>';
	$table_data_row.='<td>'.mailto(H($person->email),H($person->email), array('class' => 'underline')).'</td>';
	$table_data_row.='<td>'.H($person->phone_number).'</td>';	
	if($controller_name=='customers')
	{	

		if ($CI->config->item('enable_customer_loyalty_system'))
		{
			if ($CI->config->item('loyalty_option') == 'advanced')
			{
	         list($spend_amount_for_points, $points_to_earn) = explode(":",$CI->config->item('spend_to_point_ratio'),2);
				
				$table_data_row.='<td>'.to_quantity($person->points). ' / '.to_currency($spend_amount_for_points - $person->current_spend_for_points).'</td>';							
			}
			elseif($CI->config->item('loyalty_option') == 'simple')
			{				
			   $sales_until_discount = $CI->config->item('number_of_sales_for_discount') - $person->current_sales_for_discount;
				$table_data_row.='<td>'.to_quantity($sales_until_discount).'</td>';			
			}
		}
		
		if ($CI->config->item('customers_store_accounts'))
		{
			$table_data_row.='<td>'.to_currency($person->balance).'</td>';		
			$table_data_row.='<td>'.anchor($controller_name."/pay_now/$person->person_id",lang('common_pay'),array('title'=>lang('common_pay'),'class'=>'btn btn-primary ')).'</td>';
		}
		
	}
	if($controller_name=='employees')
	{	
		$table_data_row.='<td class="rightmost">'.anchor($controller_name."/clone_employee/$person->person_id	", lang('common_clone'),array('class'=>' ','title'=>lang('common_clone'))).'</td>';			
		
		if ($has_send_message_permission)
		{
			$table_data_row.='<td class="text-center"> <a href="'.site_url('messages/send_invidual_message/').'/'.$person->person_id.'" class="1	 manage-employees-message"  data-toggle="modal" data-target="#myModal" ><i class="ion-email"></i></a> </td>';		
		}
	}
	$table_data_row.='<td>'.anchor($controller_name."/view/$person->person_id/2", lang('common_edit'),array('class'=>'  update-person','title'=>lang($controller_name.'_update'))).'</td>';
	$table_data_row.="<td><a href='$avatar_url' class='rollover'><img src='".$avatar_url."' alt='".H($person->first_name).' '.H($person->last_name)."' class='img-polaroid avatar' width='45' /></a></td>";
	$table_data_row.='</tr>';
	
	return $table_data_row;
}

/*
Gets the html table to manage suppliers.
*/
function get_supplier_manage_table($suppliers,$controller)
{
	$CI =& get_instance();
	$table='<table class="tablesorter table table-hover" id="sortable_table">';	
	$headers = array('<input type="checkbox" id="select_all" /><label for="select_all"><span></span></label>',
	lang('suppliers_id'),
	lang('suppliers_company_name'),
	lang('common_last_name'),
	lang('common_first_name'),
	lang('common_email'),
	lang('common_phone_number'),
	'&nbsp;',
	'&nbsp;');
	$table.='<thead><tr>';
	$count = 0;
	foreach($headers as $header)
	{
		$count++;
		
		if ($count == 1)
		{
			$table.="<th>$header</th>";
		}
		elseif ($count == count($headers))
		{
			$table.="<th>$header</th>";
		}
		else
		{
			$table.="<th>$header</th>";		
		}
	}
	
	$table.='</tr></thead><tbody>';
	$table.=get_supplier_manage_table_data_rows($suppliers,$controller);
	$table.='</tbody></table>';
	return $table;
}

/*
Gets the html data rows for the supplier.
*/
function get_supplier_manage_table_data_rows($suppliers,$controller)
{
	$CI =& get_instance();
	$table_data_rows='';
	
	foreach($suppliers->result() as $supplier)
	{
		$table_data_rows.=get_supplier_data_row($supplier,$controller);
	}
	
	if($suppliers->num_rows()==0)
	{
		$table_data_rows.="<tr><td colspan='8'><span class='col-md-12 text-center text-warning' >".lang('common_no_persons_to_display')."</span></td></tr>";
	}
	
	return $table_data_rows;
}

function get_supplier_data_row($supplier,$controller)
{
	$CI =& get_instance();
	$controller_name=str_replace(BIZ_PREFIX, '', strtolower(get_class($CI)));
	$avatar_url=$supplier->image_id ?  site_url('app_files/view/'.$supplier->image_id) : base_url('assets/assets/images/avatar-default.jpg');

	$table_data_row='<tr>';
	$table_data_row.="<td><input type='checkbox' id='person_$supplier->person_id' value='".$supplier->person_id."'/><label for='person_$supplier->person_id'><span></span></label></td>";
	
	$table_data_row.='<td>'.H($supplier->person_id).'</td>';
	$table_data_row.='<td>'.H($supplier->company_name).'</td>';
	$table_data_row.='<td>'.H($supplier->last_name).'</td>';
	$table_data_row.='<td>'.H($supplier->first_name).'</td>';
	$table_data_row.='<td>'.mailto(H($supplier->email),H($supplier->email)).'</td>';
	$table_data_row.='<td>'.H($supplier->phone_number).'</td>';		
	$table_data_row.='<td>'.anchor($controller_name."/view/$supplier->person_id/2", lang('common_edit'), 'class=" "').'</td>';				
	$table_data_row.="<td><a href='$avatar_url' class='rollover'><img  src='".$avatar_url."' alt='".H($supplier->company_name)."' class='img-polaroid avatar' width='45' /></a></td>";
	$table_data_row.='</tr>';
	return $table_data_row;
}

/*
Gets the html table to manage items.
*/
function get_items_manage_table($items,$controller)
{
	$CI =& get_instance();
	$has_cost_price_permission = $CI->Employee->has_module_action_permission('items','see_cost_price', $CI->Employee->get_logged_in_employee_info()->person_id);
	$table='<table class="table tablesorter table-hover" id="sortable_table">';	


	if ($has_cost_price_permission)
	{
		$headers = array('<input type="checkbox" id="select_all" /><label for="select_all"><span></span></label>', 
		lang('common_item_id'),
		lang('common_item_number_expanded'),
		lang('common_name'),
		lang('common_category'),
		lang('common_size'),
		lang('common_cost_price'),
		lang('common_unit_price'),
		lang('items_quantity'),
		lang('items_total_quantity'),
		lang('common_inventory'),
		lang('common_clone'),
		lang('common_edit'),
		'&nbsp;'
		);
	}
	else 
	{
		$headers = array('<input type="checkbox" id="select_all" /><label for="select_all"><span></span></label>', 
		lang('common_item_id'),
		lang('common_item_number_expanded'),
		lang('common_name'),
		lang('common_category'),
		lang('common_size'),
		lang('common_unit_price'),
		lang('items_quantity'),
		lang('items_total_quantity'),
		lang('common_inventory'),
		lang('common_clone'),
		lang('common_edit'),
		'&nbsp;'
		);
		
	}
		
	$table.='<thead><tr>';
	$count = 0;
	foreach($headers as $header)
	{
		$count++;
		
		if ($count == 1)
		{
			$table.="<th class='leftmost'>$header</th>";
		}
		elseif ($count == count($headers))
		{
			$table.="<th class='rightmost'>$header</th>";
		}
		else
		{
			$table.="<th>$header</th>";		
		}
	}
	$table.='</tr></thead><tbody>';
	$table.=get_items_manage_table_data_rows($items,$controller);
	$table.='</tbody></table>';
	return $table;
}

/*
Gets the html data rows for the items.
*/
function get_items_manage_table_data_rows($items,$controller)
{
	$CI =& get_instance();
	$table_data_rows='';
	
	foreach($items->result() as $item)
	{
		$item->total_quantity = $CI->Item->getTotalInAllLocation($item->item_id);
		$table_data_rows.=get_item_data_row($item,$controller);
	}
	
	if($items->num_rows()==0)
	{
		$table_data_rows.="<tr><td colspan='12'><span class='col-md-12 text-center text-warning' >".lang('items_no_items_to_display')."</span></td></tr>";
	}
	
	return $table_data_rows;
}

function get_item_data_row($item,$controller)
{
	$CI =& get_instance();
	static $has_cost_price_permission;
		
	if (!$has_cost_price_permission)
	{
		$has_cost_price_permission = $CI->Employee->has_module_action_permission('items','see_cost_price', $CI->Employee->get_logged_in_employee_info()->person_id);
	}
	$low_inventory_class = "";
	
	$reorder_level = $item->location_reorder_level ? $item->location_reorder_level : $item->reorder_level;

	if($CI->config->item('highlight_low_inventory_items_in_items_module') && $item->quantity !== NULL && ($item->quantity<=0 || $item->quantity <= $reorder_level))
	{
		$low_inventory_class = "text-danger";
	}
	$controller_name=str_replace(BIZ_PREFIX, '', strtolower(get_class($CI)));

	$avatar_url=$item->image_id ?  site_url('app_files/view/'.$item->image_id) : base_url('assets/assets/images/avatar-default.jpg');

	$table_data_row='<tr>';
	$table_data_row.="<td><input type='checkbox' id='item_$item->item_id' value='".$item->item_id."'/><label for='item_$item->item_id'><span></span></label></td>";
	$table_data_row.='<td>'.$item->item_id.'</td>';
	$table_data_row.='<td>'.H($item->item_number).'</td>';
	$table_data_row.='<td class="not-selectable"><a class="'.$low_inventory_class.'" href="'.site_url('home/view_item_modal').'/'.$item->item_id.'" data-toggle="modal" data-target="#myModal">'.H($item->name).'</a></td>';
	$table_data_row.='<td>'.H($item->category).'</td>';
	$table_data_row.='<td>'.$item->size.'</td>';
	if($has_cost_price_permission)
	{
		$table_data_row.='<td>'.to_currency($item->location_cost_price ? $item->location_cost_price: $item->cost_price, 10).'</td>';
	}
	$table_data_row.='<td>'.to_currency($item->location_unit_price ? $item->location_unit_price : $item->unit_price, 10).'</td>';
	$table_data_row.='<td><span class="'.$low_inventory_class.'">'.to_quantity($item->quantity).'</span></td>';
	$table_data_row.='<td class="qty not-selectable" onclick="ITEM_LIST.clickEventOnQtyCell(this)"><a>'. to_quantity($item->total_quantity) .'</a></td>';
	if (!$item->is_service)
	{
		$table_data_row.='<td class="not-selectable">'.anchor($controller_name."/inventory/$item->item_id/", lang('common_inv'),array('class'=>'','title'=>lang($controller_name.'_count'))).'</td>';//inventory details	
	
	}
	else
	{
		$table_data_row.='<td>&nbsp;</td>';
		
	}

	$table_data_row.='<td class="rightmost not-selectable">'.anchor($controller_name."/clone_item/$item->item_id	", lang('common_clone'),array('class'=>' ','title'=>lang('common_clone'))).'</td>';			
	$table_data_row.='<td class="rightmost not-selectable">'.anchor($controller_name."/view/$item->item_id/2	", lang('common_edit'),array('class'=>' ','title'=>lang($controller_name.'_update'))).'</td>';		
	
	if ($avatar_url)
	{	
		$table_data_row.="<td><a href='$avatar_url' class='rollover'><img src='".$avatar_url."' alt='".H($item->name)."' class='img-polaroid' width='45' /></a></td>";
	}
	
	$table_data_row.='</tr>';
	return $table_data_row;
}


/*
Gets the html table to manage items.
*/
function get_locations_manage_table($locations,$controller)
{
	$CI =& get_instance();
	$table='<table class="table tablesorter table-hover" id="sortable_table">';	

		$headers = array('<input type="checkbox" id="select_all" /><label for="select_all"><span></span></label>', 
		lang('locations_location_id'),
		lang('locations_name'),
		lang('locations_address'),
		lang('locations_phone'),
		lang('locations_email'),
		'&nbsp;'
		);
		

		
	$table.='<thead><tr>';
	$count = 0;
	foreach($headers as $header)
	{
		$count++;
		
		if ($count == 1)
		{
			$table.="<th class='leftmost'>$header</th>";
		}
		elseif ($count == count($headers))
		{
			$table.="<th class='rightmost'>$header</th>";
		}
		else
		{
			$table.="<th>$header</th>";		
		}
	}
	$table.='</tr></thead><tbody>';
	$table.=get_locations_manage_table_data_rows($locations,$controller);
	$table.='</tbody></table>';
	return $table;
}

/*
Gets the html data rows for the items.
*/
function get_locations_manage_table_data_rows($locations,$controller)
{
	$CI =& get_instance();
	$table_data_rows='';
	
	foreach($locations->result() as $location)
	{
		$table_data_rows.=get_location_data_row($location,$controller);
	}
	
	if($locations->num_rows()==0)
	{
		$table_data_rows.="<tr><td colspan='7'><span class='col-md-12 text-center text-warning' >".lang('locations_no_locations_to_display')."</span></td></tr>";
	}
	
	return $table_data_rows;
}

function get_location_data_row($location,$controller)
{
	$CI =& get_instance();
	$controller_name=str_replace(BIZ_PREFIX, '', strtolower(get_class($CI)));

	$table_data_row='<tr>';
	$table_data_row.="<td><input type='checkbox' id='location_$location->location_id' value='".$location->location_id."'/><label for='location_$location->location_id'><span></span></label></td>";
	$table_data_row.='<td>'.$location->location_id.'</td>';
	$table_data_row.='<td>'.H($location->name).'</td>';
	$table_data_row.='<td>'.H($location->address).'</td>';
	$table_data_row.='<td>'.H($location->phone).'</td>';
	$table_data_row.='<td>'.H($location->email).'</td>';
	$table_data_row.='<td class="rightmost">'.anchor($controller_name."/view/$location->location_id/2", lang('common_edit'),array('class'=>' ','title'=>lang($controller_name.'_update'))).'</td>';		
	
	$table_data_row.='</tr>';
	return $table_data_row;
}

/*
Gets the html table to manage giftcards.
*/
function get_giftcards_manage_table( $giftcards, $controller )
{
	$CI =& get_instance();
	
	$table='<table class="tablesorter table table-hover" id="sortable_table">';
	
	$headers = array('<input type="checkbox" id="select_all" /><label for="select_all"><span></span></label>', 
	lang('common_giftcards_giftcard_number'),
	lang('common_giftcards_card_value'),
	lang('common_description'),
	lang('common_customer_name'),
	lang('common_active').'/'.lang('common_inactive'),
	lang('common_clone'),
	'&nbsp;', 
	);
	
	$table.='<thead><tr>';
	$count = 0;
	foreach($headers as $header)
	{
		$count++;
		
		if ($count == 1)
		{
			$table.="<th class='leftmost'>$header</th>";
		}
		elseif ($count == count($headers))
		{
			$table.="<th class='rightmost'>$header</th>";
		}
		else
		{
			$table.="<th>$header</th>";		
		}
	}
	$table.='</tr></thead><tbody>';
	$table.=get_giftcards_manage_table_data_rows( $giftcards, $controller );
	$table.='</tbody></table>';
	return $table;
}

/*
Gets the html data rows for the giftcard.
*/
function get_giftcards_manage_table_data_rows( $giftcards, $controller )
{
	$CI =& get_instance();
	$table_data_rows='';
	
	foreach($giftcards->result() as $giftcard)
	{
		$table_data_rows.=get_giftcard_data_row( $giftcard, $controller );
	}
	
	if($giftcards->num_rows()==0)
	{
		$table_data_rows.="<tr><td  colspan='8'><span class='col-md-12 text-center text-warning' >".lang('giftcards_no_giftcards_to_display')."</span></td></tr>";
	}
	
	return $table_data_rows;
}

function get_giftcard_data_row($giftcard,$controller)
{
	$CI =& get_instance();
	$controller_name=str_replace(BIZ_PREFIX, '', strtolower(get_class($CI)));
	$link = site_url('reports/detailed_'.$controller_name.'/'.$giftcard->customer_id.'/0');
	$cust_info = $CI->Customer->get_info($giftcard->customer_id);
	
	$table_data_row='<tr>';
	$table_data_row.="<td><input type='checkbox' id='giftcard_$giftcard->giftcard_id' value='".$giftcard->giftcard_id."'/><label for='giftcard_$giftcard->giftcard_id'><span></span></label></td>";
	$table_data_row.='<td>'.H($giftcard->giftcard_number).'</td>';
	$table_data_row.='<td>'.to_currency(H($giftcard->value), 10).'</td>';
	$table_data_row.='<td>'.H($giftcard->description).'</td>';
	$table_data_row.='<td><a class="underline" href="'.$link.'">'.H($cust_info->first_name). ' '.H($cust_info->last_name).'</a></td>';
	$table_data_row.='<td>'.($giftcard->inactive ? lang('common_inactive') : lang('common_active')).'</td>';
	$table_data_row.='<td class="rightmost">'.anchor($controller_name."/clone_giftcard/$giftcard->giftcard_id", lang('common_clone'),array('class'=>' ','title'=>lang('common_clone'))).'</td>';			
	$table_data_row.='<td class="rightmost">'.anchor($controller_name."/view/$giftcard->giftcard_id/2	", lang('common_edit'),array('class'=>' ','title'=>lang($controller_name.'_update'))).'</td>';		
	
	$table_data_row.='</tr>';
	return $table_data_row;
}

/*
Gets the html table to manage item kits.
*/
function get_item_kits_manage_table( $item_kits, $controller )
{
	$CI =& get_instance();
	
	$table='<table class="tablesorter table table-hover" id="sortable_table">';
	
	$has_cost_price_permission = $CI->Employee->has_module_action_permission('item_kits','see_cost_price', $CI->Employee->get_logged_in_employee_info()->person_id);
	
	if ($has_cost_price_permission)
	{
		$headers = array('<input type="checkbox" id="select_all" /><label for="select_all"><span></span></label>', 
		lang('item_kits_id'),
		lang('common_item_number_expanded'),
		lang('item_kits_name'),
		lang('item_kits_description'),
		lang('common_cost_price'),
		lang('common_unit_price'),
		lang('common_clone'),
		lang('common_edit'),
		);
	}
	else
	{
		$headers = array('<input type="checkbox" id="select_all" /><label for="select_all"><span></span></label>', 
		lang('item_kits_id'),
		lang('common_item_number_expanded'),
		lang('item_kits_name'),
		lang('item_kits_description'),
		lang('common_unit_price'),
		lang('common_clone'),
		lang('common_edit'),
		'&nbsp;', 
		);
	}
	$table.='<thead><tr>';
	$count = 0;
	foreach($headers as $header)
	{
		$count++;
		
		if ($count == 1)
		{
			$table.="<th class='leftmost'>$header</th>";
		}
		elseif ($count == count($headers))
		{
			$table.="<th class='rightmost'>$header</th>";
		}
		else
		{
			$table.="<th>$header</th>";		
		}
	}
	$table.='</tr></thead><tbody>';
	$table.=get_item_kits_manage_table_data_rows( $item_kits, $controller );
	$table.='</tbody></table>';
	return $table;
}

/*
Gets the html data rows for the item kits.
*/
function get_item_kits_manage_table_data_rows( $item_kits, $controller )
{
	$CI =& get_instance();
	$table_data_rows='';
	
	foreach($item_kits->result() as $item_kit)
	{
		$table_data_rows.=get_item_kit_data_row( $item_kit, $controller );
	}
	
	if($item_kits->num_rows()==0)
	{
		$table_data_rows.="<tr><td colspan='9'><span class='col-md-12 text-center text-warning' >".lang('item_kits_no_item_kits_to_display')."</span></td></tr>";
	}
	
	return $table_data_rows;
}

function get_item_kit_data_row($item_kit,$controller)
{

	$CI =& get_instance();
	$controller_name=str_replace(BIZ_PREFIX, '', strtolower(get_class($CI)));
	
	$has_cost_price_permission = $CI->Employee->has_module_action_permission('item_kits','see_cost_price', $CI->Employee->get_logged_in_employee_info()->person_id);
		
	$table_data_row='<tr>';
	$table_data_row.="<td><input type='checkbox' id='item_kit_$item_kit->item_kit_id' value='".$item_kit->item_kit_id."'/><label for='item_kit_$item_kit->item_kit_id'><span></span></label></td>";
	$table_data_row.='<td width="100px">'.'KIT '.H($item_kit->item_kit_id).'</td>';
	$table_data_row.='<td>'.H($item_kit->item_kit_number).'</td>';	
	$table_data_row.='<td><a href="'.site_url('home/view_item_kit_modal').'/'.$item_kit->item_kit_id.'" data-toggle="modal" data-target="#myModal">'.H($item_kit->name).'</a></td>';
	
	$table_data_row.='<td>'.H($item_kit->description).'</td>';
	if ($has_cost_price_permission)
	{
		$table_data_row.='<td>'.(!is_null($item_kit->cost_price) ? to_currency(($item_kit->location_cost_price ? $item_kit->location_cost_price : $item_kit->cost_price), 10) : '').'</td>';
	}
	
	$table_data_row.='<td>'.(!is_null($item_kit->unit_price) ? to_currency(($item_kit->location_unit_price ? $item_kit->location_unit_price : $item_kit->unit_price), 10) : '').'</td>';
	
	$table_data_row.='<td class="rightmost">'.anchor($controller_name."/clone_item_kit/$item_kit->item_kit_id	", lang('common_clone'),array('class'=>' ','title'=>lang('common_clone'))).'</td>';			
	
	$table_data_row.='<td class="rightmost">'.anchor($controller_name."/view/$item_kit->item_kit_id/2	", lang('common_edit'),array('class'=>' ','title'=>lang($controller_name.'_update'))).'</td>';
	$table_data_row.='</tr>';
	return $table_data_row;
}


function get_expenses_manage_table($expenses,$controller)
{
	$CI =& get_instance();
	$table='<table class="tablesorter table table-hover" id="sortable_table">';

		$headers = array('<input type="checkbox" id="select_all" /><label for="select_all"><span></span></label>', 
		lang('expenses_id'),
		lang('expenses_type'),
		lang('expenses_description'),
		lang('common_category'),
		lang('expenses_date'),
		lang('expenses_amount'),
		lang('common_tax'),
		lang('common_recipient_name'),
		lang('common_approved_by'),
		'&nbsp;'
		);
		
	$table.='<thead><tr>';
	$count = 0;
	foreach($headers as $header)
	{
		$count++;
		
		if ($count == 1)
		{
			$table.="<th class='leftmost'>$header</th>";
		}
		elseif ($count == count($headers))
		{
			$table.="<th class='rightmost'>$header</th>";
		}
		else
		{
			$table.="<th>$header</th>";		
		}
	}
	$table.='</tr></thead><tbody>';
	$table.=get_expenses_manage_table_data_rows($expenses,$controller);
	$table.='</tbody></table>';
	return $table;
}
/*
Gets the html data rows for the items.
*/
function get_expenses_manage_table_data_rows($expenses,$controller)
{
	$CI =& get_instance();
	$table_data_rows='';
	
	foreach($expenses->result() as $expense)
	{
		$table_data_rows.=get_expenses_data_row($expense,$controller);
	}
	
	if($expenses->num_rows()==0)
	{
		$table_data_rows.="<tr><td colspan='11'><span class='col-md-12 text-center text-warning' >".lang('expenses_no_expenses_to_display')."</span></td></tr>";
	}
	
	return $table_data_rows;
}

function get_expenses_data_row($expense,$controller)
{
	$CI =& get_instance();
	$controller_name=str_replace(BIZ_PREFIX, '', strtolower(get_class($CI)));
	$table_data_row='<tr>';
	$table_data_row.="<td><input type='checkbox' id='expenses_$expense->id' value='".$expense->id."'/><label for='expenses_$expense->id'><span></span></label></td>";
	$table_data_row.='<td>'.$expense->id.'</td>';
	$table_data_row.='<td>'.H($expense->expense_type).'</td>';
	$table_data_row.='<td>'.H($expense->expense_description).'</td>';
	$table_data_row.='<td>'.H($expense->category).'</td>';
	$table_data_row.='<td>'.date(get_date_format(), strtotime($expense->expense_date)).'</td>';
	$table_data_row.='<td>'.to_currency($expense->expense_amount).'</td>';
	$table_data_row.='<td>'.to_currency($expense->expense_tax).'</td>';
	$table_data_row.='<td>'.H($expense->employee_recv).'</td>';
	$table_data_row.='<td>'.H($expense->employee_appr).'</td>';
	$table_data_row.='<td class="rightmost">'.anchor($controller_name."/view/$expense->id/2	", lang('common_edit'),array('class'=>'','title'=>lang($controller_name.'_update'))).'</td>';		
	
	$table_data_row.='</tr>';
	return $table_data_row;
}

/*
Gets the html table to manage groups.
*/
function get_groups_manage_table( $groups, $controller )
{
    $table='<table class="tablesorter table table-hover" id="sortable_table">';

    $headers = array('<input type="checkbox" id="select_all" /><label for="select_all"><span></span></label>',
        'ID',
        lang('common_name'),
        lang('common_description'),
        '&nbsp;',
    );

    $table.='<thead><tr>';
    $count = 0;
    foreach($headers as $header)
    {
        $count++;

        if ($count == 1)
        {
            $table.="<th class='leftmost'>$header</th>";
        }
        elseif ($count == count($headers))
        {
            $table.="<th class='rightmost'>$header</th>";
        }
        else
        {
            $table.="<th>$header</th>";
        }
    }
    $table.='</tr></thead><tbody>';
    $table.=get_groups_manage_table_data_rows( $groups, $controller );
    $table.='</tbody></table>';
    return $table;
}

/*
Gets the html data rows for the group.
*/
function get_groups_manage_table_data_rows( $groups, $controller )
{
    $table_data_rows = '';

    foreach($groups->result() as $group)
    {
        $table_data_rows .= get_group_data_row( $group, $controller );
    }

    if($groups->num_rows() == 0)
    {
        $table_data_rows.="<tr><td colspan='5'><span class='col-md-12 text-center text-warning' >".lang('groups_no_groups_to_display')."</span></td></tr>";
    }

    return $table_data_rows;
}

function get_group_data_row($group, $controller = null)
{
    $CI =& get_instance();
    $controller_name=str_replace(BIZ_PREFIX, '', strtolower(get_class($CI)));

    $table_data_row='<tr>';
    $table_data_row.="<td width='50px'><input type='checkbox' id='group_$group->group_id' value='".$group->group_id."'/><label for='group_$group->group_id'><span></span></label></td>";
    $table_data_row.='<td width="50px">'.H($group->group_id).'</td>';
    $table_data_row.='<td width="150px">'.H($group->name).'</td>';
    $table_data_row.='<td>'.H($group->description).'</td>';
    $table_data_row.='<td class="rightmost">'.anchor($controller_name."/view/$group->group_id/2	", lang('common_edit'),array('class'=>' ','title'=>lang($controller_name.'_update'))).'</td>';

    $table_data_row.='</tr>';
    return $table_data_row;
}

/*
Gets the html table to manage departments.
*/
function get_departments_manage_table( $departments, $controller )
{
    $table='<table class="tablesorter table table-hover" id="sortable_table">';

    $headers = array('<input type="checkbox" id="select_all" /><label for="select_all"><span></span></label>',
        'ID',
        lang('common_name'),
        lang('common_employee'),
        lang('common_description'),
        '&nbsp;',
    );

    $table.='<thead><tr>';
    $count = 0;
    foreach($headers as $header)
    {
        $count++;

        if ($count == 1)
        {
            $table.="<th class='leftmost'>$header</th>";
        }
        elseif ($count == count($headers))
        {
            $table.="<th class='rightmost'>$header</th>";
        }
        else
        {
            $table.="<th>$header</th>";
        }
    }
    $table.='</tr></thead><tbody>';
    $table.=get_departments_manage_table_data_rows( $departments, $controller );
    $table.='</tbody></table>';
    return $table;
}

/*
Gets the html data rows for the department.
*/
function get_departments_manage_table_data_rows( $departments, $controller )
{
    $table_data_rows = '';

    foreach($departments->result() as $department)
    {
        foreach ($departments->employees as $key => $department_employees) {
            if ($department->department_id == $key) {
                $department->employees = $department_employees;
            }
        }
        $table_data_rows .= get_department_data_row( $department, $controller );
    }

    if($departments->num_rows() == 0)
    {
        $table_data_rows.="<tr><td colspan='5'><span class='col-md-12 text-center text-warning' >".lang('departments_no_departments_to_display')."</span></td></tr>";
    }

    return $table_data_rows;
}

function get_department_data_row($department, $controller = null)
{
    $CI =& get_instance();

    if (!class_exists('Department')) {
        $CI->load->model('Department');
    }

    $controller_name=str_replace(BIZ_PREFIX, '', strtolower(get_class($CI)));

    $table_data_row='<tr>';
    $table_data_row.="<td width='50px'><input type='checkbox' id='department_$department->department_id' value='".$department->department_id."'/><label for='department_$department->department_id'><span></span></label></td>";
    $table_data_row.='<td width="50px">'.H($department->department_id).'</td>';
    $table_data_row.='<td width="280px">' . $CI->Department->get_level_line($department, '&nbsp;', false, true) . ' ' . H($department->name) . '<span type="button" class="badge bg-primary tip-left ml-10" data-toggle="dropdown" aria-expanded="false">'.count($department->employees).'</span></td>';
    $table_data_row.='<td width="280px">'.get_department_data_row_employees($department->employees).'</td>';
    $table_data_row.='<td>'.H($department->description).'</td>';
    $table_data_row.='<td class="rightmost">'.anchor($controller_name."/view/$department->department_id/2	", lang('common_edit'),array('class'=>' ','title'=>lang($controller_name.'_update'))).'</td>';

    $table_data_row.='</tr>';
    return $table_data_row;
}

function get_department_data_row_employees($employees) {
    $data_row = '<ul class="employees">';
    if (!empty($employees)) {
        foreach ($employees as $employee) {
            $data_row .= '<li><a href="/employees/view/'.$employee->person_id.'/2">' . $employee->first_name . ' ' . $employee->last_name . '</a> - '.$employee->group_name.'</li>';
        }
    }
    $data_row .= '</ul>';
    return $data_row;
}

function get_sms_manage_table($sms, $controller) 
{
	$CI = & get_instance();
		
	$table = '<table class="tablesorter table table-hover" id="sortable_table">';
	$headers = array(
			'<input type="checkbox" id="select_all" /><label for="select_all"><span></span></label>',
			lang('customers_sms_code'),
			lang('customers_sms_title'),
			lang('customers_sms_description'),
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
	$table.=get_sms_manage_table_data_rows($sms, $controller);
	$table.='</tbody></table>';
	return $table;
}

function get_sms_manage_table_data_rows($sms, $controller) 
{
	$CI = & get_instance();
	$table_data_rows = '';

	foreach ($sms->result() as $s) {
		$table_data_rows.=get_sms_data_row($s, $controller);
	}

	if ($sms->num_rows() == 0) {
		$table_data_rows.="<tr><td colspan='11'><span class='col-md-12 text-center text-warning' >".lang('customers_sms_none')."</span></td></tr>";
	}
	return $table_data_rows;
}

function get_sms_data_row($sms, $controller) 
{
	$CI = & get_instance();
	$controller_name = str_replace(BIZ_PREFIX, '', strtolower(get_class($CI)));

	$table_data_row = '<tr>';
	$table_data_row.="<td><input type='checkbox' id='sms_$sms->id' value='" . $sms->id . "'/><label for='sms_$sms->id'><span></span></label></td>";
	$table_data_row.="<td>" . $sms->id . "</td>";
	$table_data_row.='<td>' . H($sms->title) . '</a></td>';
	$table_data_row.='<td>' . H($sms->message) . '</td>';
	$table_data_row.='<td class="rightmost">' . anchor($controller_name . "/view_sms/$sms->id/2", lang('common_edit'), array('title' => lang('customers_sms_edit'), 'class' => '')) . '</td>';
	$table_data_row.='</tr>';
	return $table_data_row;
}

function get_quotes_contract_manage_table($quotes_contract, $controller) {
	$CI = & get_instance();
	$table = '<table class="tablesorter" id="sortable_table">';
	$headers = array(
			'<input type="checkbox" id="select_all" /><label for="select_all"><span></span></label>',
			lang('customers_quotes_contract_table_code'),
			lang('customers_quotes_contract_table_title'),
			lang('customers_quotes_contract_table_type'),
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
	$table.=get_quotes_contract_manage_table_data_rows($quotes_contract, $controller);
	$table.='</tbody></table>';
	return $table;
}

function get_quotes_contract_manage_table_data_rows($quotes_contract, $controller) {
	$CI = & get_instance();
	$table_data_rows = '';

	foreach ($quotes_contract->result() as $val) {
		$table_data_rows.=get_quotes_contract_data_row($val, $controller);
	}

	if ($quotes_contract->num_rows() == 0) {
		$table_data_rows.="<tr><td colspan='5'><span class='col-md-12 text-center text-warning' >" . lang('customers_quotes_contract_none_data') . "</div></tr></tr>";
	}

	return $table_data_rows;
}

function get_quotes_contract_data_row($quotes_contract, $controller) {
	$CI = & get_instance();
	$controller_name=str_replace(BIZ_PREFIX, '', strtolower(get_class($CI)));
	$table_data_row = '<tr>';
	$table_data_row .= "<td width='5%'><input type='checkbox' id='person_$quotes_contract->id_quotes_contract' value='" . $quotes_contract->id_quotes_contract . "' data-type='".$quotes_contract->cat_quotes_contract."'/><label for='quotes_contract_$quotes_contract->id_quotes_contract'><span></span></label></td>";
	$table_data_row .= "<td width='15%'>$quotes_contract->id_quotes_contract</td>";
	$table_data_row .= "<td width='41%'>$quotes_contract->title_quotes_contract</td>";
	$table_data_row .= "<td width='35%'>" . ($quotes_contract->cat_quotes_contract == 1 ? lang('customers_quotes_contract_type_contract') : lang('customers_quotes_contract_type_quotes')) . "</td>";
	$table_data_row .= '<td width="5%" class="rightmost">' . anchor($controller_name . "/quotes_contract_view/$quotes_contract->id_quotes_contract/2", lang('common_edit'), array('title' => lang('customers_quotes_contract_update'), 'class' => '')) . '</td>';
	$table_data_row.='</tr>';
	return $table_data_row;
}

function get_mail_manage_table($mail, $controller) {
	$CI = & get_instance();
	$table = '<table class="tablesorter table table-hover" id="sortable_table">';

	$headers = array('<input type="checkbox" id="select_all" /><label for="select_all"><span></span></label>',
			'Tiêu đề',
			'Nội dung',
			'&nbsp');
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
    $table.=get_mail_manage_table_data_rows($mail, $controller);
    $table.='</tbody></table>';
    return $table;
}

/*
  Gets the html data rows for the mail.
 */

function get_mail_manage_table_data_rows($mail, $controller) {
    $CI = & get_instance();
    $table_data_rows = '';

    foreach ($mail->result() as $m) {
        $table_data_rows.=get_mail_data_row($m, $controller);
    }

    if ($mail->num_rows() == 0) {
        $table_data_rows.="<tr><td colspan='4'><div class='col-md-12 text-center text-warning'>" . lang('common_no_mail_to_display') . "</div></tr></tr>";
    }

    return $table_data_rows;
}

function get_mail_data_row($mail, $controller) {
    $CI = & get_instance();
    $controller_name = str_replace(BIZ_PREFIX, '', strtolower(get_class($CI)));

    $table_data_row = '<tr>';
    $table_data_row.="<td><input type='checkbox' id='mail_$mail->mail_id' value='" . $mail->mail_id . "'/><label for='sms_$mail->mail_id'><span></span></label></td>";
    $table_data_row.='<td>' . $mail->mail_title . '</a></td>';
    $table_data_row.='<td>' . substr($mail->mail_content, 0, 20) . '...</td>';
    $table_data_row.='<td class="rightmost">' . anchor($controller_name . "/view_mail/$mail->mail_id", lang('common_edit'), array('title' => ' Sửa mail')) . '</td>';
    $table_data_row.='</tr>';

    return $table_data_row;
}

function get_mail_manage_table_temp($mail, $controller) {
	$CI = & get_instance();
	$table = '<table class="tablesorter table table-hover" id="sortable_table">';

	$headers = array(
			'Tên KH',
			'Email',
	        '&nbsp');
	
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
    $table.=get_mail_manage_table_data_rows_temp($mail, $controller);
    $table.='</tbody></table>';
    return $table;
}

/*
  Gets the html data rows for the mail.
 */

function get_mail_manage_table_data_rows_temp($mail, $controller) {
    $CI = & get_instance();
    $table_data_rows = '';

    foreach ($mail as $key => $value ) {
        $table_data_rows.=get_mail_data_row_temp($key, $value, $controller);
    }

    if (count($mail) == 0) {
        $table_data_rows.="<tr><td colspan='4'><div class='col-md-12 text-center text-warning'>" . lang('common_no_mail_to_display') . "</div></tr></tr>";
    }

    return $table_data_rows;
}

function get_mail_data_row_temp($customer_id, $customer_info, $controller) {
    $CI = & get_instance();
	$controller_name = str_replace(BIZ_PREFIX, '', strtolower(get_class($CI)));

	$table_data_row = '<tr>';
	$table_data_row.='<td>' . H($customer_info['name']) . '</a></td>';
	$table_data_row.='<td>' . H($customer_info['email']) . '</td>';
	$table_data_row.='<td class="rightmost">' . anchor($controller_name . "#", lang('common_delete'), array('title' => $customer_id, 'class' => 'delete_email_temp btn btn-primary btn-lg','data-id'=>$customer_id)) . '</td>';
	$table_data_row.='</tr>';
	return $table_data_row;
}

?>