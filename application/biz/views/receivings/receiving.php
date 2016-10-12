<a tabindex="-1" href="#" class="dismissfullscreen <?php echo !$fullscreen ? 'hidden' : ''; ?>"><i class="ion-close-circled"></i></a>
	<?php if($this->receiving_lib->get_change_recv_id()) { ?>
		<div class="alert alert-danger">
			<?php echo lang('receivings_editing_recv'); ?> <strong><?php echo 'RECV '.$this->receiving_lib->get_change_recv_id(); ?></strong>
		</div>
	<?php } ?>

<div class="row register">
	<div class="col-lg-8 col-md-7 col-sm-12 col-xs-12 no-padding-right no-padding-left">
		<div class="register-box register-items-form">
			
			<div class="item-form">
				


				<!-- Item adding form -->
				<?php echo form_open("receivings/add",array('id'=>'add_item_form','class'=>'form-inline', 'autocomplete'=> 'off')); ?>
					<div class="input-group input-group-mobile contacts">
						<span class="input-group-addon">
							<?php echo anchor("items/view/-1/1/receiving","<i class='icon ti-pencil-alt'></i> <span class='register-btn-text'>".lang('common_new_item')."</span>", array('class'=>'none add-new-item','title'=>lang('common_new_item'), 'id' => 'new-item-mobile')); ?>
						</span>
						<div class="input-group-addon register-mode <?php echo $mode; ?>-mode dropdown">
							<?php echo anchor("#","<i class='icon ti-shopping-cart'></i><span class='register-btn-text'>".$modes[$mode]."</span>", array('class'=>'none active','title'=>$modes[$mode], 'id' => 'register-mode-mobile', 'data-target' => '#', 'data-toggle' => 'dropdown', 'aria-haspopup' => 'true', 'role' => 'button', 'aria-expanded' => 'false')); ?>
					        <ul class="dropdown-menu sales-dropdown">
					        <?php foreach ($modes as $key => $value) {
					        	if($key!=$mode){
					        ?>
					        	<li><a tabindex="-1" href="#" data-mode="<?php echo $key; ?>" class="change-mode"><?php echo $value;?></a></li>
					        <?php } 
							  } ?>
        					</ul>
						</div>
						
						<span class="input-group-addon grid-buttons">
							<?php echo anchor("#","<i class='icon ti-layout'></i> <span class='register-btn-text'> ".lang('common_show_grid')."</span>", array('class'=>'none show-grid','title'=>lang('common_show_grid'))); ?>
							<?php echo anchor("#","<i class='icon ti-layout'></i> <span class='register-btn-text'> ".lang('common_hide_grid')."</span>", array('class'=>'none hide-grid hidden','title'=>lang('common_hide_grid'))); ?>
						</span>
					</div>

					<div class="input-group contacts  register-input-group">
					<!-- Css Loader  -->
						<div class="spinner" id="ajax-loader" style="display:none">
						  <div class="rect1"></div>
						  <div class="rect2"></div>
						  <div class="rect3"></div>
						</div>
						<span class="input-group-addon">
							<?php echo anchor("items/view/-1/1/receiving","<i class='icon ti-pencil-alt'></i>", array('class'=>'none add-new-item','title'=>lang('common_new_item'), 'id' => 'new-item')); ?>
						</span>
						
						<input type="text" id="item" name="item" class="add-item-input pull-left" placeholder="<?php echo lang('common_start_typing_item_name'); ?>">
						

						<div class="input-group-addon register-mode <?php echo $mode; ?>-mode dropdown">
							<?php echo anchor("#","<i class='icon ti-shopping-cart'></i>".$modes[$mode], array('class'=>'none active','title'=>$modes[$mode], 'id' => 'register-mode', 'data-target' => '#', 'data-toggle' => 'dropdown', 'aria-haspopup' => 'true', 'role' => 'button', 'aria-expanded' => 'false')); ?>
					        <ul class="dropdown-menu sales-dropdown">
					        <?php foreach ($modes as $key => $value) {
					        	if($key!=$mode){
					        ?>
					        	<li><a tabindex="-1" href="#" data-mode="<?php echo $key; ?>" class="change-mode"><?php echo $value;?></a></li>
					        <?php } 
							  } ?>
        					</ul>
						</div>
						
						<span class="input-group-addon grid-buttons">
							<?php echo anchor("#","<i class='icon ti-layout'></i> ".lang('common_show_grid'), array('class'=>'none show-grid','title'=>lang('common_show_grid'))); ?>
							<?php echo anchor("#","<i class='icon ti-layout'></i> ".lang('common_hide_grid'), array('class'=>'none hide-grid hidden','title'=>lang('common_hide_grid'))); ?>
						</span>
					</div>
				</form>
			</div>

		</div>
		<!-- /.Item Form -->

			<!-- Register Items. @contains : Items table -->
		<div class="register-box register-items paper-cut">
			<div class="register-items-holder">
				
					
					<table id="register" class="table table-hover">

						<thead>
							<tr class="register-items-header">
								<th></th>
								<th class="item_name_heading"><?php echo lang('receivings_item_name'); ?></th>
								<th class="sales_price"><?php echo lang('receivings_cost'); ?></th>
								<th class="sales_quantity"><?php echo lang('common_quantity'); ?></th>
								
								<th class="sales_measure"><?php echo lang('common_measure'); ?></th>
								
								<th class="sales_discount"><?php echo lang('receivings_discount'); ?></th>
								<th><?php echo lang('receivings_total'); ?></th>								
							</tr>
						</thead>

				
					<tbody class="register-item-content">
						<?php
						$cart_count = 0;
						if(count($cart)==0)	{ ?>
						<tr class="cart_content_area">
							<td colspan='7'>
								<div class='text-center text-warning' > <h3><?php echo lang('common_no_items_in_cart'); ?> <span class="flatBluec"> [<?php echo lang('module_receivings') ?>]</span></h3></div>
							</td>
						</tr>
						<?php 
						}
						else
						{
						 foreach(array_reverse($cart, true) as $line=>$item) { 
						 	$cart_count = $cart_count + $item['quantity'];
							?>
							<tr class="register-item-details">
								<td class="text-center"> <?php echo anchor("receivings/delete_item/$line",'<i class="icon ion-android-cancel"></i>', array('class' => 'delete-item'));?> </td>
								<td> 
									<a tabindex = "-1" href="<?php echo isset($item['item_id']) ? site_url('home/view_item_modal/'.$item['item_id']) : site_url('home/view_item_kit_modal/'.$item['item_kit_id']) ; ?>" data-toggle="modal" data-target="#myModal" class="register-item-name" ><?php echo H($item['name']); ?><?php echo $item['size'] ? ' ('.H($item['size']).')': ''; ?></a>
								</td>
								
								
								<td class="text-center">
									<?php if ($items_module_allowed) { ?>
											<a href="#" id="price_<?php echo $line;?>" class="xeditable" data-validate-number="true" data-type="text" data-value="<?php echo to_currency_no_money($item['price']); ?>" data-pk="1" data-name="price" data-url="<?php echo site_url('receivings/edit_item/'.$line); ?>" data-title="<?php echo H(lang('common_price')); ?>"><?php echo to_currency($item['price']); ?></a>
									<?php } else { 
											echo to_currency($item['price']); 
									 }	?>
								</td>

								<td class="text-center">
										<a href="#" id="quantity_<?php echo $line; ?>" class="xeditable" data-type="text"  data-validate-number="true"  data-value="<?php echo H(to_quantity($item['quantity'])); ?>" data-pk="1" data-name="quantity" data-url="<?php echo site_url('receivings/edit_item/'.$line); ?>" data-title="<?php echo lang('common_quantity') ?>"><?php echo to_quantity($item['quantity']); ?></a>
								</td>
								
								<td class="text-center">
										<a id="measure_<?php echo $line; ?>" class="measure_item <?php echo empty($item['measure_id']) ? 'editable-disabled' : 'xeditable'; ?>" data-type="select"  data-validate-number="true"  data-value="<?php echo $item['measure_id']; ?>" data-pk="2" data-source="<?php echo site_url("items/measures/" . $item['item_id']);?>" data-name="measure" data-url="<?php echo site_url('receivings/edit_item/'.$line); ?>" data-title="<?php echo lang('common_measure') ?>"><?php echo $item['measure']; ?></a>
								</td>
																
								<td class="text-center">
									<a href="#" id="discount_<?php echo $line; ?>" class="xeditable" data-type="text"  data-validate-number="true"  data-pk="1" data-name="discount" data-value="<?php echo H($item['discount']) ?>" data-url="<?php echo site_url('receivings/edit_item/'.$line); ?>" data-title="<?php echo lang('common_discount_percent') ?>"><?php echo to_quantity($item['discount']); ?>%</a>						
								</td>
								<td class="text-center"><?php echo to_currency($item['price']*$item['quantity']-$item['price']*$item['quantity']*$item['discount']/100, 10); ?></td>
							</tr>
							<tr class="register-item-bottom">
								<td>&nbsp;</td>
								<td colspan="6">
									<dl class="register-item-extra-details dl-horizontal">
										
										<?php if ($this->receiving_lib->get_suspended_receiving_id()) {?>
											<dt><?php echo lang('common_qty_received'); ?></dt>
											<dd><a href="#" id="quantity_received_<?php echo $line;?>" class="xeditable" data-type="text" data-validate-number="true" data-pk="1" data-name="quantity_received" data-value="<?php echo H(to_quantity($item['quantity_received'])); ?>" data-url="<?php echo site_url('receivings/edit_item/'.$line); ?>" data-title="<?php echo H(lang('common_qty_received')); ?>"><?php echo H(to_quantity($item['quantity_received']));?></a></dd>
										<?php } ?>
										<dt><?php echo lang('common_stock'); ?></dt>
										<dd><?php echo to_quantity($item['cur_quantity']); ?></dd>
										
										<?php
										if ($this->config->item('calculate_average_cost_price_from_receivings'))
										{
										?>
											<dt><?php echo lang('receivings_cost_price_preview'); ?></dt>
											<dd><?php echo $item['cost_price_preview']; ?></dd>
										<?php
										}
										?>
										
										
									  <dt><?php echo lang('common_description'); ?></dt>
									  <dd>
										  <?php
											if ($item['description']!='')
											{
												echo $item['description'];
											}
											else
											{
												echo lang('common_none');
											}
											
										?>
									</dd>
									
										<?php if ($item['expire_date']) {?>
										  <dt><?php echo lang('common_expire_date'); ?></dt>
										  <dd><a href="#" id="expire_date_<?php echo $line;?>" class="expire_date" data-type="combodate" data-template="<?php echo get_js_date_format(); ?>" data-pk="1" data-name="expire_date" data-value="<?php echo date('Y-m-d', strtotime($item['expire_date'])); ?>" data-url="<?php echo site_url('receivings/edit_item/'.$line); ?>" data-title="<?php echo H(lang('common_expire_date')); ?>"><?php echo H($item['expire_date']);?></a></dd>
										 <?php } ?>
										<dt class="visible-lg">
										<?php 
										switch($this->config->item('id_to_show_on_sale_interface'))
										{
											case 'number':
											echo lang('common_item_number_expanded'); 
											break;
						
											case 'product_id':
											echo lang('common_product_id'); 
											break;
						
											case 'id':
											echo lang('common_item_id'); 
											break;
						
											default:
											echo lang('common_item_number_expanded'); 
											break;
										}
										?>
										</dt>
										<dd class="visible-lg">
										<?php 
										switch($this->config->item('id_to_show_on_sale_interface'))
										{
											case 'number':
											echo array_key_exists('item_number', $item) ? H($item['item_number']) : lang('common_none'); 
											break;
				
											case 'product_id':
											echo array_key_exists('product_id', $item) ? H($item['product_id']) : lang('common_none'); 
											break;
				
											case 'id':
											echo array_key_exists('item_id', $item) ? H($item['item_id']) : lang('common_none'); 
											break;
						
											default:
											echo array_key_exists('item_number', $item) ? H($item['item_number']) : lang('common_none'); 
											break;
										}
										?>
									</dd>	 
										 
									</dl>
								</td>
							</tr>
						<?php } }  ?>
						</tbody>
					</table>
			</div>
		

		</div>
		<!-- /.Register Items -->

	</div>

	<div class="col-lg-4 col-md-5 col-sm-12 col-xs-12">
		<div class="register-box register-right">
			

	<!-- Receive  Top Buttons  -->
			<div class="sale-buttons">
				<!-- Extra links -->
				<div class="btn-group">
					<button type="button" class="btn btn-more dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
						<i class="ion-android-more-horizontal"></i>
					</button>
					<ul class="dropdown-menu sales-dropdown" role="menu">
						
						
						<li>
							<?php echo anchor("receivings/suspended/",
								'<i class="ion-ios-list-outline"></i> '.lang('common_suspended_receivings'). ' '.lang('common_and').' <br /> '.lang('receivings_purchase_orders'),
								array('class'=>'none suspended_sales_btn','title'=>lang('common_suspended_receivings')));
							?>
						</li>
						
						<li>
							<?php echo anchor("receivings/po/",
								'<i class="ion-ios-paper"></i> '.lang('receivings_create_purchase_order'),
								array('class'=>'none suspended_sales_btn','title'=>lang('receivings_create_purchase_order')));
							?>
						</li>
					
						
						<li>
							<?php echo anchor("receivings/batch_receiving/",
								'<i class="ion-bag"></i> '.lang('batch_receivings'),
								array('class'=>'none suspended_sales_btn','title'=>lang('batch_receivings')));
							?>
						</li>
					</ul>
				</div>
				<?php if(count($cart) > 0){ ?>
				<?php echo form_open("receivings/cancel_receiving",array('id'=>'cancel_sale_form', 'autocomplete'=> 'off')); ?>
				
				<?php
					if (!$this->receiving_lib->get_change_recv_id()) {?>
						<a href="" class="btn btn-suspended"  id="suspend_recv_button" >
							<i class="ion-pause"></i>
							<?php echo lang('receivings_suspend_recv');?>
						</a>
				<?php } ?>
				<a href="" class="btn btn-cancel"  id="cancel_sale_button" >
					<i class="ion-close-circled"></i>
					<?php echo lang('receivings_cancel_receiving');?>
				</a>
			</form>
			<?php } ?>

		</div>
		<!-- /.End of receive Buttons -->

		<?php if($mode=="transfer") { ?>

			<?php if(isset($location)) {  ?>
				<!-- Customer Badge when customer is added -->
				<div class="customer-badge location">
					<div class="details">

						<a tabindex="-1" href="<?php echo site_url("locations/view/$location_id/1"); ?>" class="name">
						<span class="badge" style="background-color:<?php echo $location_color; ?>">&nbsp;</span>
							<?php echo character_limiter(H($location), 30); ?> 
						</a>
						
					</div>

				</div>
				<div class="customer-action-buttons">
					<a tabindex="-1" href="<?php echo site_url("locations/view/$location_id/1"); ?>" class="btn success">
						<i class="ion-ios-compose-outline"></i>
						Edit		
					</a>
					<?php echo ''.anchor("receivings/delete_location", '<i class="ion-close-circled"></i> '.lang('common_detach'),array('id' => 'delete_location','class'=>'btn')); ?>

				</div>
			<?php }
			else {  ?>

			<div class="customer-form">

				<!-- if the location is not set , show location adding form -->
				<?php echo form_open("receivings/select_location",array('id'=>'select_location_form', 'autocomplete'=> 'off')); ?>
					<div class="input-group contacts">
						<span class="input-group-addon">
							<?php echo anchor("locations/view/-1","<i class='ion-plus'></i>", array('class'=>'none','title'=>lang('common_new_customer'), 'id' => 'new-customer')); ?>
						</span>
						<input type="text" id="location" name="location" class="add-customer-input" placeholder="<?php echo lang('receivings_start_typing_location_name'); ?>" />

					</div>
				</form>

			</div> 
				

			<?php }  ?>


		<?php }  else {  ?>
			<?php if(isset($supplier)) {  ?>
				<!-- Customer Badge when customer is added -->
				<div class="customer-badge">
					<div class="avatar">
						<img src="<?php echo $avatar; ?>" alt="">	
					</div>
					<div class="details">
						<a tabindex="-1" href="<?php echo site_url("suppliers/view/$supplier_id/1"); ?>" class="name">
							<?php echo character_limiter(H($supplier), 30); ?> 
							<?php if ($this->config->item('suppliers_store_accounts') && isset($supplier_balance)) {?>
							<span class="<?php echo $is_over_credit_limit ? 'credit_limit_warning' : 'credit_limit_ok'; ?> balance">(<?php echo to_currency($supplier_balance); ?>)</span>
							<?php } ?>
						</a>

						<!-- supplier Email  -->
						<?php if(!empty($supplier_email)) { ?>
						<span class="email">
							<?php echo character_limiter(H($supplier_email), 25); ?>
						</span>
						<?php } ?>

						<!-- supplier edit -->
						<?php echo anchor("suppliers/view/$supplier_id/1", '<i class="ion-ios-compose-outline"></i>',  array('id' => 'edit_supplier','class'=>'btn btn-edit btn-primary pull-right','title'=>lang('receivings_update_supplier'))).''; ?>
						
					</div>

				</div>
				
				
				<div class="customer-action-buttons">
		            <?php if(!empty($supplier_email)) { ?>
		                <a href="#" class="btn <?php echo (boolean)$email_receipt ? 'email-checked' : '';?>" id="toggle_email_receipt">
		                    <i class="ion-android-mail"></i>
		                    <?php echo $is_po ? lang('receivings_email_po') : lang('common_email_receipt'); ?>?
		                </a>
		            <?php } else { ?>
		                <a href="<?php echo site_url('suppliers/view/'.$supplier_id.'/1');  ?>" class="btn" >
		                    <i class="ion-ios-compose-outline"></i>
		                    <?php echo lang('receivings_update_supplier'); ?>
		                </a>
            
		            <?php } ?>
					

					<?php
						echo form_checkbox(array(
								'name'=>'email_receipt',
								'id'=>'email_receipt',
								'value'=>'1',
								'class'       => 'email_receipt_checkbox hidden',
								'checked'=>(boolean)$email_receipt)
							);
		
							?>
				
			
					<?php echo ''.anchor("receivings/delete_supplier", '<i class="ion-close-circled"></i> '.lang('common_detach'),array('id' => 'delete_supplier','class'=>'btn')); ?>
				</div>
			<?php }
			else {  ?>

			<div class="customer-form">

				<!-- if the supplier is not set , show supplier adding form -->
				<?php echo form_open("receivings/select_supplier",array('id'=>'select_supplier_form', 'autocomplete'=> 'off')); ?>
					<div class="input-group contacts">
						<span class="input-group-addon">
							<?php echo anchor("suppliers/view/-1/1","<i class='ion-plus'></i>", array('class'=>'none','title'=>lang('receivings_new_supplier'), 'id' => 'new-customer')); ?>
						</span>
						<input type="text" id="supplier" name="supplier" class="add-customer-input" placeholder="<?php echo lang('receivings_start_typing_supplier_name'); ?>" /> 

					</div>
				</form>

			</div> 
				
				
			<?php }  ?>

		
		<?php } ?>
	</div>


<div class="register-box">
		<div style="margin: 5px 15px;">
			<p><?php echo lang('common_total_items'); ?>: <span id="sale_total_items"><?php echo $total_items; ?></span></p>
			<p><?php echo lang('common_total_qty'); ?>: <span id="sale_total_qty"><?php echo $total_qty; ?></span></p>
		</div>
	</div>

		<!-- Summary -->
		<div class="register-box register-summary paper-cut">

		
		<ul class="list-group">

		<li class="sub-total list-group-item">
			<span class="key"><?php echo lang('common_sub_total'); ?>:</span>
			<span class="value"><?php echo to_currency($subtotal); ?></span>
		</li>
		
		<?php foreach($taxes as $name=>$value) { ?>
		<li class="list-group-item">
			<span class="key">
				<?php if ($this->Employee->has_module_action_permission('receivings', 'delete_taxes', $this->Employee->get_logged_in_employee_info()->person_id)){ ?>
					<?php echo anchor("receivings/delete_tax/".rawurlencode($name),'<i class="icon ion-android-cancel"></i>', array('class' => 'delete-tax remove'));?>

				<?php } ?>
				<?php echo $name; ?>:</td>
			</span>
			<span class="value pull-right">
				<?php echo to_currency($value); ?>
			</span>
		<?php }; ?>
		</ul>

		<div class="amount-block">

			<div class="total amount-due">
				<div class="side-heading">
					<?php echo lang('common_items_in_cart'); ?>
				</div>
				<div class="amount" >
					<?php if($mode=="transfer") { echo $items_in_cart * -1; } else { echo $items_in_cart; } ?>
				</div>
			</div>
			<div class="total amount">
				<div class="side-heading">
					<?php echo lang('common_total'); ?>
				</div>
				<div class="amount total-amount" >
					<?php echo to_currency($total); ?>
				</div>
			</div>
		</div>
		<!-- ./amount block -->

		
		

			<?php
				// Only show this part if there are Items already in the Table.
				if(count($cart) > 0){ 

				if($mode!="transfer") 
				{
				?>
				<div class="add-payment">
					<div class="side-heading"><?php echo lang('common_add_payment'); ?></div>
					
					<?php
						if (!$selected_payment)
						{
	  						 $selected_payment = $this->config->item('default_payment_type') ? $this->config->item('default_payment_type') : lang('common_cash');
						}
						?>
					
						<?php foreach ($payment_options as $key => $value) { 
							$active_payment =  ($selected_payment == $value) ? "active" : "";
						?>
							<a tabindex="-1" href="#" class="btn btn-pay select-payment <?php echo $active_payment; ?>" data-payment="<?php echo H($value); ?>">
							<?php echo $value; ?>
						</a>
						<?php 
						} 
						?>
						
				</div>
				<?php
				}
				?>
					<div class="change-date">
				<?php
				echo form_checkbox(array(
					'name'=>'change_receiving_date_enable',
					'id'=>'change_receiving_date_enable',
					'value'=>'1',
					'checked'=>(boolean)$change_recv_date_enable)
				);
				echo '<label for="change_receiving_date_enable"><span></span>'.lang('receivings_change_recv_date').'</label>';
				?>
				<div id="change_receiving_date_picker" class="input-group date datepicker" >
					<span class="input-group-addon"><i class="ion-calendar"></i></span>

				<?php echo form_input(array(
					'name'=>'change_receiving_date',
					'id' => 'change_receiving_date',
					'size'=>'8',
					'class' => 'form-control',
					'value'=> date(get_date_format().' '.get_time_format(), $change_receiving_date ? strtotime($change_receiving_date) : time()),
					)
				);?>       
			</div>
			
					<div id="finish_sale" class="receivings-finish-sale">
						<?php echo form_open("receivings/".(!$is_po ? 'complete' : 'suspend'),array('id'=>'finish_sale_form', 'autocomplete'=> 'off')); ?>

						<div class="input-group add-payment-form">
							
							<?php if($mode!="transfer") { ?>
								<?php echo form_dropdown('payment_type',$payment_options, $selected_payment,'class="input-medium hidden" id="payment_type"');?>
								<?php echo form_input(array('name'=>'amount_tendered','id'=>'amount_tendered','value'=>'','class'=>'add-input ', 'accesskey' => 'p'));	?>
							<?php } ?>
							
							<?php if ($mode!= 'transfer' || ($mode=='transfer' && isset($location_id))) { ?>
								<span class="input-group-addon">
									<a href="#" id="finish_sale_button"><?php echo !$is_po ? lang('receivings_complete_receiving') : lang('receivings_suspend_and_complete_po'); ?></a>
								</span>
							<?php } ?>
						</div>
				</div>
				<div class="comment-block">
					<div class="side-heading"><label id="comment_label" for="comment"><?php echo lang('common_comments'); ?> : </label></div>
					<?php echo form_textarea(array('name'=>'comment', 'id' => 'comment', 'value'=>$comment,'rows'=>'2', 'class'=>'form-control')); ?>
				</div>
				
			</div>
			</div>
					</div>
				</form>
				<?php } ?>
			</div>
		<!-- /.Summary -->
		</div>
	</div>
</div>

<script type="text/javascript">
	<?php
	if(isset($error))
	{
		echo "show_feedback('error', ".json_encode($error).", ".json_encode(lang('common_error')).");";
	}

	if (isset($warning))
	{
		echo "show_feedback('warning', ".json_encode($warning).", ".json_encode(lang('common_warning')).");";
	}

	if (isset($success))
	{
		echo "show_feedback('success', ".json_encode($success).", ".json_encode(lang('common_success')).");";
	}
	?>
</script>


<script type="text/javascript" language="javascript">
var submitting = false;
$(document).ready(function()
{
	
	$('#toggle_email_receipt').on('click',function(e) {
		e.preventDefault();
        var checkBoxes = $("#email_receipt");
        checkBoxes.prop("checked", !checkBoxes.prop("checked")).trigger("change");
        $(this).toggleClass('email-checked');

	})

	$('#email_receipt').change(function(e) 
	{	
		e.preventDefault();
		$.post('<?php echo site_url("receivings/set_email_receipt");?>', {email_receipt: $('#email_receipt').is(':checked') ? '1' : '0'});
	});
	
	
	$('#change_receiving_date_enable').is(':checked') ? $("#change_receiving_date_picker").show() : $("#change_receiving_date_picker").hide(); 

	$('#change_receiving_date_enable').click(function() {
		if( $(this).is(':checked')) {
			$("#change_receiving_date_picker").show();
		} else {
			$("#change_receiving_date_picker").hide();
		}
	});
	
	date_time_picker_field($("#change_receiving_date"), JS_DATE_FORMAT + " "+ JS_TIME_FORMAT);
	
   $("#change_receiving_date").on("dp.change", function(e) {
		$.post('<?php echo site_url("receivings/set_change_receiving_date");?>', {change_receiving_date: $('#change_receiving_date').val()});			
   });
	
	//Input change
	$("#change_receiving_date").change(function(){
		$.post('<?php echo site_url("receivings/set_change_receiving_date");?>', {change_receiving_date: $('#change_receiving_date').val()});			
	});

	$('#change_receiving_date_enable').change(function() 
	{
		$.post('<?php echo site_url("receivings/set_change_receiving_date_enable");?>', {change_receiving_date_enable: $('#change_receiving_date_enable').is(':checked') ? '1' : '0'});
	});

	//Here just in case the loader doesn't go away for some reason
	$("#ajax-loader").hide();
	
	<?php if (!$this->agent->is_mobile()) { ?>
		<?php if (!$this->config->item('auto_focus_on_item_after_sale_and_receiving'))
		{
		?>
			if (last_focused_id && last_focused_id != 'item')
			{
				$('#'+last_focused_id).focus();
				$('#'+last_focused_id).select();
			}
			<?php 
		}
		else
		{
		?>
			setTimeout(function(){$('#item').focus();}, 10);	
		<?php
		}
		?>
	
		$(document).focusin(function(event) 
		{
			last_focused_id = $(event.target).attr('id');
		});
	<?php }
	else
	{
		if ($this->config->item('wireless_scanner_support_focus_on_item_field'))
		{
		?>
			setTimeout(function(){$('#item').focus();}, 10);				
		<?php
		}
	} ?>
	
		$('#add_item_form').ajaxForm({target: "#register_container", beforeSubmit: receivingsBeforeSubmit, success: itemScannedSuccess});
		$('#select_supplier_form,#select_location_form').ajaxForm({target: "#register_container", beforeSubmit: receivingsBeforeSubmit});

			$( "#item" ).autocomplete({
		 		source: '<?php echo site_url("receivings/item_search");?>',
				delay: 150,
		 		autoFocus: false,
		 		minLength: 0,
		 		select: function( event, ui ) 
		 		{
					$( "#item" ).val(ui.item.value);
		 			
		 			$('#add_item_form').ajaxSubmit({target: "#register_container", beforeSubmit: receivingsBeforeSubmit, success: itemScannedSuccess});
		 		},
			}).data("ui-autocomplete")._renderItem = function (ul, item) {
		         return $("<li class='item-suggestions'></li>")
		             .data("item.autocomplete", item)
			           .append('<a class="suggest-item"><div class="item-image">' +
									'<img src="' + item.image + '" alt="">' +
								'</div>' +
								'<div class="details">' +
									'<div class="name">' + 
										item.label +
									'</div>' +
									'<span class="attributes">' +
										'<?php echo lang("common_category"); ?>' + ' : <span class="value">' + (item.category ? item.category : <?php echo json_encode(lang('common_none')); ?>) + '</span>' +
									'</span>' +
								'</div>')
		             .appendTo(ul);
		     };		
		// if #mode is changed
			  //TODO: Remove this code as we don't have store account payments in recv
		$('.change-mode').click(function(e){
			e.preventDefault();
			if ($(this).data('mode') == "store_account_payment") { // Hiding the category grid
				$('#show_hide_grid_wrapper, #category_item_selection_wrapper').fadeOut();
			}else { // otherwise, show the categories grid
				$('#show_hide_grid_wrapper, #show_grid').fadeIn();
				$('#hide_grid').fadeOut();
			}
			$.post('<?php echo site_url("receivings/change_mode");?>', {mode: $(this).data('mode')}, function(response)
			{
				$("#register_container").html(response);
			});
		});


    //make username editable
    $('.xeditable').editable({
    	validate: function(value) {
            if ($.isNumeric(value) == '' && $(this).data('validate-number')) {
                return <?php echo json_encode(lang('common_only_numbers_allowed')); ?>;
            }
        },
    	success: function(response, newValue) {
			 last_focused_id = $(this).attr('id');
 			 $("#register_container").html(response);
		}
    });

    $('.measure_item .xeditable').editable({
    	success: function(response, newValue) {
			 last_focused_id = $(this).attr('id');
			 $("#register_container").html(response);
		}
    });
	 
	 $(".expire_date").editable({
     	validate: function(value) {
          if (!value) 
			 {
             return <?php echo json_encode(lang('receivings_invalid_date')); ?>;
          }
       },
		 combodate: {
			 maxYear: <?php echo date("Y", strtotime('+3 years'));?>,
			 minYear: <?php echo date("Y");?>,
		 },
     	success: function(response, newValue) {
 			 last_focused_id = $(this).attr('id');
  			 $("#register_container").html(response);
 		}
	 });

    $('.xeditable').on('shown', function(e, editable) {
    	editable.input.postrender = function() {
			//Set timeout needed when calling price_to_change.editable('show') (Not sure why)
			setTimeout(function() {
         editable.input.$input.select();
		}, 50);
    };
	});
	
	 $('.xeditable').on('hidden', function(e, editable) {
		 last_focused_id = $(this).attr('id');
	 	$('#'+last_focused_id).focus();
	 	$('#'+last_focused_id).select();
 	});
	
	<?php if (isset($cart_count)) { ?>
      	$('.cart-number').html(<?php echo $cart_count; ?>);
	<?php } ?>

	// Location form 
		$('#locatio').selectize({
			valueField: 'value',
			labelField: 'label',
			searchField: 'label',
			options: [],
			create: false,
			render: {
				option: function(item, escape) {
					return '<div class="customer-badge suggestions">' +
								'<div class="avatar">' +
									'<span class="badge" style="background-color:' + escape(item.color) + '">&nbsp;</span>' +
								'</div>' +
								'<div class="details">' +
									'<a href="#" class="name">' +
										escape(item.label) +
									'</a>' +
								'</div>' +
							'</div>';
				}
			},

			load: function(query, callback) {
				if (!query.length) return callback();
				$.ajax({
					url:'<?php echo site_url("receivings/location_search");?>'+'?term='+encodeURIComponent(query),
					type: 'GET',
					error: function() {
						callback();
					},
					success: function(res) {
						res = $.parseJSON(res);
						callback(res);
					}
				});
			}
		});

		$('#location').change(function(){
			$('#select_location_form').ajaxSubmit({target: "#register_container", beforeSubmit: receivingsBeforeSubmit});
		});

		// Select Location 
		<?php if($mode=="transfer" and !isset($location)) { ?>
		

			$( "#location" ).autocomplete({
		 		source: '<?php echo site_url("receivings/location_search");?>',
				delay: 150,
		 		autoFocus: false,
		 		minLength: 0,
		 		select: function( event, ui ) 
		 		{
		 			$.post('<?php echo site_url("receivings/select_location");?>', {location: ui.item.value }, function(response)
					{
						$("#register_container").html(response);
					});	
		 		},
			}).data("ui-autocomplete")._renderItem = function (ul, item) {
		         return $("<li class='customer-badge suggestions'></li>")
		             .data("item.autocomplete", item)
			         .append('<a class="suggest-item location-suggest"><div class="avatar">' +
									'<span class="badge" style="background-color:' + item.color + '">&nbsp;</span>' +
								'</div>' +
								'<div class="details">' +
									'<div class="name">' + 
										item.label +
									'</div>' + 
								'</div></a>')
		             .appendTo(ul);

		     };
	     <?php } ?>


		// Select Supplier 
		<?php if($mode!="transfer" and !isset($supplier)) { ?>

			$( "#supplier" ).autocomplete({
		 		source: '<?php echo site_url("receivings/supplier_search");?>',
				delay: 150,
		 		autoFocus: false,
		 		minLength: 0,
		 		select: function( event, ui ) 
		 		{
		 			$.post('<?php echo site_url("receivings/select_supplier");?>', {supplier: ui.item.value }, function(response)
					{
						$("#register_container").html(response);
					});	
		 		},
			}).data("ui-autocomplete")._renderItem = function (ul, item) {
		         return $("<li class='customer-badge suggestions'></li>")
		             .data("item.autocomplete", item)
			           .append('<a class="suggest-item"><div class="avatar">' +
									'<img src="' + item.avatar + '" alt="">' +
								'</div>' +
								'<div class="details">' +
									'<div class="name">' + 
										item.label +
									'</div>' + 
									'<span class="email">' +
										item.subtitle + 
									'</span>' +
								'</div></a>')
		             .appendTo(ul);

		     };
	     <?php } ?>


//Select Payment
		$('.select-payment').on('click',function(e){
			e.preventDefault();
			$.post('<?php echo site_url("receivings/set_selected_payment");?>', {payment: $(this).data('payment')});
			$('#payment_type').val($(this).data('payment'));
			// start_cc_processing
			$('.select-payment').removeClass('active');
			$(this).addClass('active');
			$("#amount_tendered").focus();
			$("#amount_tendered").select();
		})

		//Add payment to the sale 
		$("#add_payment_button").click(function(e)
		{
			e.preventDefault();

			$('#add_payment_form').ajaxSubmit({target: "#register_container", beforeSubmit: salesBeforeSubmit});
		});

// Show or hide item grid
		$("#show_grid, .show-grid").on('click',function(e)
		{
			e.preventDefault();
			$("#category_item_selection_wrapper").slideDown();

			$('.show-grid').addClass('hidden');
			$('.hide-grid').removeClass('hidden');
		});

		$("#hide_grid,#hide_grid_top, .hide-grid").on('click',function(e)
		{
			e.preventDefault();
			$("#category_item_selection_wrapper").slideUp();

			$('.hide-grid').addClass('hidden');
			$('.show-grid').removeClass('hidden');
		});
	

	$("#cart_contents input").change(function()
	{
		$(this.form).ajaxSubmit({target: "#register_container",beforeSubmit: receivingsBeforeSubmit});
	});
	
	$('#item,#supplier,#location').click(function()
    {
    	$(this).attr('value','');
    });

	$('#mode').change(function()
	{
		$('#mode_form').ajaxSubmit({target: "#register_container", beforeSubmit: receivingsBeforeSubmit});
	});
	
	$('#comment').change(function() 
	{
		$.post('<?php echo site_url("receivings/set_comment");?>', {comment: $('#comment').val()});
	});



	<?php if (!$is_po) { ?>
	    $("#finish_sale_form").submit(function()
		{
			<?php if($mode=="transfer" and !isset($location)) { ?>
				bootbox.alert(<?php echo json_encode(lang("receivings_location_required")); ?>);
				$('#location').focus();
				return;
				<?php } ?>
			
				var finishForm = this;
				
				bootbox.confirm(<?php echo json_encode(lang("receivings_confirm_finish_receiving")); ?>, function(result)
	    		{
					if (result)
					{
						//Prevent double submission of form
						$("#finish_sale_button").hide();
						finishForm.submit();
					}
				});
				return false;
		});
	    $("#finish_sale_button").click(function(e)
	    {
	    	e.preventDefault();
			$('#finish_sale_form').submit();
		});
	<?php } ?>
    $("#cancel_sale_button").click(function(e)
    {
     	e.preventDefault();		 
    	bootbox.confirm(<?php echo json_encode(lang("receivings_confirm_cancel_receiving")); ?>, function(result)
    	{
			if (result)
			{
				$('#cancel_sale_form').ajaxSubmit({target: "#register_container", beforeSubmit: receivingsBeforeSubmit});
			}
		});
    });

	$('.delete-item, #delete_supplier, #delete_location,.delete-tax').click(function(event)
	{
		event.preventDefault();
		$("#register_container").load($(this).attr('href'));	
	});

	$("input[type=text]").click(function() {
		$(this).select();
	});
		
	$("#suspend_recv_button<?php echo $is_po ? ', #finish_sale_button': '';?>").click(function(e)
	{
		e.preventDefault();
		bootbox.confirm(<?php echo json_encode(lang("receivings_confim_suspend_recv")); ?>, function(result)
		{
			if (result)
			{
				if ($("#comment").val())
				{
					$.post('<?php echo site_url("receivings/set_comment");?>', {comment: $('#comment').val()}, function()
					{
						doSuspendRecv();
					});						
				}
				else
				{
					doSuspendRecv();	
				}
			}
		});
	});

	$('.fullscreen').on('click',function (e) {
		e.preventDefault();
		salesRecvFullScreen();
		$.get('<?php echo site_url("home/set_fullscreen/1");?>');
	});

	$('.dismissfullscreen').on('click',function (e) {
		e.preventDefault();
		salesRecvDismissFullscren();
		$.get('<?php echo site_url("home/set_fullscreen/0");?>');
	});
});

function doSuspendRecv()
{
	<?php if (!$is_po) { ?>
		<?php if ($this->config->item('show_receipt_after_suspending_sale')) { ?>
			window.location = '<?php echo site_url("receivings/suspend"); ?>';
		<?php }else { ?>
			$("#register_container").load('<?php echo site_url("receivings/suspend"); ?>');
		<?php } ?>
		<?php 
		}
		else
		{
		?>
		window.location = '<?php echo site_url("receivings/suspend"); ?>';			
		<?php	
		} 
		?>

}
function receivingsBeforeSubmit(formData, jqForm, options)
{
	if (submitting)
	{
		return false;
	}
	$('.cart-number').html(<?php echo $cart_count; ?>);
	submitting = true;
	
	$("#ajax-loader").show();
	$("#finish_sale_button").hide();
}

function itemScannedSuccess(responseText, statusText, xhr, $form)
{
	setTimeout(function(){$('#item').focus();}, 10);
}
</script>