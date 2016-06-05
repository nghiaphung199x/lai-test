<?php
require_once (APPPATH . "models/Receiving.php");
class BizReceiving extends Receiving
{
	public function getAllTransferings()
	{		
		$location_id = $this->Employee->get_logged_in_employee_current_location_id();		
		
		$this->db->from('receivings');
		$this->db->join('suppliers', 'receivings.supplier_id = suppliers.person_id', 'left');
		$this->db->join('people', 'suppliers.person_id = people.person_id', 'left');
		$this->db->where('receivings.deleted', 0);
		// $this->db->where('receivings.transfer_status', 'pending');
		$this->db->where('receivings.transfer_to_location_id > 0');
		$this->db->where('receivings.location_id > 0');
		$this->db->where('transfer_to_location_id', $location_id);
		$this->db->order_by('receiving_id');

		$transferingList = $this->db->get()->result_array();

		for($k=0;$k<count($transferingList);$k++)
		{
			$item_names = array();
			$this->db->select('name');
			$this->db->from('items');
			$this->db->join('receivings_items', 'receivings_items.item_id = items.item_id');
			$this->db->where('receiving_id', $transferingList[$k]['receiving_id']);
		
			foreach($this->db->get()->result_array() as $row)
			{
				$item_names[] = $row['name'];
			}
			
			$transferingList[$k]['items'] = implode(', ', $item_names);
		}
		
		return $transferingList;
	}
	
	public function removeTransferPending($recId = 0, $employee_id = -1)
	{
		$this->db->where('receiving_id', $recId);
		if( $this->db->update('receivings', array('deleted' => 1,'deleted_by'=>$employee_id)) )
		{
			$this->db->delete('receivings_items', array('receiving_id' => $recId));
		}
	}

	public function save ($items,$supplier_id,$employee_id,$comment,$payment_type,$receiving_id=false, $suspended = 0, $mode='receive',$change_receiving_date = false, $is_po = 0, $location_id=-1)
	{
		if(count($items)==0)
			return -1;

		//we need to check the sale library for deleted taxes during sale
		$this->load->library('receiving_lib');

		$deleted_taxes = $this->receiving_lib->get_deleted_taxes();

		$receivings_data = array(
		'supplier_id'=> $supplier_id > 0 ? $supplier_id : null,
		'employee_id'=>$employee_id,
		'payment_type'=>$payment_type,
		'comment'=>$comment,
		'suspended' => $suspended,
		'location_id' => $this->Employee->get_logged_in_employee_current_location_id(),
		'transfer_to_location_id' => $location_id > 0 ? $location_id : NULL,
		'deleted' => 0,
		'deleted_by' => NULL,
		'deleted_taxes' =>  $deleted_taxes? serialize($deleted_taxes) : NULL,
		'is_po' => $is_po,
		);
			
		//Run these queries as a transaction, we want to make sure we do all or nothing
		$this->db->trans_start();
		
		if($change_receiving_date) 
		{
			$receiving_time = strtotime($change_receiving_date);
			if($receiving_time !== FALSE)
			{
				$receivings_data['receiving_time']=date('Y-m-d H:i:s', strtotime($change_receiving_date));
			}
		}
		else
		{
			$receivings_data['receiving_time'] = date('Y-m-d H:i:s');			
		}
		
		
		if ($receiving_id)
		{
			$previous_receiving_items = $this->get_receiving_items($receiving_id)->result_array();
			//Delete previoulsy receving so we can overwrite data
			$this->delete($receiving_id, true);
			
			
			$this->db->where('receiving_id', $receiving_id);
			$this->db->update('receivings', $receivings_data);
		}
		else
		{
			$previous_receiving_items = array();
			$this->db->insert('receivings',$receivings_data);
			$receiving_id = $this->db->insert_id();
		}

		foreach($items as $line=>$item)
		{
			$cur_item_info = $this->Item->get_info($item['item_id']);
			$cur_item_location_info = $this->Item_location->get_info($item['item_id']);
			$cost_price = ($cur_item_location_info && $cur_item_location_info->cost_price) ? $cur_item_location_info->cost_price : $cur_item_info->cost_price;
			
			$qtyOriginal = $item['quantity'];
			$qtyOriginalReceived = $item['quantity_received'];
			
			if( $cur_item_info->measure_id != $item['measure_id'] /* && ($mode == 'receive' || $mode == 'purchase_order') */)
			{
				$convertedValue = $this->ItemMeasures->getConvertedValue($item['item_id'], $item['measure_id']);
				$cost_price = $cost_price * (100 + (int)$convertedValue->cost_price_percentage_converted ) / 100;
				
				$totalQty = $item['quantity'] = $item['quantity'] * (int)$convertedValue->qty_converted;
				$item['quantity_received'] = $item['quantity_received'] * (int)$convertedValue->qty_converted;
				
				$item['price'] = $item['price'] / $totalQty;
			}
			
			$item_unit_price_before_tax = $item['price'];
			
			$expire_date = NULL;
			
			if ($item['expire_date'])
			{
				$expire_date = date('Y-m-d', strtotime($item['expire_date']));				
			}
			
			$quantity_received = 0;
			
			if ($suspended != 0 && $item['quantity_received'] !== NULL)
			{
				$quantity_received = $item['quantity_received'];
			}
			elseif($suspended==0)
			{
				$quantity_received = $item['quantity'];
			}
			
			$receivings_items_data = array
			(
				'receiving_id'=>$receiving_id,
				'item_id'=>$item['item_id'],
				'line'=>$item['line'],
				'description'=>$item['description'],
				'serialnumber'=>$item['serialnumber'],
				'quantity_purchased'=>$item['quantity'], // qty is converted to base measure
				'quantity_received'=>$quantity_received,
				'measure_id' => $item['measure_id'],
				'measure_qty' => $qtyOriginal, // qty by measure
				'measure_qty_received' => $qtyOriginalReceived, // qty by measure
				'discount_percent'=>$item['discount'],
				'item_cost_price' => $cost_price,
				'item_unit_price'=>$item['price'],
				'expire_date' => $expire_date,
			);

			$this->db->insert('receivings_items',$receivings_items_data);
			
			// TODO
			if ($suspended == 0 && $mode != 'transfer' )
			{
				if ($this->config->item('calculate_average_cost_price_from_receivings'))
				{
					$receivings_items_data['item_unit_price_before_tax'] = $item_unit_price_before_tax;
					$this->calculate_and_update_average_cost_price_for_item($item['item_id'], $receivings_items_data);
					unset($receivings_items_data['item_unit_price_before_tax']);
				}
			}
			
			//Update stock quantity IF not a service item
			// TODO -- HERE
			if (!$cur_item_info->is_service && $mode != 'transfer' )
			{
				//If we have a null quanity set it to 0, otherwise use the value
				$cur_item_location_info->quantity = $cur_item_location_info->quantity !== NULL ? $cur_item_location_info->quantity : 0;
				
				//This means we never adjusted quantity_received so we should accept all
				if ($suspended == 0 && $item['quantity_received'] === NULL)
				{	
					$inventory_to_add = $item['quantity'];
				}
				else
				{
					
					if ($suspended == 0)
					{
						//Editing sale; doesn't have option to partial receive
						if ($this->receiving_lib->get_change_recv_id())
						{
							$inventory_to_add = $item['quantity'];
						}
						else
						{
							$previous_amount_received = $this->_get_quantity_received($previous_receiving_items, $item['item_id']);
							$inventory_to_add = $previous_amount_received + $item['quantity'] - $item['quantity_received'];
						}
					}
					else
					{
						$inventory_to_add = $item['quantity_received'];
					}
					
				}
				
				if ($inventory_to_add !=0)
				{
					
					$this->Item_location->save_quantity($cur_item_location_info->quantity + $inventory_to_add, $item['item_id']);
				
					$recv_remarks ='RECV '.$receiving_id;
					$inv_data = array
					(
						'trans_date'=>date('Y-m-d H:i:s'),
						'trans_items'=>$item['item_id'],
						'trans_user'=>$employee_id,
						'trans_comment'=>$recv_remarks,
						'trans_inventory'=>$inventory_to_add,
						'location_id'=>$this->Employee->get_logged_in_employee_current_location_id()
					);
					$this->Inventory->insert($inv_data);
				}
			}
			
			// TODO
// 			if($suspended  == 0 && $mode=='transfer' && $location_id && $cur_item_location_info->quantity !== NULL && !$cur_item_info->is_service)
// 			{				
// 				$this->Item_location->save_quantity($this->Item_location->get_location_quantity($item['item_id'],$location_id) + ($item['quantity'] * -1),$item['item_id'],$location_id);
				
// 				if (!isset($inv_data))
// 				{
// 					$inv_data = array
// 					(
// 						'trans_date'=>date('Y-m-d H:i:s'),
// 						'trans_items'=>$item['item_id'],
// 						'trans_user'=>$employee_id,
// 						'trans_comment'=>'RECV '.$receiving_id,
// 					);
// 				}
				
// 				//Change values from $inv_data above and insert
// 				$inv_data['trans_inventory']=$item['quantity'] * -1;
// 				$inv_data['location_id']=$location_id;
// 				$this->Inventory->insert($inv_data);
// 			}		

			if ($this->config->item('charge_tax_on_recv') && $mode != 'transfer')
			{
				foreach($this->Item_taxes_finder->get_info($item['item_id'],'receiving') as $row)
				{
					$tax_name = $row['percent'].'% ' . $row['name'];
	
					//Only save sale if the tax has NOT been deleted
					if (!in_array($tax_name, $this->receiving_lib->get_deleted_taxes()))
					{	
						$this->db->insert('receivings_items_taxes', array(
							'receiving_id' 	=>$receiving_id,
							'item_id' 	=>$item['item_id'],
							'line'      =>$item['line'],
							'name'		=>$row['name'],
							'percent' 	=>$row['percent'],
							'cumulative'=>$row['cumulative']
						));
					}
				}
			}
		}		
		
		

		$this->db->trans_complete();
		
		if ($this->db->trans_status() === FALSE)
		{
			return -1;
		}

		return $receiving_id;
	}
	
	public function approvedTransfer(
		$items, // 1
		$supplier_id, // 2
		$employee_id, // 3
		$comment, // 4
		$payment_type, // 5
		$receiving_id=false, // 6 
		$change_receiving_date = false, // 7
		$is_po = 0, // 8
		$location_from_id=-1 // 9
	)
	{
		if(count($items)==0)
			return -1;
	
		//we need to check the sale library for deleted taxes during sale
		$this->load->library('receiving_lib');
		
		$location_id = $this->Employee->get_logged_in_employee_current_location_id();
		
		$deleted_taxes = $this->receiving_lib->get_deleted_taxes();

		$receivings_data = array(
				'supplier_id'=> $supplier_id > 0 ? $supplier_id : null,
				'employee_id'=>$employee_id,
				'payment_type'=>$payment_type,
				'comment'=>$comment,
				'suspended' => $suspended,
				'location_id' => $location_from_id,
				'transfer_to_location_id' => $location_id,
				'deleted' => 0,
				'deleted_by' => NULL,
				'deleted_taxes' =>  $deleted_taxes? serialize($deleted_taxes) : NULL,
				'is_po' => $is_po,
		);
				
		//Run these queries as a transaction, we want to make sure we do all or nothing
		$this->db->trans_start();

		if($change_receiving_date)
		{
			$receiving_time = strtotime($change_receiving_date);
			if($receiving_time !== FALSE)
			{
				$receivings_data['receiving_time']=date('Y-m-d H:i:s', strtotime($change_receiving_date));
			}
		}
		else
		{
			$receivings_data['receiving_time'] = date('Y-m-d H:i:s');
		}
	
	
		$previous_receiving_items = $this->get_receiving_items($receiving_id)->result_array();
		//Delete previoulsy receving so we can overwrite data
		// TODO
		$this->db->where('receiving_id', $receiving_id);
		$this->db->update('receivings', array('transfer_status' => 'approved'));

		foreach($items as $line=>$item)
		{
			$cur_item_info = $this->Item->get_info($item['item_id']);
			
			$cur_item_location_info = $this->Item_location->get_info($item['item_id'], $location_from_id);
			$cost_price = ($cur_item_location_info && $cur_item_location_info->cost_price) ? $cur_item_location_info->cost_price : $cur_item_info->cost_price;
			
			if( $cur_item_info->measure_id != $item['measure_id'] /* && ($mode == 'receive' || $mode == 'purchase_order') */)
			{
				$convertedValue = $this->ItemMeasures->getConvertedValue($item['item_id'], $item['measure_id']);
				$cost_price = $cost_price * (100 + (int)$convertedValue->cost_price_percentage_converted ) / 100;
			
				$totalQty = $item['quantity'] = $item['quantity'] * (int)$convertedValue->qty_converted;
			
				$item['price'] = $item['price'] / $totalQty;
			}
			
			$item_unit_price_before_tax = $item['price'];
				
			$expire_date = NULL;
				
			if ($item['expire_date'])
			{
				$expire_date = date('Y-m-d', strtotime($item['expire_date']));
			}
				
			$quantity_received = 0;
				
			if ($suspended != 0 && $item['quantity_received'] !== NULL)
			{
				$quantity_received = $item['quantity_received'];
			}
			elseif($suspended==0)
			{
				$quantity_received = $item['quantity'];
			}
				
			$receivings_items_data = array
			(
				'receiving_id'=>$receiving_id,
				'item_id'=>$item['item_id'],
				'line'=>$item['line'],
				'description'=>$item['description'],
				'serialnumber'=>$item['serialnumber'],
				'quantity_purchased'=>$item['quantity'],
				'quantity_received'=>$quantity_received,
				'discount_percent'=>$item['discount'],
				'item_cost_price' => $cost_price,
				'item_unit_price'=>$item['price'],
				'expire_date' => $expire_date,
			);
	
			// TODO
			if ($this->config->item('calculate_average_cost_price_from_receivings'))
			{
				$receivings_items_data['item_unit_price_before_tax'] = $item_unit_price_before_tax;
				$this->calculate_and_update_average_cost_price_for_item($item['item_id'], $receivings_items_data);
				unset($receivings_items_data['item_unit_price_before_tax']);
			}
				
			//Update stock quantity IF not a service item
			// TODO -- HERE
			if (!$cur_item_info->is_service)
			{
				//If we have a null quanity set it to 0, otherwise use the value
				$cur_item_location_info->quantity = $cur_item_location_info->quantity !== NULL ? $cur_item_location_info->quantity : 0;
				//This means we never adjusted quantity_received so we should accept all
				if ($item['quantity_received'] === NULL)
				{
					$inventory_to_add = $item['quantity'];
				}
				else
				{
					//Editing sale; doesn't have option to partial receive
					if ($this->receiving_lib->get_change_recv_id())
					{
						$inventory_to_add = $item['quantity'];
					}
					else
					{
						$previous_amount_received = $this->_get_quantity_received($previous_receiving_items, $item['item_id']);
						$inventory_to_add = $previous_amount_received + $item['quantity'] - $item['quantity_received'];
					}
				}
				
				// HERE YOU ARE!
				if ($inventory_to_add !=0)
				{
					$this->Item_location->save_quantity($cur_item_location_info->quantity + $inventory_to_add, $item['item_id'], $location_from_id);
					$recv_remarks ='RECV '.$receiving_id;
					$inv_data = array
					(
						'trans_date'=>date('Y-m-d H:i:s'),
						'trans_items'=>$item['item_id'],
						'trans_user'=>$employee_id,
						'trans_comment'=>$recv_remarks,
						'trans_inventory'=>$inventory_to_add,
						'location_id'=>$location_from_id
					);
					$this->Inventory->insert($inv_data);
				}
			}
				
			// TODO
			if($location_id && $cur_item_location_info->quantity !== NULL && !$cur_item_info->is_service)
			{
				$this->Item_location->save_quantity($this->Item_location->get_location_quantity($item['item_id'],$location_id) + ($item['quantity'] * -1),$item['item_id'],$location_id);

				if (!isset($inv_data))
					{
						$inv_data = array
						(
							'trans_date'=>date('Y-m-d H:i:s'),
							'trans_items'=>$item['item_id'],
							'trans_user'=>$employee_id,
							'trans_comment'=>'RECV '.$receiving_id,
						);
				}

				//Change values from $inv_data above and insert
				$inv_data['trans_inventory']=$item['quantity'] * -1;
				$inv_data['location_id']=$location_id;
				$this->Inventory->insert($inv_data);
			}

			if ($this->config->item('charge_tax_on_recv'))
			{
				foreach($this->Item_taxes_finder->get_info($item['item_id'],'receiving') as $row)
				{
					$tax_name = $row['percent'].'% ' . $row['name'];

					//Only save sale if the tax has NOT been deleted
					if (!in_array($tax_name, $this->receiving_lib->get_deleted_taxes()))
					{
						$this->db->insert('receivings_items_taxes', array(
								'receiving_id' 	=>$receiving_id,
								'item_id' 	=>$item['item_id'],
								'line'      =>$item['line'],
								'name'		=>$row['name'],
								'percent' 	=>$row['percent'],
								'cumulative'=>$row['cumulative']
						));
					}
				}
			}
		}
		$this->db->trans_complete();
		if ($this->db->trans_status() === FALSE)
		{
			return -1;
		}
		return $receiving_id;
	}
	
	public function create_receivings_items_temp_table($params)
	{
		set_time_limit(0);
	
	
		$location_ids = implode(',',Report::get_selected_location_ids());
	
		$where = '';
	
		if (isset($params['start_date']) && isset($params['end_date']))
		{
			$where = 'WHERE receiving_time BETWEEN "'.$params['start_date'].'" and "'.$params['end_date'].'"'.' and '.$this->db->dbprefix('receivings').'.location_id IN ('.$location_ids.')';
			//Added for detailed_suspended_report, we don't need this for other reports as we are always going to have start + end date
			if (isset($params['force_suspended']) && $params['force_suspended'])
			{
				$where .=' and suspended != 0';
			}
			elseif ($this->config->item('hide_suspended_recv_in_reports'))
			{
				$where .=' and suspended = 0';
			}
		}
		else
		{
			//If we don't pass in a date range, we don't need data from the temp table
			$where = 'WHERE location_id IN ('.$location_ids.')';
				
			if ($this->config->item('hide_suspended_recv_in_reports'))
			{
				$where .=' and suspended = 0';
			}
		}
		
		$decimals = $this->config->item('number_of_decimals') !== NULL && $this->config->item('number_of_decimals') != '' ? (int)$this->config->item('number_of_decimals') : 2;
		
		$this->db->query("CREATE TEMPORARY TABLE ".$this->db->dbprefix('receivings_items_temp')."
		(SELECT ".$this->db->dbprefix('receivings').".location_id as location_id, ".$this->db->dbprefix('receivings').".deleted as deleted,".$this->db->dbprefix('receivings').".deleted_by as deleted_by, receiving_time, date(receiving_time) as receiving_date, ".$this->db->dbprefix('receivings_items').".receiving_id, comment,payment_type, employee_id,
		".$this->db->dbprefix('items').".item_id, ".$this->db->dbprefix('receivings').".supplier_id, quantity_purchased,quantity_received, ". $this->db->dbprefix('receivings_items') . ".measure_id, ".$this->db->dbprefix('receivings_items').".measure_qty, ".$this->db->dbprefix('receivings_items').".measure_qty_received, item_cost_price, item_unit_price,".$this->db->dbprefix('categories').".name as category,".$this->db->dbprefix('categories').".id as category_id,
		discount_percent, ROUND((item_unit_price*quantity_purchased-item_unit_price*quantity_purchased*discount_percent/100),".$decimals.") as subtotal,
		(ROUND(item_unit_price*quantity_purchased-item_unit_price*quantity_purchased*discount_percent/100,".$decimals."))+(item_unit_price*quantity_purchased-item_unit_price*quantity_purchased*discount_percent/100)*(SUM(CASE WHEN cumulative != 1 THEN percent ELSE 0 END)/100)
		+(((item_unit_price*quantity_purchased-item_unit_price*quantity_purchased*discount_percent/100)*(SUM(CASE WHEN cumulative != 1 THEN percent ELSE 0 END)/100) + (item_unit_price*quantity_purchased-item_unit_price*quantity_purchased*discount_percent/100))
		*(SUM(CASE WHEN cumulative = 1 THEN percent ELSE 0 END))/100) as total,
		(item_unit_price*quantity_purchased-item_unit_price*quantity_purchased*discount_percent/100)*(SUM(CASE WHEN cumulative != 1 THEN percent ELSE 0 END)/100)
		+(((item_unit_price*quantity_purchased-item_unit_price*quantity_purchased*discount_percent/100)*(SUM(CASE WHEN cumulative != 1 THEN percent ELSE 0 END)/100) + (item_unit_price*quantity_purchased-item_unit_price*quantity_purchased*discount_percent/100))
		*(SUM(CASE WHEN cumulative = 1 THEN percent ELSE 0 END))/100) as tax,
		ROUND((item_unit_price*quantity_purchased-item_unit_price*quantity_purchased*discount_percent/100),".$decimals.") - (item_cost_price*quantity_purchased) as profit,
		".$this->db->dbprefix('receivings_items').".line as line, serialnumber, ".$this->db->dbprefix('receivings_items').".description as description
		FROM ".$this->db->dbprefix('receivings_items')."
		INNER JOIN ".$this->db->dbprefix('receivings')." ON  ".$this->db->dbprefix('receivings_items').'.receiving_id='.$this->db->dbprefix('receivings').'.receiving_id'."
		INNER JOIN ".$this->db->dbprefix('items')." ON  ".$this->db->dbprefix('receivings_items').'.item_id='.$this->db->dbprefix('items').'.item_id'."
		LEFT OUTER JOIN ".$this->db->dbprefix('receivings_items_taxes')." ON  "
				.$this->db->dbprefix('receivings_items').'.receiving_id='.$this->db->dbprefix('receivings_items_taxes').'.receiving_id'." and "
				.$this->db->dbprefix('receivings_items').'.item_id='.$this->db->dbprefix('receivings_items_taxes').'.item_id'." and "
				.$this->db->dbprefix('receivings_items').'.line='.$this->db->dbprefix('receivings_items_taxes').'.line'. "
		LEFT OUTER JOIN ".$this->db->dbprefix('categories')." ON  ".$this->db->dbprefix('categories').'.id='.$this->db->dbprefix('items').'.category_id'."
					
				$where
				GROUP BY receiving_id, item_id, line)");
	}
}
?>
