<?php
class MTaskPersonalComment extends CI_Model{

    protected $_table 	 = 'task_personal_comment';
    protected $_id_admin = null;
    public function __construct(){
        parent::__construct();

        $this->load->library('MY_System_Info');
        $info 			 = new MY_System_Info();
        $user_info 		 = $info->getInfo();

        $this->_id_admin = $user_info['id'];
    }

    public function countItem($arrParam = null, $options = null){
        if($options['task'] == 'public-list'){
            $ssFilter  = $arrParam['ssFilter'];

            $this->db -> select('COUNT(c.id) AS totalItem')
                      -> from($this->_table . ' AS c')
                      -> where('c.task_id', $arrParam['task_id']);

            $query = $this->db->get();

            $result = $query->row()->totalItem;
            $this->db->flush_cache();
        }
        return $result;
    }

    public function saveItem($arrParam = null, $options = null){
        if($options['task'] == 'add'){
            $data['user_id'] 				= 				$this->_id_admin;
            $data['task_id'] 				= 				$arrParam['task_id'];
            $data['content']				= 				stripslashes($arrParam['content']);
            $data['created']				= 				@date("Y-m-d H:i:s");
            $data['modified']				= 				@date("Y-m-d H:i:s");
            $data['modified_by']     		=				$this->_id_admin;

            $this->db->insert($this->_table,$data);
            $lastId = $this->db->insert_id();

            $this->db->flush_cache();

        }elseif($options['task'] == 'edit'){
            $this->db->where("id",$arrParam['id']);

            $data['content']				= 				stripslashes($arrParam['content']);
            $data['modified']				= 				@date("Y-m-d H:i:s");
            $data['modified_by']     		=				$this->_id_admin;

            $this->db->update($this->_table,$data);

            $this->db->flush_cache();

            $lastId = $arrParam['id'];
        }

        return $lastId;
    }


    public function listItem($arrParam = null, $options = null){
        if($options['task'] == 'public-list'){
            $ssFilter  = $arrParam['ssFilter'];

            $paginator = $arrParam['paginator'];

            $this->db -> select('c.id, e.username, c.content, p.image_id')
                      ->select("DATE_FORMAT(c.created, '%d/%m/%Y %H:%i:%s') as created", FALSE)
                      ->select("DATE_FORMAT(c.modified, '%d/%m/%Y %H:%i:%s') as modified", FALSE)
                      -> from($this->_table . ' AS c')
                      -> join('employees AS e', 'c.user_id = e.id', 'left')
                      -> join('people AS p', 'e.person_id = p.person_id', 'left')
                      -> where('c.task_id', $arrParam['task_id'])
                      -> order_by('c.id DESC');

            $page = (empty($arrParam['start'])) ? 1 : $arrParam['start'];
            $this->db->limit($paginator['per_page'],($page - 1)*$paginator['per_page']);

            $query = $this->db->get();

            $result = $query->result_array();
            $this->db->flush_cache();

            if(!empty($result)) {
                foreach($result as &$val){
                    $val['content'] = nl2br($val['content']);
                    if($val['image_id'] == NULL)
                        $val['image'] = base_url() . 'assets/assets/images/avatar-default.jpg';
                    else
                        $val['image'] = base_url() . 'app_files/view/'.$val['image_id'];
                }
            }
        }
        return $result;
    }

    public function getItems($arrParam = null, $options = null){
        if($options == null) {
            $this->db -> select('c.user_id, c.task_id, c.files, c.created')
                      -> from($this->_table . ' AS c')
                      -> where('c.task_id IN ('.implode(',', $arrParam['task_ids']).')');

            $query = $this->db->get();

            $result = $query->result_array();
            $this->db->flush_cache();

            return $result;
        }
    }

    public function deleteItem($arrParam = null, $options = null){
        if($options['task'] == 'delete-multi-by-task'){
            $upload_dir = FILE_TASK_PATH . 'comment/';
            $items = $this->getItems(array('task_ids'=>$arrParam['cid']));
            if(!empty($items)) {
                $files = array();
                foreach($items as $val) {
                    if(!empty($val['files'])) {
                        $file_tmp     = explode(',', $val['files']);
                        $files        = array_merge($files, $file_tmp);
                    }
                }

                if(!empty($files)) {
                    foreach($files as $file)
                        @unlink($upload_dir . $file);
                }

                $this->db->where('task_id IN (' . implode(', ', $arrParam['cid']) . ')');
                $this->db->delete($this->_table);

                $this->db->flush_cache();
            }
        }
    }

    function model_load_model($model_name)
    {
        $CI =& get_instance();
        $CI->load->model($model_name);
        return $CI->$model_name;
    }

}