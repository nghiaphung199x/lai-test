<?php
require_once (APPPATH . "controllers/Secure_area.php");

require_once (APPPATH . "controllers/interfaces/Idata_controller.php");

class BizQuotes_contracts extends Secure_area {

    function __construct($module_id = null) {
        parent::__construct('Quotes_contracts');
        $this->load->model('Quotes_contract');
    }

    function index() {
    	echo("====");echo("<pre>");
    	
    	exit("====END====");
        $config['base_url'] = site_url('quotes_contract/sorting');
        $config['total_rows'] = $this->Quotes_contract->count_all();
        $config['per_page'] = $this->config->item('number_of_items_per_page') ? (int) $this->config->item('number_of_items_per_page') : 20;
        $this->pagination->initialize($config);
        $data['pagination'] = $this->pagination->create_links();
        $data['controller_name'] = strtolower(get_class());
        $data['per_page'] = $config['per_page'];
        $data['manage_table'] = get_quotes_contract_manage_table($this->Quotes_contract->get_all($data['per_page']), $this);
        $this->load->view('quotes_contract/manage', $data);
    }

    function quotes_contract_sorting() {
        $this->check_action_permission('search');
        $search = $this->input->post('search');
        $cat = $this->input->post('cat');
        $per_page = $this->config->item('number_of_items_per_page') ? (int) $this->config->item('number_of_items_per_page') : 20;
        if ($search || $cat) {
            $config['total_rows'] = $this->Quotes_contract->search_count_all($search, $cat);
            $table_data = $this->Quotes_contract->search($search, $cat, $per_page, $this->input->post('offset') ? $this->input->post('offset') : 0, $this->input->post('order_col') ? $this->input->post('order_col') : 'id_quotes_contract', $this->input->post('order_dir') ? $this->input->post('order_dir') : 'desc');
        } else {
            $config['total_rows'] = $this->Quotes_contract->count_all();
            $table_data = $this->Quotes_contract->get_all($per_page, $this->input->post('offset') ? $this->input->post('offset') : 0, $this->input->post('order_col') ? $this->input->post('order_col') : 'id_quotes_contract', $this->input->post('order_dir') ? $this->input->post('order_dir') : 'desc');
        }
        $config['base_url'] = site_url('quotes_contract/sorting');
        $config['per_page'] = $per_page;
        $this->pagination->initialize($config);
        $data['pagination'] = $this->pagination->create_links();
        $data['manage_table'] = get_quotes_contract_manage_table_data_rows($table_data, $this);
        echo json_encode(array('manage_table' => $data['manage_table'], 'pagination' => $data['pagination']));
    }

    function quotes_contract_search() {
        $this->check_action_permission('search');
        $search = $this->input->post('search');
        $cat = $this->input->post('cat');
        $per_page = $this->config->item('number_of_items_per_page') ? (int) $this->config->item('number_of_items_per_page') : 20;
        $search_data = $this->Quotes_contract->search($search, $cat, $per_page, $this->input->post('offset') ? $this->input->post('offset') : 0, $this->input->post('order_col') ? $this->input->post('order_col') : 'id_quotes_contract', $this->input->post('order_dir') ? $this->input->post('order_dir') : 'desc');
        $config['base_url'] = site_url('quotes_contract/search');
        $config['total_rows'] = $this->Quotes_contract->search_count_all($search, $cat);
        $config['per_page'] = $per_page;
        $this->pagination->initialize($config);
        $data['pagination'] = $this->pagination->create_links();
        $data['manage_table'] = get_quotes_contract_manage_table_data_rows($search_data, $this);

        echo json_encode(array('manage_table' => $data['manage_table'], 'pagination' => $data['pagination']));
    }

    function quotes_contract_view($id = -1) {
        $this->check_action_permission('add_update');
        $data = array();
        $data['info_quotes_contract'] = $this->Quotes_contract->get_info($id);
        $this->load->view("quotes_contract/form", $data);
    }

    function quotes_contract_save($id = -1) {
        $title = $this->input->post("title_quotes_contract");
        $cat = $this->input->post("cat_quotes_contract");
        $content = $this->input->post("content_quotes_contract");
        $data = array(
            "title_quotes_contract" => $title,
            "content_quotes_contract" => $content,
            "cat_quotes_contract" => $cat
        );
        if ($this->Quotes_contract->save($data, $id)) {
            if ($id == -1) {
                echo json_encode(array('success' => true, 'message' => 'Thêm mới thành công!', 'id' => $data['id_cat']));
            } else { //previous item
                echo json_encode(array('success' => true, 'message' => 'Cập nhật thành công!', 'id' => $id));
            }
        } else {//failure
            echo json_encode(array('success' => false, 'message' => 'Lỗi thêm hoặc cập nhật! Vui lòng kiểm tra lại'));
        }
    }

    function quotes_contract_delete() {
        $this->check_action_permission('delete');
        $id = $this->input->post("ids");
        if ($this->Quotes_contract->delete_list($id)) {
            echo json_encode(array('success' => true, 'message' => 'Bạn đã xóa thành công ' . count($id) . ' mẫu báo giá - hợp đồng'));
        } else {
            echo json_encode(array('success' => false, 'message' => 'Lỗi! Vui lòng kiểm tra lại'));
        }
    }

    function quotes_contract_suggest() {
        $suggestions = $this->Quotes_contract->get_search_suggestions($this->input->get('term'), 100);
        echo json_encode($suggestions);
    }

}
