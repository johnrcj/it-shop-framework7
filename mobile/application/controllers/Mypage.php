<?php
class Mypage extends MY_Controller {
    public function __construct()
    {
        parent::__construct();
    }

    public function get_mypage_info() {
        $usr_uid = $this->param_post("usr_uid");
        $usr_type = $this->param_post("usr_type");
        $dev_type = $this->param_post("dev_type");

        $this->load->model("MUser");
        if (($ret = $this->MUser->check_user_status($usr_uid)) != RES_SUCCESS)
            $this->_response_error($ret);

        //TODO: YJ specify correct url of app store
        if ($dev_type == 1) { // Android
            $update_url =  "https://play.google.com/store/apps/details?id=kr.co.conpang";
            $info = $this->db->get_where("t_app", ['field_name' => 'update_android_url'])->row();
            if ($info != null) {
                $update_url = $info->field_value;
            }

            $share_url =  "https://play.google.com/store/apps/details?id=kr.co.conpang";
            $info = $this->db->get_where("t_app", ['field_name' => 'share_android_url'])->row();
            if ($info != null) {
                $share_url = $info->field_value;
            }
        } else { // iOS
            $update_url = "http://itunes.apple.com/lookup?bundleId=kr.co.conpang";
            $info = $this->db->get_where("t_app", ['field_name' => 'update_ios_url'])->row();
            if ($info != null) {
                $update_url = $info->field_value;
            }

            $share_url = "http://itunes.apple.com/lookup?bundleId=kr.co.conpang";
            $info = $this->db->get_where("t_app", ['field_name' => 'share_ios_url'])->row();
            if ($info != null) {
                $share_url = $info->field_value;
            }
        }

        $recognition_mark = "";
        $warning_info = $this->db->get_where("t_warning", ['kind' => 4])->row();
        if ($warning_info != null) {
            $recognition_mark = $warning_info->content;
        }

        $info = $this->db->get_where("t_user", ['uid' => $usr_uid])->row();
        if ($info != null) {
            $this->_response_success([
                'name' => $info->name,
                'register_auto' => $info->register_auto,
                'voucher_alarm' => $info->voucher_alarm,
                'voucher_alarm_time' => $info->voucher_alarm_time,
                'update_url' => $update_url,
                'share_url' => $share_url,
                'recognition_mark' => $recognition_mark,
            ]);
        } else {
            $this->_response_error(RES_ERROR_INFO_NO_EXIST);
        }
    }

    public function check_email() {
        $usr_id = $this->param_post("user_id");
        $email = $this->param_post("email");

        $this->load->model("MUser");
        if (($ret = $this->MUser->check_user_status($usr_id)) != RES_SUCCESS)
            $this->_response_error($ret);

        $email_dup_info = $this->db->get_where("t_user", ['email' => $email, 'uid<>' => $usr_id, 'status>' => '0'])->row();
        if ($email_dup_info != null) {
            $this->_response_error(RES_ERROR_EMAIL_DUP);
        } else {
            $this->_response_success();
        }
    }

    public function check_phone() {
        $usr_id = $this->param_post("user_id");
        $phone = $this->param_post("phone");

        //TODO: YJ check user information using identification API
        $verify_info = $this->verify_user_certification();

        $this->load->model("MUser");
        if (($ret = $this->MUser->check_user_status($usr_id)) != RES_SUCCESS)
            $this->_response_error($ret);

        $phone_dup_info = $this->db->get_where("t_user", ['phone' => $phone, 'uid<>' => $usr_id, 'status>' => '0'])->row();
        if ($phone_dup_info != null) {
            $this->_response_error(RES_ERROR_PHONE_DUP);
        } else {
            $this->_response_success();
        }
    }

    public function get_user_info() {
        $usr_uid = $this->param_post("usr_uid");

        $this->load->model("MUser");
        if (($ret = $this->MUser->check_user_status($usr_uid)) != RES_SUCCESS)
            $this->_response_error($ret);

        $info = $this->db->get_where("t_user", ['uid' => $usr_uid])->row();
        if ($info != null) {
            $this->_response_success([
                'phone' => $info->phone,
            ]);
        } else {
            $this->_response_error(RES_ERROR_INFO_NO_EXIST);
        }
    }

    public function modify_info() {
        $usr_id = $this->param_post("user_id");
        $email = $this->param_post("email");
        $pwd = $this->param_post("pwd");
        $phone = $this->param_post("phone");
        $devType = $this->param_post("devType");
        $fcm_token = $this->param_post("fcm_token");

        $this->load->model("MUser");
        if (($ret = $this->MUser->check_user_status($usr_id)) != RES_SUCCESS)
            $this->_response_error($ret);

        $email_dup_info = $this->db->get_where("t_user", ['email' => $email, 'uid<>' => $usr_id, 'status>' => '0'])->row();
        if ($email_dup_info != null) {
            $this->_response_error(RES_ERROR_EMAIL_DUP);
        }

        $phone_dup_info = $this->db->get_where("t_user", ['phone' => $phone, 'uid<>' => $usr_id, 'status>' => '0'])->row();
        if ($phone_dup_info != null) {
            $this->_response_error(RES_ERROR_PHONE_DUP);
        }

        $update_info = [
            'reg_time' => date("Y-m-d H:i:s"),
            'email' => $email,
            'user_type' => "1",
            'password' => $pwd != "" ? md5($pwd) : "",
            'phone' => $phone,
            'dev_type' => $devType,
            'fcm_token' => $fcm_token,
            'status' => 1,
        ];

        if ($this->db->update("t_user", $update_info, ['uid' => $usr_id])) {
            $this->_response_success();
        } else {
            $this->_response_error(RES_ERROR_DB);
        }
    }

    public function change_recognition() {
        $usr_uid = $this->param_post("usr_uid");
        $status = $this->param_post("status");

        $this->load->model("MUser");
        if (($ret = $this->MUser->check_user_status($usr_uid)) != RES_SUCCESS)
            $this->_response_error($ret);

        if ($this->db->update("t_user", ['register_auto' => $status], ['uid' => $usr_uid])) {
            $this->_response_success();
        } else {
            $this->_response_error(RES_ERROR_DB);
        }
    }

    public function change_alarm_time() {
        $usr_uid = $this->param_post("usr_uid");
        $time = $this->param_post("time");

        $this->load->model("MUser");
        if (($ret = $this->MUser->check_user_status($usr_uid)) != RES_SUCCESS)
            $this->_response_error($ret);

        if ($this->db->update("t_user", ['voucher_alarm_time' => $time], ['uid' => $usr_uid])) {
            $this->_response_success();
        } else {
            $this->_response_error(RES_ERROR_DB);
        }
    }

    public function change_alarm() {
        $usr_uid = $this->param_post("usr_uid");
        $status = $this->param_post("status");

        $this->load->model("MUser");
        if (($ret = $this->MUser->check_user_status($usr_uid)) != RES_SUCCESS)
            $this->_response_error($ret);

        if ($this->db->update("t_user", ['voucher_alarm' => $status], ['uid' => $usr_uid])) {
            $this->_response_success();
        } else {
            $this->_response_error(RES_ERROR_DB);
        }
    }

    public function get_notice_list() {
        $search_key = $this->param_post("search_key");

        $where = "(title like '%$search_key%' or content like '%$search_key%')";

        $sql = <<<EOT
            select * from t_notice where $where
EOT;

        $sql.= " order by reg_time desc";

        $this->db->trans_begin();
        $arr_data = $this->db->query($sql)->result();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
        } else {
            $this->db->trans_commit();
        }

        foreach ($arr_data as $item) {
            $item->reg_time = date("Y.m.d", strtotime($item->reg_time));
            $item->image_url = _get_file_url($item->image);
        }

        $this->_response_success([
            'list' => $arr_data
        ]);
    }

    public function get_qna_list() {
        $usr_uid = $this->param_post("usr_uid");

        $this->load->model("MUser");
        if (($ret = $this->MUser->check_user_status($usr_uid)) != RES_SUCCESS)
            $this->_response_error($ret);

        $arr_list = $this->db->select("*")
            ->from("t_qna")
            ->where("user_id", $usr_uid)
            ->order_by("reg_time", "desc")
            ->get()->result();

        foreach ($arr_list as $item) {
            $item->reg_time = date("Y.m.d", strtotime   ($item->reg_time));

            $image_urls = array();
            $images = explode(";", $item->images);
            for ($i = 0; $i < count($images); $i++) {
                if ($images[$i] != "")
                    array_push($image_urls, _get_file_url($images[$i]));
            }

            $item->image_urls = $image_urls;

            $item->answer_image_url = _get_file_url($item->answer_image);
        }

        $this->_response_success([
            'list' => $arr_list
        ]);
    }

    public function add_qna() {
        $usr_uid = $this->param_post("usr_uid");
        $title = $this->param_post("title");
        $content = $this->param_post("content");
        $images = $this->param_post("images");

        $this->load->model("MUser");
        if (($ret = $this->MUser->check_user_status($usr_uid)) != RES_SUCCESS)
            $this->_response_error($ret);

        $insert_data = [
            'reg_time' => date("Y-m-d H:i:s"),
            'user_id' => $usr_uid,
            'title' => $title,
            'content' => $content,
            'images' => $images,
        ];

        if ($this->db->insert("t_qna", $insert_data)) {
            $this->_response_success();
        } else {
            $this->_response_error(RES_ERROR_DB);
        }
    }

    public function logout() {
        $usr_uid = $this->param_post("usr_uid");

        $this->load->model("MUser");
        if (($ret = $this->MUser->check_user_status($usr_uid)) != RES_SUCCESS)
            $this->_response_error($ret);

        if ($this->db->update("t_user", ['fcm_token' => ""], ['uid' => $usr_uid])) {
            $this->_response_success();
        } else {
            $this->_response_error(RES_ERROR_DB);
        }
    }

    public function withdrawal() {
        $usr_uid = $this->param_post("usr_uid");

        $this->load->model("MUser");
        if (($ret = $this->MUser->check_user_status($usr_uid)) != RES_SUCCESS)
            $this->_response_error($ret);

        if ($this->db->update("t_user", ['status' => 0], ['uid' => $usr_uid])) {
            $this->_response_success();
        } else {
            $this->_response_error(RES_ERROR_DB);
        }
    }

    //TODO: YJ implement identify certification
    private function verify_user_certification() {
        $info['email'] = "test1@gmail.com";
        $info['phone'] = "123456";

        return $info;
    }
}