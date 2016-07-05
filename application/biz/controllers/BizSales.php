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
	
	public function set_sale_delivery_date()
	{
		$delivery_date = $this->input->post("delivery_date");
		
		$this->sale_lib->set_delivery_date($delivery_date);
		
		$this->_reload($data);
	}
	
	function deliverer_search()
	{
		//allow parallel searchs to improve performance.
		session_write_close();
		$suggestions = $this->Employee->get_search_suggestions($this->input->get('term'),100);
		echo json_encode($suggestions);
	}
	
	function select_deliverer()
	{
		$data = array();
		$deliverer_id = $this->input->post("deliverer");
			
		if ($this->Employee->exists($deliverer_id))
		{
			$this->sale_lib->set_deliverer($deliverer_id);
		} else
		{
			$data['error']=lang('sales_unable_to_add_customer');
		}
		$this->_reload($data);
	}
	
	
	function change_sale($sale_id)
	{
		$this->check_action_permission('edit_sale');
		$this->sale_lib->clear_all();
		$this->sale_lib->set_change_sale_id($sale_id);
		$this->sale_lib->copy_entire_sale($sale_id);
		if ($this->Location->get_info_for_key('enable_credit_card_processing'))
		{
			$this->sale_lib->change_credit_card_payments_to_partial();
		}
		$this->_reload(array(), false);
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
		
		$extraData['deliverer'] = $this->sale_lib->get_deliverer();
		$extraData['delivery_date'] = $this->sale_lib->get_delivery_date();
		
		//SAVE sale to database
		$sale_id_raw = $this->Sale->save($data['cart'], $customer_id, $employee_id, $sold_by_employee_id, $data['comment'],$data['show_comment_on_receipt'],$data['payments'], $suspended_change_sale_id, 0, $data['change_sale_date'], $data['balance'], $data['store_account_payment'], $extraData); 
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
		$typeOfViewFix = $typeOfView;
		if($this->config->item('config_sales_receipt_pdf_size')=='a8'&&  !strpos($typeOfView, '_fulfillment'))$typeOfViewFix = $typeOfView .'_fulfillment';
		if($fulfillment!=NULL && $fulfillment==0)$typeOfViewFix = $typeOfView;
		$data['pdf_block_html'] = $this->load->view('sales/partials/' . $typeOfViewFix, $data, TRUE);

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
		$typeOfViewFix = $typeOfView;
		if($this->config->item('config_sales_receipt_pdf_size')=='a8'&&  !strpos($typeOfView, '_fulfillment'))$typeOfViewFix = $typeOfView .'_fulfillment';
		if($fulfillment!=NULL && $fulfillment==0)$typeOfViewFix = $typeOfView;
		$data['pdf_block_html'] = $this->load->view('sales/partials/' . $typeOfViewFix, $data, TRUE);
		
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
		
		$extraData['deliverer'] = $this->sale_lib->get_deliverer();
		$extraData['delivery_date'] = $this->sale_lib->get_delivery_date();
		
		//SAVE sale to database
		$sale_id = $this->Sale->save(
				$data['cart'],
				$customer_id,
				$employee_id,
				$sold_by_employee_id,
				$comment,
				$show_comment_on_receipt,
				$data['payments'],
				$sale_id,
				$suspend_type,
				$this->config->item('change_sale_date_when_suspending') ? date('Y-m-d H:i:s') : FALSE,
				$data['balance'], 0 , $extraData);
		
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
	function suspended()
	{
		$data = array();
		$data['suspended_sales'] = $this->Sale->get_all_suspended();
		$this->load->view('sales/suspended', $data);
	}
	
	function report_quotes($sale_id) 
	{
		$data = array();
		$data['sale_id'] = $sale_id;
		$data['list_quotes'] = $this->Customer->get_list_template_quotes_contract(2);
		$customer_id = $this->sale_lib->get_customer();
		$cust_info = $this->Customer->get_info($customer_id);
		$data['email'] = $cust_info->email;

		$this->load->view('sales/form_report_quotes', $data);
	}
	
	function do_make_quotes($sale_id) {
		$id_quotes_contract = $this->input->post("quotes_id");
		$data['info_quotes_contract'] = $this->Customer->get_info_quotes_contract($id_quotes_contract);
		$data['is_sale'] = FALSE;
		$sale_info = $this->Sale->get_info($sale_id)->row_array();
		$this->sale_lib->copy_entire_sale($sale_id);
		$data['cart'] = $this->sale_lib->get_cart();
		$data['payments'] = $this->sale_lib->get_payments();
		$data['subtotal'] = $this->sale_lib->get_subtotal();
		$data['taxes'] = $this->sale_lib->get_taxes($sale_id);
		$data['total'] = $this->sale_lib->get_total($sale_id);
		$data['receipt_title'] = lang('sales_receipt');
		$data['comment'] = $this->Sale->get_comment($sale_id);
		$data['show_comment_on_receipt'] = $this->Sale->get_comment_on_receipt($sale_id);
		$data['transaction_time'] = date(get_date_format() . ' ' . get_time_format(), strtotime($sale_info['sale_time']));
		$customer_id = $this->sale_lib->get_customer();
		$emp_info = $this->Employee->get_info($sale_info['employee_id']);
// 		$info_empss = $this->Employee->get_info($sale_info['employees_id']);
// 		$data['employees_id'] = $info_empss->first_name . ' ' . $info_empss->last_name;
// 		$data['phone_number1'] = $info_empss->phone_number;
// 		$data['email1'] = $info_empss->email;
		$data['payment_type'] = $sale_info['payment_type'];
		$data['amount_change'] = $this->sale_lib->get_amount_due($sale_id) * -1;
		$data['employee'] = $emp_info->first_name . ' ' . $emp_info->last_name;
		$data['phone_number'] = $emp_info->phone_number;
		$data['email'] = $emp_info->email;
		$data['ref_no'] = $sale_info['cc_ref_no'];
		$this->load->helper('string');
		$data['payment_type'] = str_replace(array('<sup>VNĐ</sup><br />', ''), ' .VNĐ', $sale_info['payment_type']);
		$data['amount_due'] = $this->sale_lib->get_amount_due();
		
		if ($customer_id != -1) {
			$cust_info = $this->Customer->get_info($customer_id);
			$data['customer'] = $cust_info->first_name . ' ' . $cust_info->last_name;
			$data['cus_name'] = $cust_info->company_name == '' ? '' : $cust_info->company_name;
			$data['code_tax'] = $cust_info->code_tax ? $cust_info->code_tax : '';
			$data['address'] = $cust_info->address_1;
			$data['account_number'] = $cust_info->account_number;
		}
		$data['sale_id'] = $sale_id;
		$type = $this->input->post('quotes_type');
// 		$cat_baogia = $this->input->post("sales_quotes_type");
		$data['word'] = $type;
		$data['cat_baogia'] = '';
		
            $file_name = "BG_" . $sale_id . "_" . str_replace(" ", "", replace_character($data['customer'])) . "_" . date('dmYHis') . ".doc";
			if (!file_exists(APPPATH. '/excel_materials')) {
			    mkdir(APPPATH. '/excel_materials/', 0777, true);
			}
            $fp = fopen(APPPATH . "/excel_materials/" . $file_name, 'w+');
            $arr_item = array();
            $arr_service = array();
            foreach ($data['cart'] as $line => $val) {
                if ($val['item_id']) {
                    $info_item = $this->Item->get_info($val['item_id']);
                    if ($info_item->service == 0) {
                        $arr_item[] = array(
                            'item_id' => $val['item_id'],
                            'line' => $line,
                            'name' => $val['name'],
                            'item_number' => $val['item_number'],
                            'description' => $val['description'],
                            'serialnumber' => $val['serialnumber'],
                            'allow_alt_description' => $val['allow_alt_description'],
                            'is_serialized' => $val['is_serialized'],
                            'quantity' => $val['quantity'],
                            'stored_id' => $val['stored_id'],
                            'discount' => $val['discount'],
                            'price' => $val['price'],
                            'price_rate' => $val['price_rate'],
                            'taxes' => $val['taxes'],
                            'unit' => $val['unit']
                        );
                    } else {
                        $arr_service[] = array(
                            'item_id' => $val['item_id'],
                            'line' => $line,
                            'name' => $val['name'],
                            'item_number' => $val['item_number'],
                            'description' => $val['description'],
                            'serialnumber' => $val['serialnumber'],
                            'allow_alt_description' => $val['allow_alt_description'],
                            'is_serialized' => $val['is_serialized'],
                            'quantity' => $val['quantity'],
                            'stored_id' => $val['stored_id'],
                            'discount' => $val['discount'],
                            'price' => $val['price'],
                            'price_rate' => $val['price_rate'],
                            'taxes' => $val['taxes'],
                            'unit' => $val['unit']
                        );
                    }
                } else {
                    $arr_item[] = array(
                        'pack_id' => $val['pack_id'],
                        'line' => $val['line'],
                        'pack_number' => $val['pack_number'],
                        'name' => $val['name'],
                        'description' => $val['description'],
                        'quantity' => $val['quantity'],
                        'discount' => $val['discount'],
                        'price' => $val['price'],
                        'taxes' => $val['taxes'],
                        'unit' => $val['unit']
                    );
                }
            }
            $str = "";
            $str .= "<table style='border-collapse: collapse; width: 100%; margin: 0px auto; font-size: 14px;'>";
            $str .= "<tr>";
            $str .= "<th style='text-align: center; border: 1px solid #000000; padding: 10px 0px;'>STT</th>";
            $str .= "<th style='text-align: center; border: 1px solid #000000; padding: 10px 0px;'>Mã/Tên HH, DC, Gói SP</th>";
            $str .= "<th style='text-align: center; border: 1px solid #000000; padding: 10px 0px;' colspan='2'>Mô tả/Hình ảnh</th>";
            $str .= "<th style='text-align: center; border: 1px solid #000000; padding: 10px 0px;'>ĐVT</th>";
            $str .= "<th style='text-align: center; border: 1px solid #000000; padding: 10px 0px;'>SL</th>";
            $str .= "<th style='text-align: center; border: 1px solid #000000; padding: 10px 0px;'>Đơn giá</th>";
            $str .= "<th style='text-align: center; border: 1px solid #000000; padding: 10px 0px;'>CK(%)</th>";
            $str .= "<th style='text-align: center; border: 1px solid #000000; padding: 10px 0px;'>Thuế(%)</th>";
            $str .= "<th style='text-align: center; border: 1px solid #000000; padding: 10px 0px;'>Thành tiền</th>";
            $str .= "</tr>";
            $str .= "<tr>";
            $str .= "<td style='text-align: center; border: 1px solid #000000; font-style: italic; padding: 5px 0px; width: 5%;'>(No.)</td>";
            $str .= "<td style='text-align: center; border: 1px solid #000000; font-style: italic; padding: 5px 0px; width: 17.5%;'>(Code/Name)</td>";
            $str .= "<td style='text-align: center; border: 1px solid #000000; font-style: italic; padding: 5px 0px; width: 17.5%;'>(Description)</td>";
            $str .= "<td style='text-align: center; border: 1px solid #000000; font-style: italic; padding: 5px 0px; width: 10%;'>(Images)</td>";
            $str .= "<td style='text-align: center; border: 1px solid #000000; font-style: italic; padding: 5px 0px; width: 10%;'>(Units)</td>";
            $str .= "<td style='text-align: center; border: 1px solid #000000; font-style: italic; padding: 5px 0px; width: 10%;'>(Quantity)</td>";
            $str .= "<td style='text-align: center; border: 1px solid #000000; font-style: italic; padding: 5px 0px; width: 10%;'>(Unit price)</td>";
            $str .= "<td style='text-align: center; border: 1px solid #000000; font-style: italic; padding: 5px 0px; width: 5%;'>(Discount)</td>";
            $str .= "<td style='text-align: center; border: 1px solid #000000; font-style: italic; padding: 5px 0px; width: 5%;'>(Tax)</td>";
            $str .= "<td style='text-align: center; border: 1px solid #000000; font-style: italic; padding: 5px 0px; width: 10%;'>(Amount)</td>";
            $str .= "</tr>";
            $stt = 1;
            $total = 0;
            if ($cat_baogia == 1) {
                foreach ($arr_item as $line => $item) {
                    if ($item['pack_id']) {
                        $info_pack = $this->Pack->get_info($item['pack_id']);
                        $pack_item = $this->Pack_items->get_info($item['pack_id']);
                        $info_sale_pack = $this->Sale->get_sale_pack_by_sale_pack($sale_id, $item['pack_id']);
                        //$info_unit = $this->Unit->get_info($info_sale_pack->unit_pack);
                        $thanh_tien = $item['quantity'] * $item['price'] - $item['quantity'] * $item['price'] * $item['discount'] / 100 + ($item['quantity'] * $item['price'] - $item['quantity'] * $item['price'] * $item['discount'] / 100) * $item['taxes'] / 100;
                        $str .= "<tr>";
                        $str .= "<td style='text-align: center; border: 1px solid #000000; padding: 10px 5px'>" . $stt . "</td>";
                        $str .= "<td style='border: 1px solid #000000; padding: 10px 5px'>";
                        $str .= "<strong>" . $info_pack->pack_number . "/" . $info_pack->name . "(Gói SP)</strong><br>";
                        foreach ($pack_item as $val) {
                            $info_item = $this->Item->get_info($val->item_id);
                            $str .= "<p>- <strong>" . $info_item->item_number . "</strong>/" . $info_item->name . "</p>";
                        }

                        $str .= "</td>";
                        $str .= "<td style='border: 1px solid #000000; padding: 10px 5px'>" . $item['description'] . "</td>";
                        $str .= "<td style='text-align: center; border: 1px solid #000000; padding: 10px 5px'>";
                        if ($info_pack->images) {
                            $str .= "<img src='" . base_url('packs/' . $info_pack->images) . "' style='width:45px; height:45px'/>";
                        }
                        $str .= "</td>";
                        $str .= "<td style='border: 1px solid #000000; padding: 10px 5px'>" . ' ' . "</td>";
                        $str .= "<td style='text-align: right; border: 1px solid #000000; padding: 10px 5px'>" . format_quantity($item['quantity']) . "</td>";
                        $str .= "<td style='text-align: right; border: 1px solid #000000; padding: 10px 5px'>" . number_format($item['price']) . "</td>";
                        $str .= "<td style='text-align: right; border: 1px solid #000000; padding: 10px 5px'>" . number_format($item['discount']) . "</td>";
                        $str .= "<td style='text-align: right; border: 1px solid #000000; padding: 10px 5px'>" . number_format($item['taxes']) . "</td>";
                        $str .= "<td style='text-align: right; border: 1px solid #000000; padding: 10px 5px'>" . number_format($thanh_tien) . "</td>";
                        $str .= "</tr>";
                        $stt++;
                        $total += $thanh_tien;
                    } else {
                        $info_item = $this->Item->get_info($item['item_id']);
                        $info_sale_item = $this->Sale->get_sale_item_by_sale_item($sale_id, $item['item_id']);
                        //$info_unit = $this->Unit->get_info($info_sale_item->unit_item);
                        $thanh_tien = $item['quantity'] * ($item['unit'] == 'unit_from' ? $item['price_rate'] : $item['price']) - $item['quantity'] * ($item['unit'] == 'unit_from' ? $item['price_rate'] : $item['price']) * $item['discount'] / 100 + ($item['quantity'] * ($item['unit'] == 'unit_from' ? $item['price_rate'] : $item['price']) - $item['quantity'] * ($item['unit'] == 'unit_from' ? $item['price_rate'] : $item['price']) * $item['discount'] / 100) * $item['taxes'] / 100;
                        $str .= "<tr>";
                        $str .= "<td style='text-align: center; border: 1px solid #000000; padding: 10px 5px'>" . $stt . "</td>";
                        $str .= "<td style='border: 1px solid #000000; padding: 10px 5px'><strong>" . $info_item->item_number . "</strong>/" . $info_item->name . "</td>";
                        $str .= "<td style='border: 1px solid #000000; padding: 10px 5px'>" . $item['description'] . "</td>";
                        $str .= "<td style='text-align: center; border: 1px solid #000000; padding: 10px 5px'>";
                        if ($info_item->images) {
                            $str .= "<img src='" . base_url('item/' . $info_item->images) . "' style='width:45px; height:45px'/>";
                        }
                        $str .= "</td>";
                        $str .= "<td style='border: 1px solid #000000; padding: 10px 5px'>" . ' ' . "</td>";
                        $str .= "<td style='text-align: right; border: 1px solid #000000; padding: 10px 5px'>" . format_quantity($item['quantity']) . "</td>";
                        $str .= "<td style='text-align: right; border: 1px solid #000000; padding: 10px 5px'>" . number_format(($item['unit'] == 'unit_from' ? $item['price_rate'] : $item['price'])) . "</td>";
                        $str .= "<td style='text-align: right; border: 1px solid #000000; padding: 10px 5px'>" . number_format($item['discount']) . "</td>";
                        $str .= "<td style='text-align: right; border: 1px solid #000000; padding: 10px 5px'>" . number_format($item['taxes']) . "</td>";
                        $str .= "<td style='text-align: right; border: 1px solid #000000; padding: 10px 5px'>" . number_format($thanh_tien) . "</td>";
                        $str .= "</tr>";
                        $stt++;
                        $total += $thanh_tien;
                    }
                }
            } else if ($cat_baogia == 2) {
                foreach ($arr_service as $line => $item) {
                    $info_item = $this->Item->get_info($item['item_id']);
                    $info_sale_item = $this->Sale->get_sale_item_by_sale_item($sale_id, $item['item_id']);
                    //$info_unit = $this->Unit->get_info($info_sale_item->unit_item);
                    $thanh_tien = $item['quantity'] * $item['price'] - $item['quantity'] * $item['price'] * $item['discount'] / 100 + ($item['quantity'] * $item['price'] - $item['quantity'] * $item['price'] * $item['discount'] / 100) * $item['taxes'] / 100;
                    $str .= "<tr>";
                    $str .= "<td style='text-align: center; border: 1px solid #000000; padding: 10px 5px'>" . $stt . "</td>";
                    $str .= "<td style='border: 1px solid #000000; padding: 10px 5px'><strong>" . $info_item->item_number . "</strong/" . $info_item->name . "</td>";
                    $str .= "<td style='border: 1px solid #000000; padding: 10px 5px'>" . $item['description'] . "</td>";
                    $str .= "<td style='text-align: center; border: 1px solid #000000; padding: 10px 5px'>";
                    if ($info_item->images) {
                        $str .= "<img src='" . base_url('item/' . $info_item->images) . "' style='width:45px; height:45px'/>";
                    }
                    $str .= "</td>";
                    $str .= "<td style='border: 1px solid #000000; padding: 10px 5px'>" . ' ' . "</td>";
                    $str .= "<td style='text-align: right; border: 1px solid #000000; padding: 10px 5px'>" . format_quantity($item['quantity']) . "</td>";
                    $str .= "<td style='text-align: right; border: 1px solid #000000; padding: 10px 5px'>" . number_format($item['price']) . "</td>";
                    $str .= "<td style='text-align: right; border: 1px solid #000000; padding: 10px 5px'>" . number_format($item['discount']) . "</td>";
                    $str .= "<td style='text-align: right; border: 1px solid #000000; padding: 10px 5px'>" . number_format($item['taxes']) . "</td>";
                    $str .= "<td style='text-align: right; border: 1px solid #000000; padding: 10px 5px'>" . number_format($thanh_tien) . "</td>";
                    $str .= "</tr>";
                    $stt ++;
                    $total += $thanh_tien;
                }
            } else {
                foreach ($data['cart'] as $line => $item) {
                    if ($item['pack_id']) {
                        $info_pack = $this->Pack->get_info($item['pack_id']);
                        $pack_item = $this->Pack_items->get_info($item['pack_id']);
                        $info_sale_pack = $this->Sale->get_sale_pack_by_sale_pack($sale_id, $item['pack_id']);
                        //$info_unit = $this->Unit->get_info($info_sale_pack->unit_pack);
                        $thanh_tien = $item['quantity'] * $item['price'] - $item['quantity'] * $item['price'] * $item['discount'] / 100 + ($item['quantity'] * $item['price'] - $item['quantity'] * $item['price'] * $item['discount'] / 100) * $item['taxes'] / 100;
                        $str .= "<tr>";
                        $str .= "<td style='text-align: center; border: 1px solid #000000; padding: 10px 5px'>" . $stt . "</td>";
                        $str .= "<td style='border: 1px solid #000000; padding: 10px 5px'>";
                        $str .= "<strong>" . $info_pack->pack_number . "/" . $info_pack->name . "(Gói SP)</strong><br>";
                        foreach ($pack_item as $val) {
                            $info_item = $this->Item->get_info($val->item_id);
                            $str .= "<p>- <strong>" . $info_item->item_number . "</strong>/" . $info_item->name . "</p>";
                        }

                        $str .= "</td>";
                        $str .= "<td style='border: 1px solid #000000; padding: 10px 5px'>" . $item['description'] . "</td>";
                        $str .= "<td style='text-align: center; border: 1px solid #000000; padding: 10px 5px'>";
                        if ($info_pack->images) {
                            $str .= "<img src='" . base_url('packs/' . $info_pack->images) . "' width='20px' height='20px'/>";
                        }
                        $str .= "</td>";
                        $str .= "<td style='border: 1px solid #000000; padding: 10px 5px'>" . ' ' . "</td>";
                        $str .= "<td style='text-align: right; border: 1px solid #000000; padding: 10px 5px'>" . format_quantity($item['quantity']) . "</td>";
                        $str .= "<td style='text-align: right; border: 1px solid #000000; padding: 10px 5px'>" . number_format($item['price']) . "</td>";
                        $str .= "<td style='text-align: right; border: 1px solid #000000; padding: 10px 5px'>" . number_format($item['discount']) . "</td>";
                        $str .= "<td style='text-align: right; border: 1px solid #000000; padding: 10px 5px'>" . number_format($item['taxes']) . "</td>";
                        $str .= "<td style='text-align: right; border: 1px solid #000000; padding: 10px 5px'>" . number_format($thanh_tien) . "</td>";
                        $str .= "</tr>";
                        $total += $thanh_tien;
                    } else {
                        $info_item = $this->Item->get_info($item['item_id']);
                        $info_sale_item = $this->Sale->get_sale_item_by_sale_item($sale_id, $item['item_id']);
                        //$info_unit = $this->Unit->get_info($info_sale_item->unit_item);
                        $thanh_tien = $item['quantity'] * ($item['unit'] == 'unit_from' ? $item['price_rate'] : $item['price']) - $item['quantity'] * ($item['unit'] == 'unit_from' ? $item['price_rate'] : $item['price']) * $item['discount'] / 100 + ($item['quantity'] * ($item['unit'] == 'unit_from' ? $item['price_rate'] : $item['price']) - $item['quantity'] * ($item['unit'] == 'unit_from' ? $item['price_rate'] : $item['price']) * $item['discount'] / 100) * $item['taxes'] / 100;
                        $str .= "<tr>";
                        $str .= "<td style='text-align: center; border: 1px solid #000000; padding: 10px 5px'>" . $stt . "</td>";
                        $str .= "<td style='border: 1px solid #000000; padding: 10px 5px'><strong>" . $info_item->item_number . "</strong>/" . $info_item->name . "</td>";
                        $str .= "<td style='border: 1px solid #000000; padding: 10px 5px'>" . $item['description'] . "</td>";
                        $str .= "<td style='text-align: center; border: 1px solid #000000; padding: 10px 5px'>";
                        if ($info_item->images) {
                            $str .= "<img src='" . base_url('item/' . $info_item->images) . "' width='20px' height='20px'/>";
                        }
                        $str .= "</td>";
                        $str .= "<td style='border: 1px solid #000000; padding: 10px 5px'>" . ' ' . "</td>";
                        $str .= "<td style='text-align: right; border: 1px solid #000000; padding: 10px 5px'>" . format_quantity($item['quantity']) . "</td>";
                        $str .= "<td style='text-align: right; border: 1px solid #000000; padding: 10px 5px'>" . number_format(($item['unit'] == 'unit_from' ? $item['price_rate'] : $item['price'])) . "</td>";
                        $str .= "<td style='text-align: right; border: 1px solid #000000; padding: 10px 5px'>" . number_format($item['discount']) . "</td>";
                        $str .= "<td style='text-align: right; border: 1px solid #000000; padding: 10px 5px'>" . number_format($item['taxes']) . "</td>";
                        $str .= "<td style='text-align: right; border: 1px solid #000000; padding: 10px 5px'>" . number_format($thanh_tien) . "</td>";
                        $str .= "</tr>";
                        $total += $thanh_tien;
                    }
                    $stt++;
                }
            }
            $str .= "<tr>";
            $str .= "<td colspan='5' style='text-align: center; border: 1px solid #000000; padding: 10px 5px; font-weight: bold'>Tổng</td>";
            $str .= "<td colspan='5' style='text-align: right; border: 1px solid #000000; padding: 10px 5px; font-weight: bold'>" . number_format($total) . "</td>";
            $str .= "</tr>";
            $str .= "</table>";
//             $str .= "<p>Tổng giá trị (Bằng chữ): <strong><em>" . $total . "</em></strong></p>";
            $str .= "<p>Tổng giá trị (Bằng chữ): <strong><em>" . $total . "</em></strong></p>";
            $content1 = "<html>";
            $content1 .= "<meta charset='utf-8'/>";
            $content1 .= "<body style='font-size: 100% !important'>";
            $content1 .= $data['info_quotes_contract']->content_quotes_contract;
            $content1 .= "</body>";
            $content1 .= "</html>";
            $info_sale = $this->Sale->get_info_sale_order($sale_id);
            $d = $info_sale->date_debt != '0000-00-00' ? date('d', strtotime($info_sale->date_debt)) : '...';
            $m = $info_sale->date_debt != '0000-00-00' ? date('m', strtotime($info_sale->date_debt)) : '...';
            $y = $info_sale->date_debt != '0000-00-00' ? date('Y', strtotime($info_sale->date_debt)) : '...';
            $content1 = str_replace('{TITLE}', $data['info_quotes_contract']->title_quotes_contract, $content1);
            $content1 = str_replace('{TABLE_DATA}', $str, $content1);
            $content1 = str_replace('{LOGO}', "<img src='" . base_url('images/logoreport/' . $this->config->item('report_logo')) . "'/>", $content1);
            $content1 = str_replace('{TEN_NCC}', $this->config->item('company'), $content1);
            $content1 = str_replace('{DIA_CHI_NCC}', $this->config->item('address'), $content1);
            $content1 = str_replace('{SDT_NCC}', $this->config->item('phone'), $content1);
            $content1 = str_replace('{DD_NCC}', $this->config->item('corp_master_account'), $content1);
            $content1 = str_replace('{CHUCVU_NCC}', '', $content1);
            $content1 = str_replace('{TKNH_NCC}', $this->config->item('corp_number_account'), $content1);
            $content1 = str_replace('{NH_NCC}', $this->config->item('corp_bank_name'), $content1);
            $content1 = str_replace('{TEN_KH}', $data['cus_name'], $content1);
            $content1 = str_replace('{DIA_CHI_KH}', $data['address'], $content1);
            $content1 = str_replace('{SDT_KH}', '', $content1);
            $content1 = str_replace('{DD_KH}', $data['customer'], $content1);
            $content1 = str_replace('{CHUCVU_KH}', $data['positions'], $content1);
            $content1 = str_replace('{TKNH_KH}', $data['code_tax'], $content1);
            $content1 = str_replace('{NH_KH}', '', $content1);
            $content1 = str_replace('{CODE}', $sale_id, $content1);
            $content1 = str_replace('{DATE}', $d, $content1);
            $content1 = str_replace('{MONTH}', $m, $content1);
            $content1 = str_replace('{YEAR}', $y, $content1);
            fwrite($fp, $content1);
            fclose($fp);
            $file = APPPATH . "/excel_materials/" . $file_name;
            
            if ($type == 3) {
	            /* phan lam mail */
	            $cust_info = $this->Customer->get_info($customer_id);
	            $config = Array(
	                'protocol' => 'smtp',
	                'smtp_host' => 'ssl://smtp.googlemail.com',
	                'smtp_port' => 465,
	                'smtp_user' => $this->config->item('config_email_account'),
	                'smtp_pass' => $this->config->item('config_email_pass'),
	                'charset' => 'utf-8',
	                'mailtype' => 'html'
	            );
	            $this->load->library('email', $config);
	            $this->email->set_newline("\r\n");
	            $this->email->from($this->config->item('email'), $this->config->item('company'));

	            $this->email->to($cust_info->email);
	            
	            $this->email->subject($this->config->item('company') . " xin trân trọng gửi tới quý khách thư báo giá");
	            $content = "<p>Dear anh/chị:" . $data['customer'] . "</p>";
	            $content .= "<p>Dựa vào nhu cầu của Quý khách hàng.</p>";
	            $content .= "<p><b>" . $this->config->item('company') . "</b> xin phép được gửi tới Quý khách hàng báo giá chi tiết như sau:</p>";
	            $content .= "<p>Xin vui lòng xem ở file đính kèm</p>";
	            $content .= "<p><i>Để biết thêm thông tin, vui lòng liên hệ Dịch vụ khách hàng theo số điện thoại: " . $this->config->item("phone") . "</i></p>";
	            $content .= "<i>(Xin vui lòng không phản hồi email này. Đây là email được tự động gửi đi từ hệ thống của chúng tôi).</i>";
	            $content .= "<p>-----</p>";
	            $content .= "<p><i>Thanks and Regards!</i></p>";
	            if ($sale_info['employees_id'] != 0) {
	                $content .= "<p><i>" . $data['employees_id'] . "</i></p>";
	                $content .= "<p>Mobile: " . $data['phone_number1'] . "</p>";
	                $content .= "<p>Email: " . $data['email1'] . "</p>";
	            } else {
	                $content .= "<p><i>" . $data['employee'] . "</i></p>";
	                $content .= "<p>Mobile: " . $data['phone_number'] . "</p>";
	                $content .= "<p>Email: " . $data['email'] . "</p>";
	            }
	            $content .= "<p style='text-transform: uppercase;'>" . $this->config->item("company") . "</p>";
	            $content .= "<p>Rep Off  :" . $this->config->item('address') . "</p>";
	            $content .= "<p>Email    :" . $this->config->item('email') . "</p>";
	            $content .= "<p>Tel      :" . $this->config->item('phone') . " | Fax: " . $this->config->item('fax') . "</p>";
	            $content .= "<p>Web      :" . $this->config->item('website') . "</p>";
	            $this->email->message($content);
	            
	            $this->email->attach($file);
	            if ($this->email->send()) {
	                $send_success[] = $cust_info->email;
	                $data = array(
	                    'sale_id' => $sale_id,
	                    'name' => $file_name,
	                );
	                $this->Sale->insert_sale_material($data);
	                $data_history = array(
	                    'person_id' => $customer_id,
	                    'employee_id' => $this->session->userdata('person_id'),
	                    'title' => 'Báo giá',
	                    'content' => $content,
	                    'time' => date('Y-m-d H:i:s'),
	                    'file' => $file_name,
	                    'status' => 1,
	                );
	                $this->Customer->add_mail_history($data_history);
	                $this->sale_lib->clear_all();
	                redirect('sales');
	            } else {
	                $data_history = array(
	                    'person_id' => $customer_id,
	                    'employee_id' => $this->session->userdata('person_id'),
	                    'title' => 'Báo giá',
	                    'content' => $content,
	                    'time' => date('Y-m-d H:i:s'),
	                    'file' => $file_name,
	                    'status' => 0,
	                );
	                $this->Customer->add_mail_history($data_history);
	                $send_fail[] = $cust_info->email;
	                show_error($this->email->print_debugger());
	            }
	            
// 	            if (empty($send_success)) {
// 	            	echo json_encode(array('success' => false, 'message' => lang('customers_mail_not_send')));
// 	            } else if (empty($send_fail)) {
// 	            	echo json_encode(array(
// 	            			'success' => true,
// 	            			'message' => lang('customers_mail_send_success')));
// 	            } 
            /* end phan lam mail */
            } elseif ($type == '1') {
            	$this->load->view("sales/report_quotes", $data);
//             	echo json_encode(array(
//             			'success' => true,
//             			'filequotes' => $file));
            }
            
		$this->sale_lib->clear_all();

	}
	function _reload($data=array(), $is_ajax = true)
	{
		$data['is_tax_inclusive'] = $this->_is_tax_inclusive();
		if ($data['is_tax_inclusive'] && count($this->sale_lib->get_deleted_taxes()) > 0)
		{
			$this->sale_lib->clear_deleted_taxes();
		}
	
		$person_info = $this->Employee->get_logged_in_employee_info();
		$modes = array('sale'=>lang('sales_sale'),'return'=>lang('sales_return'));
	
		if($this->config->item('customers_store_accounts'))
		{
			$modes['store_account_payment'] = lang('sales_store_account_payment');
		}
		$data['cart']=$this->sale_lib->get_cart();
		$data['modes']= $modes;
		$data['mode']=$this->sale_lib->get_mode();
		$data['items_in_cart'] = $this->sale_lib->get_items_in_cart();
		$data['subtotal']=$this->sale_lib->get_subtotal();
		$data['taxes']=$this->sale_lib->get_taxes();
		$data['total']=$this->sale_lib->get_total();
		$data['line_for_flat_discount_item'] = $this->sale_lib->get_line_for_flat_discount_item();
		$data['discount_all_percent'] = $this->sale_lib->get_discount_all_percent();
		$data['discount_all_fixed'] = $this->sale_lib->get_discount_all_fixed();
		$data['items_module_allowed'] = $this->Employee->has_module_permission('items', $person_info->person_id);
		$data['comment'] = $this->sale_lib->get_comment();
		$data['show_comment_on_receipt'] = $this->sale_lib->get_comment_on_receipt();
		$data['email_receipt'] = $this->sale_lib->get_email_receipt();
		$data['payments_total']=$this->sale_lib->get_payments_totals_excluding_store_account();
		$data['selected_payment'] = $this->sale_lib->get_selected_payment();
		$data['amount_due']=$this->sale_lib->get_amount_due();
		$data['payments']=$this->sale_lib->get_payments();
		$data['change_sale_date_enable'] = $this->sale_lib->get_change_sale_date_enable();
		$data['change_sale_date'] = $this->sale_lib->get_change_sale_date();
		$data['selected_tier_id'] = $this->sale_lib->get_selected_tier_id();
		$data['is_over_credit_limit'] = false;
		$data['fullscreen'] = $this->session->userdata('fullscreen');
		$data['redeem'] = $this->sale_lib->get_redeem();
		$data['deliverer'] = $this->Employee->get_info($this->sale_lib->get_deliverer());
		$data['delivery_date'] = $this->sale_lib->get_delivery_date();
		
		$customer_id=$this->sale_lib->get_customer();
	
		if ($customer_id!=-1)
		{
			$cust_info=$this->Customer->get_info($customer_id);
		}
	
	
		$data['prompt_for_card'] = $this->sale_lib->get_prompt_for_card();
		$data['cc_processor_class_name'] = $this->_get_cc_processor() ? strtoupper(get_class($this->_get_cc_processor())) : '';
	
		if ($this->config->item('select_sales_person_during_sale'))
		{
			$employees = array('' => lang('common_not_set'));
				
			foreach($this->Employee->get_all()->result() as $employee)
			{
				if ($this->Employee->is_employee_authenticated($employee->person_id, $this->Employee->get_logged_in_employee_current_location_id()))
				{
					$employees[$employee->person_id] = $employee->first_name.' '.$employee->last_name;
				}
			}
			$data['employees'] = $employees;
			$data['selected_sold_by_employee_id'] = $this->sale_lib->get_sold_by_employee_id();
		}
	
		$tiers = array();
	
		$tiers[0] = lang('common_none');
		foreach($this->Tier->get_all()->result() as $tier)
		{
			$tiers[$tier->id]=$tier->name;
		}
	
		$data['tiers'] = $tiers;
	
		if ($this->Location->get_info_for_key('enable_credit_card_processing'))
		{
			$data['payment_options']=array(
					lang('common_cash') => lang('common_cash'),
					lang('common_check') => lang('common_check'),
					lang('common_credit') => lang('common_credit'),
					lang('common_giftcard') => lang('common_giftcard'));
	
			if($this->config->item('customers_store_accounts') && $this->sale_lib->get_mode() != 'store_account_payment')
			{
				$data['payment_options']=array_merge($data['payment_options'],	array(lang('common_store_account') => lang('common_store_account')
				));
			}
	
	
			if ($this->config->item('enable_customer_loyalty_system') && $this->config->item('loyalty_option') == 'advanced' && count(explode(":",$this->config->item('spend_to_point_ratio'),2)) == 2 &&  isset($cust_info) && $cust_info->points >=1 && $this->sale_lib->get_payment_amount(lang('common_points')) <=0)
			{
				$data['payment_options']=array_merge($data['payment_options'],	array(lang('common_points') => lang('common_points')));
			}
		}
		else
		{
			$data['payment_options']=array(
					lang('common_cash') => lang('common_cash'),
					lang('common_check') => lang('common_check'),
					lang('common_giftcard') => lang('common_giftcard'),
					lang('common_debit') => lang('common_debit'),
					lang('common_credit') => lang('common_credit')
			);
	
			if($this->config->item('customers_store_accounts') && $this->sale_lib->get_mode() != 'store_account_payment')
			{
				$data['payment_options']=array_merge($data['payment_options'],	array(lang('common_store_account') => lang('common_store_account')
				));
			}
	
			if ($this->config->item('enable_customer_loyalty_system') && $this->config->item('loyalty_option') == 'advanced' && count(explode(":",$this->config->item('spend_to_point_ratio'),2)) == 2 &&  isset($cust_info) && $cust_info->points >=1 && $this->sale_lib->get_payment_amount(lang('common_points')) <=0)
			{
				$data['payment_options']=array_merge($data['payment_options'],	array(lang('common_points') => lang('common_points')));
			}
	
		}
	
		foreach($this->Appconfig->get_additional_payment_types() as $additional_payment_type)
		{
			$data['payment_options'][$additional_payment_type] = $additional_payment_type;
		}
	
		$deleted_payment_types = $this->config->item('deleted_payment_types');
		$deleted_payment_types = explode(',',$deleted_payment_types);
	
		foreach($deleted_payment_types as $deleted_payment_type)
		{
			foreach($data['payment_options'] as $payment_option)
			{
				if ($payment_option == $deleted_payment_type)
				{
					unset($data['payment_options'][$payment_option]);
				}
			}
		}
			
		if($customer_id!=-1)
		{
			$data['customer']=$cust_info->first_name.' '.$cust_info->last_name.($cust_info->company_name==''  ? '' :' ('.$cust_info->company_name.')');
			$data['customer_email']=$cust_info->email;
			$data['customer_balance'] = $cust_info->balance;
			$data['customer_credit_limit'] = $cust_info->credit_limit;
			$data['is_over_credit_limit'] = $this->sale_lib->is_over_credit_limit();
			$data['customer_id']=$customer_id;
			$data['customer_cc_token'] = $cust_info->cc_token;
			$data['customer_cc_preview'] = $cust_info->cc_preview;
			$data['save_credit_card_info'] = $this->sale_lib->get_save_credit_card_info();
			$data['use_saved_cc_info'] = $this->sale_lib->get_use_saved_cc_info();
			$data['avatar']=$cust_info->image_id ?  site_url('app_files/view/'.$cust_info->image_id) : base_url()."assets/img/user.png"; //can be changed to  base_url()."img/avatar.png" if it is required
				
			$data['points'] = to_currency_no_money($cust_info->points);
			$data['sales_until_discount'] = $this->config->item('number_of_sales_for_discount') - $cust_info->current_sales_for_discount;
		}
		$data['customer_required_check'] = (!$this->config->item('require_customer_for_sale') || ($this->config->item('require_customer_for_sale') && isset($customer_id) && $customer_id!=-1));
		$data['suspended_sale_customer_required_check'] = (!$this->config->item('require_customer_for_suspended_sale') || ($this->config->item('require_customer_for_suspended_sale') && isset($customer_id) && $customer_id!=-1));
		$data['payments_cover_total'] = $this->_payments_cover_total();
	
		$data['discount_editable_placement'] = $this->agent->is_mobile() && !$this->agent->is_tablet() ? 'top' : 'left';
	
		if ($is_ajax)
		{
			$this->load->view("sales/register",$data);
		}
		else
		{
			$this->load->view("sales/register_initial",$data);
		}
	}
	
	function report_contract($sale_id)
	{
		$data = array();
		$data['sale_id'] = $sale_id;
		$data['list_contract'] = $this->Customer->get_list_template_quotes_contract(1);
		$customer_id = $this->sale_lib->get_customer();
		$cust_info = $this->Customer->get_info($customer_id);
		$data['email'] = $cust_info->email;
	
		$this->load->view('sales/form_report_contract', $data);
	}
	
	function do_make_contract($sale_id) {
		$id_quotes_contract = $this->input->get("contract");
		$data['info_quotes_contract'] = $this->Customer->get_info_quotes_contract($id_quotes_contract);
		$data['is_sale'] = FALSE;
		$sale_info = $this->Sale->get_info($sale_id)->row_array();
		$this->sale_lib->copy_entire_sale($sale_id);
		$data['cart'] = $this->sale_lib->get_cart();
		$data['payments'] = $this->sale_lib->get_payments();
		$data['subtotal'] = $this->sale_lib->get_subtotal();
		$data['taxes'] = $this->sale_lib->get_taxes($sale_id);
		$data['total'] = $this->sale_lib->get_total($sale_id);
		$data['receipt_title'] = lang('sales_receipt');
		$data['comment'] = $this->Sale->get_comment($sale_id);
		$data['show_comment_on_receipt'] = $this->Sale->get_comment_on_receipt($sale_id);
		$data['transaction_time'] = date(get_date_format() . ' ' . get_time_format(), strtotime($sale_info['sale_time']));
		$customer_id = $this->sale_lib->get_customer();
		$emp_info = $this->Employee->get_info($sale_info['employee_id']);
// 		$info_empss = $this->Employee->get_info($sale_info['employees_id']);
		$data['payment_type'] = $sale_info['payment_type'];
		$data['amount_change'] = $this->sale_lib->get_amount_due($sale_id) * -1;
		$data['employee'] = $emp_info->first_name . ' ' . $emp_info->last_name;
		$data['phone'] = $emp_info->phone_number;
		$data['email'] = $emp_info->email;
		$data['ref_no'] = $sale_info['cc_ref_no'];
		$this->load->helper('string');
		$data['payment_type'] = str_replace(array('<sup>VNĐ</sup><br />', ''), ' .VNĐ', $sale_info['payment_type']);
		$data['amount_due'] = $this->sale_lib->get_amount_due();
		foreach ($data['payments'] as $payment_id => $payment) {
			$payment_amount = $payment['payment_amount'];
		}
		$k = 28;
		$tongtienhang = 0;
		foreach (array_reverse($data['cart'], true) as $line => $item) {
			$tongtienhang_1 += $item['price'] * $item['quantity'] - $item['price'] * $item['quantity'] * $item['discount'] / 100;
			$k++;
		}
		$payments_cost = $tongtienhang_1 - $payment_amount;
		if ($customer_id != -1) {
			$cust_info = $this->Customer->get_info($customer_id);
			$data['customer'] = $cust_info->first_name . ' ' . $cust_info->last_name;
			$data['cus_name'] = $cust_info->company_name == '' ? '' : $cust_info->company_name;
			$data['code_tax'] = $cust_info->code_tax;
			$data['address'] = $cust_info->address_1;
			$data['account_number'] = $cust_info->account_number;
			$data['positions'] = $cust_info->positions;
		}
		$data['sale_id'] = $sale_id;
		
		$type = $this->input->post('contract_type');
		$data['word'] = $type;
		$data['cat_baogia'] = '';
		
		if ($type == '1') {
			$this->load->view("sales/report_contract", $data);
			header("Refresh:0");
		} elseif ($type == '3') {
			$file_name = "HD_" . $sale_id . "_" . str_replace(" ", "", replace_character($data['customer'])) . "_" . date('dmYHis') . ".doc";
			
			if (!file_exists(APPPATH. '/excel_materials')) {
				mkdir(APPPATH. '/excel_materials/', 0777, true);
			}
			$fp = fopen(APPPATH . "/excel_materials/" . $file_name, 'w+');
			$arr_item = array();
			$arr_service = array();
			foreach ($data['cart'] as $line => $val) {
				if ($val['item_id']) {
					$info_item = $this->Item->get_info($val['item_id']);
					if ($info_item->service == 0) {
						$arr_item[] = array(
								'item_id' => $val['item_id'],
								'line' => $line,
								'name' => $val['name'],
								'item_number' => $val['item_number'],
								'description' => $val['description'],
								'serialnumber' => $val['serialnumber'],
								'allow_alt_description' => $val['allow_alt_description'],
								'is_serialized' => $val['is_serialized'],
								'quantity' => $val['quantity'],
								'stored_id' => $val['stored_id'],
								'discount' => $val['discount'],
								'price' => $val['price'],
								'price_rate' => $val['price_rate'],
								'taxes' => $val['taxes'],
								'unit' => $val['unit']
						);
					} else {
						$arr_service[] = array(
								'item_id' => $val['item_id'],
								'line' => $line,
								'name' => $val['name'],
								'item_number' => $val['item_number'],
								'description' => $val['description'],
								'serialnumber' => $val['serialnumber'],
								'allow_alt_description' => $val['allow_alt_description'],
								'is_serialized' => $val['is_serialized'],
								'quantity' => $val['quantity'],
								'stored_id' => $val['stored_id'],
								'discount' => $val['discount'],
								'price' => $val['price'],
								'price_rate' => $val['price_rate'],
								'taxes' => $val['taxes'],
								'unit' => $val['unit']
						);
					}
				} else {
					$arr_item[] = array(
							'pack_id' => $val['pack_id'],
							'line' => $val['line'],
							'pack_number' => $val['pack_number'],
							'name' => $val['name'],
							'description' => $val['description'],
							'quantity' => $val['quantity'],
							'discount' => $val['discount'],
							'price' => $val['price'],
							'taxes' => $val['taxes'],
							'unit' => $val['unit']
					);
				}
			}
			$str .= "<table style='width: 100%; border-collapse: collapse'>";
			$str .= "<tr>";
			$str .= "<th style='text-align: center; border: 1px solid #000; padding: 8px 0px; width: 5%'>STT</th>";
			$str .= "<th style='text-align: center; border: 1px solid #000; padding: 8px 0px; width: 30%'>Tên hàng</th>";
			$str .= "<th style='text-align: center; border: 1px solid #000; padding: 8px 0px; width: 5%'>ĐVT</th>";
			$str .= "<th style='text-align: center; border: 1px solid #000; padding: 8px 0px; width: 8%'>SL</th>";
			$str .= "<th style='text-align: center; border: 1px solid #000; padding: 8px 0px; width: 14%'>Đơn giá</th>";
			$str .= "<th style='text-align: center; border: 1px solid #000; padding: 8px 0px; width: 14%'>CK(%)</th>";
			$str .= "<th style='text-align: center; border: 1px solid #000; padding: 8px 0px; width: 14%'>Thuế(%)</th>";
			$str .= "<th style='text-align: center; border: 1px solid #000; padding: 8px 0px; width: 14%'>Thành tiền</th>";
			$str .= "</tr>";
	
			$stt = 1;
			$total = 0;
			if ($cat_hopdong == 1) {
				foreach ($arr_item as $line => $item) {
					if ($item['pack_id']) {
						$info_pack = $this->Pack->get_info($item['pack_id']);
						$pack_item = $this->Pack_items->get_info($item['pack_id']);
						$info_sale_pack = $this->Sale->get_sale_pack_by_sale_pack($sale_id, $item['pack_id']);
						//$info_unit = $this->Unit->get_info($info_sale_pack->unit_pack);
						$thanh_tien = $item['quantity'] * $item['price'] - $item['quantity'] * $item['price'] * $item['discount'] / 100 + ($item['quantity'] * $item['price'] - $item['quantity'] * $item['price'] * $item['discount'] / 100) * $item['taxes'] / 100;
						$str .= "<tr>";
						$str .= "<td style='text-align: center; border: 1px solid #000000; padding: 10px 5px'>" . $stt . "</td>";
						$str .= "<td style='border: 1px solid #000000; padding: 10px 5px'>";
						$str .= "<strong>" . $info_pack->pack_number . "/" . $info_pack->name . "(Gói SP)</strong><br>";
						foreach ($pack_item as $val) {
							$info_item = $this->Item->get_info($val->item_id);
							$str .= "<p>- <strong>" . $info_item->item_number . "</strong>/" . $info_item->name . "</p>";
						}
	
						$str .= "</td>";
						$str .= "<td style='border: 1px solid #000000; padding: 10px 5px'>" . 'U_N' . "</td>";
						$str .= "<td style='text-align: right; border: 1px solid #000000; padding: 10px 5px'>" . format_quantity($item['quantity']) . "</td>";
						$str .= "<td style='text-align: right; border: 1px solid #000000; padding: 10px 5px'>" . number_format($item['price']) . "</td>";
						$str .= "<td style='text-align: right; border: 1px solid #000000; padding: 10px 5px'>" . number_format($item['discount']) . "</td>";
						$str .= "<td style='text-align: right; border: 1px solid #000000; padding: 10px 5px'>" . number_format($item['taxes']) . "</td>";
						$str .= "<td style='text-align: right; border: 1px solid #000000; padding: 10px 5px'>" . number_format($thanh_tien) . "</td>";
						$str .= "</tr>";
						$total += $thanh_tien;
					} else {
						$info_item = $this->Item->get_info($item['item_id']);
						$info_sale_item = $this->Sale->get_sale_item_by_sale_item($sale_id, $item['item_id']);
						//$info_unit = $this->Unit->get_info($info_sale_item->unit_item);
						$thanh_tien = $item['quantity'] * ($item['unit'] == 'unit_from' ? $item['price_rate'] : $item['price']) - $item['quantity'] * ($item['unit'] == 'unit_from' ? $item['price_rate'] : $item['price']) * $item['discount'] / 100 + ($item['quantity'] * ($item['unit'] == 'unit_from' ? $item['price_rate'] : $item['price']) - $item['quantity'] * ($item['unit'] == 'unit_from' ? $item['price_rate'] : $item['price']) * $item['discount'] / 100) * $item['taxes'] / 100;
						$str .= "<tr>";
						$str .= "<td style='text-align: center; border: 1px solid #000000; padding: 10px 5px'>" . $stt . "</td>";
						$str .= "<td style='border: 1px solid #000000; padding: 10px 5px'><strong>" . $info_item->item_number . "</strong>/" . $info_item->name . "</td>";
						$str .= "<td style='border: 1px solid #000000; padding: 10px 5px'>" . 'U_N' . "</td>";
						$str .= "<td style='text-align: right; border: 1px solid #000000; padding: 10px 5px'>" . format_quantity($item['quantity']) . "</td>";
						$str .= "<td style='text-align: right; border: 1px solid #000000; padding: 10px 5px'>" . number_format(($item['unit'] == 'unit_from' ? $item['price_rate'] : $item['price'])) . "</td>";
						$str .= "<td style='text-align: right; border: 1px solid #000000; padding: 10px 5px'>" . number_format($item['discount']) . "</td>";
						$str .= "<td style='text-align: right; border: 1px solid #000000; padding: 10px 5px'>" . number_format($item['taxes']) . "</td>";
						$str .= "<td style='text-align: right; border: 1px solid #000000; padding: 10px 5px'>" . number_format($thanh_tien) . "</td>";
						$str .= "</tr>";
						$total += $thanh_tien;
					}
					$stt++;
				}
			} else if ($cat_hopdong == 2) {
				foreach ($arr_service as $line => $item) {
					$info_item = $this->Item->get_info($item['item_id']);
					$info_sale_item = $this->Sale->get_sale_item_by_sale_item($sale_id, $item['item_id']);
					//$info_unit = $this->Unit->get_info($info_sale_item->unit_item);
					$thanh_tien = $item['quantity'] * $item['price'] - $item['quantity'] * $item['price'] * $item['discount'] / 100 + ($item['quantity'] * $item['price'] - $item['quantity'] * $item['price'] * $item['discount'] / 100) * $item['taxes'] / 100;
					$str .= "<tr>";
					$str .= "<td style='text-align: center; border: 1px solid #000000; padding: 10px 5px'>" . $stt . "</td>";
					$str .= "<td style='border: 1px solid #000000; padding: 10px 5px'><strong>" . $info_item->item_number . "</strong>/" . $info_item->name . "</td>";
					$str .= "<td style='border: 1px solid #000000; padding: 10px 5px'>" . 'U_N' . "</td>";
					$str .= "<td style='text-align: right; border: 1px solid #000000; padding: 10px 5px'>" . format_quantity($item['quantity']) . "</td>";
					$str .= "<td style='text-align: right; border: 1px solid #000000; padding: 10px 5px'>" . number_format($item['price']) . "</td>";
					$str .= "<td style='text-align: right; border: 1px solid #000000; padding: 10px 5px'>" . number_format($item['discount']) . "</td>";
					$str .= "<td style='text-align: right; border: 1px solid #000000; padding: 10px 5px'>" . number_format($item['taxes']) . "</td>";
					$str .= "<td style='text-align: right; border: 1px solid #000000; padding: 10px 5px'>" . number_format($thanh_tien) . "</td>";
					$str .= "</tr>";
					$total += $thanh_tien;
					$stt++;
				}
			} else {
				foreach ($data['cart'] as $line => $item) {
					if ($item['pack_id']) {
						$info_pack = $this->Pack->get_info($item['pack_id']);
						$pack_item = $this->Pack_items->get_info($item['pack_id']);
						$info_sale_pack = $this->Sale->get_sale_pack_by_sale_pack($sale_id, $item['pack_id']);
						//$info_unit = $this->Unit->get_info($info_sale_pack->unit_pack);
						$thanh_tien = $item['quantity'] * $item['price'] - $item['quantity'] * $item['price'] * $item['discount'] / 100 + ($item['quantity'] * $item['price'] - $item['quantity'] * $item['price'] * $item['discount'] / 100) * $item['taxes'] / 100;
						$str .= "<tr>";
						$str .= "<td style='text-align: center; border: 1px solid #000000; padding: 10px 5px'>" . $stt . "</td>";
						$str .= "<td style='border: 1px solid #000000; padding: 10px 5px'>";
						$str .= "<strong>" . $info_pack->pack_number . "/" . $info_pack->name . "(Gói SP)</strong><br>";
						foreach ($pack_item as $val) {
							$info_item = $this->Item->get_info($val->item_id);
							$str .= "<p>- <strong>" . $info_item->item_number . "</strong>/" . $info_item->name . "</p>";
						}
	
						$str .= "</td>";
						$str .= "<td style='border: 1px solid #000000; padding: 10px 5px'>" . 'U_N' . "</td>";
						$str .= "<td style='text-align: right; border: 1px solid #000000; padding: 10px 5px'>" . format_quantity($item['quantity']) . "</td>";
						$str .= "<td style='text-align: right; border: 1px solid #000000; padding: 10px 5px'>" . number_format($item['price']) . "</td>";
						$str .= "<td style='text-align: right; border: 1px solid #000000; padding: 10px 5px'>" . number_format($item['discount']) . "</td>";
						$str .= "<td style='text-align: right; border: 1px solid #000000; padding: 10px 5px'>" . number_format($item['taxes']) . "</td>";
						$str .= "<td style='text-align: right; border: 1px solid #000000; padding: 10px 5px'>" . number_format($thanh_tien) . "</td>";
						$str .= "</tr>";
						$total += $thanh_tien;
					} else {
						$info_item = $this->Item->get_info($item['item_id']);
						$info_sale_item = $this->Sale->get_sale_item_by_sale_item($sale_id, $item['item_id']);
						//$info_unit = $this->Unit->get_info($info_sale_item->unit_item);
						$thanh_tien = $item['quantity'] * ($item['unit'] == 'unit_from' ? $item['price_rate'] : $item['price']) - $item['quantity'] * ($item['unit'] == 'unit_from' ? $item['price_rate'] : $item['price']) * $item['discount'] / 100 + ($item['quantity'] * ($item['unit'] == 'unit_from' ? $item['price_rate'] : $item['price']) - $item['quantity'] * ($item['unit'] == 'unit_from' ? $item['price_rate'] : $item['price']) * $item['discount'] / 100) * $item['taxes'] / 100;
						$str .= "<tr>";
						$str .= "<td style='text-align: center; border: 1px solid #000000; padding: 10px 5px'>" . $stt . "</td>";
						$str .= "<td style='border: 1px solid #000000; padding: 10px 5px'><strong>" . $info_item->item_number . "</strong>/" . $info_item->name . "</td>";
						$str .= "<td style='border: 1px solid #000000; padding: 10px 5px'>" . 'U_N' . "</td>";
						$str .= "<td style='text-align: right; border: 1px solid #000000; padding: 10px 5px'>" . format_quantity($item['quantity']) . "</td>";
						$str .= "<td style='text-align: right; border: 1px solid #000000; padding: 10px 5px'>" . number_format(($item['unit'] == 'unit_from' ? $item['price_rate'] : $item['price'])) . "</td>";
						$str .= "<td style='text-align: right; border: 1px solid #000000; padding: 10px 5px'>" . number_format($item['discount']) . "</td>";
						$str .= "<td style='text-align: right; border: 1px solid #000000; padding: 10px 5px'>" . number_format($item['taxes']) . "</td>";
						$str .= "<td style='text-align: right; border: 1px solid #000000; padding: 10px 5px'>" . number_format($thanh_tien) . "</td>";
						$str .= "</tr>";
						$total += $thanh_tien;
					}
					$stt++;
				}
			}
			$str .= "<tr>";
			$str .= "<td colspan='3' style='text-align: center; font-weight: bold; border: 1px solid #000000; padding: 10px 5px'>Tổng</td>";
			$str .= "<td colspan='5' style='text-align: right; font-weight: bold; border: 1px solid #000000; padding: 10px 5px'>" . number_format($total) . "</td>";
			$str .= "</tr>";
			$str .= "</table>";
			$str .= "<p>Tổng giá trị (Bằng chữ): <strong><em>" . $total . "</em></strong></p>";
			$content1 = "<html>";
			$content1 .= "<meta charset='utf-8'/>";
			$content1 .= "<body style='font-size: 100% !important'>";
			$content1 .= $data['info_quotes_contract']->content_quotes_contract;
			$content1 .= "</body>";
			$content1 .= "</html>";
			$info_sale = $this->Sale->get_info_sale_order($sale_id);
			$d = $info_sale->date_debt != '0000-00-00' ? date('d', strtotime($info_sale->date_debt)) : '...';
			$m = $info_sale->date_debt != '0000-00-00' ? date('m', strtotime($info_sale->date_debt)) : '...';
			$y = $info_sale->date_debt != '0000-00-00' ? date('Y', strtotime($info_sale->date_debt)) : '...';
			$content1 = str_replace('{TITLE}', $data['info_quotes_contract']->title_quotes_contract, $content1);
			$content1 = str_replace('{TABLE_DATA}', $str, $content1);
			$content1 = str_replace('{LOGO}', "<img src='" . base_url('images/logoreport/' . $this->config->item('report_logo')) . "'/>", $content1);
			$content1 = str_replace('{TEN_NCC}', $this->config->item('company'), $content1);
			$content1 = str_replace('{DIA_CHI_NCC}', $this->config->item('address'), $content1);
			$content1 = str_replace('{SDT_NCC}', $this->config->item('phone'), $content1);
			$content1 = str_replace('{DD_NCC}', $this->config->item('corp_master_account'), $content1);
			$content1 = str_replace('{CHUCVU_NCC}', '', $content1);
			$content1 = str_replace('{TKNH_NCC}', $this->config->item('corp_number_account'), $content1);
			$content1 = str_replace('{NH_NCC}', $this->config->item('corp_bank_name'), $content1);
			$content1 = str_replace('{TEN_KH}', $data['cus_name'], $content1);
			$content1 = str_replace('{DIA_CHI_KH}', $data['address'], $content1);
			$content1 = str_replace('{SDT_KH}', '', $content1);
			$content1 = str_replace('{DD_KH}', $data['customer'], $content1);
			$content1 = str_replace('{CHUCVU_KH}', $data['positions'], $content1);
			$content1 = str_replace('{TKNH_KH}', $data['code_tax'], $content1);
			$content1 = str_replace('{NH_KH}', '', $content1);
			$content1 = str_replace('{CODE}', $sale_id, $content1);
			$content1 = str_replace('{DATE}', $d, $content1);
			$content1 = str_replace('{MONTH}', $m, $content1);
			$content1 = str_replace('{YEAR}', $y, $content1);
			fwrite($fp, $content1);
			fclose($fp);
			/* phan lam mail */
			$cust_info = $this->Customer->get_info($customer_id);
			$config = Array(
					'protocol' => 'smtp',
					'smtp_host' => 'ssl://smtp.googlemail.com',
					'smtp_port' => 465,
					'smtp_user' => $this->config->item('config_email_account'),
	                'smtp_pass' => $this->config->item('config_email_pass'),
					'charset' => 'utf-8',
					'mailtype' => 'html'
			);
			$this->load->library('email', $config);
			$this->email->set_newline("\r\n");
			$this->email->from($this->config->item('email'), $this->config->item('company'));
			
// 			$this->email->to($cust_info->email);
			$this->email->to('chienphuong2906@gmail.com');
			
			$this->email->subject($this->config->item('company') . " xin trân trọng gửi tới quý khách hợp đồng");
			$content = "<p>Dear anh/chị:" . $data['customer'] . "</p>";
			$content .= "<p>Dựa vào nhu cầu của Quý khách hàng.</p>";
			$content .= "<p><b>" . $this->config->item('company') . "</b> xin phép được gửi tới Quý khách hàng hợp đồng chi tiết như sau:</p>";
			$content .= "<p>Xin vui lòng xem ở file đính kèm</p>";
			$content .= "<p><i>Để biết thêm thông tin, vui lòng liên hệ Dịch vụ khách hàng theo số điện thoại: " . $this->config->item("phone") . "</i></p>";
			$content .= "<i>(Xin vui lòng không phản hồi email này. Đây là email được tự động gửi đi từ hệ thống của chúng tôi).</i>";
			$content .= "<p>-----</p>";
			$content .= "<p><i>Thanks and Regards!</i></p>";
			$content .= "<p><i>" . $data['employee'] . "</i></p>";
			$content .= "<p>Mobile: " . $data['phone'] . "</p>";
			$content .= "<p>Email: " . $data['email'] . "</p>";
	
			$content .= "------------------------------------------------------------------------";
			$content .= "<img src='" . base_url() . "images/logoreport/11.png'>";
			$content .= "<p style='text-transform: uppercase;'>" . $this->config->item("company") . "</p>";
			$content .= "<p>Rep Off  :" . $this->config->item('address') . "</p>";
			$content .= "<p>Email    :" . $this->config->item('email') . "</p>";
			$content .= "<p>Tel      :" . $this->config->item('phone') . " | Fax: " . $this->config->item('fax') . "</p>";
			$content .= "<p>Web      :" . $this->config->item('website') . "</p>";
			$this->email->message($content);
			$file = APPPATH . "/../excel_materials/" . $file_name;
			$this->email->attach($file);
			if ($this->email->send()) {
				$send_success[] = $cust_info->email;
				$data_history = array(
						'person_id' => $customer_id,
						'employee_id' => $this->session->userdata('person_id'),
						'title' => 'Hợp đồng',
						'content' => $content,
						'time' => date('Y-m-d H:i:s'),
						'file' => $file_name,
						'status' => 1,
				);
				$this->Customer->add_mail_history($data_history);
				$this->sale_lib->clear_all();
				redirect('sales');
			} else {
				$send_fail[] = $cust_info->email;
				$data_history = array(
						'person_id' => $customer_id,
						'employee_id' => $this->session->userdata('person_id'),
						'title' => 'Hợp đồng',
						'content' => $content,
						'time' => date('Y-m-d H:i:s'),
						'file' => $file_name,
						'status' => 0,
				);
				$this->Customer->add_mail_history($data_history);
				show_error($this->email->print_debugger());
			}
			/* end phan lam mail */
		}
		$this->sale_lib->clear_all();
	}
}
?>
