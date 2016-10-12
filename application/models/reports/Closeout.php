<?php
require_once ("Report.php");
class Closeout extends Report
{
	function __construct()
	{
		parent::__construct();
	}

	public function getDataColumns()
	{
		$columns = array();
		
		$columns[] = array('data'=>lang('reports_description'), 'align'=> 'left');
		$columns[] = array('data'=>lang('reports_data'), 'align'=> 'left');
		
		return $columns;		
	}
	
	public function getData()
	{		
		
		$return = array();
		
		$location_ids = self::get_selected_location_ids();
		$location_ids_string = implode(',',$location_ids);
		
		//Sales
		$this->db->select('sum(total) as total, sum(tax) as tax, sum(profit) as profit, sum(quantity_purchased) as quantity', false);
		$this->db->from('sales_items_temp');
		$this->db->where('quantity_purchased > 0');
		
		$this->db->where('deleted', 0);
		$this->db->group_by('sale_id');
		$this->db->order_by('sale_time', ($this->config->item('report_sort_order')) ? $this->config->item('report_sort_order') : 'asc');
		
				
		$sales_row = array(
			'total' => 0,
			'tax' => 0,
			'profit' => 0,
			'quantity' => 0,
		);
		
		foreach($this->db->get()->result_array() as $row)
		{
			$sales_row['total'] += to_currency_no_money($row['total'],2);
			$sales_row['tax'] += to_currency_no_money($row['tax'],2);
			$sales_row['profit'] += to_currency_no_money($row['profit'],2);
			$sales_row['quantity'] += $row['quantity'];
		}
				
		$yesterday = date('Y-m-d', strtotime($this->params['date'].' -1 days'));
		$tomorrow = date('Y-m-d', strtotime($this->params['date'].' +1 days'));

		$return[] = array(anchor('reports/closeout/'.$yesterday,'&laquo; '.lang('reports_previous_day')), anchor('reports/closeout/'.$tomorrow,lang('reports_next_day').' &raquo;'));

		$return[] = array('<h1>'.lang('reports_sales').'</h1>', '--');
		$return[] = array(lang('reports_total_sales'). ' ('.lang('common_without_tax').')', isset($sales_row['total']) ? to_currency($sales_row['total'] - $sales_row['tax']) : 0);
		$return[] = array(lang('reports_total_sales').' ('.lang('reports_items_with_tax').')', isset($sales_row['total']) ? to_currency($sales_row['total']) : 0);
		if($this->Employee->has_module_action_permission('reports','show_profit',$this->Employee->get_logged_in_employee_info()->person_id))
		{
			$return[] = array(lang('reports_profit'), isset($sales_row['profit']) ? to_currency($sales_row['profit']) : 0);
		}
					
		$this->load->model('reports/Inventory_summary');
		$model_inv_sum = $this->Inventory_summary;
		$model_inv_sum->setParams(array('supplier'=>-1,'category_id' => -1, 'export_excel' => 1, 'offset'=>0, 'inventory' => 'all','show_only_pending' => 0));
		
		$summary_data = $model_inv_sum->getSummaryData();
		
		$return[] = array(lang('reports_total_items_in_inventory'), to_quantity($summary_data['total_items_in_inventory']));
		$return[] = array(lang('reports_inventory_total'), to_currency($summary_data['inventory_total']));
		
		
		$return[] = array('&nbsp;', '&nbsp;');
		
		$this->db->select('category, sum(subtotal) as subtotal, sum(total) as total', false);
		$this->db->from('sales_items_temp');
		$this->db->where('quantity_purchased > 0');
		
		$this->db->where($this->db->dbprefix('sales_items_temp').'.deleted', 0);
		$this->db->group_by('category');
		$this->db->order_by('category');
		$category_sales = $this->db->get()->result_array();		
		
		
		foreach($category_sales as $category_sale_row)
		{
			$return[] = array($category_sale_row['category'],to_currency($category_sale_row['total']));
		}
		$return[] = array('&nbsp;', '&nbsp;');
		
		
		//Sales total count for day
		$this->db->from('sales_items_temp');
		$this->db->where('quantity_purchased > 0');
		$this->db->where('deleted', 0);
		$this->db->group_by('sale_id');
		
		$number_of_sales_transactions = $this->db->get()->num_rows();
		$average_ticket_size = $number_of_sales_transactions > 0 ? $sales_row['total']/$number_of_sales_transactions : 0;
		
		$return[] = array(lang('reports_number_of_transactions'), to_quantity($number_of_sales_transactions));
		$return[] = array(lang('reports_average_ticket_size'), to_currency($average_ticket_size));
		
		$return[] = array(lang('common_items_sold'), isset($sales_row['quantity']) ? to_quantity($sales_row['quantity']) : 0);
		
		$return[] = array('&nbsp;', '&nbsp;');
		
		$return[] = array(lang('common_tax'), isset($sales_row['tax']) ? to_currency($sales_row['tax']) : 0);		
		
		$this->load->model('reports/Summary_taxes');
		
		$this->Summary_taxes->setParams(array('start_date'=>$this->params['date'], 'end_date'=>$this->params['date'].' 23:59:59','sale_type' => 'sales'));
		$taxes = $this->Summary_taxes->getData();
		
		foreach($taxes as $tax_row)
		{
			if ($tax_row['name'] != lang('reports_non_taxable'))
			{
				$return[] = array($tax_row['name'], lang('common_tax').': '.to_currency($tax_row['tax']).'<br />'.lang('reports_subtotal').': '.to_currency($tax_row['subtotal']).'<br />'.lang('reports_total').': '.to_currency($tax_row['total']));		
			}
		}
		
		if(isset($taxes[lang('reports_non_taxable')]))
		{
			$return[] = array(lang('reports_non_taxable'), to_currency($taxes[lang('reports_non_taxable')]['total']));
		}
		
		$return[] = array('&nbsp;', '&nbsp;');

		
		$this->db->select('sales_payments.sale_id, sales_payments.payment_type, payment_amount, payment_id', false);
		$this->db->from('sales_payments');
		$this->db->join('sales', 'sales.sale_id=sales_payments.sale_id');
		$this->db->where('payment_date BETWEEN '. $this->db->escape($this->params['date']). ' and '. $this->db->escape($this->params['date']. ' 23:59:59').' and location_id IN('.$location_ids_string.')');
		
		if ($this->config->item('hide_store_account_payments_in_reports'))
		{
			$this->db->where('store_account_payment',0);
		}
		
		$this->db->where('payment_amount > 0');
		
		$this->db->where($this->db->dbprefix('sales').'.deleted', 0);
		$this->db->order_by('sale_id, payment_date , payment_type');
				
		$sales_payments = $this->db->get()->result_array();

		$payments_by_sale = array();
		foreach($sales_payments as $row)
		{
        	$payments_by_sale[$row['sale_id']][] = $row;
		}
		
		$payment_data = $this->Sale->get_payment_data($payments_by_sale,$this->params['sales_total_for_payments']);
		
		foreach($payment_data as $payment_row)
		{
			$return[] = array($payment_row['payment_type'],to_currency($payment_row['payment_amount']));
		}
		
		
		if ($this->config->item('enable_customer_loyalty_system') && $this->config->item('loyalty_option') == 'advanced')
		{
			$points = array();
		
			$this->db->select('SUM(points_used) as points_used, SUM(points_gained) as points_gained', false);
			$this->db->from('sales');
			$this->db->where('date(sale_time) BETWEEN '. $this->db->escape($this->params['date']). ' and '. $this->db->escape($this->params['date']. ' 23:59:59').' and location_id IN('.$location_ids_string.')');
			$this->db->where('deleted', 0);
			$this->db->where_in('location_id',$location_ids);
		
			$points = $this->db->get()->row_array();
		
			$return[] = array(lang('reports_points_used'), to_currency_no_money($points['points_used']));
			$return[] = array(lang('reports_points_earned'), to_currency_no_money($points['points_gained']));
		
		}
		if ($this->config->item('customers_store_accounts'))
		{
			$this->db->select("SUM(IF(transaction_amount > 0, `transaction_amount`, 0)) as debits, SUM(IF(transaction_amount < 0, `transaction_amount`, 0)) as credits", false);
			$this->db->from('store_accounts');
			$this->db->join('customers', 'customers.person_id = store_accounts.customer_id');
			$this->db->join('people', 'customers.person_id = people.person_id');
			$this->db->where('date BETWEEN '.$this->db->escape($this->params['date']).' and '.$this->db->escape($this->params['date']. ' 23:59:59'));
			
			$return[] = array('<h1>'.lang('reports_store_account').'</h1>', '--');
			
			//Store account info
			$store_account_credits_and_debits = $this->db->get()->row_array();
		
			$this->db->select('SUM(balance) as total_balance_of_all_store_accounts', false);
			$this->db->from('customers');		
			$total_store_account_balances = $this->db->get()->row_array();
		
			$store_account_info = array_merge($store_account_credits_and_debits, $total_store_account_balances);
			$return[] = array(lang('reports_debits'),to_currency($store_account_info['debits']));
			$return[] = array(lang('reports_credits'),to_currency(abs($store_account_info['credits'])));
			$return[] = array(lang('reports_total_balance_of_all_store_accounts'),to_currency($store_account_info['total_balance_of_all_store_accounts']));
		}
		
		$this->db->select('sum(total) as total, sum(tax) as tax, sum(quantity_purchased) as quantity', false);
		$this->db->from('sales_items_temp');
		$this->db->where('quantity_purchased < 0');
		
		$this->db->where('deleted', 0);
		$this->db->group_by('sale_id');
		$this->db->order_by('sale_time', ($this->config->item('report_sort_order')) ? $this->config->item('report_sort_order') : 'asc');
		
		$sales_row = array(
			'total' => 0,
			'tax' => 0,
			'quantity' => 0,
		);
		
		foreach($this->db->get()->result_array() as $row)
		{
			$sales_row['total'] += to_currency_no_money($row['total'],2);
			$sales_row['tax'] += to_currency_no_money($row['tax'],2);
			$sales_row['quantity'] += $row['quantity'];
		}
		
		$return[] = array('<h1>'.lang('reports_returns').'</h1>', '--');
		$return[] = array(lang('reports_total_returned'), isset($sales_row['total']) ? to_currency(abs($sales_row['total'])) : 0);
		
		$this->db->select('category, sum(subtotal) as subtotal, sum(total) as total', false);
		$this->db->from('sales_items_temp');
		$this->db->where('quantity_purchased < 0');
		
		$this->db->where($this->db->dbprefix('sales_items_temp').'.deleted', 0);
		$this->db->group_by('category');
		$this->db->order_by('category');
		$category_returns = $this->db->get()->result_array();		
		
		
		foreach($category_returns as $category_sale_row)
		{
			$return[] = array($category_sale_row['category'],to_currency(abs($category_sale_row['total'])));
		}
		
		//Sales total count for day
		$this->db->from('sales_items_temp');
		$this->db->where('quantity_purchased < 0');
		$this->db->where('deleted', 0);
		$this->db->group_by('sale_id');
		
		$number_of_returned_transactions = $this->db->get()->num_rows();
		
		$return[] = array(lang('reports_number_of_transactions'), to_quantity($number_of_returned_transactions));
		$return[] = array(lang('reports_items_returned'), isset($sales_row['quantity']) ? to_quantity(abs($sales_row['quantity'])) : 0);
		$return[] = array(lang('common_tax'), isset($sales_row['tax']) ? to_currency(abs($sales_row['tax'])) : 0);
		
		
		$location_ids = self::get_selected_location_ids();
		$location_ids_string = implode(',',$location_ids);
		
		$this->db->select('sales_payments.sale_id, sales_payments.payment_type, payment_amount, payment_id', false);
		$this->db->from('sales_payments');
		$this->db->join('sales', 'sales.sale_id=sales_payments.sale_id');
		$this->db->where('payment_date BETWEEN '. $this->db->escape($this->params['date']). ' and '. $this->db->escape($this->params['date']. ' 23:59:59').' and location_id IN('.$location_ids_string.')');
		
		if ($this->config->item('hide_store_account_payments_in_reports'))
		{
			$this->db->where('store_account_payment',0);
		}
		
		$this->db->where('payment_amount < 0');
		
		$this->db->where($this->db->dbprefix('sales').'.deleted', 0);
		$this->db->order_by('sale_id, payment_date , payment_type');
				
		$sales_payments = $this->db->get()->result_array();

		$payments_by_sale = array();
		foreach($sales_payments as $row)
		{
        	$payments_by_sale[$row['sale_id']][] = $row;
		}
		
		$payment_data = $this->Sale->get_payment_data($payments_by_sale,$this->params['sales_total_for_payments']);
		
		foreach($payment_data as $payment_row)
		{
			$return[] = array($payment_row['payment_type'],to_currency(abs($payment_row['payment_amount'])));
		}
		
		//Receivings
		
		$this->db->select('sum(total) as total, sum(tax) as tax, sum(quantity_purchased) as quantity', false);
		$this->db->from('receivings_items_temp');
		$this->db->where('quantity_purchased > 0');
		
		$this->db->where('deleted', 0);
		$this->db->group_by('receiving_date');
		$this->db->order_by('receiving_time', ($this->config->item('report_sort_order')) ? $this->config->item('report_sort_order') : 'asc');
		
				
		$recvs_row = $this->db->get()->row_array();
		
		$return[] = array('<h1>'.lang('reports_receivings').'</h1>', '--');
		$return[] = array(lang('reports_total_receivings'). ' ('.lang('common_without_tax').')', isset($recvs_row['total']) ? to_currency($recvs_row['total'] - $recvs_row['tax']) : 0);
		$return[] = array(lang('reports_total_receivings').' ('.lang('reports_items_with_tax').')', isset($recvs_row['total']) ? to_currency($recvs_row['total']) : 0);		
		$return[] = array('&nbsp;', '&nbsp;');
		
		$this->db->select('category, sum(subtotal) as subtotal, sum(total) as total', false);
		$this->db->from('receivings_items_temp');
		$this->db->where('quantity_purchased > 0');
		
		$this->db->where($this->db->dbprefix('receivings_items_temp').'.deleted', 0);
		$this->db->group_by('category');
		$this->db->order_by('category');
		$category_recvs = $this->db->get()->result_array();		
		
		
		foreach($category_recvs as $category_recv_row)
		{
			$return[] = array($category_recv_row['category'],to_currency($category_recv_row['total']));
		}
		$return[] = array('&nbsp;', '&nbsp;');
		
		
		//rececvings total count for day
		$this->db->from('receivings_items_temp');
		$this->db->where('quantity_purchased > 0');
		$this->db->where('deleted', 0);
		$this->db->group_by('receiving_id');
		
		$number_of_recevings_transactions = $this->db->get()->num_rows();
		$average_ticket_size = $number_of_recevings_transactions > 0 ? $recvs_row['total']/$number_of_recevings_transactions : 0;
		
		
		$return[] = array(lang('reports_number_of_transactions'), to_quantity($number_of_recevings_transactions));
		$return[] = array(lang('reports_average_ticket_size'), to_currency($average_ticket_size));
		
		$return[] = array(lang('reports_items_recved'), isset($recvs_row['quantity']) ? to_quantity($recvs_row['quantity']) : 0);
		$return[] = array('&nbsp;', '&nbsp;');
		
		$return[] = array(lang('common_tax'), isset($recvs_row['tax']) ? to_currency($recvs_row['tax']) : 0);
		
		$taxes_data = array();
		$this->load->model('reports/Summary_taxes_receivings');
		
		$this->Summary_taxes_receivings->setParams(array('start_date'=>$this->params['date'], 'end_date'=>$this->params['date'].' 23:59:59','sale_type' => 'sales'));
		$taxes = $this->Summary_taxes_receivings->getData();
		
		foreach($taxes as $tax_row)
		{
			if ($tax_row['name'] != lang('reports_non_taxable'))
			{
				$return[] = array($tax_row['name'], lang('common_tax').': '.to_currency($tax_row['tax']).'<br />'.lang('reports_subtotal').': '.to_currency($tax_row['subtotal']).'<br />'.lang('reports_total').': '.to_currency($tax_row['total']));		
			}
		}
		
		if(isset($taxes[lang('reports_non_taxable')]))
		{
			$return[] = array(lang('reports_non_taxable'), to_currency($taxes[lang('reports_non_taxable')]['total']));
		}
		
		$return[] = array('&nbsp;', '&nbsp;');
		
		$this->db->select('categories.name as category, SUM(expense_amount) as amount', false);
		$this->db->from('expenses');
		$this->db->join('categories', 'categories.id = expenses.category_id','left');
		$this->db->where('expenses.deleted', 0);
		$this->db->group_by('categories.id');
		$this->db->where($this->db->dbprefix('expenses').'.expense_date BETWEEN '. $this->db->escape($this->params['date']). ' and '. $this->db->escape($this->params['date']. ' 23:59:59').' and location_id IN('.$location_ids_string.')');
		$this->db->order_by('expenses.id');

		$category_expenses = $this->db->get()->result_array();		
		
		$total = 0;
		
		foreach($category_expenses as $category_sale_row)
		{
			$total += $category_sale_row['amount'];
		}
		
		$return[] = array('<h1>'.lang('common_expenses').'</h1>', '--');
		$return[] = array(lang('reports_total_expenses'), to_currency($total));
		
		foreach($category_expenses as $category_sale_row)
		{
			$return[] = array($category_sale_row['category'],to_currency($category_sale_row['amount']));
		}
		
		
		//Cash Tracking
		if ($this->config->item('track_cash'))
		{
			$between = 'between ' . $this->db->escape($this->params['date'] . ' 00:00:00').' and ' . $this->db->escape($this->params['date'] . ' 23:59:59');
			$this->db->select("locations.name as location_name, registers.name as register_name, register_log.*, (register_log.close_amount - register_log.open_amount - register_log.cash_sales_amount - register_log.total_cash_additions + register_log.total_cash_subtractions) as difference");
			$this->db->from('register_log as register_log');
			$this->db->join('registers', 'registers.register_id = register_log.register_id');
			$this->db->join('locations', 'registers.location_id = locations.location_id');
			$this->db->where('register_log.shift_end ' . $between);
			$this->db->where('register_log.deleted ', 0);
			$this->db->where_in('registers.location_id', $location_ids);
		
			$cash_logging = $this->db->get()->result_array();
			
			$return[] = array('<h1>'.lang('common_track_cash').'</h1>', '--');
			
			
			foreach($cash_logging as $cash_logging_row)
			{
				$data = lang('common_opening_amount').': '.to_currency($cash_logging_row['open_amount']);
				
				if ($cash_logging_row['shift_end']=='0000-00-00 00:00:00')
				{
					$data.= ' / '.lang('common_closing_amount').': '.lang('reports_register_log_open');
					$data .= ' / '.lang('common_cash_sales').': '.to_currency($cash_logging_row['cash_sales_amount']);					
					$data .= ' / '.lang('common_total_cash_additions').': '.to_currency($cash_logging_row['total_cash_additions']);					
					$data .= ' / '.lang('common_total_cash_subtractions').': '.to_currency($cash_logging_row['total_cash_subtractions']);					
				}
				else
				{					
					$data .= ' / '.lang('common_closing_amount').': '.to_currency($cash_logging_row['close_amount']);					
					$data .= ' / '.lang('common_cash_sales').': '.to_currency($cash_logging_row['cash_sales_amount']);					
					$data .= ' / '.lang('common_total_cash_additions').': '.to_currency($cash_logging_row['total_cash_additions']);					
					$data .= ' / '.lang('common_total_cash_subtractions').': '.to_currency($cash_logging_row['total_cash_subtractions']);					
				}

				$data .= ' / '.lang('reports_difference').': '.to_currency($cash_logging_row['difference']);					
				
				$return[] = array('<h2>'.$cash_logging_row['register_name'].' ('.$cash_logging_row['location_name'].')</h2>' .date(get_date_format().' '.get_time_format(), strtotime($cash_logging_row['shift_start'])).' - '.date(get_date_format().' '.get_time_format(), strtotime($cash_logging_row['shift_end'])),$data);
			}
		}
		
		
		$return[] = array(anchor('reports/closeout/'.$yesterday,'&laquo; '.lang('reports_previous_day')), anchor('reports/closeout/'.$tomorrow,lang('reports_next_day').' &raquo;'));
		
		
		return $return;
	}
	
		
	
	public function getSummaryData()
	{
		return array();
	}
}
?>