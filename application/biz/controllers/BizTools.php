<?php

require_once (APPPATH . "controllers/Secure_area.php");

class BizTools extends Secure_area {
	
	function __construct()
	{
		parent::__construct('tools');
		$this->load->helper('bizexcel');
		$this->load->model('Item');
		$this->load->model('Location');
		$this->load->model('Item_location');
		$this->load->model('Category');
		
		$this->load->model('Receiving');
		$this->load->model('Inventory');
		$this->load->model('Customer');
	}
	
	public function export($type = '') {
		switch ($type) {
			case 'items':
				$this->exportItems();
				break;
			case 'history_transfers':
				$this->exportHistoryTransfers();
				break;
			case 'history_audits':
				$this->exportHistoryAudits();
				break;
			case 'detail_inventory':
				ini_set('max_execution_time', 300);
				$this->exportDetailInventory();
				break;
			case 'account_payment':
				$this->exportAccountPayment();
				break;
		}
	}
	
	protected function exportAccountPayment() {
		$bizExcel = new BizExcel('AAccountPayment.xlsx');
		$excelContent = $bizExcel->setNumberRowStartBody(4)->setHeaderOfBody($this->getHeaderOfAccountPayment());
		$bizExcel->setDataExcel($this->getDetailAccountPayment());
		$excelContent = $bizExcel->generateFile(false);
		$this->load->helper('download');
		force_download('AccountPayment.xlsx', $excelContent);
	}
	
	protected function getDetailAccountPayment() {
		$search['start_date'] = '2016-06-01';
		$search['end_date'] = date('Y-m-d');
		$results = $this->Customer->getStoreAccountDetail($search);
		
		$allItems = [];
		foreach ($results as $record) {
			foreach ($record['store_account_transactions'] as $transItem) {
				$item = [];
				$item['customer'] = $record['customer_info']->first_name . ' ' . $record['customer_info']->last_name;
				$item['sale_id'] = $transItem['sale_id'];
				$item['date'] = $transItem['date'];
				$item['amount'] = $transItem['transaction_amount'] > 0 ? to_currency($transItem['transaction_amount']) : to_currency(0);;
				$item['balance'] = to_currency($transItem['balance']);
				$item['comment'] = $transItem['comment'];
				$allItems[] = $item;
			}
		}
		return $allItems;
	}
	
	protected function getHeaderOfAccountPayment() {
		return array(
				array(
						'col' => 'A',
						'text' => 'STT',
						'styles' => array(
								'color' => '75b6ed',
								'bold' => true,
								'is_fill' => true
						),
						'value_field' => '__AUTO__',
				),
				array(
						'col' => 'B',
						'text' => 'Khách hàng',
						'styles' => array(
								'color' => '75b6ed',
								'bold' => true,
								'is_fill' => true
						),
						'value_field' => 'customer',
				),
				array(
						'col' => 'C',
						'text' => 'ID Đơn Hàng',
						'styles' => array(
								'color' => '75b6ed',
								'bold' => true,
								'is_fill' => true
						),
						'value_field' => 'sale_id',
				),
				array(
						'col' => 'D',
						'text' => 'Ngày',
						'styles' => array(
								'color' => '75b6ed',
								'bold' => true,
								'is_fill' => true
						),
						'value_field' => 'date',
				),
		
				array(
						'col' => 'E',
						'text' => 'Sổ Ghi Nợ',
						'styles' => array(
								'color' => '75b6ed',
								'bold' => true,
								'is_fill' => true
						),
						'value_field' => 'amount',
				),
				array(
						'col' => 'F',
						'text' => 'Bảng Cân Đối',
						'styles' => array(
								'color' => '75b6ed',
								'bold' => true,
								'is_fill' => true
						),
						'value_field' => 'balance',
				),
				array(
						'col' => 'G',
						'text' => 'Ghi Chú',
						'styles' => array(
								'color' => '75b6ed',
								'bold' => true,
								'is_fill' => true
						),
						'value_field' => 'comment',
				)
		);
	}
	
	
	protected function exportDetailInventory() {
		$bizExcel = new BizExcel('ADetailInventory.xlsx');
		$excelContent = $bizExcel->setNumberRowStartBody(4)->setHeaderOfBody($this->getHeaderOfDetailInventory());
		$bizExcel->setDataExcel($this->getDetailInventory());
		$excelContent = $bizExcel->generateFile(false);
		$this->load->helper('download');
		force_download('DetailInventory.xlsx', $excelContent);
	}
	
	protected function getHeaderOfDetailInventory() {
		return array(
				array(
						'col' => 'A',
						'text' => 'STT',
						'styles' => array(
								'color' => '75b6ed',
								'bold' => true,
								'is_fill' => true
						),
						'value_field' => '__AUTO__',
				),
				array(
						'col' => 'B',
						'text' => 'Sản Phẩm',
						'styles' => array(
								'color' => '75b6ed',
								'bold' => true,
								'is_fill' => true
						),
						'value_field' => 'item',
				),
				array(
						'col' => 'C',
						'text' => 'Danh Mục',
						'styles' => array(
								'color' => '75b6ed',
								'bold' => true,
								'is_fill' => true
						),
						'value_field' => 'category',
				),
				array(
						'col' => 'D',
						'text' => 'Kho',
						'styles' => array(
								'color' => '75b6ed',
								'bold' => true,
								'is_fill' => true
						),
						'value_field' => 'location_name',
				),
		
				array(
						'col' => 'E',
						'text' => 'Ngày',
						'styles' => array(
								'color' => '75b6ed',
								'bold' => true,
								'is_fill' => true
						),
						'value_field' => 'trans_date',
				),
				array(
						'col' => 'F',
						'text' => 'Thêm/Bớt Số lượng',
						'styles' => array(
								'color' => '75b6ed',
								'bold' => true,
								'is_fill' => true
						),
						'value_field' => 'trans_inventory',
				),
				array(
						'col' => 'G',
						'text' => 'Ghi Chú',
						'styles' => array(
								'color' => '75b6ed',
								'bold' => true,
								'is_fill' => true
						),
						'value_field' => 'trans_comment',
				)
		);
	}
	
	protected function getDetailInventory() {
		$search['start_date'] = '2016-06-01';
		$search['end_date'] = date('Y-m-d');
		$allDetails = $this->Inventory->getAllDetail($search);
		$items = [];
		
		foreach ($allDetails as $row) {
			$item = [];
			$item['location_name'] = $row['location_name'];
			$item['category'] = $row['category'];
			$item['item'] = $row['name'];
			$item['trans_date'] = $row['trans_date'];
			$item['trans_comment'] = $row['trans_comment'];
			$item['trans_inventory'] = $row['trans_inventory'];
			$items[] = $item;
		}
		
		return $items;
	}
	
	protected function exportHistoryAudits() {
		$bizExcel = new BizExcel('AHistoryAudits.xlsx');
		$excelContent = $bizExcel->setNumberRowStartBody(4)->setHeaderOfBody($this->getHeaderOfHistoryAudits());
		$bizExcel->setDataExcel($this->getHistoryAudits());
		$excelContent = $bizExcel->generateFile(false);
		$this->load->helper('download');
		force_download('HistoryAudits.xlsx', $excelContent);
	}
	
	protected function getHistoryAudits() {
		$search['start_date'] = '2016-06-01';
		$search['end_date'] = date('Y-m-d');
		$historyAudits = $this->Inventory->getHistoryAuditsByAllItems($search);
		$auditItems = [];
		foreach ($historyAudits as $audit) {
			$auditRow = [];
			$auditRow['audit_location'] = $audit['location_name'];
			$auditRow['audit_date'] = $audit['count_date'];
			$auditRow['audit_item'] = $audit['name'];
			$auditRow['audit_item_category'] = $audit['category_name'];
			$auditRow['audit_item_count'] = to_quantity($audit['count']);
			$auditRow['audit_item_count_actual'] = to_quantity((int) $audit['actual_quantity']);
			$auditItems[] = $auditRow;
		}
		
		return $auditItems;
	}
	
	protected function getHeaderOfHistoryAudits() {
		return array(
				array(
						'col' => 'A',
						'text' => 'STT',
						'styles' => array(
								'color' => '75b6ed',
								'bold' => true,
								'is_fill' => true
						),
						'value_field' => '__AUTO__',
				),
				array(
						'col' => 'B',
						'text' => 'Kho',
						'styles' => array(
								'color' => '75b6ed',
								'bold' => true,
								'is_fill' => true
						),
						'value_field' => 'audit_location',
				),
				array(
						'col' => 'C',
						'text' => 'Ngày Kiểm',
						'styles' => array(
								'color' => '75b6ed',
								'bold' => true,
								'is_fill' => true
						),
						'value_field' => 'audit_date',
				),
				array(
						'col' => 'D',
						'text' => 'Sản Phẩm',
						'styles' => array(
								'color' => '75b6ed',
								'bold' => true,
								'is_fill' => true
						),
						'value_field' => 'audit_item',
				),
		
				array(
						'col' => 'E',
						'text' => 'Loại Sản Phẩm',
						'styles' => array(
								'color' => '75b6ed',
								'bold' => true,
								'is_fill' => true
						),
						'value_field' => 'audit_item_category',
				),
				array(
						'col' => 'F',
						'text' => 'Số Lượng',
						'styles' => array(
								'color' => '75b6ed',
								'bold' => true,
								'is_fill' => true
						),
						'value_field' => 'audit_item_count',
				),
				array(
						'col' => 'G',
						'text' => 'Số Lượng Thực Tế',
						'styles' => array(
								'color' => '75b6ed',
								'bold' => true,
								'is_fill' => true
						),
						'value_field' => 'audit_item_count_actual',
				)
		);
	}
	
	
	protected function exportHistoryTransfers() {
		$bizExcel = new BizExcel('AHistoryTransfers.xlsx');
		$excelContent = $bizExcel->setNumberRowStartBody(4)->setHeaderOfBody($this->getHeaderOfHistoryTransfers());
		$bizExcel->setDataExcel($this->getHistoryTransfers());
		$excelContent = $bizExcel->generateFile(false);
		$this->load->helper('download');
		force_download('HistoryTransfers.xlsx', $excelContent);
	}
	
	protected function getHistoryTransfers() {
		$search['start_date'] = '2016-06-01';
		$search['end_date'] = date('Y-m-d');
		$historyTransfers = $this->Receiving->getHistoryTransfersByAllItems($search);
		
		$transfers = [];
		foreach ($historyTransfers as $transfer) {
			$tranRow = [];
			$tranRow['recv_id'] = $transfer['receiving_id'];
			$tranRow['recv_date'] = $transfer['receiving_time'];
			$tranRow['recv_item'] = $transfer['name'];
			$tranRow['recv_qty'] = (int) to_quantity_abs($transfer['quantity_received']);
			$locationFrom = $this->Location->get_info($transfer['location_id']);
			$tranRow['recv_location_from'] = $locationFrom->name;
			$locationTo = $this->Location->get_info($transfer['transfer_to_location_id']);
			$tranRow['recv_location_to'] = $locationTo->name;
			$tranRow['recv_note'] = $transfer['comment'];
			$transfers[] = $tranRow;
		}
		return $transfers;
	}
	
	
	protected function getHeaderOfHistoryTransfers() {
		return array(
				array(
						'col' => 'A',
						'text' => 'ID Đơn Hàng',
						'styles' => array(
								'color' => '75b6ed',
								'bold' => true,
								'is_fill' => true
						),
						'value_field' => 'recv_id',
				),
				array(
						'col' => 'B',
						'text' => 'Ngày Thực Hiện',
						'styles' => array(
								'color' => '75b6ed',
								'bold' => true,
								'is_fill' => true
						),
						'value_field' => 'recv_date',
				),
				array(
						'col' => 'C',
						'text' => 'D/S Sản Phẩm',
						'styles' => array(
								'color' => '75b6ed',
								'bold' => true,
								'is_fill' => true
						),
						'value_field' => 'recv_item',
				),
				array(
						'col' => 'D',
						'text' => 'Số Lượng',
						'styles' => array(
								'color' => '75b6ed',
								'bold' => true,
								'is_fill' => true
						),
						'value_field' => 'recv_qty',
				),
				
				array(
						'col' => 'E',
						'text' => 'Kho Chuyển',
						'styles' => array(
								'color' => '75b6ed',
								'bold' => true,
								'is_fill' => true
						),
						'value_field' => 'recv_location_from',
				),
				array(
						'col' => 'F',
						'text' => 'Kho Nhận',
						'styles' => array(
								'color' => '75b6ed',
								'bold' => true,
								'is_fill' => true
						),
						'value_field' => 'recv_location_to',
				),
				array(
						'col' => 'G',
						'text' => 'Ghi Chú',
						'styles' => array(
								'color' => '75b6ed',
								'bold' => true,
								'is_fill' => true
						),
						'value_field' => 'recv_note',
				)
		);
	}
	
	protected function exportItems() {
		$bizExcel = new BizExcel('AItems.xlsx');
		
		$locations = $this->Location->get_all();
		
		$excelContent = $bizExcel->setNumberRowStartBody(4)->setHeaderOfBody($this->getHeaderOfItems());
		
		foreach ($locations->result() as $index => $location)
		{
			$bizExcel->setDataExcel($this->getItemsByLocation($location->location_id));
			$bizExcel->setActiveSheet($index, $location->name)->generateFile(false, '', false);
		}
		$excelContent = $bizExcel->generateFile(false);
		$this->load->helper('download');
		force_download('AItems.xlsx', $excelContent);
	}
	
	protected function getItemsByLocation($locationId = 0) {
		$itemsResult = $this->Item->getByLocationId($locationId);
		$items = [];
		foreach ($itemsResult->result() as $objItem) {
			if (empty($objItem->quantity)) {
				continue;
			}
			$item['name'] = $objItem->name;
			$item['category'] = $objItem->category;
			$item['cost_price'] = to_currency($objItem->cost_price);
			$item['unit_price'] = to_currency($objItem->unit_price);
			$item['qty'] = (int) to_quantity($objItem->quantity);
			$items[] = $item;
		}
		return $items;
	}
	
	protected function getHeaderOfItems() {
		return array(
            array(
                'col' => 'A',
                'text' => 'STT',
                'styles' => array(
                    'color' => '75b6ed',
                    'bold' => true,
                    'is_fill' => true
                ),
                'value_field' => '__AUTO__',
            ),
            array(
                'col' => 'B',
                'text' => 'TÊN',
                'styles' => array(
                    'color' => '75b6ed',
                    'bold' => true,
                    'is_fill' => true
                ),
                'value_field' => 'name',
            ),
            array(
                'col' => 'C',
                'text' => 'DANH MỤC',
                'styles' => array(
                    'color' => '75b6ed',
                    'bold' => true,
                    'is_fill' => true
                ),
                'value_field' => 'item_name',
            ),
            array(
                'col' => 'D',
                'text' => 'GIÁ VỐN',
                'styles' => array(
                    'color' => '75b6ed',
                    'bold' => true,
                    'is_fill' => true
                ),
                'value_field' => 'category',
            ),
            array(
                'col' => 'E',
                'text' => 'GIÁ NHẬP',
                'styles' => array(
                    'color' => '75b6ed',
                    'bold' => true,
                    'is_fill' => true
                ),
                'value_field' => 'cost_price',
            ),
            array(
                'col' => 'F',
                'text' => 'GIÁ BÁN',
                'styles' => array(
                    'color' => '75b6ed',
                    'bold' => true,
                    'is_fill' => true
                ),
                'value_field' => 'unit_price',
            ),
            array(
                'col' => 'G',
                'text' => 'SỐ LƯỢNG',
                'styles' => array(
                    'color' => '75b6ed',
                    'bold' => true,
                    'is_fill' => true
                ),
                'value_field' => 'qty',
            ),
        );
	}
}