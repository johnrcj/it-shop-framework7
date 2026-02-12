<?php


class Home extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index() {
        $this->db->limit(5);
        $this->db->order_by('reg_time', 'DESC');
        $notice_list = $this->db->get('tbl_notice')->result();

        $this->db->limit(5);
        $this->db->order_by('reg_time', 'DESC');
        $cs_list = $this->db->get('tbl_cs')->result();

        $this->db->limit(5);
        $this->db->order_by('reg_time', "DESC");

        $this->load_view('user/member_list', ['notice_list' => $notice_list, 'cs_list' => $cs_list]);
    }
}