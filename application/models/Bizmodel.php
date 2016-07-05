<?php

class Bizmodel extends CI_Model
{
    protected $import_fields = array(
        'name' => 'name',
        'description' => 'description'
    );

    protected $export_fields = array(
        'name' => 'name',
        'description' => 'description'
    );

    function exists_by_field($table, $field, $value, $like = false, $deleted = true)
    {
        $this->db->from($table);
        if ($like == null) {
            $this->db->where($field, $value);
        } else {
            $this->db->like($field, $value);
        }
        if ($deleted) {
            $this->db->where('deleted', 0);
        }
        $query = $this->db->get();
        return ($query->num_rows() > 0);
    }

    public function get_import_fields()
    {
        return $this->import_fields;
    }

    public function get_export_fields()
    {
        return $this->export_fields;
    }

    public function reset_attributes($data) {
        $CI =& get_instance();
        if (!class_exists('Attribute')) {
            $CI->load->model('Attribute');
        }
        $CI->Attribute->reset_attributes($data);
        return $this;
    }

    public function mass_reset_attributes($data) {
        $CI =& get_instance();
        if (!class_exists('Attribute')) {
            $CI->load->model('Attribute');
        }
        $CI->Attribute->mass_reset_attributes($data);
        return $this;
    }
}

?>
