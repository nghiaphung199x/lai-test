<?php

class BizQuotes_contract {

    function get_all_quotes_contract($limit = 100, $offset = 0, $col = 'id_quotes_contract', $order = 'desc') {
        $this->db->from('quotes_contract');
        $this->db->order_by($col, $order);
        $this->db->limit($limit);
        $this->db->offset($offset);
        return $this->db->get();
    }

    function count_all_quotes_contract() {
        $this->db->from('quotes_contract');
        return $this->db->count_all_results();
    }

    function get_info_quotes_contract($id) {
        $this->db->where('id_quotes_contract', $id);
        $query = $this->db->get('quotes_contract');

        if ($query->num_rows() == 1) {
            return $query->row();
        } else {
            //Get empty base parent object, as $item_id is NOT an item
            $item_obj = new stdClass();

            //Get all the fields from items table
            $fields = $this->db->list_fields('quotes_contract');

            foreach ($fields as $field) {
                $item_obj->$field = '';
            }
            return $item_obj;
        }
    }

    function exists_quotes_contract($id) {
        $this->db->from('quotes_contract');
        $this->db->where('id_quotes_contract', $id);
        $query = $this->db->get();
        return ($query->num_rows() == 1);
    }

    function save_quotes_contract(&$data, $id = false) {
        if (!$id or ! $this->exists($id)) {
            if ($this->db->insert('quotes_contract', $data)) {
                $data['id_quotes_contract'] = $this->db->insert_id();
                return true;
            }
            return false;
        } else {
            $this->db->where('id_quotes_contract', $id);
            return $this->db->update('quotes_contract', $data);
        }
    }

    function delete_list_quotes_contract($id) {
        $this->db->where_in('id_quotes_contract', $id);
        return $this->db->delete('quotes_contract');
    }

    function get_search_suggestions_quotes_contract($search, $limit = 25) {
        $suggestions = array();

        $this->db->from('quotes_contract');
        $this->db->like('title_quotes_contract', $search);
        $this->db->order_by("title_quotes_contract", "asc");
        $by_name = $this->db->get();
        foreach ($by_name->result() as $row) {
            $suggestions[] = array('label' => $row->title_quotes_contract);
        }

        $this->db->from('quotes_contract');
        $this->db->like('id_quotes_contract', $search);
        $this->db->order_by("id_quotes_contract", "asc");
        $by_id = $this->db->get();
        foreach ($by_id->result() as $row) {
            $suggestions[] = array('label' => $row->id_quotes_contract);
        }

        //only return $limit suggestions
        if (count($suggestions > $limit)) {
            $suggestions = array_slice($suggestions, 0, $limit);
        }
        return $suggestions;
    }

    function search_quotes_contract($search, $cat = '', $limit = 20, $offset = 0, $column = 'id_quotes_contract', $orderby = 'desc') {
        $this->db->from('quotes_contract');
        if ($cat) {
            $this->db->where("cat_quotes_contract", $cat);
        }
        $this->db->where("(title_quotes_contract LIKE '%" . $search . "%' OR id_quotes_contract LIKE '%" . $search . "%')");
        $this->db->order_by($column, $orderby);
        $this->db->limit($limit);
        $this->db->offset($offset);
        return $this->db->get();
    }

    function search_count_all_quotes_contract($search, $cat = '') {
        $this->db->from('quotes_contract');
        if ($cat) {
            $this->db->where("cat_quotes_contract", $cat);
        }
        $this->db->where("(title_quotes_contract LIKE '%" . $search . "%' OR id_quotes_contract LIKE '%" . $search . "%')");
        $result = $this->db->get();
        return $result->num_rows();
    }

    function get_list_template_quotes_contract($cat = '') {
        if ($cat) {
            $this->db->where("cat_quotes_contract", $cat);
        }
        $query = $this->db->get("quotes_contract");
        return $query->result();
    }

}
