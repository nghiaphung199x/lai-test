<?php $this->load->view("partial/header"); 
$this->load->helper('demo');
?>

<style type="text/css">
	tr.delivery_warning_lv1 {
		background-color: #<?php echo $this->config->item('color_warning_level1'); ?> !important;
	}
	
	tr.delivery_warning_lv2 {
		background-color: #<?php echo $this->config->item('color_warning_level2'); ?> !important;
	}
	
	tr.delivery_warning_lv3 {
		background-color: #<?php echo $this->config->item('color_warning_level3'); ?> !important;
	}
</style>

<?php if ($can_show_mercury_activate) { ?>
	<!-- mercury activation message -->
	<div class="row " id="mercury_container">
		<div class="col-md-12">
			<div class="panel">
				<div class="panel-body">
					<a id="dismiss_mercury" href="<?php echo site_url('home/dismiss_mercury_message') ?>" class="pull-right text-danger"><?php echo lang('common_dismiss'); ?></a>
					<div id="mercury_activate_container">
						<a href="https://4biz.vn/mercury_activate.php" target="_blank"><?php echo img(
							array(
								'src' => base_url().'assets/img/mercury_logo.png',
								'class'=>'hidden-print',
								'id'=>'mercury-logo',
								)); ?>
						</a>
						<h3><a href="https://4biz.vn/mercury_activate.php" target="_blank"><?php echo lang('common_credit_card_processing'); ?></a></h3>
						<a href="https://4biz.vn/mercury_activate.php" class="mercury_description" target="_blank">
							<?php echo lang('home_mercury_activate_promo_text');?>
						</a>
					</div>
				</div>
			</div>
		</div>
	</div>
<?php  } ?>

<?php 
$this->load->helper('demo');
if (!is_on_demo_host() && !$this->config->item('hide_test_mode_home')) { ?>
	<?php if($this->config->item('test_mode')) { ?>
		<div class="alert alert-danger">
			<strong><?php echo lang('common_in_test_mode'); ?>. <a href="sales/disable_test_mode"></strong>
			<a href="<?php echo site_url('home/disable_test_mode'); ?>" id="disable_test_mode"><?php echo lang('common_disable_test_mode');?></a>
		</div>
	<?php } ?>

	<?php if(!$this->config->item('test_mode')) { ?>
		<div class="row " id="test_mode_container">
			<div class="col-md-12">
				<div class="panel">
					<div class="panel-body text-center">
						<a id="dismiss_test_mode" href="<?php echo site_url('home/dismiss_test_mode') ?>" class="pull-right text-danger"><?php echo lang('common_dismiss'); ?></a>
							<strong><?php echo anchor(site_url('home/enable_test_mode'), '<i class="ion-ios-settings-strong"></i> '.lang('common_enable_test_mode'),array('id'=>'enable_test_mode')); ?></strong>
							<p><?php echo lang('common_test_mode_desc')?></p>
						</div>
					</div>
				</div>
			</div>

	<?php } ?>
<?php } ?>

<div class="text-center">					

	<?php if (!$this->config->item('hide_dashboard_statistics') /* && (!$this->agent->is_mobile() || $this->agent->is_tablet()) */) { ?>

	<div class="row">
		
		<div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">
			<a href="<?php echo site_url('sales'); ?>">
				<div class="dashboard-stats">
					<div class="left">
						<h3 class="flatBluec"><?php echo $total_sales; ?></h3>
						<h4><?php echo lang('common_total')." ".lang('module_sales'); ?></h4>
					</div>
					<div class="right flatBlue">
						<i class="ion ion-ios-cart-outline"></i>
					</div>
				</div>
			</a>
		</div>
		
		<div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">
			<a href="<?php echo site_url('items'); ?>">
				<div class="dashboard-stats">
					<div class="left">
						<h3 class="flatBluec"><?php echo $total_items; ?></h3>
						<h4><?php echo lang('common_total')." ".lang('module_items'); ?></h4>
					</div>
					<div class="right flatRed">
						<i class="icon ti-harddrive"></i>
					</div>
				</div>
			</a>
		</div>
		<div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">
			<a href="<?php echo site_url('customers'); ?>">
				<div class="dashboard-stats" id="totalCustomers">
					<div class="left">
						<h3 class="flatGreenc"><?php echo $total_customers; ?></h3>
						<h4><?php echo lang('common_total')." ".lang('module_customers'); ?></h4>
					</div>
					<div class="right flatGreen">
						<i class="ion ion-ios-people-outline"></i>
					</div>
				</div>
			</a>
		</div>
		<div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">
			<a href="<?php echo site_url('item_kits'); ?>">
				<div class="dashboard-stats">
					<div class="left">
						<h3 class="flatOrangec"><?php echo $total_item_kits; ?></h3>
						<h4><?php echo lang('common_total')." ".lang('module_item_kits'); ?></h4>
					</div>
					<div class="right flatOrange">
						<i class="ion ion-filing"></i>
					</div>
				</div>
			</a>
		</div>
	</div>
</div>

<?php } ?>
<h5 class="text-center"><?php echo lang('home_welcome_message');?></h5>
<div class="row quick-actions">

  	<?php if ($this->Employee->has_module_action_permission('reports', 'view_closeout', $this->Employee->get_logged_in_employee_info()->person_id)) { ?>
		<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
			<div class="list-group">
					<a class="list-group-item" href="<?php echo site_url('reports/closeout/'.date('Y-m-d').'/0');?>"> <i class="ion-clock"></i> <?php echo lang('home_todays_closeout_report'); ?></a>
			</div>
		</div>
	<?php } ?>
	
	<?php if ($this->Employee->has_module_action_permission('reports', 'view_sales', $this->Employee->get_logged_in_employee_info()->person_id)) { ?>
		<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
			<div class="list-group">
					<a class="list-group-item" href="<?php echo site_url('reports/detailed_sales/'.date('Y-m-d').'%2000:00:00/'.date('Y-m-d').'%2023:59:59/all/0');?>"> <i class="ion-stats-bars"></i> <?php echo lang('home_todays_detailed_sales_report'); ?></a>
			</div>
		</div>
	<?php } ?>
	
	<?php if ($this->Employee->has_module_action_permission('reports', 'view_items', $this->Employee->get_logged_in_employee_info()->person_id)) { ?>
		<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
			<div class="list-group">
					<a class="list-group-item" href="<?php echo site_url('reports/summary_items/'.date('Y-m-d').'%2000:00:00/'.date('Y-m-d').'%2023:59:59/0/0/0/-1/-1/all/0');?>"> <i class="ion-clipboard"></i> <?php echo lang('home_todays_summary_items_report'); ?></a>
			</div>
		</div>
	<?php } ?>
		
	
	<?php if ($this->Employee->has_module_permission('receivings', $this->Employee->get_logged_in_employee_info()->person_id)) { ?>
		<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
			<div class="list-group">
					<a class="list-group-item" href="<?php echo site_url('receivings'); ?>"> <i class="icon ti-cloud-down"></i> <?php echo lang('home_receivings_start_new_receiving'); ?></a>
			</div>
		</div>
	<?php } ?>
	
	<?php if ($this->Employee->has_module_permission('sales', $this->Employee->get_logged_in_employee_info()->person_id)) {	?>
		<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
			<div class="list-group">
					<a class="list-group-item" href="<?php echo site_url('sales'); ?>"> <i class="icon ti-shopping-cart"></i> <?php echo lang('home_start_new_sale'); ?></a>
			</div>
		</div>
	<?php } ?>
	
</div>

<?php if (!$this->config->item('hide_dashboard_statistics')) { ?>
<div class="row ">
		<div class="col-md-12">
			<div class="panel">
				<div class="panel-body">
					
					<?php if (can_display_graphical_report()) { ?>
					<div class="panel-heading">
						<h4 class="text-center"><?php echo lang('common_sales_info') ?></h4>	
					</div>
					<!-- Nav tabs -->
                    <ul class="nav nav-tabs piluku-tabs" role="tablist">
                        <li role="presentation" class="active"><a href="#month" data-type="monthly" aria-controls="month" role="tab"><?php echo lang('common_month') ?></a></li>
                        <li role="presentation"><a href="#week" data-type="weekly" aria-controls="week" role="tab"><?php echo lang('common_week') ?></a></li>
                    </ul>

                    <!-- Tab panes -->
                    <div class="tab-content piluku-tab-content">
                        <div role="tabpanel" class="tab-pane active" id="month">
                        	<div class="chart">
                        		<?php if(isset($month_sale) && !isset($month_sale['message'])){ ?>
									<canvas id="charts" width="400" height="100"></canvas>		
								<?php } else{ 
									echo $month_sale['message'];
									 } ?>
							</div>
                        </div>
                        <div role="tabpanel" class="tab-pane" id="week">
                        	
                       	</div>
                    </div>
						<?php } ?>
				</div>
			</div>
		</div>
	</div>
	<?php } ?>
</div>

<?php if($choose_location && count($authenticated_locations) > 1){ ?>
	

<!-- Modal -->
<div class="modal fade" id="choose_location_modal" tabindex="-1" role="dialog" aria-labelledby="chooseLocation" data-keyboard="false">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title" id="chooseLocation"><?php echo lang('common_locations_choose_location'); ?></h4>
      </div>
      <div class="modal-body">
        <ul class="list-inline choose-location-home">
        	<?php foreach ($authenticated_locations as $key => $value) { ?>
				<li><a class="set_employee_current_location_after_login" data-location-id="<?php echo $key; ?>" href="<?php echo site_url('home/set_employee_current_location_id/'.$key) ?>"> <?php echo $value; ?> </a></li>
			<?php } ?>
        </ul>
      </div>
    </div>
  </div>
</div>
<?php } ?>

<?php if($show_warning_orders_modal){ ?>
<!-- Modal -->
<div class="modal fade" id="warning_orders_modal" tabindex="-1" role="dialog" aria-labelledby="chooseLocation" data-keyboard="false">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
      	<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span class="ti-close" aria-hidden="true"></span>
				</button>
        <h4 class="modal-title" id="chooseLocation"><?php echo lang('common_warning_orders_modal'); ?></h4>
      </div>
      <div class="modal-body">
      	<div>
	        <table class="transfer_pending table table-bordered table-striped table-hover data-table" id="dTableA">
				<thead>
					<tr>
						<th><?php echo lang('ID'); ?></th>
						<th class="hidden-xs"><?php echo lang('common_date'); ?></th>
						<th class="hidden-xs"><?php echo lang('sales_customer'); ?></th>
						<th><?php echo lang('common_delivery_date'); ?></th>
						<th><?php echo lang('common_deliverer'); ?></th>
					</tr>
				</thead>
				<tbody>
					<?php foreach ($warning_orders as $warning_order) {?>
					<tr class="<?php echo getStatusOfDelivery($warning_order['delivery_date']) ?>">
						<td><a href="<?php echo site_url('sales/unsuspend/'.$warning_order['sale_id']); ?>"><?php echo ($this->config->item('sale_prefix') ? $this->config->item('sale_prefix') : 'POS' ). ' '.$warning_order['sale_id'];?></a></td>
						<td class="hidden-xs"><?php echo date(get_date_format(). ' @ '.get_time_format(),strtotime($warning_order['sale_time']));?></td>
						<td class="hidden-xs">
							<?php
							if (isset($warning_order['customer_id'])) 
							{
								$customer = $this->Customer->get_info($warning_order['customer_id']);
								$company_name = $customer->company_name;
								if($company_name) {
								echo $customer->first_name. ' '. $customer->last_name.' ('.$customer->company_name.')';
								}
								else {
									echo $customer->first_name. ' '. $customer->last_name;
								}
							}
							else
							{
							?>
								&nbsp;
							<?php
							}
							?>
						</td>
						<td><?php echo $warning_order['delivery_date']; ?></td>
						<td>
							<?php 
							$deliverer = $this->Employee->get_info($warning_order['deliverer']);
							echo $deliverer ? $deliverer->first_name . ' ' . $deliverer->last_name : '';?>
						</td>
					</tr>
					<?php } ?>
				</tbody>
			</table>
		</div>
      </div>
    </div>
  </div>
</div>
<?php } ?>


<?php if($config_show_warning_expire_time){ ?>
<!-- Modal -->
<div class="modal fade" id="warning_expire_modal" tabindex="-1" role="dialog" aria-labelledby="chooseLocation" data-keyboard="false">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
      	<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span class="ti-close" aria-hidden="true"></span>
				</button>
        <h4 class="modal-title" id="chooseLocation"><?php echo lang('common_warning_expire_item_modal'); ?></h4>
      </div>
      <div class="modal-body">
      	<div>
	        <table class="transfer_pending table table-bordered table-striped table-hover data-table" id="dTableB">
				<thead>
					<tr>
						<th><?php echo lang('common_item'); ?></th>
						<th class="hidden-xs"><?php echo lang('common_category'); ?></th>
						<th class="hidden-xs"><?php echo lang('common_company'); ?></th>
						<th><?php echo lang('common_quantity'); ?></th>
						<th><?php echo lang('common_expire_date'); ?></th>
						
					</tr>
				</thead>
				<tbody>
					<?php foreach ($expire_data as $warningItem) {?>
					<tr>
						<td><a href="<?php echo site_url('items/view/'. $warningItem['item_id'] .'/2'); ?>"><?php echo $warningItem['name'];?></td>
						<td class="hidden-xs"><?php echo $warningItem['category'];?></td>
						<td class="hidden-xs"><?php echo $warningItem['company_name'];?></td>
						<td><?php echo to_quantity($warningItem['quantity']); ?></td>
						<td><?php echo $warningItem['expire_date'];?></td>
					</tr>
					<?php } ?>
				</tbody>
			</table>
		</div>
      </div>
    </div>
  </div>
</div>
<?php } ?>


<!-- Location Message to employee -->
<script>
	$(document).ready(function(){

		$('#dTableB').DataTable({
			"sPaginationType": "bootstrap",
			"bFilter": false,
			"bInfo": false,
			"iDisplayStart ": 10,
		    "iDisplayLength": 10,
		    "bLengthChange": false
		});

		$('#dTableA').DataTable({
			"sPaginationType": "bootstrap",
			"bFilter": false,
			"bInfo": false,
			"iDisplayStart ": 10,
		    "iDisplayLength": 10,
		    "bLengthChange": false
		});
		
		<?php if ($show_warning_orders_modal){ ?>
			$('#warning_orders_modal').modal('show');
		<?php } ?>

		<?php if ($config_show_warning_expire_time){ ?>
			$('#warning_expire_modal').modal('show');
		<?php } ?>
		
		$("#dismiss_mercury").click(function(e){
			e.preventDefault();
			$.get($(this).attr('href'));
			$("#mercury_container").fadeOut();
			
		});

		$("#dismiss_test_mode").click(function(e){
			e.preventDefault();
			$.get($(this).attr('href'));
			$("#test_mode_container").fadeOut();
		});
	
		<?php if($choose_location && count($authenticated_locations) > 1) { ?>
			
			$('#choose_location_modal').modal('show');

			$(".set_employee_current_location_after_login").on('click',function(e)
			{
				e.preventDefault();

				var location_id = $(this).data('location-id');
				$.ajax({
				    type: 'POST',
				    url: '<?php echo site_url('home/set_employee_current_location_id'); ?>',
				    data: { 
				        'employee_current_location_id': location_id, 
				    },
				    success: function(){

				    	window.location = <?php echo json_encode(site_url('home')); ?>;
				    }
				});
				
			});
			
		<?php } ?>


		<?php if(isset($month_sale) && !isset($month_sale['message'])){ ?>
			var data = {
				labels: <?php echo $month_sale['day'] ?>,
				datasets: [
				{
					fillColor : "#5d9bfb",
					strokeColor : "#5d9bfb",
					highlightFill : "#5d9bfb",
					highlightStroke : "#5d9bfb",
					data: <?php echo $month_sale['count'] ?>
				}
				]
			};
			var ctx = document.getElementById("charts").getContext("2d");
			var myBarChart = new Chart(ctx).Bar(data, {
				responsive : true
			});
		<?php } ?>

	        

		$('.piluku-tabs a').on('click',function(e) {
			e.preventDefault();
			$('.piluku-tabs li').removeClass('active');
			$(this).parent('li').addClass('active');
			var type = $(this).attr('data-type');
			$.post('<?php echo site_url("home/sales_widget/'+type+'"); ?>', function(res)
			{
				var obj = jQuery.parseJSON(res);
				if(obj.message)
				{
					$(".chart").html(obj.message);
					return false;
				}
				
				renderChart(obj.day, obj.count);
				
				myBarChart.update();
			});
		});

		function renderChart(label,data){

		    $(".chart").html("").html('<canvas id="charts" width="400" height="400"></canvas>');
		    var lineChartData = {
		        labels : label,
		        datasets : [
		            {
		                fillColor : "#5d9bfb",
						strokeColor : "#5d9bfb",
						highlightFill : "#5d9bfb",
						highlightStroke : "#5d9bfb",
		                data : data
		            }
		        ]

		    }
		    var canvas = document.getElementById("charts");
		    var ctx = canvas.getContext("2d");

		    myLine = new Chart(ctx).Bar(lineChartData, {
		        responsive: true,
		        maintainAspectRatio: false
		    });
		}
	});
</script>

<?php $this->load->view("partial/footer"); ?>