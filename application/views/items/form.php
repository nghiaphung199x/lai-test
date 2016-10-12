<?php $this->load->view("partial/header"); ?>

<?php echo form_open_multipart('items/save/'.(!isset($is_clone) ? $item_info->item_id : ''),array('id'=>'item_form','class'=>'form-horizontal')); ?>
<div class="row" id="form">
	<div class="spinner" id="grid-loader" style="display:none">
	  <div class="rect1"></div>
	  <div class="rect2"></div>
	  <div class="rect3"></div>
	</div>
	<div class="col-md-12">
	<div class="panel panel-piluku">
		<div class="panel-heading">
                <h3 class="panel-title">
                    <i class="ion-edit"></i> 
                    	<?php echo lang("items_basic_information"); ?>
						<small>(<?php echo lang('common_fields_required_message'); ?>)</small>
                </h3>
        </div>

			<div class="panel-body">
				<div class="item_navigation clearfix text-center">
					<ul class="list-inline">
						<li>
							<?php
							if (isset($prev_item_id) && $prev_item_id)
							{
								echo '<div class="previous_item">';
									echo anchor('items/view/'.$prev_item_id, '&laquo; '.lang('items_prev_item'), 'class="btn btn-green btn-round"');
								echo '</div>';
							}
							?>
						</li>
						<li>
							<?php
							if (isset($next_item_id) && $next_item_id)
							{
								echo '<div class="next_item">';
									echo anchor('items/view/'.$next_item_id,lang('items_next_item').' &raquo;', 'class="btn btn-green btn-round"');
								echo '</div>';
							}
							?>
						</li>
					</ul>
				</div>

				<div class="form-group">
					<?php echo form_label(lang('common_item_number_expanded').' :', 'item_number',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label wide')); ?>
					<div class="col-sm-9 col-md-9 col-lg-10">
						<?php echo form_input(array(
							'name'=>'item_number',
							'id'=>'item_number',
							'class'=>'form-control form-inps',
							'value'=>$item_info->item_number)
						);?>
					</div>
				</div>

				<div class="form-group">
					<?php echo form_label(lang('common_product_id').' :', 'product_id',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label wide')); ?>
					<div class="col-sm-9 col-md-9 col-lg-10">
						<?php echo form_input(array(
							'name'=>'product_id',
							'id'=>'product_id',
							'class'=>'form-control form-inps',
							'value'=>$item_info->product_id)
						);?>
					</div>
				</div>
				
				<div class="form-group">	
					<label class="col-sm-3 col-md-3 col-lg-2 control-label"><?php echo lang('items_additional_item_numbers') ?></label>
					<div class="col-sm-9 col-md-3 col-lg-3">
						<table id="additional_item_numbers" class="table">
							<thead>
								<tr>
								<th><?php echo lang('common_item_number'); ?></th>
								<th><?php echo lang('common_delete'); ?></th>
								</tr>
							</thead>
							
							<tbody>
								<?php if (isset($additional_item_numbers) && $additional_item_numbers) {?>
									<?php foreach($additional_item_numbers->result() as $additional_item_number) { ?>
										<tr><td><input type="text" class="form-control form-inps" size="50" name="additional_item_numbers[]" value="<?php echo H($additional_item_number->item_number); ?>" /></td><td>
										<a class="delete_item_number" href="javascript:void(0);"><?php echo lang('common_delete'); ?></a>
									</td></tr>
									<?php } ?>
								<?php } ?>
							</tbody>
						</table>
					
						<a href="javascript:void(0);" id="add_addtional_item_number"><?php echo lang('items_add_item_number'); ?></a>
					</div>
				</div>

				<div class="form-group">
					<?php echo form_label(lang('common_item_name').' :', 'name',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label required wide')); ?>
					<div class="col-sm-9 col-md-9 col-lg-10">
						<?php echo form_input(array(
							'name'=>'name',
							'id'=>'name',
							'class'=>'form-control form-inps',
							'value'=>$item_info->name)
						);?>
					</div>
				</div>


				<div class="form-group">
					<?php echo form_label(lang('common_category').' :', 'category_id',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label  required wide')); ?>
					<div class="col-sm-9 col-md-9 col-lg-10">
						<?php echo form_dropdown('category_id', $categories,$item_info->category_id, 'class="form-control form-inps" id ="category_id"');?>
						<?php if ($this->Employee->has_module_action_permission('items', 'manage_categories', $this->Employee->get_logged_in_employee_info()->person_id)) {?>
								<div>
									<?php echo anchor("items/categories",lang('items_manage_categories'),array('target' => '_blank', 'title'=>lang('items_manage_categories')));?>
								</div>
						<?php } ?>		
					</div>
					
				</div>
				
				
				<div class="form-group">
					<?php echo form_label(lang('common_tags').' :', 'tags',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label wide')); ?>
					<div class="col-sm-9 col-md-9 col-lg-10">
						<?php echo form_input(array(
							'name'=>'tags',
							'id'=>'tags',
							'class'=>'form-control form-inps',
							'value' => $tags,
						));?>
						<?php if ($this->Employee->has_module_action_permission('items', 'manage_tags', $this->Employee->get_logged_in_employee_info()->person_id)) {?>
								<div>
									<?php echo anchor("items/manage_tags",lang('items_manage_tags'),array('target' => '_blank', 'title'=>lang('items_manage_tags')));?>
								</div>
						<?php } ?>
					</div>
				</div>

				<div class="form-group">
					<?php echo form_label(lang('common_size').' :', 'size',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label wide')); ?>
					<div class="col-sm-9 col-md-9 col-lg-10">
						<?php echo form_input(array(
							'name'=>'size',
							'id'=>'size',
							'class'=>'form-control form-inps',
							'value'=>$item_info->size)
						);?>
					</div>
				</div>

				<div class="form-group">
					<?php echo form_label(lang('common_supplier').' :', 'supplier_id',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label wide '.(!$item_info->item_id ? 'required' : ''))); ?>
					<div class="col-sm-9 col-md-9 col-lg-10">
						<?php echo form_dropdown('supplier_id', $suppliers, $selected_supplier,'class="form-control" id="supplier_id"');?>
					</div>
				</div>
				
				<div class="form-group reorder-input <?php if ($item_info->is_service){echo 'hidden';} ?>">
					<?php echo form_label(lang('items_reorder_level').' :', 'reorder_level',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label wide')); ?>
					<div class="col-sm-9 col-md-9 col-lg-10">
						<?php echo form_input(array(
							'name'=>'reorder_level',
							'id'=>'reorder_level',
							'class'=>'form-control form-inps',
							'value'=>$item_info->reorder_level ? to_quantity($item_info->reorder_level) :'')
						);?>
					</div>
				</div>
				
				<div class="form-group">
					<?php echo form_label(lang('items_days_to_expiration').' :', 'size',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label wide')); ?>
					<div class="col-sm-9 col-md-9 col-lg-10">
						<?php echo form_input(array(
							'name'=>'expire_days',
							'id'=>'expire_days',
							'class'=>'form-control form-inps',
							'value'=>$item_info->expire_days)
						);?>
					</div>
				</div>

				<div class="form-group">
					<?php echo form_label(lang('common_description').' :', 'description',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label wide')); ?>
					<div class="col-sm-9 col-md-9 col-lg-10">
						<?php echo form_textarea(array(
							'name'=>'description',
							'id'=>'description',
							'value'=>$item_info->description,
							'class'=>'form-control  text-area',
							'rows'=>'5',
							'cols'=>'17')
						);?>
					</div>
				</div>

				<div class="form-group">
					<?php echo form_label(lang('common_prices_include_tax').' :', 'tax_included',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label wide')); ?>
					<div class="col-sm-9 col-md-9 col-lg-10">
						<?php echo form_checkbox(array(
							'name'=>'tax_included',
							'id'=>'tax_included',
							'class'=>'delete-checkbox',
							'value'=>1,
							'checked'=>($item_info->tax_included || (!$item_info->item_id && $this->config->item('prices_include_tax'))) ? 1 : 0)
						);?>
						<label for="tax_included"><span></span></label>
					</div>
				</div>

				<div class="form-group">
					<?php echo form_label(lang('items_is_service').' :', 'is_service',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label wide')); ?>
					<div class="col-sm-9 col-md-9 col-lg-10">
						<?php echo form_checkbox(array(
							'name'=>'is_service',
							'id'=>'is_service',
								'class'=>'delete-checkbox',
							'value'=>1,
							'checked'=>($item_info->is_service || (!$item_info->item_id && $this->config->item('default_new_items_to_service'))) ? 1 : 0)
						);?>
						<label for="is_service"><span></span></label>
					</div>
				</div>

				<div class="form-group">
					<?php echo form_label(lang('items_allow_alt_desciption').' :', 'allow_alt_description',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label wide')); ?>
					<div class="col-sm-9 col-md-9 col-lg-10">
						<?php echo form_checkbox(array(
							'name'=>'allow_alt_description',
							'id'=>'allow_alt_description',
							'class'=>'delete-checkbox',
							'value'=>1,
							'checked'=>($item_info->allow_alt_description)? 1  :0)
						);?>
						<label for="allow_alt_description"><span></span></label>
					</div>
				</div>

				<div class="form-group">
					<?php echo form_label(lang('items_is_serialized').' :', 'is_serialized',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label wide')); ?>
					<div class="col-sm-9 col-md-9 col-lg-10">
						<?php echo form_checkbox(array(
							'name'=>'is_serialized',
							'id'=>'is_serialized',
								'class'=>'delete-checkbox',
							'value'=>1,
							'checked'=>($item_info->is_serialized)? 1 : 0)
						);?>
						<label for="is_serialized"><span></span></label>
					</div>
				</div>
				
				<div class="form-group">
	                <?php echo form_label(lang('common_choose_avatar').' :', 'image_id',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
					<div class="col-sm-9 col-md-9 col-lg-10">
	                    <div class="image-upload">
	                      	<input type="file" name="image_id" id="image_id" class="filestyle" data-icon="false" >  
	                  	</div>  &nbsp;
	                  	<div id="avatar">
			      			<?php echo $item_info->image_id ? img(array('src' => site_url('app_files/view/'.$item_info->image_id),'class'=>'img-polaroid img-polaroid-s')) : img(array('src' => base_url().'assets/img/avatar.png','class'=>'','id'=>'image_empty')); ?>
						</div>
	                </div> 
				</div>

				<?php if($item_info->image_id) {  ?>
				<div class="form-group">
					<?php echo form_label(lang('common_del_image').' :', 'del_image',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label wide')); ?>
					<div class="col-sm-9 col-md-9 col-lg-10">
						<?php echo form_checkbox(array(
							'name'=>'del_image',
							'id'=>'del_image',
							'class'=>'delete-checkbox',
							'value'=>1
						));?>
						<label for="del_image"><span></span></label>
					</div>
				</div>
				<?php } ?>
			</div><!--/panel-body -->
		</div><!-- /panel-piluku -->


		<div class="panel panel-piluku">
			<div class="panel-heading pricing-widget">
				<?php echo lang("items_pricing_and_inventory"); ?>
			</div>
			<div class="panel-body">
				<?php if ($this->Employee->has_module_action_permission('items','see_cost_price', $this->Employee->get_logged_in_employee_info()->person_id) or $item_info->name=="") { ?>
					<div class="form-group">
						<?php echo form_label(lang('common_cost_price').' ('.lang('common_without_tax').')'.' :', 'cost_price',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label required wide')); ?>
						<div class="col-sm-9 col-md-9 col-lg-10">
							<?php echo form_input(array(
								'name'=>'cost_price',
								'size'=>'8',
								'id'=>'cost_price',
								'class'=>'form-control form-inps',
								'value'=>$item_info->cost_price ? to_currency_no_money($item_info->cost_price,10) : '')
							);?>
						</div>
					</div>
				<?php 
				}
				else
				{
					echo form_hidden('cost_price', $item_info->cost_price);
				}
				?>
				
				<?php if ($this->Employee->has_module_action_permission('items','see_cost_price', $this->Employee->get_logged_in_employee_info()->person_id) or $item_info->name=="") { ?>
				
				
				<div class="form-group">
					<?php echo form_label(lang('common_change_cost_price_during_sale').' :', '',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label wide')); ?>
					
					<div class="col-sm-9 col-md-9 col-lg-10">
						<?php echo form_checkbox(array(
							'name'=>'change_cost_price',
							'id'=>'change_cost_price',
							'class' => 'delete-checkbox',
							'value'=>1,
							'checked'=>(boolean)(($item_info->change_cost_price))));
						?>
						<label for="change_cost_price"><span></span></label>
					</div>
				</div>
				<?php } elseif($item_info->change_cost_price) { 
					echo form_hidden('change_cost_price', 1);
				?>
					
				<?php } ?>
				<?php if ($this->Employee->has_module_action_permission('items','see_cost_price', $this->Employee->get_logged_in_employee_info()->person_id) or $item_info->name=="") { ?>
					
				<?php if (!$this->config->item('disable_margin_calculator')) { ?>
				<div class="form-group">
					<?php echo form_label(lang('common_margin').' :', 'margin',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label wide')); ?>
				    <div class="col-sm-9 col-md-9 col-lg-10">
					    <?php echo form_input(array(
					        'name'=>'margin',
					        'size'=>'8',
							'class'=>'form-control',
					        'id'=>'margin',
					        'value'=>'',
							  'placeholder' => lang('common_enter_margin_percent'),
							)
					    );?>
						 
				    </div>
				</div>
				<?php } ?>
				<?php } ?>
				<div class="form-group">
					<?php echo form_label(lang('common_unit_price').' :', 'unit_price',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label required wide')); ?>
					<div class="col-sm-9 col-md-9 col-lg-10">
						<?php echo form_input(array(
							'name'=>'unit_price',
							'size'=>'8',
							'id'=>'unit_price',
									'class'=>'form-control form-inps',
							'value'=>$item_info->unit_price ? to_currency_no_money($item_info->unit_price, 10) : '')
						);?>
					</div>
				</div>

				<?php foreach($tiers as $tier) { ?>	
					<div class="form-group">
						<?php echo form_label($tier->name.' :', 'tier_'.$tier->id,array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label wide')); ?>
						<div class="col-sm-9 col-md-9 col-lg-10">
							<?php echo form_input(array(
								'name'=>'item_tier['.$tier->id.']',
								'size'=>'8',
								'id'=>'tier_'.$tier->id,
								'class'=>'form-control form-inps margin10',
								'value'=> $tier_prices[$tier->id] !== FALSE ? ($tier_prices[$tier->id]->unit_price != NULL ? to_currency_no_money($tier_prices[$tier->id]->unit_price, 10) : to_quantity($tier_prices[$tier->id]->percent_off)): '')
							);?>

							<?php echo form_dropdown('tier_type['.$tier->id.']', $tier_type_options, $tier_prices[$tier->id] !== FALSE && $tier_prices[$tier->id]->unit_price === NULL ? 'percent_off' : 'unit_price','class="form-control"');?>
						</div>
					</div>
				<?php } ?>

				<div class="form-group">
					<?php echo form_label(lang('items_promo_price').' :', 'promo_price',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label wide')); ?>
				    <div class="col-sm-9 col-md-9 col-lg-10">
					    <?php echo form_input(array(
					        'name'=>'promo_price',
					        'size'=>'8',
							'class'=>'form-control',
					        'id'=>'promo_price',
					        'value'=> $item_info->promo_price ? to_currency_no_money($item_info->promo_price,10) : '')
					    );?>
				    </div>
				</div>

				<div class="form-group offset1">
					<?php echo form_label(lang('items_promo_start_date').' :', 'start_date',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label text-info wide')); ?>
					<div class="col-sm-9 col-md-9 col-lg-10">
					    <div class="input-group date" data-date="<?php echo $item_info->start_date ? date(get_date_format(), strtotime($item_info->start_date)) : ''; ?>">
							<span class="input-group-addon bg">
	                           <i class="ion ion-ios-calendar-outline"></i>
	                       	</span>
							<?php echo form_input(array(
						        'name'=>'start_date',
						        'id'=>'start_date',
								'class'=>'form-control datepicker',
						        'value'=>$item_info->start_date ? date(get_date_format(), strtotime($item_info->start_date)) : '')
						    );?> 
					    </div>
				    </div>
				</div>

				<div class="form-group offset1">
					<?php echo form_label(lang('items_promo_end_date').' :', 'end_date',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label text-info wide')); ?>
					<div class="col-sm-9 col-md-9 col-lg-10">
					    <div class="input-group date" data-date="<?php echo $item_info->end_date ? date(get_date_format(), strtotime($item_info->end_date)) : ''; ?>">
							<span class="input-group-addon bg">
	                           <i class="ion ion-ios-calendar-outline"></i>
	                       	</span>
							<?php echo form_input(array(
						        'name'=>'end_date',
						        'id'=>'end_date',
										'class'=>'form-control form-inps datepicker',
						        'value'=>$item_info->end_date ? date(get_date_format(), strtotime($item_info->end_date)) : '')
						    );?> 
						</div>
				    </div>
				</div>				

				<div class="form-group override-commission-container">
					<?php echo form_label(lang('common_override_default_commission').' :', '',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label wide')); ?>
					
					<div class="col-sm-9 col-md-9 col-lg-10">
						<?php echo form_checkbox(array(
							'name'=>'override_default_commission',
							'id'=>'override_default_commission',
							'class' => 'override_default_commission delete-checkbox',
							'value'=>1,
							'checked'=>(boolean)(($item_info->commission_percent != '') || ($item_info->commission_fixed != ''))));
						?>
						<label for="override_default_commission"><span></span></label>
					</div>
				</div>

				<div class="commission-container <?php if (!($item_info->commission_percent != '') && !($item_info->commission_fixed != '')){echo 'hidden';} ?>">
					<p style="margin-top: 10px;"><?php echo lang('common_commission_help');?></p>
					<div class="form-group">
						<?php echo form_label(lang('reports_commission'), 'commission_value',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label wide')); ?>
						<div class='col-sm-9 col-md-9 col-lg-10'>
							<?php echo form_input(array(
								'name'=>'commission_value',
								'id'=>'commission_value',
								'size'=>'8',
								'class'=>'form-control margin10 form-inps', 
								'value'=> $item_info->commission_fixed != '' ? to_quantity($item_info->commission_fixed, FALSE) : to_quantity($item_info->commission_percent, FALSE))
							);?>
							
							<?php echo form_dropdown('commission_type', array('percent' => lang('common_percentage'), 'fixed' => lang('common_fixed_amount')), $item_info->commission_fixed != '' ? 'fixed' : 'percent', 'id="commission_type"');?>
						</div>
					</div>
					
					<div class="form-group" id="commission-percent-calculation-container">	
						<?php echo form_label(lang('common_commission_percent_calculation').': ', 'commission_percent_type',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label')); ?>
						<div class="col-sm-9 col-md-9 col-lg-10">
						<?php echo form_dropdown('commission_percent_type', array(
							'selling_price'  => lang('common_unit_price'),
							'profit'    => lang('common_profit'),
							),
							$item_info->commission_percent_type,
							array('id' =>'commission_percent_type'))
							?>
						</div>
					</div>
				</div>
			
				<div class="form-group override-taxes-container">
					<?php echo form_label(lang('common_override_default_tax').' :', '',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label wide')); ?>
					<div class="col-sm-9 col-md-9 col-lg-10">
						<?php echo form_checkbox(array(
							'name'=>'override_default_tax',
							'id'=>'override_default_tax',
							'class' => 'override_default_tax_checkbox delete-checkbox',
							'value'=>1,
							'checked'=>(boolean)$item_info->override_default_tax));
						?>
						<label for="override_default_tax"><span></span></label>
					</div>
				</div>

				<div class="tax-container main <?php if (!$item_info->override_default_tax){echo 'hidden';} ?>">	
					<div class="form-group">
						<?php echo form_label(lang('common_tax_1').' :', 'tax_percent_1',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label wide')); ?>
						<div class="col-sm-9 col-md-9 col-lg-10">
							<?php echo form_input(array(
								'name'=>'tax_names[]',
								'id'=>'tax_percent_1',
								'size'=>'8',
								'class'=>'form-control margin10 form-inps',
								'placeholder' => lang('common_tax_name'),
								'value'=> isset($item_tax_info[0]['name']) ? $item_tax_info[0]['name'] : ($this->Location->get_info_for_key('default_tax_1_name') ? $this->Location->get_info_for_key('default_tax_1_name') : $this->config->item('default_tax_1_name')))
							);?>
						</div>
	                    <label class="col-sm-3 col-md-3 col-lg-2 control-label wide" for="tax_percent_name_1">&nbsp;</label>
						<div class="col-sm-9 col-md-9 col-lg-10">
							<?php echo form_input(array(
								'name'=>'tax_percents[]',
								'id'=>'tax_percent_name_1',
								'size'=>'3',
								'class'=>'form-control form-inps-tax',
								'placeholder' => lang('common_tax_percent'),
								'value'=> isset($item_tax_info[0]['percent']) ? $item_tax_info[0]['percent'] : '')
							);?>
							<div class="tax-percent-icon">%</div>
							<div class="clear"></div>
							<?php echo form_hidden('tax_cumulatives[]', '0'); ?>
						</div>
					</div>

					<div class="form-group">
						<?php echo form_label(lang('common_tax_2').' :', 'tax_percent_2',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label wide')); ?>
						<div class="col-sm-9 col-md-9 col-lg-10">
							<?php echo form_input(array(
								'name'=>'tax_names[]',
								'id'=>'tax_percent_2',
								'size'=>'8',
								'class'=>'form-control form-inps margin10',
								'placeholder' => lang('common_tax_name'),
								'value'=> isset($item_tax_info[1]['name']) ? $item_tax_info[1]['name'] : ($this->Location->get_info_for_key('default_tax_2_name') ? $this->Location->get_info_for_key('default_tax_2_name') : $this->config->item('default_tax_2_name')))
							);?>
						</div>
	                    <label class="col-sm-3 col-md-3 col-lg-2 control-label text-info wide">&nbsp;</label>
						<div class="col-sm-9 col-md-9 col-lg-10">
							<?php echo form_input(array(
								'name'=>'tax_percents[]',
								'id'=>'tax_percent_name_2',
								'size'=>'3',
								'class'=>'form-control form-inps-tax',
								'placeholder' => lang('common_tax_percent'),
								'value'=> isset($item_tax_info[1]['percent']) ? $item_tax_info[1]['percent'] : '')
							);?>
							<div class="tax-percent-icon">%</div>
							<div class="clear"></div>
							<?php echo form_checkbox('tax_cumulatives[]', '1', (isset($item_tax_info[1]['cumulative']) && $item_tax_info[1]['cumulative']) ? (boolean)$item_tax_info[1]['cumulative'] : (boolean)$this->config->item('default_tax_2_cumulative'), 'class="cumulative_checkbox" id="tax_cumulatives"'); ?>
							<label for="tax_cumulatives"><span></span></label>
						    <span class="cumulative_label">
								<?php echo lang('common_cumulative'); ?>
						    </span>
						</div>
					</div>
	                 
					<div class="col-sm-9 col-sm-offset-3 col-md-9 col-md-offset-3 col-lg-9 col-lg-offset-3"  style="visibility: <?php echo isset($item_tax_info[2]['name']) ? 'hidden' : 'visible';?>">
						<a href="javascript:void(0);" class="show_more_taxes"><?php echo lang('common_show_more');?> &raquo;</a>
					</div>
					<div class="more_taxes_container" style="display: <?php echo isset($item_tax_info[2]['name']) ? 'block' : 'none';?>">
						<div class="form-group">
							<?php echo form_label(lang('common_tax_3').' :', 'tax_percent_3',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label wide')); ?>
							<div class="col-sm-9 col-md-9 col-lg-10">
								<?php echo form_input(array(
									'name'=>'tax_names[]',
									'id'=>'tax_percent_3',
									'size'=>'8',
									'class'=>'form-control form-inps margin10',
									'placeholder' => lang('common_tax_name'),
									'value'=> isset($item_tax_info[2]['name']) ? $item_tax_info[2]['name'] : ($this->Location->get_info_for_key('default_tax_3_name') ? $this->Location->get_info_for_key('default_tax_3_name') : $this->config->item('default_tax_3_name')))
								);?>
							</div>
	                        <label class="col-sm-3 col-md-3 col-lg-2 control-label wide">&nbsp;</label>
							<div class="col-sm-9 col-md-9 col-lg-10">
								<?php echo form_input(array(
									'name'=>'tax_percents[]',
									'id'=>'tax_percent_name_3',
									'size'=>'3',
									'class'=>'form-control form-inps-tax margin10',
									'placeholder' => lang('common_tax_percent'),
									'value'=> isset($item_tax_info[2]['percent']) ? $item_tax_info[2]['percent'] : '')
								);?>
							<div class="tax-percent-icon">%</div>
							<div class="clear"></div>
							<?php echo form_hidden('tax_cumulatives[]', '0'); ?>
							</div>
						</div>

						<div class="form-group">
						<?php echo form_label(lang('common_tax_4').' :', 'tax_percent_4',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label wide')); ?>
							<div class="col-sm-9 col-md-9 col-lg-10">
							<?php echo form_input(array(
								'name'=>'tax_names[]',
								'id'=>'tax_percent_4',
								'size'=>'8',
								'class'=>'form-control  form-inps margin10',
								'placeholder' => lang('common_tax_name'),
								'value'=> isset($item_tax_info[3]['name']) ? $item_tax_info[3]['name'] : ($this->Location->get_info_for_key('default_tax_4_name') ? $this->Location->get_info_for_key('default_tax_4_name') : $this->config->item('default_tax_4_name')))
							);?>
							</div>
	                        <label class="col-sm-3 col-md-3 col-lg-2 control-label wide">&nbsp;</label>
							<div class="col-sm-9 col-md-9 col-lg-10">
							<?php echo form_input(array(
								'name'=>'tax_percents[]',
								'id'=>'tax_percent_name_4',
								'size'=>'3',
								'class'=>'form-control form-inps-tax', 
								'placeholder' => lang('common_tax_percent'),
								'value'=> isset($item_tax_info[3]['percent']) ? $item_tax_info[3]['percent'] : '')
							);?>
							<div class="tax-percent-icon">%</div>
							<div class="clear"></div>
							<?php echo form_hidden('tax_cumulatives[]', '0'); ?>
							</div>
						</div>
						
						<div class="form-group">
						<?php echo form_label(lang('common_tax_5').' :', 'tax_percent_5',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label wide')); ?>
							<div class="col-sm-9 col-md-9 col-lg-10">
								<?php echo form_input(array(
									'name'=>'tax_names[]',
									'id'=>'tax_percent_5',
									'size'=>'8',
									'class'=>'form-control  form-inps margin10',
									'placeholder' => lang('common_tax_name'),
									'value'=> isset($item_tax_info[4]['name']) ? $item_tax_info[4]['name'] : ($this->Location->get_info_for_key('default_tax_5_name') ? $this->Location->get_info_for_key('default_tax_5_name') : $this->config->item('default_tax_5_name')))
								);?>
							</div>
	                        <label class="col-sm-3 col-md-3 col-lg-2 control-label wide">&nbsp;</label>
							<div class="col-sm-9 col-md-9 col-lg-10">
								<?php echo form_input(array(
									'name'=>'tax_percents[]',
									'id'=>'tax_percent_name_5',
									'size'=>'3',
									'class'=>'form-control form-inps-tax margin10',
									'placeholder' => lang('common_tax_percent'),
									'value'=> isset($item_tax_info[4]['percent']) ? $item_tax_info[4]['percent'] : '')
								);?>
							<div class="tax-percent-icon">%</div>
							<div class="clear"></div>
							<?php echo form_hidden('tax_cumulatives[]', '0'); ?>
							</div>
						</div>
					</div> <!--End more Taxes Container-->
	                <div class="clear"></div>
				</div>
			</div><!-- /panel-body-->
		</div><!--/panel-piluku-->

		<div class="panel">
			<div class="panel-body">
				<?php foreach($locations as $location) { 

					if($location->name == $authenticated_locations[$current_logged_in_location_id]) { ?>
						<div class="item-current-location">
							<div class="form-heading">
								<?php echo $location->name; ?> (<?php echo lang('common_current_location'); ?>)
							</div>
						</div>
						<?php } else { ?>
							<div class="form-heading">
								<?php echo $location->name; ?>
							</div>
						<?php } ?>
					<?php if ($this->Employee->has_module_action_permission('items','edit_quantity', $this->Employee->get_logged_in_employee_info()->person_id)) { ?>

							<?php
							$cur_quantity = $location_items[$location->location_id]->item_id !== '' && $location_items[$location->location_id]->quantity !== NULL ? to_quantity($location_items[$location->location_id]->quantity): '0';
						
							if (!isset($is_clone))
							{
							?>
							<div class="form-group quantity-input <?php if ($item_info->is_service){echo 'hidden';} ?>">
							
								<?php echo form_label(lang('items_current_quantity').' :', '', array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label wide')); ?>
								<div class="col-sm-9 col-md-9 col-lg-10">
									<h5><?php echo $cur_quantity; ?></h5>
								</div>
							</div>
							<?php
							}
							?>	

						<div class="form-group quantity-input <?php if ($item_info->is_service){echo 'hidden';} ?>">
						<?php echo form_label(lang('items_add_minus').' :', '', array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label wide')); ?>
							<div class="col-sm-9 col-md-9 col-lg-10">
							<?php echo form_input(array(
								'name'=>'locations['.$location->location_id.'][quantity_add_minus]',
								'value' => '',
								'class'=>'form-control form-inps',
							));?>
							</div>
						</div>		
					<?php } ?>

					<?php if ($this->Location->count_all() > 1) {?>
						<div class="form-group reorder-input <?php if ($item_info->is_service){echo 'hidden';} ?>">
							<?php echo form_label(lang('items_reorder_level').' :', '', array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label wide')); ?>
							<div class="col-sm-9 col-md-9 col-lg-10">
								<?php echo form_input(array(
									'name'=>'locations['.$location->location_id.'][reorder_level]',
									'value'=> $location_items[$location->location_id]->item_id !== '' &&  $location_items[$location->location_id]->reorder_level !== NULL ? to_quantity($location_items[$location->location_id]->reorder_level): '',
										'class'=>'form-control form-inps',
								));?>
							</div>
						</div>
					<?php } ?>

					<div class="form-group">
						<?php echo form_label(lang('items_location_at_store').' :', '', array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label wide')); ?>
						<div class="col-sm-9 col-md-9 col-lg-10">
							<?php echo form_input(array(
								'name'=>'locations['.$location->location_id.'][location]',
								'class'=>'form-control form-inps',
								'value'=> $location_items[$location->location_id]->item_id !== '' ? $location_items[$location->location_id]->location: ''
							));?>
						</div>
					</div>
				
					<?php if ($this->Location->count_all() > 1) { ?>
						<div class="form-group override-prices-container">
							<?php echo form_label(lang('common_items_override_prices').' :', '',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label wide')); ?>
							<div class="col-sm-9 col-md-9 col-lg-10">
								<?php echo form_checkbox(array(
									'name'=>'locations['.$location->location_id.'][override_prices]',
									'id'=>'locations['.$location->location_id.'][override_prices]',
									'class' => 'override_prices_checkbox delete-checkbox',
									'value'=>1,
									'checked'=>(boolean)isset($location_items[$location->location_id]) && is_object($location_items[$location->location_id]) && $location_items[$location->location_id]->is_overwritten));
								?>
								<label for="<?php echo 'locations['.$location->location_id.'][override_prices]' ?>"><span></span></label>
							</div>
						</div>
						
						<div class="item-location-price-container <?php if ($location_items[$location->location_id] === FALSE || !$location_items[$location->location_id]->is_overwritten){echo 'hidden';} ?>">	
							<?php if ($this->Employee->has_module_action_permission('items','see_cost_price', $this->Employee->get_logged_in_employee_info()->person_id) or $item_info->name=="") { ?>
								<div class="form-group">
									<?php echo form_label(lang('common_cost_price').' ('.lang('common_without_tax').'):', '',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label wide')); ?>
									<div class="col-sm-9 col-md-9 col-lg-10">
										<?php echo form_input(array(
											'name'=>'locations['.$location->location_id.'][cost_price]',
											'size'=>'8',
											'class'=>'form-control form-inps',
											'value'=> $location_items[$location->location_id]->item_id !== '' && $location_items[$location->location_id]->cost_price ? to_currency_no_money($location_items[$location->location_id]->cost_price, 10): ''
										)
										);?>
									</div>
								</div>
							<?php 
							}
							else
							{
								echo form_hidden('locations['.$location->location_id.'][cost_price]', $location_items[$location->location_id]->item_id !== '' ? $location_items[$location->location_id]->cost_price: '');
							}
							?>

							<div class="form-group">
								<?php echo form_label(lang('common_unit_price').' :', '',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label wide')); ?>
								<div class="col-sm-9 col-md-9 col-lg-10">
									<?php echo form_input(array(
										'name'=>'locations['.$location->location_id.'][unit_price]',
										'size'=>'8',
										'class'=>'form-control form-inps',
										'value'=>$location_items[$location->location_id]->item_id !== '' && $location_items[$location->location_id]->unit_price ? to_currency_no_money($location_items[$location->location_id]->unit_price, 10) : ''
										)
									);?>
								</div>
							</div>

							<?php foreach($tiers as $tier) { ?>	
								<div class="form-group">
									<?php echo form_label($tier->name.' :', $tier->id,array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label wide')); ?>
									<div class='col-sm-9 col-md-9 col-lg-10'>
									<?php echo form_input(array(
										'name'=>'locations['.$location->location_id.'][item_tier]['.$tier->id.']',
										'size'=>'8',
										'id'=>$tier->id,
										'class'=>'form-control margin10 form-inps', 
										'value'=> $location_tier_prices[$location->location_id][$tier->id] !== FALSE ? ($location_tier_prices[$location->location_id][$tier->id]->unit_price != NULL ? to_currency_no_money($location_tier_prices[$location->location_id][$tier->id]->unit_price, 10) : to_quantity($location_tier_prices[$location->location_id][$tier->id]->percent_off)): '')
									);?>

									<?php echo form_dropdown('locations['.$location->location_id.'][tier_type]['.$tier->id.']', $tier_type_options, $location_tier_prices[$location->location_id][$tier->id] !== FALSE && $location_tier_prices[$location->location_id][$tier->id]->unit_price === NULL ? 'percent_off' : 'unit_price');?>
									</div>
								</div>

							<?php } ?>

							<div class="form-group">
								<?php echo form_label(lang('items_promo_price').' :', '',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label wide')); ?>
							    <div class="col-sm-9 col-md-9 col-lg-10">
								    <?php echo form_input(array(
										'name'=>'locations['.$location->location_id.'][promo_price]',
								        'size'=>'8',
									'class'=>'form-control form-inps',
										'value'=> $location_items[$location->location_id]->item_id !== '' && $location_items[$location->location_id]->promo_price ? to_currency_no_money($location_items[$location->location_id]->promo_price, 10): ''
									)
								    );?>
							    </div>
							</div>

							<div class="form-group offset1">
								<?php echo form_label(lang('items_promo_start_date').' :', '',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label text-info wide')); ?>
								<div class="col-sm-9 col-md-9 col-lg-10">
									<div class="input-group date" data-date="<?php echo $location_items[$location->location_id]->item_id !== '' &&  $location_items[$location->location_id]->start_date ? date(get_date_format(), strtotime($location_items[$location->location_id]->start_date)): ''; ?>" >
  										<span class="input-group-addon"><i class="ion-calendar"></i></span>
										<?php echo form_input(array(
											'name'=>'locations['.$location->location_id.'][start_date]',
									        'size'=>'8',
										'class'=>'form-control form-inps datepicker',
											 'value'=> $location_items[$location->location_id]->item_id !== '' && $location_items[$location->location_id]->start_date ? date(get_date_format(), strtotime($location_items[$location->location_id]->start_date)): ''
											)
										);?>       
					    			</div>
								</div>
							</div>

							<div class="form-group offset1">
								<?php echo form_label(lang('items_promo_end_date').' :', '',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label text-info wide')); ?>
							    <div class="col-sm-9 col-md-9 col-lg-10">
							    	<div class="input-group date" data-date="<?php echo $location_items[$location->location_id]->item_id !== '' && $location_items[$location->location_id]->end_date ? date(get_date_format(), strtotime($location_items[$location->location_id]->end_date)): ''; ?>" >
		  								<span class="input-group-addon"><i class="ion-calendar"></i></span>

									    <?php echo form_input(array(
											'name'=>'locations['.$location->location_id.'][end_date]',
									        'size'=>'8',
											'class'=>'form-control form-inps datepicker',
											 'value'=> $location_items[$location->location_id]->item_id !== '' && $location_items[$location->location_id]->end_date ? date(get_date_format(), strtotime($location_items[$location->location_id]->end_date)): ''
									    	));
										?> 
								    </div>
								</div>
							</div>
						</div><!-- /item-location-price-container -->

						<div class="form-group override-taxes-container">
							<?php echo form_label(lang('common_override_default_tax').' :', '',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label wide')); ?>

							<div class="col-sm-9 col-md-9 col-lg-10">
								<?php echo form_checkbox(array(
									'name'=>'locations['.$location->location_id.'][override_default_tax]',
									'id'=>'locations['.$location->location_id.'][override_default_tax]',
									'class' => 'override_default_tax_checkbox  delete-checkbox',
									'value'=>1,
									'checked'=> $location_items[$location->location_id]->item_id !== '' ? (boolean)$location_items[$location->location_id]->override_default_tax: FALSE
									));
								?>
								<label for="<?php echo 'locations['.$location->location_id.'][override_default_tax]' ?>"><span></span></label>
							</div>
						</div>

						<div class="tax-container <?php if ($location_items[$location->location_id] === FALSE || !$location_items[$location->location_id]->override_default_tax){echo 'hidden';} ?>">	
							<div class="form-group">
								<?php echo form_label(lang('common_tax_1').' :', '',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label wide')); ?>
								<div class="col-sm-9 col-md-9 col-lg-10">
									<?php echo form_input(array(
										'name'=>'locations['.$location->location_id.'][tax_names][]',
										'size'=>'8',
										'class'=>'form-control form-inps margin10',
										'placeholder' => lang('common_tax_name'),
										'value' => isset($location_taxes[$location->location_id][0]['name']) ? $location_taxes[$location->location_id][0]['name'] : ($this->Location->get_info_for_key('default_tax_1_name') ? $this->Location->get_info_for_key('default_tax_1_name') : $this->config->item('default_tax_1_name'))
									));?>
								</div>
                                <label class="col-sm-3 col-md-3 col-lg-2 control-label wide">&nbsp;</label>
								<div class="col-sm-9 col-md-9 col-lg-10">
									<?php echo form_input(array(
										'name'=>'locations['.$location->location_id.'][tax_percents][]',
										'size'=>'3',
										'class'=>'form-control form-inps-tax margin10',
										'placeholder' => lang('common_tax_percent'),
										'value' => isset($location_taxes[$location->location_id][0]['percent']) ? $location_taxes[$location->location_id][0]['percent'] : ''
									));?>
									<div class="tax-percent-icon">%</div>
									<div class="clear"></div>
									<?php echo form_hidden('locations['.$location->location_id.'][tax_cumulatives][]', '0'); ?>
								</div>
							</div>

							<div class="form-group">
								<?php echo form_label(lang('common_tax_2').' :', '',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label wide')); ?>
								<div class="col-sm-9 col-md-9 col-lg-10">
									<?php echo form_input(array(
										'name'=>'locations['.$location->location_id.'][tax_names][]',
										'size'=>'8',
										'class'=>'form-control form-inps margin10',
										'placeholder' => lang('common_tax_name'),
										'value' => isset($location_taxes[$location->location_id][1]['name']) ? $location_taxes[$location->location_id][1]['name'] : ($this->Location->get_info_for_key('default_tax_1_name') ? $this->Location->get_info_for_key('default_tax_1_name') : $this->config->item('default_tax_1_name'))
										)
									);?>
								</div>
	                            <label class="col-sm-3 col-md-3 col-lg-2 control-label wide">&nbsp;</label>
								<div class="col-sm-9 col-md-9 col-lg-10">
									<?php echo form_input(array(
										'name'=>'locations['.$location->location_id.'][tax_percents][]', 
										'size'=>'3',
										'class'=>'form-control form-inps-tax',
										'placeholder' => lang('common_tax_percent'),
										'value' => isset($location_taxes[$location->location_id][1]['percent']) ? $location_taxes[$location->location_id][1]['percent'] : ''
										)
									);?>
									<div class="tax-percent-icon">%</div>
									<div class="clear"></div>
									<?php echo form_checkbox('locations['.$location->location_id.'][tax_cumulatives][]', '1', isset($location_taxes[$location->location_id][1]['cumulative']) ? (boolean)$location_taxes[$location->location_id][1]['cumulative'] : ($this->Location->get_info_for_key('default_tax_2_cumulative') ? (boolean)$this->Location->get_info_for_key('default_tax_2_cumulative') : (boolean)$this->config->item('default_tax_2_cumulative')), 'class="cumulative_checkbox" id="locations['.$location->location_id.'][tax_cumulatives]"'); ?>
									<label for="<?php echo 'locations['.$location->location_id.'][tax_cumulatives]' ?>"><span></span></label>
								    <span class="cumulative_label">
										 <?php echo lang('common_cumulative'); ?>
								    </span>
								</div> <!-- end col-sm-9...-->
							</div><!--End form-group-->
						
							<div class="col-sm-9 col-sm-offset-3 col-md-9 col-md-offset-3 col-lg-9 col-lg-offset-3" style="visibility: <?php echo isset($location_taxes[$location->location_id][2]['name']) ? 'hidden' : 'visible';?>">
								<a href="javascript:void(0);" class="show_more_taxes"><?php echo lang('common_show_more');?> &raquo;</a>
							</div>
						
							<div class="more_taxes_container"  style="display: <?php echo isset($location_taxes[$location->location_id][2]['name']) ? 'block' : 'none';?>">
								<div class="form-group">
									<?php echo form_label(lang('common_tax_3').' :', '',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label wide')); ?>
									<div class="col-sm-9 col-md-9 col-lg-10">
										<?php echo form_input(array(
											'name'=>'locations['.$location->location_id.'][tax_names][]',
											'size'=>'8',
											'class'=>'form-control form-inps margin10',
											'placeholder' => lang('common_tax_name'),
											'value' => isset($location_taxes[$location->location_id][2]['name']) ? $location_taxes[$location->location_id][2]['name'] : ($this->Location->get_info_for_key('default_tax_3_name') ? $this->Location->get_info_for_key('default_tax_3_name') : $this->config->item('default_tax_3_name'))
										));?>
									</div>
                                	<label class="col-sm-3 col-md-3 col-lg-2 control-label wide">&nbsp;</label>
									<div class="col-sm-9 col-md-9 col-lg-10">
										<?php echo form_input(array(
											'name'=>'locations['.$location->location_id.'][tax_percents][]',
											'size'=>'3',
											'class'=>'form-control form-inps-tax',
											'placeholder' => lang('common_tax_percent'),
											'value' => isset($location_taxes[$location->location_id][2]['percent']) ? $location_taxes[$location->location_id][2]['percent'] : ''
										));?>
										<div class="tax-percent-icon">%</div>
										<div class="clear"></div>
										<?php echo form_hidden('locations['.$location->location_id.'][tax_cumulatives][]', '0'); ?>
									</div>
								</div>
							
							
								<div class="form-group">
									<?php echo form_label(lang('common_tax_4').' :', '',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label wide')); ?>
									<div class="col-sm-9 col-md-9 col-lg-10">
										<?php echo form_input(array(
											'name'=>'locations['.$location->location_id.'][tax_names][]',
											'size'=>'8',
											'class'=>'form-control form-inps margin10',
											'placeholder' => lang('common_tax_name'),
											'value' => isset($location_taxes[$location->location_id][3]['name']) ? $location_taxes[$location->location_id][3]['name'] : ($this->Location->get_info_for_key('default_tax_4_name') ? $this->Location->get_info_for_key('default_tax_4_name') : $this->config->item('default_tax_4_name'))
										));?>
									</div>
	                                <label class="col-sm-3 col-md-3 col-lg-2 control-label wide">&nbsp;</label>
									<div class="col-sm-9 col-md-9 col-lg-10">
										<?php echo form_input(array(
											'name'=>'locations['.$location->location_id.'][tax_percents][]',
											'size'=>'3',
											'class'=>'form-control form-inps-tax',
											'placeholder' => lang('common_tax_percent'),
											'value' => isset($location_taxes[$location->location_id][3]['percent']) ? $location_taxes[$location->location_id][3]['percent'] : ''
										));?>
										<div class="tax-percent-icon">%</div>
										<div class="clear"></div>
										<?php echo form_hidden('locations['.$location->location_id.'][tax_cumulatives][]', '0'); ?>
									</div>
								</div>
							
								<div class="form-group">
									<?php echo form_label(lang('common_tax_5').' :', '',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label wide')); ?>
									<div class="col-sm-9 col-md-9 col-lg-10">
										<?php echo form_input(array(
											'name'=>'locations['.$location->location_id.'][tax_names][]',
											'size'=>'8',
											'class'=>'form-control form-inps margin10',
											'placeholder' => lang('common_tax_name'),
											'value' => isset($location_taxes[$location->location_id][4]['name']) ? $location_taxes[$location->location_id][4]['name'] : ($this->Location->get_info_for_key('default_tax_5_name') ? $this->Location->get_info_for_key('default_tax_5_name') : $this->config->item('default_tax_5_name'))
										));?>
									</div>
	                                <label class="col-sm-3 col-md-3 col-lg-2 control-label wide">&nbsp;</label>
									<div class="col-sm-9 col-md-9 col-lg-10">
										<?php echo form_input(array(
											'name'=>'locations['.$location->location_id.'][tax_percents][]',
											'size'=>'3',
											'class'=>'form-control form-inps-tax',
											'placeholder' => lang('common_tax_percent'),
											'value' => isset($location_taxes[$location->location_id][4]['percent']) ? $location_taxes[$location->location_id][4]['percent'] : ''
										));?>
										<div class="tax-percent-icon">%</div>
										<div class="clear"></div>
										<?php echo form_hidden('locations['.$location->location_id.'][tax_cumulatives][]', '0'); ?>
									</div>
								</div>
							</div><!-- End more taxes container-->
                        	<div class="clear"></div>
						</div> <!-- End tax-container-->
					<?php } /*End if for multi locations*/ ?>
					
				<?php } /*End foreach for locations*/ ?>	
			
				<?php echo form_hidden('redirect', isset($redirect) ? $redirect : ''); ?>
				<?php echo form_hidden('sale_or_receiving', isset($sale_or_receiving) ? $sale_or_receiving : ''); ?>
				
				<div class="form-actions pull-right">
					<?php
					if (isset($redirect) && $redirect == 1)
					{
						echo form_button(array(
					    'name' => 'cancel',
					    'id' => 'cancel',
						 'class' => 'submit_button btn btn-danger',
					    'value' => 'true',
					    'content' => lang('common_cancel')
						));
					
					}
					?>
					&nbsp;
					<?php
						echo form_submit(array(
							'name'=>'submitf',
							'id'=>'submitf',
							'value'=>lang('common_submit'),
							'class'=>'submit_button btn btn-primary')
						);
					?>
				</div>
			
			
				<div class="item_navigation text-center">
					<ul class="list-inline">
						<li>
							<?php
							if (isset($prev_item_id) && $prev_item_id)
							{
								echo '<div class="previous_item">';
									echo anchor('items/view/'.$prev_item_id, '&laquo; '.lang('items_prev_item'), 'class="btn btn-green btn-round"');
								echo '</div>';
							}
							?>
						</li>
						<li>
							<?php
							if (isset($next_item_id) && $next_item_id)
							{
								echo '<div class="next_item">';
									echo anchor('items/view/'.$next_item_id,lang('items_next_item').' &raquo;', 'class="btn btn-green btn-round"');
								echo '</div>';
							}
							?>
						</li>
					</ul>
				</div><!-- /item_navigation -->
			</div><!-- /panel-body -->
		</div><!-- /panel -->

	</div>
</div>
		

<script type='text/javascript'>

function commission_change()
{
	if ($("#commission_type").val() == 'percent')
	{
		$("#commission-percent-calculation-container").show();
	}
	else
	{
		$("#commission-percent-calculation-container").hide();						
	}
}
$("#commission_type").change(commission_change);
$(document).ready(commission_change);

function get_taxes()
{
	var taxes = [];
	
	if (!$("#override_default_tax").prop('checked'))
	{
		var default_taxes = <?php echo json_encode($this->Item_taxes_finder->get_info($item_info->item_id)) ?>;
	
		for(var k = 0;k<default_taxes.length;k++)
		{
			taxes.push({'percent': parseFloat(default_taxes[k]['percent']), 'cumulative':default_taxes[k]['cumulative'] == 1});
		}	
	}
	else
	{
		var k=0;
		
		$('.tax-container.main input[name="tax_percents[]"]').each(function()
		{
			if ($(this).val())
			{
				taxes.push({'percent': parseFloat($(this).val()), 'cumulative': k==1 && $("#tax_cumulatives").prop('checked')});
			}
			
			k++;
		});	
	}
	return taxes;
	
}

function get_total_tax_percent()
{
	var total_tax_percent = 0;
	var taxes = get_taxes();
	for(var k = 0;k<taxes.length;k++)
	{
		total_tax_percent += parseFloat(taxes[k]['percent']);
	}
	
	return total_tax_percent;
}

function are_taxes_cumulative()
{
	var taxes = get_taxes();
	
	return (taxes.length == 2 && taxes[1].cumulative);
}

function calculate_margin_percent()
{
	if ($("#tax_included").prop('checked') )
	{
		var cost_price = parseFloat($('#cost_price').val());
		var unit_price = parseFloat($('#unit_price').val());

		var cumulative = are_taxes_cumulative();
		
		if (!cumulative)
		{
			//Margin amount
			//(100*.1)
			//100 + (100*.1) = 118.80 * .08 
	
			//cost price 100.00
			//8% tax
			//margin 10%
			//110.00 before tax
			//selling price 118.80
			//100 * 1.1 = profit 10%	
	
	
			// X = COST PRICE
			// Y = MARGIN PERCENT
			// Z = SELLING PRICE
			// Q = TAX PERCENT
			//100 * (1+ (10/100)) = 118.80 - (100 * (1+ (10/100)) * 8/100);
	
			//X * (1+Y/100) = Z - (X * (1+(Y/100)) * Q/100)
			//Y = -(100 ((Q+100) X-100 Z))/((Q+100) X) and (Q+100) X!=0

			var tax_percent = parseFloat(get_total_tax_percent());
		
			var Z = unit_price;
			var X = cost_price;
			var Q = tax_percent;
			var margin_percent = -(100*((Q+100)*X-100*Z))/((Q+100)*X);
		}
		else
		{
			var taxes = get_taxes();
			var tax_1 = 1+(taxes[0]['percent']/100);
			var tax_2 = 1+(taxes[1]['percent']/100);
			margin_percent = (unit_price / (cost_price * tax_1 * tax_2) - 1) * 100;
		}

	}
	else
	{
		var cost_price = parseFloat($('#cost_price').val());
		var unit_price = parseFloat($('#unit_price').val());
		var margin_percent =  -100 + (100*(unit_price/cost_price));
	}

	margin_percent = parseFloat(Math.round(margin_percent * 100) / 100).toFixed(<?php echo json_encode($decimals); ?>);
	
	$('#margin').val(margin_percent + '%');
}
function calculate_margin_price()
{		
	if ($("#tax_included").prop('checked') )
	{		
		var cost_price = parseFloat($('#cost_price').val());
		var margin_percent = parseFloat($("#margin").val());
		
		var cumulative = are_taxes_cumulative();
		
		if (!cumulative)
		{
			//Margin amount
			//(100*.1)
			//100 + (100*.1) = 118.80 * .08 
	
			//cost price 100.00
			//8% tax
			//margin 10%
			//110.00 before tax
			//selling price 118.80
			//100 * 1.1 = profit 10%	
	
	
			// X = COST PRICE
			// Y = MARGIN PERCENT
			// Z = SELLING PRICE
			// Q = TAX PERCENT
			//100 * (1+ (10/100)) = 118.80 - (100 * (1+ (10/100)) * 8/100);
	
			//X * (1+Y/100) = Z - (X * (1+(Y/100)) * Q/100)
			//Z = (Q X Y+100 Q X+100 X Y+10000 X)/10000
			
			var tax_percent = get_total_tax_percent();
				
			var X = cost_price;
			var Y = margin_percent;
			var Q = tax_percent;
		
			var margin_price = (Q*X*Y+100*Q*X+100*X*Y+10000*X)/10000;		
		}
		else
		{
			var marked_up_price_before_tax = cost_price * (1+(margin_percent/100));
			
			var taxes = get_taxes();
			var cumulative_tax_percent = taxes[1]['percent'];
			
			var first_tax = (marked_up_price_before_tax*(taxes[0]['percent']/100));
			var second_tax = (marked_up_price_before_tax + first_tax) *(taxes[1]['percent']/100);
			var margin_price = marked_up_price_before_tax + first_tax + second_tax;
		}
		
		margin_price = parseFloat(Math.round(margin_price * 100) / 100).toFixed(<?php echo json_encode($decimals); ?>);
	}
	else
	{
		var cost_price = parseFloat($('#cost_price').val());
		var margin_percent = parseFloat($("#margin").val());

		var margin_price = cost_price + (cost_price / 100 * (margin_percent));
		margin_price = parseFloat(Math.round(margin_price * 100) / 100).toFixed(<?php echo json_encode($decimals); ?>);
		
	}

	$('#unit_price').val(margin_price);
}


$(document).ready(function()
{	
	<?php if (!$this->config->item('disable_margin_calculator')) { ?>
	
	if ($('#unit_price').val() && $('#cost_price').val())
	{
		calculate_margin_percent();
	}
	
	$('#margin, #cost_price,.tax-container.main input[name="tax_percents[]"]').keyup(function()
	{
		if($("#margin").val() != '')
		{
			calculate_margin_price();
		}
	});
	
	<?php } ?>
	
	$('#supplier_id').selectize();
	$('#category_id').selectize({
		create: true,
		render: {
	      option_create: function(data, escape) {
				var add_new = <?php echo json_encode(lang('common_new_category')) ?>;
	        return '<div class="create">'+escape(add_new)+' <strong>' + escape(data.input) + '</strong></div>';
	      }
		}
	});
	$(".delete_item_number").click(function()
	{
		$(this).parent().parent().remove();
	});
	
	$("#add_addtional_item_number").click(function()
	{
		$("#additional_item_numbers tbody").append('<tr><td><input type="text" class="form-control form-inps" size="40" name="additional_item_numbers[]" value="" /></td><td>&nbsp;</td></tr>');
	});
	
	$("#cancel").click(cancelItemAddingFromSaleOrRecv);
	
    setTimeout(function(){$(":input:visible:first","#item_form").focus();},100);
    $('#image_id').imagePreview({ selector : '#avatar' }); // Custom preview container
		
	date_time_picker_field($('.datepicker'), JS_DATE_FORMAT);
   	
	$(".override_default_tax_checkbox, .override_prices_checkbox, .override_default_commission").change(function()
	{
		$(this).parent().parent().next().toggleClass('hidden')
	});
	
	$("#is_service").change(function()
	{
		if ($(this).prop('checked'))
		{
			$(".quantity-input").addClass('hidden');			
			$(".reorder-input").addClass('hidden');			
		}
		else
		{
			$(".quantity-input").removeClass('hidden');
			$(".reorder-input").removeClass('hidden');
		}
	});

	
	$('#tags').selectize({
		delimiter: ',',
		loadThrottle : 215,
		persist: false,
		valueField: 'value',
		labelField: 'label',
		searchField: 'label',
		create: true,
		render: {
	      option_create: function(data, escape) {
				var add_new = <?php echo json_encode(lang('common_add_new_tag')) ?>;
	        return '<div class="create">'+escape(add_new)+' <strong>' + escape(data.input) + '</strong></div>';
	      }
		},
		load: function(query, callback) {
			if (!query.length) return callback();
			$.ajax({
				url:'<?php echo site_url("items/tags");?>'+'?term='+encodeURIComponent(query),
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
	$('#item_form').validate({
		ignore: ':hidden:not([class~=selectized]),:hidden > .selectized, .selectize-control .selectize-input input',
		submitHandler:function(form)
		{
			$.post('<?php echo site_url("items/check_duplicate");?>', {term: $('#name').val()},function(data) {
			<?php if(!$item_info->item_id) {  ?>
				if(data.duplicate)
				{
					bootbox.confirm(<?php echo json_encode(lang('common_items_duplicate_exists'));?>, function(result)
					{
						if(result)
						{
							doItemSubmit(form);
						}
					});
				}
				else
				{
					doItemSubmit(form);
				}
				<?php } else { ?>
					doItemSubmit(form);
				<?php } ?>
				} , "json")
				.error(function() { 
				});
		},
		errorClass: "text-danger",
		errorElement: "span",
			highlight:function(element, errorClass, validClass) {
				$(element).parents('.form-group').removeClass('has-success').addClass('has-error');
			},
			unhighlight: function(element, errorClass, validClass) {
				$(element).parents('.form-group').removeClass('has-error').addClass('has-success');
			},
		rules:
		{
		<?php if(!$item_info->item_id) {  ?>
			item_number:
			{
				remote: 
				    { 
					url: "<?php echo site_url('items/item_number_exists');?>", 
					type: "post"
					
				    } 
			},
			product_id:
			{
				remote: 
				    { 
					url: "<?php echo site_url('items/product_id_exists');?>", 
					type: "post"
					
				    } 
			},
			supplier_id:"required",
		<?php } ?>
		
		<?php foreach($tiers as $tier) { ?>
			"<?php echo 'item_tier['.$tier->id.']'; ?>":
			{
				number: true
			},
		<?php } ?>
		
		<?php foreach($locations as $location) { ?>
			"<?php echo 'locations['.$location->location_id.'][quantity]'; ?>":
			{
				number: true
			},
			"<?php echo 'locations['.$location->location_id.'][reorder_level]'; ?>":
			{
				number: true
			},
			"<?php echo 'locations['.$location->location_id.'][cost_price]'; ?>":
			{
				number: true
			},
			"<?php echo 'locations['.$location->location_id.'][unit_price]'; ?>":
			{
				number: true
			},			
			"<?php echo 'locations['.$location->location_id.'][promo_price]'; ?>":
			{
				number: true
			},			
			<?php foreach($tiers as $tier) { ?>
				"<?php echo 'locations['.$location->location_id.'][item_tier]['.$tier->id.']'; ?>":
				{
					number: true
				},
			<?php } ?>			
			
		<?php } ?>
		
			name:"required",
			category_id:"required",
			cost_price:
			{
				required:true,
				number:true
			},

			unit_price:
			{
				required:true,
				number:true
			},
			promo_price:
			{
				number: true
			},
			reorder_level:
			{
				number:true
			}
		},
		messages:
		{			
			<?php if(!$item_info->item_id) {  ?>
			item_number:
			{
				remote: <?php echo json_encode(lang('items_item_number_exists')); ?>
				   
			},
			product_id:
			{
				remote: <?php echo json_encode(lang('items_product_id_exists')); ?>
				   
			},
			<?php } ?>
			
			<?php foreach($tiers as $tier) { ?>
				"<?php echo 'item_tier['.$tier->id.']'; ?>":
				{
					number: <?php echo json_encode(lang('common_this_field_must_be_a_number')); ?>
				},
			<?php } ?>
			
			<?php foreach($locations as $location) { ?>
				"<?php echo 'locations['.$location->location_id.'][quantity]'; ?>":
				{
					number: <?php echo json_encode(lang('common_this_field_must_be_a_number')); ?>
				},
				"<?php echo 'locations['.$location->location_id.'][reorder_level]'; ?>":
				{
					number: <?php echo json_encode(lang('common_this_field_must_be_a_number')); ?>
				},
				"<?php echo 'locations['.$location->location_id.'][cost_price]'; ?>":
				{
					number: <?php echo json_encode(lang('common_this_field_must_be_a_number')); ?>
				},
				"<?php echo 'locations['.$location->location_id.'][unit_price]'; ?>":
				{
					number: <?php echo json_encode(lang('common_this_field_must_be_a_number')); ?>
				},			
				"<?php echo 'locations['.$location->location_id.'][promo_price]'; ?>":
				{
					number: <?php echo json_encode(lang('common_this_field_must_be_a_number')); ?>
				},			
				<?php foreach($tiers as $tier) { ?>
					"<?php echo 'locations['.$location->location_id.'][item_tier]['.$tier->id.']'; ?>":
					{
						number: <?php echo json_encode(lang('common_this_field_must_be_a_number')); ?>
					},
				<?php } ?>				
			<?php } ?>
			
			name:<?php echo json_encode(lang('common_item_name_required')); ?>,
			supplier_id: <?php echo json_encode(lang('items_must_select_supplier'));?>,
			category_id:<?php echo json_encode(lang('common_category_required')); ?>,
			cost_price:
			{
				required:<?php echo json_encode(lang('items_cost_price_required')); ?>,
				number:<?php echo json_encode(lang('common_cost_price_number')); ?>
			},
			unit_price:
			{
				required:<?php echo json_encode(lang('items_unit_price_required')); ?>,
				number:<?php echo json_encode(lang('common_unit_price_number')); ?>
			},
			promo_price:
			{
				number: <?php echo json_encode(lang('common_this_field_must_be_a_number')); ?>
			}
		}
	});
});

var submitting = false;

function doItemSubmit(form)
{
	if (submitting) return;
	submitting = true;
$('#grid-loader').show();
	$(form).ajaxSubmit({
	success:function(response)
	{
$('#grid-loader').hide();
		submitting = false;		
		show_feedback(response.success ? 'success' : 'error', response.message,response.success ? <?php echo json_encode(lang('common_success')); ?> +' #' + response.item_id : <?php echo json_encode(lang('common_error')); ?>);
		
		if(response.redirect==1 && response.success)
		{ 
			if (response.sale_or_receiving == 'sale')
			{
				$.post('<?php echo site_url("sales/add");?>', {item: response.item_id}, function()
				{
					window.location.href = '<?php echo site_url('sales/index/1'); ?>';
				});
			}
			else
			{
				$.post('<?php echo site_url("receivings/add");?>', {item: response.item_id}, function()
				{
					window.location.href = '<?php echo site_url('receivings'); ?>';
				});
			}
		}
		else if(response.redirect==2 && response.success)
		{
			window.location.href = '<?php echo site_url('items'); ?>';
		}
		else
		{
			$("html, body").animate({ scrollTop: 0 }, "slow");
		}

		
		<?php if(!$item_info->item_id) { ?>
		//If we have a new item, make sure we hide the tax containers to "reset"
		$(".tax-container").addClass('hidden');
		$(".item-location-price-container").addClass('hidden');
		$('.commission-container').addClass('hidden');
		
		//Make the quantity inputs show up again in case they were hidden
		$(".quantity-input").removeClass('hidden');
		$(".reorder-input").removeClass('hidden');
		
		var selectize = $("#tags")[0].selectize;
		selectize.clear();
		selectize.clearOptions();
		
		<?php } ?>
	},
	<?php if(!$item_info->item_id) { ?>
	resetForm: true,
	<?php } ?>
	dataType:'json'
	});
}

function cancelItemAddingFromSaleOrRecv()
{
	bootbox.confirm(<?php echo json_encode(lang('items_are_you_sure_cancel')); ?>, function(result)
	{
		if (result)
		{
			<?php if (isset($sale_or_receiving) && $sale_or_receiving == 'sale') {?>
				window.location = <?php echo json_encode(site_url('sales')); ?>;
			<?php } else { ?>
				window.location = <?php echo json_encode(site_url('receivings')); ?>;
			<?php } ?>
		}
	});
}

</script>
<?php echo form_close(); ?>
</div>
<?php $this->load->view('partial/footer'); ?>
