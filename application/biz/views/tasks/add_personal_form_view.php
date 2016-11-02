<?php
    $trangthai_arr = array('Chưa thực hiện', 'Đang thực hiện', 'Hoàn thành', 'Đóng/dừng', 'Không thực hiện');
    $prioty_arr    = array('Rất cao', 'Cao', 'Trung bình', 'Thấp', 'Rất thấp');
    $id_admin      = $user_info['id'];
?>
<div class="modal-dialog modal-lg" role="document">
<div class="modal-content">
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true" class="x-close">×</span></button>
    <h4 class="modal-title">Thêm mới Công việc cá nhân</h4>
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
    </ul>
</div>
<div class="modal-body">
<form method="POST" name="task_form" id="task_form" class="form-horizontal">
<div class="tabs" id="basic_manager" style="display: block;">
    <div class="clearfix hang" style="margin-bottom: 10px;">
        <div class="col-lg-12">
            <div class="form-group">
                <label for="name" class="col-md-3 col-lg-2 control-label required">Tên công việc</label>
                <div class="col-md-9 col-lg-10">
                    <input type="text" name="name" value="" class="form-control" />
                    <span for="name" class="text-danger errors"></span>
                </div>
            </div>
        </div>

        <div class="col-lg-12">
            <div class="form-group">
                <label for="first_name" class="col-md-3 col-lg-2 control-label ">Mô tả</label>
                <div class="col-md-9 col-lg-10">
                    <textarea name="detail" class="form-control"></textarea>
                    <span for="detail" class="text-danger errors"></span>
                </div>
            </div>
        </div>

        <div class="col-lg-6" style="padding-right: 10px">
            <div class="form-group">
                <label class="col-md-3 col-lg-4 control-label required">Bắt đầu</label>
                <div class="col-md-9 col-lg-8">
                    <input type="text" name="date_start" class="form-control datepicker" />
                    <span for="date_start" class="text-danger errors"></span>
                </div>
            </div>
        </div>

        <div class="col-lg-6" style="padding-left: 10px;">
            <div class="form-group">
                <label class="col-md-3 col-lg-4 control-label required">Kết thúc</label>
                <div class="col-md-9 col-lg-8">
                    <input type="text" name="date_end" class="form-control datepicker" />
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
                            <option value="<?php echo $key; ?>"><?php echo $val; ?></option>
                        <?php
                        }
                        ?>

                    </select>
                    <input type="number" name="progress" value="0" class="form-control"/>
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
                            <option value="<?php echo $key; ?>"<?php if($key == 2) echo ' selected'; ?>><?php echo $val; ?></option>
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
                            <input type="text" autocomplete="off" id="implement_result" class="quick_search" />
                            <div class="result">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
    <?php
    }else {
    ?>
        <input type="hidden" name="implement[]" class="implement" id="implement_<?php echo $id_admin; ?>" value="<?php echo $id_admin; ?>">
    <?php
    }
    ?>
    </div>

</div>
</form>
</div>
</div>
</div>