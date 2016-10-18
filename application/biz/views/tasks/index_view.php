<?php $this->load->view("partial/header"); ?>
	<link href="https://hstatic.net/0/0/global/design/plugins/font-awesome/4.5.0/css/font-awesome.min.css" media="screen" rel="stylesheet" type="text/css">

	<link rel="stylesheet" href="<?php echo base_url();?>assets/scripts/tasks/codebase/dhtmlxgantt.css" type="text/css" />
	
	<link rel="stylesheet" href="<?php echo base_url();?>assets/tasks/css/style.css" type="text/css" media="screen" />
	<link rel="stylesheet" href="<?php echo base_url();?>assets/tasks/css/responsive.css" type="text/css" media="screen" />

	<script src="<?php echo base_url();?>assets/scripts/tasks/codebase/dhtmlxgantt.js" type="text/javascript" charset="utf-8"></script>


	<script type="text/javascript" src="<?php echo base_url() ?>assets/tasks/js/task-core.js" ></script>
	<script type="text/javascript" src="<?php echo base_url() ?>assets/tasks/js/task.js" ></script>
<?php if($tooltip == true): ?>
    <script src="<?php echo base_url();?>assets/scripts/tasks/codebase/ext/dhtmlxgantt_tooltip.js" type="text/javascript" charset="utf-8"></script>
	<script type="text/javascript">
	$( document ).ready(function() {
		gantt.templates.tooltip_text = function(start,end,task){
			return task.tooltip;
		};
	});
	</script>
<?php endif;?>
	<div class="clearfix" id="task_control">
		<div class="pull-left">
			<form action="" id="search_form" autocomplete="off" class="form-inline" method="post" accept-charset="utf-8">
				<div class="search no-left-border" style="padding-left: 5px;">
					<span role="status" aria-live="polite" class="ui-helper-hidden-accessible"></span><input type="text" class="form-control ui-autocomplete-input" name="search" id="s_keywords" value="" placeholder="Tìm kiếm dự án" autocomplete="off" style="border: 0;">
				</div>
				<div class="clear-block hidden">
					<a class="clear" href="javascript:;">
						<i class="ion ion-close-circled"></i>
					</a>	
				</div>
			</form>
		</div>
		<div class="pull-right">
				<div class="buttons-list">
					<div class="pull-right-btn">
						 <a href="<?php echo base_url() . 'tasks/project' ?>" class="btn btn-primary btn-lg" title="Quản lý vai trò"><span class="">Quản lý Dự án</span></a>	                    					
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
	<div id="my-form" class="gantt_cal_light" style=""></div>
	<div id="quick-form" class="gantt_cal_light" style=""></div>
	<div>
		<input type="hidden" name="start_date_original" id="start_date_original" />
		<input type="hidden" name="start_date_drag" id="start_date_drag" />
		<input type="hidden" name="end_date_drag" id="end_date_drag" />
	</div>
	<script type="text/javascript">
	$( document ).ready(function() {
		load_task(1);

		gantt.templates.quick_info_date = function(start, end, task){
		       return gantt.templates.task_time(start, end, task);
		};

	   // search
	   var typingTimer;       
	   $('body').on('keyup','#s_keywords',function(){
		   clearTimeout(typingTimer);
		   typingTimer = setTimeout(startSearch, 500);
	   });
	   
	   $('body').on('keydown','#s_keywords',function(){
		   clearTimeout(typingTimer);
	   });
	   
	   function startSearch () {
		   gantt.clearAll();
		   load_task(1);
	   }
	});

	</script>
<?php $this->load->view("partial/footer"); ?>