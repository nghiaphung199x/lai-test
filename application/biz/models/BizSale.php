<?php
require_once (APPPATH . "models/Sale.php");
class BizSale extends Sale
{
	function save ($items,$customer_id,$employee_id, $sold_by_employee_id, $comment,$show_comment_on_receipt,$payments,$sale_id=false, $suspended = 0, $change_sale_date=false,$balance=0, $store_account_payment = 0)
	{
		if ($this->config->item('test_mode'))
		{
			$this->load->library('sale_lib');
			$this->sale_lib->clear_all();
			return lang('sales_test_mode_transaction');
		}
		
		//Run these queries as a transaction, we want to make sure we do all or nothing
		$this->db->trans_start();
			
		$global_weighted_average_cost = FALSE;
		
		if ($this->config->item('always_use_average_cost_method'))
		{
			$global_weighted_average_cost=  $this->get_global_weighted_average_cost();
			$global_weighted_average_cost = to_currency_no_money($global_weighted_average_cost, 10);
		}
		
		if ($sale_id)
		{
			$before_save_sale_info = $this->get_info($sale_id)->row();
		}
		else
		{
			$before_save_sale_info = FALSE;
		}
		//we need to check the sale library for deleted taxes during sale
		$this->load->library('sale_lib');
		
		if(count($items)==0)
			return -1;

		$payment_types='';
		foreach($payments as $payment_id=>$payment)
		{
			$payment_types=$payment_types.$payment['payment_type'].': '.to_currency($payment['payment_amount']).'<br />';
		}
		
		$tier_id = $this->sale_lib->get_selected_tier_id();
		$deleted_taxes = $this->sale_lib->get_deleted_taxes();
		
		if (!$tier_id)
		{
			$tier_id = NULL;
		}
		
		$sales_data = array(
			'customer_id'=> $customer_id > 0 ? $customer_id : null,
			'employee_id'=>$employee_id,
			'sold_by_employee_id' => $sold_by_employee_id,
			'payment_type'=>$payment_types,
			'comment'=>$comment,
			'show_comment_on_receipt'=> $show_comment_on_receipt ?  $show_comment_on_receipt : 0,
			'suspended'=>$suspended,
			'deleted' => 0,
			'deleted_by' => NULL,
			'cc_ref_no' => $before_save_sale_info ? $before_save_sale_info->cc_ref_no : '',//Legacy for old payments; set new payments to empty
			'auth_code' => $before_save_sale_info ? $before_save_sale_info->auth_code : '',//Legacy for old payments; set new payments to empty
			'location_id' => $this->Employee->get_logged_in_employee_current_location_id(),
			'register_id' => $this->Employee->get_logged_in_employee_current_register_id(),
			'store_account_payment' => $store_account_payment,
			'tier_id' => $tier_id ? $tier_id : NULL,
			'deleted_taxes' =>  $deleted_taxes? serialize($deleted_taxes) : NULL,
		);
			
		if ($suspended == 1) //Layaway
		{
			$sales_data['was_layaway'] = 1;
		}
		elseif ($suspended == 2) //estimate
		{
			$sales_data['was_estimate'] = 1;				
		}
		
		if($sale_id)
		{
			$old_data=$this->get_info($sale_id)->row_array();
			$sales_data['sale_time']=$old_data['sale_time'];
		}
		else
		{
			$sales_data['sale_time'] = date('Y-m-d H:i:s');
		}
		
		if($change_sale_date) 
		{
			$sale_time = strtotime($change_sale_date);
			if($sale_time !== FALSE)
			{
				$sales_data['sale_time']=date('Y-m-d H:i:s', strtotime($change_sale_date));
			}
		}
		
		if ($sale_id)
		{
			//If we are NOT a suspended sale and wasn't a layaway
			if (!$this->sale_lib->get_suspended_sale_id() && !$old_data['was_layaway'])
			{
				$override_payment_time = $sales_data['sale_time'];
			}
		}
		elseif($change_sale_date)
		{
			if (!$this->sale_lib->get_suspended_sale_id())
			{
				$override_payment_time = $sales_data['sale_time'];
			}
			
		}
		
		$store_account_payment_amount = 0;
		
		if ($store_account_payment)
		{
			$store_account_payment_amount = $this->sale_lib->get_total();
		}
		
		//Only update balance + store account payments if we are NOT an estimate (suspended = 2)
		if ($suspended != 2)
		{
	   	  //Update customer store account balance
			  if($customer_id > 0 && $balance)
			  {
				  $this->db->set('balance','balance+'.$balance,false);
				  $this->db->where('person_id', $customer_id);
				  $this->db->update('customers');
			  }
			  
		     //Update customer store account if payment made
			if($customer_id > 0 && $store_account_payment_amount)
			{
				$this->db->set('balance','balance-'.$store_account_payment_amount,false);
				$this->db->where('person_id', $customer_id);
				$this->db->update('customers');
			 }
		 }
		 		 
		 $previous_store_account_amount = 0;

		 if ($sale_id !== FALSE)
		 {
			 $previous_store_account_amount = $this->get_store_account_payment_total($sale_id);
		 }
		 
		if ($sale_id)
		{
			//Delete previoulsy sale so we can overwrite data
			$this->delete($sale_id, true);
			
			$this->db->where('sale_id', $sale_id);
			$this->db->update('sales', $sales_data);
		}
		else
		{
			$this->db->insert('sales',$sales_data);
			$sale_id = $this->db->insert_id();
		}
		
		//Loyalty systems
		 if ($suspended != 2 && $customer_id > 0 && $this->config->item('enable_customer_loyalty_system'))
		 {
		   $sales_data_loy = array();	 
		   $customer_info = $this->Customer->get_info($customer_id);
		
			if ($this->config->item('loyalty_option') == 'simple')
			{
				if (!$store_account_payment)
				{
					if ($this->sale_lib->get_redeem())
					{
						$this->db->where('person_id', $customer_id);
						$this->db->set('current_sales_for_discount','current_sales_for_discount -'.$this->config->item('number_of_sales_for_discount'),false);
						$this->db->update('customers');
						$sales_data_loy['did_redeem_discount'] = 1;				
					}
					else
					{
						$this->db->where('person_id', $customer_id);
						$this->db->set('current_sales_for_discount','current_sales_for_discount +1',false);
						$this->db->update('customers');
					}
				}
			}
			else
			{
				$current_points = $customer_info->points;
				$current_spend_for_points = $customer_info->current_spend_for_points;
			
				//This is duplicated below; but this is ok so we don't break anything else
				$giftcard_payments_amount = 0;
				foreach($payments as $payment_id=>$payment)
				{
					if ( substr( $payment['payment_type'], 0, strlen( lang('common_giftcard') ) ) == lang('common_giftcard') )
					{
						$giftcard_payments_amount+=$payment['payment_amount'];
					}
				}
			
				//Don't count points or gift cards
				$total_spend_for_sale = $this->sale_lib->get_total() - $this->sale_lib->get_payment_amount(lang('common_points')) - $giftcard_payments_amount;
	         list($spend_amount_for_points, $points_to_earn) = explode(":",$this->config->item('spend_to_point_ratio'),2);
		
				if (!$store_account_payment)
				{
					//If we earn any points
					if ($current_spend_for_points + abs($total_spend_for_sale) >= $spend_amount_for_points)
					{
						$total_amount_towards_points = $current_spend_for_points + abs($total_spend_for_sale);
						$new_points = (((($total_amount_towards_points)-fmod(($total_amount_towards_points), $spend_amount_for_points))/$spend_amount_for_points) * $points_to_earn);
						
						if ($total_spend_for_sale >= 0)
						{
							$new_point_value = $current_points + $new_points;					
						}
						else
						{
							$new_point_value = $current_points - $new_points;							
						}
						
						$new_current_spend_for_points = fmod(($current_spend_for_points + $total_spend_for_sale),$spend_amount_for_points);
					}
					else
					{
						$new_current_spend_for_points = $current_spend_for_points + $total_spend_for_sale;
						$new_point_value = $current_points;
					}
			
					$sales_data_loy['points_gained'] = (int)($new_point_value -  $current_points); 
				}
				else //Don't change any values for store account payment
				{
					$new_current_spend_for_points = $current_spend_for_points;
					$new_point_value = $current_points;
				}
		
				//Redeem points
				if ($payment_amount_points = $this->sale_lib->get_payment_amount(lang('common_points')))
				{
					$points_used = to_currency_no_money($payment_amount_points / $this->config->item('point_value'));
					$new_point_value -= $points_used;
					$sales_data_loy['points_used'] = (int)$points_used;
			
				}
				else
				{
					$sales_data_loy['points_used'] = 0;
				}
		
				$new_point_value = (int) round(to_currency_no_money($new_point_value));
				$new_current_spend_for_points = to_currency_no_money($new_current_spend_for_points);
		
				$this->db->where('person_id', $customer_id);
				$this->db->update('customers', array('points' => $new_point_value, 'current_spend_for_points' => $new_current_spend_for_points));				
			 }
		 	
			if(!empty($sales_data_loy))
			{
				$this->db->where('sale_id', $sale_id);
				$this->db->update('sales', $sales_data_loy);
			}
		 }
		 				
		//Only update store account payments if we are NOT an estimate (suspended = 2)
		if ($suspended != 2)
		{
			// Our customer switched from before; add special logic
			if ($balance && $before_save_sale_info && $before_save_sale_info->customer_id && $before_save_sale_info->customer_id != $customer_id)
			{
				$store_account_transaction = array(
				   'customer_id'=>$customer_id,
				   'sale_id'=>$sale_id,
					'comment'=>$comment,
				   'transaction_amount'=>$balance,
					'balance'=>$this->Customer->get_info($customer_id)->balance,
					'date' => date('Y-m-d H:i:s')
				);

				$this->db->insert('store_accounts',$store_account_transaction);
				
				
				$store_account_transaction = array(
				   'customer_id'=>$before_save_sale_info->customer_id,
				   'sale_id'=>$sale_id,
					'comment'=>$comment,
				   'transaction_amount'=>-$previous_store_account_amount,
					'balance'=>$this->Customer->get_info($before_save_sale_info->customer_id)->balance,
					'date' => date('Y-m-d H:i:s')
				);

				$this->db->insert('store_accounts',$store_account_transaction);
				
			}
			elseif($customer_id > 0 && $balance)
			{
			 	$store_account_transaction = array(
			      'customer_id'=>$customer_id,
			      'sale_id'=>$sale_id,
					'comment'=>$comment,
			      'transaction_amount'=>$balance - $previous_store_account_amount,
					'balance'=>$this->Customer->get_info($customer_id)->balance,
					'date' => date('Y-m-d H:i:s')
				);
				
				if ($balance - $previous_store_account_amount)
				{
					$this->db->insert('store_accounts',$store_account_transaction);
				}
			 } 
			 elseif ($customer_id > 0 && $previous_store_account_amount) //We had a store account payment before has one...We need to log this
			 {
 			 	$store_account_transaction = array(
 			      'customer_id'=>$customer_id,
 			      'sale_id'=>$sale_id,
 					'comment'=>$comment,
 			      'transaction_amount'=> -$previous_store_account_amount,
 					'balance'=>$this->Customer->get_info($customer_id)->balance,
 					'date' => date('Y-m-d H:i:s')
 				);

 				$this->db->insert('store_accounts',$store_account_transaction);
				
			 } //We switched customers for a sale
			 //insert store account payment transaction 
			if($customer_id > 0 && $store_account_payment)
			{
			 	$store_account_transaction = array(
			        'customer_id'=>$customer_id,
			        'sale_id'=>$sale_id,
					'comment'=>$comment,
			       	'transaction_amount'=> -$store_account_payment_amount,
					'balance'=>$this->Customer->get_info($customer_id)->balance,
					'date' => date('Y-m-d H:i:s')
				);

				$this->db->insert('store_accounts',$store_account_transaction);
			 }
		 }
		 
		$total_giftcard_payments = 0;

		foreach($payments as $payment_id=>$payment)
		{
			//Only update giftcard payments if we are NOT an estimate (suspended = 2)
			if ($suspended != 2)
			{
				if ( substr( $payment['payment_type'], 0, strlen( lang('common_giftcard') ) ) == lang('common_giftcard') )
				{
					/* We have a gift card and we have to deduct the used value from the total value of the card. */
					$splitpayment = explode( ':', $payment['payment_type'] );
					$cur_giftcard_value = $this->Giftcard->get_giftcard_value( $splitpayment[1] );
	
					$this->Giftcard->update_giftcard_value( $splitpayment[1], $cur_giftcard_value - $payment['payment_amount'] );
					$total_giftcard_payments+=$payment['payment_amount'];
					
					$this->Giftcard->log_modification(array('sale_id' => $sale_id, "number" => $splitpayment[1], "person" => lang('common_customer'), "old_value" => $cur_giftcard_value, "new_value" => $cur_giftcard_value - $payment['payment_amount'], "type" => 'sale'));
					
				}
			}

			$sales_payments_data = array
			(
				'sale_id'=>$sale_id,
				'payment_type'=>$payment['payment_type'],
				'payment_amount'=>$payment['payment_amount'],
				'payment_date' => isset($override_payment_time) ? $override_payment_time: $payment['payment_date'],
				'truncated_card' => $payment['truncated_card'],
				'card_issuer' => $payment['card_issuer'],
				'auth_code' => $payment['auth_code'],
				'ref_no' => $payment['ref_no'],
				'cc_token' => $payment['cc_token'],
				'acq_ref_data' => $payment['acq_ref_data'],
				'process_data' => $payment['process_data'],	
				'entry_method' => $payment['entry_method'],
				'aid' => $payment['aid'],
				'tvr' => $payment['tvr'],
				'iad' => $payment['iad'],
				'tsi' => $payment['tsi'],
				'arc' => $payment['arc'],
				'cvm' => $payment['cvm'],
				'tran_type' => $payment['tran_type'],
				'application_label' => $payment['application_label'],			
			);
			$this->db->insert('sales_payments',$sales_payments_data);
		}
	
		$has_added_giftcard_value_to_cost_price = $total_giftcard_payments > 0 ? false : true;
		$store_account_item_id = $this->Item->get_store_account_item_id();
		
		foreach($items as $line=>$item)
		{
			if (isset($item['item_id']))
			{
				$cur_item_info = $this->Item->get_info($item['item_id']);
				$cur_item_location_info = $this->Item_location->get_info($item['item_id']);
				
				if( $cur_item_info->measure_id != $item['measure_id'] /* && ($mode == 'receive' || $mode == 'purchase_order') */)
				{
					$convertedValue = $this->ItemMeasures->getConvertedValue($item['item_id'], $cur_item_info->measure_id, $item['measure_id']);
					$cost_price = $cost_price * (100 + (int)$convertedValue->unit_price_percentage_converted ) / 100;
				
					$totalQty = $item['quantity'] = $item['quantity'] * (int)$convertedValue->qty_converted;
				
					$item['price'] = $item['price'] / $totalQty;
				}
				
				//Redeem profit when giftcard is used; so we set cost price to item price
				if ($item['name']==lang('common_giftcard') && !$this->Giftcard->get_giftcard_id($item['description']) && $this->config->item('calculate_profit_for_giftcard_when') == 'redeeming_giftcard')
				{
					$cost_price = $item['price'];					
				}
				elseif ($item['item_id'] != $store_account_item_id)
				{
					$cost_price = $item['cost_price'];
				}
				else // Set cost price = price so we have no profit
				{
					$cost_price = $item['price'];
				}
				
				
				if ($this->config->item('calculate_profit_for_giftcard_when') == 'selling_giftcard')
				{
					//Add to the cost price if we are using a giftcard as we have already recorded profit for sale of giftcard
					if (!$has_added_giftcard_value_to_cost_price)
					{
						$cost_price+= $total_giftcard_payments / $item['quantity'];
						$has_added_giftcard_value_to_cost_price = true;
					}
				}
				$reorder_level = ($cur_item_location_info && $cur_item_location_info->reorder_level) ? $cur_item_location_info->reorder_level : $cur_item_info->reorder_level;
				
				if ($cur_item_info->tax_included)
				{
					$this->load->helper('items');
					$item['price'] = get_price_for_item_excluding_taxes($item['item_id'], $item['price']);
				}
				
				$this->load->helper('items');
				
				$sales_items_data = array
				(
					'sale_id'=>$sale_id,
					'item_id'=>$item['item_id'],
					'line'=>$item['line'],
					'description'=>$item['description'],
					'serialnumber'=>$item['serialnumber'],
					'quantity_purchased'=>$item['quantity'],
					'discount_percent'=>$item['discount'],
					'item_cost_price' =>  $global_weighted_average_cost === FALSE ? to_currency_no_money($cost_price,10) : $global_weighted_average_cost,
					'item_unit_price'=>$item['price'],
					'commission' => get_commission_for_item($item['item_id'],$item['price'],to_currency_no_money($cost_price,10), $item['quantity'], $item['discount']),
				);
				
				$this->db->insert('sales_items',$sales_items_data);
				
				//Only update giftcard payments if we are NOT an estimate (suspended = 2)
				if ($suspended != 2)
				{
					//create giftcard from sales 
					if($item['name']==lang('common_giftcard') && !$this->Giftcard->get_giftcard_id($item['description'])) 
					{ 
						$giftcard_data = array(
							'giftcard_number'=>$item['description'],
							'value'=>$item['price'],
							'description' => $comment,
							'customer_id'=>$customer_id > 0 ? $customer_id : null,
						);
												
						$this->Giftcard->save($giftcard_data);
						
						$employee_info = $this->Employee->get_logged_in_employee_info();
						$this->Giftcard->log_modification(array('sale_id' => $sale_id, "number" => $item['description'], "person"=>$employee_info->first_name . " " . $employee_info->last_name, "new_value" => $item['price'], 'old_value' => 0, "type" => 'create'));
					}
				}
				
				//Only do stock check + inventory update if we are NOT an estimate
				if ($suspended != 2)
				{
					$stock_recorder_check=false;
					$out_of_stock_check=false;
					$email=false;
					$message = '';

					//checks if the quantity is greater than reorder level
					if(!$cur_item_info->is_service && $cur_item_location_info->quantity > $reorder_level)
					{
						$stock_recorder_check=true;
					}
				
					//checks if the quantity is greater than 0
					if(!$cur_item_info->is_service && $cur_item_location_info->quantity > 0)
					{
						$out_of_stock_check=true;
					}
				
					//Update stock quantity IF not a service 
					if (!$cur_item_info->is_service)
					{
						$cur_item_location_info->quantity = $cur_item_location_info->quantity !== NULL ? $cur_item_location_info->quantity : 0;
						$this->Item_location->save_quantity($cur_item_location_info->quantity - $item['quantity'], $item['item_id']);
					}
				
					//Re-init $cur_item_location_info after updating quantity
					$cur_item_location_info = $this->Item_location->get_info($item['item_id']);
				
					//checks if the quantity is out of stock
					if($out_of_stock_check && $cur_item_location_info->quantity <= 0)
					{
						$message= $cur_item_info->name.' '.lang('sales_is_out_stock').' '.to_quantity($cur_item_location_info->quantity);
						$email=true;
					
					}	
					//checks if the quantity hits reorder level 
					else if($stock_recorder_check && ($cur_item_location_info->quantity <= $reorder_level))
					{
						$message= $cur_item_info->name.' '.lang('sales_hits_reorder_level').' '.to_quantity($cur_item_location_info->quantity);
						$email=true;
					}
				
					//send email 
					if($this->Location->get_info_for_key('receive_stock_alert') && $email)
					{			
						$this->load->library('email');
						$config = array();
						$config['mailtype'] = 'text';				
						$this->email->initialize($config);
						$this->email->from($this->Location->get_info_for_key('email') ? $this->Location->get_info_for_key('email') : 'no-reply@mg.4biz.vn', $this->config->item('company'));
						$this->email->to($this->Location->get_info_for_key('stock_alert_email') ? $this->Location->get_info_for_key('stock_alert_email') : $this->Location->get_info_for_key('email')); 

						$this->email->subject(lang('sales_stock_alert_item_name').$this->Item->get_info($item['item_id'])->name);
						$this->email->message($message);	
						$this->email->send();
					}
				
					if (!$cur_item_info->is_service)
					{
						$qty_buy = -$item['quantity'];
						$sale_remarks =$this->config->item('sale_prefix').' '.$sale_id;
						$inv_data = array
						(
							'trans_date'=>date('Y-m-d H:i:s'),
							'trans_items'=>$item['item_id'],
							'trans_user'=>$employee_id,
							'trans_comment'=>$sale_remarks,
							'trans_inventory'=>$qty_buy,
							'location_id' => $this->Employee->get_logged_in_employee_current_location_id() 
						);
						$this->Inventory->insert($inv_data);
					}
				}
			}
			else
			{
				$cur_item_kit_info = $this->Item_kit->get_info($item['item_kit_id']);
				$cur_item_kit_location_info = $this->Item_kit_location->get_info($item['item_kit_id']);
				
				$cost_price = $item['cost_price'];
				
				if ($this->config->item('calculate_profit_for_giftcard_when') == 'selling_giftcard')
				{
					//Add to the cost price if we are using a giftcard as we have already recorded profit for sale of giftcard
					if (!$has_added_giftcard_value_to_cost_price)
					{
						$cost_price+= $total_giftcard_payments / $item['quantity'];
						$has_added_giftcard_value_to_cost_price = true;
					}
				}
				
				if ($cur_item_kit_info->tax_included)
				{
					$this->load->helper('item_kits');
					$item['price'] = get_price_for_item_kit_excluding_taxes($item['item_kit_id'], $item['price']);
				}
				
				$this->load->helper('item_kits');
				$sales_item_kits_data = array
				(
					'sale_id'=>$sale_id,
					'item_kit_id'=>$item['item_kit_id'],
					'line'=>$item['line'],
					'description'=>$item['description'],
					'quantity_purchased'=>$item['quantity'],
					'discount_percent'=>$item['discount'],
					'item_kit_cost_price' => $global_weighted_average_cost === FALSE ? ($cost_price === NULL ? 0.00 : to_currency_no_money($cost_price,10)) : $global_weighted_average_cost,
					'item_kit_unit_price'=>$item['price'],
					'commission' => get_commission_for_item_kit($item['item_kit_id'],$item['price'],$cost_price === NULL ? 0.00 : to_currency_no_money($cost_price,10), $item['quantity'], $item['discount']),
				);

				$this->db->insert('sales_item_kits',$sales_item_kits_data);
				
				foreach($this->Item_kit_items->get_info($item['item_kit_id']) as $item_kit_item)
				{
					$cur_item_info = $this->Item->get_info($item_kit_item->item_id);
					$cur_item_location_info = $this->Item_location->get_info($item_kit_item->item_id);
					
					$reorder_level = ($cur_item_location_info && $cur_item_location_info->reorder_level !== NULL) ? $cur_item_location_info->reorder_level : $cur_item_info->reorder_level;
					
					//Only do stock check + inventory update if we are NOT an estimate
					if ($suspended != 2)
					{
						$stock_recorder_check=false;
						$out_of_stock_check=false;
						$email=false;
						$message = '';


						//checks if the quantity is greater than reorder level
						if(!$cur_item_info->is_service && $cur_item_location_info->quantity > $reorder_level)
						{
							$stock_recorder_check=true;
						}

						//checks if the quantity is greater than 0
						if(!$cur_item_info->is_service && $cur_item_location_info->quantity > 0)
						{
							$out_of_stock_check=true;
						}

						//Update stock quantity IF not a service item and the quantity for item is NOT NULL
						if (!$cur_item_info->is_service)
						{
							$cur_item_location_info->quantity = $cur_item_location_info->quantity !== NULL ? $cur_item_location_info->quantity : 0;
								
							$this->Item_location->save_quantity($cur_item_location_info->quantity - ($item['quantity'] * $item_kit_item->quantity),$item_kit_item->item_id);
						}
					
						//Re-init $cur_item_location_info after updating quantity
						$cur_item_location_info = $this->Item_location->get_info($item_kit_item->item_id);
				
						//checks if the quantity is out of stock
						if($out_of_stock_check && !$cur_item_info->is_service && $cur_item_location_info->quantity <= 0)
						{
							$message= $cur_item_info->name.' '.lang('sales_is_out_stock').' '.to_quantity($cur_item_location_info->quantity);
							$email=true;

						}	
						//checks if the quantity hits reorder level 
						else if($stock_recorder_check && ($cur_item_location_info->quantity <= $reorder_level))
						{
							$message= $cur_item_info->name.' '.lang('sales_hits_reorder_level').' '.to_quantity($cur_item_location_info->quantity);
							$email=true;
						}

						//send email 
						if($this->Location->get_info_for_key('receive_stock_alert') && $email)
						{			
							$this->load->library('email');
							$config = array();
							$config['mailtype'] = 'text';				
							$this->email->initialize($config);
							$this->email->from($this->Location->get_info_for_key('email') ? $this->Location->get_info_for_key('email') : 'no-reply@mg.4biz.vn', $this->config->item('company'));
							$this->email->to($this->Location->get_info_for_key('stock_alert_email') ? $this->Location->get_info_for_key('stock_alert_email') : $this->Location->get_info_for_key('email')); 

							$this->email->subject(lang('sales_stock_alert_item_name').$cur_item_info->name);
							$this->email->message($message);	
							$this->email->send();
						}

						if (!$cur_item_info->is_service)
						{
							$qty_buy = -$item['quantity'] * $item_kit_item->quantity;
							$sale_remarks =$this->config->item('sale_prefix').' '.$sale_id;
							$inv_data = array
							(
								'trans_date'=>date('Y-m-d H:i:s'),
								'trans_items'=>$item_kit_item->item_id,
								'trans_user'=>$employee_id,
								'trans_comment'=>$sale_remarks,
								'trans_inventory'=>$qty_buy,
								'location_id' => $this->Employee->get_logged_in_employee_current_location_id()
							);
							$this->Inventory->insert($inv_data);
						}
					}
				}
			}
			
			$customer = $this->Customer->get_info($customer_id);
 			if ($customer_id == -1 or $customer->taxable)
 			{
				if (isset($item['item_id']))
				{
					foreach($this->Item_taxes_finder->get_info($item['item_id']) as $row)
					{
						$tax_name = $row['percent'].'% ' . $row['name'];
				
						//Only save sale if the tax has NOT been deleted
						if (!in_array($tax_name, $this->sale_lib->get_deleted_taxes()))
						{	
							 $this->db->insert('sales_items_taxes', array(
								'sale_id' 	=>$sale_id,
								'item_id' 	=>$item['item_id'],
								'line'      =>$item['line'],
								'name'		=>$row['name'],
								'percent' 	=>$row['percent'],
								'cumulative'=>$row['cumulative']
							));
						}
					}
				}
				else
				{
					foreach($this->Item_kit_taxes_finder->get_info($item['item_kit_id']) as $row)
					{
						$tax_name = $row['percent'].'% ' . $row['name'];
				
						//Only save sale if the tax has NOT been deleted
						if (!in_array($tax_name, $this->sale_lib->get_deleted_taxes()))
						{
							$this->db->insert('sales_item_kits_taxes', array(
								'sale_id' 		=>$sale_id,
								'item_kit_id'	=>$item['item_kit_id'],
								'line'      	=>$item['line'],
								'name'			=>$row['name'],
								'percent' 		=>$row['percent'],
								'cumulative'	=>$row['cumulative']
							));
						}
					}					
				}
			}
		}
		
		$this->db->trans_complete();
		
		if ($this->db->trans_status() === FALSE)
		{
			return -1;
		}
			
		return $sale_id;				
	}
}
?>
