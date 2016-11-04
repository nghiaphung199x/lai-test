<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            <h4 class="modal-title">Thêm mới File</h4>
        </div>
        <div class="modal-body">
            <form method="POST" name="file_form" id="file_form" class="form-horizontal" enctype="multipart/form-data">
                <input type="hidden" name="task_id" value="<?php echo $arrParam['task_id']; ?>" />
                <div class="clearfix hang">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="form-group">
                                <label class="col-md-3 col-lg-2 control-label">Tên tài liệu</label>
                                <div class="col-md-9 col-lg-10">
                                    <input type="text" name="name" value="" class="form-control">
                                    <span for="name" class="text-danger errors"></span>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-3 col-lg-2 control-label">File Upload</label>
                                <div class="col-md-9 col-lg-10">
                                    <input type="file" name="file_upload" id="file_upload" class="filestyle file_upload" tabindex="-1" style="position: absolute; clip: rect(0px 0px 0px 0px);">
                                    <div class="bootstrap-filestyle input-group"><input type="text" name="file_display" id="file_display" class="form-control " disabled=""> <span class="group-span-filestyle input-group-btn" tabindex="0"><label for="image_id" class="btn btn-file-upload "><span class="glyphicon glyphicon-folder-open"></span> <span class="buttonText" id="choose_file">Choose file</span></label></span></div>
                                    <span for="file_upload" class="text-danger errors"></span>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-3 col-lg-2 control-label">Tên file</label>
                                <div class="col-md-9 col-lg-10">
                                    <input type="text" name="file_name" id="file_name" value="" class="form-control">
                                    <span for="file_name" class="text-danger errors"></span>
                                </div>
                            </div>
                            <div class="form-group" style="margin-bottom: 0;">
                                <label class="col-md-3 col-lg-2 control-label">Mô tả</label>
                                <div class="col-md-9 col-lg-10">
                                    <textarea name="excerpt" class="form-control" style="margin-bottom: 0"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </form>

        </div>
        <div class="modal-footer" style="padding-top: 0;">
            <a href="javascript:;" onclick="save_file();" class="btn btn-primary">Lưu</a>
        </div>
    </div>
</div>
<script type="text/javascript">
    $( document ).ready(function() {
        $( "#choose_file" ).click(function() {
            $('#file_upload').trigger('click');
        });

        $( ".file_upload" ).change(function() {
            var yourstring = $(this).val();
            var filename = yourstring.replace(/^.*[\\\/]/, '')

            var output  = filename.split('/').pop().split('.').shift();
            $('#file_display').val(filename);
            $('#file_name').val(output);
        });
    });
</script>