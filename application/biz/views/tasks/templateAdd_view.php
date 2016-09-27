<?php $this->load->view("partial/header"); ?>
<link href="https://hstatic.net/0/0/global/design/plugins/font-awesome/4.5.0/css/font-awesome.min.css" media="screen" rel="stylesheet" type="text/css">
<link rel="stylesheet" href="<?php echo base_url();?>assets/scripts/tasks/codebase/dhtmlxgantt.css" type="text/css" />
<link rel="stylesheet" href="<?php echo base_url();?>assets/tasks/css/style.css" type="text/css" media="screen" />
<link rel="stylesheet" href="<?php echo base_url();?>assets/tasks/css/responsive.css" type="text/css" media="screen" />
<script src="<?php echo base_url();?>assets/scripts/tasks/codebase/dhtmlxgantt.js" type="text/javascript" charset="utf-8"></script>
<script src="http://camohub.github.io/jquery-sortable-lists/jquery-sortable-lists.js"></script>
<link rel="stylesheet" href="<?php echo base_url();?>assets/tasks/css/style.css" type="text/css" media="screen" />
<link rel="stylesheet" type="text/css" href="<?php echo base_url() . 'assets/tasks/css/tree.css'; ?>" media="screen">

<script src="<?php echo base_url() . 'assets/tasks/js/script.js'; ?>"></script>

<div class="main-content">
	<div class="row" id="form">
		<div class="spinner" id="grid-loader" style="display:none">
		   <div class="rect1"></div>
		   <div class="rect2"></div>
		   <div class="rect3"></div>
		</div>
		<div class="col-md-12">
			<form id="template_form" class="form-horizontal" method="post" accept-charset="utf-8" novalidate="novalidate">
				<input type="hidden" id="count_task" value="0" />
				<div class="panel panel-piluku">
					<div class="panel-heading">
		                <h3 class="panel-title">
		                    <i class="ion-edit"></i> 
		                    Thêm mới Template Công việc <small>(Các trường màu đỏ là cần nhập)</small> 
		                    <div class="button_control">
		                    	<i class="ion-plus-round" onclick="add_congviec();"></i>
		                    </div>                
		                </h3>
		        	</div>
		        	<div class="panel-body">
						<div class="row">
							<div class="col-md-12">
								<div class="form-group">
									<label for="name" class="required col-sm-3 col-md-3 col-lg-2 control-label ">Tên :</label>			
									<div class="col-sm-9 col-md-9 col-lg-10">
										<input type="text" name="template_name" id="template_name" value="" class="form-control">
									</div>
								</div>
								<div class="form-group">
									<label for="first_name" class="col-sm-3 col-md-3 col-lg-2 control-label ">Công việc :</label>			
									<div class="col-sm-9 col-md-9 col-lg-10" id="sort_section">
										<ul class="sTree2 listsClass" id="sTree2">
										</ul>
									</div>
								</div>
								
								<div class="form-actions pull-right">	
									<input type="button" name="submit" value="Thêm mới" onclick="add_template();" class="btn btn-primary submit_button btn-large button_new">
								</div>
							</div>
						</div>
					</div>				
				</div>
			</form>
		</div>
	</div>
</div>
<div id="quick-form" class="gantt_cal_light" style=""></div>
<script type="text/javascript">
$( document ).ready(function() {
	var options = {
			placeholderCss: {'background-color': 'white'},
			hintCss: {'background-color':'white'},
			opener: {
	            active: true,
	            as: 'html',  
	            close: '<i class="fa fa-minus c3"></i>',  
	            open: '<i class="fa fa-plus"></i>', 
	            openerCss: {

	            }
			},
			insertZonePlus: true,
			ignoreClass: 'clickable'
		};
		$('#sTree2').sortableLists( options );
	
});
</script>
<?php $this->load->view("partial/footer"); ?>