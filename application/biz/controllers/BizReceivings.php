<?php
require_once (APPPATH . "controllers/Receivings.php");

class BizReceivings extends Receivings
{
	protected $_prefixDocument = 'REC#';

	function complete()
	{
		$data['cart']=$this->receiving_lib->get_cart();
		if (empty($data['cart']))
		{
			redirect('receivings');
		}
		$data['taxes']=$this->receiving_lib->get_taxes();		
		$data['subtotal']=$this->receiving_lib->get_subtotal();		
		$data['total']=$this->receiving_lib->get_total();
		$data['receipt_title']=lang('receivings_receipt');
		$supplier_id=$this->receiving_lib->get_supplier();
		$location_id=$this->receiving_lib->get_location();
		$employee_id=$this->Employee->get_logged_in_employee_info()->person_id;
		$comment = $this->input->post('comment') ? $this->input->post('comment') : '';
		$data['comment'] = $comment;
		$emp_info=$this->Employee->get_info($employee_id);
		$payment_type = $this->input->post('payment_type');
		$data['payment_type']=$this->input->post('payment_type');
		$data['mode']=$this->receiving_lib->get_mode();
		$data['change_receiving_date'] =$this->receiving_lib->get_change_receiving_date_enable() ?  $this->receiving_lib->get_change_receiving_date() : false;
		$old_date = $this->receiving_lib->get_change_recv_id()  ? $this->Receiving->get_info($this->receiving_lib->get_change_recv_id())->row_array() : false;
		$old_date=  $old_date ? date(get_date_format().' '.get_time_format(), strtotime($old_date['receiving_time'])) : date(get_date_format().' '.get_time_format());
		$data['transaction_time']= $this->receiving_lib->get_change_receiving_date_enable() ?  date(get_date_format().' '.get_time_format(), strtotime($this->receiving_lib->get_change_receiving_date())) : $old_date;
		
		$data['suspended']  = 0;
		$data['is_po'] = 0;
		$data['discount_exists'] = $this->_does_discount_exists($data['cart']);
		

		if ($this->input->post('amount_tendered'))
		{
			$data['amount_tendered'] = $this->input->post('amount_tendered');
			$decimals = $this->config->item('number_of_decimals') !== NULL && $this->config->item('number_of_decimals') != '' ? (int)$this->config->item('number_of_decimals') : 2;
			
			$data['amount_change'] = to_currency($data['amount_tendered'] - round($data['total'], $decimals));
		}
		$data['employee']=$emp_info->first_name.' '.$emp_info->last_name;

		if($supplier_id!=-1)
		{	
			$suppl_info=$this->Supplier->get_info($supplier_id);		
			$data['supplier']=$suppl_info->company_name;
			if ($suppl_info->first_name || $suppl_info->last_name)
			{
				$data['supplier'] .= ' ('.$suppl_info->first_name.' '.$suppl_info->last_name.')';
			}
			
			$data['supplier_address_1'] = $suppl_info->address_1;
			$data['supplier_address_2'] = $suppl_info->address_2;
			$data['supplier_city'] = $suppl_info->city;
			$data['supplier_state'] = $suppl_info->state;
			$data['supplier_zip'] = $suppl_info->zip;
			$data['supplier_country'] = $suppl_info->country;
			$data['supplier_phone'] = $suppl_info->phone_number;
			$data['supplier_email'] = $suppl_info->email;
			
		}
		
		if ($this->config->item('charge_tax_on_recv'))
		{
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
				}
			}
		}

		$suspended_change_recv_id=$this->receiving_lib->get_suspended_receiving_id() ? $this->receiving_lib->get_suspended_receiving_id() : $this->receiving_lib->get_change_recv_id();

		//SAVE receiving to database		
		$receiving_id_raw = $this->Receiving->save($data['cart'], $supplier_id,$employee_id,$comment,$payment_type,$suspended_change_recv_id,0,$data['mode'], $data['change_receiving_date'],0, $location_id);
		$data['receiving_id']='RECV '.$receiving_id_raw;
		$data['receiving_id_raw']=$receiving_id_raw;
		
		if ($data['receiving_id'] == 'RECV -1')
		{
			$data['error_message'] = '';
			$data['error_message'] .= '<span class="text-danger">'.lang('receivings_transaction_failed').'</span>';
			$data['error_message'] .= '<br /><br />'.anchor('receivings','&laquo; '.lang('receivings_register'));			
			$data['error_message'] .= '<br /><br />'.anchor('receivings/complete',lang('common_try_again'). ' &raquo;');
		}
		else
		{
			if ($this->receiving_lib->get_email_receipt() && !empty($suppl_info->email))
			{
				$this->load->library('email');
				$config['mailtype'] = 'html';				
				$this->email->initialize($config);
				$this->email->from($this->Location->get_info_for_key('email') ? $this->Location->get_info_for_key('email') : 'no-reply@mg.4biz.vn', $this->config->item('company'));
				$this->email->to($suppl_info->email); 

				$this->email->subject(lang('receivings_receipt'));
				$this->email->message($this->load->view("receivings/receipt_email",$data, true));	
				$this->email->send();
		
			}
		}
		
		$current_location_id = $this->Employee->get_logged_in_employee_current_location_id();
		$current_location = $this->Location->get_info($current_location_id);
		$data['transfer_from_location'] = $current_location->name;
		
		if ($location_id > 0)
		{
			$transfer_to_location = $this->Location->get_info($location_id);
			$data['transfer_to_location'] = $transfer_to_location->name;
		}

		if ($data['receiving_id'] != 'RECV -1')
		{
			$this->receiving_lib->clear_all();
		}

		// [4biz] switch to correct view
		$typeOfView = $this->getTypeOfOrder($data['mode']);
		$data['pdf_block_html'] = $this->load->view('receivings/partials/' . $typeOfView, $data, TRUE);

		$this->load->view("receivings/receipt",$data);
	}

	protected function 	getTypeOfOrder($mode = '')
	{
		$typeOfView = 'receive';
		
		if($mode == 'transfer')
		{
			$typeOfView = 'move_inventory';
		}
		return $typeOfView;
	}
	
	function receipt($receiving_id)
	{
		//Before changing the recv session data, we need to save our current state in case they were in the middle of a recv
		$this->receiving_lib->save_current_recv_state();
	
		$receiving_info = $this->Receiving->get_info($receiving_id)->row_array();
		$this->receiving_lib->copy_entire_receiving($receiving_id, TRUE);
		$data['cart']=$this->receiving_lib->get_cart();
		$data['subtotal']=$this->receiving_lib->get_subtotal($receiving_id);
		$data['taxes']=$this->receiving_lib->get_taxes($receiving_id);
		$data['total']=$this->receiving_lib->get_total($receiving_id);
		$data['receipt_title']=lang('receivings_receipt');
		$data['transaction_time']= date(get_date_format().' '.get_time_format(), strtotime($receiving_info['receiving_time']));
		$supplier_id=$this->receiving_lib->get_supplier();
		$emp_info=$this->Employee->get_info($receiving_info['employee_id']);
		$data['payment_type']=$receiving_info['payment_type'];
		$data['override_location_id'] = $receiving_info['location_id'];
		$data['suspended'] = $receiving_info['suspended'];
		$data['comment'] = $receiving_info['comment'];
		$data['is_po'] = $receiving_info['is_po'];
		$data['discount_exists'] = $this->_does_discount_exists($data['cart']);
	
	
		$data['employee']=$emp_info->first_name.' '.$emp_info->last_name;
	
		if($supplier_id!=-1)
		{
			$supplier_info=$this->Supplier->get_info($supplier_id);
	
			$data['supplier']=$supplier_info->company_name;
			if ($supplier_info->first_name || $supplier_info->last_name)
			{
				$data['supplier'] .= ' ('.$supplier_info->first_name.' '.$supplier_info->last_name.')';
			}
				
			$data['supplier_address_1'] = $supplier_info->address_1;
			$data['supplier_address_2'] = $supplier_info->address_2;
			$data['supplier_city'] = $supplier_info->city;
			$data['supplier_state'] = $supplier_info->state;
			$data['supplier_zip'] = $supplier_info->zip;
			$data['supplier_country'] = $supplier_info->country;
			$data['supplier_phone'] = $supplier_info->phone_number;
			$data['supplier_email'] = $supplier_info->email;
				
		}
		$data['receiving_id']='RECV '.$receiving_id;
		$data['receiving_id_raw']=$receiving_id;
	
		$current_location = $this->Location->get_info($receiving_info['location_id']);
		$data['transfer_from_location'] = $current_location->name;
	
		if ($receiving_info['transfer_to_location_id'] > 0)
		{
			$transfer_to_location = $this->Location->get_info($receiving_info['transfer_to_location_id']);
			$data['transfer_to_location'] = $transfer_to_location->name;

			$data['mode'] = 'transfer';
		}
	
		// [4biz] switch to correct view
		$typeOfView = $this->getTypeOfOrder($data['mode']);
		$data['pdf_block_html'] = $this->load->view('receivings/partials/' . $typeOfView, $data, TRUE);
		
		$this->load->view("receivings/receipt",$data);
		$this->receiving_lib->clear_all();
	
		//Restore previous state saved above
		$this->receiving_lib->restore_current_recv_state();
	
	}
}