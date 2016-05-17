<?php
require_once ("Report.php");
class Specific_supplier_receiving extends Report
{
	function __construct()
	{		
		parent::__construct();
	}
	
	public function getDataColumns()
	{

		$return = array();
		
		$return['summary'] = array();
		$location_count = count(self::get_selected_location_ids());
		
		$return['summary'][] = array('data'=>lang('reports_receiving_id'), 'align'=> 'left');
		
		if ($location_count > 1)
		{
			$return['summary'][] = array('data'=>lang('common_location'), 'align'=> 'left');
		}
		
		
		$return['summary'][] = array('data'=>lang('reports_date'), 'align'=> 'left');
		$return['summary'][] = array('data'=>lang('reports_items_ordered'), 'align'=> 'left');
		$return['summary'][] = array('data'=>lang('common_qty_received'), 'align'=> 'left');
		$return['summary'][] = array('data'=>lang('reports_subtotal'), 'align'=> 'right');
		$return['summary'][] = array('data'=>lang('reports_total'), 'align'=> 'right');
		$return['summary'][] = array('data'=>lang('common_tax'), 'align'=> 'right');
		$return['summary'][] = array('data'=>lang('reports_payment_type'), 'align'=> 'right');
		$return['summary'][] = array('data'=>lang('reports_comments'), 'align'=> 'right');

		$return['details'] = array();
		$return['details'][] = array('data'=>lang('common_item_number'), 'align'=> 'left');
		$return['details'][] = array('data'=>lang('common_product_id'), 'align'=> 'left');
		$return['details'][] = array('data'=>lang('reports_name'), 'align'=> 'left');
		$return['details'][] = array('data'=>lang('reports_category'), 'align'=> 'left');
		$return['details'][] = array('data'=>lang('reports_items_ordered'), 'align'=> 'left');
		$return['details'][] = array('data'=>lang('common_qty_received'), 'align'=> 'left');
		$return['details'][] = array('data'=>lang('reports_subtotal'), 'align'=> 'right');
		$return['details'][] = array('data'=>lang('reports_total'), 'align'=> 'right');
		$return['details'][] = array('data'=>lang('common_tax'), 'align'=> 'right');
		$return['details'][] = array('data'=>lang('common_discount'), 'align'=> 'right');
		
		return $return;		
	}
	
	public function getData()
	{
		$data = array();
		$data['summary'] = array();
		$data['details'] = array();


		$this->db->select('locations.name as location_name, receiving_id, receiving_time, receiving_date, sum(quantity_purchased) as items_purchased, sum(quantity_received) as items_received, sum(total) as total, sum(subtotal) as subtotal, sum(tax) as tax, payment_type, comment', false);
		$this->db->from('receivings_items_temp');
		$this->db->join('locations', 'locations.location_id = receivings_items_temp.location_id');
		$this->db->where('supplier_id', $this->params['supplier_id']);

		if ($this->params['sale_type'] == 'sales')
		{
			$this->db->where('quantity_purchased > 0');
		}
		elseif ($this->params['sale_type'] == 'returns')
		{
			$this->db->where('quantity_purchased < 0');
		}

		$this->db->where('receivings_items_temp.deleted', 0);
		$this->db->group_by('receiving_id');
		$this->db->order_by('receiving_time', ($this->config->item('report_sort_order')) ? $this->config->item('report_sort_order') : 'asc');

		//If we are exporting NOT exporting to excel make sure to use offset and limit
		if (isset($this->params['export_excel']) && !$this->params['export_excel'])
		{
			$this->db->limit($this->report_limit);
			$this->db->offset($this->params['offset']);
		}		
		
		foreach($this->db->get()->result_array() as $sale_summary_row)
		{
			$data['summary'][$sale_summary_row['receiving_id']] = $sale_summary_row; 
		}
		
		$receiving_ids = array();
		
		foreach($data['summary'] as $sale_row)
		{
			$receiving_ids[] = $sale_row['receiving_id'];
		}
		$this->db->select('receiving_id, receiving_time, receiving_date, quantity_received, item_number, items.product_id as item_product_id, items.name as item_name, receivings_items_temp.category, quantity_purchased, serialnumber, receivings_items_temp.description,subtotal, total, tax, discount_percent', false);
		$this->db->from('receivings_items_temp');
		$this->db->join('items', 'receivings_items_temp.item_id = items.item_id', 'left');
		
		if (!empty($receiving_ids))
		{
			$receiving_ids_chunk = array_chunk($receiving_ids,25);
			$this->db->group_start();
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
		
		foreach($this->db->get()->result_array() as $sale_item_row)
		{
			$data['details'][$sale_item_row['receiving_id']][] = $sale_item_row;
		}
		return $data;
	}
	
	public function getTotalRows()
	{		
		$this->db->select("COUNT(DISTINCT(receiving_id)) as recv_count");
		$this->db->from('receivings_items_temp');
		$this->db->where('supplier_id', $this->params['supplier_id']);
		
		if ($this->params['sale_type'] == 'sales')
		{
			$this->db->where('quantity_purchased > 0');
		}
		elseif ($this->params['sale_type'] == 'returns')
		{
			$this->db->where('quantity_purchased < 0');
		}
		
		$this->db->where('receivings_items_temp.deleted', 0);
		$ret = $this->db->get()->row_array();
		return $ret['recv_count'];
	}
	
	
	public function getSummaryData()
	{
		$this->db->select('sum(total) as total,sum(subtotal) as subtotal, sum(tax) as tax', false);
		$this->db->from('receivings_items_temp');
		$this->db->where('receiving_date BETWEEN '. $this->db->escape($this->params['start_date']). ' and '. $this->db->escape($this->params['end_date']).' and supplier_id='.$this->db->escape($this->params['supplier_id']));
		
		if ($this->params['sale_type'] == 'sales')
		{
			$this->db->where('quantity_purchased > 0');
		}
		elseif ($this->params['sale_type'] == 'returns')
		{
			$this->db->where('quantity_purchased < 0');
		}
		$this->db->where('deleted', 0);
		
		
		$this->db->group_by('receiving_id');
		
		$return = array(
			'subtotal' => 0,
			'total' => 0,
			'tax' => 0,
		);
		
		foreach($this->db->get()->result_array() as $row)
		{
			$return['subtotal'] += to_currency_no_money($row['subtotal'],2);
			$return['total'] += to_currency_no_money($row['total'],2);
			$return['tax'] += to_currency_no_money($row['tax'],2);
		}
		return $return;
	}
}
?>