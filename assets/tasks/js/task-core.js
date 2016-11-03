// multi modal bootstrap
$(document).on('show.bs.modal', '.modal', function (event) {
    var zIndex = 9999040 + (10 * $('.modal:visible').length);
    $(this).css('z-index', zIndex);
    setTimeout(function() {
        $('.modal-backdrop').not('.modal-stack').css('z-index', zIndex - 1).addClass('modal-stack');
    }, 0);
});

$( document ).ready(function() {
	var array_list = ['progress', 'file'];
	// phân trang file, progress
	$.each( array_list, function( key, keyword ) {
		if(keyword == 'progress') {
			var manager_div = 'progress_manager';
		}else if(keyword == 'file') {
			var manager_div   = 'file_manager';
		}
		
		$('body').on('click','#'+manager_div+' .pagination a',function(){
			var page = $(this).attr('data-page');
			
			load_list(keyword, page);
		});
	});

    // comment
    $('body').on('click','#btnComment',function(){
        comment();
        return false;
    });

	// progress
	$('body').on('click','#progress_manager .panel-title span.tieude',function(){
		$('#progress_manager .panel-title span.tieude').removeClass('active');
		var content_id = $(this).attr('data-id');
		$('#progress_manager .table_list').hide();

		$('#'+content_id).css('display', 'block');
			
		$(this).addClass('active');
		
		if(content_id == 'progress_danhsach') {
			load_list('progress', 1);
		}else if(content_id == 'request_list')
			load_list('request', 1);
		else if(content_id == 'pheduyet_list'){
			load_list('pheduyet', 1);
		}
		
		// task search = 0
		$('#s_task_id').val(0);
	    countTiendo();
	});
	
	// search
	$('body').on('change','#s_task_id',function(){
		var content_id = $('#progress_manager span.tieude.active').attr('data-id');
		if(content_id == 'progress_danhsach') {
			load_list('progress', 1);
		}else if(content_id == 'request_list')
			load_list('request', 1);
		else if(content_id == 'pheduyet_list'){
			load_list('pheduyet', 1);
		}
	});
	
	// sort
	$('body').on('click','.my-table th',function(){
		var thElement = $('.my-table th');
		var attr = $(this).attr('data-field');
		if (typeof attr !== typeof undefined && attr !== false) {
		   if($(this).hasClass('header')) {
			   if($(this).hasClass('headerSortUp')){
				   $(this).removeClass('headerSortUp');
				   $(this).addClass('headerSortDown');
			   }else {
				   $(this).removeClass('headerSortDown');
				   $(this).addClass('headerSortUp');
			   }   
		   }else {
			   thElement.removeClass('header');
			   thElement.removeClass('headerSortUp');
			   thElement.removeClass('headerSortDown');
			   $(this).addClass('header headerSortUp');
		   }
		   
		   var elementTable = $(this).closest('.my-table');
		   var table_id     = elementTable.attr('id');
		   
		   switch (table_id){
		      case 'template_table' : {
		    	 load_list('template', 1);
		         break;
		      }

		      case 'project_table' : {
			    	 load_list('project', 1);
			         break;
			  }
		   }
		}
	});

    $( ".date_time" ).focus(function() {
        var range_element = $(this).closest('.report_date_range_complex');
        var radio = range_element.find('input[type="radio"]');
        radio.prop("checked", true);
    });
});

function load_template_project(items) {
	if(items.length) {
		 var string = new Array();
		 $.each(items, function( index, value ) {
			  var id      	   = value.id;
			  var name         = value.name;
			  var prioty       = value.prioty;
			  var sort         = value.sort;
			  var modified     = value.modified;
			  var username     = value.username;
			  
			  sort = '<span class="sort" data-id="'+id+'" data-value="'+sort+'">'+sort+'</span>';
	 
			  string[string.length] = '<tr style="cursor: pointer;">'
										+'<td class="cb"><input type="checkbox" name="template_'+id+'" id="templaet_'+id+'" value="'+id+'" class="file_checkbox"><label for="template_'+id+'"><span></span></label></td>'
										+'<td class="cb">'+name+'</td>'
										+'<td class="center cb">'+prioty+'</td>'
										+'<td class="center">'+sort+'</td>'
										+'<td class="center cb">'+modified+'</td>'
										+'<td class="center cb">'+username+'</td>'
										+'<td class="center" style="padding: 4px;">'
											+'<a href="javascript:;" onclick="edit_congviec('+id+');">Sửa</a>'
										+'</td>'
									+' </tr>	'; 
		 });
		 
		 string = string.join("");	
	}else
		var string = '<tr style="cursor: pointer;"><td colspan="5"><div class="col-log-12" style="text-align: center; color: #efcb41;">Không có dữ liệu hiển thị</div></td></tr>';

	 return string;
}

function load_template_template(items) {
	if(items.length) {
		 var string = new Array();
		 $.each(items, function( index, value ) {
			  var id      	   = value.id;
			  var name         = value.name;
			  var modified     = value.modified;
			  var username     = value.username;
			  var linkEdit 	   = BASE_URL + 'tasks/editTemplate/'+id
			  
			  string[string.length] = '<tr style="cursor: pointer;">'
										+'<td class="cb"><input type="checkbox" name="template_'+id+'" id="templaet_'+id+'" value="'+id+'" class="file_checkbox"><label for="template_'+id+'"><span></span></label></td>'
										+'<td class="cb">'+name+'</td>'
										+'<td class="center cb">'+modified+'</td>'
										+'<td class="center cb">'+username+'</td>'
										+'<td class="center" style="padding: 4px;">'
											+'<a href="'+linkEdit+'">Sửa</a>'
										+'</td>'
									+' </tr>	';
		 });
		 
		 string = string.join("");	
	}else
		var string = '<tr style="cursor: pointer;"><td colspan="5"><div class="col-log-12" style="text-align: center; color: #efcb41;">Không có dữ liệu hiển thị</div></td></tr>';

	 return string;
}

function load_template_task_child(items) {
	if(items.length) {
		 var string = new Array();
		 $.each(items, function( index, value ) {
			  var id      	   = value.id;
			  var start_date   = value.start_date;
			  var end_date     = value.end_date;
			  var finish_date  = value.finish_date;
			  var name         = '<a href="javascript:;" onclick="edit_task_grid('+id+')">'+value.name+'</a>';
			  var duration     = value.duration;
			  var percent      = value.percent;
			  var progress     = value.progress;
			  var parent       = value.parent;
			  var p_color      = value.p_color;
			  var n_color      = value.color;

             if(value.hasOwnProperty("implement")){
                 var implement    = value.implement;
             }else
                 var implement     = '';

             if(value.hasOwnProperty("space")){
                 var space    = value.space;
             }else
                 var space     = '';

			  var prioty       = value.prioty;
			  var trangthai    = value.trangthai;
			  var note    	   = value.note;

			  var positive = parseFloat(progress) * 100;
			  var negative = 100 - positive;

			  string[string.length] = '<tr>'
										+'<td>'+space+name+'</td>'
										+'<td align="center">'+prioty+'</td>'
										+'<td align="center">'+start_date+'</td>'
										+'<td align="center">'+end_date+'</td>'
										+'<td align="center">'
											+'<div class="clearfix">'
												+'<div class="progress-bar" style="float: left;">'
												  +'<div class="bar positive" style="width: '+positive+'%; background: '+p_color+'">'
												  +'</div>'
												  +'<div class="bar negative" style="width: '+negative+'%; background: '+n_color+'">'
												  +'</div>'
												  +'<span>'+positive+'%</span>'
												+'</div>'
												+'<div class="progress-text">'+note+'</div>'
											+'</div>'
										+'</td>'
										+'<td align="center">'+trangthai+'</td>'
										+'<td align="center">'+implement+'</td>'
									+'</tr>';

		 });

		 string = string.join("");
	}else
		var string = '<tr style="cursor: pointer;"><td colspan="7"><div class="col-log-12" style="text-align: center; color: #efcb41;">Không có dữ liệu hiển thị</div></td></tr>';

	 return string;
}

function load_template_project_grid(items) {
	if(items.length) {
		 var string = new Array();
		 $.each(items, function( index, value ) {
			  var id      	   = value.id;
			  var start_date   = value.start_date;
			  var end_date     = value.end_date;
			  var finish_date  = value.finish_date;
			  var name         = '<a href="javascript:;" onclick="edit_task_grid('+id+');">'+value.name+'</a>';
			  var duration     = value.duration;
			  var percent      = value.percent;
			  var progress     = value.progress;
			  var parent       = value.parent;
			  var p_color      = value.p_color;
			  var n_color      = value.color;
			  var implement    = value.implement;
			  var prioty       = value.prioty;
			  var trangthai    = value.trangthai;
			  var note    	   = value.note;
			  
			  var positive = parseFloat(progress) * 100;
			  var negative = 100 - positive;
	 
			  string[string.length] = '<tr data-tree="'+id+'">'
										+'<td class="hidden-print" style="width: 25px; text-align: center;"><a href="javascript:;" class="expand_all">-</a></td>'
										+'<td>'+name+'</td>'
										+'<td align="center">'+prioty+'</td>'
										+'<td align="center">'+start_date+'</td>'
										+'<td align="center">'+end_date+'</td>'
										+'<td align="center">'
											+'<div class="clearfix">'
												+'<div class="progress-bar" style="float: left;">'
												  +'<div class="bar positive" style="width: '+positive+'%; background: '+p_color+'">'
												  +'</div>'
												  +'<div class="bar negative" style="width: '+negative+'%; background: '+n_color+'">'
												  +'</div>'
												  +'<span>'+positive+'%</span>'
												+'</div>'
												+'<div class="progress-text">'+note+'</div>'
											+'</div>'
										+'</td>'
										+'<td align="center">'+trangthai+'</td>'
										+'<td align="center">'+implement+'</td>'
									+'</tr>'
									+'<tr data-parent="'+id+'" data-content="0" style="display: none;">'
									+'<td colspan="8" class="innertable" style="display: table-cell;">'
                                        +'<div class="clearfix">'
                                          +'<div class="col-xs-12 col-md-6 pull-right" style="padding-left: 0; padding-right: 0">'
                                             +' <select class="form-control search_date_type">'
                                                 +'<option value="0" selected="selected">-- Thời gian --</option>'
                                                 +'<option value="today">Trong ngày</option>'
                                                 +'<option value="weekend">Trong tuần</option>'
                                                 +'<option value="month">Trong tháng</option>'
                                                 +'<option value="year">Trong năm</option>'

                                             +' </select>'
                                             +' </div>'
                                             +'<div class="col-xs-12 col-md-6 pull-left" style="padding-left: 0; padding-right: 0;">'
                                                 +'<input type="text" class="form-control ui-autocomplete-input search_keywords" value="" placeholder="Tìm kiếm công việc" >'
                                                 +'<button name="submitf" class="btn btn-primary btn-lg submitf" data-id="'+id+'" data-name="'+value.name+'">Nâng cao</button>'
                                                 +'<button name="statistic" class="btn btn-primary btn-lg statistic" data-id="'+id+'" data-name="'+value.name+'">Thống kê</button>'
                                                 +'<input type="hidden" class="s_keywords s_input_filter" value="" />'
                                                 +'<input type="hidden" class="s_date_start s_input_filter" value="all" />'
                                                 +'<input type="hidden" class="s_date_start_radio s_input_filter" value="simple" />'
                                                 +'<input type="hidden" class="s_date_start_from s_input_filter" value="" />'
                                                 +'<input type="hidden" class="s_date_start_to s_input_filter" value="" />'
                                                 +'<input type="hidden" class="s_date_end s_input_filter" value="all" />'
                                                 +'<input type="hidden" class="s_date_end_radio s_input_filter" value="simple" />'
                                                 +'<input type="hidden" class="s_date_end_from s_input_filter" value="" />'
                                                 +'<input type="hidden" class="s_date_end_to s_input_filter" value="" />'
                                                 +'<input type="hidden" class="s_trangthai s_input_filter" value="" />'
                                                 +'<input type="hidden" class="s_customer s_input_filter" value="" />'
                                                 +'<input type="hidden" class="s_implement s_input_filter" value="" />'
                                                 +'<input type="hidden" class="s_xem s_input_filter" value="" />'
                                                 +'<input type="hidden" class="s_status s_input_filter" value="-1,0,1,2" />'
                                                 +'<input type="hidden" class="s_progress s_input_filter" value="-1,0,1,2" />'
                                                 +'<div class="s_trangthai_html" style="display: none;"></div>'
                                                 +'<div class="s_customer_html" style="display: none;"></div>'
                                                 +'<div class="s_implement_html" style="display: none;"></div>'
                                                 +'<div class="s_xem_html" style="display: none;"></div>'
                                             +'</div>'
                                         +'</div>'
										+'<table class="table table-bordered" id="task_childs_'+id+'" data-content="0">'
											+'<thead>'
												+'<tr align="center" style="font-weight:bold">'
													+'<td align="center" data-field="name">Tên công việc</td>'
													+'<td align="center" style="width: 8%;" data-field="prioty">Ưu tiên</td>'
													+'<td align="center" style="width: 100px;" data-field="date_start">Bắt đầu</td>'
													+'<td align="center" style="width: 100px;" data-field="date_end">Kết thúc</td>'
													+'<td align="center" style="width: 256px;" data-field="progress">Tiến độ</td>'
													+'<td align="center" style="width: 10%;" data-field="trangthai">Tình trạng</td>'
													+'<td align="center" style="width: 20%;">Phụ trách</td>'
												+'</tr>'
											+'</thead>'
											+'<tbody>'
											+'</tbody>'
										+'</table>'
									+'</tr>';

		 });
		 
		 string = string.join("");	
	}else
		var string = '<tr style="cursor: pointer;"><td colspan="8"><div class="col-log-12" style="text-align: center; color: #efcb41;">Không có dữ liệu hiển thị</div></td></tr>';

	 return string;
}

function load_template_personal(items) {
    if(items.length) {
        var string = new Array();
        $.each(items, function( index, value ) {
            var id      	 = value.id;
            var start_date   = value.start_date;
            var end_date     = value.end_date;
            var finish_date  = value.finish_date;
            var name         = '<a href="javascript:;" onclick="update_personal_task(\'edit\', \'personal\', '+id+')">'+value.name+'</a>';
            var duration     = value.duration;
            var percent      = value.percent;
            var progress     = value.progress;
            var parent       = value.parent;
            var p_color      = value.p_color;
            var n_color      = value.color;
            var prioty       = value.prioty;
            var trangthai    = value.trangthai;
            var note    	   = value.note;

            var positive = parseFloat(progress) * 100;
            var negative = 100 - positive;

            string[string.length] =    '<tr>'
                                            +'<td class="center cb">'
                                                +'<input type="checkbox" id="task_'+id+'" class="file_checkbox" value="'+id+'"><label for="task_'+id+'"><span></span></label>'
                                            +'</td>'
                                            +'<td>'+name+'</td>'
                                            +'<td class="center cb">'+prioty+'</td>'
                                            +'<td class="center cb">'+start_date+'</td>'
                                            +'<td class="center cb">'+end_date+'</td>'
                                            +'<td class="center">'
                                                +'<div class="clearfix">'
                                                    +'<div class="progress-bar" style="float: left;">'
                                                        +'<div class="bar positive" style="width: '+positive+'%; background: '+p_color+'">'
                                                        +'</div>'
                                                        +'<div class="bar negative" style="width: '+negative+'%; background: '+n_color+'">'
                                                        +'</div>'
                                                        +'<span>'+positive+'%</span>'
                                                        +'</div>'
                                                    +'<div class="progress-text">'+note+'</div>'
                                                +'</div>'
                                            +'</td>'
                                            +'<td align="center">'+trangthai+'</td>'
                                       +'</tr>';
        });

        string = string.join("");
    }else
        var string = '<tr style="cursor: pointer;"><td colspan="8"><div class="col-log-12" style="text-align: center; color: #efcb41;">Không có dữ liệu hiển thị</div></td></tr>';

    return string;
}

function load_pagination(pagination, template) {
	var linkTask = BASE_URL + 'tasks/'
	if(jQuery.type(pagination) == 'object') {
		var string = new Array();
		$.each( pagination, function( key, page ) {
			if(template == 'comment') {
				if(key == 'prev')
					string[string.length] = '<li><a class="none fn-prev fn-page" data-page="'+page+'" href="javascript:;">&lt;</a></li>';
				else if(key == 'next')
					string[string.length] = '<li><a class="fn-next fn-page" data-page="'+page+'" href="javascript:;">&gt;</a></li>';
				else if(key == 'current')
					string[string.length] = '<li><a class="fn-page active" data-page="'+page+'" href="javascript:;">'+page+'</a></li>';
				else 
					string[string.length] = '<li><a class="fn-page" data-page="'+page+'" href="javascript:;">'+page+'</a></li>';

			}else if(template == 'gantt') {
				if(key == 'prev'){
					string[string.length] = '<a href="javascript:;" data-page="'+page+'" rel="prev">&lt;</a>';
				}else if(key == 'next'){
					string[string.length] = '<a href="javascript:;" data-page="'+page+'" rel="next">&gt;</a>';
				}else if(key == 'current')
					string[string.length] = '<strong>'+page+'</strong>';
				else {
					string[string.length] = '<a href="javascript:;" data-page="'+page+'" >'+page+'</a>';
				}

			}else {
				if(key == 'prev')
					string[string.length] = '<a href="javascript:;" data-page="'+page+'">&lt;</a>';
				else if(key == 'next')
					string[string.length] = '<a href="javascript:;" data-page="'+page+'">&gt;</a>';
				else if(key == 'current')
					string[string.length] = '<strong>'+page+'</strong>';
				else 
					string[string.length] = '<a href="javascript:;" data-page="'+page+'">'+page+'</a>';
		
			}
		});
		
		switch (template)
		{
		    case 'comment' : {
		    	string = string.join("");
				string = '<ul>'+string+'</ul>';
		        break;
		    }
		    case 'gantt' : {
				string = string.join("");
				string = '<div class="pagination pagination-top hidden-print  text-center" id="pagination_top">'+string+'</div>';
			
		        break;
		    }
		    default : {
				string = string.join("");
				string = '<div class="text-center"><div class="pagination hidden-print alternate text-center">' + string + '</div></div>';
		    }
		}
	
		return string;
	}else
		return '';
}

function loading(keyword) {
	if(keyword != 'file' && keyword != 'file-personal'){
		if(keyword == 'project')
			$("#loading_3").show();
		else{
            var key_arr = new Array('progress', 'pheduyet', 'request', 'progress-personal', 'file-personal');
            if(key_arr.indexOf(keyword) != -1)
                $('#task_form #loading_1').show();
            else
                $("#loading_1").show();
        }

	}else{
		$("#loading_2").show();
	}
		 
}

function close_loading(keyword) {
	if(keyword != 'file' && keyword != 'file-personal') {
		if(keyword == 'project')
			$("#loading_3").hide();
		else{
            var key_arr = new Array('progress', 'pheduyet', 'request', 'progress-personal', 'file-personal');
            if(key_arr.indexOf(keyword) != -1)
                $('#task_form #loading_1').hide();
            else
                $("#loading_1").hide();
        }

	}else
		 $("#loading_2").hide();
}

function create_layer(type) {
	if(type == 'quick')
		var classLayer = 'overlay2';
	else
		var classLayer = 'overlay1';
	
	if($('.'+classLayer).length)
		$('.'+classLayer).css('display', 'inline-block');
	else {
		$( "body" ).append( '<div class="'+classLayer+'" style="display: inline-block;"></div>' );
	}
}	

function close_layer(type) {
	if(type == 'quick')
		var classLayer = 'overlay2';
	else
		var classLayer = 'overlay1';

	$('.'+classLayer).remove();
}

function foucs(obj) {
	$(obj).find('.quick_search').focus();
}

function doneTyping(frame_id) {
    switch(frame_id) {
        case 'customer_list':
            var url = BASE_URL + 'tasks/customerList';
            break;
        case 'trangthai_list':
            var url = BASE_URL + 'tasks/trangthaiList';
            break;
        default:
            var url = BASE_URL + 'tasks/userList';
    }

	$('#'+frame_id+' .result').html('');
	$('#'+frame_id+' .result').hide();
	var keywords = $.trim($('#'+frame_id+' .quick_search').val());

	if (keywords) {
		$.ajax({
			type: "POST",
			url: url,
			data: {
				keywords : keywords
			},
			success: function(string){
				array = $.parseJSON(string);
				css_form(frame_id);
				if(array.length) {
					var html = new Array();
					$.each(array, function( index, value ) {
						if(frame_id == 'customer_list' || frame_id == 'trangthai_list')
							html[html.length] = '<li><a href="javascript:;" data-id="'+value.id+'" data-name="'+value.name+'" onclick="add_item(this, \''+frame_id+'\');">'+value.name+'</a></li>';
						else
							html[html.length] = '<li><a href="javascript:;" data-id="'+value.id+'" data-name="'+value.name+'" onclick="add_item(this, \''+frame_id+'\');">'+value.name+' - '+value.fullname+'</a></li>';
					});

					html = html.join('');
					html = '<ul class="list">'+html+'</ul>'; 

					$('#'+frame_id+' .result').html(html);
					$('#'+frame_id+' .result').show();
				}
		    }
		});
	}
}

function cancel(typeP, type) {
	if(typeP == 'quick') {
		$('#quick-form').html('');
		$('#quick-form').hide();

		close_layer('quick');
	}else {
        $('#my_modal').modal('toggle');
	}
}

function add_item(obj, frame_id) {
    var item_name = $(obj).attr('data-name');
    var item_id   = $(obj).attr('data-id');
    var array = new Array();
    array['customer_list'] 	    = 'customer';
    array['trangthai_list'] 	= 'trangthai';
    array['xem_list'] 		    = 'xem';
    array['implement_list']     = 'implement';
    array['create_task_list']   = 'create_task';
    array['pheduyet_task_list'] = 'pheduyet_task';
    array['progress_list'] 		= 'progress_task';

    var detect_element 	 = $(obj).parents('.result').prev();
    var result_frame   	 = $(obj).parents('.result');
    var class_name 	 	 = array[frame_id];
    var object_section   = $(obj).closest('.x-select-users');
    var span_element     = object_section.find('#'+class_name+'_'+item_id);

    if(!span_element.length) {
        var html = '<span class="item"><input type="hidden" name="'+class_name+'[]" class="'+class_name+'" id="'+class_name+'_'+item_id+'" value="'+item_id+'"><a>'+item_name+'</a>&nbsp;&nbsp;<span class="x" onclick="delete_item(this);"></span></span>';
        $( html ).insertBefore( detect_element );
        result_frame.hide();
        detect_element.val('');
        detect_element.focus();
    }


//    if(!$('#'+class_name+'_'+item_id).length){
//        var html = '<span class="item"><input type="hidden" name="'+class_name+'[]" class="'+class_name+'" id="'+class_name+'_'+item_id+'" value="'+item_id+'"><a>'+item_name+'</a>&nbsp;&nbsp;<span class="x" onclick="delete_item(this);"></span></span>';
//        $( html ).insertBefore( detect_element );
//        result_frame.hide();
//        detect_element.val('');
//        detect_element.focus();
//    }
}

function delete_item(obj) {
    $(obj).parents('span.item').remove();
}

function press(frame_id) {
   if($('#'+frame_id).length) {
	   var typingTimer;                
	   var doneTypingInterval = 1000;  

	   $('#'+frame_id+' .quick_search').on('keyup', function () {
		   clearTimeout(typingTimer);
		   typingTimer = setTimeout(function(){
			   doneTyping(frame_id)
		    },doneTypingInterval);

		 });

	   //on keydown, clear the countdown 
	   $('#'+frame_id+' .quick_search').on('keydown', function () {
	   	  clearTimeout(typingTimer);
	   });
   }
}

function css_form(obj_id) {
    if($('#'+obj_id).length) {
	   var top = $("#"+obj_id+" .quick_search").offset().top - $("#"+obj_id).offset().top + 20;

	   var styles = {
	      top : top + 'px'
	   };
	   
	   $("#"+obj_id+" .result").css( styles );	
    }
}

function reset_error() {
	$('#my_modal .form-control').removeClass('has-error');
	$('#my_modal span.errors').text('');
	$('#quick-form .form-control').removeClass('has-error');
	$('#quick-form span.errors').text('');
}

function load_comment(task_id, page) {
    var url = BASE_URL + 'tasks/commentlist/'+page;
    $.ajax({
        type: "POST",
        url: url,
        data: {
            task_id : task_id
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

function load_template_file(items) {
	if(items.length) {
		 var string = new Array();
		 $.each(items, function( index, value ) {
			  var id      		= value.id;
			  var name 			= value.name;
			  var link 			= value.link;
			  var file_name 	= value.file_name;
			  var size 			= value.size;
			  var progress  	= value.progress;
			  var created_name 	= value.created_name;
			  var created 		= value.created;
			  var modified_name = value.modified_name;
			  var modified 		= value.modified;

			  string[string.length] = '<tr style="cursor: pointer;">'
											+'<td class="center cb"><input type="checkbox" id="file_'+id+'" class="file_checkbox" value="'+id+'"><label for="file_'+id+'"><span></span></label></td>'
											+'<td class="cb">'+name+'</td>'
											+'<td><a href="'+link+'" class="download" target="_blank"><i class="fa fa-download" aria-hidden="true"></i></a>'+file_name+'</td>'
											+'<td class="center cb">'+size+' Kb</td>'
											+'<td class="center cb">'+created+'</td>'
											+'<td class="center cb">'+created_name+'</td>'
											+'<td class="center cb">'+modified+'</td>'
											+'<td class="center cb">'+modified_name+'</td>'
										+'</tr>';

		 });
		 
		 string = string.join("");
	}else
		string = '<tr style="cursor: pointer;"><td colspan="8"><div class="col-log-12" style="text-align: center; color: #efcb41;">Không có dữ liệu hiển thị</div></td></tr>';

	 
	 return string;	
}

function load_template_request(items) {
	if(items.length) {
		 var string = new Array();
		 $.each(items, function( index, value ) {
			  var id      				= value.id;
			  var task_name      		= value.task_name;
			  var progress      		= value.progress;
			  var trangthai      		= value.trangthai;
			  var prioty      			= value.prioty;
			  var created      		    = value.created;
			  var user_pheduyet_name    = value.user_pheduyet_name;
			  var date_pheduyet      	= value.date_pheduyet;
			  var pheduyet      		= value.pheduyet;
			  
			  string[string.length] = '<tr>'
											+'<td>'+task_name+'</td>'
											+'<td class="center">'+progress+'</td>'
											+'<td class="center">'+trangthai+'</td>'
											+'<td class="center">'+prioty+'</td>'
											+'<td class="center">'+created+'</td>'
											+'<td class="center">'+pheduyet+'</td>'
											+'<td class="center">'+user_pheduyet_name+'</td>'
											+'<td class="center">'+date_pheduyet+'</td>'
											+'<td class="center">'
												+'<a href="javascript:;" onclick="note('+id+');">Ghi chú</a>'
											+'</td>'
										+'</tr>';
		 });
		 
		 string = string.join("");	
	}else
		var string = '<tr style="cursor: pointer;"><td colspan="9"><div class="col-log-12" style="text-align: center; color: #efcb41;">Không có dữ liệu hiển thị</div></td></tr>';

	 return string;
}

function load_template_pheduyet(items) {
	 if(items.length) {
		 var string = new Array();
		 $.each(items, function( index, value ) {
			  var id      			= value.id;
			  var date_pheduyet     = value.date_pheduyet;
			  var created      		= value.created;
			  var task_name      	= value.task_name;
			  var progress          = value.progress;
			  var trangthai      	= value.trangthai;
			  var prioty    		= value.prioty;
			  var user_name      	= value.username;
			  var pheduyet      	= value.pheduyet;
			  
			  if(value.is_xuly == true)
				  var control = '<a href="javascript:;" onclick="note('+id+');">Ghi chú</a> | <a href="javascript:;" onclick="xuly_tiendo('+id+');">Phê duyệt</a>';
			  else
				  var control = '<a href="javascript:;" onclick="note('+id+');">Ghi chú</a>';

			  string[string.length] = '<tr>'
											+'<td>'+task_name+'</td>'
											+'<td class="center">'+progress+'</td>'
											+'<td class="center">'+trangthai+'</td>'
											+'<td class="center">'+prioty+'</td>'
											+'<td class="center">'+user_name+'</td>'
											+'<td class="center">'+created+'</td>'
											+'<td class="center">'+pheduyet+'</td>'
											+'<td class="center">'+date_pheduyet+'</td>'
											+'<td class="center">'
												+control
											+'</td>'
										+'</tr>';
		 });
		 
		 string = string.join(""); 
	 }else
		 var string = '<tr style="cursor: pointer;"><td colspan="9"><div class="col-log-12" style="text-align: center; color: #efcb41;">Không có dữ liệu hiển thị</div></td></tr>';

	 return string;
}

function load_template_personal_progress(items) {
    if(items.length) {
        var string = new Array();
        $.each(items, function( index, value ) {
            var id      	= value.id;
            var user_id 	= value.created_by;
            var user_name = value.username;
            var created 	= value.created;
            var progress  = value.progress;
            var trangthai = value.trangthai;
            var pheduyet 	= value.pheduyet;
            var note 		= value.note;

            var prioty 	 = value.prioty;
            var task_name  = value.task_name;
            var task_name  = value.task_name;

            user_name = '<span style="font-weight: bold">'+user_name+'</span>';
            string[string.length] = '<tr style="cursor: pointer;">'
                +'<td class="center cb">'+progress+'</td>'
                +'<td class="center cb">'+trangthai+'</td>'
                +'<td class="center cb">'+prioty+'</td>'
                +'<td class="center cb">'+user_name+'</td>'
                +'<td class="center cb">'+created+'</td>'
                +'</tr>	';
        });

        string = string.join("");
    }else
        var string = '<tr style="cursor: pointer;"><td colspan="5"><div class="col-log-12" style="text-align: center; color: #efcb41;">Không có dữ liệu hiển thị</div></td></tr>';

    return string;
}


function load_template_progress(items) {
	 if(items.length) {
		 var string = new Array();
		 $.each(items, function( index, value ) {
			  var id      	= value.id;
			  var user_id 	= value.created_by;
			  var user_name = value.username;
			  var created 	= value.created;
			  var progress  = value.progress;
			  var trangthai = value.trangthai;
			  var pheduyet 	= value.pheduyet;
			  var note 		= value.note;
			  
			  var prioty 	 = value.prioty;
			  var task_name  = value.task_name;
			  var task_name  = value.task_name;
			  	  
			  user_name = '<span style="font-weight: bold">'+user_name+'</span>';
			  string[string.length] = '<tr style="cursor: pointer;">'		
											+'<td class="cb">'+task_name+'</td>'
											+'<td class="center cb">'+progress+'</td>'
											+'<td class="center cb">'+trangthai+'</td>'
											+'<td class="center cb">'+prioty+'</td>'
											+'<td class="center cb">'+user_name+'</td>'
											+'<td class="center cb">'+created+'</td>'
										+'</tr>	';
		 });
		 
		 string = string.join("");
	 }else 
		 var string = '<tr style="cursor: pointer;"><td colspan="6"><div class="col-log-12" style="text-align: center; color: #efcb41;">Không có dữ liệu hiển thị</div></td></tr>';
	 
	 return string;
}

function load_tempate_comment(items) {
	 var string = new Array();
	 $.each(items, function( index, value ) {
		  var id      	= value.id;
		  var username 	= value.username;
		  var content 	= value.content;
		  var created 	= value.created;
		  var image     = value.image;

		  string[string.length] = 
				  '<li class="item-comment">' 
					+'<a target="_blank" rel="nofollow" href="javascript:;" class="thumb-user" title="'+name+'">' 
						+'<img class="fn-thumb" width="50" src="'+image+'">' 
					+'</a>' 
					+'<div class="post-comment">' 
						+'<a target="_blank" rel="nofollow" class="fn-link" href="http://me.zing.vn/u/caonaman369" title="'+name+'">'+username+'</a>' 
						+'<p class="fn-content">'+content+'</p>' 
						+'<span class="fn-time">'+created+'</span>' 
					+'</div>' 
				 +'</li>' ; 

	 });

	 string = string.join("");	
	 
	 return string;
}

function countTiendo() {
	var task_id = $('#task_id').val();
	var url = BASE_URL + 'tasks/countTiendo';
	$.ajax({
		type: "POST",
		url: url,
		data: {
			task_id : task_id
		},
		success: function(string){
			var result = $.parseJSON(string);
			//console.log(result);
			$('#count_tiendo').text(result.tiendo_total);
			$('#count_request').text(result.request_total);
			$('#count_pheduyet').text(result.pheduyet_total);
	    }
	});
}


function load_list(keyword, page) {
	var task_id = $('#task_id').val();
	var data = new Object();
	data.task_id = task_id;
	
	switch (keyword){
	    case 'progress' : {
			var url	        = BASE_URL + 'tasks/progresslist/'+page;
			var manager_div = 'progress_danhsach';
			var count_span  = 'count_tiendo';
			
			var taskID = $('#s_task_id').val();
			data.taskID = taskID;

			var elementSort = $('#progress_danhsach th.header');
	        break;
	    }

        case 'progress-personal' : {
            var url	        = BASE_URL + 'tasks/personal_progress_list/'+page;
            var manager_div = 'progress_danhsach';
            var count_span  = 'count_tiendo';

            var elementSort = $('#progress_danhsach th.header');
            break;
        }

	    case 'file' : {
			var url 		  = BASE_URL + 'tasks/filelist/'+page;
			var manager_div   = 'file_manager';
			var count_span 	  = 'count_tailieu';

			var elementSort = $('#file_manager th.header');

			break;
	    }

        case 'file-personal' : {
            var url 		  = BASE_URL + 'tasks/personel_file_list/'+page;
            var manager_div   = 'file_manager';
            var count_span 	  = 'count_tailieu';

            var elementSort = $('#file_manager th.header');

            break;
        }
	    
	    case 'request' : {
			var url 		  = BASE_URL + 'tasks/requestlist/'+page;
			var manager_div   = 'request_list';
			var count_span 	  = 'count_request';
			
			var taskID = $('#s_task_id').val();
			data.taskID = taskID;
			
			var elementSort = $('#request_list th.header');
			
			break;
	    }
	    
	    case 'pheduyet' : {
			var url 		  = BASE_URL + 'tasks/pheduyetlist/'+page;
			var manager_div   = 'pheduyet_list';
			var count_span 	  = 'count_pheduyet';
			
			var taskID = $('#s_task_id').val();
			data.taskID = taskID;
			
			var elementSort = $('#pheduyet_list th.header');
			
			break;
	    }
	
	    case 'template' : {
	    	var manager_div = 'template_list';
			var url	        = BASE_URL + 'tasks/templatelist/'+page;
			var count_span  = 'count_template';
			
			var elementSort = $('#template_list th.header');
			data.keywords   = $.trim($('#s_keywords').val());
			
			break;
	    }
	    
	    case 'project' : {
	    	var manager_div = 'project_list';
			var url	        = BASE_URL + 'tasks/projectlist/'+page;
			var count_span  = 'count_project';
			
			var elementSort = $('#project_list th.header');
			data.keywords   = $.trim($('#s_keywords').val());

            break;
	    }
	    
	    case 'project-grid' : {
	    	var manager_div = 'project_grid_list';
			var url	        = BASE_URL + 'tasks/projectGridList/'+page;
	
			var elementSort = $('#project_grid_table td.header');

            data.keywords         = $.trim($('#s_keywords').val());
            data.date_start_from  = $.trim($('#s_date_start_from').val());
            data.date_start_to    = $.trim($('#s_date_start_to').val());
            data.date_end_from    = $.trim($('#s_date_end_from').val());
            data.date_end_to      = $.trim($('#s_date_end_to').val());
            data.trangthai        = $.trim($('#s_trangthai').val());
            data.customers        = $.trim($('#s_customer').val());
            data.implement        = $.trim($('#s_implement').val());
            data.xem              = $.trim($('#s_xem').val());

            break;
	    }
        case 'personal' : {
            var manager_div = 'project_grid_list';
            var url	        = BASE_URL + 'tasks/personalList/'+page;

            var elementSort = $('#project_grid_table td.header');

            data.keywords         = $.trim($('#s_keywords').val());
            data.date_start_from  = $.trim($('#s_date_start_from').val());
            data.date_start_to    = $.trim($('#s_date_start_to').val());
            data.date_end_from    = $.trim($('#s_date_end_from').val());
            data.date_end_to      = $.trim($('#s_date_end_to').val());
            data.trangthai        = $.trim($('#s_trangthai').val());
            data.customers        = $.trim($('#s_customer').val());
            data.xem              = $.trim($('#s_xem').val());

            break;
        }
	}

	// get field sort
	if(elementSort.length){
		if(elementSort.hasClass('headerSortUp')){
			data.col   = elementSort.attr('data-field');
			data.order = 'ASC';
		}else {
			data.col   = elementSort.attr('data-field');
			data.order = 'DESC';
		}
	}

    $.ajax({
        type: "POST",
        url: url,
        data: data,
        beforeSend: function() {
            loading(keyword);
        },
        success: function(string){
            close_loading(keyword);
            var result = $.parseJSON(string);
            var items = result.items;
            //console.log(items);
            var pagination = result.pagination;

            switch (keyword){
                case 'progress' : {
                    var html_string = load_template_progress(items);
                    var pagination = load_pagination(pagination);
                    break;
                }

                case 'progress-personal' : {
                    var html_string = load_template_personal_progress(items);
                    var pagination = load_pagination(pagination);
                    break;
                }

                case 'file' : {
                    var html_string = load_template_file(items);
                    var pagination = load_pagination(pagination);
                    break;
                }

                case 'file-personal' : {
                    var html_string = load_template_file(items);
                    var pagination = load_pagination(pagination);
                    break;
                }

                case 'request' : {
                    var html_string = load_template_request(items);
                    var pagination = load_pagination(pagination);
                    break;
                }

                case 'pheduyet' : {
                    var html_string = load_template_pheduyet(items);
                    var pagination = load_pagination(pagination);
                    break;
                }

                case 'template' : {
                    var html_string = load_template_template(items);
                    var pagination = load_pagination(pagination);
                    break;
                }

                case 'project' : {
                    var html_string = load_template_project(items);
                    var pagination = load_pagination(pagination, 'gantt');
                    break;
                }

                case 'project-grid' : {
                    var html_string = load_template_project_grid(items);
                    var pagination = load_pagination(pagination);
                    break;
                }

                case 'personal' : {
                    var html_string = load_template_personal(items);
                    var pagination = load_pagination(pagination);
                    break;
                }
            }

            $('#'+manager_div+' .table tbody').html(html_string);
            if($('#'+manager_div+' .text-center').length)
                $('#'+manager_div+' .text-center').replaceWith( pagination );
            else
                $('#'+manager_div).append(pagination);

            $('#'+count_span).text(result.count);

        }
    });

}

function get_data_child_task(data, project_id, tr_element) {
    var s_keywords        = tr_element.find('.s_keywords');
    var s_date_start_from = tr_element.find('.s_date_start_from');
    var s_date_start_to   = tr_element.find('.s_date_start_to');
    var s_date_end_from   = tr_element.find('.s_date_end_from');
    var s_date_end_to     = tr_element.find('.s_date_end_to');
    var s_trangthai       = tr_element.find('.s_trangthai');
    var s_customer        = tr_element.find('.s_customer');
    var s_implement       = tr_element.find('.s_implement');
    var s_xem             = tr_element.find('.s_xem');
    var s_status          = tr_element.find('.s_status');
    var s_progress        = tr_element.find('.s_progress');

    data.keywords         = $.trim(s_keywords.val());
    data.date_start_from  = $.trim(s_date_start_from.val());
    data.date_start_to    = $.trim(s_date_start_to.val());
    data.date_end_from    = $.trim(s_date_end_from.val());
    data.date_end_to      = $.trim(s_date_end_to.val());
    data.trangthai        = $.trim(s_trangthai.val());
    data.customers        = $.trim(s_customer.val());
    data.implement        = $.trim(s_implement.val());
    data.xem              = $.trim(s_xem.val());
    data.pheduyet         = $.trim(s_status.val());
    data.progress         = $.trim(s_progress.val());

    return data;
}

function load_task_childs(project_id, page) {
	var data = new Object();
    data.project_id = project_id;
	var url	        = BASE_URL + 'tasks/taskByProjectList/'+page;
	var table 	    = $('#task_childs_'+project_id);

    var elementSort = $('#task_childs_'+project_id+' td.header');

    // get filter input
    var tr_element        = $('#project_grid_table tr[data-parent="'+project_id+'"]');
    data                  = get_data_child_task(data, project_id, tr_element);

    // get field sort
    if(elementSort.length){
        if(elementSort.hasClass('headerSortUp')){
            data.col   = elementSort.attr('data-field');
            data.order = 'ASC';
        }else {
            data.col   = elementSort.attr('data-field');
            data.order = 'DESC';
        }
    }

    $.ajax({
        type: "POST",
        url: url,
        data: data,
        beforeSend: function() {
        },
        success: function(string){
            var result = $.parseJSON(string);
            var items = result.items;
            var project = result.project;

            var html_string = load_template_task_child(items);
            table.find('tbody').html(html_string);
            table.attr('data-content', 1);
        }
    });
}

function load_template_tr(item) {
    var html = '<tr data-tree="42"><td class="hidden-print" style="width: 25px; text-align: center;"><a href="javascript:;" class="expand_all">+</a></td><td class="hidden-print" style="width: 25px; text-align: center;"><a href="javascript:;"><i class="fa fa-search"></i></a></td><td>Dự án mẫu</td><td align="center">Trung bình</td><td align="center">01-10-2016</td><td align="center">27-10-2016</td><td align="center"><div class="clearfix"><div class="progress-bar" style="float: left;"><div class="bar positive" style="width: 0%; background: #4388c2"></div><div class="bar negative" style="width: 100%; background: #489ee7"></div><span>0%</span></div><div class="progress-text">Còn 6 ngày</div></div></td><td align="center">Chưa thực hiện</td><td align="center"></td></tr>';

    return html;
}

function add_tiendo() {
	var task_id = $('#task_id').val();
	var url = BASE_URL + 'tasks/addtiendo'
	$.ajax({
		type: "GET",
		url: url,
		data: {
			task_id : task_id
		},
		success: function(html){
            $('#quick_modal').html(html);
            $('#quick_modal').modal('toggle');
	    }
	});
}

function save_tiendo(task) {
	if(task == 'edit')
		var url = BASE_URL + 'tasks/edittiendo';
	else if(task == 'xuly')
		var url = BASE_URL + 'tasks/xulytiendo';
	else
		var url = BASE_URL + 'tasks/addtiendo';
	
	var checkOptions = {
	        url : url,
	        dataType: "json",  
	        success: tiendoData
	    };
    $("#progress_form").ajaxSubmit(checkOptions); 
    return false; 
}

function tiendoData(data) {
	if(data.flag == 'false') {
		toastr.error(data.message, 'Lỗi!');
	}else {
		toastr.success(data.message, 'Thông báo');
        $('#quick_modal').modal('toggle');
		
		var content_id = $('#progress_manager span.tieude.active').attr('data-id');
		if(content_id == 'progress_danhsach')
			load_list('progress', 1);
		else if(content_id == 'request_list')
			load_list('request', 1);
		else if(content_id == 'pheduyet_list')
			load_list('pheduyet', 1);
		
		countTiendo();
		if(data.reload == 'true') {
            if($('#current_project_id').length) {
                var project_id = $('#task_form [name="project_id"]').val();
                load_task_childs(project_id, 1);
            }else
                load_task(1,'clearAll');
        }

		$('#progress_manager .button').hide();
	}
}

function pheduyet() {
    var task_id = $('#task_id').val();
    $.ajax({
        type: "GET",
        url: BASE_URL + 'tasks/pheduyet',
        data: {
            id : task_id
        },
        success: function(html){
            $('#quick_modal').html(html);
            $('#quick_modal').modal('toggle');
        }
    });
}

function pheduyet_confirm() {
    var checkOptions = {
        url : BASE_URL+'tasks/pheduyet',
        dataType: "json",
        success: pheduyet_confirm_progress
    };
    $("#task_pheduyet_form").ajaxSubmit(checkOptions);
    return false;
}

function pheduyet_confirm_progress(data) {
    if(data.flag == 'false') {
        toastr.error(data.msg, 'Lỗi');
    }else {
        toastr.success(data.msg, 'Thông báo');
    }

    $('#quick_modal').modal('toggle');
    $('#my_modal').modal('toggle');
    load_task(1);
}

function edit_congviec() {
    reset_error();
    var url = BASE_URL + 'tasks/editcongviec';
    var checkOptions = {
        url : url,
        dataType: "json",
        success: taskData
    };
    $("#task_form").ajaxSubmit(checkOptions);
    return false;
}

function taskData(data) {
    if(data.flag == 'false') {
        $.each(data.errors, function( index, value ) {
            element = $( '#my_modal span[for="'+index+'"]' );
            element.prev().addClass('has-error');
            element.text(value);
        });
    }else {
        toastr.success('Cập nhật thành công!', 'Thông báo');
        $('#my_modal').modal('toggle');

        if($('#current_project_id').length){
            var project_id = $('#task_form [name="project_id"]').val();
            load_task_childs(project_id, 1);
        }else
            load_task(1, 'clearAll');
    }
}

function add_file() {
	var task_id = $('#task_id').val();
	var url = BASE_URL + 'tasks/addfile'
	$.ajax({
		type: "GET",
		url: url,
		data: {
			task_id : task_id
		},
		success: function(html){
            $('#quick_modal').html(html);
            $('#quick_modal').modal('toggle');
	    }
	});
}

function edit_file() {
	var checkbox = $(".file_checkbox:checked");
    var url = BASE_URL + 'tasks/editfile';

    if(checkbox.length == 1) {
        $(checkbox).each(function( index ) {
            file_id = $(this).val();
        });

        $.ajax({
            type: "GET",
            url: url,
            data: {
                id : file_id,
            },
            success: function(string){
                $('#quick_modal').html(string);
                $('#quick_modal').modal('toggle');
            }
        });
    }else {
        toastr.error('Chỉ chọn 1 bản ghi', 'Thông báo');
    }
}

function save_file(task) {
	reset_error();
	if(task == 'edit') 
		var url = BASE_URL + 'tasks/editfile'
	else 
		var url = BASE_URL + 'tasks/addfile'

	var checkOptions = {
	        url : url,
	        dataType: "json",  
	        success: fileData
	    };
    $("#file_form").ajaxSubmit(checkOptions); 
    return false; 
}

function fileData(data) {
	if(data.flag == 'false') {
		$.each(data.errors, function( index, value ) {	
			element = $( '#quick_modal span[for="'+index+'"]' );
			if(index == 'file_upload')
				$('#file_display').addClass('has-error');
			else	
				element.prev().addClass('has-error');
			
			element.text(value);
		});	

	}else {
		toastr.success('Cập nhật thành công!', 'Thông báo');
        $('#quick_modal').modal('toggle');

		load_list('file', 1);
	}
}

function delete_file() {
	var checkbox = $("#file_manager .file_checkbox:checked");

	if(checkbox.length) {
		var file_ids = new Array();
		$(checkbox).each(function( index ) {
			file_ids[file_ids.length] = $(this).val();
		});

        bootbox.confirm("Bạn có chắc chắn không?", function(result){
            if (result){
                $.ajax({
                    type: "POST",
                    url: BASE_URL + 'tasks/deletefile',
                    data: {
                        file_ids   : file_ids
                    },
                    success: function(string){
                        toastr.success('Cập nhật thành công!', 'Thông báo');
                        load_list('file', 1);
                    }
                });
            }
        });

	}else {
        toastr.warning('Chọn ít nhất một bản ghi!', 'Cảnh báo');
	}
}


function delete_congviec(id) {
    bootbox.confirm("Bạn có chắc muốn xóa?", function(result){
  	   if (result){
   		 var ids = new Array();
		 ids[0] = id;
		 $.ajax({
			 type: "POST",
			 url: BASE_URL + 'tasks/deletecv',
			 data: {
				 ids  : ids
			 },
			 success: function(string){
                 $('#my_modal').modal('toggle');
                 if($('#current_project_id').length) {
                     var project_id = $('#task_form [name="project_id"]').val();
                     load_task_childs(project_id, 1);
                 }else
                     load_task(1,'clearAll');
		     }
		 });
  		
  	   }
   });   
	  
}

function edit_task_grid(id) {
    var url = BASE_URL + 'tasks/editcongviec';
    $.ajax({
        type: "GET",
        url: url,
        data: {
            id : id
        },
        success: function(html){
            if(html != '') {
                $('#my_modal').html(html);
                $('#my_modal').modal('toggle');
                $('#color').colorpicker();

            }else {
                toastr.warning('Bạn không có quyền với chức năng này!', 'Cảnh báo');
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
}
// date support
function convert_date(string) {
    var str_arr = string.split(" ");
    var time     = str_arr[1];
    var date     = str_arr[0];
    var date_arr = date.split("-");
    var new_date = date_arr[2] + '-' + date_arr[1] + '-' + date_arr[0];
    new_date = new_date + ' ' + time;

    return new_date;
}
function get_current_date() {
    var d = new Date();

    var month = d.getMonth()+1;
    var day = d.getDate();

    var output = d.getFullYear() + '-' + (month<10 ? '0' : '') + month + '-' + (day<10 ? '0' : '') + day;
    return output;
}

function get_yesterday() {
    var d = new Date();
    d.setDate(d.getDate() - 1); // yesterday

    var month = d.getMonth()+1;
    var day = d.getDate();

    var output = d.getFullYear() + '-' + (month<10 ? '0' : '') + month + '-' + (day<10 ? '0' : '') + day;
    return output;
}

function get_some_days_ago(days) {
    var d = new Date();
    d.setDate(d.getDate() - days);

    var month = d.getMonth()+1;
    var day = d.getDate();

    var output = d.getFullYear() + '-' + (month<10 ? '0' : '') + month + '-' + (day<10 ? '0' : '') + day;
    return output;
}

function get_first_date_of_last_week() {
    var curr = new Date;
    var first = curr.getDate() - curr.getDay();
    var last = first + 6;

    // first day of current week
    var d = new Date(curr.setDate(first));

    // take the first day of the last week by taking that day 7 days back
    d.setDate(d.getDate() - 7);

    // now we get the day that we need
    var month = d.getMonth()+1;
    var day = d.getDate();

    var output = d.getFullYear() + '-' + (month<10 ? '0' : '') + month + '-' + (day<10 ? '0' : '') + day;
    return output;
}

function get_last_date_of_last_week() {
    var curr = new Date;
    var first = curr.getDate() - curr.getDay();
    var last = first + 6;

    // last day of current week
    var d = new Date(curr.setDate(last));

    // take the last day of the last week by taking that day 7 days back
    d.setDate(d.getDate() - 7);

    // now we get the day that we need
    var month = d.getMonth()+1;
    var day = d.getDate();

    var output = d.getFullYear() + '-' + (month<10 ? '0' : '') + month + '-' + (day<10 ? '0' : '') + day;
    return output;
}

function get_first_date_of_current_weekend() {
    var curr = new Date;
    var first = curr.getDate() - curr.getDay();
    var last = first + 6;

    var d = new Date(curr.setDate(first));

    var month = d.getMonth()+1;
    var day = d.getDate();

    var output = d.getFullYear() + '-' + (month<10 ? '0' : '') + month + '-' + (day<10 ? '0' : '') + day;
    return output;
}

function get_last_date_of_current_weekend() {
    var curr = new Date;
    var first = curr.getDate() - curr.getDay();
    var last = first + 6;

    var d = new Date(curr.setDate(last));

    var month = d.getMonth()+1;
    var day = d.getDate();

    var output = d.getFullYear() + '-' + (month<10 ? '0' : '') + month + '-' + (day<10 ? '0' : '') + day;
    return output;
}

function get_first_date_of_last_month() {
    var date = new Date();
    var d = new Date(date.getFullYear(), date.getMonth() - 1, 1);

    var month = d.getMonth()+1;
    var day = d.getDate();

    var output = d.getFullYear() + '-' + (month<10 ? '0' : '') + month + '-' + (day<10 ? '0' : '') + day;
    return output;
}

function get_last_date_of_last_month() {
    var date = new Date(), y = date.getFullYear(), m = date.getMonth();
    var d = new Date(y, m , 0);

    var month = d.getMonth()+1;
    var day = d.getDate();

    var output = d.getFullYear() + '-' + (month<10 ? '0' : '') + month + '-' + (day<10 ? '0' : '') + day;
    return output;
}

function get_first_date_of_last_year() {
    var date = new Date();
    var y = date.getFullYear() - 1;
    var output = y+'-01-01';

    return output;
}

function get_last_date_of_last_year() {
    var date = new Date();
    var y = date.getFullYear() - 1;

    var output = y+'-12-31';
    return output;
}

function get_first_date_of_current_month() {
    var date = new Date(), y = date.getFullYear(), m = date.getMonth();
    var d = new Date(y, m, 1);

    var month = d.getMonth()+1;
    var day = d.getDate();

    var output = d.getFullYear() + '-' + (month<10 ? '0' : '') + month + '-' + (day<10 ? '0' : '') + day;
    return output;
}

function get_last_date_of_current_month() {
    var date = new Date(), y = date.getFullYear(), m = date.getMonth();
    var d = new Date(y, m + 1, 0);

    var month = d.getMonth()+1;
    var day = d.getDate();

    var output = d.getFullYear() + '-' + (month<10 ? '0' : '') + month + '-' + (day<10 ? '0' : '') + day;
    return output;
}

function get_first_date_of_current_year() {
    var date = new Date();
    var y = date.getFullYear();
    var output = y+'-01-01';

    return output;

}

function get_last_date_of_current_year() {
    var date = new Date();
    var y = date.getFullYear();

    var output = y+'-12-31';
    return output;
}

// end date support
function get_item_autocomplete(data) {
    var span = '<span class="item">'
                    +'<input type="hidden" class="'+data.class+'" value="'+data.value+'">'
                    +'<a>'+data.title+'</a>&nbsp;&nbsp;'
                    +'<span class="x" onclick="delete_item(this);"></span>'
                +'</span>';

    return span;
}

function convert_string_checkbox(string) {
    var res = string.split(",");

    if($.inArray('1',res) != -1){
        res = $.grep(res, function(value) {
            return value != '1';
        });

        res = $.grep(res, function(value) {
            return value != '2';
        });

        res[res.length] = '1_2';
    }

    return res;
}

function get_two_dates(date) {
    switch(date) {
        case 'today':
            var current_date = get_current_date();
            var date_1 = current_date + ' 00:00';
            var date_2 = current_date + ' 23:59';
            break;
        case 'yesterday':
            var yesterday = get_yesterday();
            var date_1 = yesterday + ' 00:00';
            var date_2 = yesterday + ' 23:59';
            break;
        case '7_days_previous':
            var yesterday = get_yesterday();
            var days_ago = get_some_days_ago(7);

            var date_1 = days_ago + ' 00:00';
            var date_2 = yesterday + ' 23:59';
            break;

        case 'current_week':
            var date_1 = get_first_date_of_current_weekend() + ' 00:00';
            var date_2 = get_last_date_of_current_weekend() + ' 23:59';

            break;

        case 'previous_week':
            var date_1 = get_first_date_of_last_week() + ' 00:00';
            var date_2 = get_last_date_of_last_week() + ' 23:59';
            break;

        case 'current_month':
            var date_1 = get_first_date_of_current_month() + ' 00:00';
            var date_2 = get_last_date_of_current_month() + ' 23:59';

            break;
        case 'previous_month':
            var date_1 = get_first_date_of_last_month() + ' 00:00';
            var date_2 = get_last_date_of_last_month() + ' 23:59';

            break;
        case 'current_year':
            var date_1 = get_first_date_of_current_year() + ' 00:00';
            var date_2 = get_last_date_of_last_year() + ' 23:59';

            break;
        case 'previous_year':
            var date_1 = get_first_date_of_last_year() + ' 00:00';
            var date_2 = get_last_date_of_last_year() + ' 23:59';

            break;
        default:
            var date_1 = '';
            var date_2 = '';
    }

    date = {date_1 : date_1, date_2: date_2};

    return date;
}

function do_change_advance_search(type) {
    var project_id     = $('#current_project_id').val();
    var element_parent = $('#project_grid_table').find('tr[data-parent="'+project_id+'"]');

    var s_trangthai            = element_parent.find('.s_trangthai');
    var s_implement            = element_parent.find('.s_implement');
    var s_xem                  = element_parent.find('.s_xem');
    var s_trangthai            = element_parent.find('.s_trangthai');

    var s_trangthai_html       = element_parent.find('.s_trangthai_html');
    var s_implement_html       = element_parent.find('.s_implement_html');
    var s_xem_html             = element_parent.find('.s_xem_html');

    switch(type) {
        case 'implement':
            var html = get_item_autocomplete({class : 'implement', value: user_id, title: user_name});
            s_implement.val(user_id);
            s_implement_html.html(html);
            break;

        case 'xem':
            var html = get_item_autocomplete({class : 'xem', value: user_id, title: user_name});
            s_xem.val(user_id);
            s_xem_html.html(html);

            break;

        case 'cancel':
            var html = get_item_autocomplete({class : 'trangthai', value: 3, title: 'Đóng/ Dừng'});
            s_trangthai.val(3);
            s_trangthai_html.html(html);

            break;

        case 'not-done':
            var html = get_item_autocomplete({class : 'trangthai', value: 4, title: 'Không thực hiện'});
            s_trangthai.val(4);
            s_trangthai_html.html(html);

            break;

        case 'unfulfilled':
            var html = get_item_autocomplete({class : 'trangthai', value: 0, title: 'Chưa thực hiện'});
            s_trangthai.val(0);
            s_trangthai_html.html(html);

            break;

        case 'processing':
            var html = get_item_autocomplete({class : 'trangthai', value: 1, title: 'Đang tiến hành'});
            s_trangthai.val(1);
            s_trangthai_html.html(html);

            break;

        case 'slow_proccessing':
            var html = get_item_autocomplete({class : 'trangthai', value: 5, title: 'Chậm tiến độ'});
            s_trangthai.val(5);
            s_trangthai_html.html(html);

            break;

        case 'finish':
            var html = get_item_autocomplete({class : 'trangthai', value: 2, title: 'Đã hoàn thành'});
            s_trangthai.val(2);
            s_trangthai_html.html(html);

            break;

        case 'slow-finish':
            var html = get_item_autocomplete({class : 'trangthai', value: 6, title: 'Đã hoàn thành nhưng chậm tiến độ'});
            s_trangthai.val(6);
            s_trangthai_html.html(html);

            break;

    }

    $('#task_report').modal('toggle');
    load_task_childs(project_id, 1);
}