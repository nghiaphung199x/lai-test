<?php
require_once (APPPATH.'/libraries/Sale_lib.php');

class BizSale_lib extends Sale_lib
{
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
		$convertedValue = $this->CI->ItemMeasures->getConvertedValue($itemId, $itemObj->measure_id, $measureConvertedId);
		return $itemObj->unit_price * $convertedValue->qty_converted * $convertedValue->unit_price_percentage_converted / 100;
	}
	
	function add_item($item_id,$quantity=1,$discount=0,$price=null,$cost_price = null, $description=null,$serialnumber=null, $force_add = FALSE, $line = FALSE, $update_register_cart_data = TRUE)
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
			'measure_id'=>$item_info->measure_id,
			'measure' => !empty($measure) ? $measure->name : 'Chua thiet lap',
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