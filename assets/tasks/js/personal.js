$( document ).ready(function() {
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
            $('.manage-row-options').show();
            checkbox.prop('checked', true);
        }

        var checked_box = table.find('.file_checkbox:checked');
        if(checked_box.length == 0){
            $('.manage-row-options[data-table="'+data_table+'"]').addClass('hidden');
        }else {
            $('.manage-row-options[data-table="'+data_table+'"]').removeClass('hidden');
        }

    });
});
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
    }
}