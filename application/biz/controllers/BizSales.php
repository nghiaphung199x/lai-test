<?php
require_once (APPPATH . "controllers/Sales.php");

class BizSales extends Sales 
{
	protected $_prefixDocument = 'SALE#';

	function __construct()
	{
		parent::__construct();
		$this->load->helper('sale');
	}

	function edit_item($line)
	{
		$data= array();
	
		$this->form_validation->set_rules('price', 'lang:common_price', 'numeric');
		$this->form_validation->set_rules('cost_price', 'lang:common_price', 'numeric');
		$this->form_validation->set_rules('quantity', 'lang:common_quantity', 'numeric');
		$this->form_validation->set_rules('discount', 'lang:common_discount_percent', 'numeric');
	
		if($this->input->post("name"))
		{
			$variable = $this->input->post("name");
			$$variable = $this->input->post("value");
		}
	
		if (isset($discount) && $discount !== NULL && $discount == '')
		{
			$discount = 0;
		}
	
		$can_edit = TRUE;
	
		if ($this->form_validation->run() != FALSE)
		{
			if ($this->config->item('do_not_allow_out_of_stock_items_to_be_sold'))
			{
				if (isset($quantity) && $this->sale_lib->is_kit_or_item($line) == 'item')
				{
					$current_item_id = $this->sale_lib->get_item_id($line);
					$before_quantity = $this->sale_lib->get_quantity_at_line($line);
						
					if ($this->sale_lib->will_be_out_of_stock($current_item_id, isset($quantity) ? $quantity - $before_quantity : 0))
					{
						$can_edit = FALSE;
					}
				}
				elseif (isset($quantity) && $this->sale_lib->is_kit_or_item($line) == 'kit')
				{
					$current_item_kit_id = $this->sale_lib->get_kit_id($line);
					$before_quantity = $this->sale_lib->get_quantity_at_line($line);
						
					if ($this->sale_lib->will_be_out_of_stock_kit($current_item_kit_id, isset($quantity) ? $quantity - $before_quantity : 0))
					{
						$can_edit = FALSE;
					}
				}
	
				if (!$can_edit)
				{
					$data['error']=lang('sales_unable_to_add_item_out_of_stock');
				}
			}
		}
		else
		{
			$can_edit = FALSE;
			$data['error']=lang('sales_error_editing_item');
		}
	
		if($this->sale_lib->is_kit_or_item($line) == 'item')
		{
			if($this->sale_lib->out_of_stock($this->sale_lib->get_item_id($line)))
			{
				$data['warning'] = lang('sales_quantity_less_than_zero');
			}
				
			if ($this->sale_lib->below_cost_price_item($line, isset($price) ? $price : NULL, isset($discount) ? $discount : NULL, isset($cost_price)  ? $cost_price : NULL))
			{
				if ($this->config->item('do_not_allow_below_cost'))
				{
					$can_edit = FALSE;
					$data['error'] = lang('sales_selling_item_below_cost');
				}
				else
				{
					$data['warning'] = lang('sales_selling_item_below_cost');
				}
			}
		}
		elseif($this->sale_lib->is_kit_or_item($line) == 'kit')
		{
			if($this->sale_lib->out_of_stock_kit($this->sale_lib->get_kit_id($line)))
			{
				$data['warning'] = lang('sales_quantity_less_than_zero');
			}
	
			if ($this->sale_lib->below_cost_price_item($line, isset($price) ? $price : NULL, isset($discount) ? $discount : NULL, isset($cost_price)  ? $cost_price : NULL))
			{
				if ($this->config->item('do_not_allow_below_cost'))
				{
					$can_edit = FALSE;
					$data['error'] = lang('sales_selling_item_below_cost');
				}
				else
				{
					$data['warning'] = lang('sales_selling_item_below_cost');
				}
			}
		}
	
		if ($can_edit)
		{
			$this->sale_lib->edit_item(
				$line,
				isset($description) ? $description : NULL,
				isset($serialnumber) ? $serialnumber : NULL, 
				isset($quantity) ? $quantity : NULL,
				isset($discount) ? $discount : NULL,
				isset($price) ? $price: NULL,
				isset($cost_price) ? $cost_price: NULL,
				isset($measure) ? $measure: NULL
			);
		}
	
		$this->_reload($data);
	}
	
	function complete()
	{
		$this->load->helper('sale');
		///Make sure we have actually processed a transaction before compelting sale
		if (is_sale_integrated_cc_processing() && !$this->session->userdata('CC_SUCCESS'))
		{
			$this->_reload(array('error' => lang('sales_credit_card_processing_is_down')), false);
			return;
		}
		
		$data['is_sale'] = TRUE;
		$data['cart']=$this->sale_lib->get_cart();
		
		if (empty($data['cart']))
		{
			redirect('sales');
		}
			
		if (!$this->_payments_cover_total())
		{
			$this->_reload(array('error' => lang('sales_cannot_complete_sale_as_payments_do_not_cover_total')), false);
			return;
		}
		$tier_id = $this->sale_lib->get_selected_tier_id();
		$tier_info = $this->Tier->get_info($tier_id);
		$data['tier'] = $tier_info->name;
		$data['register_name'] = $this->Register->get_register_name($this->Employee->get_logged_in_employee_current_register_id());
		
		$data['subtotal']=$this->sale_lib->get_subtotal();
		$data['taxes']=$this->sale_lib->get_taxes();		
		$data['total']=$this->sale_lib->get_total();
		$data['receipt_title']= $this->config->item('override_receipt_title') ? $this->config->item('override_receipt_title') : lang('sales_receipt');
		$customer_id=$this->sale_lib->get_customer();

		// [4biz] Get customer balance before make orther
		if($customer_id != -1)
		{
			$cust_info=$this->Customer->get_info($customer_id);
			
			if ($cust_info->balance !=0)
			{
				$data['customer_balance_for_sale_before'] = $cust_info->balance;
			}
		}

		$employee_id=$this->Employee->get_logged_in_employee_info()->person_id;
		$sold_by_employee_id=$this->sale_lib->get_sold_by_employee_id();
		$data['comment'] = $this->sale_lib->get_comment();
		$data['show_comment_on_receipt'] = $this->sale_lib->get_comment_on_receipt();
		$emp_info=$this->Employee->get_info($employee_id);
		$sale_emp_info=$this->Employee->get_info($sold_by_employee_id);
		$data['payments']=$this->sale_lib->get_payments();
		$data['is_sale_cash_payment'] = $this->sale_lib->is_sale_cash_payment();
		$data['amount_change']=$this->sale_lib->get_amount_due() * -1;
		$data['balance']=$this->sale_lib->get_payment_amount(lang('common_store_account'));
		$data['employee']=$emp_info->first_name.' '.$emp_info->last_name.($sold_by_employee_id && $sold_by_employee_id != $employee_id ? '/'. $sale_emp_info->first_name.' '.$sale_emp_info->last_name: '');
		$data['ref_no'] = '';
		$data['auth_code'] = '';
		$data['discount_exists'] = $this->_does_discount_exists($data['cart']);
		
		$masked_account = $this->session->userdata('masked_account') ? $this->session->userdata('masked_account') : '';
		$card_issuer = $this->session->userdata('card_issuer') ? $this->session->userdata('card_issuer') : '';
		$auth_code = $this->session->userdata('auth_code') ? $this->session->userdata('auth_code') : '';
		$ref_no = $this->session->userdata('ref_no') ? $this->session->userdata('ref_no') : '';
		$cc_token = $this->session->userdata('cc_token') ? $this->session->userdata('cc_token') : '';
		$acq_ref_data = $this->session->userdata('acq_ref_data') ? $this->session->userdata('acq_ref_data') : '';
		$process_data = $this->session->userdata('process_data') ? $this->session->userdata('process_data') : '';
		$entry_method = $this->session->userdata('entry_method') ? $this->session->userdata('entry_method') : '';
		$aid = $this->session->userdata('aid') ? $this->session->userdata('aid') : '';
		$tvr = $this->session->userdata('tvr') ? $this->session->userdata('tvr') : '';
		$iad = $this->session->userdata('iad') ? $this->session->userdata('iad') : '';
		$tsi = $this->session->userdata('tsi') ? $this->session->userdata('tsi') : '';
		$arc = $this->session->userdata('arc') ? $this->session->userdata('arc') : '';
		$cvm = $this->session->userdata('cvm') ? $this->session->userdata('cvm') : '';
		$tran_type = $this->session->userdata('tran_type') ? $this->session->userdata('tran_type') : '';
		$application_label = $this->session->userdata('application_label') ? $this->session->userdata('application_label') : '';
				
		if ($masked_account)
		{
			if (count($this->sale_lib->get_payment_ids(lang('common_credit'))))
			{
				$cc_payment_id = current($this->sale_lib->get_payment_ids(lang('common_credit')));
				$cc_payment = $data['payments'][$cc_payment_id];
				$this->sale_lib->edit_payment($cc_payment_id, $cc_payment['payment_type'], $cc_payment['payment_amount'],$cc_payment['payment_date'], $masked_account, $card_issuer,$auth_code, $ref_no, $cc_token, $acq_ref_data, $process_data, $entry_method, $aid,$tvr,$iad, $tsi,$arc,$cvm,$tran_type,$application_label);
			
				//Make sure our payments has the latest change to masked_account
				$data['payments'] = $this->sale_lib->get_payments();
			}
		}
		
		$data['change_sale_date'] =$this->sale_lib->get_change_sale_date_enable() ?  $this->sale_lib->get_change_sale_date() : false;
		
		$old_date = $this->sale_lib->get_change_sale_id()  ? $this->Sale->get_info($this->sale_lib->get_change_sale_id())->row_array() : false;
		$old_date=  $old_date ? date(get_date_format().' '.get_time_format(), strtotime($old_date['sale_time'])) : date(get_date_format().' '.get_time_format());
		$data['transaction_time']= $this->sale_lib->get_change_sale_date_enable() ?  date(get_date_format().' '.get_time_format(), strtotime($this->sale_lib->get_change_sale_date())) : $old_date;
	
		$suspended_change_sale_id=$this->sale_lib->get_suspended_sale_id() ? $this->sale_lib->get_suspended_sale_id() : $this->sale_lib->get_change_sale_id() ;
				
		//If we have a suspended sale, update the date for the sale
		if ($this->sale_lib->get_suspended_sale_id() && $this->config->item('change_sale_date_when_completing_suspended_sale'))
		{
			$data['change_sale_date'] = date('Y-m-d H:i:s');
		}
		
		 
		$data['store_account_payment'] = ($sale_mode = $this->sale_lib->get_mode()) == 'store_account_payment' ? 1 : 0;
		
		//SAVE sale to database
		$sale_id_raw = $this->Sale->save($data['cart'], $customer_id, $employee_id, $sold_by_employee_id, $data['comment'],$data['show_comment_on_receipt'],$data['payments'], $suspended_change_sale_id, 0, $data['change_sale_date'], $data['balance'], $data['store_account_payment']); 
		$data['sale_id']=$this->config->item('sale_prefix').' '.$sale_id_raw;
		$data['sale_id_raw']=$sale_id_raw;
		
		if($customer_id!=-1)
		{
			$cust_info=$this->Customer->get_info($customer_id);
			$data['customer']=$cust_info->first_name.' '.$cust_info->last_name.($cust_info->company_name==''  ? '' :' - '.$cust_info->company_name).($cust_info->account_number==''  ? '' :' - '.$cust_info->account_number);
			$data['customer_address_1'] = $cust_info->address_1;
			$data['customer_address_2'] = $cust_info->address_2;
			$data['customer_city'] = $cust_info->city;
			$data['customer_state'] = $cust_info->state;
			$data['customer_zip'] = $cust_info->zip;
			$data['customer_country'] = $cust_info->country;
			$data['customer_phone'] = $cust_info->phone_number;
			$data['customer_email'] = $cust_info->email;			
			$data['customer_points'] = $cust_info->points;			
		   $data['sales_until_discount'] = $this->config->item('number_of_sales_for_discount') - $cust_info->current_sales_for_discount;
			
		}
		
		$this->Register_cart->add_data(array('can_email' => !$this->sale_lib->get_email_receipt(), 'sale_id' => $sale_id_raw),$this->Employee->get_logged_in_employee_current_register_id());		
		
		
		if($customer_id != -1)
		{
			$cust_info=$this->Customer->get_info($customer_id);
			
			if ($cust_info->balance !=0)
			{
				$data['customer_balance_for_sale'] = $cust_info->balance;
			}
		}
		
		//If we don't have any taxes, run a check for items so we don't show the price including tax on receipt
		if (empty($data['taxes']))
		{
			foreach(array_keys($data['cart']) as $key)
			{
				if (isset($data['cart'][$key]['item_id']))
				{
					$item_info = $this->Item->get_info($data['cart'][$key]['item_id']);
					if($item_info->tax_included)
					{
						$this->load->helper('items');
						$price_to_use = get_price_for_item_excluding_taxes($data['cart'][$key]['item_id'], $data['cart'][$key]['price']);
						$data['cart'][$key]['price'] = $price_to_use;
					}					
				}
				elseif (isset($data['cart'][$key]['item_kit_id']))
				{
					$item_info = $this->Item_kit->get_info($data['cart'][$key]['item_kit_id']);
					if($item_info->tax_included)
					{
						$price_to_use = get_price_for_item_kit_excluding_taxes($data['cart'][$key]['item_kit_id'], $data['cart'][$key]['price']);
						$data['cart'][$key]['price'] = $price_to_use;
					}					
				}
				
			}
			
		}
		
		if ($data['sale_id'] == $this->config->item('sale_prefix').' -1')
		{
			$data['error_message'] = '';
			$this->load->helper('sale');
			if (is_sale_integrated_cc_processing())
			{
				$this->sale_lib->change_credit_card_payments_to_partial();
				$data['error_message'].='<span class="text-success">'.lang('sales_credit_card_transaction_completed_successfully').'. </span><br /<br />';
			}
			$data['error_message'] .= '<span class="text-danger">'.lang('sales_transaction_failed').'</span>';
			$data['error_message'] .= '<br /><br />'.anchor('sales','&laquo; '.lang('sales_register'));
			$data['error_message'] .= '<br /><br />'.anchor('sales/complete',lang('common_try_again'). ' &raquo;');
		}
		else
		{			
			if ($this->sale_lib->get_email_receipt() && !empty($cust_info->email))
			{
				$this->load->library('email');
				$config['mailtype'] = 'html';				
				$this->email->initialize($config);
				$this->email->from($this->Location->get_info_for_key('email') ? $this->Location->get_info_for_key('email') : 'no-reply@mg.4biz.vn', $this->config->item('company'));
				$this->email->to($cust_info->email); 

				$this->email->subject(lang('sales_receipt'));
				$this->email->message($this->load->view("sales/receipt_email",$data, true));	
				$this->email->send();
			}
			
			if ($this->session->userdata('CC_SUCCESS'))
			{
				$credit_card_processor = $this->_get_cc_processor();
		
				if ($credit_card_processor)
				{
					$cc_processor_class_name = strtoupper(get_class($credit_card_processor));
			
					if ($cc_processor_class_name =='MERCURYEMVUSBPROCESSOR')
					{
						$data['reset_params'] = $credit_card_processor->get_emv_pad_reset_params();
					}
				}		
			}
		}

		if ($data['sale_id'] != $this->config->item('sale_prefix').' -1')
		{
			$this->sale_lib->clear_all();
		}
		 if($sale_mode=='sale')$data['type']='1';
		 elseif($sale_mode=='return')$data['type']='0';
		// [4biz] switch to correct view
		$typeOfView = $this->getTypeOfOrder($data['payments'], $sale_mode);
		$data['pdf_block_html'] = $this->load->view('sales/partials/' . $typeOfView, $data, TRUE);

		$this->load->view("sales/receipt",$data);
	}

	protected function getTypeOfOrder($payments = array(), $mode = '', $suspended = 0,$fulfillment=0)
	{
            if($fulfillment==0){
		$typeOfView = 'order';
		foreach ($payments as $payment) {
			if( $payment['payment_type'] == lang('common_store_account') )
			{
				$typeOfView = 'order_debit';
			}
		}

		if($mode == 'return')
		{
			$typeOfView = 'order_return';
		}
                if($mode == 'store_account_payment')
		{
			$typeOfView = 'order_liabilities';
		}

		if($suspended == 1)
		{
			$typeOfView = 'order_booked';
		} elseif ($suspended == 2) {
			$typeOfView = 'order_show_price';
		}
            }else{
		$typeOfView = 'order_fulfillment';
		foreach ($payments as $payment) {
			if( $payment['payment_type'] == lang('common_store_account') )
			{
				$typeOfView = 'order_debit_fulfillment';
			}
		}

		if($mode == 'return')
		{
			$typeOfView = 'order_return_fulfillment';
		}
                if($mode == 'store_account_payment')
		{
			$typeOfView = 'order_liabilities_fulfillment';
		}

		if($suspended == 1)
		{
			$typeOfView = 'order_booked_fulfillment';
		} elseif ($suspended == 2) {
			$typeOfView = 'order_show_price_fulfillment';
		}
                
            }
		
		return $typeOfView;
	}

	function receipt($sale_id)
	{
            $this->load->helper('sale');
            $fulfillment = $this->input->get('fulfillment');
            $type        = $this->input->get('type');
		//Before changing the sale session data, we need to save our current state in case they were in the middle of a sale
		$this->sale_lib->save_current_sale_state();
		
		$data['is_sale'] = FALSE;
		$sale_info = $this->Sale->get_info($sale_id)->row_array();
               
		$this->sale_lib->clear_all();
		$this->sale_lib->copy_entire_sale($sale_id, true);
		$data['cart']=$this->sale_lib->get_cart();

                 $customer_id=$this->sale_lib->get_customer();

		// [4biz] Get customer balance before make orther
		if($customer_id != -1)
		{
			$cust_info=$this->Customer->get_info($customer_id);
			
			if ($cust_info->balance !=0)
			{
				$data['customer_balance_for_sale_before'] = $cust_info->balance;
			}
		}
		$data['payments']=$this->sale_lib->get_payments();
		$data['is_sale_cash_payment'] = $this->sale_lib->is_sale_cash_payment();
		$data['show_payment_times'] = TRUE;
		$data['signature_file_id'] = $sale_info['signature_image_id'];
		
		$tier_id = $sale_info['tier_id'];
		$tier_info = $this->Tier->get_info($tier_id);
		$data['tier'] = $tier_info->name;
		$data['register_name'] = $this->Register->get_register_name($sale_info['register_id']);
		$data['override_location_id'] = $sale_info['location_id'];
		$data['deleted'] = $sale_info['deleted'];

		$data['subtotal']=$this->sale_lib->get_subtotal($sale_id);
		$data['taxes']=$this->sale_lib->get_taxes($sale_id);
		$data['total']=$this->sale_lib->get_total($sale_id);
		$data['receipt_title']= $this->config->item('override_receipt_title') ? $this->config->item('override_receipt_title') : lang('sales_receipt');
		$data['comment'] = $this->Sale->get_comment($sale_id);
		$data['show_comment_on_receipt'] = $this->Sale->get_comment_on_receipt($sale_id);
		$data['transaction_time']= date(get_date_format().' '.get_time_format(), strtotime($sale_info['sale_time']));
		$customer_id=$this->sale_lib->get_customer();
		
		$emp_info=$this->Employee->get_info($sale_info['employee_id']);
		$sold_by_employee_id=$sale_info['sold_by_employee_id'];
		$sale_emp_info=$this->Employee->get_info($sold_by_employee_id);
		$data['payment_type']=$sale_info['payment_type'];
		$data['amount_change']=$this->sale_lib->get_amount_due($sale_id) * -1;
		$data['employee']=$emp_info->first_name.' '.$emp_info->last_name.($sold_by_employee_id && $sold_by_employee_id != $sale_info['employee_id'] ? '/'. $sale_emp_info->first_name.' '.$sale_emp_info->last_name: '');
		$data['ref_no'] = $sale_info['cc_ref_no'];
		$data['auth_code'] = $sale_info['auth_code'];
		$data['discount_exists'] = $this->_does_discount_exists($data['cart']);
		if($customer_id!=-1)
		{
			$cust_info=$this->Customer->get_info($customer_id);
			$data['customer']=$cust_info->first_name.' '.$cust_info->last_name.($cust_info->company_name==''  ? '' :' - '.$cust_info->company_name).($cust_info->account_number==''  ? '' :' - '.$cust_info->account_number);
			$data['customer_address_1'] = $cust_info->address_1;
			$data['customer_address_2'] = $cust_info->address_2;
			$data['customer_city'] = $cust_info->city;
			$data['customer_state'] = $cust_info->state;
			$data['customer_zip'] = $cust_info->zip;
			$data['customer_country'] = $cust_info->country;
			$data['customer_phone'] = $cust_info->phone_number;
			$data['customer_email'] = $cust_info->email;
			$data['customer_points'] = $cust_info->points;
		   $data['sales_until_discount'] = $this->config->item('number_of_sales_for_discount') - $cust_info->current_sales_for_discount;
			
			if ($cust_info->balance !=0)
			{
				$data['customer_balance_for_sale'] = $cust_info->balance;
			}
		}		
		$data['sale_id']=$this->config->item('sale_prefix').' '.$sale_id;
		$data['sale_id_raw']=$sale_id;
		$data['store_account_payment'] = FALSE;
		
		foreach($data['cart'] as $item)
		{
			if ($item['name'] == lang('sales_store_account_payment'))
			{
				$data['store_account_payment'] = TRUE;
				break;
			}
		}
		
		if ($sale_info['suspended'] > 0)
		{
			if ($sale_info['suspended'] == 1)
			{
				$data['sale_type'] = lang('common_layaway');
			}
			elseif ($sale_info['suspended'] == 2)
			{
				$data['sale_type'] = lang('common_estimate');				
			}
		}
		$sale_mode =$this->sale_lib->get_mode();
                if($type=='0')$sale_mode = 'return';
                elseif($type=='1') $sale_mode = 'sales';
                if(isset($sale_info)&&$sale_info['store_account_payment'] ==1)$sale_mode = 'store_account_payment';
		// [4biz] switch to correct view
		$typeOfView = $this->getTypeOfOrder($data['payments'], $sale_mode, $sale_info['suspended'],$fulfillment);
		$data['pdf_block_html'] = $this->load->view('sales/partials/' . $typeOfView, $data, TRUE);
		
		$this->load->view("sales/receipt",$data);
		$this->sale_lib->clear_all();
		
		//Restore previous state saved above
		$this->sale_lib->restore_current_sale_state();
	}

	function suspend($suspend_type = 1)
	{
		$data['cart']=$this->sale_lib->get_cart();
		$data['subtotal']=$this->sale_lib->get_subtotal();
		$data['taxes']=$this->sale_lib->get_taxes();
		$data['total']=$this->sale_lib->get_total();
		$data['receipt_title']= $this->config->item('override_receipt_title') ? $this->config->item('override_receipt_title') : lang('sales_receipt');
		$data['transaction_time']= date(get_date_format().' '.get_time_format());
		$customer_id=$this->sale_lib->get_customer();
		$employee_id=$this->Employee->get_logged_in_employee_info()->person_id;
		$sold_by_employee_id=$this->sale_lib->get_sold_by_employee_id();
		$comment = $this->sale_lib->get_comment();
		$show_comment_on_receipt = $this->sale_lib->get_comment_on_receipt();
		$emp_info=$this->Employee->get_info($employee_id);
		//Alain Multiple payments
		$data['payments']=$this->sale_lib->get_payments();
		$data['amount_change']=$this->sale_lib->get_amount_due() * -1;
		$data['balance']=$this->sale_lib->get_payment_amount(lang('common_store_account'));
		$data['employee']=$emp_info->first_name.' '.$emp_info->last_name;

		if($customer_id!=-1)
		{
			$cust_info=$this->Customer->get_info($customer_id);
			$data['customer']=$cust_info->first_name.' '.$cust_info->last_name.($cust_info->company_name==''  ? '' :' - '.$cust_info->company_name).($cust_info->account_number==''  ? '' :' - '.$cust_info->account_number);
		}

		$total_payments = 0;

		foreach($data['payments'] as $payment)
		{
			$total_payments += $payment['payment_amount'];
		}
		
		$sale_id = $this->sale_lib->get_suspended_sale_id();
		//SAVE sale to database
		$sale_id = $this->Sale->save($data['cart'], $customer_id,$employee_id, $sold_by_employee_id, $comment,$show_comment_on_receipt,$data['payments'], $sale_id, $suspend_type,$this->config->item('change_sale_date_when_suspending') ? date('Y-m-d H:i:s') : FALSE, $data['balance']);
		
		$data['sale_id']=$this->config->item('sale_prefix').' '.$sale_id;
		if ($data['sale_id'] == $this->config->item('sale_prefix').' -1')
		{
			$this->_reload(array('error' => lang('sales_transaction_failed')));
			return;
		}
		$this->sale_lib->clear_all();
		
		if ($this->config->item('show_receipt_after_suspending_sale'))
		{
			redirect('sales/receipt/'.$sale_id);
		}
		else
		{
			$this->_reload(array('success' => lang('sales_successfully_suspended_sale')));
		}
	}
}
?>
