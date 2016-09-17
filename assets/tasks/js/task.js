	function create_layer(type) {
		if(type == 'quick')
			var classLayer = 'overlay';
		else
			var classLayer = 'dhx_modal_cover';
		
		if($('.'+classLayer).length)
			$('.'+classLayer).css('display', 'inline-block');
		else {
			$( "body" ).append( '<div class="'+classLayer+'" style="display: inline-block;"></div>' );

		}
	}	
	
	function css_form(obj_id) {
		  if($('#'+obj_id).length) {
			   var top = $("#"+obj_id+" .quick_search").offset().top - $("#"+obj_id).offset().top + 20;
			   var left = $("#"+obj_id+" .quick_search").offset().left - $("#"+obj_id).offset().left;

			   var styles = {
			      left : left + "px",
			      top : top + 'px'
			   };
			   
			   $("#"+obj_id+" .result").css( styles );	
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

	
	function close_layer(type) {
		if(type == 'quick')
			var classLayer = 'overlay';
		else
			var classLayer = 'dhx_modal_cover';

		$('.'+classLayer).remove();
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
	
	function doneTyping(frame_id) {
		if(frame_id == 'customer_list')
			var url = BASE_URL + 'tasks/customers/danhsach';
		else {
			var url = BASE_URL + 'tasks/users/danhsach';
		}

		$('#'+frame_id+' .result').html('');
		$('#'+frame_id+' .result').hide();
		var keywords = $.trim($('#'+frame_id+' .quick_search').val());
		//console.log(keywords);
		if (keywords) {
			$.ajax({
				type: "POST",
				url: url,
				data: {
					keywords : keywords
				},
				success: function(string){
					array = $.parseJSON(string);
					css_form(frame_id)
					if(array.length) {
						var html = new Array();
						$.each(array, function( index, value ) {
							html[html.length] = '<li><a href="javascript:;" data-id="'+value.id+'" data-name="'+value.name+'" onclick="add_item(this, \''+frame_id+'\');">'+value.name+'</a></li>';
						});

						html = html.join('')
						html = '<ul class="list">'+html+'</ul>'; 
	
						$('#'+frame_id+' .result').html(html);
						$('#'+frame_id+' .result').show();
					}
			    }
			});
		}
	}
	
	function foucs(obj) {
		$(obj).find('.quick_search').focus();
	}
	
	$( document ).ready(function() {
		load_task();
		
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
					   create_layer();
					   $('#my-form').removeClass('quickInfo');
					   $('#my-form').html(html);
					   $('#my-form').show();
					   
				   }else {
					   if(html != '') {   
						   create_layer();
						   $('#my-form').removeClass('quickInfo');
						   $('#my-form').html(html);
						   if ( $( "#my-form input[name='quickInfo']" ).length ) {
							   $('#my-form').addClass('quickInfo');
						   }
						   
						   $('#my-form').show();
					   }else {
						   gantt.alert({
							    text: 'Bạn không có quyền với chức năng này.', title:"Error!",
							    ok:"Yes", callback:function(){}
							});
					   }
				   }
				   
				   var frame_array = ['customer_list', 'xem_list', 'implement_list', 'create_task_list', 'pheduyet_task_list', 'progress_list'];
				   $.each(frame_array, function( index, value ) {
					  css_form(value);
					  press(value);
				   });
			    }
			});
		};
		
		gantt.templates.quick_info_date = function(start, end, task){
		       return gantt.templates.task_time(start, end, task);
		};
	});
	function load_task() {
		$.ajax({
			type: "POST",
			url: BASE_URL + 'tasks/danhsach',
			data: {
			},
			success: function(string){
			   var result = $.parseJSON(string);
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
			 				          {name:"text",       label:"Dự án/Công việc",  width: 350, tree:true},
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
						 return "nested_task"
					}
					
				    return "";
				};
				// link
				gantt.attachEvent("onBeforeLinkAdd", function(id,link){
					var task_id = link.source;
					
					if($.inArray(task_id, deny_items) == -1){
						$.ajax({
							type: "POST",
							url: BASE_URL + 'tasks/link',
							data: {
								source   : link.source,
								target   : link.target,
								type     : parseInt(link.type),
							},
							success: function(string){
								var res = $.parseJSON(string);
								if(res.flag == 'true')
									gantt.alert("Cập nhật thành công.");
						    }
						});
						return true;
					}
					else{
						gantt.alert({
						    text:"Bạn không có quyền với chức năng này.",
						    title:"Error!",
						    ok:"Yes",
						    callback:function(){}
						});
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
								//console.log(string);
						    }
						});
						return true;
					}
					else{
						gantt.alert({
						    text:"Bạn không có quyền với chức năng này.",
						    title:"Error!",
						    ok:"Yes",
						    callback:function(){}
						});
						return false;
					}
				});
				
				//drag
				gantt.attachEvent("onBeforeTaskDrag", function(id, mode, task){
					 if(mode == 'move' || mode == 'resize') {
						if ($.inArray(id, deny_items) == -1){
							var task  =gantt.getTask(id);
							return true;
						}
						else{
							gantt.alert({
							    text:"Bạn không có quyền với chức năng này.",
							    title:"Error!",
							    ok:"Yes",
							    callback:function(){}
							});
							return false;
						}
					}else if(mode == 'progress')
						return false;

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

				    gantt.confirm({
				        text: 'Cập nhật "'+new_start_date+' đến '+new_end_date+'"',
				        ok:"Đồng ý", 
				        cancel:"Hủy bỏ",
				        callback: function(result){
				        	if(result == true) {
								$.ajax({
									type: "POST",
									url: BASE_URL + 'tasks/quickupdate',
									data: {
										id 		   : id,
										date_start : start_date,
										date_end   : end_date,
									},
									success: function(string){
										var res = $.parseJSON(string);
										if(res.flag == 'true')
											gantt.alert("Cập nhật thành công.");
											
								    }
								});
				        	}else{
				        		
				        		//task.start_date = $('#start_date_original').val();
				        		//gantt.refreshData();
				        	}

				        }
				    });
				});
				
				
		    }
		});	
	}
	