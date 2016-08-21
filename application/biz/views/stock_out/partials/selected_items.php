<table class="table" id="stock_out_selected">
    <thead>
      <tr>
        <th></th>
        <th><?php echo lang('common_name'); ?></th>
        <th><?php echo lang('common_quantity'); ?></th>
        <th><?php echo lang('common_measure'); ?></th>
      </tr>
    </thead>
    <tbody>
    	<?php foreach ($items as $item) { ?>
      <tr>
        <td style="text-align: center; cursor: pointer;">
        	<input type="hidden" name="item_id" value="<?php echo $item->item_id; ?>" />
        	<?php if (empty($mode) || $mode != 'by_sale') { ?>
        		<span class="glyphicon glyphicon-remove-sign icon-remove" aria-hidden="true"></span>
        	<?php } ?>
        </td>
        <td><?php echo $item->name; ?></td>
        <td>
        	
        	<?php if (!empty($mode) && $mode == 'by_sale') { ?>
        		<?php echo to_quantity($item->totalQty); ?>
        	<?php } else {?>
        	<a href="#" class="xeditable" data-type="text"  data-validate-number="true"  data-pk="1" data-name="quantity" data-url="<?php echo site_url('stock_out/edit_item/' . $item->item_id); ?>" data-title="<?php echo lang('common_quantity') ?>"><?php echo to_quantity($item->totalQty); ?></a>
        	<?php } ?>
        </td>
        <td>
        	<?php $measure = $this->Measure->getInfo($item->measure_id);?>
        	<?php if (!empty($mode) && $mode == 'by_sale') { ?>
        		<?php echo empty($measure) ? 'Chua thiet lap' : $measure->name; ?>
        	<?php } else {?>
	        	<a class="measure_item <?php echo empty($measure) ? 'editable-disabled' : 'xeditable'; ?>" data-type="select"  data-validate-number="true"  data-value="<?php echo $measure->id; ?>" data-pk="2" data-source="<?php echo site_url("items/measures/" . $item->item_id);?>" data-name="measure" data-url="<?php echo site_url('stock_out/edit_item/' . $item->item_id); ?>" data-title="<?php echo lang('common_measure') ?>"><?php echo empty($measure) ? 'Chua thiet lap' : $measure->name; ?></a>
        	<?php } ?>
        </td>
      </tr>
      <?php } ?>
    </tbody>
  </table>
 <script type="text/javascript" language="javascript">
 $(document).ready(function(){
		$('.xeditable').editable({
	    	validate: function(value) {
	            if ($.isNumeric(value) == '' && $(this).data('validate-number')) {
						return <?php echo json_encode(lang('common_only_numbers_allowed')); ?>;
	            }
	        },
	    	success: function(response, newValue) {
				 console.log(response);
			}
	    });


		$('.measure_item .xeditable').editable({
	    	success: function(response, newValue) {
				 last_focused_id = $(this).attr('id');
				 $("#register_container").html(response);
			}
	    });

	    $('#stock_out_selected .icon-remove').unbind('click').bind('click', function(){

	    	var rowSelected = $(this).closest('tr');
	    	
	    	var _data = {};
			_data['item_id'] = $(rowSelected).find('input[name="item_id"]').val();
			coreAjax.call(
				'<?php echo site_url("stock_out/remove_item");?>',
				_data,
				function(response)
				{
					console.log(response);
					$(rowSelected).remove();
				}
			);
		});
	});
 </script>