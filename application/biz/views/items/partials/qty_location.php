<style type="text/css">
.customer-recent-sales .modal-body table tr th {
	border: 1px solid #d7dce5;
}
</style>
<!-- Modal -->
<div class="modal fade" id="qtyLocationModal" tabindex="-1"
	role="dialog" aria-labelledby="myModalLabel">
	<div class="modal-dialog customer-recent-sales" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal"
					aria-label="Close">
					<span class="ti-close" aria-hidden="true"></span>
				</button>
				<h4 class="modal-title" id="myModalLabel"><?php echo Lang('common_count_detail'); ?></h4>
			</div>
			<div class="modal-body">
				<table
					class="table table-bordered table-striped table-hover data-table"
					id="dTableA">
					<thead>
						<th>STT</th>
						<th><?php echo Lang('common_name'); ?></th>
						<th><?php echo Lang('common_location'); ?></th>
						<th><?php echo Lang('common_count'); ?></th>
					</thead>
					<tbody>
          <?php foreach ($qty_locations as $index => $qty_location) { ?>
            <tr>
							<td><?php echo $index + 1;?></td>
							<td><?php echo $qty_location['item_name'];?></td>
							<td><?php echo $qty_location['location_name'];?></td>
							<td><?php echo $qty_location['quantity_converted'];?></td>
						</tr>
          <?php } ?>
          </tbody>
				</table>
			</div>
		</div>
	</div>
</div>