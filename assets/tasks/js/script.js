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