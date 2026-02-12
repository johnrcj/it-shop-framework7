<?php
class User extends MY_Controller {
    public function __construct()
    {
        parent::__construct();
    }

    ///////////////////////////////////////
    // Member List
    ///////////////////////////////////////
    ///
    public function member_list() {
        $this->load_view("user/member_list");
    }

    public function ajax_table() {
        $limit = SSP::limit($_POST);

        $search_range = $this->input->post("search_range");
        $search_key = $this->input->post("search_key") != null ? $this->input->post("search_key") : "";

        $week_member_cnt = $this->db->query("SELECT * FROM t_user WHERE reg_time >= DATE_SUB(now(),INTERVAL 1 WEEK)")->num_rows();
        $month_member_cnt = $this->db->query("SELECT * FROM t_user WHERE reg_time >= DATE_SUB(now(),INTERVAL 1 MONTH)")->num_rows();
        $year_member_cnt = $this->db->query("SELECT * FROM t_user WHERE reg_time >= DATE_SUB(now(),INTERVAL 1 YEAR)")->num_rows();

        $where = " 1=1";

        if ($search_range == 1) {   //email
            $where .=  " and (email like '%$search_key%' or kk_email like '%$search_key%' or apple_email like '%$search_key%')";
        } else if ($search_range == 2) {   //phone
            $where .= " and phone like '%$search_key%'";
        } else {   //all
            $where .=  " and (email like '%$search_key%' or kk_email like '%$search_key%' or apple_email like '%$search_key%' or phone like '%$search_key%')";
        }

        $sql_total = <<<EOT
                select * from t_user where status > 0 AND $where
EOT;

        $sql_total.= " order by reg_time desc";

        $sql = $sql_total . " $limit";

        $this->db->trans_begin();
        $arr_data = $this->db->query($sql)->result();
        $total_data_cnt = $this->db->query($sql_total)->num_rows();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
        } else {
            $this->db->trans_commit();
        }
        if (count($arr_data) > 0) {
            $recordsTotal = $total_data_cnt;
            $recordsFiltered = $recordsTotal;
        } else {
            $recordsTotal = 0;
            $recordsFiltered = $recordsTotal;
        }

        $return_data = array(
            'list' => array(),
            'member_cnt_info' => sprintf($this->lang->line("member_cnt_info_format"),
                number_format($week_member_cnt),
                number_format($month_member_cnt),
                number_format($year_member_cnt)),
        );

        foreach ($arr_data as $row) {
            $temp = array();

            $email = $row->email;
            if ($row->user_type == 2 && $row->email == "")
                $email = $row->kk_email;
            else if ($row->user_type == 3 && $row->email == "")
                $email = $row->apple_email;

            $temp[1] = $temp['email'] = $email;
            $temp[2] = $temp['phone'] = _make_phone_format($row->phone);
            $temp[3] = $temp['reg_time'] = $row->reg_time;
            $temp[4] = $temp['uid'] = $row->uid;

            array_push($return_data['list'], $temp);
        }

        echo json_encode(SSP::generateOutData($_POST, $return_data, $recordsTotal, $recordsFiltered));
    }

    public function withdraw_user() {
        $uid = $this->input->post("uid");

        if ($this->db->update("t_user", ['status' => 0], ['uid' => $uid])) {
            echo "success";
        } else {
            echo "error";
        }
    }

    public function withdraw_multi_users() {
        $uids = $this->input->post("uids");

        foreach ($uids as $uid) {
            if ($this->db->update("t_user", ['status' => 0], ['uid' => $uid])) {

            } else {
                echo "error";
                break;
            }
        }
        echo "success";
    }


    ///////////////////////////////////////
    // Withdraw Member Manage
    ///////////////////////////////////////

    public function withdrawal_list() {
        $this->load_view("user/withdrawal_list");
    }

    public function ajax_withdraw_table() {
        $limit = SSP::limit($_POST);

        $withdrawal_member_cnt = $this->db->get_where("t_user", ['status' => 0])->num_rows();

        $where = " 1=1";

        $sql_total = <<<EOT
                select * from t_user where status = 0 AND $where
EOT;

        $sql_total.= " order by withdraw_time desc";

        $sql = $sql_total . " $limit";

        $this->db->trans_begin();
        $arr_data = $this->db->query($sql)->result();
        $total_data_cnt = $this->db->query($sql_total)->num_rows();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
        } else {
            $this->db->trans_commit();
        }
        if (count($arr_data) > 0) {
            $recordsTotal = $total_data_cnt;
            $recordsFiltered = $recordsTotal;
        } else {
            $recordsTotal = 0;
            $recordsFiltered = $recordsTotal;
        }

        $return_data = array(
            'list' => array(),
        );

        foreach ($arr_data as $row) {
            $temp = array();

            $email = $row->email;
            if ($row->user_type == 2 && $row->email == "")
                $email = $row->kk_email;
            else if ($row->user_type == 3 && $row->email == "")
                $email = $row->apple_email;

            $temp[1] = $temp['email'] = $email;
            $temp[2] = $temp['phone'] = _make_phone_format($row->phone);
            $temp[3] = $temp['withdraw_time'] = $row->reg_time;
            $temp[4] = $temp['uid'] = $row->uid;
            $temp[5] = $temp['uid'] = $row->uid;

            array_push($return_data['list'], $temp);
        }

        echo json_encode(SSP::generateOutData($_POST, $return_data, $recordsTotal, $recordsFiltered));
    }

    public function delete_user() {
        $uid = $this->input->post("uid");

        if ($this->db->update("t_user", ['status' => -2], ['uid' => $uid])) {
            echo "success";
        } else {
            echo "error";
        }
    }

    public function delete_multi_user() {
        $uids = $this->input->post("uids");

        foreach ($uids as $uid) {
            if ($this->db->update("t_user", ['status' => -2], ['uid' => $uid])) {

            } else {
                echo "error";
                break;
            }
        }
        echo "success";
    }

    public function change_member() {
        $uid = $this->input->post("uid");

        if ($this->db->update("t_user", ['status' => 1], ['uid' => $uid])) {
            echo "success";
        } else {
            echo "error";
        }
    }

    public function change_multi_member() {
        $uids = $this->input->post("uids");

        foreach ($uids as $uid) {
            if ($this->db->update("t_user", ['status' => 1], ['uid' => $uid])) {

            } else {
                echo "error";
                break;
            }
        }
        echo "success";
    }
}