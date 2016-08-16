<?php $this->load->view("partial/header");
	$controller_name="stock_out";
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
							echo form_open('stock_out/history', array('method'=>'get', 'class' => 'form_receipt_suspended_recv'));
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
								<th><?php echo lang('receivings_id'); ?></th>
								<th><?php echo lang('common_date'); ?></th>
								<th><?php echo lang('common_supplier'); ?></th>
								<th><?php echo lang('reports_items'); ?></th>
								
								
								<th><?php echo lang('common_comments'); ?></th>
								<th><?php echo lang('receivings_receipt'); ?></th>
							</tr>
						</thead>
						<tbody>
					<?php
					foreach ($history as $row)
					{
					?>
						<tr rec_id="<?php echo $row['id'];?>">
							<input type="hidden" name="stock_out_id" value="<?php echo $row['id'];?>" />
							<td># <?php echo $row['id'];?></td>
							<td><?php echo date(get_date_format(). ' @ '.get_time_format(),strtotime($row['created_time']));?></td>
							<td>
								<?php
								if (isset($row['employee']))
								{
									echo $row['employee'];
								}
								else
								{
								?>
									&nbsp;
								<?php
								}
								?>
							</td>
							<td><?php echo $row['items'];?></td>
							
							
							
							<td><?php echo $row['note'];?></td>
							
							<td>
								<a href="<?php echo site_url("stock_out/pre_print/" . $row['id'])?>" class="btn btn-primary btn-lg"><?php echo lang('common_recp'); ?></a>
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