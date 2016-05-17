<style type="text/css">
	#pdf_content {
		display: block;
		overflow: hidden;
		position: relative;
                height: auto; width: 280px;
                font-family: Arial;
		font-size: 10px !important;
	}
	#pdf_logo  {
		text-align: center;
                width: 280px;
	}
	#company_name {
		text-transform: uppercase;
		font-weight: bold;
		color: #002FC2;
                width: 280px;
                text-align: center;
                font-size: 10px;
	}
	#pdf_content span {
		color: #002FC2;
	}
	#pdf_title {
		width: 280px;
		text-align: center;
		text-transform: uppercase;
		font-weight: bold;
		font-size: 10px;
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
		min-height: 50px;
                width: 280px;
	}
        #pdf_signature p{
            min-height: 50px !important;
            margin-top: 10px;
        }
	#pdf_signature div {
		text-align: center;
	}
	#pdf_signature lable {
		font-size: 11px;
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
                margin-top: 10px;
	}
	#pdf_content table td, #pdf_content table th {
		text-align: right;
		height: auto !important;
	}
	p {
		margin: 3px 0;
	}
	.w150px {
		width: 280px;
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
                font-size: 10px;
                margin-top: 15px; 
        }
        .text-center{
            direction: rtl !important;
            text-align: center !important;
        }
        .text-bold{
            font-weight: bold !important;
        }

</style>

<div id="pdf_content">
		<div id="pdf_header">
		<div>
                    <div id="pdf_logo" class="clb text-center" style="">
				<?php if($this->config->item('company_logo')) {?>
					<?php echo img(array('src' => $this->Appconfig->get_logo_image())); ?>
				<?php } ?>
			</div>
			<div id="pdf_company">
				<p id="company_name"><?php echo $this->config->item('company'); ?></p>
				<p><span><?php echo nl2br($this->Location->get_info_for_key('address', isset($override_location_id) ? $override_location_id : FALSE)); ?></span></p>
				<p>Điện Thoại: <span><?php echo $this->Location->get_info_for_key('phone', isset($override_location_id) ? $override_location_id : FALSE); ?></span></p>
				<?php if($this->config->item('website')) { ?>
					<p>Website: <span><?php echo $this->config->item('website'); ?></span></p>
				<?php } ?>
			</div>
		</div>
		<div class="clb">
			<div class="fr w150px">
				<p>Số: <?php echo $sale_id; ?></p>
				<p>Ngày: <span><?php echo date(get_date_format(), strtotime($transaction_time)); ?></span></p>
			</div>
		</div>
	</div>
	<div id="pdf_title" class="clb">
		<p>HÓA ĐƠN GHI NỢ(A8)</p>
	</div>

	<div id="pdf_customer">
		<p>Họ tên khách hàng: <?php if ($customer) { ?> <span><?php echo $customer; ?></span> <?php } ?></p>
		<p>Ghi chú: </p>
		<p>Kho: <?php if ($this->Location->count_all() > 1) { ?><span><?php echo $this->Location->get_info_for_key('name', isset($override_location_id) ? $override_location_id : FALSE); ?></span><?php } ?></p>
		<p>Địa chỉ kho: <span><?php echo nl2br($this->Location->get_info_for_key('address', isset($override_location_id) ? $override_location_id : FALSE)); ?></span></p>
		<p><?php
                if (isset($customer_balance_for_sale_before) && $customer_balance_for_sale_before !== FALSE && !$this->config->item('hide_store_account_balance_on_receipt')) {?>

                          <?php $text = lang('sales_balance')?lang('sales_balance').': ':'Tổng nợ cũ: ';
                          echo $text; ?><?php echo to_currency_abs($customer_balance_for_sale_before); ?>
            <?php } ?>
                
	</div>	
	<div class="w100 clb">
            <table id="pdf_tbl_items" class="w100" style="border-collapse: collapse; width: 280px; font-size: 8px; margin-top: 10px; ">
			<tbody>
				<tr>
					<th style="border: 1px solid #000000; padding: 2px; line-height: normal !important;">STT </th>
                                        <th style="border: 1px solid #000000; padding: 2px;line-height: normal !important;" class="text-center">Mã MH</th>
					<th style="border: 1px solid #000000; padding: 2px;line-height: normal !important;" ><?php echo lang('common_item_name'); ?></th>
                                        <th style="border: 1px solid #000000; padding: 2px;line-height: normal !important;"  class="text-center"><?php echo lang('common_unit_report')?></th>
                                        <th style="border: 1px solid #000000; padding: 2px;line-height: normal !important;" ><?php echo SL; ?></th>
					<th style="border: 1px solid #000000; padding: 2px;line-height: normal !important;" ><?php echo lang('common_unit_sales').' ('.$this->config->item('currency_symbol').')'; ?></th>
                                        <th style="border: 1px solid #000000; padding: 2px;line-height: normal !important;"  class="text-center"><?php echo lang('common_unit_discount').' %';?></th>
                                        <th style="border: 1px solid #000000; padding: 2px;line-height: normal !important;"  class="text-center"><?php echo lang('reports_taxes') .' %'?></th>
					<th style="border: 1px solid #000000; padding: 2px;line-height: normal !important;"><?php echo lang('common_unit_total').' ('.$this->config->item('currency_symbol').')'; ?></th>
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
                                $total_money = 0;;
//                                echo 'sale';
//                                var_dump($sales_items);die;
				foreach($sales_items as $line =>  $item)
				{
                                    $total_money +=($item['item_unit_price']*$item['quantity_purchased']-$item['item_unit_price']*$item['quantity_purchased']*$item['discount_percent']/100);
					$stt ++;
					 if ($item['name'] != lang('sales_store_account_payment') && $item['name'] != lang('common_discount'))
					 {
				 		 $number_of_items_sold = $number_of_items_sold + $item['quantity_purchased'];
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
                                                <td><?php echo H($item['product_id']);?></td>
						<td><?php echo $item['name']; ?><?php if ($item_number_for_receipt){ ?> - <?php echo $item_number_for_receipt; ?><?php } ?><?php if ($item['size']){ ?> (<?php echo $item['size']; ?>)<?php } ?></td>
                                                <td></td>
                                                <td><?php echo to_quantity(abs($item['quantity_purchased'])); ?></td>
                                                <td><?php echo to_currency_no_money($item['item_unit_price']); ?></td>
                                                <td><?php echo to_quantity($item['discount_percent']);?></td>
                                                <td><?php echo to_quantity($item['tax_included']);?></td>
                                                <td><?php echo to_currency_no_money($item['item_unit_price']*$item['quantity_purchased']-$item['item_unit_price']*$item['quantity_purchased']*$item['discount_percent']/100); ?></td>
					</tr>
					<?php if (!$item['description']=="" ||(isset($item['serialnumber']) && $item['serialnumber'] !="") ) {?>
					<tr>
						<td colspan="5">
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
                                    <td class="border-bottom text-bold" colspan="9"><?php echo lang('common_total_money').': '; echo to_currency_no_money(abs($total_money)); ?></td>
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
						<td class="border-bottom border-top text-bold" colspan="9"><?php echo lang('common_tax').': '; echo to_currency_no_money(abs($total_tax),1); ?></td>
					</tr>
				<?php }else {?>
					<?php foreach($taxes as $name=>$value) { ?>
						<tr>
							<td class="border-bottom border-top text-bold" colspan="9"><?php echo $name.': '; 
                                                        echo to_currency_no_money(abs($value),1); ?></td>
						</tr>
					<?php }; ?>
				<?php } ?>
                                <tr>
                                    <td class="border-bottom border-top text-bold" colspan="9"><?php echo lang('common_total').': '; echo $this->config->item('round_cash_on_sales') && $is_sale_cash_payment ? to_currency_no_money(round_to_nearest_05($total)) : to_currency_no_money($total); ?></td>
				</tr>
				<?php foreach($payments as $payment_id => $payment) { ?>
				<tr>
                                    <td colspan="9" class="border-top border-bottom text-bold"><?php if (($is_integrated_credit_sale || sale_has_partial_credit_card_payment()) && ($payment['payment_type'] == lang('common_credit') ||  $payment['payment_type'] == lang('sales_partial_credit'))) { ?>
						<?php echo $payment['card_issuer']. ': '.$payment['truncated_card'].': '; ?>
					<?php } else { ?>
						<?php $splitpayment=explode(':',$payment['payment_type']); echo $splitpayment[0].': '; ?>
					<?php }  echo $this->config->item('round_cash_on_sales') && $payment['payment_type'] == lang('common_cash') ? ': '.to_currency_no_money(round_to_nearest_05($payment['payment_amount'])) : to_currency_no_money($payment['payment_amount']); ?></td>
				</tr>
				<?php } ?>
                                
				<?php foreach($payments as $payment) {?>
					<?php if (strpos($payment['payment_type'], lang('common_giftcard'))!== FALSE) {?>
						<?php $giftcard_payment_row = explode(':', $payment['payment_type']); ?>
						<tr>
							<td colspan="9" class="border-top border-bottom text-bold"><?php echo lang('sales_giftcard_balance');  
                                                        echo $payment['payment_type'].': ';
                                                        echo to_currency_no_money($this->Giftcard->get_giftcard_value(end($giftcard_payment_row))); ?></td>
						</tr>
					<?php }?>
				<?php }?>

				<?php if ($amount_change >= 0) {?>
					<tr>
						<td colspan="9" class="border-top border-bottom text-bold"><?php echo lang('common_change_due').': '; 
                                                echo $this->config->item('round_cash_on_sales')  && $is_sale_cash_payment ?  to_currency_no_money(round_to_nearest_05($amount_change)) : to_currency_no_money($amount_change); ?></td>
					</tr>
				<?php } else { ?>
					<tr>
						<td colspan="9" class="border-top border-bottom text-bold"><?php echo lang('common_amount_due').': '; 
                                                echo $this->config->item('round_cash_on_sales')  && $is_sale_cash_payment ?  to_currency_no_money(round_to_nearest_05($amount_change * -1)) : to_currency_no_money($amount_change * -1); ?></td>
					</tr>
				<?php } ?>
                                        <?php if (isset($customer_balance_for_sale) && $customer_balance_for_sale !== FALSE && !$this->config->item('hide_store_account_balance_on_receipt')) {?>
					<tr>
						<td colspan="9" class="border-bottom border-top text-bold">Tổng nợ cuối: 
                                                    <?php echo to_currency_no_money($customer_balance_for_sale); ?></td>
					</tr>
				<?php } ?>
				<?php if ($this->config->item('enable_customer_loyalty_system') && isset($sales_until_discount) && !$this->config->item('hide_sales_to_discount_on_receipt') && $this->config->item('loyalty_option') == 'simple') {?>
					<tr>
						<td colspan="9" class="border-top border-bottom text-bold"><?php echo lang('common_sales_until_discount').': '; 
                                                echo $sales_until_discount <= 0 ? lang('sales_redeem_discount_for_next_sale') : to_quantity($sales_until_discount); ?></td>
					</tr>
				<?php } ?>
                                        
				<?php if ($this->config->item('enable_customer_loyalty_system') && isset($customer_points) && !$this->config->item('hide_points_on_receipt') && $this->config->item('loyalty_option') == 'advanced') {?>
					<tr>
						<td colspan="9" class="border-top border-bottom text-bold"><?php echo lang('common_points').': '; 
                                                echo to_quantity($customer_points); ?></td>
					</tr>
				<?php } ?>

				<?php if ($ref_no) { ?>
					<tr>
						<td colspan="9" class="border-top border-bottom text-bold"><?php echo lang('sales_ref_no').': '; 
                                                echo lang('sales_ref_no'); ?></td>
					</tr>
				<?php }

				if (isset($auth_code) && $auth_code) { ?>
					<tr>
						<td colspan="9" class="border-top border-bottom text-bold"><?php echo lang('sales_auth_code').': '; 
                                                echo $auth_code; ?></td>
					</tr>
				<?php } ?>
                                        <tr ><td colspan="9" style="border-left: none;border-right: none; border-bottom: none;"></td></tr>
                                        
			</tbody>
		</table>
	</div>
	<div>
            <p>Số tiền viết bằng chữ: <span><?php echo get_string_number(to_currency_no_money(round_to_nearest_05($amount_change)));?></span></p>
	</div >
		<?php if($this->config->item('return_policy')){?>
		<div id="policy"><?php echo $this->config->item('return_policy'); ?></div>
    <?php }?>     
    <?php if($this->config->item('hide_barcode_on_sales_and_recv_receipt')){ ?>
                <div style="text-align: center;"><?php echo "<img src='".site_url('barcode')."?barcode=$sale_id&text=$sale_id' />"; ?></div>
    <?php }?>
	<div class="clb">
            <div class="fr" style="margin-top: 10px;">
			<p>Ngày ..... tháng ..... năm .......</p>
		</div>
	</div>
	<div id="pdf_signature" class="w100 clb">
		<div class="w20 fl">
			<p><lable>Người lập phiếu</lable></p>
			<p class="fontI">(ký, họ tên)</p>
		</div>
		<div class="w20 fl">
			<p><lable>Người nhận hàng</lable></p>
			<p class="fontI">(ký, họ tên)</p>
		</div>
		<div class="w20 fl">
			<p><lable>Thủ kho</lable></p>
			<p class="fontI">(ký, họ tên)</p>
		</div>
		<div class="w20 fl">
			<p><lable>Kế toán trưởng</lable></p>
			<p class="fontI">(ký, họ tên)</p>
		</div>
		<div class="w20 fl">
			<p><lable>Giám đốc</lable></p>
			<p class="fontI">(ký, họ tên)</p>
		</div>
	</div>
	<div id="pdf_footer" class="w100 clb">
		<p class="fontI">(Cần kiểm tra đối chiếu khi lập, giao, nhận hàng hóa)</p>
	</div>
</div>
