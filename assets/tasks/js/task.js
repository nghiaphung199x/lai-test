	var taskId = null;
	var frame_id = null;
	var type = null;
	var deny_items = new Array();
	var drag_task = new Array();

	$( document ).ready(function() {
        gantt.templates.tooltip_text = function(start,end,task){
            return task.tooltip;
            //return false;
        };

        $( "#btn_tooltip" ).click(function() {
            gantt_tooltip();
        });

        // enable autocomplete
        var frame_array = ['customer_list', 'xem_list', 'implement_list', 'trangthai_list','create_task_list', 'pheduyet_task_list', 'progress_list'];
        $.each(frame_array, function( index, value ) {
            css_form(value);
            press(value);
        });

        // search process
        date_time_picker_field_report($('#adv_date_start_from'), JS_DATE_FORMAT+ " "+JS_TIME_FORMAT);
        date_time_picker_field_report($('#adv_date_start_to'), JS_DATE_FORMAT+ " "+JS_TIME_FORMAT);
        date_time_picker_field_report($('#adv_date_end_from'), JS_DATE_FORMAT+ " "+JS_TIME_FORMAT);
        date_time_picker_field_report($('#adv_date_end_to'), JS_DATE_FORMAT+ " "+JS_TIME_FORMAT);
        $('label[for="simple_radio"] span').click(function() {
            var label_element = $(this).closest('label');
            var element_radio = label_element.prev();
            element_radio.prop("checked", true);

        });

        // search
        var typingTimer;
        $('body').on('keyup','#search_keywords',function(){
            clearTimeout(typingTimer);
            var text = $(this).val();
            $('#s_keywords').val(text);
            typingTimer = setTimeout(startSearch, 500);
        });

        $('body').on('keydown','#search_keywords',function(){
            clearTimeout(typingTimer);
        });

        function startSearch () {
            load_task(1, 'clearAll');
        }

        $('body').on('change','#search_date_type',function(){
            var value                = $(this).val();

            var s_date_start_to      = $('#s_date_start_to');
            var s_date_start_from    = $('#s_date_start_from');
            var s_date_end_to        = $('#s_date_end_to');
            var s_date_end_from      = $('#s_date_end_from');
            var s_trangthai          = $('#s_trangthai');
            var s_date_start_radio   = $('#s_date_start_radio');
            var s_date_end_radio     = $('#s_date_end_radio');
            var s_status             = $('#s_status');
            var s_progress           = $('#s_progress');
            var s_customer           = $('#s_customer');
            var s_trangthai          = $('#s_trangthai');
            var s_implement          = $('#s_implement');
            var s_xem                = $('#s_xem');

            var s_trangthai_html     = $('#s_trangthai_html');
            var s_customer_html      = $('#s_customer_html');
            var s_implement_html     = $('#s_implement_html');
            var s_xem_html           = $('#s_xem_html');

            var data = {class: 'trangthai', value: 0, title: 'Chưa thực hiện'};
            var span_trangthai_0 = get_item_autocomplete(data);

            var data = {class: 'trangthai', value: 1, title: 'Đang thực hiện'};
            var span_trangthai_1 = get_item_autocomplete(data);

            //reset some element input
            s_trangthai.val('');
            s_trangthai_html.html('');
            s_customer.val('');
            s_customer_html.html('');
            s_implement.val('');
            s_implement_html.html('');
            s_xem.val('');
            s_xem_html.html('');

            switch(value) {
                case 'today':
                    var current_date = get_current_date();
                    s_date_start_to.val(current_date + ' 23:59');
                    s_date_end_from.val(current_date + ' 00:00');
                    s_trangthai.val('0,1');
                    s_trangthai_html.html(span_trangthai_0 + span_trangthai_1);

                    s_date_start_radio.val('complex');
                    s_date_end_radio.val('complex');
                    break;

                case 'weekend':
                    var firstDay = get_first_date_of_current_weekend();
                    var lastDay = get_last_date_of_current_weekend();

                    s_date_start_to.val(lastDay + ' 23:59');
                    s_date_end_from.val(firstDay + ' 00:00');
                    s_trangthai.val('0,1');
                    s_trangthai_html.html(span_trangthai_0 + span_trangthai_1);

                    s_date_start_radio.val('complex');
                    s_date_end_radio.val('complex');

                    break;

                case 'month':
                    var firstDay = get_first_date_of_current_month();
                    var lastDay = get_last_date_of_current_month();

                    s_date_start_to.val(lastDay + ' 59:59');
                    s_date_end_from.val(firstDay + ' 00:00');
                    s_trangthai.val('0,1');
                    s_trangthai_html.html(span_trangthai_0 + span_trangthai_1);

                    s_date_start_radio.val('complex');
                    s_date_end_radio.val('complex');
                    break;

                case 'year':
                    var firstDay = get_first_date_of_current_year();
                    var lastDay = get_last_date_of_current_year();

                    s_date_start_to.val(lastDay + ' 59:59');
                    s_date_end_from.val(firstDay + ' 00:00');
                    s_trangthai.val('0,1');
                    s_trangthai_html.html(span_trangthai_0 + span_trangthai_1);

                    s_date_start_radio.val('complex');
                    s_date_end_radio.val('complex');
                    break;

                default:
                    s_date_start_to.val('');
                    s_date_end_from.val('');
                    s_trangthai.val('');
                    s_trangthai_html.html('');

                    s_date_start_radio.val('simple');
                    s_date_end_radio.val('simple');
            }
            load_task(1, 'clearAll');
        });

        //advance search click
        $('body').on('click','#btn_advance_project',function(){
            set_project_form_input();
            $("#advance_project_search").modal();
        });

        $('#advance_project_search').on('hidden.bs.modal', function () {
            reset_form();
        })

        $('body').on('click','#btn_p_search_advance',function(){
            set_project_hidden_input();
            load_task(1, 'clearAll');
            $('#advance_project_search').modal('toggle');
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
				$('.manage-row-options').addClass('hidden');
            else
                $('.manage-row-options').removeClass('hidden');
	    });
		
		// task pagination
		$('body').on('click', '#pagination_top a', function(){
			var page = $(this).attr('data-page');
			load_task(page, 'clearAll');
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
                    parent : parent
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

                            load_task(1, 'clearAll');
                        }
                    });
                }else{
                    load_task(1, 'clearAll');
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
	
	function load_task(page, type) {
        var data = new Object();

        var s_keywords        = $('#s_keywords');
        var s_date_start_from = $('#s_date_start_from');
        var s_date_start_to   = $('#s_date_start_to');
        var s_date_end_from   = $('#s_date_end_from');
        var s_trangthai       = $('#s_trangthai');
        var s_date_end_to     = $('#s_date_end_to');
        var s_customer        = $('#s_customer');
        var s_implement       = $('#s_implement');
        var s_xem             = $('#s_xem');

        data.keywords         = $.trim(s_keywords.val());
        data.date_start_from  = $.trim(s_date_start_from.val());
        data.date_start_to    = $.trim(s_date_start_to.val());
        data.date_end_from    = $.trim(s_date_end_from.val());
        data.date_end_to      = $.trim(s_date_end_to.val());
        data.trangthai        = $.trim(s_trangthai.val());
        data.customers        = $.trim(s_customer.val());
        data.implement        = $.trim(s_implement.val());
        data.xem              = $.trim(s_xem.val());

        if(data.trangthai == '0')
            data.trangthai = 'zero';

		$.ajax({
			type: "POST",
			url: BASE_URL + 'tasks/danhsach/'+page,
			data: data,
			beforeSend: function() {
	             loading();
	        },
			success: function(string){
			   close_loading();

               if(type == 'clearAll')
                   gantt.clearAll();

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
			load_task(1,'clearAll');
		}
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


    function set_project_hidden_input() {
        var search_keywords        = $('#search_keywords');
        var search_date_type       = $('#search_date_type');

        var s_keywords             = $('#s_keywords');

        var s_date_start           = $('#s_date_start');
        var s_date_start_radio     = $('#s_date_start_radio');
        var s_date_start_from      = $('#s_date_start_from');
        var s_date_start_to        = $('#s_date_start_to');

        var s_date_end             = $('#s_date_end');
        var s_date_end_radio       = $('#s_date_end_radio');
        var s_date_end_from        = $('#s_date_end_from');
        var s_date_end_to          = $('#s_date_end_to');

        var s_trangthai            = $('#s_trangthai');
        var s_customer             = $('#s_customer');
        var s_implement            = $('#s_implement');
        var s_xem                  = $('#s_xem');

        var s_trangthai_html       = $('#s_trangthai_html');
        var s_customer_html        = $('#s_customer_html');
        var s_implement_html       = $('#s_implement_html');
        var s_xem_html             = $('#s_xem_html');

        //set values for each elements
        var adv_date_start_radio_value = $('[name="adv_date_start_radio"]:checked').val();

        s_keywords.val($('#adv_name').val());

        // date_start
        s_date_start_radio.val(adv_date_start_radio_value);
        if(adv_date_start_radio_value == 'simple') {
            var adv_date_start_value = $('#adv_date_start').val();
            var date = get_two_dates(adv_date_start_value);

            s_date_start_from.val(date.date_1);
            s_date_start_to.val(date.date_2);
            s_date_start_radio.val(adv_date_start_radio_value);
        }else {
            s_date_start.val('all');
            s_date_start_from.val($('#adv_date_start_from').val());
            s_date_start_to.val($('#adv_date_start_to').val());
        }
        s_date_start.val($('#adv_date_start').val());

        // date_end
        var adv_date_end_radio_value = $('[name="adv_date_end_radio"]:checked').val();
        s_date_end_radio.val(adv_date_end_radio_value);
        if(adv_date_end_radio_value == 'simple') {
            var adv_date_end_value = $('#adv_date_end').val();
            var date = get_two_dates(adv_date_end_value);

            s_date_end_from.val(date.date_1);
            s_date_end_to.val(date.date_2);
            s_date_end_radio.val(adv_date_end_radio_value);
        }else {
            s_date_end.val('all');
            s_date_end_from.val($('#adv_date_end_from').val());
            s_date_end_to.val($('#adv_date_end_to').val());
        }
        s_date_end.val($('#adv_date_end').val());

        // trangthai
        var item             = new Array();
        var item_string      = '';
        var item_html        = new Array();
        var item_html_string = '';
        var span_trangthai_item = $('#trangthai_list .item');
        if(span_trangthai_item.length) {
            $( span_trangthai_item ).each(function() {
                var span_element  = $(this);
                item[item.length] = span_element.find('.trangthai').val();
                item_html[item_html.length] = span_element[0].outerHTML;
            });
        }

        item_string      = item.join();
        item_html_string = item_html.join('');
        s_trangthai.val(item_string);
        s_trangthai_html.html(item_html_string);

        // customer
        var item             = new Array();
        var item_string      = '';
        var item_html        = new Array();
        var item_html_string = '';

        var span_customer_item = $('#customer_list .item');
        if(span_customer_item.length) {
            $( span_customer_item ).each(function() {
                var span_element  = $(this);
                item[item.length] = span_element.find('.customer').val();
                item_html[item_html.length] = span_element[0].outerHTML;
            });
        }

        item_string      = item.join();
        item_html_string = item_html.join('');
        s_customer.val(item_string);
        s_customer_html.html(item_html_string);

        // implement
        var item             = new Array();
        var item_string      = '';
        var item_html        = new Array();
        var item_html_string = '';

        var span_implement_item = $('#implement_list .item');
        if(span_implement_item.length) {
            $( span_implement_item ).each(function() {
                var span_element  = $(this);
                item[item.length] = span_element.find('.implement').val();
                item_html[item_html.length] = span_element[0].outerHTML;
            });
        }

        item_string      = item.join();
        item_html_string = item_html.join('');
        s_implement.val(item_string);
        s_implement_html.html(item_html_string);

        // xem
        var item             = new Array();
        var item_string      = '';
        var item_html        = new Array();
        var item_html_string = '';

        var span_xem_item = $('#xem_list .item');
        if(span_xem_item.length) {
            $( span_xem_item ).each(function() {
                var span_element  = $(this);
                item[item.length] = span_element.find('.xem').val();
                item_html[item_html.length] = span_element[0].outerHTML;
            });
        }

        item_string      = item.join();
        item_html_string = item_html.join('');
        s_xem.val(item_string);
        s_xem_html.html(item_html_string);

        // others
        search_keywords.val('');
        search_date_type.val('0');
    }

    function set_project_form_input() {
        var s_keywords           = $('#s_keywords');

        var s_date_start_radio   = $('#s_date_start_radio');
        var date_start_value     = s_date_start_radio.val();
        var s_date_start         = $('#s_date_start');
        var s_date_start_from    = $('#s_date_start_from');
        var s_date_start_to      = $('#s_date_start_to');

        var s_date_end_radio     = $('#s_date_end_radio');
        var date_end_value       = s_date_end_radio.val();
        var s_date_end           = $('#s_date_end');
        var s_date_end_from      = $('#s_date_end_from');
        var s_date_end_to        = $('#s_date_end_to');

        var s_trangthai_html     = $('#s_trangthai_html');
        var s_customer_html      = $('#s_customer_html');
        var s_implement_html     = $('#s_implement_html');
        var s_xem_html           = $('#s_xem_html');

        $('[name="adv_date_start_radio"]').filter('[value='+date_start_value+']').prop('checked', true);
        if(date_start_value == 'simple') {
            $('#adv_date_start').val(s_date_start.val());
        }else {
            var s_date_start_from_value = s_date_start_from.val();
            if(s_date_start_from_value != '') {
                $('#adv_date_start_from_formatted').val(convert_date(s_date_start_from_value));
                $('#adv_date_start_from').val(s_date_start_from_value);
            }

            var s_date_start_to_value = s_date_start_to.val();
            if(s_date_start_to_value != '') {
                $('#adv_date_start_to_formatted').val(convert_date(s_date_start_to_value));
                $('#adv_date_start_to').val(s_date_start_to_value);
            }
        }

        $('[name="adv_date_end_radio"]').filter('[value='+date_end_value+']').prop('checked', true);
        if(date_end_value == 'simple') {
            $('#adv_date_end').val(s_date_end.val());
        }else {
            var s_date_end_from_value = s_date_end_from.val();
            if(s_date_end_from_value != '') {
                $('#adv_date_end_from_formatted').val(convert_date(s_date_end_from_value));
                $('#adv_date_end_from').val(s_date_end_from_value);
            }

            var s_date_end_to_value = s_date_end_to.val();
            if(s_date_end_to_value != '') {
                $('#adv_date_end_to_formatted').val(convert_date(s_date_end_to_value));
                $('#adv_date_end_to').val(s_date_end_to_value);
            }
        }

        $('#adv_name').val(s_keywords.val());

        var html = s_trangthai_html.html();
        $(html).insertBefore( "#trangthai_result" );

        html = s_customer_html.html();
        $(html).insertBefore( "#customer_result" );

        html = s_implement_html.html();
        $(html).insertBefore( "#implement_result" );

        html = s_xem_html.html();
        $(html).insertBefore( "#xem_result" );
    }

    function reset_form() {
        $('input[name=adv_date_start_radio][value="simple"]').prop('checked', true);
        $('#adv_date_start').val('all');
        $('#adv_date_start_from_formatted').val('');
        $('#adv_date_start_from').val('');
        $('#adv_date_start_to_formatted').val('');
        $('#adv_date_start_to').val('');

        $('input[name=adv_date_end_radio][value="simple"]').prop('checked', true);
        $('#adv_date_end').val('all');
        $('#adv_date_end_from_formatted').val('');
        $('#adv_date_end_from').val('');
        $('#adv_date_end_to_formatted').val('');
        $('#adv_date_end_to').val('');

        $('#adv_name').val('');
        $('#trangthai_list span.item').remove();
        $('#customer_list span.item').remove();
        $('#implement_list span.item').remove();
        $('#xem_list span.item').remove();

    }

