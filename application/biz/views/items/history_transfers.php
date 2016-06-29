<?php $this->load->view("partial/header");
	$controller_name="items";
 ?>
	
	<div class="container-fluid">
		<div class="row manage-table">
			<div class="panel panel-piluku">
				<div class="panel-heading">
					<h3 class="panel-title hidden-print">
						 <?php echo lang('items_history_transfer_title'); ?>
					</h3>
				</div>
				<div style="min-height: 410px;" class="panel-body nopadding table_holder table-responsive">
					<div class="col-md-12" style="padding-top: 20px;">
						<?php 
							echo form_open('items/history_transfer', array('method'=>'get', 'class' => 'form_receipt_suspended_recv'));
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
								<div class="col-md-6">
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
								<th><?php echo lang('receivings_id'); ?></th>
								<th><?php echo lang('common_date'); ?></th>
								<th><?php echo lang('common_supplier'); ?></th>
								<th><?php echo lang('reports_items'); ?></th>
								
								<th><?php echo lang('receivings_transfer_from'); ?></th>
								<th><?php echo lang('receivings_transfer_to'); ?></th>
								
								<th><?php echo lang('common_comments'); ?></th>
								<th><?php echo lang('receivings_receipt'); ?></th>
								<th><?php echo lang('common_email_receipt'); ?></th>
							</tr>
						</thead>
						<tbody>
					<?php
					foreach ($history_transfers as $transfer)
					{
					?>
						<tr rec_id="<?php echo $transfer['receiving_id'];?>">
							<input type="hidden" name="rec_id" value="<?php echo $transfer['receiving_id'];?>" />
							<td>RECV <?php echo $transfer['receiving_id'];?></td>
							<td><?php echo date(get_date_format(). ' @ '.get_time_format(),strtotime($transfer['receiving_time']));?></td>
							<td>
								<?php
								if (isset($transfer['supplier_id']))
								{
									$supplier = $this->Supplier->get_info($transfer['supplier_id']);
									echo $supplier->company_name.' ('.$supplier->first_name. ' '. $supplier->last_name.')';
								}
								else
								{
								?>
									&nbsp;
								<?php
								}
								?>
							</td>
							<td><?php echo $transfer['items'];?></td>
							
							<td><?php 
							$transferFrom = $this->Location->get_info($transfer['location_id']);
							echo $transferFrom->name;?></td>
							
							<td><?php 
							$transferTo = $this->Location->get_info($transfer['transfer_to_location_id']);
							echo $transferTo->name;?></td>
							
							<td><?php echo $transfer['comment'];?></td>
							
							<td>
								<?php 
								echo form_open('receivings/receipt/'.$transfer['receiving_id'], array('method'=>'get', 'class' => 'form_receipt_suspended_recv'));
								?>
								<input type="submit" name="submit" value="<?php echo lang('common_recp'); ?>" id="submit_receipt" class="btn btn-primary">
								<?php echo form_close(); ?>
							</td>
							
							<td>
							<?php
							if ($transfer['email']) 
							{
								echo form_open('receivings/email_receipt/'.$transfer['receiving_id'], array('method'=>'get', 'class' => 'form_email_receipt_suspended_sale'));
								?>
									<input type="submit" name="submit" value="<?php echo $transfer['is_po'] ? lang('receivings_email_po') : lang('common_email_receipt'); ?>" id="submit_receipt" class="btn btn-primary">
								<?php echo form_close(); ?>
							<?php } ?>
							
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

var TRANSFER_HISTORY = {
	_datatable : null,
	init: function()
	{
		TRANSFER_HISTORY.initDataTable();

		date_time_picker_field_report($('#start_date'), JS_DATE_FORMAT);
		date_time_picker_field_report($('#end_date'), JS_DATE_FORMAT);
	},
	initDataTable: function()
	{
		TRANSFER_HISTORY._datatable = $('#dTableA').DataTable({
			"sPaginationType": "bootstrap"
		});
	}
}

$( document ).ready(function() {
	TRANSFER_HISTORY.init();
});

</script>