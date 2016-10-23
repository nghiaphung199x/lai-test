<?php $this->load->view("partial/header"); ?>
<link href="https://hstatic.net/0/0/global/design/plugins/font-awesome/4.5.0/css/font-awesome.min.css" media="screen" rel="stylesheet" type="text/css">
<link rel="stylesheet" href="<?php echo base_url();?>assets/tasks/css/style.css" type="text/css" media="screen" />
<link rel="stylesheet" href="<?php echo base_url();?>assets/tasks/css/responsive.css" type="text/css" media="screen" />

<script type="text/javascript" src="<?php echo base_url() ?>assets/tasks/js/task-core.js" ></script>
<script type="text/javascript" src="<?php echo base_url() ?>assets/tasks/js/script.js" ></script>

<div class="manage_buttons">
<div class="manage-row-options">
	<div class="email_buttons text-center">		
		<a href="javascript:;" class="btn btn-red btn-lg" title="Xóa" onclick="delete_template();"><span class="">Xóa lựa chọn</span></a>		
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
<button type="button" class="btn btn-primary" data-toggle="modal" data-target=".bs-example-modal-lg">Large modal</button>
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
                            <div class="x-select-users" x-name="customer" id="customer_list" x-title="Khách hàng" style="display: inline-block; width: 100%;" onclick="foucs(this);">
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
                        <div class="form-actions pull-right">
                            <input type="submit" name="submitf" value="Thực hiện" id="submitf" style="margin-right: 16px;" class=" submit_button btn btn-primary">
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
#project_grid_table .s_keywords {
    width: 400px;
    margin-bottom: 5px;
    float: left;
    margin-right: 10px;
}

#project_grid_table .s_list {
    width: 400px;
    float: right;
    margin-bottom: 5px;
}

#project_grid_table .search_date_type {
    width: 300px;
    float: right;
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

    //advance search click
    $('body').on('click','.submitf',function(){
        var task_name       = $(this).attr('data-name');
        var project_id      = $(this).attr('data-id');
        current_project_id  = project_id;
        var element_parent  = $(this).closest('tr[data-parent]');

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

        $('[name="adv_date_start_radio"]').filter('[value='+date_start_value+']').prop('checked', true);
        $('#adv_date_start').val(s_date_start.val());

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

        $('[name="adv_date_end_radio"]').filter('[value='+date_end_value+']').prop('checked', true);
        $('#adv_date_end').val(s_date_end.val());

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

        $('#my_search_task span').text(task_name);

        $("#advance_task_search").modal();
    });

    $('body').on('change','.search_date_type',function(){
        var value                = $(this).val();
        var element_parent       = $(this).closest('tr[data-parent]');
        var s_date_start_to      = element_parent.find('.s_date_start_to');
        var s_date_start_from    = element_parent.find('.s_date_start_from');
        var s_date_end_to        = element_parent.find('.s_date_end_to');
        var s_date_end_from      = element_parent.find('.s_date_end_from');
        var s_trangthai          = element_parent.find('.s_trangthai');
        var s_trangthai_html     = element_parent.find('.s_trangthai_html');
        var s_date_start_radio   = element_parent.find('.s_date_start_radio');
        var s_date_end_radio     = element_parent.find('.s_date_end_radio');

        var data = {class: 'trangthai', value: 0, title: 'Chưa thực hiện'};
        var span_trangthai_0 = get_item_autocomplete(data);

        var data = {class: 'trangthai', value: 1, title: 'Đang thực hiện'};
        var span_trangthai_1 = get_item_autocomplete(data);

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
    });

    // event when close modal
    $('#advance_task_search').on('hidden.bs.modal', function () {

    })

    // search process
    date_time_picker_field_report($('#adv_date_start_from'), JS_DATE_FORMAT+ " "+JS_TIME_FORMAT);
    date_time_picker_field_report($('#adv_date_start_to'), JS_DATE_FORMAT+ " "+JS_TIME_FORMAT);
    date_time_picker_field_report($('#adv_date_end_from'), JS_DATE_FORMAT+ " "+JS_TIME_FORMAT);
    date_time_picker_field_report($('#adv_date_end_to'), JS_DATE_FORMAT+ " "+JS_TIME_FORMAT);
    $('label[for="simple_radio"] span').click(function() {
        var label_element = $(this).closest('label');
        var element_radio = label_element.prev();
        element_radio.prop("checked", true);

        alert(element_radio.attr('name'));
    });

    $( ".date_time" ).focus(function() {
        var range_element = $(this).closest('.report_date_range_complex');
        var radio = range_element.find('input[type="radio"]');
        radio.prop("checked", true);
    });
});
</script>

<?php $this->load->view("partial/footer"); ?>