<?php $this->load->view("partial/header"); ?>
<link href="https://hstatic.net/0/0/global/design/plugins/font-awesome/4.5.0/css/font-awesome.min.css" media="screen" rel="stylesheet" type="text/css">
<link rel="stylesheet" href="<?php echo base_url();?>assets/tasks/css/style.css" type="text/css" media="screen" />
<link rel="stylesheet" href="<?php echo base_url();?>assets/tasks/css/responsive.css" type="text/css" media="screen" />

<script type="text/javascript" src="<?php echo base_url() ?>assets/tasks/js/task-core.js" ></script>
<script type="text/javascript" src="<?php echo base_url() ?>assets/tasks/js/script.js" ></script>

<div class="manage_buttons">
<div class="manage-row-options">
	<div class="email_buttons text-center">		
		<a href="javascript:;" class="btn btn-red btn-lg" title="Xóa" onclick="delete_template();"><span class="">Xóa lựa chọn</span></a>
	</div>
</div>
<div class="cl">
	<div class="pull-left">
		<form action="" id="search_form" autocomplete="off" class="form-inline" method="post" accept-charset="utf-8">
			<div class="search no-left-border">
				<span role="status" aria-live="polite" class="ui-helper-hidden-accessible"></span><input type="text" class="form-control ui-autocomplete-input" id="s_keywords" value="" placeholder="Tìm kiếm đự án" autocomplete="off">
			</div>
			<div class="clear-block hidden">
			    <i class="ion ion-close-circled"></i>
			</div>
		</form>
	</div>
	<div class="pull-right">
		<div class="buttons-list" style="padding-top: 0;">
			<div class="pull-right-btn">
				<a href="<?php echo base_url() . 'tasks/templateAdd' ?>" id="new-person-btn" class="btn btn-primary btn-lg" title="Thêm mới Template" style="margin-top: 16px;"><span class="">Thêm mới Template</span></a>					
			</div>
		</div>				
	</div>
    <div class="cl"></div>
</div>
</div>
<div class="container-fluid" id="project_grid_list">
	<div class="row manage-table">
		<div class="panel panel-piluku">
			<div class="panel-heading" style="border: 0; padding-left: 0;">
				<div class="gantt_title">
					<h3 class="panel-title">
						<span class="tieude"><a href="<?php echo base_url() . 'tasks'; ?>">Lược đồ</a></span>
						<span class="tieude active">Danh sách</span>
						<i class="fa fa-spinner fa-spin" id="loading_1"></i>	
					</h3>
				</div>
			</div>
			<div class="panel-body">
				<div class="table-responsive">
					<table class="table tablesorter table-reports table-bordered table-tree" id="project_grid_table">
						<thead>
							<tr align="center" style="font-weight:bold">
								<td class="hidden-print" style="width: 25px;"><a href="#" class="expand_all">&nbsp</a></td>
								<td align="center" data-field="name">Tên Dự án</td>
								<td align="center" style="width: 8%;" data-field="prioty">Ưu tiên</td>
								<td align="center" style="width: 100px;" data-field="date_start">Bắt đầu</td>
								<td align="center" style="width: 100px;" data-field="date_end">Kết thúc</td>
								<td align="center" style="width: 256px;" data-field="progress">Tiến độ</td>
								<td align="center" style="width: 10%;" data-field="trangthai">Tình trạng</td>
								<td align="center" style="width: 20%;">Phụ trách</td>			
							</tr>
						</thead>
						<tbody>

						</tbody>
					</table>
				</div>
			</div>
		</div>	
	</div>
</div>
<div id="advance_task_search" class="modal fade bs-example-modal-lg search-advance-form" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true" class="x-close">×</span></button>
                <h4 class="modal-title" id="my_search_task">Tìm kiếm công việc cho "<span>[Tên dự án]</span>"</h4>
            </div>
            <div class="modal-body">
                <form class="form-horizontal form-horizontal-mobiles">
                    <input type="hidden" name="curret_project_id" id="current_project_id" value="0" />
                    <div class="form-group">
                        <label for="simple_radio" class="col-sm-3 col-md-3 col-lg-2 control-label">Bắt đầu :</label>
                        <div class="col-sm-9 col-md-2 col-lg-2">
                            <input type="radio" name="adv_date_start_radio" value="simple" checked="checked">
                            <label for="simple_radio"><span></span></label>
                            <select name="adv_date_start" id="adv_date_start" class="form-control" style="width: 150px;">
                                <option value="today">Hôm nay</option>
                                <option value="yesterday">Ngày hôm qua</option>
                                <option value="7_days_previous">7 ngày qua</option>
                                <option value="current_week">Tuần này</option>
                                <option value="previous_week">Tuần trước</option>
                                <option value="current_month">Tháng này</option>
                                <option value="previous_month">Tháng trước</option>
                                <option value="current_year">Năm nay</option>
                                <option value="previous_year">Năm trước</option>
                                <option value="all" selected="selected">Toàn bộ thời gian</option>
                            </select>
                        </div>
                    </div>
                    <div class="report_date_range_complex">
                        <div class="form-group">
                            <label for="complex_radio" class="col-sm-3 col-md-3 col-lg-2 control-label  ">Tùy chỉnh :</label>
                            <div class="col-sm-9 col-md-9 col-lg-10">
                                <input type="radio" name="adv_date_start_radio" value="complex">
                                <label for="simple_radio"><span></span></label>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="input-group input-daterange" id="reportrange">
		                                    <span class="input-group-addon bg">
					                           Từ
                                            </span>
                                            <input type="text" class="form-control date_time" name="adv_date_start_from" id="adv_date_start_from" />
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="input-group input-daterange" id="reportrange1">
		                                    <span class="input-group-addon bg">
			                                    Đến
                                            </span>
                                            <input type="text" class="form-control date_time" name="adv_date_start_to" id="adv_date_start_to">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="simple_radio" class="col-sm-3 col-md-3 col-lg-2 control-label">Kết thúc :</label>
                        <div class="col-sm-9 col-md-2 col-lg-2">
                            <input type="radio" name="adv_date_end_radio" value="simple" checked="checked">
                            <label for="simple_radio"><span></span></label>
                            <select name="adv_date_end" id="adv_date_end" class="form-control" style="width: 150px;">
                                <option value="today">Hôm nay</option>
                                <option value="yesterday">Ngày hôm qua</option>
                                <option value="7_days_previous">7 ngày qua</option>
                                <option value="current_week">Tuần này</option>
                                <option value="previous_week">Tuần trước</option>
                                <option value="current_month">Tháng này</option>
                                <option value="previous_month">Tháng trước</option>
                                <option value="current_year">Năm nay</option>
                                <option value="previous_year">Năm trước</option>
                                <option value="all" selected="selected">Toàn bộ thời gian</option>
                            </select>
                        </div>
                    </div>
                    <div class="report_date_range_complex">
                        <div class="form-group">
                            <label for="simple_radio" class="col-sm-3 col-md-3 col-lg-2 control-label  ">Tùy chỉnh :</label>
                            <div class="col-sm-9 col-md-9 col-lg-10">
                                <input type="radio" name="adv_date_end_radio" value="complex">
                                <label for="complex_radio"><span></span></label>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="input-group input-daterange" id="reportrange">
		                                    <span class="input-group-addon bg">
					                           Từ
                                            </span>
                                            <input type="text" class="form-control date_time" name="adv_date_end_from" id="adv_date_end_from">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="input-group input-daterange" id="reportrange1">
		                                    <span class="input-group-addon bg">
			                                    Đến
                                            </span>
                                            <input type="text" class="form-control date_time" name="adv_date_end_to" id="adv_date_end_to">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="simple_radio" class="col-sm-3 col-md-3 col-lg-2 control-label">Tiêu đề:</label>
                        <div class="col-sm-9 col-md-9 col-lg-10">
                            <input type="text" id="adv_name" class="form-control" />
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="simple_radio" class="col-sm-3 col-md-3 col-lg-2 control-label">Trạng thái :</label>
                        <div class="col-sm-9 col-md-9 col-lg-10">
                            <div class="x-select-users" x-name="trangthai" id="trangthai_list" x-title="Trang thái" style="display: inline-block; width: 100%;" onclick="foucs(this);">
                                <input type="text" autocomplete="off" id="trangthai_result" class="quick_search">
                                <div class="result" style="top: 27px; display: none;">

                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="simple_radio" class="col-sm-3 col-md-3 col-lg-2 control-label">Khách hàng :</label>
                        <div class="col-sm-9 col-md-9 col-lg-10">
                            <div class="x-select-users" x-name="customer" id="customer_list" x-title="Khách hàng" style="display: inline-block; width: 100%;" onclick="foucs(this);">
                                <input type="text" autocomplete="off" id="customer_result" class="quick_search">
                                <div class="result" style="top: 27px; display: none;">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="simple_radio" class="col-sm-3 col-md-3 col-lg-2 control-label">Phụ trách :</label>
                        <div class="col-sm-9 col-md-9 col-lg-10">
                            <div class="x-select-users" x-name="implement" id="implement_list" x-title="Người phụ trách" style="display: inline-block; width: 100%;" onclick="foucs(this);">
                                <input type="text" autocomplete="off" id="implement_result" class="quick_search">
                                <div class="result" style="top: 27px;">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="simple_radio" class="col-sm-3 col-md-3 col-lg-2 control-label">Theo dõi :</label>
                        <div class="col-sm-9 col-md-9 col-lg-10">
                            <div class="x-select-users" x-name="xem" id="xem_list" x-title="Người được xem" style="display: inline-block; width: 100%;" onclick="foucs(this);">
                                <input type="text" autocomplete="off" id="xem_result" class="quick_search">
                                <div class="result" style="top: 27px;">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="" class="col-sm-3 col-md-3 col-lg-2 col-sm-3 col-md-3 col-lg-2 control-label">Công việc :</label>
                        <div class="col-sm-9 col-md-9 col-lg-10">
                            <ul class="list-inline">
                                <li>
                                    <input type="checkbox" name="status[]" value="-1" id="status_-1" class="reports_selected_location_ids_checkboxes">
                                    <label for="status_-1"><span></span>Chờ xử lý</label>
                                </li>
                                <li>
                                    <input type="checkbox" name="status[]" value="0" id="status_0" class="reports_selected_location_ids_checkboxes">
                                    <label for="status_0"><span></span>Không phê duyệt</label>
                                </li>
                                <li>
                                    <input type="checkbox" name="status[]" value="1-2" id="status_1_2" class="reports_selected_location_ids_checkboxes">
                                    <label for="status_1_2"><span></span>Đã phê duyệt</label>
                                </li>
                           </ul>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="" class="col-sm-3 col-md-3 col-lg-2 col-sm-3 col-md-3 col-lg-2 control-label">Tiến độ :</label>
                        <div class="col-sm-9 col-md-9 col-lg-10">
                            <ul class="list-inline">
                                <li>
                                    <input type="checkbox" name="progress[]" value="-1" id="progress_-1" class="reports_selected_location_ids_checkboxes">
                                    <label for="progress_-1"><span></span>Chờ xử lý</label>
                                </li>
                                <li>
                                    <input type="checkbox" name="progress[]" value="0" id="progress_0" class="reports_selected_location_ids_checkboxes">
                                    <label for="progress_0"><span></span>Không phê duyệt</label>
                                </li>
                                <li>
                                    <input type="checkbox" name="progress[]" value="1-2" id="progress_1_2" class="reports_selected_location_ids_checkboxes">
                                    <label for="progress_1_2"><span></span>Đã phê duyệt</label>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="form-group" style="margin-bottom: 5px;">
                        <div class="form-actions pull-right">
                            <input type="button" name="submitf" value="Thực hiện" id="btn_search_advance" style="margin-right: 16px;" class=" submit_button btn btn-primary">
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div id="task_report" class="modal fade bs-example-modal-md" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header"> <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button> <h4 class="modal-title" id="my_report_task">Thống kê <span>[Dự án]</span></h4> </div>
            <div class="modal-body">
                <ul>
                    <li class="all">Công việc: <span>0</span></li>
                    <li class="implement">Phụ trách: <span>0</span></li>
                    <li class="xem">Theo dõi: <span>0</span></li>
                    <li class="cancel">Đóng dừng: <span>0</span></li>
                    <li class="not-done">Không thực hiện: <span>0</span></li>
                    <li class="unfulfilled">Chưa thực hiện: <span>0</span></li>
                    <li class="processing">Đang tiến hành: <span>0</span></li>
                    <li class="slow_proccessing">Chậm tiến độ: <span>0</span></li>
                    <li class="finish">Đã hoàn thành: <span>0</span></li>
                    <li class="slow-finish">Đã hoàn thành nhưng chậm tiến độ: <span>0</span></li>
                </ul>
             </div>
        </div>
    </div>
</div>
<style>
.modal .modal-title {
    font-weight: bold;
}

.search-advance-form {
    font-family: Arial;
}
.search-advance-form span.x-close {
    font-size: 21px !important;
}
.detailed-reports i.fa-search {
	font-size: 16px;
    margin-right: 0;
}

#project_grid_table td {
    padding: 4px;
}

#project_grid_table td[data-field] {
    cursor: pointer;
}
</style>
<script type="text/javascript">
function set_hidden_input() {
    var current_project_id     = $('#current_project_id').val();
    var element_parent         = $('#project_grid_table').find('tr[data-parent='+current_project_id+']');

    var search_keywords        = element_parent.find('.search_keywords');
    var search_date_type       = element_parent.find('.search_date_type');

    var s_keywords             = element_parent.find('.s_keywords');

    var s_date_start           = element_parent.find('.s_date_start');
    var s_date_start_radio     = element_parent.find('.s_date_start_radio');
    var s_date_start_from      = element_parent.find('.s_date_start_from');
    var s_date_start_to        = element_parent.find('.s_date_start_to');

    var s_date_end             = element_parent.find('.s_date_end');
    var s_date_end_radio       = element_parent.find('.s_date_end_radio');
    var s_date_end_from        = element_parent.find('.s_date_end_from');
    var s_date_end_to          = element_parent.find('.s_date_end_to');

    var s_trangthai            = element_parent.find('.s_trangthai');
    var s_customer             = element_parent.find('.s_customer');
    var s_implement            = element_parent.find('.s_implement');
    var s_xem                  = element_parent.find('.s_xem');
    var s_status               = element_parent.find('.s_status');
    var s_progress             = element_parent.find('.s_progress');

    var s_trangthai_html       = element_parent.find('.s_trangthai_html');
    var s_customer_html        = element_parent.find('.s_customer_html');
    var s_implement_html       = element_parent.find('.s_implement_html');
    var s_xem_html             = element_parent.find('.s_xem_html');

    //set values for each elements
    var adv_date_start_radio_value = $('[name="adv_date_start_radio"]:checked').val();

    s_keywords.val($('#adv_name').val());

    // date_start
    s_date_start_radio.val(adv_date_start_radio_value);
    if(adv_date_start_radio_value == 'simple') {
        var adv_date_start_value = $('#adv_date_start').val();
        var date = get_two_dates(adv_date_start_value);

        s_date_start_from.val(date.date_1);
        s_date_start_to.val(date.date_2);
        s_date_start_radio.val(adv_date_start_radio_value);
    }else {
        s_date_start.val('all');
        s_date_start_from.val($('#adv_date_start_from').val());
        s_date_start_to.val($('#adv_date_start_to').val());
    }
    s_date_start.val($('#adv_date_start').val());

    // date_end
    var adv_date_end_radio_value = $('[name="adv_date_end_radio"]:checked').val();
    s_date_end_radio.val(adv_date_end_radio_value);
    if(adv_date_end_radio_value == 'simple') {
        var adv_date_end_value = $('#adv_date_end').val();
        var date = get_two_dates(adv_date_end_value);

        s_date_end_from.val(date.date_1);
        s_date_end_to.val(date.date_2);
        s_date_end_radio.val(adv_date_end_radio_value);
    }else {
        s_date_end.val('all');
        s_date_end_from.val($('#adv_date_end_from').val());
        s_date_end_to.val($('#adv_date_end_to').val());
    }
    s_date_end.val($('#adv_date_end').val());

    // trangthai
    var item             = new Array();
    var item_string      = '';
    var item_html        = new Array();
    var item_html_string = '';
    var span_trangthai_item = $('#trangthai_list .item');
    if(span_trangthai_item.length) {
        $( span_trangthai_item ).each(function() {
            var span_element  = $(this);
            item[item.length] = span_element.find('.trangthai').val();
            item_html[item_html.length] = span_element[0].outerHTML;
        });
    }

    item_string      = item.join();
    item_html_string = item_html.join('');
    s_trangthai.val(item_string);
    s_trangthai_html.html(item_html_string);

    // customer
    var item             = new Array();
    var item_string      = '';
    var item_html        = new Array();
    var item_html_string = '';

    var span_customer_item = $('#customer_list .item');
    if(span_customer_item.length) {
        $( span_customer_item ).each(function() {
            var span_element  = $(this);
            item[item.length] = span_element.find('.customer').val();
            item_html[item_html.length] = span_element[0].outerHTML;
        });
    }

    item_string      = item.join();
    item_html_string = item_html.join('');
    s_customer.val(item_string);
    s_customer_html.html(item_html_string);

    // implement
    var item             = new Array();
    var item_string      = '';
    var item_html        = new Array();
    var item_html_string = '';

    var span_implement_item = $('#implement_list .item');
    if(span_implement_item.length) {
        $( span_implement_item ).each(function() {
            var span_element  = $(this);
            item[item.length] = span_element.find('.implement').val();
            item_html[item_html.length] = span_element[0].outerHTML;
        });
    }

    item_string      = item.join();
    item_html_string = item_html.join('');
    s_implement.val(item_string);
    s_implement_html.html(item_html_string);

    // xem
    var item             = new Array();
    var item_string      = '';
    var item_html        = new Array();
    var item_html_string = '';

    var span_xem_item = $('#xem_list .item');
    if(span_xem_item.length) {
        $( span_xem_item ).each(function() {
            var span_element  = $(this);
            item[item.length] = span_element.find('.xem').val();
            item_html[item_html.length] = span_element[0].outerHTML;
        });
    }

    item_string      = item.join();
    item_html_string = item_html.join('');
    s_xem.val(item_string);
    s_xem_html.html(item_html_string);

    //Tasks
    var checkbox = $("input[name='status[]']:checked");
    var checkbox_val = new Array();
    if(checkbox.length) {
        $( checkbox ).each(function() {
            checkbox_val[checkbox_val.length] = $(this).val();
        });
    }

    checkbox_val = checkbox_val.join(',');
    s_status.val(checkbox_val);

    // Progress
    var checkbox = $("input[name='progress[]']:checked");
    var checkbox_val = new Array();
    if(checkbox.length) {
        $( checkbox ).each(function() {
            checkbox_val[checkbox_val.length] = $(this).val();
        });
    }

    checkbox_val = checkbox_val.join(',');
    s_progress.val(checkbox_val);
    search_keywords.val('');
    search_date_type.val('0');
}

function set_form_input(project_id, task_name) {
    $('#my_search_task span').text(task_name);

    var element_parent  = $('#project_grid_table').find('tr[data-parent='+project_id+']');
    var s_keywords           = element_parent.find('.s_keywords');

    var s_date_start_radio   = element_parent.find('.s_date_start_radio');
    var date_start_value     = s_date_start_radio.val();
    var s_date_start         = element_parent.find('.s_date_start');
    var s_date_start_from    = element_parent.find('.s_date_start_from');
    var s_date_start_to      = element_parent.find('.s_date_start_to');

    var s_date_end_radio     = element_parent.find('.s_date_end_radio');
    var date_end_value       = s_date_end_radio.val();
    var s_date_end           = element_parent.find('.s_date_end');
    var s_date_end_from      = element_parent.find('.s_date_end_from');
    var s_date_end_to        = element_parent.find('.s_date_end_to');

    var s_status             = element_parent.find('.s_status');
    var s_progress           = element_parent.find('.s_progress');

    var s_trangthai_html     = element_parent.find('.s_trangthai_html');
    var s_customer_html      = element_parent.find('.s_customer_html');
    var s_implement_html     = element_parent.find('.s_implement_html');
    var s_xem_html           = element_parent.find('.s_xem_html');

    $('[name="adv_date_start_radio"]').filter('[value='+date_start_value+']').prop('checked', true);
    if(date_start_value == 'simple') {
        $('#adv_date_start').val(s_date_start.val());
    }else {
        var s_date_start_from_value = s_date_start_from.val();
        if(s_date_start_from_value != '') {
            $('#adv_date_start_from_formatted').val(convert_date(s_date_start_from_value));
            $('#adv_date_start_from').val(s_date_start_from_value);
        }

        var s_date_start_to_value = s_date_start_to.val();
        if(s_date_start_to_value != '') {
            $('#adv_date_start_to_formatted').val(convert_date(s_date_start_to_value));
            $('#adv_date_start_to').val(s_date_start_to_value);
        }
    }

    $('[name="adv_date_end_radio"]').filter('[value='+date_end_value+']').prop('checked', true);
    if(date_end_value == 'simple') {
        $('#adv_date_end').val(s_date_end.val());
    }else {
        var s_date_end_from_value = s_date_end_from.val();
        if(s_date_end_from_value != '') {
            $('#adv_date_end_from_formatted').val(convert_date(s_date_end_from_value));
            $('#adv_date_end_from').val(s_date_end_from_value);
        }

        var s_date_end_to_value = s_date_end_to.val();
        if(s_date_end_to_value != '') {
            $('#adv_date_end_to_formatted').val(convert_date(s_date_end_to_value));
            $('#adv_date_end_to').val(s_date_end_to_value);
        }
    }

    $('#adv_name').val(s_keywords.val());

    var html = s_trangthai_html.html();
    $(html).insertBefore( "#trangthai_result" );

    html = s_customer_html.html();
    $(html).insertBefore( "#customer_result" );

    html = s_implement_html.html();
    $(html).insertBefore( "#implement_result" );

    html = s_xem_html.html();
    $(html).insertBefore( "#xem_result" );

    var s_status_value = s_status.val();
    var res = new Array();
    if (s_status_value) {
        res = convert_string_checkbox(s_status_value);
        $.each(res, function( index, value ) {
            $('#status_'+value).prop('checked', true);
        });
    }

    var s_progress_value = s_progress.val();
    res = new Array();
    if (s_progress_value) {
        res = convert_string_checkbox(s_progress_value);
        $.each(res, function( index, value ) {
            $('#progress_'+value).prop('checked', true);
        });
    }
}

function reset_form() {
    $('input[name=adv_date_start_radio][value="simple"]').prop('checked', true);
    $('#adv_date_start').val('all');
    $('#adv_date_start_from_formatted').val('');
    $('#adv_date_start_from').val('');
    $('#adv_date_start_to_formatted').val('');
    $('#adv_date_start_to').val('');

    $('input[name=adv_date_end_radio][value="simple"]').prop('checked', true);
    $('#adv_date_end').val('all');
    $('#adv_date_end_from_formatted').val('');
    $('#adv_date_end_from').val('');
    $('#adv_date_end_to_formatted').val('');
    $('#adv_date_end_to').val('');

    $('#adv_name').val('');
    $('#trangthai_list span.item').remove();
    $('#customer_list span.item').remove();
    $('#implement_list span.item').remove();
    $('#xem_list span.item').remove();

}
$( document ).ready(function() {
	load_list('project-grid', 1);
    var current_project_id = 0;

	$('body').on('click','.table-tree .expand_all',function(){
        var symbol = $(this).text();
    	var tr_element = $(this).closest('tr');
    	var table_element = $(this).closest('table');
    	var id = tr_element.attr('data-tree');

    	var tr_child = table_element.find('tr[data-parent="'+id+'"]');
  		if(symbol == '+'){
  			tr_child.hide();
  			$(this).text('-');

  		}else{
            var table_child     = $('#task_childs_'+id);
            var data_content    = table_child.attr('data-content');
            if(data_content == 0) {
                load_task_childs(id, 1);
            }

  			tr_child.show();
  			$(this).text('+');
  		}
	});

    //sort
    $('body').on('click','table [data-field]',function(){
        var attr     = $(this).attr('data-field');
        var table    = $(this).closest('table');
        var table_id = table.attr('id');
        if($(this).hasClass('header')) {
            if($(this).hasClass('headerSortUp')){
                $(this).removeClass('headerSortUp');
                $(this).addClass('headerSortDown');
            }else {
                $(this).removeClass('headerSortDown');
                $(this).addClass('headerSortUp');
            }
        }else {
            table.find('td').removeClass('header');
            table.find('td').removeClass('headerSortUp');
            table.find('td').removeClass('headerSortDown');
            $(this).addClass('header headerSortUp');
        }

        if(table_id == 'project_grid_table') {
            load_list('project-grid', 1);
        }else {
            var tr_parent = table.closest('[data-parent]');
            var project_id = tr_parent.attr('data-parent');
            load_task_childs(project_id, 1);
        }
    });

    $('body').on('click','#btn_search_advance',function(){
        set_hidden_input();

        var project_id     = $('#current_project_id').val();
        load_task_childs(project_id, 1);
        $('#advance_task_search').modal('toggle');
    });

    //advance search click
    $('body').on('click','.submitf',function(){
        var task_name       = $(this).attr('data-name');
        var project_id      = $(this).attr('data-id');
        current_project_id  = project_id;

        $('#current_project_id').val(project_id);
        set_form_input(current_project_id, task_name);
        $("#advance_task_search").modal();
    });

    // statistic click
    $('body').on('click','.statistic',function(){
        var task_name       = $(this).attr('data-name');
        var project_id      = $(this).attr('data-id');
        current_project_id = project_id;

        $('#current_project_id').val(project_id);
        $('#my_report_task span').html(task_name);

        var data = new Object();
        data.project_id = project_id;

        // get filter input
        var tr_element        = $('#project_grid_table tr[data-parent="'+project_id+'"]');
        data                  = get_data_child_task(data, project_id, tr_element);
        $.ajax({
            type: "POST",
            url: BASE_URL + 'tasks/tasks_child_statistic',
            data: data,
            success: function(string){
                var result = $.parseJSON(string);
                $('#task_report li.all span').text(result.all);
                $('#task_report li.implement span').text(result.implement);
                $('#task_report li.xem span').text(result.xem);
                $('#task_report li.cancel span').text(result.cancel);
                $('#task_report li.not-done span').text(result.not_done);
                $('#task_report li.unfulfilled span').text(result.unfulfilled);
                $('#task_report li.processing span').text(result.processing);
                $('#task_report li.slow_proccessing span').text(result.slow_proccessing);
                $('#task_report li.finish span').text(result.finish);
                $('#task_report li.slow-finish span').text(result.slow_finish);

                $("#task_report").modal();
            }
        });

    });

    $('body').on('change','.search_date_type',function(){
        var value                = $(this).val();
        var element_parent       = $(this).closest('tr[data-parent]');
        var project_id           = element_parent.attr('data-parent');
        var s_date_start_to      = element_parent.find('.s_date_start_to');
        var s_date_start_from    = element_parent.find('.s_date_start_from');
        var s_date_end_to        = element_parent.find('.s_date_end_to');
        var s_date_end_from      = element_parent.find('.s_date_end_from');
        var s_trangthai          = element_parent.find('.s_trangthai');
        var s_date_start_radio   = element_parent.find('.s_date_start_radio');
        var s_date_end_radio     = element_parent.find('.s_date_end_radio');
        var s_status             = element_parent.find('.s_status');
        var s_progress           = element_parent.find('.s_progress');
        var s_customer           = element_parent.find('.s_customer');
        var s_trangthai          = element_parent.find('.s_trangthai');
        var s_implement          = element_parent.find('.s_implement');
        var s_xem                = element_parent.find('.s_xem');

        var s_trangthai_html     = element_parent.find('.s_trangthai_html');
        var s_customer_html      = element_parent.find('.s_customer_html');
        var s_implement_html     = element_parent.find('.s_implement_html');
        var s_xem_html           = element_parent.find('.s_xem_html');

        var data = {class: 'trangthai', value: 0, title: 'Chưa thực hiện'};
        var span_trangthai_0 = get_item_autocomplete(data);

        var data = {class: 'trangthai', value: 1, title: 'Đang thực hiện'};
        var span_trangthai_1 = get_item_autocomplete(data);

        //reset some element input
        s_trangthai.val('');
        s_trangthai_html.html('');
        s_customer.val('');
        s_customer_html.html('');
        s_implement.val('');
        s_implement_html.html('');
        s_xem.val('');
        s_xem_html.html('');
        s_status.val('-1,0,1,2');
        s_progress.val('-1,0,1,2');

        switch(value) {
            case 'today':
                var current_date = get_current_date();
                s_date_start_to.val(current_date + ' 23:59');
                s_date_end_from.val(current_date + ' 00:00');
                s_trangthai.val('0,1');
                s_trangthai_html.html(span_trangthai_0 + span_trangthai_1);

                s_date_start_radio.val('complex');
                s_date_end_radio.val('complex');
                break;

            case 'weekend':
                var firstDay = get_first_date_of_current_weekend();
                var lastDay = get_last_date_of_current_weekend();

                s_date_start_to.val(lastDay + ' 23:59');
                s_date_end_from.val(firstDay + ' 00:00');
                s_trangthai.val('0,1');
                s_trangthai_html.html(span_trangthai_0 + span_trangthai_1);

                s_date_start_radio.val('complex');
                s_date_end_radio.val('complex');

                break;

            case 'month':
                var firstDay = get_first_date_of_current_month();
                var lastDay = get_last_date_of_current_month();

                s_date_start_to.val(lastDay + ' 59:59');
                s_date_end_from.val(firstDay + ' 00:00');
                s_trangthai.val('0,1');
                s_trangthai_html.html(span_trangthai_0 + span_trangthai_1);

                s_date_start_radio.val('complex');
                s_date_end_radio.val('complex');
                break;

            case 'year':
                var firstDay = get_first_date_of_current_year();
                var lastDay = get_last_date_of_current_year();

                s_date_start_to.val(lastDay + ' 59:59');
                s_date_end_from.val(firstDay + ' 00:00');
                s_trangthai.val('0,1');
                s_trangthai_html.html(span_trangthai_0 + span_trangthai_1);

                s_date_start_radio.val('complex');
                s_date_end_radio.val('complex');
                break;

            default:
                s_date_start_to.val('');
                s_date_end_from.val('');
                s_trangthai.val('');
                s_trangthai_html.html('');

                s_date_start_radio.val('simple');
                s_date_end_radio.val('simple');
        }

        load_task_childs(project_id, 1);
    });

    // event when close modal
    $('#advance_task_search').on('hidden.bs.modal', function () {
        reset_form();
    })

    $('#task_report').on('hidden.bs.modal', function () {
        $('#task_report li.all span').text('0');
        $('#task_report li.implement span').text('0');
        $('#task_report li.xem span').text('0');
        $('#task_report li.cancel span').text('0');
        $('#task_report li.not-done span').text('0');
        $('#task_report li.unfulfilled span').text('0');
        $('#task_report li.processing span').text('0');
        $('#task_report li.slow_proccessing span').text('0');
        $('#task_report li.finish span').text('0');
        $('#task_report li.slow-finish span').text('0');
    })

    // autocomplete
    var frame_array = ['customer_list', 'xem_list', 'implement_list', 'trangthai_list'];
    $.each(frame_array, function( index, value ) {
        css_form(value);
        press(value);
    });

    // search process
    date_time_picker_field_report($('#adv_date_start_from'), JS_DATE_FORMAT+ " "+JS_TIME_FORMAT);
    date_time_picker_field_report($('#adv_date_start_to'), JS_DATE_FORMAT+ " "+JS_TIME_FORMAT);
    date_time_picker_field_report($('#adv_date_end_from'), JS_DATE_FORMAT+ " "+JS_TIME_FORMAT);
    date_time_picker_field_report($('#adv_date_end_to'), JS_DATE_FORMAT+ " "+JS_TIME_FORMAT);
    $('label[for="simple_radio"] span').click(function() {
        var label_element = $(this).closest('label');
        var element_radio = label_element.prev();
        element_radio.prop("checked", true);

    });

    $( ".date_time" ).focus(function() {
        var range_element = $(this).closest('.report_date_range_complex');
        var radio = range_element.find('input[type="radio"]');
        radio.prop("checked", true);
    });

    //search tasks
    var typingTimer;
    $('body').on('keyup','.search_keywords',function(){
        clearTimeout(typingTimer);
        var tr_element = $(this).closest('[data-parent]');
        var project_id = tr_element.attr('data-parent');
        typingTimer = setTimeout('startSearch('+project_id+')', 500);
    });

    $('body').on('keydown','.search_keywords',function(){
        clearTimeout(typingTimer);
    });

    // search project
    $('body').on('keyup','#s_keywords',function(){
        clearTimeout(typingTimer);
        typingTimer = setTimeout(project_search, 500);
    });
});

function project_search() {
    load_list('project-grid', 1);
}

function startSearch (project_id) {
    var tr_element       = $('#project_grid_table tr[data-parent="'+project_id+'"]');
    var s_keywords       = tr_element.find('.s_keywords');
    var search_keywords  = tr_element.find('.search_keywords');
    var s_customer       = tr_element.find('.s_customer');
    var s_customer_html  = tr_element.find('.s_customer_html');
    var s_implement      = tr_element.find('.s_implement');
    var s_implement_html = tr_element.find('.s_implement_html');
    var s_xem            = tr_element.find('.s_xem');
    var s_xem_html       = tr_element.find('.s_xem_html');
    var s_status         = tr_element.find('.s_status');
    var s_progress       = tr_element.find('.s_progress');

    s_customer.val('');
    s_customer_html.html('');
    s_implement.val('');
    s_implement_html.html('');
    s_xem.val('');
    s_xem_html.html('');
    s_status.val('-1,0,1,2');
    s_progress.val('-1,0,1,2');

    s_keywords.val(search_keywords.val());

    load_task_childs(project_id, 1);

}
</script>

<?php $this->load->view("partial/footer"); ?>