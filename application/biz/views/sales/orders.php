<?php $this->load->view("partial/header");
	$controller_name="sales";
 ?>
	
	<div class="container-fluid">
		<div class="row manage-table">
			<div class="panel panel-piluku">
				<div class="panel-heading">
					<h3 class="panel-title hidden-print">
						 <?php echo lang('sale_orders_title'); ?>
					</h3>
				</div>
				<div style="min-height: 410px;" class="panel-body nopadding table_holder table-responsive">
					<div class="col-md-12" style="padding-top: 20px;">
						<?php 
							echo form_open('sales/orders', array('method'=>'get', 'class' => 'form_receipt_suspended_recv'));
						?>
								<div class="col-md-3">
									<div class="input-group input-daterange" id="reportrange">
	                                    <span class="input-group-addon bg">
				                           <?php echo lang('reports_from'); ?>
				                       	</span>
	                                    <input type="text" class="form-control start_date" name="start_date" id="start_date" value="<?php echo $start_date;?>">
	                                </div>
								</div>
								<div class="col-md-3">
									<div class="input-group input-daterange" id="reportrange1">
	                                    <span class="input-group-addon bg">
		                                    <?php echo lang('reports_to'); ?>
		                                </span>
	                                    <input type="text" class="form-control end_date" name="end_date" id="end_date" value="<?php echo $end_date;?>">
	                                </div>	
								</div>

								<div class="col-md-3">
									<div class="form-actions pull-left">
										<button style="height: 38px;" type="submit" id="search" class="btn btn-primary submit_button">Thực hiện</button>
									</div>
								</div>
							<?php echo form_close(); ?>
						</div>
						<div class="col-md-12">
						<table class="transfer_pending table table-bordered table-striped table-hover data-table" id="dTableA">
						<thead>
							<tr>
								<th><?php echo lang('sales_id'); ?></th>
								<th><?php echo lang('common_date'); ?></th>
								<th><?php echo lang('reports_customers'); ?></th>
								<th><?php echo lang('common_sale_employee'); ?></th>
								
								<th><?php echo lang('common_total_price'); ?></th>
								<th><?php echo lang('common_tax'); ?></th>
                                <th><?php echo lang('common_discount'); ?></th>
								
								<th><?php echo lang('common_comments'); ?></th>
								<th><?php echo lang('sales_receipt'); ?></th>
							</tr>
						</thead>
						<tbody>
					<?php
					foreach ($orders as $order)
					{
					?>
						<tr sale_id="<?php echo $order['sale_id'];?>">
							<input type="hidden" name="sale_id" value="<?php echo $order['sale_id'];?>" />
							<td>RECV <?php echo $order['sale_id'];?></td>
							<td><?php echo date(get_date_format(). ' @ '.get_time_format(),strtotime($order['sale_time']));?></td>
							<td>
								<?php echo $order['first_name'] . ' ' . $order['last_name'] ?>
							</td>
							<?php 
								$employee = $this->Employee->get_info($order['sold_by_employee_id']);
							?>
    						<td><?php echo $employee->first_name . ' ' . $employee->last_name; ?></td>                        
	
							<td><?php echo to_currency($order['total_price']); ?></td>
							
							<td><?php $taxes = $this->sale_lib->get_taxes($order['sale_id']); 
							if (!empty($taxes)) {
								foreach ($taxes as $key => $tax) {
									echo $key . ': ' . to_currency($tax). ' <br/>';
								}
							}
							?></td>
							<td><?php echo to_currency($order['total_discount']); ?></td>
							
							<td><?php echo $order['comments'];?></td>
							<td>
								<?php 
								echo form_open('sales/receipt/'.$order['sale_id'], array('method'=>'get', 'class' => 'form_receipt_suspended_recv'));
								?>
								<input type="submit" name="submit" value="<?php echo lang('common_recp'); ?>" id="submit_receipt" class="btn btn-primary">
								<?php echo form_close(); ?>
							</td>
						</tr>
					<?php
					}
					?>
					</tbody>
				</table>
				</div>			
			</div>
		</div>
	</div>
</div>
<?php $this->load->view("partial/footer"); ?>

<script type="text/javascript">
	
$(".form_email_receipt_suspended_sale").ajaxForm({success: function()
{
	bootbox.alert("<?php echo lang('common_receipt_sent'); ?>");
}});
$(".form_delete_suspended_recv").submit(function()
{
	var form = this;
	
	bootbox.confirm(<?php echo json_encode(lang("receivings_delete_confirmation")); ?>, function(result)
	{
		if (result)
		{
			form.submit();
		}
	});
	
	return false;
});

var SALES_ORDERS = {
	_datatable : null,
	init: function()
	{
		SALES_ORDERS.initDataTable();

		date_time_picker_field_report($('#start_date'), JS_DATE_FORMAT);
		date_time_picker_field_report($('#end_date'), JS_DATE_FORMAT);
	},
	initDataTable: function()
	{
		SALES_ORDERS._datatable = $('#dTableA').DataTable({
			"sPaginationType": "bootstrap"
		});
	}
}

$( document ).ready(function() {
	SALES_ORDERS.init();
});

</script>