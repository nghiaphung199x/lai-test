$( document ).ready(function() {
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

        var data_table = $('#project_grid_table').attr('data-table');
        load_list(data_table, 1);

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