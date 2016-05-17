<div class="row">
	<div class="col-md-12">
	<?php if(isset($pagination) && $pagination) {  ?>
		<div class="pagination hidden-print alternate text-center" id="pagination_top" >
			<?php echo $pagination;?>
		</div>
	<?php }  ?>
		<div class="panel panel-piluku reports-printable">
			<div class="panel-heading">
				<?php echo lang('reports_reports'); ?> - <?php echo $title ?>
				<span title="<?php echo $subtitle ?>" class="badge bg-primary tip-left pull-right"><?php echo $subtitle ?></span>
			</div>
			<div class="panel-body">
				<div class="table-responsive no-hover">
					<table id="contents" class="table">
						<tr>
							<td id="item_table">
								<div id="table_holder">
									<table class="tablesorter table report" id="sortable_table">
										<thead>
											<tr>
												<td><a href="#" class="expand_all" style="font-weight: bold; ">+</a></td>
												<?php foreach ($headers['summary'] as $header) { ?>
												<td align="<?php echo $header['align']; ?>"><?php echo $header['data']; ?></td>
												<?php } ?>
											</tr>
										</thead>
										<tbody>
											<?php foreach ($summary_data as $key=>$row) { ?>
											<tr>
												<td><a href="#" class="expand" style="font-weight: bold;">+</a></td>
												<?php foreach ($row as $cell) { ?>
												<td align="<?php echo $cell['align']; ?>"><?php echo $cell['data']; ?></td>
												<?php } ?>
											</tr>
											<tr>
												<td colspan="<?php echo count($headers['summary']) + 1; ?>" class="innertable" style="display:none;">
													<table class="innertable table table-reports table-bordered" >
														<thead>
															<tr>
																<?php foreach ($headers['details'] as $header) { ?>
																<td align="<?php echo $header['align']; ?>"><?php echo $header['data']; ?></td>
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
							</td>
						</tr>
					</table>
				</div>
				<div id="report_summary" class="tablesorter pull-right report report-sumary">
					<?php foreach($overall_summary_data as $name=>$value) { ?>
						<div class="summary_row">
							<span class="name"><?php echo lang('reports_'.$name) ?></span>
							<span class="value"><?php echo to_currency($value) ?></span>
						</div>
					<?php }?>
				</div>
			</div>
		</div>
		<?php if(isset($pagination) && $pagination) {  ?>
			<div class="pagination hidden-print alternate text-center" id="pagination_top" >
				<?php echo $pagination;?>
			</div>
		<?php }  ?>
	</div>
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
</script>