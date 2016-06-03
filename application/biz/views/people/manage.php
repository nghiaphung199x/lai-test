<?php $this->load->view("partial/header"); ?>
<script type="text/javascript">
	$(document).ready(function() 
	{ 
		<?php if ($controller_name == 'suppliers') { ?>
			var table_columns = ['','<?php echo $this->db->dbprefix('people'); ?>'+'.person_id', 'company_name','last_name','first_name','email','phone_number'];
			
			<?php } else { ?>
				var table_columns = ['','<?php echo $this->db->dbprefix('people'); ?>'+'.person_id','last_name','email','phone_number'];
			<?php } ?>

				enable_sorting("<?php echo site_url("$controller_name/sorting"); ?>",table_columns, <?php echo $per_page; ?>, <?php echo json_encode($order_col);?>, <?php echo json_encode($order_dir); ?>);
				enable_select_all();
				enable_checkboxes();
				enable_row_selection();
				enable_search('<?php echo site_url("$controller_name");?>',<?php echo json_encode(lang("common_confirm_search"));?>);
				enable_email('<?php echo site_url("$controller_name/mailto")?>');
				enable_delete(<?php echo json_encode(lang($controller_name."_confirm_delete"));?>,<?php echo json_encode(lang($controller_name."_none_selected"));?>);
				enable_cleanup(<?php echo json_encode(lang($controller_name."_confirm_cleanup"));?>);

				<?php if ($this->session->flashdata('manage_success_message')) { ?>
					show_feedback('success', <?php echo json_encode($this->session->flashdata('manage_success_message')); ?>, <?php echo json_encode(lang('common_success')); ?>);
				<?php } ?>
			
				$('#labels').click(function()
				{
					var selected = get_selected_values();
					if (selected.length == 0)
					{
						bootbox.alert(<?php echo json_encode(lang('common_must_select_customer_for_labels')); ?>);
						return false;
					}

					$(this).attr('href','<?php echo site_url("$controller_name/mailing_labels");?>/'+selected.join('~'));
				});
				$('#sendSMS').click(function(){
					var selected = get_selected_values();
					if (selected.length == 0 || selected.length >1)
					{
						bootbox.alert(<?php echo json_encode(lang('common_must_select_customer_for_sms')); ?>);
						return false;
					}

					$(this).attr('href','<?php echo site_url("$controller_name/send_sms");?>/'+selected['0']);
				});
		}); 
</script>


<div class="manage_buttons">
<div class="manage-row-options hidden">
	<div class="email_buttons text-center">
		<?php if ($controller_name =='customers') { ?>
		<a class="btn btn-primary btn-lg" title="<?php echo (lang('customers_sms_send_sms'));?>" id="sendSMS" href="<?php echo current_url(). '#'; ?>"  data-toggle="modal" data-target="#myModal">
			<span class=""><?php echo (lang('customers_sms_send_sms')); ?></span>
		</a>
		<?php } ?>
		<a class="btn btn-primary btn-lg disabled email email_inactive" title="<?php echo lang("common_email");?>" id="email" href="<?php echo current_url(). '#'; ?>" >
			<span class=""><?php echo lang('common_email'); ?></span>
		</a>
		
		<a class="btn btn-primary btn-lg labels" title="<?php echo lang("common_mailing_labels");?>" id="labels" href="<?php echo current_url(). '#'; ?>" >
			<span class=""><?php echo lang('common_mailing_labels'); ?></span>
		</a>
		

		<?php if ($this->Employee->has_module_action_permission($controller_name, 'delete', $this->Employee->get_logged_in_employee_info()->person_id)) {?>
		<?php echo anchor("$controller_name/delete",
			'<span class="">'.lang('common_delete').'</span>'
			,array('id'=>'delete', 'class'=>'btn btn-red btn-lg disabled delete_inactive ','title'=>lang("common_delete"))); ?>
		<?php } ?>

		<a href="#" class="btn btn-lg btn-clear-selection btn-warning"><?php echo lang('common_clear_selection'); ?></a>
	</div>
</div>
	<div class="row">
		<div class="col-md-5">
			<?php echo form_open("$controller_name/search",array('id'=>'search_form', 'autocomplete'=> 'off', 'class' => 'form-inline')); ?>
				<div class="search no-left-border">
					<input type="text" class="form-control" name ='search' id='search' value="<?php echo H($search); ?>" placeholder="<?php echo lang('common_search'); ?> <?php echo lang('module_'.$controller_name); ?>"/>
				</div>
				
				<?php if(isset($type) && $type == 'customer') { ?>
				<div class="form-group">
					<label><?php echo lang('customers_filter_tier'); ?></label>
					<?php echo form_dropdown('tier_id', $tiers, $selected_tier,'class=""');?>
					<label style="margin-left: 10px;"><?php echo lang('customers_filter_created_by'); ?></label>
					<?php echo form_dropdown('created_by', $employees, $selected_employee,'class=""');?>
				</div>
				<?php } ?>
				<div class="clear-block <?php echo ($search=='') ? 'hidden' : ''  ?>">
					<a class="clear" href="<?php echo site_url($controller_name.'/clear_state'); ?>">
						<i class="ion ion-close-circled"></i>
					</a>	
				</div>

				
			</form>	
			
		</div>
		<div class="col-md-7">	
			<div class="buttons-list">
				<div class="pull-right-btn">
					<?php if ($this->Employee->has_module_action_permission($controller_name, 'add_update', $this->Employee->get_logged_in_employee_info()->person_id)) {?>
					<?php echo anchor("$controller_name/view/-1/",
						'<span class="">'.lang($controller_name.'_new').'</span>',
						array('id' => 'new-person-btn', 'class'=>'btn btn-primary btn-lg', 'title'=>lang($controller_name.'_new')));
					}	
					?>
					<div class="piluku-dropdown">
						
						<button type="button" class="btn btn-more dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
							<i class="ion-android-more-horizontal"></i>
						</button>
						<ul class="dropdown-menu" role="menu">
							<li>
								<?php if ($controller_name =='customers') {  
								?>
								<?php echo anchor("$controller_name/manage_sms/",
									'<span class="">'.lang('customers_sms_menu_link').'</span>',
									array('class'=>'hidden-xs','title'=>lang('customers_sms_menu_link')));
								} ?>
							</li>
							<li>
								<?php if ($controller_name =='customers') {  
								?>
								<?php echo anchor("$controller_name/quotes_contract",
									'<span class="">'.lang('quotes_contract_menu_link').'</span>',
									array('class'=>'hidden-xs','title'=>lang('quotes_contract_menu_link')));
								} ?>
							</li>
							<li>
								<?php if ($controller_name =='customers' || $controller_name == 'suppliers') {  
								?>
								<?php echo anchor("$controller_name/excel_import/",
									'<span class="">'.lang('common_excel_import').'</span>',
									array('class'=>'hidden-xs','title'=>lang('common_excel_import')));
								} ?>
							</li>
							<li>
								<?php
								if ($controller_name == 'customers' || $controller_name == 'employees' || $controller_name == 'suppliers') {	
									echo anchor("$controller_name/excel_export",
										'<span class="">'.lang('common_excel_export').'</span>',
										array('class'=>'hidden-xs import','title'=>lang('common_excel_export')));

								}
								?>
							</li>
							<li>
								<?php if ($controller_name =='customers' or $controller_name =='employees' or $controller_name =='suppliers') {?>
									<?php echo 
									anchor("$controller_name/cleanup",
										'<span class="">'.lang($controller_name."_cleanup_old_customers").'</span>',
										array('id'=>'cleanup', 
											'class'=>'','title'=> lang($controller_name."_cleanup_old_customers"))); 
											?>
								<?php } ?>
							</li>
						</ul>
					</div>
				</div>
			</div>				
		</div>
	</div>
</div>

	<div class="container-fluid">
		<div class="row manage-table">
			<div class="panel panel-piluku">
				<div class="panel-heading">
				<h3 class="panel-title">
					<?php echo lang('common_list_of').' '.lang('module_'.$controller_name); ?>
					<span title="<?php echo $total_rows; ?> total <?php echo $controller_name?>" class="badge bg-primary tip-left"><?php echo $total_rows; ?></span>
					<span class="panel-options custom">
						<?php if($pagination) {  ?>
							<div class="pagination pagination-top hidden-print  text-center" id="pagination_top">
								<?php echo $pagination;?>		
							</div>
						<?php }  ?>
					</span>
				</h3>
			</div>
				<div class="panel-body nopadding table_holder table-responsive" >
					<?php echo $manage_table; ?>			
				</div>	
		</div>	
		<?php if($pagination) {  ?>
		<div class="text-center">
		<div class="pagination hidden-print alternate text-center" id="pagination_bottom" >
			<?php echo $pagination;?>
		</div>
		<?php } ?>
		</div>
	</div>
</div>
<script type="text/javascript">
var CUSTOMER_MANAGE = {
	init: function()
	{
		CUSTOMER_MANAGE.changeEventOnTypeOfCustomer();
		CUSTOMER_MANAGE.changeEventOnCreateBy();
	},
	
	changeEventOnTypeOfCustomer: function()
	{
		$('#search_form [name="tier_id"]').change(function(){
			$('#search_form').submit();
		});
	},
	changeEventOnCreateBy: function()
	{
		$('#search_form [name="created_by"]').change(function(){
			$('#search_form').submit();
		});
	}
}

$( document ).ready(function() {
	CUSTOMER_MANAGE.init();
});

</script>
<?php $this->load->view("partial/footer"); ?>