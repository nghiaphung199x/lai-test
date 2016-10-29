<?php $this->load->view("partial/header"); ?>
<link href="https://hstatic.net/0/0/global/design/plugins/font-awesome/4.5.0/css/font-awesome.min.css" media="screen" rel="stylesheet" type="text/css">
<link rel="stylesheet" href="<?php echo base_url();?>assets/tasks/css/style.css" type="text/css" media="screen" />
<link rel="stylesheet" href="<?php echo base_url();?>assets/tasks/css/responsive.css" type="text/css" media="screen" />
<link rel="stylesheet" href="<?php echo base_url();?>assets/scripts/tasks/codebase/dhtmlxgantt.css" type="text/css" />
<script src="<?php echo base_url();?>assets/scripts/tasks/codebase/dhtmlxgantt.js" type="text/javascript" charset="utf-8"></script>

<script type="text/javascript" src="<?php echo base_url() ?>assets/tasks/js/task-core.js" ></script>
<script type="text/javascript" src="<?php echo base_url() ?>assets/tasks/js/script.js" ></script>

<div class="manage_buttons">
<div class="manage-row-options">
	<div class="email_buttons text-center">		
		<a href="javascript:;" class="btn btn-red btn-lg" title="Xóa" onclick="delete_project();"><span class="">Xóa lựa chọn</span></a>		
	</div>
</div>
<div class="cl">
	<div class="pull-left">
		<form id="search_form" autocomplete="off" class="form-inline" method="post" accept-charset="utf-8">
			<div class="search no-left-border">
				<span role="status" aria-live="polite" class="ui-helper-hidden-accessible"></span><input type="text" class="form-control ui-autocomplete-input" name="search" id="s_keywords" value="" placeholder="Tìm kiếm dự án" autocomplete="off">
			</div>
			<div class="clear-block hidden">
				<i class="ion ion-close-circled"></i>
			</a>	
			<a class="clear" href="javascript:;">
	</div>
		</form>
	</div>
	<div class="pull-right">
		<div class="buttons-list" style="padding-top: 0;">
			<div class="pull-right-btn">
				<a href="javascript:;" onclick="add_project();" class="btn btn-primary btn-lg" title="Thêm mới Template"><span class="">Thêm mới Dự án</span></a>					
			</div>
		</div>				
	</div>
    <div class="cl"></div>
</div>
</div>
<div class="container-fluid">
	<div class="row manage-table" id="project_list">
		<div class="panel panel-piluku">
			<div class="panel-heading">
				<h3 class="panel-title">
					Thông tin Dự án			
					<span class="badge bg-primary tip-left" id="count_project">0</span>
					<span class="panel-options custom">
					</span>
				</h3>
				<i class="fa fa-spinner fa-spin" id="loading_3"></i>
			</div>

			<div class="panel-body nopadding table_holder table-responsive">
				<table class="tablesorter table  table-hover my-table" id="project_table">
					<thead>
						<tr>
							<th class="leftmost" style="width: 20px;">
								<input type="checkbox"><label for="select_all" class="check_tatca"><span></span></label>
							</th>
							<th data-field="name">Tên</th>
							<th data-field="prioty">Ưu tiên</th>
							<th>Sắp xếp</th>
							<th style="width: 20%;" data-field="modified">Cập nhật cuối</th>
							<th style="width: 20%;" data-field="username">Cập nhật bởi</th>
							<th style="width: 100px;">&nbsp;</th>
						</tr>
					</thead>
					<tbody>
					</tbody>
				</table>			
			</div>	
		</div>	
	</div>
</div>


<script type="text/javascript">
$( document ).ready(function() {
	load_list('project', 1);
    $('body').on('dblclick', 'span.sort', function(){
  	   var $el   	  = $(this);
  	   var old_value  = $el.attr('data-value');
  	   var id  		  = $el.attr('data-id');
  	   var $input = $('<input class="no-character" style="width: 50px;"/>').val( $el.text() );
  	   $el.replaceWith( $input );
  	   
  	   var sort = function(){
  	  	   new_value = $.trim($input.val());
  		  if (!new_value) {
  	  	      value = old_value;
  	  	  	  toastr.error('Giá trị sửa đổi không được rỗng!', 'Lỗi');

  	  	  	  var $span = $('<span class="sort" data-id="'+id+'" />').text( value );
	  	      $input.replaceWith( $span );
  	  	  }else {
			 
	    	  bootbox.confirm("Bạn có chắc muốn lưu lại?", function(result){
    	    	  	 if (result){
    	   	  			 $.ajax({
    		  				type: "POST",
    		  				url: BASE_URL + 'tasks/sort',
    		  				data: {
    		  					id   : id,
    		  					sort : new_value,
    		  					
    		  				},
    		  				success: function(string){
    		  					var res = $.parseJSON(string);
    		  					if(res.flag == 'false'){
        		  					value = old_value;
    		  						toastr.error(res.msg, 'Lỗi!');
    		  					}else {
    		  						toastr.success(res.msg, 'Thông báo');
   		  						 	value = $input.val();

   		  						 	load_list('project', 1);
    		  					}

   		  				      var $span = $('<span class="sort" data-id="'+id+'" data-value="'+value+'"/>').text( value );
		  			  	      $input.replaceWith( $span );
    		  			    }
    		  			 });   
    	    	  	}else {
   	    	  		   value = old_value;
      	    	  	   var $span = $('<span class="sort" data-id="'+id+'" data-value="'+value+'" />').text( value );
         		  	   $input.replaceWith( $span );
    	    	  	}
	    	   });   	
  	  	  }

  	   };
  	   
  	   $input.one('blur', sort).focus();
  	 });

	$('body').on('click','.manage-table-file tbody tr td.cb',function(){
		 var checkbox = $(this).closest('tr').find('input[type="checkbox"]');
		 var manage_tab = checkbox.closest('.manage-table-file');
		 var manage_tab_id = manage_tab.attr('id');

		 if (checkbox.prop('checked') == true){ 
			  checkbox.prop('checked', false);
		 }else{
			 $('.manage-row-options.2').show();
			 checkbox.prop('checked', true);
		 }

		var checked_box = $(".file_checkbox:checked");
		if(checked_box.length == 0) 
			$('.manage-row-options.2').hide();
  });

   // search
   var typingTimer;       
   $('body').on('keyup','#s_keywords',function(){
	   clearTimeout(typingTimer);
	   typingTimer = setTimeout(startSearch, 300);
   });
   
   $('body').on('keydown','#s_keywords',function(){
	   clearTimeout(typingTimer);
   });
   
   function startSearch () {
	  load_list('project', 1);
   }

});
</script>
<style>
#my-form .form-group select.form-control {
	padding: 6px 7px;
	font-size: 14px;
}

#my-form #s_task_id, #quick-form .form-group select {
	padding: 6px 7px;
	font-size: 14px;
}

</style>
<?php $this->load->view("partial/footer"); ?>