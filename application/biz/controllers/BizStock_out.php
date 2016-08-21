<?php
require_once (APPPATH . "controllers/Secure_area.php");

class BizStock_out extends Secure_area
{
	const STOCK_OUT_SESSION_KEY = 'STOCK_OUT_DETAIL';
	
	function __construct()
	{
		parent::__construct('stock_out');
		$this->load->library('MySession');
		
		$this->load->model('Item');
		$this->load->model('Sale');
		$this->load->model('Measure');
		$this->load->model('StockOut');
		$this->load->model('Customer');
		$this->load->library('sale_lib');
		$this->load->model('Item_kit');
		
	}
	
	public function index() {
		$saleId = $this->input->get('sId');
		if (!empty($saleId)) {
			$stockOutData = [];
			$saleInfo = $this->Sale->get_info($saleId)->row();
			if (!empty($saleInfo)) {
				$stockOutData['mode'] = 'by_sale';
				$stockOutData['customer'] = $saleInfo->customer_id;
				$items = $this->Sale->get_sale_items($saleId)->result();
				foreach ($items as $item) {
					$stockOutData['items'][$item->item_id] = $this->Item->get_info($item->item_id);
					$stockOutData['items'][$item->item_id]->totalQty = $item->measure_qty;
					if (!empty($item->measure_id)) {
						$stockOutData['items'][$item->item_id]->measure_id = $item->measure_id;
					}
				}
				$this->mysession->setValue(self::STOCK_OUT_SESSION_KEY, $stockOutData);
			}
		}
		
		
		$data['stock_out_data'] = $this->mysession->getValue(self::STOCK_OUT_SESSION_KEY);
		
		$deliverer = null;
		if (!empty($data['stock_out_data']['deliverer'])) {
			$deliverer = $this->Employee->get_info($data['stock_out_data']['deliverer']);
		}
		
		$customer = null;
		if (!empty($data['stock_out_data']['customer'])) {
			$customer = $this->Customer->get_info($data['stock_out_data']['customer']);
		}
		
		$data['deliverer'] = $deliverer;
		$data['customer'] = $customer;
		
		$data['selected_tml'] = $this->load->view('stock_out/partials/selected_items', $data['stock_out_data'], TRUE);
		$this->load->view('stock_out/index', $data);
	}
	
	public function change_mode() {
		$this->mysession->setValue(self::STOCK_OUT_SESSION_KEY, null);
		
		$stockOutMode = $this->input->post('mode');
		$stockOutData = $this->mysession->getValue(self::STOCK_OUT_SESSION_KEY);
		$stockOutData['mode'] = empty($stockOutMode) ? '' : $stockOutMode;
		$this->mysession->setValue(self::STOCK_OUT_SESSION_KEY, $stockOutData);
		echo json_encode(['success' => 1]);
	}
	
	function search()
	{
		$stockOutData = $this->mysession->getValue(self::STOCK_OUT_SESSION_KEY);
		if ($stockOutData['mode'] == 'by_sale') {
			session_write_close();
			$suggestions = $this->Sale->getSaleForStockOut($this->input->get('term'));
			echo json_encode($suggestions);
		} else {
			//allow parallel searchs to improve performance.
			session_write_close();
			$suggestions = $this->Item->get_item_search_suggestions($this->input->get('term'),100);
			$suggestions = array_merge($suggestions, $this->Item_kit->get_item_kit_search_suggestions_sales_recv($this->input->get('term'),100));
			echo json_encode($suggestions);
		}
	}
	
	public function edit_item($itemId = 0)
	{
		$stockOutData = $this->mysession->getValue(self::STOCK_OUT_SESSION_KEY);
		$qty = 0;
		$measure = 0;
		if ($this->input->post('name') == 'quantity')
		{
			$qty = $this->input->post('value');
		} elseif ($this->input->post('name') == 'measure') {
			$measure = $this->input->post('value');
		}
		
		if (!isset($stockOutData['items'][$itemId]))
		{
			$stockOutData['items'][$itemId] = $this->Item->get_info($itemId);
		}
		
		if (!empty($qty)) {
			$stockOutData['items'][$itemId]->totalQty = $qty;
		}
		
		if (!empty($measure)) {
			$stockOutData['items'][$itemId]->measure_id = $measure;
		}
		
		$this->mysession->setValue(self::STOCK_OUT_SESSION_KEY, $stockOutData);
	}
	
	public function cancel() {
		$this->mysession->setValue(self::STOCK_OUT_SESSION_KEY, null);
		redirect('stock_out');
	}
	
	public function history()
	{
		$data = array();
		$start_date = $this->input->get('start_date');
		if (empty($start_date)) {
			$data['start_date'] = date('d-m-Y', strtotime("-30 days"));
			$search['start_date'] = date('Y-m-d', strtotime("-30 days"));
		} else {
			$data['start_date'] = $this->input->get('start_date_formatted');
			$search['start_date'] = $this->input->get('start_date');
		}
	
		$end_date = $this->input->get('end_date');
	
		if (empty($end_date)) {
			$data['end_date'] = date('d-m-Y');
			$search['end_date'] = date('Y-m-d');
		} else {
			$data['end_date'] = $this->input->get('end_date_formatted');
			$search['end_date'] = $this->input->get('end_date');
		}
		$data['history'] = $this->StockOut->getHistory($search);
		
		$this->load->view('stock_out/history', $data);
	}
	
	public function store_item()
	{
		$stockOutData = $this->mysession->getValue(self::STOCK_OUT_SESSION_KEY);
		$itemId = $this->input->post('item_id');
		
		if ($stockOutData['mode'] == 'by_sale') {
			// Get sale_items_kit
			$saleInfo = $this->Sale->get_info($itemId)->row();
			
			if (!empty($saleInfo)) {
				$stockOutData['customer'] = $saleInfo->customer_id;
					
				$items = $this->Sale->get_sale_items($itemId)->result();
				foreach ($items as $item) {
					$stockOutData['items'][$item->item_id] = $this->Item->get_info($item->item_id);
					$stockOutData['items'][$item->item_id]->totalQty = $item->measure_qty;
					if (!empty($item->measure_id)) {
						$stockOutData['items'][$item->item_id]->measure_id = $item->measure_id;
					}
				}
			}
		} else {
			if (isset($stockOutData['items'][$itemId]))
			{
				$stockOutData['items'][$itemId]->totalQty ++;
			} else {
					
				if($this->sale_lib->is_valid_item_kit($itemId))
				{
					$itemKit = $this->Item_kit->get_info($itemId);
					$stockOutData['items'][$itemId] = $itemKit;
			
					$stockOutData['items'][$itemId]->itemType = 'kit';
			
				} else {
					$stockOutData['items'][$itemId] = $this->Item->get_info($itemId);
				}
				$stockOutData['items'][$itemId]->totalQty = 1;
			}
		}
		
		$this->mysession->setValue(self::STOCK_OUT_SESSION_KEY, $stockOutData);
		echo json_encode(array(
				'success' => true, 
				'html' => $this->load->view('stock_out/partials/selected_items', $stockOutData, TRUE)
		));
	}
	
	public function remove_item() {
		$itemId = $this->input->post('item_id');
		
		$stockOutData = $this->mysession->getValue(self::STOCK_OUT_SESSION_KEY);
		if (isset($stockOutData['items'][$itemId])) {
			unset($stockOutData['items'][$itemId]);
		}
		$this->mysession->setValue(self::STOCK_OUT_SESSION_KEY, $stockOutData);
		
		echo json_encode(['success' => 1]);
	}
	
	public function select_delivery() {
		$delivererId = $this->input->post('deliverer_id');
		
		$stockOutData = $this->mysession->getValue(self::STOCK_OUT_SESSION_KEY);
		$stockOutData['deliverer'] = $delivererId;
		$this->mysession->setValue(self::STOCK_OUT_SESSION_KEY, $stockOutData);
		$deliverer = $this->Employee->get_info($delivererId);
		echo json_encode(['success' => 1, 'deliverer' => $deliverer->first_name . ' ' . $deliverer->last_name]);
	}
	
	public function select_customer() {
		$customerId = $this->input->post('customer_id');
		
		$stockOutData = $this->mysession->getValue(self::STOCK_OUT_SESSION_KEY);
		$stockOutData['customer'] = $customerId;
		$this->mysession->setValue(self::STOCK_OUT_SESSION_KEY, $stockOutData);
		
		$customer = $this->Customer->get_info($customerId);
		
		echo json_encode(['success' => 1, 'customer' => $customer->first_name . ' ' . $customer->last_name]);
	}
	
	public function finish() {
		$stockOutData = $this->mysession->getValue(self::STOCK_OUT_SESSION_KEY);
		$stockOutData['comment'] = $this->input->post('comment');
		
		$stockOutId = $this->StockOut->save($stockOutData);
		$stockOutData['stock_out_id'] = $stockOutId;
		$this->mysession->setValue(self::STOCK_OUT_SESSION_KEY, $stockOutData);
		redirect('stock_out/pre_print');
	}
	
	public function pre_print($id = 0) {
		if (!empty($id)) {
			$stockOutData = $this->mysession->getValue(self::STOCK_OUT_SESSION_KEY);
			$stockOut = $this->StockOut->getInfo($id);
			
			$stockOutData['customer'] = $stockOut->customer_id;
			$stockOutData['deliverer'] = $stockOut->deliverer_id;
			$stockOutData['comment'] = $stockOut->comment;
			
			foreach ($stockOut->items as $item) {
				$stockOutData['items'][$item->item_id] = $item;
				$stockOutData['items'][$item->item_id]->totalQty = $item->stockOut_totalQty;
				if (!empty($item->stockOut_measureId)) {
					$stockOutData['items'][$item->item_id]->measure_id = $item->stockOut_measureId;
				}
			}
			
			$this->mysession->setValue(self::STOCK_OUT_SESSION_KEY, $stockOutData);
		}
		
		$data['stock_out_data'] = $stockOutData = $this->mysession->getValue(self::STOCK_OUT_SESSION_KEY);
		
		$data['pdf_block_html'] = $this->load->view('stock_out/partials/pdf', $stockOutData, TRUE);
		$this->mysession->setValue(self::STOCK_OUT_SESSION_KEY, null);
		$this->load->view('stock_out/pre_print', $data);
	}
}
