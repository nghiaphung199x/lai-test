<?php

class Attribute extends CI_Model
{

    /*
        Defines types
    */
    const YES = 1;
    const NO = 0;
    const ATTRIBUTE_TYPE_TEXT = 1;
    const ATTRIBUTE_TYPE_NUMBER = 2;
    const ATTRIBUTE_TYPE_TEXTAREA = 3;
    const ATTRIBUTE_TYPE_SELECT = 4;
    const ATTRIBUTE_TYPE_CHECKBOX = 5;
    const ATTRIBUTE_TYPE_RADIO = 6;
    const ATTRIBUTE_TYPE_EDITOR = 7;
    const ATTRIBUTE_TYPE_FILE = 8;

    /*
        Get all types
    */
    public function get_types() {
        $attribute_types = array();
        $attribute_types[self::ATTRIBUTE_TYPE_TEXT] = lang('attributes_type_text');
        $attribute_types[self::ATTRIBUTE_TYPE_NUMBER] = lang('attributes_type_number');
        $attribute_types[self::ATTRIBUTE_TYPE_TEXTAREA] = lang('attributes_type_textarea');
        $attribute_types[self::ATTRIBUTE_TYPE_SELECT] = lang('attributes_type_select');
        $attribute_types[self::ATTRIBUTE_TYPE_CHECKBOX] = lang('attributes_type_checkbox');
        $attribute_types[self::ATTRIBUTE_TYPE_RADIO] = lang('attributes_type_radio');
        $attribute_types[self::ATTRIBUTE_TYPE_EDITOR] = lang('attributes_type_editor');
        $attribute_types[self::ATTRIBUTE_TYPE_FILE] = lang('attributes_type_file');
        return $attribute_types;
    }

    /*
        Determines if a given id is an attribute
    */
    function exists($id)
    {
        $this->db->from('attributes');
        $this->db->where('attributes.id', $id);
        $query = $this->db->get();

        return ($query->num_rows() == 1);
    }

    function attribute_name_exists($name)
    {
        $this->db->from('attributes');
        $this->db->where('attributes.name', $name);
        $query = $this->db->get();
        if ($query->num_rows() == 1) {
            return $query->row()->name;
        }
    }

    /*
        Returns all the attributes
    */
    function get_all($limit = 10000, $offset = 0, $col = 'sort_order', $order = 'ASC')
    {
        $order_by = "ORDER BY " . $col . " " . $order;
        if (!$this->config->item('speed_up_search_queries')) {
            $order_by = "ORDER BY " . $col . " " . $order;
        }

        $attributes = $this->db->dbprefix('attributes');
        $data = $this->db->query("SELECT *
						FROM " . $attributes . "
						WHERE deleted = 0 $order_by
						LIMIT  " . $offset . "," . $limit);
        return $data;
    }

    function count_all()
    {
        $this->db->from('attributes');
        $this->db->where('deleted', 0);
        return $this->db->count_all_results();
    }

    /*
        Gets information about a particular attribute
    */
    function get_info($id, $can_cache = TRUE)
    {
        if ($can_cache) {
            static $cache = array();
            if (isset($cache[$id])) {
                return $cache[$id];
            }
        } else {
            $cache = array();
        }
        $this->db->from('attributes');
        $this->db->where('attributes.id', $id);
        $query = $this->db->get();

        if ($query->num_rows() == 1) {
            $cache[$id] = $query->row();
            /* Extract all options */
            if (!empty($cache[$id]->options)) {
                $cache[$id]->options = @unserialize($cache[$id]->options);
            }
            return $cache[$id];
        } else {
            /* Get empty base parent object */
            $attribute_obj = new stdClass();

            /* Get all the fields from attribute table */
            $fields = $this->db->list_fields('attributes');

            /* Append those fields to base parent object, we have a complete empty object */
            foreach ($fields as $field) {
                $attribute_obj->$field = '';
            }

            /* Extract all options */
            if (!empty($attribute_obj)) {
                $attribute_obj->options = @unserialize($attribute_obj->options);
            }

            return $attribute_obj;
        }
    }

    /*
        Gets information about multiple attributes
    */
    function get_multiple_info($ids)
    {
        $this->db->from('attributes');
        $this->db->where_in('attributes.id', $ids);
        $this->db->order_by('name', 'ASC');
        return $this->db->get();
    }

    /*
        Inserts or updates an attribute
    */
    function save($data, $id = false)
    {
        $success = false;
        if (isset($data['options'])) {
            foreach ($data['options'] as $key => $option) {
                if (empty($option['label']) || empty($option['value'])) {
                    unset($data['options'][$key]);
                }
            }
            $data['options'] = serialize($data['options']);
        } else {
            $data['options'] = serialize(array());
        }

        /* Insert Attribute */
        if (!$id) {
            if ($this->db->insert('attributes', $data)) {
                $id = $this->db->insert_id();
                return $id;
            }
            return $success;
        }

        /* Update Attribute */
        $this->db->where('id', $id);
        $success = $this->db->update('attributes', $data);
        return $success;
    }

    /*
        Deletes one attribute
    */
    function delete($id)
    {
        $success = false;

        //Run these queries as a transaction, we want to make sure we do all or nothing
        $this->db->trans_start();

        //Delete permissions
        if ($this->db->delete('permissions', array('id' => $id)) && $this->db->delete('permissions_actions', array('id' => $id))) {
            $this->db->where('id', $id);
            $success = $this->db->update('attributes', array('deleted' => 1));
        }
        $this->db->trans_complete();
        return $success;
    }

    /*
    Deletes a list of attributes
    */
    function delete_list($ids)
    {
        $this->db->where_in('id', $ids);
        $success = $this->db->update('attributes', array('deleted' => 1));
        return $success;
    }

    /*
	    Get search suggestions to find attributes
	*/
    function get_search_suggestions($search, $limit = 25)
    {
        if (!trim($search)) {
            return array();
        }

        $suggestions = array();

        if ($this->config->item('supports_full_text') && !$this->config->item('legacy_search_method')) {
            $this->db->select("attributes.*, MATCH (`name`, `description`) AGAINST (" . $this->db->escape(escape_full_text_boolean_search($search) . '*') . " IN BOOLEAN MODE) as rel", false);
            $this->db->from('attributes');
            $this->db->where("MATCH (`name`, `description`) AGAINST (" . $this->db->escape(escape_full_text_boolean_search($search) . '*') . " IN BOOLEAN MODE)", NULL, FALSE);
            $this->db->where('deleted', 0);
            $this->db->limit($limit);
            $this->db->order_by('rel ASC');
            $by_number = $this->db->get();

            $temp_suggestions = array();
            foreach ($by_number->result() as $row) {
                $data = array(
                    'name' => H($row->name),
                    'description' => H($row->description),
                    'avatar' => base_url() . "assets/img/item-kit.png"
                );

                $temp_suggestions[$row->id] = $data;
            }

            foreach ($temp_suggestions as $key => $value) {
                $suggestions[] = array('value' => $key, 'label' => $value['name'], 'avatar' => $value['avatar'], 'subtitle' => $value['description']);
            }
        } else {
            $this->db->from('attributes');
            $this->db->like('name', $search);
            $this->db->or_like('description', $search);
            $this->db->where('deleted', 0);
            $this->db->limit($limit);
            $by_number = $this->db->get();

            $temp_suggestions = array();
            foreach ($by_number->result() as $row) {
                $data = array(
                    'name' => H($row->name),
                    'description' => H($row->description),
                    'avatar' => base_url() . "assets/img/item-kit.png"
                );

                $temp_suggestions[$row->id] = $data;
            }

            $this->load->helper('array');
            uasort($temp_suggestions, 'sort_assoc_array_by_name');

            foreach ($temp_suggestions as $key => $value) {
                $suggestions[] = array('value' => $key, 'label' => $value['name'], 'avatar' => $value['avatar'], 'subtitle' => $value['description']);
            }

        }
        //only return $limit suggestions
        if (count($suggestions > $limit)) {
            $suggestions = array_slice($suggestions, 0, $limit);
        }
        return $suggestions;
    }

    /*
    Preform a search on attributes
    */
    function search($search, $limit = 20, $offset = 0, $column = 'name', $orderby = 'asc')
    {
        $this->db->from('attributes');
        if ($search) {
            if ($this->config->item('supports_full_text') && !$this->config->item('legacy_search_method')) {
                $this->db->where("(MATCH (name, description) AGAINST (" . $this->db->escape(escape_full_text_boolean_search($search) . '*') . " IN BOOLEAN MODE)) and " . $this->db->dbprefix('attributes') . ".deleted=0", NULL, FALSE);
            } else {
                $this->db->where("(name LIKE '%" . $this->db->escape_like_str($search) . "%' or
				description LIKE '%" . $this->db->escape_like_str($search) . "%') and deleted=0");
            }
        } else {
            $this->db->where('deleted', 0);
        }
        if (!$this->config->item('speed_up_search_queries')) {
            $this->db->order_by($column, $orderby);
        }
        $this->db->limit($limit);
        $this->db->offset($offset);
        return $this->db->get();
    }

    function search_count_all($search, $limit = 10000)
    {
        $this->db->from('attributes');
        if ($search) {
            if ($this->config->item('supports_full_text') && !$this->config->item('legacy_search_method')) {
                $this->db->where("(MATCH (name, description) AGAINST (" . $this->db->escape(escape_full_text_boolean_search($search) . '*') . " IN BOOLEAN MODE" . ")) and " . $this->db->dbprefix('attributes') . ".deleted=0", NULL, FALSE);
            } else {
                $this->db->where("(name LIKE '%" . $this->db->escape_like_str($search) . "%' or
				description LIKE '%" . $this->db->escape_like_str($search) . "%') and deleted=0");
            }
        } else {
            $this->db->where('deleted', 0);
        }
        $this->db->limit($limit);
        $result = $this->db->get();
        return $result->num_rows();
    }

    function cleanup()
    {
        $attribute_data = array('name' => null, 'description' => null);
        $this->db->where('deleted', 1);
        return $this->db->update('attributes', $attribute_data);
    }

    /*
        Gets the html table to manage groups.
    */
    function get_grid($groups, $controller)
    {
        $table='<table class="tablesorter table table-hover" id="sortable_table">';

        $headers = array('<input type="checkbox" id="select_all" /><label for="select_all"><span></span></label>',
            'ID',
            lang('common_name'),
            lang('common_description'),
            '&nbsp;',
        );

        $table.='<thead><tr>';
        $count = 0;
        foreach($headers as $header)
        {
            $count++;

            if ($count == 1)
            {
                $table.="<th class='leftmost'>$header</th>";
            }
            elseif ($count == count($headers))
            {
                $table.="<th class='rightmost'>$header</th>";
            }
            else
            {
                $table.="<th>$header</th>";
            }
        }
        $table.='</tr></thead><tbody>';
        $table.= $this->get_rows( $groups, $controller );
        $table.='</tbody></table>';
        return $table;
    }

    /*
        Gets the html data rows for the attribute.
    */
    public function get_rows($attributes, $controller)
    {
        $rows = '';

        foreach ($attributes->result() as $attribute) {
            $rows .= $this->get_row($attribute, $controller);
        }

        if ($attributes->num_rows() == 0) {
            $rows .= "<tr><td colspan='5'><p class='mt-10 col-md-12 text-center text-warning' >" . lang('attributes_no_attributes_to_display') . "</p></td></tr>";
        }

        return $rows;
    }

    public function get_row($attribute)
    {
        $CI =& get_instance();
        $controller_name = str_replace(BIZ_PREFIX, '', strtolower(get_class($CI)));

        $row = '<tr>';
        $row .= "<td width='50px'><input type='checkbox' id='attribute_$attribute->id' value='" . $attribute->id . "'/><label for='attribute_$attribute->id'><span></span></label></td>";
        $row .= '<td width="50px">' . H($attribute->id) . '</td>';
        $row .= '<td width="150px">' . H($attribute->name) . '</td>';
        $row .= '<td>' . H($attribute->description) . '</td>';
        $row .= '<td class="rightmost">' . anchor($controller_name . "/view/$attribute->id/2	", lang('common_edit'), array('class' => ' ', 'title' => lang($controller_name . '_update'))) . '</td>';

        $row .= '</tr>';

        return $row;
    }

    public function get_html($attribute) {
        $CI =& get_instance();
        $CI->load->view("attributes/renderer/" . $this->get_type_html($attribute), array('attribute' => $attribute));
    }

    public function get_type_html($attribute) {
        switch ($attribute->type) {
            case self::ATTRIBUTE_TYPE_TEXT:
                $type = 'text';
                break;
            case self::ATTRIBUTE_TYPE_NUMBER:
                $type = 'number';
                break;
            case self::ATTRIBUTE_TYPE_TEXTAREA:
                $type = 'textarea';
                break;
            case self::ATTRIBUTE_TYPE_SELECT:
                $type = 'select';
                break;
            case self::ATTRIBUTE_TYPE_CHECKBOX:
                $type = 'checkbox';
                break;
            case self::ATTRIBUTE_TYPE_RADIO:
                $type = 'radio';
                break;
            case self::ATTRIBUTE_TYPE_EDITOR:
                $type = 'editor';
                break;
            case self::ATTRIBUTE_TYPE_FILE:
                $type = 'file';
                break;
            default:
                $type = 'text';
                break;
        }
        return $type;
    }

    public function get_entity_attributes($data) {
        $this->db->select('*');
        $this->db->from('attribute_values');
        $this->db->join('attributes', 'attribute_values.attribute_id = attributes.id');
        $this->db->where('attribute_values.entity_id', $data['entity_id']);
        $this->db->where('attribute_values.entity_type', $data['entity_type']);
        $collection = $this->db->get()->result();
        $attributes = array();
        foreach ($collection as $attribute) {
            $attributes[$attribute->id] = $attribute;
        }
        return $attributes;
    }

    public function set_attributes($data) {
        if (!empty($data['entity_id'])) {
            $this->db->insert('attribute_values', $data);
        }
        return $this;
    }

    public function reset_attributes($data) {
        if (!empty($data['entity_id'])) {
            $this->db->delete('attribute_values', array('entity_id' => $data['entity_id'], 'entity_type' => $data['entity_type']));
        }
        return $this;
    }

    public function get_attribute_by_code($code) {
        $this->db->select('*');
        $this->db->from('attributes');
        $this->db->where('code', $code);
        return $this->db->get()->row();
    }

    public function get_attribute_value_by_code($data) {
        $attribute = $this->get_attribute_by_code($data['code']);
        $this->db->select('entity_value');
        $this->db->from('attribute_values');
        $this->db->where('entity_id', $data['entity_id']);
        $this->db->where('entity_type', $data['entity_type']);
        $this->db->where('attribute_id', $attribute->id);
        $row = $this->db->get()->row();
        return $row->entity_value;
    }
}

?>
