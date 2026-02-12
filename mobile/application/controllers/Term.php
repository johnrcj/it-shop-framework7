<?php
require_once(APPPATH . '../application/core/Common.php');

class Term extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    public function get_signup_term_title() {
        $nes_title = "";
        $opt_title = "";

        $info = $this->db->get_where("t_terms", ['kind' => 1])->row();
        if ($info != null) {
            $nes_title = $info->title;
        }

        $info = $this->db->get_where("t_terms", ['kind' => 2])->row();
        if ($info != null) {
            $opt_title = $info->title;
        }

        $this->_response_success([
            'nes_title' => $nes_title,
            'opt_title' => $opt_title,
        ]);
    }

    public function get_signup_term_content() {
        $nes_title = "";
        $nes_content = "";
        $opt_title = "";
        $opt_content = "";

        $info = $this->db->get_where("t_terms", ['kind' => 1])->row();
        if ($info != null) {
            $nes_title = $info->title;
            $nes_content = $info->content;
        }

        $info = $this->db->get_where("t_terms", ['kind' => 2])->row();
        if ($info != null) {
            $opt_title = $info->title;
            $opt_content = $info->content;
        }

        $this->_response_success([
            'nes_title' => $nes_title,
            'nes_content' => $nes_content,
            'opt_title' => $opt_title,
            'opt_content' => $opt_content,
        ]);
    }

    public function get_refund_term_content() {
        $nes_title = "";
        $nes_content = "";
        $opt_title1 = "";
        $opt_content1 = "";
        $opt_title2 = "";
        $opt_content2 = "";
        $opt_title3 = "";
        $opt_content3 = "";
        $opt_title4 = "";
        $opt_content4 = "";

        $info = $this->db->get_where("t_terms", ['kind' => 4])->row();
        if ($info != null) {
            $nes_title = $info->title;
            $nes_content = $info->content;
        }

        $info = $this->db->get_where("t_terms", ['kind' => 5])->row();
        if ($info != null) {
            $opt_title1 = $info->title;
            $opt_content1 = $info->content;
        }

        $info = $this->db->get_where("t_terms", ['kind' => 6])->row();
        if ($info != null) {
            $opt_title2 = $info->title;
            $opt_content2 = $info->content;
        }

        $info = $this->db->get_where("t_terms", ['kind' => 7])->row();
        if ($info != null) {
            $opt_title3 = $info->title;
            $opt_content3 = $info->content;
        }

        $info = $this->db->get_where("t_terms", ['kind' => 8])->row();
        if ($info != null) {
            $opt_title4 = $info->title;
            $opt_content4 = $info->content;
        }

        $this->_response_success([
            'nes_title' => $nes_title,
            'nes_content' => $nes_content,
            'opt_title1' => $opt_title1,
            'opt_content1' => $opt_content1,
            'opt_title2' => $opt_title2,
            'opt_content2' => $opt_content2,
            'opt_title3' => $opt_title3,
            'opt_content3' => $opt_content3,
            'opt_title4' => $opt_title4,
            'opt_content4' => $opt_content4,
        ]);
    }

    public function get_use_term() {
        $info = $this->db->get_where("t_terms", ['kind' => 3])->row();
        if ($info != null) {
            $this->_response_success([
                'title' => $info->title,
                'content' => $info->content,
            ]);
        } else {
            $this->_response_error(RES_ERROR_INFO_NO_EXIST);
        }
    }
}