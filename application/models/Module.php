<?php
class Module extends CI_Model 
{
    function __construct()
    {
        parent::__construct();
    }
	
	function get_module_name($module_id)
	{
		$query = $this->db->get_where('modules', array('module_id' => $module_id), 1);
		
		if ($query->num_rows() ==1)
		{
			$row = $query->row();
			return lang($row->name_lang_key);
		}
		
		$this->lang->load('error');
		return lang('error_unknown');
	}
	
	function get_module_desc($module_id)
	{
		$query = $this->db->get_where('modules', array('module_id' => $module_id), 1);
		if ($query->num_rows() ==1)
		{
			$row = $query->row();
			return lang($row->desc_lang_key);
		}
		$this->lang->load('error');
		return lang('error_unknown');	
	}
	
	function get_all_modules()
	{
		$this->db->from('modules');
		$this->db->order_by("sort", "asc");
		return $this->db->get();		
	}
	
	function get_allowed_modules($person_id, $check_group_modules = true)
	{
		$this->db->from('modules');
		$this->db->join('permissions', 'permissions.module_id = modules.module_id');
		$this->db->where('permissions.person_id', $person_id);
        $this->db->order_by('sort', 'ASC');
        $modules = $this->db->get();

        return $modules;
	}

    function get_all_allowed_modules($person) {
        $allowed_modules = array();

        $this->db->from('modules');
        $this->db->join('permissions', 'permissions.module_id = modules.module_id');
        $this->db->where('permissions.person_id', $person->person_id);
        $this->db->order_by('sort', 'ASC');
        $modules = $this->db->get()->result();

        foreach ($modules as $module) {
            $allowed_modules[$module->module_id] = $module;
        }
        
        /**
         * Level Group Modules
         **/
        $this->db->from('modules');
        $this->db->join('group_permissions', 'group_permissions.module_id = modules.module_id');
        $this->db->where('group_permissions.group_id', $person->group_id);
        $this->db->order_by('sort', 'ASC');
        $modules = $this->db->get()->result();

        foreach ($modules as $module) {
            if (!isset($allowed_modules[$module->module_id])) {
                $allowed_modules[$module->module_id] = $module;
            }
        }

        /* After Combined Allowed Modules, Then Sorting By Field [Sort] */
        usort($allowed_modules, function($a, $b) {
            return ($a->sort > $b->sort);
        });

        return $allowed_modules;
    }
}
?>
