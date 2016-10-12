<?php
require_once ("Report.php");
class Deleted_receivings extends Report
{
	function __construct()
	{
		parent::__construct();
	}
	
	public function getDataColumns()
	{
		$return = array('summary' => array(array('data'=>lang('reports_receiving_id'), 'align'=>'left'), array('data'=>lang('reports_date'), 'align'=>'left'), array('data'=>lang('reports_items_ordered'), 'align'=>'left'),array('data'=>lang('common_qty_received'), 'align'=>'left'), array('data'=>lang('reports_received_by'), 'align'=>'left'), array('data'=>lang('reports_supplied_by'), 'align'=>'left'),  array('data'=>lang('reports_subtotal'), 'align'=>'right'), array('data'=>lang('reports_total'), 'align'=>'right'),  array('data'=>lang('common_tax'), 'align'=>'right'), array('data'=>lang('reports_payment_type'), 'align'=>'left'), array('data'=>lang('reports_comments'), 'align'=>'left')),
					'details' => array(array('data'=>lang('reports_name'), 'align'=>'left'),array('data'=>lang('common_product_id'), 'align'=> 'left'), array('data'=>lang('reports_category'), 'align'=>'left'),array('data'=>lang('common_size'), 'align'=>'left'), array('data'=>lang('reports_items_ordered'), 'align'=>'left'),array('data'=>lang('common_qty_received'), 'align'=>'left'), array('data'=>lang('reports_subtotal'), 'align'=>'right'), array('data'=>lang('reports_total'), 'align'=>'right'),  array('data'=>lang('common_tax'), 'align'=>'right'), array('data'=>lang('common_discount'), 'align'=>'left'))
		);		
		
		$location_count = count(self::get_selected_location_ids());
		
		if ($location_count > 1)
		{
			array_unshift($return['summary'], array('data'=>lang('common_location'), 'align'=> 'left'));
		}
		
		
		return $return;
	}
	
	public function getData()
	{
		$data = array();
		$data['summary'] = array();
		$data['details'] = array();
	
		$this->db->select('locations.name as location_name, receiving_id, receiving_date, sum(quantity_purchased) as items_purchased,sum(quantity_received) as items_received, CONCAT(employee.first_name," ",employee.last_name) as employee_name, CONCAT(supplier.company_name, " (",people.first_name," ",people.last_name, ")") as supplier_name, sum(subtotal) as subtotal, sum(total) as total, sum(tax) as tax, sum(profit) as profit, payment_type, comment', false);
		$this->db->from('receivings_items_temp');
		$this->db->join('locations', 'locations.location_id = receivings_items_temp.location_id');
		$this->db->join('people as employee', 'receivings_items_temp.employee_id = employee.person_id');
		$this->db->join('suppliers as supplier', 'receivings_items_temp.supplier_id = supplier.person_id', 'left');
		$this->db->join('people as people', 'people.person_id = supplier.person_id', 'left');
		if ($this->params['sale_type'] == 'sales')
		{
			$this->db->where('quantity_purchased > 0');
		}
		elseif ($this->params['sale_type'] == 'returns')
		{
			$this->db->where('quantity_purchased < 0');
		}
		
		
		$this->db->where('receivings_items_temp.deleted', 1);
		$this->db->group_by('receiving_id');
		$this->db->order_by('receiving_time', ($this->config->item('report_sort_order')) ? $this->config->item('report_sort_order') : 'asc');

		//If we are exporting NOT exporting to excel make sure to use offset and limit
		if (isset($this->params['export_excel']) && !$this->params['export_excel'])
		{
			$this->db->limit($this->report_limit);
			$this->db->offset($this->params['offset']);
		}		
		
		foreach($this->db->get()->result_array() as $receiving_summary_row)
		{
			$data['summary'][$receiving_summary_row['receiving_id']] = $receiving_summary_row; 
		}
		
		$receiving_ids = array();
		
		foreach($data['summary'] as $receiving_row)
		{
			$receiving_ids[] = $receiving_row['receiving_id'];
		}

		$this->db->select('name, receiving_id, receiving_date, receivings_items_temp.category, quantity_purchased, quantity_received, serialnumber,total, subtotal, tax, discount_percent,items.product_id, items.size', false);
		$this->db->from('receivings_items_temp');
		$this->db->join('items', 'receivings_items_temp.item_id = items.item_id');

		if (!empty($receiving_ids))
		{
			$this->db->group_start();
			$receiving_ids_chunk = array_chunk($receiving_ids,25);
			foreach($receiving_ids_chunk as $receiving_ids)
			{
				$this->db->or_where_in('receiving_id', $receiving_ids);
			}
			$this->db->group_end();
		}
		else
		{
			$this->db->where('1', '2', FALSE);		
		}

		foreach($this->db->get()->result_array() as $receiving_item_row)
		{
			$data['details'][$receiving_item_row['receiving_id']][] = $receiving_item_row;
		}

		return $data;
	}
	
	public function getTotalRows()
	{		
		$this->db->select("COUNT(DISTINCT(receiving_id)) as receiving_count");
		$this->db->from('receivings_items_temp');
		if ($this->params['sale_type'] == 'sales')
		{
			$this->db->where('quantity_purchased > 0');
		}
		elseif ($this->params['sale_type'] == 'returns')
		{
			$this->db->where('quantity_purchased < 0');
		}
				
		$this->db->where('receivings_items_temp.deleted', 1);
		$ret = $this->db->get()->row_array();
		return $ret['receiving_count'];

	}
	
	public function getSummaryData()
	{
		$this->db->select('sum(tax) as tax, sum(total) as total', false);
		$this->db->from('receivings_items_temp');
		if ($this->params['sale_type'] == 'sales')
		{
			$this->db->where('quantity_purchased > 0');
		}
		elseif ($this->params['sale_type'] == 'returns')
		{
			$this->db->where('quantity_purchased < 0');
		}
		
		$this->db->where('deleted', 1);
		return $this->db->get()->row_array();
	}
}
?>