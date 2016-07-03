<?php
require_once (APPPATH . "controllers/Expenses.php");

class BizExpenses extends Expenses 
{
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
}
?>

