<?php $this->load->view("partial/header"); ?>
	<link href="https://hstatic.net/0/0/global/design/plugins/font-awesome/4.5.0/css/font-awesome.min.css" media="screen" rel="stylesheet" type="text/css">
	
	<link rel="stylesheet" href="<?php echo base_url();?>assets/scripts/tasks/codebase/dhtmlxgantt.css" type="text/css" />
	
	<link rel="stylesheet" href="<?php echo base_url();?>assets/tasks/css/style.css" type="text/css" media="screen" />
	<link rel="stylesheet" href="<?php echo base_url();?>assets/tasks/css/responsive.css" type="text/css" media="screen" />
	
	<script src="<?php echo base_url();?>assets/scripts/tasks/codebase/dhtmlxgantt.js" type="text/javascript" charset="utf-8"></script>
	<script type="text/javascript" src="<?php echo base_url() ?>assets/tasks/js/task.js" ></script>
<style>
.gantt_title {
	border-bottom: 1px solid #e9e9e9;
    background: white;
    width: 100%;
    padding: 10px 15px;
}

.gantt_title .panel-title {
	font-size: 16px;
    font-weight: bold;
    margin-bottom: 5px;
    padding-top: 5px;
    color: #555555;
}
</style>
	<div class="gantt_title">
		<h3 class="panel-title">
			Thông tin Dự án/ Công việc
		</h3>
	</div>
	<div id="gantt_here" style='width:100%; min-height: 300px;'></div>
	<div id="my-form" class="gantt_cal_light" style=""></div>
	<div id="quick-form" class="gantt_cal_light" style=""></div>
	<div>
		<input type="hidden" name="start_date_original" id="start_date_original" />
		<input type="hidden" name="start_date_drag" id="start_date_drag" />
		<input type="hidden" name="end_date_drag" id="end_date_drag" />
	</div>
<?php $this->load->view("partial/footer"); ?>