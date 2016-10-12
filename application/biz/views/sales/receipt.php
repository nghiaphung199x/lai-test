<?php $this->load->view("partial/header"); ?>
<script src="<?php echo base_url().'assets/js/jquery.maskMoney.js';?>" type="text/javascript"></script>
<?php
	$this->load->helper('sale');
	$return_policy = ($loc_return_policy = $this->Location->get_info_for_key('return_policy', isset($override_location_id) ? $override_location_id : FALSE)) ? $loc_return_policy : $this->config->item('return_policy');
	$is_integrated_credit_sale = is_sale_integrated_cc_processing();
	$is_credit_card_sale = is_credit_card_sale();
	
	$signature_needed = ($is_credit_card_sale && !$is_integrated_credit_sale) ||  is_store_account_sale();
	
	//Check for EMV signature for non pin verified
	if (!$signature_needed && $is_integrated_credit_sale)
	{
		foreach($payments as $payment_id=>$payment)
		{
			if ($payment['cvm'] != 'PIN VERIFIED')
			{
				$signature_needed = TRUE;
				break;
			}
		}
	}
	
	if (isset($error_message))
	{
		echo '<h1 style="text-align: center;">'.$error_message.'</h1>';
		exit;
	}
?>

<div class="manage_buttons hidden-print">
	<div class="row">
		<div class="col-md-6"><?php 
                ?>
			<div class="hidden-print search no-left-border">
				<ul class="list-inline print-buttons">
                                    <?php if($this->input->get('fulfillment')!== '1'){?>
                                    <li style="padding-right: 0px;">
							<?php 
							 if ($sale_id_raw != lang('sales_test_mode_transaction') && !$store_account_payment && $this->Employee->has_module_action_permission('sales', 'edit_sale', $this->Employee->get_logged_in_employee_info()->person_id)){

						   		$edit_sale_url = (isset($sale_type) && ($sale_type == lang('common_layaway') || $sale_type == lang('common_estimate'))) ? 'unsuspend' : 'change_sale';
								echo form_open("sales/$edit_sale_url/".$sale_id_raw,array('id'=>'sales_change_form')); ?>
								<button class="btn btn-primary btn-lg hidden-print" id="edit_sale"> <?php echo lang('sales_edit'); ?> </button>

							<?php }	?>
							</form>		
						</li>
					
					<li style="padding-right: 0px;">
						<button class="btn btn-primary btn-lg hidden-print gift_receipt" id="gift_receipt_button" onclick="toggle_gift_receipt()" > <?php echo lang('sales_gift_receipt'); ?> </button>
					</li>
					<li style="padding-right: 0px;">
						<?php if (!empty($customer_email)) { ?>
							<?php echo anchor('sales/email_receipt/'.$sale_id_raw, lang('common_email_receipt'), array('id' => 'email_receipt','class' => 'btn btn-primary btn-lg hidden-print'));?>
						<?php }?>
					</li>
                                    <?php }else {?>
					<li>
						<button class="btn btn-primary btn-lg hidden-print" id="fufillment_sheet_button" onclick="window.open('<?php echo site_url("sales/receipt/$sale_id_raw"); ?>', 'blank');" > <?php echo lang('sales_receipt'); ?></button>
					</li>
                                    <?php }?>
                                    <?php 
                                            if($this->config->item('config_sales_receipt_pdf_size')=='a8' && $this->input->get('fulfillment')==NULL ){?>
                                                <li style="padding-right: 0px;">
                                                    <button class="btn btn-primary btn-lg hidden-print" id="fufillment_sheet_button" onclick="window.open('<?php echo site_url("sales/receipt/$sale_id_raw?fulfillment=0&type=".$type); ?>', 'blank');" > <?php echo lang('sales_regular_receipt'); ?></button>
                                                </li>
                                    <?php }?>
				</ul>
			</div>
		</div>
		<div class="col-md-6">	
			<div class="buttons-list">
				<div class="pull-right-btn">
					<ul class="list-inline print-buttons">
						<li>
							<?php
							echo form_checkbox(array(
								'name'        => 'print_duplicate_receipt',
								'id'          => 'print_duplicate_receipt',
								'value'       => '1',
							)).'&nbsp;<label for="print_duplicate_receipt"><span></span>'.lang('sales_duplicate_receipt').'</label>';
								?>		
						</li>
						<li>
							<button class="btn btn-primary btn-lg hidden-print" id="print_button" onclick="print_receipt()" > <?php echo lang('common_print'); ?> </button>		
						</li>
						<li>
							<button class="btn btn-primary btn-lg hidden-print" id="new_sale_button_1" onclick="window.location='<?php echo site_url('sales'); ?>'" > <?php echo lang('sales_new_sale'); ?> </button>	
						</li>
					</ul>
				</div>
			</div>				
		</div>
	</div>
</div>
<div class="row manage-table receipt_<?php echo $this->config->item('receipt_text_size') ? $this->config->item('receipt_text_size') : 'small';?>" id="receipt_wrapper">
	<div class="col-md-12" id="receipt_wrapper_inner">
		<div class="panel panel-piluku">
			<?php echo $pdf_block_html; ?>
	</div>
			<!--container-->
		</div>		
	</div>
</div>
</div>


<div id="duplicate_receipt_holder">
	
</div>

<?php if ($this->config->item('print_after_sale') && $this->uri->segment(2) == 'complete')
{
?>
<script type="text/javascript">
$(window).bind("load", function() {
	print_receipt();
});
</script>
<?php }  ?>

<script type="text/javascript">

$(document).ready(function(){
	
	$("#edit_sale").click(function(e)
	{
		e.preventDefault();
		bootbox.confirm(<?php echo json_encode(lang('sales_sale_edit_confirm')); ?>,function(result)
		{
			if (result)
			{
				$("#sales_change_form").submit();
			}
		});
	});
	$("#email_receipt").click(function()
	{
		$.get($(this).attr('href'), function()
		{
			show_feedback('success', <?php echo json_encode(lang('common_receipt_sent')); ?>, <?php echo json_encode(lang('common_success')); ?>);
			
		});
		
		return false;
	});
});

$('#print_duplicate_receipt').click(function()
{
	if ($('#print_duplicate_receipt').prop('checked'))
	{
	   var receipt = $('#receipt_wrapper').clone();
	   $('#duplicate_receipt_holder').html(receipt);
		$("#duplicate_receipt_holder").addClass('visible-print-block');
		$("#duplicate_receipt_holder .receipt_type_label").text(<?php echo json_encode(lang('sales_duplicate_receipt')); ?>);
		$(".receipt_type_label").show();		
		$(".receipt_type_label").addClass('show_receipt_labels');		
	}
	else
	{
		$("#duplicate_receipt_holder").empty();
		$("#duplicate_receipt_holder").removeClass('visible-print-block');
		$(".receipt_type_label").hide();
		$(".receipt_type_label").removeClass('show_receipt_labels');	
	}
});

<?php
$this->load->helper('sale');
if ($this->config->item('automatically_print_duplicate_receipt_for_cc_transactions') && $is_credit_card_sale)
{
?>
	$("#print_duplicate_receipt").trigger('click');
<?php
}
?>

function print_receipt()
 {
	var conten_pdf = document.getElementById('pdf_content').outerHTML;
	var css = $('#receipt_wrapper style[type="text/css"]').html();
	var html ='<style type="text/css">';
	html += css ;
	html +='</style>';
	html+=conten_pdf;
	newWin = window.open("");
	newWin.document.write(html);
	newWin.print();
 	<?php
 	if ($this->config->item('redirect_to_sale_or_recv_screen_after_printing_receipt'))
 	{
 	?>
 	window.location = '<?php echo site_url('sales'); ?>';
 	<?php
 	}
 	?>
 }
 
 function toggle_gift_receipt()
 {
	 var gift_receipt_text = <?php echo json_encode(lang('sales_gift_receipt')); ?>;
	 var regular_receipt_text = <?php echo json_encode(lang('sales_regular_receipt')); ?>;
	 
	 if ($("#gift_receipt_button").hasClass('regular_receipt'))
	 {
		 $('#gift_receipt_button').addClass('gift_receipt');	 	
		 $('#gift_receipt_button').removeClass('regular_receipt');
		 $("#gift_receipt_button").text(gift_receipt_text);	
		 $('.gift_receipt_element').show();	
	 }
	 else
	 {
		 $('#gift_receipt_button').removeClass('gift_receipt');	 	
		 $('#gift_receipt_button').addClass('regular_receipt');
		 $("#gift_receipt_button").text(regular_receipt_text);
		 $('.gift_receipt_element').hide();	
	 }
 	
 }
 
//timer for sig refresh
var refresh_timer;
var sig_canvas = document.getElementById('sig_cnv');

<?php
//Only use Sig touch on mobile
if ($this->agent->is_mobile())
{
?>
	var signaturePad = new SignaturePad(sig_canvas);
<?php
}
?>
$("#capture_digital_sig_button").click(function()
{	
	<?php
	//Only use Sig touch on mobile
	if ($this->agent->is_mobile())
	{
	?>
		signaturePad.clear();
	<?php
	}
	else
	{
	?>
		try
		{
			if (TabletConnectQuery()==0)
			{
				bootbox.alert(<?php echo json_encode(lang('sales_unable_to_connect_to_signature_pad')); ?>);
				return;
			}	
		}
		catch(exception) 
		{
			bootbox.alert(<?php echo json_encode(lang('sales_unable_to_connect_to_signature_pad')); ?>);
			return;			
		}
		
	   var ctx = document.getElementById('sig_cnv').getContext('2d');
	   SigWebSetDisplayTarget(ctx);
	   SetDisplayXSize( 500 );
	   SetDisplayYSize( 100 );
	   SetJustifyMode(0);
	   refresh_timer = SetTabletState(1,ctx,50);
	   KeyPadClearHotSpotList();
	   ClearSigWindow(1);
	   ClearTablet();
	<?php
	}
	?>
	
	$("#capture_digital_sig_button").hide();
	$("#digital_sig_holder").show();
});

$("#capture_digital_sig_clear_button").click(function()
{
	<?php
	//Only use Sig touch on mobile
	if ($this->agent->is_mobile())
	{
	?>
		signaturePad.clear();
	<?php
	}
	else
	{
	?>
   	ClearTablet();	
	<?php
	}
	?>
});

$("#capture_digital_sig_done_button").click(function()
{
	<?php
	//Only use Sig touch on mobile
	if ($this->agent->is_mobile())
	{
	?>
	   if(signaturePad.isEmpty())
	   {
	      bootbox.alert(<?php echo json_encode(lang('sales_no_sig_captured')); ?>);
	   }
	   else
	   {
			SigImageCallback(signaturePad.toDataURL().split(",")[1]);
			$("#capture_digital_sig_button").show();
	   }	
	<?php
	}
	else
	{
	?>
		if(NumberOfTabletPoints() == 0)
		{
		   bootbox.alert(<?php echo json_encode(lang('sales_no_sig_captured')); ?>);
		}
		else
		{
		   SetTabletState(0,refresh_timer);
		   //RETURN TOPAZ-FORMAT SIGSTRING
		   SetSigCompressionMode(1);
			var sig = GetSigString();

		   //RETURN BMP BYTE ARRAY CONVERTED TO BASE64 STRING
		   SetImageXSize(500);
		   SetImageYSize(100);
		   SetImagePenWidth(5);
		   GetSigImageB64(SigImageCallback);
			$("#capture_digital_sig_button").show();
		}
	<?php
	}
	?>
});

function SigImageCallback( str )
{
 $("#digital_sig_holder").hide();
 $.post('<?php echo site_url('sales/sig_save'); ?>', {sale_id: <?php echo json_encode($sale_id_raw); ?>, image: str}, function(response)
 {
	 $("#signature_holder").empty();
	 $("#signature_holder").append('<img src="'+SITE_URL+'/app_files/view/'+response.file_id+'" width="250" />');
 }, 'json');

}
 
<?php
//EMV Usb Reset
if (isset($reset_params))
{
?>
 var data = {};
 <?php
 foreach($reset_params['post_data'] as $name=>$value)
 {
	 if ($name && $value)
	 {
	 ?>
	 data['<?php echo $name; ?>'] = '<?php echo $value; ?>';
 	 <?php 
	 }
 }
 ?>	

 mercury_emv_pad_reset(<?php echo json_encode($reset_params['post_host']); ?>, <?php echo $this->Location->get_info_for_key('listener_port'); ?>, data);
<?php
}
?>
</script>

<?php if($is_integrated_credit_sale && $is_sale) { ?>
<script type="text/javascript">
show_feedback('success', <?php echo json_encode(lang('sales_credit_card_processing_success')); ?>, <?php echo json_encode(lang('common_success')); ?>);	
</script>
<?php } ?>

<!-- This is used for mobile apps to print receipt-->
<script type="text/print" id="print_output"><?php echo $this->config->item('company'); ?>

<?php echo $this->Location->get_info_for_key('address',isset($override_location_id) ? $override_location_id : FALSE); ?>

<?php echo $this->Location->get_info_for_key('phone',isset($override_location_id) ? $override_location_id : FALSE); ?>

<?php if($this->config->item('website')) { ?>
<?php echo $this->config->item('website'); ?>
<?php } ?>

<?php echo $receipt_title; ?>

<?php echo $transaction_time; ?>

<?php if(isset($customer))
{
?>
<?php echo lang('common_customer').": ".$customer; ?>
<?php if (!$this->config->item('remove_customer_contact_info_from_receipt')) { ?>
	
<?php if(!empty($customer_address_1)){ ?><?php echo lang('common_address'); ?>: <?php echo $customer_address_1. ' '.$customer_address_2; ?>
	
<?php } ?>
<?php if (!empty($customer_city)) { echo $customer_city.' '.$customer_state.', '.$customer_zip; ?>

<?php } ?>
<?php if (!empty($customer_country)) { echo $customer_country; ?>
	
<?php } ?>
<?php if(!empty($customer_phone)){ ?><?php echo lang('common_phone_number'); ?> : <?php echo $customer_phone; ?>
	
<?php } ?>
<?php if(!empty($customer_email)){ ?><?php echo lang('common_email'); ?> : <?php echo $customer_email; ?><?php } ?>

<?php
}
else
{
?>
	
<?php
}
}
?>
<?php echo lang('sales_id').": ".$sale_id; ?>
<?php if (isset($sale_type)) { ?>
<?php echo $sale_type; ?>
<?php } ?>

<?php echo lang('common_employee').": ".$employee; ?>

<?php 
if($this->Location->get_info_for_key('enable_credit_card_processing',isset($override_location_id) ? $override_location_id : FALSE))
{
	echo lang('common_merchant_id').': '.$this->Location->get_merchant_id(isset($override_location_id) ? $override_location_id : FALSE);
}
?>

<?php echo lang('common_item'); ?>            <?php echo lang('common_price'); ?> <?php echo lang('common_quantity'); ?><?php if($discount_exists){echo ' '.lang('common_discount_percent');}?> <?php echo lang('common_total'); ?>

---------------------------------------
<?php
foreach(array_reverse($cart, true) as $line=>$item)
{
?>
<?php echo character_limiter($item['name'], 14,'...'); ?><?php echo strlen($item['name']) < 14 ? str_repeat(' ', 14 - strlen($item['name'])) : ''; ?> <?php echo str_replace('<span style="white-space:nowrap;">-</span>', '-', to_currency($item['price'])); ?> <?php echo to_quantity($item['quantity']); ?><?php if($discount_exists){echo ' '.$item['discount'];}?> <?php echo str_replace('<span style="white-space:nowrap;">-</span>', '-', to_currency($item['price']*$item['quantity']-$item['price']*$item['quantity']*$item['discount']/100)); ?>

  <?php echo $item['description']; ?>  <?php echo isset($item['serialnumber']) ? $item['serialnumber'] : ''; ?>
	

<?php
}
?>

<?php echo lang('common_sub_total'); ?>: <?php echo str_replace('<span style="white-space:nowrap;">-</span>', '-', to_currency($subtotal)); ?>


<?php foreach($taxes as $name=>$value) { ?>
<?php echo $name; ?>: <?php echo str_replace('<span style="white-space:nowrap;">-</span>', '-', to_currency($value)); ?>

<?php }; ?>

<?php echo lang('common_total'); ?>: <?php echo $this->config->item('round_cash_on_sales') && $is_sale_cash_payment ?  str_replace('<span style="white-space:nowrap;">-</span>', '-', to_currency(round_to_nearest_05($total))) : str_replace('<span style="white-space:nowrap;">-</span>', '-', to_currency($total)); ?>

<?php echo lang('common_items_sold'); ?>: <?php echo to_quantity($number_of_items_sold); ?>

<?php
	foreach($payments as $payment_id=>$payment)
{ ?>

<?php echo (isset($show_payment_times) && $show_payment_times) ?  date(get_date_format().' '.get_time_format(), strtotime($payment['payment_date'])) : lang('common_payment'); ?>  <?php if (($is_integrated_credit_sale || sale_has_partial_credit_card_payment()) && ($payment['payment_type'] == lang('common_credit') ||  $payment['payment_type'] == lang('sales_partial_credit'))) { echo $payment['card_issuer']. ': '.$payment['truncated_card']; ?> <?php } else { ?><?php $splitpayment=explode(':',$payment['payment_type']); echo $splitpayment[0]; ?> <?php } ?><?php echo $this->config->item('round_cash_on_sales') && $payment['payment_type'] == lang('common_cash') ?  str_replace('<span style="white-space:nowrap;">-</span>', '-', to_currency(round_to_nearest_05($payment['payment_amount']))) : str_replace('<span style="white-space:nowrap;">-</span>', '-', to_currency($payment['payment_amount'])); ?>

<?php if ($payment['entry_method']) { ?>
	
<?php echo lang('sales_entry_method'). ': '.$payment['entry_method']; ?>
	
<?php } ?>
<?php if ($payment['tran_type']) { ?><?php echo lang('sales_transaction_type'). ': '.$payment['tran_type']; ?>
	
<?php } ?>
<?php if ($payment['application_label']) { ?><?php echo lang('sales_application_label'). ': '.$payment['application_label']; ?>
	
<?php } ?>
<?php if ($payment['ref_no']) { ?><?php echo lang('sales_ref_no'). ': '.$payment['ref_no']; ?>
	
<?php } ?>
<?php if ($payment['auth_code']) { ?><?php echo lang('sales_auth_code'). ': '.$payment['auth_code']; ?>
	
<?php } ?>
<?php if ($payment['aid']) { ?><?php echo 'AID: '.$payment['aid']; ?>
	
<?php } ?>
<?php if ($payment['tvr']) { ?><?php echo 'TVR: '.$payment['tvr']; ?>

<?php } ?>
<?php if ($payment['tsi']) { ?><?php echo 'TSI: '.$payment['tsi']; ?>
	
<?php } ?>
<?php if ($payment['arc']) { ?><?php echo 'ARC: '.$payment['arc']; ?>
	
<?php } ?>
<?php if ($payment['cvm']) { ?><?php echo 'CVM: '.$payment['cvm']; ?>
<?php } ?>
<?php
}
?>	
<?php foreach($payments as $payment) { $giftcard_payment_row = explode(':', $payment['payment_type']);?>
<?php if (strpos($payment['payment_type'], lang('common_giftcard'))!== FALSE) {?><?php echo lang('sales_giftcard_balance'); ?>  <?php echo $payment['payment_type'];?>: <?php echo str_replace('<span style="white-space:nowrap;">-</span>', '-', to_currency($this->Giftcard->get_giftcard_value(end($giftcard_payment_row)))); ?>
	<?php }?>
<?php }?>
<?php if ($amount_change >= 0) {?>
<?php echo lang('common_change_due'); ?>: <?php echo $this->config->item('round_cash_on_sales')  && $is_sale_cash_payment ?  str_replace('<span style="white-space:nowrap;">-</span>', '-', to_currency(round_to_nearest_05($amount_change))) : str_replace('<span style="white-space:nowrap;">-</span>', '-', to_currency($amount_change)); ?>
<?php
}
else
{
?>
<?php echo lang('common_amount_due'); ?>: <?php echo $this->config->item('round_cash_on_sales')  && $is_sale_cash_payment ?  str_replace('<span style="white-space:nowrap;">-</span>', '-', to_currency(round_to_nearest_05($amount_change * -1))) : str_replace('<span style="white-space:nowrap;">-</span>', '-', to_currency($amount_change * -1)); ?>
<?php
} 
?>
<?php if ($this->config->item('enable_customer_loyalty_system') && isset($customer_points) && !$this->config->item('hide_points_on_receipt')) {?>
	
<?php echo lang('common_points'); ?>: <?php echo to_currency_no_money($customer_points); ?>
<?php } ?>

<?php if (isset($customer_balance_for_sale) && $customer_balance_for_sale !== FALSE && !$this->config->item('hide_store_account_balance_on_receipt')) {?>

<?php echo lang('sales_customer_account_balance'); ?>: <?php echo to_currency($customer_balance_for_sale); ?>
<?php
}
?>
<?php
if ($ref_no)
{
?>

<?php echo lang('sales_ref_no'); ?>: <?php echo $ref_no; ?>
<?php
}
if (isset($auth_code) && $auth_code)
{
?>

<?php echo lang('sales_auth_code'); ?>: <?php echo $auth_code; ?>
<?php
}
?>
<?php if($show_comment_on_receipt==1){echo $comment;} ?>

<?php if(!$this->config->item('hide_signature')) { ?>
<?php if ($signature_needed) {?>		
<?php echo lang('sales_signature'); ?>: 
---------------------------------------
<?php 
if ($is_credit_card_sale)
{
	echo lang('sales_card_statement');
}
?><?php }?><?php } ?></script>
<?php $this->load->view("partial/footer"); ?>
