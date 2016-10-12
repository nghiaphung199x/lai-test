<div class="panel panel-piluku">
			<style type="text/css">
	#pdf_content {
		width: 70%;
		display: block;
		overflow: hidden;
		position: relative;
		padding: 20px;
		font-size: 12px;
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
		/*font-weight: bold;*/
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
		.text-bold{
			font-weight: bold;
		}
		th{
			text-align: center !important;
		}
		
</style>
<div id="pdf_content">
	<div id="pdf_header">
		<div>
			<div id="pdf_logo" class="fl">
							</div>
			<div id="pdf_company">
				<p id="company_name">4Biz by LifeTek, LLC</p>
				<p><span>123 Nowhere street</span></p>
				<p>Điện Thoại: <span>555-555-5555</span></p>
							</div>
		</div>
		<div class="clb">
			<div class="fr w150px">
				<p>Số: POS 9</p>
				<p>Ngày: <span>05/06/2016</span></p>
			</div>
		</div>
	</div>
	<div id="pdf_title" class="clb">
			<p style="font-weight: normal;">PHIẾU XUẤT KHO CHI NHÁNH  <span style="color: black;font-weight: bold "> SỐ 114</span></p>
			<p style="font-weight: bold"> Ngày: 22-03-2016 00:00:00</p>
	</div>

	
	<div class="w100 clb">
		<table id="pdf_tbl_items" class="w100">
			<tbody>
				<tr>
					<th class="text-center">STT</th>
					<th class="text-center">Mã mặt hàng</th>
					<th class="text-center">Tên mặt hàng</th>
					<th class="text-center">Kho chuyển</th>
					<th class="text-center">Kho nhận</th>
					<th class="text-center">Số lượng</th>
					<th class="text-center">Giá bán</th>
					<th class="text-center">Thành tiền (VNĐ)</th>
					<th class="text-center">Thời gian</th>
				</tr>
				<tr>
					<td>1</td>
					<td>2DH001</td>
					<td>Điều hòa</td>
					<td>Kho tổng</td>
					<td>PHỐ NỐI</td>
					
					<td>1</td>
					<td>80.000</td>
					<td>80.000</td>
					<td style="text-align: center">22-03-2016 <br>13:48:34</td>
				</tr>
			</tbody>
		</table>
	</div>
	<div>
		<p style="text-align: center">Tổng tiền: <span> 80.000</span></p>
	</div>
	<div class="clb">
		<div class="fr">
			<p>Ngày ..... tháng ..... năm .......</p>
		</div>
	</div>
	<div id="pdf_signature" class="w100 clb">
		<div class="w20 fl">
			<p><lable>Thủ kho</lable></p>
			<p class="fontI">(ký, họ tên)</p><p>
		</p></div>
		<div class="w20 fl">
					<p><lable>Người vận chuyển</lable></p>
			<p class="fontI">(ký, họ tên)</p><p>
		</p></div>
		<div class="w20 fl">
			<p><lable  style="font-weight: unset;">Người nhận</lable></p>
			<p class="fontI">(ký, họ tên)</p><p>
		</p></div>
		<div class="w20 fl">
			<p><lable>Kế toán</lable></p>
			<p class="fontI">(ký, họ tên)</p><p>
		</p></div>
		<div class="w20 fl">
			<p><lable>Giám đốc</lable></p>
			<p class="fontI">(ký, họ tên)</p><p>
		</p></div>
	</div>
	<div id="pdf_footer" class="w100 clb">
		<p class="fontI">(Cần kiểm tra đối chiếu khi lập, giao, nhận hàng hóa)</p>
	</div>
</div>	</div>