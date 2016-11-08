<?php
$id   		= $item['id'];
$name   	= $item['name'];
$color   	= $item['color'];
$detail 	= nl2br($item['detail']);
$progress 	= $item['progress'];
$percent 	= $item['percent'];
$parent 	= $item['parent'];
$project_id = $item['project_id'];
$date_start = $item['date_start'];
$date_end 	= $item['date_end'];
$duration 	= $item['duration'];
$trangthai  = $item['trangthai'];
$prioty 	= $item['prioty'];
$pheduyet   = $item['pheduyet'];
$date_finish= $item['date_finish'];
$pheduyet_note = nl2br($item['pheduyet_note']);
$project_name  = $project_item['name'];
$created_by_name = $item['created_by_name'];

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

if($pheduyet >= 0)
    $btnPheduyet = false;

if($pheduyet == -1)
    $name_ext = ' (Chờ phê duyệt)';
elseif($pheduyet == 0)
    $name_ext = ' (Không được phê duyệt)';

$styleBasic    = ' style="display: block"';
$styleDetail   = ' style="margin-top: -10px;"';

if($pheduyet == 0) {
    $styleBasic  = '';
    $styleDetail = ' style="display: block; margin-top: -10px;"';
}
?>
<div class="modal-dialog modal-lg" role="document">
<div class="modal-content">
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true" class="x-close">×</span></button>
    <h4 class="modal-title"><?php echo $title; ?></h4>
</div>
<div class="toolbars">
    <ul class="list clearfix">
        <li class="btn-save"><a href="javascript:;" onclick="edit_congviec();"><i class="fa fa-floppy-o"></i>Lưu</a></li>
        <li class="btn-delete"><a href="javascript:;" onclick="delete_congviec(<?php echo $id; ?>);"><i class="fa fa-times"></i>Xóa</a></li>
        <li class="btn-cancel"><a href="javascript:;" onclick="cancel('full', 'new');"><i class="fa fa-times-circle"></i>Đóng</a></li>
        <?php if($btnPheduyet == true):?>
            <li class="btn-pheduyet"><a href="javascript:;" onclick="pheduyet();"><i class="fa fa-gavel"></i>Duyệt</a></li>
        <?php endif;?>
    </ul>
</div>
<div class="arrord_nav">
    <ul class="list clearfix">
<?php if($pheduyet != 0): ?>
        <li class="active" data-id="basic_manager"><span class="title">Cơ bản</span></li>
<?php endif; ?>
<?php if($pheduyet == 1 || $pheduyet == 2): ?>
        <li data-id="progress_manager"><span class="title">Tiến độ</span></li>
<?php endif; ?>
<?php if($pheduyet != 0): ?>
        <li data-id="file_manager"><span class="title">Tài liệu</span></li>
<?php endif; ?>
        <li data-id="detail_manager"><span class="title">Chi tiết</span></li>
    </ul>
</div>
<div class="modal-body">
<form method="POST" name="task_form" id="task_form" class="form-horizontal">
<input type="hidden" name="id" id="task_id" value="<?php echo $id; ?>" />
<input type="hidden" name="parent" value="<?php echo $parent; ?>" />
<input type="hidden" name="project_id" value="<?php echo $project_id; ?>" />
<div class="tabs" id="basic_manager"<?php echo $styleBasic; ?>>
<div class="clearfix hang" style="margin-bottom: 10px;">
    <div class="col-lg-12">
        <div class="form-group">
            <label for="name" class="col-md-3 col-lg-2 control-label required"><?php echo $congviec_title; ?></label>
            <div class="col-md-9 col-lg-10">
                <input type="text" name="name" value="<?php echo $name; ?>" class="form-control" />
                <span for="name" class="text-danger errors"></span>
            </div>
        </div>
    </div>
    <div class="col-lg-12" style="display: none;">
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
                <label for="first_name" class="col-md-3 col-lg-2 control-label ">Tỷ lệ</label>
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
                <div class="col-lg-6" style="padding-right: 10px">
                    <div class="form-group">
                        <label class="col-md-3 col-lg-4 control-label required">Bắt đầu</label>
                        <div class="col-md-9 col-lg-8">
                            <input type="text" name="date_start" class="form-control datepicker" value="<?php echo $date_start; ?>" />
                            <span for="date_start" class="text-danger errors"></span>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6" style="padding-left: 10px;">
                    <div class="form-group">
                        <label class="col-md-3 col-lg-4 control-label required">Kết thúc</label>
                        <div class="col-md-9 col-lg-8">
                            <input type="text" name="date_end" class="form-control datepicker" value="<?php echo $date_end; ?>" />
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
                            <div class="x-select-users" x-name="xem" id="xem_list" x-title="Ng??i ???c xem" style="display: inline-block; width: 100%;" onclick="foucs(this);">
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
                            <div class="x-select-users" x-name="implement" id="implement_list" x-title="Ng??i ph? trách" style="display: inline-block; width: 100%;" onclick="foucs(this);">
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
        <?php endif; ?>
    </div>

</div>
</div>
<?php if($pheduyet == 1 || $pheduyet == 2): ?>
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
<?php endif; ?>

<div class="manage-table manage-table-file tabs" id="file_manager">
    <div class="manage-row-options 2 hidden">
        <div class="control">
            <a href="javascript:;" class="btn btn-red btn-lg delete_inactive" title="Sửa" onclick="edit_file();"><span class="">Sửa</span></a>
            <a href="javascript:;" class="btn btn-delete" onclick="delete_file();">Xóa lựa chọn</a>
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
<div class="manage-table tabs" id="detail_manager"<?php echo $styleDetail; ?>>
    <table width="100%" cellpadding="7" class="x-info" style="border:0">
        <tbody>
        <tr>
            <td class="x-info-top" colspan="4" style="padding-left: 5px; padding-right: 10px; font-size: 16px; border: 0 !important;">
                <span class="tl" style="font-weight: bold;"><i class="fa fa-pencil"></i> Thông tin chi tiết</span>
            </td>
        </tr>
        <tr>
            <td class="x-info-label"><?php echo $congviec_title;  ?></td>
            <td class="x-info-content" style="font-weight: bold;" colspan="3"><?php echo $name . $name_ext; ?></td>
        </tr>
        <?php if($pheduyet == 0 && !empty($pheduyet_note)):?>
            <tr>
                <td class="x-info-label">Lý do</td>
                <td class="x-info-content" colspan="3"><?php echo $pheduyet_note; ?></td>
            </tr>
        <?php endif; ?>
        <?php
        if(!empty($item['customers'])){
            foreach($item['customers'] as $val)
                $customer_names[] = $val['name'];

            $customer_names = implode(', ', $customer_names);

        }
        ?>
        <tr>
            <td class="x-info-label">Khách hàng</td>
            <td class="x-info-content" style="font-weight: bold;" colspan="3"><?php echo $customer_names; ?></td>
        </tr>
        <tr>
            <td class="x-info-label">Bắt đầu</td>
            <td class="x-info-content"><?php echo $date_start; ?></td>
            <td class="x-info-label">Kết thúc</td>
            <td class="x-info-content"><?php echo $date_end; ?></td>
        </tr>
        <?php if($trangthai == 2):?>
            <tr>
                <td class="x-info-label">Thực tế</td>
                <td class="x-info-content" colspan="3" style="font-weight: bold;"><?php echo $date_finish; ?></td>
            </tr>
        <?php endif; ?>
        <tr>
            <td class="x-info-label">Tình trạng</td>
            <td class="x-info-content"><?php echo $trangthai_arr[$trangthai]; ?></td>
            <td class="x-info-label">Dự án</td>
            <td class="x-info-content"><?php echo $project_name; ?></td>
        </tr>
        <tr>
            <td class="x-info-label">Tiến độ</td>
            <td class="x-info-content"><?php echo $progress; ?>%</td>
            <td class="x-info-label">Mức ưu tiên</td>
            <td class="x-info-content"><?php echo $prioty_arr[$prioty]; ?></td>
        </tr>
        <tr>
            <td class="x-info-label">Phụ trách</td>
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
            <td class="x-info-label">Người tạo</td>
            <td class="x-info-content" colspan="3"><span class="root"><?php echo $created_by_name; ?></span></td>
        </tr>
        <tr>
            <td class="x-info-label">Mô tả</td>
            <td class="x-info-content" colspan="3"><?php echo $detail; ?></td>
        </tr>
        <tr>
            <td class="x-info-label" style="border-bottom: inherit; border-bottom: 1px solid #d7dce5;"">Tài liệu đính kèm</td>
            <td class="x-info-content" colspan="3" style="vertical-align: middle; border-bottom: 1px solid #d7dce5;">
                <ul class="attach-file">
                    <?php
                    if(!empty($item['files'])) {
                        $upload_dir = base_url() . 'assets/tasks/files/';
                        foreach($item['files'] as $val) {
                            $file_name = $val['file_name'];
                            $size      = $val['size'] . ' Bytes';
                            $link      = $upload_dir . $file_name;
                            ?>
                            <li><a href="<?php echo $link; ?>" target="_blank"><?php echo $file_name; ?> (<?php echo $size; ?>)</a></li>
                        <?php
                        }
                    }else {
                        ?>
                        <li>Không có File đính kèm.</li>
                    <?php
                    }
                    ?>

                </ul>
            </td>
        </tr>
        </tbody>

    </table>
    <?php if($no_comment != true): ?>
        <div id="comment_section">
            <div class="title"><i class="fa fa-comment"></i> Ý kiến thảo luận</div>
            <div method="POST" id="task_comment" class="frm-comment fn-comment">
                <input type="hidden" name="task_id" id="task_id" value="<?php echo $id; ?>" />
                <input type="hidden" name="parent" id="parent" value="<?php echo $parent; ?>" />
                <p class="avatar"><img class="fn-useravatar" src="http://data.ht/images/no-avatar.png"></p>
                <div class="wrap-comment">
                    <textarea name="content" id="comment_content" cols="30" rows="10"></textarea>
                    <p class="frm-checkbox" style="display: none;">
                        <span>Đính kèm</span>
                    </p>
                    <input type="button" value="Bình luận" name="btnSubmit" id="btnComment" class="button btn-dark-blue pull-right" />
                </div>
            </div>
            <ul id="commentList" class="list-comment"></ul>
            <div class="phantrang"></div>

        </div>
    <?php endif; ?>
</div>
</form>
</div>
</div>

</div>
<script type="text/javascript">
    $( document ).ready(function() {
        load_list('progress', 1);
        load_list('file', 1);
        countTiendo();
        $('#color').colorpicker({color: '<?php echo $color; ?>'});
        $('#add_navigation .title').click(function(e){
            if(!$( this ).hasClass( "active" )) {
                $('#add_navigation .active').parent().find('.content').slideUp();
                $('#add_navigation .active').removeClass('active');
                $(this).addClass('active');

                var content_show = $(this).attr('data-id');
                $('#add_navigation #'+ content_show).slideDown();
            }
        });
        $( "#my_modal .arrord_nav ul.list > li" ).click(function() {
            $( "#my_modal .arrord_nav ul.list > li" ).removeClass('active');
            var data_id = $(this).attr('data-id');
            $('#my_modal .tabs').hide();
            $(this).addClass('active');
            $('#'+data_id).show();
        });

        var task_id = $('#task_id').val();
        load_comment(task_id, 1);
    });
</script>