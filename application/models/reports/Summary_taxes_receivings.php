<?php
require_once ("Report.php");
class Summary_taxes_receivings extends Report
{
	function __construct()
	{
		parent::__construct();
	}
	
	public function getDataColumns()
	{
		return array(array('data'=>lang('reports_tax_percent'), 'align'=>'left'),array('data'=>lang('reports_subtotal'), 'align'=>'left'), array('data'=>lang('common_tax'), 'align'=>'left'),array('data'=>lang('reports_total'), 'align'=>'left'));
	}
	
	public function getData()
	{
		$this->db->select('receiving_id, item_id, line');
		$this->db->from('receivings_items_temp');
		
		if ($this->params['sale_type'] == 'sales')
		{
			$this->db->where('quantity_purchased > 0');
		}
		elseif ($this->params['sale_type'] == 'returns')
		{
			$this->db->where('quantity_purchased < 0');
		}
		
		$this->db->where($this->db->dbprefix('receivings_items_temp').'.deleted', 0);
		
		$taxes_data = array();
		foreach($this->db->get()->result_array() as $row)
		{
			$this->getTaxesForItems($row['receiving_id'], $row['item_id'], $row['line'], $taxes_data);
		}
		
		$this->getNonTaxableTotalForItems($taxes_data);
				
		//If we are exporting NOT exporting to excel make sure to use offset and limit
		if (isset($this->params['export_excel']) && !$this->params['export_excel'])
		{
			$taxes_data = array_slice($taxes_data, $this->params['offset'], $this->report_limit);
		}
		
		
		return $taxes_data;
	}
	
	function getTotalRows()
	{
		$location_ids = self::get_selected_location_ids();
		$location_ids_string = implode(',',$location_ids);
		
		$this->db->select('COUNT(DISTINCT(CONCAT('.$this->db->dbprefix('receivings_items_taxes').'.name,'.$this->db->dbprefix('receivings_items_taxes').'.percent))) as tax_count', false);
		$this->db->from('receivings_items_taxes');
		$this->db->join('receivings', 'receivings.receiving_id=receivings_items_taxes.receiving_id');
		$this->db->where('receiving_time BETWEEN '. $this->db->escape($this->params['start_date']). ' and '. $this->db->escape($this->params['end_date']).' and location_id IN('.$location_ids_string.')');
		$this->db->where($this->db->dbprefix('receivings').'.deleted', 0);
		
		$ret = $this->db->get()->row_array();
		
		//add 1 for non taxable
		return $ret['tax_count'] + 1;
	}
	
	function getTaxesForItems($receiving_id, $item_id, $line, &$taxes_data)
	{
		$query = $this->db->query("SELECT name, percent, cumulative, item_unit_price, item_cost_price, quantity_purchased, discount_percent FROM ".$this->db->dbprefix('receivings_items_taxes').' 
		JOIN '.$this->db->dbprefix('receivings_items'). ' USING(receiving_id, item_id, line) WHERE '.
		$this->db->dbprefix('receivings_items_taxes').'.receiving_id = '.$receiving_id.' and '.
		$this->db->dbprefix('receivings_items_taxes').'.item_id = '.$item_id.' and '.
		$this->db->dbprefix('receivings_items_taxes').'.line = '.$line. ' ORDER BY cumulative');
		
		$tax_result = $query->result_array();
		for($k=0;$k<count($tax_result);$k++)
		{
			$row = $tax_result[$k];
			if ($row['cumulative'])
			{
				$previous_tax = $tax;
				$subtotal = ($row['item_unit_price']*$row['quantity_purchased']-$row['item_unit_price']*$row['quantity_purchased']*$row['discount_percent']/100);
				$tax = ($subtotal + $tax) * ($row['percent'] / 100);
			}
			else
			{
				$subtotal = ($row['item_unit_price']*$row['quantity_purchased']-$row['item_unit_price']*$row['quantity_purchased']*$row['discount_percent']/100);
				$tax = $subtotal * ($row['percent'] / 100);
			}
			
			if (empty($taxes_data[$row['name'].' ('.$row['percent'] . '%)']))
			{
				$taxes_data[$row['name'].' ('.$row['percent'] . '%)'] = array('name' => $row['name'].' ('.$row['percent'] . '%)', 'tax' => 0, 'subtotal' => 0, 'total' => 0);
			}
						
			$taxes_data[$row['name'].' ('.$row['percent'] . '%)']['subtotal'] += to_currency_no_money($subtotal);
			$taxes_data[$row['name'].' ('.$row['percent'] . '%)']['tax'] += ($tax);
			$taxes_data[$row['name'].' ('.$row['percent'] . '%)']['total'] += ($subtotal+ $tax);
			
		}
		
	}
		
	function getNonTaxableTotalForItems(&$taxes_data)
	{
		$where_addtion_suspended = '';
		
		if ($this->config->item('hide_suspended_recv_in_reports'))
		{
			$where_addtion_suspended =' and suspended = 0';
		}
		
		
		$location_ids = self::get_selected_location_ids();
		$location_ids_string = implode(',',$location_ids);
	
		$non_taxable_items_result = $this->db->query("SELECT item_unit_price as unit_price, quantity_purchased, discount_percent 
		FROM `".$this->db->dbprefix('receivings_items')."` 
		LEFT OUTER JOIN `".$this->db->dbprefix('receivings_items_taxes')."` USING (receiving_id, item_id, line)
		INNER join `".$this->db->dbprefix('receivings')."` USING (receiving_id)
		WHERE ".$this->db->dbprefix('receivings').".receiving_time BETWEEN ".$this->db->escape($this->params['start_date'])." and ".$this->db->escape($this->params['end_date']).
		" and ".$this->db->dbprefix('receivings').".location_id IN(".$location_ids_string.") and percent IS NULL and deleted = 0 $where_addtion_suspended");
	
		$total = 0;
		foreach($non_taxable_items_result->result_array() as $row)
		{
			$total += to_currency_no_money($row['unit_price']*$row['quantity_purchased']-$row['unit_price']*$row['quantity_purchased']*$row['discount_percent']/100);	
		}
		
		$total = $total;
		$taxes_data[lang('reports_non_taxable')] = array(
			'name' => lang('reports_non_taxable'),
			'subtotal' => $total,
			'total' => $total,
			'tax' => 0
		);
			
	}
	public function getSummaryData()
	{
		$this->db->select('sum(subtotal) as subtotal, sum(total) as total, sum(tax) as tax, sum(profit) as profit', false);
		$this->db->from('receivings_items_temp');
		
		if ($this->params['sale_type'] == 'sales')
		{
			$this->db->where('quantity_purchased > 0');
		}
		elseif ($this->params['sale_type'] == 'returns')
		{
			$this->db->where('quantity_purchased < 0');
		}
		
		$this->db->where($this->db->dbprefix('receivings_items_temp').'.deleted', 0);
				
		$this->db->group_by('receiving_id');
		
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