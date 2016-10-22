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
<div class="modal fade bs-example-modal-lg search-advance-form" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true" class="x-close">×</span></button>
                <h4 class="modal-title" id="myLargeModalLabel">Tìm kiếm công việc cho "Dự án mẫu"</h4>
            </div>
            <div class="modal-body">
                <form class="form-horizontal form-horizontal-mobiles">
                    <div class="form-group">
                        <label for="simple_radio" class="col-sm-3 col-md-3 col-lg-2 control-label">Bắt đầu :</label>
                        <div class="col-sm-9 col-md-2 col-lg-2">
                            <input type="radio" name="report_type" id="simple_radio" value="simple" checked="checked">
                            <label for="simple_radio"><span></span></label>
                            <select name="report_date_range_simple" id="report_date_range_simple" class="form-control">
                                <option value="today">Hôm nay</option>
                                <option value="yesterday">Ngày hôm qua</option>
                                <option value="7_days_previous">7 ngày qua</option>
                                <option value="current_week">Tuần này</option>
                                <option value="previous_week">Tuần trước</option>
                                <option value="current_month">Tháng này</option>
                                <option value="previous_month">Tháng trước</option>
                                <option value="current_year">Năm nay</option>
                                <option value="previous_year">Năm trước</option>
                                <option value="all">Toàn bộ thời gian</option>
                            </select>
                        </div>
                    </div>
                    <div id="report_date_range_complex">
                        <div class="form-group">
                            <label for="complex_radio" class="col-sm-3 col-md-3 col-lg-2 control-label  ">Tùy chỉnh :</label>
                            <div class="col-sm-9 col-md-9 col-lg-10">
                                <input type="radio" name="report_type" id="complex_radio" value="complex">
                                <label for="complex_radio"><span></span></label>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="input-group input-daterange" id="reportrange">
		                                    <span class="input-group-addon bg">
					                           Từ					                       	</span>
                                            <input type="text" class="form-control start_date" name="start_date_formatted" id="start_date_formatted"><input type="hidden" id="start_date" name="start_date" value="2016-10-22 00:00">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="input-group input-daterange" id="reportrange1">
		                                    <span class="input-group-addon bg">
			                                    Đến			                                </span>
                                            <input type="text" class="form-control end_date" name="end_date_formatted" id="end_date_formatted"><input type="hidden" id="end_date" name="end_date" value="2016-10-22 23:59">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="simple_radio" class="col-sm-3 col-md-3 col-lg-2 control-label">Kết thúc :</label>
                        <div class="col-sm-9 col-md-2 col-lg-2">
                            <input type="radio" name="report_type" id="simple_radio" value="simple" checked="checked">
                            <label for="simple_radio"><span></span></label>
                            <select name="report_date_range_simple" id="report_date_range_simple" class="form-control">
                                <option value="today">Hôm nay</option>
                                <option value="yesterday">Ngày hôm qua</option>
                                <option value="7_days_previous">7 ngày qua</option>
                                <option value="current_week">Tuần này</option>
                                <option value="previous_week">Tuần trước</option>
                                <option value="current_month">Tháng này</option>
                                <option value="previous_month">Tháng trước</option>
                                <option value="current_year">Năm nay</option>
                                <option value="previous_year">Năm trước</option>
                                <option value="all">Toàn bộ thời gian</option>
                            </select>
                        </div>
                    </div>
                    <div id="report_date_range_complex">
                        <div class="form-group">
                            <label for="complex_radio" class="col-sm-3 col-md-3 col-lg-2 control-label  ">Tùy chỉnh :</label>
                            <div class="col-sm-9 col-md-9 col-lg-10">
                                <input type="radio" name="report_type" id="complex_radio" value="complex">
                                <label for="complex_radio"><span></span></label>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="input-group input-daterange" id="reportrange">
		                                    <span class="input-group-addon bg">
					                           Từ					                       	</span>
                                            <input type="text" class="form-control start_date" name="start_date_formatted" id="start_date_formatted"><input type="hidden" id="start_date" name="start_date" value="2016-10-22 00:00">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="input-group input-daterange" id="reportrange1">
		                                    <span class="input-group-addon bg">
			                                    Đến			                                </span>
                                            <input type="text" class="form-control end_date" name="end_date_formatted" id="end_date_formatted"><input type="hidden" id="end_date" name="end_date" value="2016-10-22 23:59">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="simple_radio" class="col-sm-3 col-md-3 col-lg-2 control-label">Tiêu đề:</label>
                        <div class="col-sm-9 col-md-9 col-lg-10">
                            <input type="text" class="form-control" />
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="simple_radio" class="col-sm-3 col-md-3 col-lg-2 control-label">Trạng thái :</label>
                        <div class="col-sm-9 col-md-9 col-lg-10">
                            <div class="x-select-users" x-name="customer" id="customer_list" x-title="Khách hàng" style="display: inline-block; width: 100%;" onclick="foucs(this);">
                                <span class="item"><input type="hidden" name="customer[]" class="customer" id="customer_6" value="6"><a>Hoàn thành </a>&nbsp;&nbsp;<span class="x" onclick="delete_item(this);"></span></span>
                                <div class="result" style="top: 27px; display: none;">

                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="simple_radio" class="col-sm-3 col-md-3 col-lg-2 control-label">Khách hàng :</label>
                        <div class="col-sm-9 col-md-9 col-lg-10">
                            <div class="x-select-users" x-name="customer" id="customer_list" x-title="Khách hàng" style="display: inline-block; width: 100%;" onclick="foucs(this);">
                                <span class="item"><input type="hidden" name="customer[]" class="customer" id="customer_6" value="6"><a>dad </a>&nbsp;&nbsp;<span class="x" onclick="delete_item(this);"></span></span><span class="item"><input type="hidden" name="customer[]" class="customer" id="customer_5" value="5"><a>Amrit Group </a>&nbsp;&nbsp;<span class="x" onclick="delete_item(this);"></span></span><span class="item"><input type="hidden" name="customer[]" class="customer" id="customer_1" value="1"><a>Toàn Khánh Nguyễn</a>&nbsp;&nbsp;<span class="x" onclick="delete_item(this);"></span></span><input type="text" autocomplete="off" id="customer_result" class="quick_search">
                                <div class="result" style="top: 27px; display: none;"><ul class="list"><li><a href="javascript:;" data-id="1" data-name="Toàn Khánh Nguyễn" onclick="add_item(this, 'customer_list');">Toàn Khánh Nguyễn</a></li></ul></div>
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
});
</script>

<?php $this->load->view("partial/footer"); ?>