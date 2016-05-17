<?php $this->load->view("partial/header"); ?> 
<div id="sales_page_holder">
	<div id="customer_display_container" class="sales clearfix">
	  <?php $this->load->view("sales/customer_display"); ?>
	</div>
</div>

<script>
	setInterval(function(){
		$("#customer_display_container").load('<?php echo site_url('sales/customer_display_update/'.$register_id); ?>');
	}, 800);

$(document).on('click', "#email_receipt",function()
{
	$.get($(this).attr('href'), function()
	{
		show_feedback('success', <?php echo json_encode(lang('common_receipt_sent')); ?>, <?php echo json_encode(lang('common_success')); ?>);
		
	});
	
	return false;
});

$(document).ready(function(){
	$('.fullscreen').on('click',function (e) {
		e.preventDefault();
		salesRecvFullScreen();
		$.get('<?php echo site_url("home/set_fullscreen_customer_display/1");?>');
	});
	
	$(document).on('click', ".dismissfullscreen",function(e) {
		e.preventDefault();
		salesRecvDismissFullscren();
		$.get('<?php echo site_url("home/set_fullscreen_customer_display/0");?>');
	});

	$(window).load(function()
	{
		setTimeout(function()
		{
		<?php if ($fullscreen_customer_display) { ?>
			$('.fullscreen').click();
		<?php }
		else {
		?>
		$('.dismissfullscreen').click();	
		<?php
		} ?>
		
		}, 0);
	});
});


</script>

<?php $this->load->view("partial/footer"); ?>
