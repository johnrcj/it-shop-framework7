<?php
require_once(APPPATH . '../application/core/Common.php');

class Intro extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        $this->load->view("splash");
    }

    public function check_login() {
        $usr_type = $this->param_post("usr_type");
        $email = $this->param_post("email");
        $name = $this->param_post("name");
        $pwd = $this->param_post("pwd");
        $dev_type = $this->param_post("dev_type");
        $fcm_token = $this->param_post("fcm_token");

        if ($usr_type == 1) {
            $info = $this->db->get_where("t_user", ['email' => $email, 'status>' => '0'])->row();
            if ($info == null) {
                $email_dup_info = $this->db->get_where("t_user", ['email' => $email, 'status' => '0'])->row();
                if ($email_dup_info != null) {
                    $this->_response_error(RES_ERROR_USR_BLOCK);
                } else {
                    $this->_response_error(RES_ERROR_INCORRECT_EMAIL);
                }
            }
            if ($info->password != md5($pwd)) {
                $this->_response_error(RES_ERROR_INCORRECT_PWD);
            }
        } else if ($usr_type == 2) { // KK login
            $info = $this->db->get_where("t_user", ['kk_email' => $email, 'status>' => '0'])->row();
            if ($info == null) {
                $email_dup_info = $this->db->get_where("t_user", ['kk_email' => $email, 'status' => '0'])->row();
                if ($email_dup_info != null) {
                    $this->_response_error(RES_ERROR_EMAIL_DUP);
                } else {
                    $this->_response_error(RES_ERROR_INCORRECT_EMAIL);
                }
            }
        } else {// if ($usr_type == 3) { // Apple login
            $info = $this->db->get_where("t_user", ['apple_email' => $email, 'status>' => '0'])->row();
            if ($info == null) {
                $email_dup_info = $this->db->get_where("t_user", ['kk_email' => $email, 'status' => '0'])->row();
                if ($email_dup_info != null) {
                    $this->_response_error(RES_ERROR_EMAIL_DUP);
                } else {
                    $this->_response_error(RES_ERROR_INCORRECT_EMAIL);
                }
            }
        }

        $update_info['dev_type'] = $dev_type;
        $update_info['user_type'] = $usr_type;
        if ($name != "")
            $update_info['name'] = $name;

        if ($fcm_token != "") {
            $this->db->update("t_user", ['fcm_token' => ''], ['fcm_token' => $fcm_token]);
            $update_info['fcm_token'] = $fcm_token;
        }

        $this->db->update("t_user", $update_info, ['uid' => $info->uid]);

        $this->_response_success([
            'usr_uid' => $info->uid,
        ]);
    }

    public function signup() {
        $usr_type = $this->param_post("usr_type");
        $email = $this->param_post("email");
        $name = $this->param_post("name");
        $pwd = $this->param_post("pwd");
        $phone = $this->param_post("phone");
        $devType = $this->param_post("devType");
        $fcm_token = $this->param_post("fcm_token");
        $uid = null;

        if ($usr_type == 1) { // Member signup
            $email_dup_info = $this->db->get_where("t_user", ['email' => $email, 'status>' => '0'])->row();
            if ($email_dup_info != null) {
                $this->_response_error(RES_ERROR_EMAIL_DUP);
            }
            $email_dup_info = $this->db->get_where("t_user", ['email' => $email, 'status' => '0'])->row();
            if ($email_dup_info != null) {
                $uid = $email_dup_info->uid;
            }


            // TODO: YJ get user name from User Cert API
            $name = "USER" . rand(0, 100); // test code
        } else if ($usr_type == 2) { // KK signup
            $email_dup_info = $this->db->get_where("t_user", ['kk_email' => $email, 'status>' => '0'])->row();
            if ($email_dup_info != null) {
                $this->_response_error(RES_ERROR_EMAIL_DUP);
            }
            $email_dup_info = $this->db->get_where("t_user", ['kk_email' => $email, 'status' => '0'])->row();
            if ($email_dup_info != null) {
                $uid = $email_dup_info->uid;
            }
        } else { // Apple signup
            $email_dup_info = $this->db->get_where("t_user", ['apple_email' => $email, 'status>' => '0'])->row();
            if ($email_dup_info != null) {
                $this->_response_error(RES_ERROR_EMAIL_DUP);
            }
            $email_dup_info = $this->db->get_where("t_user", ['apple_email' => $email, 'status' => '0'])->row();
            if ($email_dup_info != null) {
                $uid = $email_dup_info->uid;
            }
        }

        if ($uid == null) {
            $insert_data = [
                'reg_time' => date("Y-m-d H:i:s"),
                'name' => $name,
                'email' => $usr_type == 1 ? $email : "",
                'kk_email' => $usr_type == 2 ? $email : "",
                'apple_email' => $usr_type == 3 ? $email : "",
                'user_type' => $usr_type,
                'password' => $pwd != "" ? md5($pwd) : "",
                'phone' => $phone,
                'dev_type' => $devType,
                'fcm_token' => $fcm_token,
                'status' => 1,
                'voucher_alarm' => 0,
                'voucher_alarm_time' => "2,0,0",
                'register_auto' => 0,
            ];

            $this->db->insert("t_user", $insert_data);
            $insert_id = $this->db->insert_id();

            if ($insert_id > 0) {
                $this->_response_success([
                    'usr_uid' => $insert_id,
                ]);
            } else {
                $this->_response_error(RES_ERROR_DB);
            }
        } else {
            if ($this->db->update("t_user",
                ['name' => $name,
                'email' => $usr_type == 1 ? $email : "",
                'kk_email' => $usr_type == 2 ? $email : "",
                'apple_email' => $usr_type == 3 ? $email : "",
                'user_type' => $usr_type,
                'password' => $pwd != "" ? md5($pwd) : "",
                'phone' => $phone,
                'dev_type' => $devType,
                'fcm_token' => $fcm_token,
                'status' => 1,
                'voucher_alarm' => 0,
                'voucher_alarm_time' => "2,0,0",
                'register_auto' => 0],
                ['uid' => $uid])) {
                $this->_response_success([
                    'usr_uid' => $uid,
                ]);
            } else {
                $this->_response_error(RES_ERROR_DB);
            }
        }
    }

    public function check_email() {
        $email = $this->param_post("email");

        $usr_info = $this->db->get_where("t_user", ['email' => $email, 'status>' => '0'])->row();
        if ($usr_info == null) {
            $this->_response_success();
        } else {
            $this->_response_error(RES_ERROR_DUPLICATE);
        }
    }

    public function check_phone() {
        $phone = $this->param_post("phone");

        //TODO: YJ check user information using identification API
        $verify_info = $this->verify_user_certification();

        $usr_info = $this->db->get_where("t_user", ['phone' => $phone, 'status>' => '0'])->row();
        if ($usr_info == null) {
            $this->_response_success();
        } else {
            $this->_response_error(RES_ERROR_DUPLICATE);
        }
    }

    //TODO: YJ send user information using identification API
    public function find_pwd() {
        $verify_info = $this->verify_user_certification();
        $email = $verify_info['email'];

        $usr_info = $this->db->get_where("t_user", ['email' => $email, 'user_type' => 1])->row();
        if ($usr_info == null) {
            $this->_response_error(RES_ERROR_INFO_NO_EXIST);
        } else {
            $this->_response_success([
                'usr_uid' => $usr_info->uid,
            ]);
        }
        $this->_response_success();
    }

    public function update_pwd() {
        $usr_uid = $this->param_post("usr_uid");
        $pwd = $this->param_post("pwd");

        if ($this->db->update("t_user", ['password' => md5($pwd)], ['uid' => $usr_uid])) {
            //TODO: YJ send to email
//            $this->_send_email($email, "", $tmp_pwd);

            $this->_response_success();
        } else {
            $this->_response_error(RES_ERROR_DB);
        }
    }

    //TODO: YJ send user information using identification API
    public function find_id() {
        $verify_info = $this->verify_user_certification();
        $phone = $verify_info['phone'];

        $usr_info = $this->db->get_where("t_user", ['phone' => $phone, 'user_type' => 1])->row();
        if ($usr_info == null) {
            $this->_response_error(RES_ERROR_INFO_NO_EXIST);
        } else {
            $email = $usr_info->email;
            if (preg_match('/@([^\.]*)\./', $email, $m)) {
                $email_mask = _obfuscate_email($email);
            } else {
                $partial_id = "";

                for ($i = 0; $i < strlen($email); $i++) {
                    if ($i % 2 == 0) {
                        $partial_id .=$email[$i];
                    } else {
                        $partial_id .='*' ;
                    }
                }

                $email_mask = $partial_id;
            }

            $this->_response_success([
                'email' => $usr_info->email,
                'email_mask' => $email_mask,
            ]);
        }
    }

    //TODO: YJ implement identify certification
    private function verify_user_certification() {
        $info['email'] = "test1@gmail.com";
        $info['phone'] = "123456";

        return $info;
    }

    public function file_upload() {
        $upload_result = $this->_file_upload(date('Y/m/d'), 'img', false);

        $this->_response_success([
            "url" => _get_file_url($upload_result),
            "file" => $upload_result,
        ]);
    }

    //TODO: YJ crontab hourly
    public function send_notification() {
        $date = @date("Y-m-d");
        $time = @date('H');

        // set old alarm flag as 0
        $this->db->update("t_alarm", ['new_flag' => '0']);
        $sql = <<<EOT
            select uid, voucher_alarm_time from t_user
            where status > 0 and voucher_alarm = 1
EOT;
        $user_list = $this->db->query($sql)->result();
        foreach ($user_list as $user) {
            $times = explode(",", $user->voucher_alarm_time);
            if (count($times) >= 3) {
                $day = (int)$times[0] + 1;
                $hour = (int)$times[2];
                if ($times[1] == 1)
                    $hour = 12 + (int)$times[2];

                if ($time == $hour) {
                    $sql = <<<EOT
                        select * from t_voucher 
                        where user_id = $user->uid and expire_date >= '$date' and DATEDIFF(expire_date, '$date') = $day and refund = 0
EOT;
                    echo($sql);
                    $voucher_list = $this->db->query($sql)->result();
                    if ($voucher_list != null) {
                        $this->load->model("MAlarm");

                        $this->MAlarm->add_alarm($user->uid, ALARM_VOUCHER_COMMENT);
                    }
                }
            }
        }
    }
}