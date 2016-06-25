<!DOCTYPE html>
<head>
	<meta charset="UTF-8" />
    <title><?php 
		 $this->load->helper('demo');
		 echo !is_on_demo_host() ?  $this->config->item('company').' -- '.lang('common_powered_by').' 4Biz by LifeTek' : 'Demo - 4Biz by LifeTek | Easy to use Online POS Software' ?></title>
	<link rel="icon" href="<?php echo base_url();?>favicon.ico" type="image/x-icon"/>	
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0"/> <!--320-->
	<base href="<?php echo base_url();?>" />
	
	<link rel="icon" href="<?php echo base_url();?>favicon.ico" type="image/x-icon"/>
	<script type="text/javascript">
		var SITE_URL= "<?php echo site_url(); ?>";
		var BASE_URL= "<?php echo base_url(); ?>";
		var ENABLE_SOUNDS = <?php echo $this->config->item('enable_sounds') ? 'true' : 'false'; ?>;
		var JS_DATE_FORMAT = <?php echo json_encode(get_js_date_format()); ?>;
		var JS_TIME_FORMAT = <?php echo json_encode(get_js_time_format()); ?>;
		var LOCALE =  <?php echo json_encode(get_js_locale()); ?>;
		var IS_MOBILE = <?php echo $this->agent->is_mobile() ? 'true' : 'false'; ?>;
		var DISABLE_QUICK_EDIT = <?php echo $this->config->item('disable_quick_edit') ? 'true' : 'false'; ?>;
	</script>
	<?php 
	$this->load->helper('assets');
	foreach(get_css_files() as $css_file) { ?>
		<link rel="stylesheet" type="text/css" href="<?php echo base_url().$css_file['path'].'?'.ASSET_TIMESTAMP;?>" />
	<?php } ?>
	<?php foreach(get_js_files() as $js_file) { ?>
		<script src="<?php echo base_url().$js_file['path'].'?'.ASSET_TIMESTAMP;?>" type="text/javascript" charset="UTF-8"></script>
	<?php } ?>
	<script type="text/javascript">
		COMMON_SUCCESS = <?php echo json_encode(lang('common_success')); ?>;
		COMMON_ERROR = <?php echo json_encode(lang('common_error')); ?>;
		
		bootbox.addLocale('ar', {
		    OK : 'حسنا',
		    CANCEL : 'إلغاء',
		    CONFIRM : 'تأكيد'			
		});
		
		bootbox.addLocale('km', {
		    OK :'យល់ព្រម',
		    CANCEL : 'បោះបង់',
		    CONFIRM : 'បញ្ជាក់ការ'			
		});
		bootbox.setLocale(LOCALE);
		$.ajaxSetup ({
			cache: false,
			headers: { "cache-control": "no-cache" }
		});
		toastr.options = {
		  "closeButton": true,
		  "debug": false,
		  "newestOnTop": false,
		  "progressBar": false,
		  "positionClass": "toast-top-right",
		  "preventDuplicates": false,
		  "onclick": null,
		  "showDuration": "300",
		  "hideDuration": "1000",
		  "timeOut": "5000",
		  "extendedTimeOut": "1000",
		  "showEasing": "swing",
		  "hideEasing": "linear",
		  "showMethod": "fadeIn",
		  "hideMethod": "fadeOut"
		}
		
    $.fn.editableform.buttons = 
      '<button tabindex="-1" type="submit" class="btn btn-primary btn-sm editable-submit">'+
        '<i class="icon ti-check"></i>'+
      '</button>'+
      '<button tabindex="-1" type="button" class="btn btn-default btn-sm editable-cancel">'+
        '<i class="icon ti-close"></i>'+
      '</button>';
	  
 	  $.fn.editable.defaults.emptytext = <?php echo json_encode(lang('common_empty')); ?>;
		
		$(document).ready(function()
		{
			$(".wrapper.mini-bar .left-bar").hover(
			   function() {
			     $(this).parent().removeClass('mini-bar');
			   }, function() {
			     $(this).parent().addClass('mini-bar');
			   }
			 );

		    $('.menu-bar').click(function(e){                  
		    	e.preventDefault();
		        $(".wrapper").toggleClass('mini-bar');        
		    }); 
    					
			//Ajax submit current location
			$(".set_employee_current_location_id").on('click',function(e)
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
				    	window.location.reload(true);	
				    }
				});
				
			});
			
			$(".set_employee_language").on('click',function(e)
			{
				e.preventDefault();

				var language_id = $(this).data('language-id');
				$.ajax({
				    type: 'POST',
				    url: '<?php echo site_url('employees/set_language'); ?>',
				    data: { 
				        'employee_language_id': language_id, 
				    },
				    success: function(){
				    	window.location.reload(true);	
				    }
				});
				
			});

            /* Validate Form Fields: Begin */
            var $form_validate = $('.form-validate');
            if ($form_validate.size() > 0) {
                $form_validate.validate();
            }
            /* Validate Form Fields: End */
			
			<?php
			//If we are using on browser close (NULL or ""; both false) then we want to keep session alive
			if (!$this->Appconfig->get_raw_phppos_session_expiration())
			{		
			?>
				//Keep session alive by sending a request every 5 minutes
				setInterval(function(){$.get('<?php echo site_url('home/keep_alive'); ?>');}, 300000);
			<?php } ?>
		});
	</script>

</head>
<body>
	<div class="modal fade hidden-print" id="myModal" tabindex="-1" role="dialog" aria-hidden="true"></div>
	<div class="modal fade hidden-print" id="myModalDisableClose" tabindex="-1" role="dialog" aria-hidden="true" data-keyboard="false" data-backdrop="static"></div>
	
	<div class="wrapper <?php echo $this->uri->segment(1)=='sales' || $this->uri->segment(1)=='receivings'  ? 'mini-bar sales-bar' : ''; ?>">
		<div class="left-bar hidden-print" >
			<div class="admin-logo" style="<?php echo isset($location_color) && $location_color ? 'background-color: '.$location_color.' !important': ''; ?>">
				<div class="logo-holder pull-left">
					<?php echo img(
					array(
						'src' => $this->Appconfig->get_logo_image(),
						'class'=>'hidden-print logo',
						'id'=>'header-logo',

					)); ?>
				</div>
				<!-- logo-holder -->
				<?php  

				?>			
			</div>
			<!-- admin-logo -->

			<ul class="list-unstyled menu-parent" id="mainMenu">
			
				
				<li  <?php echo $this->uri->segment(1)=='home'  ? 'class="active"' : ''; ?>>
					<a tabindex = "-1" href="<?php echo site_url('home'); ?>" class="waves-effect waves-light">
						<i class="icon ti-dashboard"></i>
						<span class="text"><?php echo lang('common_dashboard'); ?></span>
					</a></li>
				<?php foreach($all_allowed_modules as $module) { ?>
                    <?php if (empty($module->main_menu)) continue; ?>
					<li <?php echo $module->module_id==$this->uri->segment(1)  ? 'class="active"' : ''; ?>>
						<a tabindex = "-1" href="<?php echo site_url("$module->module_id");?>"  class="waves-effect waves-light">
							<i class="icon ti-<?php echo $module->icon; ?>"></i>
							<span class="text"><?php echo lang("module_".$module->module_id) ?></span>
						</a>
					</li>
				<?php } ?>
				<?php
				if ($this->config->item('timeclock')) 
				{
				?>
					<li <?php echo 'timeclocks'==$this->uri->segment(1)  ? 'class="active"' : ''; ?>>
						<a tabindex = "-1" href="<?php echo site_url("timeclocks");?>">
							<i class="icon ti-alarm-clock"></i>
							<span class="text"><?php echo lang("employees_timeclock") ?></span>
						</a>
					</li>				
				<?php
				}
				?>
				
                <li>
					<?php
					if ($this->config->item('track_cash') && $this->Register->is_register_log_open()) {
						$continue = $this->config->item('timeclock') ? 'timeclocks' : 'logout';
						echo anchor("sales/closeregister?continue=$continue",'<i class="icon ti-power-off"></i><span class="text">'.lang("common_logout").'</span>', array('tabindex' => '-1'));
					} else {
						
						if ($this->config->item('timeclock') && $this->Employee->is_clocked_in())
						{
							echo anchor("timeclocks",'<i class="icon ti-power-off"></i><span class="text">'.lang("common_logout").'</span>', array('tabindex' => '-1'));
						}
						else
						{
							echo anchor("home/logout",'<i class="icon ti-power-off"></i><span class="text">'.lang("common_logout").'</span>', array('tabindex' => '-1'));
						}
					}
					?>

                </li>
			</ul>
		</div>
		<!-- left-bar -->

		<div class="content" id="content">
		<div class="overlay hidden-print"></div>			
			<div class="top-bar hidden-print">				
				<nav class="navbar navbar-default top-bar">
					<div class="menu-bar-mobile" id="open-left"><i class="ti-menu"></i></div>
					<div class="nav navbar-nav top-elements navbar-breadcrumb hidden-xs">
						 <?php 
						 $this->load->helper('breadcrumb');
						 echo create_breadcrumb(); ?>
					</div>	

					<ul class="nav navbar-nav navbar-right top-elements">															
					<?php if ($this->config->item('show_clock_on_header')) { ?>
					<li>
						
						<?php
						$url = 'javascript:void(0);';
						
						if ($this->config->item('timeclock'))
						{
							$url = site_url('timeclocks');
						}
							
						?>
						<a href="<?php echo $url;?>" class="visible-lg">
							<?php echo date(get_time_format()); ?>
							<?php echo date(get_date_format()) ?>
						</a>
					</li>
					<?php } ?>
					<?php if(($this->uri->segment(1)=='sales' && $this->uri->segment(2) != 'receipt' && $this->uri->segment(2) != 'complete') || ($this->uri->segment(1)=='receivings' && $this->uri->segment(2) != 'receipt' && $this->uri->segment(2) != 'complete')) { ?>
						<li class="dropdown">
							<a tabindex = "-1" href="#" class="fullscreen" data-toggle="" role="button" aria-expanded="false"><i class="ion-arrow-expand  icon-notification"></i></a>
						</li>
						<li class="dropdown">
							<a tabindex = "-1" data-target="#" class="" data-toggle="" role="button" aria-expanded="false"><i class="ion-bag  icon-notification"></i><span class="badge info-number cart cart-number count">0</span></a>
						</li>

					<?php } ?>
					<?php if ($this->Employee->has_module_permission('messages', $user_info->person_id)) {?>
						
						<li class="dropdown">
							<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false"><i class="ion-ios-bell-outline  icon-notification"></i><span class="badge info-number count <?php echo $new_message_count > 0 ? 'bell': '';?>" id="unread_message_count"><?php echo $new_message_count; ?></span></a>
								<ul class="dropdown-menu animated fadeInUp wow message_drop neat_drop" data-wow-duration="1500ms" role="menu">
								<?php foreach ($this->Employee->get_messages(4) as $key => $value) { ?>
									<li>
										<a href="<?php echo site_url('messages/view/'.$value['message_id']); ?>">
											<span class="avatar_left"><img src="<?php echo base_url(); ?>assets/assets/images/avatar-default.jpg" alt=""></span>
											<span class="text_info"><?php echo $value['message']; ?></span> 
											<span class="time_info"><?php echo date(get_date_format().' '.get_time_format(), strtotime($value['created_at'])) ?> <i class="ion-record <?php echo !$value['message_read'] ? 'online' : ''?>"></i></span> 
										</a>
									</li>	
							 	<?php	} ?>
									<li class="bottom-links">
										<a href="<?php echo site_url('messages') ?>" class="last_info"><?php echo lang('common_see_all_notifications');?></a>
									</li>
									<?php if ($this->Employee->has_module_action_permission('messages','send_message',$this->Employee->get_logged_in_employee_info()->person_id)) {  ?>									
									
										<li class="bottom-links">
											<a href="<?php echo site_url('messages/sent_messages'); ?>" class="last_info"><?php echo lang('common_view_sent_message') ?></a>
										</li>
									
										<li class="bottom-links">
											<a href="<?php echo site_url('messages/send_message') ?>" class="last_info"><?php echo lang('employees_send_message');?></a>
										</li>
									<?php } ?>
								</ul>
						</li>
						<?php } ?>
					<?php if (count($authenticated_locations) > 1) { ?>
						<li class="dropdown">
							<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false"> <?php echo $authenticated_locations[$current_logged_in_location_id]; ?> <span class="drop-icon"><i class="ion ion-chevron-down"></i></span></a>
								<ul class="dropdown-menu animated fadeInUp wow locations-drop locations-drop neat_drop" data-wow-duration="1500ms" role="menu">
								<?php foreach ($authenticated_locations as $key => $value) { ?>
									<li><a class="set_employee_current_location_id" data-location-id="<?php echo $key; ?>" href="<?php echo site_url('home/set_employee_current_location_id/'.$key) ?>"><span class="badge" style="background-color:<?php echo $this->Location->get_info($key)->color; ?>">&nbsp;</span> <?php echo $value; ?> </a></li>
								<?php } ?>
								</ul>
							</li>	

					<?php } ?>
						<?php if ($this->config->item('show_language_switcher')) { ?>
						<?php 
						$languages = array('english'  => 'English',
							'indonesia'    => 'Indonesia',
							'spanish'   => 'Español', 
							'french'    => 'Fançais',
							'italian'    => 'Italiano',
							'german'    => 'Deutsch',
							'dutch'    => 'Nederlands',
							'portugues'    => 'Portugues',
							'arabic' => 'العَرَبِيةُ‎‎',
							'khmer' => 'Khmer',
						);

						?>	
						<!-- redirect($_SERVER['HTTP_REFERER']);	 -->
						<li class="dropdown">
							<a tabindex = "-1" href="#" class="dropdown-toggle language-dropdown" data-toggle="dropdown" role="button" aria-expanded="false"><img class=
							"flag_img" src="<?php echo base_url(); ?>assets/assets/images/flags/<?php echo $user_info->language ? $user_info->language : "english";  ?>.png" alt=""> <span class="hidden-sm"> <?php echo $user_info->language ? $languages[$user_info->language] : $languages["english"];  ?></span><span class="drop-icon"><i class="ion ion-chevron-down"></i></span></a>
							<ul class="dropdown-menu animated fadeInUp wow language-drop neat_drop" data-wow-duration="1500ms" role="menu">
							<?php foreach ($languages as $key => $value) { 
								if($user_info->language!=$key){
							 	?>
								<li><a tabindex = "-1" href="<?php echo site_url('employees/set_language/') ?>" data-language-id="<?php echo $key; ?>" class="set_employee_language"><img class="flag_img" src="<?php echo base_url(); ?>assets/assets/images/flags/<?php echo $key; ?>.png" alt="flags"><?php echo $value; ?></a></li>
							<?php } } ?>
							</ul>
						</li>	
						<?php } ?>
							
						

						
						<li class="dropdown">
							<a tabindex = "-1" href="#" class="dropdown-toggle avatar_width" data-toggle="dropdown" role="button" aria-expanded="false"><span class="avatar-holder">

							<?php echo $user_info->image_id ? img(array('src' => site_url('app_files/view/'.$user_info->image_id))) : img(array('src' => base_url('assets/assets/images/avatar-default.jpg'))); ?></span>

							<span class="avatar_info hidden-sm"><?php echo $user_info->first_name." ".$user_info->last_name; ?></span></a>
							<ul class="dropdown-menu user-dropdown animated fadeInUp wow avatar_drop neat_drop" data-wow-duration="1500ms"  role="menu">
								<?php if ($this->Employee->has_module_permission('config', $user_info->person_id)) {?>
								
									<li><?php echo anchor("config",'<i class="ion-android-settings"></i><span class="text">'.lang("common_settings").'</span>', array('tabindex' => '-1')); ?></li>
								<?php } ?>
								<li>
									<a tabindex = "-1" id="switch_user" href="<?php echo site_url('login/switch_user/'.($this->uri->segment(1) == 'sales' ? '0' : '1'));  ?>" data-toggle="modal" data-target="#myModalDisableClose"><i class="ion-ios-toggle-outline"></i><span class="text"><?php echo lang('common_switch_user'); ?></span></a>
								</li>
								<li>
									<a tabindex = "-1" title="" href="<?php echo site_url('login/edit_profile')?>" data-toggle="modal" data-target="#myModal"><i class="ion-edit"></i><span class="text"><?php echo lang('common_edit_profile'); ?></span></a>
								</li>
								<?php
								if ($this->config->item('timeclock')) 
								{
								?>
					         		<li>
									<?php
										echo anchor("timeclocks",'<i class="ion-clock"></i>'.lang("employees_timeclock"), array('tabindex' => '-1'));
					 				?>
									</li>
								<?php
								}
								?>								
								<li>
								<?php
									if ($this->config->item('track_cash') && $this->Register->is_register_log_open()) {
										$continue = $this->config->item('timeclock') ? 'timeclocks' : 'logout';
										echo anchor("sales/closeregister?continue=$continue",'<i class="ion-power"></i><span class="text">'.lang("common_logout").'</span>',array('class'=>'logout_button','tabindex' => '-1'));
									} else {
										
										if ($this->config->item('timeclock') && $this->Employee->is_clocked_in())
										{
											echo anchor("timeclocks",'<i class="ion-power"></i><span class="text">'.lang("common_logout").'</span>',array('class'=>'logout_button','tabindex' => '-1'));
										}
										else
										{
											echo anchor("home/logout",'<i class="ion-power"></i><span class="text">'.lang("common_logout").'</span>',array('class'=>'logout_button','tabindex' => '-1'));
										}
									}
									?>
								</li>			
							</ul>
						</li>							
					</ul>
				</nav>
			</div>
			<!-- top-bar -->
			<div class="main-content">
