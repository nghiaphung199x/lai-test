<?php
require_once (APPPATH . "controllers/Items.php");

class BizItems extends Items 
{
	function __construct()
	{
		parent::__construct();
		$this->load->model('Receiving');
		$this->load->model('Item_location');
		$this->load->library('MySession');
		$this->load->model('Measure');
		$this->load->model('ItemMeasures');
		$this->load->helper('items');
	}
	
	public function measures($item_id) {
		$measuresConverted = $this->Measure->getAvailableMeasuresByItemId($item_id);
		$measureJsonFormat = array();
		foreach ($measuresConverted as $measure)
		{
			$measureJsonFormat[] = array('value' => $measure['id'], 'text' => $measure['name']);
		}
		echo json_encode($measureJsonFormat);
	}
	
	function save($item_id=-1)
	{
		$this->load->model('Item_taxes');
		$this->load->model('Item_location');
		$this->load->model('Item_location_taxes');
	
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
		
		// TODO XXX
		$measureId = $this->input->post('measure_id');
		$isMeasureConvert = $this->input->post('convert_measure');
		$measureData = array();
		if(!empty($isMeasureConvert)) {
			$measureData = $this->input->post('measure_converted');
		}
	
		$item_data = array(
				'name'=>$this->input->post('name'),
				'description'=>$this->input->post('description'),
				'tax_included'=>$this->input->post('tax_included') ? $this->input->post('tax_included') : 0,
				'category_id'=>$category_id,
				'measure_id'=>$measureId,
				'measure_converted'=>isset($isMeasureConvert) ? $isMeasureConvert : 0,
				'size'=>$this->input->post('size'),
				'expire_days'=>$this->input->post('expire_days') ?  $this->input->post('expire_days') : NULL,
				'supplier_id'=>$this->input->post('supplier_id')== -1 || $this->input->post('supplier_id') == '' ? null:$this->input->post('supplier_id'),
				'item_number'=>$this->input->post('item_number')=='' ? null:$this->input->post('item_number'),
				'product_id'=>$this->input->post('product_id')=='' ? null:$this->input->post('product_id'),
				'cost_price'=>$this->input->post('cost_price'),
				'change_cost_price' => $this->input->post('change_cost_price') ? $this->input->post('change_cost_price') : 0,
				'unit_price'=>$this->input->post('unit_price'),
				'promo_price'=>$this->input->post('promo_price') ? $this->input->post('promo_price') : NULL,
				'start_date'=>$this->input->post('start_date') ? date('Y-m-d', strtotime($this->input->post('start_date'))) : NULL,
				'end_date'=>$this->input->post('end_date') ?date('Y-m-d', strtotime($this->input->post('end_date'))) : NULL,
				'reorder_level'=>$this->input->post('reorder_level')!='' ? $this->input->post('reorder_level') : NULL,
				'is_service'=>$this->input->post('is_service') ? $this->input->post('is_service') : 0 ,
				'allow_alt_description'=>$this->input->post('allow_alt_description') ? $this->input->post('allow_alt_description') : 0 ,
				'is_serialized'=>$this->input->post('is_serialized') ? $this->input->post('is_serialized') : 0,
				'override_default_tax'=> $this->input->post('override_default_tax') ? $this->input->post('override_default_tax') : 0,
		);
	
		if ($this->input->post('override_default_commission'))
		{
			if ($this->input->post('commission_type') == 'fixed')
			{
				$item_data['commission_fixed'] = (float)$this->input->post('commission_value');
				$item_data['commission_percent_type'] = '';
				$item_data['commission_percent'] = NULL;
			}
			else
			{
				$item_data['commission_percent'] = (float)$this->input->post('commission_value');
				$item_data['commission_percent_type'] = $this->input->post('commission_percent_type');
				$item_data['commission_fixed'] = NULL;
			}
		}
		else
		{
			$item_data['commission_percent'] = NULL;
			$item_data['commission_fixed'] = NULL;
			$item_data['commission_percent_type'] = '';
		}
	
		$employee_id=$this->Employee->get_logged_in_employee_info()->person_id;
		$cur_item_info = $this->Item->get_info($item_id);
	
		$redirect=$this->input->post('redirect');
		$sale_or_receiving=$this->input->post('sale_or_receiving');
	
		if($this->Item->save($item_data,$item_id))
		{
			$this->Tag->save_tags_for_item(isset($item_data['item_id']) ? $item_data['item_id'] : $item_id, $this->input->post('tags'));
			$tier_type = $this->input->post('tier_type');
				
			if ($this->input->post('item_tier'))
			{
				foreach($this->input->post('item_tier') as $tier_id => $price_or_percent)
				{
					if ($price_or_percent)
					{
						$tier_data=array('tier_id'=>$tier_id);
						$tier_data['item_id'] = isset($item_data['item_id']) ? $item_data['item_id'] : $item_id;
	
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
							
						$this->Item->save_item_tiers($tier_data,$item_id);
					}
					else
					{
						$this->Item->delete_tier_price($tier_id, $item_id);
					}
	
				}
			}
				
				
			$success_message = '';
				
			//New item
			if($item_id==-1)
			{
				$success_message = lang('common_successful_adding').' '.$item_data['name'];
				$this->session->set_flashdata('manage_success_message', $success_message);
				echo json_encode(array('success'=>true,'message'=>$success_message,'item_id'=>$item_data['item_id'],'redirect' => $redirect, 'sale_or_receiving'=>$sale_or_receiving));
				$item_id = $item_data['item_id'];
			}
			else //previous item
			{
				$success_message = lang('common_items_successful_updating').' '.$item_data['name'];
				$this->session->set_flashdata('manage_success_message', $success_message);
				echo json_encode(array('success'=>true,'message'=>$success_message,'item_id'=>$item_id,'redirect' => $redirect, 'sale_or_receiving'=>$sale_or_receiving));
			}
				
			if ($this->input->post('additional_item_numbers') && is_array($this->input->post('additional_item_numbers')))
			{
				$this->Additional_item_numbers->save($item_id, $this->input->post('additional_item_numbers'));
			}
			else
			{
				$this->Additional_item_numbers->delete($item_id);
			}
				
			if ($this->input->post('locations'))
			{
				foreach($this->input->post('locations') as $location_id => $item_location_data)
				{
					$override_prices = isset($item_location_data['override_prices']) && $item_location_data['override_prices'];
					$quantity_add_minus = isset($item_location_data['quantity_add_minus']) && $item_location_data['quantity_add_minus'] ? $item_location_data['quantity_add_minus'] : 0;
						
					$item_location_before_save = $this->Item_location->get_info($item_id,$location_id);
					$new_quantity = ($item_location_before_save->quantity ? $item_location_before_save->quantity : 0) + $quantity_add_minus;
						
					$data = array(
							'location_id' => $location_id,
							'item_id' => $item_id,
							'location' => $item_location_data['location'],
							'cost_price' => $override_prices && $item_location_data['cost_price'] != '' ? $item_location_data['cost_price'] : NULL,
							'unit_price' => $override_prices && $item_location_data['unit_price'] != '' ? $item_location_data['unit_price'] : NULL,
							'promo_price' => $override_prices && $item_location_data['promo_price'] != '' ? $item_location_data['promo_price'] : NULL,
							'start_date' => $override_prices && $item_location_data['promo_price']!='' && $item_location_data['start_date'] != '' ? date('Y-m-d', strtotime($item_location_data['start_date'])) : NULL,
							'end_date' => $override_prices && $item_location_data['promo_price'] != '' && $item_location_data['end_date'] != '' ? date('Y-m-d', strtotime($item_location_data['end_date'])) : NULL,
							'quantity' => !$this->input->post('is_service')  ? $new_quantity : NULL,
							'reorder_level' => isset($item_location_data['reorder_level']) && $item_location_data['reorder_level'] != '' ? $item_location_data['reorder_level'] : NULL,
							'override_default_tax'=> isset($item_location_data['override_default_tax'] ) && $item_location_data['override_default_tax'] != '' ? $item_location_data['override_default_tax'] : 0,
					);
					$this->Item_location->save($data, $item_id,$location_id);
						
	
					if (isset($item_location_data['item_tier']))
					{
						$tier_type = $item_location_data['tier_type'];
	
						foreach($item_location_data['item_tier'] as $tier_id => $price_or_percent)
						{
							//If we are overriding prices and we have a price/percent, add..otherwise delete
							if ($override_prices && $price_or_percent)
							{
								$tier_data=array('tier_id'=>$tier_id);
								$tier_data['item_id'] = isset($item_data['item_id']) ? $item_data['item_id'] : $item_id;
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
	
								$this->Item_location->save_item_tiers($tier_data,$item_id, $location_id);
							}
							else
							{
								$this->Item_location->delete_tier_price($tier_id, $item_id, $location_id);
							}
	
						}
					}
						
	
					if (isset($item_location_data['tax_names']))
					{
						$location_items_taxes_data = array();
						$tax_names = $item_location_data['tax_names'];
						$tax_percents = $item_location_data['tax_percents'];
						$tax_cumulatives = $item_location_data['tax_cumulatives'];
						for($k=0;$k<count($tax_percents);$k++)
						{
							if (is_numeric($tax_percents[$k]))
							{
								$location_items_taxes_data[] = array('name'=>$tax_names[$k], 'percent'=>$tax_percents[$k], 'cumulative' => isset($tax_cumulatives[$k]) ? $tax_cumulatives[$k] : '0' );
							}
						}
						$this->Item_location_taxes->save($location_items_taxes_data, $item_id, $location_id);
					}
						
					if (isset($item_location_data['quantity_add_minus']) && $item_location_data['quantity_add_minus'] && !$this->input->post('is_service'))
					{
						$inv_data = array
						(
								'trans_date'=>date('Y-m-d H:i:s'),
								'trans_items'=>$item_id,
								'trans_user'=>$employee_id,
								'trans_comment'=>lang('items_manually_editing_of_quantity'),
								'trans_inventory'=>$item_location_data['quantity_add_minus'],
								'location_id' => $location_id,
						);
						$this->Inventory->insert($inv_data);
					}
				}
			}
			$items_taxes_data = array();
			$tax_names = $this->input->post('tax_names');
			$tax_percents = $this->input->post('tax_percents');
			$tax_cumulatives = $this->input->post('tax_cumulatives');
			for($k=0;$k<count($tax_percents);$k++)
			{
				if (is_numeric($tax_percents[$k]))
				{
					$items_taxes_data[] = array('name'=>$tax_names[$k], 'percent'=>$tax_percents[$k], 'cumulative' => isset($tax_cumulatives[$k]) ? $tax_cumulatives[$k] : '0' );
				}
			}
			$this->Item_taxes->save($items_taxes_data, $item_id);
				
				
			//Delete Image
			if($this->input->post('del_image') && $item_id != -1)
			{
				if($cur_item_info->image_id != null)
				{
					$this->load->model('Appfile');
					$this->Item->update_image(NULL,$item_id);
					$this->Appfile->delete($cur_item_info->image_id);
				}
			}
	
			//Save Image File
			if(!empty($_FILES["image_id"]) && $_FILES["image_id"]["error"] == UPLOAD_ERR_OK)
			{
				$allowed_extensions = array('png', 'jpg', 'jpeg', 'gif');
				$extension = strtolower(pathinfo($_FILES["image_id"]["name"], PATHINFO_EXTENSION));
	
				if (in_array($extension, $allowed_extensions))
				{
					$config['image_library'] = 'gd2';
					$config['source_image']	= $_FILES["image_id"]["tmp_name"];
					$config['create_thumb'] = FALSE;
					$config['maintain_ratio'] = TRUE;
					$config['width']	 = 400;
					$config['height']	= 300;
					$this->load->library('image_lib', $config);
					$this->image_lib->resize();
					$this->load->model('Appfile');
						
					$image_file_id = $this->Appfile->save($_FILES["image_id"]["name"], file_get_contents($_FILES["image_id"]["tmp_name"]));
				}
	
				$this->Item->update_image($image_file_id,$item_id);
			}
			
			// TODO XXX
			$this->ItemMeasures->deleteByItemId($item_id);
			if(!empty($measureData)) {
				foreach ($measureData as $measureConverted)
				{
					$itemMeasure = array(
						'item_id' => $item_id,
						'measure_id' => $measureId,
						'measure_converted_id' => $measureConverted['id'],
						'measure_converted_id' => $measureConverted['id'],
						'qty_converted' => $measureConverted['qty'],
						'cost_price_percentage_converted' => $measureConverted['cost_price'],
						'unit_price_percentage_converted' => $measureConverted['unit_price'],
					);
					
					$this->ItemMeasures->save($itemMeasure);
				}
				
			}
		}
		else //failure
		{
			echo json_encode(array('success'=>false,'message'=>lang('common_error_adding_updating').' '.
					$item_data['name'],'item_id'=>-1));
		}
	
	}
	
	function _get_item_data($item_id)
	{
		$this->load->helper('report');
	
		$data = array();
		$data['controller_name']=$this->_controller_name;
	
		$data['item_info']=$this->Item->get_info($item_id);
		
		$data['item_info']->measures_converted = $this->ItemMeasures->getMeasuresByItemId($item_id);
	
		$data['categories'][''] = lang('common_select_category');
	
		$categories = $this->Category->sort_categories_and_sub_categories($this->Category->get_all_categories_and_sub_categories());
		foreach($categories as $key=>$value)
		{
			$name = str_repeat('&nbsp;&nbsp;', $value['depth']).$value['name'];
			$data['categories'][$key] = $name;
		}
	
		$data['tags'] = implode(',',$this->Tag->get_tags_for_item($item_id));
	
		$data['measures'] = array();
		$data['measures']['-1'] = '--- Chọn Đơn Vị Tính ---';
		$measures = $this->Measure->get_all();
		foreach($measures as $key=>$measure)
		{
			$data['measures'][$key] = $measure['name'];
		}
	
		$data['item_tax_info']=$this->Item_taxes->get_info($item_id);
		$data['tiers']=$this->Tier->get_all()->result();
		$data['locations'] = array();
		$data['location_tier_prices'] = array();
		$data['additional_item_numbers'] = $this->Additional_item_numbers->get_item_numbers($item_id);
	
		if ($item_id != -1)
		{
			$data['next_item_id'] = $this->Item->get_next_id($item_id);
			$data['prev_item_id'] = $this->Item->get_prev_id($item_id);;
		}
			
		foreach($this->Location->get_all()->result() as $location)
		{
			if($this->Employee->is_location_authenticated($location->location_id))
			{
				$data['locations'][] = $location;
				$data['location_items'][$location->location_id] = $this->Item_location->get_info($item_id,$location->location_id);
				$data['location_taxes'][$location->location_id] = $this->Item_location_taxes->get_info($item_id, $location->location_id);
	
				foreach($data['tiers'] as $tier)
				{
					$tier_prices = $this->Item_location->get_tier_price_row($tier->id,$data['item_info']->item_id, $location->location_id);
					if (!empty($tier_prices))
					{
						$data['location_tier_prices'][$location->location_id][$tier->id] = $tier_prices;
					}
					else
					{
						$data['location_tier_prices'][$location->location_id][$tier->id] = FALSE;
					}
				}
			}
	
		}
	
	
		if ($item_id == -1)
		{
			$suppliers = array(''=> lang('common_not_set'), '-1' => lang('common_none'));
		}
		else
		{
			$suppliers = array('-1' => lang('common_none'));
		}
		foreach($this->Supplier->get_all()->result_array() as $row)
		{
			$suppliers[$row['person_id']] = $row['company_name'] .' ('.$row['first_name'] .' '. $row['last_name'].')';
		}
	
		$data['tier_prices'] = array();
		$data['tier_type_options'] = array('unit_price' => lang('common_fixed_price'), 'percent_off' => lang('common_percent_off'));
		foreach($data['tiers'] as $tier)
		{
			$tier_prices = $this->Item->get_tier_price_row($tier->id,$data['item_info']->item_id);
	
			if (!empty($tier_prices))
			{
				$data['tier_prices'][$tier->id] = $tier_prices;
			}
			else
			{
				$data['tier_prices'][$tier->id] = FALSE;
			}
		}
	
		$data['suppliers']=$suppliers;
		$data['selected_supplier'] = $this->Item->get_info($item_id)->supplier_id;
	
		$decimals = $this->Appconfig->get_raw_number_of_decimals();
		$decimals = $decimals !== NULL && $decimals!= '' ? $decimals : 2;
		$data['decimals'] = $decimals;
	
		return $data;
	}
	
	function manage_measures()
	{
		// $this->check_action_permission('manage_measures');
		$measures = $this->Measure->get_all();
		$data = array('measures' => $measures, 'measure_list' => $this->_measureList());
		$this->load->view('items/measures',$data);
	}
	
	function _measureList()
	{
		$measures = $this->Measure->get_all();
		$return = '<ul>';
		foreach($measures as $measureId => $measure)
		{
			$return .='<li>'.$measure['name'].
			'<a href="javascript:void(0);" class="edit_measure" data-name = "'.H($measure['name']).'" data-measure_id="'.$measureId.'">['.lang('common_edit').']</a> '.
			'<a href="javascript:void(0);" class="delete_measure" data-measure_id="'.$measureId.'">['.lang('common_delete').']</a> ';
			$return .='</li>';
		}
		$return .='</ul>';
	
		return $return;
	}
	
	function saveMeasure($measureId = FALSE)
	{
		// $this->check_action_permission('manage_tags');
		$measureName = $this->input->post('measure_name');
	
		if ($this->Measure->save($measureName, $measureId))
		{
			echo json_encode(array('success'=>true,'message'=>lang('items_tag_successful_adding').' '.$measureName));
		}
		else
		{
			echo json_encode(array('success'=>false,'message'=>lang('items_tag_successful_error')));
		}
	}
	
	function deleteMeasure()
	{
		// $this->check_action_permission('manage_tags');
		$measureId = $this->input->post('measure_id');
		if($this->Measure->delete($measureId))
		{
			echo json_encode(array('success'=>true,'message'=>lang('items_successful_deleted')));
		}
		else
		{
			echo json_encode(array('success'=>false,'message'=>lang('items_cannot_be_deleted')));
		}
	}
	
	function measureList()
	{
		echo $this->_measureList();
	}
	
	public function showNotAudit() {
		$response = array('success' => 1);
		$data = array();
		$count_id = $this->input->post('count_id', 0);;
		$data['audit_items'] = $this->Inventory->get_items_counted($count_id, NULL,NULL);
		$auditedIds = array_map(function($item) {
			return $item['item_id'];
		}, $data['audit_items']);
		$extra['category_id'] = (int) $this->mysession->getValue('AUDIT_CATEGORY');
		$data['notAuditedItems'] = $this->Item->getNotAuditedInLocation($auditedIds, $extra);
		$response['html'] = $this->load->view('items/partials/not_audited', $data, TRUE);
		echo json_encode($response);
	}
	
	function item_search()
	{
		//allow parallel searchs to improve performance.
		$extra['category_id'] = (int) $this->mysession->getValue('AUDIT_CATEGORY');
		$extra['by_current_location'] = true;
		session_write_close();
		$suggestions = $this->Item->get_item_search_suggestions($this->input->get('term'),100, $extra);
		echo json_encode($suggestions);
	}
	
	function do_count($count_id, $offset = 0)
	{
		$this->check_action_permission('count_inventory');
		$this->session->set_userdata('current_count_id',$count_id);
	
		$data = array();
		$config = array();
		$config['base_url'] = site_url("items/do_count/$count_id");
		$config['per_page'] = $this->config->item('number_of_items_per_page') ? (int)$this->config->item('number_of_items_per_page') : 20;
		$config['total_rows'] = $this->Inventory->get_number_of_items_counted($count_id);
		$config['uri_segment'] = 4;
		$data['per_page'] = $config['per_page'];
		$data['count_id'] = $count_id;
	
		$data['total_rows'] = $config['total_rows'];
		$this->load->library('pagination');$this->pagination->initialize($config);
		$data['pagination'] = $this->pagination->create_links();
		$data['count_info'] = $this->Inventory->get_count_info($count_id);
	
		$data['items_counted'] = $this->Inventory->get_items_counted($count_id,$config['per_page'], $offset);
		$data['mode'] = $this->session->userdata('count_mode') ? $this->session->userdata('count_mode') : 'scan_and_set';
		$data['modes'] = array('scan_and_set' => lang('items_scan_and_set'), 'scan_and_add' => lang('items_scan_and_add') );
		
		$categories = $this->Category->get_all_categories_and_sub_categories();
		
		$data['categories'] = array_map(function($item){
			return $item['name'];
		}, $categories);
		$data['categories']['all'] = 'Tất cả';
		$data['selected_category'] = 'all';
		$this->load->view('items/do_count', $data);
	}
	
	public function setCategory()
	{
		$response = array('success' => 1);
		$categoryId = $this->input->post('category_id', 0);
		$this->mysession->setValue('AUDIT_CATEGORY', $categoryId);
		echo json_encode($response);
	}
	
	public function clear_low_inventory() {
		$params = $this->session->userdata('item_search_data') ? $this->session->userdata('item_search_data') : array();
		$params['low_inventory'] = 0;
		$this->session->set_userdata("item_search_data", $params);
		redirect('items');
	}
	
	function index($offset=0)
	{
		$params = $this->session->userdata('item_search_data') ? $this->session->userdata('item_search_data') : array('offset' => 0, 'order_col' => 'name', 'order_dir' => 'asc', 'search' => FALSE, 'category_id' => FALSE, 'fields' => 'all');
		if ($offset!=$params['offset'])
		{
			redirect('items/index/'.$params['offset']);
		}
	
		$this->check_action_permission('search');
		$config['base_url'] = site_url('items/sorting');
		$config['per_page'] = $this->config->item('number_of_items_per_page') ? (int)$this->config->item('number_of_items_per_page') : 20;
	
		$data['controller_name']=$this->_controller_name;
		$data['per_page'] = $config['per_page'];
		$data['search'] = $params['search'] ? $params['search'] : "";
		$data['category_id'] = $params['category_id'] ? $params['category_id'] : "";
		$data['categories'][''] = lang('common_all');
		$categories = $this->Category->sort_categories_and_sub_categories($this->Category->get_all_categories_and_sub_categories());
		foreach($categories as $key=>$value)
		{
			$name = str_repeat('&nbsp;&nbsp;', $value['depth']).$value['name'];
			$data['categories'][$key] = $name;
		}
	
		$data['fields'] = $params['fields'] ? $params['fields'] : "all";
	
		if ($data['search'] || $data['category_id'])
		{
			$config['total_rows'] = $this->Item->search_count_all($data['search'], $data['category_id'],10000, $data['fields']);
			$allItems = $this->Item->getSearchAll($data['search'], $data['category_id'],10000, $data['fields']);
			$table_data = $this->Item->search($data['search'],$data['category_id'],$data['per_page'],$params['offset'],$params['order_col'],$params['order_dir'], $data['fields']);
		}
		else
		{
			$config['total_rows'] = $this->Item->count_all();
			$allItems = $this->Item->get_all();
			$table_data = $this->Item->get_all($data['per_page'],$params['offset'],$params['order_col'],$params['order_dir']);
		}
		
		$countLowInventory = 0;
		$items = array();
		foreach($allItems->result() as $item)
		{
			$reorder_level = $item->location_reorder_level ? $item->location_reorder_level : $item->reorder_level;
			if($item->quantity !== NULL && ($item->quantity<=0 || $item->quantity <= $reorder_level))
			{
				$countLowInventory++;
				$items[] = $item;
			}
		}
		$data['countLowInventory'] = $countLowInventory;
		$data['$params'] = $params;
		
		$data['total_rows'] = $config['total_rows'];
		$this->load->library('pagination');$this->pagination->initialize($config);
		$data['pagination'] = $this->pagination->create_links();
		$data['order_col'] = $params['order_col'];
		$data['order_dir'] = $params['order_dir'];
		if ($params['low_inventory'] == 1) {
			$data['manage_table']=get_items_manage_table($items,$this, true);
		} else {
			$data['manage_table']=get_items_manage_table($table_data,$this);
		}
		$data['low_inventory'] = (isset($params['low_inventory']) && $params['low_inventory']) == 1 ? true : false;
		
		$this->load->view('items/manage',$data);
	}
	
	public function qty_location()
	{
		$response = array('success' => 1);
		$data = array();
		$itemId = $this->input->post('item_id', 0);
		$data['qty_locations'] = getItemConvertedQtyAllLocation($itemId);
		
		$response['html'] = $this->load->view('items/partials/qty_location', $data, TRUE);
		echo json_encode($response);
	}

	public function transfer_pending()
	{
		$data = array();
		$data['transferings'] = $this->Receiving->getAllTransferings();
		$this->load->view('items/transferings', $data);
	}
	
	public function delete_transfer()
	{
		$response = array('success' => 1);
		$recId = $this->input->post('rec_id', 0);
		$this->Receiving->removeTransferPending($recId, $this->Employee->get_logged_in_employee_info()->person_id);
		echo json_encode($response);
	}
	
	public function approve_transfer()
	{
		$response = array('success' => 1);
	
		$recId = $this->input->post('rec_id', 0);
		$this->receiving_lib->clear_all();
		$this->receiving_lib->copy_entire_receiving($recId);
	
		$recInfo = $this->Receiving->get_info($recId)->row_array();
	
		$data['cart']=$this->receiving_lib->get_cart();
		if (empty($data['cart']))
		{
			$response['success'] = 0;
		}
	
		$supplier_id=$recInfo['supplier_id'];
		$location_to_id=$recInfo['transfer_to_location_id'];
		$location_from_id=$recInfo['location_id'];
		$employee_id=$recInfo['employee_id'];
		$comment = $recInfo['comment'];
		$payment_type = $recInfo['payment_type'];
	
		$recId = $this->Receiving->approvedTransfer(
				$data['cart'],
				$supplier_id,
				$employee_id,
				$comment,
				$payment_type,
				$recId,
				$recInfo['receiving_time'],
				0,
				$location_from_id
				);
	
		if($supplier_id!=-1)
		{
			$suppl_info=$this->Supplier->get_info($supplier_id);
		}
	
		if($recId > 0 && $this->receiving_lib->get_email_receipt() && !empty($suppl_info->email))
		{
			$this->load->library('email');
			$config['mailtype'] = 'html';
			$this->email->initialize($config);
			$this->email->from($this->Location->get_info_for_key('email') ? $this->Location->get_info_for_key('email') : 'no-reply@mg.4biz.vn', $this->config->item('company'));
			$this->email->to($suppl_info->email);
	
			$this->email->subject(lang('receivings_receipt'));
			$this->email->message($this->load->view("receivings/receipt_email",$data, true));
			$this->email->send();
		}
		$this->receiving_lib->clear_all();
		echo json_encode($response);
	}
	
	function finish_count($update_inventory = 0)
	{
		$this->check_action_permission('count_inventory');
	
		$count_id = $this->session->userdata('current_count_id');
	
		if ($update_inventory && $this->Employee->has_module_action_permission('items','edit_quantity', $this->Employee->get_logged_in_employee_info()->person_id))
		{
			$this->Inventory->update_inventory_from_count($count_id);
			
			$data['audit_items'] = $this->Inventory->get_items_counted($count_id, NULL,NULL);
			$data['create_datetime'] = date(get_date_format().' '.get_time_format(), strtotime());
			$data['count_id'] = $count_id;
			$this->load->view("items/audit",$data);
		} else {
			$this->Inventory->set_count($count_id, 'closed');
			redirect('items/count');
		}
	}
	
	function low_inventory()
	{
		$this->check_action_permission('search');
		$search=$this->input->post('search');
		$category_id = $this->input->post('category_id');
		$offset = $this->input->post('offset') ? $this->input->post('offset') : 0;
		$order_col = $this->input->post('order_col') ? $this->input->post('order_col') : 'name';
		$order_dir = $this->input->post('order_dir') ? $this->input->post('order_dir'): 'asc';
		$fields = $this->input->post('fields') ? $this->input->post('fields') : 'all';
		$per_page=$this->config->item('number_of_items_per_page') ? (int)$this->config->item('number_of_items_per_page') : 20;
		
		$item_search_data = array(
				'offset' => $offset,
				'order_col' => $order_col, 
				'order_dir' => $order_dir, 
				'search' => $search, 
				'category_id' => $category_id, 
				'fields' => $fields, 
				'low_inventory' => 1);
		
		$this->session->set_userdata("item_search_data",$item_search_data);
		
		
		$params = $this->session->userdata('item_search_data') ? $this->session->userdata('item_search_data') : array();
		
		$allItems=$this->Item->search(
			$search, 
			$category_id, 
			$per_page,
			$offset, 
			$order_col,
			$order_dir, 
			$fields
		);
		
		$countItems = 0;
		$countLowInventory = 0;
		$items = array();
		foreach($allItems->result() as $item)
		{
			$reorder_level = $item->location_reorder_level ? $item->location_reorder_level : $item->reorder_level;
			if($item->quantity !== NULL && ($item->quantity<=0 || $item->quantity <= $reorder_level))
			{
				$items[] = $item;
				$countLowInventory ++;
			}
			$countItems ++;
		}
		
		$config['base_url'] = site_url('items/search');
		$config['per_page'] = $per_page ;
		$this->load->library('pagination');
		$this->pagination->initialize($config);
		$data['pagination'] = $this->pagination->setTotalRows($countLowInventory)->create_links();
		$data['manage_table']=get_items_manage_table_data_rows_with_array($items,$this);
		echo json_encode(array('manage_table' => $data['manage_table'], 'pagination' => $data['pagination'], 'count_items' => $countItems));
	}
	
	function search()
	{
		$this->check_action_permission('search');
		$search=$this->input->post('search');
		$category_id = $this->input->post('category_id');
		$offset = $this->input->post('offset') ? $this->input->post('offset') : 0;
		$order_col = $this->input->post('order_col') ? $this->input->post('order_col') : 'name';
		$order_dir = $this->input->post('order_dir') ? $this->input->post('order_dir'): 'asc';
		$fields = $this->input->post('fields') ? $this->input->post('fields') : 'all';
	
		$params = $this->session->userdata('item_search_data') ? $this->session->userdata('item_search_data') : array();
		$item_search_data = array(
				'offset' => $offset, 
				'order_col' => $order_col, 
				'order_dir' => $order_dir, 
				'search' => $search,  
				'category_id' => $category_id, 
				'fields' => $fields, 'low_inventory' => $params['low_inventory']);
		$this->session->set_userdata("item_search_data",$item_search_data);
		$per_page=$this->config->item('number_of_items_per_page') ? (int)$this->config->item('number_of_items_per_page') : 20;
		$search_data=$this->Item->search($search, $category_id, $per_page,$this->input->post('offset') ? $this->input->post('offset') : 0, $this->input->post('order_col') ? $this->input->post('order_col') : 'name' ,$this->input->post('order_dir') ? $this->input->post('order_dir'): 'asc', $fields);
		$config['base_url'] = site_url('items/search');
		$config['total_rows'] = $this->Item->search_count_all($search, $category_id,10000, $fields);
		$config['per_page'] = $per_page ;
	
		$totalQty = 0;
		$totalQtyAllLoc = 0;
		
		$countLowInventory = 0;
		$items = array();
		foreach($search_data->result() as $item)
		{
			$reorder_level = $item->location_reorder_level ? $item->location_reorder_level : $item->reorder_level;
			if($item->quantity !== NULL && ($item->quantity<=0 || $item->quantity <= $reorder_level))
			{
				$items[] = $item;
				$countLowInventory ++;
			}
			
			$totalQty += (int) $item->quantity;
			$totalQtyAllLoc += (int) $this->Item->getTotalInAllLocation($item->item_id);
		}
		
		if ($params['low_inventory'] === 1) {
			$data['manage_table']=get_items_manage_table_data_rows_with_array($items,$this);
		} else {
			$data['manage_table']=get_items_manage_table_data_rows($search_data,$this);
		}
		
		$this->load->library('pagination');$this->pagination->initialize($config);
		$data['pagination'] = $this->pagination->create_links();
		echo json_encode(array(
			'manage_table' => $data['manage_table'], 
			'pagination' => $data['pagination'], 
			'count_items' => $search_data->num_rows(), 
			'count_low_inventory' => $countLowInventory,
			'totalQty' => $totalQty,
			'totalQtyAllLoc' => $totalQtyAllLoc,
			)
		);
	}
}
?>
