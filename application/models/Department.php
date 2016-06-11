<?php

class Department extends CI_Model
{
    /*
    Determines if a given department_id is an department
    */
    function exists($department_id)
    {
        $this->db->from('departments');
        $this->db->where('departments.department_id', $department_id);
        $query = $this->db->get();

        return ($query->num_rows() == 1);
    }

    function department_name_exists($name)
    {
        $this->db->from('departments');
        $this->db->where('departments.name', $name);
        $query = $this->db->get();
        if ($query->num_rows() == 1) {
            return $query->row()->name;
        }
    }

    /*
    Returns all the departments
    */
    function get_all($limit = 10000, $offset = 0, $col = 'path', $order = 'ASC')
    {
        $order_by = '';
        if (!$this->config->item('speed_up_search_queries')) {
            $order_by = "ORDER BY " . $col . " " . $order;
        }

        $departments = $this->db->dbprefix('departments');
        $data = $this->db->query("SELECT *
						FROM " . $departments . "
						WHERE deleted = 0 $order_by
						LIMIT  " . $offset . "," . $limit);
        return $data;
    }

    function count_all()
    {
        $this->db->from('departments');
        $this->db->where('deleted', 0);
        return $this->db->count_all_results();
    }

    /*
    Gets information about a particular department
    */
    function get_info($department_id, $can_cache = TRUE)
    {
        if ($can_cache) {
            static $cache = array();
            if (isset($cache[$department_id])) {
                return $cache[$department_id];
            }
        } else {
            $cache = array();
        }
        $this->db->from('departments');
        $this->db->where('departments.department_id', $department_id);
        $query = $this->db->get();

        if ($query->num_rows() == 1) {
            $cache[$department_id] = $query->row();
            return $cache[$department_id];
        } else {
            /* Get empty base parent object */
            $department_obj = new stdClass();

            /* Get all the fields from department table */
            $fields = $this->db->list_fields('departments');

            /* Append those fields to base parent object, we have a complete empty object */
            foreach ($fields as $field) {
                $department_obj->$field = '';
            }

            return $department_obj;
        }
    }

    /*
    Gets information about multiple departments
    */
    function get_multiple_info($department_ids)
    {
        $this->db->from('departments');
        $this->db->where_in('departments.department_id', $department_ids);
        $this->db->order_by('name', 'ASC');
        return $this->db->get();
    }

    /*
    Inserts or updates an department
    */
    function save($data, $department_id = false)
    {
        $success = false;

        /* Insert Department */
        if (!$department_id) {
            if ($this->db->insert('departments', $data)) {
                $department_id = $this->db->insert_id();

                /* Update Path By Parent ID */
                $update_data = array();
                if (empty($data['parent_id'])) {
                    $update_data['path'] = '/' . $department_id . '/';
                } else {
                    $update_data['path'] = $this->get_path($data['parent_id']) . $department_id . '/';
                }
                $this->db->where('department_id', $department_id);
                $this->db->update('departments', $update_data);

                return $department_id;
            }
            return $success;
        }

        /* Update Path By Parent ID */
        if (empty($data['parent_id'])) {
            $data['path'] = '/' . $department_id . '/';
        } else {
            $data['path'] = $this->get_path($data['parent_id']) . $department_id . '/';
        }

        /* Update Department */
        $this->db->where('department_id', $department_id);
        $success = $this->db->update('departments', $data);
        return $success;
    }

    /*
    Deletes one department
    */
    function delete($department_id)
    {
        $success = false;

        //Run these queries as a transaction, we want to make sure we do all or nothing
        $this->db->trans_start();

        //Delete permissions
        if ($this->db->delete('permissions', array('department_id' => $department_id)) && $this->db->delete('permissions_actions', array('department_id' => $department_id))) {
            $this->db->where('department_id', $department_id);
            $success = $this->db->update('departments', array('deleted' => 1));
        }
        $this->db->trans_complete();
        return $success;
    }

    /*
    Deletes a list of departments
    */
    function delete_list($department_ids)
    {
        $this->db->where_in('department_id', $department_ids);
        $success = $this->db->update('departments', array('deleted' => 1));
        return $success;
    }

    /*
	Get search suggestions to find departments
	*/
    function get_search_suggestions($search, $limit = 25)
    {
        if (!trim($search)) {
            return array();
        }

        $suggestions = array();

        if ($this->config->item('supports_full_text') && !$this->config->item('legacy_search_method')) {
            $this->db->select("departments.*, MATCH (`name`, `description`) AGAINST (" . $this->db->escape(escape_full_text_boolean_search($search) . '*') . " IN BOOLEAN MODE) as rel", false);
            $this->db->from('departments');
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
                    'avatar' => base_url() . "assets/img/user.png"
                );

                $temp_suggestions[$row->department_id] = $data;
            }

            foreach ($temp_suggestions as $key => $value) {
                $suggestions[] = array('value' => $key, 'label' => $value['name'], 'avatar' => $value['avatar'], 'subtitle' => $value['description']);
            }
        } else {
            $this->db->from('departments');
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
                    'avatar' => base_url() . "assets/img/user.png"
                );

                $temp_suggestions[$row->department_id] = $data;
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
    Preform a search on departments
    */
    function search($search, $limit = 20, $offset = 0, $column = 'name', $orderby = 'asc')
    {
        $this->db->from('departments');
        if ($search) {
            if ($this->config->item('supports_full_text') && !$this->config->item('legacy_search_method')) {
                $this->db->where("(MATCH (name, description) AGAINST (" . $this->db->escape(escape_full_text_boolean_search($search) . '*') . " IN BOOLEAN MODE)) and " . $this->db->dbprefix('departments') . ".deleted=0", NULL, FALSE);
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
        $this->db->from('departments');
        if ($search) {
            if ($this->config->item('supports_full_text') && !$this->config->item('legacy_search_method')) {
                $this->db->where("(MATCH (name, description) AGAINST (" . $this->db->escape(escape_full_text_boolean_search($search) . '*') . " IN BOOLEAN MODE" . ")) and " . $this->db->dbprefix('departments') . ".deleted=0", NULL, FALSE);
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
        $department_data = array('name' => null, 'description' => null);
        $this->db->where('deleted', 1);
        return $this->db->update('departments', $department_data);
    }

    /*
     * Get Path Of Department
     * @param int $id
     * @return string
     * */
    public function get_path($id)
    {
        $model = new self();
        $model->db->reset_query();
        $model->db->select('path');
        $model->db->from('departments');
        $model->db->where(array('department_id' => $id, 'deleted' => 0));
        $row = $model->db->get()->row();
        if (isset($row->path)) {
            return $row->path;
        }
        return null;
    }

    /*
     * Get Line Level Of Department (Format Tree View)
     * @param mixed $department
     * @return string
     * */
    public function get_level_line($department, $char = '&nbsp;', $start_item = false, $multiplication = true)
    {
        $line = '';
        if (empty($department->path)) {
            return $line;
        }
        if ($department->path[0] != '/') {
            $department->path = '/' . $department->path;
        }
        $department->level = count(explode('/', $department->path)) - 2;
        if (!$start_item) {
            if ($department->level == 1) {
                return $line;
            }
        }
        for ($i = 0; $i < $department->level; $i++) {
            if ($multiplication) {
                for ($t = 0; $t < 1; $t++) {
                    $char .= $char;
                }
            }
            $line .= $char;
        }
        return $line;
    }

    public function get_employees($department_ids, $limit = 10000, $offset = 0, $col = 'last_name', $order = 'asc')
    {
        $order_by = '';
        if (!$this->config->item('speed_up_search_queries')) {
            $order_by = "ORDER BY " . $col . " " . $order;
        }
        $people = $this->db->dbprefix('people');
        $employees = $this->db->dbprefix('employees');
        $groups = $this->db->dbprefix('groups');
        $query = sprintf('SELECT %s.*, %s.*, %s.name AS group_name FROM %s JOIN %s ON (%s.person_id = %s.person_id) JOIN %s ON (%s.group_id = %s.group_id) WHERE %s.deleted = 0 AND department_id IN (%s) %s LIMIT %s, %s',
                         $people, $employees, $groups, $employees, $people, $employees, $people, $groups, $groups, $employees, $employees, implode(',', $department_ids), $order_by, $offset, $limit);
        $data = $this->db->query($query);
        $department_employees = array();
        foreach ($data->result() as $employee) {
            $department_employees[$employee->department_id][] = $employee;
        }
        return $department_employees;
    }
}

?>
