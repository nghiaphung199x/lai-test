<?php $this->load->view("partial/header"); ?>
	<div class="row">
		<div class="col-md-6 col-md-offset-3">
			<div class="widget-box">
				<div class="widget-title">
				</div>
				<div class="widget-content nopadding">
		
					<h1 class="text-danger text-center" style="font-size:100px;"><i class="ion-trash-b"></i></h1>
					<?php  if ($success) { ?>
						<div class="alert alert-success text-center">
							
							<h4><strong><?php echo lang('sales_delete_successful'); ?></strong></h4>
							<?php echo anchor('sales/receipt/'.$sale_id, lang('sales_receipt'), array('target' =>'_blank')); ?>
						</div>

						</div>
					<?php } else { ?>
						<div class="alert alert-danger text-center">
							<h4><strong><?php echo lang('sales_delete_unsuccessful'); ?></strong></h4>
						</div>
					<?php } ?>				
				</div>
			</div>
		</div>
	</div>
<?php $this->load->view("partial/footer"); ?>