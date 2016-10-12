<style type="text/css">
.customer-recent-sales .modal-body table tr th {
	border: 1px solid #d7dce5;
}
</style>
<!-- Modal -->
<div class="modal fade" id="NotAuditedLocationModal" tabindex="-1"
	role="dialog" aria-labelledby="myModalLabel">
	<div class="modal-dialog customer-recent-sales" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal"
					aria-label="Close">
					<span class="ti-close" aria-hidden="true"></span>
				</button>
				<h4 class="modal-title" id="myModalLabel"><?php echo Lang('common_not_audit_detail'); ?></h4>
			</div>
			<div class="modal-body">
				<table
					class="table table-bordered table-striped table-hover data-table"
					id="dTableA">
					<thead>
						<th>STT</th>
						<th><?php echo Lang('common_name'); ?></th>
						<th><?php echo Lang('common_category'); ?></th>
						<th><?php echo Lang('common_count'); ?></th>
					</thead>
					<tbody>
          <?php foreach ($notAuditedItems as $index => $item) { ?>
            <tr>
							<td><?php echo $index + 1;?></td>
							<td><?php echo $item['name'];?></td>
							<td><?php echo $item['category'];?></td>
							<td><?php echo qtyToString($item['item_id'], $item['location_quantity']);?></td>
						</tr>
          <?php } ?>
          </tbody>
				</table>
				
				<div style="text-align: center;">
					<a href="<?php echo site_url("items/extract_not_audit_items?count_id=" . $count_id);?>" class="btn btn-primary btn-lg">Xuất file excel</a>
				</div>
			</div>
		</div>
	</div>
</div>

<script>
	$(document).ready(function(){
		$('#dTableA').DataTable({
			"sPaginationType": "bootstrap",
			"bFilter": false,
			"bInfo": false,
			"iDisplayStart ": 10,
		    "iDisplayLength": 10,
		    "bLengthChange": false
		});
	});
</script>