<?php
$id  	                = $item['id'];
$task_id                = $item['task_id'];
$name 	                = $item['name'];
$file_name              = $item['file_name'];
$size  	                = $item['size'];
$excerpt                = nl2br($item['excerpt']);

$file_name_without_ext  = preg_replace('/\\.[^.\\s]{3,4}$/', '', $file_name);
?>
<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            <h4 class="modal-title">Sửa File</h4>
        </div>
        <div class="modal-body" style="padding-bottom: 0;">
            <form method="POST" name="file_form" id="file_form" action="" class="form-horizontal" enctype="multipart/form-data">
                <input type="hidden" name="id" value="<?php echo $id; ?>" />
                <div class="clearfix hang" style="margin-bottom: 10px;">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="form-group">
                                <label class="col-md-3 col-lg-2 control-label">Tên tài liệu</label>
                                <div class="col-md-9 col-lg-10">
                                    <input type="text" name="name" value="<?php echo $name; ?>" class="form-control">
                                    <span for="name" class="text-danger errors"></span>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-3 col-lg-2 control-label">File Upload</label>
                                <div class="col-md-9 col-lg-10">
                                    <input type="file" name="file_upload" class="file_upload" id="file_upload" class="filestyle" tabindex="-1" style="position: absolute; clip: rect(0px 0px 0px 0px);">
                                    <div class="bootstrap-filestyle input-group"><input type="text" name="file_display" id="file_display" class="form-control " disabled=""> <span class="group-span-filestyle input-group-btn" tabindex="0"><label for="image_id" class="btn btn-file-upload "><span class="glyphicon glyphicon-folder-open"></span> <span class="buttonText" id="choose_file">Choose file</span></label></span></div>
                                    <span for="file_upload" class="text-danger errors"></span>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-3 col-lg-2 control-label">Tên file</label>
                                <div class="col-md-9 col-lg-10">
                                    <input type="text" name="file_name" id="file_name" value="<?php echo $file_name_without_ext; ?>" class="form-control">
                                    <span for="file_name" class="text-danger errors"></span>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-3 col-lg-2 control-label">Mô tả</label>
                                <div class="col-md-9 col-lg-10">
                                    <textarea name="excerpt" class="form-control" style="margin-bottom: 0;"><?php echo $excerpt; ?></textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </form>

        </div>
        <div class="modal-footer" style="padding-top: 0;">
            <a href="javascript:;" onclick="save_personal_file('edit');" class="btn btn-primary">Lưu</a>
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
            $('#file_display').val(filename);
            $('#file_name').val(filename);
        });
    });
</script>