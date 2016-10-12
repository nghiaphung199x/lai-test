<?php 
	$id   		= $item['id'];
	$name   	= $item['name'];
	$color   	= $item['color'];
	$detail 	= nl2br($item['detail']);
	$progress 	= $item['progress'] * 100;
	$percent 	= $item['percent'] * 100;
	$parent 	= $item['parent'];
	$project_id = $item['project_id'];
	$date_start = $item['date_start'];
	$date_end 	= $item['date_end'];
	$duration 	= $item['duration'];
	$trangthai  = $item['trangthai'];
	$prioty 	= $item['prioty'];
	$pheduyet   = $item['pheduyet'];
	
	$task_permission = $user_info['task_permission'];
	
	$btnPheduyet = true;
	$is_create_task = false; // có được cấp quyền tạo việc hay không
	if($parent > 0) {
		$title = 'Công việc thuộc "'.$parent_item['name'].'"';
		$congviec_title = 'Tên công việc';
	
		if(in_array('permission_all_task', $task_permission))
			$is_create_task = true;
		else {
			$project_implement = array();
			if(!empty($project_relation)) {
				foreach($project_relation as $val) {
					if($val['is_implement'] == 1)
						$project_implement[] = $val['user_id'];
				}
			}
				
			if(in_array('permission_brand_task', $task_permission) && in_array($user_info['id'], $project_implement))
				$is_create_task = true;
		}
		
		//check phê duyệt
		if(!in_array($user_info['id'], $item['is_pheduyet_parent']))
			$btnPheduyet = false;


	}else{
		$title = 'Dự án "'.$item['name'].'"';
		$congviec_title = 'Tên dự án';

		if(in_array('permisson_project', $task_permission)) 
			$is_create_task = true;
		
		$btnPheduyet = false;
	}

	$trangthai_arr = array('Chưa thực hiện', 'Đang thực hiện', 'Hoàn thành', 'Đóng/dừng', 'Không thực hiện');
	$prioty_arr    = array('Rất cao', 'Cao', 'Trung bình', 'Thấp', 'Rất thấp');

	if($pheduyet == 1)
		$btnPheduyet = false;
	
?>	
<?php if($arrParam['t'] != 'quick'):?>
		<div class="gantt_cal_ltitle" style="cursor: pointer;"><span class="gantt_mark">&nbsp;</span>
			<span class="gantt_time"><?php echo $title; ?></span>
		</div>
		<div class="toolbars">
			<ul class="list clearfix">
				<li class="btn-save"><a href="javascript:;" onclick="edit_congviec();"><i class="fa fa-floppy-o"></i>Lưu</a></li>
<?php if($btnPheduyet == true):?>		
				<li class="btn-pheduyet"><a href="javascript:;" onclick="pheduyet();"><i class="fa fa-gavel"></i>Phê duyệt</a></li>
<?php endif;?>
				<li class="btn-delete"><a href="javascript:;" onclick="delete_congviec(<?php echo $id; ?>);"><i class="fa fa-times"></i>Xóa</a></li>
				<li class="btn-detail"><a href="javascript:;" onclick="detail();"><i class="fa fa-info"></i>Chi tiết</a></li>
				<li class="btn-cancel"><a href="javascript:;" onclick="cancel('full');"><i class="fa fa-times-circle"></i>Đóng</a></li>
				
			</ul>
		</div>
<?php endif;?>
		<div class="arrord_nav">
			<ul class="list clearfix">
				<li class="active" data-id="basic_manager"><span class="title">Cơ bản</span></li>
				<li data-id="progress_manager"><span class="title">Tiến độ</span></li>
				<li data-id="file_manager"><span class="title">Tài liệu</span></li>
			</ul>
		</div>
		<div class="gantt_cal_larea">
			<form method="POST" name="task_form" id="task_form" class="form-horizontal">
				<input type="hidden" name="id" id="task_id" value="<?php echo $id; ?>" />
				<input type="hidden" name="parent" value="<?php echo $parent; ?>" />
				<input type="hidden" name="project_id" value="<?php echo $project_id; ?>" />
				<input type="hidden" name="type" value="1" />
				<div class="tabs" id="basic_manager" style="display: block;">
					<div class="clearfix hang" style="margin-bottom: 10px;">
						<div class="col-lg-12">
							<div class="form-group">
								<label for="first_name" class="col-md-3 col-lg-2 control-label"><?php echo $congviec_title; ?></label>			
								<div class="col-md-9 col-lg-10">
									<input type="text" name="name" value="<?php echo $name; ?>" class="form-control" />
									<span for="name" class="text-danger errors"></span>
								</div>
							</div>
						</div>
						<div class="col-lg-12">
							<div class="form-group">
								<label for="first_name" class="col-md-3 col-lg-2 control-label">Màu sắc</label>			
								<div class="col-md-9 col-lg-10">
									<input type="text" name="color" id="color" value="<?php echo $color; ?>" class="form-control" />
									<span for="color" class="text-danger errors"></span>
								</div>
							</div>
						</div>	
<?php if($parent > 0):?>	
						<div class="col-lg-12">
							<div class="form-group">
								<label for="first_name" class="col-md-3 col-lg-2 control-label">Tỷ lệ</label>			
								<div class="col-md-9 col-lg-10">
									<input type="number" name="percent" value="<?php echo $percent; ?>" class="form-control" />
									<span for="percent" class="text-danger errors"></span>
								</div>
							</div>
						</div>
<?php endif;?>
						<div class="col-lg-12">
							<div class="form-group">
								<label for="first_name" class="col-md-3 col-lg-2 control-label ">Mô tả</label>			
								<div class="col-md-9 col-lg-10">
									<textarea name="detail" class="form-control"><?php echo $detail; ?></textarea>
									<span for="detail" class="text-danger errors"></span>
								</div>
							</div>
						</div>
					</div>
					<div class="clearfix hang">
						<div id="add_navigation">
							<div class="title active" style="border-top: 1px solid #ccc;" data-id="thietlap_content">Thông tin</div>
							<div id="thietlap_content" class="content">
								<div class="row">
									<div class="col-lg-6">
										<div class="form-group">
											<label class="col-d-3 col-lg-4 control-label">Bắt đầu</label>
											<div class="col-md-9 col-lg-8">
												<input type="text" name="date_start" value="<?php echo $date_start; ?>" class="form-control datepicker" />
												<span for="date_start" class="text-danger errors"></span>
											</div>
										</div>
									</div>
									<div class="col-lg-6">
										<div class="form-group">
											<label class="col-md-3 col-lg-4 control-label">Kết thúc</label>
											<div class="col-md-9 col-lg-8">
												<input type="text" name="date_end" value="<?php echo $date_end; ?>" class="form-control datepicker" />
												<span for="date_end" class="text-danger errors"></span>
											</div>
										</div>
									</div>
									<div class="col-lg-12">
										<div class="form-group">
											<label class="col-md-3 col-lg-2 control-label">Khách hàng</label>
											<div class="col-md-9 col-lg-10">
												<div class="x-select-users" x-name="customer" id="customer_list" x-title="Khách hàng" style="display: inline-block; width: 100%;" onclick="foucs(this);">	
<?php 
	if(!empty($item['customers'])) {
		foreach($item['customers'] as $val) {
?>
													<span class="item"><input type="hidden" name="customer[]" class="customer" id="customer_<?php echo $val['id']; ?>" value="<?php echo $val['id']; ?>"><a><?php echo $val['name']; ?></a>&nbsp;&nbsp;<span class="x" onclick="delete_item(this);"></span></span>
<?php 
		}
	}
?>
													<input type="text" autocomplete="off" id="customer_result" class="quick_search" />
													<div class="result">			
													</div>
												</div>
											</div>
										</div>						
									</div>
									<div class="col-lg-12">
										<div class="form-group">
											<label class="col-md-3 col-lg-2 control-label">Được xem</label>
											<div class="col-md-9 col-lg-10">
												<div class="x-select-users" x-name="xem" id="xem_list" x-title="Người được xem" style="display: inline-block; width: 100%;" onclick="foucs(this);">	
<?php 
	if(!empty($item['is_xem'])) {
		foreach($item['is_xem'] as $key => $val) {
			$keyArr = explode('-', $key);
			if($keyArr[0] == $id) {
				$user_id   = $val['id'];
				$user_name = $val['username'];
?>
										<span class="item"><input type="hidden" name="xem[]" class="xem" id="xem_<?php echo $user_id; ?>" value="<?php echo $user_id; ?>"><a><?php echo $user_name; ?></a>&nbsp;&nbsp;<span class="x" onclick="delete_item(this);"></span></span>

<?php 
			}

		}
	}
?>	
													<input type="text" autocomplete="off" id="xem_result" class="quick_search" />
													<div class="result">			
													</div>
												</div>
											</div>
										</div>						
									</div>
									<div class="col-lg-12">
										<div class="form-group">
											<label class="col-md-3 col-lg-2 control-label">Phụ trách</label>
											<div class="col-md-9 col-lg-10">
												<div class="x-select-users" x-name="implement" id="implement_list" x-title="Người phụ trách" style="display: inline-block; width: 100%;" onclick="foucs(this);">	
<?php 
	if(!empty($item['is_implement'])) {
		foreach($item['is_implement'] as $key => $val) {
			$keyArr = explode('-', $key);
			if($keyArr[0] == $id) {
				$user_id   = $val['id'];
				$user_name = $val['username'];		
?>
										<span class="item"><input type="hidden" name="implement[]" class="implement" id="implement_<?php echo $user_id; ?>" value="<?php echo $user_id; ?>"><a><?php echo $user_name; ?></a>&nbsp;&nbsp;<span class="x" onclick="delete_item(this);"></span></span>

<?php 
			}
		}
	}
?>
													<input type="text" autocomplete="off" id="implement_result" class="quick_search" />
													<div class="result">
													</div>
												</div>
											</div>
										</div>						
									</div>
	<?php if($is_create_task == true):?>
									<div class="col-lg-12">
										<div class="form-group">
											<label class="col-md-3 col-lg-2 control-label">Phê duyệt tiến độ</label>
											<div class="col-md-9 col-lg-10">
												<div class="x-select-users" x-name="progress_list" id="progress_list" x-title="" style="display: inline-block; width: 100%;" onclick="foucs(this);">	
<?php 
	if(!empty($item['is_progress'])) {
		foreach($item['is_progress'] as $key => $val) {
			$keyArr = explode('-', $key);
			if($keyArr[0] == $id) {
				$user_id   = $val['id'];
				$user_name = $val['username'];		
?>
										<span class="item"><input type="hidden" name="progress_task[]" class="progress_task" id="progress_task_<?php echo $user_id; ?>" value="<?php echo $user_id; ?>"><a><?php echo $user_name; ?></a>&nbsp;&nbsp;<span class="x" onclick="delete_item(this);"></span></span>								

<?php 
			}

		}
	}
?>
													<input type="text" autocomplete="off" id="progress_result" class="quick_search" />
													<div class="result">			
													</div>
												</div>
											</div>
										</div>						
									</div>	
	<?php endif;?>								
									
								</div>
							</div>
	<?php if($is_create_task == true):?>
							<div class="title" data-id="permission_content">Cấp quyền</div>
							<div id="permission_content" class="content">
								<div class="row">
									<div class="col-lg-12">
										<div class="form-group">
											<label class="col-md-3 col-lg-2 control-label">Công việc con</label>
											<div class="col-md-9 col-lg-10">
												<div class="x-select-users" x-name="create_task_list" id="create_task_list" x-title="" style="display: inline-block; width: 100%;" onclick="foucs(this);">	
<?php 
	if(!empty($item['is_create_task'])) {
		foreach($item['is_create_task'] as $key => $val) {
			$keyArr = explode('-', $key);
			if($keyArr[0] == $id) {
				$user_id   = $val['id'];
				$user_name = $val['username'];
?>
										<span class="item"><input type="hidden" name="create_task[]" class="create_task" id="create_task_<?php echo $user_id; ?>" value="<?php echo $user_id; ?>"><a><?php echo $user_name; ?></a>&nbsp;&nbsp;<span class="x" onclick="delete_item(this);"></span></span>

<?php 
			}
		}
	}
?>	
													<input type="text" autocomplete="off" id="create_task_result" class="quick_search" />
													<div class="result">			
													</div>
												</div>
											</div>
										</div>						
									</div>		
									<div class="col-lg-12">
										<div class="form-group">
											<label class="col-md-3 col-lg-2 control-label">Phê duyệt CV</label>
											<div class="col-md-9 col-lg-10">
												<div class="x-select-users" x-name="pheduyet_task_list" id="pheduyet_task_list" x-title="" style="display: inline-block; width: 100%;" onclick="foucs(this);">	
<?php 
	if(!empty($item['is_pheduyet'])) {
		foreach($item['is_pheduyet'] as $key => $val) {
			$keyArr = explode('-', $key);
			if($keyArr[0] == $id) {
				$user_id   = $val['id'];
				$user_name = $val['username'];		
?>
										<span class="item"><input type="hidden" name="pheduyet_task[]" class="pheduyet_task" id="pheduyet_task_<?php echo $user_id; ?>" value="<?php echo $user_id; ?>"><a><?php echo $user_name; ?></a>&nbsp;&nbsp;<span class="x" onclick="delete_item(this);"></span></span>								

<?php 
			}

		}
	}
?>	
													<input type="text" autocomplete="off" id="pheduyet_result" class="quick_search" />
													<div class="result">			
													</div>
												</div>
											</div>
										</div>	
															
									</div>	
								

								</div>
							</div>
	<?php endif;?>
	
						</div>
					</div>	
				</div>
				<div class="manage-table tabs" id="progress_manager">
					<div class="control clearfix">		
						<div class="pull-left">
							<select name="fields" id="s_task_id" class="form-control" id="fields">
								<option value="0">Tất cả</option>
<?php 
if(!empty($slbTasks)) {
	foreach($slbTasks as $val) {
?>
								<option value="<?php echo $val['id']; ?>"><?php echo $val['name']; ?></option>
<?php 
	}
}
	
?>


							</select>
						</div>
						<div class="pull-right">
							<div class="buttons-list">
								<div class="pull-right-btn">
								   <a href="javascript:;" id="new-person-btn" onclick="add_tiendo();" class="btn btn-primary btn-lg" title="Thêm mới tiến độ"><span class="">Thêm mới tiến độ</span></a>	
								</div>
							</div>				
						</div>
					</div>
					<div class="panel-heading">
						<h3 class="panel-title">
							<span class="tieude active" data-id="progress_danhsach">Lịch sử</span>
							<span id="count_tiendo" title="total suppliers" class="badge bg-primary tip-left">0</span>
							
							<span class="tieude" style="margin-left: 10px;" data-id="request_list">Yêu cầu phê duyệt</span>
							<span id="count_request" title="total suppliers" class="badge bg-primary tip-left">0</span>
							
							<span class="tieude" style="margin-left: 10px;" data-id="pheduyet_list">Phê duyệt</span>
							<span id="count_pheduyet" title="total suppliers" class="badge bg-primary tip-left">0</span>
							<i class="fa fa-spinner fa-spin" id="loading_1"></i>
						</h3>
					</div>
					<div class="panel-body nopadding table_holder table-responsive table_list" id="progress_danhsach">
						<table class="tablesorter table table-hover sortable_table">
							<thead>
								<tr>
									<th style="width: 20%;" data-field="task_name">Công việc</th>
									<th style="width: 10%;" data-field="progress">Tiến độ</th>
									<th style="width: 15%;" data-field="trangthai">Tình trạng</th>
									<th style="width: 10%;" data-field="prioty">Ưu tiên</th>						
									<th data-field="username">Tài khoản</th>
									<th data-field="date_phe" style="width: 15%;">Ngày</th>
								</tr>
							</thead>
							<tbody>	
							</tbody>
						</table>	
					</div>
					<div class="panel-body nopadding table_holder table-responsive table_list" id="request_list" style="display: none;">
						<table class="tablesorter table table-hover sortable_table">
							<thead>
								<tr>
									<th data-field="task_name">Công việc</th>
									<th style="width: 5%;" data-field="progress">Tiến độ</th>
									<th style="width: 10%;" data-field="trangthai">Tình trạng</th>
									<th style="width: 10%;" data-field="prioty">Ưu tiên</th>						
									<th style="width: 15%;" data-field="created">Ngày gửi</th>
									<th style="width: 10%;" data-field="pheduyet">Phê duyệt</th>
									<th style="width: 10%;" data-field="user_pheduyet">Người phê duyệt</th>
									<th style="width: 10%;" data-field="date_pheduyet">Ngày phê duyệt</th>
									<th style="width: 10%;"></th>
								</tr>
							</thead>
							<tbody>
							</tbody>
						</table>	
					</div>
					<div class="panel-body nopadding table_holder table-responsive table_list" id="pheduyet_list" style="display: none;">
						<table class="tablesorter table table-hover sortable_table">
							<thead>
								<tr>
									<th data-field="task_name">Công việc</th>
									<th style="width: 5%;" data-field="progress">Tiến độ</th>
									<th style="width: 10%;" data-field="trangthai">Tình trạng</th>
									<th style="width: 10%;" data-field="prioty">Ưu tiên</th>						
									<th style="width: 10%;" data-field="username">Người gửi</th>
									<th style="width: 10%;" data-field="created">Ngày gửi</th>
									<th style="width: 10%;" data-field="pheduyet">Phê duyệt</th>
									<th style="width: 10%;" data-field="date_pheduyet">Ngày phê duyệt</th>
									<th style="width: 20%;"></th>
								</tr>
							</thead>
							<tbody>
							</tbody>	
						</table>
					</div>
				</div>
				<div class="manage-table manage-table-file tabs" id="file_manager">
					<div class="manage-row-options 2">
						<div class="control">	
							<a href="javascript:;" class="btn btn-red btn-lg delete_inactive" title="Sửa" onclick="edit_file();"><span class="">Sửa</span></a>		
							<a href="javascript:;" class="btn btn-lg btn-clear-selection btn-warning" onclick="delete_file();">Xóa lựa chọn</a>
						</div>
					</div>
					<div class="control clearfix">		
						<div class="pull-right">
							<div class="buttons-list">
								<div class="pull-right-btn">
								   <a href="javascript:;" id="new-person-btn" onclick="add_file();" class="btn btn-primary btn-lg" title="Thêm mới tiến độ"><span class="">Thêm mới File</span></a>	
								</div>
							</div>				
						</div>
					</div>
					
					<div class="panel-heading">
						<h3 class="panel-title">
							<span class="tieude active">Danh sách tài liệu</span>
							<span id="count_tailieu" title="total suppliers" class="badge bg-primary tip-left">0</span>
							<i class="fa fa-spinner fa-spin" id="loading_2"></i>
						</h3>
					</div>
	
					<div class="panel-body nopadding table_holder table-responsive table_list">
						<table class="tablesorter table table-hover" id="sortable_table">
							<thead>
								<tr>
									<th style="width: 50px;"><input type="checkbox"><label><span class="check_tatca"></span></label></th>
									<th data-field="name">Tên tài liệu</th>
									<th style="width: 20%;" data-field="file_name">Tên file</th>
									<th style="width: 14%;" data-field="size">Kích thước</th>
									<th style="width: 14%;" data-field="created">Ngày tạo</th>
									<th style="width: 10%;" data-field="username">Người tạo</th>
									<th style="width: 14%;" data-field="modified">Cập nhật cuối</th>
									<th style="width: 10%;">Cập nhật bởi</th>
								</tr>
							</thead>
							<tbody>
							</tbody>
						</table>
					</div>
				</div>
			</form>
		</div>
<script type="text/javascript">
$( document ).ready(function() {
	load_list('progress', 1);
	load_list('file', 1);
	countTiendo();

	$('#color').colorpicker({color: '<?php echo $color; ?>',});
	$('#add_navigation .title').click(function(e){
		if(!$( this ).hasClass( "active" )) {
			$('#add_navigation .active').parent().find('.content').slideUp();
		    $('#add_navigation .active').removeClass('active');
		    $(this).addClass('active');
		    
		    var content_show = $(this).attr('data-id');
		    $('#add_navigation #'+ content_show).slideDown();
		}
	});

	$( "#my-form .arrord_nav ul.list > li" ).click(function() {
		$( "#my-form .arrord_nav ul.list > li" ).removeClass('active');
		var data_id = $(this).attr('data-id');
		 $('#my-form .gantt_cal_larea .tabs').hide();
		 $(this).addClass('active');
		 $('#'+data_id).show();
	});
});
</script>