<?php $this->load->view("partial/header");
	$controller_name="items";
 ?>
<style type="text/css">
	tr.delivery_warning_lv1 {
		background-color: #<?php echo $this->config->item('color_warning_level1'); ?> !important;
	}
	
	tr.delivery_warning_lv2 {
		background-color: #<?php echo $this->config->item('color_warning_level2'); ?> !important;
	}
	
	tr.delivery_warning_lv3 {
		background-color: #<?php echo $this->config->item('color_warning_level3'); ?> !important;
	}
</style>
	<div class="container-fluid">
		<div class="row manage-table">
			<div class="panel panel-piluku">
				<div class="panel-heading">
					<h3 class="panel-title hidden-print">
						 <?php echo lang('sales_list_of_suspended_sales'); ?>
					</h3>
				</div>
				<div class="panel-body nopadding table_holder table-responsive" >
					

						<table class="table table-bordered table-striped table-hover data-table" id="dTable">
				<thead>	<tr>
					<th><?php echo lang('sales_suspended_sale_id'); ?></th>
					<th><?php echo lang('common_date'); ?></th>
					<th><?php echo lang('common_type'); ?></th>
					<th><?php echo lang('sales_customer'); ?></th>
					
					<th><?php echo lang('common_delivery_date'); ?></th>
					<th><?php echo lang('common_deliverer'); ?></th>
					
					<th><?php echo lang('common_unsuspend'); ?></th>
					<th><?php echo lang('sales_receipt'); ?></th>
					<th><?php echo lang('common_email_receipt'); ?></th>
					<?php if ($this->Employee->has_module_action_permission('sales', 'delete_suspended_sale', $this->Employee->get_logged_in_employee_info()->person_id)){ ?>
					<th><?php echo lang('common_delete'); ?></th>
					<?php } ?>
				</tr>
				</thead>
				<tbody>
				<?php
				foreach ($suspended_sales as $suspended_sale)
				{
				?>
					<tr class="<?php echo getStatusOfDelivery($suspended_sale['delivery_date']) ?>">
						<td><?php echo ($this->config->item('sale_prefix') ? $this->config->item('sale_prefix') : 'POS' ). ' '.$suspended_sale['sale_id'];?></td>
						<td><?php echo date(get_date_format(). ' @ '.get_time_format(),strtotime($suspended_sale['sale_time']));?></td>
						<td width="15%">
							<?php if ($suspended_sale['suspended']== 1) 
							{
								echo lang('common_layaway'); 
							?>
							<?php } else { ?>
								<?php echo anchor('/sales/sales_quotes/'. $suspended_sale['sale_id'], lang('Báo giá'), array('id' => 'make_sales_quotes','class' => 'btn btn-primary', 'data-toggle'=>"modal",'data-target'=>"#myModal"));?>
								<?php echo anchor('/sales/sales_contract/'. $suspended_sale['sale_id'], lang('Hợp đồng'), array('id' => 'make_sales_contract','class' => 'btn btn-primary', 'data-toggle'=>"modal",'data-target'=>"#myModal"));?>
							<?php }?>
						</td>
						<td>
							<?php
							if (isset($suspended_sale['customer_id'])) 
							{
								$customer = $this->Customer->get_info($suspended_sale['customer_id']);
								$company_name = $customer->company_name;
								if($company_name) {
								echo $customer->first_name. ' '. $customer->last_name.' ('.$customer->company_name.')';
								}
								else {
									echo $customer->first_name. ' '. $customer->last_name;
								}
							}
							else
							{
							?>
								&nbsp;
							<?php
							}
							?>
						</td>
						<td><?php echo $suspended_sale['delivery_date']; ?></td>
						<td><?php 
						$deliverer = $this->Employee->get_info($suspended_sale['deliverer']);
						echo $deliverer ? $deliverer->first_name . ' ' . $deliverer->last_name : '';?></td>
						<td>
							<?php 
							echo form_open('sales/unsuspend');
							echo form_hidden('suspended_sale_id', $suspended_sale['sale_id']);
							?>
							<input type="submit" name="submit" value="<?php echo lang('common_unsuspend'); ?>" id="submit_unsuspend" class="btn btn-primary" />
							<?php echo form_close(); ?>
						</td>
						<td>
							<?php 
							echo form_open('sales/receipt/'.$suspended_sale['sale_id'], array('method'=>'get', 'class' => 'form_receipt_suspended_sale'));
							?>
							<input type="submit" name="submit" value="<?php echo lang('common_recp'); ?>" id="submit_receipt" class="btn btn-primary" />
							<?php echo form_close(); ?>
						</td>
						<td>
						<?php
						if ($suspended_sale['email']) 
						{
							echo form_open('sales/email_receipt/'.$suspended_sale['sale_id'], array('method'=>'get', 'class' => 'form_email_receipt_suspended_sale'));
							?>
								<input type="submit" name="submit" value="<?php echo lang('common_email'); ?>" id="submit_receipt" class="btn btn-primary" />
							<?php echo form_close(); ?>
						<?php } ?>
						
						</td>
						<?php 
						if ($this->Employee->has_module_action_permission('sales', 'delete_suspended_sale', $this->Employee->get_logged_in_employee_info()->person_id)){
						?>
						<td>
						<?php
						 	echo form_open('sales/delete_suspended_sale', array('class' => 'form_delete_suspended_sale'));
							echo form_hidden('suspended_sale_id', $suspended_sale['sale_id']);
							?>
							<input type="submit" name="submitf" value="<?php echo lang('common_delete'); ?>" id="submit_delete" class="btn btn-danger">
							<?php echo form_close(); ?>
						</td>
						<?php } ?>
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
$(".form_delete_suspended_sale").submit(function()
{
	var formDelete = this;
	bootbox.confirm(<?php echo json_encode(lang("sales_delete_confirmation")); ?>, function(result)
	{
		if (result)
		{
			formDelete.submit();
		}		
	});
	
	return false;
	
});

$(".form_email_receipt_suspended_sale").ajaxForm({success: function()
{
	bootbox.alert("<?php echo lang('common_receipt_sent'); ?>");
}});	

$('#dTable').dataTable({
	"sPaginationType": "bootstrap"
});

</script>