<?php
class MAlarm extends MY_Model {
    public function __construct()
    {
        parent::__construct();
    }

    public function add_alarm($target_usr_uid, $type) {
        $content = "";
        switch ($type) {
            case ALARM_VOUCHER_COMMENT: //voucher date limit
                $content = "I have coupons that expire soon. Check it out.";
                break;
        }

        $insert_log = [
            'reg_time' => date("Y-m-d H:i:s"),
            'user_id' => $target_usr_uid,
            'type' => $type,
            'content' => $content,
            'new_flag' => 1
        ];

        if ($this->db->insert("t_alarm", $insert_log)) {
            $usr_info = $this->db->get_where("t_user", ['uid' => $target_usr_uid])->row();
            $push_flag = 0;
            switch ($type) {
                case ALARM_VOUCHER_COMMENT: //voucher date limit
                    $push_flag = $usr_info->voucher_alarm;
                    break;
            }

            if ($push_flag == 1 && $usr_info->fcm_token != "") {
                //TODO: YJ send push
                $dev_type = $usr_info->dev_type;    //1:android, 2:ios
                $this->load->library('fcm_library');
                $push_data = [
                    'type' => $type,
                    'title' => "Shop",
                    'message' => $content,
                ];

//                $this->fcm_library->_send_push($dev_type, $usr_info->fcm_token, $push_data);
            }
        }
    }

    public function get_alarm_list($usr_uid) {
        $arr_alarm = $this->db->select("*")
            ->from("t_alarm")
            ->where("usr_uid", $usr_uid)
            ->order_by("reg_time", "desc")
            ->get()->result();

        foreach ($arr_alarm as $item) {
            $item->reg_time = date("Y.m.d H:i", strtotime($item->reg_time));
        }

        return $arr_alarm;
    }
}