<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            <h4 class="modal-title">Thêm mới công việc</h4>
        </div>
        <div class="modal-body">
            <form method="POST" name="template_task_form" id="template_task_form" class="form-horizontal" enctype="multipart/form-data">
                <div class="clearfix hang">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="form-group" style="margin-bottom: 0">
                                <label class="col-md-3 col-lg-2 control-label">Tiêu đề</label>
                                <div class="col-md-9 col-lg-10">
                                    <input type="text" name="template_task" id="template_task" value="" class="form-control">
                                    <span for="name" class="text-danger" class="errors"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
        <div class="modal-footer" style="padding-top: 0;">
            <a href="javascript:;" id="btn_save_templae_task" class="btn btn-primary">Lưu</a>
        </div>
    </div>
</div>
<script type="text/javascript">
    $( "#btn_save_templae_task" ).click(function() {
        $('#template_task_form span[for="name"]').text('');
        $('#template_task').removeClass('has-error');
        var task_name = $.trim($('#template_task').val());
        if (!task_name) {
            $('#template_task').addClass('has-error');
            $('#template_task_form span[for="name"]').text('Tên công việc không được rỗng.')
        }else {
            var count_task = $('#count_task').val();
            count_task     = parseInt(count_task) + 1;
            $('#count_task').val(count_task);

            $('#sTree2').append('<li data-module="'+count_task+'" id="t_'+count_task+'" data-name="'+task_name+'"><div>'+task_name+'</div></li>');
            $('#quick_modal').modal('toggle');
        }
    });
</script>