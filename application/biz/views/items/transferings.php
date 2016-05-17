<?php $this->load->view("partial/header");
	$controller_name="items";
 ?>
	
	<div class="container-fluid">
		<div class="row manage-table">
			<div class="panel panel-piluku">
				<div class="panel-heading">
					<h3 class="panel-title hidden-print">
						 <?php echo lang('items_transfer_pending_title'); ?>
					</h3>
				</div>
				<div class="panel-body nopadding table_holder table-responsive">
						<table class="transfer_pending table table-bordered table-striped table-hover data-table" id="dTableA">
						<thead>
							<tr>
								<th><?php echo lang('receivings_id'); ?></th>
								<th><?php echo lang('common_date'); ?></th>
								<th><?php echo lang('common_supplier'); ?></th>
								<th><?php echo lang('reports_items'); ?></th>
								<th><?php echo lang('common_comments'); ?></th>
								<th><?php echo lang('common_unsuspend'); ?></th>
								<th><?php echo lang('receivings_receipt'); ?></th>
								<th><?php echo lang('common_email_receipt'); ?></th>
								<th>&nbsp;</th>
							</tr>
						</thead>
						<tbody>
					<?php
					foreach ($transferings as $transfering)
					{
					?>
						<tr rec_id="<?php echo $transfering['receiving_id'];?>">
							<input type="hidden" name="rec_id" value="<?php echo $transfering['receiving_id'];?>" />
							<td>RECV <?php echo $transfering['receiving_id'];?></td>
							<td><?php echo date(get_date_format(). ' @ '.get_time_format(),strtotime($transfering['receiving_time']));?></td>
							<td>
								<?php
								if (isset($transfering['supplier_id']))
								{
									$supplier = $this->Supplier->get_info($transfering['supplier_id']);
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
							<td><?php echo $transfering['items'];?></td>
							<td><?php echo $transfering['comment'];?></td>
							<td >
								<?php 
								echo form_open('receivings/unsuspend');
								echo form_hidden('suspended_receiving_id', $transfering['receiving_id']);
								?>
								<input type="submit" name="submit" value="<?php echo lang('common_unsuspend'); ?>" id="submit_unsuspend" class="btn btn-primary">
								<?php echo form_close(); ?>
							</td>
							<td>
								<?php 
								echo form_open('receivings/receipt/'.$transfering['receiving_id'], array('method'=>'get', 'class' => 'form_receipt_suspended_recv'));
								?>
								<input type="submit" name="submit" value="<?php echo lang('common_recp'); ?>" id="submit_receipt" class="btn btn-primary">
								<?php echo form_close(); ?>
							</td>
							
							<td>
							<?php
							if ($transfering['email']) 
							{
								echo form_open('receivings/email_receipt/'.$transfering['receiving_id'], array('method'=>'get', 'class' => 'form_email_receipt_suspended_sale'));
								?>
									<input type="submit" name="submit" value="<?php echo $transfering['is_po'] ? lang('receivings_email_po') : lang('common_email_receipt'); ?>" id="submit_receipt" class="btn btn-primary">
								<?php echo form_close(); ?>
							<?php } ?>
							
							</td>
							<td class="status">
								<?php if($transfering['transfer_status'] == 'pending') { ?>
								<button style="float: left;" type="button" class="btn btn-success btn_approve"><?php echo lang('common_approve'); ?></button>
								<button style="float: left; margin-left: 10px;" type="button" class="btn btn-danger btn_delete"><?php echo lang('common_delete'); ?></button>
								<?php } else {?>
									<i class="icon-success ion-checkmark-circled"></i> <?php echo lang('common_approved'); ?>
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

var TRANSFER_PENDING = {
	_datatable : null,
	init: function()
	{
		TRANSFER_PENDING.initDataTable();
	},
	initDataTable: function()
	{
		TRANSFER_PENDING._datatable = $('#dTableA').DataTable({
			"sPaginationType": "bootstrap"
		});
		TRANSFER_PENDING.clickEventOnDeleteRow();
		TRANSFER_PENDING.clickEventOnApproveBtn();
	},
	clickEventOnDeleteRow: function()
	{
		$('#dTableA tbody').on('click', 'td .btn_delete', function(){
			$('#dTableA tr').removeClass('row-selected');
			var _data = {};
			_data['rec_id'] = $(this).closest('tr').find('input[name="rec_id"]').val();
			$(this).closest('tr').addClass('row-selected');
			bootbox.confirm(<?php echo json_encode(lang("receivings_delete_confirmation")); ?>, function(result)
			{
				if (result)
				{
					coreAjax.call(
						'<?php echo site_url("items/delete_transfer");?>',
						_data,
						function(response)
						{
							if(response.success)
							{
								// location.reload();
								show_feedback('success', "<?php echo lang('common_delete'); ?>" + ' #REC' + _data['rec_id'], <?php echo json_encode(lang('common_success')); ?>);
								TRANSFER_PENDING._datatable.row('.row-selected').remove().draw( false );
							}
						}
					);
				}
			});
		});
	},
	clickEventOnApproveBtn : function()
	{
		$('#dTableA tbody').on('click', 'td .btn_approve', function(){
			$('#dTableA tr').removeClass('row-selected');
			var _data = {};
			_data['rec_id'] = $(this).closest('tr').find('input[name="rec_id"]').val();
			$(this).closest('tr').addClass('row-selected');
			var _status = $(this).closest('tr').find('td.status');
			bootbox.confirm(<?php echo json_encode(lang("receivings_approve_confirmation")); ?>, function(result)
			{
				if (result)
				{
					coreAjax.call(
						'<?php echo site_url("items/approve_transfer");?>',
						_data,
						function(response)
						{
							if(response.success)
							{
								// location.reload();
								// TRANSFER_PENDING._datatable.row('.row-selected').remove().draw( false );
								show_feedback('success', "<?php echo lang('common_approved'); ?>" + ' #REC' + _data['rec_id'], <?php echo json_encode(lang('common_success')); ?>);
								$(_status).html('<i class="icon-success ion-checkmark-circled"></i> <?php echo lang('common_approved'); ?>');
							}
						}
					);
				}
			});
		});
	}
}

$( document ).ready(function() {
	TRANSFER_PENDING.init();
});

</script>