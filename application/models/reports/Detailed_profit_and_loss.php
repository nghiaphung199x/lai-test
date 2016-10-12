<?php
require_once ("Report.php");
class Detailed_profit_and_loss extends Report
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
		
		$data = array();
		
		$sales_totals = array();
		$this->db->select('sale_id, sum(total) as total', false);
		$this->db->from('sales_items_temp');
		$this->db->group_by('sale_id');
			
		foreach($this->db->get()->result_array() as $sale_total_row)
		{
			$sales_totals[$sale_total_row['sale_id']] = to_currency_no_money($sale_total_row['total']);
		}

		$this->db->select('sales_payments.sale_id, sales_payments.payment_type, payment_amount', false);
		$this->db->from('sales_payments');
		$this->db->join('sales', 'sales.sale_id=sales_payments.sale_id');
		$this->db->where('date(sale_time) BETWEEN '. $this->db->escape($this->params['start_date']). ' and '. $this->db->escape($this->params['end_date']));
		
		//We only want sales, we don't want negative transactions
		$this->db->where('payment_amount > 0');
		
		$this->db->where($this->db->dbprefix('sales').'.deleted', 0);
		$this->db->where_in('location_id', $location_ids);
		
		$this->db->order_by('sale_id, payment_date, payment_type');
		$sales_payments = $this->db->get()->result_array();
		
		$payments_by_sale = array();
		
		foreach($sales_payments as $row)
		{
        	$payments_by_sale[$row['sale_id']][] = $row;
		}
		
		$payment_data = array();
		
		foreach($payments_by_sale as $sale_id => $payment_rows)
		{
			if(isset($sales_totals[$sale_id])){
				$total_sale_balance = $sales_totals[$sale_id];
			}
			
			foreach($payment_rows as $row)
			{
				$payment_amount = $row['payment_amount'] <= $total_sale_balance ? $row['payment_amount'] : $total_sale_balance;
				
				if (!isset($payment_data[$row['payment_type']]))
				{
					$payment_data[$row['payment_type']] = array('payment_type' => $row['payment_type'], 'payment_amount' => 0 );
				}
				
				if ($total_sale_balance != 0)
				{
					$payment_data[$row['payment_type']]['payment_amount'] += $payment_amount;
				}
				
				$total_sale_balance-=$payment_amount;
			}
		}
				
		$data['sales_by_payments'] = $payment_data;		
		
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
		
		foreach($data['sales_by_category'] as $sales_by_category)
		{
			$total+=$sales_by_category['total'];
		}
		
		
		$returns_total = array();
		$this->db->select('sale_id, sum(total) as total', false);
		$this->db->from('sales_items_temp');
		$this->db->group_by('sale_id');
			
		foreach($this->db->get()->result_array() as $return_total_row)
		{
			$returns_total[$return_total_row['sale_id']] = to_currency_no_money($return_total_row['total']);
		}

		$this->db->select('sales_payments.sale_id, sales_payments.payment_type, payment_amount', false);
		$this->db->from('sales_payments');
		$this->db->join('sales', 'sales.sale_id=sales_payments.sale_id');
		$this->db->where('date(sale_time) BETWEEN '. $this->db->escape($this->params['start_date']). ' and '. $this->db->escape($this->params['end_date']));
		
		//We only want returns, we don't want positive transactions
		$this->db->where('payment_amount < 0');
		
		$this->db->where($this->db->dbprefix('sales').'.deleted', 0);
		$this->db->where_in('location_id', $location_ids);
		
		$this->db->order_by('sale_id, payment_date, payment_type');
		$sales_payments = $this->db->get()->result_array();
		
		$payments_by_sale = array();
		
		foreach($sales_payments as $row)
		{
        	$payments_by_sale[$row['sale_id']][] = $row;
		}
		
		$payment_data = array();
		
		foreach($payments_by_sale as $sale_id => $payment_rows)
		{
			if(isset($returns_total[$sale_id])){
				$total_sale_balance = $returns_total[$sale_id];
			}
			
			foreach($payment_rows as $row)
			{
				$payment_amount = $row['payment_amount'] <= $total_sale_balance ? $row['payment_amount'] : $total_sale_balance;
				
				if (!isset($payment_data[$row['payment_type']]))
				{
					$payment_data[$row['payment_type']] = array('payment_type' => $row['payment_type'], 'payment_amount' => 0 );
				}
				
				if ($total_sale_balance != 0)
				{
					$payment_data[$row['payment_type']]['payment_amount'] += $payment_amount;
				}
				
				$total_sale_balance-=$payment_amount;
			}
		}
				
		$data['returns_by_payments'] = $payment_data;		
		
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
		
		foreach($data['returns_by_category'] as $returns_by_category)
		{
			$total+=$returns_by_category['total'];
		}
		
		
		$this->db->select('category, sum(total) as total', false);
		$this->db->from('receivings_items_temp');
		
		$this->db->where($this->db->dbprefix('receivings_items_temp').'.deleted', 0);
		$this->db->group_by('category');
		$this->db->order_by('category');

		$data['receivings_by_category'] = $this->db->get()->result_array();
		
		
		$this->db->select('SUM(item_unit_price * quantity_purchased * ( discount_percent /100 )) as discount');
		$this->db->from('sales_items_temp');
		$this->db->where('discount_percent > 0');
		$this->db->where($this->db->dbprefix('sales_items_temp').'.deleted', 0);
		
		$data['discount_total'] = $this->db->get()->row_array();
		
		
		$this->db->select('SUM(item_unit_price * quantity_purchased) as discount');
		$this->db->from('sales_items_temp');
		$this->db->join('items', 'sales_items_temp.item_id = items.item_id');
		$this->db->where('items.name', lang('common_discount'));

		$result=$this->db->get();				
		if ($result->num_rows() > 0)
		{
			$query_result = $result->result();
			$discount = $query_result[0]->discount;
			
			$data['discount_total']['discount']+= -$discount;
		}
		
		$total-=$data['discount_total']['discount'];
		
				
		$this->db->select('sum(tax) as tax', false);
		$this->db->from('sales_items_temp');
		$this->db->where('deleted', 0);
		$data['taxes'] = $this->db->get()->row_array();
		
		$total-=$data['taxes']['tax'];
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