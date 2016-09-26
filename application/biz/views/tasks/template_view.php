<?php $this->load->view("partial/header"); ?>
<div class="main-content">
<div class="manage_buttons">
<div class="manage-row-options hidden">
	<div class="email_buttons text-center">
		 <a class="btn btn-primary btn-lg" title="Gửi SMS" id="sendSMS" href="http://localhost/4biz2016/customers#" data-toggle="modal" data-target="#myModal">
			 <span class="">Gửi SMS</span>
		 </a>
	     <a href="http://localhost/4biz2016/customers/save_list_send_mail" id="sendToMailTemp" class="btn btn-primary btn-lg" title="Thêm vào DS mail tạm"><span class="">Thêm vào DS mail tạm</span></a>			<a class="btn btn-primary btn-lg check_list_send_sms" id="check_list_send_sms" href="customers/save_list_send_sms">
			<span class="">Danh sách sms tạm</span>
		 </a>
				
		 <a class="btn btn-primary btn-lg" title="E-Mail" id="sendMail" href="http://localhost/4biz2016/customers#" data-toggle="modal" data-target="#myModal">
			<span class="">E-Mail</span>
		 </a>
				
		<a href="http://localhost/4biz2016/customers/delete" id="delete" class="btn btn-red btn-lg delete_inactive disabled" title="Xóa"><span class="">Xóa</span></a>		
		<a href="#" class="btn btn-lg btn-clear-selection btn-warning">Xóa lựa chọn</a>
	</div>
</div>
<div class="cl">
	<div class="pull-left">
		<form action="http://localhost/4biz2016/customers/search" id="search_form" autocomplete="off" class="form-inline" method="post" accept-charset="utf-8">
			<div class="search no-left-border">
				<span role="status" aria-live="polite" class="ui-helper-hidden-accessible"></span><input type="text" class="form-control ui-autocomplete-input" name="search" id="search" value="" placeholder="Tìm kiếm Khách hàng" autocomplete="off">
			</div>

			<div class="clear-block hidden">
				<a class="clear" href="http://localhost/4biz2016/customers/clear_state">
					<i class="ion ion-close-circled"></i>
				</a>	
			</div>
		</form>
	</div>
	<div class="pull-right">
		<div class="buttons-list">
			<div class="pull-right-btn">
				<a href="http://localhost/4biz2016/customers/view/-1" id="new-person-btn" class="btn btn-primary btn-lg" title="Thêm mới khách hàng"><span class="">Thêm mới khách hàng</span></a>					
			</div>
		</div>				
	</div>
    <div class="cl"></div>
</div>
</div>

	<div class="container-fluid">
		<div class="row manage-table">
			<div class="panel panel-piluku">
				<div class="panel-heading">
				<h3 class="panel-title">
					Thông tin Khách hàng					
					<span title="0 total customers" class="badge bg-primary tip-left">0</span>
					<span class="panel-options custom">
					</span>
				</h3>
			</div>
			<div class="panel-body nopadding table_holder table-responsive">
				<table class="tablesorter table  table-hover" id="sortable_table">
					<thead>
						<tr>
							<th class="leftmost">
								<input type="checkbox" id="select_all"><label for="select_all"><span></span></label></th><th>ID</th><th>Tên</th><th class="header headerSortUp">E-Mail</th><th>Số điện thoại</th><th>Điểm / Số tiền chi ra cho 1 điểm trong lần mua hàng tới</th><th>Công nợ</th><th>&nbsp;</th><th class="rightmost">&nbsp;</th></tr></thead><tbody><tr style="cursor: pointer;"><td class=""><input type="checkbox" name="person_368" id="person_368" value="368"><label for="person_368"><span></span></label></td><td class="">368</td><td class=""><a href="http://localhost/4biz2016/reports/specific_customer/2016-03-26/2016-09-26%2023:59:59/368/all/0" class="underline">Toàn Khánh Nguyễn</a></td><td class=""><a href="mailto:" class="underline"></a></td><td class=""></td><td class="">0 / 1,000 VNĐ</td><td class="">0 VNĐ</td><td class=""><a href="http://localhost/4biz2016/customers/pay_now/368" title="Thanh toán" class="btn btn-primary ">Thanh toán</a></td><td class=""><a href="http://localhost/4biz2016/customers/view/368/2" class="  update-person" title="Cập nhật">Sửa</a></td><td class=""><a href="http://localhost/4biz2016/assets/assets/images/avatar-default.jpg" class="rollover"><img src="http://localhost/4biz2016/assets/assets/images/avatar-default.jpg" alt="Toàn Khánh Nguyễn" class="img-polaroid avatar" width="45"></a></td></tr><tr style="cursor: pointer;"><td><input type="checkbox" name="person_369" id="person_369" value="369"><label for="person_369"><span></span></label></td><td>369</td><td><a href="http://localhost/4biz2016/reports/specific_customer/2016-03-26/2016-09-26%2023:59:59/369/all/0" class="underline">Mến Đoàn</a></td><td><a href="mailto:" class="underline"></a></td><td></td><td>0 / 1,000 VNĐ</td><td>0 VNĐ</td><td><a href="http://localhost/4biz2016/customers/pay_now/369" title="Thanh toán" class="btn btn-primary ">Thanh toán</a></td><td><a href="http://localhost/4biz2016/customers/view/369/2" class="  update-person" title="Cập nhật">Sửa</a></td><td><a href="http://localhost/4biz2016/assets/assets/images/avatar-default.jpg" class="rollover"><img src="http://localhost/4biz2016/assets/assets/images/avatar-default.jpg" alt="Mến Đoàn" class="img-polaroid avatar" width="45"></a></td></tr><tr style="cursor: pointer;"><td><input type="checkbox" name="person_372" id="person_372" value="372"><label for="person_372"><span></span></label></td><td>372</td><td><a href="http://localhost/4biz2016/reports/specific_customer/2016-03-26/2016-09-26%2023:59:59/372/all/0" class="underline">GT Food Group </a></td><td><a href="mailto:" class="underline"></a></td><td></td><td>0 / 1,000 VNĐ</td><td>0 VNĐ</td><td><a href="http://localhost/4biz2016/customers/pay_now/372" title="Thanh toán" class="btn btn-primary ">Thanh toán</a></td><td><a href="http://localhost/4biz2016/customers/view/372/2" class="  update-person" title="Cập nhật">Sửa</a></td><td><a href="http://localhost/4biz2016/assets/assets/images/avatar-default.jpg" class="rollover"><img src="http://localhost/4biz2016/assets/assets/images/avatar-default.jpg" alt="GT Food Group " class="img-polaroid avatar" width="45"></a></td></tr><tr style="cursor: pointer;"><td><input type="checkbox" name="person_373" id="person_373" value="373"><label for="person_373"><span></span></label></td><td>373</td><td><a href="http://localhost/4biz2016/reports/specific_customer/2016-03-26/2016-09-26%2023:59:59/373/all/0" class="underline">Epol </a></td><td><a href="mailto:" class="underline"></a></td><td></td><td>0 / 1,000 VNĐ</td><td>0 VNĐ</td><td><a href="http://localhost/4biz2016/customers/pay_now/373" title="Thanh toán" class="btn btn-primary ">Thanh toán</a></td><td><a href="http://localhost/4biz2016/customers/view/373/2" class="  update-person" title="Cập nhật">Sửa</a></td><td><a href="http://localhost/4biz2016/assets/assets/images/avatar-default.jpg" class="rollover"><img src="http://localhost/4biz2016/assets/assets/images/avatar-default.jpg" alt="Epol " class="img-polaroid avatar" width="45"></a></td></tr><tr style="cursor: pointer;"><td><input type="checkbox" name="person_374" id="person_374" value="374"><label for="person_374"><span></span></label></td><td>374</td><td><a href="http://localhost/4biz2016/reports/specific_customer/2016-03-26/2016-09-26%2023:59:59/374/all/0" class="underline">Amrit Group </a></td><td><a href="mailto:" class="underline"></a></td><td></td><td>0 / 1,000 VNĐ</td><td>0 VNĐ</td><td><a href="http://localhost/4biz2016/customers/pay_now/374" title="Thanh toán" class="btn btn-primary ">Thanh toán</a></td><td><a href="http://localhost/4biz2016/customers/view/374/2" class="  update-person" title="Cập nhật">Sửa</a></td><td><a href="http://localhost/4biz2016/assets/assets/images/avatar-default.jpg" class="rollover"><img src="http://localhost/4biz2016/assets/assets/images/avatar-default.jpg" alt="Amrit Group " class="img-polaroid avatar" width="45"></a></td></tr></tbody>
				
				
				</table>			
			</div>	
			</div>	
		</div>
	</div>
</div>
<?php $this->load->view("partial/footer"); ?>