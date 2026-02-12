<?php
class Warning extends MY_Controller {
    public function __construct()
    {
        parent::__construct();
    }

    public function warning_list() {
        $this->load_view("warning/warning_list");
    }

    public function warning_detail() {
        $uid = $this->input->get("uid");
        $mode = $this->input->get("mode");

        $info = $this->db->get_where("t_warning", ['uid' => $uid])->row();
        $this->load_view("warning/warning_detail", array(
            "edit_uid" => $uid,
            "info" => $info,
            "mode" => $mode // 0: view, 1: create, 2: edit
        ));
    }

    public function ajax_table() {
        $limit = SSP::limit($_POST);

        $sql_total = <<<EOT
                select * from t_warning order by kind asc
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
            $temp[0] = $temp['kind'] = _make_warning($row->kind);
            $temp[1] = $temp['create_date'] = date("Y.m.d H:i:s", strtotime($row->reg_time));
            $temp[2] = $temp['modify_date'] = date("Y.m.d H:i:s", strtotime($row->mod_time));
            $temp[3] = $temp['uid'] = $row->uid;
            $temp[4] = $temp['uid'] = $row->uid;

            array_push($return_data, $temp);
        }

        echo json_encode(SSP::generateOutData($_POST, $return_data, $recordsTotal, $recordsFiltered));
    }

    public function delete_warning() {
        $uid = $this->input->post("uid");

        if ($this->db->delete("t_warning", ['uid' => $uid])) {
            echo "success";
        } else {
            echo "error";
        }
    }

    public function update_warning() {
        $edit_uid = $this->input->post("edit_uid");
        $content = $this->input->post("content");
        $kind = $this->input->post("kind");

        $insert_data = [
            'content' => $content,
            'kind' => $kind,
        ];

        if ($edit_uid == 0) {
            $info_org = $this->db->get_where("t_warning", ['kind' => $kind])->row();
            if ($info_org != null) {
                $this->db->delete("t_warning", ['uid' => $info_org->uid]);
            }

            $insert_data['reg_time'] = date("Y-m-d H:i:s");
            $insert_data['mod_time'] = date("Y-m-d H:i:s");

            $this->db->insert("t_warning", $insert_data);
            $insert_id = $this->db->insert_id();
            if ($insert_id > 0) {
                echo "success";
            } else {
                echo "error";
            }
        } else {
            $insert_data['mod_time'] = date("Y-m-d H:i:s");

            if ($this->db->update("t_warning", $insert_data, ['uid' => $edit_uid])) {
                echo "success";
            } else {
                echo "error";
            }
        }
    }
}