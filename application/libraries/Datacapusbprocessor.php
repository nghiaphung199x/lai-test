<?php
require_once ("Creditcardprocessor.php");
abstract class Datacapusbprocessor extends Creditcardprocessor
{
	private $com_port;
	private $listener_port;
	private $merchant_id;
	private $sequence_no;	
	private $secure_device_emv;
	private $secure_device_non_emv;
	
	function __construct($controller,$secure_device_emv, $secure_device_non_emv)
	{
		parent::__construct($controller);
		$this->com_port = $this->controller->Location->get_info_for_key('com_port');
		$this->listener_port =  $this->controller->Location->get_info_for_key('listener_port');
		$this->merchant_id = $this->controller->Location->get_info_for_key('emv_merchant_id');
		$this->secure_device_emv = $secure_device_emv;
		$this->secure_device_non_emv = $secure_device_non_emv;
		if (!$this->controller->session->userdata('sequence_no'))
		{
			$this->controller->session->set_userdata('sequence_no', '0010010010');
		}
		
		$this->sequence_no = $this->controller->session->userdata('sequence_no');
	}
	
	public function get_emv_param_download_params()
	{
		$return = array(
			'post_host' => '127.0.0.1', 
			'post_data' => array(
				'HostOrIP' => '127.0.0.1',
				'IpPort' => '9000',
				'MerchantID' => NULL,//Not set as this will be set by locations form; or if sales controller by this class
				'OperatorID' => (!defined("ENVIRONMENT") or ENVIRONMENT == 'development') ? 'test' : $this->controller->Employee->get_logged_in_employee_info()->person_id,				
				'TranCode' => 'EMVParamDownload',
				'SecureDevice' => $this->secure_device_emv,
				'TStream' => 'Admin',
				'PadType' => 'VX805',
				'ComPort' => NULL,//Not set as this will be set by locations form; or if sales controller by this class,
				'SequenceNo' => $this->sequence_no,
			)
		);
		
		if (strtoupper(get_class($this->controller)) == 'SALES')
		{
			$return['post_data']['ComPort'] = $this->com_port;
			$return['post_data']['MerchantID'] = $this->merchant_id;
		}

		return $return;
	}
	
	
	public function get_emv_pad_reset_params()
	{
		$return = array(
			'post_host' => '127.0.0.1', 
			'post_data' => array(
				'HostOrIP' => '127.0.0.1',
				'IpPort' => '9000',
				'MerchantID' => NULL,//Not set as this will be set by locations form; or if sales controller by this class
				'OperatorID' => (!defined("ENVIRONMENT") or ENVIRONMENT == 'development') ? 'test' : $this->controller->Employee->get_logged_in_employee_info()->person_id,				
				'TranCode' => 'EMVPadReset',
				'SecureDevice' => $this->secure_device_emv,
				'TStream' => 'Admin',
				'PadType' => 'VX805',
				'ComPort' => NULL,//Not set as this will be set by locations form; or if sales controller by this class,
				'SequenceNo' => $this->sequence_no,
			)
		);
		
		if (strtoupper(get_class($this->controller)) == 'SALES')
		{
			$return['post_data']['ComPort'] = $this->com_port;
			$return['post_data']['MerchantID'] = $this->merchant_id;
		}

		return $return;
		
	}
	
	public function start_cc_processing()
	{
		$cc_amount = to_currency_no_money($this->controller->sale_lib->get_payment_amount(lang('common_credit')));
		$tax_amount = to_currency_no_money(($this->controller->sale_lib->get_total() - $this->controller->sale_lib->get_subtotal()) * ($cc_amount / $this->controller->sale_lib->get_total()));
		$customer_id = $this->controller->sale_lib->get_customer();
		$customer_name = '';
		if ($customer_id != -1)
		{
			$customer_info=$this->controller->Customer->get_info($customer_id);
			$customer_name = $customer_info->first_name.' '.$customer_info->last_name;
		}
		
		//Regular sale NOT using saved credit card
		if(!$this->controller->sale_lib->get_use_saved_cc_info())
		{
			$prompt = $this->controller->sale_lib->get_prompt_for_card();
			
			$InvoiceNo = $this->_get_session_invoice_no();
			
			$post_data = array(
				'HostOrIP' => '127.0.0.1',
				'IpPort' => '9000',
				'Memo' => 'PHP POS EMV '.APPLICATION_VERSION,
				'TranCode' => $cc_amount > 0 ? 'EMVSale' : 'EMVReturn',
				'OperatorID' => (!defined("ENVIRONMENT") or ENVIRONMENT == 'development') ? 'test' : $this->controller->Employee->get_logged_in_employee_info()->person_id,
				'MerchantID' => $this->merchant_id,
				'LaneID' => $this->controller->Employee->get_logged_in_employee_current_register_id()  && $this->controller->Employee->get_logged_in_employee_current_register_id() <=999 ? $this->controller->Employee->get_logged_in_employee_current_register_id()  : 0,
				'ComPort' => $this->com_port,
				'TStream' => 'Transaction',
				'SecureDevice' => $this->secure_device_emv,
				'InvoiceNo' => $InvoiceNo,
				'RefNo' => $InvoiceNo, //Suppose to be same as InvoiceNum for EMVSale
				'Purchase' => to_currency_no_money(abs($cc_amount)),
				'RecordNo' => 'RecordNumberRequested',
				'AcctNo' => $prompt ? 'Prompt' : 'SecureDevice',
				'Frequency' => 'OneTime',
				'PartialAuth' => 'Allow',
				'SequenceNo' => $this->sequence_no,
			);
			
			if (isset($customer_info) && $customer_info->zip && $this->_is_valid_zip($customer_info->zip))
			{
				$customer_info->zip = str_replace('-','',$customer_info->zip);
				$post_data['Zip'] = $customer_info->zip;
			}
						
			$this->controller->load->view('sales/datacap_emv_usb_start_cc_processing', 
				array(
				'form_url' => 'http://127.0.0.1:'.$this->listener_port.'/method4',	
				'post_data' => $post_data,
				'reset_params' => $this->get_emv_pad_reset_params(),
				));
			}
			elseif($customer_info->cc_token)
			{
				if ($cc_amount <= 0)
				{
					$this->controller->_reload(array('error' => lang('sales_charging_card_failed_please_try_again')), false);
					return;
				}
				
				$InvoiceNo = $this->_get_session_invoice_no();				
				
				$post_data = array(
					'IpAddress' => '127.0.0.1',
					'IpPort' => '9000',
					'Memo' => 'PHP POS EMV '.APPLICATION_VERSION,
					'TranType' => 'Credit',
					'TranCode' => 'SaleByRecordNo',
					'OperatorID' => (!defined("ENVIRONMENT") or ENVIRONMENT == 'development') ? 'test' : $this->controller->Employee->get_logged_in_employee_info()->person_id,
					'MerchantID' => $this->merchant_id,
					'LaneID' => $this->controller->Employee->get_logged_in_employee_current_register_id()  && $this->controller->Employee->get_logged_in_employee_current_register_id() <=999 ? $this->controller->Employee->get_logged_in_employee_current_register_id()  : 0,
					'ComPort' => $this->com_port,
					'TStream' => 'Transaction',
					'SecureDevice' => $this->secure_device_non_emv,
					'InvoiceNo' => $InvoiceNo,
					'Purchase' => to_currency_no_money($cc_amount),
					'RecordNo' => $customer_info->cc_token,
					'AcctNo' => 'SecureDevice',
					'Frequency' => 'OneTime',
				);
				
				if (isset($customer_info) && $customer_info->zip && $this->_is_valid_zip($customer_info->zip))
				{
					$customer_info->zip = str_replace('-','',$customer_info->zip);
					$post_data['Zip'] = $customer_info->zip;
				}
				
				$this->controller->load->view('sales/datacap_emv_usb_start_cc_processing_use_saved_card',				
				array(
				'form_url' => 'http://127.0.0.1:'.$this->listener_port.'/method4',
				'payments_cover_total' => $this->controller->_payments_cover_total(),
				'post_data' => $post_data));
			}
			else
			{
				$this->controller->_reload(array('error' => lang('sales_charging_card_failed_please_try_again')), false);
				return;
			}
	}
	
	public function finish_cc_processing_saved_card()
	{
	   $ResponseOrigin = urldecode($this->controller->input->request('ResponseOrigin'));
	   $DSIXReturnCode = urldecode($this->controller->input->request('DSIXReturnCode'));
	   $CmdStatus = urldecode($this->controller->input->request('CmdStatus'));
	   $TextResponse = urldecode($this->controller->input->request('TextResponse'));
	   $MerchantID = urldecode($this->controller->input->request('MerchantID'));
	   $AcctNo = urldecode($this->controller->input->request('AcctNo'));
	   $CardType = urldecode($this->controller->input->request('CardType'));
	   $TranCode = urldecode($this->controller->input->request('TranCode'));
	   $AuthCode = urldecode($this->controller->input->request('AuthCode'));
	   $CaptureStatus = urldecode($this->controller->input->request('CaptureStatus'));
	   $RefNo = urldecode($this->controller->input->request('RefNo'));
	   $InvoiceNo = urldecode($this->controller->input->request('InvoiceNo'));
	   $OperatorID = urldecode($this->controller->input->request('OperatorID'));
	   $Purchase = urldecode($this->controller->input->request('Purchase'));
	   $Authorize = urldecode($this->controller->input->request('Authorize'));
	   $AcqRefData = urldecode($this->controller->input->request('AcqRefData'));
	   $RecordNo = urldecode($this->controller->input->request('RecordNo'));
	   $ProcessData = urldecode($this->controller->input->request('ProcessData'));
		$EntryMethod = urldecode($this->controller->input->request('EntryMethod'));
		$ApplicationLabel = urldecode($this->controller->input->request('ApplicationLabel'));
		$AID = urldecode($this->controller->input->request('AID'));
		$TVR = urldecode($this->controller->input->request('TVR'));
		$IAD = urldecode($this->controller->input->request('IAD'));
		$TSI = urldecode($this->controller->input->request('TSI'));
		$ARC = urldecode($this->controller->input->request('ARC'));
		$CVM = urldecode($this->controller->input->request('CVM'));		
			
		if ($CmdStatus == 'Approved')	
		{
			//Make sure we remove invoice number in case of partial auth...We need a new invoice number
			$this->controller->sale_lib->clear_invoice_no();
			
			$person_info = array('person_id' => $this->controller->sale_lib->get_customer());
			$customer_info = array('cc_token' => $RecordNo);
			$this->controller->Customer->save_customer($person_info,$customer_info,$this->controller->sale_lib->get_customer());
			$this->controller->session->set_userdata('ref_no', $RefNo);
			$this->controller->session->set_userdata('auth_code', $AuthCode);
			$this->controller->session->set_userdata('masked_account', $AcctNo);
			$this->controller->session->set_userdata('card_issuer', $CardType);
			$this->controller->session->set_userdata('cc_token', $RecordNo);
			$this->controller->session->set_userdata('acq_ref_data', $AcqRefData);
			$this->controller->session->set_userdata('process_data', $ProcessData);
			$this->controller->session->set_userdata('aid', $AID);
			$this->controller->session->set_userdata('tvr', $TVR);
			$this->controller->session->set_userdata('iad', $IAD);
			$this->controller->session->set_userdata('arc', $ARC);
			$this->controller->session->set_userdata('cvm', $CVM);
			$this->controller->session->set_userdata('tsi', $TSI);
			$this->controller->session->set_userdata('application_label', $ApplicationLabel);
			$this->controller->session->set_userdata('tran_type', $TranCode);
			
			//If the sale payments cover the total, redirect to complete (receipt)
			if ($this->controller->_payments_cover_total())
			{
				$this->controller->session->set_userdata('CC_SUCCESS', TRUE);
				redirect(site_url('sales/complete'));
			}
			else //Change payment type to Partial Credit Card and show sales interface
			{
				$credit_card_amount = to_currency_no_money($this->controller->sale_lib->get_payment_amount(lang('common_credit')));

				$partial_transaction = array(
					'AuthCode' => $AuthCode,
					'InvoiceNo' => $InvoiceNo,
					'MerchantID' => $this->merchant_id ,
					'OperatorID' => (!defined("ENVIRONMENT") or ENVIRONMENT == 'development') ? 'test' : $this->controller->Employee->get_logged_in_employee_info()->person_id,
					'Purchase' => $Purchase,
					'RefNo' => $RefNo,
					'RecordNo' => $RecordNo,
					'AcqRefData' =>$AcqRefData,
					'ProcessData' => $ProcessData,
				);
									
				$this->controller->sale_lib->delete_payment($this->controller->sale_lib->get_payment_ids(lang('common_credit')));
				$this->controller->sale_lib->add_payment(lang('sales_partial_credit'), $credit_card_amount, FALSE, $AcctNo, $CardType,$AuthCode, $RefNo, $RecordNo,$AcqRefData,$ProcessData,$EntryMethod, $AID, $TVR, $IAD, $TSI, $ARC, $CVM, $TranCode, $ApplicationLabel);
				$this->controller->sale_lib->add_partial_transaction($partial_transaction);
				$this->controller->_reload(array('warning' => lang('sales_credit_card_partially_charged_please_complete_sale_with_another_payment_method')), false);			
			}
		}
		else
		{
			//If we have failed, remove cc token and cc preview
			$person_info = array('person_id' => $this->controller->sale_lib->get_customer());
			$customer_info = array('cc_token' => NULL, 'cc_preview' => NULL, 'card_issuer' => NULL);
			$this->controller->Customer->save_customer($person_info,$customer_info,$this->controller->sale_lib->get_customer());
			
			//Clear cc token for using saved cc info
			$this->controller->sale_lib->clear_use_saved_cc_info();
			
			if ($CmdStatus == 'Declined')
			{
				$this->controller->session->set_userdata('ref_no', $RefNo);
				$this->controller->session->set_userdata('auth_code', $AuthCode);
				$this->controller->session->set_userdata('masked_account', urldecode($this->controller->input->request('Line6')));
				$this->controller->session->set_userdata('entry_method', $EntryMethod);
				$this->controller->session->set_userdata('aid', $AID);
				$this->controller->session->set_userdata('tvr', $TVR);
				$this->controller->session->set_userdata('iad', $IAD);
				$this->controller->session->set_userdata('arc', $ARC);
				$this->controller->session->set_userdata('cvm', $CVM);
				$this->controller->session->set_userdata('tsi', $TSI);
				$this->controller->session->set_userdata('application_label', $ApplicationLabel);
				$this->controller->session->set_userdata('tran_type', $TranCode);
				
				redirect(site_url('sales/declined'));
			}
			else
			{
				$this->controller->_reload(array('error' => lang('sales_charging_card_failed_please_try_again')), false);
			}
		}
	}
	
	public function finish_cc_processing()
	{		
	   $ResponseOrigin = urldecode($this->controller->input->request('ResponseOrigin'));
	   $DSIXReturnCode = urldecode($this->controller->input->request('DSIXReturnCode'));
	   $CmdStatus = urldecode($this->controller->input->request('CmdStatus'));
	   $TextResponse = urldecode($this->controller->input->request('TextResponse'));
	   $MerchantID = urldecode($this->controller->input->request('MerchantID'));
	   $AcctNo = urldecode($this->controller->input->request('AcctNo'));
	   $CardType = urldecode($this->controller->input->request('CardType'));
	   $TranCode = urldecode($this->controller->input->request('TranCode'));
	   $AuthCode = urldecode($this->controller->input->request('AuthCode'));
	   $CaptureStatus = urldecode($this->controller->input->request('CaptureStatus'));
	   $RefNo = urldecode($this->controller->input->request('RefNo'));
	   $InvoiceNo = urldecode($this->controller->input->request('InvoiceNo'));
	   $OperatorID = urldecode($this->controller->input->request('OperatorID'));
	   $Purchase = urldecode($this->controller->input->request('Purchase'));
	   $Authorize = urldecode($this->controller->input->request('Authorize'));
	   $AcqRefData = urldecode($this->controller->input->request('AcqRefData'));
	   $RecordNo = urldecode($this->controller->input->request('RecordNo'));
	   $ProcessData = urldecode($this->controller->input->request('ProcessData'));
		$EntryMethod = urldecode($this->controller->input->request('EntryMethod'));
		$ApplicationLabel = urldecode($this->controller->input->request('ApplicationLabel'));
		$AID = urldecode($this->controller->input->request('AID'));
		$TVR = urldecode($this->controller->input->request('TVR'));
		$IAD = urldecode($this->controller->input->request('IAD'));
		$TSI = urldecode($this->controller->input->request('TSI'));
		$ARC = urldecode($this->controller->input->request('ARC'));
		$CVM = urldecode($this->controller->input->request('CVM'));
		
		if ($CmdStatus == 'Approved')
		{
			//Make sure we remove invoice number in case of partial auth...We need a new invoice number
			$this->controller->sale_lib->clear_invoice_no();
			
			$this->controller->session->set_userdata('ref_no', $RefNo);
			$this->controller->session->set_userdata('auth_code', $AuthCode);
			$this->controller->session->set_userdata('cc_token', $RecordNo);
			$this->controller->session->set_userdata('acq_ref_data', $AcqRefData);
			$this->controller->session->set_userdata('process_data', $ProcessData);
			$this->controller->session->set_userdata('entry_method', $EntryMethod);
			$this->controller->session->set_userdata('aid', $AID);
			$this->controller->session->set_userdata('tvr', $TVR);
			$this->controller->session->set_userdata('iad', $IAD);
			$this->controller->session->set_userdata('arc', $ARC);
			$this->controller->session->set_userdata('cvm', $CVM);
			$this->controller->session->set_userdata('tsi', $TSI);
			$this->controller->session->set_userdata('application_label', $ApplicationLabel);
			$this->controller->session->set_userdata('tran_type', $TranCode);
			
			
			//Payment covers purchase amount
			if ($Authorize == $Purchase)
			{
				$this->controller->session->set_userdata('masked_account', $AcctNo);
				$this->controller->session->set_userdata('card_issuer', $CardType);
				
				$info=$this->controller->Customer->get_info($this->controller->sale_lib->get_customer());
				
				//We want to save/update card when we have a customer AND they have chosen to save
				if (($this->controller->sale_lib->get_save_credit_card_info()) && $this->controller->sale_lib->get_customer() != -1)
				{
					$person_info = array('person_id' => $this->controller->sale_lib->get_customer());
					$customer_info = array('cc_token' => $RecordNo, 'cc_preview' => $AcctNo, 'card_issuer' => $CardType);
					$this->controller->Customer->save_customer($person_info,$customer_info,$this->controller->sale_lib->get_customer());
				}
				
				//If the sale payments cover the total, redirect to complete (receipt)
				if ($this->controller->_payments_cover_total())
				{
					$this->controller->session->set_userdata('CC_SUCCESS', TRUE);
					redirect(site_url('sales/complete'));
				}
				else //Change payment type to Partial Credit Card and show sales interface
				{
					$InvoiceNo = substr((date('mdy')).(time() - strtotime("today")).($this->controller->Employee->get_logged_in_employee_info()->person_id), 0, 16);
					
					$credit_card_amount = to_currency_no_money($this->controller->sale_lib->get_payment_amount(lang('common_credit')));
					
					$partial_transaction = array(
						'AuthCode' => $AuthCode,
						'InvoiceNo' => $InvoiceNo,
						'MerchantID' => $this->merchant_id ,
						'OperatorID' => (!defined("ENVIRONMENT") or ENVIRONMENT == 'development') ? 'test' : $this->controller->Employee->get_logged_in_employee_info()->person_id,
						'Purchase' => $Purchase,
						'RefNo' => $RefNo,
						'RecordNo' => $RecordNo,
						'AcqRefData' =>$AcqRefData,
						'ProcessData' => $ProcessData,
						
					);
					
															
					$this->controller->sale_lib->delete_payment($this->controller->sale_lib->get_payment_ids(lang('common_credit')));
					$this->controller->sale_lib->add_payment(lang('sales_partial_credit'), $credit_card_amount, FALSE, $AcctNo, $CardType,$AuthCode, $RefNo, $RecordNo,$AcqRefData,$ProcessData,$EntryMethod, $AID, $TVR, $IAD, $TSI, $ARC, $CVM, $TranCode, $ApplicationLabel);
					$this->controller->sale_lib->add_partial_transaction($partial_transaction);
					$this->controller->_reload(array('warning' => lang('sales_credit_card_partially_charged_please_complete_sale_with_another_payment_method')), false);			
				}
			}
			elseif($Authorize < $Purchase)
			{
				$InvoiceNo = substr((date('mdy')).(time() - strtotime("today")).($this->controller->Employee->get_logged_in_employee_info()->person_id), 0, 16);
				
				$partial_transaction = array(
					'AuthCode' => $AuthCode,
					'InvoiceNo' => $InvoiceNo,
					'MerchantID' => $this->merchant_id ,
					'OperatorID' => (!defined("ENVIRONMENT") or ENVIRONMENT == 'development') ? 'test' : $this->controller->Employee->get_logged_in_employee_info()->person_id,
					'Purchase' => $Authorize,
					'RefNo' => $RefNo,
					'RecordNo' => $RecordNo,
					'AcqRefData' =>$AcqRefData,
					'ProcessData' => $ProcessData,
				);
				
				$this->controller->sale_lib->delete_payment($this->controller->sale_lib->get_payment_ids(lang('common_credit')));
				$this->controller->sale_lib->add_payment(lang('sales_partial_credit'), $Authorize, FALSE, $AcctNo, $CardType,$AuthCode, $RefNo, $RecordNo,$AcqRefData,$ProcessData,$EntryMethod, $AID, $TVR, $IAD, $TSI, $ARC, $CVM, $TranCode, $ApplicationLabel);
				$this->controller->sale_lib->add_partial_transaction($partial_transaction);
				$this->controller->_reload(array('warning' => lang('sales_credit_card_partially_charged_please_complete_sale_with_another_payment_method')), false);
			}
		}
		else
		{
			if ($CmdStatus == 'Declined')
			{
				$this->controller->session->set_userdata('ref_no', $RefNo);
				$this->controller->session->set_userdata('auth_code', $AuthCode);
				$this->controller->session->set_userdata('masked_account', urldecode($this->controller->input->request('Line6')));
				$this->controller->session->set_userdata('entry_method', $EntryMethod);
				$this->controller->session->set_userdata('aid', $AID);
				$this->controller->session->set_userdata('tvr', $TVR);
				$this->controller->session->set_userdata('iad', $IAD);
				$this->controller->session->set_userdata('arc', $ARC);
				$this->controller->session->set_userdata('cvm', $CVM);
				$this->controller->session->set_userdata('tsi', $TSI);
				$this->controller->session->set_userdata('application_label', $ApplicationLabel);
				$this->controller->session->set_userdata('tran_type', $TranCode);
				
				redirect(site_url('sales/declined'));
			}
			else
			{
				$this->controller->_reload(array('error' => $CmdStatus.': '.$TextResponse), false);
			}
		}
	}
	public function cancel_cc_processing()
	{
		$this->controller->sale_lib->delete_payment($this->controller->sale_lib->get_payment_ids(lang('common_credit')));
		$this->controller->_reload(array('error' => lang('sales_cc_processing_cancelled')), false);
	}
	
	public function void_partial_transactions()
	{		
		if ($partial_transactions = $this->controller->sale_lib->get_partial_transactions())
		{
			$this->controller->load->view('sales/datacap_emv_usb_void_transactions', 
				array(
				'form_url' => 'http://127.0.0.1:'.$this->listener_port.'/method4',	
				'SequenceNo' => $this->sequence_no, 
				'transactions' => 	$partial_transactions,
				'SecureDevice' => $this->secure_device_emv,
				'OperatorID' => (!defined("ENVIRONMENT") or ENVIRONMENT == 'development') ? 'test' : $this->controller->Employee->get_logged_in_employee_info()->person_id,				
				'MerchantID' => $this->merchant_id,
				'IpAddress' => '127.0.0.1',
				'IpPort' => '9000',
				'Memo' => 'PHP POS EMV '.APPLICATION_VERSION,
				'ComPort' => $this->com_port,
				'LaneID' => $this->controller->Employee->get_logged_in_employee_current_register_id()  && $this->controller->Employee->get_logged_in_employee_current_register_id() <=999 ? $this->controller->Employee->get_logged_in_employee_current_register_id()  : 0,
				
			));
		}
		
		//Always return true as error handling is in JS
		return TRUE;
	}	
	
	public function void_sale($sale_id)
	{
		if ($this->controller->Sale->can_void_cc_sale($sale_id))
		{
			$payments = $this->_get_cc_payments_for_sale($sale_id);
			
			$transactions = array();
			foreach($payments as $payment)
			{
				$InvoiceNo = substr((date('mdy')).(time() - strtotime("today")).($this->controller->Employee->get_logged_in_employee_info()->person_id), 0, 16);
				
				$transactions[] = array(
					'AuthCode' => $payment['auth_code'],
					'InvoiceNo' => $InvoiceNo,
					'MerchantID' => $this->merchant_id ,
					'OperatorID' => (!defined("ENVIRONMENT") or ENVIRONMENT == 'development') ? 'test' : $this->controller->Employee->get_logged_in_employee_info()->person_id,
					'Purchase' => to_currency_no_money($payment['payment_amount']),
					'RefNo' =>  $payment['ref_no'],
					'RecordNo' => $payment['cc_token'],
					'AcqRefData' =>$payment['acq_ref_data'],
					'ProcessData' => $payment['process_data'],
				);
			}
			
			$this->controller->load->view('sales/datacap_emv_usb_void_transactions', 
				array(
				'include_header_and_footer' => TRUE,
				'is_sale_delete' => TRUE,
				'SequenceNo' => $this->sequence_no, 
				'SecureDevice' => $this->secure_device_emv,
				'sale_id' => $sale_id,
				'OperatorID' => (!defined("ENVIRONMENT") or ENVIRONMENT == 'development') ? 'test' : $this->controller->Employee->get_logged_in_employee_info()->person_id,				
				'form_url' => 'http://127.0.0.1:'.$this->listener_port.'/method4',
				'transactions' => 	$transactions,
				'MerchantID' => $this->merchant_id,
				'IpAddress' => '127.0.0.1',
				'IpPort' => '9000',
				'Memo' => 'PHP POS EMV '.APPLICATION_VERSION,
				'ComPort' => $this->com_port,
				'LaneID' => $this->controller->Employee->get_logged_in_employee_current_register_id()  && $this->controller->Employee->get_logged_in_employee_current_register_id() <=999 ? $this->controller->Employee->get_logged_in_employee_current_register_id()  : 0,
			));			
			//Always return true as error handling is in JS
			return TRUE;
		}
		
		return FALSE;
	}
	
	public function void_return($sale_id)
	{
		if ($this->controller->Sale->can_void_cc_return($sale_id))
		{
			$payments = $this->_get_cc_payments_for_sale($sale_id);
			
			$transactions = array();
			foreach($payments as $payment)
			{
				$InvoiceNo = substr((date('mdy')).(time() - strtotime("today")).($this->controller->Employee->get_logged_in_employee_info()->person_id), 0, 16);
				
				$transactions[] = array(
					'AuthCode' => $payment['auth_code'],
					'InvoiceNo' => $InvoiceNo,
					'MerchantID' => $this->merchant_id ,
					'OperatorID' => (!defined("ENVIRONMENT") or ENVIRONMENT == 'development') ? 'test' : $this->controller->Employee->get_logged_in_employee_info()->person_id,
					'Purchase' => to_currency_no_money(abs($payment['payment_amount'])),
					'RefNo' =>  $payment['ref_no'],
					'RecordNo' => $payment['cc_token'],
					'AcqRefData' =>$payment['acq_ref_data'],
					'ProcessData' => $payment['process_data'],
				);
			}
			
			$this->controller->load->view('sales/datacap_emv_usb_void_return_transactions', 
				array(
				'sale_id' => $sale_id,
				'OperatorID' => (!defined("ENVIRONMENT") or ENVIRONMENT == 'development') ? 'test' : $this->controller->Employee->get_logged_in_employee_info()->person_id,				
				'form_url' => 'http://127.0.0.1:'.$this->listener_port.'/method4',
				'SequenceNo' => $this->sequence_no, 
				'SecureDevice' => $this->secure_device_emv,
				'transactions' => 	$transactions,
				'MerchantID' => $this->merchant_id,
				'IpAddress' => '127.0.0.1',
				'IpPort' => '9000',
				'Memo' => 'PHP POS EMV '.APPLICATION_VERSION,
				'ComPort' => $this->com_port,
				'LaneID' => $this->controller->Employee->get_logged_in_employee_current_register_id()  && $this->controller->Employee->get_logged_in_employee_current_register_id() <=999 ? $this->controller->Employee->get_logged_in_employee_current_register_id()  : 0,
			));			
			//Always return true as error handling is in JS
			return TRUE;
		}
		
		return FALSE;
	}
}