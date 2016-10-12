<?php $this->load->view("partial/header"); ?>
<script type="text/javascript">
$(document).ready(function()
{
    var table_columns = ["",'giftcard_number',"value",'last_name','inactive', ''];

	 enable_sorting("<?php echo site_url("$controller_name/sorting"); ?>",table_columns, <?php echo $per_page; ?>, <?php echo json_encode($order_col);?>, <?php echo json_encode($order_dir); ?>);
	 enable_select_all();
    enable_checkboxes();
    enable_row_selection();
    enable_search('<?php echo site_url("$controller_name");?>',<?php echo json_encode(lang("common_confirm_search"));?>);
    enable_delete(<?php echo json_encode(lang($controller_name."_confirm_delete"));?>,<?php echo json_encode(lang($controller_name."_none_selected"));?>);

	$('#generate_barcodes').click(function()
    {
    	var selected = get_selected_values();
    	if (selected.length == 0)
    	{
    		bootbox.alert(<?php echo json_encode(lang('common_must_select_item_for_barcode')); ?>);
    		return false;
    	}

    	$(this).attr('href','<?php echo site_url("giftcards/generate_barcodes");?>/'+selected.join('~'));
    });

	$('#generate_barcode_labels').click(function()
    {
    	var selected = get_selected_values();
    	if (selected.length == 0)
    	{
    		bootbox.alert(<?php echo json_encode(lang('common_must_select_item_for_barcode')); ?>);
    		return false;
    	}

    	$(this).attr('href','<?php echo site_url("giftcards/generate_barcode_labels");?>/'+selected.join('~'));
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
				3: { sorter: false}
			}
		});
	}
}

</script>
<div class="manage_buttons">
	<div class="manage-row-options hidden">
		<div class="email_buttons text-center">
			<?php echo
				anchor("$controller_name/generate_barcode_labels",
				'<span class="">'.lang("common_barcode_labels").'</span>',
				array('id'=>'generate_barcode_labels',
					'class' => 'btn btn-primary btn-lg hidden-xs disabled',
					'title'=>lang('common_barcode_labels')));
			?>
			<?php echo
				anchor("$controller_name/generate_barcodes",
				'<span class="">'.lang("common_barcode_sheet").'</span>',
				array('id'=>'generate_barcodes',
					'class' => 'btn btn-primary btn-lg hidden-xs disabled',
					'target' => '_blank',
					'title'=>lang('common_barcode_sheet')));
			?>
			<?php echo
				anchor("$controller_name/delete",
				'<span class="">'.lang("common_delete").'</span>',
				array('id'=>'delete',
					'class'=>'btn btn-red btn-lg disabled'));
			?>

			<a href="#" class="btn btn-lg btn-clear-selection btn-warning"><?php echo lang('common_clear_selection'); ?></a>
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
			</form>

		</div>
		<div class="col-md-8">
			<div class="buttons-list">
				<div class="pull-right-btn">
					<?php echo
						anchor("$controller_name/view/-1/",
						'<span class="">'.lang($controller_name.'_new').'</span>',
						array('class'=>'btn btn-primary btn-lg new',
							'title'=>lang($controller_name.'_new')));
					?>

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

						<li>
							<?php echo anchor("$controller_name/excel_export",
							'<span class="">'.lang("common_excel_export").'</span>',
								array('class'=>'hidden-xs'));
							?>
						</li>
						<li>
							<?php echo anchor("http://giftcards.4biz.vn",
							'<span class="">'.lang("giftcards_buy").'</span>',
								array('class'=>'hidden-xs', 'target'=>'_blank'));
							?>
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
						<div class="pagination  pagination-top hidden-print alternate text-center" id="pagination_top" >
							<?php echo $pagination;?>
						</div>
					<?php }  ?>
					</span>
				</h3>
			</div>
			<div class="panel-body nopadding table_holder table-responsive"  >
					<?php echo $manage_table; ?>
			</div>

		</div>
	</div>
</div>
<?php if($pagination) {  ?>
<div class="row pagination hidden-print alternate text-center" id="pagination_bottom" >
	<?php echo $pagination;?>
</div>
<?php } ?>
</div>
<?php $this->load->view("partial/footer"); ?>