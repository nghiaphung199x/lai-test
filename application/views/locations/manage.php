<?php $this->load->view("partial/header"); 
$this->load->helper('demo');
?>
<script type="text/javascript">
$(document).ready(function()
{
	var table_columns = ["","location_id","name",'','phone','email',''];
	
	 enable_sorting("<?php echo site_url("$controller_name/sorting"); ?>",table_columns, <?php echo $per_page; ?>, <?php echo json_encode($order_col);?>, <?php echo json_encode($order_dir); ?>);
	 enable_select_all();
    enable_checkboxes();
    enable_row_selection();
    enable_search('<?php echo site_url("$controller_name");?>',<?php echo json_encode(lang("common_confirm_search"));?>);
    enable_delete(<?php echo json_encode(lang($controller_name."_confirm_delete"));?>,<?php echo json_encode(lang($controller_name."_none_selected"));?>);
	 <?php if ($this->session->flashdata('manage_success_message')) { ?>
		show_feedback('success', <?php echo json_encode($this->session->flashdata('manage_success_message')); ?>, <?php echo json_encode(lang('common_success')); ?>);
	 <?php } ?>
});

</script>
<div class="manage_buttons">

<div class="manage-row-options hidden">
	<div class="email_buttons text-center">
		
		<?php if ($this->Employee->has_module_action_permission($controller_name, 'delete', $this->Employee->get_logged_in_employee_info()->person_id)) {?>					
			<?php echo 
				anchor("$controller_name/delete",
				'<span class="">'.lang('common_delete').'</span>',
				array('id'=>'delete', 
					'class'=>'btn btn-red btn-lg tip-bottom disabled','title'=>lang("common_delete"))); 
			?>
		<?php } ?>
	</div>
</div>

	<div class="row">
		<div class="col-md-4">
			<?php echo form_open("$controller_name/search",array('id'=>'search_form', 'autocomplete'=> 'off')); ?>
				<div class="search no-left-border">
					<input type="text" class="form-control" name ='search' id='search' value="<?php echo H($search); ?>" placeholder="<?php echo lang('common_search'); ?> <?php echo lang('module_'.$controller_name); ?>"/>
				</div>
				<div class="clear-block <?php echo ($search=='') ? 'hidden' : ''  ?>">
					<a class="clear" href="<?php echo site_url($controller_name.'/clear_state'); ?>">
						<i class="ion ion-close-circled"></i>
					</a>	
				</div>
			<?php echo form_close() ?>
			
		</div>
		<div class="col-md-8">	
			<div class="buttons-list">
				<div class="pull-right-btn">
					<?php if ($this->Employee->has_module_action_permission($controller_name, 'add_update', $this->Employee->get_logged_in_employee_info()->person_id)) {?>				
								
						<?php echo 
						anchor("$controller_name/view/-1/",
						'<span class="">'.lang($controller_name.'_new').'</span>',
						array('class'=>'btn btn-primary btn-lg', 
							'title'=>lang($controller_name.'_new'),
							'id' => 'new_location_btn'));
						?>
					<?php } ?>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="row add-location-link">
	<div class="col-md-12 text-center">
		<?php if (!is_on_demo_host()) { ?>
			<strong><a href="http://4biz.vn/buy_additional.php" target="_blank"><?php echo lang('locations_adding_location_requires_addtional_license'); ?></a></strong>
		<?php } ?>
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
							<div class="pagination hidden-print alternate text-center fg-toolbar ui-toolbar" id="pagination_top" >
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
	</div>
</div>

<?php if($pagination) {  ?>
<div class="row pagination hidden-print alternate text-center fg-toolbar ui-toolbar" id="pagination_bottom" >
	<?php echo $pagination;?>
</div>
<?php } ?>
</div>
<?php if (!is_on_demo_host()) { ?>
	<script type="text/javascript">
	// $('#new_location_btn').click(function()
	// {
	// 	bootbox.confirm(<?php echo json_encode(lang('locations_confirm_purchase')); ?>, function(result)
	// 	{
	// 		if (!result)
	// 		{
	// 			window.location='http://4biz.vn/buy_additional.php';
	// 		}
	// 		else
	// 		{
	// 			window.location = $("#new_location_btn").attr('href');
	// 		}
	// 	});
		
	// 	return false;
	// })
	</script>	
<?php } ?>		
<?php $this->load->view("partial/footer"); ?>