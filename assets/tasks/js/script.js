$( document ).ready(function() {
	// xử lý checkbox
	$('body').on('click','.my-table .check_tatca',function(){
		  var checkbox = $(this).closest('th').find('input[type="checkbox"]'); 
		  if (checkbox.prop('checked') == true){ 
			  //$('.manage-row-options').hide();
			 checkbox.prop('checked', false);
			 $(this).parents('.table').find('td input[type="checkbox"]').prop('checked', false);
		  }else{
			 // $('.manage-row-options').show();
			  checkbox.prop('checked', true);
			  $(this).parents('.table').find('td input[type="checkbox"]').prop('checked', true);
		  }
   });
	
   $('body').on('click','.my-table tbody tr td.cb',function(){
	    var checkbox = $(this).closest('tr').find('input[type="checkbox"]');
		 
	    if (checkbox.prop('checked')==true){ 
		  checkbox.prop('checked', false);
	    }else{
		  checkbox.prop('checked', true);
	    }
   });
   
   
   $('body').on('click','.del_task',function(e){
	   alert('thật sự đã vào đây');
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
   
	//sort
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
		   }
		}
	});
   
   // search
   var typingTimer;       
   $('body').on('keyup','#template_keywords',function(){
	   clearTimeout(typingTimer);
	   typingTimer = setTimeout(startSearch, 300);
   });
   
   $('body').on('keydown','#template_keywords',function(){
	   clearTimeout(typingTimer);
   });
   
   function startSearch () {
	  load_list('template', 1);
   }
  
});

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

function load_pagination(pagination, template) {
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
		
		string = string.join("");
		
		if(template == 'comment') {
			string = '<ul>'+string+'</ul>';
		}else
			string = '<div class="text-center"><div class="pagination hidden-print alternate text-center">' + string + '</div></div>';

		return string;
	}else
		return '';
}

function loading(keyword) {
	$("#loading_1").show();
}

function close_loading(keyword) {
	$("#loading_1").hide();
}

function load_list(keyword, page) {
	var data = new Object();
	switch (keyword){
	    case 'template' : {
	    	var manager_div = 'template_list';
			var url	        = BASE_URL + 'tasks/templatelist/'+page;
			var count_span  = 'count_template';
			
			var elementSort = $('#template_list th.header');
			data.keywords   = $.trim($('#template_keywords').val());
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
			 close_loading();
			 var result     = $.parseJSON(string);
			 var items      = result.items; 
			 var pagination = result.pagination;
			 
			 switch (keyword){
			    case 'template' : {
			    	 var html_string = load_template_template(items);
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
	    	gantt.deleteTask(taskId);
	    }
	}
}

function close_layer(type) {
	if(type == 'quick')
		var classLayer = 'overlay2';
	else
		var classLayer = 'overlay1';

	$('.'+classLayer).remove();
}

function add_congviec() {
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

function del_template_task(id) {
	console.log($('#t_'+id));
	
	
//	parent_item = $('#t_'+id).closest('ul'); 
//	
//	console.log(parent_item);
//	if(parent_item.hasClass('listsClass')){
//		alert('1');
//		//$('#t_'+id).remove();
//		console.log($('#t_'+id));
//	}else{
//		alert('2');
//		console.log(parent_item);
//		//parent_item.remove();
//	}
		
}

function list_templat_task() {
	var url = BASE_URL + 'tasks/listTemplateTask'
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
			}
	    }
	});
}

