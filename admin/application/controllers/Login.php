<?php
class Login extends CI_Controller{
    public function __construct()
    {
        parent::__construct();
        $this->load->helper("url");
        $this->load->library("session");
        $this->load->database();
        $this->lang->load('en', 'english');
    }

    public function index(){
        $this->load->view("login/index");
    }

    public function login(){
        $usrid = $this->input->post("usrid");
        $password = $this->input->post("password");

        $usr_info = $this->db->get_where("t_admin", ['user_id' => $usrid])->row();

        if ($usr_info == null)
            die ('no_exist');

        $usr_info = $this->db->get_where('t_admin', ['user_id' => $usrid,'password' => md5($password)])->row();
        if ($usr_info == null)
            die ('no_password');

        $this->session->set_userdata(SESSION_MANAGER_UID, $usr_info->uid);
        $this->session->set_userdata(SESSION_MANAGER_USRID, $usrid);
        echo 'success';
    }

    public function logout(){
        $this->session->set_userdata(SESSION_MANAGER_UID,"");
        $this->session->set_userdata(SESSION_MANAGER_USRID,"");
        redirect($this->index());
    }

    public function change_manager_info(){
        $id = $this->input->post("id");
        $old_password = $this->input->post("old_password");
        $new_password = $this->input->post("new_password");

        $manager_info = $this->db->get_where("t_admin", ['uid' => $this->session->userdata(SESSION_MANAGER_UID)])->row();
        if ($manager_info != null) {
            if ($manager_info->password != md5($old_password)) {
                echo "error_old_password";
            } else {
                $update_data = [
                    'user_id' => $id,
                    'password' => md5($new_password),
                ];
                if ($this->db->update("t_admin", $update_data, ['uid' => $this->session->userdata(SESSION_MANAGER_UID)])) {
                    $this->session->set_userdata(SESSION_MANAGER_USRID, $id);
                    echo "success";
                } else {
                    echo "err";
                }
            }
        } else {
            echo "err";
        }
    }
}