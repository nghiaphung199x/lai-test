<div class="gantt_cal_ltitle" style="cursor: pointer;"><span class="gantt_mark">&nbsp;</span>
	<span class="gantt_time">Danh sách công việc</span>
	<span class="gantt_mark" onclick="cancel('quick');"><i class="fa fa-times"></i></span>
</div>
<div class="gantt_cal_larea">
	<div class="manage-table tabs" id="template_task_list">
		<div class="panel-heading">
			<h3 class="panel-title">
				<span class="tieude" data-id="progress_danhsach">Danh sách</span>
				<span id="count_template_task" title="total suppliers" class="badge bg-primary tip-left"><?php echo count($items); ?></span>
			</h3>
		</div>
		<div class="panel-body nopadding table_holder table-responsive table_list">
			<table class="tablesorter table table-hover sortable_table">
				<tbody>
<?php 
		if(!empty($items)) {
			foreach($items as $val) {
				$id   = $val['id'];
				$name = str_repeat("&nbsp&nbsp&nbsp",$val['level'] - 1) . '<span data-editable>'.$val['name'].'</span>';
				if(isset($orderings[$val['id']])) {
					$child_ids = $orderings[$val['id']];
					$child_ids = implode(',', $child_ids);
				}
					
?>
					<tr>
						<td><?php echo $name; ?></td>
						<td class="center"><a href="javascript:;" data-id="<?php echo $id; ?>" data-child="<?php echo $child_ids; ?>" onclick="del_template_task(this);">Xóa</a></td>
					</tr>
<?php 
			}
		}else {
?>
					<tr><td colspan="2"><div class="col-log-12" style="text-align: center; color: #efcb41;">Không có dữ liệu hiển thị</div></td></tr>
<?php 
		}
?>

				</tbody>
			</table>
		</div>
	</div>
</div>