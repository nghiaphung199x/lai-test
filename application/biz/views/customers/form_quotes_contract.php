<?php $this->load->view("partial/header"); ?>


<div class="row" id="form">
	<div class="spinner" id="grid-loader" style="display: none">
		<div class="rect1"></div>
		<div class="rect2"></div>
		<div class="rect3"></div>
	</div>
	
	<div class="col-md-12">
		<?php echo form_open('customers/quotes_contract_save/' . $info_quotes_contract->id_quotes_contract, array('id' => 'quotes_contract_form','class'=>'form-horizontal')); ?>
		<div class="panel panel-piluku">
			<div class="panel-heading">
				<h3 class="panel-title">
					<i class="ion-edit"></i> 
		                    <?php echo lang('common_list_of').' '.lang('module_customers_quotes_contract'); ?>
							<small>(<?php echo lang('common_fields_required_message'); ?>)</small>
				</h3>
			</div>
			<fieldset id="item_basic_info">
				<h3 class="panel-title" style="padding:15px 20px;"><?php echo lang('customers_quotes_contract_replate_info');?></h3>
				<table id="table_char">
					 <tr>
						  <th>Nhà cung cấp (NCC)</th>
						  <th>Khách hàng (KH)</th>
						  <th>Từ thay thế khác</th>
					 </tr>
					 <tr>
						  <td>
								<ul>
									 <li class="li_char">- {LOGO}: Logo</li>
									 <li class="li_char">- {TEN_NCC}: Tên</li>
									 <li class="li_char">- {DIA_CHI_NCC}: Địa chỉ</li>
									 <li class="li_char">- {SDT_NCC}: Số điện thoại</li>
									 <li class="li_char">- {DD_NCC}: Đại diện</li>
									 <li class="li_char">- {CHUCVU_NCC}: Chức vụ</li>
									 <li class="li_char">- {TKNH_NCC}: Tài khoản ngân hàng</li>
									 <li class="li_char">- {NH_NCC}: Chi nhánh ngân hàng</li>                            
								</ul>
						  </td>
						  <td>
								<ul>
									 <li class="li_char">- {TEN_KH}: Tên</li>
									 <li class="li_char">- {DIA_CHI_KH}: Địa chỉ</li>
									 <li class="li_char">- {SDT_KH}: Số điện thoại</li>
									 <li class="li_char">- {DD_KH}: Đại diện</li>
									 <li class="li_char">- {CHUCVU_KH}: Chức vụ</li>
									 <li class="li_char">- {TKNH_KH}: Tài khoản ngân hàng</li>
									 <li class="li_char">- {NH_KH}: Chi nhánh ngân hàng</li>                            
								</ul>
						  </td>
						  <td>
								<ul>
									 <li class="li_char">- {TITLE}: Tiêu đề báo giá - hợp đồng</li>
									 <li class="li_char">- {TABLE_DATA}: Bảng danh sách hàng hóa, dịch vụ</li>
									 <li class="li_char">- {CODE}: Mã báo giá - hợp đồng</li>
									 <li class="li_char">- {DATE}: Ngày báo giá</li>
									 <li class="li_char">- {MONTH}: Tháng báo giá</li>
									 <li class="li_char">- {YEAR}: Năm báo giá</li>
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
								echo form_label(lang('customers_quotes_contract_type').' :', 'type',array('class'=>'required wide col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
								<div class="col-sm-9 col-md-9 col-lg-10">
									<select name="cat_quotes_contract" class="select_form form-control">
			                            <?php if ($info_quotes_contract->cat_quotes_contract == 1) { ?>
			                                <option value=""><?php echo(lang('customers_quotes_contract_type_select'));?></option>
			                                <option value="1" selected="selected"><?php echo(lang('customers_quotes_contract_type_contract'));?></option>
			                                <option value="2"><?php echo(lang('customers_quotes_contract_type_quotes'));?></option>
			                            <?php } else if ($info_quotes_contract->cat_quotes_contract == 2) { ?>
			                                <option value=""><?php echo(lang('customers_quotes_contract_type_select'));?></option>
			                                <option value="1"><?php echo(lang('customers_quotes_contract_type_contract'));?></option>
			                                <option value="2" selected="selected"><?php echo(lang('customers_quotes_contract_type_quotes'));?></option>
			                            <?php } else { ?>
			                                <option value="" selected="selected"><?php echo(lang('customers_quotes_contract_type_select'));?></option>
			                                <option value="1"><?php echo(lang('customers_quotes_contract_type_contract'));?></option>
			                                <option value="2"><?php echo(lang('customers_quotes_contract_type_quotes'));?></option>
			                            <?php }
			                            ?>                            
			                        </select>
								</div>
						</div>
	
						<div class="form-group">
								<?php 
								echo form_label(lang('customers_quotes_contract_title').' :', 'title',array('class'=>'required wide col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
								<div class="col-sm-9 col-md-9 col-lg-10">
									<?php echo form_input(array(
										'name'=>'title_quotes_contract',
										'id'=>'title_quotes_contract',
										'class'=>'form-control',
										'value'=>$info_quotes_contract->title_quotes_contract)		
									);?>
								</div>
						</div>
	
						<div class="form-group">
								<?php 
								echo form_label(lang('customers_quotes_contract_content').' :', 'content',array('class'=>'required wide col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
								<div class="col-sm-9 col-md-9 col-lg-10">
									<?php echo form_textarea(array(
										'name'=>'content_quotes_contract',
										'id'=>'content_quotes_contract',
										'class'=>'form-control text-area',
										'value'=>$info_quotes_contract->content_quotes_contract)		
									);?>
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




<script src="<?php echo base_url() ?>assets/js/biz/ckeditor/ckeditor.js" type="text/javascript"></script>
<script type="text/javascript">
    $(document).ready(function () {
        CKEDITOR.replace('content_quotes_contract');
        CKEDITOR.config.height = 300;
        CKEDITOR.on('instanceReady', function () {
            $.each(CKEDITOR.instances, function (instance) {
                CKEDITOR.instances[instance].document.on("keyup", CK_jQ);
                CKEDITOR.instances[instance].document.on("paste", CK_jQ);
                CKEDITOR.instances[instance].document.on("keypress", CK_jQ);
                CKEDITOR.instances[instance].document.on("blur", CK_jQ);
                CKEDITOR.instances[instance].document.on("change", CK_jQ);
            });
        });

        function CK_jQ() {
            for (instance in CKEDITOR.instances) {
                CKEDITOR.instances[instance].updateElement();
            }
        }
    });
</script>
<script type='text/javascript'>
    //validation and submit handling
    $(document).ready(function () {
        setTimeout(function () {
            $(":input:visible:first", "#quotes_contract_form").focus();
        }, 100);
        $("#cancel").click(cancelCustomerAdding);

        var submitting = false;
        $('#quotes_contract_form').validate({/*sau khi them submit no se goi lai manage*/
            submitHandler: function (form) {
            	if (submitting) return;
                submitting = true;
                
                $(form).ajaxSubmit({
                	success:function(response){
                		submitting = false;
    					show_feedback(response.success ? 'success' : 'error',response.message, response.success ? <?php echo json_encode(lang('common_success')); ?>  : <?php echo json_encode(lang('common_error')); ?>);
    					
                        if(response.success){
                        	window.location.href = '<?php echo site_url('customers/quotes_contract'); ?>';
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
                cat_quotes_contract: {
                    required: true
                },
                title_quotes_contract: {
                    required: true
                },
                content_quotes_contract: {
                    required: true
                }
            },
            messages: {
                cat_quotes_contract: {
                    required: <?php echo json_encode(lang('customers_quotes_contract_type_required'));?>
                },
                title_quotes_contract: {
                    required: <?php echo json_encode(lang('customers_quotes_contract_title_required'));?>
                },
                content_quotes_contract: {
                    required: <?php echo json_encode(lang('customers_quotes_contract_content_required'));?>
                }
            }
        });
    });

    function cancelCustomerAdding()
    {
    	bootbox.confirm(<?php echo json_encode(lang('customers_quotes_contract_are_you_sure_cancel')); ?>, function(response)
    	{
    		if (response)
    		{
    			window.location = <?php echo json_encode(site_url('customers/quotes_contract')); ?>;
    		}
    	});
    }
</script>
<?php $this->load->view("partial/footer"); ?>

<style type="text/css">
    #table_char{
        width: 90%;
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