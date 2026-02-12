<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * Created by PhpStorm.
 * User: KGY
 * Date: 2018-01-12
 * Time: 오전 10:12
 */
class MY_Controller extends CI_Controller{
    public function __construct()
    {
        parent::__construct();
        $this->load->helper("url");
        $this->load->helper("download");
        $this->load->library("session");
        $this->load->helper("common_helper");
        $this->load->model("SSP");
        $this->load->database();
        $this->lang->load('en', 'english');
        date_default_timezone_set("Asia/Seoul");
    }

    public function load_view($view_name='',$data=array(),$flag = true){
        if($this->session->has_userdata(SESSION_MANAGER_UID) && $this->session->userdata(SESSION_MANAGER_UID) > 0) {
            $data['manager_name'] = $this->session->userdata(SESSION_MANAGER_USRID);
            if ($flag) {
                $this->load->view("layout/header", $data);
                $this->load->view($view_name, $data);
                $this->load->view("layout/footer");
            } else {
                $this->load->view($view_name, $data);
            }
        } else {
            redirect(site_url("login/index"));
        }
    }

    protected function _file_upload($dir_path, $file_name, $should_redirect = true, $redirect_url = '', $file_type = 'jpg|jpeg|png|mp4')
    {
        // $file_name 으로 올라온 파일이 없다면 빈 문짜열 귀환
        if (!isset($_FILES[$file_name]) || empty($_FILES[$file_name]['tmp_name'])) {
            return '';
        }

        $config['upload_path'] = _make_dir($dir_path);
        $config['allowed_types'] = $file_type;
        //$config['file_name'] = _unique_string();
        $config['file_name'] = $_FILES[$file_name]['name'];

        $this->load->library('upload');
        $this->upload->initialize($config);

        if ($this->upload->do_upload($file_name)) {
            return $dir_path . '/' . $this->upload->data('file_name');
        } else {
            // 파일업로드 실패라면 에러메시지귀환, 실행중지
            if ($should_redirect) {
                $this->_show_res_msg($this->upload->display_errors('', ''), 'error', '오류');
                redirect($redirect_url == '' ? base_url('member/index') : $redirect_url);
            } else {
                die (json_encode([
                    'code' => API_RES_ERR_FILE_UPLOAD,
                    'msg' => $this->upload->display_errors('', '')
                ]));
            }
        }

        return '';
    }

    public function _send_email($to, $subject, $message)
    {
        $this->load->library('email');

        $this->email->from('beaulabs.kr@gmail.com');
        $this->email->to($to);
        $this->email->subject($subject);
        $this->email->message($message);
        $ret = $this->email->send();

        return $ret;
    }

    public function get_usr_type($login_type) {
        if($login_type == 1) {
            return "KT";
        } else if($login_type == 2) {
            return "Insta";
        } else if($login_type == 3) {
            return "Email";
        } else {
            return "Email";
        }
    }

    public function get_member_status_str($status, $withdrawal_path) {
        $return_str = "";
        if($status == 1) {
            $return_str = "정상"; 
        } else if($status == 0) {
            $return_str = "차단";
        } else if($status == -1) {
            if($withdrawal_path == 1) {
                $return_str = "탈퇴(사용자)";
            } else {
                $return_str = "탈퇴(관리자)";
            }
        }
        return $return_str;
    }

    public function get_shop_status($status) {
        $return_str = "";
        if($status == 0) {
            $return_str = "신청";
        } else if($status == 1) {
            $return_str = "승인";
        } else if($status == -1) {
            $return_str = "반려";
        } else if($status == -2) {
            $return_str = "페쇄(개설자)";
        } else if($status == -3) {
            $return_str = "페새(관리자)";
        }
        return $return_str;
    }

    public function get_interest_healing_str($interest_healing) {
        $arr_healing = explode(",", $interest_healing);
        $return_str = "";
        for($i=0; $i<count($arr_healing); $i++) {
            $healing_str = "";
            switch ($arr_healing[$i]) {
                case 1:
                    $healing_str = "Eyebrow";
                    break;
                case 2:
                    $healing_str = "Eyelashes";
                    break;
                case 3:
                    $healing_str = "Eye line/Lips";
                    break;
                case 4:
                    $healing_str = "HairLine/SMP";
                    break;
                case 5:
                    $healing_str = "ETC";
                    break;
                default:
                    break;
            }
            if($return_str == "") {
                $return_str = $healing_str;
            } else {
                $return_str .= ",".$healing_str;
            }
        }

        return $return_str;
    }

    public function get_artist_request_inflow_path($inflow_path) {
        $return_str = "";
        switch ($inflow_path) {
            case 1:
                $return_str = "마이페이지";
                break;
            case 2:
                $return_str = "Shop상세";
                break;
            default:
                break;
        }
        return $return_str;
    }

    public function report_reason($reason) {
        $return_str = "";
        switch ($reason) {
            case 1:
                $return_str = "Commerical Purpose/promotion";
                break;
            case 2:
                $return_str = "False Info";
                break;
            case 3:
                $return_str = "Repeat posting of the same content";
                break;
            case 4:
                $return_str = "Etc";
                break;
            default:
                break;
        }
        return $return_str;
    }

    public function get_nickname_format($nickname) {
        if(count($nickname) <= 5) {
            return $nickname;
        } else {
            $return_str = "";
            $return_str = substr($nickname, 0, 5);
            for($i=0; $i<count($nickname) - 5; $i++) {
                $return_str .= "*";
            }

            return $return_str;
        }
    }

    public function _response_success($data = array())
    {
        return $this->_make_response(API_RES_SUCCESS, 'Success', $data);
    }

    public function _response_error($error_code, $msg = '')
    {
        if (empty($msg)) {
            switch ($error_code) {
                default:
                case API_RES_SUCCESS:
                    $msg = 'Success';
                    break;
                case API_RES_ERR_UNKNOWN:
                    $msg = 'Server error';
                    break;
                case API_RES_ERR_PARAMETER:
                    $msg = 'Parameter error ';
                    break;
                case API_RES_ERR_DB:
                    $msg = 'Database error';
                    break;
                case API_RES_ERR_INFO_NO_EXIST:
                    $msg = 'No Exist';
                    break;
                case API_RES_ERR_DUPLICATE:
                    $msg = 'Already exists';
                    break;
                case API_RES_ERR_PRIVILEGE:
                    $msg = 'Improper privilege error';
                    break;
                case API_RES_ERR_INCORRECT:
                    $msg = 'Verification code is not valid';
                    break;
                case API_RES_ERR_FILE_UPLOAD:
                    $msg = 'File upload error';
                    break;
            }
        } /*else if ($error_code == API_RESULT_ERROR_PARAMETER) {
            $msg = 'Parameter error (' . $msg . ')';
        }*/

        return $this->_make_response($error_code, $msg, array());
    }

    private function _make_response($code, $msg, $data)
    {
        $response = [
            'code' => $code,
            'msg' => $msg
        ];

        foreach ($data as $key => $value) {
            $response[$key] = $value;
        }

        if (isset($_REQUEST['pretty']) && $_REQUEST['pretty'] == 'TRUE') {
            die ('<textarea readonly style="width: 100%; height: 99%; border: none; resize: none">' . json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . '</textarea>');
        } else {
            die (json_encode($response));
        }
    }

    public function get_part_list($str_part) {
        $arr_part = explode(",", $str_part);
        $return_arr = array();

        foreach ($arr_part as $item) {
            if($item != "") {
                array_push($return_arr, $this->get_part_name($item));
            }
        }

        return $return_arr;
    }

    public function get_part_name($part) {
        switch (intval($part)) {
            case 1:
                return $this->lang->line("eye");
                break;
            case 2:
                return $this->lang->line("sleep");
                break;
            case 3:
                return $this->lang->line("fat_grafting");
                break;
            case 4:
                return $this->lang->line("contour");
                break;
            case 5:
                return $this->lang->line("chest");
                break;
            case 6:
                return $this->lang->line("lifting");
                break;
            case 7:
                return $this->lang->line("filter");
                break;
            case 8:
                return $this->lang->line("liposuction");
                break;
            case 9:
                return $this->lang->line("dentist");
                break;
            case 10:
                return $this->lang->line("skin");
                break;
            case 11:
                return $this->lang->line("hair_transplant");
                break;
            case 12:
                return $this->lang->line("other");
                break;
            default:
                return "";
                break;
        }
    }
}