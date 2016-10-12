<?php 
	$id   		= $item['id'];
	$name   	= $item['name'];
	$detail 	= nl2br($item['detail']);
	$progress 	= $item['progress'] * 100;
	$parent 	= $item['parent'];
	$project_id = $item['project_id'];
	$date_start = $item['date_start'];
	$date_end 	= $item['date_end'];
	$duration 	= $item['duration'];
	$trangthai  = $item['trangthai'];
	$prioty 	= $item['prioty'];
	$pheduyet   = $item['pheduyet'];
	
	$project_name   	= $project_item['name'];
	$created_by_name   	= $item['created_by_name'];
	
	$task_permission = $user_info['task_permission'];

	$btnPheduyet = true;

	if($parent > 0) {
		$congviec_title = 'Tên công việc';
		$title = 'Công việc thuộc "'.$parent_item['name'].'"';

		//check phê duyệt
		if(!in_array($user_info['id'], $item['is_pheduyet_parent']))
			$btnPheduyet = false;
		
	}else{
		$title = 'Dự án "'.$name.'"';
		$congviec_title = 'Tên dự án';
		
		$btnPheduyet = false;
	}
	
	$trangthai_arr = array('Chưa thực hiện', 'Đang thực hiện', 'Hoàn thành', 'Đóng/dừng', 'Không thực hiện');
	$prioty_arr    = array('Rất cao', 'Cao', 'Trung bình', 'Thấp', 'Rất thấp');
	
	if($pheduyet == 1)
		$btnPheduyet = false;

	if($is_xem == true && $task != 'quick')
		$style = ' padding-top: 0';
	else
		$style = ' padding-top: 5px';

?>	
<?php if($arrParam['t'] != 'quick' && $btnPheduyet == true):?>
		<div class="gantt_cal_ltitle" style="cursor: pointer;"><span class="gantt_mark">&nbsp;</span>
			<span class="gantt_time"><?php echo $title; ?></span>
		</div>
		<div class="toolbars">
			<ul class="list clearfix">

				<li class="btn-cancel"><a href="javascript:;" onclick="cancel();"><i class="fa fa-times-circle"></i>Hủy bỏ</a></li>		
				<li class="btn-pheduyet right"><a href="javascript:;"><i class="fa fa-gavel"></i>Phê duyệt</a></li>
			</ul>
		</div>
<?php endif;?>

		<div class="gantt_cal_larea" style="height: 450px; overflow: auto;<?php echo $style; ?>">
			<table width="100%" cellpadding="7" class="x-info" style="border:0">
		        <tbody>
		            <tr>
		                <td class="x-info-top" colspan="4" style="background: #489ee7; color: white; border: 0 !important;">
			                <span class="tl">Thông tin chi tiết</span>
<?php if($arrParam['t'] == 'quick'):?>
			                <span class="gantt_mark" onclick="cancel();"><i class="fa fa-times"></i></span>
<?php endif;?>		                
		                </td>
		            </tr>
		            <tr>
		                <td class="x-info-label no-border-left"><?php echo $congviec_title;  ?></td>
		                <td class="x-info-content" style="color: red;font-weight: bold;" colspan="3"><?php echo $name; ?></td>
		            </tr>
<?php 
	if(!empty($item['customers'])){
		foreach($item['customers'] as $val)
			$customer_names[] = $val['name'];
		
		$customer_names = implode(', ', $customer_names);
		
	}
?>
		            <tr>
		                <td class="x-info-label no-border-left">Khách hàng</td>
		                <td class="x-info-content" style="font-weight: bold;" colspan="3"><?php echo $customer_names; ?></td>
		            </tr>
		            <tr>
		                <td class="x-info-label  no-border-left">Bắt đầu</td>
		                <td class="x-info-content"><?php echo $date_start; ?></td>
		                <td class="x-info-label">Kết thúc</td>
		                <td class="x-info-content"><?php echo $date_end; ?></td>
		            </tr>
		            <tr>
		                <td class="x-info-label  no-border-left">Tình trạng</td>
		                <td class="x-info-content"><?php echo $trangthai_arr[$trangthai]; ?></td>
		                <td class="x-info-label">Dự án</td>
		                <td class="x-info-content"><?php echo $project_name; ?></td>
		            </tr>
		            <tr>
		                <td class="x-info-label  no-border-left">Tiến độ</td>
		                <td class="x-info-content"><?php echo $progress; ?>%</td>
		                <td class="x-info-label">Mức ưu tiên</td>
		                <td class="x-info-content"><?php echo $prioty_arr[$prioty]; ?></td>
		            </tr>
		            <tr>
		                <td class="x-info-label  no-border-left">Phụ trách</td>
		                <td class="x-info-content">
<?php 
	if(!empty($item['is_implement'])) {
		foreach($item['is_implement'] as $key => $val) {
			$implement_ids = array();
			$keyArr = explode('-', $key);

			if($keyArr[0] == $id)
				$implement_ids[] = $val['id'];
			
			$implement[$val['id']] = $val['username'];
		}

		foreach($implement as $user_id => $user_name) {
			if(in_array($user_id, $implement_ids))
				$implement_names[] = '<span class="root">'.$user_name.'</span>';
			else
				$implement_names[] = '<span>'.$user_name.'</span>';
		}
		
		$implement_names = implode(', ', $implement_names);
		echo $implement_names;
	}
?>        
		                </td>
		                <td class="x-info-label">Người được xem</td>
		                <td class="x-info-content">
<?php 
	if(!empty($item['is_xem'])) {
		foreach($item['is_xem'] as $key => $val) {
			$xem_ids = array();
			$keyArr = explode('-', $key);

			if($keyArr[0] == $id)
				$xem_ids[] = $val['id'];
			
			$xem[$val['id']] = $val['username'];
		}

		foreach($xem as $user_id => $user_name) {
			if(in_array($user_id, $xem_ids))
				$xem_names[] = '<span class="root">'.$user_name.'</span>';
			else
				$xem_names[] = '<span>'.$user_name.'</span>';
		}
		
		$xem_names = implode(', ', $xem_names);
		echo $xem_names;
	}
?>                   
		                </td>
		            </tr>
		            <tr>
		                <td class="x-info-label  no-border-left">Người tạo</td>
		                <td class="x-info-content" colspan="3"><span class="root"><?php echo $created_by_name; ?></span></td>
		            </tr>
		            <tr>
		                <td class="x-info-label  no-border-left">Mô tả</td>
		                <td class="x-info-content" colspan="3"><?php echo $detail; ?></td>
		            </tr>
		            <tr>
		                <td class="x-info-label  no-border-left">Tài liệu đính kèm</td>
		                <td class="x-info-content" colspan="3" style="vertical-align: middle;">
<?php 
		if(!empty($item['files'])) {
?>
		                	<ul class="attach-file">
<?php 
			foreach($item['files'] as $file) {
				$link 		= base_url() . 'assets/tasks/files/' . $file['file_name'];
				$file_name  = $file['file_name'];
?>
		                		<li><i class="fa fa-file-code-o"></i><a href="<?php echo $link; ?>" target="_blank"><?php echo $file_name; ?></a></li>
<?php 
			}
?>

		                	</ul>
<?php 
		}else 
			echo ' <i>Không có file đính kèm.</i>';
?>

		                </td>
		            </tr>
		        </tbody>
		    </table>
<?php 
	if($no_comment != true) {
?>
			<div id="comment_section">
				<div class="title">Ý kiến thảo luận</div>
				<form method="POST" id="task_comment" class="frm-comment fn-comment">
					<input type="hidden" name="task_id" id="task_id" value="<?php echo $id; ?>" />
					<input type="hidden" name="parent" id="parent" value="<?php echo $parent; ?>" />
					<p class="avatar"><img class="fn-useravatar" src="http://data.ht/images/no-avatar.png"></p>
					<div class="wrap-comment">
						<textarea name="content" id="comment_content" cols="30" rows="10"></textarea>
						<p class="frm-checkbox">
							<span>Đính kèm</span>
						</p>
						<input type="button" value="Bình luận" name="btnSubmit" id="btnComment" class="button btn-dark-blue pull-right" />
					</div>
				</form>
				<ul id="commentList" class="list-comment"></ul>
				<div class="phantrang"></div>
			
			</div>
<?php 
	}
?>   

		</div>
<script type="text/javascript">
var task_id = $('#task_id').val();
load_comment(task_id, 1);
</script>