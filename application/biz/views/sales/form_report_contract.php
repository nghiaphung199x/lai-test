<div class="modal-dialog">
	<div class="modal-content customer-recent-sales">
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-label=<?php echo json_encode(lang('common_close')); ?>><span aria-hidden="true" class="ti-close"></span></button>
			<h4 class="modal-title"> <?php echo lang('sales_contract_title'); ?></h4>
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
						echo form_open('sales/do_make_contract/'.$sale_id,array('id'=>'form_make_contract','class'=>'form-horizontal'));
					?>
					<ul id="error_message_box" class="text-danger"></ul>
				
					<div class="form-group">	
						<?php echo form_label(lang('sales_contract_template'), 'list_contract',array('class'=>'required col-sm-4 col-md-4 col-lg-4 control-label')); ?>
						<div class='form_field'>
					        <select name="contract_id" id="contract_id">
					            <option value="0">--Chọn Template---</option>
					            <?php
					            foreach ($list_contract as $contract){
					            ?>    
					            <option value="<?php echo $contract['id_quotes_contract']?>"><?php echo $contract['title_quotes_contract'];?></option>
					            <?php
					            }
					            ?>
					        </select>
					   </div>
				 	</div>
					<div class="form-group">
						<?php echo form_label(lang('sales_contract_type'), 'list_contract_type',array('class'=>'col-sm-4 col-md-4 col-lg-4 control-label')); ?>
						<div class='form_field'>
					        <select name="contract_type" id="contract_type">
					            <option value="1">Word</option>
					            <!-- <option value="2">Excel</option> -->
					            <option value="3">Email</option>
					        </select>
					        <span id="contract_customer_email" style="display: none;">( <?php echo($email); ?> )</span>
					   </div>
					</div>
					
					<div class="modal-footer">
						<div class="form-acions">
							<?php
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
    setTimeout(function(){$(":input:visible:first","#form_make_contract").focus();},100);
    $("#form_make_contract").submit(function(){
    	if ($("#contract_id").val() ==0){
        	bootbox.alert("<?php echo lang('sales_contract_error_selected'); ?>");
            return false;
        }
    });
    $("#contract_type").change(function() {
    	var type = $("#contract_type").val();
    	if (type == 3) {
        	//$("#contract_customer_email").show();
    	} else {
    		$("#contract_customer_email").hide();
    	}
	});
});
</script>