<?php $this->load->view("partial/header"); ?>
<div class="row" id="form">
	<div class="spinner" id="grid-loader" style="display:none">
	  <div class="rect1"></div>
	  <div class="rect2"></div>
	  <div class="rect3"></div>
	</div>
	<div class="col-md-12">
		<?php echo form_open_multipart('customers/do_excel_import/',array('id'=>'item_form','class'=>'form-horizontal')); ?>
		<div class="panel panel-piluku">
			<div class="panel-heading">
				<?php echo lang('customers_mass_import_from_excel'); ?>
			</div>
			<div class="panel-body">
                <h3><?php echo lang('common_step_1'); ?>: </h3>
                <p><?php echo lang('customers_step_1_desc'); ?></p>
                <ul class="list-inline">
                    <li>
                        <a class="btn btn-green btn-sm " href="<?php echo site_url('customers/excel'); ?>"><?php echo lang('customers_new_customers_import'); ?></a>
                    </li>
                    <li>
                        <?php echo lang('common_or');?>
                    </li>
                    <li>
                        <a class="btn btn-green btn-sm " href="<?php echo site_url('customers/excel_export'); ?>"><?php echo lang('customers_update_customers_import'); ?></a>
                    </li>
                </ul>

                <h3><?php echo lang('common_step_2'); ?>: </h3>
                <p><?php echo lang('customers_step_2_desc'); ?></p>
                <div class="col-md-12">
                    <div class="form-group">
                        <div class="text-danger" id="error_message_box"></div>
                        <?php echo form_label(lang('common_file_path').' :', 'file_path',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label')); ?>
                        <input type="file" name="file_path" id="file_path" class="filestyle form-control" data-icon="false" >
                    </div>
                    <div class="form-group text-right">
                        <?php echo form_submit(array(
                        'name'=>'submitf',
                        'id'=>'submitf',
                        'value'=>lang('common_submit'),
                        'class'=>'btn btn-primary btn-lg')
                        ); ?>
                    </div>
                </div>

                <h3><?php echo lang('common_step_3'); ?>: </h3>
                <p><?php echo lang('common_step_3_desc'); ?></p>
                <p><?php echo lang('common_required_select_columns'); ?></p>
                <p><?php echo lang('common_customers_import_note'); ?></p>
                <div class="form-group ajax-result" id="import-result"></div>
			</div>
		</div>
        <?php echo form_close(); ?>
	</div>
</div>
<script type='text/javascript'>
	//validation and submit handling
	$(document).ready(function()
	{
        $(".wrapper").addClass("mini-bar");
        $(".wrapper.mini-bar .left-bar").hover(
            function() {
                $(this).parent().removeClass('mini-bar');
            }, function() {
                $(this).parent().addClass('mini-bar');
            }
        );

		var submitting = false;
		$('#item_form').validate({
			submitHandler:function(form)
			{
				if (submitting) return;
				submitting = true;
				$("#grid-loader").show();
				$(form).ajaxSubmit({
					success:function(response)
					{
						$("#grid-loader").hide();
						if(!response.success)
						{ 
							show_feedback('error',response.message,<?php echo json_encode(lang('common_error')); ?>);
						}
						else
						{
                            $("#import-result").html(response.html);
							//show_feedback('success',response.message,<?php echo json_encode(lang('common_success')); ?>);
						}
						submitting = false;
					},
					dataType:'json',
					resetForm: false
				});
			},
			errorLabelContainer: "#error_message_box",
	 		wrapper: "li",
			highlight:function(element, errorClass, validClass) {
				$(element).parents('.form-group').addClass('error');
			},
			unhighlight: function(element, errorClass, validClass) {
				$(element).parents('.form-group').removeClass('error');
				$(element).parents('.form-group').addClass('success');
			},
			rules: 
			{
				file_path:"required"
	   		},
			messages: 
			{
	   			file_path:<?php echo json_encode(lang('customers_full_path_to_excel_required')); ?>
			}
		});
	});
</script>
<?php $this->load->view("partial/footer"); ?>