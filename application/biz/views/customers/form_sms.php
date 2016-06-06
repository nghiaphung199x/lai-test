<?php $this->load->view("partial/header"); ?>

<div class="row" id="form">
	<div class="spinner" id="grid-loader" style="display: none">
		<div class="rect1"></div>
		<div class="rect2"></div>
		<div class="rect3"></div>
	</div>

	<div class="col-md-12">
			<?php echo form_open('customers/save_sms/'.$info_sms->id,array('id'=>'sms_form','class'=>'form-horizontal')); 	?>

			<div class="panel panel-piluku">
			<div class="panel-heading">
				<h3 class="panel-title">
					<i class="ion-edit"></i> 
		                    <?php echo lang("customers_sms_basic_information"); ?>
							<small>(<?php echo lang('common_fields_required_message'); ?>)</small>
					<br />
					<small>(<?php echo lang('customers_sms_note_basic_information'); ?>)</small>
				</h3>
			</div>
			<div class="panel-body">
				<div class="row">
					<div class="col-md-12">

						<div class="form-group">
								<?php 
								echo form_label(lang('customers_sms_title').' :', 'title',array('class'=>'required wide col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
								<div class="col-sm-9 col-md-9 col-lg-10">
									<?php echo form_input(array(
										'class'=>'form-control',
										'name'=>'sms_title',
										'id'=>'sms_title',
										'value'=>$info_sms->title)
									);?>
								</div>
						</div>

						<div class="form-group">
								<?php 
								echo form_label(lang('customers_sms_description').' :', 'message',array('class'=>'required wide col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
								<div class="col-sm-9 col-md-9 col-lg-10">
									<?php echo form_textarea(array(
										'name'=>'sms_message',
										'id'=>'sms_message',
										'class'=>'form-control text-area',
										'value'=>$info_sms->message,
										'rows'=>'5',
										'cols'=>'17',
										'onkeyup' => 'countChar(this)')		
									);?>
								</div>
						</div>

						<div class="form-group">
								<?php 
								echo form_label(lang('customers_sms_num_character').' :', 'title',array('class'=>'wide col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
								<div class="col-sm-9 col-md-9 col-lg-10">
									<?php echo form_input(array(
										'name'=>'sms_num_char',
										'id'=>'sms_num_char',
										'value'=>$info_sms->number_char,
										'style' => 'width: 50px !important; border: none; text-align: center;',
										'readonly' => 'readonly')
									);?>
								</div>
						</div>

						<div class="form-group">
								<?php 
								echo form_label(lang('customers_sms_num_message').' :', 'title',array('class'=>'wide col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
								<div class="col-sm-9 col-md-9 col-lg-10">
									<?php echo form_input(array(
										'name'=>'sms_num_mess',
										'id'=>'sms_num_mess',
										'value'=>$info_sms->number_message,
										'style' => 'width: 50px !important; border: none; text-align: center;',
										'readonly' => 'readonly')
									);?>
								</div>
						</div>

					</div>
				</div>
				
				<?php echo form_hidden('redirect', $redirect); ?>
				<div class="form-actions pull-right">
					<?php
					echo form_button(array(
					    'name' => 'cancel',
					    'id' => 'cancel',
						 'class' => 'submit_button btn btn-danger',
					    'value' => 'true',
					    'content' => lang('common_cancel')
					));
					?>
					
					<?php
					echo form_submit(array(
						'name'=>'submitf',
						'id'=>'submitf',
						'value'=>lang('common_submit'),
						'class'=>' submit_button btn btn-primary')
					);
					?>
				</div>
			</div>
		</div>
			<?php echo form_close();?>
	</div>
	<!-- /row -->
</div>
</div>

<script type='text/javascript'>
//validation and submit handling
$(document).ready(function(){
	$("#cancel").click(cancelCustomerAddingSMS);
    setTimeout(function(){$(":input:visible:first","#sms_form").focus();},100);
    var submitting = false;
    $('#sms_form').validate({
    	submitHandler:function(form){
            if (submitting) return;
            submitting = true;
            $(form).ajaxSubmit({
            	success:function(response){
            		submitting = false;
					show_feedback(response.success ? 'success' : 'error',response.message, response.success ? <?php echo json_encode(lang('common_success')); ?>  : <?php echo json_encode(lang('common_error')); ?>);
					
                    if(response.success){
                    	window.location.href = '<?php echo site_url('customers/manage_sms'); ?>';
                    }
                },
                dataType:'json'
            });
        },
        errorClass: "text-danger",
		errorElement: "span",
			highlight:function(element, errorClass, validClass) {
				$(element).parents('.form-group').removeClass('has-success').addClass('has-error');
			},
			unhighlight: function(element, errorClass, validClass) {
				$(element).parents('.form-group').removeClass('has-error').addClass('has-success');
			},
        wrapper: "li",
        rules:{
            sms_title:{
                required: true,                
            },
            sms_message:{
                required: true,
                maxlength: 460
            },
        },
        messages:{
        	sms_title:{
                required: <?php echo json_encode(lang('customers_sms_title_required'));?>,                
            },
            sms_message:{
                required: <?php echo json_encode(lang('customers_sms_title_required'));?>,
                maxlength: <?php echo json_encode(lang('customers_sms_message_maxlength'));?>
            },
        }
    });
    
});
function countChar(input){
    var len = input.value.length;
    $("#sms_num_char").val(len);
    if(len <= 156){
        $("#sms_num_mess").val(1);
    }else{
        var number_mess = (1 + Math.ceil((len - 156)/152));
        $("#sms_num_mess").val(number_mess);
    }
}

function cancelCustomerAddingSMS()
{
	bootbox.confirm(<?php echo json_encode(lang('customers_sms_are_you_sure_cancel')); ?>, function(response)
	{
		if (response)
		{
			window.location = <?php echo json_encode(site_url('customers/manage_sms')); ?>;
		}
	});
}
</script>

<?php $this->load->view("partial/footer"); ?>