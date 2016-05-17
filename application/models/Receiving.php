<?php
class Receiving extends CI_Model
{
	public function __construct()
	{
      parent::__construct();
		$this->load->model('Inventory');	
	}
	
	public function get_info($receiving_id)
	{
		$this->db->from('receivings');
		$this->db->where('receiving_id',$receiving_id);
		return $this->db->get();
	}

	function exists($receiving_id)
	{
		$this->db->from('receivings');
		$this->db->where('receiving_id',$receiving_id);
		$query = $this->db->get();

		return ($query->num_rows()==1);
	}
	
	function update($receiving_data, $receiving_id)
	{
		$this->db->where('receiving_id', $receiving_id);
		$success = $this->db->update('receivings',$receiving_data);
		
		return $success;
	}
		
	function _get_quantity_received($items,$item_id)
	{
		foreach($items as $item)
		{
			if ($item['item_id'] == $item_id)
			{
				return $item['quantity_received'];
			}
		}
		
		return NULL;
	}


	function save ($items,$supplier_id,$employee_id,$comment,$payment_type,$receiving_id=false, $suspended = 0, $mode='receive',$change_receiving_date = false, $is_po = 0, $location_id=-1)
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

			$this->db->insert('receivings_items',$receivings_items_data);
			
			if ($suspended == 0)
			{
				if ($this->config->item('calculate_average_cost_price_from_receivings'))
				{
					$receivings_items_data['item_unit_price_before_tax'] = $item_unit_price_before_tax;
					$this->calculate_and_update_average_cost_price_for_item($item['item_id'], $receivings_items_data);
					unset($receivings_items_data['item_unit_price_before_tax']);
				}
			}
			
			//Update stock quantity IF not a service item
			if (!$cur_item_info->is_service)
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
			
			if($suspended  == 0 && $mode=='transfer' && $location_id && $cur_item_location_info->quantity !== NULL && !$cur_item_info->is_service)
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
	
	function delete($receiving_id, $all_data = false, $update_quantity = true)
	{
		$recv_info = $this->get_info($receiving_id)->row_array();		
		$suspended = $recv_info['suspended'];
		
		$employee_id=$this->Employee->get_logged_in_employee_info()->person_id;
		
		if ($update_quantity)
		{
			$this->db->select('receivings.location_id, item_id, quantity_purchased, quantity_received, transfer_to_location_id');
			$this->db->from('receivings_items');
			$this->db->join('receivings', 'receivings.receiving_id = receivings_items.receiving_id');
			$this->db->where('receivings.receiving_id', $receiving_id);
		
			foreach($this->db->get()->result_array() as $receiving_item_row)
			{
					$receiving_location_id = $receiving_item_row['location_id'];
					$cur_item_info = $this->Item->get_info($receiving_item_row['item_id']);	
					$cur_item_location_info = $this->Item_location->get_info($receiving_item_row['item_id']);
			
					$previous_amount_received = $receiving_item_row['quantity_received'];
			
					if ($suspended != 0)
					{
						$inventory_to_remove = $receiving_item_row['quantity_received'];
					}
					else
					{
						$inventory_to_remove = $receiving_item_row['quantity_purchased'];
					}
			
					if ($inventory_to_remove !=0)
					{
						$this->Item_location->save_quantity($cur_item_location_info->quantity - $inventory_to_remove,$receiving_item_row['item_id']);
			
						$recv_remarks ='RECV '.$receiving_id;
						$inv_data = array
						(
							'trans_date'=>date('Y-m-d H:i:s'),
							'trans_items'=>$receiving_item_row['item_id'],
							'trans_user'=>$employee_id,
							'trans_comment'=>$recv_remarks,
							'trans_inventory'=>$inventory_to_remove*-1,
							'location_id'=>$this->Employee->get_logged_in_employee_current_location_id()
						);
						$this->Inventory->insert($inv_data);
			
					}
		
		
				if ($suspended  == 0 && $receiving_item_row['transfer_to_location_id'])
				{
					$cur_item_location_transfer_info = $this->Item_location->get_info($receiving_item_row['item_id'], $receiving_item_row['transfer_to_location_id']);
				
					$this->Item_location->save_quantity($cur_item_location_transfer_info->quantity + $receiving_item_row['quantity_purchased'],$receiving_item_row['item_id'], $receiving_item_row['transfer_to_location_id']);
		
					$sale_remarks ='RECV '.$receiving_id;
					$inv_data = array
						(
						'trans_date'=>date('Y-m-d H:i:s'),
						'trans_items'=>$receiving_item_row['item_id'],
						'trans_user'=>$employee_id,
						'trans_comment'=>$sale_remarks,
						'trans_inventory'=>$receiving_item_row['quantity_purchased'] * 1,
						'location_id'=>$receiving_item_row['transfer_to_location_id']
						);
						$this->Inventory->insert($inv_data);
				}		
			 
			}
		}
		
		if ($all_data)
		{
			$this->db->delete('receivings_items', array('receiving_id' => $receiving_id));
			$this->db->delete('receivings_items_taxes', array('receiving_id' => $receiving_id));
		}
		
		$this->db->where('receiving_id', $receiving_id);
		return $this->db->update('receivings', array('deleted' => 1,'deleted_by'=>$employee_id));
	}
	
	function undelete($receiving_id)
	{
		$employee_id=$this->Employee->get_logged_in_employee_info()->person_id;
	
		$recv_info = $this->get_info($receiving_id)->row_array();		
		$suspended = $recv_info['suspended'];
		
		$this->db->select('receivings.location_id, item_id, quantity_purchased, quantity_received, transfer_to_location_id');
		$this->db->from('receivings_items');
		$this->db->join('receivings', 'receivings.receiving_id = receivings_items.receiving_id');
		$this->db->where('receivings.receiving_id', $receiving_id);
	
		foreach($this->db->get()->result_array() as $receiving_item_row)
		{
				$receiving_location_id = $receiving_item_row['location_id'];
				$cur_item_info = $this->Item->get_info($receiving_item_row['item_id']);	
				$cur_item_location_info = $this->Item_location->get_info($receiving_item_row['item_id']);
		
				$previous_amount_received = $receiving_item_row['quantity_received'];
		
				if ($suspended != 0)
				{
					$inventory_to_add = $receiving_item_row['quantity_received'];
				}
				else
				{
					$inventory_to_add = $receiving_item_row['quantity_purchased'];
				}
		
				if ($inventory_to_add !=0)
				{
					$this->Item_location->save_quantity($cur_item_location_info->quantity + $inventory_to_add,$receiving_item_row['item_id']);
		
					$recv_remarks ='RECV '.$receiving_id;
					$inv_data = array
					(
						'trans_date'=>date('Y-m-d H:i:s'),
						'trans_items'=>$receiving_item_row['item_id'],
						'trans_user'=>$employee_id,
						'trans_comment'=>$recv_remarks,
						'trans_inventory'=>$inventory_to_add,
						'location_id'=>$this->Employee->get_logged_in_employee_current_location_id()
					);
					$this->Inventory->insert($inv_data);
		
				}
	
	
				if ($suspended == 0 && $receiving_item_row['transfer_to_location_id'])
				{
					$cur_item_location_transfer_info = $this->Item_location->get_info($receiving_item_row['item_id'], $receiving_item_row['transfer_to_location_id']);
					
					$this->Item_location->save_quantity($cur_item_location_transfer_info->quantity - $receiving_item_row['quantity_purchased'],$receiving_item_row['item_id'], $receiving_item_row['transfer_to_location_id']);
			
					$sale_remarks ='RECV '.$receiving_id;
					$inv_data = array
						(
						'trans_date'=>date('Y-m-d H:i:s'),
						'trans_items'=>$receiving_item_row['item_id'],
						'trans_user'=>$employee_id,
						'trans_comment'=>$sale_remarks,
						'trans_inventory'=>$receiving_item_row['quantity_purchased'] * -1,
						'location_id'=>$receiving_item_row['transfer_to_location_id']
						);
						$this->Inventory->insert($inv_data);
				}
		 
		}
		
		
		
		$this->db->where('receiving_id', $receiving_id);
		return $this->db->update('receivings', array('deleted' => 0,'deleted_by'=>NULL));
	}

	function get_receiving_items($receiving_id)
	{
		$this->db->from('receivings_items');
		$this->db->where('receiving_id',$receiving_id);
		return $this->db->get();
	}

	function get_supplier($receiving_id)
	{
		$this->db->from('receivings');
		$this->db->where('receiving_id',$receiving_id);
		return $this->Supplier->get_info($this->db->get()->row()->supplier_id);
	}
	
	//We create a temp table that allows us to do easy report/receiving queries
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
		".$this->db->dbprefix('items').".item_id, ".$this->db->dbprefix('receivings').".supplier_id, quantity_purchased,quantity_received, item_cost_price, item_unit_price,".$this->db->dbprefix('categories').".name as category,".$this->db->dbprefix('categories').".id as category_id,
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
	
	
	function drop_receivings_items_temp_table()
	{
		$this->db->query('DROP TABLE IF EXISTS '.$this->db->dbprefix('receivings_items_temp'));
	}
	
	
	function calculate_and_update_average_cost_price_for_item($item_id,$current_receivings_items_data)
	{
		//Dont calculate averages unless we receive quanitity > 0
		if ($current_receivings_items_data['quantity_purchased'] > 0)
		{
			$cost_price_avg = false;
			$averaging_method = $this->config->item('averaging_method');
		
			$cur_item_info = $this->Item->get_info($item_id);
			$cur_item_location_info = $this->Item_location->get_info($item_id);
		
			if ($averaging_method == 'moving_average')
			{
				$current_cost_price = ($cur_item_location_info && $cur_item_location_info->cost_price) ? $cur_item_location_info->cost_price : $cur_item_info->cost_price;			
				$current_quantity = $cur_item_location_info->quantity > 0 ? $cur_item_location_info->quantity : 0;
				$current_inventory_value = $current_cost_price * $current_quantity;
			
				$received_cost_price = $current_receivings_items_data['item_unit_price_before_tax'] * (1 - ($current_receivings_items_data['discount_percent']/100));
				$received_quantity = $current_receivings_items_data['quantity_purchased'];
				$new_inventory_value = $received_cost_price * $received_quantity;
			
				$cost_price_avg = ($current_inventory_value + $new_inventory_value) / ($current_quantity + $received_quantity);
			
			}
			elseif ($averaging_method == 'historical_average')
			{
				if ($cur_item_location_info && $cur_item_location_info->cost_price)
				{
					$location_id = $this->Employee->get_logged_in_employee_current_location_id();
					$result = $this->db->query("SELECT ROUND((SUM(item_unit_price*quantity_purchased-item_unit_price*quantity_purchased*discount_percent/100)) / SUM(quantity_purchased),10) as cost_price_average 
					FROM ".$this->db->dbprefix('receivings_items').' '.
					'JOIN '.$this->db->dbprefix('receivings').' ON '.$this->db->dbprefix('receivings').'.receiving_id = '.$this->db->dbprefix('receivings_items').'.receiving_id '.
					'WHERE quantity_purchased > 0 and item_id='.$this->db->escape($item_id).' and location_id = '.$this->db->escape($location_id))->result();
				}
				else
				{
					$result = $this->db->query("SELECT ROUND((SUM(item_unit_price*quantity_purchased-item_unit_price*quantity_purchased*discount_percent/100)) / SUM(quantity_purchased),10) as cost_price_average 
					FROM ".$this->db->dbprefix('receivings_items'). '
					WHERE quantity_purchased > 0 and item_id='.$this->db->escape($item_id))->result();				
				}
			
				$cost_price_avg = $result[0]->cost_price_average;
			}
			elseif ($averaging_method == 'dont_average') //Don't average just use current price
			{
				$cost_price_avg = $current_receivings_items_data['item_unit_price_before_tax'];
			}
		
			if ($cost_price_avg !== FALSE)
			{
				$cost_price_avg = to_currency_no_money($cost_price_avg, 10);
				//If we have a location cost price, update that value
				if ($cur_item_location_info && $cur_item_location_info->cost_price)
				{
					$item_location_data = array('cost_price' => $cost_price_avg);
					$this->Item_location->save($item_location_data,$item_id);
				}
				else
				{
					//Update cost price
					$item_data = array('cost_price'=>$cost_price_avg);
					$this->Item->save($item_data,$item_id);
				}
			}
		}
	}

	function calculate_cost_price_preview($item_id,$price, $additional_quantity, $discount_percent)
	{
		if ($additional_quantity > 0)
		{
			$cost_price_avg = false;
			$averaging_method = $this->config->item('averaging_method');
		
			$cur_item_info = $this->Item->get_info($item_id);
			$cur_item_location_info = $this->Item_location->get_info($item_id);
			
			if ($averaging_method == 'moving_average')
			{
				$current_cost_price = ($cur_item_location_info && $cur_item_location_info->cost_price) ? $cur_item_location_info->cost_price : $cur_item_info->cost_price;			
				$current_quantity = $cur_item_location_info->quantity > 0 ? $cur_item_location_info->quantity : 0;
				$current_inventory_value = $current_cost_price * $current_quantity;
			
				$received_cost_price = $price * (1 - ($discount_percent/100));
				$received_quantity = $additional_quantity;
				$new_inventory_value = $received_cost_price * $received_quantity;
			
				$cost_price_avg = ($current_inventory_value + $new_inventory_value) / ($current_quantity + $received_quantity);
			
			}
			elseif ($averaging_method == 'historical_average')
			{
				if ($cur_item_location_info && $cur_item_location_info->cost_price)
				{
					$location_id = $this->Employee->get_logged_in_employee_current_location_id();
					$result = $this->db->query("SELECT ROUND((SUM(item_unit_price*quantity_purchased-item_unit_price*quantity_purchased*discount_percent/100)),10) as cost_price_sum,  SUM(quantity_purchased) as cost_price_quantity_sum
					FROM ".$this->db->dbprefix('receivings_items').' '.
					'JOIN '.$this->db->dbprefix('receivings').' ON '.$this->db->dbprefix('receivings').'.receiving_id = '.$this->db->dbprefix('receivings_items').'.receiving_id '.
					'WHERE quantity_purchased > 0 and item_id='.$this->db->escape($item_id).' and location_id = '.$this->db->escape($location_id))->result();
				}
				else
				{
					$result = $this->db->query("SELECT ROUND((SUM(item_unit_price*quantity_purchased-item_unit_price*quantity_purchased*discount_percent/100)),10) as cost_price_sum,  SUM(quantity_purchased) as cost_price_quantity_sum
					FROM ".$this->db->dbprefix('receivings_items'). '
					WHERE quantity_purchased > 0 and item_id='.$this->db->escape($item_id))->result();				
				}
				
				$cost_price_sum = $result[0]->cost_price_sum + ($price*$additional_quantity-$price*$additional_quantity*$discount_percent/100);
				$cost_price_quantity_sum = $result[0]->cost_price_quantity_sum + $additional_quantity;
				
				$cost_price_avg = $cost_price_sum/$cost_price_quantity_sum;
			}
			elseif ($averaging_method == 'dont_average') //Don't average just use current price
			{
				$cost_price_avg = $price;
			}
		
			return to_currency($cost_price_avg,10);
		}
	
		return FALSE;
	}
	
	function get_all_suspended()
	{		
		$location_id = $this->Employee->get_logged_in_employee_current_location_id();		
		
		$this->db->from('receivings');
		$this->db->join('suppliers', 'receivings.supplier_id = suppliers.person_id', 'left');
		$this->db->join('people', 'suppliers.person_id = people.person_id', 'left');
		$this->db->where('receivings.deleted', 0);
		$this->db->where('receivings.suspended', 1);
		$this->db->where('location_id', $location_id);
		$this->db->order_by('receiving_id');
		$receivings = $this->db->get()->result_array();

		for($k=0;$k<count($receivings);$k++)
		{
			$item_names = array();
			$this->db->select('name');
			$this->db->from('items');
			$this->db->join('receivings_items', 'receivings_items.item_id = items.item_id');
			$this->db->where('receiving_id', $receivings[$k]['receiving_id']);
		
			foreach($this->db->get()->result_array() as $row)
			{
				$item_names[] = $row['name'];
			}
			
			$receivings[$k]['items'] = implode(', ', $item_names);
		}
		
		return $receivings;
	}
	
	function get_suspended_receivings_for_item($item_id)
	{
		$this->db->from('receivings');
		$this->db->join('receivings_items', 'receivings.receiving_id = receivings_items.receiving_id');
		$this->db->where('receivings.suspended', '1');
		$this->db->where('receivings_items.item_id', $item_id);
		
		return $this->db->get()->result_array();
	}
	
	function get_receiving_items_taxes($receiving_id, $line = FALSE)
	{
		$item_where = '';
		
		if ($line)
		{
			$item_where = 'and '.$this->db->dbprefix('receivings_items').'.line = '.$line;
		}

		$query = $this->db->query('SELECT name, percent, cumulative, item_unit_price as price, quantity_purchased as quantity, discount_percent as discount '.
		'FROM '. $this->db->dbprefix('receivings_items_taxes'). ' JOIN '.
		$this->db->dbprefix('receivings_items'). ' USING (receiving_id, item_id, line) '.
		'WHERE '.$this->db->dbprefix('receivings_items_taxes').".receiving_id = $receiving_id".' '.$item_where.' '.
		'ORDER BY '.$this->db->dbprefix('receivings_items').'.line,'.$this->db->dbprefix('receivings_items').'.item_id,cumulative,name,percent');
		return $query->result_array();
	}
	
	function get_deleted_taxes($receiving_id)
	{
		$this->db->from('receivings');
		$this->db->where('receiving_id',$receiving_id);
		return unserialize($this->db->get()->row()->deleted_taxes);
	}
	
}
?>
