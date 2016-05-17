<?php
require_once ("Report.php");
class Summary_profit_and_loss extends Report
{
	function __construct()
	{
		parent::__construct();
	}
	
	public function getDataColumns()
	{
		return array();		
	}
	
	public function getData()
	{
		$location_ids = self::get_selected_location_ids();
		
		$total = 0;
		
		$this->db->select('sales_items_temp.category, sum(total) as total', false);
		$this->db->from('sales_items_temp');
		$this->db->join('items', 'sales_items_temp.item_id = items.item_id', 'left');
		
		$this->db->where($this->db->dbprefix('sales_items_temp').'.deleted', 0);
		$this->db->where('total > 0');
		$this->db->group_start();
		$this->db->where('items.name !=', lang('common_discount'));
		$this->db->or_where('items.name IS NULL');
		$this->db->group_end();
		
		
		$this->db->group_by('category');
		$this->db->order_by('category');
		
		$data['sales_by_category'] = $this->db->get()->result_array();
		
		
		$sales_total = 0;
		foreach($data['sales_by_category'] as $sales_by_category)
		{
			$sales_total+=$sales_by_category['total'];
		}	
		$total+=$sales_total;
		$data['sales_total'] = $sales_total;



		$this->db->select('sales_items_temp.category, sum(total) as total', false);
		$this->db->from('sales_items_temp');
		$this->db->join('items', 'sales_items_temp.item_id = items.item_id', 'left');
		
		$this->db->where($this->db->dbprefix('sales_items_temp').'.deleted', 0);
		$this->db->where('total < 0');
		$this->db->group_start();
		$this->db->where('items.name !=', lang('common_discount'));
		$this->db->or_where('items.name IS NULL');
		$this->db->group_end();
		
		
		$this->db->group_by('category');
		$this->db->order_by('category');
		
		$data['returns_by_category'] = $this->db->get()->result_array();
		
		$returns_total = 0;
		foreach($data['returns_by_category'] as $returns_by_category)
		{
			$returns_total+=$returns_by_category['total'];
		}

		$total+=$returns_total;
		$data['returns_total'] = $returns_total;
		
		$this->db->select('category, sum(total) as total', false);
		$this->db->from('receivings_items_temp');
		
		$this->db->where($this->db->dbprefix('receivings_items_temp').'.deleted', 0);
		$receivings_row = $this->db->get()->row_array();
		$data['receivings_total'] = $receivings_row['total'];
		
		
		$this->db->select('SUM(item_unit_price * quantity_purchased * ( discount_percent /100 )) as discount');
		$this->db->from('sales_items_temp');
		$this->db->where('discount_percent > 0');
		$this->db->where($this->db->dbprefix('sales_items_temp').'.deleted', 0);
		
		$discount_row = $this->db->get()->row_array();
		$data['discount_total'] = $discount_row['discount'];
		
		$this->db->select('SUM(item_unit_price * quantity_purchased) as discount');
		$this->db->from('sales_items_temp');
		$this->db->join('items', 'sales_items_temp.item_id = items.item_id');
		$this->db->where('items.name', lang('common_discount'));

		$result=$this->db->get();				
		if ($result->num_rows() > 0)
		{
			$query_result = $result->result();
			$discount = $query_result[0]->discount;
			
			$data['discount_total']+= -$discount;
		}
		
		$total-=$data['discount_total'];
		
		$this->db->select('sum(tax) as tax', false);
		$this->db->from('sales_items_temp');
		$this->db->where('deleted', 0);
		$tax_row = $this->db->get()->row_array();
		$data['taxes_total'] = $tax_row['tax'];
		
		$total-=$tax_row['tax'];
		$data['total'] = $total;
		
      $this->db->select('sum(expense_amount) as expense_amount', false);
		$this->db->from('expenses');
		$this->db->where('deleted', 0);
      $this->db->where('date(expense_date) BETWEEN '. $this->db->escape($this->params['start_date']). ' and '. $this->db->escape($this->params['end_date']));
		$expenses_row = $this->db->get()->row_array();		
      $data['expense_amount'] = $expenses_row ['expense_amount'];
		
		
		$this->db->select('sum(profit) as profit, SUM(commission) as commission', false);
		$this->db->from('sales_items_temp');
		$this->db->where('deleted', 0);
		$profit_row = $this->db->get()->row_array();

      $data['commission'] = $profit_row ['commission'];
		
		$data['profit'] = $profit_row['profit'] - $data['expense_amount'] - $profit_row['commission'];
                
		return $data;
	}
	
	public function getSummaryData()
	{
		return array();
	}
}
?>