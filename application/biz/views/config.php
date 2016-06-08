<?php
$this->load->view("partial/header"); 
$this->load->helper('demo');
?>
<div class="manage_buttons">
	<div class="row">

		<div class="col-md-12">	
		<!-- Css Loader  -->
			<div class="spinner hidden" id="ajax-loader">
			  <div class="rect1"></div>
			  <div class="rect2"></div>
			  <div class="rect3"></div>
			</div>

			<div class="buttons-list config-page">
				<div class="search-tpl pull-left">
					<?php echo "<div class='row config-search'><div class='col-md-12'><input type='text' class='form-control' name ='search' id='search'  placeholder='".lang('common_search')."' /></div></div>"; ?>
				</div>
				<div class="pull-right-btn">
					<?php echo anchor('config/backup', lang('config_backup_database'), array('class' => 'btn btn-primary btn-lg dbBackup')); ?>
					<?php echo anchor('config/is_update_available', lang('common_check_for_update'), array('class' => 'checkForUpdate btn btn-success btn-lg')); ?> 
					<?php echo anchor('config/optimize',lang('config_optimize_database'), array('class' => 'dbOptimize btn btn-primary btn-lg')); ?> 
				</div>
			</div>
		</div>
	
	<?php 
	$this->load->helper('update');
	if (is_on_phppos_host()) {?>
	<div class="col-md-12">
		<div class="search">
			<?php echo lang('config_billing_is_managed_through_paypal');?>
		</div>
	</div>
	<?php } ?>
	
	</div>
</div>
<div class="text-center location-settings">
	<?php echo lang('config_looking_for_location_settings').' '.anchor($this->Location->count_all() > 1 ? 'locations' : 'locations/view/1', lang('module_locations').' '.lang('config_module'), 'class="btn btn-info"');?>
</div>
<div class="config-panel">
	<div class="row">
		<?php echo form_open_multipart('config/save/',array('id'=>'config_form','class'=>'form-horizontal', 'autocomplete'=> 'off'));  ?>
		<!-- Company Information -->
		<div class="col-md-12">
			<div class="panel panel-piluku">
				<div class="panel-heading">
					<?php echo lang("config_company_info"); ?>
				</div>
				<div class="panel-body">
					<div class="form-group">	
						<?php echo form_label(lang('config_company_logo').' :', 'company_logo',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
						<div class="col-sm-9 col-md-9 col-lg-10">
							
							<input type="file" name="company_logo" id="company_logo" class="filestyle" data-icon="false">  	
						</div>	
					</div>
					<div class="form-group">	
						<?php echo form_label(lang('config_delete_logo').' :', 'delete_logo',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
						<div class="col-sm-9 col-md-9 col-lg-10">
							<?php echo form_checkbox('delete_logo', '1', null,'id="delete_logo"');?>
							<label for="delete_logo"><span></span></label>
						</div>	
					</div>
					<div class="form-group">	
						<?php echo form_label(lang('common_company').' :', 'company',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label  required')); ?>
						<div class="col-sm-9 col-md-9 col-lg-10 input-field">
							<?php echo form_input(array(
								'class'=>'validate form-control form-inps',
							'name'=>'company',
							'id'=>'company',
							'value'=>$this->config->item('company')));?>
						</div>
					</div>
					<div class="form-group">	
						<?php echo form_label(lang('config_website').' :', 'website',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
						<div class="col-sm-9 col-md-9 col-lg-10 input-field">
						<?php echo form_input(array(
							'class'=>'form-control form-inps',
							'name'=>'website',
							'id'=>'website',
							'value'=>$this->config->item('website')));?>
						</div>
					</div>
				</div>
			</div>	
		</div>
		<!-- Taxes & Currency -->
		<div class="col-md-12">
			<div class="panel panel-piluku">
				<div class="panel-heading">
					<?php echo lang("config_tax_currency_info"); ?>
				</div>
				<div class="panel-body">
					<div class="form-group">	
						<?php echo form_label(lang('common_prices_include_tax').' :', 'prices_include_tax',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
						<div class="col-sm-9 col-md-9 col-lg-10">
						<?php echo form_checkbox(array(
							'name'=>'prices_include_tax',
							'id'=>'prices_include_tax',
							'value'=>'prices_include_tax',
							'checked'=>$this->config->item('prices_include_tax')));?>
							<label for="prices_include_tax"><span></span></label>
						</div>
					</div>
					
					<div class="form-group">	
						<?php echo form_label(lang('config_charge_tax_on_recv').' :', 'charge_tax_on_recv',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
						<div class="col-sm-9 col-md-9 col-lg-10">
						<?php echo form_checkbox(array(
							'name'=>'charge_tax_on_recv',
							'id'=>'charge_tax_on_recv',
							'value'=>'charge_tax_on_recv',
							'checked'=>$this->config->item('charge_tax_on_recv')));?>
							<label for="charge_tax_on_recv"><span></span></label>
						</div>
					</div>
					
					
					
					<div class="form-group">	
						<?php echo form_label(lang('common_default_tax_rate_1').' :', 'default_tax_1_rate',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
						<div class="col-sm-4 col-md-4 col-lg-5">
							<?php echo form_input(array(
							'class'=>'form-control form-inps',
							'name'=>'default_tax_1_name',
							'placeholder' => lang('common_tax_name'),
							'id'=>'default_tax_1_name',
							'size'=>'10',
							'value'=>$this->config->item('default_tax_1_name')!==NULL ? $this->config->item('default_tax_1_name') : lang('common_sales_tax_1')));?>
						</div>
						
						<div class="col-sm-4 col-md-4 col-lg-5">
							<?php echo form_input(array(
							'class'=>'form-control form-inps-tax',
							'placeholder' => lang('common_tax_percent'),
							'name'=>'default_tax_1_rate',
							'id'=>'default_tax_1_rate',
							'size'=>'4',
							'value'=>$this->config->item('default_tax_1_rate')));?>
							<div class="tax-percent-icon">%</div>
							<div class="clear"></div>
						</div>
					</div>
					<div class="form-group">	
						<?php echo form_label(lang('common_default_tax_rate_2').' :', 'default_tax_1_rate',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
						<div class="col-sm-4 col-md-4 col-lg-5">
							<?php echo form_input(array(
							'class'=>'form-control form-inps',
							'name'=>'default_tax_2_name',
							'placeholder' => lang('common_tax_name'),
							'id'=>'default_tax_2_name',
							'size'=>'10',
							'value'=>$this->config->item('default_tax_2_name')!==NULL ? $this->config->item('default_tax_2_name') : lang('common_sales_tax_2')));?>
						</div>

						<div class="col-sm-4 col-md-4 col-lg-5">
							<?php echo form_input(array(
							'class'=>'form-control form-inps-tax',	
							'name'=>'default_tax_2_rate',
							'placeholder' => lang('common_tax_percent'),
							'id'=>'default_tax_2_rate',
							'size'=>'4',
							'value'=>$this->config->item('default_tax_2_rate')));?>
							<div class="tax-percent-icon">%</div>
							<div class="clear"></div>
							<?php echo form_checkbox('default_tax_2_cumulative', '1', $this->config->item('default_tax_2_cumulative') ? true : false, 'id="default_tax_2_cumulative" class="cumulative_checkbox"');  ?>
							<label for="default_tax_2_cumulative"><span></span></label>
							<span class="cumulative_label">
								<?php echo lang('common_cumulative'); ?>
							</span>
						</div>
						
						<div class="col-sm-9 col-sm-offset-3 col-md-9 col-md-offset-3 col-lg-9 col-lg-offset-3" style="display: <?php echo $this->config->item('default_tax_3_rate') ? 'none' : 'block';?>">
							<a href="javascript:void(0);" class="show_more_taxes btn btn-orange btn-round"><?php echo lang('common_show_more');?> &raquo;</a>
						</div>
						
						<div class="col-md-12 more_taxes_container" style="display: <?php echo $this->config->item('default_tax_3_rate') ? 'block' : 'none';?>">
							<div class="form-group">	
								<?php echo form_label(lang('common_default_tax_rate_3').' :', 'default_tax_3_rate',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
								<div class="col-sm-4 col-md-4 col-lg-5">
									<?php echo form_input(array(
									'class'=>'form-control form-inps',
									'name'=>'default_tax_3_name',
									'placeholder' => lang('common_tax_name'),
									'id'=>'default_tax_3_name',
									'size'=>'10',
									'value'=>$this->config->item('default_tax_3_name')!==NULL ? $this->config->item('default_tax_3_name') : ''));?>
								</div>
						
								<div class="col-sm-4 col-md-4 col-lg-5">
									<?php echo form_input(array(
									'class'=>'form-control form-inps-tax',
									'placeholder' => lang('common_tax_percent'),
									'name'=>'default_tax_3_rate',
									'id'=>'default_tax_3_rate',
									'size'=>'4',
									'value'=>$this->config->item('default_tax_3_rate')));?>
									<div class="tax-percent-icon">%</div>
									<div class="clear"></div>
								</div>
							</div>
							
							
							<div class="form-group">	
								<?php echo form_label(lang('common_default_tax_rate_4').' :', 'default_tax_4_rate',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
								<div class="col-sm-4 col-md-4 col-lg-5">
									<?php echo form_input(array(
									'class'=>'form-control form-inps',
									'placeholder' => lang('common_tax_name'),
									'name'=>'default_tax_4_name',
									'id'=>'default_tax_4_name',
									'size'=>'10',
									'value'=>$this->config->item('default_tax_4_name')!==NULL ? $this->config->item('default_tax_4_name') : ''));?>
								</div>
						
								<div class="col-sm-4 col-md-4 col-lg-5">
									<?php echo form_input(array(
									'class'=>'form-control form-inps-tax',
									'placeholder' => lang('common_tax_percent'),
									'name'=>'default_tax_4_rate',
									'id'=>'default_tax_4_rate',
									'size'=>'4',
									'value'=>$this->config->item('default_tax_4_rate')));?>
									<div class="tax-percent-icon">%</div>
									<div class="clear"></div>
								</div>
							</div>
							
							<div class="form-group">	
								<?php echo form_label(lang('common_default_tax_rate_5').' :', 'default_tax_5_rate',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
								<div class="col-sm-4 col-md-4 col-lg-5">
									<?php echo form_input(array(
									'class'=>'form-control form-inps',
									'placeholder' => lang('common_tax_name'),
									'name'=>'default_tax_5_name',
									'id'=>'default_tax_5_name',
									'size'=>'10',
									'value'=>$this->config->item('default_tax_5_name')!==NULL ? $this->config->item('default_tax_5_name') : ''));?>
								</div>
						
								<div class="col-sm-4 col-md-4 col-lg-5">
									<?php echo form_input(array(
									'class'=>'form-control form-inps-tax',
									'placeholder' => lang('common_tax_percent'),
									'name'=>'default_tax_5_rate',
									'id'=>'default_tax_5_rate',
									'size'=>'4',
									'value'=>$this->config->item('default_tax_5_rate')));?>
									<div class="tax-percent-icon">%</div>
									<div class="clear"></div>
								</div>
							</div>
						</div>
					</div>
					<div class="form-group">	
						<?php echo form_label(lang('config_barcode_price_include_tax').' :', 'barcode_price_include_tax',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
						<div class="col-sm-9 col-md-9 col-lg-10">
						<?php echo form_checkbox(array(
							'name'=>'barcode_price_include_tax',
							'id'=>'barcode_price_include_tax',
							'value'=>'barcode_price_include_tax',
							'checked'=>$this->config->item('barcode_price_include_tax')));?>
							<label for="barcode_price_include_tax"><span></span></label>
						</div>
					</div>
					<div class="form-group">	
						<?php echo form_label(lang('config_currency_symbol').' :', 'currency_symbol',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
						<div class="col-sm-9 col-md-9 col-lg-10">
							<?php echo form_input(array(
								'class'=>'form-control form-inps',
							'name'=>'currency_symbol',
							'id'=>'currency_symbol',
							'value'=>$this->config->item('currency_symbol')));?>
						</div>
					</div>				
 					<div class="form-group">	
 					<?php echo form_label(lang('config_number_of_decimals').' :', 'number_of_decimals',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label')); ?>
 						<div class="col-sm-9 col-md-9 col-lg-10">
 						<?php echo form_dropdown('number_of_decimals', array(
 							''  => lang('config_let_system_decide'),
 							'0'    => '0',
 							'1'    => '1',
 							'2'    => '2',
 							'3'    => '3',
 							'4'    => '4',
 							'5'    => '5',
						),
 							$this->config->item('number_of_decimals')===NULL ? '' : $this->config->item('number_of_decimals') , 'class="form-control" id="number_of_decimals"');
 							?>
 						</div>						
 					</div>				
					
					<div class="form-group">	
						<?php echo form_label(lang('config_thousands_separator').' :', 'thousands_separator',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label')); ?>
						<div class="col-sm-9 col-md-9 col-lg-10 input-field">
							<?php echo form_input(array(
								'class'=>'validate form-control form-inps',
							'name'=>'thousands_separator',
							'id'=>'thousands_separator',
							'value'=>$this->config->item('thousands_separator') ? $this->config->item('thousands_separator') : ','));?>
						</div>
					</div>
					
					<div class="form-group">	
						<?php echo form_label(lang('config_decimal_point').' :', 'decimal_point',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label')); ?>
						<div class="col-sm-9 col-md-9 col-lg-10 input-field">
							<?php echo form_input(array(
								'class'=>'validate form-control form-inps',
							'name'=>'decimal_point',
							'id'=>'decimal_point',
							'value'=>$this->config->item('decimal_point') ? $this->config->item('decimal_point') : '.'));?>
						</div>
					</div>
					
					<div class="form-group">	
					<?php echo form_label(lang('config_currency_denoms').' :', '',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
						<div class="table-responsive col-sm-9 col-md-4 col-lg-4">
						<table id="currency_denoms" class="table">
							<thead>
								<tr>
								<th><?php echo lang('common_denomination'); ?></th>
								<th><?php echo lang('config_currency_value'); ?></th>
								<th><?php echo lang('common_delete'); ?></th>
								</tr>
							</thead>
							
							<tbody>
							<?php foreach($currency_denoms->result() as $currency_denom) { ?>
								<tr>
									<td><input type="text" name="currency_denoms_name[]" class="form-control" value="<?php echo H($currency_denom->name); ?>" /></td>
									<td><input type="text" name="currency_denoms_value[]" class="form-control" value="<?php echo H(to_currency_no_money($currency_denom->value)); ?>" /></td>
									<td><a class="delete_currency_denom text-primary" href="javascript:void(0);"><?php echo lang('common_delete'); ?></a></td>
								</tr>
							<?php } ?>
							</tbody>
						</table>
						
						<a href="javascript:void(0);" id="add_denom"><?php echo lang('config_add_currency_denom'); ?></a>
						</div>
					</div>
				</div>
			</div>
		</div>
		<!-- Sales & Receipt -->
		<div class="col-md-12">
			<div class="panel panel-piluku">
				<div class="panel-heading">
					<?php echo lang("config_sales_receipt_info"); ?>
				</div>
				<div class="panel-body">
					<div class="form-group">	
						<?php echo form_label(lang('config_prefix').' :', 'sale_prefix',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label  required')); ?>
						<div class="col-sm-9 col-md-9 col-lg-10">
							<?php echo form_input(array(
								'class'=>'form-control form-inps',
							'name'=>'sale_prefix',
							'id'=>'sale_prefix',
							'value'=>$this->config->item('sale_prefix')));?>
						</div>
					</div>
					
					<div class="form-group">	
						<?php echo form_label(lang('config_override_receipt_title').' :', 'override_receipt_title',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label')); ?>
						<div class="col-sm-9 col-md-9 col-lg-10">
							<?php echo form_input(array(
								'class'=>'form-control form-inps',
							'name'=>'override_receipt_title',
							'id'=>'override_receipt_title',
							'value'=>$this->config->item('override_receipt_title')));?>
						</div>
					</div>
					
					<div class="form-group">	
						<?php echo form_label(lang('config_id_to_show_on_sale_interface').' :', 'id_to_show_on_sale_interface',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label  required')); ?>
						<div class="col-sm-9 col-md-9 col-lg-10">
						<?php echo form_dropdown('id_to_show_on_sale_interface', array(
							'number'  => lang('common_item_number_expanded'),
							'product_id'    => lang('common_product_id'),
							'id'   => lang('common_item_id')
							),
							$this->config->item('id_to_show_on_sale_interface'), 'class="form-control" id="id_to_show_on_sale_interface"')
							?>
						</div>
					</div>
					
					
					<div class="form-group">	
						<?php echo form_label(lang('config_show_item_id_on_receipt').' :', 'show_item_id_on_receipt',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
						<div class="col-sm-9 col-md-9 col-lg-10">
						<?php echo form_checkbox(array(
							'name'=>'show_item_id_on_receipt',
							'id'=>'show_item_id_on_receipt',
							'value'=>'show_item_id_on_receipt',
							'checked'=>$this->config->item('show_item_id_on_receipt')));?>
							<label for="show_item_id_on_receipt"><span></span></label>
						</div>
					</div>
					
					
					<div class="form-group">	
						<?php echo form_label(lang('config_print_after_sale').' :', 'print_after_sale',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
						<div class="col-sm-9 col-md-9 col-lg-10">
						<?php echo form_checkbox(array(
							'name'=>'print_after_sale',
							'id'=>'print_after_sale',
							'value'=>'print_after_sale',
							'checked'=>$this->config->item('print_after_sale')));?>
							<label for="print_after_sale"><span></span></label>
						</div>
					</div>
					<div class="form-group">	
						<?php echo form_label(lang('config_print_after_receiving').' :', 'print_after_receiving',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
						<div class="col-sm-9 col-md-9 col-lg-10">
						<?php echo form_checkbox(array(
							'name'=>'print_after_receiving',
							'id'=>'print_after_receiving',
							'value'=>'print_after_receiving',
							'checked'=>$this->config->item('print_after_receiving')));?>
							<label for="print_after_receiving"><span></span></label>
						</div>
					</div>
					<div class="form-group">	
						<?php echo form_label(lang('config_auto_focus_on_item_after_sale_and_receiving').' :', 'auto_focus_on_item_after_sale_and_receiving',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
						<div class="col-sm-9 col-md-9 col-lg-10">
						<?php echo form_checkbox(array(
							'name'=>'auto_focus_on_item_after_sale_and_receiving',
							'id'=>'auto_focus_on_item_after_sale_and_receiving',
							'value'=>'auto_focus_on_item_after_sale_and_receiving',
							'checked'=>$this->config->item('auto_focus_on_item_after_sale_and_receiving')));?>
							<label for="auto_focus_on_item_after_sale_and_receiving"><span></span></label>
						</div>
					</div>
										
					<div class="form-group">	
						<?php echo form_label(lang('config_hide_signature').' :', 'hide_signature',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
						<div class="col-sm-9 col-md-9 col-lg-10">
						<?php echo form_checkbox(array(
							'name'=>'hide_signature',
							'id'=>'hide_signature',
							'value'=>'hide_signature',
							'checked'=>$this->config->item('hide_signature')));?>
							<label for="hide_signature"><span></span></label>
						</div>
					</div>
					
					<div class="form-group">	
						<?php echo form_label(lang('config_number_of_recent_sales').' :', 'number_of_recent_sales',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
						<div class="col-sm-9 col-md-9 col-lg-10">

						<?php echo form_dropdown('number_of_recent_sales', 
						 array(
							'1'=>'1',
							'2'=>'2',
							'5'=>'5',
							'10'=>'10',
							'20'=>'20',
							'50'=>'50'
							), $this->config->item('number_of_recent_sales') ? $this->config->item('number_of_recent_sales') : '10', 'class="form-control" id="number_of_recent_sales"');
							?>

						</div>
					</div>
					
					<div class="form-group">	
						<?php echo form_label(lang('config_remove_customer_contact_info_from_receipt').' :', 'remove_customer_contact_info_from_receipt',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
						<div class="col-sm-9 col-md-9 col-lg-10">
						<?php echo form_checkbox(array(
							'name'=>'remove_customer_contact_info_from_receipt',
							'id'=>'remove_customer_contact_info_from_receipt',
							'value'=>'remove_customer_contact_info_from_receipt',
							'checked'=>$this->config->item('remove_customer_contact_info_from_receipt')));?>
						<label for="remove_customer_contact_info_from_receipt"><span></span></label>
						</div>
					</div>
					
					
					
					<div class="form-group">	
						<?php echo form_label(lang('config_hide_customer_recent_sales').' :', 'hide_customer_recent_sales',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
						<div class="col-sm-9 col-md-9 col-lg-10">
						<?php echo form_checkbox(array(
							'name'=>'hide_customer_recent_sales',
							'id'=>'hide_customer_recent_sales',
							'value'=>'hide_customer_recent_sales',
							'checked'=>$this->config->item('hide_customer_recent_sales')));?>
							<label for="hide_customer_recent_sales"><span></span></label>
						</div>
					</div>
					<div class="form-group">	
						<?php echo form_label(lang('disable_confirmation_sale').' :', 'disable_confirmation_sale',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
						<div class="col-sm-9 col-md-9 col-lg-10">
						<?php echo form_checkbox(array(
							'name'=>'disable_confirmation_sale',
							'id'=>'disable_confirmation_sale',
							'value'=>'disable_confirmation_sale',
							'checked'=>$this->config->item('disable_confirmation_sale')));?>
							<label for="disable_confirmation_sale"><span></span></label>
						</div>
					</div>
					
					<div class="form-group">	
						<?php echo form_label(lang('disable_quick_complete_sale').' :', 'disable_quick_complete_sale',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
						<div class="col-sm-9 col-md-9 col-lg-10">
						<?php echo form_checkbox(array(
							'name'=>'disable_quick_complete_sale',
							'id'=>'disable_quick_complete_sale',
							'value'=>'disable_quick_complete_sale',
							'checked'=>$this->config->item('disable_quick_complete_sale')));?>
							<label for="disable_quick_complete_sale"><span></span></label>
						</div>
					</div>
					
					
					<div class="form-group">	
						<?php echo form_label(lang('config_round_cash_on_sales').' :', 'round_cash_on_sales',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
						<div class="col-sm-9 col-md-9 col-lg-10">
						<?php echo form_checkbox(array(
							'name'=>'round_cash_on_sales',
							'id'=>'round_cash_on_sales',
							'value'=>'round_cash_on_sales',
							'checked'=>$this->config->item('round_cash_on_sales')));?>
							<label for="round_cash_on_sales"><span></span></label>
						</div>
					</div>
					<div class="form-group">	
						<?php echo form_label(lang('config_automatically_email_receipt').' :', 'automatically_email_receipt',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
						<div class="col-sm-9 col-md-9 col-lg-10">
						<?php echo form_checkbox(array(
							'name'=>'automatically_email_receipt',
							'id'=>'automatically_email_receipt',
							'value'=>'automatically_email_receipt',
							'checked'=>$this->config->item('automatically_email_receipt')));?>
							<label for="automatically_email_receipt"><span></span></label>
						</div>
					</div>
					
					<div class="form-group">	
						<?php echo form_label(lang('config_automatically_print_duplicate_receipt_for_cc_transactions').' :', 'automatically_print_duplicate_receipt_for_cc_transactions',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
						<div class="col-sm-9 col-md-9 col-lg-10">
						<?php echo form_checkbox(array(
							'name'=>'automatically_print_duplicate_receipt_for_cc_transactions',
							'id'=>'automatically_print_duplicate_receipt_for_cc_transactions',
							'value'=>'automatically_print_duplicate_receipt_for_cc_transactions',
							'checked'=>$this->config->item('automatically_print_duplicate_receipt_for_cc_transactions')));?>
							<label for="automatically_print_duplicate_receipt_for_cc_transactions"><span></span></label>
						</div>
					</div>
					
					
					<div class="form-group">	
						<?php echo form_label(lang('config_automatically_show_comments_on_receipt').' :', 'automatically_show_comments_on_receipt',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
						<div class="col-sm-9 col-md-9 col-lg-10">
						<?php echo form_checkbox(array(
							'name'=>'automatically_show_comments_on_receipt',
							'id'=>'automatically_show_comments_on_receipt',
							'value'=>'automatically_show_comments_on_receipt',
							'checked'=>$this->config->item('automatically_show_comments_on_receipt')));?>
							<label for="automatically_show_comments_on_receipt"><span></span></label>
						</div>
					</div>
					<div class="form-group">	
						<?php echo form_label(lang('config_automatically_calculate_average_cost_price_from_receivings').' :', 'calculate_average_cost_price_from_receivings',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
						<div class="col-sm-9 col-md-9 col-lg-10">
						<?php echo form_checkbox(array(
							'name'=>'calculate_average_cost_price_from_receivings',
							'id'=>'calculate_average_cost_price_from_receivings',
							'value'=>'1',
							'checked'=>$this->config->item('calculate_average_cost_price_from_receivings')));?>
							<label for="calculate_average_cost_price_from_receivings"><span></span></label>
						</div>
					</div>
					
					<div id="average_cost_price_from_receivings_methods">
						<div class="form-group">	
						<?php echo form_label(lang('config_averaging_method').' :', 'averaging_method',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label')); ?>
							<div class="col-sm-9 col-md-9 col-lg-10">
								<?php echo form_dropdown('averaging_method', array('moving_average' => lang('config_moving_average'), 'historical_average' => lang('config_historical_average'), 'dont_average' => lang('config_dont_average_use_current_recv_price')), $this->config->item('averaging_method'),'class="form-control" id="averaging_method"'); ?>
							</div>
						</div>
					</div>
										
					<div class="form-group">	
						<?php echo form_label(lang('config_always_use_average_cost_method').' :', 'always_use_average_cost_method',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
						<div class="col-sm-9 col-md-9 col-lg-10">
						<?php echo form_checkbox(array(
							'name'=>'always_use_average_cost_method',
							'id'=>'always_use_average_cost_method',
							'value'=>'1',
							'checked'=>$this->config->item('always_use_average_cost_method')));?>
							<label for="always_use_average_cost_method"><span></span></label>
						</div>
					</div>
					
					
					<div class="form-group">	
						<?php echo form_label(lang('config_hide_suspended_recv_in_reports').' :', 'hide_suspended_recv_in_reports',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
						<div class="col-sm-9 col-md-9 col-lg-10">
						<?php echo form_checkbox(array(
							'name'=>'hide_suspended_recv_in_reports',
							'id'=>'hide_suspended_recv_in_reports',
							'value'=>'1',
							'checked'=>$this->config->item('hide_suspended_recv_in_reports')));?>
							<label for="hide_suspended_recv_in_reports"><span></span></label>
						</div>
					</div>
					
					<div class="form-group">	
						<?php echo form_label(lang('common_track_cash').' :', 'track_cash',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
						<div class="col-sm-9 col-md-9 col-lg-10">
						<?php echo form_checkbox(array(
							'name'=>'track_cash',
							'id'=>'track_cash',
							'value'=>'1',
							'checked'=>$this->config->item('track_cash')));?>
							<label for="track_cash"><span></span></label>
						</div>
					</div>
					<div class="form-group">	
						<?php echo form_label(lang('config_disable_giftcard_detection').' :', 'disable_giftcard_detection',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
						<div class="col-sm-9 col-md-9 col-lg-10">
						<?php echo form_checkbox(array(
							'name'=>'disable_giftcard_detection',
							'id'=>'disable_giftcard_detection',
							'value'=>'1',
							'checked'=>$this->config->item('disable_giftcard_detection')));?>
							<label for="disable_giftcard_detection"><span></span></label>
						</div>
					</div>
					
					<div class="form-group">	
						<?php echo form_label(lang('config_calculate_profit_for_giftcard_when').' :', 'calculate_profit_for_giftcard_when',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
						<div class="col-sm-9 col-md-9 col-lg-10">


							<?php echo form_dropdown('calculate_profit_for_giftcard_when', array(
								''  => lang('common_do_nothing'),
								'redeeming_giftcard'   => lang('config_redeeming_giftcard'), 
								'selling_giftcard'  => lang('config_selling_giftcard'),
							),
							$this->config->item('calculate_profit_for_giftcard_when'), 'class="form-control" id="calculate_profit_for_giftcard_when"');
							?>
						</div>
					</div>
					
					<div class="form-group">	
						<?php echo form_label(lang('config_always_show_item_grid').' :', 'always_show_item_grid',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
						<div class="col-sm-9 col-md-9 col-lg-10">
						<?php echo form_checkbox(array(
							'name'=>'always_show_item_grid',
							'id'=>'always_show_item_grid',
							'value'=>'1',
							'checked'=>$this->config->item('always_show_item_grid')));?>
							<label for="always_show_item_grid"><span></span></label>
						</div>
					</div>
					
					<div class="form-group">	
						<?php echo form_label(lang('config_hide_out_of_stock_grid').' :', 'hide_out_of_stock_grid',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
						<div class="col-sm-9 col-md-9 col-lg-10">
						<?php echo form_checkbox(array(
							'name'=>'hide_out_of_stock_grid',
							'id'=>'hide_out_of_stock_grid',
							'value'=>'1',
							'checked'=>$this->config->item('hide_out_of_stock_grid')));?>
							<label for="hide_out_of_stock_grid"><span></span></label>
						</div>
					</div>
										
					<div class="form-group">	
						<?php echo form_label(lang('config_default_type_for_grid').' :', 'default_type_for_grid',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
						<div class="col-sm-9 col-md-9 col-lg-10">

							<?php echo form_dropdown('default_type_for_grid', array(
								'categories'  => lang('reports_categories'), 
								'tags'  => lang('common_tags'),
							),
							$this->config->item('default_type_for_grid'), 'class="form-control" id="default_type_for_grid"');
							?>
						</div>
					</div>
					
					
					<div class="form-group">	
						<?php echo form_label(lang('config_hide_barcode_on_sales_and_recv_receipt').' :', 'hide_barcode_on_sales_and_recv_receipt',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
						<div class="col-sm-9 col-md-9 col-lg-10">
						<?php echo form_checkbox(array(
							'name'=>'hide_barcode_on_sales_and_recv_receipt',
							'id'=>'hide_barcode_on_sales_and_recv_receipt',
							'value'=>'1',
							'checked'=>$this->config->item('hide_barcode_on_sales_and_recv_receipt')));?>
							<label for="hide_barcode_on_sales_and_recv_receipt"><span></span></label>
						</div>
					</div>
					
					<div class="form-group">	
						<?php echo form_label(lang('config_round_tier_prices_to_2_decimals').' :', 'round_tier_prices_to_2_decimals',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
						<div class="col-sm-9 col-md-9 col-lg-10">
						<?php echo form_checkbox(array(
							'name'=>'round_tier_prices_to_2_decimals',
							'id'=>'round_tier_prices_to_2_decimals',
							'value'=>'1',
							'checked'=>$this->config->item('round_tier_prices_to_2_decimals')));?>
							<label for="round_tier_prices_to_2_decimals"><span></span></label>
						</div>
					</div>

					<div class="form-group">	
						<?php echo form_label(lang('config_group_all_taxes_on_receipt').' :', 'group_all_taxes_on_receipt',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
						<div class="col-sm-9 col-md-9 col-lg-10">
						<?php echo form_checkbox(array(
							'name'=>'group_all_taxes_on_receipt',
							'id'=>'group_all_taxes_on_receipt',
							'value'=>'1',
							'checked'=>$this->config->item('group_all_taxes_on_receipt')));?>
							<label for="group_all_taxes_on_receipt"><span></span></label>
						</div>
					</div>
					
					<div class="form-group">	
						<?php echo form_label(lang('config_require_customer_for_sale').' :', 'require_customer_for_sale',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
						<div class="col-sm-9 col-md-9 col-lg-10">
						<?php echo form_checkbox(array(
							'name'=>'require_customer_for_sale',
							'id'=>'require_customer_for_sale',
							'value'=>'1',
							'checked'=>$this->config->item('require_customer_for_sale')));?>
							<label for="require_customer_for_sale"><span></span></label>
						</div>
					</div>
					
					<div class="form-group">	
						<?php echo form_label(lang('config_require_customer_for_suspended_sale').' :', 'require_customer_for_suspended_sale',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
						<div class="col-sm-9 col-md-9 col-lg-10">
						<?php echo form_checkbox(array(
							'name'=>'require_customer_for_suspended_sale',
							'id'=>'require_customer_for_suspended_sale',
							'value'=>'1',
							'checked'=>$this->config->item('require_customer_for_suspended_sale')));?>
							<label for="require_customer_for_suspended_sale"><span></span></label>
						</div>
					</div>
									
					
					<div class="form-group">	
						<?php echo form_label(lang('config_redirect_to_sale_or_recv_screen_after_printing_receipt').' :', 'redirect_to_sale_or_recv_screen_after_printing_receipt',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
						<div class="col-sm-9 col-md-9 col-lg-10">
						<?php echo form_checkbox(array(
							'name'=>'redirect_to_sale_or_recv_screen_after_printing_receipt',
							'id'=>'redirect_to_sale_or_recv_screen_after_printing_receipt',
							'value'=>'1',
							'checked'=>$this->config->item('redirect_to_sale_or_recv_screen_after_printing_receipt')));?>
							<label for="redirect_to_sale_or_recv_screen_after_printing_receipt"><span></span></label>
						</div>
					</div>
					
										
					<div class="form-group">	
						<?php echo form_label(lang('config_payment_types').' :', 'additional_payment_types',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
						<div class="col-sm-9 col-md-9 col-lg-10">
							<a href="#" class="btn btn-primary payment_types"><?php echo lang('common_cash'); ?></a> 
							<a href="#" class="btn btn-primary payment_types"><?php echo lang('common_check'); ?></a> 
							<a href="#" class="btn btn-primary payment_types"><?php echo lang('common_giftcard'); ?></a> 
							<a href="#" class="btn btn-primary payment_types"><?php echo lang('common_debit'); ?></a> 
							<a href="#" class="btn btn-primary payment_types"><?php echo lang('common_credit'); ?></a>
							<br>
							<br>
							<?php echo form_input(array(
								'class'=>'form-control form-inps',
								'name'=>'additional_payment_types',
								'id'=>'additional_payment_types',
								'size'=> 40,
								'value'=>$this->config->item('additional_payment_types')));?>
						</div>
					</div>
					<div class="form-group">	
						<?php echo form_label(lang('config_default_payment_type').' :', 'default_payment_type',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
						<div class="col-sm-9 col-md-9 col-lg-10">
						<?php echo form_dropdown('default_payment_type', $payment_options, $this->config->item('default_payment_type'),'class="form-control" id="default_payment_type"'); ?>
						</div>
					</div>
											
					<div class="form-group">	
						<?php echo form_label(lang('config_receipt_text_size').' :', 'receipt_text_size',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
						<div class="col-sm-9 col-md-9 col-lg-10">
						<?php echo form_dropdown('receipt_text_size', $receipt_text_size_options, $this->config->item('receipt_text_size'),'class="form-control" id="receipt_text_size"'); ?>
						</div>
					</div>
					
					<div class="form-group">	
						<?php echo form_label(lang('config_select_sales_person_during_sale').' :', 'select_sales_person_during_sale',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
						<div class="col-sm-9 col-md-9 col-lg-10">
						<?php echo form_checkbox(array(
							'name'=>'select_sales_person_during_sale',
							'id'=>'select_sales_person_during_sale',
							'value'=>'1',
							'checked'=>$this->config->item('select_sales_person_during_sale')));?>
							<label for="select_sales_person_during_sale"><span></span></label>
						</div>
					</div>
					
					<div class="form-group">	
						<?php echo form_label(lang('config_default_sales_person').' :', 'default_sales_person',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
						<div class="col-sm-9 col-md-9 col-lg-10"> 
						<?php echo form_dropdown('default_sales_person', array('logged_in_employee' => lang('common_logged_in_employee'), 'not_set' => lang('common_not_set')), $this->config->item('default_sales_person'),'class="form-control" id="default_sales_person"'); ?>
						</div>
					</div>
					
					<div class="form-group">	
						<?php echo form_label(lang('common_commission_default_rate').' ('.lang('common_commission_help').'):', 'commission_default_rate',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label')); ?>
						<div class="col-sm-9 col-md-9 col-lg-10">
							<?php echo form_input(array(
							'name'=>'commission_default_rate',
							'id'=>'commission_default_rate',
							'class'=>'form-control',
							'value'=>$this->config->item('commission_default_rate')));?>%
						</div>
					</div>
					
					<div class="form-group">	
						<?php echo form_label(lang('common_commission_percent_calculation').': ', 'commission_percent_type',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label')); ?>
						<div class="col-sm-9 col-md-9 col-lg-10">
						<?php echo form_dropdown('commission_percent_type', array(
							'selling_price'  => lang('common_unit_price'),
							'profit'    => lang('common_profit'),
							),
							$this->config->item('commission_percent_type'),
							array('id' => 'commission_percent_type'))
							?>
						</div>
					</div>
					
					
					<div class="form-group">	
						<?php echo form_label(lang('config_disable_sale_notifications').' :', 'disable_sale_notifications',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
						<div class="col-sm-9 col-md-9 col-lg-10">
						<?php echo form_checkbox(array(
							'name'=>'disable_sale_notifications',
							'id'=>'disable_sale_notifications',
							'value'=>'1',
							'checked'=>$this->config->item('disable_sale_notifications')));?>
							<label for="disable_sale_notifications"><span></span></label>
						</div>
					</div>
					<div class="form-group">	
						<?php echo form_label(lang('config_change_sale_date_for_new_sale').' :', 'change_sale_date_for_new_sale',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
						<div class="col-sm-9 col-md-9 col-lg-10">
						<?php echo form_checkbox(array(
							'name'=>'change_sale_date_for_new_sale',
							'id'=>'change_sale_date_for_new_sale',
							'value'=>'1',
							'checked'=>$this->config->item('change_sale_date_for_new_sale')));?>
							<label for="change_sale_date_for_new_sale"><span></span></label>
						</div>
					</div>
					
					
					<div class="form-group">	
						<?php echo form_label(lang('config_do_not_group_same_items').' :', 'do_not_group_same_items',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
						<div class="col-sm-9 col-md-9 col-lg-10">
						<?php echo form_checkbox(array(
							'name'=>'do_not_group_same_items',
							'id'=>'do_not_group_same_items',
							'value'=>'1',
							'checked'=>$this->config->item('do_not_group_same_items')));?>
							<label for="do_not_group_same_items"><span></span></label>
						</div>
					</div>

					<div class="form-group">	
						<?php echo form_label(lang('config_hide_store_account_balance_on_receipt').' :', 'hide_store_account_balance_on_receipt',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
						<div class="col-sm-9 col-md-9 col-lg-10">
						<?php echo form_checkbox(array(
							'name'=>'hide_store_account_balance_on_receipt',
							'id'=>'hide_store_account_balance_on_receipt',
							'value'=>'1',
							'checked'=>$this->config->item('hide_store_account_balance_on_receipt')));?>
							<label for="hide_store_account_balance_on_receipt"><span></span></label>
						</div>
					</div>
					
					<div class="form-group">	
						<?php echo form_label(lang('config_disable_store_account_when_over_credit_limit').' :', 'disable_store_account_when_over_credit_limit',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
						<div class="col-sm-9 col-md-9 col-lg-10">
						<?php echo form_checkbox(array(
							'name'=>'disable_store_account_when_over_credit_limit',
							'id'=>'disable_store_account_when_over_credit_limit',
							'value'=>'1',
							'checked'=>$this->config->item('disable_store_account_when_over_credit_limit')));?>
							<label for="disable_store_account_when_over_credit_limit"><span></span></label>
						</div>
					</div>
					
					
					<div class="form-group">	
					<?php echo form_label(lang('config_store_account_statement_message').' :', 'store_account_statement_message',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label')); ?>
						<div class="col-sm-9 col-md-9 col-lg-10">
						<?php echo form_textarea(array(
							'name'=>'store_account_statement_message',
							'id'=>'store_account_statement_message',
							'class'=>'form-control text-area',
							'rows'=>'4',
							'cols'=>'30',
							'value'=>$this->config->item('store_account_statement_message')));?>
						</div>
					</div>
					
					
					
					<div class="form-group">	
						<?php echo form_label(lang('config_prompt_for_ccv_swipe').' :', 'prompt_for_ccv_swipe',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
						<div class="col-sm-9 col-md-9 col-lg-10">
						<?php echo form_checkbox(array(
							'name'=>'prompt_for_ccv_swipe',
							'id'=>'prompt_for_ccv_swipe',
							'value'=>'1',
							'checked'=>$this->config->item('prompt_for_ccv_swipe')));?>
							<label for="prompt_for_ccv_swipe"><span></span></label>
						</div>
					</div>
					
					
					
					<div class="form-group">	
						<?php echo form_label(lang('config_do_not_allow_below_cost').' :', 'do_not_allow_below_cost',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
						<div class="col-sm-9 col-md-9 col-lg-10">
						<?php echo form_checkbox(array(
							'name'=>'do_not_allow_below_cost',
							'id'=>'do_not_allow_below_cost',
							'value'=>'1',
							'checked'=>$this->config->item('do_not_allow_below_cost')));?>
							<label for="do_not_allow_below_cost"><span></span></label>
						</div>
					</div>
					
					<div class="form-group">	
						<?php echo form_label(lang('config_sales_receipt_pdf_size').' :', 'config_sales_receipt_pdf_size',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
						<div class="col-sm-9 col-md-9 col-lg-10">
							<?php echo form_dropdown('config_sales_receipt_pdf_size', array(
								'a4'   => 'A4', 
								'a5'  => 'A5'
							),
							$this->config->item('config_sales_receipt_pdf_size'), 'class="form-control" id="config_sales_receipt_pdf_size"');
							?>
						</div>
					</div>
					
					
				</div>
			</div>
		</div>
		<!-- Suspended Sales/Layaways -->
		<div class="col-md-12">
			<div class="panel panel-piluku">
				<div class="panel-heading">
					<?php echo lang("config_suspended_sales_layaways_info"); ?>
				</div>
				<div class="panel-body">
					<div class="form-group">	
						<?php echo form_label(lang('common_hide_layaways_sales_in_reports').' :', 'hide_layaways_sales_in_reports',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
						<div class="col-sm-9 col-md-9 col-lg-10">
						<?php echo form_checkbox(array(
							'name'=>'hide_layaways_sales_in_reports',
							'id'=>'hide_layaways_sales_in_reports',
							'value'=>'1',
							'checked'=>$this->config->item('hide_layaways_sales_in_reports')));?>
							<label for="hide_layaways_sales_in_reports"><span></span></label>
						</div>
					</div>
					<div class="form-group">	
						<?php echo form_label(lang('config_hide_store_account_payments_in_reports').' :', 'hide_store_account_payments_in_reports',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
						<div class="col-sm-9 col-md-9 col-lg-10">
						<?php echo form_checkbox(array(
							'name'=>'hide_store_account_payments_in_reports',
							'id'=>'hide_store_account_payments_in_reports',
							'value'=>'1',
							'checked'=>$this->config->item('hide_store_account_payments_in_reports')));?>
							<label for="hide_store_account_payments_in_reports"><span></span></label>
						</div>
					</div>
					
					<div class="form-group">	
						<?php echo form_label(lang('config_hide_store_account_payments_from_report_totals').' :', 'hide_store_account_payments_from_report_totals',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
						<div class="col-sm-9 col-md-9 col-lg-10">
						<?php echo form_checkbox(array(
							'name'=>'hide_store_account_payments_from_report_totals',
							'id'=>'hide_store_account_payments_from_report_totals',
							'value'=>'1',
							'checked'=>$this->config->item('hide_store_account_payments_from_report_totals')));?>
							<label for="hide_store_account_payments_from_report_totals"><span></span></label>
						</div>
					</div>
					
					<div class="form-group">	
						<?php echo form_label(lang('config_change_sale_date_when_suspending').' :', 'change_sale_date_when_suspending',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
						<div class="col-sm-9 col-md-9 col-lg-10">
						<?php echo form_checkbox(array(
							'name'=>'change_sale_date_when_suspending',
							'id'=>'change_sale_date_when_suspending',
							'value'=>'1',
							'checked'=>$this->config->item('change_sale_date_when_suspending')));?>
							<label for="change_sale_date_when_suspending"><span></span></label>
						</div>
					</div>
					<div class="form-group">	
						<?php echo form_label(lang('config_change_sale_date_when_completing_suspended_sale').' :', 'change_sale_date_when_completing_suspended_sale',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
						<div class="col-sm-9 col-md-9 col-lg-10">
						<?php echo form_checkbox(array(
							'name'=>'change_sale_date_when_completing_suspended_sale',
							'id'=>'change_sale_date_when_completing_suspended_sale',
							'value'=>'1',
							'checked'=>$this->config->item('change_sale_date_when_completing_suspended_sale')));?>
							<label for="change_sale_date_when_completing_suspended_sale"><span></span></label>
						</div>
					</div>
					<div class="form-group">	
						<?php echo form_label(lang('config_show_receipt_after_suspending_sale').' :', 'show_receipt_after_suspending_sale',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
						<div class="col-sm-9 col-md-9 col-lg-10">
						<?php echo form_checkbox(array(
							'name'=>'show_receipt_after_suspending_sale',
							'id'=>'show_receipt_after_suspending_sale',
							'value'=>'1',
							'checked'=>$this->config->item('show_receipt_after_suspending_sale')));?>
							<label for="show_receipt_after_suspending_sale"><span></span></label>
						</div>
					</div>
				</div>
			</div>
		</div>
		<!-- Application Settings -->
		<div class="col-md-12">
			<div class="panel panel-piluku">
				<div class="panel-heading">
					<?php echo lang("config_application_settings_info"); ?>
				</div>	
				<div class="panel-body">
					<?php if ($this->Employee->has_module_action_permission('items', 'manage_categories', $this->Employee->get_logged_in_employee_info()->person_id)) {?>
						<div class="form-group">	
							<div class="col-sm-9 col-md-9 col-lg-10">
								<?php echo anchor("items/categories",lang('items_manage_categories'),array('target' => '_blank', 'title'=>lang('items_manage_categories')));?>
							</div>
						</div>
					<?php } ?>		
					
					<?php if ($this->Employee->has_module_action_permission('items', 'manage_tags', $this->Employee->get_logged_in_employee_info()->person_id)) {?>
						<div class="form-group">	
							<div class="col-sm-9 col-md-9 col-lg-10">
								<?php echo anchor("items/manage_tags",lang('items_manage_tags'),array('target' => '_blank', 'title'=>lang('items_manage_tags')));?>
							</div>
						</div>
					<?php } ?>		
					
					
					<?php if (!is_on_demo_host()) { ?>
						<div class="form-group">	
						<?php echo form_label(lang('config_test_mode').' ('.lang('config_test_mode_help').'):', 'test_mode',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
							<div class="col-sm-9 col-md-9 col-lg-10">
							<?php echo form_checkbox(array(
								'name'=>'test_mode',
								'id'=>'test_mode',
								'value'=>'test_mode',
								'checked'=>$this->config->item('test_mode')));?>
								<label for="test_mode"><span></span></label>
							</div>
						</div>
					<?php } ?>
					<div class="form-group">	
					<?php echo form_label(lang('config_fast_user_switching').' :', 'fast_user_switching',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
						<div class="col-sm-9 col-md-9 col-lg-10">
						<?php echo form_checkbox(array(
							'name'=>'fast_user_switching',
							'id'=>'fast_user_switching',
							'value'=>'fast_user_switching',
							'checked'=>$this->config->item('fast_user_switching')));?>
							<label for="fast_user_switching"><span></span></label>
						</div>
					</div>
					

					
					<div class="form-group">	
					<?php echo form_label(lang('config_require_employee_login_before_each_sale').' :', 'require_employee_login_before_each_sale',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
						<div class="col-sm-9 col-md-9 col-lg-10">
						<?php echo form_checkbox(array(
							'name'=>'require_employee_login_before_each_sale',
							'id'=>'require_employee_login_before_each_sale',
							'value'=>'require_employee_login_before_each_sale',
							'checked'=>$this->config->item('require_employee_login_before_each_sale')));?>
							<label for="require_employee_login_before_each_sale"><span></span></label>
						</div>
					</div>
					
					<div class="form-group">	
					<?php echo form_label(lang('config_keep_same_location_after_switching_employee').' :', 'keep_same_location_after_switching_employee',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
						<div class="col-sm-9 col-md-9 col-lg-10">
						<?php echo form_checkbox(array(
							'name'=>'keep_same_location_after_switching_employee',
							'id'=>'keep_same_location_after_switching_employee',
							'value'=>'keep_same_location_after_switching_employee',
							'checked'=>$this->config->item('keep_same_location_after_switching_employee')));?>
							<label for="keep_same_location_after_switching_employee"><span></span></label>
						</div>
					</div>

					<?php if(is_on_demo_host()) { ?>
						<div class="form-group">	
							<div class="col-sm-9 col-md-9 col-lg-10">
							<span class="text-danger"><?php echo lang('config_cannot_change_language'); ?></span>
							</div>
						</div>
					<?php } ?>
					<div class="form-group">	
					<?php echo form_label(lang('common_language').' :', 'language',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label  required')); ?>
						<div class="col-sm-9 col-md-9 col-lg-10">
						<?php echo form_dropdown('language', array(
							'vietnam'    => 'Việt Nam',
							'english'  => 'English',
//							'indonesia'    => 'Indonesia',
//							'spanish'   => 'Español', 
//							'french'    => 'Fançais',
//							'italian'    => 'Italiano',
//							'german'    => 'Deutsch',
//							'dutch'    => 'Nederlands',
//							'portugues'    => 'Portugues',
//              			    'arabic' => 'العَرَبِيةُ‎‎',
//							'khmer' => 'Khmer',
							),
							$this->Appconfig->get_raw_language_value(), 'class="form-control" id="language"');
							?>
						</div>						
					</div>
					
						<div class="form-group">	
						<?php echo form_label(lang('config_date_format').' :', 'date_format',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label  required')); ?>
							<div class="col-sm-9 col-md-9 col-lg-10">
							<?php echo form_dropdown('date_format', array(
								'middle_endian'    => '12/30/2000',
								'little_endian'  => '30-12-2000',
								'big_endian'   => '2000-12-30'), $this->config->item('date_format'), 'class="form-control" id="date_format"');
								?>
							</div>
						</div>

						<div class="form-group">	
						<?php echo form_label(lang('config_time_format').' :', 'time_format',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label  required')); ?>
							<div class="col-sm-9 col-md-9 col-lg-10">
							<?php echo form_dropdown('time_format', array(
								'12_hour'    => '1:00 PM',
								'24_hour'  => '13:00'
								), $this->config->item('time_format'), 'class="form-control" id="time_format"');
								?>
							</div>
						</div>
						
						<div class="form-group">	
							<?php echo form_label(lang('config_id_to_show_on_barcode').' :', 'id_to_show_on_barcode',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
							<div class="col-sm-9 col-md-9 col-lg-10">
							<?php echo form_dropdown('id_to_show_on_barcode', array(
								'id'   => lang('common_item_id'),
								'number'  => lang('common_item_number_expanded'),
								'product_id'    => lang('common_product_id'),
								),
								$this->config->item('id_to_show_on_barcode'), 'class="form-control" id="id_to_show_on_barcode"')
								?>
							</div>
						</div>
						
						<div class="form-group">	
							<?php echo form_label(lang('config_hide_price_on_barcodes').' :', 'hide_price_on_barcodes',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
							<div class="col-sm-9 col-md-9 col-lg-10">
							<?php echo form_checkbox(array(
								'name'=>'hide_price_on_barcodes',
								'id'=>'hide_price_on_barcodes',
								'value'=>'hide_price_on_barcodes',
								'checked'=>$this->config->item('hide_price_on_barcodes')));?>
								<label for="hide_price_on_barcodes"><span></span></label>
							</div>
						</div>
						
															
						<div class="form-group">	
						<?php echo form_label(lang('config_customers_store_accounts').' :', 'customers_store_accounts',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
							<div class="col-sm-9 col-md-9 col-lg-10">
							<?php echo form_checkbox(array(
								'name'=>'customers_store_accounts',
								'id'=>'customers_store_accounts',
								'value'=>'customers_store_accounts',
								'checked'=>$this->config->item('customers_store_accounts')));?>
								<label for="customers_store_accounts"><span></span></label>
							</div>
						</div>
						
						
						<div class="form-group">	
						<?php echo form_label(lang('config_enable_customer_loyalty_system').' :', 'enable_customer_loyalty_system',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
							<div class="col-sm-9 col-md-9 col-lg-10">
							<?php echo form_checkbox(array(
								'name'=>'enable_customer_loyalty_system',
								'id'=>'enable_customer_loyalty_system',
								'value'=>'enable_customer_loyalty_system',
								'checked'=>$this->config->item('enable_customer_loyalty_system')));?>
								<label for="enable_customer_loyalty_system"><span></span></label>
							</div>
						</div>
						
						<div id="loyalty_setup">
							
							<div class="form-group" id="loyalty_option_wrapper">	
								<?php echo form_label(lang('config_loyalty_option').' :', 'loyalty_option',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label')); ?>
								<div class="col-sm-9 col-md-9 col-lg-10">
								<?php echo form_dropdown('loyalty_option', 
								 array(
									'simple'=> lang('config_simple'),
									'advanced'=>lang('config_advanced'),
								), $this->config->item('loyalty_option') ? $this->config->item('loyalty_option') : '20', 'class="form-control" id="loyalty_option"');
									?>
								</div>
							</div>		
							
							
							<div id="loyalty_setup_simple" style="display: none;">
									<div class="form-group">	
									<?php echo form_label(lang('config_number_of_sales_for_discount').' :', 'number_of_sales_for_discount',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
										<div class="col-sm-9 col-md-9 col-lg-10">
									<?php echo form_input(array(
										'class'=>'validate form-control form-inps',
										'name'=>'number_of_sales_for_discount',
										'id'=>'number_of_sales_for_discount',
										'value'=>$this->config->item('number_of_sales_for_discount')));?>
										</div>
									</div>
									
									<div class="form-group">	
									<?php echo form_label(lang('config_discount_percent_earned').' :', 'discount_percent_earned',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
										<div class="col-sm-9 col-md-9 col-lg-10">
									<?php echo form_input(array(
										'class'=>'validate form-control form-inps',
										'name'=>'discount_percent_earned',
										'id'=>'discount_percent_earned',
										'value'=>$this->config->item('discount_percent_earned')));?>
										</div>
									</div>
									
									<div class="form-group">	
									<?php echo form_label(lang('hide_sales_to_discount_on_receipt').' :', 'hide_sales_to_discount_on_receipt',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
										<div class="col-sm-9 col-md-9 col-lg-10">
										<?php echo form_checkbox(array(
											'name'=>'hide_sales_to_discount_on_receipt',
											'id'=>'hide_sales_to_discount_on_receipt',
											'value'=>'hide_sales_to_discount_on_receipt',
											'checked'=>$this->config->item('hide_sales_to_discount_on_receipt')));?>
											<label for="hide_sales_to_discount_on_receipt"><span></span></label>
										</div>
									</div>
									
									
							</div>
					
							
							<div id="loyalty_setup_advanced" style="display :none;">					
								<?php
								$spend_amount_for_points = '';
								$points_to_earn= '';
								if (strpos($this->config->item('spend_to_point_ratio'),':') !== FALSE)
								{
						      	list($spend_amount_for_points, $points_to_earn) = explode(":",$this->config->item('spend_to_point_ratio'),2);
								}
								?>
								<div class="form-group">	
								<?php echo form_label(lang('config_spend_to_point_ratio').' :', 'spend_amount_for_points',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
									<div class="col-sm-9 col-md-9 col-lg-10">
							
								<?php echo form_input(array(
									'class'=>'validate form-control form-inps',
									'name'=>'spend_amount_for_points',
									'id'=>'spend_amount_for_points',
									'placeholder' => lang('config_loyalty_explained_spend_amount'),
									'value'=>$spend_amount_for_points));?>
									<?php echo form_input(array(
										'class'=>'validate form-control form-inps',
										'name'=>'points_to_earn',
										'id'=>'points_to_earn',
										'placeholder' => lang('config_loyalty_explained_points_to_earn'),
										'value'=>$points_to_earn));?>
									</div>
							</div>
							
							<div class="form-group">	
							<?php echo form_label(lang('config_point_value').' :', 'point_value',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
								<div class="col-sm-9 col-md-9 col-lg-10">
							<?php echo form_input(array(
								'class'=>'validate form-control form-inps',
								'name'=>'point_value',
								'id'=>'point_value',
								'value'=>$this->config->item('point_value') ? to_currency_no_money($this->config->item('point_value')) : ''));?>

								</div>
							</div>
							
							<div class="form-group">	
							<?php echo form_label(lang('config_hide_points_on_receipt').' :', 'hide_points_on_receipt',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
								<div class="col-sm-9 col-md-9 col-lg-10">
								<?php echo form_checkbox(array(
									'name'=>'hide_points_on_receipt',
									'id'=>'hide_points_on_receipt',
									'value'=>'hide_points_on_receipt',
									'checked'=>$this->config->item('hide_points_on_receipt')));?>
									<label for="hide_points_on_receipt"><span></span></label>
								</div>
							</div>
							
						</div>													
						</div>
						
						
						<div class="form-group">	
						<?php echo form_label(lang('config_do_not_allow_out_of_stock_items_to_be_sold').' :', 'do_not_allow_out_of_stock_items_to_be_sold',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
							<div class="col-sm-9 col-md-9 col-lg-10">
							<?php echo form_checkbox(array(
								'name'=>'do_not_allow_out_of_stock_items_to_be_sold',
								'id'=>'do_not_allow_out_of_stock_items_to_be_sold',
								'value'=>'do_not_allow_out_of_stock_items_to_be_sold',
								'checked'=>$this->config->item('do_not_allow_out_of_stock_items_to_be_sold')));?>
								<label for="do_not_allow_out_of_stock_items_to_be_sold"><span></span></label>
							</div>
						</div>
						
						<div class="form-group">	
						<?php echo form_label(lang('config_highlight_low_inventory_items_in_items_module').' :', 'highlight_low_inventory_items_in_items_module',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
							<div class="col-sm-9 col-md-9 col-lg-10">
							<?php echo form_checkbox(array(
								'name'=>'highlight_low_inventory_items_in_items_module',
								'id'=>'highlight_low_inventory_items_in_items_module',
								'value'=>'highlight_low_inventory_items_in_items_module',
								'checked'=>$this->config->item('highlight_low_inventory_items_in_items_module')));?>
								<label for="highlight_low_inventory_items_in_items_module"><span></span></label>
							</div>
						</div>
						
						
						<div class="form-group">	
						<?php echo form_label(lang('config_enable_timeclock').' :', 'timeclock',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
							<div class="col-sm-9 col-md-9 col-lg-10">
							<?php echo form_checkbox(array(
								'name'=>'timeclock',
								'id'=>'timeclock',
								'value'=>'timeclock',
								'checked'=>$this->config->item('timeclock')));?>
								<label for="timeclock"><span></span></label>
							</div>
						</div>
						
						<div class="form-group">	
						<?php echo form_label(lang('config_enable_sounds').' :', 'enable_sounds',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
							<div class="col-sm-9 col-md-9 col-lg-10">
							<?php echo form_checkbox(array(
								'name'=>'enable_sounds',
								'id'=>'enable_sounds',
								'value'=>'enable_sounds',
								'checked'=>$this->config->item('enable_sounds')));?>
								<label for="enable_sounds"><span></span></label>
							</div>
						</div>
					
						<div class="form-group">	
						<?php echo form_label(lang('config_edit_item_price_if_zero_after_adding').' :', 'edit_item_price_if_zero_after_adding',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
							<div class="col-sm-9 col-md-9 col-lg-10">
							<?php echo form_checkbox(array(
								'name'=>'edit_item_price_if_zero_after_adding',
								'id'=>'edit_item_price_if_zero_after_adding',
								'value'=>'edit_item_price_if_zero_after_adding',
								'checked'=>$this->config->item('edit_item_price_if_zero_after_adding')));?>
								<label for="edit_item_price_if_zero_after_adding"><span></span></label>
							</div>
						</div>
					
						<div class="form-group">	
							<?php echo form_label(lang('config_number_of_items_per_page').' :', 'number_of_items_per_page',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label  required')); ?>
							<div class="col-sm-9 col-md-9 col-lg-10">
							<?php echo form_dropdown('number_of_items_per_page', 
							 array(
								'20'=>'20',
								'50'=>'50',
								'100'=>'100',
								'200'=>'200',
								'500'=>'500'
								), $this->config->item('number_of_items_per_page') ? $this->config->item('number_of_items_per_page') : '20', 'class="form-control" id="number_of_items_per_page"');
								?>
							</div>
						</div>		
						
						<div class="form-group">	
							<?php echo form_label(lang('config_number_of_items_in_grid').' :', 'number_of_items_in_grid',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label  required')); ?>
							<div class="col-sm-9 col-md-9 col-lg-10">
								<?php 
								$numbers = array();
								foreach(range(1, 50) as $number) 
								{ 
									$numbers[$number] = $number;
									
								}
								?> 
							<?php echo form_dropdown('number_of_items_in_grid', 
								 $numbers, $this->config->item('number_of_items_in_grid') ? $this->config->item('number_of_items_in_grid') : '14', 'class="form-control" id="number_of_items_in_grid"');
								?>
							</div>
						</div>				
								
					<div class="form-group">	
					<?php echo form_label(lang('config_default_new_items_to_service').' :', 'default_new_items_to_service',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
						<div class="col-sm-9 col-md-9 col-lg-10">
						<?php echo form_checkbox(array(
							'name'=>'default_new_items_to_service',
							'id'=>'default_new_items_to_service',
							'value'=>'default_new_items_to_service',
							'checked'=>$this->config->item('default_new_items_to_service')));?>
							<label for="default_new_items_to_service"><span></span></label>
						</div>
					</div>
					
												
						<div class="form-group">	
						<?php echo form_label(lang('config_hide_dashboard_statistics').' :', 'hide_dashboard_statistics',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
							<div class="col-sm-9 col-md-9 col-lg-10">
							<?php echo form_checkbox(array(
								'name'=>'hide_dashboard_statistics',
								'id'=>'hide_dashboard_statistics',
								'value'=>'1',
								'checked'=>$this->config->item('hide_dashboard_statistics')));?>
								<label for="hide_dashboard_statistics"><span></span></label>
							</div>
						</div>

						<div class="form-group">	
						<?php echo form_label(lang('config_show_language_switcher').' :', 'show_language_switcher',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
							<div class="col-sm-9 col-md-9 col-lg-10">
							<?php echo form_checkbox(array(
								'name'=>'show_language_switcher',
								'id'=>'show_language_switcher',
								'value'=>'1',
								'checked'=>$this->config->item('show_language_switcher')));?>
								<label for="show_language_switcher"><span></span></label>
							</div>
						</div>

						<div class="form-group">	
						<?php echo form_label(lang('config_show_clock_on_header').' :', 'show_clock_on_header',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
							<div class="col-sm-9 col-md-9 col-lg-10">
							<?php echo form_checkbox(array(
								'name'=>'show_clock_on_header',
								'id'=>'show_clock_on_header',
								'value'=>'1',
								'checked'=>$this->config->item('show_clock_on_header')));?>
								<label for="show_clock_on_header"><span></span></label>
								    <p class="help-block"><?php echo lang('config_show_clock_on_header_help_text'); ?></p>

							</div>
						</div>

						
						<div class="form-group">	
						<?php echo form_label(lang('config_legacy_detailed_report_export').' :', 'legacy_detailed_report_export',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
							<div class="col-sm-9 col-md-9 col-lg-10">
							<?php echo form_checkbox(array(
								'name'=>'legacy_detailed_report_export',
								'id'=>'legacy_detailed_report_export',
								'value'=>'1',
								'checked'=>$this->config->item('legacy_detailed_report_export')));?>
								<label for="legacy_detailed_report_export"><span></span></label>
							</div>
						</div>
						
						<div class="form-group">	
						<?php echo form_label(lang('config_report_sort_order').' :', 'report_sort_order',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
							<div class="col-sm-9 col-md-9 col-lg-10">
							<?php echo form_dropdown('report_sort_order', array('asc' => lang('config_asc'), 'desc' => lang('config_desc')), $this->config->item('report_sort_order'),'class="form-control" id="report_sort_order"'); ?>
							</div>
						</div>
						
						<div class="form-group">	
						<?php echo form_label(lang('config_speed_up_search_queries').' ('.lang('config_speed_up_note').')'.' :', 'speed_up_search_queries',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
							<div class="col-sm-9 col-md-9 col-lg-10">
							<?php echo form_checkbox(array(
								'name'=>'speed_up_search_queries',
								'id'=>'speed_up_search_queries',
								'value'=>'1',
								'checked'=>$this->config->item('speed_up_search_queries')));?>
								<label for="speed_up_search_queries"><span></span></label>
							</div>
						</div>
						
						<div class="form-group">	
						<?php echo form_label(lang('config_disable_margin_calculator').' :', 'disable_margin_calculator',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
							<div class="col-sm-9 col-md-9 col-lg-10">
							<?php echo form_checkbox(array(
								'name'=>'disable_margin_calculator',
								'id'=>'disable_margin_calculator',
								'value'=>'1',
								'checked'=>$this->config->item('disable_margin_calculator')));?>
								<label for="disable_margin_calculator"><span></span></label>
							</div>
						</div>
						
						<div class="form-group">	
						<?php echo form_label(lang('config_disable_quick_edit').' :', 'disable_quick_edit',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
							<div class="col-sm-9 col-md-9 col-lg-10">
							<?php echo form_checkbox(array(
								'name'=>'disable_quick_edit',
								'id'=>'disable_quick_edit',
								'value'=>'1',
								'checked'=>$this->config->item('disable_quick_edit')));?>
								<label for="disable_quick_edit"><span></span></label>
							</div>
						</div>
						
						<div class="form-group">	
						<?php echo form_label(lang('config_legacy_search_method').' :', 'legacy_search_method',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
								<div class="col-sm-9 col-md-9 col-lg-10">
								<?php 
								$legacy_search_options = array(
									'name'=>'legacy_search_method',
									'id'=>'legacy_search_method',
									'value'=>'1',
									'checked'=>$this->config->item('legacy_search_method') || !$this->config->item('supports_full_text'));
								
									if (!$this->config->item('supports_full_text'))
									{
										$legacy_search_options['disabled'] = 'disabled';
									}
								
									echo form_checkbox($legacy_search_options);
								
									?>
									<label for="legacy_search_method"><span></span></label>
								</div>
						</div>
						
							
						<div class="form-group">	
						<?php echo form_label(lang('common_return_policy').' :', 'return_policy',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label required')); ?>
							<div class="col-sm-9 col-md-9 col-lg-10">
							<?php echo form_textarea(array(
								'name'=>'return_policy',
								'id'=>'return_policy',
								'class'=>'form-control text-area',
								'rows'=>'4',
								'cols'=>'30',
								'value'=>$this->config->item('return_policy')));?>
							</div>
						</div>

						<div class="form-group">	
						<?php echo form_label(lang('common_announcement_special').' :', 'announcement_special',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label')); ?>
							<div class="col-sm-9 col-md-9 col-lg-10">
							<?php echo form_textarea(array(
								'name'=>'announcement_special',
								'id'=>'announcement_special',
								'class'=>'form-control text-area',
								'rows'=>'4',
								'cols'=>'30',
								'value'=>$this->config->item('announcement_special')));?>
							</div>
						</div>
						
						<div class="form-group">	
						<?php echo form_label(lang('config_spreadsheet_format').' :', 'spreadsheet_format',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
							<div class="col-sm-9 col-md-9 col-lg-10">
							<?php echo form_dropdown('spreadsheet_format', array('CSV' => lang('config_csv'), 'XLSX' => lang('config_xlsx')), $this->config->item('spreadsheet_format'),'class="form-control" id="spreadsheet_format"'); ?>
							</div>
						</div>
						
						<div class="form-group">	
						<?php echo form_label(lang('config_mailing_labels_type').' :', 'mailing_labels_type',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
							<div class="col-sm-9 col-md-9 col-lg-10">
							<?php echo form_dropdown('mailing_labels_type', array('pdf' => 'PDF', 'excel' => 'Excel'), $this->config->item('mailing_labels_type'),'class="form-control" id="mailing_labels_type"'); ?>
							</div>
						</div>
						
						
						<div class="form-group">	
						<?php echo form_label(lang('config_phppos_session_expiration').' :', 'phppos_session_expiration',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
							<div class="col-sm-9 col-md-9 col-lg-10">
							<?php echo form_dropdown('phppos_session_expiration',$phppos_session_expirations, $this->config->item('phppos_session_expiration')!==NULL ? $this->config->item('phppos_session_expiration') : 0,'class="form-control" id="phppos_session_expiration"'); ?>
							</div>
						</div>
						
						
						
						<div class="form-group no-padding-right">	
						<?php echo form_label(lang('config_price_tiers').' :', '',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
							<div class="col-md-9 col-sm-9 col-lg-10">
								<div class="table-responsive">
									<table id="price_tiers" class="table">
										<thead>
											<tr>
											<th><?php echo lang('config_sort'); ?></th>
											<th><?php echo lang('common_tier_name'); ?></th>
											<th><?php echo lang('common_delete'); ?></th>
											</tr>
										</thead>
										
										<tbody>
										<?php foreach($tiers->result() as $tier) { ?>
											<tr><td><span class="ui-icon ui-icon-arrowthick-2-n-s"></span></td><td><input type="text" data-index="<?php echo $tier->id; ?>" class="tiers_to_edit form-control" name="tiers_to_edit[<?php echo $tier->id; ?>]" value="<?php echo H($tier->name); ?>" /></td><td>
											<?php if ($this->Employee->has_module_action_permission('items', 'delete', $this->Employee->get_logged_in_employee_info()->person_id) || $this->Employee->has_module_action_permission('item_kits', 'delete', $this->Employee->get_logged_in_employee_info()->person_id)) {?>				
											<a class="delete_tier" href="javascript:void(0);" data-tier-id='<?php echo $tier->id; ?>'><?php echo lang('common_delete'); ?></a>
											<?php }else { ?>
												&nbsp;
											<?php } ?>
											</td></tr>
										<?php } ?>
										</tbody>
									</table>
									
									<a href="javascript:void(0);" id="add_tier"><?php echo lang('config_add_tier'); ?></a>
									</div>
								</div>
							</div>
						<div class="form-actions">
						<?php echo form_submit(array(
							'name'=>'submitf',
							'id'=>'submitf',
							'value'=>lang('common_submit'),
							'class'=>'submit_button floating-button btn btn-primary btn-lg pull-right')); ?>
						</div>				
				</div>
			</div>
		</div>
                
		<div class="col-md-12">
			<div class="panel panel-piluku">
				<div class="panel-heading">
					<?php echo lang("config_sms_email_title"); ?>
				</div>
				<div class="panel-body">
					<div class="form-group">	
						<?php echo form_label(lang('config_brand_name').' :', 'sms_brand_name',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
						<div class="col-sm-9 col-md-9 col-lg-10">
						<input type="text" data-index="<?php echo $tier->id; ?>" class="form-control" name="sms_brand_name" value="<?php echo H($this->config->item('config_sms_brand_name')); ?>" />
						</div>
					</div>
                    <div class="form-group">	
						<?php echo form_label(lang('config_sms_user').' :', 'sms_user',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
						<div class="col-sm-9 col-md-9 col-lg-10">
							<input type="text" data-index="<?php echo $tier->id; ?>" class="form-control" name="sms_user" value="<?php echo H($this->config->item('config_sms_user')); ?>" />
						</div>
					</div>
					<div class="form-group">	
						<?php echo form_label(lang('config_sms_pass').' :', 'sms_pass',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
						<div class="col-sm-9 col-md-9 col-lg-10">
							<input type="password" data-index="<?php echo $tier->id; ?>" class="form-control" name="sms_pass" value="<?php echo H($this->config->item('config_sms_pass')); ?>" />
						</div>
					</div>
					<div class="form-group">	
						<?php echo form_label(lang('config_email_account').' :', 'email_account',array('class'=>'required col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
						<div class="col-sm-9 col-md-9 col-lg-10">
							<?php echo form_input(array(
							'class'=>'valid form-control form-inps',
							'type' => 'text',
							'name'=>'email_account',
							'id'=>'email_account',
							'value'=>$this->config->item('config_email_account')));?>
						</div>
					</div>
					<div class="form-group">	
						<?php echo form_label(lang('config_email_pass').' :', 'email_pass',array('class'=>'required col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
						<div class="col-sm-9 col-md-9 col-lg-10">
							<?php echo form_input(array(
							'class'=>'valid form-control form-inps',
							'type' => 'password',
							'name'=>'email_pass',
							'id'=>'email_pass',
							'value'=>$this->config->item('config_email_pass')));?>
						</div>
					</div>
					<div class="form-group">	
						<?php echo form_label(lang('config_email_pass_again').' :', 'email_pass_again',array('class'=>'required col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
						<div class="col-sm-9 col-md-9 col-lg-10">
							<?php echo form_input(array(
							'class'=>'valid form-control form-inps',
							'type' => 'password',
							'name'=>'email_pass_again',
							'id'=>'email_pass_again',
							'value'=>$this->config->item('config_email_pass')));?>
						</div>
					</div>
				</div>
			</div>
		</div>
		<?php echo form_close(); ?>	
	</div>
</div>
</div>
<script type='text/javascript'>
//validation and submit handling
$(document).ready(function()
{	
	$(".delete_tier").click(function()
	{
		$("#config_form").append('<input type="hidden" name="tiers_to_delete[]" value="'+$(this).data('tier-id')+'" />');
		$(this).parent().parent().remove();
	});
	
	$("#price_tiers tbody").sortable();
	
	var add_index = -1;
	$("#add_tier").click(function()
	{
		$("#price_tiers tbody").append('<tr><td><span class="ui-icon ui-icon-arrowthick-2-n-s"></span></td><td><input type="text" class="tiers_to_edit form-control" data-index="'+add_index+'" name="tiers_to_edit['+add_index+']" value="" /></td><td>&nbsp;</td></tr>');
		
		add_index--;
	});
	
	$('#additional_payment_types').selectize({
	    delimiter: ',',
	    persist: false,
	    create: function(input) {
	        return {
	            value: input,
	            text: input
	        }
	    }
	});
	
	$(".delete_currency_denom").click(function()
	{
		$(this).parent().parent().remove();
	});
	
	$("#add_denom").click(function()
	{
		$("#currency_denoms tbody").append('<tr><td><input type="text" class="form-control" name="currency_denoms_name[]" value="" /></td><td><input type="text" class="form-control" name="currency_denoms_value[]" value="" /></td><td>&nbsp;</td></tr>');
	});
		
	
	$(".dbOptimize").click(function(event)
	{
		event.preventDefault();
		$('#ajax-loader').removeClass('hidden');
		
		$.getJSON($(this).attr('href'), function(response) 
		{
			$('#ajax-loader').addClass('hidden');
			bootbox.alert(response.message);
		});
		
	});
	
	$(".checkForUpdate").click(function(event)
	{
		event.preventDefault();
		$('#ajax-loader').removeClass('hidden');
		
		$.getJSON($(this).attr('href'), function(update_available) 
		{
			$('#ajax-loader').addClass('hidden');
			if(update_available)
			{
				bootbox.alert(<?php echo json_encode(lang('common_update_available'));?>);
				window.location="http://4biz.vn/downloads.php";
			}
			else
			{
				bootbox.alert(<?php echo json_encode(lang('common_not_update_available')); ?>);
			}
		});
		
	});
	
	var submitting = false;
	$('#config_form').validate({
		submitHandler:function(form)
		{
			if (submitting) return;
			submitting = true;
			$(form).ajaxSubmit({
			success:function(response)
			{
				//Don't let the tiers be double submitted, so we change the name
				$('.tiers_to_edit').filter(function() {
				    return parseInt($(this).data('index')) < 0;
				}).attr('name','tiers_added[]');
				
				if(response.success)
				{
					show_feedback('success',response.message,<?php echo json_encode(lang('common_success')); ?>);
				}
				else
				{
					show_feedback('error',response.message,<?php echo json_encode(lang('common_error')); ?>);
					
				}
				submitting = false;
			},
			dataType:'json'
		});

		},
		errorClass: "text-danger",
		errorElement: "span",
		highlight:function(element, errorClass, validClass) {
			$(element).parents('.form-group').removeClass('has-success').addClass('has-error');
		},
		unhighlight: function(element, errorClass, validClass) {
			$(element).parents('.form-group').removeClass('has-error').addClass('has-success');
		},
		rules: 
		{
    		company: "required",
    		sale_prefix: "required",
    		email_account: "email",
    		email_pass: "required",
    		email_pass_again: {
                equalTo: "#email_pass"
            },
			return_policy:
			{
				required: true
			}
   	},
		messages: 
		{
     		company: <?php echo json_encode(lang('config_company_required')); ?>,
     		sale_prefix: <?php echo json_encode(lang('config_sale_prefix_required')); ?>,
     		email_account: <?php echo json_encode(lang('config_email_account_required')); ?>,
     		email_pass: <?php echo json_encode(lang('config_email_pass_required')); ?>,
     		email_pass_again: 
         	{
             	'equalTo': <?php echo json_encode(lang('config_email_pass_err_dupplicate')); ?>,
			},
			return_policy:
			{
				required:<?php echo json_encode(lang('config_return_policy_required')); ?>
			},
	
		}
	});
	
});

$("#calculate_average_cost_price_from_receivings").change(check_calculate_average_cost_price_from_receivings).ready(check_calculate_average_cost_price_from_receivings);

function check_calculate_average_cost_price_from_receivings()
{
	if($("#calculate_average_cost_price_from_receivings").prop('checked'))
	{
		$("#average_cost_price_from_receivings_methods").show();
	}
	else
	{
		$("#average_cost_price_from_receivings_methods").hide();
	}
}

$("#enable_customer_loyalty_system,#loyalty_option").change(check_loyalty_setup).ready(check_loyalty_setup);

function check_loyalty_setup()
{
	if($("#enable_customer_loyalty_system").prop('checked'))
	{
		$("#loyalty_setup").show();
	}
	else
	{
		$("#loyalty_setup").hide();
	}
	
	if ($("#loyalty_option").val() == 'simple')
	{
		$("#loyalty_setup_simple").show();
		$("#loyalty_setup_advanced").hide();
	}
	else
	{
		$("#loyalty_setup_simple").hide();	
		$("#loyalty_setup_advanced").show();	
	}
}

$(".config-panel").sieve({ itemSelector: "div.form-group", searchInput: $('#search')});

$("#search").focus();

<?php
$deleted_payment_types = $this->config->item('deleted_payment_types');
$deleted_payment_types = explode(',',$deleted_payment_types);

foreach($deleted_payment_types as $deleted_payment_type)
{
?>
	$( ".payment_types" ).each(function() {
		if ($(this).text() == <?php echo json_encode($deleted_payment_type); ?>)
		{
			$(this).removeClass('btn-primary');			
			$(this).addClass('deleted btn-danger');			
		}
	});
<?php
}
?>
save_deleted_payments();

$(".payment_types").click(function(e)
{
	e.preventDefault();
	$(this).toggleClass('btn-primary');
	$(this).toggleClass('deleted btn-danger');
	save_deleted_payments();
});

function save_deleted_payments()
{
	$(".deleted_payment_types").remove();
	
	var deleted_payment_types = [];
	$( ".payment_types.deleted" ).each(function() {
		deleted_payment_types.push($(this).text());
	});
	$("#config_form").append('<input class="deleted_payment_types" type="hidden" name="deleted_payment_types" value="'+deleted_payment_types.join()+'" />');
	
}

</script>
<?php $this->load->view("partial/footer"); ?>
