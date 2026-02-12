<?php
class Qna extends MY_Controller {
    public function __construct()
    {
        parent::__construct();
    }

    public function qna_list() {
        $this->load_view("qna/qna_list");
    }

    public function qna_detail() {
        $uid = $this->input->get("uid");
        $mode = $this->input->get("mode");

        $info = $this->db->get_where("t_qna", ['uid' => $uid])->row();
        if ($info != null) {
            $image_urls = array();
            $images = explode(";", $info->images);
            for ($i = 0; $i < count($images); $i++) {
                if ($images[$i] != "")
                    array_push($image_urls, _get_file_url($images[$i]));
            }

            $info->image_urls = $image_urls;

            $info->answer_image_url = _get_file_url($info->answer_image);
        }

        $usr_info = $this->db->get_where("t_user", ['uid' => $info->user_id])->row();

        $this->load_view("qna/qna_detail", array(
            'edit_uid' => $uid,
            'info' => $info,
            'usr_info' => $usr_info,
            "mode" => $mode // 0: view, 1: create, 2: edit
        ));
    }

    public function ajax_table() {
        $limit = SSP::limit($_POST);

        $search_answer = $this->input->post("search_answer");
        $search_range = $this->input->post("search_range");
        $search_key = $this->input->post("search_key") != null ? $this->input->post("search_key") : "";

        $where = " 1=1";

        if ($search_answer == 1) {
            $where .=  " and Q.answer_time is not null";
        } else if ($search_answer == 2) {
            $where .=  " and Q.answer_time is null";
        }

        if ($search_range == 1) {   //title
            $where .=  " and Q.title like '%$search_key%'";
        } else if ($search_range == 2) {   //contents
            $where .= " and Q.content like '%$search_key%'";
        } else if ($search_range == 3) {   //email
            $where .= " and (U.email like '%$search_key%' or U.kk_email like '%$search_key%' or U.apple_email like '%$search_key%')";
        } else {   //all
            $where .=  " and (U.email like '%$search_key%' or U.kk_email like '%$search_key%' or U.apple_email like '%$search_key%' or Q.title like '%$search_key%' or Q.content like '%$search_key%')";
        }

        $sql_total = <<<EOT
            select Q.*, U.email, U.kk_email, U.apple_email, U.user_type, U.phone from t_qna Q 
            join t_user U on Q.user_id=U.uid
            where $where
            order by Q.reg_time desc
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

            $email = $row->email;
            if ($row->user_type == 2 && $row->email == "")
                $email = $row->kk_email;
            else if ($row->user_type == 3 && $row->email == "")
                $email = $row->apple_email;

            $temp[0] = $temp['uid'] = $row->uid;
            $temp[1] = $temp['title'] = $row->title;
            $temp[2] = $temp['email'] = $email;
            $temp[3] = $temp['create_date'] = date("Y.m.d H:i:s", strtotime($row->reg_time));
            $temp[4] = $temp['answer_status'] = $row->answer_time != "" ? 1 : 0;
            $temp[5] = $temp['uid'] = $row->uid;
            $temp[6] = $temp['uid'] = $row->uid;

            array_push($return_data, $temp);
        }

        echo json_encode(SSP::generateOutData($_POST, $return_data, $recordsTotal, $recordsFiltered));
    }

    public function update_qna() {
        $edit_uid = $this->input->post("edit_uid");
        $answer_content = $this->input->post("answer_content");
        $img = $this->_file_upload(date('Y/m/d'), 'image', false);

        $insert_data = [
            'answer_content' => $answer_content,
        ];

        if ($img != "") {
            $insert_data['answer_image'] = $img;
        }

        if ($edit_uid != 0) {
            $insert_data['answer_time'] = date("Y-m-d H:i:s");

            if ($this->db->update("t_qna", $insert_data, ['uid' => $edit_uid])) {
                $info = $this->db->get_where("t_qna", ['uid' => $edit_uid])->row();

                //TODO: send alarm to clients
                //$this->load->model("MAlarm");
                //$this->MAlarm->add_alarm_data($info->user_id, $edit_uid, ALARM_TYPE_QNA_ANSWER);
                echo "success";
            } else {
                echo "error";
            }
        } else {
            echo "error";
        }
    }

    public function delete_qna() {
        $uid = $this->input->post("uid");

        $info = $this->db->get_where("t_qna", ['uid' => $uid])->row();
        if ($info != null) {
            $images = explode(";", $info->images);
            for ($i = 0; $i < count($images); $i++) {
                if ($images[$i] != "")
                    _remove_file(_get_file_path($images[$i]));
            }
            _remove_file(_get_file_path($info->answer_image));
        }

        if ($this->db->delete("t_qna", ['uid' => $uid])) {
            echo "success";
        } else {
            echo "error";
        }
    }

    public function delete_multi_qnas() {
        $uids = $this->input->post("uids");

        foreach ($uids as $uid) {
            $info = $this->db->get_where("t_qna", ['uid' => $uid])->row();
            if ($info != null) {
                $images = explode(";", $info->images);
                for ($i = 0; $i < count($images); $i++) {
                    if ($images[$i] != "")
                        _remove_file(_get_file_path($images[$i]));
                }
                _remove_file(_get_file_path($info->answer_image));
            }

            if ($this->db->delete("t_qna", ['uid' => $uid])) {

            } else {
                echo "error";
                break;
            }
        }
        echo "success";
    }
}