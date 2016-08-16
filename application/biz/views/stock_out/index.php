<?php $this->load->view("partial/header");?>
 <div id="sales_page_holder">
 	<div id="register_container" class="sales clearfix">
 		<form action="<?php echo site_url('stock_out/finish'); ?>" method="post">
		 <div class="row register">
			<div class="col-lg-8 col-md-7 col-sm-12 col-xs-12 no-padding-right no-padding-left">
				<div class="register-box register-items-form">
					<div class="item-form">
					
						<?php $cart_count = 0; ?>
						<?php echo form_open("stock_id/add",array('id'=>'add_item_form','class'=>'form-inline', 'autocomplete'=> 'off')); ?>
							<div class="input-group contacts register-input-group">
		
								<!-- Css Loader  -->
								<div class="spinner" id="ajax-loader" style="display:none">
								  <div class="rect1"></div>
								  <div class="rect2"></div>
								  <div class="rect3"></div>
								</div>
								
								<input type="text" id="item" name="item" class="add-item-input" style="border-left-width: 1px;" placeholder="<?php echo lang('common_start_typing_item_name'); ?>">
				
								<div class="input-group-addon register-mode <?php echo $stock_out_data['mode']; ?>-mode dropdown">
									<?php 
									
									$text = 'Trực Tiếp';
									
									if ($stock_out_data['mode'] == 'by_sale') {
										$text = 'Đơn Hàng';
									}
									
									echo anchor("","<i class='icon ti-share-alt'></i> <span>" . $text . "</span>", array('class'=>'none active','tabindex'=>'-1','title'=> 'AAAA', 'id' => 'select-mode-2', 'data-target' => '#', 'data-toggle' => 'dropdown', 'aria-haspopup' => 'true', 'role' => 'button', 'aria-expanded' => 'false')); ?>
							        <ul class="dropdown-menu sales-dropdown">
							        	<li><a id="free_style" tabindex="-1" data-mode="A" class="change-mode">Xuất kho trực tiếp</a></li>
							        	<li><a id="by_sale" tabindex="-1" data-mode="B" class="change-mode">Xuất kho theo đơn hàng</a></li>
		        					</ul>
								</div>
							</div>
						</form>
					
					</div>
				</div>
				
				<div class="register-box register-items paper-cut">
					<div id="block_selected_items">
						<?php echo $selected_tml; ?>
					</div>
					<div style="padding: 0 20px;">
						<p>Ghi Chú:</p>
						<textarea style="width: 100%" rows="5" name="comment"></textarea>
					</div>
				</div>
			</div>
			
			<div class="col-lg-4 col-md-5 col-sm-12 col-xs-12 no-padding-right">
				<div class="register-box register-right">
					<div class="sale-buttons">
						<div class="btn-group">
							
							<button type="button" class="btn btn-more dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
								<i class="ion-android-more-horizontal"></i>
							</button>
							
							<ul class="dropdown-menu sales-dropdown" role="menu">
								<li>
									<?php echo '<a href="'. site_url("stock_out/history") .'" class="look-up-receipt" data-toggle="modal"><i class="ion-document"></i> '. lang('common_history_stock_out') .'</a>';?>
								</li>
								<li>
									<?php echo anchor("items/history_transfer",
										'<i class="ion-document"></i> <span class="">'.lang("common_history_transfer").'</span>',
										array('class'=>'',
										'title'=>lang('common_history_transfer')));
									?>
								</li>
							</ul>
							
							<a href="<?php echo site_url("stock_out/cancel"); ?>" class="btn btn-cancel" id="cancel_sale_button">
								<i class="ion-close-circled"></i>Hủy đơn hàng
							</a>
						</div>
					</div>
				</div>
				
				<div class="register-box register-right">
					<div class="customer-form deliverer">

						<div class="input-group contacts">
							<span class="input-group-addon">
								<?php echo anchor("customers/view/-1/1","<i class='ion-person-add'></i>", array('class'=>'none','title'=>lang('common_new_customer'), 'id' => 'new-customer','tabindex' => '-1')); ?>
							</span>
							<input type="text" id="customer" name="customer" class="add-customer-input" placeholder="Khách hàng">
						</div>
						<div>
							Khách hàng: <span id="customer_name"><?php echo($customer) ? $customer->first_name . ' ' . $customer->last_name : ''; ?></span>
						</div>
					</div> 
		
					<div class="customer-form deliverer">
						<div class="input-group contacts">
								<span class="input-group-addon">
									<?php echo anchor("employees/view/-1","<i class='ion-person-add'></i>", array('class'=>'none','title'=>lang('common_new_customer'), 'id' => 'new-customer','tabindex' => '-1')); ?>
								</span>
								<input type="text" id="deliverer" name="deliverer" class="add-customer-input" placeholder="Nhân viên xuất hàng">
						</div>
						<div>
							Nhân viên xuất hàng: <span id="deliverer_name"><?php echo $deliverer ? $deliverer->first_name . ' ' . $deliverer->last_name : ''; ?></span>
						</div>
					</div>
					<div class="customer-form">
						<button class="btn btn-primary active" type="submit" >Hoàn Thành</button>
					</div>
				</div>
			</div>
		</div>
		</form>
	</div>
 </div>
 
 <script type="text/javascript" language="javascript">
	$(document).ready(function(){
		$('#item, #deliverer').click(function()
		{
			$(this).attr('value','');
		});


		$("#item").autocomplete({
	 		source: '<?php echo site_url("stock_out/search");?>',
			delay: 150,
	 		autoFocus: false,
	 		minLength: 0,
	 		select: function( event, ui ) 
	 		{
	 			$( "#item" ).val(ui.item.value);
	 			var _data = {};
				_data['item_id'] = ui.item.value;
				coreAjax.call(
					'<?php echo site_url("stock_out/store_item");?>',
					_data,
					function(response)
					{
						$('#block_selected_items').html(response.html);
					}
				);
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

			if($( "#deliverer" ).length) {
				$('#deliverer').blur(function()
				{
					$(this).attr('value',<?php echo json_encode(lang('sales_start_typing_deliverer_name')); ?>);
				});
				
				$( "#deliverer" ).autocomplete({
			 		source: '<?php echo site_url("sales/deliverer_search");?>',
					delay: 150,
			 		autoFocus: false,
			 		minLength: 0,
			 		select: function( event, ui ) 
			 		{
			 			$( "#deliverer" ).val(ui.item.value);
			 			var _data = {};
						_data['deliverer_id'] = ui.item.value;
						coreAjax.call(
							'<?php echo site_url("stock_out/select_delivery");?>',
							_data,
							function(response)
							{
								$('#deliverer_name').text(response.deliverer);
							}
						);
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
			}

			$( "#customer" ).autocomplete({
		 		source: '<?php echo site_url("sales/customer_search");?>',
				delay: 150,
		 		autoFocus: false,
		 		minLength: 0,
		 		select: function( event, ui ) 
		 		{
		 			$( "#customer" ).val(ui.item.value);
		 			var _data = {};
					_data['customer_id'] = ui.item.value;
					coreAjax.call(
						'<?php echo site_url("stock_out/select_customer");?>',
						_data,
						function(response)
						{
							$('#customer_name').text(response.customer);
						}
					);
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

		 $('#free_style').unbind('click').bind('click', function(){
			 var _data = {};
				_data['mode'] = 'free_style';
				coreAjax.call(
					'<?php echo site_url("stock_out/change_mode");?>',
					_data,
					function(response)
					{
						location.reload();
					}
				);
		 });

		 $('#by_sale').unbind('click').bind('click', function(){
			 var _data = {};
				_data['mode'] = 'by_sale';
				coreAjax.call(
					'<?php echo site_url("stock_out/change_mode");?>',
					_data,
					function(response)
					{
						location.reload();
					}
				);
		 });
	});
 </script>
 
<?php $this->load->view("partial/footer"); ?>