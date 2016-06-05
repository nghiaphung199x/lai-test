<?php
require_once (APPPATH . "controllers/Item_kits.php");
class BizItem_kits extends Item_kits
{
	function __construct()
	{
		parent::__construct();
		$this->load->model('Measure');
	}
	public function count_available_kits()
	{
		$items = $this->input->post('items');
		echo json_encode(array('success' => 1, 'available_kits' => $this->Item_kit->countAvailableKits($items)));
	}
	
	function item_search()
	{
		$this->load->model('Item');
		$this->load->model('ItemMeasures');
		//allow parallel searchs to improve performance.
		session_write_close();
		$suggestions = $this->Item->get_item_search_suggestions($this->input->get('term'),100);
		$this->load->model('Item_location');
		foreach ($suggestions as &$item) {
			$item['qty'] = (int) $this->Item_location->get_location_quantity($item['value']);
			$measures = $this->Measure->getAvailableMeasuresByItemId($item['value']);
			$item['measures'] = null;
			if( !empty($measures) ) {
				foreach($measures as $measure)
				{
					$item['measures'][$measure['id']] = $measure['name'];
				}
			} else {
				$item['measures']['-1'] = 'Chưa thiết lập';
			}
		}
		
		echo json_encode($suggestions);
	}
	
	function view($item_kit_id=-1,$redirect=0)
	{
		$this->load->model('Item_kit_items');
		$this->load->model('Item_kit_taxes');
		$this->load->model('Tier');
		$this->load->model('Item');
		$this->load->model('Item_kit_location');
		$this->load->model('Item_kit_location_taxes');
		$this->load->model('Supplier');
		$this->load->model('Item_kit_taxes_finder');
		$this->load->model('Item_location');
	
		$this->check_action_permission('add_update');
		$data = $this->_get_item_kit_data($item_kit_id);
		$data['redirect']=$redirect;
		
		$this->load->view("item_kits/form",$data);
	}
	
	function save($item_kit_id=-1)
	{
		$this->load->model('Item_kit_taxes');
		$this->load->model('Item_kit_items');
		$this->load->model('Item_kit_location');
		$this->load->model('Item_kit_location_taxes');
	
		$this->check_action_permission('add_update');
	
		if (!$this->Category->exists($this->input->post('category_id')))
		{
			if (!$category_id = $this->Category->get_category_id($this->input->post('category_id')))
			{
				$category_id = $this->Category->save($this->input->post('category_id'));
			}
		}
		else
		{
			$category_id = $this->input->post('category_id');
		}
			
		$item_kit_data = array(
				'item_kit_number'=>$this->input->post('item_kit_number')=='' ? null:$this->input->post('item_kit_number'),
				'product_id'=>$this->input->post('product_id')=='' ? null:$this->input->post('product_id'),
				'name'=>$this->input->post('name'),
				'category_id'=>$category_id,
				'tax_included'=>$this->input->post('tax_included') ? $this->input->post('tax_included') : 0,
				'unit_price'=>$this->input->post('unit_price')=='' ? null:$this->input->post('unit_price'),
				'cost_price'=>$this->input->post('cost_price')=='' ? null:$this->input->post('cost_price'),
				'change_cost_price' => $this->input->post('change_cost_price') ? $this->input->post('change_cost_price') : 0,
				'description'=>$this->input->post('description'),
				'override_default_tax'=> $this->input->post('override_default_tax') ? $this->input->post('override_default_tax') : 0,
		);
	
		if ($this->input->post('override_default_commission'))
		{
			if ($this->input->post('commission_type') == 'fixed')
			{
				$item_kit_data['commission_fixed'] = (float)$this->input->post('commission_value');
				$item_kit_data['commission_percent_type'] = '';
				$item_kit_data['commission_percent'] = NULL;
			}
			else
			{
				$item_kit_data['commission_percent'] = (float)$this->input->post('commission_value');
				$item_kit_data['commission_percent_type'] = $this->input->post('commission_percent_type');
				$item_kit_data['commission_fixed'] = NULL;
			}
		}
		else
		{
			$item_kit_data['commission_percent'] = NULL;
			$item_kit_data['commission_fixed'] = NULL;
			$item_kit_data['commission_percent_type'] = '';
		}
	
		$redirect=$this->input->post('redirect');
	
		if($this->Item_kit->save($item_kit_data,$item_kit_id))
		{
				
			$this->Tag->save_tags_for_item_kit(isset($item_kit_data['item_kit_id']) ? $item_kit_data['item_kit_id'] : $item_kit_id, $this->input->post('tags'));
				
			$tier_type = $this->input->post('tier_type');
				
			if ($this->input->post('item_kit_tier'))
			{
				foreach($this->input->post('item_kit_tier') as $tier_id => $price_or_percent)
				{
					if ($price_or_percent)
					{
						$tier_data=array('tier_id'=>$tier_id);
						$tier_data['item_kit_id'] = isset($item_kit_data['item_kit_id']) ? $item_kit_data['item_kit_id'] : $item_kit_id;
	
						if ($tier_type[$tier_id] == 'unit_price')
						{
							$tier_data['unit_price'] = $price_or_percent;
							$tier_data['percent_off'] = NULL;
						}
						else
						{
							$tier_data['percent_off'] = (float)$price_or_percent;
							$tier_data['unit_price'] = NULL;
						}
							
						$this->Item_kit->save_item_tiers($tier_data,$item_kit_id);
					}
					else
					{
						$this->Item_kit->delete_tier_price($tier_id, $item_kit_id);
					}
				}
			}
	
			$success_message = '';
			//New item kit
			if($item_kit_id==-1)
			{
				$success_message = lang('item_kits_successful_adding').' '.$item_kit_data['name'];
				echo json_encode(array('success'=>true,'message'=>$success_message,'item_kit_id'=>$item_kit_data['item_kit_id'],'redirect'=>$redirect));
				$item_kit_id = $item_kit_data['item_kit_id'];
			}
			else //previous item
			{
				$success_message = lang('item_kits_successful_updating').' '.$item_kit_data['name'];
				$this->session->set_flashdata('manage_success_message', $success_message);
				echo json_encode(array('success'=>true,'message'=>$success_message,'item_kit_id'=>$item_kit_id,'redirect'=>$redirect));
			}
				
				
			if ($this->input->post('locations'))
			{
				foreach($this->input->post('locations') as $location_id => $item_kit_location_data)
				{
					$override_prices = isset($item_kit_location_data['override_prices']) && $item_kit_location_data['override_prices'];
	
					$data = array(
							'location_id' => $location_id,
							'item_kit_id' => $item_kit_id,
							'cost_price' => $override_prices && $item_kit_location_data['cost_price'] != '' ? $item_kit_location_data['cost_price'] : NULL,
							'unit_price' => $override_prices && $item_kit_location_data['unit_price'] != '' ? $item_kit_location_data['unit_price'] : NULL,
							'override_default_tax'=> isset($item_kit_location_data['override_default_tax'] ) && $item_kit_location_data['override_default_tax'] != '' ? $item_kit_location_data['override_default_tax'] : 0,
					);
					$this->Item_kit_location->save($data, $item_kit_id,$location_id);
						
	
					if (isset($item_kit_location_data['item_tier']))
					{
						$tier_type = $item_kit_location_data['tier_type'];
	
						foreach($item_kit_location_data['item_tier'] as $tier_id => $price_or_percent)
						{
							//If we are overriding prices and we have a price/percent, add..otherwise delete
							if ($override_prices && $price_or_percent)
							{
								$tier_data=array('tier_id'=>$tier_id);
								$tier_data['item_kit_id'] = isset($item_data['item_kit_id']) ? $item_data['item_kit_id'] : $item_kit_id;
								$tier_data['location_id'] = $location_id;
									
								if ($tier_type[$tier_id] == 'unit_price')
								{
									$tier_data['unit_price'] = $price_or_percent;
									$tier_data['percent_off'] = NULL;
								}
								else
								{
									$tier_data['percent_off'] = (float)$price_or_percent;
									$tier_data['unit_price'] = NULL;
								}
	
								$this->Item_kit_location->save_item_tiers($tier_data,$item_kit_id, $location_id);
							}
							else
							{
								$this->Item_kit_location->delete_tier_price($tier_id, $item_kit_id, $location_id);
							}
	
						}
					}
						
					$location_items_taxes_data = array();
	
					$tax_names = $item_kit_location_data['tax_names'];
					$tax_percents = $item_kit_location_data['tax_percents'];
					$tax_cumulatives = $item_kit_location_data['tax_cumulatives'];
					for($k=0;$k<count($tax_percents);$k++)
					{
						if (is_numeric($tax_percents[$k]))
						{
							$location_items_taxes_data[] = array('name'=>$tax_names[$k], 'percent'=>$tax_percents[$k], 'cumulative' => isset($tax_cumulatives[$k]) ? $tax_cumulatives[$k] : '0' );
						}
					}
					$this->Item_kit_location_taxes->save($location_items_taxes_data, $item_kit_id, $location_id);
				}
			}
				
			if ($this->input->post('item_kit_item'))
			{
				$measures = $this->input->post('item_kit_measue');
				
				$item_kit_items = array();
				foreach($this->input->post('item_kit_item') as $item_id => $quantity)
				{
					$item_kit_items[] = array(
							'item_id' => $item_id,
							'quantity' => $quantity,
							'measure_id' => (isset($measures[$item_id]) && $measures[$item_id] > 0) ? $measures[$item_id] : null
					);
				}
					
				$this->Item_kit_items->save($item_kit_items, $item_kit_id);
			}
				
			$item_kits_taxes_data = array();
			$tax_names = $this->input->post('tax_names');
			$tax_percents = $this->input->post('tax_percents');
			$tax_cumulatives = $this->input->post('tax_cumulatives');
			for($k=0;$k<count($tax_percents);$k++)
			{
				if (is_numeric($tax_percents[$k]))
				{
					$item_kits_taxes_data[] = array('name'=>$tax_names[$k], 'percent'=>$tax_percents[$k], 'cumulative' => isset($tax_cumulatives[$k]) ? $tax_cumulatives[$k] : '0' );
				}
			}
			$this->Item_kit_taxes->save($item_kits_taxes_data, $item_kit_id);
		}
		else//failure
		{
			echo json_encode(array('success'=>false,'message'=>lang('item_kits_error_adding_updating').' '.
					$item_kit_data['name'],'item_kit_id'=>-1));
		}
	
	}
}
?>
