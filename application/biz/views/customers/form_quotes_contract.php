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
		                    <?php echo lang("customers_sms_basic_information"); ?>
							<small>(<?php echo lang('common_fields_required_message'); ?>)</small>
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




<script src="<?php echo base_url() ?>public/ckeditor/ckeditor.js" type="text/javascript"></script>
<div id="content_area_wrapper">
    <div id="content_area" style="font-size: 14px">
        <table id="title_bar_new">
            <tr>
                <td id="title_icon">                    
                    <a href="<?php echo base_url('quotes_contract') ?>" ><div class="newface_back"></div></a>
                </td>
                <td id="title" style="width: 300px">&nbsp;<?php echo lang('module_quotes_contract'); ?></td>               
                <td id="title_search_new"></td>
            </tr>
        </table>
        <div id="required_fields_message"><?php echo lang('common_fields_required_message'); ?></div>
        <fieldset id="item_basic_info">
            <legend>Thông tin các từ thay thế</legend>
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
        <ul id="error_message_box"></ul>   
        <fieldset id="item_basic_info">            
            <legend><?php echo lang("quotes_contract_basic_information"); ?></legend>
            <?php echo form_open('quotes_contract/save/' . $info_quotes_contract->id_quotes_contract, array('id' => 'quotes_contract_form')); ?>
            <table class="table_form">
                <tr>
                    <td class="left_table_form"><?php echo form_label('Loại mẫu:', 'cat', array('class' => 'required wide')); ?></td>
                    <td class="right_table_form">
                        <select name="cat_quotes_contract" class="select_form">
                            <?php if ($info_quotes_contract->cat_quotes_contract == 1) { ?>
                                <option value="">Loại mẫu HĐ - BG</option>
                                <option value="1" selected="selected">Hợp đồng</option>
                                <option value="2">Báo giá</option>
                            <?php } else if ($info_quotes_contract->cat_quotes_contract == 2) { ?>
                                <option value="">Loại mẫu HĐ - BG</option>
                                <option value="1">Hợp đồng</option>
                                <option value="2" selected="selected">Báo giá</option>
                            <?php } else { ?>
                                <option value="" selected="selected">Loại mẫu HĐ - BG</option>
                                <option value="1">Hợp đồng</option>
                                <option value="2">Báo giá</option>
                            <?php }
                            ?>                            
                        </select>
                    </td>
                </tr>
                <tr>
                    <td class="left_table_form"><?php echo form_label('Tiêu đề:', 'title', array('class' => 'required wide')); ?></td>
                    <td class="right_table_form"><input type="text" name="title_quotes_contract" id="title_quotes_contract" value="<?= $info_quotes_contract->title_quotes_contract ?>"></td>
                </tr>
                <tr>
                    <td class="left_table_form"><?php echo form_label('Nội dung:', 'content', array('class' => 'required wide')); ?></td>
                    <td class="right_table_form"><textarea name="content_quotes_contract" id="quotes_contract"><?= $info_quotes_contract->content_quotes_contract; ?></textarea></td>
                </tr>
            </table>
            <?php
            echo form_submit(array(
                'name' => 'submit_item',
                'id' => 'submit_item',
                'value' => lang('common_submit'),
                'class' => 'submit_button float_right')
            );
            ?>
            <?php echo form_close(); ?>
        </fieldset>
    </div>
</div>
<script type="text/javascript">
    $(document).ready(function () {
        CKEDITOR.replace('quotes_contract');
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
        $('#quotes_contract_form').validate({/*sau khi them submit no se goi lai manage*/
            submitHandler: function (form) {
                $(form).ajaxSubmit({
                    success: function (response) {
                        if (!response.success) {
                            alert(response.message);
                        } else {
                            alert(response.message);
                            window.location = "<?= base_url('quotes_contract'); ?>";
                        }
                    },
                    dataType: 'json'
                });
            },
            errorLabelContainer: "#error_message_box",
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
                    required: 'Bạn chưa chọn loại mẫu'
                },
                title_quotes_contract: {
                    required: 'Bạn chưa nhập tiêu đề'
                },
                content_quotes_contract: {
                    required: 'Bạn chưa nhập nội dung'
                }
            }
        });
    });
</script>
<?php $this->load->view("partial/footer"); ?>
<style type="text/css">
    .table_form {
        border-collapse: collapse;
        width: 100%;
        margin: 0px auto;
    }
    .table_form tr td{
        padding: 10px 5px;
    }
    .left_table_form{
        width: 200px;
    }

    .right_table_form{
        width: 750px;
    }

    #quotes_contract{
        width: 95%;
        height: 300px;
        padding: 10px;
    }

    #title_quotes_contract{
        width: 50%;
        padding: 5px;
    }

    .select_form{
        padding: 3px 10px;
    }

    #table_char{
        width: 100%;
        border-collapse: collapse;
        margin: 0px auto;
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