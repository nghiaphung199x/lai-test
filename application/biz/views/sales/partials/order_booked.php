<style type="text/css">
	#pdf_content {
		width: 700px;
		display: block;
		overflow: hidden;
		position: relative;
		padding: 20px;
		font-size: 12px;
	}
        #table-responsive{
            max-width: 700px;
        }
	#pdf_logo img {
		max-height: 70px;
	}
	#company_name {
		text-transform: uppercase;
		font-weight: bold;
		color: #002FC2
	}
	#pdf_content span {
		color: #002FC2;
	}
	#pdf_title {
		width: 100%;
		text-align: center;
		text-transform: uppercase;
		font-weight: bold;
		font-size: 16px;
		margin-top: 12px;
	}
	#pdf_tbl_items {
		border-collapse: collapse;
		font-size: 12px;
		margin: 10px 0;
	}
	#pdf_tbl_items tboby {
		display: table-row-group;
		vertical-align: middle;
		border-color: inherit;
	}
	#pdf_tbl_items tr {
		display: table-row;
		vertical-align: inherit;
		border-color: inherit;
	}

	#pdf_tbl_items th, #pdf_tbl_items td {
		border: 1px solid #000;
		padding: 3px;
	}

	#pdf_signature {
		min-height: 150px;
	}
	#pdf_signature div {
		text-align: center;
	}
	#pdf_signature lable {
		font-size: 14px;
		font-weight: bold;
	}

	.fl {
		float: left;
	}
	.fr {
		float: right;
	}
	.clb {
		clear: both;
	}
	.w50 {
		width: 50%;
	}

	.w20 {
		width: 20%;
	}

	.w100 {
		width: 100%;
	}
	.pb20 {
		padding-bottom: 20px;
	}

	.pt20 {
		padding-top: 20px;
	}

	#pdf_header h3, #pdf_header p {
		text-align: center;
	}
	#pdf_footer {
		text-align: center;
	}
	#pdf_content table td, #pdf_content table th {
		text-align: right;
		height: auto !important;
	}
	p {
		margin: 3px 0;
	}
	.w150px {
		width: 150px;
	}
	.fontI {
		font-style: italic;
	}
        .border-bottom{
                border-bottom: 1px dotted rgb(0, 0, 0) !important;
        }
        .border-left{
                border-left: none !important;
        }
        .border-right{
                border-right: none!important;
        }
        .border-top{
                border-top: none !important;
        }
        #policy{
                font-weight: bold;
                text-align: center;
                font-size: 1.3em;
                margin-top: 10px; 
        }
        .text-center{
            direction: rtl !important;
            text-align: center !important;
        }
        .text-bold{
            font-weight: bold !important;
        }
        table th, table td{
            line-height: normal !important;
        }
    /* Medium Devices, Desktops */
    @media only screen and (max-width : 992px) {

    }

    /* Small Devices, Tablets */
    @media only screen and (max-width : 768px) {
        .table-responsive{
               max-width: 700px;
            }
    }
    @media only screen and (max-width : 767px) and (max-width: 481px) {
        .table-responsive{
               max-width: 700px;
            }
    }

    /* Extra Small Devices, Phones */ 
    @media only screen and (max-width : 480px) {
        .table-responsive{
                max-width: 300px;
            } 
    }

    /* Custom, iPhone Retina */ 
    @media only screen and (max-width : 320px) {
        .table-responsive{
                max-width: 284px;
            } 
    }
        /*@media screen and (min-device-width: 481px) and (max-device-width: 768px)*/

</style>
<div id="pdf_content">
	<div id="pdf_header">
		<div id="pdf_logo" class="fl">
			<?php if($this->config->item('company_logo')) {?>
				<?php echo img(array('src' => $this->Appconfig->get_logo_image())); ?>
			<?php } ?>
		</div>
		<div id="pdf_company" class="fl">
			<p id="company_name"><?php echo $this->config->item('company'); ?></p>
			<p><span><?php echo nl2br($this->Location->get_info_for_key('address', isset($override_location_id) ? $override_location_id : FALSE)); ?></span></p>
			<p>Điện Thoại: <span><?php echo $this->Location->get_info_for_key('phone', isset($override_location_id) ? $override_location_id : FALSE); ?></span></p>
			<?php if($this->config->item('website')) { ?>
				<p>Website: <span><?php echo $this->config->item('website'); ?></span></p>
			<?php } ?>
		</div>

		<div class="fr w150px">
			<p>Số: <?php echo $sale_id; ?></p>
			<p>Ngày: <span><?php echo date(get_date_format(), strtotime($transaction_time)); ?></span></p>
		</div>
	</div>
	<div id="pdf_title" class="clb">
		<p>HÓA ĐƠN ĐẶT HÀNG</p>
	</div>

	<div id="pdf_customer">
		<p>Họ tên khách hàng: <?php if ($customer) { ?> <span><?php echo $customer; ?></span> <?php } ?></p>
		<p>Ghi chú: </p>
		<p>Kho: <?php if ($this->Location->count_all() > 1) { ?><span><?php echo $this->Location->get_info_for_key('name', isset($override_location_id) ? $override_location_id : FALSE); ?></span><?php } ?></p>
		<p>Địa chỉ kho: <span><?php echo nl2br($this->Location->get_info_for_key('address', isset($override_location_id) ? $override_location_id : FALSE)); ?></span></p>
		
	</div>	
	<div class="w100 clb table-responsive">
		<table id="pdf_tbl_items" class="w100 table">
			<tbody>
				<tr>
					<th>STT</th>
					<th>Mã MH</th>
					<th><?php echo lang('common_item_name'); ?></th>
                                        <th class="text-center"><?php echo lang('common_unit_report')?></th>
					<th><?php echo lang('common_quantity'); ?></th>
                                        <th><?php echo lang('common_unit_sales').' ('.$this->config->item('currency_symbol').')'; ?></th>
					
					<th>Chiết khấu (%)</th>
                                        <th class="text-center"><?php echo lang('reports_taxes') .' %'?></th>
					<th><?php echo lang('common_unit_total').' ('.$this->config->item('currency_symbol').')'; ?></th>
				</tr>

				<?php
					if ($discount_item_line = $this->sale_lib->get_line_for_flat_discount_item())
					{
						$discount_item = $cart[$discount_item_line];
						unset($cart[$discount_item_line]);
						array_unshift($cart,$discount_item);
					}
				 
				$number_of_items_sold = 0;
				$stt = 0;
                                $total_money = 0;
				foreach(array_reverse($cart, true) as $line => $item)
				{
                                    $total_money +=($item['price']*$item['quantity']-$item['price']*$item['quantity']*$item['discount']/100);
					$stt ++;
					 if ($item['name'] != lang('sales_store_account_payment') && $item['name'] != lang('common_discount'))
					 {
				 		 $number_of_items_sold = $number_of_items_sold + $item['quantity'];
					 }
					 
					$item_number_for_receipt = false;
					
					if ($this->config->item('show_item_id_on_receipt'))
					{
						switch($this->config->item('id_to_show_on_sale_interface'))
						{
							case 'number':
							$item_number_for_receipt = array_key_exists('item_number', $item) ? H($item['item_number']) : H($item['item_kit_number']);
							break;
						
							case 'product_id':
							$item_number_for_receipt = array_key_exists('product_id', $item) ? H($item['product_id']) : ''; 
							break;
						
							case 'id':
							$item_number_for_receipt = array_key_exists('item_id', $item) ? H($item['item_id']) : 'KIT '.H($item['item_kit_id']); 
							break;
						
							default:
							$item_number_for_receipt = array_key_exists('item_number', $item) ? H($item['item_number']) : H($item['item_kit_number']);
							break;
						}
					}
				?>

					<tr>
						<td><?php echo $stt; ?></td>
						<td><?php echo $item['item_number']; ?></td>
						<td><?php echo $item['name']; ?><?php if ($item_number_for_receipt){ ?> - <?php echo $item_number_for_receipt; ?><?php } ?><?php if ($item['size']){ ?> (<?php echo $item['size']; ?>)<?php } ?></td>
                                                <td><?php echo isset($item['measure']) ? $item['measure'] : ''; ?></td>
						<td><?php echo to_quantity(abs($item['quantity'])); ?></td>
                                                <td><?php echo NumberFormatToCurrency($item['price']); ?></td>
						<td><?php echo $item['discount']; ?></td>
                                                <td><?php echo to_quantity($item['tax_included']);?></td>
						<td><?php echo NumberFormatToCurrency(abs($item['price']*$item['quantity']-$item['price']*$item['quantity']*$item['discount']/100)); ?></td>
					</tr>
					<?php if (!$item['description']=="" ||(isset($item['serialnumber']) && $item['serialnumber'] !="") ) {?>
					<tr>
						<td colspan="9">
							<?php if(!$item['description']==""){ ?>
								<div class="invoice-desc"><?php echo $item['description']; ?></div>
							<?php } ?>

							<?php if(isset($item['serialnumber']) && $item['serialnumber'] !=""){ ?>
								<div class="invoice-desc"><?php echo $item['serialnumber']; ?></div>
							<?php } ?>
						</td>
					</tr>
                                        
					<?php } ?>
				<?php } ?>
                                        <tr>
                                            <td class="border-bottom text-bold" colspan="9"><?php echo lang('common_total_money').': '; echo NumberFormatToCurrency(abs($total_money)); ?></td>
                                        </tr>
				 <?php if ($this->config->item('group_all_taxes_on_receipt')) { ?>
					<?php 
					$total_tax = 0;
					foreach($taxes as $name=>$value) 
					{
						$total_tax+=$value;
				 	}
					?>
					<tr>
                                            <td colspan="9" class="border-bottom border-top text-bold"><?php echo lang('common_tax').': '; 
                                                echo NumberFormatToCurrency($total_tax); ?></td>
					</tr>
				<?php }else {?>
					<?php foreach($taxes as $name=>$value) { ?>
						<tr>
							<td colspan="9"  class="border-bottom border-top text-bold"><?php echo $name.': '; 
                                                        echo NumberFormatToCurrency($value); ?></td>
						</tr>
					<?php }; ?>
				<?php } ?>

				<tr>
					<td class="border-bottom border-top text-bold" colspan="9"><?php echo lang('common_total').': ';  echo $this->config->item('round_cash_on_sales') && $is_sale_cash_payment ? NumberFormatToCurrency(round_to_nearest_05($total)) : NumberFormatToCurrency($total); ?></td>
				</tr>

				<?php foreach($payments as $payment_id => $payment) { ?>
				<tr>
					<td class="border-bottom border-top text-bold" colspan="9"><?php if (($is_integrated_credit_sale || sale_has_partial_credit_card_payment()) && ($payment['payment_type'] == lang('common_credit') ||  $payment['payment_type'] == lang('sales_partial_credit'))) { ?>
						<?php echo $payment['card_issuer']. ': '.$payment['truncated_card']; ?>
					<?php } else { ?>
						<?php $splitpayment=explode(':',$payment['payment_type']); echo $splitpayment[0]; ?>
					<?php }  echo $this->config->item('round_cash_on_sales') && $payment['payment_type'] == lang('common_cash') ?  ': '.  NumberFormatToCurrency(round_to_nearest_05($payment['payment_amount'])) : ': '.  NumberFormatToCurrency($payment['payment_amount']); ?></td>
				</tr>
				<?php } ?>

				<?php foreach($payments as $payment) {?>
					<?php if (strpos($payment['payment_type'], lang('common_giftcard'))!== FALSE) {?>
						<?php $giftcard_payment_row = explode(':', $payment['payment_type']); ?>
						<tr>
							<td class="border-bottom border-top text-bold" colspan="9"><?php echo lang('sales_giftcard_balance') .': ';
                                                        echo $payment['payment_type'].': ';echo NumberFormatToCurrency($this->Giftcard->get_giftcard_value(end($giftcard_payment_row))); ?></td>
						</tr>
					<?php }?>
				<?php }?>

				<?php if ($amount_change >= 0) {?>
					<tr>
                                            <td class="border-bottom border-top text-bold" colspan="9"><?php echo lang('common_change_due').': ';  echo $this->config->item('round_cash_on_sales')  && $is_sale_cash_payment ? NumberFormatToCurrency(round_to_nearest_05($amount_change)) : to_currency($amount_change); ?></td>
					</tr>
				<?php } ?>

				<?php if (isset($customer_balance_for_sale) && $customer_balance_for_sale !== FALSE && !$this->config->item('hide_store_account_balance_on_receipt')) {?>
					<tr>
                                            <td class="border-bottom border-top text-bold" colspan="9"><?php echo lang('sales_customer_account_balance').': ';  echo NumberFormatToCurrency($customer_balance_for_sale); ?></td>
					</tr>
				<?php } ?>

				<?php if ($this->config->item('enable_customer_loyalty_system') && isset($sales_until_discount) && !$this->config->item('hide_sales_to_discount_on_receipt') && $this->config->item('loyalty_option') == 'simple') {?>
					<tr>
						<td class="border-bottom border-top text-bold" colspan="9"><?php echo lang('common_sales_until_discount').': '; echo $sales_until_discount <= 0 ? lang('sales_redeem_discount_for_next_sale') : to_quantity($sales_until_discount); ?></td>
					</tr>
				<?php } ?>
				<?php if ($this->config->item('enable_customer_loyalty_system') && isset($customer_points) && !$this->config->item('hide_points_on_receipt') && $this->config->item('loyalty_option') == 'advanced') {?>
					<tr>
						<td class="border-bottom border-top text-bold" colspan="9"><?php echo lang('common_points').': '; echo to_quantity($customer_points); ?></td>
					</tr>
				<?php } ?>

				<?php if ($ref_no) { ?>
					<tr>
						<td class="border-bottom border-top text-bold" colspan="9"><?php echo lang('sales_ref_no') .': '; echo lang('sales_ref_no'); ?></td>
					</tr>
				<?php }

				if (isset($auth_code) && $auth_code) { ?>
					<tr>
						<td class="border-bottom border-top text-bold" colspan="9"><?php echo lang('sales_auth_code').': '; echo $auth_code; ?></td>
					</tr>
				<?php } ?>
                                        <tr ><td colspan="9" style="border-left: none;border-right: none; border-bottom: none;"></td></tr>
			</tbody>
		</table>
	</div>
	<div>
            <p>Số tiền viết bằng chữ: <span><?php echo getStringNumber($total);?></span></p>
	</div>
		<div id="policy"><?php echo $this->config->item('return_policy'); ?></div>
                <?php if($this->config->item('hide_barcode_on_sales_and_recv_receipt')==1){?>
                <div style="text-align: center;">
                    <?php echo "<img src='".site_url('barcode')."?barcode=$sale_id&text=$sale_id' />"; ?>
                </div>
            <?php }?>
	<div class="clb">
		<div class="fr">
			<p>Ngày ..... tháng ..... năm .......</p>
		</div>
	</div>
	<div id="pdf_signature" class="w100 clb">
		<div class="w20 fl">
			<p><lable>Khách hàng</lable></p>
			<p class="fontI">(ký, họ tên)<p>
		</div>
		<div class="w20 fl">
			<p><lable>Nhân viên bán hàng</lable></p>
			<p class="fontI">(ký, họ tên)<p>
		</div>
		
	</div>
	<div id="pdf_footer" class="w100 clb">
		<p class="fontI">(Cần kiểm tra đối chiếu khi lập, giao, nhận hàng hóa)</p>
	</div>
</div>