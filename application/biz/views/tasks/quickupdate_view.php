<?php 
	$project_id = $item['project_id'];
	$date_start = $item['date_start'];
	$date_end   = $item['date_end'];
	$parent   	= $item['parent'];
	$id  		= $item['id'];
	$pheduyet  	= $item['pheduyet'];
	$progress  	= $item['progress'] * 100;;
	
	$trangthai_arr = array('Chưa thực hiện', 'Đang thực hiện', 'Hoàn thành', 'Đóng/dừng', 'Không thực hiện');
	$prioty_arr    = array('Rất cao', 'Cao', 'Trung bình', 'Thấp', 'Rất thấp');
	
	$btnPheduyet = true;
	if($parent > 0) {
		$title = $item['name'];
		
		//check phê duyệt
		if(!in_array($user_info['id'], $item['is_pheduyet_parent']))
			$btnPheduyet = false;
		
	}else {
		$title = 'Dự án "'.$item['name'].'"';
		$btnPheduyet = false;
	}
	
	if($pheduyet == 1)
		$btnPheduyet = false;
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
                <li class="btn-cancel"><a href="javascript:;" onclick="cancel('full', 'new');"><i class="fa fa-times-circle"></i>Đóng</a></li>
                <?php if($btnPheduyet == true):?>
                    <li class="btn-pheduyet"><a href="javascript:;" onclick="pheduyet();"><i class="fa fa-gavel"></i>Duyệt</a></li>
                <?php endif;?>
            </ul>
        </div>
        <div class="arrord_nav">
            <ul class="list clearfix">
                <li data-id="progress_manager"><span class="title">Tiến độ</span></li>
                <li data-id="detail_manager"><span class="title">Chi tiết</span></li>
            </ul>
        </div>
        <div class="modal-body">
            <form method="POST" name="task_form" id="task_form" class="form-horizontal">
                <input type="hidden" name="id" id="task_id" value="<?php echo $id; ?>" />
                <input type="hidden" name="parent" value="<?php echo $parent; ?>" />
                <input type="hidden" name="project_id" value="<?php echo $project_id; ?>" />
                <div class="manage-table tabs" style="display: block;" id="progress_manager">
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
                <div class="manage-table tabs" id="detail_manager">
                    <table width="100%" cellpadding="7" class="x-info" style="border:0">
                        <tbody>
                        <tr>
                            <td class="x-info-top" colspan="4" style="padding-left: 10px; padding-right: 10px; border: 0 !important;">
                                <span class="tl" style="font-weight: bold;"><i class="fa fa-pencil"></i> Thông tin chi tiết</span>
                            </td>
                        </tr>
                        <tr>
                            <td class="x-info-label"><?php echo $congviec_title;  ?></td>
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
                            <td class="x-info-label">Khách hàng</td>
                            <td class="x-info-content" style="font-weight: bold;" colspan="3"><?php echo $customer_names; ?></td>
                        </tr>
                        <tr>
                            <td class="x-info-label">Bắt đầu</td>
                            <td class="x-info-content"><?php echo $date_start; ?></td>
                            <td class="x-info-label">Kết thúc</td>
                            <td class="x-info-content"><?php echo $date_end; ?></td>
                        </tr>

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
                                    <li><a href="#" target="_blank">bo-tai-lieu-thu-vien.zip (2077302 Byte)</a></li>
                                    <li><a href="#" target="_blank">tai-lieu-tuyet-mat.rar (248245 Byte)</a></li>
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
                                    <p class="frm-checkbox">
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