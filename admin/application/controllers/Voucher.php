<?php
class Voucher extends MY_Controller {
    public function __construct()
    {
        parent::__construct();
    }

    ///////////////////////////////////////
    // Category Manage
    ///////////////////////////////////////

    public function category_list() {
        $this->load_view("voucher/category_list");
    }

    public function ajax_category_table() {
        $limit = SSP::limit($_POST);

        $sql_total = <<<EOT
                select * from t_category
EOT;

        $sql_total.= " order by title asc";

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

        $return_data = array();

        foreach ($arr_data as $row) {
            $temp = array();
            $temp[0] = $temp['uid'] = $row->uid;
            $temp[1] = $temp['title'] = $row->title;
            $temp[2] = $temp['uid'] = $row->uid;

            array_push($return_data, $temp);
        }

        echo json_encode(SSP::generateOutData($_POST, $return_data, $recordsTotal, $recordsFiltered));
    }

    public function category_detail() {
        $uid = $this->input->get("uid");
        $mode = $this->input->get("mode");

        $info = $this->db->get_where("t_category", ['uid' => $uid])->row();

        $this->load_view("voucher/category_detail", array(
            "edit_uid" => $uid,
            "info" => $info,
            "mode" => $mode // 0: view, 1: create, 2: edit
        ));
    }

    public function update_category() {
        $edit_uid = $this->input->post("edit_uid");
        $title = $this->input->post("title");

        $insert_data = [
            'title' => $title,
        ];

        $category_dup_info = $this->db->get_where("t_category", ['title' => $title])->row();
        if ($category_dup_info != null) {
            if ($edit_uid == 0) {
                echo "error";
                return;
            } else if ($edit_uid != 0 && $edit_uid != $category_dup_info->uid) {
                echo "error";
                return;
            }
        }

        if ($edit_uid == 0) {
            $this->db->insert("t_category", $insert_data);
            $insert_id = $this->db->insert_id();
            if ($insert_id > 0) {
                echo "success";
            } else {
                echo "error";
            }
        } else {
            if ($this->db->update("t_category", $insert_data, ['uid' => $edit_uid])) {
                echo "success";
            } else {
                echo "error";
            }
        }
    }

    public function delete_category() {
        $uid = $this->input->post("uid");

        if ($this->db->delete("t_category", ['uid' => $uid])) {
            echo "success";
        } else {
            echo "error";
        }
    }

    public function delete_multi_categories() {
        $uids = $this->input->post("uids");

        foreach ($uids as $uid) {
            if ($this->db->delete("t_category", ['uid' => $uid])) {

            } else {
                echo "error";
                break;
            }
        }
        echo "success";
    }

    ///////////////////////////////////////
    // Voucher List
    ///////////////////////////////////////

    public function voucher_list() {
        $this->load_view("voucher/voucher_list");
    }

    public function ajax_voucher_table() {
        $limit = SSP::limit($_POST);

        $search_range = $this->input->post("search_range");
        $search_key = $this->input->post("search_key") != null ? $this->input->post("search_key") : "";

        $week_voucher_cnt = $this->db->query("SELECT * FROM t_voucher WHERE reg_time >= DATE_SUB(now(),INTERVAL 1 WEEK)")->num_rows();
        $month_voucher_cnt = $this->db->query("SELECT * FROM t_voucher WHERE reg_time >= DATE_SUB(now(),INTERVAL 1 MONTH)")->num_rows();
        $year_voucher_cnt = $this->db->query("SELECT * FROM t_voucher WHERE reg_time >= DATE_SUB(now(),INTERVAL 1 YEAR)")->num_rows();

        $where = " 1=1";

        if ($search_range == 1) {   //email
            $where .=  " and (U.email like '%$search_key%' or U.kk_email like '%$search_key%' or U.apple_email like '%$search_key%')";
        } else if ($search_range == 2) {   //phone
            $where .= " and U.phone like '%$search_key%'";
        } else if ($search_range == 3) {   //barcode
            $where .= " and V.barcode like '%$search_key%'";
        } else {   //all
            $where .=  " and (U.email like '%$search_key%' or U.kk_email like '%$search_key%' or U.apple_email like '%$search_key%' or U.phone like '%$search_key%' or V.barcode like '%$search_key%')";
        }

        $sql_total = <<<EOT
            select V.*, U.email, U.kk_email, U.apple_email, U.user_type, U.phone, C.title as category_title from t_voucher V 
            left join t_user U on V.user_id=U.uid 
            left join t_category C on V.category_id=C.uid 
            where $where
            order by V.reg_time desc
EOT;

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
            'voucher_cnt_info' => sprintf($this->lang->line("voucher_cnt_info_format"),
                number_format($week_voucher_cnt),
                number_format($month_voucher_cnt),
                number_format($year_voucher_cnt)),
        );

        if ($recordsFiltered > 0) {
            $index = $recordsTotal - $_POST['start'];
        }

        foreach ($arr_data as $row) {
            $temp = array();

            $email = $row->email;
            if ($row->user_type == 2 && $row->email == "")
                $email = $row->kk_email;
            else if ($row->user_type == 3 && $row->email == "")
                $email = $row->apple_email;

            $temp[0] = $temp['email'] = $email;
            $temp[1] = $temp['phone'] = _make_phone_format($row->phone);
            $temp[2] = $temp['barcode'] = $row->barcode;
            $temp[3] = $temp['name'] = $row->name;
            $temp[4] = $temp['expire_date'] = $row->expire_date;
            $temp[5] = $temp['category'] = $row->category_title;
            $temp[6] = $temp['where_use'] = $row->where_use;
            $temp[7] = $temp['memo'] = $row->memo;
            $temp[8] = $temp['price'] = $row->price;

            array_push($return_data['list'], $temp);
        }

        echo json_encode(SSP::generateOutData($_POST, $return_data, $recordsTotal, $recordsFiltered));
    }

    ///////////////////////////////////////
    // Refunc Application List
    ///////////////////////////////////////
    ///
    public function refund_list() {
        $this->load_view("voucher/refund_list");
    }

    public function ajax_refund_table() {
        $limit = SSP::limit($_POST);

        $search_approval = $this->input->post("search_approval");
        $search_range = $this->input->post("search_range");
        $search_key = $this->input->post("search_key") != null ? $this->input->post("search_key") : "";

        $where = " 1=1";

        if ($search_approval == 1) {
            $where .=  " and R.approval = $search_approval";
        } else if ($search_approval == 2) {
            $where .=  " and R.approval <> 1";
        }

        if ($search_range == 1) {   //email
            $where .=  " and (U.email like '%$search_key%' or U.kk_email like '%$search_key%' or U.apple_email like '%$search_key%')";
        } else if ($search_range == 2) {   //phone
            $where .= " and U.phone like '%$search_key%'";
        } else if ($search_range == 3) {   //barcode
            $where .= " and V.barcode like '%$search_key%'";
        } else {   //all
            $where .=  " and (U.email like '%$search_key%' or U.kk_email like '%$search_key%' or U.apple_email like '%$search_key%' or U.phone like '%$search_key%' or V.barcode like '%$search_key%')";
        }

        $sql_total = <<<EOT
            select R.*, V.uid voucher_id, 
            V.name, V.barcode, V.expire_date, V.where_use, V.price, V.memo, V.where_use, 
            U.email, U.kk_email, U.apple_email, U.user_type, U.phone, C.title category_title, B.name bank_name from t_refund R 
            left join t_voucher V on R.voucher_id=V.uid 
            left join t_user U on V.user_id=U.uid 
            left join t_category C on V.category_id=C.uid 
            left join t_bank B on R.bank_id=B.uid 
            where $where
            order by V.reg_time desc
EOT;

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
            'list' => array()
        );

        foreach ($arr_data as $row) {
            $temp = array();

            $email = $row->email;
            if ($row->user_type == 2 && $row->email == "")
                $email = $row->kk_email;
            else if ($row->user_type == 3 && $row->email == "")
                $email = $row->apple_email;

            $temp[0] = $temp['uid'] = $row->uid;
            $temp[1] = $temp['email'] = $email;
            $temp[2] = $temp['phone'] = _make_phone_format($row->phone);
            $temp[3] = $temp['barcode'] = $row->barcode;
            $temp[4] = $temp['name'] = $row->name;
            $temp[5] = $temp['expire_date'] = $row->expire_date;
            $temp[6] = $temp['category'] = $row->category_title;
            $temp[7] = $temp['where_use'] = $row->where_use;
            $temp[8] = $temp['memo'] = $row->memo;
            $temp[9] = $temp['price'] = $row->price;
            $temp[10] = $temp['bank'] = $row->bank_name;
            $temp[11] = $temp['account'] = $row->account_number;
            $temp[12] = $temp['approval'] = $row->approval;

            array_push($return_data['list'], $temp);
        }

        echo json_encode(SSP::generateOutData($_POST, $return_data, $recordsTotal, $recordsFiltered));
    }

    public function change_approval() {
        $uid = $this->input->post("refund_id");
        $approval = $this->input->post("approval");

        if($this->db->update("t_refund", ['approval' => $approval], ['uid' => $uid])) {
            if($approval == 1) {
                //TODO: YJ create push alarm
                $this->load->library("mylibrary");
                $url = site_url("Voucher/send_refund_create_push");
                $param = array(
                    'event_uid' => $uid
                );
                $this->mylibrary->do_in_background($url, $param);
            }
            echo "success";
        } else {
            echo "error";
        }
    }

    public function change_multi_approval() {
        $uids = $this->input->post("uids");
        $approval = $this->input->post("approval");

        foreach ($uids as $uid) {
            if ($this->db->update("t_refund", ['approval' => $approval], ['uid' => $uid])) {
                if ($approval == 1) {
                    //TODO: YJ create push alarm
//                    $this->load->library("mylibrary");
//                    $url = site_url("Voucher/send_refund_create_push");
//                    $param = array(
//                        'event_uid' => $uid
//                    );
//                    $this->mylibrary->do_in_background($url, $param);
                }
            } else {
                echo "error";
                break;
            }
        }
        echo "success";
    }

    public function send_refund_create_push() {
        $refund_uid = $this->input->post("refund_uid");
        $arr_user = $this->db->get_where("t_user", ['status' => 0])->result();
        $this->load->model("MAlarm");
        foreach($arr_user as $item) {
            //TODO: YJ send push
            $this->MAlarm->add_alarm_data($item->uid, $refund_uid, ALARM_TYPE_EVENT);
        }
    }
}