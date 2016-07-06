<?php
require_once ("Person_controller.php");
class Employees extends Person_controller
{
    function __construct()
    {
        parent::__construct('employees');
        $this->lang->load('employees');
        $this->lang->load('module');

    }

    function index($offset = 0)
    {
        $params = $this->session->userdata('employee_search_data') ? $this->session->userdata('employee_search_data') : array('offset' => 0, 'order_col' => 'last_name', 'order_dir' => 'asc', 'search' => FALSE);
        if ($offset != $params['offset']) {
            redirect('employees/index/' . $params['offset']);
        }
        $this->check_action_permission('search');
        $config['base_url'] = site_url('employees/sorting');
        $config['per_page'] = $this->config->item('number_of_items_per_page') ? (int)$this->config->item('number_of_items_per_page') : 20;

        $data['controller_name'] = $this->_controller_name;
        $data['per_page'] = $config['per_page'];
        $data['search'] = $params['search'] ? $params['search'] : "";
        if ($data['search']) {
            $config['total_rows'] = $this->Employee->search_count_all($data['search']);
            $table_data = $this->Employee->search($data['search'], $data['per_page'], $params['offset'], $params['order_col'], $params['order_dir']);
        } else {
            $config['total_rows'] = $this->Employee->count_all();
            $table_data = $this->Employee->get_all($data['per_page'], $params['offset'], $params['order_col'], $params['order_dir']);
        }

        $data['total_rows'] = $config['total_rows'];
        $this->load->library('pagination');
        $this->pagination->initialize($config);
        $data['pagination'] = $this->pagination->create_links();
        $data['order_col'] = $params['order_col'];
        $data['order_dir'] = $params['order_dir'];
        $data['manage_table'] = get_people_manage_table($table_data, $this);
        $this->load->view('people/manage', $data);
    }


    function sorting()
    {
        $this->check_action_permission('search');
        $search = $this->input->post('search') ? $this->input->post('search') : "";
        $per_page = $this->config->item('number_of_items_per_page') ? (int)$this->config->item('number_of_items_per_page') : 20;
        $offset = $this->input->post('offset') ? $this->input->post('offset') : 0;
        $order_col = $this->input->post('order_col') ? $this->input->post('order_col') : 'last_name';
        $order_dir = $this->input->post('order_dir') ? $this->input->post('order_dir') : 'asc';


        $employee_search_data = array('offset' => $offset, 'order_col' => $order_col, 'order_dir' => $order_dir, 'search' => $search);
        $this->session->set_userdata("employee_search_data", $employee_search_data);
        if ($search) {
            $config['total_rows'] = $this->Employee->search_count_all($search);
            $table_data = $this->Employee->search($search, $per_page, $this->input->post('offset') ? $this->input->post('offset') : 0, $this->input->post('order_col') ? $this->input->post('order_col') : 'last_name', $this->input->post('order_dir') ? $this->input->post('order_dir') : 'asc');
        } else {
            $config['total_rows'] = $this->Employee->count_all();
            $table_data = $this->Employee->get_all($per_page, $this->input->post('offset') ? $this->input->post('offset') : 0, $this->input->post('order_col') ? $this->input->post('order_col') : 'last_name', $this->input->post('order_dir') ? $this->input->post('order_dir') : 'asc');
        }
        $config['base_url'] = site_url('employees/sorting');
        $config['per_page'] = $per_page;
        $this->load->library('pagination');
        $this->pagination->initialize($config);
        $data['pagination'] = $this->pagination->create_links();
        $data['manage_table'] = get_people_manage_table_data_rows($table_data, $this);
        echo json_encode(array('manage_table' => $data['manage_table'], 'pagination' => $data['pagination']));
    }

    function clear_state()
    {
        $this->session->unset_userdata('employee_search_data');
        redirect('employees');
    }

    function check_duplicate()
    {
        echo json_encode(array('duplicate' => $this->Employee->check_duplicate($this->input->post('term'))));

    }

    /* added for excel expert */
    function excel_export()
    {
        set_time_limit(0);
        $this->load->helper('download');
        $data = $this->Employee->get_all()->result_object();
        $this->load->helper('report');
        $rows = array();
        $row = array(lang('common_username'), lang('common_first_name'), lang('common_last_name'), lang('common_email'), lang('common_phone_number'), lang('common_address_1'), lang('common_address_2'), lang('common_city'), lang('common_state'), lang('common_zip'), lang('common_country'), lang('common_comments'));
        $rows[] = $row;
        foreach ($data as $r) {
            $row = array(
                $r->username,
                $r->first_name,
                $r->last_name,
                $r->email,
                $r->phone_number,
                $r->address_1,
                $r->address_2,
                $r->city,
                $r->state,
                $r->zip,
                $r->country,
                $r->comments
            );
            $rows[] = $row;
        }
        $this->load->helper('spreadsheet');
        $content = array_to_spreadsheet($rows);
        force_download('employees_export.' . ($this->config->item('spreadsheet_format') == 'XLSX' ? 'xlsx' : 'csv'), $content);
        exit;
    }


    /*
    Returns employee table data rows. This will be called with AJAX.
    */
    function search()
    {
        $this->check_action_permission('search');
        $search = $this->input->post('search');
        $offset = $this->input->post('offset') ? $this->input->post('offset') : 0;
        $order_col = $this->input->post('order_col') ? $this->input->post('order_col') : 'last_name';
        $order_dir = $this->input->post('order_dir') ? $this->input->post('order_dir') : 'asc';

        $employee_search_data = array('offset' => $offset, 'order_col' => $order_col, 'order_dir' => $order_dir, 'search' => $search);
        $this->session->set_userdata("employee_search_data", $employee_search_data);
        $per_page = $this->config->item('number_of_items_per_page') ? (int)$this->config->item('number_of_items_per_page') : 20;
        $search_data = $this->Employee->search($search, $per_page, $this->input->post('offset') ? $this->input->post('offset') : 0, $this->input->post('order_col') ? $this->input->post('order_col') : 'last_name', $this->input->post('order_dir') ? $this->input->post('order_dir') : 'asc');
        $config['base_url'] = site_url('employees/search');
        $config['total_rows'] = $this->Employee->search_count_all($search);
        $config['per_page'] = $per_page;

        $this->load->library('pagination');
        $this->pagination->initialize($config);
        $data['pagination'] = $this->pagination->create_links();
        $data['manage_table'] = get_people_manage_table_data_rows($search_data, $this);
        echo json_encode(array('manage_table' => $data['manage_table'], 'pagination' => $data['pagination']));

    }

    function mailing_labels($employee_ids)
    {
        $data['mailing_labels'] = array();

        foreach (explode('~', $employee_ids) as $employee_id) {
            $employee_info = $this->Employee->get_info($employee_id);

            $label = array();
            $label['name'] = $employee_info->first_name . ' ' . $employee_info->last_name;
            $label['address_1'] = $employee_info->address_1;
            $label['address_2'] = $employee_info->address_2;
            $label['city'] = $employee_info->city;
            $label['state'] = $employee_info->state;
            $label['zip'] = $employee_info->zip;
            $label['country'] = $employee_info->country;

            $data['mailing_labels'][] = $label;

        }
        $data['type'] = $this->config->item('mailing_labels_type') == 'excel' ? 'excel' : 'pdf';
        $this->load->view("mailing_labels", $data);
    }

    /*
    Gives search suggestions based on what is being searched for
    */
    function suggest()
    {
        //allow parallel searchs to improve performance.
        session_write_close();
        $suggestions = $this->Employee->get_search_suggestions($this->input->get('term'), 100);
        echo json_encode($suggestions);
    }

    function _get_employee_data($employee_id)
    {
        $data = array();

        $data['person_info'] = $this->Employee->get_info($employee_id);
        $data['logged_in_employee_id'] = $this->Employee->get_logged_in_employee_info()->person_id;
        $data['all_modules'] = $this->Module->get_all_modules();
        $groups = $this->Group->get_all()->result();
        foreach ($groups as $group) {
            $data['groups'][$group->group_id] = $group->name;
        }
        $departments = $this->Department->get_all()->result();
        foreach ($departments as $department) {
            $data['departments'][$department->department_id] = $this->Department->get_level_line($department, '--', true, false) . ' ' . $department->name;
        }
        $data['controller_name'] = $this->_controller_name;

        /* Load Attribute Sets, Groups And Required Attributes */
        $this->load->model('Attribute_set');
        $this->load->model('Attribute_group');
        $this->load->model('Attribute');

        $data['attribute_sets'] = $this->Attribute_set->get_all()->result();
        $data['attribute_groups'] = $this->Attribute_group->get_all()->result();
        $data['attribute_values'] = $this->Attribute->get_entity_attributes(array('entity_id' => $employee_id, 'entity_type' => 'employees'));
        if (!empty($data['person_info']->attribute_set_id)) {
            $data['attributes'] = $this->Attribute_set->get_attributes($data['person_info']->attribute_set_id);
        }
        if (!empty($data['attribute_groups'])) {
            foreach ($data['attribute_groups'] as $key => $attribute_group) {
                if (!empty($data['attributes'])) {
                    foreach ($data['attributes'] as $attribute) {
                        if ($attribute->attribute_group_id == $attribute_group->id) {
                            $data['attribute_groups'][$key]->has_attributes = true;
                        }
                    }
                }
            }
        }

        $locations_list = $this->Location->get_all()->result();
        $authenticated_locations = $this->Employee->get_authenticated_location_ids($employee_id);
        $logged_in_employee_authenticated_locations = $this->Employee->get_authenticated_location_ids($data['logged_in_employee_id']);
        $can_assign_all_locations = $this->Employee->has_module_action_permission('employees', 'assign_all_locations', $this->Employee->get_logged_in_employee_info()->person_id);

        $locations = array();
        foreach ($locations_list as $row) {
            $has_access = in_array($row->location_id, $authenticated_locations);
            $can_assign_access = $can_assign_all_locations || (in_array($row->location_id, $logged_in_employee_authenticated_locations));

            $locations[$row->location_id] = array('name' => $row->name, 'has_access' => $has_access, 'can_assign_access' => $can_assign_access);
        }

        $data['locations'] = $locations;

        return $data;
    }

    /*
    Loads the employee edit form
    */
    function view($employee_id = -1, $redirect_code = 0)
    {
        $this->load->model('Module_action');
        $this->check_action_permission('add_update');
        $data = $this->_get_employee_data($employee_id);
        $data['redirect_code'] = $redirect_code;
        $this->load->view("employees/form", $data);
    }

    function clone_employee($employee_id)
    {
        $this->load->model('Module_action');

        $this->check_action_permission('add_update');
        $data = $this->_get_employee_data($employee_id);

        //Unset unique identifiers
        $data['person_info']->first_name = '';
        $data['person_info']->last_name = '';
        $data['person_info']->email = '';
        $data['person_info']->phone_number = '';
        $data['person_info']->image_id = '';
        $data['person_info']->address_1 = '';
        $data['person_info']->address_2 = '';
        $data['person_info']->comments = '';
        $data['person_info']->username = '';
        $data['person_info']->employee_number = '';
        $data['person_info']->birthday = '';
        $data['person_info']->reason_inactive = '';
        $data['person_info']->inactive = '';
        $data['person_info']->termination_date = '';

        $data['redirect_code'] = 2;
        $data['is_clone'] = TRUE;
        $this->load->view("employees/form", $data);
    }


    function exmployee_exists()
    {
        if ($this->Employee->employee_username_exists($this->input->post('username')))
            echo 'false';
        else
            echo 'true';

    }

    /*
    Inserts/updates an employee
    */
    function save($employee_id = -1)
    {
        $this->check_action_permission('add_update');
        $person_data = array(
            'first_name' => $this->input->post('first_name'),
            'last_name' => $this->input->post('last_name'),
            'email' => $this->input->post('email'),
            'phone_number' => $this->input->post('phone_number'),
            'address_1' => $this->input->post('address_1'),
            'address_2' => $this->input->post('address_2'),
            'city' => $this->input->post('city'),
            'state' => $this->input->post('state'),
            'zip' => $this->input->post('zip'),
            'country' => $this->input->post('country'),
            'comments' => $this->input->post('comments')
        );
        $permission_data = $this->input->post("permissions") != false ? $this->input->post("permissions") : array();
        $permission_action_data = $this->input->post("permissions_actions") != false ? $this->input->post("permissions_actions") : array();
        $location_data = $this->input->post('locations');
        $redirect_code = $this->input->post('redirect_code');
        //Password has been changed OR first time password set
        if ($this->input->post('password') != '') {
            $employee_data = array(
                'attribute_set_id' => $this->input->post('attribute_set_id'),
                'department_id' => $this->input->post('department_id'),
                'group_id' => $this->input->post('group_id'),
                'username' => $this->input->post('username'),
                'password' => md5($this->input->post('password')),
                'inactive' => $this->input->post('inactive') && $employee_id != 1 ? 1 : 0,
                'reason_inactive' => $this->input->post('reason_inactive') ? $this->input->post('reason_inactive') : NULL,
                'hire_date' => $this->input->post('hire_date') ? date('Y-m-d', strtotime($this->input->post('hire_date'))) : NULL,
                'employee_number' => $this->input->post('employee_number') ? $this->input->post('employee_number') : NULL,
                'birthday' => $this->input->post('birthday') ? date('Y-m-d', strtotime($this->input->post('birthday'))) : NULL,
                'termination_date' => $this->input->post('termination_date') ? date('Y-m-d', strtotime($this->input->post('termination_date'))) : NULL,
                'force_password_change' => $this->input->post('force_password_change') ? 1 : 0,
            );
        } else //Password not changed
        {
            $employee_data = array(
                'attribute_set_id' => $this->input->post('attribute_set_id'),
                'department_id' => $this->input->post('department_id'),
                'group_id' => $this->input->post('group_id'),
                'username' => $this->input->post('username'),
                'inactive' => $this->input->post('inactive') && $employee_id != 1 ? 1 : 0,
                'reason_inactive' => $this->input->post('reason_inactive') ? $this->input->post('reason_inactive') : NULL,
                'hire_date' => $this->input->post('hire_date') ? date('Y-m-d', strtotime($this->input->post('hire_date'))) : NULL,
                'employee_number' => $this->input->post('employee_number') ? $this->input->post('employee_number') : NULL,
                'birthday' => $this->input->post('birthday') ? date('Y-m-d', strtotime($this->input->post('birthday'))) : NULL,
                'termination_date' => $this->input->post('termination_date') ? date('Y-m-d', strtotime($this->input->post('termination_date'))) : NULL,
                'force_password_change' => $this->input->post('force_password_change') ? 1 : 0,
            );
        }

        //Commission
        $employee_data['commission_percent'] = (float)$this->input->post('commission_percent');
        $employee_data['commission_percent_type'] = $this->input->post('commission_percent_type');
        $employee_data['hourly_pay_rate'] = (float)$this->input->post('hourly_pay_rate');

        $this->load->helper('directory');

        $valid_languages = str_replace(DIRECTORY_SEPARATOR, '', directory_map(APPPATH . 'language/', 1));
        $employee_data = array_merge($employee_data, array('language' => in_array($this->input->post('language'), $valid_languages) ? $this->input->post('language') : 'english'));

        $this->load->helper('demo');
        if ((is_on_demo_host()) && $employee_id == 1) {
            //failure
            echo json_encode(array('success' => false, 'message' => lang('common_employees_error_updating_demo_admin'), 'person_id' => -1));
        } elseif ((is_array($location_data) && count($location_data) > 0) && $this->Employee->save_employee($person_data, $employee_data, $permission_data, $permission_action_data, $location_data, $employee_id)) {

            /* Update Extended Attributes */
            if (!class_exists('Attribute')) {
                $this->load->model('Attribute');
            }
            $attributes = $this->input->post('attributes');
            if (!empty($attributes)) {
                $this->Attribute->reset_attributes(array('entity_id' => $employee_id, 'entity_type' => 'employees'));
                foreach ($attributes as $attribute_id => $value) {
                    $attribute_value = array('entity_id' => $employee_id, 'entity_type' => 'employees', 'attribute_id' => $attribute_id, 'entity_value' => $value);
                    $this->Attribute->set_attributes($attribute_value);
                }
            }
            /* End Update */

            if ($this->Location->get_info_for_key('mailchimp_api_key')) {
                $this->Person->update_mailchimp_subscriptions($this->input->post('email'), $this->input->post('first_name'), $this->input->post('last_name'), $this->input->post('mailing_lists'));
            }

            $success_message = '';

            //New employee
            if ($employee_id == -1) {
                $success_message = lang('common_employees_successful_adding') . ' ' . $person_data['first_name'] . ' ' . $person_data['last_name'];
                echo json_encode(array('success' => true, 'message' => $success_message, 'person_id' => $employee_data['person_id'], 'redirect_code' => $redirect_code));
            } else //previous employee
            {
                $success_message = lang('common_employees_successful_updating') . ' ' . $person_data['first_name'] . ' ' . $person_data['last_name'];
                $this->session->set_flashdata('manage_success_message', $success_message);
                echo json_encode(array('success' => true, 'message' => $success_message, 'person_id' => $employee_id, 'redirect_code' => $redirect_code));
            }


            //Delete Image
            if ($this->input->post('del_image') && $employee_id != -1) {
                $employee_info = $this->Employee->get_info($employee_id);
                if ($employee_info->image_id != null) {
                    $this->Person->update_image(NULL, $employee_id);
                    $this->load->model('Appfile');
                    $this->Appfile->delete($employee_info->image_id);
                }
            }

            //Save Image File
            if (!empty($_FILES["image_id"]) && $_FILES["image_id"]["error"] == UPLOAD_ERR_OK) {

                $allowed_extensions = array('png', 'jpg', 'jpeg', 'gif');
                $extension = strtolower(pathinfo($_FILES["image_id"]["name"], PATHINFO_EXTENSION));
                if (in_array($extension, $allowed_extensions)) {
                    $config['image_library'] = 'gd2';
                    $config['source_image'] = $_FILES["image_id"]["tmp_name"];
                    $config['create_thumb'] = FALSE;
                    $config['maintain_ratio'] = TRUE;
                    $config['width'] = 400;
                    $config['height'] = 300;
                    $this->load->library('image_lib', $config);
                    $this->image_lib->resize();
                    $this->load->model('Appfile');
                    $image_file_id = $this->Appfile->save($_FILES["image_id"]["name"], file_get_contents($_FILES["image_id"]["tmp_name"]));
                }
                if ($employee_id == -1) {
                    $this->Person->update_image($image_file_id, $employee_data['person_id']);
                } else {
                    $this->Person->update_image($image_file_id, $employee_id);

                }
            }
        } else //failure
        {
            echo json_encode(array('success' => false, 'message' => lang('common_employees_error_adding_updating') . ' ' .
                $person_data['first_name'] . ' ' . $person_data['last_name'], 'person_id' => -1));
        }
    }

    function set_language()
    {
        $employee_id = $this->Employee->get_logged_in_employee_info()->person_id;
        $this->load->helper('directory');

        $valid_languages = str_replace(DIRECTORY_SEPARATOR, '', directory_map(APPPATH . 'language/', 1));
        $language_id = in_array($this->input->post('employee_language_id'), $valid_languages) ? $this->input->post('employee_language_id') : 'english';

        $this->load->helper('demo');
        if ((is_on_demo_host()) && $employee_id == 1) {
            //failure
            echo json_encode(array('success' => false, 'message' => lang('common_employees_error_updating_demo_admin'), 'person_id' => -1));
        } else {
            $this->Employee->set_language($language_id, $employee_id);
        }
    }

    /*
    This deletes employees from the employees table
    */
    function delete()
    {
        $this->check_action_permission('delete');
        $employees_to_delete = $this->input->post('ids');

        if (in_array(1, $employees_to_delete)) {
            //failure
            echo json_encode(array('success' => false, 'message' => lang('employees_cannot_delete_default_user')));
        } elseif ($this->Employee->delete_list($employees_to_delete)) {
            echo json_encode(array('success' => true, 'message' => lang('employees_successful_deleted') . ' ' .
                count($employees_to_delete) . ' ' . lang('employees_one_or_multiple')));
        } else {
            echo json_encode(array('success' => false, 'message' => lang('employees_cannot_be_deleted')));
        }
    }

    function cleanup()
    {
        $this->Employee->cleanup();
        echo json_encode(array('success' => true, 'message' => lang('employees_cleanup_sucessful')));
    }

    function excel_import()
    {
        $this->check_action_permission('add_update');
        $this->load->view("employees/excel_import", null);
    }

    /**
     * @Loads the form for excel import
     */
    function do_excel_import()
    {
        $this->check_action_permission('add_update');
        $this->load->helper('demo');
        if (is_on_demo_host()) {
            $msg = lang('common_excel_import_disabled_on_demo');
            echo json_encode(array('success' => false, 'message' => $msg));
            return;
        }
        if ($_FILES['file_path']['error'] != UPLOAD_ERR_OK) {
            $msg = lang('common_excel_import_failed');
            echo json_encode(array('success' => false, 'message' => $msg));
            return;
        } else {
            if (($handle = fopen($_FILES['file_path']['tmp_name'], "r")) !== FALSE) {
                $this->load->helper('spreadsheet');
                $objPHPExcel = file_to_obj_php_excel($_FILES['file_path']['tmp_name']);
                $end_column = $objPHPExcel->setActiveSheetIndex(0)->getHighestColumn();
                $this->load->model('Attribute_set');
                $data['attribute_sets'] = $this->Attribute_set->get_all()->result();
                $data['sheet'] = $objPHPExcel->getActiveSheet();
                $data['num_rows'] = $objPHPExcel->setActiveSheetIndex(0)->getHighestRow();
                $data['columns'] = range('A', $end_column);
                $data['fields'] = $this->Employee->get_import_fields();
                $data['person_fields'] = $this->Employee->get_person_import_fields();
                $html = $this->load->view('employees/import/result', $data, true);
                $result = array('success' => true, 'message' => lang('item_kits_import_success'), 'html' => $html);
                echo json_encode($result);
                return;
            } else {
                echo json_encode(array('success' => false, 'message' => lang('common_upload_file_not_supported_format')));
                return;
            }
        }
        $result = array('success' => true, 'message' => lang('item_kits_import_success'));
        echo json_encode($result);
    }

    /**
     * Import Real Data
     **/
    public function action_import_data()
    {
        $this->check_action_permission('add_update');
        $this->load->helper('demo');
        if (is_on_demo_host()) {
            $msg = lang('common_excel_import_disabled_on_demo');
            echo json_encode(array('success' => false, 'message' => $msg));
            return;
        }
        $this->load->model('Attribute');
        $entity_type = 'employees';
        $person_entity_type = 'people';
        $check_duplicate_field = $this->input->post('check_duplicate_field');
        $field_parts = explode(':', $check_duplicate_field);
        if (count($field_parts) == 2) {
            $check_duplicate_field_type = $field_parts[0];
            $check_duplicate_field_name = $field_parts[1];
        }
        $attribute_set_id = $this->input->post('attribute_set_id');
        $columns = $this->input->post('columns');
        $rows = $this->input->post('rows');
        $selected_rows = $this->input->post('selected_rows');
        $stored_rows = 0;
        $person_import_fields = $this->Employee->get_person_import_fields();
        if (empty($rows) || empty($selected_rows)) {
            $msg = lang('common_error');
            echo json_encode(array('success' => true, 'message' => $msg));
            return;
        }
        foreach ($rows as $index => $row) {
            if (!isset($selected_rows[$index])) {
                continue;
            }
            $data = array('attribute_set_id' => $attribute_set_id);
            $person_data = array();
            $extend_data = array();
            foreach ($columns as $excel_column => $field_column) {
                if (!empty($field_column) && !empty($row[$excel_column])) {
                    $field_parts = explode(':', $field_column);

                    /* Set Basic Attributes */
                    if (count($field_parts) == 2) {
                        switch ($field_parts[0]) {
                            case 'person':
                                $person_data[$field_parts[1]] = $row[$excel_column];
                                break;
                            case 'basic':
                                $data[$field_parts[1]] = $row[$excel_column];
                                break;
                            case 'extend':
                                $extend_data = array(
                                    'entity_type' => $entity_type,
                                    'attribute_id' => $field_parts[1],
                                    'entity_value' => $row[$excel_column],
                                );
                                break;
                            default:
                                $data[$field_parts[1]] = $row[$excel_column];
                                break;
                        }
                    }
                }
            }
            try {
                /* Check duplicate item */
                $exists_row = false;
                if (isset($check_duplicate_field_type) && isset($check_duplicate_field_name)) {
                    switch ($check_duplicate_field_type) {
                        case 'person':
                            $exists_row = $this->Person->exists_by_field($person_entity_type, $check_duplicate_field_name, $person_data[$check_duplicate_field_name], false, false);
                            break;
                        case 'basic':
                            $exists_row = $this->Employee->exists_by_field($entity_type, $check_duplicate_field_name, $data[$check_duplicate_field_name]);
                            break;
                        case 'extend':
                            $exists_row = $this->Attribute->exists_by_value($entity_type, $extend_data['attribute_id'], $extend_data['entity_value']);
                            break;
                        default:
                            $exists_row = $this->Employee->exists_by_field($entity_type, $check_duplicate_field_name, $data[$check_duplicate_field_name]);
                            break;
                    }
                }

                if (!$exists_row) {

                    /* Auto fill empty person fields */
                    foreach ($person_import_fields as $person_import_field) {
                        if (!isset($person_data[$person_import_field])) {
                            $person_data[$person_import_field] = '';
                        }
                    }

                    /* Auto fill required fields */
                    if (empty($data['password'])) {
                        $data['password'] = '123456a@';
                    }
                    if (empty($person_data['birth_date'])) {
                        $person_data['birth_date'] = '';
                    }
                    if (empty($person_data['email'])) {
                        $person_data['email'] = 'unknown@gmail.com';
                    }
                    if (empty($person_data['address_1'])) {
                        $person_data['address_1'] = '';
                    }
                    if (empty($person_data['address_2'])) {
                        $person_data['address_2'] = '';
                    }
                    if (empty($person_data['city'])) {
                        $person_data['city'] = '';
                    }
                    if (empty($person_data['state'])) {
                        $person_data['state'] = '';
                    }
                    if (empty($person_data['zip'])) {
                        $person_data['zip'] = '';
                    }
                    if (empty($person_data['country'])) {
                        $person_data['country'] = '';
                    }
                    if (empty($person_data['phone_number'])) {
                        $person_data['phone_number'] = '';
                    }
                    if (empty($person_data['comments'])) {
                        $person_data['comments'] = '';
                    }

                    $permission_data = array();
                    $permission_action_data = array();
                    $location_data = array();

                    $employee_id = $this->Employee->save_employee($person_data, $data, $permission_data, $permission_action_data, $location_data);

                    if (!empty($employee_id)) {
                        $stored_rows++;
                        /* Set extended attributes */
                        if (!empty($extend_data)) {
                            $extend_data['entity_id'] = $employee_id;
                            $this->Attribute->set_attributes($extend_data);
                        }
                    }
                }
            } catch (Exception $ex) {
                continue;
            }
        }
        if (!empty($stored_rows)) {
            $msg = $stored_rows . ' ' . lang('common_record_stored');
            echo json_encode(array('success' => true, 'message' => $msg));
            return;
        }
        $msg = $stored_rows . ' ' . lang('common_record_stored');
        echo json_encode(array('success' => false, 'message' => $msg));
    }
}

?>