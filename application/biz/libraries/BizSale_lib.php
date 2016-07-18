<?php
require_once (APPPATH.'/libraries/Sale_lib.php');

class BizSale_lib extends Sale_lib
{
	function clear_all()
	{
		$this->clear_mode();
		$this->empty_cart();
		$this->clear_comment();
		$this->clear_show_comment_on_receipt();
		$this->clear_change_sale_date();
		$this->clear_change_sale_date_enable();
		$this->clear_email_receipt();
		$this->empty_payments();
		$this->delete_customer(false);
		$this->delete_suspended_sale_id();
		$this->delete_change_sale_id();
		$this->delete_partial_transactions();
		$this->clear_save_credit_card_info();
		$this->clear_use_saved_cc_info();
		$this->clear_prompt_for_card();
		$this->clear_selected_tier_id();
		$this->clear_deleted_taxes();
		$this->clear_cc_info();
		$this->clear_sold_by_employee_id();
		$this->clear_selected_payment();
		$this->clear_invoice_no();
		$this->clear_redeem();
		$this->clear_deliverer();
		$this->clear_delivery_date();
		$this->clear_supporter();
	}
	
	public function clear_delivery_date()
	{
		$this->CI->session->unset_userdata('sale_delivery_date');
	}
	
	public function set_delivery_date($delivery_date)
	{
		$this->CI->session->set_userdata('sale_delivery_date', $delivery_date);
	}
	
	public function get_delivery_date()
	{
		return $this->CI->session->userdata('sale_delivery_date');
	}
	
	public function clear_deliverer()
	{
		$this->CI->session->unset_userdata('sale_deliverer_id');
	}

	public function clear_supporter()
	{
		$this->CI->session->unset_userdata('sale_supporter_id');
	}
	
	public function set_deliverer($deliverer_id)
	{
		$this->CI->session->set_userdata('sale_deliverer_id', $deliverer_id);
	}
	
	public function get_deliverer()
	{
		return $this->CI->session->userdata('sale_deliverer_id');
	}
	
	public function set_supporter($supporter_id)
	{
		$this->CI->session->set_userdata('sale_supporter_id', $supporter_id);
	}
	
	public function get_supporter()
	{
		return $this->CI->session->userdata('sale_supporter_id');
	}
	
	function copy_entire_sale($sale_id, $is_receipt = false)
	{
		$this->empty_cart();
		$this->delete_customer(false);
		$sale_taxes = $this->get_taxes($sale_id);
	
		foreach($this->CI->Sale->get_sale_items($sale_id)->result() as $row)
		{
			$item_info = $this->CI->Item->get_info($row->item_id);
			$price_to_use = $row->item_unit_price;
				
			//If we have tax included, but we don't have any taxes for sale, pretend that we do have taxes so the right price shows up
			if ($item_info->tax_included && empty($sale_taxes) && !$is_receipt)
			{
				$this->CI->load->helper('items');
	
				$price_to_use = get_price_for_item_including_taxes($row->item_id, $row->item_unit_price);
			}
			elseif($item_info->tax_included)
			{
				$this->CI->load->helper('items');
	
				$price_to_use = get_price_for_item_including_taxes($row->line, $row->item_unit_price,$sale_id);
			}
			$this->add_item(
				$row->item_id,
				$row->quantity_purchased,
				$row->discount_percent,
				$price_to_use,
				$row->item_cost_price, 
				$row->description,
				$row->serialnumber, 
				TRUE, 
				$row->line, 
				FALSE, $sale_id);
		}
	
		foreach($this->CI->Sale->get_sale_item_kits($sale_id)->result() as $row)
		{
			$item_kit_info = $this->CI->Item_kit->get_info($row->item_kit_id);
			$price_to_use = $row->item_kit_unit_price;
	
			//If we have tax included, but we don't have any taxes for sale, pretend that we do have taxes so the right price shows up
			if ($item_kit_info->tax_included && empty($sale_taxes) && !$is_receipt)
			{
				$this->CI->load->helper('item_kits');
				$price_to_use = get_price_for_item_kit_including_taxes($row->item_kit_id, $row->item_kit_unit_price);
			}
			elseif ($item_kit_info->tax_included)
			{
				$this->CI->load->helper('item_kits');
				$price_to_use = get_price_for_item_kit_including_taxes($row->line, $row->item_kit_unit_price,$sale_id);
			}
	
			$this->add_item_kit('KIT '.$row->item_kit_id,$row->quantity_purchased,$row->discount_percent,$price_to_use,$row->item_kit_cost_price,$row->description, TRUE, $row->line, FALSE);
		}
		foreach($this->CI->Sale->get_sale_payments($sale_id)->result() as $row)
		{
			$this->add_payment($row->payment_type,$row->payment_amount, $row->payment_date, $row->truncated_card, $row->card_issuer, $row->auth_code, $row->ref_no, $row->cc_token, $row->acq_ref_data, $row->process_data, $row->entry_method, $row->aid, $row->tvr, $row->iad, $row->tsi, $row->arc, $row->cvm, $row->tran_type, $row->application_label);
				
		}
		$this->update_register_cart_data();
	
		$customer_info = $this->CI->Sale->get_customer($sale_id);
		$this->set_customer($customer_info->person_id, false);
	
		$this->set_comment($this->CI->Sale->get_comment($sale_id));
		$this->set_comment_on_receipt($this->CI->Sale->get_comment_on_receipt($sale_id));
	
		$this->set_sold_by_employee_id($this->CI->Sale->get_sold_by_employee_id($sale_id));
		$this->set_deleted_taxes($this->CI->Sale->get_deleted_taxes($sale_id));
		
		$saleInfo = $this->CI->Sale->getInfo($sale_id);
		
		$this->set_deliverer($saleInfo['deliverer']);
		$this->set_delivery_date($saleInfo['delivery_date']);
	}
	
	function edit_item($line,$description = NULL,$serialnumber = NULL,$quantity = NULL,$discount = NULL,$price = NULL, $cost_price = NULL, $measureId = NULL)
	{
		$items = $this->get_cart();
		if(isset($items[$line]))
		{
			if ($description !== NULL ) {
				$items[$line]['description'] = $description;
			}
			if ($serialnumber !== NULL ) {
				$items[$line]['serialnumber'] = $serialnumber;
			}
			if ($quantity !== NULL ) {
				$items[$line]['quantity'] = $quantity;
			}
			if ($discount !== NULL ) {
				$items[$line]['discount'] = $discount;
			}
			if ($price !== NULL ) {
				$items[$line]['price'] = $price;
			}
			if ($cost_price !== NULL ) {
				$items[$line]['cost_price'] = $cost_price;
			}
			if ($measureId /* && ($this->get_mode() == 'receive' || $this->get_mode() == 'purchase_order') */) {
				$items[$line]['measure_id'] = (int) $measureId;
				$measure = $this->CI->Measure->getInfo((int) $measureId);
				$items[$line]['measure'] = $measure->name;
				$itemObj = $this->CI->Item->get_info($items[$line]['item_id']);
				if($measureId != $itemObj->measure_id) {
					$items[$line]['price'] = $this->getPriceByMeasureConverted($items[$line]['item_id'], (int) $measureId);
				} else {
					$items[$line]['price'] = $itemObj->unit_price;
				}
			}
			
			$this->set_cart($items);
				
			return true;
		}
	
		return false;
	}
	
	protected function getPriceByMeasureConverted($itemId = 0, $measureConvertedId = 0){
		$itemObj = $this->CI->Item->get_info($itemId);
		$convertedValue = $this->CI->ItemMeasures->getConvertedValue($itemId, $measureConvertedId);
		return $itemObj->unit_price * $convertedValue->qty_converted * $convertedValue->unit_price_percentage_converted / 100;
	}
	
	function add_item(
			$item_id,
			$quantity=1,
			$discount=0,
			$price=null,
			$cost_price = null,
			$description=null,
			$serialnumber=null,
			$force_add = FALSE,
			$line = FALSE,
			$update_register_cart_data = TRUE,
			$saleId = 0
	)
	{			
		$store_account_item_id = $this->CI->Item->get_store_account_item_id();
		
		//Do NOT allow item to get added unless in store_account_payment mode
		if (!$force_add && $this->get_mode() !=='store_account_payment' && $store_account_item_id == $item_id)
		{
			return FALSE;
		}
		
		//make sure item exists
		if(!$this->CI->Item->exists(does_contain_only_digits($item_id) ? (int)$item_id : -1))	
		{
			//try to get item id given an item_number
			$item_id = $this->CI->Item->get_item_id($item_id);

			if(!$item_id)
				return false;
		}
		else
		{
			$item_id = (int)$item_id;
		}
		
		if ($this->CI->config->item('do_not_allow_out_of_stock_items_to_be_sold'))
		{
			if (!$force_add && $this->will_be_out_of_stock($item_id,$quantity))
			{
				return FALSE;
			}
		}
		
		$item_info = $this->CI->Item->get_info($item_id);
		$item_location_info = $this->CI->Item_location->get_info($item_id);
		
		//Alain Serialization and Description

		//Get all items in the cart so far...
		$items = $this->get_cart();

        //We need to loop through all items in the cart.
        //If the item is already there, get it's key($updatekey).
        //We also need to get the next key that we are going to use in case we need to add the
        //item to the cart. Since items can be deleted, we can't use a count. we use the highest key + 1.

        $maxkey=0;                       //Highest key so far
        $itemalreadyinsale=FALSE;        //We did not find the item yet.
		$insertkey=0;                    //Key to use for new entry.
		$updatekey=0;                    //Key to use to update(quantity)

		foreach ($items as $item)
		{
            //We primed the loop so maxkey is 0 the first time.
            //Also, we have stored the key in the element itself so we can compare.

			if($maxkey <= $item['line'])
			{
				$maxkey = $item['line'];
			}

			if(isset($item['item_id']) && $item['item_id']==$item_id)
			{
				$itemalreadyinsale=TRUE;
				$updatekey=$item['line'];
				
				if($item_info->description==$items[$updatekey]['description'] && $item_info->name==lang('common_giftcard'))
				{
					return false;
				}
			}
		}

		$insertkey=$maxkey+1;

		$today =  strtotime(date('Y-m-d'));
		$price_to_use= $this->get_price_for_item($item_id);		

		$item_info = $this->CI->Item->get_info($item_id);
		$item_location_info = $this->CI->Item_location->get_info($item_id);
		$measure = $this->CI->Measure->getInfo($item_info->measure_id);

		if ($saleId) {
			$measureOnSale = $this->CI->Sale->getMeasureOnSaleItem($saleId, $item_id);
			if($measureOnSale && $measureOnSale->id && $measureOnSale->id != $measure->id) {
				$quantity = $measureOnSale->measure_qty;
				$price = $this->getPriceByMeasureConverted($item_id, (int) $measureOnSale->measure_id);
				$measure = $measureOnSale;
			}
		}

		$cost_price_to_use = ($item_location_info && $item_location_info->cost_price) ? $item_location_info->cost_price : $item_info->cost_price;
				 
		//array/cart records are identified by $insertkey and item_id is just another field.
		$item = array(($line === FALSE ? $insertkey : $line)=>
		array(
			'item_id'=>$item_id,
			'line'=>$line === FALSE ? $insertkey : $line,
			'name'=>$item_info->name,
			'change_cost_price' =>$item_info->change_cost_price,
			'cost_price' => $cost_price!=null ? $cost_price : $cost_price_to_use,
			'size' => $item_info->size,
			'item_number'=>$item_info->item_number,
			'product_id' => $item_info->product_id,
			'description'=>$description!=null ? $description: $item_info->description,
			'serialnumber'=>$serialnumber!=null ? $serialnumber: '',
			'allow_alt_description'=>$item_info->allow_alt_description,
			'is_serialized'=>$item_info->is_serialized,
			'quantity'=>$quantity,
			'measure_id'=>$measure->id,
			'measure' => !empty($measure) ? $measure->name : lang('common_not_set'),
			'cur_quantity' => $item_location_info->quantity,
			'discount'=>$discount,
			'price'=>$price!=null ? $price:$price_to_use,
			'tax_included'=> $item_info->tax_included,
			)
		);
		
		//Item already exists and is not serialized, add to quantity
		if($itemalreadyinsale && ($item_info->is_serialized ==0) && !$this->CI->config->item('do_not_group_same_items') && isset($items[$line === FALSE ? $updatekey : $line]))
		{
			$items[$line === FALSE ? $updatekey : $line]['quantity']+=$quantity;
		}
		else
		{
			//add to existing array
			$items+=$item;
		}

		$this->set_cart($items,$update_register_cart_data);
		return true;

	}
}
?>