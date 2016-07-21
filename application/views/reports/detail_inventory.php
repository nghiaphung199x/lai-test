<?php $this->load->view("partial/header"); ?>

<div class="row">
	<div class="col-md-12">
		<div class="panel panel-piluku reports-printable">
			<div class="panel-heading">
				<?php echo lang('reports_reports'); ?> - <?php echo $title ?>
				<small class="reports-range"><?php echo $subtitle ?></small>
			</div>
			<div class="panel-body">
				<div class="table-responsive">
					<table class="table table-bordered table-striped table-reports" id="sortable_table">
						<thead>
							<tr>
								<th style="font-weight: bold;">Mã HH</th>
								<th style="font-weight: bold;">Tên HH</th>
								<th style="font-weight: bold;">Danh mục</th>
								<th style="font-weight: bold;">Kho</th>
								<th style="font-weight: bold;">Số lượng</th>
								<th style="font-weight: bold;">Đơn vị tính</th>
								<th style="font-weight: bold;">Thành tiền</th>
							</tr>
						</thead>
						
						<tbody>
						
						<?php
						
						foreach ($data as $locationId => $items) {
							
							foreach ($items as $id => $item) {
								
							?>
							<tr>
								<td><?php echo $item['product_id']; ?></td>
								<td><?php echo $item['name']; ?></td>
								<td><?php echo $item['category']; ?></td>
								<td><?php echo $item['location_name']; ?></td>
								<td><?php echo to_quantity($item['quantity']); ?></td>
								<td><?php echo empty($item['measure_name']) ? 'Chưa thiết lập' : $item['measure_name']; ?></td>
								<td><?php echo NumberFormatToCurrency($item['quantity'] * $item['cost_price']); ?></td>
							</tr>
							<?php } ?>
						<?php } ?>
						</tbody>
					</table>
				</div>
				<div class="text-center">
					<button class="btn btn-primary text-white hidden-print" id="print_button"  > <?php echo lang('common_print'); ?> </button>	
				</div>
			</div>
		</div>
	</div>
</div>
</div>


<script type="text/javascript" language="javascript">
function init_table_sorting()
{
	//Only init if there is more than one row
	if($('.tablesorter tbody tr').length >1)
	{
		$("#sortable_table").tablesorter(); 
	}
}
function print_report()
{
	window.print();
}
$(document).ready(function()
{
	
	<?php if ($this->uri->segment(2) != 'closeout')  { ?>
	init_table_sorting();
	
	var headIndex = 0;
	<?php if($this->uri->segment(2)== 'detailed_register_log' || $this->uri->segment(2) == 'detailed_inventory' || $this->uri->segment(2) =='detailed_timeclock' || $this->uri->segment(2) == 'detailed_expenses') { ?>
		headIndex = 2;	
	<?php } ?>

		<?php if($this->uri->segment(2)== 'summary_customers' || $this->uri->segment(2)== 'store_account_activity' || $this->uri->segment(2) =='specific_customer_store_account' ||
		$this->uri->segment(2)== 'inventory_low' || $this->uri->segment(2) =='inventory_summary' 
		) { ?>
			headIndex = 1;	
		<?php } ?>
		
	$("#sortable_table").stacktable({headIndex: headIndex});
	<?php } ?>

	$('#print_button').click(function(e){
		e.preventDefault();
		print_report();
	});
});
</script>
<?php $this->load->view("partial/footer"); ?>