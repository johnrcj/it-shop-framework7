<?php
class Terms extends MY_Controller {
    public function __construct()
    {
        parent::__construct();
    }

    public function terms_list() {
        $this->load_view("terms/terms_list");
    }

    public function terms_detail() {
        $uid = $this->input->get("uid");
        $mode = $this->input->get("mode");

        $info = $this->db->get_where("t_terms", ['uid' => $uid])->row();
        $this->load_view("terms/terms_detail", array(
            "edit_uid" => $uid,
            "info" => $info,
            "mode" => $mode // 0: view, 1: create, 2: edit
        ));
    }

    public function ajax_table() {
        $limit = SSP::limit($_POST);

        $sql_total = <<<EOT
                select * from t_terms order by kind asc
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

        $return_data = array();

        foreach ($arr_data as $row) {
            $temp = array();
            $temp[0] = $temp['title'] = $row->title;
            $temp[1] = $temp['create_date'] = date("Y.m.d H:i:s", strtotime($row->reg_time));
            $temp[2] = $temp['modify_date'] = date("Y.m.d H:i:s", strtotime($row->mod_time));
            $temp[3] = $temp['kind'] = $this->lang->line(_make_terms($row->kind));
            $temp[4] = $temp['uid'] = $row->uid;
            $temp[5] = $temp['uid'] = $row->uid;

            array_push($return_data, $temp);
        }

        echo json_encode(SSP::generateOutData($_POST, $return_data, $recordsTotal, $recordsFiltered));
    }

    public function delete_terms() {
        $uid = $this->input->post("uid");

        if ($this->db->delete("t_terms", ['uid' => $uid])) {
            echo "success";
        } else {
            echo "error";
        }
    }

    public function update_terms() {
        $edit_uid = $this->input->post("edit_uid");
        $title = $this->input->post("title");
        $content = $this->input->post("content");
        $kind = $this->input->post("kind");

        $insert_data = [
            'title' => $title,
            'content' => $content,
            'kind' => $kind,
        ];

        if ($edit_uid == 0) {
            $info_org = $this->db->get_where("t_terms", ['kind' => $kind])->row();
            if ($info_org != null) {
                $this->db->delete("t_terms", ['uid' => $info_org->uid]);
            }

            $insert_data['reg_time'] = date("Y-m-d H:i:s");
            $insert_data['mod_time'] = date("Y-m-d H:i:s");

            $this->db->insert("t_terms", $insert_data);
            $insert_id = $this->db->insert_id();
            if ($insert_id > 0) {
                echo "success";
            } else {
                echo "error";
            }
        } else {
            $insert_data['mod_time'] = date("Y-m-d H:i:s");

            if ($this->db->update("t_terms", $insert_data, ['uid' => $edit_uid])) {
                echo "success";
            } else {
                echo "error";
            }
        }
    }
}