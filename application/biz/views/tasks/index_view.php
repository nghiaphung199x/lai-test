<?php $this->load->view("partial/header"); ?>
	<link href="https://hstatic.net/0/0/global/design/plugins/font-awesome/4.5.0/css/font-awesome.min.css" media="screen" rel="stylesheet" type="text/css">
	
	<link rel="stylesheet" href="<?php echo base_url();?>assets/scripts/tasks/codebase/dhtmlxgantt.css" type="text/css" />
	
	<link rel="stylesheet" href="<?php echo base_url();?>assets/tasks/css/style.css" type="text/css" media="screen" />
	<link rel="stylesheet" href="<?php echo base_url();?>assets/tasks/css/responsive.css" type="text/css" media="screen" />
	
	<script src="<?php echo base_url();?>assets/scripts/tasks/codebase/dhtmlxgantt.js" type="text/javascript" charset="utf-8"></script>
	<script type="text/javascript" src="<?php echo base_url() ?>assets/tasks/js/task.js" ></script>

	<div id="gantt_here" style='width:100%; height: 700px; margin-top: 50px;'></div>
	<div id="my-form" class="gantt_cal_light" style=""></div>
	<div id="quick-form" class="gantt_cal_light" style=""></div>
	<div>
		<input type="hidden" name="start_date_original" id="start_date_original" />
		<input type="hidden" name="start_date_drag" id="start_date_drag" />
		<input type="hidden" name="end_date_drag" id="end_date_drag" />
	</div>
<?php $this->load->view("partial/footer"); ?>