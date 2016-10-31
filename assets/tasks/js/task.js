	var taskId = null;
	var frame_id = null;
	var type = null;
	var deny_items = new Array();
	var drag_task = new Array();

	$( document ).ready(function() {
        gantt.templates.tooltip_text = function(start,end,task){
            //return task.tooltip;
            return false;
        };

        $( "#btn_tooltip" ).click(function() {
            gantt_tooltip();
        });

		// checkbox	
		$('body').on('click','.manage-table tbody tr td.cb',function(){
			 var checkbox = $(this).closest('tr').find('input[type="checkbox"]');
			 var manage_tab = checkbox.closest('.manage-table');
			 var manage_tab_id = manage_tab.attr('id');

			 if (checkbox.prop('checked') == true){ 
				  checkbox.prop('checked', false);
			 }else{
				 $('.manage-row-options').show();
				 checkbox.prop('checked', true);
			 }

			var checked_box = $(".file_checkbox:checked");
			if(checked_box.length == 0) 
				$('.manage-row-options').hide();
	    });
		
		// task pagination
		$('body').on('click', '#pagination_top a', function(){
			gantt.clearAll();
			var page = $(this).attr('data-page');
			load_task(page);
		});

		// check all
		$('body').on('click','.manage-table .check_tatca',function(){
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

		// comment
		$('body').on('click','#btnComment',function(){
			 comment();
			 return false; 
	    });

		// template task select box
		$('body').on('change','#task_template',function(){
			 var task_template_id = $(this).val();
             var progress_input = $('#my_modal .form-group input[name="progress"]');
             var trangthai_input = $('#my_modal .form-group select[name="trangthai"]');
             var trangthai_name = $('#trangthai_name');

			 if(task_template_id > 0) {
                 progress_input.hide();
                 trangthai_input.find('option[value=0]').prop('selected','selected');
                 trangthai_input.hide();
                 trangthai_name.show();
			 }else {
                 progress_input.show();
                 trangthai_input.show();
                 trangthai_name.hide();
			 }
			
	    });

        // event when close my_modal
        $('#my_modal').on('hidden.bs.modal', function () {
            var type = $('#current_type').val();
            if(taskId != undefined && type == 'new')
                gantt.deleteTask(taskId);
        })
		
		// gantt
		gantt.showLightbox = function(id) {
		    taskId = id;

		    var task   = gantt.getTask(id);
		    var parent = parseInt(task.parent);

		    if(task.$new == true){
		    	type = 'new';
		    	url = BASE_URL + 'tasks/addcongviec';
		    } else
			    url = BASE_URL + 'tasks/editcongviec';
		    
		    parent = task.parent;
			$.ajax({
				type: "GET",
				url: url,
				data: {
					id : id,
					parent: parent
				},
				success: function(html){
                   if(type == 'new') {
                       $('#my_modal').html(html);
                       $('#my_modal').modal('toggle');
                   }else {
                       if(html != '') {
                        $('#my_modal').html(html);
                        $('#my_modal').modal('toggle');
                        $('#color').colorpicker();

                    }else {
                        toastr.warning('Bạn không có quyền với chức năng này!', 'Cảnh báo');
                    }
                }

               //picker
               date_time_picker_field($('.datepicker'), JS_DATE_FORMAT);
               // end picker

               var frame_array = ['customer_list', 'xem_list', 'implement_list', 'create_task_list', 'pheduyet_task_list', 'progress_list'];
               $.each(frame_array, function( index, value ) {
                  css_form(value);
                  press(value);
               });
			    }
			});
		};

		
		gantt.attachEvent("onBeforeTaskDrag", function(id, mode, task){
            return false;
//			 if(mode == 'move' || mode == 'resize') {
//				if ($.inArray(id, deny_items) == -1){
//					var task  = gantt.getTask(id);
//					return true;
//				}
//				else{
//                    toastr.warning('Bạn không có quyền với chức năng này!', 'Cảnh báo');
//					return false;
//				}
//			}else if(mode == 'progress')
//				return false;
		});

		gantt.attachEvent("onTaskDrag", function(id, mode, task, original){
			var start_date_original = new Date(task.start_date);
			var end_date_original   = new Date(task.end_date);
			var start_hour 		    = start_date_original.getHours();
			var end_hour 			= end_date_original.getHours();

			if(start_hour >= 12){
				start_date_original.setDate(start_date_original.getDate() + 1);

			}
			var start_date = start_date_original.getFullYear() + '-' + (start_date_original.getMonth() + 1) + '-' + start_date_original.getDate();

			if(end_hour < 12){
				end_date_original.setDate(end_date_original.getDate() - 1);
			}

			var end_date = end_date_original.getFullYear() + '-' + (end_date_original.getMonth()+1) + '-' + end_date_original.getDate();
			
			$('#start_date_original').val(original.start_date);
			$('#start_date_drag').val(start_date);
			$('#end_date_drag').val(end_date);
												
		    return true;
		});		
			
		gantt.attachEvent("onAfterTaskDrag", function(id, mode, e){
		    var start_date = $('#start_date_drag').val();
		    var end_date   = $('#end_date_drag').val();
		    
		    var res_start = start_date.split("-");
		    var res_end   = end_date.split("-");
		    
		    var new_start_date = res_start[2] + '/' + res_start[1] + '/' + res_start[0];
		    var new_end_date   = res_end[2] + '/' + res_end[1] + '/' + res_end[0];


            bootbox.confirm('Cập nhật "'+new_start_date+' đến '+new_end_date+'"', function(result){
                if(result == true) {
                    $.ajax({
                        type: "POST",
                        url: BASE_URL + 'tasks/quickupdate',
                        data: {
                            id 		   : id,
                            date_start : start_date,
                            date_end   : end_date
                        },
                        success: function(string){
                            var res = $.parseJSON(string);
                            if(res.flag == 'false') {
                                toastr.error(res.msg, 'Lỗi');
                            }else {
                                toastr.success(res.msg, 'Thông báo');
                            }

                            gantt.clearAll();
                            load_task(1);
                        }
                    });
                }else{
                    gantt.clearAll();
                    load_task(1);
                }
            });

		});
		
		// link
		gantt.attachEvent("onBeforeLinkAdd", function(id,link){
			var task_id = link.source;
			if($.inArray(task_id, deny_items) == -1){
                bootbox.confirm("Bạn có chắc chắn không?", function(result){
                    if (result){
                        $.ajax({
                            type: "POST",
                            url: BASE_URL + 'tasks/link',
                            data: {
                                source   : link.source,
                                target   : link.target,
                                type     : parseInt(link.type)
                            },
                            success: function(string){
                                var res = $.parseJSON(string);

                                if(res.flag == 'true'){
                                    toastr.success(res.msg, 'Thông báo');
                                }else {
                                    toastr.warning(res.msg, 'Cảnh báo');
                                    gantt.deleteLink(id);
                                }
                            }
                        });
                        return true;
                    }else {
                        gantt.deleteLink(id);
                    }
                });

			}
			else{
                toastr.warning('Bạn không có quyền với chức năng này!', 'Cảnh báo');

				return false;
			}
		});
		
		gantt.attachEvent("onBeforeLinkDelete", function(id,item){
		    var task_id = item.source;
			if($.inArray(task_id, deny_items) == -1){
				$.ajax({
					type: "POST",
					url: BASE_URL + 'tasks/delete',
					data: {
						link_id   : id,

					},
					success: function(string){

				    }
				});
				return true;
			}
			else{
                toastr.warning('Bạn không có quyền với chức năng này!', 'Cảnh báo');

				return false;
			}
		});
	});

    function gantt_tooltip() {
        var element = $( "#btn_tooltip" );
        if(element.hasClass('active')) {
            element.removeClass('active').addClass('unactive');
            element.text('Bật Tooltip');
            gantt.templates.tooltip_text = function(start,end,task){
                return false;
            };
        }else {
            element.text('Tắt Tooltip');
            element.removeClass('unactive').addClass('active');
            gantt.templates.tooltip_text = function(start,end,task){
                return task.tooltip;
            };
        }
    }
	
	function load_task(page) {
		var keywords = $.trim($('#s_keywords').val());
		$.ajax({
			type: "POST",
			url: BASE_URL + 'tasks/danhsach/'+page,
			data: {
				keywords : keywords
			},
			beforeSend: function() {
	             loading();
	        },
			success: function(string){ 
			   close_loading();
			   var result 	  = $.parseJSON(string);
			   var data 	  = new Array();
			   var deny_items = new Array();
			   if(jQuery.isEmptyObject(result.ketqua) == false) {
				   $.each(result.ketqua, function(i, value) {
					   data[data.length] = value;
					});	   
				   
			   }else
				   data = new Array();

			   var task = new Object();
			   
			   task.data = data;
			   task.links = result.links;
			   
			   gantt.config.columns = [
			 				          {name:"text",       label:"Tiêu đề",  width: 350, tree:true},
			 						  {name:"start_date", label:"Bắt đầu",   align: "center" },
			 						  {name:"duration",   label:"Số ngày",   align: "center" },
			 						  {name:"add",        label:"",          width:44 }
			 				   ];
			   
			 
			   gantt.init("gantt_here");
			   gantt.parse(task);
		
			   drag_task = result.drag_task;
			  
			   if(result.deny.length) {
				   deny_items = result.deny;
				   $.each(deny_items, function(i, task_id) {
					   if(task_id != "0")
						   $('.gantt_row[task_id="'+task_id+'"] .gantt_last_cell').hide();
					   else
						   $('#gantt_here [column_id="add"]').remove();
				   });
			   }

				gantt.templates.grid_row_class = function( start, end, task ){
					task_id = task.id;
					if ($.inArray(task_id, deny_items) != -1)
					{
						 return "nested_task";
					}
					
				    return "";
				};
				
				//pagination
				var pagination  = result.pagination;
				pagination_html = load_pagination(pagination, 'gantt');
				
				//$('#count_project').text(result.count);
				$('#gantt_pagination').html(pagination_html);
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
				element = $( '#my_modal span[for="'+index+'"]' );
				element.prev().addClass('has-error');
				element.text(value);
			});	
		}else {
			toastr.success('Cập nhật thành công!', 'Thông báo');
            $('#my_modal').modal('toggle');
			load_task(1);
		}
	}

	function load_comment(task_id, page) {
		var url = BASE_URL + 'tasks/commentlist/'+page;
		$.ajax({
			type: "POST",
			url: url,
			data: {
				task_id : task_id,
			},
			success: function(string){
				var result = $.parseJSON(string);
				var items = result.items;
				if(items.length) {
					var html_string = load_tempate_comment(items);
					var pagination = load_pagination(pagination);
					
					$('#commentList').html(html_string);	
					$('#commentList').html(html_string);	
				}
		    }
		});
	}

	function xuly_tiendo(id) {
		var url = BASE_URL + 'tasks/xulytiendo';
		$.ajax({
			type: "GET",
			url: url,
			data: {
				id : id
			},
			success: function(html){
				  $('#quick-form').html(html);
				  $('#quick-form').show();
				  create_layer('quick');
		    }
		});
	}
	
	function note(id) {
		var url = BASE_URL + 'tasks/note';
		$.ajax({
			type: "POST",
			url: url,
			data: {
				id : id
			},
			success: function(html){
				  $('#quick-form').html(html);
				  $('#quick-form').show();
				  create_layer('quick');
		    }
		});
	}
	
	function comment() {
        $('#comment_content').removeClass('error');
		var checkOptions = {
				url : BASE_URL + 'tasks/addcomment',
		        dataType: "json",  
		        success: commentData
		    };
	    $("#task_form").ajaxSubmit(checkOptions);
    }

    function commentData(data) {
        if(data.flag == 'false') {
            if(data.type == 'content'){
                $('#comment_content').addClass('error');
                toastr.error(data.msg, 'Lỗi!');
            }
        }else {
            toastr.success(data.msg, 'Thông báo!');

            load_comment(data.task_id, 1);
            $('#comment_content').val('');
        }
	}
	
	function edit() {
		$('.btn-back').remove();
		var task_id = $('#task_id').val();
		var parent = $('#parent').val();

		var url = BASE_URL+'tasks/editcongviec?t=quick'

		$.ajax({
			type: "GET",
			url: url,
			data: {
				id 		   : task_id,
				parent 	   : parent
			},
			success: function(string){
				$('#my-form .arrord_nav').remove();
				$('#my-form .gantt_cal_larea').remove();
				$('#my-form').append(string);	
				
				$('#my-form .btn-save').html('<a href="javascript:;" onclick="edit_congviec();"><i class="fa fa-floppy-o"></i>Lưu</a>');

			    var frame_array = ['customer_list', 'xem_list', 'implement_list', 'create_task_list', 'pheduyet_task_list', 'progress_list'];
			    $.each(frame_array, function( index, value ) {
				   css_form(value);
				   press(value);
			    });
			    
			    // picker
				date_time_picker_field($('.datepicker'), JS_DATE_FORMAT);
				// end picker
		    }
		});
	}