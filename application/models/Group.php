<?php

class Group extends CI_Model
{
    /*
    Determines if a given group_id is an group
    */
    function exists($group_id)
    {
        $this->db->from('groups');
        $this->db->where('groups.group_id', $group_id);
        $query = $this->db->get();

        return ($query->num_rows() == 1);
    }

    function group_name_exists($name)
    {
        $this->db->from('groups');
        $this->db->where('groups.name', $name);
        $query = $this->db->get();

        if ($query->num_rows() == 1) {
            return $query->row()->username;
        }
    }

    /*
    Returns all the groups
    */
    function get_all($limit = 10000, $offset = 0, $col = 'name', $order = 'asc')
    {
        $order_by = '';
        if (!$this->config->item('speed_up_search_queries')) {
            $order_by = "ORDER BY " . $col . " " . $order;
        }

        $groups = $this->db->dbprefix('groups');
        $data = $this->db->query("SELECT *
						FROM " . $groups . "
						WHERE deleted =0 $order_by 
						LIMIT  " . $offset . "," . $limit);
        return $data;
    }

    function count_all()
    {
        $this->db->from('groups');
        $this->db->where('deleted', 0);
        return $this->db->count_all_results();
    }

    /*
    Gets information about a particular group
    */
    function get_info($group_id, $can_cache = TRUE)
    {
        if ($can_cache) {
            static $cache = array();
            if (isset($cache[$group_id])) {
                return $cache[$group_id];
            }
        } else {
            $cache = array();
        }
        $this->db->from('groups');
        $this->db->where('groups.group_id', $group_id);
        $query = $this->db->get();

        if ($query->num_rows() == 1) {
            $cache[$group_id] = $query->row();
            return $cache[$group_id];
        } else {
            /* Get empty base parent object */
            $group_obj = new stdClass();

            /* Get all the fields from group table */
            $fields = $this->db->list_fields('groups');

            /* Append those fields to base parent object, we have a complete empty object */
            foreach ($fields as $field) {
                $group_obj->$field = '';
            }

            return $group_obj;
        }
    }

    /*
    Gets information about multiple groups
    */
    function get_multiple_info($group_ids)
    {
        $this->db->from('groups');
        $this->db->where_in('groups.group_id', $group_ids);
        $this->db->order_by("name", "asc");
        return $this->db->get();
    }

    /*
    Inserts or updates an group
    */
    function save($data, $group_id = false)
    {
        if (!$group_id) {
            if ($this->db->insert('groups', $data)) {
                $data['group_id'] = $this->db->insert_id();
                return $data['group_id'];
            }
            return false;
        }
        $this->db->where('group_id', $group_id);
        return $this->db->update('groups', $data);
    }

    /*
    Deletes one group
    */
    function delete($group_id)
    {
        $success = false;

        //Run these queries as a transaction, we want to make sure we do all or nothing
        $this->db->trans_start();

        //Delete permissions
        if ($this->db->delete('permissions', array('group_id' => $group_id)) && $this->db->delete('permissions_actions', array('group_id' => $group_id))) {
            $this->db->where('group_id', $group_id);
            $success = $this->db->update('groups', array('deleted' => 1));
        }
        $this->db->trans_complete();
        return $success;
    }

    /*
    Deletes a list of groups
    */
    function delete_list($group_ids)
    {
        $this->db->where_in('group_id', $group_ids);
        $success = $this->db->update('groups', array('deleted' => 1));
        return $success;
    }

    function check_duplicate($term)
    {
        $this->db->from('groups');
        $this->db->join('people', 'groups.group_id=people.group_id');
        $this->db->where('deleted', 0);
        $query = $this->db->where("CONCAT(first_name,' ',last_name) = " . $this->db->escape($term));
        $query = $this->db->get();

        if ($query->num_rows() > 0) {
            return true;
        }
    }

    /*
	Get search suggestions to find groups
	*/
    function get_search_suggestions($search, $limit = 25)
    {
        if (!trim($search))
        {
            return array();
        }

        $suggestions = array();

        if($this->config->item('supports_full_text') && !$this->config->item('legacy_search_method'))
        {
            $this->db->select("groups.*, MATCH (`name`, `description`) AGAINST (".$this->db->escape(escape_full_text_boolean_search($search).'*')." IN BOOLEAN MODE) as rel", false);
            $this->db->from('groups');
            $this->db->where("MATCH (`name`, `description`) AGAINST (".$this->db->escape(escape_full_text_boolean_search($search).'*')." IN BOOLEAN MODE)", NULL, FALSE);
            $this->db->where('deleted', 0);
            $this->db->limit($limit);
            $this->db->order_by('rel ASC');
            $by_number = $this->db->get();

            $temp_suggestions = array();
            foreach($by_number->result() as $row)
            {
                $data = array(
                    'name' => H($row->name),
                    'description' => H($row->description),
                    'avatar' => base_url()."assets/img/user.png"
                );

                $temp_suggestions[$row->group_id] = $data;
            }

            foreach($temp_suggestions as $key => $value)
            {
                $suggestions[] = array('value'=> $key, 'label' => $value['name'], 'avatar' => $value['avatar'], 'subtitle' => $value['description']);
            }
        }
        else
        {
            $this->db->from('groups');
            $this->db->like('name', $search);
            $this->db->or_like('description', $search);
            $this->db->where('deleted', 0);
            $this->db->limit($limit);
            $by_number = $this->db->get();

            $temp_suggestions = array();
            foreach($by_number->result() as $row)
            {
                $data = array(
                    'name' => H($row->name),
                    'description' => H($row->description),
                    'avatar' => base_url()."assets/img/user.png"
                );

                $temp_suggestions[$row->group_id] = $data;
            }

            $this->load->helper('array');
            uasort($temp_suggestions, 'sort_assoc_array_by_name');

            foreach($temp_suggestions as $key => $value)
            {
                $suggestions[] = array('value'=> $key, 'label' => $value['name'],'avatar' => $value['avatar'], 'subtitle' => $value['description']);
            }

        }
        //only return $limit suggestions
        if(count($suggestions > $limit))
        {
            $suggestions = array_slice($suggestions, 0,$limit);
        }
        return $suggestions;
    }

    /*
    Preform a search on groups
    */
    function search($search, $limit = 20, $offset = 0, $column = 'last_name', $orderby = 'asc')
    {
        $this->db->from('groups');
        if ($search) {
            if ($this->config->item('supports_full_text') && !$this->config->item('legacy_search_method')) {
                $this->db->where("(MATCH (name, description) AGAINST (" . $this->db->escape(escape_full_text_boolean_search($search) . '*') . " IN BOOLEAN MODE)) and " . $this->db->dbprefix('groups') . ".deleted=0", NULL, FALSE);
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
        $this->db->from('groups');
        if ($search) {
            if ($this->config->item('supports_full_text') && !$this->config->item('legacy_search_method')) {
                $this->db->where("(MATCH (name, description) AGAINST (" . $this->db->escape(escape_full_text_boolean_search($search) . '*') . " IN BOOLEAN MODE" . ")) and " . $this->db->dbprefix('groups') . ".deleted=0", NULL, FALSE);
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

    /*
	Determins whether the employee specified employee has access the specific module.
	*/
    function has_module_permission($module_id,$person_id)
    {
        //if no module_id is null, allow access
        if($module_id==null)
        {
            return true;
        }

        static $cache;

        if (isset($cache[$module_id.'|'.$person_id]))
        {
            return $cache[$module_id.'|'.$person_id];
        }

        $query = $this->db->get_where('permissions', array('person_id' => $person_id,'module_id'=>$module_id), 1);
        $cache[$module_id.'|'.$person_id] = $query->num_rows() == 1;
        return $cache[$module_id.'|'.$person_id];
    }

    function has_module_action_permission($module_id, $action_id, $person_id)
    {
        //if no module_id is null, allow access
        if($module_id==null)
        {
            return true;
        }

        static $cache;

        if (isset($cache[$module_id.'|'.$action_id.'|'.$person_id]))
        {
            return $cache[$module_id.'|'.$action_id.'|'.$person_id];
        }


        $query = $this->db->get_where('permissions_actions', array('person_id' => $person_id,'module_id'=>$module_id,'action_id'=>$action_id), 1);
        $cache[$module_id.'|'.$action_id.'|'.$person_id] =  $query->num_rows() == 1;
        return $cache[$module_id.'|'.$action_id.'|'.$person_id];
    }

    function cleanup()
    {
        $group_data = array('username' => null);
        $this->db->where('deleted', 1);
        return $this->db->update('groups', $group_data);
    }
}

?>
