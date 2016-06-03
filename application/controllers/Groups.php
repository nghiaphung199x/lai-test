<?php

require_once ("Secure_area.php");
require_once ("interfaces/Idata_controller.php");

class Groups extends Secure_area implements Idata_controller
{
    function __construct()
    {
        parent::__construct('groups');
        $this->lang->load('groups');
        $this->lang->load('module');
        $this->load->model('Group');
    }

    function index($offset = 0)
    {
        $params = $this->session->userdata('group_search_data') ? $this->session->userdata('group_search_data') : array('offset' => 0, 'order_col' => 'name', 'order_dir' => 'ASC', 'search' => FALSE);
        if ($offset != $params['offset']) {
            redirect('groups/index/' . $params['offset']);
        }
        $this->check_action_permission('search');
        $config['base_url'] = site_url('groups/sorting');
        $config['per_page'] = $this->config->item('number_of_items_per_page') ? (int)$this->config->item('number_of_items_per_page') : 20;

        $data['controller_name'] = $this->_controller_name;
        $data['per_page'] = $config['per_page'];
        $data['search'] = $params['search'] ? $params['search'] : "";
        if ($data['search']) {
            $config['total_rows'] = $this->Group->search_count_all($data['search']);
            $table_data = $this->Group->search($data['search'], $data['per_page'], $params['offset'], $params['order_col'], $params['order_dir']);
        } else {
            $config['total_rows'] = $this->Group->count_all();
            $table_data = $this->Group->get_all($data['per_page'], $params['offset'], $params['order_col'], $params['order_dir']);
        }
        $this->load->library('pagination');
        $this->pagination->initialize($config);
        $data['pagination'] = $this->pagination->create_links();
        $data['order_col'] = $params['order_col'];
        $data['order_dir'] = $params['order_dir'];
        $data['total_rows'] = $config['total_rows'];
        $data['manage_table'] = get_groups_manage_table($table_data, $this);
        $this->load->view('groups/manage', $data);
    }

    function sorting()
    {
        $this->check_action_permission('search');
        $search = $this->input->post('search') ? $this->input->post('search') : "";
        $per_page = $this->config->item('number_of_items_per_page') ? (int)$this->config->item('number_of_items_per_page') : 20;

        $offset = $this->input->post('offset') ? $this->input->post('offset') : 0;
        $order_col = $this->input->post('order_col') ? $this->input->post('order_col') : 'name';
        $order_dir = $this->input->post('order_dir') ? $this->input->post('order_dir') : 'ASC';


        $group_search_data = array('offset' => $offset, 'order_col' => $order_col, 'order_dir' => $order_dir, 'search' => $search);
        $this->session->set_userdata("group_search_data", $group_search_data);

        if ($search) {
            $config['total_rows'] = $this->Group->search_count_all($search);
            $table_data = $this->Group->search($search, $per_page, $this->input->post('offset') ? $this->input->post('offset') : 0, $this->input->post('order_col') ? $this->input->post('order_col') : 'name', $this->input->post('order_dir') ? $this->input->post('order_dir') : 'ASC');
        } else {
            $config['total_rows'] = $this->Group->count_all();
            $table_data = $this->Group->get_all($per_page, $this->input->post('offset') ? $this->input->post('offset') : 0, $this->input->post('order_col') ? $this->input->post('order_col') : 'name', $this->input->post('order_dir') ? $this->input->post('order_dir') : 'ASC');
        }
        $config['base_url'] = site_url('groups/sorting');
        $config['per_page'] = $per_page;
        $this->load->library('pagination');
        $this->pagination->initialize($config);
        $data['pagination'] = $this->pagination->create_links();
        $data['manage_table'] = get_groups_manage_table_data_rows($table_data, $this);
        echo json_encode(array('manage_table' => $data['manage_table'], 'pagination' => $data['pagination']));
    }

    function search()
    {
        $this->check_action_permission('search');
        $search = $this->input->post('search');
        $offset = $this->input->post('offset') ? $this->input->post('offset') : 0;
        $order_col = $this->input->post('order_col') ? $this->input->post('order_col') : 'name';
        $order_dir = $this->input->post('order_dir') ? $this->input->post('order_dir') : 'ASC';

        $group_search_data = array('offset' => $offset, 'order_col' => $order_col, 'order_dir' => $order_dir, 'search' => $search);
        $this->session->set_userdata("group_search_data", $group_search_data);
        $per_page = $this->config->item('number_of_items_per_page') ? (int)$this->config->item('number_of_items_per_page') : 20;
        $search_data = $this->Group->search($search, $per_page, $this->input->post('offset') ? $this->input->post('offset') : 0, $this->input->post('order_col') ? $this->input->post('order_col') : 'name', $this->input->post('order_dir') ? $this->input->post('order_dir') : 'ASC');
        $config['base_url'] = site_url('groups/search');
        $config['total_rows'] = $this->Group->search_count_all($search);
        $config['per_page'] = $per_page;

        $this->load->library('pagination');
        $this->pagination->initialize($config);
        $data['pagination'] = $this->pagination->create_links();
        $data['manage_table'] = get_groups_manage_table_data_rows($search_data, $this);
        echo json_encode(array('manage_table' => $data['manage_table'], 'pagination' => $data['pagination']));

    }

    /*
    Gives search suggestions based on what is being searched for
    */
    function suggest()
    {
        //allow parallel searchs to improve performance.
        session_write_close();
        $suggestions = $this->Group->get_search_suggestions($this->input->get('term'), 100);
        echo json_encode($suggestions);
    }

    function _get_data($group_id)
    {
        $data = array();
        $data['entity'] = $this->Group->get_info($group_id);
        $data['all_modules'] = $this->Module->get_all_modules();
        $data['controller_name'] = $this->_controller_name;
        $data['logged_in_employee_id'] = $this->Employee->get_logged_in_employee_info()->person_id;
        return $data;
    }

    /*
        View detail Group
    */
    function view($group_id = -1, $redirect_code = 0)
    {
        $this->load->model('Module_action');
        $this->check_action_permission('add_update');
        $data = $this->_get_data($group_id);
        $data['redirect_code'] = $redirect_code;
        $this->load->view("groups/form", $data);
    }

    /*
        Inserts/Updates Group
    */
    function save($group_id = false)
    {
        /* Check Permission */
        $this->check_action_permission('add_update');

        /* Get All Data Submit */
        $data = $this->input->post('group');
        $permission_data = $this->input->post('permissions') != false ? $this->input->post('permissions') : array();
        $permission_action_data = $this->input->post('permissions_actions') != false ? $this->input->post('permissions_actions') : array();

        /* Get Redirect Code */
        $redirect_code = $this->input->post('redirect_code');

        if ($data['group_id'] = $this->Group->save($data, $group_id, $permission_data, $permission_action_data)) {
            /* New Group */
            if (!$group_id) {
                $success_message = lang('groups_created_successful') . ' [' . $data['name'] . ']';
                echo json_encode(array('success' => true, 'message' => $success_message, 'group_id' => $data['group_id'], 'redirect_code' => $redirect_code));
            } else {
                /* Update Group */
                $success_message = lang('groups_updated_successful') . ' [' . $data['name'] . ']';
                $this->session->set_flashdata('manage_success_message', $success_message);
                echo json_encode(array('success' => true, 'message' => $success_message, 'group_id' => $group_id, 'redirect_code' => $redirect_code));
            }
        } else {
            /* Failure */
            echo json_encode(array(
                'success' => false,
                'message' => lang('groups_error_adding_updating') . ' ' . $data['name'], 'group_id' => -1));
        }
    }

    /*
    This deletes groups from the groups table
    */
    function delete()
    {
        $this->check_action_permission('delete');
        $groups_to_delete = $this->input->post('ids');

        if ($this->Group->delete_list($groups_to_delete)) {
            echo json_encode(array('success' => true, 'message' => lang('groups_successful_deleted') . ' ' .
                count($groups_to_delete) . ' ' . lang('groups_one_or_multiple')));
        } else {
            echo json_encode(array('success' => false, 'message' => lang('groups_cannot_be_deleted')));
        }
    }

    function cleanup()
    {
        $this->Group->cleanup();
        echo json_encode(array('success' => true, 'message' => lang('groups_cleanup_sucessful')));
    }

    function clear_state()
    {
        $this->session->unset_userdata('group_search_data');
        redirect('groups');
    }

    function excel()
    {
        $this->load->helper('download');
        $this->load->helper('report');
        $header_row = $this->_excel_get_header_row();
        $this->load->helper('spreadsheet');
        $content = array_to_spreadsheet(array($header_row));
        force_download('groups_import.' . ($this->config->item('spreadsheet_format') == 'XLSX' ? 'xlsx' : 'csv'), $content);
    }

    function _excel_get_header_row()
    {
        return array(lang('groups_field_name'), lang('groups_field_description'));
    }

    function excel_export()
    {
        set_time_limit(0);

        $data = $this->Group->get_all()->result_object();
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
        force_download('groups_export.' . ($this->config->item('spreadsheet_format') == 'XLSX' ? 'xlsx' : 'csv'), $content);
        exit;
    }

    /**
     * @Loads the form for customers excel import
     */
    function excel_import()
    {
        $this->check_action_permission('add_update');
        $this->load->view("groups/excel_import", null);
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

                    $group_id = $this->Group->get_group_id($name);

                    $current_group = $this->Group->get_info($group_id);
                    $old_group_value = $current_group->value;

                    //If we don't have a gift card number skip the import
                    if (!$name) {
                        continue;
                    }

                    $group_data = array(
                        'name' => $name,
                        'description' => $description
                    );

                    if (!$this->Group->save($group_data, $group_id ? $group_id : FALSE)) {
                        echo json_encode(array('success' => false, 'message' => lang('groups_duplicate_group')));
                        return;
                    }
                }
            } else {
                echo json_encode(array('success' => false, 'message' => lang('common_upload_file_not_supported_format')));
                return;
            }
        }
        $this->db->trans_complete();
        echo json_encode(array('success' => true, 'message' => lang('groups_import_success')));
    }

    function test() {
        $logged_employee = $this->Employee->get_logged_in_employee_info();
        $module_id = 'departments';
        $action_id = 'add_update';
        $check_group_permission = true;
        echo $this->Employee->has_module_permission($module_id, $logged_employee->person_id, $check_group_permission) ? sprintf(' Allow module %s for current logged user', $module_id) : sprintf(' Deny module %s for current logged user', $module_id);
        echo '<br/>';
        echo $this->Employee->has_module_action_permission($module_id, $action_id, $logged_employee->person_id, $check_group_permission) ? sprintf(' Allow module action %s|%s for current logged user', $module_id, $action_id) : sprintf(' Deny module action %s|%s for current logged user', $module_id, $action_id);
    }
}

?>