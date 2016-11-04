<?php
class MTaskPersonalProgress extends CI_Model{

    protected $_table       = 'task_personal_progress';
    protected $_items       = null;
    protected $_task_ids    = null;
    protected $_is_progress = null;
    protected $_id_admin    = null;
    protected $_admin_name  = null;
    protected $_prioty      = null;
    protected $_trangthai   = null;
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

        $this->_prioty    = lang('task_prioty');
        $this->_trangthai = lang('task_trangthai');
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

    public function countItem($arrParam = null, $options = null){
        if($options['task'] == 'public-list'){
            $this->db -> select('COUNT(p.id) AS totalItem')
                      -> from($this->_table . ' AS p')
                      -> where('p.task_id', $arrParam['task_id']);


            $query = $this->db->get();
            $result = $query->row()->totalItem;
            $this->db->flush_cache();
        }

        return $result;
    }

    public function listItem($arrParam = null, $options = null){
        if($options['task'] == 'public-list'){
            $paginator = $arrParam['paginator'];
            $this->db -> select('p.id, p.created_by, p.trangthai, t.name as task_name, p.progress, e.username, p.prioty')
                      ->select("DATE_FORMAT(p.created, '%d/%m/%Y %H:%i:%s') as created", FALSE)
                      -> from($this->_table . ' AS p')
                      -> join('tasks_personal as t', 't.id = p.task_id', 'left')
                      -> join('employees AS e', 'e.id = p.created_by', 'left')
                      -> where('p.task_id', $arrParam['task_id']);

            $page = (empty($arrParam['start'])) ? 1 : $arrParam['start'];
            $this->db->limit($paginator['per_page'],($page - 1)*$paginator['per_page']);

            if(!empty($arrParam['col']) && !empty($arrParam['order'])){
                $col   = $this->_fields[$arrParam['col']];
                $order = $arrParam['order'];

                $this->db->order_by($col, $order);
            }else {
                $this->db->order_by('p.created', 'DESC');
            }

            $query = $this->db->get();

            $result = $query->result_array();
            $this->db->flush_cache();

            if(!empty($result)) {
                foreach($result as &$val) {
                    $val['trangthai'] = $this->_trangthai[$val['trangthai']];

                    $val['progress'] = $val['progress'] * 100 . '%';
                    $val['prioty']   = $this->_prioty[$val['prioty']];
                }
            }
        }

        return $result;
    }

    public function deleteItem($arrParam = null, $options = null){
        if($options['task'] == 'delete-multi-by-task'){
            $this->db->where('task_id IN (' . implode(', ', $arrParam['cid']) . ')');
            $this->db->delete($this->_table);

            $this->db->flush_cache();
        }
    }
}