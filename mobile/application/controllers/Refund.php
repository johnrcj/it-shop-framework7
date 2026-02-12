<?php
class Refund extends MY_Controller {
    public function __construct()
    {
        parent::__construct();
    }

    public function get_refund_list() {
        $usr_uid = $this->param_post("usr_uid");
        $now = @date("Y-m-d");

        $this->load->model("MUser");
        if (($ret = $this->MUser->check_user_status($usr_uid)) != RES_SUCCESS)
            $this->_response_error($ret);

        $sql = <<<EOT
            select r.*, v.* 
            from t_refund r 
            join t_voucher v on r.voucher_id=v.uid 
            where v.user_id = $usr_uid and r.approval=0
EOT;

        $res_refund_array = $this->db->query($sql)->result();
        foreach ($res_refund_array as $item) {
            $item->image_url = _get_file_url($item->image);
        }

        $res_refund_count = 0;
        if (count($res_refund_array) > 0) {
            $res_refund_count = count($res_refund_array);
        }

        $sql = <<<EOT
            select v.* from t_voucher v 
            left join t_refund r on v.uid=r.voucher_id 
            where r.voucher_id is null and v.expire_date < '$now' and v.use_end = 0 and v.user_id = $usr_uid
EOT;
        $res_voucher_array = $this->db->query($sql)->result();
        foreach ($res_voucher_array as $item) {
            $item->image_url = _get_file_url($item->image);
        }

        $res_voucher_count = 0;
        if (count($res_voucher_array)) 
        {
            $res_voucher_count = count($res_voucher_array);
        }

        $lookup_date = date("Y.m.d");

        $cash_mark = "";
        $warning_info = $this->db->get_where("t_warning", ['kind' => 1])->row();
        if ($warning_info != null) {
            $cash_mark = $warning_info->content;
        }

        $this->_response_success([                        
            'refund_count' => $res_refund_count,
            'refund_array' => $res_refund_array,
            'voucher_count' => $res_voucher_count,
            'voucher_array' => $res_voucher_array,
            'lookup_date' => $lookup_date,
            'cash_mark' => $cash_mark,
        ]);
    }

    public function get_settlement_info() {
        $usr_uid = $this->param_post("usr_uid");
        $sel_ids = $this->param_post("sel_ids");

        $this->load->model("MUser");
        if (($ret = $this->MUser->check_user_status($usr_uid)) != RES_SUCCESS)
            $this->_response_error($ret);

        $voucher_id_list = explode(" ", $sel_ids);
        $condition = "uid = " . $voucher_id_list[0];

        for ($i = 1; $i < count($voucher_id_list); $i++) {
            $condition = $condition . " or uid = " . $voucher_id_list[$i];
        }

        $sql = <<<EOT
            select * from t_voucher where $condition
EOT;

        $voucher_list = $this->db->query($sql)->result();
        foreach ($voucher_list as $item) {
            $item->image_url = _get_file_url($item->image);
        }

        $check_cash_mark = "";
        $warning_info = $this->db->get_where("t_warning", ['kind' => 2])->row();
        if ($warning_info != null) {
            $check_cash_mark = $warning_info->content;
        }

        $amount_cash_mark = "";
        $warning_info = $this->db->get_where("t_warning", ['kind' => 3])->row();
        if ($warning_info != null) {
            $amount_cash_mark = $warning_info->content;
        }

        $this->_response_success([                        
            'voucher_list' => $voucher_list,
            'amount_cash_mark' => $amount_cash_mark,
            'check_cash_mark' => $check_cash_mark
        ]);
    }

    public function get_my_voucher_info() {
        $usr_uid = $this->param_post("usr_uid");

        //TODO: YJ specify content of information
        $info_image = "2021/11/28/16381105595759921.jpg";
        $info = $this->db->get_where("t_app", ['field_name' => 'my_voucher_image'])->row();
        if ($info != null) {
            $info_image = $info->field_value;
        }

        $nes_title = "";
        $opt_title1 = "";
        $opt_title2 = "";
        $opt_title3 = "";
        $opt_title4 = "";

        $info = $this->db->get_where("t_terms", ['kind' => 4])->row();
        if ($info != null) {
            $nes_title = $info->title;
        }

        $info = $this->db->get_where("t_terms", ['kind' => 5])->row();
        if ($info != null) {
            $opt_title1 = $info->title;
        }

        $info = $this->db->get_where("t_terms", ['kind' => 6])->row();
        if ($info != null) {
            $opt_title2 = $info->title;
        }

        $info = $this->db->get_where("t_terms", ['kind' => 7])->row();
        if ($info != null) {
            $opt_title3 = $info->title;
        }

        $info = $this->db->get_where("t_terms", ['kind' => 8])->row();
        if ($info != null) {
            $opt_title4 = $info->title;
        }

        $this->_response_success([
            'info_url' => _get_file_url($info_image),
            'nes_title' => $nes_title,
            'opt_title1' => $opt_title1,
            'opt_title2' => $opt_title2,
            'opt_title3' => $opt_title3,
            'opt_title4' => $opt_title4,
        ]);
    }

    public function  get_account_page_info() {
        $usr_uid = $this->param_post("usr_uid");

        $this->load->model("MUser");
        if (($ret = $this->MUser->check_user_status($usr_uid)) != RES_SUCCESS)
            $this->_response_error($ret);

        //TODO: YJ specify content of information
        $bank_info = "Information!!!";
        $info = $this->db->get_where("t_app", ['field_name' => 'bank_info'])->row();
        if ($info != null) {
            $bank_info = $info->field_value;
        }

        $sql = <<<EOT
            select * from t_bank
EOT;

        $bank_list = $this->db->query($sql)->result();

        $this->_response_success([
            'bank_info' => $bank_info,
            'bank_list' => $bank_list,
        ]);
    }

    public function verify_account() {
        $usr_uid = $this->param_post("usr_uid");
        $payment = $this->param_post("payment");
        $account = $this->param_post("account");

        $this->load->model("MUser");
        if (($ret = $this->MUser->check_user_status($usr_uid)) != RES_SUCCESS)
            $this->_response_error($ret);

        $this->load->model("MUser");
        if (($ret = $this->MUser->check_user_status($usr_uid)) != RES_SUCCESS)
            $this->_response_error($ret);

        //TODO: YJ verify bank account


        $this->_response_success([
            'account_name'=> "Account 1",
        ]);
    }

    public function register_refund() {
        $usr_uid = $this->param_post("usr_uid");
        $sel_ids = $this->param_post("sel_ids");
//        $info['payment'] = $this->param_post("payment");
        $info['bank_id'] = $this->param_post("bank_id");
        $info['account_number'] = $this->param_post("account_number");
        $info['approval'] = 0;
        $info['reg_time'] = date("Y-m-d H:i:s");

        $this->load->model("MUser");
        if (($ret = $this->MUser->check_user_status($usr_uid)) != RES_SUCCESS)
            $this->_response_error($ret);
        
        $id_list = explode(" ", $sel_ids);
        foreach ($id_list as $value) {
            $info['voucher_id'] = $value;

            //TODO: YJ process refund


            $this->db->insert("t_refund", $info);

            $this->db->update("t_voucher", ['refund' => 1], ['uid' => $value]);
        }

        $this->_response_success();
    }
}