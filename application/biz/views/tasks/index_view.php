<?php $this->load->view("partial/header"); ?>
	<link href="http://fontawesome.io/assets/font-awesome/css/font-awesome.css" media="screen" rel="stylesheet" type="text/css">
	<link rel="stylesheet" href="<?php echo base_url();?>assets/scripts/tasks/codebase/dhtmlxgantt.css" type="text/css" />

	<link rel="stylesheet" href="<?php echo base_url();?>assets/tasks/css/style.css" type="text/css" media="screen" />
	<link rel="stylesheet" href="<?php echo base_url();?>assets/tasks/css/responsive.css" type="text/css" media="screen" />

	<script src="<?php echo base_url();?>assets/scripts/tasks/codebase/dhtmlxgantt.js" type="text/javascript" charset="utf-8"></script>

	<script type="text/javascript" src="<?php echo base_url() ?>assets/tasks/js/task-core.js" ></script>
	<script type="text/javascript" src="<?php echo base_url() ?>assets/tasks/js/task.js" ></script>
    <script src="<?php echo base_url();?>assets/scripts/tasks/codebase/ext/dhtmlxgantt_tooltip.js" type="text/javascript" charset="utf-8"></script>
	<div class="clearfix" id="task_control">
		<div class="pull-left">
			<div action="" id="search_form" autocomplete="off" class="form-inline" method="post" accept-charset="utf-8">
				<div class="search no-left-border" style="padding-left: 5px;">
					<span role="status" aria-live="polite" class="ui-helper-hidden-accessible"></span><input type="text" class="form-control ui-autocomplete-input" name="search" id="search_keywords" value="" placeholder="Tìm kiếm dự án" autocomplete="off" style="border: 0;">
                    <select class="form-control" id="search_date_type"><option value="0" selected="selected">-- Thời gian --</option><option value="today">Trong ngày</option><option value="weekend">Trong tuần</option><option value="month">Trong tháng</option><option value="year">Trong năm</option> </select>
                    <button name="btn_advance_project" id="btn_advance_project" class="btn btn-primary btn-lg">Nâng cao</button>

                    <input type="hidden" id="s_keywords" value="">
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
                    <input type="hidden" id="s_implement" value="" />
                    <input type="hidden" id="s_xem" value="" />
                    <div id="s_trangthai_html" style="display: none;"></div>
                    <div id="s_customer_html" style="display: none;"></div>
                    <div id="s_implement_html" style="display: none;"></div>
                    <div id="s_xem_html" style="display: none;"></div>

                </div>
				<div class="clear-block hidden">
					<a class="clear" href="javascript:;">
						<i class="ion ion-close-circled"></i>
					</a>	
				</div>
			</div>
		</div>
		<div class="pull-right">
				<div class="buttons-list">
					<div class="pull-right-btn">
                        <button id="btn_tooltip" class="btn btn-primary btn-lg active">Tắt Tooltip</button>
                        <a href="<?php echo base_url() . 'tasks/project' ?>" class="btn btn-primary btn-lg" title="Quản lý vai trò"><span class="">Dự án</span></a>
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
	</div>
	<div class="gantt_title">
		<h3 class="panel-title">
			<span class="tieude active">Lược đồ</span>
			<span class="tieude"><a href="<?php echo base_url() . 'tasks/grid'; ?>">Danh sách</a></span>
			<i class="fa fa-spinner fa-spin" id="loading_1"></i>
			<span class="panel-options custom" id="gantt_pagination">
			</span>
		</h3>
	</div>
	<div id="gantt_here" style='width:100%; min-height: 500px;'></div>
	<div>
		<input type="hidden" name="start_date_original" id="start_date_original" />
		<input type="hidden" name="start_date_drag" id="start_date_drag" />
		<input type="hidden" name="end_date_drag" id="end_date_drag" />
	</div>

    <div id="my_modal" class="modal fade bs-example-modal-lg search-advance-form" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">

    </div>

    <div class="modal fade box-modal" id="quick_modal">
    </div>
    <div id="advance_project_search" class="modal fade bs-example-modal-lg search-advance-form" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true" class="x-close">×</span></button>
                    <h4 class="modal-title" id="my_search_task">Tìm kiếm dự án</h4>
                </div>
                <div class="modal-body">
                    <form class="form-horizontal form-horizontal-mobiles">
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

                        <div class="form-group" style="margin-bottom: 5px;">
                            <div class="form-actions pull-right">
                                <input type="button" name="submitf" value="Thực hiện" id="btn_p_search_advance" style="margin-right: 16px;" class=" submit_button btn btn-primary">
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

	<script type="text/javascript">
	$( document ).ready(function() {
		load_task(1);

		gantt.templates.quick_info_date = function(start, end, task){
		       return gantt.templates.task_time(start, end, task);
		};

        $('body').on('click','#my_modal .manage-table table th',function(){
            var thElement = $('#my_modal .manage-table table th');
            var attr = $(this).attr('data-field');
            if (typeof attr !== typeof undefined && attr !== false) {
                if($(this).hasClass('header')) {
                    if($(this).hasClass('headerSortUp')){
                        $(this).removeClass('headerSortUp');
                        $(this).addClass('headerSortDown');
                    }else {
                        $(this).removeClass('headerSortDown');
                        $(this).addClass('headerSortUp');
                    }
                }else {
                    thElement.removeClass('header');
                    thElement.removeClass('headerSortUp');
                    thElement.removeClass('headerSortDown');
                    $(this).addClass('header headerSortUp');
                }

                var li_element = $('.arrord_nav ul li.active');
                var className  = li_element.attr('data-id');
                if(className == 'progress_manager') {
                    var content_id = $('#progress_manager span.tieude.active').attr('data-id');
                    if(content_id == 'progress_danhsach') {
                        load_list('progress', 1);
                    }else if(content_id == 'request_list')
                        load_list('request', 1);
                    else if(content_id == 'pheduyet_list'){
                        load_list('pheduyet', 1);
                    }
                }else
                    load_list('file', 1);
            }
        });

    });

	</script>
<?php $this->load->view("partial/footer"); ?>