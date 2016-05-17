<?php $this->load->view("partial/header"); ?>
	<?php if ($count_info->status == 'open') { ?>
			
		<ul id="do_count_actions" class="list-inline">
			<li>
				<label><?php echo lang('common_category')?></label>
				<?php echo form_dropdown('category_id', $categories, $selected_category,'class=""');?>
			</li>
			<li style="float: right;">
				<?php echo anchor('items/excel_import_count', lang('common_excel_import'),array('class'=>'btn btn-success btn-lg'));?>
			</li>
		</ul>
	<?php } ?>

<div id="content-header" class="hidden-print">
	<div class="col-lg-12 col-md-12 no-padding-left visible-lg visible-md">
</div>
</div>
<div id="count_container">
	<?php $this->load->view("items/do_count_data"); ?>
</div>
<script type="text/javascript">
var AUDIT_ITEM = {
	init: function()
	{
		AUDIT_ITEM.changeEventOnCategory();
		AUDIT_ITEM.clickEventOnShowNotAuditItems();
	},
	
	changeEventOnCategory: function()
	{
		$('#not_audit').unbind('click').bind('click', function(){
			var _data = {};
			_data['count_id'] = <?php echo $count_id; ?>;
			coreAjax.call(
				'<?php echo site_url("items/showNotAudit");?>',
				_data,
				function(response)
				{
					// console.log(response);
					
					$('#NotAuditedLocationModal').remove();
					$('body').append(response.html);
					$('#NotAuditedLocationModal').modal('show');
				}
			);
		});
	},
	clickEventOnShowNotAuditItems: function()
	{
		$('#do_count_actions [name="category_id"]').change(function(){
			var _data = {};
			_data['category_id'] = $(this).val();
			coreAjax.callWithoutMask(
				'<?php echo site_url("items/setCategory");?>',
				_data,
				function(response)
				{
					// console.log(response);
				}
			);
		});
	}
}

$( document ).ready(function() {
	AUDIT_ITEM.init();
});
</script>
<?php $this->load->view('partial/footer'); ?>