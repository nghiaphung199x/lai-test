<div class="modal-dialog">
	<div class="modal-content customer-recent-sales">
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-label=<?php echo json_encode(lang('common_close')); ?>><span aria-hidden="true" class="ti-close"></span></button>
			<h4 class="modal-title"> <?php echo lang('sales_quotes_title'); ?></h4>
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
						echo form_open('sales/do_make_quotes/'.$sale_id,array('id'=>'form_make_quotes','class'=>'form-horizontal'));
					?>
					<ul id="error_message_box" class="text-danger"></ul>
				
					<div class="form-group">	
						<?php echo form_label(lang('sales_quotes_template').' :', 'list_quotes',array('class'=>'required col-sm-4 col-md-4 col-lg-4 control-label')); ?>
						<div class='form_field'>
					        <select name="quotes_id" id="quotes_id">
					            <option value="">--Chọn Template---</option>
					            <?php
					            foreach ($list_quotes as $quotes){
					            ?>    
					            <option value="<?php echo $quotes['id_quotes_contract']?>"><?php echo $quotes['title_quotes_contract'];?></option>
					            <?php
					            }
					            ?>
					        </select>
					   </div>
				 	</div>
					<div class="form-group">
						<?php echo form_label(lang('sales_quotes_type').' :', 'list_quotes_type',array('class'=>'col-sm-4 col-md-4 col-lg-4 control-label')); ?>
						<div class='form_field'>
					        <select name="quotes_type" id="quotes_type">
					            <option value="1">Word</option>
					            <!-- <option value="2">Excel</option> -->
					            <option value="3">Email</option>
					        </select>
					        <span id="quotes_customer_email" style="display: none;">( <?php echo($email); ?> )</span>
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
$(document).ready(function(){
    setTimeout(function(){$(":input:visible:first","#form_make_quotes").focus();},100);

    $("#form_make_quotes").submit(function(){
    	if ($("#quotes_id").val() ==0){
        	bootbox.alert("<?php echo lang('sales_quotes_error_selected'); ?>");
            return false;
        }
    });
    
    $("#quotes_type").change(function() {
    	var type = $("#quotes_type").val();
    	if (type == 3) {
        	$("#quotes_customer_email").show();
    	} else {
    		$("#quotes_customer_email").hide();
    	}
	});
});
</script>