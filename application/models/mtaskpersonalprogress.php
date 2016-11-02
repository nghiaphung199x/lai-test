<?php
class MTaskPersonalProgress extends CI_Model{

    protected $_table       = 'task_personal_progress';
    protected $_items       = null;
    protected $_task_ids    = null;
    protected $_is_progress = null;
    protected $_id_admin    = null;
    protected $_admin_name  = null;
    protected $_fields		= array();

    public function __construct(){
        parent::__construct();

        $this->load->library('MY_System_Info');
        $info 			 = new MY_System_Info();
        $user_info 		 = $info->getInfo();

        $this->_id_admin 	    = $user_info['id'];
        $this->_admin_name 		= $user_info['username'];

        $this->_fields 			= array(
            'task_name' 	 => 't.name',        'progress' 	=> 'p.progress',
            'trangthai' 	 => 'p.trangthai',   'prioty'		=> 'p.prioty',

            'created'   	 => 'p.created',      'username'   => 'e.username',
        );
    }

    public function saveItem($arrParam = null, $options = null) {
        if($options['task'] == 'add') {
            $data['task_id'] 			= $arrParam['task_id'];
            $data['trangthai'] 			= $arrParam['trangthai'];
            $data['prioty'] 			= $arrParam['prioty'];
            $data['progress'] 			= $arrParam['progress'] / 100;

            $data['note']				= stripslashes($arrParam['note']);

            $data['created']			= @date("Y-m-d H:i:s");
            $data['created_by']     	= $this->_id_admin;

            $this->db->insert($this->_table,$data);
            $lastId = $this->db->insert_id();
            $this->db->flush_cache();

            return $lastId;
        }

    }

}