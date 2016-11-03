<?php
$id   		= $item['id'];
$name   	= $item['name'];
$detail 	= nl2br($item['detail']);
$progress 	= $item['progress'] * 100;
$parent 	= $item['parent'];
$date_start = $item['date_start'];
$date_end 	= $item['date_end'];
$duration 	= $item['duration'];
$trangthai  = $item['trangthai'];
$prioty 	= $item['prioty'];
$date_finish= $item['date_finish'];

$trangthai_arr = array('Chưa thực hiện', 'Đang thực hiện', 'Hoàn thành', 'Đóng/dừng', 'Không thực hiện');
$prioty_arr    = array('Rất cao', 'Cao', 'Trung bình', 'Thấp', 'Rất thấp');

?>
<div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true" class="x-close">×</span></button>
            <h4 class="modal-title">Sửa Công việc Cá nhân </h4>
        </div>
        <div class="toolbars">
            <ul class="list clearfix">
                <li class="btn-save"><a href="javascript:;" onclick="add_personal_task();"><i class="fa fa-floppy-o"></i>Lưu</a></li>
                <li class="btn-cancel"><a href="javascript:;" onclick="cancel('full', 'new');"><i class="fa fa-times-circle"></i>Đóng</a></li>
            </ul>
        </div>
        <div class="arrord_nav">
            <ul class="list clearfix">
                <li class="active" data-id="basic_manager"><span class="title">Cơ bản</span></li>
                <li data-id="progress_manager"><span class="title">Tiến độ</span></li>
                <li data-id="file_manager"><span class="title">Tài liệu</span></li>
                <li data-id="detail_manager"><span class="title">Chi tiết</span></li>
            </ul>
        </div>
        <div class="modal-body">
            <form method="POST" name="task_form" id="task_form" class="form-horizontal">
                <input type="hidden" name="task_id" id="task_id" value="<?php echo $id; ?>" />
                <div class="tabs" id="basic_manager" style="display: block;">
                    <div class="clearfix hang" style="margin-bottom: 10px;">
                        <div class="col-lg-12">
                            <div class="form-group">
                                <label for="name" class="col-md-3 col-lg-2 control-label required">Tên công việc</label>
                                <div class="col-md-9 col-lg-10">
                                    <input type="text" name="name" class="form-control" value="<?php echo $name; ?>" />
                                    <span for="name" class="text-danger errors"></span>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-12">
                            <div class="form-group">
                                <label for="first_name" class="col-md-3 col-lg-2 control-label ">Mô tả</label>
                                <div class="col-md-9 col-lg-10">
                                    <textarea name="detail" class="form-control"><?php echo $detail; ?></textarea>
                                    <span for="detail" class="text-danger errors"></span>
                                </div>
                            </div>
                        </div>

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

                        <div class="col-lg-6" style="padding-right: 10px">
                            <div class="form-group">
                                <label class="col-md-3 col-lg-4 control-label">Trạng thái</label>
                                <div class="col-md-9 col-lg-8">
                                    <select name="trangthai" class="form-control">
                                        <?php
                                        foreach($trangthai_arr as $key => $val) {
                                            ?>
                                            <option value="<?php echo $key; ?>"<?php if($trangthai == $key) echo ' selected'; ?>><?php echo $val; ?></option>
                                        <?php
                                        }
                                        ?>

                                    </select>
                                    <input type="number" name="progress" value="<?php echo $progress; ?>" class="form-control"/>
                                    <span for="progress" class="text-danger errors"></span>
                                </div>
                            </div>

                        </div>
                        <div class="col-lg-6" style="padding-left: 10px">
                            <div class="form-group">
                                <label class="col-md-3 col-lg-4 control-label">Ưu tiên</label>
                                <div class="col-md-9 col-lg-8">
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
                                <label class="col-md-3 col-lg-2 control-label">Theo dõi</label>
                                <div class="col-md-9 col-lg-10">
                                    <div class="x-select-users" x-name="xem" id="xem_list" x-title="Người được xem" style="display: inline-block; width: 100%;" onclick="foucs(this);">
                                        <?php
                                        if(!empty($item['xems'])) {
                                            foreach($item['xems'] as $key => $val) {
                                                $user_id   = $val['id'];
                                                $user_name = $val['username'];
                                        ?>
                                                    <span class="item"><input type="hidden" name="xem[]" class="xem" id="xem_<?php echo $user_id; ?>" value="<?php echo $user_id; ?>"><a><?php echo $user_name; ?></a>&nbsp;&nbsp;<span class="x" onclick="delete_item(this);"></span></span>

                                         <?php

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
                        <?php
                        if($arrParam['type'] == 'follow') {
                            ?>
                            <div class="col-lg-12">
                                <div class="form-group">
                                    <label class="col-md-3 col-lg-2 control-label">Phụ trách</label>
                                    <div class="col-md-9 col-lg-10">
                                        <div class="x-select-users" x-name="implement" id="implement_list" x-title="Người phụ trách" style="display: inline-block; width: 100%;" onclick="foucs(this);">
                                    <?php
                                    if(!empty($item['implements'])) {
                                        foreach($item['implements'] as $key => $val) {
                                            $user_id   = $val['id'];
                                            $user_name = $val['username'];
                                            ?>
                                            <span class="item"><input type="hidden" name="xem[]" class="xem" id="xem_<?php echo $user_id; ?>" value="<?php echo $user_id; ?>"><a><?php echo $user_name; ?></a>&nbsp;&nbsp;<span class="x" onclick="delete_item(this);"></span></span>

                                        <?php

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
                        <?php
                        }else {
                            if(!empty($item['implement_ids'])) {
                                foreach($item['implement_ids'] as $user_id) {
                       ?>
                                    <input type="hidden" name="implement[]" class="implement" value="<?php echo $user_id; ?>">
                       <?php
                                }
                            }

                        ?>

                        <?php
                        }
                        ?>
                    </div>

                </div>
                <div class="manage-table tabs" id="progress_manager">
                    <div class="control clearfix">
                        <div class="pull-left">

                        </div>
                        <div class="pull-right">
                            <div class="buttons-list">
                                <div class="pull-right-btn">
                                    <a href="javascript:;" id="new-person-btn" onclick="add_personal_tiendo();" class="btn btn-primary btn-lg" title="Thêm mới tiến độ"><span class="">Thêm mới tiến độ</span></a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="panel-heading">
                        <h3 class="panel-title">
                            <span class="tieude active" data-id="progress_danhsach">Lịch sử</span>
                            <span id="count_tiendo" title="total suppliers" class="badge bg-primary tip-left">0</span>

                            <i class="fa fa-spinner fa-spin" id="loading_1"></i>
                        </h3>
                    </div>
                    <div class="panel-body nopadding table_holder table-responsive table_list" id="progress_danhsach">
                        <table class="tablesorter table table-hover sortable_table" data-table="progress-personal">
                            <thead>
                            <tr>
                                <th style="width: 10%;" data-field="progress">Tiến độ</th>
                                <th data-field="trangthai">Tình trạng</th>
                                <th style="width: 10%;" data-field="prioty">Ưu tiên</th>
                                <th data-field="username" style="width: 20%;" >Tài khoản</th>
                                <th data-field="date_phe" style="width: 15%;">Ngày</th>
                            </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="manage-table manage-table-file tabs" id="file_manager">
                    <div class="manage-row-options 2 hidden" data-table="file-personal">
                        <div class="control">
                            <a href="javascript:;" class="btn btn-red btn-lg delete_inactive" title="Sửa" onclick="edit_personal_file();"><span class="">Sửa</span></a>
                            <a href="javascript:;" class="btn btn-delete" onclick="delete_personal_file();">Xóa lựa chọn</a>
                        </div>
                    </div>
                    <div class="control clearfix">
                        <div class="pull-right">
                            <div class="buttons-list">
                                <div class="pull-right-btn">
                                    <a href="javascript:;" id="new-person-btn" onclick="add_personal_file();" class="btn btn-primary btn-lg" title="Thêm mới tiến độ"><span class="">Thêm mới File</span></a>
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
                        <table class="tablesorter table table-hover" id="sortable_table" data-table="file-personal">
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
                <div class="manage-table tabs" id="detail_manager" style="margin-top: -10px;">
                    <table width="100%" cellpadding="7" class="x-info" style="border:0">
                        <tbody>
                        <tr>
                            <td class="x-info-top" colspan="4" style="padding-left: 5px; padding-right: 10px; font-size: 16px; border: 0 !important;">
                                <span class="tl" style="font-weight: bold;"><i class="fa fa-pencil"></i> Thông tin chi tiết</span>
                            </td>
                        </tr>
                        <tr>
                            <td class="x-info-label">Công việc: </td>
                            <td class="x-info-content" style="font-weight: bold;" colspan="3"><?php echo $name . $name_ext; ?></td>
                        </tr>

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
                            <td class="x-info-label">Tiến độ</td>
                            <td class="x-info-content"><?php echo $progress; ?>%</td>
                        </tr>
                        <tr>
                            <td class="x-info-label">Mức ưu tiên</td>
                            <td class="x-info-content"><?php echo $prioty_arr[$prioty]; ?></td>
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
                        </tr>
                        <tr>
                            <td class="x-info-label">Người được xem</td>
                            <td class="x-info-content" colspan="3">
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
                                    <input type="button" value="Bình luận" name="btnSubmit" onclick="comment_personal();"  class="button btn-dark-blue pull-right" />
                                </div>
                            </div>
                            <ul id="commentList" class="list-comment"></ul>
                            <div class="phantrang"></div>

                        </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script type="text/javascript">
    $( document ).ready(function() {
        load_list('progress-personal', 1);
        load_list('file-personal', 1);

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
        load_personal_comment(task_id, 1);
    });
</script>