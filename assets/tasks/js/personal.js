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

    //sort
    $('body').on('click','table [data-field]',function(){
        var table    = $(this).closest('table');

        if($(this).hasClass('header')) {
            if($(this).hasClass('headerSortUp')){
                $(this).removeClass('headerSortUp');
                $(this).addClass('headerSortDown');
            }else {
                $(this).removeClass('headerSortDown');
                $(this).addClass('headerSortUp');
            }
        }else {
            table.find('th').removeClass('header');
            table.find('th').removeClass('headerSortUp');
            table.find('th').removeClass('headerSortDown');
            $(this).addClass('header headerSortUp');
        }

        var data_table = table.attr('data-table');
        load_list(data_table, 1);

    });

    //checkbox
    $('body').on('click','table[data-table] td.cb',function(){
        var checkbox = $(this).closest('tr').find('input[type="checkbox"]');
        var table = checkbox.closest('[data-table]');
        var data_table = table.attr('data-table');

        if (checkbox.prop('checked') == true){
            checkbox.prop('checked', false);
        }else{
            checkbox.prop('checked', true);
        }

        var checked_box = table.find('.file_checkbox:checked');
        if(checked_box.length == 0){
            $('.manage-row-options[data-table="'+data_table+'"]').addClass('hidden');
        }else {
            $('.manage-row-options[data-table="'+data_table+'"]').removeClass('hidden');
        }

    });

    // check all
    $('body').on('click','table[data-table] .check_tatca',function(){
        var checkbox = $(this).closest('th').find('input[type="checkbox"]');
        var table = checkbox.closest('[data-table]');
        var data_table = table.attr('data-table');

        if (checkbox.prop('checked') == true){
            $('.manage-row-options[data-table="'+data_table+'"]').addClass('hidden');
            checkbox.prop('checked', false);
            table.find('td input[type="checkbox"]').prop('checked', false);
        }else{
            $('.manage-row-options[data-table="'+data_table+'"]').removeClass('hidden');
            checkbox.prop('checked', true);
            table.find('td input[type="checkbox"]').prop('checked', true);
        }

        var checked_box = table.find('.file_checkbox:checked');
        if(checked_box.length == 0){
            $('.manage-row-options[data-table="'+data_table+'"]').addClass('hidden');
        }else {
            $('.manage-row-options[data-table="'+data_table+'"]').removeClass('hidden');
        }
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
        load_list(data_table, 1);
    }

    // search date type
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
        load_list(data_table, 1);
    });

    // event when modal close
    $('#advance_task_search').on('hidden.bs.modal', function () {
        reset_form();
    })

    //advance search click
    $('body').on('click','#btn_advance_task',function(){
        set_form_input();
        $("#advance_task_search").modal();
    });

    $('body').on('click','#btn_search_advance',function(){
        set_hidden_input();
        load_list(data_table, 1);
        $('#advance_task_search').modal('toggle');
    });

    // statistic click
    $('body').on('click','.statistic',function(){
        var data = new Object();
        // get filter input
        data.keywords         = $.trim($('#s_keywords').val());
        data.date_start_from  = $.trim($('#s_date_start_from').val());
        data.date_start_to    = $.trim($('#s_date_start_to').val());
        data.date_end_from    = $.trim($('#s_date_end_from').val());
        data.date_end_to      = $.trim($('#s_date_end_to').val());
        data.trangthai        = $.trim($('#s_trangthai').val());
        data.customers        = $.trim($('#s_customer').val());
        data.implement        = $.trim($('#s_implement').val());
        data.xem              = $.trim($('#s_xem').val());
        data.data_table       = data_table;
        if(data.trangthai == '0')
            data.trangthai == 'zero';

        $.ajax({
            type: "POST",
            url: BASE_URL + 'tasks/personal_statistic',
            data: data,
            success: function(string){
                var result = $.parseJSON(string);
                $('#task_report li.all span').text(result.all);
                $('#task_report li.cancel span').text(result.cancel);
                $('#task_report li.not-done span').text(result.not_done);
                $('#task_report li.unfulfilled span').text(result.unfulfilled);
                $('#task_report li.processing span').text(result.processing);
                $('#task_report li.slow_proccessing span').text(result.slow_proccessing);
                $('#task_report li.finish span').text(result.finish);
                $('#task_report li.slow-finish span').text(result.slow_finish);

                $("#task_report").modal();
            }
        });
    });

});

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



function set_hidden_input() {
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


function set_form_input() {
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

function load_personal_comment(task_id, page) {
    var url = BASE_URL + 'tasks/personal_comment_list/'+page;
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

function update_personal_task(task, type, id) {
    if (typeof id == 'undefined')
      id = 0;

    if(task == 'new') {
        url = BASE_URL + 'tasks/add_personal';
    }else if(task == 'edit')
        url = BASE_URL + 'tasks/edit_personal';

    $.ajax({
        type: "GET",
        url: url,
        data: {
            id : id,
            type: type
        },
        success: function(html){
            if(task == 'new') {
                $('#my_modal').html(html);
                $('#my_modal').modal('toggle');
            }else {
                if(html != '') {
                    $('#my_modal').html(html);
                    $('#my_modal').modal('toggle');

                }else {
                    toastr.warning('Bạn không có quyền với chức năng này!', 'Cảnh báo');
                }
            }

            //picker
            date_time_picker_field($('.datepicker'), JS_DATE_FORMAT);
            // end picker

            var frame_array = ['customer_list', 'xem_list', 'implement_list'];
            $.each(frame_array, function( index, value ) {
                css_form(value);
                press(value);
            });
        }
    });
}

function add_personal_task() {
    reset_error();
    var checkOptions = {
        url : BASE_URL+'tasks/add_personal',
        dataType: "json",
        success: add_personal_task_data
    };
    $("#task_form").ajaxSubmit(checkOptions);
    return false;
}

function add_personal_task_data(data) {
    if(data.flag == 'false') {
        $.each(data.errors, function( index, value ) {
            element = $( '#my_modal span[for="'+index+'"]' );
            element.prev().addClass('has-error');
            element.text(value);
        });
    }else {
        toastr.success('Cập nhật thành công!', 'Thông báo');
        $('#my_modal').modal('toggle');
        load_list('personal', 1);
    }
}

function add_personal_tiendo() {
    var task_id = $('#task_id').val();
    var url = BASE_URL + 'tasks/add_personal_tiendo'
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

function save_personal_tiendo() {
    var url = BASE_URL + 'tasks/add_personal_tiendo'

    var checkOptions = {
        url : url,
        dataType: "json",
        success: save_personal_tiendo_data
    };
    $("#progress_form").ajaxSubmit(checkOptions);
    return false;
}

function save_personal_tiendo_data(data) {
    if(data.flag == 'false') {
        toastr.error(data.msg, 'Lỗi!');
    }else {
        toastr.success(data.msg, 'Thông báo');
        $('#quick_modal').modal('toggle');

        load_list('progress-personal', 1);
        load_list('personal', 1);
    }
}

function delete_personal(id) {
    var flag       = true;
    var ids        = new Array();
    var table      = $('[data-table="personal"]');
    var manage_row = $('.manage-row-options[data-table="personal"]');
    var check_all  = table.find('.check_tatca');
    check_all      = check_all.closest('label').prev();

    if (typeof id == 'undefined') {
        var checkbox = $("[data-table='personal'] .file_checkbox:checked");
        if(checkbox.length) {
            $(checkbox).each(function( index ) {
                ids[ids.length] = $(this).val();
            });
        }else {
            flag = false;
            toastr.warning('Chọn ít nhất một bản ghi!', 'Cảnh báo');
        }
    }else {
        ids[ids.length] = id;
    }

    if(flag == true) {
        bootbox.confirm("Bạn có chắc chắn không?", function(result){
            if (result){
                $.ajax({
                    type: "POST",
                    url: BASE_URL + 'tasks/delete_personal',
                    data: {
                        ids   : ids
                    },
                    success: function(string){
                        toastr.success('Cập nhật thành công!', 'Thông báo');
                        load_list('personal', 1);

                        manage_row.addClass('hidden');
                        check_all.prop('checked', false);
                    }
                });
            }
        });
    }
}

function add_personal_file() {
    var task_id = $('#task_id').val();
    var url = BASE_URL + 'tasks/add_personal_file'
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

function edit_personal_file() {
    var checkbox = $("#file_manager .file_checkbox:checked");
    var url = BASE_URL + 'tasks/edit_personal_file';

    if(checkbox.length == 1) {
        $(checkbox).each(function( index ) {
            file_id = $(this).val();
        });

        $.ajax({
            type: "GET",
            url: url,
            data: {
                id : file_id
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

function save_personal_file(task) {
    reset_error();
    if(task == 'edit')
        var url = BASE_URL + 'tasks/edit_personal_file'
    else
        var url = BASE_URL + 'tasks/add_personal_file'

    var checkOptions = {
        url : url,
        dataType: "json",
        success: save_personal_file_data
    };
    $("#file_form").ajaxSubmit(checkOptions);
    return false;
}

function save_personal_file_data(data) {
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

        load_list('file-personal', 1);
        $('.manage-row-options[data-table="file-personal"]').addClass('hidden');
    }
}

function delete_personal_file() {
    var table      =  $('[data-table="file-personal"]');
    var manage_row = $('.manage-row-options[data-table="file-personal"]');
    var checkbox   = $("#file_manager .file_checkbox:checked");
    var check_all  = table.find('.check_tatca');
    check_all      = check_all.closest('label').prev();

    if(checkbox.length) {
        var file_ids = new Array();
        $(checkbox).each(function( index ) {
            file_ids[file_ids.length] = $(this).val();
        });

        bootbox.confirm("Bạn có chắc chắn không?", function(result){
            if (result){
                $.ajax({
                    type: "POST",
                    url: BASE_URL + 'tasks/delete_personal_file',
                    data: {
                        file_ids   : file_ids
                    },
                    success: function(string){
                        toastr.success('Cập nhật thành công!', 'Thông báo');
                        load_list('file-personal', 1);

                        manage_row.addClass('hidden');
                        check_all.prop('checked', false);
                    }
                });
            }
        });
    }else {
        toastr.warning('Chọn ít nhất một bản ghi!', 'Cảnh báo');
    }
}

function comment_personal() {
    $('#comment_content').removeClass('error');
    var checkOptions = {
        url : BASE_URL + 'tasks/add_personal_comment',
        dataType: "json",
        success: comment_personal_data
    };
    $("#task_form").ajaxSubmit(checkOptions);
}

function comment_personal_data(data) {
    if(data.flag == 'false') {
        if(data.type == 'content'){
            $('#comment_content').addClass('error');
            toastr.error(data.msg, 'Lỗi!');
        }
    }else {
        toastr.success(data.msg, 'Thông báo!');

        load_personal_comment(data.task_id, 1);
        $('#comment_content').val('');
    }
}
