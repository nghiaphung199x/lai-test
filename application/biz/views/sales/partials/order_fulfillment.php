<?php 
$width = 0;
$font_size = 0;
$font_size_logo = 0;
$width_image_logo= '94px';
$commany_name_padding_top= 0;
$commany_name_width= 0;
$height= 0;
if($this->config->item('config_sales_receipt_pdf_size')=='a8'){
    $width = '245px';
    $font_size = '11px';
    $font_size_logo = '13px';
    $commany_name_padding_top='21px';
    $commany_name_width = '125px';
    $commany_name_width_logo = '120px';
    $height = '70px';
}
elseif($this->config->item('config_sales_receipt_pdf_size')=='a58'){
    $width ='140px';
    $font_size = '9px';
    $font_size_logo = '11px';
    $commany_name_padding_top='7px';
    $commany_name_width = '67px';
    $commany_name_width_logo = '67px';
    $height = '53px';
}
?>
<style type="text/css">
	#pdf_content {
		display: block;
		overflow: hidden;
		position: relative;
                height: auto; width: <?php echo $width;?>;
                font-family: Arial;
		font-size: <?php echo $font_size;?> !important;
                line-height: normal !important;
	}
	#pdf_logo  {
                height: <?php echo $height?>;
	}
        #pdf_logo img{
            width: <?php echo $commany_name_width_logo?>;
            float: left;
        }
        #pdf_logo p{
            font-size: <?php echo $font_size_logo;?>;
        }
        #pdf_company p{
            font-size: <?php echo $font_size;?>
        }
	#company_name {
		text-transform: uppercase;
		font-weight: bold;
		color: #002FC2;
                font-size: <?php echo $font_size;?>;
                float: left;
                padding-top: <?php echo $commany_name_padding_top?>;
                width: <?php echo $commany_name_width?>;
	}
	#pdf_content span {
		color: #002FC2;
	}
	#pdf_title {
		width: <?php echo $width?>;
		text-align: center;
		text-transform: uppercase;
		font-weight: bold;
		font-size: <?php echo $font_size;?>;
                margin-top: 10px;
	}
	#pdf_tbl_items {
		border-collapse: collapse;
		font-size: <?php echo $font_size;?>;
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
		padding: 3px 0px;
                font-weight: normal;
                line-height: normal !important;
                font-size: <?php echo $font_size;?> !important;;
	}
        #pdf_tbl_items th{
            font-size: <?php echo $font_size;?> !important; 
			text-align: center !important;
        }

	#pdf_signature {
		min-height: 50px;
                width: <?php echo $width?>;
	}
        #pdf_signature p{
            min-height: 50px !important;
        }
	#pdf_signature div {
		text-align: center;
	}
	#pdf_signature lable {
		font-size: 7px;
		font-weight: bold;
	}
        .text-left{
            text-align: left;
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
        .clb p{
            font-size: <?php echo $font_size;?>;
        }
	.w50 {
		width: 50%;
	}

	.w20 {
		width: 20%;
	}
	.w100 {
		width: 99%;
	}
	.pb20 {
		padding-bottom: 20px;
	}
	.pt20 {
		padding-top: 20px;
	}

	#pdf_header h3, #pdf_header p {
	}
	#pdf_footer {
		text-align: center;
	}
	#pdf_content table td, #pdf_content table th {
		text-align: right;
		height: auto !important;
	}
        .text-left{
            text-align: left !important;
        }
	p {
		margin: 2px 0;
	}
	.w150px {
		width: <?php echo $width?>;
                margin-bottom: 10px;
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
                font-size: <?php echo $font_size;?>;
        }
        .text-center{
            text-align: center !important;
        }
        .text-bold{
            font-weight: bold !important;
        }
        .text-right{
            text-align: right;
        }

</style>


 <div id="pdf_content">
		<div id="pdf_header">
		<div>
                    <div id="pdf_logo" class="clb" style="">
                        <?php if($this->config->item('company_logo')) {?>
                                <?php echo img(array('src' => $this->Appconfig->get_logo_image())); ?>
                        <?php } ?>
                        <p id="company_name"><?php echo $this->config->item('company'); ?></p>
                    </div>
			<div id="pdf_company">
				<p><?php echo lang('locations_address').': ';?> <span><?php echo nl2br($this->Location->get_info_for_key('address', isset($override_location_id) ? $override_location_id : FALSE)); ?></span></p>
				<p><?php echo lang('common_phone_number').': ';?> <span><?php echo $this->Location->get_info_for_key('phone', isset($override_location_id) ? $override_location_id : FALSE); ?></span></p>
				<?php if($this->config->item('website')) { ?>
					<p>Website: <span><?php echo $this->config->item('website'); ?></span></p>
				<?php } ?>
			</div>
		</div>
                <div class="fr w150px">
                        <p>Số: <?php echo $sale_id; ?></p>
                        <p>Ngày: <span><?php echo date(get_date_format(), strtotime($transaction_time)); ?></span></p>
                </div>
	</div>
	<div id="pdf_title" class="clb">
		<p>HÓA ĐƠN BÁN HÀNG</p>
	</div>
     <div>
         <p><?php echo lang('common_sale_person').': ';echo $user_info->first_name." ".$user_info->last_name; ?></p>
     </div>
	<div class="w100 clb">
            <table id="pdf_tbl_items" class="w100" style="border-collapse: collapse; margin-top: 10px; ">
			<tbody>
				<tr>
					<th >STT</th>
					<th ><?php echo lang('common_item_name'); ?></th>
                                        <th  ><?php echo SL; ?></th>
					<th ><?php echo lang('common_unit_sales_a8'); ?></th>
                                        <th  class="text-center"><?php echo lang('common_unit_discount_a8').' %';?></th>
					<th ><?php echo lang('common_unit_total_a8'); ?></th>
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
                                $total_money_cash = 0;
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
                                            <td class="text-center"><?php echo $stt; ?></td>
                                                <td class="text-left"><?php echo $item['name']; ?><?php if ($item_number_for_receipt){ ?> - <?php echo $item_number_for_receipt; ?><?php } ?><?php if ($item['size']){ ?> (<?php echo $item['size']; ?>)<?php } ?></td>
                                                <td><?php echo to_quantity(abs($item['quantity'])); ?></td>
                                                <td><?php echo NumberFormatToCurrency($item['price']); ?></td>
                                                <td><?php echo to_quantity($item['discount']);?></td>
                                                <td><?php echo NumberFormatToCurrency(abs($item['price']*$item['quantity']-$item['price']*$item['quantity']*$item['discount']/100)); ?></td>
					</tr>
					<?php if (!$item['description']=="" ||(isset($item['serialnumber']) && $item['serialnumber'] !="") ) {?>
					<tr>
						<td colspan="8">
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
                                    <td class="border-bottom text-bold" colspan="8"><?php echo lang('common_total_money').': '; echo NumberFormatToCurrency(abs($total_money)); ?></td>
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
						<td class="border-bottom border-top text-bold" colspan="8"><?php echo lang('common_tax').': '; echo NumberFormatToCurrency(abs($total_tax),1); ?></td>
					</tr>
				<?php }else {?>
					<?php foreach($taxes as $name=>$value) { ?>
						<tr>
							<td class="border-bottom border-top text-bold" colspan="8"><?php echo $name.': '; 
                                                        echo NumberFormatToCurrency(abs($value),1); ?></td>
						</tr>
					<?php }; ?>
				<?php } ?>
                                
                                <tr>
                                    <td class="border-bottom border-top text-bold" colspan="8"><?php echo lang('common_total').': '; echo $this->config->item('round_cash_on_sales') && $is_sale_cash_payment ? NumberFormatToCurrency(round_to_nearest_05($total)) : NumberFormatToCurrency($total); ?></td>
				</tr>
				<?php foreach($payments as $payment_id => $payment) { ?>
				<tr>
                                    <td colspan="8" class="border-top border-bottom text-bold"><?php if (($is_integrated_credit_sale || sale_has_partial_credit_card_payment()) && ($payment['payment_type'] == lang('common_credit') ||  $payment['payment_type'] == lang('sales_partial_credit'))) { ?>
						<?php echo $payment['card_issuer']. ': '.$payment['truncated_card'].': '; 
                                                $total_money_cash += $payment['truncated_card'];
                                                ?>
					<?php } else { ?>
						<?php $splitpayment=explode(':',$payment['payment_type']); echo $splitpayment[0].': '; ?>
					<?php }
                                        if( $this->config->item('round_cash_on_sales') && $payment['payment_type'] == lang('common_cash')) { 
                                            echo ': '.NumberFormatToCurrency(round_to_nearest_05($payment['payment_amount']));
                                            $total_money_cash += round_to_nearest_05($payment['payment_amount']);
                                                
                                        }
                                        else {
                                            $total_money_cash += $payment['payment_amount'];
                                            echo NumberFormatToCurrency($payment['payment_amount']); 
                                        }
                                        ?></td>
				</tr>
				<?php } ?>

				<?php foreach($payments as $payment) {?>
					<?php if (strpos($payment['payment_type'], lang('common_giftcard'))!== FALSE) {?>
						<?php $giftcard_payment_row = explode(':', $payment['payment_type']); ?>
						<tr>
							<td colspan="8" class="border-top border-bottom text-bold"><?php echo lang('sales_giftcard_balance');  
                                                        echo $payment['payment_type'].': ';
                                                        echo NumberFormatToCurrency($this->Giftcard->get_giftcard_value(end($giftcard_payment_row))); ?></td>
						</tr>
					<?php }?>
				<?php }?>

				<?php if ($amount_change >= 0) {?>
					<tr>
						<td colspan="8" class="border-top border-bottom text-bold"><?php echo lang('common_change_due').': '; 
                                                echo $this->config->item('round_cash_on_sales')  && $is_sale_cash_payment ?  NumberFormatToCurrency(round_to_nearest_05($amount_change)) : NumberFormatToCurrency($amount_change); ?></td>
					</tr>
				<?php } else { ?>
					<tr>
						<td colspan="8" class="border-top border-bottom text-bold"><?php echo lang('common_amount_due').': '; 
                                                echo $this->config->item('round_cash_on_sales')  && $is_sale_cash_payment ?  NumberFormatToCurrency(round_to_nearest_05($amount_change * -1)) : NumberFormatToCurrency($amount_change * -1); ?></td>
					</tr>
				<?php } ?>

				<?php if ($this->config->item('enable_customer_loyalty_system') && isset($sales_until_discount) && !$this->config->item('hide_sales_to_discount_on_receipt') && $this->config->item('loyalty_option') == 'simple') {?>
					<tr>
						<td colspan="8" class="border-top border-bottom text-bold"><?php echo lang('common_sales_until_discount').': '; 
                                                echo $sales_until_discount <= 0 ? lang('sales_redeem_discount_for_next_sale') : to_quantity($sales_until_discount); ?></td>
					</tr>
				<?php } ?>
				<?php if ($this->config->item('enable_customer_loyalty_system') && isset($customer_points) && !$this->config->item('hide_points_on_receipt') && $this->config->item('loyalty_option') == 'advanced') {?>
					<tr>
						<td colspan="8" class="border-top border-bottom text-bold"><?php echo lang('common_points').': '; 
                                                echo to_quantity($customer_points); ?></td>
					</tr>
				<?php } ?>

				<?php if ($ref_no) { ?>
					<tr>
						<td colspan="8" class="border-top border-bottom text-bold"><?php echo lang('sales_ref_no').': '; 
                                                echo lang('sales_ref_no'); ?></td>
					</tr>
				<?php }

				if (isset($auth_code) && $auth_code) { ?>
					<tr>
						<td colspan="8" class="border-top border-bottom text-bold"><?php echo lang('sales_auth_code').': '; 
                                                echo $auth_code; ?></td>
					</tr>
				<?php } ?>
                                        <tr ><td colspan="8" style="border-left: none;border-right: none; border-bottom: none;"></td></tr>
                                        
			</tbody>
		</table>
	</div>
	<div>
            <p>Số tiền viết bằng chữ: <span><?php echo getStringNumber(round_to_nearest_05($total_money_cash));?></span></p>
	</div>
		<?php if($this->config->item('return_policy')){?>
		<div class="text-center"id=" policy"><?php echo $this->config->item('return_policy'); ?></div>
    <?php }?>     
    <?php if($this->config->item('hide_barcode_on_sales_and_recv_receipt')){ ?>
                <div style="text-align: center;"><?php echo "<img src='".site_url('barcode')."?barcode=$sale_id&text=$sale_id' />"; ?></div>
    <?php }?>
	<div id="pdf_footer" class="w100 clb">
		<p class="fontI">(Cần kiểm tra đối chiếu khi lập, giao, nhận hàng hóa)</p>
	</div>
</div>