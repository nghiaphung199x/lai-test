<?php $this->load->view("partial/header"); ?>
<script type="text/javascript">
$(document).ready(function()
{
	
	<?php
	$has_cost_price_permission = $this->Employee->has_module_action_permission('item_kits','see_cost_price', $this->Employee->get_logged_in_employee_info()->person_id);
	if ($has_cost_price_permission)
	{
	?>
		var table_columns = ["","item_kit_id","item_kit_number","name",'','cost_price','unit_price',''];
	<?php	
	}
	else
	{
	?>
		var table_columns = ["","item_kit_id","item_kit_number","name",'','unit_price',''];
	<?php	
	}
	?>
	enable_sorting("<?php echo site_url("$controller_name/sorting"); ?>",table_columns, <?php echo $per_page; ?>, <?php echo json_encode($order_col);?>, <?php echo json_encode($order_dir); ?>);
	enable_select_all();
    enable_checkboxes();
    enable_row_selection();
    enable_search('<?php echo site_url("$controller_name");?>',<?php echo json_encode(lang("common_confirm_search"));?>);
    enable_delete(<?php echo json_encode(lang($controller_name."_confirm_delete"));?>,<?php echo json_encode(lang($controller_name."_none_selected"));?>);
	enable_cleanup(<?php echo json_encode(lang("item_kits_confirm_cleanup"));?>);
    
    $('#generate_barcodes').click(function()
    {
    	var selected = get_selected_values();
    	if (selected.length == 0)
    	{
    		bootbox.alert(<?php echo json_encode(lang('common_must_select_item_for_barcode')); ?>);
    		return false;
    	}

    	$(this).attr('href','<?php echo site_url("item_kits/generate_barcodes");?>/'+selected.join('~'));
    });

    $('#generate_barcode_labels').click(function()
    {
    	var selected = get_selected_values();
    	if (selected.length == 0)
    	{
    		bootbox.alert(<?php echo json_encode(lang('common_must_select_item_for_barcode')); ?>);
    		return false;
    	}

    	$(this).attr('href','<?php echo site_url("item_kits/generate_barcode_labels");?>/'+selected.join('~'));
    });
	 
	 <?php if ($this->session->flashdata('manage_success_message')) { ?>
		show_feedback('success', <?php echo json_encode($this->session->flashdata('manage_success_message')); ?>, <?php echo json_encode(lang('common_success')); ?>);
	 <?php } ?>
});

function init_table_sorting()
{
	//Only init if there is more than one row
	if($('.tablesorter tbody tr').length >1)
	{
		$("#sortable_table").tablesorter(
		{
			sortList: [[1,0]],
			headers:
			{
				0: { sorter: false},
				5: { sorter: false}
			}

		});
	}
}
</script>
<div class="manage_buttons">
	<div class="manage-row-options hidden">
		<div class="email_buttons items text-center">
			<?php echo 
				anchor("$controller_name/generate_barcode_labels",
				'<span class="">'.lang("common_barcode_labels").'</span>',
				array('id'=>'generate_barcode_labels', 
					'class' => 'btn btn-primary btn-lg  disabled ',
					'title'=>lang('common_barcode_labels'))); 
			?>
			<?php echo 
				anchor("$controller_name/generate_barcodes",
				'<span class="">'.lang("common_barcode_sheet").'</span>',
				array('id'=>'generate_barcodes',
				 	'class' => 'btn btn-primary btn-lg  disabled ',
					'title'=>lang('common_barcode_sheet'),
					'target' => '_blank')); 
			?>
			
			<?php if ($this->Employee->has_module_action_permission($controller_name, 'delete', $this->Employee->get_logged_in_employee_info()->person_id)) {?>				
				
					<?php echo 
						anchor("$controller_name/delete",
						'<span class="">'.lang("common_delete").'</span>',
						array('id'=>'delete', 
							'class'=>'btn btn-red btn-lg disabled','title'=>lang("common_delete"))); 
					?>
			<?php } ?>
			<a href="#" class="btn btn-lg btn-warning btn-clear-selection"><?php echo lang('common_clear_selection'); ?></a>
		</div>
	</div>
	<div class="row">
		<div class="col-md-8">
			<?php echo form_open("$controller_name/search",array('id'=>'search_form', 'autocomplete'=> 'off', 'class'=>'')); ?>
				<div class="search search-items no-left-border">
					<ul class="list-inline">
						<li><input type="text" class="form-control" name ='search' id='search' value="<?php echo H($search); ?>" placeholder="<?php echo lang('common_search'); ?> <?php echo lang('module_'.$controller_name); ?>"/></li>
						
						
						<li>
						<?php echo lang('common_fields'); ?>: 
						<?php echo form_dropdown('fields', 
						 array(
							'all'=>lang('common_all'),
							$this->db->dbprefix('item_kits').'.item_kit_id' => lang('item_kits_id'),
							$this->db->dbprefix('item_kits').'.item_kit_number' => lang('common_item_number_expanded'),
							$this->db->dbprefix('item_kits').'.product_id' => lang('common_product_id'),
							$this->db->dbprefix('item_kits').'.name' => lang('item_kits_name'),
							$this->db->dbprefix('item_kits').'.description' => lang('common_description'),
							$this->db->dbprefix('item_kits').'.cost_price' => lang('common_cost_price'),
							$this->db->dbprefix('item_kits').'.unit_price' => lang('common_unit_price'),
							$this->db->dbprefix('tags').'.name' => lang('common_tag'),
							),$fields, 'class="form-control" id="fields"');
							?>
							</li>
						<li>
							<?php echo lang('common_category'); ?>: 	
							<?php echo form_dropdown('category_id', $categories,$category_id, 'class="form-control" id="category_id"'); ?>
						</li>
						
						<li><?php echo form_submit('submitf', lang('common_search'),'class="btn btn-primary btn-lg"'); ?></li>
						<li>
							<div class="clear-block items-clear-block <?php echo ($search=='') ? 'hidden' : ''  ?>">
								<a class="clear" href="<?php echo site_url($controller_name.'/clear_state'); ?>">
									<i class="ion ion-close-circled"></i>
								</a>	
							</div>
						</li>
					</ul>
				</div>
			<?php echo form_close() ?>
		</div>
		<div class="col-md-4">
			<div class="buttons-list items-buttons">
				<div class="pull-right-btn">
					<?php if ($this->Employee->has_module_action_permission($controller_name, 'add_update', $this->Employee->get_logged_in_employee_info()->person_id)) {?>				
						<?php echo 
							anchor("$controller_name/view/-1/",
							'<span class="">'.lang($controller_name.'_new').'</span>',
							array('class'=>'btn btn-primary btn-lg', 
								'title'=>lang($controller_name.'_new')));
						?>
					<?php } ?>

					<div class="piluku-dropdown">
						<button type="button" class="btn btn-more dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
						<i class="ion-android-more-horizontal"></i>
					</button>
					<ul class="dropdown-menu" role="menu">
                        <li>
                            <?php echo anchor("$controller_name/excel_import/",
                            '<span class="">'.lang("common_excel_import").'</span>',
                            array('class'=>' ',
                                'title'=>lang('common_excel_import')));
                            ?>
                        </li>
						<li class="">
							<?php if ($this->Employee->has_module_action_permission('items', 'manage_categories', $this->Employee->get_logged_in_employee_info()->person_id)) {?>
								<?php echo anchor("items/categories",
									'<span class="">'.lang("items_manage_categories").'</span>',
									array('class'=>'',
									'title'=>lang('items_manage_categories')));
								?>
							<?php } ?>		
						</li>
						<li class="">
							<?php if ($this->Employee->has_module_action_permission('items', 'manage_tags', $this->Employee->get_logged_in_employee_info()->person_id)) {?>
								<?php echo anchor("items/manage_tags",
									'<span class="">'.lang("items_manage_tags").'</span>',
									array('class'=>'',
									'title'=>lang('items_manage_tags')));
								?>
							<?php } ?>				
						</li>
						<li>
							<?php echo anchor("$controller_name/excel_export",
							'<span class="">'.lang("common_excel_export").'</span>',
								array('class'=>'import ','title'=>lang('common_excel_export')));
							?>
						</li>
						
						<?php if ($this->Employee->has_module_action_permission($controller_name, 'delete', $this->Employee->get_logged_in_employee_info()->person_id)) {?>
							<li>
								<?php echo 
									anchor("$controller_name/cleanup",
									'<span class="">'.lang("item_kits_cleanup_old_item_kits").'</span>',
									array('id'=>'cleanup', 
									'class'=>'','title'=>lang("item_kits_cleanup_old_item_kits"))); 
								?>
							</li>
						<?php }?>
						
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
			<div class="panel-body nopadding  table_holder table-responsive"  >
						<?php echo $manage_table; ?>			
			</div>
			
		</div>
	</div>
</div>
<?php if($pagination) {  ?>
<div class="text-center">
	<div class="row pagination hidden-print alternate text-center" id="pagination_bottom" >
		<?php echo $pagination;?>
	</div>
</div>
<?php } ?>
</div>
<?php $this->load->view("partial/footer"); ?>
