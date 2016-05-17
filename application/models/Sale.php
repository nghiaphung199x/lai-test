<?php
class Sale extends CI_Model
{
	public function __construct()
	{
      parent::__construct();
		$this->load->model('Inventory');	
	}
	
	public function get_info($sale_id)
	{
		$this->db->from('sales');
		$this->db->where('sale_id',$sale_id);
		return $this->db->get();
	}
	
	function get_cash_sales_total_for_shift($shift_start, $shift_end)
    {
		$sales_totals = $this->get_sales_totaled_by_id($shift_start, $shift_end);
		$register_id = $this->Employee->get_logged_in_employee_current_register_id();
        
		$this->db->select('sales_payments.sale_id, sales_payments.payment_type, payment_amount, payment_id', false);
      $this->db->from('sales_payments');
      $this->db->join('sales','sales_payments.sale_id=sales.sale_id');
		$this->db->where('sales_payments.payment_date >=', $shift_start);
		$this->db->where('sales_payments.payment_date <=', $shift_end);
		$this->db->where('register_id', $register_id);
		$this->db->where($this->db->dbprefix('sales').'.deleted', 0);
		$this->db->order_by('payment_date');
		
		$payments_by_sale = array();
		$sales_payments = $this->db->get()->result_array();
		
		foreach($sales_payments as $row)
		{
        	$payments_by_sale[$row['sale_id']][] = $row;
		}
				
		$payment_data = $this->Sale->get_payment_data($payments_by_sale,$sales_totals);
		
		if (isset($payment_data[lang('common_cash')]))
		{
			return $payment_data[lang('common_cash')]['payment_amount'];
		}
		
		return 0.00;
    }
	
	function get_payment_data($payments_by_sale,$sales_totals)
	{
		$payment_data = array();
		
		$sale_ids = array_keys($payments_by_sale);
		$all_payments_for_sales = $this->_get_all_sale_payments($sale_ids);
		
		foreach($all_payments_for_sales as $sale_id => $payment_rows)
		{
			if (isset($sales_totals[$sale_id]))
			{
				$total_sale_balance = $sales_totals[$sale_id];		
				foreach($payment_rows as $payment_row)
				{
					//Postive sale total, positive payment
					if ($sales_totals[$sale_id] >= 0 && $payment_row['payment_amount'] >=0)
					{
						$payment_amount = $payment_row['payment_amount'] <= $total_sale_balance ? $payment_row['payment_amount'] : $total_sale_balance;
					}//Negative sale total negative payment
					elseif ($sales_totals[$sale_id] < 0 && $payment_row['payment_amount']  < 0)
					{
						$payment_amount = $payment_row['payment_amount'] >= $total_sale_balance ? $payment_row['payment_amount'] : $total_sale_balance;
					}//Positive Sale total negative payment
					elseif($sales_totals[$sale_id] >= 0 && $payment_row['payment_amount']  < 0)
					{
						$payment_amount = $payment_row['payment_amount'];
					}//Negtive sale total postive payment
					elseif($sales_totals[$sale_id] < 0 && $payment_row['payment_amount']  >= 0)
					{
						$payment_amount = $payment_row['payment_amount'];
					}
					
					if (!isset($payment_data[$payment_row['payment_type']]))
					{
						$payment_data[$payment_row['payment_type']] = array('payment_type' => $payment_row['payment_type'], 'payment_amount' => 0 );
					}
					
					$exists = $this->_does_payment_exist_in_array($payment_row['payment_id'], $payments_by_sale[$sale_id]);
					
					
					if (($total_sale_balance != 0 || 
						($sales_totals[$sale_id] >= 0 && $payment_row['payment_amount']  < 0) ||
						($sales_totals[$sale_id] < 0 && $payment_row['payment_amount']  >= 0)) && $exists)
					{
						$payment_data[$payment_row['payment_type']]['payment_amount'] += $payment_amount;
					}

					$total_sale_balance-=$payment_amount;					
				}
			}
		}
		
		return $payment_data;
	}
	
	function _does_payment_exist_in_array($payment_id, $payments)
	{
		foreach($payments as $payment)
		{
			if($payment['payment_id'] == $payment_id)
			{
				return TRUE;
			}
		}
		
		return FALSE;
	}
		
	function _get_all_sale_payments($sale_ids)
	{
		$return = array();
		
		if (count($sale_ids) > 0)
		{
			$this->db->select('sales_payments.*, sales.sale_time');
      	$this->db->from('sales_payments');
      	$this->db->join('sales', 'sales.sale_id=sales_payments.sale_id');
			
			$this->db->group_start();
			$sale_ids_chunk = array_chunk($sale_ids,25);
			foreach($sale_ids_chunk as $sale_ids)
			{
				$this->db->or_where_in('sales_payments.sale_id', $sale_ids);
			}
			$this->db->group_end();
			$this->db->order_by('payment_date');
			
			$result = $this->db->get()->result_array();
			
			foreach($result as $row)
			{
				$return[$row['sale_id']][] = $row;
			}
		}
		return $return;
	}
		
	function get_payment_data_grouped_by_sale($payments_by_sale,$sales_totals)
	{
		$payment_data = array();
		
		$sale_ids = array_keys($payments_by_sale);
		$all_payments_for_sales = $this->_get_all_sale_payments($sale_ids);
		
		foreach($all_payments_for_sales as $sale_id => $payment_rows)
		{
			if (isset($sales_totals[$sale_id]))
			{
				$total_sale_balance = $sales_totals[$sale_id];
			
				foreach($payment_rows as $payment_row)
				{
					//Postive sale total, positive payment
					if ($sales_totals[$sale_id] >= 0 && $payment_row['payment_amount'] >=0)
					{
						$payment_amount = $payment_row['payment_amount'] <= $total_sale_balance ? $payment_row['payment_amount'] : $total_sale_balance;
					}//Negative sale total negative payment
					elseif ($sales_totals[$sale_id] < 0 && $payment_row['payment_amount']  < 0)
					{
						$payment_amount = $payment_row['payment_amount'] >= $total_sale_balance ? $payment_row['payment_amount'] : $total_sale_balance;
					}//Positive Sale total negative payment
					elseif($sales_totals[$sale_id] >= 0 && $payment_row['payment_amount']  < 0)
					{
						$payment_amount = $payment_row['payment_amount'];
					}//Negtive sale total postive payment
					elseif($sales_totals[$sale_id] < 0 && $payment_row['payment_amount']  >= 0)
					{
						$payment_amount = $payment_row['payment_amount'];
					}
				
					if (!isset($payment_data[$sale_id][$payment_row['payment_type']]))
					{
						$payment_data[$sale_id][$payment_row['payment_type']] = array('sale_id' => $sale_id,'payment_type' => $payment_row['payment_type'], 'payment_amount' => 0,'payment_date' => $payment_row['payment_date'], 'sale_time' => $payment_row['sale_time'] );
					}
				
					$exists = $this->_does_payment_exist_in_array($payment_row['payment_id'], $payments_by_sale[$sale_id]);
				
					if (($total_sale_balance != 0 || 
						($sales_totals[$sale_id] >= 0 && $payment_row['payment_amount']  < 0) ||
						($sales_totals[$sale_id] < 0 && $payment_row['payment_amount']  >= 0)) && $exists)
					{
						$payment_data[$sale_id][$payment_row['payment_type']]['payment_amount'] += $payment_amount;
					}
				
					$total_sale_balance-=$payment_amount;
				}
			}
		}
		
		return $payment_data;
	}
	
	
	function get_sales_totaled_by_id($shift_start, $shift_end)
	{
		$register_id = $this->Employee->get_logged_in_employee_current_register_id();
		
		$this->db->select('sales.sale_id', false);
      $this->db->from('sales');
      $this->db->join('sales_payments','sales_payments.sale_id=sales.sale_id');
		$this->db->where('sales_payments.payment_date >=', $shift_start);
		$this->db->where('sales_payments.payment_date <=', $shift_end);
		$this->db->where('register_id', $register_id);
		$this->db->where($this->db->dbprefix('sales').'.deleted', 0);
		
		$sale_ids = array();
		$result = $this->db->get()->result();
		foreach($result as $row)
		{
			$sale_ids[] = $row->sale_id;
		}
		
		$sales_totals = array();
		
		if (count($sale_ids) > 0)
		{
			$where = 'WHERE '.$this->db->dbprefix('sales').'.sale_id IN('.implode(',',$sale_ids).')';
			$this->_create_sales_items_temp_table_query($where);
			$this->db->select('sale_id, SUM(total) as total', false);
			$this->db->from('sales_items_temp');
			$this->db->group_by('sale_id');
			
			foreach($this->db->get()->result_array() as $sale_total_row)
			{
				$sales_totals[$sale_total_row['sale_id']] = $sale_total_row['total'];
			}
		}
		
		return $sales_totals;
	}
	 
	function exists($sale_id)
	{
		$this->db->from('sales');
		$this->db->where('sale_id',$sale_id);
		$query = $this->db->get();

		return ($query->num_rows()==1);
	}
	
	function update($sale_data, $sale_id)
	{
		$this->db->where('sale_id', $sale_id);
		$success = $this->db->update('sales',$sale_data);
		
		return $success;
	}
	
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
	
	function update_store_account($sale_id,$undelete=0)
	{
		//update if Store account payment exists
		$this->db->from('sales_payments');
		$this->db->where('payment_type',lang('common_store_account'));
		$this->db->where('sale_id',$sale_id);
		$to_be_paid_result = $this->db->get();
		
		$customer_id=$this->get_customer($sale_id)->person_id;
		
		
		if($to_be_paid_result->num_rows() >=1)
		{
			foreach($to_be_paid_result->result() as $to_be_paid)
			{
				if($to_be_paid->payment_amount) 
				{
					//update customer balance
					if($undelete==0)
					{
						$this->db->set('balance','balance-'.$to_be_paid->payment_amount,false);
					}
					else
					{
						$this->db->set('balance','balance+'.$to_be_paid->payment_amount,false);
					}
					$this->db->where('person_id', $customer_id);
					$this->db->update('customers'); 
				
				}
			}			
		}
	}
	
	function update_giftcard_balance($sale_id,$undelete=0)
	{
		//if gift card payment exists add the amount to giftcard balance
			$this->db->from('sales_payments');
			$this->db->like('payment_type',lang('common_giftcard'));
			$this->db->where('sale_id',$sale_id);
			$sales_payment = $this->db->get();
			
			if($sales_payment->num_rows() >=1)
			{
				foreach($sales_payment->result() as $row)
				{
					$giftcard_number=str_ireplace(lang('common_giftcard').':','',$row->payment_type);
					$cur_giftcard_value = $this->Giftcard->get_giftcard_value($giftcard_number);
					$value=$row->payment_amount;
					
					$value_to_add_subtract = 0;
					if($undelete==0)
					{
						$this->db->set('value','value+'.$value,false);
						$value_to_add_subtract = $value;		
					}
					else
					{
						$this->db->set('value','value-'.$value,false);
						$value_to_add_subtract = -$value;		
					}
					$this->db->where('giftcard_number', $giftcard_number);
					$this->db->update('giftcards'); 
					$this->Giftcard->log_modification(array('sale_id' => $sale_id, "number" => $giftcard_number, "old_value" => $cur_giftcard_value, "new_value" => $cur_giftcard_value + $value_to_add_subtract, "type" => $undelete ? 'sale_undelete' : 'sale_delete'));
				}
			}
	}
	
	function update_loyalty_simple_count($sale_id, $undelete=0)
	{
		$sale_info = $this->get_info($sale_id)->row_array();
		$store_account_payment = $sale_info['store_account_payment'];
		$customer_id = $sale_info['customer_id'];
		$suspended = $sale_info['suspended'];
		
	 	if (!$store_account_payment && $suspended != 2 && $customer_id > 0 && $this->config->item('enable_customer_loyalty_system') && $this->config->item('loyalty_option') == 'simple')
		{
			if ($sale_info['did_redeem_discount'])
			{
				$this->db->where('person_id', $customer_id);
				$this->db->set('current_sales_for_discount','current_sales_for_discount'.($undelete ? ' - ' : ' + ').$this->config->item('number_of_sales_for_discount'),false);
				$this->db->update('customers');				
			}
			else
			{
				$this->db->where('person_id', $customer_id);
				$this->db->set('current_sales_for_discount','current_sales_for_discount'.($undelete ? ' + ' : ' - ').'1',false);
				$this->db->update('customers');				
			}
		}
	}
	function update_points($sale_id, $undelete=0)
	{
		$sale_info = $this->get_info($sale_id)->row_array();
		$store_account_payment = $sale_info['store_account_payment'];
		$customer_id = $sale_info['customer_id'];
		$suspended = $sale_info['suspended'];
			
		 //Update points information if we have NOT a store account payment and not an estimate and we have a customer and we have loyalty enabled
		 if (!$store_account_payment && $suspended != 2 && $customer_id > 0 && $this->config->item('enable_customer_loyalty_system') && $this->config->item('loyalty_option') == 'advanced')
		 {
		   $customer_info = $this->Customer->get_info($customer_id);
			$current_points = $customer_info->points;
			$current_spend_for_points = $customer_info->current_spend_for_points;
			$total_spend_for_sale = $this->get_sale_total($sale_id);
			
			
			//Remove giftcard from spend
			$this->db->from('sales_payments');
			$this->db->like('payment_type',lang('common_giftcard'));
			$this->db->where('sale_id',$sale_id);
			$sales_payment = $this->db->get();
			
			if($sales_payment->num_rows() >=1)
			{
				foreach($sales_payment->result() as $row)
				{
					$total_spend_for_sale-=$row->payment_amount;
				}
			}

			//update if Store account payment exists
			$this->db->from('sales_payments');
			$this->db->where('payment_type',lang('common_points'));
			$this->db->where('sale_id',$sale_id);
			$points_payment = $this->db->get()->row_array();
			
			$points_payment =	isset($points_payment['payment_amount']) ? $points_payment['payment_amount'] : 0;
			
			//We should NOT count point payments for adding/removing points as we will do this later (at the end of this function)
			$total_spend_for_sale-=$points_payment;
			
		   list($spend_amount_for_points, $points_to_earn) = explode(":",$this->config->item('spend_to_point_ratio'),2);
			
			if($undelete) //Put points back
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
				
				$this->db->where('person_id', $customer_id);
				$this->db->update('customers', array('points' => $new_point_value, 'current_spend_for_points' => $new_current_spend_for_points));
				
				//If we are undeleting a sale; any points used should be removed back
				if ($sale_info['points_used'])
				{
 				  $this->db->set('points','points-'.$sale_info['points_used'],false);
 				  $this->db->where('person_id', $customer_id);
 				  $this->db->update('customers');
				}
				
		 }
		 else //Take points away
		 {
			if ($current_spend_for_points - abs($total_spend_for_sale) >=0) //Just need to remove current spend
			{
				$new_point_value = $current_points;
				$new_current_spend_for_points = $current_spend_for_points - $total_spend_for_sale;
			}
			else
			{
				
				$total_amount_towards_points = $current_spend_for_points + abs($total_spend_for_sale);
				$new_points =  (((($total_amount_towards_points)-fmod(($total_amount_towards_points), $spend_amount_for_points))/$spend_amount_for_points) * $points_to_earn);
				
				if ($total_spend_for_sale >= 0)
				{
					$new_point_value = $current_points - $new_points;					
				}
				else
				{
					$new_point_value = $current_points + $new_points;							
				}
				
				$new_current_spend_for_points = fmod(($current_spend_for_points - $total_spend_for_sale),$spend_amount_for_points);
			}
			
			$new_point_value = (int) round(to_currency_no_money($new_point_value));
			$new_current_spend_for_points = to_currency_no_money($new_current_spend_for_points);
			
			$this->db->where('person_id', $customer_id);
			$this->db->update('customers', array('points' => $new_point_value, 'current_spend_for_points' => $new_current_spend_for_points));
		 	
			
			//If we are deleting a sale; any points used shouold be added back
			if ($sale_info['points_used'])
			{
			  $this->db->set('points','points+'.$sale_info['points_used'],false);
			  $this->db->where('person_id', $customer_id);
			  $this->db->update('customers');
			}
		 }
	  }
	}
	
	function get_sale_total($sale_id)
	{		
		$decimals = $this->config->item('number_of_decimals') !== NULL && $this->config->item('number_of_decimals') != '' ? (int)$this->config->item('number_of_decimals') : 2;		
		$query = "SELECT ROUND(SUM(total),$decimals)as total FROM (
		(SELECT
		(ROUND(item_unit_price*quantity_purchased-item_unit_price*quantity_purchased*discount_percent/100,CASE WHEN tax_included =1 THEN 10 ELSE $decimals END))+(item_unit_price*quantity_purchased-item_unit_price*quantity_purchased*discount_percent/100)*(SUM(CASE WHEN cumulative != 1 THEN percent ELSE 0 END)/100) 
		+(((item_unit_price*quantity_purchased-item_unit_price*quantity_purchased*discount_percent/100)*(SUM(CASE WHEN cumulative != 1 THEN percent ELSE 0 END)/100) + (item_unit_price*quantity_purchased-item_unit_price*quantity_purchased*discount_percent/100))
		*(SUM(CASE WHEN cumulative = 1 THEN percent ELSE 0 END))/100) as total
		FROM ".$this->db->dbprefix('sales_items')."
		INNER JOIN ".$this->db->dbprefix('sales')." ON  ".$this->db->dbprefix('sales_items').'.sale_id='.$this->db->dbprefix('sales').'.sale_id'."
		INNER JOIN ".$this->db->dbprefix('items')." ON  ".$this->db->dbprefix('sales_items').'.item_id='.$this->db->dbprefix('items').'.item_id'."
		LEFT OUTER JOIN ".$this->db->dbprefix('suppliers')." ON  ".$this->db->dbprefix('items').'.supplier_id='.$this->db->dbprefix('suppliers').'.person_id'."
		LEFT OUTER JOIN ".$this->db->dbprefix('sales_items_taxes')." ON  "
		.$this->db->dbprefix('sales_items').'.sale_id='.$this->db->dbprefix('sales_items_taxes').'.sale_id'." and "
		.$this->db->dbprefix('sales_items').'.item_id='.$this->db->dbprefix('sales_items_taxes').'.item_id'." and "
		.$this->db->dbprefix('sales_items').'.line='.$this->db->dbprefix('sales_items_taxes').'.line'. "
		LEFT OUTER JOIN ".$this->db->dbprefix('registers')." ON  ".$this->db->dbprefix('registers').'.register_id='.$this->db->dbprefix('sales').'.register_id'."
		LEFT OUTER JOIN ".$this->db->dbprefix('categories')." ON  ".$this->db->dbprefix('categories').'.id='.$this->db->dbprefix('items').'.category_id'."
		WHERE ".$this->db->dbprefix('sales').".sale_id = $sale_id
		GROUP BY ".$this->db->dbprefix('sales_items').".sale_id, ".$this->db->dbprefix('sales_items').".item_id, ".$this->db->dbprefix('sales_items').".line) 
		UNION ALL
		(SELECT
		(ROUND(item_kit_unit_price*quantity_purchased-item_kit_unit_price*quantity_purchased*discount_percent/100,CASE WHEN tax_included =1 THEN 10 ELSE $decimals END))+(item_kit_unit_price*quantity_purchased-item_kit_unit_price*quantity_purchased*discount_percent/100)*(SUM(CASE WHEN cumulative != 1 THEN percent ELSE 0 END)/100) 
		+(((item_kit_unit_price*quantity_purchased-item_kit_unit_price*quantity_purchased*discount_percent/100)*(SUM(CASE WHEN cumulative != 1 THEN percent ELSE 0 END)/100) + (item_kit_unit_price*quantity_purchased-item_kit_unit_price*quantity_purchased*discount_percent/100))
		*(SUM(CASE WHEN cumulative = 1 THEN percent ELSE 0 END))/100) as total
		FROM ".$this->db->dbprefix('sales_item_kits')."
		INNER JOIN ".$this->db->dbprefix('sales')." ON  ".$this->db->dbprefix('sales_item_kits').'.sale_id='.$this->db->dbprefix('sales').'.sale_id'."
		INNER JOIN ".$this->db->dbprefix('item_kits')." ON  ".$this->db->dbprefix('sales_item_kits').'.item_kit_id='.$this->db->dbprefix('item_kits').'.item_kit_id'."
		LEFT OUTER JOIN ".$this->db->dbprefix('sales_item_kits_taxes')." ON  "
		.$this->db->dbprefix('sales_item_kits').'.sale_id='.$this->db->dbprefix('sales_item_kits_taxes').'.sale_id'." and "
		.$this->db->dbprefix('sales_item_kits').'.item_kit_id='.$this->db->dbprefix('sales_item_kits_taxes').'.item_kit_id'." and "
		.$this->db->dbprefix('sales_item_kits').'.line='.$this->db->dbprefix('sales_item_kits_taxes').'.line'. "
		LEFT OUTER JOIN ".$this->db->dbprefix('registers')." ON  ".$this->db->dbprefix('registers').'.register_id='.$this->db->dbprefix('sales').'.register_id'."
		LEFT OUTER JOIN ".$this->db->dbprefix('categories')." ON  ".$this->db->dbprefix('categories').'.id='.$this->db->dbprefix('item_kits').'.category_id'."
		WHERE ".$this->db->dbprefix('sales').".sale_id = $sale_id
		GROUP BY ".$this->db->dbprefix('sales_item_kits').".sale_id, ".$this->db->dbprefix('sales_item_kits').".item_kit_id, ".$this->db->dbprefix('sales_item_kits').".line)) as total_for_sale";
		
		$row = $this->db->query($query)->row_array();
		if (isset($row['total']))
		{
			return $row['total'];
		}
		
		return 0;
	}
	
	function delete($sale_id, $all_data = false)
	{
		$sale_info = $this->get_info($sale_id)->row_array();
		$suspended = $sale_info['suspended'];
		$employee_id=$this->Employee->get_logged_in_employee_info()->person_id;
		
		//Only update stock quantity if we are NOT an estimate ($suspendd = 2)
		if ($suspended != 2)
		{
			$this->db->select('sales.location_id, item_id, quantity_purchased');
			$this->db->from('sales_items');
			$this->db->join('sales', 'sales.sale_id = sales_items.sale_id');
			$this->db->where('sales_items.sale_id', $sale_id);
		
			foreach($this->db->get()->result_array() as $sale_item_row)
			{
				$sale_location_id = $sale_item_row['location_id'];
				$cur_item_info = $this->Item->get_info($sale_item_row['item_id']);	
				$cur_item_location_info = $this->Item_location->get_info($sale_item_row['item_id'], $sale_location_id);
			
				$cur_item_quantity = $this->Item_location->get_location_quantity($sale_item_row['item_id'], $sale_location_id);
			
				if (!$cur_item_info->is_service)
				{
					//Update stock quantity
					$this->Item_location->save_quantity($cur_item_quantity + $sale_item_row['quantity_purchased'],$sale_item_row['item_id'], $sale_location_id);
					
					$sale_remarks =$this->config->item('sale_prefix').' '.$sale_id;
					$inv_data = array
					(
						'location_id' => $sale_location_id,
						'trans_date'=>date('Y-m-d H:i:s'),
						'trans_items'=>$sale_item_row['item_id'],
						'trans_user'=>$employee_id,
						'trans_comment'=>$sale_remarks,
						'trans_inventory'=>$sale_item_row['quantity_purchased']
					);
					$this->Inventory->insert($inv_data);
				}
			}
		}

		//Only update stock quantity + store accounts + giftcard balance if we are NOT an estimate ($suspended = 2)
		if ($suspended != 2)
		{		
			$this->db->select('sales.location_id, item_kit_id, quantity_purchased');
			$this->db->from('sales_item_kits');
			$this->db->join('sales', 'sales.sale_id = sales_item_kits.sale_id');
			$this->db->where('sales_item_kits.sale_id', $sale_id);
		
			foreach($this->db->get()->result_array() as $sale_item_kit_row)
			{
				foreach($this->Item_kit_items->get_info($sale_item_kit_row['item_kit_id']) as $item_kit_item)
				{
					$sale_location_id = $sale_item_kit_row['location_id'];
					$cur_item_info = $this->Item->get_info($item_kit_item->item_id);
					$cur_item_location_info = $this->Item_location->get_info($item_kit_item->item_id, $sale_location_id);

					if (!$cur_item_info->is_service)
					{
						$cur_item_location_info->quantity = $cur_item_location_info->quantity !== NULL ? $cur_item_location_info->quantity : 0;
					
						$this->Item_location->save_quantity($cur_item_location_info->quantity + ($sale_item_kit_row['quantity_purchased'] * $item_kit_item->quantity),$item_kit_item->item_id, $sale_location_id);

						$sale_remarks =$this->config->item('sale_prefix').' '.$sale_id;
						$inv_data = array
						(
							'location_id' => $sale_location_id,
							'trans_date'=>date('Y-m-d H:i:s'),
							'trans_items'=>$item_kit_item->item_id,
							'trans_user'=>$employee_id,
							'trans_comment'=>$sale_remarks,
							'trans_inventory'=>$sale_item_kit_row['quantity_purchased'] * $item_kit_item->quantity
						);
						$this->Inventory->insert($inv_data);
					}				
				}
			}

			$this->update_store_account($sale_id);
			$this->update_giftcard_balance($sale_id);
			$this->update_points($sale_id);
			$this->update_loyalty_simple_count($sale_id);
			
			//Only insert store account transaction if we aren't deleting the whole sale.
			//When deleting the whole sale save() takes care of this
			if (!$all_data)
			{
		 		$previous_store_account_amount = $this->get_store_account_payment_total($sale_id);
			
				if ($previous_store_account_amount)
				{	
					$store_account_transaction = array(
			   		'customer_id'=>$sale_info['customer_id'],
			      	'sale_id'=>$sale_id,
						'comment'=>$sale_info['comment'],
			      	'transaction_amount'=>-$previous_store_account_amount,
						'balance'=>$this->Customer->get_info($sale_info['customer_id'])->balance,
						'date' => date('Y-m-d H:i:s')
					);
					$this->db->insert('store_accounts',$store_account_transaction);
				}
			}
		}
		
		if ($all_data)
		{
			$this->db->delete('sales_payments', array('sale_id' => $sale_id)); 
			$this->db->delete('sales_items_taxes', array('sale_id' => $sale_id)); 
			$this->db->delete('sales_items', array('sale_id' => $sale_id)); 
			$this->db->delete('sales_item_kits_taxes', array('sale_id' => $sale_id)); 
			$this->db->delete('sales_item_kits', array('sale_id' => $sale_id)); 
		}

		$this->db->where('sale_id', $sale_id);
		return $this->db->update('sales', array('deleted' => 1,'deleted_by'=>$employee_id));
	}
	
	function undelete($sale_id)
	{
		$sale_info = $this->get_info($sale_id)->row_array();
		$suspended = $sale_info['suspended'];
		$employee_id=$this->Employee->get_logged_in_employee_info()->person_id;
	
		//Only update stock quantity + store accounts + giftcard balance if we are NOT an estimate ($suspended = 2)
		if ($suspended != 2)
		{		
			$this->db->select('sales.location_id, item_id, quantity_purchased');
			$this->db->from('sales_items');
			$this->db->join('sales', 'sales.sale_id = sales_items.sale_id');
			$this->db->where('sales_items.sale_id', $sale_id);
		
			foreach($this->db->get()->result_array() as $sale_item_row)
			{
				$sale_location_id = $sale_item_row['location_id'];
				$cur_item_info = $this->Item->get_info($sale_item_row['item_id']);	
				$cur_item_location_info = $this->Item_location->get_info($sale_item_row['item_id'], $sale_location_id);

				if (!$cur_item_info->is_service && $cur_item_location_info->quantity !== NULL)
				{
					//Update stock quantity
					$this->Item_location->save_quantity($cur_item_location_info->quantity - $sale_item_row['quantity_purchased'],$sale_item_row['item_id']);
		
					$sale_remarks =$this->config->item('sale_prefix').' '.$sale_id;
					$inv_data = array
					(
						'location_id' => $sale_location_id,
						'trans_date'=>date('Y-m-d H:i:s'),
						'trans_items'=>$sale_item_row['item_id'],
						'trans_user'=>$employee_id,
						'trans_comment'=>$sale_remarks,
						'trans_inventory'=>-$sale_item_row['quantity_purchased']
						);
					$this->Inventory->insert($inv_data);
				}
			}
		
			$this->update_store_account($sale_id,1);
			$this->update_giftcard_balance($sale_id,1);
			$this->update_points($sale_id,1);
			$this->update_loyalty_simple_count($sale_id,1);
			
		 	$previous_store_account_amount = $this->get_store_account_payment_total($sale_id);
			
			if ($previous_store_account_amount)
			{	
			 	$store_account_transaction = array(
			      'customer_id'=>$sale_info['customer_id'],
			      'sale_id'=>$sale_id,
					'comment'=>$sale_info['comment'],
			      'transaction_amount'=>$previous_store_account_amount,
					'balance'=>$this->Customer->get_info($sale_info['customer_id'])->balance,
					'date' => date('Y-m-d H:i:s')
				);
				$this->db->insert('store_accounts',$store_account_transaction);
			}
			
			
			$this->db->select('sales.location_id, item_kit_id, quantity_purchased');
			$this->db->from('sales_item_kits');
			$this->db->join('sales', 'sales.sale_id = sales_item_kits.sale_id');
			$this->db->where('sales_item_kits.sale_id', $sale_id);
		
			foreach($this->db->get()->result_array() as $sale_item_kit_row)
			{
				foreach($this->Item_kit_items->get_info($sale_item_kit_row['item_kit_id']) as $item_kit_item)
				{
					$sale_location_id = $sale_item_kit_row['location_id'];
					$cur_item_info = $this->Item->get_info($item_kit_item->item_id);
					$cur_item_location_info = $this->Item_location->get_info($item_kit_item->item_id, $sale_location_id);
					if (!$cur_item_info->is_service && $cur_item_location_info->quantity !== NULL)
					{
						$this->Item_location->save_quantity($cur_item_location_info->quantity - ($sale_item_kit_row['quantity_purchased'] * $item_kit_item->quantity),$item_kit_item->item_id, $sale_location_id);
					
						$sale_remarks =$this->config->item('sale_prefix').' '.$sale_id;
						$inv_data = array
						(
							'location_id' => $sale_location_id,
							'trans_date'=>date('Y-m-d H:i:s'),
							'trans_items'=>$item_kit_item->item_id,
							'trans_user'=>$employee_id,
							'trans_comment'=>$sale_remarks,
							'trans_inventory'=>-$sale_item_kit_row['quantity_purchased'] * $item_kit_item->quantity
						);
						$this->Inventory->insert($inv_data);					
					}
				}
			}	
		}
		
		$this->db->where('sale_id', $sale_id);
		return $this->db->update('sales', array('deleted' => 0, 'deleted_by' => NULL));
	}

	function get_sale_items($sale_id)
	{
		$this->db->from('sales_items');
		$this->db->where('sale_id',$sale_id);
		$this->db->order_by('line');
		return $this->db->get();
	}
	
	function get_sale_items_ordered_by_category($sale_id)
	{
		$this->db->select('items.*, sales_items.*, categories.name as category, sales_items.description as sales_items_description');
		$this->db->from('sales_items');
		$this->db->join('items', 'items.item_id = sales_items.item_id');
		$this->db->join('categories', 'categories.id = items.category_id');
		$this->db->where('sale_id',$sale_id);
		$this->db->order_by('categories.name, items.name');
		return $this->db->get();		
	}

	function get_sale_item_kits($sale_id)
	{
		$this->db->from('sales_item_kits');
		$this->db->where('sale_id',$sale_id);
		$this->db->order_by('line');
		return $this->db->get();
	}
	
	function get_sale_item_kits_ordered_by_category($sale_id)
	{
		$this->db->select('item_kits.*, sales_item_kits.*, categories.name as category');
		$this->db->from('sales_item_kits');
		$this->db->join('item_kits', 'item_kits.item_kit_id = sales_item_kits.item_kit_id');
		$this->db->join('categories', 'categories.id = item_kits.category_id');
		$this->db->where('sale_id',$sale_id);
		$this->db->order_by('categories.name, item_kits.name');
		return $this->db->get();		
	}
	
	function get_sale_items_taxes($sale_id, $line = FALSE)
	{
		$item_where = '';
		
		if ($line)
		{
			$item_where = 'and '.$this->db->dbprefix('sales_items').'.line = '.$line;
		}

		$query = $this->db->query('SELECT name, percent, cumulative, item_unit_price as price, quantity_purchased as quantity, discount_percent as discount '.
		'FROM '. $this->db->dbprefix('sales_items_taxes'). ' JOIN '.
		$this->db->dbprefix('sales_items'). ' USING (sale_id, item_id, line) '.
		'WHERE '.$this->db->dbprefix('sales_items_taxes').".sale_id = $sale_id".' '.$item_where.' '.
		'ORDER BY '.$this->db->dbprefix('sales_items').'.line,'.$this->db->dbprefix('sales_items').'.item_id,cumulative,name,percent');
		return $query->result_array();
	}
	
	function get_sale_item_kits_taxes($sale_id, $line = FALSE)
	{
		$item_kit_where = '';
		
		if ($line)
		{
			$item_kit_where = 'and '.$this->db->dbprefix('sales_item_kits').'.line = '.$line;
		}
		
		$query = $this->db->query('SELECT name, percent, cumulative, item_kit_unit_price as price, quantity_purchased as quantity, discount_percent as discount '.
		'FROM '. $this->db->dbprefix('sales_item_kits_taxes'). ' JOIN '.
		$this->db->dbprefix('sales_item_kits'). ' USING (sale_id, item_kit_id, line) '.
		'WHERE '.$this->db->dbprefix('sales_item_kits_taxes').".sale_id = $sale_id".' '.$item_kit_where.' '.
		'ORDER BY '.$this->db->dbprefix('sales_item_kits').'.line,'.$this->db->dbprefix('sales_item_kits').'.item_kit_id,cumulative,name,percent');
		return $query->result_array();	
	}

	function get_sale_payments($sale_id)
	{
		$this->db->from('sales_payments');
		$this->db->where('sale_id',$sale_id);
		return $this->db->get();
	}

	function get_customer($sale_id)
	{
		$this->db->from('sales');
		$this->db->where('sale_id',$sale_id);
		return $this->Customer->get_info($this->db->get()->row()->customer_id);
	}
	
	function get_comment($sale_id)
	{
		$this->db->from('sales');
		$this->db->where('sale_id',$sale_id);
		return $this->db->get()->row()->comment;
	}
	
	function get_comment_on_receipt($sale_id)
	{
		$this->db->from('sales');
		$this->db->where('sale_id',$sale_id);
		return $this->db->get()->row()->show_comment_on_receipt;
	}
		
	function get_sold_by_employee_id($sale_id)
	{
		$this->db->from('sales');
		$this->db->where('sale_id',$sale_id);
		return $this->db->get()->row()->sold_by_employee_id;
	}

	//We create a temp table that allows us to do easy report/sales queries
	public function create_sales_items_temp_table($params)
	{
		$where = '';
		
		if (isset($params['sale_ids']))
		{
			if (!empty($params['sale_ids']))
			{
				for($k=0;$k<count($params['sale_ids']);$k++)
				{
					$params['sale_ids'][$k] = $this->db->escape($params['sale_ids'][$k]);
				}
				
				$where.='WHERE '.$this->db->dbprefix('sales').".sale_id IN(".implode(',', $params['sale_ids']).")";
			}
			else
			{
				$where.='WHERE '.$this->db->dbprefix('sales').".sale_id IN(0)";
			}
		}
		elseif (isset($params['start_date']) && isset($params['end_date']))
		{
			$location_ids = implode(',',Report::get_selected_location_ids());
			
			$where = 'WHERE sale_time BETWEEN '.$this->db->escape($params['start_date']).' and '.$this->db->escape($params['end_date']).' and '.$this->db->dbprefix('sales').'.location_id IN ('.$location_ids.')'. (($this->config->item('hide_store_account_payments_in_reports') ) ? ' and '.$this->db->dbprefix('sales').'.store_account_payment=0' : '');
		
			//Added for detailed_suspended_report, we don't need this for other reports as we are always going to have start + end date
			if (isset($params['force_suspended']) && $params['force_suspended'])
			{
				$where .=' and (suspended != 0 or (was_layaway = 1 or was_estimate = 1))';				
			}
			elseif ($this->config->item('hide_layaways_sales_in_reports'))
			{
				$where .=' and suspended = 0';
			}
			else
			{
				$where .=' and suspended != 2';					
			}
		}
		elseif ($this->config->item('hide_layaways_sales_in_reports'))
		{
			$location_ids = implode(',',Report::get_selected_location_ids());
			$where .='WHERE suspended = 0'.' and '.$this->db->dbprefix('sales').'.location_id IN ('.$location_ids.')'.(($this->config->item('hide_store_account_payments_in_reports') ) ? ' and '.$this->db->dbprefix('sales').'.store_account_payment=0' : '');
		}
		else
		{
			$location_ids = implode(',',Report::get_selected_location_ids());
			$where .='WHERE suspended != 2'.' and '.$this->db->dbprefix('sales').'.location_id IN ('.$location_ids.')'.(($this->config->item('hide_store_account_payments_in_reports') ) ? ' and '.$this->db->dbprefix('sales').'.store_account_payment=0' : '');				
		}
		
		if ($where == '')
		{
			$location_ids = implode(',',Report::get_selected_location_ids());
			$where = 'WHERE suspended != 2 and '.$this->db->dbprefix('sales').'.location_id IN ('.$location_ids.')'.(($this->config->item('hide_store_account_payments_in_reports') ) ? ' and '.$this->db->dbprefix('sales').'.store_account_payment=0' : '');
		}
	
		$return = $this->_create_sales_items_temp_table_query($where);		
		return $return;
	}
	
	function _create_sales_items_temp_table_query($where)
	{
		set_time_limit(0);
		
		$decimals = $this->config->item('number_of_decimals') !== NULL && $this->config->item('number_of_decimals') != '' ? (int)$this->config->item('number_of_decimals') : 2;
		
		return $this->db->query("CREATE TEMPORARY TABLE ".$this->db->dbprefix('sales_items_temp')."
		(SELECT ".$this->db->dbprefix('sales').".location_id as location_id, ".$this->db->dbprefix('sales').".deleted as deleted,".$this->db->dbprefix('sales').".deleted_by as deleted_by, sale_time, date(sale_time) as sale_date, ".$this->db->dbprefix('registers').'.name as register_name,'.$this->db->dbprefix('sales_items').".sale_id, comment,payment_type, customer_id, employee_id, sold_by_employee_id, 
		".$this->db->dbprefix('items').".item_id, NULL as item_kit_id, supplier_id, quantity_purchased, item_cost_price, item_unit_price, ".$this->db->dbprefix('categories').'.name as category'.", ".$this->db->dbprefix('categories').'.id as category_id'.", 
		discount_percent, ROUND(item_unit_price*quantity_purchased-item_unit_price*quantity_purchased*discount_percent/100,CASE WHEN tax_included =1 THEN 10 ELSE $decimals END) as subtotal,
		".$this->db->dbprefix('sales_items').".line as line, serialnumber, ".$this->db->dbprefix('sales_items').".description as description,
		(ROUND(item_unit_price*quantity_purchased-item_unit_price*quantity_purchased*discount_percent/100,CASE WHEN tax_included =1 THEN 10 ELSE $decimals END))+(item_unit_price*quantity_purchased-item_unit_price*quantity_purchased*discount_percent/100)*(SUM(CASE WHEN cumulative != 1 THEN percent ELSE 0 END)/100) 
		+(((item_unit_price*quantity_purchased-item_unit_price*quantity_purchased*discount_percent/100)*(SUM(CASE WHEN cumulative != 1 THEN percent ELSE 0 END)/100) + (item_unit_price*quantity_purchased-item_unit_price*quantity_purchased*discount_percent/100))
		*(SUM(CASE WHEN cumulative = 1 THEN percent ELSE 0 END))/100) as total,
		(item_unit_price*quantity_purchased-item_unit_price*quantity_purchased*discount_percent/100)*(SUM(CASE WHEN cumulative != 1 THEN percent ELSE 0 END)/100) 
		+(((item_unit_price*quantity_purchased-item_unit_price*quantity_purchased*discount_percent/100)*(SUM(CASE WHEN cumulative != 1 THEN percent ELSE 0 END)/100) + (item_unit_price*quantity_purchased-item_unit_price*quantity_purchased*discount_percent/100))
		*(SUM(CASE WHEN cumulative = 1 THEN percent ELSE 0 END))/100) as tax,
		ROUND((item_unit_price*quantity_purchased-item_unit_price*quantity_purchased*discount_percent/100),CASE WHEN tax_included =1 THEN 10 ELSE $decimals END) - (item_cost_price*quantity_purchased) as profit, commission, store_account_payment,item_cost_price as sale_item_temp_cost_price, points_used, points_gained
		FROM ".$this->db->dbprefix('sales_items')."
		INNER JOIN ".$this->db->dbprefix('sales')." ON  ".$this->db->dbprefix('sales_items').'.sale_id='.$this->db->dbprefix('sales').'.sale_id'."
		INNER JOIN ".$this->db->dbprefix('items')." ON  ".$this->db->dbprefix('sales_items').'.item_id='.$this->db->dbprefix('items').'.item_id'."
		LEFT OUTER JOIN ".$this->db->dbprefix('suppliers')." ON  ".$this->db->dbprefix('items').'.supplier_id='.$this->db->dbprefix('suppliers').'.person_id'."
		LEFT OUTER JOIN ".$this->db->dbprefix('sales_items_taxes')." ON  "
		.$this->db->dbprefix('sales_items').'.sale_id='.$this->db->dbprefix('sales_items_taxes').'.sale_id'." and "
		.$this->db->dbprefix('sales_items').'.item_id='.$this->db->dbprefix('sales_items_taxes').'.item_id'." and "
		.$this->db->dbprefix('sales_items').'.line='.$this->db->dbprefix('sales_items_taxes').'.line'. "
		LEFT OUTER JOIN ".$this->db->dbprefix('registers')." ON  ".$this->db->dbprefix('registers').'.register_id='.$this->db->dbprefix('sales').'.register_id'."
		LEFT OUTER JOIN ".$this->db->dbprefix('categories')." ON  ".$this->db->dbprefix('categories').'.id='.$this->db->dbprefix('items').'.category_id'."
		$where
		GROUP BY sale_id, item_id, line) 
		UNION ALL
		(SELECT ".$this->db->dbprefix('sales').".location_id as location_id, ".$this->db->dbprefix('sales').".deleted as deleted,".$this->db->dbprefix('sales').".deleted_by as deleted_by, sale_time, date(sale_time) as sale_date, ".$this->db->dbprefix('registers').'.name as register_name,'.$this->db->dbprefix('sales_item_kits').".sale_id, comment,payment_type, customer_id, employee_id, sold_by_employee_id,
		NULL as item_id, ".$this->db->dbprefix('item_kits').".item_kit_id, '' as supplier_id, quantity_purchased, item_kit_cost_price, item_kit_unit_price,".$this->db->dbprefix('categories').'.name as category'.", ".$this->db->dbprefix('categories').'.id as category_id'.",
		discount_percent, ROUND(item_kit_unit_price*quantity_purchased-item_kit_unit_price*quantity_purchased*discount_percent/100,CASE WHEN tax_included =1 THEN 10 ELSE $decimals END) as subtotal,
		".$this->db->dbprefix('sales_item_kits').".line as line, '' as serialnumber, ".$this->db->dbprefix('sales_item_kits').".description as description,
		(ROUND(item_kit_unit_price*quantity_purchased-item_kit_unit_price*quantity_purchased*discount_percent/100,CASE WHEN tax_included =1 THEN 10 ELSE $decimals END))+(item_kit_unit_price*quantity_purchased-item_kit_unit_price*quantity_purchased*discount_percent/100)*(SUM(CASE WHEN cumulative != 1 THEN percent ELSE 0 END)/100) 
		+(((item_kit_unit_price*quantity_purchased-item_kit_unit_price*quantity_purchased*discount_percent/100)*(SUM(CASE WHEN cumulative != 1 THEN percent ELSE 0 END)/100) + (item_kit_unit_price*quantity_purchased-item_kit_unit_price*quantity_purchased*discount_percent/100))
		*(SUM(CASE WHEN cumulative = 1 THEN percent ELSE 0 END))/100) as total,
		(item_kit_unit_price*quantity_purchased-item_kit_unit_price*quantity_purchased*discount_percent/100)*(SUM(CASE WHEN cumulative != 1 THEN percent ELSE 0 END)/100) 
		+(((item_kit_unit_price*quantity_purchased-item_kit_unit_price*quantity_purchased*discount_percent/100)*(SUM(CASE WHEN cumulative != 1 THEN percent ELSE 0 END)/100) + (item_kit_unit_price*quantity_purchased-item_kit_unit_price*quantity_purchased*discount_percent/100))
		*(SUM(CASE WHEN cumulative = 1 THEN percent ELSE 0 END))/100) as tax,
		ROUND((item_kit_unit_price*quantity_purchased-item_kit_unit_price*quantity_purchased*discount_percent/100),CASE WHEN tax_included =1 THEN 10 ELSE $decimals END) - (item_kit_cost_price*quantity_purchased) as profit, commission, store_account_payment, item_kit_cost_price as sale_item_temp_cost_price, points_used, points_gained
		FROM ".$this->db->dbprefix('sales_item_kits')."
		INNER JOIN ".$this->db->dbprefix('sales')." ON  ".$this->db->dbprefix('sales_item_kits').'.sale_id='.$this->db->dbprefix('sales').'.sale_id'."
		INNER JOIN ".$this->db->dbprefix('item_kits')." ON  ".$this->db->dbprefix('sales_item_kits').'.item_kit_id='.$this->db->dbprefix('item_kits').'.item_kit_id'."
		LEFT OUTER JOIN ".$this->db->dbprefix('sales_item_kits_taxes')." ON  "
		.$this->db->dbprefix('sales_item_kits').'.sale_id='.$this->db->dbprefix('sales_item_kits_taxes').'.sale_id'." and "
		.$this->db->dbprefix('sales_item_kits').'.item_kit_id='.$this->db->dbprefix('sales_item_kits_taxes').'.item_kit_id'." and "
		.$this->db->dbprefix('sales_item_kits').'.line='.$this->db->dbprefix('sales_item_kits_taxes').'.line'. "
		LEFT OUTER JOIN ".$this->db->dbprefix('registers')." ON  ".$this->db->dbprefix('registers').'.register_id='.$this->db->dbprefix('sales').'.register_id'."
		LEFT OUTER JOIN ".$this->db->dbprefix('categories')." ON  ".$this->db->dbprefix('categories').'.id='.$this->db->dbprefix('item_kits').'.category_id'."
		$where
		GROUP BY sale_id, item_kit_id, line) ORDER BY sale_id, line");
	}
	
	function drop_sales_items_temp_table()
	{
		$this->db->query('DROP TABLE IF EXISTS '.$this->db->dbprefix('sales_items_temp'));
	}
	
	public function get_giftcard_value( $giftcardNumber )
	{
		if ( !$this->Giftcard->exists( $this->Giftcard->get_giftcard_id($giftcardNumber)))
			return 0;
		
		$this->db->from('giftcards');
		$this->db->where('giftcard_number',$giftcardNumber);
		return $this->db->get()->row()->value;
	}
	
	function get_all_suspended($suspended_types = array(1,2))
	{		
		$location_id = $this->Employee->get_logged_in_employee_current_location_id();		
		
		$this->db->from('sales');
		$this->db->join('customers', 'sales.customer_id = customers.person_id', 'left');
		$this->db->join('people', 'customers.person_id = people.person_id', 'left');
		$this->db->where('sales.deleted', 0);
		$this->db->where_in('suspended', $suspended_types);
		$this->db->where('location_id', $location_id);
		$this->db->order_by('sale_id');
		$sales = $this->db->get()->result_array();

		for($k=0;$k<count($sales);$k++)
		{
			$item_names = array();
			$this->db->select('name');
			$this->db->from('items');
			$this->db->join('sales_items', 'sales_items.item_id = items.item_id');
			$this->db->where('sale_id', $sales[$k]['sale_id']);
		
			foreach($this->db->get()->result_array() as $row)
			{
				$item_names[] = $row['name'];
			}
			
			$this->db->select('name');
			$this->db->from('item_kits');
			$this->db->join('sales_item_kits', 'sales_item_kits.item_kit_id = item_kits.item_kit_id');
			$this->db->where('sale_id', $sales[$k]['sale_id']);
		
			foreach($this->db->get()->result_array() as $row)
			{
				$item_names[] = $row['name'];
			}
			
			
			$sales[$k]['items'] = implode(', ', $item_names);
		}
		
		return $sales;
		
	}
	
	function count_all()
	{
		$this->db->from('sales');
		$this->db->where('deleted',0);
		
		if ($this->config->item('hide_store_account_payments_in_reports'))
		{
			$this->db->where('store_account_payment',0);
		}
		
		return $this->db->count_all_results();
	}
	
	function get_recent_sales_for_customer($customer_id)
	{
		$return = array();
		
		$this->db->select('sales.*, SUM(quantity_purchased) as items_purchased');
		$this->db->from('sales');
		$this->db->join('sales_items', 'sales.sale_id = sales_items.sale_id');
		$this->db->where('customer_id', $customer_id);
		$this->db->where('deleted', 0);
		$this->db->order_by('sale_time DESC');
		$this->db->group_by('sales.sale_id');
		$this->db->limit($this->config->item('number_of_recent_sales') ? $this->config->item('number_of_recent_sales') : 10);
		
		foreach($this->db->get()->result_array() as $row)
		{
			$return[] = $row;
		}

		return $return;
	}
	
	function get_store_account_payment_total($sale_id)
	{
		$this->db->select('SUM(payment_amount) as store_account_payment_total', false);
		$this->db->from('sales_payments');
		$this->db->where('sale_id', $sale_id);
		$this->db->where('payment_type', lang('common_store_account'));
		
		$sales_payments = $this->db->get()->row_array();	
		
		return $sales_payments['store_account_payment_total'] ? $sales_payments['store_account_payment_total'] : 0;
	}
	
	function get_deleted_taxes($sale_id)
	{
		$this->db->from('sales');
		$this->db->where('sale_id',$sale_id);
		return unserialize($this->db->get()->row()->deleted_taxes);
	}
	
	function get_sales_per_day_for_range($start_date, $end_date)
	{
		$logged_in_location_id = $this->Employee->get_logged_in_employee_current_location_id();
		
		$this->db->select('count(*) as count, date(sale_time) as sale_date', false);
		$this->db->from('sales');
		$this->db->group_by('sale_date');
		$this->db->order_by('sale_date');
		$this->db->where('location_id', $logged_in_location_id);
		$this->db->where('sale_time BETWEEN '.$this->db->escape($start_date).' and '.$this->db->escape($end_date).' and deleted = 0');
		$return = $this->db->get()->result_array();
		return $return;
	}
	
	function get_quantity_sold_for_item_in_sale($sale_id, $item_id)
	{
		$this->db->select('quantity_purchased');
		$this->db->from('sales_items');
		$this->db->where('sale_id',$sale_id);
		$this->db->where('item_id',$item_id);
		$row = $this->db->get()->row_array();
		
		return empty($row) ? 0 : $row['quantity_purchased'];
	}
	
	function get_quantity_sold_for_item_kit_in_sale($sale_id, $item_kit_id)
	{
		$this->db->select('quantity_purchased');
		$this->db->from('sales_item_kits');
		$this->db->where('sale_id',$sale_id);
		$this->db->where('item_kit_id',$item_kit_id);
		$row = $this->db->get()->row_array();
		
		return empty($row) ? 0 : $row['quantity_purchased'];
		
	}
	
	function can_void_cc_sale($sale_id)
	{
		$processor = false;
		
		if ($this->Location->get_info_for_key('credit_card_processor') == 'mercury' || !$this->Location->get_info_for_key('credit_card_processor'))
		{
			$processor = 'mercury';
		}
		elseif($this->Location->get_info_for_key('credit_card_processor') == 'stripe')
		{
			$processor = 'stripe';
		}
		elseif($this->Location->get_info_for_key('credit_card_processor') == 'braintree')
		{
			$processor = 'braintree';
		}
		
		$this->db->from('sales_payments');
		$this->db->where('sale_id',$sale_id);
		$this->db->where_in('payment_type', array(lang('common_credit'),lang('sales_partial_credit')));
		
		$result = $this->db->get()->result_array();
		
		if (empty($result))
		{
			return FALSE;
		}

		foreach($result as $row)
		{
			if ($processor == 'mercury')
			{
				if(!($row['auth_code'] && $row['ref_no'] && $row['cc_token'] && $row['acq_ref_data'] && $row['process_data'] && $row['payment_amount'] > 0))
				{
					return FALSE;
				}
			}
			elseif($processor == 'stripe' || $processor == 'braintree')
			{
				if (!$row['ref_no'])
				{
					return FALSE;
				}
			}
		}
		
		return TRUE;
	}
	
	function can_void_cc_return($sale_id)
	{
		$processor = false;
		
		
		if ($this->Location->get_info_for_key('credit_card_processor') == 'mercury' || !$this->Location->get_info_for_key('credit_card_processor'))
		{
			$processor = 'mercury';
		}
		elseif($this->Location->get_info_for_key('credit_card_processor') == 'stripe')
		{
			$processor = 'stripe';
		}
		elseif($this->Location->get_info_for_key('credit_card_processor') == 'braintree')
		{
			$processor = 'braintree';
		}
		
		$this->db->from('sales_payments');
		$this->db->where('sale_id',$sale_id);
		$this->db->where_in('payment_type', array(lang('common_credit'),lang('sales_partial_credit')));
		
		$result = $this->db->get()->result_array();
		
		if (empty($result))
		{
			return FALSE;
		}

		foreach($result as $row)
		{
			if ($processor == 'mercury')
			{
				//TODO: Don't need acq_ref_data for EMV USB for some reason...Should find out why
				if(!($row['auth_code'] && $row['ref_no'] && $row['cc_token'] && $row['process_data'] && $row['payment_amount'] < 0))
				{
					return FALSE;
				}
				
			}
			elseif($processor == 'stripe' || $processor == 'braintree')
			{
				return FALSE;
			}
		}
		
		return TRUE;
	}
	
	function get_item_ids_sold_for_date_range($start_date, $end_date, $supplier_id, $location_id = FALSE)
	{
		if ($location_id === FALSE)
		{
			$location_id = $this->Employee->get_logged_in_employee_current_location_id();
		}
		
		$this->db->select('sales_items.item_id');
		$this->db->from('sales_items');
		$this->db->join('items', 'sales_items.item_id = items.item_id');
		$this->db->join('sales', 'sales.sale_id = sales_items.sale_id');
		$this->db->where('sale_time BETWEEN '.$this->db->escape($start_date).' and '.$this->db->escape($end_date).' and sales.deleted = 0');
		$this->db->where('supplier_id', $supplier_id);
		$this->db->where('location_id', $location_id);
		$item_ids = array();
		
		foreach($this->db->get()->result_array() as $row)
		{
			$item_ids[$row['item_id']] = $row['item_id'];
		}
		
		return array_values($item_ids);
	}
	
	function get_last_sale_id($location_id = FALSE)
	{
		if ($location_id === FALSE)
		{
			$location_id = $this->Employee->get_logged_in_employee_current_location_id();
		}
		
		$this->db->select('sale_id');
		$this->db->from('sales');
		$this->db->where('deleted', 0);
		$this->db->where('location_id', $location_id);
		$this->db->order_by('sale_id DESC');
		$this->db->limit(1);
		$query = $this->db->get();
		
		if ($row = $query->row_array())
		{
			return $row['sale_id'];
		}
		
		return FALSE;
		
	}
	
	function get_global_weighted_average_cost()
	{
		$current_location=$this->Employee->get_logged_in_employee_current_location_id();
		
		$this->db->select('sum(IFNULL('.$this->db->dbprefix('location_items').'.cost_price, '.$this->db->dbprefix('items').'.cost_price) * quantity) / sum(quantity) as weighted_cost', FALSE);
		$this->db->from('items');
		$this->db->join('location_items', 'location_items.item_id = items.item_id and location_id = '.$current_location, 'left');
		$this->db->where('is_service !=', 1);
		$this->db->where('items.deleted', 0);
		
		$row = $this->db->get()->row_array();
		
		return $row['weighted_cost'];
		
	}
}
?>
