<?php
require_once (APPPATH . "controllers/Expenses.php");

class BizExpenses extends Expenses 
{
	function __construct() {
		parent::__construct();
		$this->load->helper('bizexcel');
	}
	function index($offset = 0) {
		$params = $this->session->userdata('expenses_search_data') ? $this->session->userdata('expenses_search_data') : array('offset' => 0, 'order_col' => 'id', 'order_dir' => 'desc', 'search' => FALSE);
	
		if ($offset != $params['offset']) {
			redirect('expenses/index/' . $params['offset']);
		}
	
		$this->check_action_permission('search');
		$config['base_url'] = site_url('expenses/sorting');
		$config['total_rows'] = $this->Expense->count_all();
		$config['per_page'] = $this->config->item('number_of_items_per_page') ? (int) $this->config->item('number_of_items_per_page') : 20;
		$data['controller_name'] = $this->_controller_name;
		$data['per_page'] = $config['per_page'];
		$data['search'] = $params['search'] ? $params['search'] : "";
		if ($data['search']) {
			$config['total_rows'] = $this->Expense->search_count_all($data['search']);
			$table_data = $this->Expense->search($data['search'], $data['per_page'], $params['offset'], $params['order_col'], $params['order_dir']);
		} else {
			$config['total_rows'] = $this->Expense->count_all();
			$table_data = $this->Expense->get_all($data['per_page'], $params['offset'], $params['order_col'], $params['order_dir']);
		}
		$this->load->library('pagination');$this->pagination->initialize($config);
		$data['pagination'] = $this->pagination->create_links();
		$data['order_col'] = $params['order_col'];
		$data['order_dir'] = $params['order_dir'];
		$data['total_rows'] = $config['total_rows'];
		$data['manage_table'] = get_expenses_manage_table($table_data, $this);
		$this->load->view('expenses/manage', $data);
	}
	
	public function reprint($id = 0) {
		$data = [];
		$data['expense_info'] = $this->Expense->get_info($id);
		$typeOfPrint = 'A4.php';
		$data['print_block_html'] = $this->load->view('expenses/partials/' . $typeOfPrint, $data, TRUE);
		$this->load->view('expenses/reprint', $data);
	}
	
	public function export_excel($id = 0) {
		$bizExcel = new BizExcel('A2.xlsx');
		$excelContent = $bizExcel->setExtraData($this->getExtraDataForExportExpense($id))
							->generateFile(false);
		$this->load->helper('download');
		force_download('export_expense.xlsx', $excelContent);
		exit;
	}
	
	protected function getExtraDataForExportExpense($id = 0) {
		$expense_info = $this->Expense->get_info($id);
		$receiver = $this->Employee->get_info ( $expense_info->employee_id );
		return [
				[
					'cell' => 'A1',
					'value' => $this->config->item('company')
				],
				[
					'cell' => 'A2',
					'value' => $this->Location->get_info_for_key('address', isset($override_location_id) ? $override_location_id : FALSE)
				],
				[
					'cell' => 'I3',
					'value' => date(get_date_format(), strtotime($expense_info->expense_date))
				],
				[
					'cell' => 'C9',
					'value' => $receiver->first_name . ' ' . $receiver->last_name
				],
				[
					'cell' => 'C10',
					'value' => $this->Location->get_info_for_key('address', isset($override_location_id) ? $override_location_id : FALSE)
				],
				[
					'cell' => 'C11',
					'value' => $expense_info->expense_description
				],
				[
					'cell' => 'C12',
					'value' => NumberFormatToCurrency($expense_info->expense_amount) . $this->config->item('currency_symbol')
				],
				[
					'cell' => 'A13',
					'value' => '(Số tiền viết bằng chữ):' . getStringNumber((int) $expense_info->expense_amount)
				],
		];
	}
}
?>

