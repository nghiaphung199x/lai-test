<?php
if($export_excel == 1)
{
	if (!$this->config->item('legacy_detailed_report_export'))
	{
		$rows = array();
	
		$row = array();
		foreach ($headers['details'] as $header) 
		{
			$row[] = strip_tags($header['data']);
		}

		foreach ($headers['summary'] as $header) 
		{
			$row[] = strip_tags($header['data']);
		}
		$rows[] = $row;
	
		foreach ($summary_data as $key=>$datarow) 
		{		
			foreach($details_data[$key] as $datarow2)
			{
				$row = array();
				foreach($datarow2 as $cell)
				{
					$row[] = str_replace('<span style="white-space:nowrap;">-</span>', '-', strip_tags($cell['data']));				
				}
			
				foreach($datarow as $cell)
				{
					$row[] = str_replace('<span style="white-space:nowrap;">-</span>', '-', strip_tags($cell['data']));
				}
				$rows[] = $row;
			}
		
		}
	}
	else
	{
		$rows = array();
		$row = array();
		foreach ($headers['summary'] as $header) 
		{
			$row[] = strip_tags($header['data']);
		}
		$rows[] = $row;
	
		foreach ($summary_data as $key=>$datarow) 
		{
			$row = array();
			foreach($datarow as $cell)
			{
				$row[] = str_replace('<span style="white-space:nowrap;">-</span>', '-', strip_tags($cell['data']));
			
			}
		
			$rows[] = $row;

			$row = array();
			foreach ($headers['details'] as $header) 
			{
				$row[] = strip_tags($header['data']);
			}
		
			$rows[] = $row;
		
			foreach($details_data[$key] as $datarow2)
			{
				$row = array();
				foreach($datarow2 as $cell)
				{
					$row[] = str_replace('<span style="white-space:nowrap;">-</span>', '-', strip_tags($cell['data']));				
				}
				$rows[] = $row;
			}
		}
	}
	$this->load->helper('spreadsheet');
	$content = array_to_spreadsheet($rows);
	$this->load->helper('download');
	force_download(strip_tags($title) . '.'.($this->config->item('spreadsheet_format') == 'XLSX' ? 'xlsx' : 'csv'), $content);
	exit;
}

?>
<?php $this->load->view("partial/header"); ?>

<div class="row">
	<?php foreach($overall_summary_data as $name=>$value) { ?>
	    <div class="col-md-3 col-xs-12 col-sm-6 ">
	        <div class="info-seven primarybg-info">
	            <div class="logo-seven hidden-print"><i class="ti-widget dark-info-primary"></i></div>
	            <?php echo to_currency($value); ?>
	            <p><?php echo lang('reports_'.$name); ?></p>
	        </div>
	    </div>
	<?php }?>
</div>

<?php if(isset($pagination) && $pagination) {  ?>
	<div class="pagination hidden-print alternate text-center" id="pagination_top" >
		<?php echo $pagination;?>
	</div>
<?php }  ?>
	
	
<div class="row">
	<div class="col-md-12">
		<div class="panel panel-piluku reports-printable">
			<div class="panel-heading">
				<?php echo lang('reports_reports'); ?> - <?php echo $title ?>
				<small class="reports-range"><?php echo $subtitle ?></small>
			</div>
			<div class="panel-body">
				<div class="table-responsive">
				<table class="table table-hover detailed-reports table-reports table-bordered  tablesorter" id="sortable_table">
					<thead>
						<tr align="center" style="font-weight:bold">
							<td class="hidden-print"><a href="#" class="expand_all" >+</a></td>
							<?php foreach ($headers['summary'] as $header) { ?>
							<td align="<?php echo $header['align']; ?>"><?php echo $header['data']; ?></td>
							<?php } ?>
						
						</tr>
					</thead>
					<tbody>
						<?php foreach ($summary_data as $key=>$row) { ?>
						<tr>
							<td class="hidden-print"><a href="#" class="expand" style="font-weight: bold;">+</a></td>
							<?php foreach ($row as $cell) { ?>
							<td align="<?php echo $cell['align']; ?>"><?php echo $cell['data']; ?></td>
							<?php } ?>
						</tr>
						<tr>
							<td colspan="<?php echo count($headers['summary']) + 1; ?>" class="innertable" style="display:none;">
								<table class="table table-bordered">
									<thead>
										<tr>
											<?php foreach ($headers['details'] as $header) { ?>
											<th align="<?php echo $header['align']; ?>"><?php echo $header['data']; ?></th>
											<?php } ?>
										</tr>
									</thead>

									<tbody>
										<?php foreach ($details_data[$key] as $row2) { ?>

											<tr>
												<?php foreach ($row2 as $cell) { ?>
												<td align="<?php echo $cell['align']; ?>"><?php echo $cell['data']; ?></td>
												<?php } ?>
											</tr>
										<?php } ?>
									</tbody>
								</table>
							</td>
						</tr>
						<?php } ?>
					</tbody>
				</table>
				</div>
				<div class="text-center">
					<button class="btn btn-primary text-white hidden-print" id="print_button"  > <?php echo lang('common_print'); ?> </button>	
				</div>
			</div>
		</div>
	</div>
</div>
	
	<?php if(isset($pagination) && $pagination) {  ?>
		<div class="pagination hidden-print alternate text-center" id="pagination_top" >
			<?php echo $pagination;?>
		</div>
	<?php }  ?>
</div>
<script type="text/javascript" language="javascript">
$(document).ready(function()
{
	$(".tablesorter a.expand").click(function(event)
	{
		$(event.target).parent().parent().next().find('td.innertable').toggle();
		
		if ($(event.target).text() == '+')
		{
			$(event.target).text('-');
		}
		else
		{
			$(event.target).text('+');
		}
		return false;
	});
	
	$(".tablesorter a.expand_all").click(function(event)
	{
		$('td.innertable').toggle();
		
		if ($(event.target).text() == '+')
		{
			$(event.target).text('-');
			$(".tablesorter a.expand").text('-');
		}
		else
		{
			$(event.target).text('+');
			$(".tablesorter a.expand").text('+');
		}
		return false;
	});
	
});

function print_report()
{
	window.print();
}
$(document).ready(function()
{
	$('#print_button').click(function(e){
		e.preventDefault();
		print_report();
	});
});

</script>
<?php $this->load->view("partial/footer"); ?>