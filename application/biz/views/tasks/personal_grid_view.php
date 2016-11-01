<?php $this->load->view("partial/header"); ?>
    <link href="http://fontawesome.io/assets/font-awesome/css/font-awesome.css" media="screen" rel="stylesheet" type="text/css">
    <link rel="stylesheet" href="<?php echo base_url();?>assets/tasks/css/style.css" type="text/css" media="screen" />
    <link rel="stylesheet" href="<?php echo base_url();?>assets/tasks/css/responsive.css" type="text/css" media="screen" />

    <script type="text/javascript" src="<?php echo base_url() ?>assets/tasks/js/task-core.js" ></script>
    <script type="text/javascript" src="<?php echo base_url() ?>assets/tasks/js/personal.js" ></script>

    <div class="manage_buttons">
        <div class="manage-row-options">
            <div class="email_buttons text-center">
                <a href="javascript:;" class="btn btn-red btn-lg" title="Xóa" onclick="delete_template();"><span class="">Xóa lựa chọn</span></a>
            </div>
        </div>
        <div class="cl">
            <div class="pull-left">
                <div action="" id="search_form" autocomplete="off" class="form-inline" method="post" accept-charset="utf-8">
                    <div class="search no-left-border">
                        <span role="status" aria-live="polite" class="ui-helper-hidden-accessible"></span><input type="text" class="form-control ui-autocomplete-input" id="search_keywords" value="" placeholder="Tìm kiếm đự án" autocomplete="off">
                        <select class="form-control" id="search_date_type">
                            <option value="0" selected="selected">-- Thời gian --</option>
                            <option value="today">Trong ngày</option>
                            <option value="weekend">Trong tuần</option>
                            <option value="month">Trong tháng</option>
                            <option value="year">Trong năm</option>
                        </select>
                        <button name="btn_advance_project" id="btn_advance_project" class="btn btn-primary btn-lg">Nâng cao</button>

                        <input type="hidden" id="s_keywords" />
                        <input type="hidden" id="s_date_start" value="all" />
                        <input type="hidden" id="s_date_start_radio" value="simple" />
                        <input type="hidden" id="s_date_start_from" value="" />
                        <input type="hidden" id="s_date_start_to" value="" />
                        <input type="hidden" id="s_date_end" value="all" />
                        <input type="hidden" id="s_date_end_radio" value="simple" />
                        <input type="hidden" id="s_date_end_from" value="" />
                        <input type="hidden" id="s_date_end_to" value="" />
                        <input type="hidden" id="s_trangthai" value="" />
                        <input type="hidden" id="s_customer" value="" />
                        <input type="hidden" id="s_xem" value="" />
                        <div id="s_trangthai_html" style="display: none;"></div>
                        <div id="s_customer_html" style="display: none;"></div>
                        <div id="s_xem_html" style="display: none;"></div>
                    </div>
                    <div class="clear-block hidden">
                        <i class="ion ion-close-circled"></i>
                    </div>
                </div>
            </div>
            <div class="pull-right">
                <div class="buttons-list">
                    <div class="pull-right-btn">
                        <a href="javascript:;" onclick="update_personal_task('new');" class="btn btn-primary btn-lg" title="Thêm mới"><span class="">Thêm mới</span></a>
                        <div class="piluku-dropdown">
                            <button type="button" class="btn btn-more dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                                <i class="ion-android-more-horizontal"></i>
                            </button>
                            <ul class="dropdown-menu" role="menu">
                                <li></li>
                                <li></li>
                                <li></li>
                                <li></li>
                                <li></li>
                                <li>
                                    <a href="<?php echo base_url() . 'tasks/template' ?>" class="hidden-xs" title="Danh sách template"><span class="">Danh sách Template</span></a>
                                </li>
                            </ul>
                        </div>
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
                            <span class="tieude active">Cá nhân</span>
                            <span class="tieude"><a href="<?php echo base_url() . 'personal_project'; ?>">Theo dự án</a></span>

                            <i class="fa fa-spinner fa-spin" id="loading_1"></i>
                        </h3>
                    </div>
                </div>
                <div class="panel-body">
                    <div class="table-responsive">
                        <table class="table tablesorter table-reports table-bordered table-tree" id="project_grid_table">
                            <thead>
                            <tr align="center" style="font-weight:bold">
                                <td class="hidden-print" style="width: 30px;">
                                    <input type="checkbox">
                                    <label for="select_all" class="check_tatca"><span></span></label>
                                </td>
                                <td align="center" data-field="name">Tên Công việc</td>
                                <td align="center" style="width: 8%;" data-field="prioty">Ưu tiên</td>
                                <td align="center" style="width: 100px;" data-field="date_start">Bắt đầu</td>
                                <td align="center" style="width: 100px;" data-field="date_end">Kết thúc</td>
                                <td align="center" style="width: 256px;" data-field="progress">Tiến độ</td>
                                <td align="center" style="width: 10%;" data-field="trangthai">Tình trạng</td>
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
    <div id="my_modal" class="modal fade bs-example-modal-lg search-advance-form" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">

    </div>
    <div class="modal fade box-modal" id="quick_modal">
    </div>
    <div id="advance_task_search" class="modal fade bs-example-modal-lg search-advance-form" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true" class="x-close">×</span></button>
                    <h4 class="modal-title" id="my_search_task">Tìm kiếm công việc cá nhân</h4>
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
                            <label for="simple_radio" class="col-sm-3 col-md-3 col-lg-2 control-label">Theo dõi :</label>
                            <div class="col-sm-9 col-md-9 col-lg-10">
                                <div class="x-select-users" x-name="xem" id="xem_list" x-title="Người được xem" style="display: inline-block; width: 100%;" onclick="foucs(this);">
                                    <input type="text" autocomplete="off" id="xem_result" class="quick_search">
                                    <div class="result" style="top: 27px;">
                                    </div>
                                </div>
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
                        <li class="implement">Phụ trách: <span onclick="do_change_advance_search('implement');">0</span></li>
                        <li class="xem">Theo dõi: <span onclick="do_change_advance_search('xem');">0</span></li>
                        <li class="cancel">Đóng dừng: <span onclick="do_change_advance_search('cancel');">0</span></li>
                        <li class="not-done">Không thực hiện: <span onclick="do_change_advance_search('not-done');">0</span></li>
                        <li class="unfulfilled" onclick="do_change_advance_search('unfulfilled');">Chưa thực hiện: <span>0</span></li>
                        <li class="processing" onclick="do_change_advance_search('processing');">Đang tiến hành: <span>0</span></li>
                        <li class="slow_proccessing" onclick="do_change_advance_search('slow_proccessing');">Chậm tiến độ: <span>0</span></li>
                        <li class="finish" onclick="do_change_advance_search('finish');">Đã hoàn thành: <span>0</span></li>
                        <li class="slow-finish" onclick="do_change_advance_search('slow-finish');">Đã hoàn thành nhưng chậm tiến độ: <span>0</span></li>
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
            height: 61px !important;
        }

        #project_grid_table input[type="checkbox"] + label span {
            margin: 0;
        }
    </style>
    <script type="text/javascript">
        $( document ).ready(function() {
            load_list('personal', 1);
        });
    </script>

<?php $this->load->view("partial/footer"); ?>