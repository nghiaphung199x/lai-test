<?php $this->load->view("partial/header"); ?>
<div class="manage_buttons hidden-print">
	<div class="buttons-list">
		<div class="pull-right-btn">
			<button class="btn btn-primary btn-lg hidden-print" id="print_button" onClick="do_print()" > <?php echo lang('common_print'); ?> </button>
			<?php 
				echo anchor('items/new_count', lang('items_new_inventory_count'),array('class'=>'btn btn-primary btn-lg'));
				if ($status == 'closed') 	
				{ 
				 	echo anchor('items/count/open', lang('items_show_open_counts'),array('class'=>'btn btn-success btn-lg'));
				}
				else
				{
					echo anchor('items/count/closed', lang('items_show_closed_counts'),array('class'=>'btn btn-warning btn-lg'));	
				}
			?>
		</div>
	</div>

</div>
<style type="text/css">
	#pdf_content {
		width: 700px;
		display: block;
		overflow: hidden;
		position: relative;
		margin-left: 10px;
		font-size: 12px;
	}
	#pdf_header {
		height: 100px;
	}
	#pdf_logo {
		position: relative;
		top: 0px;
		left: 0px;
	}
	#pdf_logo img {
		max-height: 70px;
	}
	#pdf_company {
		position: relative;
		top: -70px;
	}
	#company_name {
		text-transform: uppercase;
		font-weight: bold;
		color: #002FC2
	}
	#pdf_short_info {
		position: relative;
		left: 550px;
		top: -70px;
	}
	#pdf_sinnature_time {
		position: absolute;
		left: 500px;
	}
	#pdf_content span {
		color: #002FC2;
	}
	#pdf_title {
		width: 100%;
		text-align: center;
		text-transform: uppercase;
		font-weight: bold;
		font-size: 16px;
		margin-top: 12px;
	}
	#pdf_tbl_items {
		border-collapse: collapse;
		font-size: 12px;
		margin: 10px 0;
	}
	#pdf_tbl_items tboby {
		display: table-row-group;
		vertical-align: middle;
		border-color: inherit;
	}
	#pdf_tbl_items tr {
		display: table-row;
		vertical-align: inherit;
		border-color: inherit;
	}

	#pdf_tbl_items th, #pdf_tbl_items td {
		border: 1px solid #000;
		padding: 3px;
	}

	#pdf_signature {
		min-height: 150px;
		padding-top: 30px;
	}
	#pdf_signature div {
		text-align: center;
	}
	#pdf_signature lable {
		font-size: 16px;
		font-weight: bold;
	}
	.w50 {
		width: 50%;
	}

	.w20 {
		width: 20%;
	}

	.w100 {
		width: 100%;
	}
	.pb20 {
		padding-bottom: 20px;
	}

	.pt20 {
		padding-top: 20px;
	}

	#pdf_header h3, #pdf_header p {
		text-align: center;
	}
	#pdf_footer {
		text-align: center;
		margin-top: 100px;
	}
	#pdf_content table td, #pdf_content table th {
		text-align: right;
		height: auto !important;
	}
	p {
		margin: 3px 0;
	}
	.w150px {
		width: 150px;
	}
	.fontI {
		font-style: italic;
	}
</style>
<?php
if (isset($error_message))
{
	echo '<h1 style="text-align: center;">'.$error_message.'</h1>';
	exit;
}
?>

<div class="row manage-table receipt_<?php echo $this->config->item('receipt_text_size') ? $this->config->item('receipt_text_size') : 'small';?>" id="receipt_wrapper">
	<div class="col-md-12" id="receipt_wrapper_inner">
		<div class="panel panel-piluku">
			<div class="panel-body panel-pad">
				
<!-- BEGIN -->
<div id="pdf_content">
	<div id="pdf_header">
		<div id="pdf_logo">
			<?php if($this->config->item('company_logo')) {?>
				<?php echo img(array('src' => $this->Appconfig->get_logo_image())); ?>
			<?php } ?>
		</div>
		<div id="pdf_company">
			<p id="company_name"><?php echo $this->config->item('company'); ?></p>
			<p><span><?php echo nl2br($this->Location->get_info_for_key('address', isset($override_location_id) ? $override_location_id : FALSE)); ?></span></p>
			<p>Điện Thoại: <span><?php echo $this->Location->get_info_for_key('phone', isset($override_location_id) ? $override_location_id : FALSE); ?></span>, Fax: <span><?php echo $this->Location->get_info_for_key('fax', isset($override_location_id) ? $override_location_id : FALSE); ?></span></p>
			<?php if($this->config->item('website')) { ?>
				<p>Website: <span><?php echo $this->config->item('website'); ?></span></p>
			<?php } ?>
		</div>
	</div>
	<div id="pdf_title" class="clb">
		<p>BIÊN BẢN KIỂM TRA SỐ <?php echo $count_id; ?></p>
	</div>

	<div id="pdf_customer">
		<p>Ngày: <?php echo $create_datetime; ?></p>
	</div>
	<div class="w100 clb">
		<table id="pdf_tbl_items" class="w100">
			<tbody>
				<tr>
					<th>STT</th>
					<th><?php echo lang('common_item_number'); ?></th>
					<th><?php echo lang('common_item_name'); ?></th>
					<th><?php echo lang('common_category'); ?></th>
					<th><?php echo lang('common_location'); ?></th>
					<th>Số lượng kho</th>
					<th>Số lượng kiểm</th>
					<th>Chênh lệch</th>
				</tr>
				<?php $stt = 0; $total_count = 0;?>
				<?php foreach($audit_items as $line=>$item) { ?>
					<?php $stt ++; $total_count += $item['count']; ?>
					<tr>
						<td><?php echo $stt; ?></td>
						<td><?php echo $item['item_number']; ?><?php if ($item['size']){ ?> (<?php echo $item['size']; ?>)<?php } ?></td>
						<td><?php echo $item['name']; ?></td>
						<td><?php echo $item['category']; ?></td>
						<td><?php echo $this->Location->get_info_for_key('name',$item['location_id']); ?></td>
						<td><?php echo to_quantity($item['actual_quantity']); ?></td>
						<td><?php echo to_quantity($item['count']); ?></td>
						<td><?php echo to_quantity(($item['count'] - $item['actual_quantity'])); ?></td>
					</tr>
					<?php if (!$item['description']=="" ||(isset($item['serialnumber']) && $item['serialnumber'] !="") ) {?>
					<tr>
						<td colspan="8">
							<?php if(!$item['description']==""){ ?>
								<div class="invoice-desc"><?php echo $item['description']; ?></div>
							<?php } ?>

							<?php if(isset($item['serialnumber']) && $item['serialnumber'] !=""){ ?>
		                    	<div class="invoice-desc"><?php echo $item['serialnumber']; ?></div>
		                    <?php } ?>
						</td>
					</tr>
					<?php } ?>
				<?php } ?>
				<tr>
					<td colspan="6">Tổng</td>
					<td><?php echo $total_count; ?></td>
					<td></td>
				</tr>
			</tbody>
		</table>
	</div>
</div>
<!-- END -->
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

function do_print()
{
	window.print();
	<?php
	if ($this->config->item('redirect_to_sale_or_recv_screen_after_printing_receipt'))
	{
	?>
 	window.location = '<?php echo site_url('items/count'); ?>';
	<?php
	}
	?>
}
</script>
