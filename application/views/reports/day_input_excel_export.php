<?php $this->load->view("partial/header"); ?>
<div class="row">
	<div class="col-md-12">
		<div class="panel panel-piluku">
			<div class="panel-heading">
				<?php echo lang('reports_date'); ?>
			</div>
			<div class="panel-body">
				<?php
				if(isset($error))
				{
					echo "<div class='error_message'>".$error."</div>";
				}
				?>
				<form  class="form-horizontal form-horizontal-mobiles">

					<div id='report_date_range_complex'>
						<div class="form-group">
							<?php echo form_label(lang('reports_day').' :', '',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label')); ?>
							<div class="col-md-3 col-sm-3">
								<div class="input-group input-daterange" id="reportrange">
                                    <span class="input-group-addon bg">
			                           <?php echo lang('reports_from'); ?>
			                       	</span>
                                    <input type="text" class="form-control start_date" name="start_date" id="start_date">
                                </div>
							</div>
						</div>
					</div>

					<div class="form-group">
						<?php echo form_label(lang('reports_export_to_excel').' :', '', array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label  ')); ?> 
						<div class="col-sm-9 col-md-9 col-lg-10">
							<input type="radio" name="export_excel" id="export_excel_yes" value='1' /> <?php echo lang('common_yes'); ?>  &nbsp;&nbsp;
							<label for="export_excel_yes"><span></span></label>
							<input type="radio" name="export_excel" id="export_excel_no" value='0' checked='checked' /> <?php echo lang('common_no'); ?> &nbsp;&nbsp;
							<label for="export_excel_no"><span></span></label>
						</div>
					</div>
					
					<?php $this->load->view('partial/reports/locations_select');?>

					<div class="form-actions pull-right">
						<?php
						echo form_button(array(
							'name'=>'generate_report',
							'id'=>'generate_report',
							'content'=>lang('common_submit'),
							'class'=>'btn btn-primary submit_button btn-large')
						);
						?>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>
</div>


<script type="text/javascript" language="javascript">
	$(document).ready(function()
	{
		$("#generate_report").click(function()
		{
			var sale_type = $("#sale_type").val();
			var export_excel = 0;
			if ($("#export_excel_yes").prop('checked'))
			{
				export_excel = 1;
			}

			var start_date = $("#start_date").val();

			window.location = window.location+'/'+start_date +'/'+ export_excel;
		});

		date_time_picker_field_report($('#start_date'), JS_DATE_FORMAT);

	});
</script>
<?php $this->load->view("partial/footer"); ?>