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
								<th style="font-weight: bold;" colspan="2" rowspan="2">Mặt hàng</th>
								<th style="font-weight: bold;" colspan="2" rowspan="2">Tồn đầu kỳ</th>
								<th style="font-weight: bold;" colspan="4">Phát sinh trong kỳ</th>
								<th style="font-weight: bold;" colspan="2" rowspan="2">Tồn cuối kỳ</th>
							</tr>
							<tr>
								<th style="font-weight: bold;" colspan="2">Nhập</th>
								<th style="font-weight: bold;" colspan="2">Xuất</th>
							</tr>
							<tr></tr>
							<tr>
								<th style="font-weight: bold;">Mã HH</th>
								<th style="font-weight: bold;">Tên HH</th>
								<th style="font-weight: bold;">Số lượng</th>
								<th style="font-weight: bold;">Thành tiền</th>
								<th style="font-weight: bold;">Số lượng</th>
								<th style="font-weight: bold;">Thành tiền</th>
								<th style="font-weight: bold;">Số lượng</th>
								<th style="font-weight: bold;">Thành tiền</th>
								<th style="font-weight: bold;">Số lượng</th>
								<th style="font-weight: bold;">Thành tiền</th>
							</tr>
						</thead>
						
						<tbody>
						
						<?php
						
						foreach ($data as $locationId => $items) {
							if (empty($items)) continue;
							
							$total_qty_before = 0;
							$total_price_before = 0;
							
							$total_qty_in = 0;
							$total_cost_in = 0;
							
							$total_qty_out = 0;
							$total_price_out = 0;
							
							$total_qty_after = 0;
							$total_price_after = 0;
								
							
							foreach ($items as $id => $item) {
								$total_qty_before += $item['trans_total_qty_before'];
								$total_price_before += abs($item['trans_total_price_before_origin']);
								$total_qty_in += $item['total_qty_in'];
								$total_cost_in += abs($item['total_cost_in_origin']);
								$total_qty_out += $item['total_qty_out'];
								$total_price_out += abs($item['total_price_out_origin']);
								$total_qty_after += $item['trans_total_qty_after'];
								$total_price_after += abs($item['trans_total_price_after_origin']);
							?>
							<tr>
								<td><?php echo $item['product_id']; ?></td>
								<td><?php echo $item['name']; ?></td>
								<td><?php echo (int) $item['trans_total_qty_before']; ?></td>
								<td><?php echo $item['trans_total_price_before']; ?></td>
								<td><?php echo (int) $item['total_qty_in']; ?></td>
								<td><?php echo $item['total_cost_in']; ?></td>
								<td><?php echo (int) $item['total_qty_out']; ?></td>
								<td><?php echo $item['total_price_out']; ?></td>
								<td><?php echo (int) $item['trans_total_qty_after']; ?></td>
								<td><?php echo $item['trans_total_price_after']; ?></td>
							</tr>
							<?php } 
								$location = $this->Location->get_info($locationId);
							?>
							<tr>
								<td style="font-weight: bold; color: red" colspan="2">( kho <?php echo $location->name; ?>)</td>
								
								<td style="font-weight: bold; color: red"><?php echo $total_qty_before; ?></td>
								<td style="font-weight: bold; color: red"><?php echo NumberFormatToCurrency($total_price_before); ?></td>
								<td style="font-weight: bold; color: red"><?php echo $total_qty_in; ?></td>
								<td style="font-weight: bold; color: red"><?php echo NumberFormatToCurrency($total_cost_in); ?></td>
								<td style="font-weight: bold; color: red"><?php echo $total_qty_out; ?></td>
								<td style="font-weight: bold; color: red"><?php echo NumberFormatToCurrency($total_price_out); ?></td>
								<td style="font-weight: bold; color: red"><?php echo $total_qty_after; ?></td>
								<td style="font-weight: bold; color: red"><?php echo NumberFormatToCurrency($total_price_after); ?></td>
							</tr>
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