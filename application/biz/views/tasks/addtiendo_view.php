<?php 
$trangthai_arr = array('Chưa thực hiện', 'Đang thực hiện', 'Hoàn thành', 'Đóng/dừng', 'Không thực hiện');
$prioty_arr    = array('Rất cao', 'Cao', 'Trung bình', 'Thấp', 'Rất thấp');
$task_id   = $item['id'];
$trangthai = $item['trangthai'];
$prioty    = $item['prioty'];
$progress  = $item['progress'];

$style = '';
if($item['lft'] != $item['rgt'] - 1)
	$style = ' style="width: 100%; margin-right: 0;"';
?>
<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            <h4 class="modal-title">Cập nhật tiến độ</h4>
        </div>
        <div class="modal-body">
            <form method="POST" name="task_form" id="progress_form" class="form-horizontal">
                <input type="hidden" name="task_id" value="<?php echo $task_id; ?>" />
                <div class="clearfix hang" style="margin-bottom: 10px;">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="form-group">
                                <label class="col-md-3 col-lg-2 control-label">Trạng thái</label>
                                <div class="col-md-9 col-lg-10">
                                    <select name="trangthai" class="form-control"<?php echo $style; ?>>
                                        <?php
                                        foreach($trangthai_arr as $key => $val) {
                                            ?>
                                            <option value="<?php echo $key; ?>"<?php if($key == $trangthai) echo ' selected'; ?>><?php echo $val; ?></option>
                                        <?php
                                        }
                                        ?>

                                    </select>
                                    <?php  if($item['lft'] == $item['rgt'] - 1){ ?>
                                        <input type="number" name="progress" value="<?php echo $progress; ?>" class="form-control"/>
                                    <?php  }else {?>

                                        <input type="hidden" name="progress" value="-1" class="form-control"/>
                                    <?php }?>

                                </div>
                            </div>

                        </div>
                        <div class="col-lg-12">
                            <div class="form-group">
                                <label class="col-md-3 col-lg-2 control-label">Ưu tiên</label>
                                <div class="col-md-9 col-lg-10">
                                    <select name="prioty" class="form-control">
                                        <?php
                                        foreach($prioty_arr as $key => $val) {
                                            ?>
                                            <option value="<?php echo $key; ?>"<?php if($key == $prioty) echo ' selected'; ?>><?php echo $val; ?></option>
                                        <?php
                                        }
                                        ?>

                                    </select>
                                </div>
                            </div>

                        </div>
                        <div class="col-lg-12">
                            <div class="form-group">
                                <label class="col-md-3 col-lg-2 control-label">Ghi chú</label>
                                <div class="col-md-9 col-lg-10">
                                    <textarea name="note" class="form-control"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </form>
        </div>
        <div class="modal-footer">
            <a href="javascript:;" onclick="save_tiendo();" class="btn btn-primary">Lưu</a>
        </div>
    </div>
</div>