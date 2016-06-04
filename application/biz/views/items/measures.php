<?php $this->load->view("partial/header"); ?>
<?php echo form_open('items/saveMeasure/',array('id'=>'tag_form','class'=>'form-horizontal')); ?>
		<div class="row">
			<div class="col-md-12">
				<div class="panel panel-piluku">
					<div class="panel-heading"><?php echo lang("items_manage_measures"); ?></div>
					<div class="panel-body">
						<a href="javascript:void(0);" class="add_measure" data-measure_id="0">[<?php echo lang('items_add_tag'); ?>]</a>
							<div id="measure_list" class="tag-tree">
								<?php echo $measure_list; ?>
							</div>
						<a href="javascript:void(0);" class="add_measure" data-measure_id="0">[<?php echo lang('items_add_tag'); ?>]</a>
					</div>
				</div>
			</div>
		</div><!-- /row -->
		<?php  echo form_close(); ?>
	</div>

			
<script type='text/javascript'>

$(document).on('click', ".edit_measure",function()
{
	var measure_id = $(this).data('measure_id');
	bootbox.prompt({
	  title: <?php echo json_encode(lang('items_please_enter_measure_name')); ?>,
	  value: $(this).data('name'),
	  callback: function(measure_name) {
		  
	  	if (measure_name)
	  	{
	  		$.post('<?php echo site_url("items/saveMeasure");?>'+'/'+measure_id, {measure_name : measure_name},function(response) {	
	  			show_feedback(response.success ? 'success' : 'error', response.message,response.success ? <?php echo json_encode(lang('common_success')); ?> : <?php echo json_encode(lang('common_error')); ?>);
	  			if (response.success)
	  			{
	  				$('#measure_list').load("<?php echo site_url("items/measureList"); ?>");
	  			}
	  		}, "json");

	  	}
	  }
	});
});

$(document).on('click', ".add_measure",function()
{
	bootbox.prompt(<?php echo json_encode(lang('items_please_enter_measure_name')); ?>, function(measure_name)
	{
		if (measure_name)
		{
			$.post('<?php echo site_url("items/saveMeasure");?>', {measure_name : measure_name},function(response) {
			
				show_feedback(response.success ? 'success' : 'error', response.message,response.success ? <?php echo json_encode(lang('common_success')); ?> : <?php echo json_encode(lang('common_error')); ?>);

				//Refresh tree if success
				if (response.success)
				{
					$('#measure_list').load("<?php echo site_url("items/measureList"); ?>");
				}
			}, "json");

		}
	});
});

$(document).on('click', ".delete_measure",function()
{
	var measure_id = $(this).data('measure_id');
	if (measure_id)
	{
		bootbox.confirm(<?php echo json_encode(lang('items_tag_delete_confirmation')); ?>, function(result)
		{
			if (result)
			{
				$.post('<?php echo site_url("items/deleteMeasure");?>', {measure_id : measure_id},function(response) {
				
					show_feedback(response.success ? 'success' : 'error', response.message,response.success ? <?php echo json_encode(lang('common_success')); ?> : <?php echo json_encode(lang('common_error')); ?>);

					//Refresh tree if success
					if (response.success)
					{
						$('#measure_list').load("<?php echo site_url("items/measureList"); ?>");
					}
				}, "json");
			}
		});
	}
	
});

</script>
<?php $this->load->view('partial/footer'); ?>
