$( document ).ready(function() {
    var data_table = $('#project_grid_table').attr('data-table');
    // autocomplete
    var frame_array = ['customer_list', 'xem_list', 'implement_list', 'trangthai_list'];
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

	// handle checkbox
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

    // project, tasks child
    $('body').on('click','.table-tree .expand_all',function(){
        var symbol = $(this).text();
        var tr_element = $(this).closest('tr');
        var table_element = $(this).closest('table');
        var id = tr_element.attr('data-tree');

        var tr_child = table_element.find('tr[data-parent="'+id+'"]');
        if(symbol == '+'){
            tr_child.hide();
            $(this).text('-');

        }else{
            var table_child     = $('#task_childs_'+id);
            var data_content    = table_child.attr('data-content');
            if(data_content == 0) {
                load_task_childs(id, 1);
            }

            tr_child.show();
            $(this).text('+');
        }
    });

    //sort
    $('body').on('click','table [data-field]',function(){
        var attr     = $(this).attr('data-field');
        var table    = $(this).closest('table');
        var table_id = table.attr('id');
        if($(this).hasClass('header')) {
            if($(this).hasClass('headerSortUp')){
                $(this).removeClass('headerSortUp');
                $(this).addClass('headerSortDown');
            }else {
                $(this).removeClass('headerSortDown');
                $(this).addClass('headerSortUp');
            }
        }else {
            table.find('td').removeClass('header');
            table.find('td').removeClass('headerSortUp');
            table.find('td').removeClass('headerSortDown');
            $(this).addClass('header headerSortUp');
        }

        if(table_id == 'project_grid_table') {
            if(data_table == 'task_list')
                load_list(data_table, 1);
            else
                load_list('project-grid', 1);
        }else {
            var tr_parent = table.closest('[data-parent]');
            var project_id = tr_parent.attr('data-parent');
            load_task_childs(project_id, 1);
        }
    });

    $('body').on('click','#btn_search_advance',function(){
        if($('#task_section').is(":visible")) {
            set_hidden_input();

            var project_id     = $('#current_project_id').val();
            load_task_childs(project_id, 1);
        }else {
            set_project_hidden_input();
            load_list('project-grid', 1);
        }

        $('#advance_task_search').modal('toggle');
    });

    //advance search click
    $('body').on('click','.submitf',function(){
        var task_name       = $(this).attr('data-name');
        var project_id      = $(this).attr('data-id');
        current_project_id  = project_id;

        $('#current_project_id').val(project_id);
        set_form_input(current_project_id, task_name);

        $('#task_section').show();
        $('#progress_section').show();

        $("#advance_task_search").modal();
    });

    //advance project search click
    $('body').on('click','#btn_advance_project',function(){
        set_project_form_input();
        if(data_table == 'task_list')
            $('#my_search_task').text('Tìm kiếm Công việc');
        else
            $('#my_search_task').text('Tìm kiếm Dự án');

        $('#task_section').hide();
        $('#progress_section').hide();
        $("#advance_task_search").modal();
    });

    // statistic click
    $('body').on('click','.statistic',function(){
        if(data_table == 'task_list') {
            var data        = get_data_hidden();
            var url         = BASE_URL + 'tasks/tasks_statistic';
            var s_trangthai = $('#s_trangthai');
        }else {
            var task_name       = $(this).attr('data-name');
            var project_id      = $(this).attr('data-id');
            var data            = new Object();
            var tr_element      = $('#project_grid_table tr[data-parent="'+project_id+'"]');
            var s_trangthai     = tr_element.find('.s_trangthai');
            var url             = BASE_URL + 'tasks/tasks_child_statistic';

            current_project_id = project_id;
            $('#current_project_id').val(project_id);
            $('#my_report_task span').html(task_name);

            data                  = get_data_child_task(data, project_id, tr_element);
            data.project_id       = project_id;
        }
        console.log(data);
        $.ajax({
            type: "POST",
            url: url,
            data: data,
            success: function(string){
               var status = ['unfulfilled', 'processing', 'finish', 'cancel', 'not-done', 'slow_proccessing', 'slow-finish'];
               var trangthai_value = s_trangthai.val();

               if(trangthai_value)
                  var trangthai_arr = trangthai_value.split(",");
               else
                  var trangthai_arr = new Array();

                if(trangthai_arr.length == 0)
                    $('#task_report li a').removeClass('unclick');
                else {
                    $('#task_report li a').removeClass('unclick');
                    for (i = 0; i < status.length; i++) {
                        var str = i.toString();
                        var status_element = status[i];
                        if(trangthai_arr.indexOf(str) == -1){
                            $('#task_report li.'+status_element+' a').addClass('unclick');
                        }
                    }
                }

                var result = $.parseJSON(string);
                $('#task_report li.all a').text(result.all);
                $('#task_report li.implement a').text(result.implement);
                $('#task_report li.xem a').text(result.xem);
                $('#task_report li.cancel a').text(result.cancel);
                $('#task_report li.not-done a').text(result.not_done);
                $('#task_report li.unfulfilled a').text(result.unfulfilled);
                $('#task_report li.processing a').text(result.processing);
                $('#task_report li.slow_proccessing a').text(result.slow_proccessing);
                $('#task_report li.finish a').text(result.finish);
                $('#task_report li.slow-finish a').text(result.slow_finish);

                $("#task_report").modal();
            }
        });
    });

    $('body').on('change','.search_date_type',function(){
        var value                = $(this).val();
        var element_parent       = $(this).closest('tr[data-parent]');
        var project_id           = element_parent.attr('data-parent');
        var s_date_start_to      = element_parent.find('.s_date_start_to');
        var s_date_start_from    = element_parent.find('.s_date_start_from');
        var s_date_end_to        = element_parent.find('.s_date_end_to');
        var s_date_end_from      = element_parent.find('.s_date_end_from');
        var s_trangthai          = element_parent.find('.s_trangthai');
        var s_date_start_radio   = element_parent.find('.s_date_start_radio');
        var s_date_end_radio     = element_parent.find('.s_date_end_radio');
        var s_status             = element_parent.find('.s_status');
        var s_progress           = element_parent.find('.s_progress');
        var s_customer           = element_parent.find('.s_customer');
        var s_trangthai          = element_parent.find('.s_trangthai');
        var s_implement          = element_parent.find('.s_implement');
        var s_xem                = element_parent.find('.s_xem');

        var s_trangthai_html     = element_parent.find('.s_trangthai_html');
        var s_customer_html      = element_parent.find('.s_customer_html');
        var s_implement_html     = element_parent.find('.s_implement_html');
        var s_xem_html           = element_parent.find('.s_xem_html');

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
        s_status.val('-1,0,1,2');
        s_progress.val('-1,0,1,2');

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

        load_task_childs(project_id, 1);
    });

    // event when close modal
    $('#advance_task_search').on('hidden.bs.modal', function () {
        reset_form();
    })

    $('#task_report').on('hidden.bs.modal', function () {
        $('#task_report li.all span').text('0');
        $('#task_report li.implement span').text('0');
        $('#task_report li.xem span').text('0');
        $('#task_report li.cancel span').text('0');
        $('#task_report li.not-done span').text('0');
        $('#task_report li.unfulfilled span').text('0');
        $('#task_report li.processing span').text('0');
        $('#task_report li.slow_proccessing span').text('0');
        $('#task_report li.finish span').text('0');
        $('#task_report li.slow-finish span').text('0');
    })

    //search tasks
    var typingTimer;
    $('body').on('keyup','.search_keywords',function(){
        clearTimeout(typingTimer);
        var tr_element = $(this).closest('[data-parent]');
        var project_id = tr_element.attr('data-parent');

        typingTimer = setTimeout('startSearch('+project_id+')', 500);
    });

    $('body').on('keydown','.search_keywords',function(){
        clearTimeout(typingTimer);
    });

    // search project
    $('body').on('keyup','#search_keywords',function(){
        var value = $(this).val();
        $('#s_keywords').val(value);
        clearTimeout(typingTimer);
        typingTimer = setTimeout(project_search, 500);
    });

    $('body').on('keydown','.search_keywords',function(){
        clearTimeout(typingTimer);
    });

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

        if(data_table == 'task_list')
            load_list(data_table, 1);
        else
            load_list('project-grid', 1);
    });

});

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


function project_search() {
    var dt = $('#project_grid_table').attr('data-table')
    if(dt == 'task_list')
        load_list(dt, 1);
    else
        load_list('project-grid', 1);
}

function startSearch (project_id) {
    var tr_element       = $('#project_grid_table tr[data-parent="'+project_id+'"]');
    var s_keywords       = tr_element.find('.s_keywords');
    var search_keywords  = tr_element.find('.search_keywords');
    var s_customer       = tr_element.find('.s_customer');
    var s_customer_html  = tr_element.find('.s_customer_html');
    var s_implement      = tr_element.find('.s_implement');
    var s_implement_html = tr_element.find('.s_implement_html');
    var s_xem            = tr_element.find('.s_xem');
    var s_xem_html       = tr_element.find('.s_xem_html');
    var s_status         = tr_element.find('.s_status');
    var s_progress       = tr_element.find('.s_progress');

    s_customer.val('');
    s_customer_html.html('');
    s_implement.val('');
    s_implement_html.html('');
    s_xem.val('');
    s_xem_html.html('');
    s_status.val('-1,0,1,2');
    s_progress.val('-1,0,1,2');

    s_keywords.val(search_keywords.val());

    load_task_childs(project_id, 1);

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

    var s_status             = $('#s_status');
    var s_progress           = $('#s_progress');

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

    var s_status_value = s_status.val();
    var res = new Array();
    if (s_status_value) {
        res = convert_string_checkbox(s_status_value);
        $.each(res, function( index, value ) {
            $('#status_'+value).prop('checked', true);
        });
    }

    var s_progress_value = s_progress.val();
    res = new Array();
    if (s_progress_value) {
        res = convert_string_checkbox(s_progress_value);
        $.each(res, function( index, value ) {
            $('#progress_'+value).prop('checked', true);
        });
    }
}


function set_hidden_input() {
    var current_project_id     = $('#current_project_id').val();
    var element_parent         = $('#project_grid_table').find('tr[data-parent='+current_project_id+']');

    var search_keywords        = element_parent.find('.search_keywords');
    var search_date_type       = element_parent.find('.search_date_type');

    var s_keywords             = element_parent.find('.s_keywords');

    var s_date_start           = element_parent.find('.s_date_start');
    var s_date_start_radio     = element_parent.find('.s_date_start_radio');
    var s_date_start_from      = element_parent.find('.s_date_start_from');
    var s_date_start_to        = element_parent.find('.s_date_start_to');

    var s_date_end             = element_parent.find('.s_date_end');
    var s_date_end_radio       = element_parent.find('.s_date_end_radio');
    var s_date_end_from        = element_parent.find('.s_date_end_from');
    var s_date_end_to          = element_parent.find('.s_date_end_to');

    var s_trangthai            = element_parent.find('.s_trangthai');
    var s_customer             = element_parent.find('.s_customer');
    var s_implement            = element_parent.find('.s_implement');
    var s_xem                  = element_parent.find('.s_xem');
    var s_status               = element_parent.find('.s_status');
    var s_progress             = element_parent.find('.s_progress');

    var s_trangthai_html       = element_parent.find('.s_trangthai_html');
    var s_customer_html        = element_parent.find('.s_customer_html');
    var s_implement_html       = element_parent.find('.s_implement_html');
    var s_xem_html             = element_parent.find('.s_xem_html');

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

    //Tasks
    var checkbox = $("input[name='status[]']:checked");
    var checkbox_val = new Array();

    if(checkbox.length) {
        $( checkbox ).each(function() {
            var str = $(this).val();
            var value = str.replace("1-2", "1,2");

            checkbox_val[checkbox_val.length] = value;
        });
    }

    checkbox_val = checkbox_val.join(',');
    s_status.val(checkbox_val);

    // Progress
    var checkbox = $("input[name='progress[]']:checked");
    var checkbox_val = new Array();
    if(checkbox.length) {
        $( checkbox ).each(function() {
            var str = $(this).val();
            var value = str.replace("1-2", "1,2");

            checkbox_val[checkbox_val.length] = value;
        });
    }

    checkbox_val = checkbox_val.join(',');
    s_progress.val(checkbox_val);
    search_keywords.val('');
    search_date_type.val('0');
}

function set_form_input(project_id, task_name) {
    $('#my_search_task').text('Tìm công việc cho "'+task_name+'"');

    var element_parent  = $('#project_grid_table').find('tr[data-parent='+project_id+']');
    var s_keywords           = element_parent.find('.s_keywords');

    var s_date_start_radio   = element_parent.find('.s_date_start_radio');
    var date_start_value     = s_date_start_radio.val();
    var s_date_start         = element_parent.find('.s_date_start');
    var s_date_start_from    = element_parent.find('.s_date_start_from');
    var s_date_start_to      = element_parent.find('.s_date_start_to');

    var s_date_end_radio     = element_parent.find('.s_date_end_radio');
    var date_end_value       = s_date_end_radio.val();
    var s_date_end           = element_parent.find('.s_date_end');
    var s_date_end_from      = element_parent.find('.s_date_end_from');
    var s_date_end_to        = element_parent.find('.s_date_end_to');

    var s_status             = element_parent.find('.s_status');
    var s_progress           = element_parent.find('.s_progress');

    var s_trangthai_html     = element_parent.find('.s_trangthai_html');
    var s_customer_html      = element_parent.find('.s_customer_html');
    var s_implement_html     = element_parent.find('.s_implement_html');
    var s_xem_html           = element_parent.find('.s_xem_html');

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

    var s_status_value = s_status.val();
    var res = new Array();
    if (s_status_value) {
        res = convert_string_checkbox(s_status_value);
        $.each(res, function( index, value ) {
            $('#status_'+value).prop('checked', true);
        });
    }

    var s_progress_value = s_progress.val();
    res = new Array();
    if (s_progress_value) {
        res = convert_string_checkbox(s_progress_value);
        $.each(res, function( index, value ) {
            $('#progress_'+value).prop('checked', true);
        });
    }
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

    $('#status_-1').prop('checked', false);
    $('#status_0').prop('checked', false);
    $('#status_1_2').prop('checked', false);

    $('#progress_-1').prop('checked', false);
    $('#progress_0').prop('checked', false);
    $('#progress_1_2').prop('checked', false)

}


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
		
	// remove on table
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
					ids   : task_ids
				},
				success: function(string){
					toastr.success('Cập nhật thành công!', 'Thông báo');
					load_list('project', 1);
			    }
			});
		}
	});
}