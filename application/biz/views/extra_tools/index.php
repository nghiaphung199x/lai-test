<?php $this->load->view("partial/header");
 ?>
	<div class="container-fluid">
		<div class="row manage-table">
			<div class="panel panel-piluku">
				<div class="panel-heading">
					<h3 class="panel-title hidden-print">
						 Trích xuất dữ liệu
					</h3>
				</div>
				<div style="padding: 15px;" class="panel-body nopadding table_holder table-responsive">
					<a href="<?php echo site_url("extra_tools/export/account_payment");?>"  style="padding: 10px; margin: 5px 0;" class="btn btn-default">Dữ liệu công nợ (tất cả)</a>
					<a href="<?php echo site_url("extra_tools/export/items");?>" style="padding: 10px; margin: 5px 0;" class="btn btn-default">Dữ liệu sản phẩm (tất cả)</a>
					<a href="<?php echo site_url("extra_tools/export/history_transfers");?>" style="padding: 10px; margin: 5px 0;" class="btn btn-default">Dữ liệu lịch sử chuyển kho (trong tháng <?php echo (int) date('m');?>)</a>
					<a href="<?php echo site_url("extra_tools/export/history_audits");?>" style="padding: 10px; margin: 5px 0;" class="btn btn-default">Dữ liệu lịch sử kiểm kho (trong tháng <?php echo (int) date('m');?>)</a>
					<a href="<?php echo site_url("extra_tools/export/receivings");?>" style="padding: 10px; margin: 5px 0;" class="btn btn-default">Dữ liệu hóa đơn nhập hàng (trong tháng <?php echo (int) date('m');?>)</a>
					<a href="<?php echo site_url("extra_tools/export/sales");?>" style="padding: 10px; margin: 5px 0;" class="btn btn-default">Dữ liệu hóa đơn bán hàng (trong tháng <?php echo (int) date('m');?>)</a>
				</div>
			</div>
		</div>
	</div>
<?php $this->load->view("partial/footer"); ?>