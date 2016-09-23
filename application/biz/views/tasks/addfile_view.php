
		<div class="gantt_cal_ltitle" style="cursor: pointer;"><span class="gantt_mark">&nbsp;</span>
			<span class="gantt_time">Thêm mới file</span>
		</div>
		<div class="toolbars">
			<ul class="list clearfix">
				<li class="btn-save"><a href="javascript:;" onclick="save_file();"><i class="fa fa-floppy-o"></i>Lưu</a></li>
				<li class="btn-cancel"><a href="javascript:;" onclick="cancel('quick');"><i class="fa fa-times-circle"></i>Đóng</a></li>
			</ul>
		</div>
		<div class="gantt_cal_larea">
			<form method="POST" name="file_form" id="file_form" class="form-horizontal" enctype="multipart/form-data">
				<input type="hidden" name="task_id" value="<?php echo $arrParam['task_id']; ?>" />
				<div class="clearfix hang" style="margin-bottom: 10px;">
					<div class="row">
						<div class="col-lg-12">
							<div class="form-group">
								<label class="col-md-3 col-lg-2 control-label">Tên tài liệu</label>
								<div class="col-md-9 col-lg-10">
									<input type="text" name="name" value="" class="form-control">
									<span for="name" class="text-danger" class="errors"></span>
								</div>
							</div>
							<div class="form-group">
								<label class="col-md-3 col-lg-2 control-label">File Upload</label>
								<div class="col-md-9 col-lg-10">
									<input type="file" name="file_upload" id="file_upload" class="filestyle file_upload" tabindex="-1" style="position: absolute; clip: rect(0px 0px 0px 0px);">
									<div class="bootstrap-filestyle input-group"><input type="text" name="file_display" id="file_display" class="form-control " disabled=""> <span class="group-span-filestyle input-group-btn" tabindex="0"><label for="image_id" class="btn btn-file-upload "><span class="glyphicon glyphicon-folder-open"></span> <span class="buttonText" id="choose_file">Choose file</span></label></span></div>
									<span for="file_upload" class="text-danger" class="errors"></span>
								</div>
							</div>
							<div class="form-group">
								<label class="col-md-3 col-lg-2 control-label">Tên file</label>
								<div class="col-md-9 col-lg-10">
									<input type="text" name="file_name" id="file_name" value="" class="form-control">
									<span for="file_name" class="text-danger" class="errors"></span>
								</div>
							</div>
							<div class="form-group">
								<label class="col-md-3 col-lg-2 control-label">Mô tả</label>
								<div class="col-md-9 col-lg-10">
									<textarea name="excerpt" class="form-control"></textarea>
								</div>
							</div>
						</div>
					</div>

				</div>
			</form>
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