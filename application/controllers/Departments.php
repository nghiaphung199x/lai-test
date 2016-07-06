<?php

require_once ("Secure_area.php");
require_once ("interfaces/Idata_controller.php");

class Departments extends Secure_area implements Idata_controller
{
    function __construct()
    {
        parent::__construct('departments');
        $this->lang->load('departments');
        $this->lang->load('module');
        $this->load->model('Department');
        global $global_breadcrumb;
        $global_breadcrumb[] = array('label' => 'Dashboard', 'url' => site_url('home'));
        $global_breadcrumb[] = array('label' => lang('departments_manage'), 'url' => site_url('departments'));
    }

    function index($offset = 0)
    {
        $params = $this->session->userdata('department_search_data') ? $this->session->userdata('department_search_data') : array('offset' => 0, 'order_col' => 'path', 'order_dir' => 'ASC', 'search' => FALSE);
        if ($offset != $params['offset']) {
            redirect('departments/index/' . $params['offset']);
        }
        $this->check_action_permission('search');
        $config['base_url'] = site_url('departments/sorting');
        $config['per_page'] = $this->config->item('number_of_items_per_page') ? (int)$this->config->item('number_of_items_per_page') : 20;

        $data['controller_name'] = $this->_controller_name;
        $data['per_page'] = $config['per_page'];
        $data['search'] = $params['search'] ? $params['search'] : "";
        if ($data['search']) {
            $config['total_rows'] = $this->Department->search_count_all($data['search']);
            $table_data = $this->Department->search($data['search'], $data['per_page'], $params['offset'], $params['order_col'], $params['order_dir']);
        } else {
            $config['total_rows'] = $this->Department->count_all();
            $table_data = $this->Department->get_all($data['per_page'], $params['offset'], $params['order_col'], $params['order_dir']);
        }
        $department_ids = array();
        foreach ($table_data->result() as $department) {
            $department_ids[] = $department->department_id;
        }

        $table_data->employees = $this->Department->get_employees($department_ids);

        $this->load->library('pagination');
        $this->pagination->initialize($config);
        $data['pagination'] = $this->pagination->create_links();
        $data['order_col'] = $params['order_col'];
        $data['order_dir'] = $params['order_dir'];
        $data['total_rows'] = $config['total_rows'];
        $data['manage_table'] = get_departments_manage_table($table_data, $this);
        $this->load->view('departments/manage', $data);
    }

    function sorting()
    {
        $this->check_action_permission('search');
        $search = $this->input->post('search') ? $this->input->post('search') : "";
        $per_page = $this->config->item('number_of_items_per_page') ? (int)$this->config->item('number_of_items_per_page') : 20;

        $offset = $this->input->post('offset') ? $this->input->post('offset') : 0;
        $order_col = $this->input->post('order_col') ? $this->input->post('order_col') : 'name';
        $order_dir = $this->input->post('order_dir') ? $this->input->post('order_dir') : 'asc';


        $department_search_data = array('offset' => $offset, 'order_col' => $order_col, 'order_dir' => $order_dir, 'search' => $search);
        $this->session->set_userdata("department_search_data", $department_search_data);

        if ($search) {
            $config['total_rows'] = $this->Department->search_count_all($search);
            $table_data = $this->Department->search($search, $per_page, $this->input->post('offset') ? $this->input->post('offset') : 0, $this->input->post('order_col') ? $this->input->post('order_col') : 'path', $this->input->post('order_dir') ? $this->input->post('order_dir') : 'ASC');
        } else {
            $config['total_rows'] = $this->Department->count_all();
            $table_data = $this->Department->get_all($per_page, $this->input->post('offset') ? $this->input->post('offset') : 0, $this->input->post('order_col') ? $this->input->post('order_col') : 'path', $this->input->post('order_dir') ? $this->input->post('order_dir') : 'ASC');
        }
        $config['base_url'] = site_url('departments/sorting');
        $config['per_page'] = $per_page;
        $this->load->library('pagination');
        $this->pagination->initialize($config);
        $data['pagination'] = $this->pagination->create_links();
        $data['manage_table'] = get_departments_manage_table_data_rows($table_data, $this);
        echo json_encode(array('manage_table' => $data['manage_table'], 'pagination' => $data['pagination']));
    }

    function search()
    {
        $this->check_action_permission('search');
        $search = $this->input->post('search');
        $offset = $this->input->post('offset') ? $this->input->post('offset') : 0;
        $order_col = $this->input->post('order_col') ? $this->input->post('order_col') : 'path';
        $order_dir = $this->input->post('order_dir') ? $this->input->post('order_dir') : 'ASC';

        $department_search_data = array('offset' => $offset, 'order_col' => $order_col, 'order_dir' => $order_dir, 'search' => $search);
        $this->session->set_userdata("department_search_data", $department_search_data);
        $per_page = $this->config->item('number_of_items_per_page') ? (int)$this->config->item('number_of_items_per_page') : 20;
        $search_data = $this->Department->search($search, $per_page, $this->input->post('offset') ? $this->input->post('offset') : 0, $this->input->post('order_col') ? $this->input->post('order_col') : 'name', $this->input->post('order_dir') ? $this->input->post('order_dir') : 'asc');
        $config['base_url'] = site_url('departments/search');
        $config['total_rows'] = $this->Department->search_count_all($search);
        $config['per_page'] = $per_page;

        $this->load->library('pagination');
        $this->pagination->initialize($config);
        $data['pagination'] = $this->pagination->create_links();
        $data['manage_table'] = get_departments_manage_table_data_rows($search_data, $this);
        echo json_encode(array('manage_table' => $data['manage_table'], 'pagination' => $data['pagination']));

    }

    /*
    Gives search suggestions based on what is being searched for
    */
    function suggest()
    {
        //allow parallel searchs to improve performance.
        session_write_close();
        $suggestions = $this->Department->get_search_suggestions($this->input->get('term'), 100);
        echo json_encode($suggestions);
    }

    function _get_data($department_id)
    {
        $data = array();
        $data['entity'] = $this->Department->get_info($department_id);
        $data['parents'] = $this->Department->get_all()->result();
        $data['all_modules'] = $this->Module->get_all_modules();
        $data['controller_name'] = $this->_controller_name;
        $data['logged_in_employee_id'] = $this->Employee->get_logged_in_employee_info()->person_id;
        return $data;
    }

    /*
        View detail Department
    */
    function view($department_id = -1, $redirect_code = 0)
    {
        global $global_breadcrumb;
        $global_breadcrumb[] = array('label' => lang('common_edit'), 'url' => '#');
        $this->load->model('Module_action');
        $this->check_action_permission('add_update');
        $data = $this->_get_data($department_id);
        $data['redirect_code'] = $redirect_code;
        $this->load->view("departments/form", $data);
    }

    /*
        Inserts/Updates Department
    */
    function save($department_id = false)
    {
        /* Check Permission */
        $this->check_action_permission('add_update');

        /* Get All Data Submit */
        $data = $this->input->post('department');
        $permission_data = $this->input->post('permissions') != false ? $this->input->post('permissions') : array();
        $permission_action_data = $this->input->post('permissions_actions') != false ? $this->input->post('permissions_actions') : array();

        /* Get Redirect Code */
        $redirect_code = $this->input->post('redirect_code');

        if ($data['department_id'] = $this->Department->save($data, $department_id, $permission_data, $permission_action_data)) {
            /* New Department */
            if (!$department_id) {
                $success_message = lang('departments_created_successful') . ' [' . $data['name'] . ']';
                echo json_encode(array('success' => true, 'message' => $success_message, 'department_id' => $data['department_id'], 'redirect_code' => $redirect_code));
            } else {
                /* Update Department */
                $success_message = lang('departments_updated_successful') . ' [' . $data['name'] . ']';
                $this->session->set_flashdata('manage_success_message', $success_message);
                echo json_encode(array('success' => true, 'message' => $success_message, 'department_id' => $department_id, 'redirect_code' => $redirect_code));
            }
        } else {
            /* Failure */
            echo json_encode(array(
                'success' => false,
                'message' => lang('departments_error_adding_updating') . ' ' . $data['name'], 'department_id' => -1));
        }
    }

    /*
    This deletes departments from the departments table
    */
    function delete()
    {
        $this->check_action_permission('delete');
        $departments_to_delete = $this->input->post('ids');

        if ($this->Department->delete_list($departments_to_delete)) {
            echo json_encode(array('success' => true, 'message' => lang('departments_successful_deleted') . ' ' .
                count($departments_to_delete) . ' ' . lang('departments_one_or_multiple')));
        } else {
            echo json_encode(array('success' => false, 'message' => lang('departments_cannot_be_deleted')));
        }
    }

    function cleanup()
    {
        $this->Department->cleanup();
        echo json_encode(array('success' => true, 'message' => lang('departments_cleanup_sucessful')));
    }

    function clear_state()
    {
        $this->session->unset_userdata('department_search_data');
        redirect('departments');
    }

    function excel()
    {
        $this->load->helper('download');
        $this->load->helper('report');
        $header_row = $this->_excel_get_header_row();
        $this->load->helper('spreadsheet');
        $content = array_to_spreadsheet(array($header_row));
        force_download('departments_import.' . ($this->config->item('spreadsheet_format') == 'XLSX' ? 'xlsx' : 'csv'), $content);
    }

    function _excel_get_header_row()
    {
        return array(lang('departments_field_name'), lang('departments_field_description'));
    }

    function excel_export()
    {
        set_time_limit(0);

        $data = $this->Department->get_all()->result_object();
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
        force_download('departments_export.' . ($this->config->item('spreadsheet_format') == 'XLSX' ? 'xlsx' : 'csv'), $content);
        exit;
    }

    /**
     * @Loads the form for customers excel import
     */
    function excel_import()
    {
        $this->check_action_permission('add_update');
        $this->load->view("departments/excel_import", null);
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

        if ($_FILES['file_path']['error'] != UPLOAD_ERR_OK) {
            $msg = lang('common_excel_import_failed');
            echo json_encode(array('success' => false, 'message' => $msg));
            return;
        } else {
            if (($handle = fopen($_FILES['file_path']['tmp_name'], "r")) !== FALSE) {
                $this->load->helper('spreadsheet');
                $objPHPExcel = file_to_obj_php_excel($_FILES['file_path']['tmp_name']);
                $sheet = $objPHPExcel->getActiveSheet();
                $num_rows = $objPHPExcel->setActiveSheetIndex(0)->getHighestRow();

                //Loop through rows, skip header row
                for ($k = 2; $k <= $num_rows; $k++) {
                    $name = $sheet->getCellByColumnAndRow(0, $k)->getValue();
                    if (!$name) {
                        $name = '';
                    }

                    $description = $sheet->getCellByColumnAndRow(1, $k)->getValue();

                    $department_id = $this->Department->get_department_id($name);

                    $current_department = $this->Department->get_info($department_id);
                    $old_department_value = $current_department->value;

                    //If we don't have a gift card number skip the import
                    if (!$name) {
                        continue;
                    }

                    $department_data = array(
                        'name' => $name,
                        'description' => $description
                    );

                    if (!$this->Department->save($department_data, $department_id ? $department_id : FALSE)) {
                        echo json_encode(array('success' => false, 'message' => lang('departments_duplicate_department')));
                        return;
                    }
                }
            } else {
                echo json_encode(array('success' => false, 'message' => lang('common_upload_file_not_supported_format')));
                return;
            }
        }
        $this->db->trans_complete();
        echo json_encode(array('success' => true, 'message' => lang('departments_import_success')));
    }
}

?>