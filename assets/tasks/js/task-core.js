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
	
	// sort
	$('body').on('click','#my-form .manage-table table th',function(){
		var thElement = $('#my-form .manage-table table th');
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
		   
		   var li_element = $('.arrord_nav ul li.active');
		   var className  = li_element.attr('data-id');
		   if(className == 'progress_manager') {
				var content_id = $('#progress_manager span.tieude.active').attr('data-id');
				if(content_id == 'progress_danhsach') {
					load_list('progress', 1);
				}else if(content_id == 'request_list')
					load_list('request', 1);
				else if(content_id == 'pheduyet_list'){
					load_list('pheduyet', 1);
				}   
		   }else
			   load_list('file', 1);
		}
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

function load_template_project_grid(items) {
	if(items.length) {
		 var string = new Array();
		 $.each(items, function( index, value ) {
			  var id      	   = value.id;
			  var start_date   = value.start_date;
			  var end_date     = value.end_date;
			  var finish_date  = value.finish_date;
			  var name         = value.name;
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
			  
			  var positive = parseInt(progress) * 100;
			  var negative = 100 - positive;
			  
			  string[string.length] = '<tr data-tree="'+id+'">'
										+'<td class="hidden-print" style="width: 25px; text-align: center;"><a href="javascript:;" class="expand_all">-</a></td>'
										+'<td class="hidden-print" style="width: 25px; text-align: center;"><a href="javascript:;"><i class="fa fa-search"></i></a></td>'
										+'<td>'+name+'</td>'
										+'<td align="center">'+prioty+'</td>'
										+'<td align="center">'+start_date+'</td>'
										+'<td align="center">'+end_date+'</td>'
										+'<td align="center">'
											+'<div class="clearfix">'
												+'<div class="progress-bar" style="float: left;">'
												  +'<div class="bar positive" style="width: '+positive+'%; background: '+p_color+'">'
												    +'<span>'+positive+'%</span>'
												  +'</div>'
												  +'<div class="bar negative" style="width: '+negative+'%; background: '+n_color+'">'
												    +'<span></span>'
												  +'</div>'
												+'</div>'
												+'<div class="progress-text">'+note+'</div>'
											+'</div>'
										+'</td>'
										+'<td align="center">'+trangthai+'</td>'
										+'<td align="center">'+implement+'</td>'
									+'</tr>'
									+'<tr data-parent="'+id+'" data-content="0" style="display: none;">'
									+'<td colspan="9" class="innertable" style="display: table-cell;">'
										+'<table class="table table-bordered" id="task_childs_'+id+'">'
											+'<thead>'
												+'<tr align="center" style="font-weight:bold">'
													+'<td align="center">Tên công việc</td>'
													+'<td align="center" style="width: 8%;">Ưu tiên</td>'
													+'<td align="center" style="width: 100px;">Bắt đầu</td>'
													+'<td align="center" style="width: 100px;">Kết thúc</td>'
													+'<td align="center" style="width: 256px;">Tiến độ</td>'
													+'<td align="center" style="width: 10%;">Tình trạng</td>'
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
		var string = '<tr style="cursor: pointer;"><td colspan="9"><div class="col-log-12" style="text-align: center; color: #efcb41;">Không có dữ liệu hiển thị</div></td></tr>';

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
	if(keyword != 'file'){
		if(keyword == 'project')
			$("#loading_3").show();
		else
			$("#loading_1").show();
	}else{
		$("#loading_2").show();
	}
		 
}

function close_loading(keyword) {
	if(keyword != 'file') {
		if(keyword == 'project')
			$("#loading_3").hide();
		else
			$("#loading_1").hide();
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
	if(frame_id == 'customer_list')
		var url = BASE_URL + 'tasks/customerList';
	else {
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
						if(frame_id == 'customer_list')
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
		$('#my-form').html('');
		$('#my-form').hide();
		close_layer();

		if(type == 'new'){
			if(taskId != undefined)
				gantt.deleteTask(taskId);
	    }
	}
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
	$('#my-form .form-control').removeClass('has-error');
	$('#my-form span.errors').text('');
	$('#quick-form .form-control').removeClass('has-error');
	$('#quick-form span.errors').text('');
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
											+'<td><a href="'+link+'" class="download"><i class="fa fa-download" aria-hidden="true"></i></a>'+file_name+'</td>'
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
	    
	    case 'file' : {
			var url 		  = BASE_URL + 'tasks/filelist/'+page;
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
	    }
	    
	    case 'project-grid' : {
	    	var manager_div = 'project_grid_list';
			var url	        = BASE_URL + 'tasks/projectGridList/'+page;
	
			var elementSort = $('#project_grid_table th.header');
			data.keywords   = $.trim($('#s_keywords').val());
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
			var pagination = result.pagination;

			switch (keyword){
			    case 'progress' : {
			    	var html_string = load_template_progress(items);
			    	var pagination = load_pagination(pagination);
			    	break;
			    }
			    
			    case 'file' : {
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

function load_task_childs(project_id, page) {
		var url	        = BASE_URL + 'tasks/taskByProjectList/'+page;
		var manager_div = 'progress_danhsach';
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
			  $('#quick-form').html(html);
			  $('#quick-form').show();
			  create_layer('quick');
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
		$('#quick-form').html('');
		$('#quick-form').hide();
		
		var content_id = $('#progress_manager span.tieude.active').attr('data-id');
		if(content_id == 'progress_danhsach')
			load_list('progress', 1);
		else if(content_id == 'request_list')
			load_list('request', 1);
		else if(content_id == 'pheduyet_list')
			load_list('pheduyet', 1);
		
		countTiendo();
		if(data.reload == 'true')
			load_task();
		
		close_layer('quick');
		
		$('#progress_manager .button').hide();
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
			  $('#quick-form').html(html);
			  $('#quick-form').show();
			  create_layer('quick');
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
				  $('#quick-form').html(string);
				  $('#quick-form').show();
				  create_layer('quick');
		    }
		});
	}else {
		gantt.alert({
		    text: 'Chỉ chọn một bản ghi',
		    title:"Lỗi!",
		    ok:"Đóng",
		    callback:function(){}
		});
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
			element = $( '#quick-form span[for="'+index+'"]' );
			if(index == 'file_upload')
				$('#file_display').addClass('has-error');
			else	
				element.prev().addClass('has-error');
			
			element.text(value);
		});	

	}else {
		toastr.success('Cập nhật thành công!', 'Thông báo');
		$('#quick-form').html('');
		$('#quick-form').hide();

		load_list('file', 1);
		close_layer('quick');
	}
}

function delete_file() {
	var checkbox = $("#file_manager .file_checkbox:checked");

	if(checkbox.length) {
		var file_ids = new Array();
		$(checkbox).each(function( index ) {
			file_ids[file_ids.length] = $(this).val();
		});
		
	    gantt.confirm({
	        text: 'Xóa tài liệu',
	        ok:"Đồng ý", 
	        cancel:"Hủy bỏ",
	        callback: function(result){
	        	if(result == true) {
					$.ajax({
						type: "POST",
						url: BASE_URL + 'tasks/deletefile',
						data: {
							file_ids   : file_ids,
						},
						success: function(string){
							toastr.success('Cập nhật thành công!', 'Thông báo');
							load_list('file', 1);
					    }
					});
	        	}
	        }
	    });
		
	}else {
		gantt.alert({
		    text: 'Chọn ít nhất một bản ghi',
		    title:"Lỗi!",
		    ok:"Đóng",
		    callback:function(){}
		});
	}
}

function detail() {
	var task_id = $('#task_id').val();
	$.ajax({
		type: "POST",
		url: BASE_URL + 'tasks/detail?task=quick',
		data: {
			id 		   : task_id,
		},
		success: function(string){
			$('#my-form .arrord_nav').remove();
			$('#my-form .gantt_cal_larea').remove();
			$('#my-form').append(string);	
			if($('#my-form .btn-save').length)
				$('#my-form .btn-save').html('<a href="javascript:;" onclick="edit();"><i class="fa fa-edit"></i>Sửa</a>');
			else{
				if(!$('.btn-back').length) {
					var btn = '<li class="btn-back"><a href="javascript:;" onclick="edit();"><i class="fa fa-calendar"></i>Tiến độ</a></li>';
					$(btn).insertBefore( ".btn-detail" );
				}
			}	
	    }
	});
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
				 location.reload();
		     }
		 });
  		
  	   }
   });   
	  
}
