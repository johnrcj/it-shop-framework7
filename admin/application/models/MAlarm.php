<?php
class MAlarm extends MY_Model {
    public function __construct()
    {
        parent::__construct();
    }

    public function add_alarm_data($target_usr_uid, $target_uid, $type) {
        $content = "";
        switch ($type) {
            case ALARM_TYPE_EVENT: //register new event
                $content = "New event is registered.";
                break;
            case ALARM_TYPE_QNA_ANSWER: //answer of question
                $content = "Answer of question is arrived.";
                break;
        }

        $insert_log = [
            'reg_time' => date("Y-m-d H:i:s"),
            'user_id' => $target_usr_uid,
            'type' => $type,
            'content' => $content,
            'new_flag' => 1
        ];

        if($this->db->insert("t_alarm", $insert_log)) {
            $usr_info = $this->db->get_where("t_user", ['uid' => $target_usr_uid])->row();
            $push_flag = 0;
            switch ($type) {
                case ALARM_TYPE_EVENT: //register new event
                    $push_flag = $usr_info->event_alarm;
                    break;
                case ALARM_TYPE_QNA_ANSWER: //answer of question
                    $push_flag = $usr_info->answer_qna_alarm;
                    break;
            }

            if($push_flag == 1 && $usr_info->fcm_token != "") {
                //TODO: send push
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
}