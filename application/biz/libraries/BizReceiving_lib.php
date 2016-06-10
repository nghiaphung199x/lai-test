<?php
require_once (APPPATH.'/libraries/Receiving_lib.php');
class BizReceiving_lib extends Receiving_lib
{
	function add_item($item_id,$quantity=1,$quantity_received=NULL,$discount=0,$price=null,$description=null,$serialnumber=null,$expire_date= null, $force_add = FALSE, $line = FALSE)
	{
		//make sure item exists in database.
		if(!$force_add && !$this->CI->Item->exists(does_contain_only_digits($item_id) ? (int)$item_id : -1))
		{
			//try to get item id given an item_number
			$item_id = $this->CI->Item->get_item_id($item_id);

			if(!$item_id)
				return false;
		}

		//Get items in the receiving so far.
		$items = $this->get_cart();

        //We need to loop through all items in the cart.
        //If the item is already there, get it's key($updatekey).
        //We also need to get the next key that we are going to use in case we need to add the
        //item to the list. Since items can be deleted, we can't use a count. we use the highest key + 1.

        $maxkey=0;                       //Highest key so far
        $itemalreadyinsale=FALSE;        //We did not find the item yet.
		$insertkey=0;                    //Key to use for new entry.
		$updatekey=0;                    //Key to use to update(quantity)

		foreach ($items as $item)
		{
            //We primed the loop so maxkey is 0 the first time.
            //Also, we have stored the key in the element itself so we can compare.
            //There is an array function to get the associated key for an element, but I like it better
            //like that!

			if($maxkey <= $item['line'])
			{
				$maxkey = $item['line'];
			}

			if($item['item_id']==$item_id)
			{
				$itemalreadyinsale=TRUE;
				$updatekey=$item['line'];
			}
		}

		$insertkey=$maxkey+1;

		$cur_item_info = $this->CI->Item->get_info($item_id);

		$cur_item_location_info = $this->CI->Item_location->get_info($item_id);
		
		$default_cost_price = ($cur_item_location_info && $cur_item_location_info->cost_price) ? $cur_item_location_info->cost_price : $cur_item_info->cost_price;
		
		if ($expire_date === NULL && $cur_item_info->expire_days !== NULL)
		{
			$expire_date = date(get_date_format(), strtotime('+ '.$cur_item_info->expire_days. ' days'));
		}
		elseif($expire_date !== NULL)
		{
			$expire_date = date(get_date_format(),strtotime($expire_date));
		}
		else
		{
			$expire_date = NULL;
		}
		
		$measure = $this->CI->Measure->getInfo($cur_item_info->measure_id);
		//array records are identified by $insertkey and item_id is just another field.
		$item = array(($line === FALSE ? $insertkey : $line)=>
		array(
			'item_id'=>$item_id,
			'line'=>$line === FALSE ? $insertkey : $line,
			'name'=>$this->CI->Item->get_info($item_id)->name,
			'size' => $this->CI->Item->get_info($item_id)->size,
			'item_number'=>$cur_item_info->item_number,
			'product_id' => $cur_item_info->product_id,
			'description'=>$description!=null ? $description: $this->CI->Item->get_info($item_id)->description,
			'serialnumber'=>$serialnumber!=null ? $serialnumber: '',
			'allow_alt_description'=>$this->CI->Item->get_info($item_id)->allow_alt_description,
			'is_serialized'=>$this->CI->Item->get_info($item_id)->is_serialized,
			'quantity'=>$quantity,
			'measure_id'=>$cur_item_info->measure_id,
			'measure' => !empty($measure) ? $measure->name : 'Chua thiet lap',
			'cur_quantity' => $cur_item_location_info->quantity,
			'quantity_received' => $quantity_received,
			'discount'=>$discount,
			'price'=>$price!=null ? $price: $default_cost_price,
			'expire_date' => $expire_date,
			'cost_price_preview' => $this->calculate_average_cost_price_preview($item_id, $price!=null ? $price: $default_cost_price, $quantity,$discount),
			)
		);
		
		//Item already exists
		if($itemalreadyinsale && !$this->CI->config->item('do_not_group_same_items') && isset($items[$line === FALSE ? $updatekey : $line]))
		{
			$items[$line === FALSE ? $updatekey : $line]['quantity']+=$quantity;
			$items[$updatekey]['cost_price_preview']=$this->calculate_average_cost_price_preview($item_id, $price!=null ? $price: $default_cost_price, $quantity,$discount);
		}
		else
		{
			//add to existing array
			$items+=$item;
		}

		$this->set_cart($items);
		return true;

	}
	
	function edit_item($line,$description = NULL,$serialnumber = NULL,$expire_date= null, $quantity = NULL,$quantity_received=NULL,$discount = NULL,$price = NULL, $measureId = NULL )
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
				
			if ($expire_date !== NULL ) {
	
				if ($expire_date == '')
				{
					$items[$line]['expire_date'] = NULL;
				}
				else
				{
					$items[$line]['expire_date'] =  date(get_date_format(),strtotime($expire_date));
				}
			}
				
			if ($quantity_received !== NULL ) {
				$items[$line]['quantity_received'] = $quantity_received;
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
			
			if ($measureId /* && ($this->get_mode() == 'receive' || $this->get_mode() == 'purchase_order') */) {
				$items[$line]['measure_id'] = (int) $measureId;
				$measure = $this->CI->Measure->getInfo((int) $measureId);
				$items[$line]['measure'] = $measure->name;
				$itemObj = $this->CI->Item->get_info($items[$line]['item_id']);
				if($measureId != $itemObj->measure_id) {
					$items[$line]['price'] = $this->getPriceByMeasureConverted($items[$line]['item_id'], (int) $measureId);
				} else {
					$items[$line]['price'] = $itemObj->cost_price;
				}
			}
			$items[$line]['cost_price_preview']=$this->calculate_average_cost_price_preview($items[$line]['item_id'], $items[$line]['price'], $items[$line]['quantity'],$items[$line]['discount']);
			
			$this->set_cart($items);
				
			return true;
		}
	
		return false;
	}
	
	protected function getPriceByMeasureConverted($itemId = 0, $measureConvertedId = 0){
		$itemObj = $this->CI->Item->get_info($itemId);
		$convertedValue = $this->CI->ItemMeasures->getConvertedValue($itemId, $measureConvertedId);
		return $itemObj->cost_price * $convertedValue->qty_converted * $convertedValue->cost_price_percentage_converted / 100;
	}
}
?>