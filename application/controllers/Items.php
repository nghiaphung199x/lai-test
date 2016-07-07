<?php

require_once ("Secure_area.php");
require_once ("interfaces/Idata_controller.php");

class Items extends Secure_area implements Idata_controller
{
    function __construct()
    {
        parent::__construct('items');
        $this->load->model('Inventory');
        $this->load->model('Additional_item_numbers');
        $this->lang->load('items');
        $this->lang->load('module');
        $this->load->model('Item');
        $this->load->model('Category');
        $this->load->model('Tag');
        $this->load->model('Supplier');

    }

    //change text to check line endings
    //new line endings

    function index($offset = 0)
    {
        $params = $this->session->userdata('item_search_data') ? $this->session->userdata('item_search_data') : array('offset' => 0, 'order_col' => 'name', 'order_dir' => 'asc', 'search' => FALSE, 'category_id' => FALSE, 'fields' => 'all');
        if ($offset != $params['offset']) {
            redirect('items/index/' . $params['offset']);
        }

        $this->check_action_permission('search');
        $config['base_url'] = site_url('items/sorting');
        $config['per_page'] = $this->config->item('number_of_items_per_page') ? (int)$this->config->item('number_of_items_per_page') : 20;

        $data['controller_name'] = $this->_controller_name;
        $data['per_page'] = $config['per_page'];
        $data['search'] = $params['search'] ? $params['search'] : "";
        $data['category_id'] = $params['category_id'] ? $params['category_id'] : "";
        $data['categories'][''] = lang('common_all');
        $categories = $this->Category->sort_categories_and_sub_categories($this->Category->get_all_categories_and_sub_categories());
        foreach ($categories as $key => $value) {
            $name = str_repeat('&nbsp;&nbsp;', $value['depth']) . $value['name'];
            $data['categories'][$key] = $name;
        }

        $data['fields'] = $params['fields'] ? $params['fields'] : "all";

        if ($data['search'] || $data['category_id']) {
            $config['total_rows'] = $this->Item->search_count_all($data['search'], $data['category_id'], 10000, $data['fields']);
            $table_data = $this->Item->search($data['search'], $data['category_id'], $data['per_page'], $params['offset'], $params['order_col'], $params['order_dir'], $data['fields']);
        } else {
            $config['total_rows'] = $this->Item->count_all();
            $table_data = $this->Item->get_all($data['per_page'], $params['offset'], $params['order_col'], $params['order_dir']);
        }

        $data['total_rows'] = $config['total_rows'];
        $this->load->library('pagination');
        $this->pagination->initialize($config);
        $data['pagination'] = $this->pagination->create_links();
        $data['order_col'] = $params['order_col'];
        $data['order_dir'] = $params['order_dir'];
        $data['manage_table'] = get_items_manage_table($table_data, $this);
        $this->load->view('items/manage', $data);
    }

    function categories()
    {
        $this->check_action_permission('manage_categories');
        $categories = $this->Category->get_all_categories_and_sub_categories_as_tree();
        $data = array('category_tree' => $this->_category_tree_list($categories));
        $this->load->view('items/categories', $data);
    }

    function save_category($category_id = FALSE)
    {
        $this->check_action_permission('manage_categories');

        $parent_id = $this->input->post('parent_id');
        $category_name = $this->input->post('category_name');
        $hide_from_grid = $this->input->post('hide_from_grid') ? 1 : 0;

        if (!$parent_id) {
            $parent_id = NULL;
        }

        if ($this->Category->save($category_name, $hide_from_grid, $parent_id, $category_id)) {
            echo json_encode(array('success' => true, 'message' => lang('items_category_successful_adding') . ' ' . $category_name));
        } else {
            echo json_encode(array('success' => false, 'message' => lang('items_category_successful_error')));
        }
    }

    function delete_category()
    {
        $this->check_action_permission('manage_categories');
        $category_id = $this->input->post('category_id');
        if ($this->Category->delete($category_id)) {
            echo json_encode(array('success' => true, 'message' => lang('items_successful_deleted')));
        } else {
            echo json_encode(array('success' => false, 'message' => lang('items_cannot_be_deleted')));
        }
    }

    function get_category_tree_list()
    {
        $categories = $this->Category->get_all_categories_and_sub_categories_as_tree();
        echo $this->_category_tree_list($categories);
    }

    function manage_tags()
    {
        $this->check_action_permission('manage_tags');
        $tags = $this->Tag->get_all();
        $data = array('tags' => $tags, 'tag_list' => $this->_tag_list());
        $this->load->view('items/tags', $data);
    }

    function save_tag($tag_id = FALSE)
    {
        $this->check_action_permission('manage_tags');
        $tag_name = $this->input->post('tag_name');

        if ($this->Tag->save($tag_name, $tag_id)) {
            echo json_encode(array('success' => true, 'message' => lang('items_tag_successful_adding') . ' ' . $tag_name));
        } else {
            echo json_encode(array('success' => false, 'message' => lang('items_tag_successful_error')));
        }
    }

    function delete_tag()
    {
        $this->check_action_permission('manage_tags');
        $tag_id = $this->input->post('tag_id');
        if ($this->Tag->delete($tag_id)) {
            echo json_encode(array('success' => true, 'message' => lang('items_successful_deleted')));
        } else {
            echo json_encode(array('success' => false, 'message' => lang('items_cannot_be_deleted')));
        }
    }

    function tag_list()
    {
        echo $this->_tag_list();
    }

    function _tag_list()
    {
        $tags = $this->Tag->get_all();
        $return = '<ul>';
        foreach ($tags as $tag_id => $tag) {
            $return .= '<li>' . $tag['name'] .
                '<a href="javascript:void(0);" class="edit_tag" data-name = "' . H($tag['name']) . '" data-tag_id="' . $tag_id . '">[' . lang('common_edit') . ']</a> ' .
                '<a href="javascript:void(0);" class="delete_tag" data-tag_id="' . $tag_id . '">[' . lang('common_delete') . ']</a> ';
            $return .= '</li>';
        }
        $return .= '</ul>';

        return $return;
    }

    function _category_tree_list($tree)
    {
        $return = '';
        if (!is_null($tree) && count($tree) > 0) {
            $return = '<ul>';
            foreach ($tree as $node) {
                $return .= '<li>' . $node->name . ' <a href="javascript:void(0);" class="add_child_category" data-category_id="' . $node->id . '">[' . lang('items_add_child_category') . ']</a> ' .
                    '<a href="javascript:void(0);" class="edit_category" data-name = "' . H($node->name) . '" data-parent_id = "' . $node->parent_id . '" data-category_id="' . $node->id . '">[' . lang('common_edit') . ']</a> ' .
                    '<a href="javascript:void(0);" class="delete_category" data-category_id="' . $node->id . '">[' . lang('common_delete') . ']</a> ' .
                    '&nbsp;&nbsp;&nbsp;<label for="hide_from_grid_' . $node->id . '">' . lang('items_hide_from_item_grid') . '</label> <input type="checkbox" ' . ($node->hide_from_grid ? 'checked="checked"' : '') . ' class="hide_from_grid" id="hide_from_grid_' . $node->id . '" value="1" name="hide_from_grid_' . $node->id . '" data-category_id="' . $node->id . '" /> <label for="hide_from_grid_' . $node->id . '"><span></span></label>';
                $return .= $this->_category_tree_list($node->children);
                $return .= '</li>';
            }
            $return .= '</ul>';
        }

        return $return;
    }

    function sorting()
    {
        $this->check_action_permission('search');
        $search = $this->input->post('search') ? $this->input->post('search') : "";
        $category_id = $this->input->post('category_id');
        $fields = $this->input->post('fields') ? $this->input->post('fields') : 'all';

        $per_page = $this->config->item('number_of_items_per_page') ? (int)$this->config->item('number_of_items_per_page') : 20;
        $offset = $this->input->post('offset') ? $this->input->post('offset') : 0;
        $order_col = $this->input->post('order_col') ? $this->input->post('order_col') : 'name';
        $order_dir = $this->input->post('order_dir') ? $this->input->post('order_dir') : 'asc';


        $item_search_data = array('offset' => $offset, 'order_col' => $order_col, 'order_dir' => $order_dir, 'search' => $search, 'category_id' => $category_id, 'fields' => $fields);
        $this->session->set_userdata("item_search_data", $item_search_data);
        if ($search || $category_id) {
            $config['total_rows'] = $this->Item->search_count_all($search, $category_id);
            $table_data = $this->Item->search($search, $category_id, $per_page, $this->input->post('offset') ? $this->input->post('offset') : 0, $this->input->post('order_col') ? $this->input->post('order_col') : 'name', $this->input->post('order_dir') ? $this->input->post('order_dir') : 'asc', $fields);
        } else {
            $config['total_rows'] = $this->Item->count_all();
            $table_data = $this->Item->get_all($per_page, $this->input->post('offset') ? $this->input->post('offset') : 0, $this->input->post('order_col') ? $this->input->post('order_col') : 'name', $this->input->post('order_dir') ? $this->input->post('order_dir') : 'asc');
        }
        $config['base_url'] = site_url('items/sorting');
        $config['per_page'] = $per_page;
        $this->load->library('pagination');
        $this->pagination->initialize($config);
        $data['pagination'] = $this->pagination->create_links();
        $data['manage_table'] = get_items_manage_table_data_rows($table_data, $this);
        echo json_encode(array('manage_table' => $data['manage_table'], 'pagination' => $data['pagination']));
    }


    function find_item_info()
    {
        $item_number = $this->input->post('scan_item_number');
        echo json_encode($this->Item->find_item_info($item_number));
    }

    function item_number_exists()
    {
        if ($this->Item->account_number_exists($this->input->post('item_number')))
            echo 'false';
        else
            echo 'true';

    }

    function product_id_exists()
    {
        if ($this->Item->product_id_exists($this->input->post('product_id')))
            echo 'false';
        else
            echo 'true';

    }

    function check_duplicate()
    {
        echo json_encode(array('duplicate' => $this->Item->check_duplicate($this->input->post('term'))));
    }

    function search()
    {
        $this->check_action_permission('search');
        $search = $this->input->post('search');
        $category_id = $this->input->post('category_id');
        $offset = $this->input->post('offset') ? $this->input->post('offset') : 0;
        $order_col = $this->input->post('order_col') ? $this->input->post('order_col') : 'name';
        $order_dir = $this->input->post('order_dir') ? $this->input->post('order_dir') : 'asc';
        $fields = $this->input->post('fields') ? $this->input->post('fields') : 'all';

        $item_search_data = array('offset' => $offset, 'order_col' => $order_col, 'order_dir' => $order_dir, 'search' => $search, 'category_id' => $category_id, 'fields' => $fields);
        $this->session->set_userdata("item_search_data", $item_search_data);
        $per_page = $this->config->item('number_of_items_per_page') ? (int)$this->config->item('number_of_items_per_page') : 20;
        $search_data = $this->Item->search($search, $category_id, $per_page, $this->input->post('offset') ? $this->input->post('offset') : 0, $this->input->post('order_col') ? $this->input->post('order_col') : 'name', $this->input->post('order_dir') ? $this->input->post('order_dir') : 'asc', $fields);
        $config['base_url'] = site_url('items/search');
        $config['total_rows'] = $this->Item->search_count_all($search, $category_id, 10000, $fields);
        $config['per_page'] = $per_page;

        $this->load->library('pagination');
        $this->pagination->initialize($config);
        $data['pagination'] = $this->pagination->create_links();
        $data['manage_table'] = get_items_manage_table_data_rows($search_data, $this);
        echo json_encode(array('manage_table' => $data['manage_table'], 'pagination' => $data['pagination']));
    }

    /*
    Gives search suggestions based on what is being searched for
    */
    function suggest()
    {
        //allow parallel searchs to improve performance.
        session_write_close();
        $suggestions = $this->Item->get_manage_items_search_suggestions($this->input->get('term'), 100);
        echo json_encode($suggestions);
    }

    function item_search()
    {
        //allow parallel searchs to improve performance.
        session_write_close();
        $suggestions = $this->Item->get_item_search_suggestions($this->input->get('term'), 100);
        echo json_encode($suggestions);
    }

    function get_info($item_id = -1)
    {
        echo json_encode($this->Item->get_info($item_id));
    }

    function _get_item_data($item_id)
    {
        $this->load->helper('report');

        $data = array();
        $data['controller_name'] = $this->_controller_name;

        $data['item_info'] = $this->Item->get_info($item_id);

        $data['categories'][''] = lang('common_select_category');

        $categories = $this->Category->sort_categories_and_sub_categories($this->Category->get_all_categories_and_sub_categories());
        foreach ($categories as $key => $value) {
            $name = str_repeat('&nbsp;&nbsp;', $value['depth']) . $value['name'];
            $data['categories'][$key] = $name;
        }

        $data['tags'] = implode(',', $this->Tag->get_tags_for_item($item_id));
        $data['item_tax_info'] = $this->Item_taxes->get_info($item_id);
        $data['tiers'] = $this->Tier->get_all()->result();
        $data['locations'] = array();
        $data['location_tier_prices'] = array();
        $data['additional_item_numbers'] = $this->Additional_item_numbers->get_item_numbers($item_id);

        if ($item_id != -1) {
            $data['next_item_id'] = $this->Item->get_next_id($item_id);
            $data['prev_item_id'] = $this->Item->get_prev_id($item_id);
            ;
        }

        foreach ($this->Location->get_all()->result() as $location) {
            if ($this->Employee->is_location_authenticated($location->location_id)) {
                $data['locations'][] = $location;
                $data['location_items'][$location->location_id] = $this->Item_location->get_info($item_id, $location->location_id);
                $data['location_taxes'][$location->location_id] = $this->Item_location_taxes->get_info($item_id, $location->location_id);

                foreach ($data['tiers'] as $tier) {
                    $tier_prices = $this->Item_location->get_tier_price_row($tier->id, $data['item_info']->item_id, $location->location_id);
                    if (!empty($tier_prices)) {
                        $data['location_tier_prices'][$location->location_id][$tier->id] = $tier_prices;
                    } else {
                        $data['location_tier_prices'][$location->location_id][$tier->id] = FALSE;
                    }
                }
            }

        }


        if ($item_id == -1) {
            $suppliers = array('' => lang('common_not_set'), '-1' => lang('common_none'));
        } else {
            $suppliers = array('-1' => lang('common_none'));
        }
        foreach ($this->Supplier->get_all()->result_array() as $row) {
            $suppliers[$row['person_id']] = $row['company_name'] . ' (' . $row['first_name'] . ' ' . $row['last_name'] . ')';
        }

        $data['tier_prices'] = array();
        $data['tier_type_options'] = array('unit_price' => lang('common_fixed_price'), 'percent_off' => lang('common_percent_off'));
        foreach ($data['tiers'] as $tier) {
            $tier_prices = $this->Item->get_tier_price_row($tier->id, $data['item_info']->item_id);

            if (!empty($tier_prices)) {
                $data['tier_prices'][$tier->id] = $tier_prices;
            } else {
                $data['tier_prices'][$tier->id] = FALSE;
            }
        }

        $data['suppliers'] = $suppliers;
        $data['selected_supplier'] = $this->Item->get_info($item_id)->supplier_id;

        $decimals = $this->Appconfig->get_raw_number_of_decimals();
        $decimals = $decimals !== NULL && $decimals != '' ? $decimals : 2;
        $data['decimals'] = $decimals;

        return $data;
    }

    function view($item_id = -1, $redirect = 0, $sale_or_receiving = 'sale')
    {
        $this->load->model('Item_taxes');
        $this->load->model('Tier');
        $this->load->model('Item_location');
        $this->load->model('Item_location_taxes');
        $this->load->model('Supplier');
        $this->load->model('Item_taxes_finder');

        $this->check_action_permission('add_update');
        $this->load->helper('report');

        $data = $this->_get_item_data($item_id);
        $data['redirect'] = $redirect;
        $data['sale_or_receiving'] = $sale_or_receiving;

        $this->load->view("items/form", $data);
    }

    function clone_item($item_id)
    {
        $this->load->model('Item_taxes');
        $this->load->model('Tier');
        $this->load->model('Item_location');
        $this->load->model('Item_location_taxes');
        $this->load->model('Supplier');
        $this->load->model('Item_taxes_finder');

        $this->check_action_permission('add_update');
        $this->load->helper('report');
        $data = $this->_get_item_data($item_id);
        $data['redirect'] = 2;
        //Unset unique identifiers
        $data['item_info']->item_number = '';
        $data['item_info']->product_id = '';
        $data['additional_item_numbers'] = array();
        $data['is_clone'] = TRUE;
        $this->load->view("items/form", $data);
    }

    function inventory($item_id = -1, $offset = 0)
    {
        $this->load->model('Item_location');

        $this->check_action_permission('edit_quantity');
        $data['item_info'] = $this->Item->get_info($item_id);
        $data['item_location_info'] = $this->Item_location->get_info($item_id);


        $config['base_url'] = site_url('items/inventory/' . $item_id);
        $config['per_page'] = $this->config->item('number_of_items_per_page') ? (int)$this->config->item('number_of_items_per_page') : 20;
        $config['total_rows'] = $this->Inventory->count_all($item_id);
        $config['uri_segment'] = 4;
        $this->load->library('pagination');
        $this->pagination->initialize($config);
        $data['pagination'] = $this->pagination->create_links();
        $data['inventory_data'] = $this->Inventory->get_inventory_data_for_item($item_id, $config['per_page'], $offset)->result_array();

        $this->load->view("items/inventory", $data);
    }

    function generate_barcodes($item_ids)
    {
        $this->load->model('Item_taxes');
        $this->load->model('Item_location');
        $this->load->model('Item_location_taxes');
        $this->load->model('Item_taxes_finder');

        $this->load->helper('items');
        $data['items'] = get_items_barcode_data($item_ids);
        $data['scale'] = 1;
        $this->load->view("barcode_sheet", $data);
    }

    function generate_barcode_labels($item_ids)
    {
        $this->load->model('Item_taxes');
        $this->load->model('Item_location');
        $this->load->model('Item_location_taxes');
        $this->load->model('Item_taxes_finder');

        $this->load->helper('items');
        $data['items'] = get_items_barcode_data($item_ids);
        $data['scale'] = 1;
        $this->load->view("barcode_labels", $data);
    }

    function generate_barcodes_from_recv($recv_id)
    {
        $this->load->model('Item_taxes');
        $this->load->model('Item_location');
        $this->load->model('Item_location_taxes');
        $this->load->model('Item_taxes_finder');
        $this->load->model('Receiving');
        $item_ids = array();

        foreach ($this->Receiving->get_receiving_items($recv_id)->result() as $item) {
            for ($k = 0; $k < abs((int)$item->quantity_purchased); $k++) {
                $item_ids[] = $item->item_id;
            }
        }

        $data = array();
        $this->load->helper('items');
        $data['items'] = get_items_barcode_data(implode('~', $item_ids));
        $data['scale'] = 1;
        $data['from_recv'] = $recv_id;
        $this->load->view("barcode_sheet", $data);
    }


    function generate_barcodes_labels_from_recv($recv_id)
    {
        $this->load->model('Item_taxes');
        $this->load->model('Item_location');
        $this->load->model('Item_location_taxes');
        $this->load->model('Item_taxes_finder');
        $this->load->model('Receiving');

        $item_ids = array();

        foreach ($this->Receiving->get_receiving_items($recv_id)->result() as $item) {
            for ($k = 0; $k < abs((int)$item->quantity_purchased); $k++) {
                $item_ids[] = $item->item_id;
            }
        }

        $data = array();
        $this->load->helper('items');
        $data['items'] = get_items_barcode_data(implode('~', $item_ids));
        $data['scale'] = 1;
        $data['from_recv'] = $recv_id;
        $this->load->view("barcode_labels", $data);
    }


    function bulk_edit()
    {
        $this->load->model('Supplier');
        $this->load->model('Tier');

        $this->check_action_permission('add_update');
        $this->load->helper('report');
        $data = array();

        $suppliers = array('' => lang('common_do_nothing'), '-1' => lang('common_none'));
        foreach ($this->Supplier->get_all()->result_array() as $row) {
            $suppliers[$row['person_id']] = $row['company_name'] . ' (' . $row['first_name'] . ' ' . $row['last_name'] . ')';
        }
        $data['suppliers'] = $suppliers;

        $data['categories'][''] = lang('common_do_nothing');

        $categories = $this->Category->sort_categories_and_sub_categories($this->Category->get_all_categories_and_sub_categories());
        foreach ($categories as $key => $value) {
            $name = str_repeat('&nbsp;&nbsp;', $value['depth']) . $value['name'];
            $data['categories'][$key] = $name;
        }

        $data['item_cost_price_choices'] = array(
            '' => lang('common_do_nothing'),
            'fixed' => lang('common_fixed_price'),
            'percent' => lang('items_increase_decrease_percent'),
        );


        $data['change_cost_price_during_sale_choices'] = array(
            '' => lang('common_do_nothing'),
            '0' => lang('common_no'),
            '1' => lang('common_yes'));

        $data['item_unit_price_choices'] = array(
            '' => lang('common_do_nothing'),
            'fixed' => lang('common_fixed_price'),
            'percent' => lang('items_increase_decrease_percent'),
        );


        $data['item_promo_price_choices'] = array(
            '' => lang('common_do_nothing'),
            'fixed' => lang('common_fixed_price'),
            'percent' => lang('items_increase_decrease_percent'),
        );

        $data['override_default_commission_choices'] = array(
            '' => lang('common_do_nothing'),
            '0' => lang('common_no'),
            '1' => lang('common_yes'));

        $data['override_default_tax_choices'] = array(
            '' => lang('common_do_nothing'),
            '0' => lang('common_no'),
            '1' => lang('common_yes'));

        $data['allow_alt_desciption_choices'] = array(
            '' => lang('common_do_nothing'),
            1 => lang('items_change_all_to_allow_alt_desc'),
            0 => lang('items_change_all_to_not_allow_allow_desc'));


        $data['serialization_choices'] = array(
            '' => lang('common_do_nothing'),
            1 => lang('items_change_all_to_serialized'),
            0 => lang('items_change_all_to_unserialized'));

        $data['tax_included_choices'] = array(
            '' => lang('common_do_nothing'),
            '0' => lang('common_no'),
            '1' => lang('common_yes'));

        $data['is_service_choices'] = array(
            '' => lang('common_do_nothing'),
            '0' => lang('common_no'),
            '1' => lang('common_yes'));


        $this->load->view("items/form_bulk", $data);
    }

    function save($item_id = -1)
    {
        $this->load->model('Item_taxes');
        $this->load->model('Item_location');
        $this->load->model('Item_location_taxes');

        $this->check_action_permission('add_update');

        if (!$this->Category->exists($this->input->post('category_id'))) {
            if (!$category_id = $this->Category->get_category_id($this->input->post('category_id'))) {
                $category_id = $this->Category->save($this->input->post('category_id'));
            }
        } else {
            $category_id = $this->input->post('category_id');
        }

        $item_data = array(
            'name' => $this->input->post('name'),
            'description' => $this->input->post('description'),
            'tax_included' => $this->input->post('tax_included') ? $this->input->post('tax_included') : 0,
            'category_id' => $category_id,
            'size' => $this->input->post('size'),
            'expire_days' => $this->input->post('expire_days') ? $this->input->post('expire_days') : NULL,
            'supplier_id' => $this->input->post('supplier_id') == -1 || $this->input->post('supplier_id') == '' ? null : $this->input->post('supplier_id'),
            'item_number' => $this->input->post('item_number') == '' ? null : $this->input->post('item_number'),
            'product_id' => $this->input->post('product_id') == '' ? null : $this->input->post('product_id'),
            'cost_price' => $this->input->post('cost_price'),
            'change_cost_price' => $this->input->post('change_cost_price') ? $this->input->post('change_cost_price') : 0,
            'unit_price' => $this->input->post('unit_price'),
            'promo_price' => $this->input->post('promo_price') ? $this->input->post('promo_price') : NULL,
            'start_date' => $this->input->post('start_date') ? date('Y-m-d', strtotime($this->input->post('start_date'))) : NULL,
            'end_date' => $this->input->post('end_date') ? date('Y-m-d', strtotime($this->input->post('end_date'))) : NULL,
            'reorder_level' => $this->input->post('reorder_level') != '' ? $this->input->post('reorder_level') : NULL,
            'is_service' => $this->input->post('is_service') ? $this->input->post('is_service') : 0,
            'allow_alt_description' => $this->input->post('allow_alt_description') ? $this->input->post('allow_alt_description') : 0,
            'is_serialized' => $this->input->post('is_serialized') ? $this->input->post('is_serialized') : 0,
            'override_default_tax' => $this->input->post('override_default_tax') ? $this->input->post('override_default_tax') : 0,
        );

        if ($this->input->post('override_default_commission')) {
            if ($this->input->post('commission_type') == 'fixed') {
                $item_data['commission_fixed'] = (float)$this->input->post('commission_value');
                $item_data['commission_percent_type'] = '';
                $item_data['commission_percent'] = NULL;
            } else {
                $item_data['commission_percent'] = (float)$this->input->post('commission_value');
                $item_data['commission_percent_type'] = $this->input->post('commission_percent_type');
                $item_data['commission_fixed'] = NULL;
            }
        } else {
            $item_data['commission_percent'] = NULL;
            $item_data['commission_fixed'] = NULL;
            $item_data['commission_percent_type'] = '';
        }

        $employee_id = $this->Employee->get_logged_in_employee_info()->person_id;
        $cur_item_info = $this->Item->get_info($item_id);

        $redirect = $this->input->post('redirect');
        $sale_or_receiving = $this->input->post('sale_or_receiving');

        if ($this->Item->save($item_data, $item_id)) {
            $this->Tag->save_tags_for_item(isset($item_data['item_id']) ? $item_data['item_id'] : $item_id, $this->input->post('tags'));
            $tier_type = $this->input->post('tier_type');

            if ($this->input->post('item_tier')) {
                foreach ($this->input->post('item_tier') as $tier_id => $price_or_percent) {
                    if ($price_or_percent) {
                        $tier_data = array('tier_id' => $tier_id);
                        $tier_data['item_id'] = isset($item_data['item_id']) ? $item_data['item_id'] : $item_id;

                        if ($tier_type[$tier_id] == 'unit_price') {
                            $tier_data['unit_price'] = $price_or_percent;
                            $tier_data['percent_off'] = NULL;
                        } else {
                            $tier_data['percent_off'] = (float)$price_or_percent;
                            $tier_data['unit_price'] = NULL;
                        }

                        $this->Item->save_item_tiers($tier_data, $item_id);
                    } else {
                        $this->Item->delete_tier_price($tier_id, $item_id);
                    }

                }
            }


            $success_message = '';

            //New item
            if ($item_id == -1) {
                $success_message = lang('common_successful_adding') . ' ' . $item_data['name'];
                $this->session->set_flashdata('manage_success_message', $success_message);
                echo json_encode(array('success' => true, 'message' => $success_message, 'item_id' => $item_data['item_id'], 'redirect' => $redirect, 'sale_or_receiving' => $sale_or_receiving));
                $item_id = $item_data['item_id'];
            } else //previous item
            {
                $success_message = lang('common_items_successful_updating') . ' ' . $item_data['name'];
                $this->session->set_flashdata('manage_success_message', $success_message);
                echo json_encode(array('success' => true, 'message' => $success_message, 'item_id' => $item_id, 'redirect' => $redirect, 'sale_or_receiving' => $sale_or_receiving));
            }

            if ($this->input->post('additional_item_numbers') && is_array($this->input->post('additional_item_numbers'))) {
                $this->Additional_item_numbers->save($item_id, $this->input->post('additional_item_numbers'));
            } else {
                $this->Additional_item_numbers->delete($item_id);
            }

            if ($this->input->post('locations')) {
                foreach ($this->input->post('locations') as $location_id => $item_location_data) {
                    $override_prices = isset($item_location_data['override_prices']) && $item_location_data['override_prices'];
                    $quantity_add_minus = isset($item_location_data['quantity_add_minus']) && $item_location_data['quantity_add_minus'] ? $item_location_data['quantity_add_minus'] : 0;

                    $item_location_before_save = $this->Item_location->get_info($item_id, $location_id);
                    $new_quantity = ($item_location_before_save->quantity ? $item_location_before_save->quantity : 0) + $quantity_add_minus;

                    $data = array(
                        'location_id' => $location_id,
                        'item_id' => $item_id,
                        'location' => $item_location_data['location'],
                        'cost_price' => $override_prices && $item_location_data['cost_price'] != '' ? $item_location_data['cost_price'] : NULL,
                        'unit_price' => $override_prices && $item_location_data['unit_price'] != '' ? $item_location_data['unit_price'] : NULL,
                        'promo_price' => $override_prices && $item_location_data['promo_price'] != '' ? $item_location_data['promo_price'] : NULL,
                        'start_date' => $override_prices && $item_location_data['promo_price'] != '' && $item_location_data['start_date'] != '' ? date('Y-m-d', strtotime($item_location_data['start_date'])) : NULL,
                        'end_date' => $override_prices && $item_location_data['promo_price'] != '' && $item_location_data['end_date'] != '' ? date('Y-m-d', strtotime($item_location_data['end_date'])) : NULL,
                        'quantity' => !$this->input->post('is_service') ? $new_quantity : NULL,
                        'reorder_level' => isset($item_location_data['reorder_level']) && $item_location_data['reorder_level'] != '' ? $item_location_data['reorder_level'] : NULL,
                        'override_default_tax' => isset($item_location_data['override_default_tax']) && $item_location_data['override_default_tax'] != '' ? $item_location_data['override_default_tax'] : 0,
                    );
                    $this->Item_location->save($data, $item_id, $location_id);


                    if (isset($item_location_data['item_tier'])) {
                        $tier_type = $item_location_data['tier_type'];

                        foreach ($item_location_data['item_tier'] as $tier_id => $price_or_percent) {
                            //If we are overriding prices and we have a price/percent, add..otherwise delete
                            if ($override_prices && $price_or_percent) {
                                $tier_data = array('tier_id' => $tier_id);
                                $tier_data['item_id'] = isset($item_data['item_id']) ? $item_data['item_id'] : $item_id;
                                $tier_data['location_id'] = $location_id;

                                if ($tier_type[$tier_id] == 'unit_price') {
                                    $tier_data['unit_price'] = $price_or_percent;
                                    $tier_data['percent_off'] = NULL;
                                } else {
                                    $tier_data['percent_off'] = (float)$price_or_percent;
                                    $tier_data['unit_price'] = NULL;
                                }

                                $this->Item_location->save_item_tiers($tier_data, $item_id, $location_id);
                            } else {
                                $this->Item_location->delete_tier_price($tier_id, $item_id, $location_id);
                            }

                        }
                    }


                    if (isset($item_location_data['tax_names'])) {
                        $location_items_taxes_data = array();
                        $tax_names = $item_location_data['tax_names'];
                        $tax_percents = $item_location_data['tax_percents'];
                        $tax_cumulatives = $item_location_data['tax_cumulatives'];
                        for ($k = 0; $k < count($tax_percents); $k++) {
                            if (is_numeric($tax_percents[$k])) {
                                $location_items_taxes_data[] = array('name' => $tax_names[$k], 'percent' => $tax_percents[$k], 'cumulative' => isset($tax_cumulatives[$k]) ? $tax_cumulatives[$k] : '0');
                            }
                        }
                        $this->Item_location_taxes->save($location_items_taxes_data, $item_id, $location_id);
                    }

                    if (isset($item_location_data['quantity_add_minus']) && $item_location_data['quantity_add_minus'] && !$this->input->post('is_service')) {
                        $inv_data = array
                        (
                            'trans_date' => date('Y-m-d H:i:s'),
                            'trans_items' => $item_id,
                            'trans_user' => $employee_id,
                            'trans_comment' => lang('items_manually_editing_of_quantity'),
                            'trans_inventory' => $item_location_data['quantity_add_minus'],
                            'location_id' => $location_id,
                        );
                        $this->Inventory->insert($inv_data);
                    }
                }
            }
            $items_taxes_data = array();
            $tax_names = $this->input->post('tax_names');
            $tax_percents = $this->input->post('tax_percents');
            $tax_cumulatives = $this->input->post('tax_cumulatives');
            for ($k = 0; $k < count($tax_percents); $k++) {
                if (is_numeric($tax_percents[$k])) {
                    $items_taxes_data[] = array('name' => $tax_names[$k], 'percent' => $tax_percents[$k], 'cumulative' => isset($tax_cumulatives[$k]) ? $tax_cumulatives[$k] : '0');
                }
            }
            $this->Item_taxes->save($items_taxes_data, $item_id);


            //Delete Image
            if ($this->input->post('del_image') && $item_id != -1) {
                if ($cur_item_info->image_id != null) {
                    $this->load->model('Appfile');
                    $this->Item->update_image(NULL, $item_id);
                    $this->Appfile->delete($cur_item_info->image_id);
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

                $this->Item->update_image($image_file_id, $item_id);
            }
        } else //failure
        {
            echo json_encode(array('success' => false, 'message' => lang('common_error_adding_updating') . ' ' .
                $item_data['name'], 'item_id' => -1));
        }

    }

    function save_inventory($item_id = -1)
    {
        $this->load->model('Item_location');

        $this->check_action_permission('add_update');
        $employee_id = $this->Employee->get_logged_in_employee_info()->person_id;
        $cur_item_info = $this->Item->get_info($item_id);
        $cur_item_location_info = $this->Item_location->get_info($item_id);

        $inv_data = array
        (
            'trans_date' => date('Y-m-d H:i:s'),
            'trans_items' => $item_id,
            'trans_user' => $employee_id,
            'trans_comment' => $this->input->post('trans_comment'),
            'trans_inventory' => $this->input->post('newquantity'),
            'location_id' => $this->Employee->get_logged_in_employee_current_location_id()
        );
        $this->Inventory->insert($inv_data);

        //Update stock quantity
        if ($this->Item_location->save_quantity($cur_item_location_info->quantity + $this->input->post('newquantity'), $item_id)) {
            echo json_encode(array('success' => true, 'message' => lang('common_items_successful_updating') . ' ' .
                $cur_item_info->name, 'item_id' => $item_id));
        } else //failure
        {
            echo json_encode(array('success' => false, 'message' => lang('common_error_adding_updating') . ' ' .
                $cur_item_info->name, 'item_id' => -1));
        }

    }

    function clear_state()
    {
        $this->session->unset_userdata('item_search_data');
        redirect('items');
    }

    function bulk_update()
    {
        $this->load->model('Item_location');
        $this->load->model('Item_taxes');

        $cost_price_percent = FALSE;
        $unit_price_percent = FALSE;
        $promo_price_percent = FALSE;

        $this->db->trans_start();

        $this->check_action_permission('add_update');
        $items_to_update = $this->input->post('item_ids');
        $select_inventory = $this->get_select_inventory();

        //clears the total inventory selection
        $this->clear_select_inventory();

        $item_data = array();


        foreach ($_POST as $key => $value) {
            if ($key == 'submit' || $key == 'tags' || $key == 'tier_types' || $key == 'tier_values') {
                continue;
            }

            //This field is nullable, so treat it differently
            if ($key == 'supplier_id') {
                if ($value != '') {
                    $item_data["$key"] = $value == '-1' ? null : $value;
                }
            } elseif ($value != '' && ($key == 'start_date' || $key == 'end_date')) {
                $item_data["$key"] = date('Y-m-d', strtotime($value));
            } elseif ($value != '' && $key == 'quantity') {
                $this->Item_location->update_multiple(array('quantity' => $value), $items_to_update, $select_inventory);
            } elseif ($value != '' && $key == 'item_cost_price_method' && $this->input->post('cost_price')) {
                if ($value == 'fixed') {
                    $item_data["cost_price"] = $this->input->post('cost_price');
                } elseif ($value == 'percent') {
                    $cost_price_percent = (float)$this->input->post('cost_price');
                }
            } elseif ($value != '' && $key == 'item_unit_price_method' && $this->input->post('unit_price')) {
                if ($value == 'fixed') {
                    $item_data["unit_price"] = $this->input->post('unit_price');
                } elseif ($value == 'percent') {
                    $unit_price_percent = (float)$this->input->post('unit_price');
                }
            } elseif ($value != '' && $key == 'item_promo_price_method' && $this->input->post('promo_price')) {
                if ($value == 'fixed') {
                    $item_data["promo_price"] = $this->input->post('promo_price');
                } elseif ($value == 'percent') {
                    $promo_price_percent = (float)$this->input->post('promo_price');
                }
            } elseif ($value != '' and !(in_array($key, array('cost_price', 'unit_price', 'promo_price', 'item_cost_price_method', 'item_unit_price_method', 'item_promo_price_method', 'item_ids', 'tax_names', 'tax_percents', 'tax_cumulatives', 'select_inventory', 'commission_value', 'commission_type', 'commission_percent_type', 'override_default_commission')))) {
                $item_data["$key"] = $value;
            }
        }

        //If we have any of the percents to update then we will update them (one or more)
        if ($cost_price_percent || $unit_price_percent || $promo_price_percent) {
            $this->Item->update_multiple_percent($items_to_update, $select_inventory, $cost_price_percent, $unit_price_percent, $promo_price_percent);
        }

        $this->Item->update_tiers($items_to_update, $select_inventory, $this->input->post('tier_types'), $this->input->post('tier_values'));

        if ($this->input->post('override_default_commission') != '') {
            if ($this->input->post('override_default_commission') == 1) {
                if ($this->input->post('commission_type') == 'fixed') {
                    $item_data['commission_fixed'] = (float)$this->input->post('commission_value');
                    $item_data['commission_percent_type'] = '';
                    $item_data['commission_percent'] = NULL;
                } else {
                    $item_data['commission_percent'] = (float)$this->input->post('commission_value');
                    $item_data['commission_percent_type'] = $this->input->post('commission_percent_type');
                    $item_data['commission_fixed'] = NULL;
                }
            } else {
                $item_data['commission_percent'] = NULL;
                $item_data['commission_fixed'] = NULL;
                $item_data['commission_percent_type'] = '';

            }
        }

        //Item data could be empty if tax information is being updated
        if (empty($item_data) || $this->Item->update_multiple($item_data, $items_to_update, $select_inventory)) {
            //Only update tax data of we are override taxes
            if (isset($item_data['override_default_tax']) && $item_data['override_default_tax']) {
                $items_taxes_data = array();
                $tax_names = $this->input->post('tax_names');
                $tax_percents = $this->input->post('tax_percents');
                $tax_cumulatives = $this->input->post('tax_cumulatives');

                for ($k = 0; $k < count($tax_percents); $k++) {
                    if (is_numeric($tax_percents[$k])) {
                        $items_taxes_data[] = array('name' => $tax_names[$k], 'percent' => $tax_percents[$k], 'cumulative' => isset($tax_cumulatives[$k]) ? $tax_cumulatives[$k] : '0');
                    }
                }

                if (!empty($items_taxes_data)) {
                    $this->Item_taxes->save_multiple($items_taxes_data, $items_to_update, $select_inventory);
                }
            }

            //Update all items with tags
            if ($this->input->post('tags')) {
                if ($select_inventory == 0) {
                    foreach ($items_to_update as $item_id) {
                        $this->Tag->save_tags_for_item($item_id, $this->input->post('tags'));
                    }
                } else {
                    $params = $this->session->userdata('item_search_data') ? $this->session->userdata('item_search_data') : array('offset' => 0, 'order_col' => 'name', 'order_dir' => 'asc', 'search' => FALSE, 'category_id' => FALSE, 'fields' => 'all');
                    $total_items = $this->Item->count_all();
                    $result = $this->Item->search(isset($params['search']) ? $params['search'] : '', isset($params['category_id']) ? $params['category_id'] : '', $total_items, 0, 'name', 'asc', isset($params['fields']) ? $params['fields'] : 'all');

                    foreach ($result->result() as $item) {
                        $this->Tag->save_tags_for_item($item->item_id, $this->input->post('tags'));
                    }
                }
            }
            echo json_encode(array('success' => true, 'message' => lang('items_successful_bulk_edit')));
        } else {
            echo json_encode(array('success' => false, 'message' => lang('items_error_updating_multiple')));
        }

        $this->db->trans_complete();
    }

    function delete()
    {
        $this->check_action_permission('delete');
        $items_to_delete = $this->input->post('ids');
        $select_inventory = $this->get_select_inventory();
        $params = $this->session->userdata('item_search_data') ? $this->session->userdata('item_search_data') : array('offset' => 0, 'order_col' => 'name', 'order_dir' => 'asc', 'search' => FALSE, 'category_id' => FALSE, 'fields' => 'all');
        $total_rows = $select_inventory ? $this->Item->search_count_all(isset($params['search']) ? $params['search'] : '', isset($params['category_id']) ? $params['category_id'] : '', $this->Item->count_all(), isset($params['fields']) ? $params['fields'] : 'all') : count($items_to_delete);
        //clears the total inventory selection
        $this->clear_select_inventory();
        if ($this->Item->delete_list($items_to_delete, $select_inventory)) {
            echo json_encode(array('success' => true, 'message' => lang('items_successful_deleted') . ' ' .
                $total_rows . ' ' . lang('items_one_or_multiple')));
        } else {
            echo json_encode(array('success' => false, 'message' => lang('items_cannot_be_deleted')));
        }
    }

    function _excel_get_header_row()
    {
        $this->load->model('Tier');

        $header_row = array();

        $header_row[] = lang('common_item_number');
        $header_row[] = lang('common_product_id');
        $header_row[] = lang('common_item_name');
        $header_row[] = lang('common_category');
        $header_row[] = lang('common_supplier_id');
        $header_row[] = lang('common_cost_price');
        $header_row[] = lang('common_unit_price');
        $header_row[] = lang('items_promo_price');
        $header_row[] = lang('items_promo_start_date');
        $header_row[] = lang('items_promo_end_date');

        foreach ($this->Tier->get_all()->result() as $tier) {
            $header_row[] = $tier->name;
        }

        $header_row[] = lang('items_price_includes_tax');
        $header_row[] = lang('items_is_service');
        $header_row[] = lang('items_quantity');
        $header_row[] = lang('items_reorder_level');
        $header_row[] = lang('common_description');
        $header_row[] = lang('items_allow_alt_desciption');
        $header_row[] = lang('items_is_serialized');
        $header_row[] = lang('common_size');
        $header_row[] = lang('reports_commission');
        $header_row[] = lang('items_commission_percent_based_on_profit');
        $header_row[] = lang('items_non_taxable');
        $header_row[] = lang('common_tags');
        $header_row[] = lang('items_days_to_expiration');
        $header_row[] = lang('common_change_cost_price_during_sale');
        return $header_row;
    }

    function excel()
    {
        $this->load->helper('report');
        $header_row = $this->_excel_get_header_row();
        $this->load->helper('spreadsheet');
        $content = array_to_spreadsheet(array($header_row));
        $this->load->helper('download');
        force_download('items_import.' . ($this->config->item('spreadsheet_format') == 'XLSX' ? 'xlsx' : 'csv'), $content);
    }

    function excel_export()
    {
        $this->load->model('Tier');

        set_time_limit(0);

        $data = $this->Item->get_all($this->Item->count_all())->result_object();
        $non_taxable_item_ids = $this->Item->get_non_taxable_item_ids();
        $tier_prices = $this->Item->get_all_tiers_prices();
        $this->load->helper('report');

        $header_row = $this->_excel_get_header_row();
        $header_row[] = lang('common_item_id');
        $rows[] = $header_row;

        $tiers = $this->Tier->get_all()->result();
        $categories = $this->Category->get_all_categories_and_sub_categories_as_indexed_by_category_id();

        foreach ($data as $r) {
            $row = array();
            $row[] = $r->item_number;
            $row[] = $r->product_id;
            $row[] = $r->name;
            $row[] = isset($categories[$r->category_id]) ? $categories[$r->category_id] : '';
            $row[] = $r->supplier_id;
            $row[] = to_currency_no_money($r->cost_price, 10);
            $row[] = to_currency_no_money($r->unit_price);

            $row[] = $r->promo_price ? to_currency_no_money($r->promo_price) : '';
            $row[] = $r->start_date ? date(get_date_format(), strtotime($r->start_date)) : '';
            $row[] = $r->end_date ? date(get_date_format(), strtotime($r->end_date)) : '';

            foreach ($tiers as $tier) {
                $tier_id = $tier->id;
                $value = '';

                if (isset($tier_prices[$r->item_id][$tier->id])) {
                    $value = $tier_prices[$r->item_id][$tier->id]['unit_price'] !== NULL ? to_currency_no_money($tier_prices[$r->item_id][$tier->id]['unit_price']) : $tier_prices[$r->item_id][$tier->id]['percent_off'] . '%';
                }

                $row[] = $value;
            }


            $row[] = $r->tax_included ? 'y' : '';
            $row[] = $r->is_service ? 'y' : '';
            $row[] = to_quantity($r->quantity, FALSE);
            $row[] = to_quantity($r->reorder_level, fALSE);
            $row[] = $r->description;
            $row[] = $r->allow_alt_description ? 'y' : '';
            $row[] = $r->is_serialized ? 'y' : '';
            $row[] = $r->size;
            $commission = '';

            if ($r->commission_fixed) {
                $commission = to_currency_no_money($r->commission_fixed);
            } elseif ($r->commission_percent) {
                $commission = to_currency_no_money($r->commission_percent) . '%';
            }

            $row[] = $commission;
            $row[] = $r->commission_percent_type == 'profit' ? 'y' : '';
            $row[] = isset($non_taxable_item_ids[$r->item_id]) && $non_taxable_item_ids[$r->item_id] === TRUE ? 'y' : '';
            $row[] = $r->tags;
            $row[] = $r->expire_days ? $r->expire_days : '';
            $row[] = $r->change_cost_price ? 'y' : '';
            $row[] = $r->item_id;

            $rows[] = $row;
        }
        $this->load->helper('spreadsheet');
        $content = array_to_spreadsheet($rows);
        $this->load->helper('download');
        force_download('items_export.' . ($this->config->item('spreadsheet_format') == 'XLSX' ? 'xlsx' : 'csv'), $content);
        exit;
    }

    function excel_import()
    {
        $this->check_action_permission('add_update');
        $this->load->view("items/excel_import", null);
    }

    function cleanup()
    {
        $this->Item->cleanup();
        echo json_encode(array('success' => true, 'message' => lang('items_cleanup_sucessful')));
    }


    function select_inventory()
    {
        $this->session->set_userdata('select_inventory', 1);
    }

    function get_select_inventory()
    {
        return $this->session->userdata('select_inventory') ? $this->session->userdata('select_inventory') : 0;
    }

    function clear_select_inventory()
    {
        $this->session->unset_userdata('select_inventory');

    }

    function tags()
    {
        //allow parallel searchs to improve performance.
        session_write_close();
        $suggestions = $this->Tag->get_tag_suggestions($this->input->get('term'), 25);
        echo json_encode($suggestions);
    }

    function count($status = 'open', $offset = 0)
    {
        $this->check_action_permission('count_inventory');
        $data = array();
        $config = array();
        $config['base_url'] = site_url("items/count/$status");
        $config['per_page'] = $this->config->item('number_of_items_per_page') ? (int)$this->config->item('number_of_items_per_page') : 20;
        $config['total_rows'] = $this->Inventory->get_count_by_status($status);
        $config['uri_segment'] = 4;
        $data['per_page'] = $config['per_page'];


        $data['total_rows'] = $config['total_rows'];
        $this->load->library('pagination');
        $this->pagination->initialize($config);
        $data['pagination'] = $this->pagination->create_links();

        $counts = $this->Inventory->get_counts_by_status($status, $config['per_page'], $offset)->result_array();

        $data['counts'] = $counts;
        $data['status'] = $status;
        $this->load->view('items/count', $data);
    }

    function new_count()
    {
        $this->check_action_permission('count_inventory');
        $count_id = $this->Inventory->create_count();
        redirect('items/do_count/' . $count_id);
    }

    function do_count($count_id, $offset = 0)
    {
        $this->check_action_permission('count_inventory');
        $this->session->set_userdata('current_count_id', $count_id);

        $data = array();
        $config = array();
        $config['base_url'] = site_url("items/do_count/$count_id");
        $config['per_page'] = $this->config->item('number_of_items_per_page') ? (int)$this->config->item('number_of_items_per_page') : 20;
        $config['total_rows'] = $this->Inventory->get_number_of_items_counted($count_id);
        $config['uri_segment'] = 4;
        $data['per_page'] = $config['per_page'];


        $data['total_rows'] = $config['total_rows'];
        $this->load->library('pagination');
        $this->pagination->initialize($config);
        $data['pagination'] = $this->pagination->create_links();
        $data['count_info'] = $this->Inventory->get_count_info($count_id);

        $data['items_counted'] = $this->Inventory->get_items_counted($count_id, $config['per_page'], $offset);
        $data['mode'] = $this->session->userdata('count_mode') ? $this->session->userdata('count_mode') : 'scan_and_set';
        $data['modes'] = array('scan_and_set' => lang('items_scan_and_set'), 'scan_and_add' => lang('items_scan_and_add'));

        $this->load->view('items/do_count', $data);
    }

    function add_item_to_inventory_count()
    {
        $this->check_action_permission('count_inventory');
        $this->load->model('Item_location');

        $item = $this->input->post('item');
        $count_id = $this->session->userdata('current_count_id');
        $mode = $this->session->userdata('count_mode') ? $this->session->userdata('count_mode') : 'scan_and_set';

        if ($item && $count_id) {
            if (!$this->Item->exists(does_contain_only_digits($item) ? (int)$item : -1)) {
                //try to get item id given an item_number
                $item = $this->Item->get_item_id($item);
            }

            if ($item) {
                $current_count = $this->Inventory->get_count_item_current_quantity($count_id, $item);
                $actual_quantity = $this->Inventory->get_count_item_actual_quantity($count_id, $item);

                if ($actual_quantity !== NULL) {
                    $current_inventory_value = $actual_quantity;
                } else {
                    $current_inventory_value = $this->Item_location->get_location_quantity($item);
                }

                if ($mode == 'scan_and_add') {
                    $this->Inventory->set_count_item($count_id, $item, $current_count + 1, $current_inventory_value);
                } else {
                    $this->Inventory->set_count_item($count_id, $item, $current_count, $current_inventory_value);
                }
            }
        }

        $this->_reload_inventory_counts();
    }

    function edit_count()
    {
        $this->check_action_permission('count_inventory');
        $name = $this->input->post('name');
        $count_id = $this->input->post('pk');
        $$name = $this->input->post('value');

        $this->Inventory->set_count($count_id, isset($status) ? $status : FALSE, isset($comment) ? $comment : FALSE);
    }

    function excel_import_count()
    {
        $this->check_action_permission('count_inventory');
        $this->load->view("items/excel_import_count", null);
    }

    function _excel_get_header_row_count()
    {
        return array(lang('common_item_id') . '/' . lang('common_item_number') . '/' . lang('common_product_id'), lang('items_count'));
    }

    function excel_count()
    {
        $this->load->helper('report');
        $header_row = $this->_excel_get_header_row_count();
        $this->load->helper('spreadsheet');
        $content = array_to_spreadsheet(array($header_row));
        $this->load->helper('download');
        force_download('items_count.' . ($this->config->item('spreadsheet_format') == 'XLSX' ? 'xlsx' : 'csv'), $content);
    }


    function do_excel_import_count()
    {
        $this->check_action_permission('count_inventory');
        $this->load->model('Item_location');

        $count_id = $this->session->userdata('current_count_id');
        $this->load->helper('demo');
        if (is_on_demo_host()) {
            $msg = lang('common_excel_import_disabled_on_demo');
            echo json_encode(array('success' => false, 'message' => $msg));
            return;
        }

        $file_info = pathinfo($_FILES['file_path']['name']);
        if ($file_info['extension'] != 'xlsx' && $file_info['extension'] != 'csv') {
            echo json_encode(array('success' => false, 'message' => lang('common_upload_file_not_supported_format')));
            return;
        }


        set_time_limit(0);
        $this->db->trans_start();
        $msg = 'do_excel_import';

        $category_map = array();
        $failCodes = array();
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
                    $item_id = $sheet->getCellByColumnAndRow(0, $k)->getValue();
                    if (!$item_id) {
                        continue;
                    }

                    $quantity = $sheet->getCellByColumnAndRow(1, $k)->getValue();
                    if (!$quantity) {
                        continue;
                    }

                    if ($item_id && $quantity) {
                        if (!$this->Item->exists(does_contain_only_digits($item_id) ? (int)$item_id : -1)) {
                            //try to get item id given an item_number
                            $item_id = $this->Item->get_item_id($item_id);
                        }

                        if ($item_id) {
                            $current_inventory_value = $this->Item_location->get_location_quantity($item_id);
                            $this->Inventory->set_count_item($count_id, $item_id, $quantity, $current_inventory_value);
                        }
                    }

                }

                $this->db->trans_complete();
                echo json_encode(array('success' => true, 'message' => lang('items_import_successful')));

            } else {
                echo json_encode(array('success' => false, 'message' => lang('common_upload_file_not_supported_format')));
                return;
            }
        }
    }

    function count_import_success()
    {
        $count_id = $this->session->userdata('current_count_id');
        redirect('items/do_count/' . $count_id);
    }

    function finish_count($update_inventory = 0)
    {
        $this->check_action_permission('count_inventory');

        $count_id = $this->session->userdata('current_count_id');

        if ($update_inventory && $this->Employee->has_module_action_permission('items', 'edit_quantity', $this->Employee->get_logged_in_employee_info()->person_id)) {
            $this->Inventory->update_inventory_from_count($count_id);
        }

        $this->Inventory->set_count($count_id, 'closed');
        redirect('items/count');
    }

    function edit_count_item()
    {
        $this->check_action_permission('count_inventory');

        $name = $this->input->post('name');
        $item_id = $this->input->post('pk');
        $$name = $this->input->post('value');
        $count_id = $this->session->userdata('current_count_id');

        $current_count = $this->Inventory->get_count_item_current_quantity($count_id, $item_id);
        $actual_quantity = $this->Inventory->get_count_item_actual_quantity($count_id, $item_id);

        if ($actual_quantity !== NULL) {
            $current_inventory_value = $actual_quantity;
        } else {
            $current_inventory_value = $this->Item_location->get_location_quantity($item_id);
        }

        $this->Inventory->set_count_item($count_id, $item_id, isset($quantity) ? $quantity : $current_count, $current_inventory_value, isset($comment) ? $comment : FALSE);
        $this->_reload_inventory_counts();
    }

    function delete_inventory_count_item($item_id)
    {
        $this->check_action_permission('count_inventory');

        $count_id = $this->session->userdata('current_count_id');
        $this->Inventory->delete_count_item($count_id, $item_id);
        redirect('items/do_count/' . $count_id);
    }

    function delete_inventory_count($count_id, $go_back_to_status = 'open')
    {
        $this->check_action_permission('count_inventory');

        $this->Inventory->delete_inventory_count($count_id);
        redirect("items/count/$go_back_to_status");
    }

    function reload_inventory_counts()
    {
        $this->check_action_permission('count_inventory');

        $this->_reload_inventory_counts();
    }

    function change_count_mode()
    {
        $this->check_action_permission('count_inventory');

        $this->session->set_userdata('count_mode', $this->input->post('mode'));

        $this->_reload_inventory_counts();
    }

    function _reload_inventory_counts($data = array())
    {
        $this->check_action_permission('count_inventory');

        $count_id = $this->session->userdata('current_count_id');
        $config = array();

        $config['base_url'] = site_url("items/do_count/$count_id");
        $config['per_page'] = $this->config->item('number_of_items_per_page') ? (int)$this->config->item('number_of_items_per_page') : 20;
        $config['total_rows'] = $this->Inventory->get_number_of_items_counted($count_id);
        $config['uri_segment'] = 4;
        $data['per_page'] = $config['per_page'];
        $data['count_info'] = $this->Inventory->get_count_info($count_id);

        $data['total_rows'] = $config['total_rows'];
        $this->load->library('pagination');
        $this->pagination->initialize($config);
        $data['pagination'] = $this->pagination->create_links();

        $data['items_counted'] = $this->Inventory->get_items_counted($count_id, $config['per_page']);

        $data['mode'] = $this->session->userdata('count_mode') ? $this->session->userdata('count_mode') : 'scan_and_set';
        $data['modes'] = array('scan_and_set' => lang('items_scan_and_set'), 'scan_and_add' => lang('items_scan_and_add'));

        $this->load->view("items/do_count_data", $data);
    }

    /*
    function do_excel_import()
    {
        $this->load->helper('demo');
        $this->load->model('Tier');
        $this->load->model('Item_taxes');
        $this->load->model('Item_location');
        $this->load->model('Supplier');

        if (is_on_demo_host()) {
            $msg = lang('common_excel_import_disabled_on_demo');
            echo json_encode(array('success' => false, 'message' => $msg));
            return;
        }

        $file_info = pathinfo($_FILES['file_path']['name']);
        if ($file_info['extension'] != 'xlsx' && $file_info['extension'] != 'csv') {
            echo json_encode(array('success' => false, 'message' => lang('common_upload_file_not_supported_format')));
            return;
        }


        set_time_limit(0);
        $this->check_action_permission('add_update');
        $this->db->trans_start();
        $msg = 'do_excel_import';

        $categories_indexed_by_name = $this->Category->get_all_categories_and_sub_categories_as_indexed_by_name_key();

        $category_map = array();
        $failCodes = array();
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
                $price_tiers_count = $this->Tier->count_all();
                $tiers = $this->Tier->get_all()->result();
                //Loop through rows, skip header row
                for ($k = 2; $k <= $num_rows; $k++) {
                    $name = $sheet->getCellByColumnAndRow(2, $k)->getValue();
                    if (!$name) {
                        $name = '';
                    }

                    $description = $sheet->getCellByColumnAndRow(14 + $price_tiers_count, $k)->getValue();

                    if (!$description) {
                        $description = '';
                    }

                    $category = $sheet->getCellByColumnAndRow(3, $k)->getValue();

                    if (!$category) {
                        $category_id = NULL;
                    } else {
                        $category_id = NULL;

                        if (!isset($categories_indexed_by_name[strtoupper($category)])) {
                            $this->Category->create_categories_as_needed($category, $categories_indexed_by_name);
                        }

                        $category_id = $categories_indexed_by_name[strtoupper($category)];

                    }

                    $cost_price = $sheet->getCellByColumnAndRow(5, $k)->getValue();

                    if ($cost_price == NULL) {
                        $cost_price = 0;
                    }

                    $unit_price = $sheet->getCellByColumnAndRow(6, $k)->getValue();

                    if ($unit_price == NULL) {
                        $unit_price = 0;
                    }

                    $tax_included = $sheet->getCellByColumnAndRow(10 + $price_tiers_count, $k)->getValue();
                    $tax_included = ($tax_included != null && $tax_included != '' and $tax_included != '0' and strtolower($tax_included) != 'n') ? '1' : '0';

                    $is_service = $sheet->getCellByColumnAndRow(11 + $price_tiers_count, $k)->getValue();
                    $is_service = ($is_service != null && $is_service != '' and $is_service != '0' and strtolower($is_service) != 'n') ? '1' : '0';

                    $quantity = $sheet->getCellByColumnAndRow(12 + $price_tiers_count, $k)->getValue();
                    $reorder_level = $sheet->getCellByColumnAndRow(13 + $price_tiers_count, $k)->getValue();

                    $supplier_id = $sheet->getCellByColumnAndRow(4, $k)->getValue();

                    if ($supplier_id) {
                        $supplier_name_before_searching = $supplier_id;
                        $supplier_id = $this->Supplier->exists($supplier_id) ? $supplier_id : $this->Supplier->find_supplier_id($supplier_id);

                        if (!$supplier_id) {
                            $person_data = array('first_name' => '', 'last_name' => '');
                            $supplier_data = array('company_name' => $supplier_name_before_searching);
                            $this->Supplier->save_supplier($person_data, $supplier_data);
                            $supplier_id = $supplier_data['person_id'];
                        }
                    }

                    $allow_alt_description = $sheet->getCellByColumnAndRow(15 + $price_tiers_count, $k)->getValue();
                    $allow_alt_description = ($allow_alt_description != null && $allow_alt_description != '' and $allow_alt_description != '0' and strtolower($allow_alt_description) != 'n') ? '1' : '0';

                    $is_serialized = $sheet->getCellByColumnAndRow(16 + $price_tiers_count, $k)->getValue();
                    $is_serialized = ($is_serialized != null && $is_serialized != '' and $is_serialized != '0' and strtolower($is_serialized) != 'n') ? '1' : '0';

                    $non_taxable = $sheet->getCellByColumnAndRow(20 + $price_tiers_count, $k)->getValue();
                    $non_taxable = ($non_taxable != null && $non_taxable != '' and $non_taxable != '0' and strtolower($non_taxable) != 'n') ? '1' : '0';

                    $size = $sheet->getCellByColumnAndRow(17 + $price_tiers_count, $k)->getValue();
                    if (!$size) {
                        $size = '';
                    }

                    $item_number = $sheet->getCellByColumnAndRow(0, $k)->getValue();
                    $product_id = $sheet->getCellByColumnAndRow(1, $k)->getValue();
                    $item_id = $sheet->getCellByColumnAndRow(24 + $price_tiers_count, $k)->getValue();

                    if (!$item_id) {
                        $item_id = FALSE;
                    }

                    //If we don't have a name or unit price skip the import
                    if (!$name || !$unit_price) {
                        continue;
                    }

                    $item_data = array(
                        'name' => $name,
                        'description' => $description,
                        'category_id' => $category_id,
                        'cost_price' => $cost_price,
                        'unit_price' => $unit_price,
                        'tax_included' => $tax_included,
                        'is_service' => $is_service,
                        'reorder_level' => $reorder_level,
                        'supplier_id' => $supplier_id,
                        'allow_alt_description' => $allow_alt_description,
                        'is_serialized' => $is_serialized,
                        'size' => $size,
                    );

                    if ($item_number != "") {
                        $item_data['item_number'] = $item_number;
                    } else {
                        $item_data['item_number'] = NULL;
                    }

                    if ($product_id != "") {
                        $item_data['product_id'] = $product_id;
                    } else {
                        $item_data['product_id'] = NULL;
                    }

                    if ($non_taxable) {
                        $item_data['override_default_tax'] = 1;
                    } else {
                        $item_data['override_default_tax'] = 0;
                    }

                    $commission = $sheet->getCellByColumnAndRow(18 + $price_tiers_count, $k)->getValue();

                    $commission_percent_based_on_profit = $sheet->getCellByColumnAndRow(19 + $price_tiers_count, $k)->getValue();
                    $commission_percent_type = ($commission_percent_based_on_profit != null && $commission_percent_based_on_profit != '' and $commission_percent_based_on_profit != '0' and strtolower($commission_percent_based_on_profit) != 'n') ? 'profit' : 'selling_price';

                    if ($commission != '') {
                        if (strpos($commission, '%') === FALSE) {
                            $item_data['commission_fixed'] = (float)$commission;
                            $item_data['commission_percent'] = NULL;
                            $item_data['commission_percent_type'] = '';

                        } else {
                            $item_data['commission_percent'] = (float)$commission;
                            $item_data['commission_fixed'] = NULL;
                            $item_data['commission_percent_type'] = $commission_percent_type;

                        }
                    } else {
                        $item_data['commission_percent'] = NULL;
                        $item_data['commission_fixed'] = NULL;
                        $item_data['commission_percent_type'] = '';
                    }

                    $promo_price = $sheet->getCellByColumnAndRow(7, $k)->getValue();
                    $start_date = date('Y-m-d', strtotime($sheet->getCellByColumnAndRow(8, $k)->getValue()));
                    $end_date = date('Y-m-d', strtotime($sheet->getCellByColumnAndRow(9, $k)->getValue()));

                    if ($promo_price == NULL) {
                        $promo_price = NULL;
                        $start_date = NULL;
                        $end_date = NULL;
                    }

                    $item_data['promo_price'] = $promo_price;
                    $item_data['start_date'] = $start_date;
                    $item_data['end_date'] = $end_date;

                    $expire_days = $sheet->getCellByColumnAndRow(22 + $price_tiers_count, $k)->getValue();

                    if ($expire_days == NULL) {
                        $expire_days = NULL;
                    }

                    $item_data['expire_days'] = $expire_days;


                    $change_cost_price = $sheet->getCellByColumnAndRow(23 + $price_tiers_count, $k)->getValue();

                    $change_cost_price = ($change_cost_price != null && $change_cost_price != '' and $change_cost_price != '0' and strtolower($change_cost_price) != 'n') ? '1' : '0';


                    if ($change_cost_price) {
                        $item_data['change_cost_price'] = 1;
                    } else {
                        $item_data['change_cost_price'] = 0;
                    }


                    if ($this->Item->save($item_data, $item_id)) {
                        $promo_end_date_col_index = 9;
                        $counter = 0;
                        //Save price tiers
                        foreach ($tiers as $tier) {
                            $tier_id = $tier->id;

                            $tier_data = array('tier_id' => $tier_id);
                            $tier_data['item_id'] = isset($item_data['item_id']) ? $item_data['item_id'] : $item_id;
                            $tier_value = $sheet->getCellByColumnAndRow($promo_end_date_col_index + ($counter + 1), $k)->getValue();

                            if ($tier_value) {
                                if (strpos($tier_value, '%') === FALSE) {
                                    $tier_data['unit_price'] = $tier_value;
                                    $tier_data['percent_off'] = NULL;
                                } else {
                                    $tier_data['percent_off'] = (float)$tier_value;
                                    $tier_data['unit_price'] = NULL;
                                }

                                $this->Item->save_item_tiers($tier_data, isset($item_data['item_id']) ? $item_data['item_id'] : $item_id);
                            } else {
                                $this->Item->delete_tier_price($tier_id, isset($item_data['item_id']) ? $item_data['item_id'] : $item_id);
                            }

                            $counter++;
                        }


                        $tags = $sheet->getCellByColumnAndRow(21 + $price_tiers_count, $k)->getValue();

                        if ($tags) {
                            $this->Tag->save_tags_for_item(isset($item_data['item_id']) ? $item_data['item_id'] : $item_id, $tags);
                        }

                        $item_location_before_save = $this->Item_location->get_info($item_id, $this->Employee->get_logged_in_employee_current_location_id());


                        if ($quantity !== NULL) {
                            $this->Item_location->save_quantity($quantity != null ? $quantity : NULL, isset($item_data['item_id']) ? $item_data['item_id'] : $item_id);

                            $employee_id = $this->Employee->get_logged_in_employee_info()->person_id;
                            $emp_info = $this->Employee->get_info($employee_id);
                            $comment = lang('items_csv_import');

                            //Only log inventory if quantity changes
                            if (!$item_data['is_service'] && $quantity != $item_location_before_save->quantity) {
                                $inv_data = array
                                (
                                    'trans_date' => date('Y-m-d H:i:s'),
                                    'trans_items' => isset($item_data['item_id']) ? $item_data['item_id'] : $item_id,
                                    'trans_user' => $employee_id,
                                    'trans_comment' => $comment,
                                    'trans_inventory' => $quantity - $item_location_before_save->quantity,
                                    'location_id' => $this->Employee->get_logged_in_employee_current_location_id()
                                );
                                $this->Inventory->insert($inv_data);
                            }
                        }

                        if ($non_taxable) {
                            $this->Item_taxes->delete(isset($item_data['item_id']) ? $item_data['item_id'] : $item_id);
                        }
                    } else //insert or update item failure
                    {
                        echo json_encode(array('success' => false, 'message' => lang('items_duplicate_item_ids')));
                        return;
                    }
                }
                //LOOP DONE (for items)
            } else {
                echo json_encode(array('success' => false, 'message' => lang('common_upload_file_not_supported_format')));
                return;
            }
        }

        $this->db->trans_complete();
        echo json_encode(array('success' => true, 'message' => lang('items_import_successful')));
    }
    */

    public function do_excel_import() {
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
                $data['attribute_sets'] = $this->Attribute_set->get_by_related_object('items');
                $data['sheet'] = $objPHPExcel->getActiveSheet();
                $data['num_rows'] = $objPHPExcel->setActiveSheetIndex(0)->getHighestRow();
                $data['columns'] = range('A', $end_column);
                $data['fields'] = $this->Item->get_import_fields();
                $html = $this->load->view('items/import/result', $data, true);
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
        $entity_type = 'items';
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
        if (empty($rows) || empty($selected_rows)) {
            $msg = lang('common_error');
            echo json_encode(array('success' => false, 'message' => $msg));
            return;
        }
        $stored_rows = 0;
        $error_rows = array();
        foreach ($rows as $index => $row) {
            if (!isset($selected_rows[$index])) {
                continue;
            }
            $data = array('attribute_set_id' => $attribute_set_id);
            $extend_data = $extend_rows = array();
            foreach ($columns as $excel_column => $field_column) {
                if (!empty($field_column) && !empty($row[$excel_column])) {
                    $field_parts = explode(':', $field_column);

                    /* Set Basic Attributes */
                    if (count($field_parts) == 2) {
                        switch ($field_parts[0]) {
                            case 'basic':
                                $data[$field_parts[1]] = $row[$excel_column];
                                break;
                            case 'extend':
                                $extend_data = array(
                                    'entity_type' => $entity_type,
                                    'attribute_id' => $field_parts[1],
                                    'entity_value' => $row[$excel_column],
                                );
                                $extend_rows[] = $extend_data;
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
                        case 'basic':
                            if (!empty($data[$check_duplicate_field_name])) {
                                $exists_row = $this->Item->exists_by_field($entity_type, $check_duplicate_field_name, $data[$check_duplicate_field_name]);
                            }
                            break;
                        case 'extend':
                            if (!empty($extend_data['entity_value'])) {
                                $exists_row = $this->Attribute->exists_by_value($entity_type, $extend_data['attribute_id'], $extend_data['entity_value']);
                            }
                            break;
                        default:
                            if (!empty($data[$check_duplicate_field_name])) {
                                $exists_row = $this->Item->exists_by_field($entity_type, $check_duplicate_field_name, $data[$check_duplicate_field_name]);
                            }
                            break;
                    }
                }
                // Test
                if (!$exists_row) {
                    if (empty($data['unit_price']) || !is_numeric($data['unit_price'])) {
                        $data['unit_price'] = 0;    
                    }
                    if (empty($data['cost_price']) || !is_numeric($data['cost_price'])) {
                        $data['cost_price'] = 0;    
                    }
                    $item_id = $this->Item->save($data);
                    if (!empty($item_id)) {
                        $stored_rows++;
                        /* Set extended attributes */
                        if (!empty($extend_rows)) {
                            foreach ($extend_rows as $extend_data) {
                                $extend_data['entity_id'] = $item_id;
                                $this->Attribute->set_attributes($extend_data);
                            }
                        }
                    }
                } else {
                    $error_rows[] = $row;
                }
            } catch (Exception $ex) {
                $error_rows[] = $row;
                continue;
            }
        }
        $error_html = '';
        if (!empty($error_rows)) {
            $error_html = $this->load->view('import/error/rows', array('num_rows' => count($error_rows), 'rows' => $error_rows, 'columns' => $columns), true);
        }
        if (!empty($stored_rows)) {
            $msg = $stored_rows . ' ' . lang('common_record_stored');
            echo json_encode(array('success' => true, 'message' => $msg, 'error_html' => $error_html));
            return;
        }
        $msg = $stored_rows . ' ' . lang('common_record_stored');
        echo json_encode(array('success' => false, 'message' => $msg, 'error_html' => $error_html));
    }

}

?>
