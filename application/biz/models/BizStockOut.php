<?php
class BizStockOut extends CI_Model
{
	public function getInfo($id = 0) {
		$location_id = $this->Employee->get_logged_in_employee_current_location_id();
		$this->db->from('stock_out');
		$this->db->where('id', $id);
		$this->db->where('location_id', $location_id);
		
		$stockOutInfo = $this->db->get()->row();
		
		$this->db->select('items.*, stock_out_items.qty as stockOut_totalQty, stock_out_items.measure_id as stockOut_measureId');
		$this->db->from('items');
		$this->db->join('stock_out_items', 'stock_out_items.item_id = items.item_id');
		$this->db->where('stock_out_id', $stockOutInfo->id);
		$stockOutInfo->items = $this->db->get()->result();
		
		return $stockOutInfo;
	}
	
	public function getHistory($search = []) {
		$location_id = $this->Employee->get_logged_in_employee_current_location_id();
		$this->db->select('stock_out.*, CONCAT(employee.first_name, " ", employee.last_name) as employee , CONCAT(customer.first_name, " ", customer.last_name) as customer');
		$this->db->from('stock_out');
		$this->db->join('people as employee', 'stock_out.deliverer_id = employee.person_id', 'left');
		$this->db->join('people as customer', 'stock_out.customer_id = customer.person_id', 'left');
		$this->db->where('stock_out.location_id', $location_id);
		
		if (!empty($search['start_date'])) {
			$this->db->where('created_time >= ', $search['start_date']);
		}
	
		if (!empty($search['end_date'])) {
			$this->db->where('created_time <= ', $search['end_date'] . ' 23:59:59');
		}
	
		$this->db->order_by('stock_out.id');
		
		$history = $this->db->get()->result_array();
	
		for($k=0;$k<count($history);$k++)
		{
			$item_names = array();
			$this->db->select('name');
			$this->db->from('items');
			$this->db->join('stock_out_items', 'stock_out_items.item_id = items.item_id');
			$this->db->where('stock_out_id', $history[$k]['id']);
	
			foreach($this->db->get()->result_array() as $row)
			{
				$item_names[] = $row['name'];
			}
	
			$history[$k]['items'] = implode(', ', $item_names);
		}
	
		return $history;
	}
	
	public function save($stockOutData = []) {
		$location_id = $this->Employee->get_logged_in_employee_current_location_id();
		
		$stockOutRecord = [
			'customer_id' => empty($stockOutData['customer']) ? 0 :  $stockOutData['customer'],
			'deliverer_id' => empty($stockOutData['deliverer']) ? 0 : $stockOutData['deliverer'],
			'location_id' => $location_id,
			'comment	' => empty($stockOutData['comment']) ? '' : $stockOutData['comment'],
			'created_time' => date('Y-m-d H:i:s'),
		];
		
		$this->db->insert('stock_out',$stockOutRecord);
		$stockId = $this->db->insert_id();
		
		foreach ($stockOutData['items'] as $item) {
			$stockItemsData = [
					'stock_out_id' => $stockId,
					'item_id' => empty($item->item_id) ? 0 : $item->item_id,
					'item_kit_id' => empty($item->item_kit_id) ? 0 : $item->item_kit_id,
					'qty' => (int) $item->totalQty,
					'measure_id' => empty($item->measure_id) ? 0 : (int) $item->measure_id,
			];
			$this->db->insert('stock_out_items',$stockItemsData);
			
			$stock_recorder_check=false;
			$out_of_stock_check=false;
			
			
			if (!empty($item->item_id)) {
			
				$cur_item_info = $this->Item->get_info($item->item_id);
				$cur_item_location_info = $this->Item_location->get_info($item->item_id);
					
				//checks if the quantity is greater than reorder level
				if(!$cur_item_info->is_service && $cur_item_location_info->quantity > $reorder_level)
				{
					$stock_recorder_check=true;
				}
					
				//checks if the quantity is greater than 0
				if(!$cur_item_info->is_service && $cur_item_location_info->quantity > 0)
				{
					$out_of_stock_check=true;
				}
					
				if (!$cur_item_info->is_service)
				{
					$cur_item_location_info->quantity = $cur_item_location_info->quantity !== NULL ? $cur_item_location_info->quantity : 0;
					$this->Item_location->save_quantity($cur_item_location_info->quantity - $item->totalQty, $item->item_id);
				}
			} elseif (!empty($item->item_kit_id)) {
				$cur_item_kit_info = $this->Item_kit->get_info($item->item_kit_id);
				$cur_item_kit_location_info = $this->Item_kit_location->get_info($item->item_kit_id);
					
				foreach($cur_item_kit_info as $item_kit_item)
				{
					$cur_item_info = $this->Item->get_info($item_kit_item->item_id);
					$cur_item_location_info = $this->Item_location->get_info($item_kit_item->item_id);
					$cur_item_location_info->quantity = $cur_item_location_info->quantity !== NULL ? $cur_item_location_info->quantity : 0;
					$this->Item_location->save_quantity($cur_item_location_info->quantity - ((int) $item->totalQty * $item_kit_item->quantity), $item_kit_item->item_id);
				}
			}
		}
		
		if ($stockOutData['mode'] == 'by_sale') {
			$this->Sale->StockOut($stockOutData['sale_id']);
		}
		return $stockId;
	}
}