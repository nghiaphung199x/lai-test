<?php
require_once (APPPATH . "controllers/Reports.php");
class BizReports extends Reports 
{
	function __construct()
	{
		parent::__construct();
		$this->load->helper('items');
		$this->load->helper('bizexcel');
	}
	
	function summary_inventory($start_date, $end_date, $export_excel=0, $offset = 0)
	{
		$start_date = rawurldecode($start_date);
		$end_date = rawurldecode($end_date);
		$this->check_action_permission('view_inventory_reports');
		
		$locationIds = Report::get_selected_location_ids();
		
		$historyTrans = $this->Inventory->getAllHistoryTrans(['start_date' => $start_date, 'end_date' => $end_date, 'locationIds' => $locationIds]);
		
		$historyTransBefore = $this->Inventory->getAllHistoryTransBefore(['end_date' => $start_date, 'locationIds' => $locationIds]);
		
		$allItems = [];
		foreach ($historyTrans as $item) {
			$allItems[$item['location_id']][$item['item_id']][] = $item;
		}
		
		
		$allTransItems = [];
		foreach ($allItems as $locationId => $items) {
			foreach ($items as $itemId => $rows) {
				$totalIn = 0;
				$totalOut = 0;
				
				foreach ($rows as $row) {
					if ($row['trans_inventory'] > 0) {
						$totalIn += $row['trans_inventory'];
					} else {
						$totalOut += $row['trans_inventory'];
					}
				}
				$allTransItems[$locationId][$itemId] = [
						'item_id' => $itemId,
						'product_id' => $row['product_id'],
						'name' => $row['name'],
						'category' => $row['category'],
						'cost_price' => $row['cost_price'],
						'unit_price' => $row['unit_price'],
						'total_qty_in' => to_quantity($totalIn),
						'total_cost_in' => NumberFormatToCurrency($totalIn * $row['cost_price']),
						'total_cost_in_origin' => $totalIn * $row['cost_price'],
						'total_qty_out' => to_quantity($totalOut),
						'total_price_out' => NumberFormatToCurrency($totalOut * $row['unit_price']),
						'total_price_out_origin' => $totalOut * $row['unit_price']
				];
			}
			
		}
		
		$allItems = [];
		foreach ($allTransItems as $locationId => $items) {
			
			foreach ($items as $_item) {
				$item['trans_total_qty_before'] = 0;
				foreach ($historyTransBefore as $beforeItem) {
					if ($beforeItem['location_id'] == $locationId && $beforeItem['item_id'] == $_item['item_id'] ) {
						$_item['trans_total_qty_before'] = to_quantity($beforeItem['trans_total_qty']);
						$_item['trans_total_price_before'] = NumberFormatToCurrency($_item['trans_total_qty_before'] * $_item['unit_price']);
						$_item['trans_total_price_before_origin'] = $_item['trans_total_qty_before'] * $_item['unit_price'];
					}
				}
				$_item['trans_total_qty_after'] = to_quantity($_item['trans_total_qty_before'] + $_item['total_qty_in'] + $_item['total_qty_out']);
				$_item['trans_total_price_after'] = NumberFormatToCurrency($_item['trans_total_qty_after'] * $_item['unit_price']);
				$_item['trans_total_price_after_origin'] = $_item['trans_total_qty_after'] * $_item['unit_price'];
				$allItems[$locationId][] = $_item;
			}
		}
		
		foreach ($locationIds as $locationId) {
			if (!isset($allItems[$locationId])) {
				$allItems[$locationId] = [];
			}
		}
		
		if ($export_excel) {
			$bizExcel = new BizExcel('ASummaryInventory.xlsx');
			$bizExcel->setNumberRowStartBody(5)->setHeaderOfBody($this->getHeaderOfSummaryInventory());
			$index = 0;
			foreach ($allItems as $locationId => $items)
			{
				$location = $this->Location->get_info($locationId);
				$bizExcel->setDataExcel($items);
				$bizExcel->addToNewSheet($location->name)->generateFile(false, '', false);
				$index ++;
			}
			
			$excelContent = $bizExcel->generateFile(false);
			$this->load->helper('download');
			force_download('SummaryInventory.xlsx', $excelContent);
		} else {
			
			$data = array(
				"title" => lang('reports_summary_inventory_report'),
				"subtitle" => date(get_date_format(), strtotime($start_date)) .'-'.date(get_date_format(), strtotime($end_date)),
				"data" => $allItems,
			);
			$this->load->view("reports/summary_inventory", $data);
			
		}
		
	}
	
	protected function getHeaderOfSummaryInventory() {
		return array(
				array(
						'col' => 'A',
						'value_field' => 'product_id',
				),
				array(
						'col' => 'B',
						'value_field' => 'name',
				),
				array(
						'col' => 'C',
						'value_field' => 'trans_total_qty_before',
						'footer' => 'SUM'
				),
				array(
						'col' => 'D',
						'value_field' => 'trans_total_price_before',
						'footer' => 'SUM'
				),
	
				array(
						'col' => 'E',
						'value_field' => 'total_qty_in',
						'footer' => 'SUM'
				),
				array(
						'col' => 'F',
						'value_field' => 'total_cost_in',
						'footer' => 'SUM'
				),
				array(
						'col' => 'G',
						'value_field' => 'total_qty_out',
						'footer' => 'SUM'
				),
				array(
						'col' => 'H',
						'value_field' => 'total_price_out',
						'footer' => 'SUM'
				),
				array(
						'col' => 'I',
						'value_field' => 'trans_total_qty_after',
						'footer' => 'SUM'
				),
	
				array(
						'col' => 'J',
						'value_field' => 'trans_total_price_after',
						'footer' => 'SUM'
				)
		);
	}
	
	function detailed_suspended_receivings($start_date, $end_date, $supplier_id,$sale_type, $export_excel=0, $offset=0)
	{
		$this->load->model('Receiving');
		$this->check_action_permission('view_receivings');
		$start_date=rawurldecode($start_date);
		$end_date=rawurldecode($end_date);
	
		$this->load->model('reports/Detailed_receivings');
		$model = $this->Detailed_receivings;
		$model->setParams(array('start_date'=>$start_date, 'end_date'=>$end_date, 'sale_type' => $sale_type, 'offset' => $offset, 'export_excel' => $export_excel, 'supplier_id' => $supplier_id, 'force_suspended' => true));
	
		$this->Receiving->create_receivings_items_temp_table(array('start_date'=>$start_date, 'end_date'=>$end_date, 'sale_type' => $sale_type, 'force_suspended' => true));
		$config = array();
		$config['base_url'] = site_url("reports/detailed_receivings/".rawurlencode($start_date).'/'.rawurlencode($end_date)."/$supplier_id/$sale_type/$export_excel");
		$config['total_rows'] = $model->getTotalRows();
		$config['per_page'] = $this->config->item('number_of_items_per_page') ? (int)$this->config->item('number_of_items_per_page') : 20;
		$config['uri_segment'] = 8;
		$this->load->library('pagination');$this->pagination->initialize($config);
	
	
		$headers = $model->getDataColumns();
		$report_data = $model->getData();
	
		$summary_data = array();
		$details_data = array();
	
		$location_count = count(Report::get_selected_location_ids());

		foreach($report_data['summary'] as $key=>$row)
		{
			$summary_data[$key] = array(
				array('data'=>anchor('receivings/receipt/'.$row['receiving_id'], '<i class="ion-printer"></i>', array('target' => '_blank')).' '.anchor('receivings/edit/'.$row['receiving_id'], '<i class="ion-document-text"></i>', array('target' => '_blank')).' '.anchor('receivings/edit/'.$row['receiving_id'], lang('common_edit').' '.$row['receiving_id'], array('target' => '_blank')).' ['.anchor('items/generate_barcodes_from_recv/'.$row['receiving_id'], lang('common_barcode_sheet'), array('target' => '_blank')).' / '.anchor('items/generate_barcodes_labels_from_recv/'.$row['receiving_id'], lang('common_barcode_labels'), array('target' => '_blank')).']', 'align'=> 'left'), 
				array('data'=>date(get_date_format(), strtotime($row['receiving_date'])), 'align'=> 'left'), 
				array('data'=>to_quantity($row['items_purchased']), 'align'=> 'left'),
				array('data'=>to_quantity($row['reports_measure_purchased']), 'align'=> 'left'), 
				array('data'=>$row['employee_name'], 'align'=> 'left'), 
				array('data'=>$row['supplier_name'], 'align'=> 'left'), 
				array('data'=>to_currency($row['subtotal']), 'align'=> 'right'), 
				array('data'=>to_currency($row['total']), 'align'=> 'right'),
				array('data'=>to_currency($row['tax']), 'align'=> 'right'), 
				array('data'=>$row['payment_type'], 'align'=> 'left'), 
				array('data'=>$row['comment'], 'align'=> 'left')
			);
				
			if ($location_count > 1)
			{
				array_unshift($summary_data[$key], array('data'=>$row['location_name'], 'align'=> 'left'));
			}
				
			foreach($report_data['details'][$key] as $drow)
			{
				if( $drow['measure_qty'] && $drow['measure_name'] ) {
					$details_data[$key][] = array(
						array('data'=>$drow['item_name'], 'align'=> 'left'),
						array('data'=>$drow['product_id'], 'align'=> 'left'),
						array('data'=>$drow['category'], 'align'=> 'left'),
						array('data'=>$drow['size'], 'align'=> 'left'),
						// array('data'=>to_quantity($drow['quantity_purchased']), 'align'=> 'left'),
						// array('data'=>to_quantity($drow['quantity_received']), 'align'=> 'left'),
						array('data'=>to_quantity($drow['measure_qty']), 'align'=>'left'),
						array('data'=>$drow['measure_name'], 'align'=>'left'),
							
						array('data'=>to_currency($drow['subtotal']), 'align'=> 'right'),
						array('data'=>to_currency($drow['total']), 'align'=> 'right'),
						array('data'=>to_currency($drow['tax']), 'align'=> 'right'),
						array('data'=>$drow['discount_percent'].'%', 'align'=> 'left')
					);
				} else {
					$this->load->model('Item');
					$details_data[$key][] = array(
						array('data'=>$drow['name'], 'align'=> 'left'),
						array('data'=>$drow['product_id'], 'align'=> 'left'),
						array('data'=>$drow['category'], 'align'=> 'left'),
						array('data'=>$drow['size'], 'align'=> 'left'),
						array('data'=>to_quantity($drow['quantity_purchased']), 'align'=> 'left'),
						// array('data'=>to_quantity($drow['quantity_received']), 'align'=> 'left'),
						array('data'=> $this->Item->getMeasureName($drow['item_id']), 'align'=>'left'),
							
						array('data'=>to_currency($drow['subtotal']), 'align'=> 'right'),
						array('data'=>to_currency($drow['total']), 'align'=> 'right'),
						array('data'=>to_currency($drow['tax']), 'align'=> 'right'),
						array('data'=>$drow['discount_percent'].'%', 'align'=> 'left')
					);
				}
			}
		}
	
		$data = array(
				"title" =>lang('reports_detailed_suspended_receivings_report'),
				"subtitle" => date(get_date_format(), strtotime($start_date)) .'-'.date(get_date_format(), strtotime($end_date)),
				"headers" => $model->getDataColumns(),
				"summary_data" => $summary_data,
				"details_data" => $details_data,
				"overall_summary_data" => $model->getSummaryData(),
				"export_excel" => $export_excel,
				"pagination" => $this->pagination->create_links(),
		);
	
		$this->load->view("reports/tabular_details",$data);
	}
	
	function summary_items($start_date, $end_date, $do_compare, $compare_start_date, $compare_end_date, $supplier_id = -1, $category_id = -1, $sale_type = 'all', $export_excel=0, $offset = 0)
	{
		$this->load->model('Category');
		$this->load->model('Sale');
		$this->check_action_permission('view_items');
		$start_date=rawurldecode($start_date);
		$end_date=rawurldecode($end_date);
		$compare_start_date=rawurldecode($compare_start_date);
		$compare_end_date=rawurldecode($compare_end_date);
	
	
		$this->load->model('reports/Summary_items');
		$model = $this->Summary_items;
		$model->setParams(array('start_date'=>$start_date, 'end_date'=>$end_date, 'sale_type' => $sale_type, 'category_id' => $category_id, 'supplier_id' => $supplier_id, 'offset' => $offset, 'export_excel' => $export_excel));
	
		$this->Sale->create_sales_items_temp_table(array('start_date'=>$start_date, 'end_date'=>$end_date, 'sale_type' => $sale_type));
	
		$config = array();
		$config['base_url'] = site_url("reports/summary_items/".rawurlencode($start_date).'/'.rawurlencode($end_date).'/'.$do_compare.'/'.rawurlencode($compare_start_date).'/'.rawurlencode($compare_end_date)."/$supplier_id/$category_id/$sale_type/$export_excel");
		$config['total_rows'] = $model->getTotalRows();
		$config['per_page'] = $this->config->item('number_of_items_per_page') ? (int)$this->config->item('number_of_items_per_page') : 20;
		$config['uri_segment'] = 12;
	
		$this->load->library('pagination');$this->pagination->initialize($config);
	
		$tabular_data = array();
		$report_data = $model->getData();
		$summary_data = $model->getSummaryData();
	
		if ($do_compare)
		{
			$compare_to_items = array();
				
			for($k=0;$k<count($report_data);$k++)
			{
				$compare_to_items[] = $report_data[$k]['item_id'];
			}
				
			$model_compare = $this->Summary_items;
			$model_compare->setParams(array('start_date'=>$compare_start_date, 'end_date'=>$compare_end_date, 'sale_type' => $sale_type, 'category_id' => $category_id, 'supplier_id' => $supplier_id, 'offset' => $offset, 'export_excel' => $export_excel, 'compare_to_items' => $compare_to_items));
				
			$this->Sale->drop_sales_items_temp_table();
			$this->Sale->create_sales_items_temp_table(array('start_date'=>$compare_start_date, 'end_date'=>$compare_end_date, 'sale_type' => $sale_type));
				
			$report_data_compare = $model_compare->getData();
			$report_data_summary_compare = $model_compare->getSummaryData();
		}
	
	
		foreach($report_data as $row)
		{
			if ($do_compare)
			{
				$index_compare = -1;
				$item_id_to_compare_to = $row['item_id'];
	
				for($k=0;$k<count($report_data_compare);$k++)
				{
					if ($report_data_compare[$k]['item_id'] == $item_id_to_compare_to)
					{
						$index_compare = $k;
						break;
					}
				}
	
				if (isset($report_data_compare[$index_compare]))
				{
					$row_compare = $report_data_compare[$index_compare];
				}
				else
				{
					$row_compare = FALSE;
				}
			}
				
			$data_row = array();
			$data_row[] = array('data'=>$row['name'], 'align' => 'left');
			$data_row[] = array('data'=>$row['item_number'], 'align' => 'left');
			$data_row[] = array('data'=>$row['product_id'], 'align' => 'left');
			$data_row[] = array('data'=>$row['category'], 'align' => 'left');
			$data_row[] = array('data'=>to_currency($row['current_selling_price']), 'align' => 'right');
			$data_row[] = array('data'=>qtyToString($row['item_id'], $row['quantity']), 'align' => 'left');
			$data_row[] = array('data'=>qtyToString($row['item_id'], $row['quantity_purchased']).($do_compare && $row_compare ? ' / <span class="compare '.($row_compare['quantity_purchased'] >= $row['quantity_purchased'] ? ($row['quantity_purchased'] == $row_compare['quantity_purchased'] ?  '' : 'compare_better') : 'compare_worse').'">'.to_quantity($row_compare['quantity_purchased']) .'</span>' :''), 'align' => 'left');
			$data_row[] = array('data'=>to_currency($row['subtotal']).($do_compare && $row_compare ? ' / <span class="compare '.($row_compare['subtotal'] >= $row['subtotal'] ? ($row['subtotal'] == $row_compare['subtotal'] ?  '' : 'compare_better') : 'compare_worse').'">'.to_currency($row_compare['subtotal']) .'</span>' :''), 'align' => 'right');
			$data_row[] = array('data'=>to_currency($row['total']).($do_compare && $row_compare ? ' / <span class="compare '.($row_compare['total'] >= $row['total'] ? ($row['total'] == $row_compare['total'] ?  '' : 'compare_better') : 'compare_worse').'">'.to_currency($row_compare['total']) .'</span>' :''), 'align' => 'right');
			$data_row[] = array('data'=>to_currency($row['tax']).($do_compare && $row_compare ? ' / <span class="compare '.($row_compare['tax'] >= $row['tax'] ? ($row['tax'] == $row_compare['tax'] ?  '' : 'compare_better') : 'compare_worse').'">'.to_currency($row_compare['tax']) .'</span>' :''), 'align' => 'right');
			if($this->has_profit_permission)
			{
				$data_row[] = array('data'=>to_currency($row['profit']).($do_compare && $row_compare ? ' / <span class="compare '.($row_compare['profit'] >= $row['profit'] ? ($row['profit'] == $row_compare['profit'] ?  '' : 'compare_better') : 'compare_worse').'">'.to_currency($row_compare['profit']) .'</span>' :''), 'align' => 'right');
			}
			$tabular_data[] = $data_row;
	
		}
	
		if ($do_compare)
		{
			foreach($summary_data as $key=>$value)
			{
				$summary_data[$key] = to_currency($value) . ' / <span class="compare '.($report_data_summary_compare[$key] >= $value ? ($value == $report_data_summary_compare[$key] ?  '' : 'compare_better') : 'compare_worse').'">'.to_currency($report_data_summary_compare[$key]).'</span>';
			}
				
		}
	
		$data = array(
				"title" => lang('reports_items_summary_report'),
				"subtitle" => date(get_date_format(), strtotime($start_date)) .'-'.date(get_date_format(), strtotime($end_date)).($do_compare  ? ' '. lang('reports_compare_to'). ' '. date(get_date_format(), strtotime($compare_start_date)) .'-'.date(get_date_format(), strtotime($compare_end_date)) : ''),
				"headers" => $model->getDataColumns(),
				"data" => $tabular_data,
				"summary_data" => $summary_data,
				"export_excel" => $export_excel,
				"pagination" => $this->pagination->create_links()
		);
	
		$this->load->view("reports/tabular",$data);
	}
	
	function inventory_low($supplier = -1, $category_id = -1, $inventory = 'all', $reorder_only = 0, $export_excel=0, $offset=0)
	{
		$category_id = rawurldecode($category_id);
	
		$this->check_action_permission('view_inventory_reports');
		$this->load->model('reports/Inventory_low');
		$model = $this->Inventory_low;
		$model->setParams(array('supplier'=>$supplier,'category_id' => $category_id, 'export_excel' => $export_excel, 'offset'=>$offset, 'inventory' => $inventory, 'reorder_only' => $reorder_only));
	
		$config = array();
		$config['base_url'] = site_url("reports/inventory_low/$supplier/$category_id/$inventory/$reorder_only/export_excel");
		$config['total_rows'] = $model->getTotalRows();
		$config['per_page'] = $this->config->item('number_of_items_per_page') ? (int)$this->config->item('number_of_items_per_page') : 20;
		$config['uri_segment'] = 8;
		$this->load->library('pagination');$this->pagination->initialize($config);
	
		$tabular_data = array();
		$report_data = $model->getData();
		$location_count = count(Report::get_selected_location_ids());
	
		foreach($report_data as $row)
		{
			$data_row = array();
				
	
			if ($location_count > 1)
			{
				$data_row[] = array('data'=>$row['location_name'], 'align' => 'left');
			}
			$data_row[] = array('data'=>$row['item_id'], 'align' => 'left');
			$data_row[] = array('data'=>$row['name'], 'align' => 'left');
			$data_row[] = array('data'=>$row['category'], 'align'=> 'left');
			$data_row[] = array('data'=>$row['company_name'], 'align'=> 'left');
			$data_row[] = array('data'=>$row['item_number'], 'align'=> 'left');
			$data_row[] = array('data'=>$row['product_id'], 'align'=> 'left');
			$data_row[] = array('data'=>$row['description'], 'align'=> 'left');
			$data_row[] = array('data'=>$row['size'], 'align'=> 'left');
			$data_row[] = array('data'=>$row['location'], 'align'=> 'left');
				
			if($this->has_cost_price_permission)
			{
				$data_row[] = array('data'=>to_currency($row['cost_price']), 'align'=> 'right');
			}
			$data_row[] = array('data'=>to_currency($row['unit_price']), 'align'=> 'right');
			$data_row[] = array('data'=>qtyToString($row['item_id'], $row['quantity']), 'align'=> 'left');
			$data_row[] = array('data'=>to_quantity($row['reorder_level']), 'align'=> 'left');
				
			$tabular_data[] = $data_row;
				
		}
	
		$data = array(
				"title" => lang('reports_low_inventory_report'),
				"subtitle" => '',
				"headers" => $model->getDataColumns(),
				"data" => $tabular_data,
				"summary_data" => $model->getSummaryData(),
				"export_excel" => $export_excel,
				"pagination" => $this->pagination->create_links(),
		);
	
		$this->load->view("reports/tabular",$data);
	}
	
	function detailed_sales($start_date, $end_date, $sale_type, $export_excel=0, $offset = 0)
	{
		$this->load->model('Sale');
		$this->check_action_permission('view_sales');
		$start_date=rawurldecode($start_date);
		$end_date=rawurldecode($end_date);
	
		$this->load->model('reports/Detailed_sales');
		$model = $this->Detailed_sales;
		$model->setParams(array('start_date'=>$start_date, 'end_date'=>$end_date, 'sale_type' => $sale_type, 'offset' => $offset, 'export_excel' => $export_excel));
	
		$this->Sale->create_sales_items_temp_table(array('start_date'=>$start_date, 'end_date'=>$end_date, 'sale_type' => $sale_type));
		$config = array();
		$config['base_url'] = site_url("reports/detailed_sales/".rawurlencode($start_date).'/'.rawurlencode($end_date)."/$sale_type/$export_excel");
		$config['total_rows'] = $model->getTotalRows();
		$config['per_page'] = $this->config->item('number_of_items_per_page') ? (int)$this->config->item('number_of_items_per_page') : 20;
		$config['uri_segment'] = 7;
		$this->load->library('pagination');$this->pagination->initialize($config);
	
		$headers = $model->getDataColumns();
		$report_data = $model->getData();
		$summary_data = array();
		$details_data = array();
	
		$location_count = count(Report::get_selected_location_ids());
	
		foreach($report_data['summary'] as $key=>$row)
		{
			$summary_data_row = array();
	
			$link = site_url('reports/specific_customer/'.$start_date.'/'.$end_date.'/'.$row['customer_id'].'/all/0');
				
			$summary_data_row[] = array('data'=>anchor('sales/receipt/'.$row['sale_id'], '<i class="ion-printer"></i>', array('target' => '_blank', 'class'=>'hidden-print')).'<span class="visible-print">'.$row['sale_id'].'</span>'.anchor('sales/edit/'.$row['sale_id'], '<i class="ion-document-text"></i>', array('target' => '_blank')).' '.anchor('sales/edit/'.$row['sale_id'], lang('common_edit').' '.$row['sale_id'], array('target' => '_blank','class'=>'hidden-print')), 'align'=>'left');
				
			if ($location_count > 1)
			{
				$summary_data_row[] = array('data'=>$row['location_name'], 'align' => 'left');
			}
				
			$summary_data_row[] = array('data'=>date(get_date_format().'-'.get_time_format(), strtotime($row['sale_time'])), 'align'=>'left');
			$summary_data_row[] = array('data'=>$row['register_name'], 'align'=>'left');
			// $summary_data_row[] = array('data'=>to_quantity($row['items_purchased']), 'align'=>'left');
			$summary_data_row[] = array('data'=>$row['employee_name'].($row['sold_by_employee'] && $row['sold_by_employee'] != $row['employee_name'] ? '/'. $row['sold_by_employee']: ''), 'align'=>'left');
			$summary_data_row[] = array('data'=>'<a href="'.$link.'" target="_blank">'.$row['customer_name'].(isset($row['account_number']) && $row['account_number'] ? ' ('.$row['account_number'].')' : '').'</a>', 'align'=>'left');
			$summary_data_row[] = array('data'=>to_currency($row['subtotal']), 'align'=>'right');
			$summary_data_row[] = array('data'=>to_currency($row['total']), 'align'=>'right');
			$summary_data_row[] = array('data'=>to_currency($row['tax']), 'align'=>'right');
				
			if($this->has_profit_permission)
			{
				$summary_data_row[] = array('data'=>to_currency($row['profit']), 'align'=>'right');
			}
				
			$summary_data_row[] = array('data'=>$row['payment_type'], 'align'=>'right');
			$summary_data_row[] = array('data'=>$row['comment'], 'align'=>'right');
			$summary_data[$key] = $summary_data_row;
				
			foreach($report_data['details'][$key] as $drow)
			{
				$details_data_row = array();
	
				$details_data_row[] = array('data'=>isset($drow['item_number']) ? $drow['item_number'] : $drow['item_kit_number'], 'align'=>'left');
				$details_data_row[] = array('data'=>isset($drow['item_product_id']) ? $drow['item_product_id'] : $drow['item_kit_product_id'], 'align'=>'left');
				$details_data_row[] = array('data'=>isset($drow['item_name']) ? $drow['item_name'] : $drow['item_kit_name'], 'align'=>'left');
				$details_data_row[] = array('data'=>$drow['category'], 'align'=>'left');
				$details_data_row[] = array('data'=>$drow['size'], 'align'=>'left');
				$details_data_row[] = array('data'=>$drow['supplier_name']. ' ('.$drow['supplier_id'].')', 'align'=>'left');
				$details_data_row[] = array('data'=>$drow['serialnumber'], 'align'=>'left');
				$details_data_row[] = array('data'=>$drow['description'], 'align'=>'left');
				$details_data_row[] = array('data'=>to_currency($drow['current_selling_price']), 'align'=>'left');
				
				if( $drow['measure_qty'] && $drow['measure_name'] ) {
					$details_data_row[] = array('data'=>to_quantity($drow['measure_qty']), 'align'=>'left');
					$details_data_row[] = array('data'=>$drow['measure_name'], 'align'=>'left');
				} else {
					$details_data_row[] = array('data'=>to_quantity($drow['quantity_purchased']), 'align'=>'left');
					$this->load->model('Item');
					$details_data_row[] = array('data'=> $this->Item->getMeasureName($drow['item_id']), 'align'=>'left');
				}
				
				$details_data_row[] = array('data'=>to_currency($drow['subtotal']), 'align'=>'right');
				$details_data_row[] = array('data'=>to_currency($drow['total']), 'align'=>'right');
				$details_data_row[] = array('data'=>to_currency($drow['tax']), 'align'=>'right');
	
				if($this->has_profit_permission)
				{
					$details_data_row[] = array('data'=>to_currency($drow['profit']), 'align'=>'right');
				}
	
				$details_data_row[] = array('data'=>$drow['discount_percent'].'%', 'align'=>'left');
				$details_data[$key][] = $details_data_row;
			}
		}
	
		$data = array(
				"title" =>lang('reports_detailed_sales_report'),
				"subtitle" => date(get_date_format(), strtotime($start_date)) .'-'.date(get_date_format(), strtotime($end_date)),
				"headers" => $model->getDataColumns(),
				"summary_data" => $summary_data,
				"details_data" => $details_data,
				"overall_summary_data" => $model->getSummaryData(),
				"export_excel" => $export_excel,
				"pagination" => $this->pagination->create_links(),
		);
	
		$this->load->view("reports/tabular_details",$data);
	}
	
	function detailed_receivings($start_date, $end_date, $supplier_id,$sale_type, $export_excel=0, $offset=0)
	{
		$this->load->model('Receiving');
		$this->check_action_permission('view_receivings');
		$start_date=rawurldecode($start_date);
		$end_date=rawurldecode($end_date);
		$this->load->model('reports/Detailed_receivings');
		$model = $this->Detailed_receivings;
		$model->setParams(array('start_date'=>$start_date, 'end_date'=>$end_date, 'sale_type' => $sale_type, 'offset' => $offset, 'export_excel' => $export_excel, 'supplier_id' => $supplier_id));
	
		$this->Receiving->create_receivings_items_temp_table(array('start_date'=>$start_date, 'end_date'=>$end_date, 'sale_type' => $sale_type));
		$config = array();
		$config['base_url'] = site_url("reports/detailed_receivings/".rawurlencode($start_date).'/'.rawurlencode($end_date)."/$supplier_id/$sale_type/$export_excel");
		$config['total_rows'] = $model->getTotalRows();
		
		$config['per_page'] = $this->config->item('number_of_items_per_page') ? (int)$this->config->item('number_of_items_per_page') : 20;
		$config['uri_segment'] = 8;
		$this->load->library('pagination');$this->pagination->initialize($config);
	
	
		$headers = $model->getDataColumns();
		$report_data = $model->getData();

		$summary_data = array();
		$details_data = array();
		$location_count = count(Report::get_selected_location_ids());
	
		foreach($report_data['summary'] as $key=>$row)
		{
			$summary_data[$key] = array(
					array('data'=>anchor('receivings/receipt/'.$row['receiving_id'], '<i class="ion-printer"></i>', array('target' => '_blank')).' '.anchor('receivings/edit/'.$row['receiving_id'], '<i class="ion-document-text"></i>', array('target' => '_blank')).' '.anchor('receivings/edit/'.$row['receiving_id'], lang('common_edit').' '.$row['receiving_id'], array('target' => '_blank')).' ['.anchor('items/generate_barcodes_from_recv/'.$row['receiving_id'], lang('common_barcode_sheet'), array('target' => '_blank')).' / '.anchor('items/generate_barcodes_labels_from_recv/'.$row['receiving_id'], lang('common_barcode_labels'), array('target' => '_blank')).']', 'align'=> 'left'), 
					array('data'=>date(get_date_format(), strtotime($row['receiving_date'])), 'align'=> 'left'), 
					// array('data'=>to_quantity($row['items_purchased']), 'align'=> 'left'),
					// array('data'=>to_quantity($row['items_received']), 'align'=> 'left'), 
					array('data'=>$row['employee_name'], 'align'=> 'left'), 
					array('data'=>$row['supplier_name'], 'align'=> 'left'), 
					array('data'=>to_currency($row['subtotal']), 'align'=> 'right'), 
					array('data'=>to_currency($row['total']), 'align'=> 'right'),
					array('data'=>to_currency($row['tax']), 'align'=> 'right'), 
					array('data'=>$row['payment_type'], 'align'=> 'left'), 
					array('data'=>$row['comment'], 'align'=> 'left'));
				
			if ($location_count > 1)
			{
				array_unshift($summary_data[$key], array('data'=>$row['location_name'], 'align'=> 'left'));
			}
				
			foreach($report_data['details'][$key] as $drow)
			{
				if($drow['measure_id']) {
					$this->load->model('Measure');
					$measure = $this->Measure->getInfo($drow['measure_id']);
					$details_data[$key][] = array(
						array('data'=>$drow['name'], 'align'=> 'left'),
						array('data'=>$drow['product_id'], 'align'=> 'left'),
						array('data'=>$drow['category'], 'align'=> 'left'),
						array('data'=>$drow['size'], 'align'=> 'left'),
						array('data'=>to_quantity($drow['measure_qty']), 'align'=> 'left'),
						// array('data'=>to_quantity($drow['measure_qty_received']), 'align'=> 'left'),
						array('data'=> $measure->name, 'align'=> 'left'),
						array('data'=>to_currency($drow['subtotal']), 'align'=> 'right'),
						array('data'=>to_currency($drow['total']), 'align'=> 'right'),
						array('data'=>to_currency($drow['tax']), 'align'=> 'right'),
						array('data'=>$drow['discount_percent'].'%', 'align'=> 'left')
					);
					
				} else {
					$this->load->model('Item');
					$details_data[$key][] = array(
						array('data'=>$drow['name'], 'align'=> 'left'),
						array('data'=>$drow['product_id'], 'align'=> 'left'),
						array('data'=>$drow['category'], 'align'=> 'left'),
						array('data'=>$drow['size'], 'align'=> 'left'),
						array('data'=>to_quantity($drow['quantity_purchased']), 'align'=> 'left'),
// 						array('data'=>to_quantity($drow['quantity_received']), 'align'=> 'left'),
						array('data'=>$this->Item->getMeasureName($drow['item_id']), 'align'=> 'left'),
						array('data'=>to_currency($drow['subtotal']), 'align'=> 'right'),
						array('data'=>to_currency($drow['total']), 'align'=> 'right'),
						array('data'=>to_currency($drow['tax']), 'align'=> 'right'),
						array('data'=>$drow['discount_percent'].'%', 'align'=> 'left')
					);
				}
			}
		}
	
		$data = array(
				"title" =>lang('reports_detailed_receivings_report'),
				"subtitle" => date(get_date_format(), strtotime($start_date)) .'-'.date(get_date_format(), strtotime($end_date)),
				"headers" => $model->getDataColumns(),
				"summary_data" => $summary_data,
				"details_data" => $details_data,
				"overall_summary_data" => $model->getSummaryData(),
				"export_excel" => $export_excel,
				"pagination" => $this->pagination->create_links(),
		);
	
		$this->load->view("reports/tabular_details",$data);
	}
	
	function detailed_count_report($start_date, $end_date, $export_excel=0, $offset = 0)
	{
		$this->check_action_permission('view_inventory_reports');
	
		$start_date=rawurldecode($start_date);
		$end_date=rawurldecode($end_date);
	
		$this->load->model('reports/Detailed_inventory_count_report');
		$model = $this->Detailed_inventory_count_report;
		$model->setParams(array('start_date'=>$start_date, 'end_date'=>$end_date, 'offset' => $offset, 'export_excel' => $export_excel));
	
		$config = array();
		$config['base_url'] = site_url("reports/detailed_count_report/".rawurlencode($start_date).'/'.rawurlencode($end_date)."/$export_excel");
		$config['total_rows'] = $model->getTotalRows();
		$config['per_page'] = $this->config->item('number_of_items_per_page') ? (int)$this->config->item('number_of_items_per_page') : 20;
		$config['uri_segment'] = 6;
		$this->load->library('pagination');$this->pagination->initialize($config);
	
		$headers = $model->getDataColumns();
		$report_data = $model->getData();
	
		$summary_data = array();
		$details_data = array();
		$location_count = count(Report::get_selected_location_ids());
	
		foreach($report_data['summary'] as $key=>$row)
		{
			$status = '';
			switch($row['status'])
			{
				case 'open':
					$status = lang('common_open');
					break;
	
				case 'closed':
					$status = lang('common_closed');
					break;
			}
			
			$totalQtyCount = 0;
			foreach($report_data['details'][$key] as $drow)
			{
				$details_data_row = array();
				$details_data_row[] = array('data'=>$drow['item_number'], 'align'=>'left');
				$details_data_row[] = array('data'=>$drow['product_id'], 'align'=>'left');
				$details_data_row[] = array('data'=>$drow['name'], 'align'=>'left');
				$details_data_row[] = array('data'=>$drow['category'], 'align'=>'left');
				$details_data_row[] = array('data'=>$drow['size'], 'align'=>'left');
				$details_data_row[] = array('data'=>to_quantity($drow['count']), 'align'=>'left');
				$details_data_row[] = array('data'=>to_quantity($drow['actual_quantity']), 'align'=>'left');
				$details_data_row[] = array('data'=>$drow['comment'], 'align'=>'left');
				$details_data[$key][] = $details_data_row;
				$totalQtyCount += $drow['count'];
			}
			
			$summary_data_row = array(
					array('data'=>date(get_date_format().' '.get_time_format(), strtotime($row['count_date'])), 'align'=>'left'),
					array('data'=>$status, 'align'=>'left'),
					array('data'=>$row['employee_name'], 'align'=>'left'),
					array('data'=>to_quantity($row['items_counted']), 'align'=>'left'),
					array('data'=>to_quantity($totalQtyCount), 'align'=>'left'),
					array('data'=>to_quantity($row['difference']), 'align'=>'left'),
					array('data'=>$row['comment'], 'align'=>'left'),
			);
	
			if ($location_count > 1)
			{
				array_unshift($summary_data_row, array('data'=>$row['location_name'], 'align'=>'left'));
			}
			$summary_data[$key] = $summary_data_row;
		}
		$data = array(
				"title" =>lang('reports_detailed_count_report'),
				"subtitle" => date(get_date_format(), strtotime($start_date)) .'-'.date(get_date_format(), strtotime($end_date)),
				"headers" => $model->getDataColumns(),
				"summary_data" => $summary_data,
				"details_data" => $details_data,
				"overall_summary_data" => $model->getSummaryData(),
				"export_excel" => $export_excel,
				"pagination" => $this->pagination->create_links(),
		);
		$this->load->view("reports/tabular_details", $data);
	}
}
?>
