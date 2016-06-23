<?php

require_once ("Secure_area.php");
require_once ("interfaces/Idata_controller.php");

class Attribute_sets extends Secure_area implements Idata_controller
{
    function __construct()
    {
        parent::__construct('attribute_sets');
        $this->lang->load('attribute_sets');
        $this->lang->load('module');
        $this->load->model('Attribute_set');
    }

    function index($offset = 0)
    {
        $params = $this->session->userdata('attribute_set_search_data') ? $this->session->userdata('attribute_set_search_data') : array('offset' => 0, 'order_col' => 'id', 'order_dir' => 'DESC', 'search' => FALSE);
        if ($offset != $params['offset']) {
            redirect('attribute_sets/index/' . $params['offset']);
        }
        $this->check_action_permission('search');
        $config['base_url'] = site_url('attribute_sets/sorting');
        $config['per_page'] = $this->config->item('number_of_items_per_page') ? (int)$this->config->item('number_of_items_per_page') : 20;

        $data['controller_name'] = $this->_controller_name;
        $data['per_page'] = $config['per_page'];
        $data['search'] = $params['search'] ? $params['search'] : "";
        if ($data['search']) {
            $config['total_rows'] = $this->Attribute_set->search_count_all($data['search']);
            $table_data = $this->Attribute_set->search($data['search'], $data['per_page'], $params['offset'], $params['order_col'], $params['order_dir']);
        } else {
            $config['total_rows'] = $this->Attribute_set->count_all();
            $table_data = $this->Attribute_set->get_all($data['per_page'], $params['offset'], $params['order_col'], $params['order_dir']);
        }

        $this->load->library('pagination');
        $this->pagination->initialize($config);
        $data['pagination'] = $this->pagination->create_links();
        $data['order_col'] = $params['order_col'];
        $data['order_dir'] = $params['order_dir'];
        $data['total_rows'] = $config['total_rows'];
        $data['manage_table'] = $this->Attribute_set->get_grid($table_data, $this);
        $this->load->view('attribute_sets/manage', $data);
    }

    function sorting()
    {
        $this->check_action_permission('search');
        $search = $this->input->post('search') ? $this->input->post('search') : "";
        $per_page = $this->config->item('number_of_items_per_page') ? (int)$this->config->item('number_of_items_per_page') : 20;

        $offset = $this->input->post('offset') ? $this->input->post('offset') : 0;
        $order_col = $this->input->post('order_col') ? $this->input->post('order_col') : 'name';
        $order_dir = $this->input->post('order_dir') ? $this->input->post('order_dir') : 'asc';


        $attribute_set_search_data = array('offset' => $offset, 'order_col' => $order_col, 'order_dir' => $order_dir, 'search' => $search);
        $this->session->set_userdata("attribute_set_search_data", $attribute_set_search_data);

        if ($search) {
            $config['total_rows'] = $this->Attribute_set->search_count_all($search);
            $table_data = $this->Attribute_set->search($search, $per_page, $this->input->post('offset') ? $this->input->post('offset') : 0, $this->input->post('order_col') ? $this->input->post('order_col') : 'id', $this->input->post('order_dir') ? $this->input->post('order_dir') : 'DESC');
        } else {
            $config['total_rows'] = $this->Attribute_set->count_all();
            $table_data = $this->Attribute_set->get_all($per_page, $this->input->post('offset') ? $this->input->post('offset') : 0, $this->input->post('order_col') ? $this->input->post('order_col') : 'id', $this->input->post('order_dir') ? $this->input->post('order_dir') : 'DESC');
        }
        $config['base_url'] = site_url('attribute_sets/sorting');
        $config['per_page'] = $per_page;
        $this->load->library('pagination');
        $this->pagination->initialize($config);
        $data['pagination'] = $this->pagination->create_links();
        $data['manage_table'] = $this->Attribute_set->get_grid($table_data, $this);
        echo json_encode(array('manage_table' => $data['manage_table'], 'pagination' => $data['pagination']));
    }

    function search()
    {
        $this->check_action_permission('search');
        $search = $this->input->post('search');
        $offset = $this->input->post('offset') ? $this->input->post('offset') : 0;
        $order_col = $this->input->post('order_col') ? $this->input->post('order_col') : 'id';
        $order_dir = $this->input->post('order_dir') ? $this->input->post('order_dir') : 'DESC';

        $attribute_set_search_data = array('offset' => $offset, 'order_col' => $order_col, 'order_dir' => $order_dir, 'search' => $search);
        $this->session->set_userdata("attribute_set_search_data", $attribute_set_search_data);
        $per_page = $this->config->item('number_of_items_per_page') ? (int)$this->config->item('number_of_items_per_page') : 20;
        $search_data = $this->Attribute_set->search($search, $per_page, $this->input->post('offset') ? $this->input->post('offset') : 0, $this->input->post('order_col') ? $this->input->post('order_col') : 'name', $this->input->post('order_dir') ? $this->input->post('order_dir') : 'asc');
        $config['base_url'] = site_url('attribute_sets/search');
        $config['total_rows'] = $this->Attribute_set->search_count_all($search);
        $config['per_page'] = $per_page;

        $this->load->library('pagination');
        $this->pagination->initialize($config);
        $data['pagination'] = $this->pagination->create_links();
        $data['manage_table'] = $this->Attribute_set->get_grid($search_data, $this);
        echo json_encode(array('manage_table' => $data['manage_table'], 'pagination' => $data['pagination']));

    }

    /*
        Gives search suggestions based on what is being searched for
    */
    function suggest()
    {
        //allow parallel searchs to improve performance.
        session_write_close();
        $suggestions = $this->Attribute_set->get_search_suggestions($this->input->get('term'), 100);
        echo json_encode($suggestions);
    }

    function _get_data($attribute_set_id)
    {
        $data = array();
        $data['entity'] = $this->Attribute_set->get_info($attribute_set_id);
        $data['parents'] = $this->Attribute_set->get_all()->result();
        $data['all_modules'] = $this->Module->get_all_modules();
        $data['controller_name'] = $this->_controller_name;
        $data['logged_in_employee_id'] = $this->Employee->get_logged_in_employee_info()->person_id;
        return $data;
    }

    /*
        View detail Attribute_set
    */
    function view($attribute_set_id = -1, $redirect_code = 0)
    {
        $this->load->model('Module_action');
        $this->check_action_permission('add_update');
        $data = $this->_get_data($attribute_set_id);
        $data['redirect_code'] = $redirect_code;
        $this->load->view("attribute_sets/form", $data);
    }

    /*
        Inserts/Updates Attribute_set
    */
    function save($attribute_set_id = false)
    {
        /* Check Permission */
        $this->check_action_permission('add_update');

        /* Get All Data Submit */
        $data = $this->input->post('attribute_set');
        $permission_data = $this->input->post('permissions') != false ? $this->input->post('permissions') : array();
        $permission_action_data = $this->input->post('permissions_actions') != false ? $this->input->post('permissions_actions') : array();

        /* Get Redirect Code */
        $redirect_code = $this->input->post('redirect_code');

        if ($data['attribute_set_id'] = $this->Attribute_set->save($data, $attribute_set_id, $permission_data, $permission_action_data)) {
            /* New Attribute_set */
            if (!$attribute_set_id) {
                $success_message = lang('attribute_sets_created_successful') . ' [' . $data['name'] . ']';
                echo json_encode(array('success' => true, 'message' => $success_message, 'attribute_set_id' => $data['attribute_set_id'], 'redirect_code' => $redirect_code));
            } else {
                /* Update Attribute_set */
                $success_message = lang('attribute_sets_updated_successful') . ' [' . $data['name'] . ']';
                $this->session->set_flashdata('manage_success_message', $success_message);
                echo json_encode(array('success' => true, 'message' => $success_message, 'attribute_set_id' => $attribute_set_id, 'redirect_code' => $redirect_code));
            }
        } else {
            /* Failure */
            echo json_encode(array(
                'success' => false,
                'message' => lang('attribute_sets_error_adding_updating') . ' ' . $data['name'], 'attribute_set_id' => -1));
        }
    }

    /*
    This deletes attribute_sets from the attribute_sets table
    */
    function delete()
    {
        $this->check_action_permission('delete');
        $attribute_sets_to_delete = $this->input->post('ids');

        if ($this->Attribute_set->delete_list($attribute_sets_to_delete)) {
            echo json_encode(array('success' => true, 'message' => lang('attribute_sets_successful_deleted') . ' ' .
                count($attribute_sets_to_delete) . ' ' . lang('attribute_sets_one_or_multiple')));
        } else {
            echo json_encode(array('success' => false, 'message' => lang('attribute_sets_cannot_be_deleted')));
        }
    }

    function cleanup()
    {
        $this->Attribute_set->cleanup();
        echo json_encode(array('success' => true, 'message' => lang('attribute_sets_cleanup_sucessful')));
    }

    function clear_state()
    {
        $this->session->unset_userdata('attribute_set_search_data');
        redirect('attribute_sets');
    }

    function excel()
    {
        $this->load->helper('download');
        $this->load->helper('report');
        $header_row = $this->_excel_get_header_row();
        $this->load->helper('spreadsheet');
        $content = array_to_spreadsheet(array($header_row));
        force_download('attribute_sets_import.' . ($this->config->item('spreadsheet_format') == 'XLSX' ? 'xlsx' : 'csv'), $content);
    }

    function _excel_get_header_row()
    {
        return array(lang('attribute_sets_field_name'), lang('attribute_sets_field_description'));
    }

    function excel_export()
    {
        set_time_limit(0);

        $data = $this->Attribute_set->get_all()->result_object();
        $this->load->helper('report');
        $rows = array();
        $rows[] = $this->_excel_get_header_row();
        foreach ($data as $r) {
            $row = array(
                $r->name,
                $r->description,
            );
            $rows[] = $row;
        }

        $this->load->helper('download');
        $this->load->helper('spreadsheet');
        $content = array_to_spreadsheet($rows);
        force_download('attribute_sets_export.' . ($this->config->item('spreadsheet_format') == 'XLSX' ? 'xlsx' : 'csv'), $content);
        exit;
    }

    /**
     * @Loads the form for customers excel import
     */
    function excel_import()
    {
        $this->check_action_permission('add_update');
        $this->load->view("attribute_sets/excel_import", null);
    }

    /**
     * @imports gift cards
     * imports as new if gift card number is not found
     */
    function do_excel_import()
    {
        $this->load->helper('demo');

        if (is_on_demo_host()) {
            $msg = lang('common_excel_import_disabled_on_demo');
            echo json_encode(array('success' => false, 'message' => $msg));
            return;
        }

        set_time_limit(0);
        $this->check_action_permission('add_update');
        $this->db->trans_start();

        if ($_FILES['file_id']['error'] != UPLOAD_ERR_OK) {
            $msg = lang('common_excel_import_failed');
            echo json_encode(array('success' => false, 'message' => $msg));
            return;
        } else {
            if (($handle = fopen($_FILES['file_id']['tmp_name'], "r")) !== FALSE) {
                $this->load->helper('spreadsheet');
                $objPHPExcel = file_to_obj_php_excel($_FILES['file_id']['tmp_name']);
                $sheet = $objPHPExcel->getActiveSheet();
                $num_rows = $objPHPExcel->setActiveSheetIndex(0)->getHighestRow();

                //Loop through rows, skip header row
                for ($k = 2; $k <= $num_rows; $k++) {
                    $name = $sheet->getCellByColumnAndRow(0, $k)->getValue();
                    if (!$name) {
                        $name = '';
                    }

                    $description = $sheet->getCellByColumnAndRow(1, $k)->getValue();

                    $attribute_set_id = $this->Attribute_set->get_attribute_set_id($name);

                    $current_attribute_set = $this->Attribute_set->get_info($attribute_set_id);
                    $old_attribute_set_value = $current_attribute_set->value;

                    //If we don't have a gift card number skip the import
                    if (!$name) {
                        continue;
                    }

                    $attribute_set_data = array(
                        'name' => $name,
                        'description' => $description
                    );

                    if (!$this->Attribute_set->save($attribute_set_data, $attribute_set_id ? $attribute_set_id : FALSE)) {
                        echo json_encode(array('success' => false, 'message' => lang('attribute_sets_duplicate_attribute_set')));
                        return;
                    }
                }
            } else {
                echo json_encode(array('success' => false, 'message' => lang('common_upload_file_not_supported_format')));
                return;
            }
        }
        $this->db->trans_complete();
        echo json_encode(array('success' => true, 'message' => lang('attribute_sets_import_success')));
    }
}

?>