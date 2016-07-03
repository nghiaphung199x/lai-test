<?php

class Attribute_set extends CI_Model
{
    /*
        Determines if a given id is an attribute_set
    */
    function exists($id)
    {
        $this->db->from('attribute_sets');
        $this->db->where('attribute_sets.id', $id);
        $query = $this->db->get();

        return ($query->num_rows() == 1);
    }

    function attribute_set_name_exists($name)
    {
        $this->db->from('attribute_sets');
        $this->db->where('attribute_sets.name', $name);
        $query = $this->db->get();
        if ($query->num_rows() == 1) {
            return $query->row()->name;
        }
    }

    /*
        Returns all the attribute_sets
    */
    function get_all($limit = 10000, $offset = 0, $col = 'id', $order = 'DESC')
    {
        $order_by = '';
        if (!$this->config->item('speed_up_search_queries')) {
            $order_by = "ORDER BY " . $col . " " . $order;
        }

        $attribute_sets = $this->db->dbprefix('attribute_sets');
        $data = $this->db->query("SELECT *
						FROM " . $attribute_sets . "
						WHERE deleted = 0 $order_by
						LIMIT  " . $offset . "," . $limit);
        return $data;
    }

    function count_all()
    {
        $this->db->from('attribute_sets');
        $this->db->where('deleted', 0);
        return $this->db->count_all_results();
    }

    /*
        Gets information about a particular attribute_set
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
        $this->db->from('attribute_sets');
        $this->db->where('attribute_sets.id', $id);
        $query = $this->db->get();

        if ($query->num_rows() == 1) {
            $cache[$id] = $query->row();
            return $cache[$id];
        } else {
            /* Get empty base parent object */
            $attribute_set_obj = new stdClass();

            /* Get all the fields from attribute_set table */
            $fields = $this->db->list_fields('attribute_sets');

            /* Append those fields to base parent object, we have a complete empty object */
            foreach ($fields as $field) {
                $attribute_set_obj->$field = '';
            }

            return $attribute_set_obj;
        }
    }

    /*
        Gets information about multiple attribute_sets
    */
    function get_multiple_info($ids)
    {
        $this->db->from('attribute_sets');
        $this->db->where_in('attribute_sets.id', $ids);
        $this->db->order_by('name', 'ASC');
        return $this->db->get();
    }

    /*
        Inserts or updates an attribute_set
    */
    function save($data, $id = false)
    {
        $success = false;

        /* Insert Attribute_set */
        if (!$id) {
            if ($this->db->insert('attribute_sets', $data)) {
                $id = $this->db->insert_id();
                return $id;
            }
            return $success;
        }

        /* Update Attribute_set */
        $this->db->where('id', $id);
        if ($this->db->update('attribute_sets', $data)) {
            return $id;
        }
        return $success;
    }

    /*
        Deletes one attribute_set
    */
    function delete($id)
    {
        $success = false;

        //Run these queries as a transaction, we want to make sure we do all or nothing
        $this->db->trans_start();

        //Delete permissions
        if ($this->db->delete('permissions', array('id' => $id)) && $this->db->delete('permissions_actions', array('id' => $id))) {
            $this->db->where('id', $id);
            $success = $this->db->update('attribute_sets', array('deleted' => 1));
        }
        $this->db->trans_complete();
        return $success;
    }

    /*
    Deletes a list of attribute_sets
    */
    function delete_list($ids)
    {
        $this->db->where_in('id', $ids);
        $success = $this->db->update('attribute_sets', array('deleted' => 1));
        return $success;
    }

    /*
	    Get search suggestions to find attribute_sets
	*/
    function get_search_suggestions($search, $limit = 25)
    {
        if (!trim($search)) {
            return array();
        }

        $suggestions = array();

        if ($this->config->item('supports_full_text') && !$this->config->item('legacy_search_method')) {
            $this->db->select("attribute_sets.*, MATCH (`name`, `description`) AGAINST (" . $this->db->escape(escape_full_text_boolean_search($search) . '*') . " IN BOOLEAN MODE) as rel", false);
            $this->db->from('attribute_sets');
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
            $this->db->from('attribute_sets');
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
    Preform a search on attribute_sets
    */
    function search($search, $limit = 20, $offset = 0, $column = 'name', $orderby = 'asc')
    {
        $this->db->from('attribute_sets');
        if ($search) {
            if ($this->config->item('supports_full_text') && !$this->config->item('legacy_search_method')) {
                $this->db->where("(MATCH (name, description) AGAINST (" . $this->db->escape(escape_full_text_boolean_search($search) . '*') . " IN BOOLEAN MODE)) and " . $this->db->dbprefix('attribute_sets') . ".deleted=0", NULL, FALSE);
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
        $this->db->from('attribute_sets');
        if ($search) {
            if ($this->config->item('supports_full_text') && !$this->config->item('legacy_search_method')) {
                $this->db->where("(MATCH (name, description) AGAINST (" . $this->db->escape(escape_full_text_boolean_search($search) . '*') . " IN BOOLEAN MODE" . ")) and " . $this->db->dbprefix('attribute_sets') . ".deleted=0", NULL, FALSE);
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
        $attribute_set_data = array('name' => null, 'description' => null);
        $this->db->where('deleted', 1);
        return $this->db->update('attribute_sets', $attribute_set_data);
    }

    /*
        Gets the html table to manage groups.
    */
    function get_grid($groups, $controller, $headers = true)
    {
        $table='<table class="tablesorter table table-hover" id="sortable_table">';
        if ($headers) {
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
            $table.='</tr></thead>';
        }
        $table.='<tbody>';
        $table.= $this->get_rows( $groups, $controller );
        $table.='</tbody></table>';
        return $table;
    }

    /*
        Gets the html data rows for the attribute_set.
    */
    public function get_rows($attribute_sets, $controller)
    {
        $rows = '';

        foreach ($attribute_sets->result() as $attribute_set) {
            $rows .= $this->get_row($attribute_set, $controller);
        }

        if ($attribute_sets->num_rows() == 0) {
            $rows .= "<tr><td colspan='5'><p class='mt-10 col-md-12 text-center text-warning' >" . lang('attribute_sets_no_attribute_sets_to_display') . "</p></td></tr>";
        }

        return $rows;
    }

    public function get_row($attribute_set)
    {
        $CI =& get_instance();
        $controller_name = str_replace(BIZ_PREFIX, '', strtolower(get_class($CI)));

        $row = '<tr>';
        $row .= "<td width='50px'><input type='checkbox' id='attribute_set_$attribute_set->id' value='" . $attribute_set->id . "'/><label for='attribute_set_$attribute_set->id'><span></span></label></td>";
        $row .= '<td width="50px">' . H($attribute_set->id) . '</td>';
        $row .= '<td width="150px">' . H($attribute_set->name) . '</td>';
        $row .= '<td>' . H($attribute_set->description) . '</td>';
        $row .= '<td class="rightmost">' . anchor($controller_name . "/view/$attribute_set->id/2	", lang('common_edit'), array('class' => ' ', 'title' => lang($controller_name . '_update'))) . '</td>';

        $row .= '</tr>';

        return $row;
    }

    public function combine($attribute_set_id, $attribute_group_id, $attribute_id) {
        $data = array(
            'attribute_set_id' => $attribute_set_id,
            'attribute_group_id' => $attribute_group_id,
            'attribute_id' => $attribute_id
        );
        $this->db->insert('attribute_sets_combined', $data);
    }

    public function clear_combined($attribute_set_id) {
        $this->db->delete('attribute_sets_combined', array('attribute_set_id' => $attribute_set_id));
    }

    public function get_attributes($attribute_set_id) {
        $attribute_sets_combined = $this->db->dbprefix('attribute_sets_combined');
        $attributes = $this->db->dbprefix('attributes');
        return $this->db->query("SELECT * FROM " . $attribute_sets_combined . " AS ac INNER JOIN " . $attributes . " AS at ON (at.id = ac.attribute_id) WHERE attribute_set_id = " . $attribute_set_id)->result();
    }
}

?>
