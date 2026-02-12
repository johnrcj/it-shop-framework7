<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class MY_Controller extends CI_Controller
{

    /**
     * Determine that the page requires login.
     * If false, the page doesn't need login.
     */
    protected $isCheckPrivilegeController = true;
    /**
     * Save login user's profile now.
     */
    protected $g_manager = null;
    protected $g_manager_privilege = null;
    /**
     * A list of models to be auto-loaded
     */
    protected $models = [];
    /**
     * A formatting string for the model auto-loading feature.
     * The percent symbol (%) will be replaced with the model name.
     */
    protected $model_string = '%';
    /**
     * A list of helpers to be auto-loaded
     */
    protected $helpers = [];
    /**
     * A list of libraries to be auto-loaded
     */
    protected $libraries = [];

    /**
     * Error info
     */
    private $error_title = 'Notice';
    private $error_class = 'success';
    private $error_msg = '';
    private $error_flag = false;

    /**
     * Page css
     * @var array css
     */
    protected $css = [];

    public $latitude = 0;
    public $longitude = 0;

    /*
 * Page Title
 * */
    protected $page_title = '';
    protected $page_route = array();
    protected $page_level_css = array();
    protected $page_level_js = array();
    protected $page_active_menu = '';

    public function __construct()
    {
        parent::__construct();

        $this->_load_models();
        $this->_load_helpers();
        $this->_load_libraries();

        $this->load->helper('common_helper');
        $this->lang->load('en', 'english');
        $this->load->database();

//		$this->load->model('MFile');

//		if (!$this->isCheckPrivilegeController)
//			return;

//		if (!$this->session->has_userdata('g_manager')) {
//			if (!$this->input->is_ajax_request()) {
//				redirect('login');
//			} else {
//				die('Please login.');
//			}
//		}
//
//		$this->g_manager = $this->session->userdata('g_manager');
    }

    /* --------------------------------------------------------------
     * MODEL LOADING
     * ------------------------------------------------------------ */

    /**
     * Load models based on the $this->models array
     */
    private function _load_models()
    {
        foreach ($this->models as $model) {
            $this->load->model($this->_model_name($model));
        }
    }

    /**
     * Returns the loadable model name based on
     * the model formatting string
     *
     * @param String $model model name to load
     * @return String
     */
    protected function _model_name($model)
    {
        return str_replace('%', $model, $this->model_string);
    }

    protected function getMe()
    {
        if (!$this->session->has_userdata(SESSION_IND))
            return false;
        return $this->session->userdata(SESSION_IND);
    }

    /* --------------------------------------------------------------
     * HELPER LOADING
     * ------------------------------------------------------------ */

    /**
     * Load helpers based on the $this->helpers array
     */
    private function _load_helpers()
    {
        foreach ($this->helpers as $helper) {
            $this->load->helper($helper);
        }
    }

    /* --------------------------------------------------------------
     * LIBRARY LOADING
     * ------------------------------------------------------------ */

    /**
     * Load libraries based on the $this->libraries array
     */
    private function _load_libraries()
    {
        foreach ($this->libraries as $library) {
            $this->load->library($library);
        }
    }

    /**
     * File upload method
     * Save uploaded file to destination folder
     * If succeeded in saving, return file name.
     *
     * @param string $dir_path upload folder path
     * @param string $file_name upload file name
     * @param boolean $should_redirect If uploading is failed, determine to return redirect // false : return json in response to api request
     * @param string $redirect_url url to return when uploading fails
     * @param string $file_type upload file type
     * @return string
     */

    protected function _file_upload($dir_path, $file_name, $should_redirect = true, $redirect_url = '', $file_type = 'jpg|jpeg|png')
    {
        // The uploaded file with $file_name no exist, return empty string.
        if (!isset($_FILES[$file_name]) || empty($_FILES[$file_name]['tmp_name'])) {
            return '';
        }

        $config['upload_path'] = _make_dir($dir_path);
        $config['allowed_types'] = $file_type;
        $config['file_name'] = _unique_string();

        $this->load->library('upload', $config);

        if ($this->upload->do_upload($file_name)) {
            return $dir_path . "/" . $this->upload->data('file_name');
        } else {
            // If file uploading fails, stop running
            if ($should_redirect) {
                $this->_show_res_msg($this->upload->display_errors('', ''), 'error', 'Error');
                redirect($redirect_url == '' ? base_url('member/index') : $redirect_url);
            } else {
                die (json_encode([
                    'code' => 1,
                    'msg' => $this->upload->display_errors('', '')
                ]));
            }
        }

        return '';
    }

    /**
     * Multiple file uploading method
     * Parameter description equals the above method's one.
     *
     * @param string $dir_path
     * @param $file_name
     * @param bool $should_redirect
     * @param string $redirect_url
     * @param string $file_type
     * @return array
     */
    protected function _multi_file_upload($dir_path, $file_name, $should_redirect = true, $redirect_url = '', $file_type = 'jpg|jpeg|png')
    {
        $files = [];

        // The uploaded file with $file_name no exist, return empty string.
        if (empty($_FILES[$file_name]) || count($_FILES[$file_name]['name']) < 1) {
            return $files;
        }

        $this->load->library('upload');

        $config['upload_path'] = _make_dir($dir_path);
        $config['allowed_types'] = $file_type;

        for ($nInd = 0; $nInd < count($_FILES[$file_name]['name']); $nInd++) {
            if (!empty($_FILES[$file_name]['name'][$nInd])) {
                $config['file_name'] = _unique_string();
                $this->upload->initialize($config);

                $_FILES['server_upload_file']['name'] = is_array($_FILES[$file_name]['name']) ? $_FILES[$file_name]['name'][$nInd] : $_FILES[$file_name]['name'];
                $_FILES['server_upload_file']['type'] = is_array($_FILES[$file_name]['type']) ? $_FILES[$file_name]['type'][$nInd] : $_FILES[$file_name]['type'];
                $_FILES['server_upload_file']['tmp_name'] = is_array($_FILES[$file_name]['tmp_name']) ? $_FILES[$file_name]['tmp_name'][$nInd] : $_FILES[$file_name]['tmp_name'];
                $_FILES['server_upload_file']['error'] = is_array($_FILES[$file_name]['error']) ? $_FILES[$file_name]['error'][$nInd] : $_FILES[$file_name]['error'];
                $_FILES['server_upload_file']['size'] = is_array($_FILES[$file_name]['size']) ? $_FILES[$file_name]['size'][$nInd] : $_FILES[$file_name]['size'];

                if ($this->upload->do_upload('server_upload_file')) {
                    array_push($files, $dir_path . DIRECTORY_SEPARATOR . $this->upload->data('file_name'));
                } else {
                    // If file uploading fails, stop running
                    if ($should_redirect) {
                        $this->_show_res_msg($this->upload->display_errors('', ''), 'error', 'Error');
                        redirect($redirect_url == '' ? base_url('member/index') : $redirect_url);
                    } else {
                        die (json_encode([
                            'code' => 1,
                            'msg' => $this->upload->display_errors('', '')
                        ]));
                    }
                }
            }
        }

        return $files;
    }

    protected function view_detail($template_name, $main_data = [])
    {

//		$data['main'] = $this->load->view($template_name, $main_data, true);
//
//		if ($this->session->has_userdata('error')) {
//			$data['error'] = $this->session->userdata('error');
//			$this->session->unset_userdata('error');
//		} else {
//			$data['error'] = array('error_flag' => false);
//		}
//
//		$data['css_list'] = $this->css;

        $this->load->view($template_name, $main_data);
    }

    public function load_ajaxview($main_v_path, $main_v_param = array())
    {
        $this->load->view($main_v_path, $main_v_param);
    }

    protected function _show_res_msg($res_msg = '', $res_class = 'success', $res_title = 'Notice')
    {
        $this->error_flag = true;
        $this->error_msg = $res_msg;
        $this->error_class = $res_class;
        $this->error_title = $res_title;

        $error = array(
            'error_flag' => $this->error_flag,
            'error_msg' => $this->error_msg,
            'error_class' => $this->error_class,
            'error_title' => $this->error_title,
        );

        $this->session->set_tempdata('error', $error, 30); //Save error message while 30s.
    }

    protected function _set_temp_value($value)
    {
        $this->session->set_flashdata('wiz_temp', $value);
    }

    protected function _get_temp_value()
    {
        return $this->session->flashdata('wiz_temp');
    }

    public function _send_email($to, $subject, $message)
    {
        $this->load->library('email');

        $this->email->from('winpeech@gmail.com');
        $this->email->to($to);
        $this->email->subject($subject);
        $this->email->message($message);
        $ret = $this->email->send();

        return $ret;
    }

    public function param_get($param)
    {
        return $this->quote_smart($this->input->get($param));
    }

    public function param_post($param)
    {
        return $this->quote_smart($this->input->post($param));
    }

    public function quote_smart($value)
    {
        // remove stripsladhes(/).
        if (get_magic_quotes_gpc()) {
            $value = stripslashes($value);
        }
        // Quote if not integer:
        $value = htmlspecialchars($value, ENT_QUOTES);
        $value = stripslashes($value);
        if (!is_numeric($value)) {
            //$value=mysql_real_escape_string($value);
        }

        return $value;
    }

    public function send_push($p_fcm_token, $p_type, $p_msg, $p_data)
    {
        if (count($p_fcm_token) > 999) {
            $cnt = intval(count($p_fcm_token) / 1000);
            for ($i = 0; $i < $cnt; $i++) {
                $subTokens = array();
                for ($j = 0; $j < 1000; $j++) {
                    array_push($subTokens, $p_fcm_token[$i * 1000 + $j]);
                }
                $this->send_push1($subTokens, $p_type, $p_msg, $p_data);
            }

            $subTokens = array();
            for ($j = $cnt * 1000; $j < count($p_fcm_token); $j++) {
                array_push($subTokens, $p_fcm_token[$j]);
            }
            return $this->send_push1($subTokens, $p_type, $p_msg, $p_data);
        } else {

            $subTokens = array($p_fcm_token);
            return $this->send_push1($p_fcm_token, $p_type, $p_msg, $p_data);
        }
    }

    public function send_push1($p_fcm_token, $p_type, $p_msg, $p_data)
    {
        $server_key = "AAAAfhRI71o:APA91bFcRCR7AQpAb7xQNiY5Iza5VRfnuyA6BEXE87p8vbUIMNLQuzDTPIgMiWCR5pA5I8yQjlbZOUYMgGdx1Yl01y_l4ZHoaB9rXv2ylW6PVXG0VON3EmYlYYd4BeeBmvZwa52lE4Gz";
        $data = array(
            'type' => $p_type,
            'message' => $p_msg,
            'data' => $p_data
        );

        //Creating the notification array.
        $notification = array('title' => 'AgainSchool', 'type' => $p_type, 'text' => $p_msg, 'data' => $p_data);

        $fields = array(
            'data' => $data,
//            'to' => $p_fcm_token,
            'registration_ids' => $p_fcm_token,
            // for iOS
            'notification' => $notification,
            'priority' => 'high'
        );

        //header with content_type api key
        $headers = array(
            'Authorization:key=' . $server_key,
            'Content-Type:application/json'
        );

        //CURL request to route notification to FCM connection server (provided by Google)
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
        $result = curl_exec($ch);
        if ($result === FALSE) {
            return $this->lang->line("message_send_fail");
            //die('Oops! FCM Send Error: ' . curl_error($ch));
        }
        curl_close($ch);
        return $this->lang->line("message_send_success");
    }

//	public function _send_push($device, $tokens, $data)
//	{
//		$url = 'https://fcm.googleapis.com/fcm/send';
//
//        $fields['to'] = $tokens;
//        if ($device == 1) {
//            $f_key = 'data';
//        } else {
//            $f_key = 'notification';
//        }
//        $fields[$f_key] = array(
//            'title' => $data['title'],
//            'body' => $data['body'],
//            'sound' => 'default',
//            'badge' => 0,
//            'content_available' => true
//        );
//
//        if ($data != null) {
//            $fields[$f_key] = array_merge($fields[$f_key], $data);
//        }
//
//        $this->curl_request_async($url, $fields);
//
//        return $fields;
//	}

    function curl_request_async($url, $params, $type = 'POST')
    {
        $google_api_key = "AAAAN7Nuj6c:APA91bEZrztBeQxIbGhpCggxhPMmfC0PDiH8PFbSKfcP4m6cPfIZQ4r3IqRRp3r6k6nQLevgm9qkVByJz_1pOepxiBDxIA8yLzyBYm8xjqPeWQLU4mlJeME7miJcUurjLi7r9awgJutJ";
        $post_string = json_encode($params);

        $parts = parse_url($url);
        if ($parts['scheme'] == 'http') {
            $fp = fsockopen($parts['host'], isset($parts['port']) ? $parts['port'] : 80, $errno, $errstr, 30);
        } else if ($parts['scheme'] == 'https') {
            $fp = fsockopen("ssl://" . $parts['host'], isset($parts['port']) ? $parts['port'] : 443, $errno, $errstr, 30);
        }

        // Data goes in the path for a GET request
        if ('GET' == $type)
            $parts['path'] .= '?' . $post_string;

        $out = "$type " . $parts['path'] . " HTTP/1.1\r\n";
        $out .= "Host: " . $parts['host'] . "\r\n";
        $out .= "Authorization: key=" . $google_api_key . "\r\n";
        $out .= "Content-Type: application/json\r\n";
        $out .= "Content-Length: " . strlen($post_string) . "\r\n";
        $out .= "Connection: Close\r\n\r\n";
        // Data goes in the request body for a POST request
        if ('POST' == $type && isset($post_string))
            $out .= $post_string;

        fwrite($fp, $out);
        fclose($fp);
    }

    public function getUniqueString($ext = '')
    {
        $returnVar = "" . round(microtime(true) * 1000) . rand(1000, 9999);
        if (!empty($ext)) {
            $returnVar .= "." . $ext;
        }
        return $returnVar;
    }

    public function getDistance($latitude1, $longitude1, $latitude2, $longitude2)
    {

        if (($latitude1 == $latitude2) && ($longitude1 == $longitude2)) {
            return 0;
        } // distance is zero because they're the same point

        $p1 = deg2rad($latitude1);
        $p2 = deg2rad($latitude2);
        $dp = deg2rad($latitude2 - $latitude1);
        $dl = deg2rad($longitude2 - $longitude1);
        $a = (sin($dp / 2) * sin($dp / 2)) + (cos($p1) * cos($p2) * sin($dl / 2) * sin($dl / 2));
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        $r = 6371008; // Earth's average radius, in meters
        $d = $r * $c;

        return $d; // distance, in meters
    }


    public function getAlphanumericRandomString($length)
    {
        $permitted_chars = '0123456789abcdefghijklmnopqrstuvwxyz';
        return substr(str_shuffle($permitted_chars), 0, $length);
    }

    /**
     * Method returning result about server requesting.
     *
     * @param integer $error_code
     * @param string $msg
     * @return array
     */
    public function _response_error($error_code, $msg = '', $data = array())
    {
        if (empty($msg)) {
            switch ($error_code) {
                default:
                case RES_SUCCESS:
                    $msg = 'Success';
                    break;
                case RES_ERROR_UNKNOWN:
                    $msg = 'Server error';
                    break;
                case RES_ERROR_PARAMETER:
                    $msg = 'Parameter error ';
                    break;
                case RES_ERROR_DB:
                    $msg = 'Database error';
                    break;
                case RES_ERROR_INFO_NO_EXIST:
                    $msg = 'No Exist';
                    break;
                case RES_ERROR_DUPLICATE:
                    $msg = 'Already exists';
                    break;
                case RES_ERROR_PRIVILEGE:
                    $msg = 'Improper privilege error';
                    break;
                case RES_ERROR_INCORRECT:
                    $msg = 'Verification code is not valid';
                    break;
                case RES_ERROR_FILE_UPLOAD:
                    $msg = 'File upload error';
                    break;
                case RES_ERROR_EMAIL_DUP:
                    $msg = 'Email duplicate error';
                    break;
                case RES_ERROR_PHONE_DUP:
                    $msg = 'Phone duplicate error';
                    break;
                case RES_ERROR_INCORRECT_EMAIL:
                    $msg = 'Email is incorrect.';
                    break;
                case RES_ERROR_INCORRECT_PWD:
                    $msg = 'Pwd is incorrect.';
                    break;
                case RES_ERROR_USR_BLOCK:
                    $msg = 'Block user error.';
                    break;
                case RES_ERROR_USR_EXIT:
                    $msg = 'Withdrawal user error.';
                    break;
                case RES_ERROR_NO_SESSION:
                    $msg = 'No session';
                    break;
                case RES_ERROR_VOUCHER_DUP:
                    $msg = 'Voucher duplication error.';
                    break;
                case RES_ERROR_RECOGNITION:
                    $msg = 'Voucher recognition error.';
                    break;
            }
        } /*else if ($error_code == RESULT_ERROR_PARAMETER) {
            $msg = 'Parameter error (' . $msg . ')';
        }*/

        return $this->_make_response($error_code, $msg, $data);
    }

    public function _response_success($data = array())
    {
        return $this->_make_response(RES_SUCCESS, 'Success', $data);
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

    /**
     * Load view with layout
     * */
    public function load_view($path, $vars = array())
    {
        $html = $this->load->view($path, $vars, true);

        $data = array(
            'html_body' => $html,
            'page_title' => $this->page_title,
            'page_route' => $this->page_route,
            'page_level_css' => $this->page_level_css,
            'page_level_js' => $this->page_level_js,
            'page_active_menu' => $this->page_active_menu
        );

        $this->load->view('backend/layout', $data);
    }

    public function get_datatable_req_param()
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
            return false;
        }

        $params = $this->input->get();
        $size = $this->input->get('length');
        $page = ($this->input->get('start') / $size) + 1;

        $params['size'] = $size;
        $params['page'] = $page;

        return $params;
    }

    protected function print_datatable_data($count, $data)
    {
        $json_data = array(
            "draw" => intval($this->input->get('draw')),
            "recordsTotal" => intval($count),
            "recordsFiltered" => intval($count),
            "data" => $data
        );

        die(json_encode($json_data));
    }

    protected function create_datatable_item($object, $params)
    {
        $new_object = array();
        foreach ($params as $k => $param) {
            if (!isset($object[$param])) {
                $new_object[$param] = $new_object[$k] = '';
            } else {
                $new_object[$param] = $new_object[$k] = $object[$param];
            }

        }
        return $new_object;
    }

    public function delete_user_info($ind)
    {
        $user_info = $this->db->select('*')
            ->where('ind', $ind)
            ->get('user')->row();
        if (is_null($user_info)) {
            $this->_response_error(RES_ERROR_INFO_NO_EXIST);
        }

        $this->db->where('user', $ind);
        $this->db->or_where('company', $ind);
        if (!$this->db->delete('company_like')) {
            $this->_response_error(RES_ERROR_DB);
        }

        $sql = <<<EOT
        delete R from review R left join const_apply A on R.const_apply = A.ind where R.user = $ind or A.user = $ind
EOT;
        if (!$this->db->query($sql)) {
            $this->_response_error(API_RES_ERROR_DB);
        }

        $this->db->where('user', $ind);
        if (!$this->db->delete('const_apply')) {
            $this->_response_error(RES_ERROR_DB);
        }

        $this->db->where('user', $ind);
        if (!$this->db->delete('find_job')) {
            $this->_response_error(RES_ERROR_DB);
        }

        $this->db->where('user', $ind);
        if (!$this->db->delete('find_worker')) {
            $this->_response_error(RES_ERROR_DB);
        }

        $this->db->where('ind', $ind);
        if (!$this->db->delete('user')) {
            $this->_response_error(RES_ERROR_DB);
        }
    }

    /**
     */
    public function check_duplicate_login()
    {
        $device_id = isset($_REQUEST['device_id']) ? $_REQUEST['device_id'] : "";
        $logon_user = isset($_REQUEST['logon_user']) ? $_REQUEST['logon_user'] : "";

        $user_info = $this->db->select('*')
            ->where('ind', $logon_user)
            ->get('user')->row();
        if (is_null($user_info)) {
            $this->_response_error(RES_ERROR_INFO_NO_EXIST);
        }

        if ((int)$user_info->is_allow_multi_devices == 1) {
            return;
        } else {
            if ($user_info->logon_device_id != $device_id) {
                $this->_response_error(RES_ERROR_NO_SESSION);
            }
        }
    }

    function send_sms($phone, $msg, $reason)
    {
        $userid = 'hmss4217';
        $passwd = 'wjdtnqls4217';
        $hpSender = '01055304217';
        $hpReceiver = $phone;

        //$hpMesg = $msg;
        $hpMesg = iconv("UTF-8", "EUC-KR", $msg);

        $hpMesg = urlencode($hpMesg);
        $endAlert = 0;  // 전송완료알림창 ( 1:띄움, 0:안띄움 )

        // 한줄로 이어쓰기 하세요.
        $send_result = $this->sendSMSMsg("/MSG/send/web_admin_send.htm?userid=$userid&passwd=$passwd&sender=$hpSender&receiver=$hpReceiver&encode=1&end_alert=$endAlert&message=$hpMesg&allow_mms=1");

        return $send_result;
    }

    public function sendSMSMsg($url)
    {
        $fp = fsockopen("211.233.20.184", 80, $errno, $errstr, 10);
        if (!$fp) echo "$errno : $errstr";

        fwrite($fp, "GET $url HTTP/1.0\r\nHost: 211.233.20.184\r\n\r\n");
        $flag = 0;

        $out = '';
        while (!feof($fp)) {
            $row = fgets($fp, 1024);

            if ($flag) $out .= $row;
            if ($row == "\r\n") $flag = 1;
        }
        fclose($fp);

        return $out;
    }
}
