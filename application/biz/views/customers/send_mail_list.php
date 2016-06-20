<div class="modal-dialog">
	<div class="modal-content customer-recent-sales">
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-label=<?php echo json_encode(lang('common_close')); ?>><span aria-hidden="true" class="ti-close"></span></button>
			<h4 class="modal-title"> <?php echo lang('customers_mail_send_mail'); ?></h4>
		</div>
		<div class="modal-body ">
			<div class="row" id="form">
				
				<div class="spinner" id="grid-loader" style="display:none">
				  <div class="rect1"></div>
				  <div class="rect2"></div>
				  <div class="rect3"></div>
				</div>
				<div class="col-md-12">
					<?php 
						echo form_open('customers/do_send_mail',array('id'=>'send_mail_form','class'=>'form-horizontal'));
					?>
					<ul id="error_message_box" class="text-danger"></ul>
				
					<div class="form-group">	
						<?php echo form_label(lang('customers_mail_list_mail').' :', 'list_mail',array('class'=>'col-sm-5 col-md-5 col-lg-4 control-label')); ?>
						<div class='form_field'>
					        <select name="mail_id">
					            <option value="">--Chọn mail---</option>
					            <?php
					            foreach ($list_mail->result_array() as $mail){
					            ?>    
					            <option value="<?php echo $mail['id']?>"><?php echo $mail['title'];?></option>
					            <?php
					            }
					            ?>
					        </select>
					   </div>
				 	</div>

					<div class="modal-footer">
						<div class="form-acions">
							<?php
								echo form_hidden('type_send',1);
								echo form_submit(array(
									'name'=>'submit',
									'id'=>'submit',
									'value'=>lang('common_submit'),
									'class'=>'btn btn-primary btn-block float_right btn-lg')
								);

							?>
						</div>
					</div>
					<?php echo form_close(); ?>
				</div>
			</div>
		</div>
	</div>
</div>


<script type='text/javascript'>
//validation and submit handling
$(document).ready(function(){
    setTimeout(function(){$(":input:visible:first","#send_mail_form").focus();},100);
    var submitting = false;
    $('#send_mail_form').validate({
        submitHandler:function(form)
        {
            $(form).ajaxSubmit({
                success:function(response){
                    console.log(response);
                    submitting = false;
                    show_feedback(response.success ? 'success' : 'error',response.message, response.success ? <?php //echo json_encode(lang('common_success')); ?>  : <?php //echo json_encode(lang('common_error')); ?>);      
                }
                dataType:'json',
            });
        },
        errorLabelContainer: "#error_message_box",
        wrapper: "li",
        rules: 
        {			
            mail_id: "required"
        },
        messages: 
        {			
            mail_id: <?php echo json_encode(lang('customers_mail_selected_template')); ?>,
        }
    });    
});
</script>