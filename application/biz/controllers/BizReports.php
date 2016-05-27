<?php
require_once (APPPATH . "controllers/Reports.php");
class BizReports extends Reports 
{	
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
}
?>
