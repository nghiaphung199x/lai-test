<?php $this->load->view("partial/header"); ?>
<script type="text/javascript">
$(document).ready(function (){
    var table_columns = ["", "id", 'title','', ''];
    enable_select_all();
    enable_checkboxes();
    enable_row_selection();
	enable_search('<?php echo site_url("$controller_name/suggest_sms");?>',<?php echo json_encode(lang("common_confirm_search"));?>);
    enable_delete(<?php echo json_encode('Bạn muốn xóa SMS này?'); ?>,<?php echo json_encode(lang($controller_name . "_none_selected")); ?>);
        $(".delete_sms_tmp").click(function(){
        var id = $(this).attr("data-id");
        var parent = $(this).parent().parent();
        var data = "ids=" + id;
        $.ajax({
            type: "post",
            url: '<?php echo site_url("$controller_name/delete_sms_tmp_id");?>',
            data: data,
            success: function(data){
                $(parent).remove();
//                $(".number_mail").html(data);
            }
        });
        return false;
    });     
//    delete_sms_tmp_all
$('.delete_all_sms_tmp').click(function(){
  $.ajax({
            type: "post",
            url: '<?php echo site_url("$controller_name/delete_sms_tmp_all");?>',
            success: function(data){
                $('table tbody').html("<tr><td colspan='3' style='text-align: center'>Không có khách hàng nào</td></tr>")
            }
        });
})  
})
</script>

<div class="manage_buttons">
	<div class="manage-row-options hidden">
		<div class="email_buttons text-center">
			<?php if ($this->Employee->has_module_action_permission($controller_name, 'delete', $this->Employee->get_logged_in_employee_info()->person_id)) {?>
			<?php echo anchor("$controller_name/delete_sms",
				'<span class="">'.lang('common_delete').'</span>'
				,array('id'=>'delete', 'class'=>'btn btn-red btn-lg disabled delete_inactive ','title'=>lang("common_delete"))); ?>
			<?php } ?>
	
			<a href="#" class="btn btn-lg btn-clear-selection btn-warning"><?php echo lang('common_clear_selection'); ?></a>
		</div>
	</div>
	<div class="row">
		<div class="col-md-5">
			<?php echo form_open("$controller_name/search_sms",array('id'=>'search_form', 'autocomplete'=> 'off', 'class' => 'form-inline')); ?>
				<div class="search no-left-border">
					<input type="text" class="form-control" name ='search' id='search' value="<?php echo H($search); ?>" placeholder="<?php echo lang('common_search'); ?> <?php echo lang('module_'.$controller_name .'_sms'); ?>"/>
				</div>		
			</form>	
			
		</div>
	</div>
</div>
<div class="container-fluid">
	<div class="row manage-table">
		<div class="panel panel-piluku">
			<div class="panel-heading">
				<h3 class="panel-title">
					<?php echo lang('common_list_of').' '.lang('module_'.$controller_name.'_sms_tmp'); ?>
					<span title="<?php echo $total_rows; ?> total <?php echo $controller_name?>" class="badge bg-primary tip-left"><?php echo $total_rows; ?></span>
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
			</div>		
			
		</div>
	</div>
</div>

</div>