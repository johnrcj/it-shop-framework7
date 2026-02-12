<?php
class Notice extends MY_Controller {
    public function __construct()
    {
        parent::__construct();
    }

    public function notice_list() {
        $this->load_view("notice/notice_list");
    }

    public function notice_detail() {
        $uid = $this->input->get("uid");
        $mode = $this->input->get("mode");

        $info = $this->db->get_where("t_notice", ['uid' => $uid])->row();
        if ($info != null) {
            $info->image_url = _get_file_url($info->image);
        }

        $this->load_view("notice/notice_detail", array(
            "edit_uid" => $uid,
            "info" => $info,
            "mode" => $mode // 0: view, 1: create, 2: edit
        ));
    }

    public function ajax_table() {
        $limit = SSP::limit($_POST);

        $search_range = $this->input->post("search_range");
        $search_key = $this->input->post("search_key") != null ? $this->input->post("search_key") : "";

        $where = " 1=1";

        if ($search_range == 1) {   //title
            $where .=  " and title like '%$search_key%'";
        } else if ($search_range == 2) {   //contents
            $where .= " and content like '%$search_key%'";
        } else {   //all
            $where .=  " and (title like '%$search_key%' or content like'%$search_key%')";
        }

        $sql_total = <<<EOT
            select * from t_notice where $where
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

        $return_data = array();

        if ($recordsFiltered > 0) {
            $index = $recordsTotal - $_POST['start'];
        }

        foreach ($arr_data as $row) {
            $temp = array();
            $temp[1] = $temp['title'] = $row->title;
            $temp[2] = $temp['create_date'] = date("Y.m.d H:i:s", strtotime($row->reg_time));
            $temp[3] = $temp['uid'] = $row->uid;
            $temp[4] = $temp['uid'] = $row->uid;

            array_push($return_data, $temp);
        }

        echo json_encode(SSP::generateOutData($_POST, $return_data, $recordsTotal, $recordsFiltered));
    }

    public function delete_notice() {
        $uid = $this->input->post("uid");

        $info = $this->db->get_where("t_notice", ['uid' => $uid])->row();
        if ($info != null) {
            _remove_file(_get_file_path($info->image));
        }

        if ($this->db->delete("t_notice", ['uid' => $uid])) {
            echo "success";
        } else {
            echo "error";
        }
    }

    public function delete_multi_notices() {
        $uids = $this->input->post("uids");

        foreach ($uids as $uid) {
            $info = $this->db->get_where("t_notice", ['uid' => $uid])->row();
            if ($info != null) {
                _remove_file(_get_file_path($info->image));
            }

            if ($this->db->delete("t_notice", ['uid' => $uid])) {

            } else {
                echo "error";
                break;
            }
        }
        echo "success";
    }

    public function update_notice() {
        $edit_uid = $this->input->post("edit_uid");
        $title = $this->input->post("title");
        $content = $this->input->post("content");

        $img = $this->_file_upload(date('Y/m/d'), 'image', false);

        $insert_data = [
            'title' => $title,
            'content' => $content,
        ];

        if ($img != "") {
            $insert_data['image'] = $img;
        }

        if ($edit_uid == 0) {
            $insert_data['reg_time'] = date("Y-m-d H:i:s");

            $this->db->insert("t_notice", $insert_data);
            $insert_id = $this->db->insert_id();
            if ($insert_id > 0) {
                echo "success";
            } else {
                echo "error";
            }
        } else {
            if ($this->db->update("t_notice", $insert_data, ['uid' => $edit_uid])) {
                echo "success";
            } else {
                echo "error";
            }
        }
    }
}