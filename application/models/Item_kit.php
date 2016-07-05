<?php

require_once 'Bizmodel.php';

class Item_kit extends Bizmodel
{
    protected $import_fields = array(
        'item_kit_id' => 'item_kit_id',
        'name' => 'name',
        'description' => 'description',
        'unit_price' => 'unit_price',
        'cost_price' => 'cost_price',
        'commission_value' => 'commission_value',
        'commission_percent' => 'commission_percent',
        'product_id' => 'product_id'
    );

    protected $export_fields = array(
        'item_kit_id' => 'item_kit_id',
        'name' => 'name',
        'description' => 'description',
        'unit_price' => 'unit_price',
        'cost_price' => 'cost_price',
        'commission_value' => 'commission_value',
        'commission_percent' => 'commission_percent',
        'product_id' => 'product_id'
    );

    /*
    Determines if a given item_id is an item kit
    */
    function exists($item_kit_id)
    {
        $this->db->from('item_kits');
        $this->db->where('item_kit_id', $item_kit_id);
        $query = $this->db->get();

        return ($query->num_rows() == 1);
    }

    /*
    Returns all the item kits
    */
    function get_all($limit = 10000, $offset = 0, $col = 'name', $ord = 'asc')
    {
        $current_location = $this->Employee->get_logged_in_employee_current_location_id();

        $this->db->select('item_kits.*, categories.name as category,
		location_item_kits.unit_price as location_unit_price,
		location_item_kits.cost_price as location_cost_price');
        $this->db->from('item_kits');
        $this->db->join('categories', 'categories.id = item_kits.category_id', 'left');
        $this->db->join('location_item_kits', 'location_item_kits.item_kit_id = item_kits.item_kit_id and location_id = ' . $current_location, 'left');
        $this->db->where('item_kits.deleted', 0);

        if (!$this->config->item('speed_up_search_queries')) {
            $this->db->order_by($col, $ord);
        }
        $this->db->limit($limit);
        $this->db->offset($offset);
        return $this->db->get();
    }

    function count_all()
    {
        $this->db->from('item_kits');
        $this->db->where('deleted', 0);
        return $this->db->count_all_results();
    }

    /*
    Gets information about a particular item kit
    */
    function get_info($item_kit_id, $can_cache = TRUE)
    {
        if ($can_cache) {
            static $cache = array();
        } else {
            $cache = array();
        }

        if (is_array($item_kit_id)) {
            $item_kits = $this->get_multiple_info($item_kit_id)->result();

            foreach ($item_kits as $item_kit) {
                $cache[$item_kit->item_kit_id] = $item_kit;
            }

            return $item_kits;
        } else {
            if (isset($cache[$item_kit_id])) {
                return $cache[$item_kit_id];
            }
        }

        //If we are NOT an int return empty item
        if (!is_numeric($item_kit_id)) {
            //Get empty base parent object, as $item_kit_id is NOT an item kit
            $item_obj = new stdClass();

            //Get all the fields from items table
            $fields = $this->db->list_fields('item_kits');

            foreach ($fields as $field) {
                $item_obj->$field = '';
            }

            return $item_obj;
        }

        //KIT #
        $pieces = explode(' ', $item_kit_id);

        if (count($pieces) == 2) {
            $item_kit_id = (int)$pieces[1];
        }

        $this->db->from('item_kits');
        $this->db->where('item_kit_id', $item_kit_id);

        $query = $this->db->get();

        if ($query->num_rows() == 1) {
            $cache[$item_kit_id] = $query->row();
            return $cache[$item_kit_id];
        } else {
            //Get empty base parent object, as $item_kit_id is NOT an item kit
            $item_obj = new stdClass();

            //Get all the fields from items table
            $fields = $this->db->list_fields('item_kits');

            foreach ($fields as $field) {
                $item_obj->$field = '';
            }

            return $item_obj;
        }
    }


    function check_duplicate($term)
    {
        $this->db->from('item_kits');
        $this->db->where('deleted', 0);
        $query = $this->db->where("name = " . $this->db->escape($term));
        $query = $this->db->get();

        if ($query->num_rows() > 0) {
            return true;
        }

    }

    /*
    Get an item_kit_id given an item kit number
    */
    function get_item_kit_id($item_kit_number)
    {
        $this->db->from('item_kits');
        $this->db->where('item_kit_number', $item_kit_number);
        $this->db->or_where('product_id', $item_kit_number);
        $query = $this->db->get();

        if ($query->num_rows() >= 1) {
            return $query->row()->item_kit_id;
        }

        return false;
    }

    /*
    Gets information about multiple item kits
    */
    function get_multiple_info($item_kit_ids)
    {
        $this->db->from('item_kits');
        if (!empty($item_kit_ids)) {
            $this->db->group_start();
            $item_kit_ids_chunk = array_chunk($item_kit_ids, 25);
            foreach ($item_kit_ids_chunk as $item_kit_ids) {
                $this->db->or_where_in('item_kit_id', $item_kit_ids);
            }
            $this->db->group_end();
        } else {
            $this->db->where('1', '2', FALSE);
        }

        $this->db->order_by("name", "asc");
        return $this->db->get();
    }


    /*
    Inserts or updates an item kit
    */
    function save(&$item_kit_data, $item_kit_id = false)
    {
        if (!$item_kit_id or !$this->exists($item_kit_id)) {
            if ($this->db->insert('item_kits', $item_kit_data)) {
                $item_kit_id = $this->db->insert_id();
                $item_kit_data['item_kit_id'] = $item_kit_id;
                return $item_kit_id;
            }
            return false;
        }

        $this->db->where('item_kit_id', $item_kit_id);
        return $this->db->update('item_kits', $item_kit_data);
    }

    /*
    Deletes one item kit
    */
    function delete($item_kit_id)
    {
        $this->reset_attributes(array('entity_id' => $item_kit_id, 'entity_type' => 'item_kits'));
        $this->db->where('item_kit_id', $item_kit_id);
        return $this->db->update('item_kits', array('deleted' => 1));
    }

    /*
    Deletes a list of item kits
    */
    function delete_list($item_kit_ids)
    {
        $this->mass_reset_attributes(array('entity_ids' => $item_kit_ids, 'entity_type' => 'item_kits'));
        $this->db->where_in('item_kit_id', $item_kit_ids);
        return $this->db->update('item_kits', array('deleted' => 1));
    }

    /*
   Get search suggestions to find kits
   */
    function get_manage_item_kits_search_suggestions($search, $limit = 25)
    {
        if (!trim($search)) {
            return array();
        }

        $suggestions = array();

        if ($this->config->item('supports_full_text') && !$this->config->item('legacy_search_method')) {
            $this->db->select("item_kits.name,item_kit_id,categories.name as category,MATCH (" . $this->db->dbprefix('item_kits') . ".name) AGAINST (" . $this->db->escape(escape_full_text_boolean_search($search) . '*') . " IN BOOLEAN MODE) as rel", false);
            $this->db->from('item_kits');
            $this->db->join('categories', 'categories.id = item_kits.category_id', 'left');
            $this->db->where("MATCH (" . $this->db->dbprefix('item_kits') . ".name) AGAINST (" . $this->db->escape(escape_full_text_boolean_search($search) . '*') . " IN BOOLEAN MODE)", NULL, FALSE);
            $this->db->where('item_kits.deleted', 0);
            $this->db->limit($limit);
            $this->db->order_by("rel DESC");
            $by_name = $this->db->get();

            $temp_suggestions = array();
            foreach ($by_name->result() as $row) {
                $data = array(
                    'name' => $row->name,
                    'subtitle' => $row->category,
                    'avatar' => base_url() . "assets/img/user.png"
                );
                $temp_suggestions[$row->item_kit_id] = $data;
            }

            foreach ($temp_suggestions as $key => $value) {
                $suggestions[] = array('value' => $key, 'label' => $value['name'], 'avatar' => $value['avatar'], 'subtitle' => $value['subtitle'] ? $value['subtitle'] : lang('common_none'));
            }

            $this->db->select("item_kit_number,categories.name as category,MATCH (item_kit_number) AGAINST (" . $this->db->escape(escape_full_text_boolean_search($search) . '*') . " IN BOOLEAN MODE) as rel", false);
            $this->db->from('item_kits');
            $this->db->join('categories', 'categories.id = item_kits.category_id', 'left');
            $this->db->where("MATCH (item_kit_number) AGAINST (" . $this->db->escape(escape_full_text_boolean_search($search) . '*') . " IN BOOLEAN MODE)", NULL, FALSE);
            $this->db->where('item_kits.deleted', 0);
            $this->db->limit($limit);
            $this->db->order_by("rel DESC");
            $by_item_kit_number = $this->db->get();
            $temp_suggestions = array();
            foreach ($by_item_kit_number->result() as $row) {
                $data = array(
                    'name' => $row->item_kit_number,
                    'subtitle' => $row->category,
                    'avatar' => base_url() . "assets/img/user.png"
                );
                $temp_suggestions[$row->item_kit_id] = $data;

            }


            foreach ($temp_suggestions as $key => $value) {
                $suggestions[] = array('value' => $key, 'label' => $value['name'], 'avatar' => $value['avatar'], 'subtitle' => $value['subtitle'] ? $value['subtitle'] : lang('common_none'));

            }

            $this->db->select("product_id,categories.name as category,MATCH (product_id) AGAINST (" . $this->db->escape(escape_full_text_boolean_search($search) . '*') . " IN BOOLEAN MODE) as rel", false);
            $this->db->from('item_kits');
            $this->db->join('categories', 'categories.id = item_kits.category_id', 'left');
            $this->db->where("MATCH (product_id) AGAINST (" . $this->db->escape(escape_full_text_boolean_search($search) . '*') . " IN BOOLEAN MODE)", NULL, FALSE);
            $this->db->where('item_kits.deleted', 0);
            $this->db->limit($limit);
            $this->db->order_by("rel DESC");
            $by_product_id = $this->db->get();
            $temp_suggestions = array();
            foreach ($by_product_id->result() as $row) {
                $data = array(
                    'name' => $row->product_id,
                    'subtitle' => $row->category,
                    'avatar' => base_url() . "assets/img/user.png"
                );
                $temp_suggestions[$row->item_kit_id] = $data;

            }


            foreach ($temp_suggestions as $key => $value) {
                $suggestions[] = array('value' => $key, 'label' => $value['name'], 'avatar' => $value['avatar'], 'subtitle' => $value['subtitle'] ? $value['subtitle'] : lang('common_none'));

            }

            $this->db->select("tags.name, MATCH (" . $this->db->dbprefix('tags') . ".name) AGAINST (" . $this->db->escape(escape_full_text_boolean_search($search) . '*') . " IN BOOLEAN MODE) as rel", false);
            $this->db->from('item_kits_tags');
            $this->db->join('tags', 'item_kits_tags.tag_id=tags.id');
            $this->db->where("MATCH (" . $this->db->dbprefix('tags') . ".name) AGAINST (" . $this->db->escape(escape_full_text_boolean_search($search) . '*') . " IN BOOLEAN MODE)", NULL, FALSE);

            $this->db->limit($limit);
            $this->db->order_by("rel DESC");

            $by_tags = $this->db->get();
            $temp_suggestions = array();

            foreach ($by_tags->result() as $row) {
                $data = array(
                    'name' => $row->name,
                    'subtitle' => '',
                    'avatar' => base_url() . "assets/img/user.png"
                );
                // $temp_suggestions[$row->item_kit_id] = $data;
                $temp_suggestions[] = $data;
            }


            foreach ($temp_suggestions as $key => $value) {
                $suggestions[] = array('value' => $key, 'label' => $value['name'], 'avatar' => $value['avatar'], 'subtitle' => $value['subtitle'] ? $value['subtitle'] : lang('common_none'));

            }

        } else {
            $this->db->select('item_kits.*, categories.name as category');
            $this->db->from('item_kits');
            $this->db->join('categories', 'categories.id = item_kits.category_id', 'left');
            $this->db->like('item_kits.name', $search);
            $this->db->where('item_kits.deleted', 0);
            $this->db->limit($limit);
            $by_name = $this->db->get();
            $temp_suggestions = array();
            foreach ($by_name->result() as $row) {
                $data = array(
                    'name' => $row->name,
                    'subtitle' => $row->category,
                    'avatar' => base_url() . "assets/img/user.png"
                );
                $temp_suggestions[$row->item_kit_id] = $data;
            }

            foreach ($temp_suggestions as $key => $value) {
                $suggestions[] = array('value' => $key, 'label' => $value['name'], 'avatar' => $value['avatar'], 'subtitle' => $value['subtitle'] ? $value['subtitle'] : lang('common_none'));
            }

            $this->db->select('item_kits.*, categories.name as category');
            $this->db->from('item_kits');
            $this->db->join('categories', 'categories.id = item_kits.category_id', 'left');
            $this->db->like('item_kit_number', $search);
            $this->db->where('item_kits.deleted', 0);
            $this->db->limit($limit);
            $by_item_kit_number = $this->db->get();
            $temp_suggestions = array();
            foreach ($by_item_kit_number->result() as $row) {
                $data = array(
                    'name' => $row->item_kit_number,
                    'subtitle' => $row->category,
                    'avatar' => base_url() . "assets/img/user.png"
                );
                $temp_suggestions[$row->item_kit_id] = $data;
            }

            foreach ($temp_suggestions as $key => $value) {
                $suggestions[] = array('value' => $key, 'label' => $value['name'], 'avatar' => $value['avatar'], 'subtitle' => $value['subtitle'] ? $value['subtitle'] : lang('common_none'));
            }

            $this->db->select('item_kits.*, categories.name as category');
            $this->db->from('item_kits');
            $this->db->join('categories', 'categories.id = item_kits.category_id', 'left');
            $this->db->like('product_id', $search);
            $this->db->where('item_kits.deleted', 0);
            $this->db->limit($limit);
            $by_product_id = $this->db->get();
            $temp_suggestions = array();
            foreach ($by_product_id->result() as $row) {
                $data = array(
                    'name' => $row->product_id,
                    'subtitle' => $row->category,
                    'avatar' => base_url() . "assets/img/user.png"
                );
                $temp_suggestions[$row->item_kit_id] = $data;
            }

            foreach ($temp_suggestions as $key => $value) {
                $suggestions[] = array('value' => $key, 'label' => $value['name'], 'avatar' => $value['avatar'], 'subtitle' => $value['subtitle'] ? $value['subtitle'] : lang('common_none'));

            }

            $this->db->from('item_kits_tags');
            $this->db->join('tags', 'item_kits_tags.tag_id=tags.id');
            $this->db->like('name', $search);
            $this->db->where('deleted', 0);
            $this->db->limit($limit);

            $by_tags = $this->db->get();
            $temp_suggestions = array();

            foreach ($by_tags->result() as $row) {
                $data = array(
                    'name' => $row->name,
                    'subtitle' => '',
                    'avatar' => base_url() . "assets/img/user.png"
                );
                $temp_suggestions[$row->item_kit_id] = $data;
            }


            foreach ($temp_suggestions as $key => $value) {
                $suggestions[] = array('value' => $key, 'label' => $value['name'], 'avatar' => $value['avatar'], 'subtitle' => $value['subtitle'] ? $value['subtitle'] : lang('common_none'));

            }
        }

        //only return $limit suggestions
        if (count($suggestions > $limit)) {
            $suggestions = array_slice($suggestions, 0, $limit);
        }
        return $suggestions;

    }

    function get_item_kit_search_suggestions_sales_recv($search, $limit = 25)
    {
        if (!trim($search)) {
            return array();
        }

        $suggestions = array();

        if ($this->config->item('supports_full_text') && !$this->config->item('legacy_search_method')) {
            $this->db->select("item_kits.*,categories.name as category,MATCH (" . $this->db->dbprefix('item_kits') . ".name) AGAINST (" . $this->db->escape(escape_full_text_boolean_search($search) . '*') . " IN BOOLEAN MODE) as rel", false);
            $this->db->from('item_kits');
            $this->db->join('categories', 'categories.id = item_kits.category_id', 'left');
            $this->db->where("MATCH (" . $this->db->dbprefix('item_kits') . ".name) AGAINST (" . $this->db->escape(escape_full_text_boolean_search($search) . '*') . " IN BOOLEAN MODE)", NULL, FALSE);
            $this->db->where('item_kits.deleted', 0);
            $this->db->limit($limit);
            $this->db->order_by("rel DESC");
            $by_name = $this->db->get();


            $temp_suggestions = array();

            foreach ($by_name->result() as $row) {
                $data = array(
                    'image' => base_url() . "assets/img/item-kit.png",
                    'category' => $row->category,
                    'item_kit_number' => $row->item_kit_number,
                );

                if ($row->category) {
                    $data['label'] = $row->name . ' (' . $row->category . ')';
                    $temp_suggestions['KIT ' . $row->item_kit_id] = $data;
                } else {
                    $data['label'] = $row->name;
                    $temp_suggestions['KIT ' . $row->item_kit_id] = $data;
                }
            }


            foreach ($temp_suggestions as $key => $value) {
                $suggestions[] = array('value' => $key, 'label' => $value['label'], 'image' => $value['image'], 'category' => $value['category'], 'item_kit_number' => $value['item_kit_number']);
            }

            $this->db->select("item_kits.*,categories.name as category, MATCH (" . $this->db->dbprefix('item_kits') . ".item_kit_number) AGAINST (" . $this->db->escape(escape_full_text_boolean_search($search) . '*') . " IN BOOLEAN MODE) as rel", false);
            $this->db->from('item_kits');
            $this->db->join('categories', 'categories.id = item_kits.category_id', 'left');
            $this->db->where("MATCH (item_kit_number) AGAINST (" . $this->db->escape(escape_full_text_boolean_search($search) . '*') . " IN BOOLEAN MODE)", NULL, FALSE);
            $this->db->where('item_kits.deleted', 0);
            $this->db->limit($limit);
            $this->db->order_by("rel DESC");
            $by_item_kit_number = $this->db->get();

            $temp_suggestions = array();

            foreach ($by_item_kit_number->result() as $row) {
                $data = array(
                    'label' => $row->item_kit_number,
                    'image' => base_url() . "assets/img/item-kit.png",
                    'category' => $row->category,
                    'item_kit_number' => $row->item_kit_number,
                );

                $temp_suggestions['KIT ' . $row->item_kit_id] = $data;
            }

            foreach ($temp_suggestions as $key => $value) {
                $suggestions[] = array('value' => $key, 'label' => $value['label'], 'image' => $value['image'], 'category' => $value['category'], 'item_kit_number' => $value['item_kit_number']);
            }

            $this->db->select("item_kits.*,categories.name as category, MATCH (" . $this->db->dbprefix('item_kits') . ".product_id) AGAINST (" . $this->db->escape(escape_full_text_boolean_search($search) . '*') . " IN BOOLEAN MODE) as rel", false);
            $this->db->from('item_kits');
            $this->db->join('categories', 'categories.id = item_kits.category_id', 'left');
            $this->db->where("MATCH (product_id) AGAINST (" . $this->db->escape(escape_full_text_boolean_search($search) . '*') . " IN BOOLEAN MODE)", NULL, FALSE);
            $this->db->where('item_kits.deleted', 0);
            $this->db->limit($limit);
            $this->db->order_by("rel DESC");

            $by_product_id = $this->db->get();

            $temp_suggestions = array();

            foreach ($by_product_id->result() as $row) {
                $data = array(
                    'label' => $row->product_id,
                    'image' => base_url() . "assets/img/item-kit.png",
                    'category' => $row->category,
                    'item_kit_number' => $row->item_kit_number,
                );

                $temp_suggestions['KIT ' . $row->item_kit_id] = $data;
            }

            foreach ($temp_suggestions as $key => $value) {
                $suggestions[] = array('value' => $key, 'label' => $value['label'], 'image' => $value['image'], 'category' => $value['category'], 'item_kit_number' => $value['item_kit_number']);
            }
        } else {
            $this->db->select("item_kits.*,categories.name as category", false);
            $this->db->from('item_kits');
            $this->db->join('categories', 'categories.id = item_kits.category_id', 'left');
            $this->db->like($this->db->dbprefix('item_kits') . '.name', $search);
            $this->db->where('item_kits.deleted', 0);
            $this->db->limit($limit);
            $by_name = $this->db->get();


            $temp_suggestions = array();

            foreach ($by_name->result() as $row) {
                $data = array(
                    'image' => base_url() . "assets/img/item-kit.png",
                    'category' => $row->category,
                    'item_kit_number' => $row->item_kit_number,
                );

                if ($row->category) {
                    $data['label'] = $row->name . ' (' . $row->category . ')';
                    $temp_suggestions['KIT ' . $row->item_kit_id] = $data;
                } else {
                    $data['label'] = $row->name;
                    $temp_suggestions['KIT ' . $row->item_kit_id] = $data;
                }
            }
            $this->load->helper('array');
            uasort($temp_suggestions, 'sort_assoc_array_by_label');

            foreach ($temp_suggestions as $key => $value) {
                $suggestions[] = array('value' => $key, 'label' => $value['label'], 'image' => $value['image'], 'category' => $value['category'], 'item_kit_number' => $value['item_kit_number']);
            }

            $this->db->select("item_kits.*,categories.name as category", false);
            $this->db->from('item_kits');
            $this->db->join('categories', 'categories.id = item_kits.category_id', 'left');
            $this->db->like($this->db->dbprefix('item_kits') . '.item_kit_number', $search);
            $this->db->where('item_kits.deleted', 0);
            $this->db->limit($limit);
            $by_item_kit_number = $this->db->get();


            $temp_suggestions = array();

            foreach ($by_item_kit_number->result() as $row) {
                $data = array(
                    'label' => $row->item_kit_number,
                    'image' => base_url() . "assets/img/item-kit.png",
                    'category' => $row->category,
                    'item_kit_number' => $row->item_kit_number,
                );

                $temp_suggestions['KIT ' . $row->item_kit_id] = $data;
            }

            uasort($temp_suggestions, 'sort_assoc_array_by_label');

            foreach ($temp_suggestions as $key => $value) {
                $suggestions[] = array('value' => $key, 'label' => $value['label'], 'image' => $value['image'], 'category' => $value['category'], 'item_kit_number' => $value['item_kit_number']);
            }

            $this->db->select("item_kits.*,categories.name as category", false);
            $this->db->from('item_kits');
            $this->db->join('categories', 'categories.id = item_kits.category_id', 'left');
            $this->db->like($this->db->dbprefix('item_kits') . '.product_id', $search);
            $this->db->where('item_kits.deleted', 0);
            $this->db->limit($limit);

            $by_product_id = $this->db->get();

            $temp_suggestions = array();

            foreach ($by_product_id->result() as $row) {
                $data = array(
                    'label' => $row->product_id,
                    'image' => base_url() . "assets/img/item-kit.png",
                    'category' => $row->category,
                    'item_kit_number' => $row->item_kit_number,
                );

                $temp_suggestions['KIT ' . $row->item_kit_id] = $data;
            }

            uasort($temp_suggestions, 'sort_assoc_array_by_label');

            foreach ($temp_suggestions as $key => $value) {
                $suggestions[] = array('value' => $key, 'label' => $value['label'], 'image' => $value['image'], 'category' => $value['category'], 'item_kit_number' => $value['item_kit_number']);
            }
        }

        for ($k = count($suggestions) - 1; $k >= 0; $k--) {
            if (!$suggestions[$k]['label']) {
                unset($suggestions[$k]);
            }
        }

        $suggestions = array_values($suggestions);

        //only return $limit suggestions
        if (count($suggestions > $limit)) {
            $suggestions = array_slice($suggestions, 0, $limit);
        }

        return $suggestions;

    }


    function search($search, $category_id = false, $limit = 20, $offset = 0, $column = 'name', $orderby = 'asc', $fields = 'all')
    {
        $current_location = $this->Employee->get_logged_in_employee_current_location_id();

        if (!$this->config->item('speed_up_search_queries')) {
            $this->db->distinct();
        }

        $this->db->select('item_kits.*,
		location_item_kits.unit_price as location_unit_price,
		location_item_kits.cost_price as location_cost_price');
        $this->db->from('item_kits');
        $this->db->join('location_item_kits', 'location_item_kits.item_kit_id = item_kits.item_kit_id and location_id = ' . $current_location, 'left');
        $this->db->join('item_kits_tags', 'item_kits_tags.item_kit_id = item_kits.item_kit_id', 'left');
        $this->db->join('tags', 'tags.id = item_kits_tags.tag_id', 'left');

        $this->db->join('categories', 'categories.id = item_kits.category_id', 'left');

        if ($fields == 'all') {
            if ($search) {
                if ($this->config->item('supports_full_text') && !$this->config->item('legacy_search_method')) {
                    $this->db->where("(MATCH (" . $this->db->dbprefix('item_kits') . ".name, item_kit_number, product_id, description) AGAINST (" . $this->db->escape(escape_full_text_boolean_search($search) . '*') . " IN BOOLEAN MODE" . ") or MATCH(" . $this->db->dbprefix('tags') . ".name) AGAINST (" . $this->db->escape(escape_full_text_boolean_search($search) . '*') . " IN BOOLEAN MODE" . "))and " . $this->db->dbprefix('item_kits') . ".deleted=0", NULL, FALSE);
                } else {
                    $this->db->where("(" . $this->db->dbprefix('item_kits') . ".name LIKE '%" . $this->db->escape_like_str($search) .
                        "%' or item_kit_number LIKE '%" . $this->db->escape_like_str($search) . "%'" .
                        "or product_id LIKE '%" . $this->db->escape_like_str($search) . "%' or
					description LIKE '%" . $this->db->escape_like_str($search) . "%') and " . $this->db->dbprefix('item_kits') . ".deleted=0");
                }
            }
        } else {
            if ($search) {
                //Exact Match fields
                if ($fields == $this->db->dbprefix('item_kits') . '.item_kit_id' || $fields == $this->db->dbprefix('item_kits') . '.cost_price'
                    || $fields == $this->db->dbprefix('item_kits') . '.unit_price' || $fields == $this->db->dbprefix('tags') . '.name'
                ) {
                    $this->db->where("$fields = " . $this->db->escape($search) . " and " . $this->db->dbprefix('item_kits') . ".deleted=0");
                } else {
                    if ($this->config->item('supports_full_text') && !$this->config->item('legacy_search_method')) {
                        //Fulltext
                        $this->db->where("MATCH($fields) AGAINST ('\"" . $this->db->escape_str(escape_full_text_boolean_search($search) . '*') . "\"' IN BOOLEAN MODE" . ") and " . $this->db->dbprefix('item_kits') . ".deleted=0");
                    } else {
                        $this->db->like($fields, $search);
                        $this->db->where($this->db->dbprefix('item_kits') . ".deleted=0");
                    }
                }
            }
        }

        if ($category_id) {
            $this->db->where('categories.id', $category_id);
        }

        if (!$this->config->item('speed_up_search_queries')) {
            $this->db->order_by($column, $orderby);
        }

        if (!$search) //If we don't have a search make sure we filter out deleted items
        {
            $this->db->where('item_kits.deleted', 0);
        }

        $this->db->limit($limit);
        $this->db->offset($offset);
        return $this->db->get();
    }


    function search_count_all($search, $limit = 10000)
    {
        if ($this->config->item('speed_up_search_queries')) {
            return $limit;
        }
        $this->db->from('item_kits');
        $this->db->join('item_kits_tags', 'item_kits_tags.item_kit_id = item_kits.item_kit_id', 'left');
        $this->db->join('tags', 'tags.id = item_kits_tags.tag_id', 'left');

        if ($search) {
            if ($this->config->item('supports_full_text') && !$this->config->item('legacy_search_method')) {
                $this->db->where("(MATCH (" . $this->db->dbprefix('item_kits') . ".name, item_kit_number, product_id, description) AGAINST (" . $this->db->escape(escape_full_text_boolean_search($search) . '*') . " IN BOOLEAN MODE" . ") or MATCH(" . $this->db->dbprefix('tags') . ".name) AGAINST (" . $this->db->escape(escape_full_text_boolean_search($search) . '*') . " IN BOOLEAN MODE" . "))and " . $this->db->dbprefix('item_kits') . ".deleted=0", NULL, FALSE);
            } else {
                $this->db->where("(" . $this->db->dbprefix('item_kits') . ".name LIKE '%" . $this->db->escape_like_str($search) .
                    "%' or item_kit_number LIKE '%" . $this->db->escape_like_str($search) . "%' or
				description LIKE '%" . $this->db->escape_like_str($search) . "%') and " . $this->db->dbprefix('item_kits') . ".deleted=0");
            }

        } else {
            $this->db->where('item_kits.deleted', 0);
        }

        $result = $this->db->get();
        return $result->num_rows();
    }

    function get_tier_price_row($tier_id, $item_kit_id)
    {
        $this->db->from('item_kits_tier_prices');
        $this->db->where('tier_id', $tier_id);
        $this->db->where('item_kit_id ', $item_kit_id);
        return $this->db->get()->row();
    }

    function delete_tier_price($tier_id, $item_kit_id)
    {

        $this->db->where('tier_id', $tier_id);
        $this->db->where('item_kit_id', $item_kit_id);
        $this->db->delete('item_kits_tier_prices');
    }

    function tier_exists($tier_id, $item_kit_id)
    {
        $this->db->from('item_kits_tier_prices');
        $this->db->where('tier_id', $tier_id);
        $this->db->where('item_kit_id', $item_kit_id);
        $query = $this->db->get();

        return ($query->num_rows() >= 1);

    }

    function save_item_tiers($tier_data, $item_kit_id)
    {
        if ($this->tier_exists($tier_data['tier_id'], $item_kit_id)) {
            $this->db->where('tier_id', $tier_data['tier_id']);
            $this->db->where('item_kit_id', $item_kit_id);

            return $this->db->update('item_kits_tier_prices', $tier_data);

        }

        return $this->db->insert('item_kits_tier_prices', $tier_data);
    }

    function cleanup()
    {
        $item_kit_data = array('item_kit_number' => null, 'product_id' => null);
        $this->db->where('deleted', 1);
        return $this->db->update('item_kits', $item_kit_data);
    }
}

?>
