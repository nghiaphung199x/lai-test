<?php $this->load->view("partial/header"); ?>

<?php
if (isset($error_message))
{
	echo '<h1 style="text-align: center;">'.$error_message.'</h1>';
	exit;
}
?>

<div class="manage_buttons hidden-print">
	<div class="row">
		<div class="col-md-6">
			<span class="hidden-print search no-left-border">
				<ul class="list-inline print-buttons">
					<li></li>
					
					<?php
					 if ($this->Employee->has_module_action_permission('receivings', 'edit_receiving', $this->Employee->get_logged_in_employee_info()->person_id)){
				   		$edit_recv_url = $suspended ? 'unsuspend' : 'change_recv';
						echo '<li>';
						echo form_open("receivings/$edit_recv_url/".$receiving_id_raw,array('id'=>'receivings_change_form')); ?>
						<button class="btn btn-primary btn-lg hidden-print" id="edit_recv" > <?php echo lang('receivings_edit'); ?> </button>
							</form>		
						</li>
				
					<?php }	?>
					<li>
						<button class="btn btn-primary btn-lg hidden-print" id="barcode_labels_button" onClick="window.location='<?php echo site_url('items/generate_barcodes_labels_from_recv/'.$receiving_id_raw); ?>'"; > <?php echo lang('common_barcode_labels'); ?> </button>						
					</li>
					<li>
						<button class="btn btn-primary btn-lg hidden-print" id="barcode_sheet_button" onClick="window.open('<?php echo site_url('items/generate_barcodes_from_recv/'.$receiving_id_raw); ?>','_blank');" > <?php echo lang('common_barcode_sheet'); ?> </button>						
					</li>
					
					<li>
						<?php if (!empty($supplier_email)) { ?>
							<?php echo anchor('receivings/email_receipt/'.$receiving_id_raw, $is_po ? lang('receivings_email_po') : lang('common_email_receipt'), array('id' => 'email_receipt','class' => 'btn btn-primary btn-lg hidden-print'));?>
						<?php }?>
					</li>
					
				</ul>
			</span>
		</div>
		<div class="col-md-6">	
			<div class="buttons-list">
				<div class="pull-right-btn">
					<ul class="list-inline print-buttons">
						<li>
							<button class="btn btn-primary btn-lg hidden-print" id="print_button" onClick="do_print()" > <?php echo lang('common_print'); ?> </button>							
						</li>
						<li>
							<button class="btn btn-primary btn-lg hidden-print" id="new_receiving_button_1" onclick="window.location='<?php echo site_url('receivings'); ?>'" > <?php echo lang('receivings_new_receiving'); ?> </button>
						</li>
					</ul>
				</div>
			</div>				
		</div>
	</div>
</div>

<div class="row manage-table receipt_<?php echo $this->config->item('receipt_text_size') ? $this->config->item('receipt_text_size') : 'small';?>" id="receipt_wrapper">
	<div class="col-md-12" id="receipt_wrapper_inner">
		<div class="panel panel-piluku">
			<div class="panel-body panel-pad">
				<?php echo $pdf_block_html; ?>
			</div>
		</div>
	</div>
</div>
<?php $this->load->view("partial/footer"); ?>
<script type="text/javascript">

$("#edit_recv").click(function(e)
{
	e.preventDefault();
	bootbox.confirm(<?php echo json_encode(lang('receivings_edit_confirm')); ?>, function(result)
	{
		if (result)
		{
			$("#receivings_change_form").submit();
		}
	});
});

$("#email_receipt").click(function()
{
	$.get($(this).attr('href'), function()
	{
		show_feedback('success', <?php echo json_encode(lang('common_receipt_sent')); ?>, <?php echo json_encode(lang('common_success')); ?>);
		
	});
	
	return false;
});

<?php if ($this->config->item('print_after_receiving') && $this->uri->segment(2) == 'complete')
{
?>
$(window).load(function()
{
	do_print();
});
<?php
}
?>
function do_print()
{
	window.print();
	<?php
	if ($this->config->item('redirect_to_sale_or_recv_screen_after_printing_receipt'))
	{
	?>
 	window.location = '<?php echo site_url('receivings'); ?>';
	<?php
	}
	?>
}
</script>
