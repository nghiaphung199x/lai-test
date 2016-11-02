<?php
class MTaskPersonal extends CI_Model{

    protected $_table         = 'tasks_personal';
    protected $_fields        = array();
    protected $_id_admin      = null;

    protected $_prioty          = null;
    protected $_trangthai       = null;

    public function __construct(){
        parent::__construct();

        $this->load->library('MY_System_Info');
        $info 			 = new MY_System_Info();
        $user_info 		 = $info->getInfo();

        $this->_id_admin = $user_info['id'];

        $this->_fields 	 =  array(
            'name' 	 		=> 't.name',
            'prioty' 	 	=> 't.prioty',
            'date_start' 	=> 't.date_start',
            'date_end' 	 	=> 't.date_end',
            'modified' 	    => 't.modified',
            'username' 		=> 'e.username',
        );

        $this->_prioty    = lang('task_prioty');
        $this->_trangthai = lang('task_trangthai');

    }

    public function countItem($arrParams = null, $options = null) {
        if($options == null) {
            $id_admin = $this->_id_admin;
            $this->db -> select('COUNT(t.id) AS totalItem')
                      -> from($this->_table . ' AS t');

            $this->db->where("CONCAT(',',t.implements,',') LIKE '%,$id_admin,%'");

            $where = $this->get_where_from_filter($arrParams);
            if(!empty($where)) {
                foreach($where as $wh)
                    $this->db->where($wh);
            }

            $query = $this->db->get();

            $result = $query->row()->totalItem;
        }
        return $result;
    }

    public function getItem($arrParams = null, $options = null){
        if($options['task'] == 'public-info') {
            $tblCustomers = $this->model_load_model('MTaskCustomers');
            $tblUsers     = $this->model_load_model('MTaskUser');

            $this->db->select("t.*")
                     ->select("DATE_FORMAT(t.date_finish, '%d-%m-%Y') as date_finish", FALSE)
                     ->select("DATE_FORMAT(t.date_start, '%d-%m-%Y') as date_start", FALSE)
                     ->select("DATE_FORMAT(t.date_end, '%d-%m-%Y') as date_end", FALSE)
                     ->from($this->_table . ' as t')
                     ->where('t.id',$arrParams['id']);


            $query = $this->db->get();
            $result =  $query->row_array();
            $this->db->flush_cache();
            if(!empty($result)) {
                $customers = array();
                if(!empty($result['customer_ids'])) {
                    $cid = explode(',', $result['customer_ids']);
                    $customers = $tblCustomers->getItems(array('cid'=>$cid));
                }

                $user_ids = $implement_ids = $xem_ids = $implements = $xems = array();
                if(!empty($result['implements'])) {
                    $implement_ids = explode(',', $result['implements']);
                    $user_ids = array_merge($user_ids, $implement_ids);
                }

                if(!empty($result['xems'])) {
                    $xem_ids = explode(',', $result['xems']);
                    $user_ids = array_merge($user_ids, $xem_ids);
                }

                if(!empty($user_ids)) {
                    $users = $tblUsers->getItems(array('user_ids'=>$user_ids));
                }

                if(!empty($implement_ids)) {
                    foreach($implement_ids as $user_id)
                        $implements[$user_id] = $users[$user_id];
                }

                if(!empty($xem_ids)) {
                    foreach($xem_ids as $user_id)
                        $xems[$user_id] = $users[$user_id];
                }

                $result['implements']    = $implements;
                $result['xems']          = $xems;
                $result['implement_ids'] = $implement_ids;
                $result['xem_ids']       = $xem_ids;
            }
        }
        return $result;
    }

    public function listItem($arrParams = null, $options = null) {
        $paginator = $arrParams['paginator'];
        if($options == null) {
            $id_admin = $this->_id_admin;

            $this->db->select("DATE_FORMAT(t.date_start, '%d-%m-%Y') as start_date", FALSE);
            $this->db->select("DATE_FORMAT(t.date_end, '%d-%m-%Y') as end_date", FALSE);
            $this->db->select("DATE_FORMAT(t.date_finish, '%d-%m-%Y') as finish_date", FALSE);
            $this->db->select("t.id, t.name, t.duration, t.created, t.prioty, t.trangthai, t.progress")
                     ->from($this->_table . ' AS t');

            $this->db->where("CONCAT(',',t.implements,',') LIKE '%,$id_admin,%'");

            if(!empty($arrParams['col']) && !empty($arrParams['order'])){
                $col   = $this->_fields[$arrParams['col']];
                $order = $arrParams['order'];

                $this->db->order_by($col, $order);
                if($arrParams['col'] != 'date_start')
                    $this->db->order_by('t.date_start', 'DESC');
            }else {
                $this->db ->order_by("t.prioty",'ASC')
                          ->order_by('t.date_start', 'DESC');
            }

            $where = $this->get_where_from_filter($arrParams);
            if(!empty($where)) {
                foreach($where as $wh)
                    $this->db->where($wh);
            }

            $page = (empty($arrParams['start'])) ? 1 : $arrParams['start'];
            $this->db->limit($paginator['per_page'],($page - 1)*$paginator['per_page']);

            $query = $this->db->get();

            $result = $query->result_array();

            if(!empty($result)) {
                foreach($result as &$val) {
                    if($val['trangthai'] == 0 || $val['trangthai'] == 1){	// chưa thực hiên + đang thực hiện
                        $now        = date('Y-m-d', strtotime(date("d-m-Y")));
                        $date_end   = date('Y-m-d', strtotime($val['end_date']));

                        $datediff 	= strtotime($now) - strtotime($date_end);
                        $duration 	= floor($datediff/(60*60*24));
                        if($duration <= 0){
                            $val['note'] 		= 'Còn '.abs($duration).' ngày';
                            $val['p_color'] 	= '#4388c2';
                            $val['color'] 		= '#489ee7';

                        }else{
                            $val['note']    =  'Quá '.abs($duration).' ngày';
                            $val['p_color'] = '#aa142f';
                            $val['color']   = '#c90d2f';
                        }

                    }elseif($val['trangthai'] == 2) {// hoàn thành
                        $end_date      = date('Y-m-d', strtotime($val['end_date']));
                        $finish_date   = date('Y-m-d', strtotime($val['finish_date']));

                        $datediff 	= strtotime($end_date) - strtotime($finish_date);
                        $duration 	= floor($datediff/(60*60*24));

                        if($duration < 0){
                            $val['note']    = 'Trễ '.abs($duration).' ngày';
                            $val['p_color'] = '#4a6242';
                            $val['color']   = '#516e47';

                        }elseif($duration > 0){
                            $val['p_color']   = '#a9fa01';
                            $val['color']     = '#91d20a';
                            $val['note']      = 'Sớm '.abs($duration).' ngày';
                        }else{
                            $val['p_color'] = '#18c33e';
                            $val['color']   = '#12e841';
                        }

                    }elseif($val['trangthai'] == 3) {
                        $val['p_color'] = '#e0d91c';
                        $val['color']   = '#bdb720';
                    }elseif($val['trangthai'] == 4) {
                        $val['p_color'] = '#303020';
                        $val['color']   = '#303023';
                    }

                    $val['prioty']    = $this->_prioty[$val['prioty']];
                    $val['trangthai'] = $this->_trangthai[$val['trangthai']];
                }
            }
        }

        return $result;
    }

    public function saveItem($arrParam = null, $options = null) {
        if($options['task'] == 'add') {
            if(isset($arrParam['customer'])) {
                $customer_ids = implode(',', $arrParam['customer']);
            }

            if(isset($arrParam['implement'])) {
                $implements = implode(',', $arrParam['implement']);
            }

            if(isset($arrParam['xem'])) {
                $xems = implode(',', $arrParam['xem']);
            }

            if($arrParam['progress'] == 100)
                $date_finish = @date("Y-m-d H:i:s");
            else
                $date_finish = '0000/00/00 00:00:00';

            $data['name']				    =       stripslashes($arrParam['name']);
            $data['detail']				    =       stripslashes($arrParam['detail']);
            $data['progress']				= 		$arrParam['progress'] / 100;
            $data['date_start']				= 		$arrParam['date_start'];
            $data['date_end']				= 		$arrParam['date_end'];
            $data['date_finish']			= 		$date_finish;
            $data['duration']				= 		$arrParam['duration'];
            $data['created']				= 		@date("Y-m-d H:i:s");
            $data['created_by']				= 		$this->_id_admin;
            $data['modified']				= 		@date("Y-m-d H:i:s");
            $data['modified_by']			= 		$this->_id_admin;
            $data['trangthai']				= 		$arrParam['trangthai'];
            $data['prioty']					= 		$arrParam['prioty'];
            $data['customer_ids']			= 		$customer_ids;
            $data['implements']			    = 		$implements;
            $data['xems']			        = 		$xems;

            $this->db->insert($this->_table,$data);
            $lastId = $this->db->insert_id();
        }

        return $lastId;
    }

    protected function get_where_from_filter($arrParams, $options = null) {
        $where = array();
        if(!empty($arrParams['keywords'])) {
            $keywords = $arrParams['keywords'];
            $where[] = '(t.name LIKE \'%'.$keywords.'%\' OR t.detail LIKE \'%'.$keywords.'%\')';
        }

        if(!empty($arrParams['date_start_from'])) {
            $date_start_from = $arrParams['date_start_from'];
            $where[] 	     = 't.date_start >= \''.$date_start_from.'\'';

        }

        if(!empty($arrParams['date_start_to'])) {
            $date_start_to = $arrParams['date_start_to'];
            $where[] 	   = 't.date_start <= \''.$date_start_to.'\'';
        }

        if(!empty($arrParams['date_end_from'])) {
            $date_end_from = $arrParams['date_end_from'];
            $where[] 	   = 't.date_end >= \''.$date_end_from.'\'';

        }

        if(!empty($arrParams['date_end_to'])) {
            $date_end_to = $arrParams['date_end_to'];
            $where[] 	 = 't.date_end <= \''.$date_end_to.'\'';

        }

        if(!empty($arrParams['trangthai'])) {
            $current_now = date('Y-m-d H:i:s');

            $trangthai     = $arrParams['trangthai'];
            $trangthai_arr = explode(',', $arrParams['trangthai']);

            if(in_array(2, $trangthai_arr)) {
                if(($key = array_search(6, $trangthai_arr)) !== false) {
                    unset($trangthai_arr[$key]);
                }
            }

            if(in_array(0, $trangthai_arr) && in_array(1, $trangthai_arr)) {
                if(($key = array_search(5, $trangthai_arr)) !== false) {
                    unset($trangthai_arr[$key]);
                }
            }

            if(!in_array(5, $trangthai_arr) && !in_array(6, $trangthai_arr)) {
                $where[] = 't.trangthai IN ('.$trangthai.')';

            }else {
                $where_clause = array();
                if(in_array(5, $trangthai_arr)) {
                    if(($key = array_search(0, $trangthai_arr)) !== false) {
                        unset($trangthai_arr[$key]);
                    }

                    if(($key = array_search(1, $trangthai_arr)) !== false) {
                        unset($trangthai_arr[$key]);
                    }

                    if(($key = array_search(5, $trangthai_arr)) !== false) {
                        unset($trangthai_arr[$key]);
                    }

                    $where_clause[] = "t.trangthai IN (0,1) AND TIMESTAMPDIFF(SECOND, t.date_end, '$current_now') > 0";
                }

                if(in_array(6, $trangthai_arr)) {
                    if(($key = array_search(2, $trangthai_arr)) !== false) {
                        unset($trangthai_arr[$key]);
                    }

                    if(($key = array_search(6, $trangthai_arr)) !== false) {
                        unset($trangthai_arr[$key]);
                    }

                    $where_clause[] = "t.trangthai IN (2) AND TIMESTAMPDIFF(SECOND, t.date_finish, '$current_now') > 0";
                }

                if(!empty($trangthai_arr)) {
                    $where_clause[] = 't.trangthai IN ('.implode(',', $trangthai_arr).')';
                }

                $where_clause = implode(' OR ', $where_clause);
                $where[] = $where_clause;
            }

        }

        if(!empty($arrParams['customers'])) {
            $customers = explode(',', $arrParams['customers']);
            $where_clause = array();
            foreach($customers as $cus_id) {
                $where_clause[] = "CONCAT(',',customer_ids,',') LIKE '%,$cus_id,%'";
            }

            $where[] = implode(' OR ', $where_clause);
        }

        if(!empty($arrParams['implement'])) {
            $implement = explode(',', $arrParams['implement']);
            $where_clause = array();
            foreach($implement as $id) {
                $where_clause[] = "CONCAT(',',t.implements,',') LIKE '%,$id,%'";
            }

            $where[] = implode(' OR ', $where_clause);
        }

        if(!empty($arrParams['xem'])) {
            $xem = explode(',', $arrParams['xem']);
            $where_clause = array();
            foreach($xem as $id) {
                $where_clause[] = "CONCAT(',',t.xems,',') LIKE '%,$id,%'";
            }

            $where[] = implode(' OR ', $where_clause);
        }

        return $where;

    }

    function model_load_model($model_name)
    {
        $CI =& get_instance();
        $CI->load->model($model_name);
        return $CI->$model_name;
    }

}