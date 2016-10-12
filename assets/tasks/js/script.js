$( document ).ready(function() {
	// xử lý checkbox
	$('body').on('click','.my-table .check_tatca',function(){
		  var checkbox = $(this).closest('th').find('input[type="checkbox"]'); 
		  if (checkbox.prop('checked') == true){  
			 $('.manage-row-options').hide();
			 checkbox.prop('checked', false);
			 $(this).parents('.table').find('td input[type="checkbox"]').prop('checked', false);
		  }else{
			  $('.manage-row-options').show();
			  checkbox.prop('checked', true);
			  $(this).parents('.table').find('td input[type="checkbox"]').prop('checked', true);
		  }
    });
	
	// no character
    $('body').on('keypress', '.no-character', function(event){
	    var regex = new RegExp("^[0-9]+$");
	    var key = String.fromCharCode(!event.charCode ? event.which : event.charCode);
	    if (!regex.test(key)) {
	       event.preventDefault();
	       return false;
	    }
    });
    

   $('body').on('click','.my-table tbody tr td.cb',function(){
	    var checkbox = $(this).closest('tr').find('input[type="checkbox"]');
	    if (checkbox.prop('checked') == true){ 
	        $('.manage-row-options').hide();
		    checkbox.prop('checked', false);
	    }else{
	        $('.manage-row-options').show();
		    checkbox.prop('checked', true);
	    }
   });
   
   // template task list table
   $('body').on('click','#btnListTasks',function(){
		var tree_array    = $('#sTree2').sortableListsToArray();
		var tasks = new Array();
		if(tree_array.length > 0) {
			$.each( tree_array, function( key, value ) {
				tmp = new Object();
				tmp.id   = value.id;
				tmp.name = $('#'+value.id).attr('data-name');
				if (typeof value.parentId === "undefined") {
				    tmp.parent = 'root';
				}else
					tmp.parent = value.parentId;
				
				tasks[tasks.length] = tmp;
			});
		}

		var url = BASE_URL + 'tasks/listTemplateTask';
		$.ajax({
			type: "GET",
			url: url,
			data: {
				tasks : tasks
			},
			success: function(html){
				 //console.log('---' + html);
				  $('#quick-form').html(html);
				  $('#quick-form').show();
				  create_layer('quick');
		    }
		});   
    });
   
    //Turn text element into input field - update template task
    $('body').on('dblclick', '[data-editable]', function(){
	   var $el = $(this);
	   trElement    = $el.closest('tr');
	   var id = trElement.find('a').attr('data-id');
	  
	   var $input = $('<input/>').val( $el.text() );
	   $el.replaceWith( $input );
	   
	   var save = function(){
	     var $span = $('<span data-editable />').text( $input.val() );
	     $input.replaceWith( $span );
	     
	     data = new Object();
	     
	     data.text    = $input.val();
	     data.id      = id;

	     do_something(data);
	   };
	   
	   $input.one('blur', save).focus();
	 });
    
	// phân trang
    var array_list = ['template'];
	$.each( array_list, function( key, keyword ) {
		if(keyword == 'template') {
			var manage_div = 'template_list';
		}
		
		$('body').on('click','#'+manage_div+' .pagination a',function(){
			var page = $(this).attr('data-page');
			load_list(keyword, page);
		});
	});
});


function add_template_task() {
	var url = BASE_URL + 'tasks/addcvtemplate'
	$.ajax({
		type: "GET",
		url: url,
		data: {
		},
		success: function(html){
			  $('#quick-form').html(html);
			  $('#quick-form').show();
			  create_layer('quick');
	    }
	});
}

function delete_template() {
	var checkbox = $(".file_checkbox:checked");
	var template_ids = new Array();
	$(checkbox).each(function( index ) {
		template_ids[template_ids.length] = $(this).val();
	});
	
	bootbox.confirm('Bạn có chắc muốn xóa không?', function(result){
		if (result){
			$.ajax({
				type: "POST",
				url: BASE_URL + 'tasks/deleteTemplate',
				data: {
					template_ids   : template_ids,
				},
				success: function(string){
					toastr.success('Cập nhật thành công!', 'Thông báo');
					load_list('template', 1);
			    }
			});
		}
	});
}

function del_template_task(obj) {
	var id = $(obj).attr('data-id');
	parent_item = $('#'+id).closest('ul'); 

	if(parent_item.hasClass('listsClass')){
		$('#'+id).remove();
	}else{
		parent_item.remove();
	}
		
	// remove trên table
	$(obj).closest('tr').remove();
	var child_ids = $(obj).attr('data-child');
	if (child_ids) {
		var child_ids = child_ids.split(",");
		$.each(child_ids, function( index, value ) {
			$('#template_task_list tbody tr a[data-id="'+value+'"]').closest('tr').remove();
		});
	}
	
	var count = $('#template_task_list tbody tr').length;
	$('#count_template_task').text(count);
	
	if(count == 0) {
		$('#template_task_list tbody').html('<tr><td colspan="2"><div class="col-log-12" style="text-align: center; color: #efcb41;">Không có dữ liệu hiển thị</div></td></tr>');
	}
}

function do_something(data) {
	var id   = data.id;
	var text = data.text;
	$('#'+id+ ' > div').html(text);
}

function add_template() {
	var template_name = $.trim($('#template_name').val());
	var tree_array    = $('#sTree2').sortableListsToArray();
	var tasks = new Array();
	if(tree_array.length > 0) {
		$.each( tree_array, function( key, value ) {
			tmp = new Object();
			tmp.id   = value.id;
			tmp.name = $('#'+value.id).attr('data-name');
			if (typeof value.parentId === "undefined") {
			    tmp.parent = 'root';
			}else
				tmp.parent = value.parentId;
			
			tasks[tasks.length] = tmp;
		});
	}
	
	$.ajax({
		type: "POST",
		url: BASE_URL + 'tasks/templateAdd',
		data: {
			template_name : template_name,
			tasks : tasks
		},
		success: function(string){
			var res = $.parseJSON(string);
			
			if(res.flag == 'false'){
				toastr.error(res.msg, 'Lỗi!');
			}else {
				toastr.success(res.msg, 'Thông báo');
				$('#template_name').val('');
				$('#sTree2').html('');
			}
	    }
	});
}


function add_project() {
	url = BASE_URL + 'tasks/addcongviec';
	
	$.ajax({
		type: "GET",
		url: url,
		data: {
			id : 0,
			parent: 0
		},
		success: function(html){
		   create_layer();
		   $('#my-form').removeClass('quickInfo');
		   $('#my-form').html(html);
		   $('#my-form').show();

		   $('#color').colorpicker({color: '#489ee7',});
		   
		   // picker
		   date_time_picker_field($('.datepicker'), JS_DATE_FORMAT);
		   // end picker
		   
		   var frame_array = ['customer_list', 'xem_list', 'implement_list', 'create_task_list', 'pheduyet_task_list', 'progress_list'];
		   $.each(frame_array, function( index, value ) {
			  css_form(value);
			  press(value);
		   });
	    }
	});
}

function add_congviec() {
	reset_error();
	var checkOptions = {
	        url : BASE_URL+'tasks/addcongviec',
	        dataType: "json",  
	        success: congviecData
	    };
    $("#task_form").ajaxSubmit(checkOptions); 
    return false; 
}

function congviecData(data) {
	if(data.flag == 'false') {
		$.each(data.errors, function( index, value ) {	
			element = $( '#my-form span[for="'+index+'"]' );
			element.prev().addClass('has-error');
			element.text(value);
		});	
	}else {
		toastr.success('Cập nhật thành công!', 'Thông báo');
		$('#my-form').html('');
		$('#my-form').hide();
		
		load_list('project', 1);
		close_layer();
	}
}

function delete_project() {
	var checkbox = $(".file_checkbox:checked");
	var task_ids = new Array();
	$(checkbox).each(function( index ) {
		task_ids[task_ids.length] = $(this).val();
	});
	
	bootbox.confirm('Bạn có chắc muốn xóa không?', function(result){
		if (result){
			$.ajax({
				type: "POST",
				url: BASE_URL + 'tasks/deletecv',
				data: {
					ids   : task_ids,
				},
				success: function(string){
					toastr.success('Cập nhật thành công!', 'Thông báo');
					load_list('project', 1);
			    }
			});
		}
	});
}

function edit_congviec(id) {
	var url = BASE_URL + 'tasks/editcongviec';
	$.ajax({
		type: "GET",
		url: url,
		data: {
			id : id,
			parent: 0
		},
		success: function(html){
		   if(html != '') {   
			   create_layer();
			   $('#my-form').html(html);
			   $('#my-form').show(); 
			   $('#color').colorpicker();
			   
		   }else {
			   gantt.alert({
				    text: 'Bạn không có quyền với chức năng này.', title:"Cảnh báo!",
				    ok:"Đóng", callback:function(){}
				});
		   }
		   
		   // picker
		   date_time_picker_field($('.datepicker'), JS_DATE_FORMAT);
		   // end picker
		   
		   var frame_array = ['customer_list', 'xem_list', 'implement_list', 'create_task_list', 'pheduyet_task_list', 'progress_list'];
		   $.each(frame_array, function( index, value ) {
			  css_form(value);
			  press(value);
		   });
	    }
	});
}