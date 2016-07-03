<style type="text/css">
#pdf_content {
	width: 700px;
	display: block;
	overflow: hidden;
	position: relative;
	padding: 20px;
	font-size: 12px;
}

#table-responsive {
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
/* 	text-align: center; */
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

.w220px {
	width: 220px;
}

.fontI {
	font-style: italic;
}

.border-bottom {
	border-bottom: 1px dotted rgb(0, 0, 0) !important;
}

.border-left {
	border-left: none !important;
}

.border-right {
	border-right: none !important;
}

.border-top {
	border-top: none !important;
}

#policy {
	font-weight: bold;
	text-align: center;
	font-size: 1.3em;
	margin-top: 10px;
}

.text-center {
	direction: rtl !important;
	text-align: center !important;
}

.text-bold {
	font-weight: bold !important;
}

table th, table td {
	line-height: normal !important;
}
/* Medium Devices, Desktops */
@media only screen and (max-width : 992px) {
}

/* Small Devices, Tablets */
@media only screen and (max-width : 768px) {
	.table-responsive {
		max-width: 700px;
	}
}

@media only screen and (max-width : 767px) and (max-width: 481px) {
	.table-responsive {
		max-width: 700px;
	}
}

/* Extra Small Devices, Phones */
@media only screen and (max-width : 480px) {
	.table-responsive {
		max-width: 300px;
	}
}

/* Custom, iPhone Retina */
@media only screen and (max-width : 320px) {
	.table-responsive {
		max-width: 284px;
	}
}
/*@media screen and (min-device-width: 481px) and (max-device-width: 768px)*/
</style>
<div id="pdf_content">
	<div id="pdf_header">
	<?php
	$receiver = $this->Employee->get_info ( $expense_info->employee_id );
	?>
		<div id="pdf_company" class="fl">
			<p id="company_name"><?php echo $this->config->item('company'); ?></p>
			<p>
				<span><?php echo nl2br($this->Location->get_info_for_key('address', isset($override_location_id) ? $override_location_id : FALSE)); ?></span>
			</p>
			<p>
				Điện Thoại: <span><?php echo $this->Location->get_info_for_key('phone', isset($override_location_id) ? $override_location_id : FALSE); ?></span>
			</p>
			<?php if($this->config->item('website')) { ?>
				<p>
				Website: <span><?php echo $this->config->item('website'); ?></span>
			</p>
			<?php } ?>
		</div>

		<div class="fr w220px">
			<p>Mẫu Số 02 TT</p>
			<p>Ban hành theo QĐ số 48/2006/QĐ - BTC</p>
			<p>
				Ngày: <span><?php echo date(get_date_format(), strtotime($expense_info->expense_date)); ?></span>
			</p>
		</div>
	</div>
	<div id="pdf_title" class="clb">
		<p>PHIẾU CHI</p>
	</div>
	<div class="fr w220px">
		<p>Số: <?php echo $expense_info->id; ?></p>
<!-- 		<p>Nợ:</p> -->
<!-- 		<p>Có:</p> -->
	</div>
	<div id="pdf_customer" class="clb">
		<p>Người nộp tiền: <?php echo $receiver->first_name . ' ' . $receiver->last_name; ?></p>
		<p>Địa chỉ: <?php echo nl2br($this->Location->get_info_for_key('address', isset($override_location_id) ? $override_location_id : FALSE)); ?></p>
		<p>Lý do nộp: <?php echo $expense_info->expense_description; ?></p>
		<p>Số tiền: <?php echo NumberFormatToCurrency($expense_info->expense_amount); ?> <?php echo $this->config->item('currency_symbol'); ?></p>
		<p>
			Số tiền viết bằng chữ: <span><?php echo getStringNumber((int) $expense_info->expense_amount);?></span>
		</p>
	</div>

	<div class="clb">
		<div class="fr">
			<p>Ngày ..... tháng ..... năm .......</p>
		</div>
	</div>
	<div id="pdf_signature" class="w100 clb">
		<div class="w20 fl">
			<p>
				<lable>Người lập phiếu</lable>
			</p>
			<p class="fontI">(ký, họ tên)</p>
			<p></p>
		</div>
		<div class="w20 fl">
			<p>
				<lable>Người thu tiền</lable>
			</p>
			<p class="fontI">(ký, họ tên)</p>
			<p></p>
		</div>
		<div class="w20 fl">
			<p>
				<lable>Thủ quỹ</lable>
			</p>
			<p class="fontI">(ký, họ tên)</p>
			<p></p>
		</div>
		<div class="w20 fl">
			<p>
				<lable>Kế toán trưởng</lable>
			</p>
			<p class="fontI">(ký, họ tên)</p>
			<p></p>
		</div>
		<div class="w20 fl">
			<p>
				<lable>Giám đốc</lable>
			</p>
			<p class="fontI">(ký, họ tên)</p>
			<p></p>
		</div>
	</div>
	<div id="pdf_footer" class="w100 clb">
		<p class="fontI">Đã nhận đủ số tiền (viết bằng chữ): ......................................................................</p>
		<p class="fontI"> + Tý giá ngoại tệ (Vàng, bạc, đá quý): ......................................................................</p>
		<p class="fontI"> + Số tiền quy đổi: ......................................................................</p>
		<p class="fontI"> + Liên gửi ra ngoài phải đóng dấu: ......................................................................</p>
	</div>
</div>