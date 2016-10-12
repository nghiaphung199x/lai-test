<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	<style type="text/css">
		body {
			font-family: "Times New Roman", DejaVu Sans;
		}
		#pdf_content {
			width: 700px;
			display: block;
			overflow: hidden;
			position: relative;
			margin-left: 10px;
			font-size: 12px;
		}
		#pdf_header {
			height: 100px;
		}
		#pdf_logo {
			position: relative;
			top: 0px;
			left: 0px;
		}
		#pdf_logo img {
			max-height: 70px;
		}
		#pdf_company {
			position: relative;
			top: -70px;
		}
		#company_name {
			text-transform: uppercase;
			font-weight: bold;
			color: #002FC2
		}
		#pdf_short_info {
			position: relative;
			left: 550px;
			top: -70px;
		}
		#pdf_sinnature_time {
			position: absolute;
			left: 500px;
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
			padding-top: 30px;
		}
		#pdf_signature div {
			text-align: center;
		}
		#pdf_signature lable {
			font-size: 16px;
			font-weight: bold;
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
			margin-top: 100px;
		}
		table td, table th {
			text-align: right;
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
	</style>
</head>
<body>
	<div id="pdf_content">
		<div id="pdf_header">
			<div id="pdf_logo">
				<?php if($this->config->item('company_logo')) {?>
					<?php echo img(array('src' => $this->Appconfig->get_logo_image())); ?>
				<?php } ?>
			</div>
			<div id="pdf_company">
				<p id="company_name"><?php echo $this->config->item('company'); ?></p>
				<p><span><?php echo nl2br($this->Location->get_info_for_key('address', isset($override_location_id) ? $override_location_id : FALSE)); ?></span></p>
				<p>Điện Thoại: <span><?php echo $this->Location->get_info_for_key('phone', isset($override_location_id) ? $override_location_id : FALSE); ?></span>, Fax: <span><?php echo $this->Location->get_info_for_key('fax', isset($override_location_id) ? $override_location_id : FALSE); ?></span></p>
				<?php if($this->config->item('website')) { ?>
					<p>Website: <span><?php echo $this->config->item('website'); ?></span></p>
				<?php } ?>
			</div>
			<div class="w100">
				<div id="pdf_short_info" class="w150px">
					<p>Số: <?php echo $receiving_id; ?></p>
					<p>Ngày: <span><?php echo $transaction_date; ?></span></p>
				</div>
			</div>
		</div>
		<div id="pdf_title" class="clb">
			<p>Phiếu nhập kho</p>
		</div>

		<div id="pdf_customer">
			<p>Đơn vị cung cấp: <?php if ($supplier) { ?> <span><?php echo $supplier; ?></span> <?php } ?></p>
			<p>Ghi chú: </p>
		</div>
		<div class="w100 clb">
			<table id="pdf_tbl_items" class="w100">
				<tbody>
					<tr>
						<th>STT</th>
						<th><?php echo lang('common_item_name'); ?></th>
						<th><?php echo lang('common_price'); ?></th>
						<th><?php echo lang('common_quantity'); ?></th>
						<th><?php echo lang('common_total'); ?></th>
					</tr>
					<?php $stt = 0; ?>
					<?php foreach(array_reverse($cart, true) as $line=>$item) { ?>
						<?php $stt ++; ?>
						<tr>
							<td><?php echo $stt; ?></td>
							<td><?php echo $item['name']; ?><?php if ($item['size']){ ?> (<?php echo $item['size']; ?>)<?php } ?></td>
							<td><?php echo to_currency($item['price']); ?></td>
							<td><?php echo to_quantity($item['quantity']); ?></td>
							<td><?php echo to_currency($item['price']*$item['quantity']-$item['price']*$item['quantity']*$item['discount']/100); ?></td>
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

					<?php if ($this->config->item('charge_tax_on_recv')) {?>
						<?php if ($this->config->item('group_all_taxes_on_receipt')) { ?>
							<?php  $total_tax = 0;
								foreach($taxes as $name=>$value) 
								{
									$total_tax+=$value;
							 	} ?>
							 	<tr>
									<td colspan="4"><?php echo lang('common_tax'); ?></td>
									<td><?php echo to_currency($total_tax); ?></td>
								</tr>
						<?php }else {?>
								<?php foreach($taxes as $name=>$value) { ?>
									<tr>
										<td colspan="4"><?php echo $name; ?></td>
										<td><?php echo to_currency($value); ?></td>
									</tr>
								<?php } ?>
						<?php } ?>
					<?php } ?>

					<tr>
						<td colspan="4"><?php echo lang('common_total'); ?></td>
						<td><?php echo to_currency($total); ?></td>
					</tr>
					<tr>
						<td colspan="4"><?php echo lang('common_payment'); ?></td>
						<td><?php echo $payment_type; ?></td>
					</tr>

					<?php if(isset($amount_change)) { ?>
						<tr>
							<td colspan="4"><?php echo lang('common_amount_tendered'); ?></td>
							<td><?php echo to_currency($amount_tendered); ?></td>
						</tr>
						<tr>
							<td colspan="4"><?php echo lang('common_change_due'); ?></td>
							<td><?php echo $amount_change; ?></td>
						</tr>
					<?php } ?>
				</tbody>
			</table>
		</div>
		<div>
			<p>Số tiền viết bằng chữ: <span>.................................................................................</span></p>
		</div>

		<div class="clb">
			<div id="pdf_sinnature_time">
				<p>Ngày ..... tháng ..... năm .......</p>
			</div>
		</div>
		<div id="pdf_signature" class="w100">
			<table class="w100">
				<tr>
					<th>Người lập phiếu</th>
					<th>Người nhận hàng</th>
					<th>Thủ kho</th>
					<th>Kế toán trưởng</th>
				</tr>
				<tr>
					<td><p class="fontI">(ký, họ tên)</p></td>
					<td><p class="fontI">(ký, họ tên)</p></td>
					<td><p class="fontI">(ký, họ tên)</p></td>
					<td><p class="fontI">(ký, họ tên)</p></td>
				</tr>
			</table>
		</div>
		<div id="pdf_footer" class="w100">
			<p class="fontI">(Cần kiểm tra đối chiếu khi lập, giao, nhận hàng hóa)</p>
		</div>
	</div>
</body>
</html>