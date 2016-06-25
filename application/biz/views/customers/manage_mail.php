<?php $this->load->view("partial/header"); ?>
<script type="text/javascript">
$(document).ready(function (){
    var table_columns = ["", "mail_id", 'mail_title','', ''];
    enable_sorting("<?php echo site_url("$controller_name/sorting_mail"); ?>", table_columns, <?php echo $per_page; ?>);
    enable_select_all();
    enable_checkboxes();
    enable_row_selection();
	enable_search('<?php echo site_url("$controller_name/suggest_mail");?>',<?php echo json_encode(lang("common_confirm_search"));?>);
    enable_delete(<?php echo json_encode(lang('customers_mail_delete_msg_confrim')); ?>,<?php echo json_encode(lang($controller_name . "_none_selected")); ?>);
});

</script>

<div class="manage_buttons">
	<div class="manage-row-options hidden">
		<div class="email_buttons text-center">
			<?php if ($this->Employee->has_module_action_permission($controller_name, 'delete', $this->Employee->get_logged_in_employee_info()->person_id)) {?>
			<?php echo anchor("$controller_name/delete_mail",
				'<span class="">'.lang('common_delete').'</span>'
				,array('id'=>'delete', 'class'=>'btn btn-red btn-lg disabled delete_inactive ','title'=>lang("common_delete"))); ?>
			<?php } ?>
	
			<a href="#" class="btn btn-lg btn-clear-selection btn-warning"><?php echo lang('common_clear_selection'); ?></a>
		</div>
	</div>
	<div class="row">
		<div class="col-md-5">
			<?php echo form_open("$controller_name/search_mail",array('id'=>'search_form', 'autocomplete'=> 'off', 'class' => 'form-inline')); ?>
				<div class="search no-left-border">
					<input type="text" class="form-control" name ='search' id='search' value="<?php echo isset($search) ? H($search) : ''; ?>" placeholder="<?php echo lang('common_search'); ?> <?php echo lang('module_'.$controller_name .'_mail'); ?>"/>
				</div>
				<div class="clear-block <?php echo (!isset($search)||$search=='') ? 'hidden' : ''  ?>">
					<a class="clear" href="<?php echo site_url($controller_name.'/clear_state_mail'); ?>">
						<i class="ion ion-close-circled"></i>
					</a>	
				</div>
			</form>	
			
		</div>
		<div class="col-md-7">	
			<div class="buttons-list">
				<div class="pull-right-btn">
					<?php if ($this->Employee->has_module_action_permission($controller_name, 'add_update', $this->Employee->get_logged_in_employee_info()->person_id)) {?>
					<?php echo anchor("$controller_name/view_mail/-1/",
						'<span class="">'.lang('customers_mail_new').'</span>',
						array('id' => 'new-person-btn', 'class'=>'btn btn-primary btn-lg', 'title'=>lang('customers_mail_new')));
					}	
					?>
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
					<?php echo lang('common_list_of').' '.lang('module_'.$controller_name.'_mail'); ?>
					<span title="<?php echo isset($total_rows) ? $total_rows : ''; ?> total <?php echo $controller_name?>" class="badge bg-primary tip-left"><?php echo isset($total_rows) ? $total_rows : ''; ?></span>
					<div class="panel-options custom">
					<?php if($pagination) {  ?>
						<div class="pagination pagination-top hidden-print  text-center" id="pagination_top">
							<?php echo $pagination;?>		
						</div>
					<?php }  ?>
				</div>
				</h3>
			</div>
			<div class="panel-body nopadding table_holder table-responsive" >
				<?php echo $manage_table; ?>			
			</div>		
			
		</div>
	</div>
</div>

</div>