<?php
require_once ("Person_controller.php");

class Suppliers extends Person_controller
{
    function __construct()
    {
        parent::__construct('suppliers');
        $this->lang->load('suppliers');
        $this->lang->load('module');
        $this->load->model('Supplier_taxes');
        $this->load->model('Supplier');
    }


    function index($offset = 0)
    {
        $params = $this->session->userdata('supplier_search_data') ? $this->session->userdata('supplier_search_data') : array('offset' => 0, 'order_col' => 'company_name', 'order_dir' => 'asc', 'search' => FALSE);
        if ($offset != $params['offset']) {
            redirect('suppliers/index/' . $params['offset']);
        }
        $this->check_action_permission('search');
        $config['base_url'] = site_url('suppliers/sorting');
        $config['per_page'] = $this->config->item('number_of_items_per_page') ? (int)$this->config->item('number_of_items_per_page') : 20;

        $data['controller_name'] = $this->_controller_name;
        $data['per_page'] = $config['per_page'];
        $data['search'] = $params['search'] ? $params['search'] : "";
        if ($data['search']) {
            $config['total_rows'] = $this->Supplier->search_count_all($data['search']);
            $table_data = $this->Supplier->search($data['search'], $data['per_page'], $params['offset'], $params['order_col'], $params['order_dir']);
        } else {
            $config['total_rows'] = $this->Supplier->count_all();
            $table_data = $this->Supplier->get_all($data['per_page'], $params['offset'], $params['order_col'], $params['order_dir']);
        }
        $this->load->library('pagination');
        $this->pagination->initialize($config);
        $data['pagination'] = $this->pagination->create_links();
        $data['order_col'] = $params['order_col'];
        $data['order_dir'] = $params['order_dir'];
        $data['total_rows'] = $config['total_rows'];

        $data['manage_table'] = get_supplier_manage_table($table_data, $this);
        $this->load->view('people/manage', $data);
    }


    function sorting()
    {
        $this->check_action_permission('search');
        $search = $this->input->post('search') ? $this->input->post('search') : "";
        $per_page = $this->config->item('number_of_items_per_page') ? (int)$this->config->item('number_of_items_per_page') : 20;
        $offset = $this->input->post('offset') ? $this->input->post('offset') : 0;
        $order_col = $this->input->post('order_col') ? $this->input->post('order_col') : 'company_name';
        $order_dir = $this->input->post('order_dir') ? $this->input->post('order_dir') : 'asc';


        $supplier_search_data = array('offset' => $offset, 'order_col' => $order_col, 'order_dir' => $order_dir, 'search' => $search);
        $this->session->set_userdata("supplier_search_data", $supplier_search_data);
        if ($search) {
            $config['total_rows'] = $this->Supplier->search_count_all($search);
            $table_data = $this->Supplier->search($search, $per_page, $this->input->post('offset') ? $this->input->post('offset') : 0, $this->input->post('order_col') ? $this->input->post('order_col') : 'company_name', $this->input->post('order_dir') ? $this->input->post('order_dir') : 'asc');
        } else {
            $config['total_rows'] = $this->Supplier->count_all();
            $table_data = $this->Supplier->get_all($per_page, $this->input->post('offset') ? $this->input->post('offset') : 0, $this->input->post('order_col') ? $this->input->post('order_col') : 'company_name', $this->input->post('order_dir') ? $this->input->post('order_dir') : 'asc');
        }
        $config['base_url'] = site_url('suppliers/sorting');
        $config['per_page'] = $per_page;
        $this->load->library('pagination');
        $this->pagination->initialize($config);
        $data['pagination'] = $this->pagination->create_links();
        $data['manage_table'] = get_supplier_manage_table_data_rows($table_data, $this);
        echo json_encode(array('manage_table' => $data['manage_table'], 'pagination' => $data['pagination']));
    }

    function _excel_get_header_row()
    {
        return array(lang('suppliers_company_name'), lang('common_first_name'), lang('common_last_name'), lang('common_email'), lang('common_phone_number'), lang('common_address_1'), lang('common_address_2'), lang('common_city'), lang('common_state'), lang('common_zip'), lang('common_country'), lang('common_comments'), lang('suppliers_account_number'));
    }

    function clear_state()
    {
        $this->session->unset_userdata('supplier_search_data');
        redirect('suppliers');
    }

    function excel()
    {
        $this->load->helper('report');
        $header_row = $this->_excel_get_header_row();
        $this->load->helper('spreadsheet');
        $content = array_to_spreadsheet(array($header_row));
        $this->load->helper('download');
        force_download('import_suppliers.' . ($this->config->item('spreadsheet_format') == 'XLSX' ? 'xlsx' : 'csv'), $content);
    }

    /*
	function do_excel_import()
	{
		$this->load->helper('demo');
		if (is_on_demo_host())
		{
			$msg = lang('common_excel_import_disabled_on_demo');
			echo json_encode( array('success'=>false,'message'=>$msg) );
			return;
		}

		$file_info = pathinfo($_FILES['file_path']['name']);
		if($file_info['extension'] != 'xlsx' && $file_info['extension'] != 'csv')
		{
			echo json_encode(array('success'=>false,'message'=>lang('common_upload_file_not_supported_format')));
			return;
		}
		
		set_time_limit(0);
		$this->check_action_permission('add_update');
		$this->db->trans_start();
				
		$msg = 'do_excel_import';
		$failCodes = array();
		if ($_FILES['file_path']['error']!=UPLOAD_ERR_OK)
		{
			$msg = lang('common_excel_import_failed');
			echo json_encode( array('success'=>false,'message'=>$msg) );
			return;
		}
		else
		{
			if (($handle = fopen($_FILES['file_path']['tmp_name'], "r")) !== FALSE)
			{
				$this->load->helper('spreadsheet');
				$objPHPExcel = file_to_obj_php_excel($_FILES['file_path']['tmp_name']);
				$sheet = $objPHPExcel->getActiveSheet();
				$num_rows = $objPHPExcel->setActiveSheetIndex(0)->getHighestRow();
				
				//Loop through rows, skip header row
				for($k = 2;$k<=$num_rows; $k++)
				{
					
					$company_name = $sheet->getCellByColumnAndRow(0, $k)->getValue();
					if (!$company_name)
					{
						$company_name = '';
					}
					
					
					$first_name = $sheet->getCellByColumnAndRow(1, $k)->getValue();
					if (!$first_name)
					{
						$first_name = '';
					}
					
					$last_name = $sheet->getCellByColumnAndRow(2, $k)->getValue();
					if (!$last_name)
					{
						$last_name = '';
					}

					$email = $sheet->getCellByColumnAndRow(3, $k)->getValue();
					if (!$email)
					{
						$email = '';
					}

					$phone_number = $sheet->getCellByColumnAndRow(4, $k)->getValue();
					if (!$phone_number)
					{
						$phone_number = '';
					}

					$address_1 = $sheet->getCellByColumnAndRow(5, $k)->getValue();
					if (!$address_1)
					{
						$address_1 = '';
					}

					$address_2 = $sheet->getCellByColumnAndRow(6, $k)->getValue();
					if (!$address_2)
					{
						$address_2 = '';
					}

					$city = $sheet->getCellByColumnAndRow(7, $k)->getValue();
					if (!$city)
					{
						$city = '';
					}

					$state = $sheet->getCellByColumnAndRow(8, $k)->getValue();
					if (!$state)
					{
						$state = '';
					}

					$zip = $sheet->getCellByColumnAndRow(9, $k)->getValue();
					if (!$zip)
					{
						$zip = '';
					}

					$country = $sheet->getCellByColumnAndRow(10, $k)->getValue();
					if (!$country)
					{
						$country = '';
					}

					$comments = $sheet->getCellByColumnAndRow(11, $k)->getValue();
					if (!$comments)
					{
						$comments = '';
					}

					$account_number = $sheet->getCellByColumnAndRow(12, $k)->getValue();
					if (!$account_number)
					{
						$account_number = NULL;
					}					
					
						
					//If we don't have a company name  or first name skip the import
					if (!($company_name || $first_name))
					{
						continue;
					}
					
					$person_data = array(
					'first_name'=>$first_name,
					'last_name'=>$last_name,
					'email'=>$email,
					'phone_number'=>$phone_number,
					'address_1'=>$address_1,
					'address_2'=>$address_2,
					'city'=>$city,
					'state'=>$state,
					'zip'=>$zip,
					'country'=>$country,
					'comments'=>$comments
					);
					
					$supplier_data=array(
					'account_number'=>$account_number,
					'company_name' => $company_name,
					);
					
					$person_id = $sheet->getCellByColumnAndRow(13, $k)->getValue();
					
					if(!$this->Supplier->save_supplier($person_data,$supplier_data,$person_id ? $person_id : FALSE))
					{	
						echo json_encode( array('success'=>false,'message'=>lang('suppliers_duplicate_account_id')));
						return;
					}
				}
			}
			else 
			{
				echo json_encode( array('success'=>false,'message'=>lang('common_upload_file_not_supported_format')));
				return;
			}
		}
		$this->db->trans_complete();
		echo json_encode(array('success'=>true,'message'=>lang('suppliers_import_successfull')));
	}
    */

    function excel_import()
    {
        $this->check_action_permission('add_update');
        $this->load->view("suppliers/excel_import", null);
    }

    /* added for excel expert */
    function excel_export()
    {

        set_time_limit(0);

        $data = $this->Supplier->get_all($this->Supplier->count_all())->result_object();
        $this->load->helper('report');
        $rows = array();
        $header_row = $this->_excel_get_header_row();
        $header_row[] = lang('suppliers_id');
        $rows[] = $header_row;

        foreach ($data as $r) {
            $row = array(
                $r->company_name,
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
                $r->comments,
                $r->account_number,
                $r->person_id
            );
            $rows[] = $row;
        }
        $this->load->helper('spreadsheet');
        $content = array_to_spreadsheet($rows);
        $this->load->helper('download');
        force_download('suppliers_export.' . ($this->config->item('spreadsheet_format') == 'XLSX' ? 'xlsx' : 'csv'), $content);
        exit;
    }

    /*
    Returns supplier table data rows. This will be called with AJAX.
    */
    function search()
    {
        $this->check_action_permission('search');
        $search = $this->input->post('search');
        $offset = $this->input->post('offset') ? $this->input->post('offset') : 0;
        $order_col = $this->input->post('order_col') ? $this->input->post('order_col') : 'company_name';
        $order_dir = $this->input->post('order_dir') ? $this->input->post('order_dir') : 'asc';

        $supplier_search_data = array('offset' => $offset, 'order_col' => $order_col, 'order_dir' => $order_dir, 'search' => $search);
        $this->session->set_userdata("supplier_search_data", $supplier_search_data);
        $per_page = $this->config->item('number_of_items_per_page') ? (int)$this->config->item('number_of_items_per_page') : 20;
        $search_data = $this->Supplier->search($search, $per_page, $this->input->post('offset') ? $this->input->post('offset') : 0, $this->input->post('order_col') ? $this->input->post('order_col') : 'company_name', $this->input->post('order_dir') ? $this->input->post('order_dir') : 'asc');
        $config['base_url'] = site_url('suppliers/search');
        $config['total_rows'] = $this->Supplier->search_count_all($search);
        $config['per_page'] = $per_page;

        $this->load->library('pagination');
        $this->pagination->initialize($config);
        $data['pagination'] = $this->pagination->create_links();
        $data['manage_table'] = get_supplier_manage_table_data_rows($search_data, $this);
        echo json_encode(array('manage_table' => $data['manage_table'], 'pagination' => $data['pagination']));

    }

    function mailing_labels($supplier_ids)
    {
        $data['mailing_labels'] = array();

        foreach (explode('~', $supplier_ids) as $supplier_id) {
            $supplier_info = $this->Supplier->get_info($supplier_id);

            $label = array();
            $label['name'] = $supplier_info->company_name . ': ' . $supplier_info->first_name . ' ' . $supplier_info->last_name;
            $label['address_1'] = $supplier_info->address_1;
            $label['address_2'] = $supplier_info->address_2;
            $label['city'] = $supplier_info->city;
            $label['state'] = $supplier_info->state;
            $label['zip'] = $supplier_info->zip;
            $label['country'] = $supplier_info->country;

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
        $suggestions = $this->Supplier->get_supplier_search_suggestions($this->input->get('term'), 100);
        echo json_encode($suggestions);
    }

    /*
    Loads the supplier edit form
    */
    function view($supplier_id = -1, $redirect = 0)
    {
        $this->check_action_permission('add_update');
        $data = array();
        $data['controller_name'] = $this->_controller_name;
        $data['person_info'] = $this->Supplier->get_info($supplier_id);
        $data['supplier_tax_info'] = $this->Supplier_taxes->get_info($supplier_id);
        $data['redirect'] = $redirect;
        $this->load->view("suppliers/form", $data);
    }

    /*
    Inserts/updates a supplier
    */
    function save($supplier_id = -1)
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
        $supplier_data = array(
            'company_name' => $this->input->post('company_name'),
            'account_number' => $this->input->post('account_number') == '' ? null : $this->input->post('account_number'),
            'override_default_tax' => $this->input->post('override_default_tax') ? $this->input->post('override_default_tax') : 0,
        );

        $redirect = $this->input->post('redirect');

        if ($this->Supplier->save_supplier($person_data, $supplier_data, $supplier_id)) {
            if ($this->Location->get_info_for_key('mailchimp_api_key')) {
                $this->Person->update_mailchimp_subscriptions($this->input->post('email'), $this->input->post('first_name'), $this->input->post('last_name'), $this->input->post('mailing_lists'));
            }

            $success_message = '';

            //New supplier
            if ($supplier_id == -1) {
                $success_message = lang('suppliers_successful_adding') . ' ' . $supplier_data['company_name'];
                echo json_encode(array('success' => true, 'redirect' => $redirect, 'message' => $success_message, 'person_id' => $supplier_data['person_id']));
                $supplier_id = $supplier_data['person_id'];

            } else //previous supplier
            {
                $success_message = lang('suppliers_successful_updating') . ' ' . $supplier_data['company_name'];
                $this->session->set_flashdata('manage_success_message', $success_message);
                echo json_encode(array('success' => true, 'redirect' => $redirect, 'message' => $success_message, 'person_id' => $supplier_id));
            }

            $suppliers_taxes_data = array();
            $tax_names = $this->input->post('tax_names');
            $tax_percents = $this->input->post('tax_percents');
            $tax_cumulatives = $this->input->post('tax_cumulatives');
            for ($k = 0; $k < count($tax_percents); $k++) {
                if (is_numeric($tax_percents[$k])) {
                    $suppliers_taxes_data[] = array('name' => $tax_names[$k], 'percent' => $tax_percents[$k], 'cumulative' => isset($tax_cumulatives[$k]) ? $tax_cumulatives[$k] : '0');
                }
            }
            $this->Supplier_taxes->save($suppliers_taxes_data, $supplier_id);


            //Delete Image
            if ($this->input->post('del_image') && $supplier_id != -1) {
                $supplier_info = $this->Supplier->get_info($supplier_id);
                if ($supplier_info->image_id != null) {
                    $this->Person->update_image(NULL, $supplier_id);
                    $this->load->model('Appfile');
                    $this->Appfile->delete($supplier_info->image_id);
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

                if ($supplier_id == -1) {
                    $this->Person->update_image($image_file_id, $supplier_data['person_id']);
                } else {
                    $this->Person->update_image($image_file_id, $supplier_id);

                }
            }
        } else //failure
        {
            echo json_encode(array('success' => false, 'message' => lang('suppliers_error_adding_updating') . ' ' .
                $supplier_data['company_name'], 'person_id' => -1));
        }
    }

    function account_number_exists()
    {
        if ($this->Supplier->account_number_exists($this->input->post('account_number')))
            echo 'false';
        else
            echo 'true';

    }

    /*
    This deletes suppliers from the suppliers table
    */
    function delete()
    {
        $this->check_action_permission('delete');
        $suppliers_to_delete = $this->input->post('ids');

        if ($this->Supplier->delete_list($suppliers_to_delete)) {
            echo json_encode(array('success' => true, 'message' => lang('suppliers_successful_deleted') . ' ' .
                count($suppliers_to_delete) . ' ' . lang('suppliers_one_or_multiple')));
        } else {
            echo json_encode(array('success' => false, 'message' => lang('suppliers_cannot_be_deleted')));
        }
    }

    function cleanup()
    {
        $this->Supplier->cleanup();
        echo json_encode(array('success' => true, 'message' => lang('suppliers_cleanup_sucessful')));
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
                $data['fields'] = $this->Supplier->get_import_fields();
                $data['person_fields'] = $this->Supplier->get_person_import_fields();
                $html = $this->load->view('suppliers/import/result', $data, true);
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
        $entity_type = 'suppliers';
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
        $person_import_fields = $this->Supplier->get_person_import_fields();
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
                            $exists_row = $this->Person->exists_by_field($person_entity_type, $check_duplicate_field_name, $data[$check_duplicate_field_name]);
                            break;
                        case 'basic':
                            $exists_row = $this->Supplier->exists_by_field($entity_type, $check_duplicate_field_name, $data[$check_duplicate_field_name]);
                            break;
                        case 'extend':
                            $exists_row = $this->Attribute->exists_by_value($entity_type, $extend_data['attribute_id'], $extend_data['entity_value']);
                            break;
                        default:
                            $exists_row = $this->Supplier->exists_by_field($entity_type, $check_duplicate_field_name, $data[$check_duplicate_field_name]);
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
                    $supplier_id = $this->Supplier->save_supplier($person_data, $data, null);
                    if (!empty($supplier_id)) {
                        $stored_rows++;
                        /* Set extended attributes */
                        if (!empty($extend_data)) {
                            $extend_data['entity_id'] = $supplier_id;
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