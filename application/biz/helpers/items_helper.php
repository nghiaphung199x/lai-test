<?php
function getItemConvertedQtyAllLocation($itemId) {
	$CI =& get_instance();
	$itemInfo = $CI->Item->get_info($itemId);
	$allQty = $CI->Item_location->getQtyOfEachLocation($itemId);
	$measures = $CI->ItemMeasures->getMeasuresByItemId($itemId);
	usort($measures, function($a, $b){
		return $b['qty_converted'] - $a['qty_converted'];
	});
	
	foreach ($allQty as &$qty) {
		$convertedQtyToString = '';
		$convertedQty = getItemConvertedQty($qty['quantity'], $itemInfo->measure_id, $measures);
		foreach ($convertedQty as $convertedMeasureId => $convertedMeasureQty)
		{
			$measure = $CI->Measure->getInfo($convertedMeasureId);
			$convertedQtyToString .= to_quantity($convertedMeasureQty) . ' ' . $measure->name . ', ';
		}
		$qty['quantity_converted'] = rtrim(trim($convertedQtyToString), ",");
	}
	return $allQty;
}

function qtyToString($itemId, $qty) {
	$CI =& get_instance();
	$measures = $CI->ItemMeasures->getMeasuresByItemId($itemId);
	$itemInfo = $CI->Item->get_info($itemId);
	
	$measure = $CI->Measure->getInfo($itemInfo->measure_id);
	$convertedQtyToString = to_quantity($qty) . ' ' . $measure->name . ', ';
	
	$convertedQty = getItemConvertedQty($qty['quantity'], $itemInfo->measure_id, $measures);
	
	if( !empty($convertedQty) ) {
		foreach ($convertedQty as $convertedMeasureId => $convertedMeasureQty)
		{
			$measure = $CI->Measure->getInfo($convertedMeasureId);
			if ($measure && $convertedMeasureId != $itemInfo->measure_id) {
				$convertedQtyToString .= to_quantity($convertedMeasureQty) . ' ' . $measure->name . ', ';
			}
		}
	}
	
	return rtrim(trim($convertedQtyToString), ",");
}


function getItemConvertedQty($qty, $baseMeasureId, $measures) {
	$result = array();
	foreach ($measures as $measure) {
		if($qty >= $measure['qty_converted']) {
			$result[$measure['measure_converted_id']] = (int)($qty / $measure['qty_converted']);
			$qty = (int)$qty % $measure['qty_converted'];
		}
	}
	if( $qty ) {
		$result[$baseMeasureId] = $qty;
	}
	return $result;
}

function get_items_barcode_data($item_ids)
{
	$CI =& get_instance();	
	
	$hide_prices = $CI->config->item('hide_price_on_barcodes');
	
	$result = array();

	$item_ids = explode('~', $item_ids);
	foreach ($item_ids as $item_id)
	{
		$barcode_number = number_pad($item_id,10);		
		$item_info = $CI->Item->get_info($item_id);
		
		if ($id_to_show_on_barcode = $CI->config->item('id_to_show_on_barcode'))
		{
			if ($id_to_show_on_barcode == 'id')
			{
				$barcode_number = number_pad($item_id,10);
			}
			elseif($id_to_show_on_barcode == 'number')
			{
				$barcode_number = $item_info->item_number;
			}
			elseif($id_to_show_on_barcode == 'product_id')
			{
				$barcode_number = $item_info->product_id;
			}
		}
		
		$item_location_info = $CI->Item_location->get_info($item_id);
		
		$today =  strtotime(date('Y-m-d'));
		$is_item_location_promo = ($item_location_info->start_date !== NULL && $item_location_info->end_date !== NULL) && (strtotime($item_location_info->start_date) <= $today && strtotime($item_location_info->end_date) >= $today);
		$is_item_promo = ($item_info->start_date !== NULL && $item_info->end_date !== NULL) && (strtotime($item_info->start_date) <= $today && strtotime($item_info->end_date) >= $today);
		
		$regular_item_price = $item_location_info->unit_price ? $item_location_info->unit_price : $item_info->unit_price;
		
		
		if ($is_item_location_promo)
		{
			$item_price = $item_location_info->promo_price;
		}
		elseif ($is_item_promo)
		{
			$item_price = $item_info->promo_price;
		}
		else
		{
			$item_price = $item_location_info->unit_price ? $item_location_info->unit_price : $item_info->unit_price;
		}		
		
		if($CI->config->item('barcode_price_include_tax'))
		{
			if($item_info->tax_included)
			{
				$result[] = array('name' => !$hide_prices ? (($is_item_location_promo || $is_item_promo ? '<span style="text-decoration: line-through;font-size:12px;">'.to_currency($regular_item_price).'</span> ' : ' ').'<span style="font-size:12px;">'.to_currency($item_price).'</span>: '.$item_info->name.($item_info->size ? ' ('.$item_info->size.')' : '')) : $item_info->name.($item_info->size ? ' ('.$item_info->size.')' : '') , 'id'=> $barcode_number);
			}
			else
			{				
				$result[] = array('name' => !$hide_prices ? (($is_item_location_promo || $is_item_promo ? '<span style="text-decoration: line-through;font-size:12px;">'.to_currency(get_price_for_item_including_taxes($item_id,$regular_item_price)).'</span> ' : ' ').'<span style="font-size:12px;">'.to_currency(get_price_for_item_including_taxes($item_id,$item_price)).'</span>: '.$item_info->name.($item_info->size ? ' ('.$item_info->size.')' : '')) : $item_info->name.($item_info->size ? ' ('.$item_info->size.')' : ''), 'id'=> $barcode_number);
	  	 	}
	  }
	  else
	  {
		if ($item_info->tax_included)
		{
		    $result[] = array('name' => !$hide_prices ? (($is_item_location_promo || $is_item_promo ? '<span style="text-decoration: line-through;font-size:12px;">'.to_currency(get_price_for_item_excluding_taxes($item_id, $regular_item_price)).'</span> ' : ' ').'<span style="font-size:12px;">'.to_currency(get_price_for_item_excluding_taxes($item_id, $item_price)).'</span>: '.$item_info->name.($item_info->size ? ' ('.$item_info->size.')' : '')) : $item_info->name.($item_info->size ? ' ('.$item_info->size.')' : ''), 'id'=> $barcode_number);
		}
		else
		{
			$result[] = array('name' => !$hide_prices ? (($is_item_location_promo || $is_item_promo ? '<span style="text-decoration: line-through;font-size:12px;">'.to_currency($regular_item_price).'</span> ' : ' ').'<span style="font-size:12px;">'.to_currency($item_price).'</span>: '.$item_info->name.($item_info->size ? ' ('.$item_info->size.')' : '')) : $item_info->name.($item_info->size ? ' ('.$item_info->size.')' : ''), 'id'=> $barcode_number);
	  	}
	  }
	}
	return $result;
}

function get_price_for_item_excluding_taxes($item_id_or_line, $item_price_including_tax, $sale_id = FALSE, $receiving_id = FALSE)
{
	$return = FALSE;
	$CI =& get_instance();
	
	if ($sale_id !== FALSE)
	{
		$tax_info = $CI->Sale->get_sale_items_taxes($sale_id, $item_id_or_line);
	}	
	elseif($receiving_id !== FALSE)
	{
		$tax_info = $CI->Receiving->get_receiving_items_taxes($receiving_id, $item_id_or_line);
	}
	else
	{
		$tax_info = $CI->Item_taxes_finder->get_info($item_id_or_line);
	}
	
	if (count($tax_info) == 2 && $tax_info[1]['cumulative'] == 1)
	{
		$return = $item_price_including_tax/(1+($tax_info[0]['percent'] /100) + ($tax_info[1]['percent'] /100) + (($tax_info[0]['percent'] /100) * (($tax_info[1]['percent'] /100))));
	}
	else //0 or more taxes NOT cumulative
	{
		$total_tax_percent = 0;
		
		foreach($tax_info as $tax)
		{
			$total_tax_percent+=$tax['percent'];
		}
		
		$return = $item_price_including_tax/(1+($total_tax_percent /100));
	}
	
	if ($return !== FALSE)
	{
		return to_currency_no_money($return, 10);
	}
	
	return FALSE;
}

function get_price_for_item_including_taxes($item_id_or_line, $item_price_excluding_tax, $sale_id = FALSE, $receiving_id = FALSE)
{
	$return = FALSE;
	$CI =& get_instance();
	if ($sale_id !== FALSE)
	{
		$tax_info = $CI->Sale->get_sale_items_taxes($sale_id,$item_id_or_line);
	}	
	elseif($receiving_id !== FALSE)
	{
		$tax_info = $CI->Receiving->get_receiving_items_taxes($receiving_id, $item_id_or_line);
	}
	else
	{
		$tax_info = $CI->Item_taxes_finder->get_info($item_id_or_line);
	}
	
	if (count($tax_info) == 2 && $tax_info[1]['cumulative'] == 1)
	{
		$first_tax = ($item_price_excluding_tax*($tax_info[0]['percent']/100));
		$second_tax = ($item_price_excluding_tax + $first_tax) *($tax_info[1]['percent']/100);
		$return = $item_price_excluding_tax + $first_tax + $second_tax;
	}	
	else //0 or more taxes NOT cumulative
	{
		$total_tax_percent = 0;
		
		foreach($tax_info as $tax)
		{
			$total_tax_percent+=$tax['percent'];
		}
		
		$return = $item_price_excluding_tax*(1+($total_tax_percent /100));
	}

	
	if ($return !== FALSE)
	{
		return to_currency_no_money($return, 10);
	}
	
	return FALSE;
}

function get_commission_for_item($item_id, $price, $cost, $quantity,$discount)
{
	$CI =& get_instance();
	$CI->load->library('sale_lib');

	$employee_id=$CI->sale_lib->get_sold_by_employee_id();
	$sales_person_info = $CI->Employee->get_info($employee_id);
	$employee_id=$CI->Employee->get_logged_in_employee_info()->person_id;
	$logged_in_employee_info = $CI->Employee->get_info($employee_id);
	
	$item_info = $CI->Item->get_info($item_id);
	
	if ($item_info->commission_fixed !== NULL)
	{
		return $quantity*$item_info->commission_fixed;
	}
	elseif($item_info->commission_percent !== NULL)
	{
		$commission_percent_type = $item_info->commission_percent_type == 'profit' ? 'profit' : 'selling_price';
		
		if ($commission_percent_type == 'selling_price')
		{
			return to_currency_no_money(($price*$quantity-$price*$quantity*$discount/100)*($item_info->commission_percent/100));			
		}
		else //Profit
		{
			return to_currency_no_money((($price*$quantity-$price*$quantity*$discount/100) - ($cost*$quantity)) * ($item_info->commission_percent/100));				
		}
	}
	elseif($CI->config->item('select_sales_person_during_sale'))
	{
		if($sales_person_info->commission_percent > 0)
		{
			$commission_percent_type = $sales_person_info->commission_percent_type == 'profit' ? 'profit' : 'selling_price';
			
			if ($commission_percent_type == 'selling_price')
			{
				return to_currency_no_money(($price*$quantity-$price*$quantity*$discount/100)*((float)($sales_person_info->commission_percent)/100));
			}
			else
			{
				return to_currency_no_money((($price*$quantity-$price*$quantity*$discount/100) - ($cost*$quantity)) * ($sales_person_info->commission_percent/100));				
			}
		}
		
		$commission_percent_type = $CI->config->item('commission_percent_type') == 'profit' ? 'profit' : 'selling_price';
		
		if ($commission_percent_type == 'profit')
		{
			return to_currency_no_money((($price*$quantity-$price*$quantity*$discount/100) - ($cost*$quantity)) * ((float)($CI->config->item('commission_default_rate'))/100));				
		}
		else
		{
			return to_currency_no_money(($price*$quantity-$price*$quantity*$discount/100)*((float)($CI->config->item('commission_default_rate'))/100));
		}
		
	}
	elseif($logged_in_employee_info->commission_percent > 0)
	{
		$commission_percent_type = $logged_in_employee_info->commission_percent_type == 'profit' ? 'profit' : 'selling_price';
		
		if ($commission_percent_type == 'selling_price')
		{
			return to_currency_no_money(($price*$quantity-$price*$quantity*$discount/100)*((float)($logged_in_employee_info->commission_percent)/100));
		}
		else
		{
			return to_currency_no_money((($price*$quantity-$price*$quantity*$discount/100) - ($cost*$quantity)) * ($logged_in_employee_info->commission_percent/100));				
		}
	}
	else
	{
		$commission_percent_type = $CI->config->item('commission_percent_type') == 'profit' ? 'profit' : 'selling_price';
		
		if ($commission_percent_type == 'profit')
		{
			return to_currency_no_money((($price*$quantity-$price*$quantity*$discount/100) - ($cost*$quantity)) * ((float)($CI->config->item('commission_default_rate'))/100));				
		}
		else
		{
			return to_currency_no_money(($price*$quantity-$price*$quantity*$discount/100)*((float)($CI->config->item('commission_default_rate'))/100));
		}
	}
}

function cache_item_and_item_kit_cart_info($cart)
{
	$CI =& get_instance();
	$item_ids = array();
	$item_kit_ids = array();
	
	foreach($cart as $cart_item)
	{
		if (isset($cart_item['item_id']))
		{
			$item_ids[] = $cart_item['item_id'];
		}
		elseif(isset($cart_item['item_kit_id']))
		{
			$item_kit_ids[] = $cart_item['item_kit_id'];			
		}
	}
	
	$CI->Item->get_info($item_ids);
	$CI->Item_kit->get_info($item_kit_ids);

	$CI->Item_location->get_info($item_ids, false, true);
	$CI->Item_kit_location->get_info($item_kit_ids, false, true);
	
}


	function replace_character($string){
		$search = array(
				'#(à|á|ạ|ả|ã|â|ầ|ấ|ậ|ẩ|ẫ|ă|ằ|ắ|ặ|ẳ|ẵ)#',
				'#(è|é|ẹ|ẻ|ẽ|ê|ề|ế|ệ|ể|ễ)#',
				'#(ì|í|ị|ỉ|ĩ)#',
				'#(ò|ó|ọ|ỏ|õ|ô|ồ|ố|ộ|ổ|ỗ|ơ|ờ|ớ|ợ|ở|ỡ)#',
				'#(ù|ú|ụ|ủ|ũ|ư|ừ|ứ|ự|ử|ữ)#',
				'#(ỳ|ý|ỵ|ỷ|ỹ)#',
				'#(đ)#',
				'#(À|Á|Ạ|Ả|Ã|Â|Ầ|Ấ|Ậ|Ẩ|Ẫ|Ă|Ằ|Ắ|Ặ|Ẳ|Ẵ)#',
				'#(È|É|Ẹ|Ẻ|Ẽ|Ê|Ề|Ế|Ệ|Ể|Ễ)#',
				'#(Ì|Í|Ị|Ỉ|Ĩ)#',
				'#(Ò|Ó|Ọ|Ỏ|Õ|Ô|Ồ|Ố|Ộ|Ổ|Ỗ|Ơ|Ờ|Ớ|Ợ|Ở|Ỡ)#',
				'#(Ù|Ú|Ụ|Ủ|Ũ|Ư|Ừ|Ứ|Ự|Ử|Ữ)#',
				'#(Ỳ|Ý|Ỵ|Ỷ|Ỹ)#',
				'#(Đ)#'
		);
		$repalce = array(
				'a',
				'e',
				'i',
				'o',
				'u',
				'y',
				'd',
				'A',
				'E',
				'I',
				'O',
				'U',
				'Y',
				'D'
		);
		$string = preg_replace($search, $repalce, $string);
		return $string;
	}
?>