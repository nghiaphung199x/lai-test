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
                    <select class="form-control search_date_type" id="search_date_type"><option value="0" selected="selected">-- Thời gian --</option><option value="today">Trong ngày</option><option value="weekend">Trong tuần</option><option value="month">Trong tháng</option><option value="year">Trong năm</option> </select>

                    <input type="hidden" id="s_keywords" value="">
                    <input type="hidden" name="s_date_start_from" id="s_date_start_from" value="" />
                    <input type="hidden" name="s_date_start_to" id="s_date_start_to" value="" />
                    <input type="hidden" name="s_date_end_from" id="s_date_end_from" value="" />
                    <input type="hidden" name="s_date_end_to" id="s_date_end_to" value="" />
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
	<script type="text/javascript">
	$( document ).ready(function() {
		load_task(1);

		gantt.templates.quick_info_date = function(start, end, task){
		       return gantt.templates.task_time(start, end, task);
		};

	   // search
	   var typingTimer;       
	   $('body').on('keyup','#search_keywords',function(){
		   clearTimeout(typingTimer);
           var text = $(this).val();
           $('#s_keywords').val(text);
		   typingTimer = setTimeout(startSearch, 500);
	   });
	   
	   $('body').on('keydown','#search_keywords',function(){
		   clearTimeout(typingTimer);
	   });
	   
	   function startSearch () {
		   gantt.clearAll();
		   load_task(1);
	   }

        $('body').on('change','#search_date_type',function(){
            var value   =  $(this).val();
            var s_date_start_to = $('#s_date_start_to');
            var s_date_end_from = $('#s_date_end_from');
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
	});

	</script>
<?php $this->load->view("partial/footer"); ?>