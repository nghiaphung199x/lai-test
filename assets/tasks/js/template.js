$( document ).ready(function() {
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
                $('#quick_modal').html(html);
                $('#quick_modal').modal('toggle');
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
});

function add_template_task() {
    var url = BASE_URL + 'tasks/addcvtemplate'
    $.ajax({
        type: "GET",
        url: url,
        data: {
        },
        success: function(html){
            $('#quick_modal').html(html);
            $('#quick_modal').modal('toggle');
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

function update_template(id) {
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
    var data           = new Object();
    data.template_name = template_name;
    data.tasks         = tasks;
    if(id > 0) {
        data.id = id;
        var url = BASE_URL + 'tasks/editTemplate';
    }else {
        var url = BASE_URL + 'tasks/templateAdd';
    }

    $.ajax({
        type: "POST",
        url: url,
        data: data,
        success: function(string){
            var res = $.parseJSON(string);

            if(res.flag == 'false'){
                toastr.error(res.msg, 'Lỗi!');
            }else {
                window.location = BASE_URL + 'tasks/template';
            }
        }
    });
}