<?php $this->load->view("partial/header"); ?>

<div class="row" id="form">
	<div class="spinner" id="grid-loader" style="display: none">
		<div class="rect1"></div>
		<div class="rect2"></div>
		<div class="rect3"></div>
	</div>
	
	<div class="col-md-12">
		<?php echo form_open('customers/save_mail/' . $mail_info->mail_id, array('id' => 'manage_mail_form','class'=>'form-horizontal')); ?>
		<div class="panel panel-piluku">
			<div class="panel-heading">
				<h3 class="panel-title">
					<i class="ion-edit"></i> 
		                    <?php echo lang('common_list_of').' '.lang('module_customers_mail'); ?>
							<small>(<?php echo lang('common_fields_required_message'); ?>)</small>
				</h3>
			</div>
			<fieldset id="item_basic_info">
				<h3 class="panel-title" style="padding:15px 20px;"><?php echo lang('customers_manage_mail_replate_info');?></h3>
				<table id="table_char">
					<tr>
					  <th>Khách hàng</th>
					  <th>Nhân viên</th>
					  <th>Công ty</th>
					  <th>Hợp đồng</th>
					</tr>
					<tr>
					  <td>
							<ul>
								<li class="li_char">__FIRST_NAME__ = <strong>HỌ</strong></li>                            
								<li class="li_char">__LAST_NAME__ = <strong>TÊN</strong></li>                             
								<li class="li_char">__PHONE_NUMBER__ = <strong>SỐ ĐIỆN THOẠI</strong></li>                           
								<li class="li_char">__EMAIL__ = <strong>EMAIL</strong></li>
								<li class="li_char">__COMPANY_CUSTOMER__ = <strong>TÊN CÔNG TY</strong></li>                          
							</ul>
					  </td>
					  <td>
							<ul>
								<li class="li_char">__FIRST_NAME_EMPLOYEE__ = <strong>HỌ</strong></li>
								<li class="li_char">__LAST_NAME_EMPLOYEE__ = <strong>TÊN</strong></li>                                  
								<li class="li_char">__PHONE_NUMBER_EMPLOYEE__ = <strong>SỐ ĐIỆN THOẠI</strong></li>
								<li class="li_char">__EMAIL_EMPLOYEE__ = <strong>EMAIL</strong></li>
							</ul>
					  </td>
					  <td>
							<ul>
								<li class="li_char">__NAME_COMPANY__ = <strong>TÊN</strong></li>
								<li class="li_char">__ADDRESS_COMPANY__ = <strong>ĐỊA CHỈ</strong></li>
								<li class="li_char">__EMAIL_COMPANY__ = <strong>EMAIL</strong></li>
								<li class="li_char">__FAX_COMPANY__ = <strong>FAX</strong></li>
								<li class="li_char">__WEBSITE_COMPANY__ = <strong>WEBSITE</strong></li>
							</ul>
					  </td>
					  <td>
							<ul>
								<li class="li_char">__NAME_CONTRACT__ = <strong>TÊN HĐ</strong></li>
								<li class="li_char">__NUMBER_CONTRACT__ = <strong>SỐ HĐ</strong></li>
								<li class="li_char">__START_DATE__ = <strong>NGÀY KÝ</strong></li>
								<li class="li_char">__EXPIRATION_DATE__ = <strong>NGÀY HẾT HẠN</strong></li>
							</ul>
					  </td>
					</tr>
				</table>
			</fieldset>
			<div class="panel-body">
				<div class="row">
					<div class="col-md-12">
	
						<div class="form-group">
								<?php 
								echo form_label(lang('customers_manage_mail_title').' :', 'title',array('class'=>'required wide col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
								<div class="col-sm-9 col-md-9 col-lg-10">
									<?php echo form_input(array(
                                            'name'=>'mail_title',
                                            'id'=>'mail_title',
											'class'=>'form-control',
                                            'value'=>$mail_info->mail_title)
                                    );?>
								</div>
						</div>
	
						<div class="form-group">
								<?php 
								echo form_label(lang('customers_manage_mail_content').' :', 'content',array('class'=>'required wide col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
								<div class="col-sm-9 col-md-9 col-lg-10">
									<?php echo form_textarea(array(
                                            'name'=>'mail_content',
                                            'id'=>'mail_content',
                                            'class'=>'form-control text-area',
                                            'value'=>$mail_info->mail_content)
                                    );?>
                                    <?php echo display_ckeditor($ckeditor);?>
								</div>
						</div>
					</div>
				</div>
				
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
                     echo form_hidden('row_id', $mail_info->mail_id);
					 echo form_submit(array(
						'name'=>'submit',
						'id'=>'submit',
						'style'=>'margin-right:10px',
						'value'=>lang('common_submit'),
						'class'=>'submit_button float_right')
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
  $(document).ready(function()
  {
		setTimeout(function(){$(":input:visible:first","#manage_mail_form").focus();},100);
		$("#cancel").click(cancelCustomerAdding);
		
		var submitting = false;
		var submitting = false;
        $('#manage_mail_form').validate({/*sau khi them submit no se goi lai manage*/
            submitHandler: function (form) {
            	if (submitting) return;
                submitting = true;
                
                $(form).ajaxSubmit({
                	success:function(response){
                		submitting = false;
    					show_feedback(response.success ? 'success' : 'error',response.message, response.success ? <?php echo json_encode(lang('common_success')); ?>  : <?php echo json_encode(lang('common_error')); ?>);
    					
                        if(response.success){
                        	window.location.href = '<?php echo site_url('customers/manage_mail'); ?>';
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
            ignore: [],
			rules: {	                                   
				  mail_title: "required",
				  mail_content:{
						required: function(){
							 CKEDITOR.instances.mail_content.updateElement();
						},
				  }
			 },
			 messages: {
				  mail_title: <?php echo lang('customers_mail_required_title');?>,
				  mail_content: <?php echo lang('customers_mail_required_content');?>,
			 }
		});

        function cancelCustomerAdding()
        {
        	bootbox.confirm(<?php echo json_encode(lang('customers_quotes_contract_are_you_sure_cancel')); ?>, function(response)
        	{
        		if (response)
        		{
        			window.location = <?php echo json_encode(site_url('customers/manage_mail')); ?>;
        		}
        	});
        }
  });
</script>
<style type="text/css">
    #table_char{
        width: 95%;
        border-collapse: collapse;
        float: right;
    	margin-right: 10px;
    }
    #table_char tr th{
        text-align: center;
        border: 1px solid #CDCDCD;
        padding: 5px 0px;
    }

    #table_char tr td{
        padding: 5px;
        border: 1px solid #CDCDCD;
        vertical-align: top;
    }
    .li_char{
        padding: 4px 0px;
    }
</style>
<?php $this->load->view("partial/footer"); ?>