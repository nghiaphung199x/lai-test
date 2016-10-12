
		<div class="gantt_cal_ltitle" style="cursor: pointer;"><span class="gantt_mark">&nbsp;</span>
			<span class="gantt_time">Thêm mới công việc</span>
		</div>
		<div class="toolbars">
			<ul class="list clearfix">
				<li class="btn-save" id="btn_save_templae_task"><a href="javascript:;"><i class="fa fa-floppy-o"></i>Lưu</a></li>
				<li class="btn-cancel"><a href="javascript:;" onclick="cancel('quick');"><i class="fa fa-times-circle"></i>Đóng</a></li>
			</ul>
		</div>
		<div class="gantt_cal_larea">
			<form method="POST" name="template_task_form" id="template_task_form" class="form-horizontal" enctype="multipart/form-data">
				<div class="clearfix hang" style="margin-bottom: 10px;">
					<div class="row">
						<div class="col-lg-12">
							<div class="form-group">
								<label class="col-md-3 col-lg-2 control-label">Tên công việc</label>
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

		cancel('quick');
    }
});
</script>