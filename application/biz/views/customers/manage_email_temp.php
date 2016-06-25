
<script type="text/javascript">
$(document).ready(function (){
    var table_columns = ["", "mail_id", 'mail_title','', ''];
    enable_select_all();
    enable_checkboxes();
    enable_row_selection();
    enable_delete(<?php echo json_encode(lang('customers_mail_delete_msg_confrim')); ?>,<?php echo json_encode(lang($controller_name . "_none_selected")); ?>);
    $(".delete_email_temp").click(function(){
        var id = $(this).attr("data-id");
        var parent = $(this).parent().parent();
        var data = "ids=" + id;
        $.ajax({
            type: "post",
            url: '<?php echo site_url("$controller_name/remove_mail_list");?>',
            data: data,
            success: function(data){
                $(parent).remove();
                $(".total-mail-temp").html(data);
            }
        });
        return false;
    });     

    $("#delete_mail_list").click(function(){
        var parent = $(this).parent().parent().find("table tbody");
        $.ajax({
            type: "post",
            url: '<?php echo site_url("$controller_name/remove_mail_list");?>',
            success: function(data){
                $(parent).remove();
                setTimeout(function() { location.reload();},2000);
            }
        });
        return false;
    });

    $("#myModal .close").click(function(){
    	location.reload();
    });
})
</script>

<div class="modal-dialog">
	<div class="modal-content customer-recent-sales">
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-label=<?php echo json_encode(lang('common_close')); ?>><span aria-hidden="true" class="ti-close"></span></button>
			<h4 class="modal-title"> <?php echo lang('customers_mail_send_mail'); ?></h4>
		</div>
		<div class="modal-body ">
			<div class="panel panel-piluku">
				<div class="panel-heading">
					<h3 class="panel-title">
						<?php echo lang('module_'.$controller_name.'_mail_tmp'); ?>
						<span title="<?php echo $total_rows; ?> total <?php echo $controller_name?>" class="badge bg-primary tip-left total-mail-temp"><?php echo $total_rows; ?></span>
						<div class="panel-options custom">
						<?php if($pagination) {  ?>
							<div class="pagination pagination-top hidden-print  text-center" id="pagination_top">
								<?php echo $pagination;?>		
							</div>
						<?php }  ?>    
						</div>
					</h3>
				</div>
				<div class="panel-body nopadding table_holder table-responsive" >
					<?php echo $manage_table; ?>
					
					<?php if ($total_rows){?>
					<div class="form-group" style="height: 25px;">
				        <?php echo anchor("$controller_name/send_mail?type_send=1",
							lang('customers_mail_send'),
							array(
							  'class' => 'btn btn-primary btn-lg', 
							  'id'    =>'sendmail_list', 
							  'title' => lang('customers_mail_send'),
							  'data-toggle' => "modal",
							  'data-target' => "#myModal"
							));
						?>
						<?php echo anchor("$controller_name/#",
							lang('customers_mail_delete_all_temp'),
							array(
							  'class' => 'btn btn-primary btn-lg', 
							  'id'    =>'delete_mail_list', 
							  'title' => lang('customers_mail_delete_all_temp'),
							  'style' => 'width: 90px !important;float:right;line-height:0px;margin-right: 10px;',
							));
						?>
				 	</div>
				 	<?php } ?>		
				</div>		
				
			</div>
			
		</div>
	</div>
</div>
