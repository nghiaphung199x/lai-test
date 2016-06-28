<style type="text/css">
	#pdf_content {
		display: block;
		overflow: hidden;
		position: relative;
                height: auto; width: 245px;
                font-family: Arial;
		font-size: 7px !important;
                line-height: 17px !important;
	}
	#pdf_logo  {
		text-align: center;
                width: 245px;
	}
        #pdf_logo img{
            width: 110px;
        }
	#company_name {
		text-transform: uppercase;
		font-weight: bold;
		color: #002FC2;
                width: 245px;
                text-align: center;
                font-size: 7px;
	}
	#pdf_content span {
		color: #002FC2;
	}
	#pdf_title {
		width: 245px;
		text-align: center;
		text-transform: uppercase;
		font-weight: bold;
		font-size: 7px;
	}
	#pdf_tbl_items {
		border-collapse: collapse;
		font-size: 7px;
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
                font-size: 7px !important;;
	}
        #pdf_tbl_items th{
            font-size: 7px !important; 
        }

	#pdf_signature {
		min-height: 50px;
                width: 245px;
	}
        #pdf_signature p{
            min-height: 50px !important;
            margin-top: 10px;
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
		margin: 2px 0;
	}
	.w150px {
		width: 245px;
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
                font-size: 7px;
                margin-top: 10px; 
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
			<div id="pdf_logo" class="fl">
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
				
			</div>
		</div>
	</div>
	<div id="pdf_title" class="clb">
		<p>HÓA ĐƠN CÔNG NỢ (A8)</p>
	</div>

	<div id="pdf_customer">
                <p>Ngày: <span><?php echo date(get_date_format(), strtotime($transaction_time)); ?></span></p>  
                
		<p>Họ tên khách hàng: <?php if ($customer) { ?> <span><?php echo $customer; ?></span> <?php } ?></p>
                <p>Địa chỉ: <?php if ($customer_address_1) { ?> <span><?php echo $customer_address_1; ?></span> <?php } ?></p>
                <p>Tiền khách đưa:<span>  <?php echo $this->config->item('round_cash_on_sales') && $is_sale_cash_payment ? NumberFormatToCurrency(round_to_nearest_05($total)) : NumberFormatToCurrency($total); ?>VNĐ</span> </p>
                
		<p>Tổng tiền còn nợ:<span><?php
                if (isset($customer_balance_for_sale) && $customer_balance_for_sale !== FALSE && !$this->config->item('hide_store_account_balance_on_receipt')) {?>

                          <?php echo to_currency_abs($customer_balance_for_sale); 
                                              ?>
            <?php } ?>
                    </span>  </p>
	</div>	
	
	<div>
            <p>Số tiền viết bằng chữ: <span><?php echo getStringNumber(round_to_nearest_05($total));?></span></p>
	</div>
    <?php if($this->config->item('return_policy')){?>
		<div id="policy"><?php echo $this->config->item('return_policy'); ?></div>
    <?php }?>     
    <?php if($this->config->item('hide_barcode_on_sales_and_recv_receipt')){ ?>
                <div style="text-align: center;"><?php echo "<img src='".site_url('barcode')."?barcode=$sale_id&text=$sale_id' />"; ?></div>
    <?php }?>
	<div class="clb">
		<div class="fr">
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