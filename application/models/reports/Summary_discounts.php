<?php
require_once ("Report.php");
class Summary_discounts extends Report
{
	function __construct()
	{
		parent::__construct();
	}
	
	public function getDataColumns()
	{
		return array(array('data'=>lang('common_discount'), 'align'=> 'left'),array('data'=>lang('common_count').'/'.lang('reports_total'), 'align'=> 'left'));
	}
	
	public function getData()
	{
		$return = array();
		
		$this->db->select('CONCAT(discount_percent, "%") as discount, count(*) as summary', false);
		$this->db->from('sales_items_temp');
		$this->db->where('discount_percent > 0');
		if ($this->params['sale_type'] == 'sales')
		{
			$this->db->where('quantity_purchased > 0');
		}
		elseif ($this->params['sale_type'] == 'returns')
		{
			$this->db->where('quantity_purchased < 0');
		}
		$this->db->where($this->db->dbprefix('sales_items_temp').'.deleted', 0);
		$this->db->group_by('sales_items_temp.discount_percent');
		$this->db->order_by('discount_percent');
				
		$percent_discounts = $this->db->get()->result_array();
		$return = $percent_discounts;
		
		
		$this->db->select('COUNT(*) as discount_count');
		$this->db->from('sales_items_temp');
		$this->db->join('items', 'sales_items_temp.item_id = items.item_id');
		$this->db->where('items.name', lang('common_discount'));
		$discount_count = $this->db->get()->row()->discount_count;
		
		$this->db->select('SUM(item_unit_price * quantity_purchased) as discount_total');
		$this->db->from('sales_items_temp');
		$this->db->join('items', 'sales_items_temp.item_id = items.item_id');
		$this->db->where('items.name', lang('common_discount'));


		$result=$this->db->get();				
		if ($result->num_rows() > 0)
		{
			$query_result = $result->result();
			$discount = $query_result[0]->discount_total;
			
			$return[] = array('discount_count' =>$discount_count, 'discount' => lang('common_discount'), 'summary' => to_currency(abs($discount)));
		}
		
		return $return;
	}
	
	function getTotalRows()
	{
		$this->db->select('COUNT(DISTINCT(discount_percent)) as discount_count');
		$this->db->from('sales_items_temp');
		if ($this->params['sale_type'] == 'sales')
		{
			$this->db->where('quantity_purchased > 0');
		}
		elseif ($this->params['sale_type'] == 'returns')
		{
			$this->db->where('quantity_purchased < 0');
		}
		
		$this->db->where($this->db->dbprefix('sales_items_temp').'.deleted', 0);
		$this->db->where('discount_percent > 0');
		$ret = $this->db->get()->row_array();
		return $ret['discount_count'] + 1; // + 1 for flat discount
	}
	
	
	public function getSummaryData()
	{
		$this->db->select('sum(subtotal) as subtotal, sum(total) as total, sum(tax) as tax,sum(profit) as profit', false);
		$this->db->from('sales_items_temp');
		if ($this->params['sale_type'] == 'sales')
		{
			$this->db->where('quantity_purchased > 0');
		}
		elseif ($this->params['sale_type'] == 'returns')
		{
			$this->db->where('quantity_purchased < 0');
		}
		$this->db->where($this->db->dbprefix('sales_items_temp').'.deleted', 0);
		
		if ($this->config->item('hide_store_account_payments_from_report_totals'))
		{
			$this->db->where('store_account_payment', 0);
		}
		
		$this->db->group_by('sale_id');
		
		$return = array(
			'subtotal' => 0,
			'total' => 0,
			'tax' => 0,
			'profit' => 0,
		);
		
		foreach($this->db->get()->result_array() as $row)
		{
			$return['subtotal'] += to_currency_no_money($row['subtotal'],2);
			$return['total'] += to_currency_no_money($row['total'],2);
			$return['tax'] += to_currency_no_money($row['tax'],2);
			$return['profit'] += to_currency_no_money($row['profit'],2);
		}
		if(!$this->Employee->has_module_action_permission('reports','show_profit',$this->Employee->get_logged_in_employee_info()->person_id))
		{
			unset($return['profit']);
		}
		return $return;
	}
}
?>