<?php
class Main extends MY_Controller {
    public function __construct()
    {
        parent::__construct();
    }

    public function get_searchkey_info() {
        $usr_uid = $this->param_post("usr_uid");

        $this->load->model("MUser");
        if (($ret = $this->MUser->check_user_status($usr_uid)) != RES_SUCCESS)
            $this->_response_error($ret);

        $key_sql = <<<EOT
            select * from t_searchkey 
            where user_id=$usr_uid 
            group by keyword order by reg_time desc limit 10
EOT;

        $searchkey_list = $this->db->query($key_sql)->result();

        $this->_response_success([            
            'searchkey_list' => $searchkey_list
        ]);
    }    

    public function get_search_result() {
        $usr_uid = $this->param_post("usr_uid");
        $search_key = $this->param_post("search_key");

        if ($search_key != "") {
            $key_dup_info = $this->db->get_where("t_searchkey", ['keyword' => $search_key, 'user_id' => $usr_uid])->row();
            if ($key_dup_info != null) {
                $this->_response_success();
            }

            $search_log = [
                'reg_time' => date("Y-m-d H:i:s"),
                'user_id' => $usr_uid,
                'keyword' => $search_key
            ];
            $this->db->insert("t_searchkey", $search_log);
        }
        $this->_response_success();
    }

    public function remove_searchkey() {
        $usr_uid = $this->param_post("usr_uid");
        $keyword = $this->param_post("keyword");

        $this->load->model("MUser");
        if (($ret = $this->MUser->check_user_status($usr_uid)) != RES_SUCCESS)
            $this->_response_error($ret);

        if($this->db->delete("t_searchkey", ['user_id' => $usr_uid, 'keyword' => $keyword])) {
            $this->_response_success();
        } else {
            $this->_response_error(RES_ERROR_DB);
        }
    }

    public function remove_recent_searchkey() {
        $usr_uid = $this->param_post("usr_uid");

        $this->load->model("MUser");
        if (($ret = $this->MUser->check_user_status($usr_uid)) != RES_SUCCESS)
            $this->_response_error($ret);

        if($this->db->delete("t_searchkey", ['user_id' => $usr_uid])) {
            $this->_response_success();
        } else {
            $this->_response_error(RES_ERROR_DB);
        }
    }

    public function get_category_list() {
        $key_sql = "select * from t_category";

        $list = $this->db->query($key_sql)->result();

        $this->_response_success([                        
            'list' => $list
        ]);
    }

    /*
     * recognize using a voucher image
    */
    public function recognize_voucher() {
        $image = $this->param_post("image");

        // test code to recognize image uploaded using $image
        // determine barcode, name, expire_date, where_use, price
        $info = $this->recognize($image);

        if ($info['valid'] == 1) {
            $this->_response_success([
                'info' => $info,
            ]);
        } else {
            $this->_response_error(RES_ERROR_RECOGNITION);
        }
    }

    /*
     * recognize using a voucher image
    */
    public function recognize_multi_vouchers() {
        $paths = $this->param_post("paths");

        $images = array();
        $paths = explode(";", $paths);
        for ($i = 0; $i < count($paths); $i++) {
            if ($paths[$i] != "") {
                // test code to recognize image uploaded using $image
                // determine barcode, name, expire_date, where_use, price
                $info = $this->recognize($paths[$i]);
                if ($info['valid'] == 1)
                    $info['image_url'] = _get_file_url($info['image']);

                array_push($images, $info);
            }
        }

        $this->_response_success([
            'image_list' => $images,
        ]);
    }

    public function add_voucher() {
        $info['uid'] = $this->param_post("uid");
        $info['user_id'] = $this->param_post("user_id");
        $info['barcode'] = $this->param_post("barcode");
        $info['name'] = $this->param_post("name");
        $info['image'] = $this->param_post("image");
        $info['category_id'] = $this->param_post("category_id");
        $info['expire_date'] = $this->param_post("expire_date");
        $info['where_use'] = $this->param_post("where_use");
        $info['memo'] = $this->param_post("memo");
        $info['price'] = $this->param_post("price");
        $info['use_end'] = $this->param_post("use_end");
        $info['refund'] = 0;
        $info['reg_time'] = date("Y-m-d H:i:s");

        $this->load->model("MUser");
        if (($ret = $this->MUser->check_user_status($info['user_id'])) != RES_SUCCESS)
            $this->_response_error($ret);

        $voucher_dup_info = $this->db->get_where("t_voucher", ['barcode' => $info['barcode']])->row();
        if ($voucher_dup_info != null) {
            if ($info['uid'] == 0)
                $this->_response_error(RES_ERROR_VOUCHER_DUP);
            else if ($info['uid'] != 0 && $info['uid'] != $voucher_dup_info->uid)
                $this->_response_error(RES_ERROR_VOUCHER_DUP);
        }

        if ($info['uid'] == 0) {
            $this->db->insert("t_voucher", $info);    
        } else {
            $this->db->update("t_voucher", $info, ['uid' => $info['uid']]);
        }
        
        $this->_response_success();
    }

    /*
     * recognize voucher information from image has uploaded
     * TODO: recongnition engine
     * here are testing code
     * */
    private function recognize($image) {
        $info = array();

//        $result = rand(0, 1);
//        if ($result == 1) {
            $info['valid'] = 1;
            $info['barcode'] = rand(10000001, 99999999);
            $info['name'] = "Voucher Name" . rand(0, 100);
            $info['expire_date'] = date('Y-m-d', strtotime('+' . rand(-15, 15) . ' days'));
            $info['where_use'] = "Where use to " . rand(0, 10000);
            $info['image'] = $image;
            $info['price'] = rand(100, 10000);
//        } else {
//            $info['valid'] = 0;
//        }

        return $info;
    }

    public function get_home_info() {
        $info['type'] = $this->param_post("type");
        $info['search_key'] = $this->param_post("search_key");
        $user_id = $this->param_post("user_id");

        $this->load->model("MUser");
        if (($ret = $this->MUser->check_user_status($user_id)) != RES_SUCCESS)
            $this->_response_error($ret);

        $order_by = "";
        if ($info['type'] == "registered") {
            $order_by = "v.reg_time desc";
        } else if ($info['type'] == "validity") {
            $order_by = "v.expire_date asc";

        } else if ($info['type'] == "brand") {
            $order_by = "v.name asc";
        } else
            $order_by = "v.reg_time desc";

        $search_str = "";
        if (strlen($info['search_key']) > 0) {
            $search_str = " and v.name like '%" . $info['search_key'] . "%' ";
        }
        
        $now = @date("Y-m-d");

        $available_sql = <<<EOT
            select v.*, c.title category_title from t_voucher v 
            left join t_category c on v.category_id = c.uid 
            where v.user_id = $user_id and v.expire_date >= '$now' and v.use_end = 0 and v.refund = 0 $search_str
            order by $order_by
EOT;
        $available_array = $this->db->query($available_sql)->result();

        $use_end_sql = <<<EOT
            select v.*, c.title category_title from t_voucher v 
            left join t_category c on v.category_id = c.uid 
            where v.user_id = $user_id and v.expire_date >= '$now' and v.use_end = 1 and v.refund = 0 $search_str 
            order by  $order_by
EOT;
        $use_end_array = $this->db->query($use_end_sql)->result();

        $expired_sql = <<<EOT
            select v.*, c.title category_title from t_voucher v 
            left join t_category c on v.category_id = c.uid 
            where v.user_id = $user_id and v.use_end = 0 and v.expire_date < '$now'  and v.refund = 0 $search_str 
            order by $order_by
EOT;
        $expired_array = $this->db->query($expired_sql)->result();

        $register_auto = 0;
        $usr_info = $this->db->get_where("t_user", ['uid' => $user_id])->row();
        if ($usr_info != null) {
            $register_auto = $usr_info->register_auto;
        }

        foreach ($available_array as $item) {
            $item->remain_days = date_diff(new DateTime($now), new DateTime($item->expire_date))->days;
            $item->image_url = _get_file_url($item->image);
        }

        foreach ($use_end_array as $item) {
            $item->remain_days = date_diff(new DateTime($now), new DateTime($item->expire_date))->days;
            $item->image_url = _get_file_url($item->image);
        }

        foreach ($expired_array as $item) {
            $item->image_url = _get_file_url($item->image);
        }

        $this->_response_success([                        
            'available_count' => count($available_array),
            'available_list' => $available_array,
            'use_end_count' => count($use_end_array),
            'use_end_list' => $use_end_array,
            'expired_count' => count($expired_array),
            'expired_list' => $expired_array,
            'register_auto' => $register_auto,
        ]);
    }

    public function use_end() {
        $usr_id = $this->param_post("user_id");
        $uid = $this->param_post("id");

        $this->load->model("MUser");
        if (($ret = $this->MUser->check_user_status($usr_id)) != RES_SUCCESS)
            $this->_response_error($ret);

        $uid_list = explode(",", $uid);
        foreach ($uid_list as $value) {
            $sql = <<<EOT
                update t_voucher set use_end = 1 
                where uid=$value
EOT;
            $this->db->query($sql);
        }        
        $this->get_home_info();
    }

    public function delete_voucher() {
        $usr_id = $this->param_post("user_id");
        $uid = $this->param_post("id");

        $this->load->model("MUser");
        if (($ret = $this->MUser->check_user_status($usr_id)) != RES_SUCCESS)
            $this->_response_error($ret);

        $uid_list = explode(",", $uid);
        foreach ($uid_list as $value) {
            $info = $this->db->get_where("t_voucher", ['uid' => $value])->row();
            if ($info != null) {
                _remove_file(_get_file_path($info->image));
            }

            $sql = <<<EOT
                delete from t_voucher 
                where uid=$value
EOT;
            $this->db->query($sql);
        }
        $this->get_home_info();
    }

    public function use_end_cancel() {
        $usr_id = $this->param_post("user_id");
        $uid = $this->param_post("id");

        $this->load->model("MUser");
        if (($ret = $this->MUser->check_user_status($usr_id)) != RES_SUCCESS)
            $this->_response_error($ret);

        $uid_list = explode(",", $uid);
        foreach ($uid_list as $value)
        {
            $sql = <<<EOT
                update t_voucher set use_end = 0 
                where uid=$value
EOT;
            $this->db->query($sql);
        }
        $this->get_home_info();
    }

    public function get_voucher_info() {
        $usr_id = $this->param_post("user_id");
        $uid= $this->param_post("id");

        $this->load->model("MUser");
        if (($ret = $this->MUser->check_user_status($usr_id)) != RES_SUCCESS)
            $this->_response_error($ret);

        $info = $this->db->get_where("t_voucher", ['uid' => $uid])->row();
        if ($info != null) {
            $info->expire_date = date("Y-m-d", strtotime($info->expire_date));
            $info->image_url = _get_file_url($info->image);
            $this->_response_success(['info' => $info]);
        } else {
            $this->_response_error(RES_ERROR_DB);
        }
    }
}