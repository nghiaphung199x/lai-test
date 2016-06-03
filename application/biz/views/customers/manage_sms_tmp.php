<?php $this->load->view("partial/header"); ?>
<script type="text/javascript">
$(document).ready(function (){
    var table_columns = ["", "id", 'title','', ''];
    enable_sorting("<?php echo site_url("$controller_name/sorting_sms"); ?>", table_columns, <?php echo $per_page; ?>);
    enable_select_all();
    enable_checkboxes();
    enable_row_selection();
	enable_search('<?php echo site_url("$controller_name/suggest_sms");?>',<?php echo json_encode(lang("common_confirm_search"));?>);
    enable_delete(<?php echo json_encode('Bạn muốn xóa SMS này?'); ?>,<?php echo json_encode(lang($controller_name . "_none_selected")); ?>);
        $(".delete_sms_tmp").click(function(){
        var id = $(this).attr("id");
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
		<div class="col-md-7">	
			<div class="buttons-list">
				<div class="pull-right-btn">
					<?php if ($this->Employee->has_module_action_permission($controller_name, 'add_update', $this->Employee->get_logged_in_employee_info()->person_id)) {?>
					<?php echo anchor("$controller_name/view_sms/-1/",
						'<span class="">'.lang('customers_sms_new').'</span>',
						array('id' => 'new-person-btn', 'class'=>'btn btn-primary btn-lg', 'title'=>lang('customers_sms_new')));
					}	
					?>
				</div>
			</div>				
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
                            <table class="mytable table_sms" cellspacing="0" style="width: 100%; margin: 0px !important;">
                                     <thead>
                                        <tr>
                                            <th><input type='checkbox' id='sms_all' value=' '/><label for='sms_all'><span></span></label></th>
                                            <th>Tên KH</th>
                                            <th>Di động</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                <?php
                                if(isset($_SESSION['sms_tmp']) && $_SESSION['sms_tmp']!=NULL){
                                ?>
                                    <tbody>
                                        <?php
                                        foreach ($_SESSION['sms_tmp'] as $value){
                                            $i++;
                                        ?>
                                        <tr><td><input type='checkbox' id='sms_<?php echo $value['person_id'];?>' value=' '/><label for='sms_<?php echo $value['person_id'];?>'><span></span></label></td>
                                            <td ><?php echo $value['name'];?></td>
                                            <td ><?php echo $value['phone_number']?></td>
                                            <td >
                                                <a class="delete_sms_tmp a-menu" id="<?php echo $value['person_id'];?>">Xóa</a>
                                            </td>
                                        </tr>
                                        <?php
                                        }
                                        ?>
                                        <tr>
                                            <td colspan="2">
                                                <?php echo anchor("$controller_name/send_sms_list",
                                                    'Gửi SMS',
                                                    array(
                                                        'class' => 'bulk_edit_inactive submit_button float_right', 
                                                        'id'    =>'sendsms_list', 
                                                        'title' =>'Gửi SMS',
                                                        'style' => 'width: 50px !important;',
                                                        'data-toggle'=> "modal",
                                                        'data-target'=>"#myModal"
                                                    ));
//                                                ?>
<!--                                                <a class="btn btn-primary btn-lg" title="<?php //echo (lang('customers_sms_send_sms'));?>" id="sendSMS" href="<?php echo current_url(). '#'; ?>"  data-toggle="modal" data-target="#myModal">
                                                    <span class=""><?php //echo (lang('customers_sms_send_sms')); ?></span>
                                                </a>-->
                                            </td>
                                            <td>
                                                <a style="width: 65px !important;" class="delete_all_sms_tmp a-menu" id="0">Xóa tất cả</a>
                                            </td>
                                        </tr>
                                    </tbody>
                                <?php
                                }else{
                                    echo "<tr>";
                                        echo "<td colspan='3' style='text-align: center'>Không có khách hàng nào</td>";
                                    echo "</tr>";
                                }
                                ?>
                                </table>
			</div>
			<div class="panel-body nopadding table_holder table-responsive" >
				<?php echo $manage_table; ?>			
			</div>		
			
		</div>
	</div>
</div>

</div>