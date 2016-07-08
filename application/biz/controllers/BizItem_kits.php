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
				$item['measures']['-1'] = lang('common_not_set');
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
		$this->load->model('Attribute_set');
		$this->load->model('Attribute_group');
		$this->load->model('Attribute');

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
				'attribute_set_id'=>$this->input->post('attribute_set_id')=='' ? null:$this->input->post('attribute_set_id'),
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
            /* Update Extended Attributes */
            if (!class_exists('Attribute')) {
                $this->load->model('Attribute');
            }
            $attributes = $this->input->post('attributes');
            if (!empty($attributes)) {
                $this->Attribute->reset_attributes(array('entity_id' => $item_kit_id, 'entity_type' => 'item_kits'));
                foreach ($attributes as $attribute_id => $value) {
                    $attribute_value = array('entity_id' => $item_kit_id, 'entity_type' => 'item_kits', 'attribute_id' => $attribute_id, 'entity_value' => $value);
                    $this->Attribute->set_attributes($attribute_value);
                }
            }
            /* End Update */

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

    /**
     * @Loads the form for excel import
     */
    function excel_import()
    {
        $this->check_action_permission('add_update');
        $this->load->view("item_kits/excel_import", null);
    }

    /**
     * @Loads the form for excel import
     */
    function do_excel_import()
    {
        $this->check_action_permission('add_update');
        $this->load->helper('demo');
        if (is_on_demo_host()) {
            $msg = lang('common_excel_import_disabled_on_demo');
            echo json_encode(array('success' => false, 'message' => $msg));
            return;
        }
        if ($_FILES['file_path']['error'] != UPLOAD_ERR_OK) {
            $msg = lang('common_excel_import_failed');
            echo json_encode(array('success' => false, 'message' => $msg));
            return;
        } else {
            if (($handle = fopen($_FILES['file_path']['tmp_name'], "r")) !== FALSE) {
                $this->load->helper('spreadsheet');
                $objPHPExcel = file_to_obj_php_excel($_FILES['file_path']['tmp_name']);
                $end_column = $objPHPExcel->setActiveSheetIndex(0)->getHighestColumn();
                $this->load->model('Attribute_set');
                $data['attribute_sets'] = $this->Attribute_set->get_by_related_object('item_kits');
                $data['sheet'] = $objPHPExcel->getActiveSheet();
                $data['num_rows'] = $objPHPExcel->setActiveSheetIndex(0)->getHighestRow();
                $data['columns'] = range('A', $end_column);
                $data['fields'] = $this->Item_kit->get_import_fields();
                $html = $this->load->view('item_kits/import/result', $data, true);
                $result = array('success' => true, 'message' => lang('item_kits_import_success'), 'html' => $html);
                echo json_encode($result);
                return;
            } else {
                echo json_encode(array('success' => false, 'message' => lang('common_upload_file_not_supported_format')));
                return;
            }
        }
        $result = array('success' => true, 'message' => lang('item_kits_import_success'));
        echo json_encode($result);
    }

    /*
     * Import Real Data
     **/
    public function action_import_data() {
        $this->check_action_permission('add_update');
        $this->load->helper('demo');
        if (is_on_demo_host()) {
            $msg = lang('common_excel_import_disabled_on_demo');
            echo json_encode(array('success' => false, 'message' => $msg));
            return;
        }
        $this->load->model('Attribute');
        $entity_type = 'item_kits';
        $check_duplicate_field = $this->input->post('check_duplicate_field');
        $field_parts = explode(':', $check_duplicate_field);
        if (count($field_parts) == 2) {
            $check_duplicate_field_type =  $field_parts[0];
            $check_duplicate_field_name =  $field_parts[1];
        }
        $attribute_set_id = $this->input->post('attribute_set_id');
        $columns = $this->input->post('columns');
        $rows = $this->input->post('rows');
        $selected_rows = $this->input->post('selected_rows');
        if (empty($rows) || empty($selected_rows)) {
            $msg = lang('common_error');
            echo json_encode(array('success' => false, 'message' => $msg));
            return;
        }
        $stored_rows = 0;
        $error_rows = array();
        foreach ($rows as $index => $row) {
            if (!isset($selected_rows[$index])) {
                continue;
            }
            $data = array('attribute_set_id' => $attribute_set_id);
            $extend_data = $extend_rows = array();
            foreach ($columns as $excel_column => $field_column) {
                if (!empty($field_column) && !empty($row[$excel_column])) {
                    $field_parts = explode(':', $field_column);

                    /* Set Basic Attributes */
                    if (count($field_parts) == 2) {
                        if ($field_parts[0] == 'basic') {
                            $data[$field_parts[1]] = $row[$excel_column];
                        } else {
                            $extend_data = array(
                                'entity_type' => $entity_type,
                                'attribute_id' => $field_parts[1],
                                'entity_value' => $row[$excel_column],
                            );
                            $extend_rows[] = $extend_data;
                        }
                    }
                }
            }
            try {
                /* Check duplicate item */
                $exists_row = false;
                if (isset($check_duplicate_field_type) && isset($check_duplicate_field_name)) {
                    if ($check_duplicate_field_type == 'basic') {
                        if (!empty($data[$check_duplicate_field_name])) {
                            $exists_row = $this->Item_kit->exists_by_field($entity_type, $check_duplicate_field_name, $data[$check_duplicate_field_name]);
                        }
                    } else {
                        if (!empty($extend_data['entity_value'])) {
                            $exists_row = $this->Attribute->exists_by_value($entity_type, $extend_data['attribute_id'], $extend_data['entity_value']);
                        }
                    }
                }
                if (!$exists_row) {
                    $item_kit_id = $this->Item_kit->save($data);
                    if (!empty($item_kit_id)) {
                        $stored_rows++;
                        /* Set extended attributes */
                        if (!empty($extend_rows)) {
                            foreach ($extend_rows as $extend_data) {
                                $extend_data['entity_id'] = $item_kit_id;
                                $this->Attribute->set_attributes($extend_data);
                            }
                        }
                    }
                } else {
                    $error_rows[] = $row;
                }
            } catch (Exception $ex) {
                $error_rows[] = $row;
                continue;
            }
        }
        $error_html = '';
        if (!empty($error_rows)) {
            $error_html = $this->load->view('import/error/rows', array('num_rows' => count($error_rows), 'rows' => $error_rows, 'columns' => $columns), true);
        }
        if (!empty($stored_rows)) {
            $msg = $stored_rows . ' ' . lang('common_record_stored');
            echo json_encode(array('success' => true, 'message' => $msg, 'error_html' => $error_html));
            return;
        }
        $msg = $stored_rows . ' ' . lang('common_record_stored');
        echo json_encode(array('success' => false, 'message' => $msg, 'error_html' => $error_html));
    }
}
?>
