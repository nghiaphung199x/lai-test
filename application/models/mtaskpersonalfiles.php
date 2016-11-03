<?php
class MTaskPersonalFiles extends CI_Model{

    protected $_table  = 'task_personal_files';
    protected $_id_admin 		= null;
    protected $_fields = array();

    public function __construct(){
        parent::__construct();

        $this->load->library('MY_System_Info');
        $info 			 = new MY_System_Info();
        $user_info 		 = $info->getInfo();

        $this->_id_admin = $user_info['id'];

        $this->_fields = array(
            'name' 	 	 =>  'f.name',
            'file_name' 	 =>  'f.file_name',
            'size' 	 	 =>  'f.size',
            'created' 	 =>  'f.created',
            'username' 	 =>  'e.username',
            'modified' 	 =>  'f.modified',
        );

    }

    public function getItem($arrParam = null, $options = null){
        if($options['task'] == 'public-info'){
            $this->db->select('f.*')
                     ->from($this->_table . ' as f')
                     ->where('f.id',$arrParam['id']);

            $query = $this->db->get();
            $result = $query->row_array();
            $this->db->flush_cache();
        }

        return $result;
    }

    public function countItem($arrParam = null, $options = null){
        if($options['task'] == 'public-list'){
            $ssFilter  = $arrParam['ssFilter'];

            $this->db -> select('COUNT(f.id) AS totalItem')
                     -> from($this->_table . ' AS f')
                     -> where('f.task_id', $arrParam['task_id']);

            $query = $this->db->get();

            $result = $query->row()->totalItem;

            $this->db->flush_cache();
        }
        return $result;
    }

    public function saveItem($arrParam = null, $options = null){
        if($options['task'] == 'add'){
            $data['task_id'] 				= 				$arrParam['task_id'];
            $data['name']					= 				stripslashes($arrParam['name']);
            $data['file_name']				= 				stripslashes($arrParam['file_name']);
            $data['size'] 					= 				$arrParam['size'];
            $data['extension'] 				= 				$arrParam['extension'];
            $data['excerpt']				= 				stripslashes($arrParam['excerpt']);

            $data['created']				= 				@date("Y-m-d H:i:s");
            $data['modified']				= 				@date("Y-m-d H:i:s");
            $data['created_by']     		=				$this->_id_admin;
            $data['modified_by']     		=				$this->_id_admin;

            $this->db->insert($this->_table,$data);
            $lastId = $this->db->insert_id();

            $this->db->flush_cache();

        }elseif($options['task'] == 'edit'){
            $this->db->where("id",$arrParam['id']);

            $data['task_id'] 				= 				$arrParam['task_id'];
            $data['name']					= 				stripslashes($arrParam['name']);
            $data['file_name']				= 				stripslashes($arrParam['file_name']);
            $data['size'] 					= 				$arrParam['size'];
            $data['extension'] 				= 				$arrParam['extension'];
            $data['excerpt']				= 				stripslashes($arrParam['excerpt']);

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
            $paginator = $arrParam['paginator'];

            $this->db -> select('f.id,f.name, f.file_name, f.size, f.created_by, f.modified_by')
                      ->select("DATE_FORMAT(f.created, '%d/%m/%Y %H:%i:%s') as created", FALSE)
                      ->select("DATE_FORMAT(f.modified, '%d/%m/%Y %H:%i:%s') as modified", FALSE)
                      -> from($this->_table . ' AS f')
                      -> where('f.task_id', $arrParam['task_id']);

            $page = (empty($arrParam['start'])) ? 1 : $arrParam['start'];
            $this->db->limit($paginator['per_page'],($page - 1)*$paginator['per_page']);

            if(!empty($arrParam['col']) && !empty($arrParam['order'])){
                $col   = $this->_fields[$arrParam['col']];
                $order = $arrParam['order'];

                $this->db->order_by($col, $order);
            }else
                $this->db->order_by('f.id', 'DESC');

            $query = $this->db->get();

            $result = $query->result_array();
            $this->db->flush_cache();

            if(!empty($result)) {
                $upload_dir = base_url() . 'assets/tasks/files/';
                $userTable = $this->model_load_model('MTaskUser');
                foreach($result as $val) {
                    $user_ids[] = $val['created_by'];
                    $user_ids[] = $val['modified_by'];
                }

                $user_ids = array_unique($user_ids);
                $user_infos = $userTable->getItems(array('user_ids'=>$user_ids));

                foreach($result as &$val) {
                    $val['created_name']  = $user_infos[$val['created_by']]['username'];
                    $val['modified_name'] = $user_infos[$val['modified_by']]['username'];
                    $val['link']		  = $upload_dir . $val['file_name'];
                }

            }

        }
        return $result;
    }

    function model_load_model($model_name)
    {
        $CI =& get_instance();
        $CI->load->model($model_name);
        return $CI->$model_name;
    }
}

?>