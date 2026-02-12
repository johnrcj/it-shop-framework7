<?php
require_once APPPATH.'../application/libraries/Classes/PHPExcel.php';
require_once APPPATH.'../application/libraries/Classes/PHPExcel/IOFactory.php';
/**
 * Created by PhpStorm.
 * User: Thomas
 * Date: 2016-09-11
 * Time: 오후 9:10
 */
class Common
{
    public static $excel_title = array("A", "B", "C", "D", "E", "F", "G", "H", "I", "J", "K", "L", "M", "N", "O", "P", "Q", "R", "S", "T", "U", "V", "W", "X", "Y", "Z");

    function __construct()
    {
        date_default_timezone_set("Asia/Seoul");
    }

    public static function echoError($result_code)
    {
        $result[STR_RESULT_CODE] = $result_code;

        self::dieJson($result);
    }

    public static function echoResult($result)
    {
        self::dieJson($result);
    }

    public static function dieJson($result)
    {
        die (json_encode($result));
    }

    public static function getPageCount($size, $display_num)
    {
        if ($display_num == 0) {
            return 0;
        } else {
            return ceil($size / $display_num);
        }
    }

    public static function getOffset($page, $display_num)
    {
        if ($page > 1)
            return ($page - 1) * $display_num;
        else
            return 0;
    }

    public static function removeDir($path)
    {
        $it = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($path),
            RecursiveIteratorIterator::CHILD_FIRST
        );

        foreach ($it as $file) {
            if (in_array($file->getBasename(), array('.', '..'))) {
                continue;
            } elseif ($file->isDir()) {
                rmdir($file->getPathname());
            } elseif ($file->isFile() || $file->isLink()) {
                unlink($file->getPathname());
            }
        }

        rmdir($path);

        /**
         * Another way
         * public static function deleteDir($path) {
         *      $class_func = array(__CLASS__, __FUNCTION__);
         *      return is_file($path) ?
         * @unlink($path) :
         *      array_map($class_func, glob($path.'/*')) == @rmdir($path);
         * }
         */
    }

    public static function getFilename($type, $extension = "png")
    {
        $rand = rand(10000, 99999);
        return $type . $rand . "." . $extension;
    }

    public static function getFilePath($strType = "", $id = 0, $filename = "")
    {
        $path = "/uploads";
        if (!file_exists(Common::getServerPath($path))) {
            mkdir(Common::getServerPath($path), 0777, true);
        }

        if ($strType != "") {
            $path .= "/" . $strType;

            if (!file_exists(Common::getServerPath($path))) {
                mkdir(Common::getServerPath($path), 0777, true);
            }
        }

        if ($id != 0 && $id != "") {
            $path .= "/" . $id;

            if (!file_exists(Common::getServerPath($path))) {
                mkdir(Common::getServerPath($path), 0777, true);
            }
        }

        if ($filename != "") {
            $path .= "/" . $filename;
        }

        return $path;
    }

    public static function getServerPath($path)
    {
        $real_path = SERVER_PATH . $path;

        return $real_path;
    }


    public static function getServerUrlPath($path)
    {
        if($path == "") {
            return "";
        }

        if(self::fileExists(self::getServerPath($path))) {
            $url = SERVER_URL . $path;
        } else {
            $url = base_url()."assets/images/img_photo_default.png";
        }

        return $url;
    }

    public static function fileExists($file_path)
    {
        if (is_file($file_path) && file_exists($file_path)) {
            return true;
        }

        return false;
    }

    public static function goUrl($url, $msg = "")
    {
        echo "<script language='javascript'>";
        echo "location.href = '$url';";
        if ($msg != "")
            echo "alert('$msg');";
        echo "</script>";
    }

    public static function getDiffTime($time)
    {
        $year = substr($time, 0, 4);
        $month = substr($time, 5, 2);
        $day = substr($time, 8, 2);
        $hour = substr($time, 11, 2);
        $minute = substr($time, 14, 2);
        $second = substr($time, 17);

        $now_time = date("Y-m-d H:i:s");
        $now_year = substr($now_time, 0, 4);
        $now_month = substr($now_time, 5, 2);
        $now_day = substr($now_time, 8, 2);
        $now_hour = substr($now_time, 11, 2);
        $now_minute = substr($now_time, 14, 2);
        $now_second = substr($now_time, 17);

        $minute_ago = date("Y.m.d H:i:s", mktime($hour + 1, $minute, $second, $month, $day, $year));
        $hour_ago = date("Y.m.d H:i:s", mktime($hour, $minute, $second, $month, $day + 1, $year));
        $week_ago = date("Y.m.d H:i:s", mktime($hour, $minute, $second, $month, $day + 7, $year));
        $four_week_ago = date("Y.m.d H:i:s", mktime($hour, $minute, $second, $month, $day + 28, $year));
        $next_year = date("Y.m.d H:i:s", mktime(0, 0, 0, 1, 1, $year + 1));

        $old_time_second = strtotime($time);
        $current_time_second = strtotime($now_time);
        $result_data = "";

        $status = true;

        if ($now_time >= $next_year) {
            $result_data = $year . "." . $month . "." . $day;
            switch (date("N", mktime($hour + 1, $minute, $second, $month, $day, $year))) {
                case 1:
                    $result_data .= "(월) ";
                    break;
                case 2:
                    $result_data .= "(화) ";
                    break;
                case 3:
                    $result_data .= "(수) ";
                    break;
                case 4:
                    $result_data .= "(목) ";
                    break;
                case 5:
                    $result_data .= "(금) ";
                    break;
                case 6:
                    $result_data .= "(토) ";
                    break;
                case 7:
                    $result_data .= "(일) ";
                    break;
            }

            if ($hour < 12) {
                $result_data .= "오전 " . $hour . ":" . $minute;
            } else {
                $result_data .= "오후 " . ($hour - 12) . ":" . $minute;
            }

            $status = false;

        }

        if ($status && $now_time >= $four_week_ago && $time < $next_year) {
            $result_data = $month . "." . $day;
            switch (date("N", mktime($hour + 1, $minute, $second, $month, $day, $year))) {
                case 1:
                    $result_data .= "(월) ";
                    break;
                case 2:
                    $result_data .= "(화) ";
                    break;
                case 3:
                    $result_data .= "(수) ";
                    break;
                case 4:
                    $result_data .= "(목) ";
                    break;
                case 5:
                    $result_data .= "(금) ";
                    break;
                case 6:
                    $result_data .= "(토) ";
                    break;
                case 7:
                    $result_data .= "(일) ";
                    break;
            }

//            switch(date("A",mktime($hour + 1, $minute, $second, $month, $day, $year))){
//                case "AM":
//                    $result_data.="오전 ".$hour.":".$minute;
//                    break;
//                case "PM":
//                    $result_data.="오후 ".($hour-12).":".$minute;
//                    break;
//            }

            if ($hour < 12) {
                $result_data .= "오전 " . $hour . ":" . $minute;
            } else {
                $result_data .= "오후 " . ($hour - 12) . ":" . $minute;
            }

            $status = false;
        }

        if ($status && $now_time >= $week_ago && $now_time < $four_week_ago) {
            $result_data = ceil(($current_time_second - $old_time_second) / (3600 * 24 * 7)) . "주 전";
            $status = false;
        }

        if ($status && $now_time >= $hour_ago && $now_time < $week_ago) {
            $result_data = floor(($current_time_second - $old_time_second) / (3600 * 24)) . "일 전";
            $status = false;
        }

        if ($status && $now_time >= $minute_ago && $now_time < $hour_ago) {
            $result_data = ceil(($current_time_second - $old_time_second) / (60 * 60)) . "시간 전";
            $status = false;
        }

        if ($status && $now_time < $minute_ago) {
            $result_data = ceil(($current_time_second - $old_time_second) / 60) . "분 전";
            $status = false;
        }
        return $result_data;
    }

    public static function getCurrentTime()
    {
        return date("Y-m-d H:i:s");
    }

    public static function getArray($subject, $divider = "}{")
    {
        if (strlen($subject) > 2) {
            $subject = substr($subject, 1, strlen($subject));
            $subject = substr($subject, 0, strlen($subject) - 1);

            return explode($divider, $subject);
        } else {
            return array();
        }
    }

    public static function getRandomString($length = 6)
    {
        $characters = 'abcdefghijklmnopqrstuvwxyz0123456789';
        $charactersLength = strlen($characters);

        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }

        return $randomString;
    }

    public static function getYesterday()
    {
        return date("Y.m.d H:i:s", mktime(date("H"), date("i"), date("s"), date("m"), date("d") - 1, date("Y")));
    }

    public static function getTomorrow()
    {
        return date("Y.m.d H:i:s", mktime(date("H"), date("i"), date("s"), date("m"), date("d") + 1, date("Y")));
    }

    public static function plusOneDay($day)
    {
        $result = strtotime($day) + 60 * 60 * 24 * 1;

        return date("Y.m.d H:i:s", $result);
    }

    public static function minusOneDay($day)
    {
        $result = strtotime($day) - 60 * 60 * 24 * 1;

        return date("Y.m.d H:i:s", $result);
    }

    // 금주의 월요일
    public static function getFirstDayOfWeek()
    {
        $nOffset = date("N");

        return date("Y.m.d H:i:s", mktime(0, 0, 0, date("m"), date("d") - ($nOffset - 1), date("Y")));
    }

    // 금주의 일요일
    public static function getLastDayOfWeek()
    {
        $nOffset = date("N");

        return date("Y.m.d H:i:s", mktime(23, 59, 59, date("m"), date("d") + (7 - $nOffset), date("Y")));
    }

    // 전주의 월요일
    public static function getFirstDayOfLastWeek()
    {
        $nOffset = date("N");

        return date("Y.m.d H:i:s", time() - 60 * 60 * 24 * ($nOffset + 6));
    }

    // 전주의 일요일
    public static function getLastDayOfLastWeek()
    {
        $nOffset = date("N");

        return date("Y.m.d H:i:s", time() - 60 * 60 * 24 * $nOffset);
    }

    public static function crypt($plaintext)
    {
        return md5($plaintext);
    }

    public static function getServiceName($service)
    {
        $str_service_name = STR_SERVICE_BAR;

        switch ($service) {
            case SERVICE_BAR :
                $str_service_name = STR_SERVICE_BAR;
                break;
            case SERVICE_BEAUTY :
                $str_service_name = STR_SERVICE_BEAUTY;
                break;
            case SERVICE_CAFE :
                $str_service_name = STR_SERVICE_CAFE;
                break;
            case SERVICE_DRINK :
                $str_service_name = STR_SERVICE_DRINK;
                break;
            case SERVICE_HOSPITAL :
                $str_service_name = STR_SERVICE_HOSPITAL;
                break;
            case SERVICE_HOTEL :
                $str_service_name = STR_SERVICE_HOTEL;
                break;
            case SERVICE_SHOP :
                $str_service_name = STR_SERVICE_SHOP;
                break;
            case SERVICE_RECREATION :
                $str_service_name = STR_SERVICE_RECREATION;
                break;
        }

        return $str_service_name;
    }

    public static function getFeelName($feel)
    {
        $str_feel = STR_FEEL_UNHAPPY;

        switch ($feel) {
            case FEEL_SATISFY :
                $str_feel = STR_FEEL_SATISFY;
                break;
            case FEEL_NORMAL :
                $str_feel = STR_FEEL_NORMAL;
                break;
            case FEEL_UNHAPPY :
                $str_feel = STR_FEEL_UNHAPPY;
                break;
        }

        return $str_feel;
    }

    public static function getDayDiffTime($time){
        $old_time_second = strtotime($time);
        $current_time_second = strtotime(date("Y-m-d"));
        $result_data = floor(($old_time_second - $current_time_second) / (3600 * 24));
        if($result_data > 0){
            return $result_data;
        }else{
            return 0;
        }
    }

//    function SendMesg($url) {
//        $fp = fsockopen("211.233.20.184", 80, $errno, $errstr, 10);
//        if(!$fp) echo "$errno : $errstr";
//
//        fwrite($fp, "GET $url HTTP/1.0\r\nHost: 211.233.20.184\r\n\r\n");
//        $flag = 0;
//
//        while(!feof($fp)){
//            $row = fgets($fp, 1024);
//
//            if($flag) $out .= $row;
//            if($row=="\r\n") $flag = 1;
//        }
//        fclose($fp);
//        return $out;
//    }

    public static function getDayOfAfterDay($time,$day,$month,$year){
        $year1 = substr($time, 0, 4);
        $month1 = substr($time, 5, 2);
        $day1 = substr($time, 8, 2);

        $result = date("Y.m.d H:i:s", mktime(0, 0, 0, $month1+$month, $day1 + $day, $year1+$year));
        return $result;
    }
    
    public static function getDayofWeek($time){
        $year = substr($time, 0, 4);
        $month = substr($time, 5, 2);
        $day = substr($time, 8, 2);
        switch (date("N", mktime(0, 0, 0, $month, $day, $year))) {
            case 1:
                return"월";
                break;
            case 2:
                return"화";
                break;
            case 3:
                return"수";
                break;
            case 4:
                return"목";
                break;
            case 5:
                return"금";
                break;
            case 6:
                return"토";
                break;
            case 7:
                return"일";
                break;
        }
    }

    public static function getFirstDayOfWeekFromDate($date) {
        $week_num = date("N",strtotime($date))-1;
        $first_day = Date("Y-m-d",strtotime("-$week_num days",strtotime($date)));
        return $first_day;
    }

    public static function getLastDayOfWeekFromDate($date) {
        $week_num = 7 - date("N",strtotime($date));
        $last_day = Date("Y-m-d",strtotime("+$week_num days",strtotime($date)));
        return $last_day;
    }

    public static function getMemberNo() {
        return mt_rand(10000000,99999999);
    }

    public static function getRegType($type){
        $str = "";
        switch ($type){
            case 1:
                $str = REG_TYPE_NORMAL;
                break;

            case 2:
                $str = REG_TYPE_KAKAO;
                break;

            case 3:
                $str = REG_TYPE_FACEBOOK;
                break;

            case 4:
                $str = REG_TYPE_GOOGLE;
                break;
        }

        return $str;
    }
    
    public static function getCertStatus($status) {
        $str = "";
        switch ($status){
            case 1:
                $str = "인증";
                break;

            case 0:
                $str = "미인증";
                break;
        }

        return $str;
    }

    public static function getGender($gender)
    {
        if ($gender == 1)
            return "남";
        else
            return "여";
    }

    // 반점으로 구분된 문자로부터 checked를 얻는 함수
    public static function getCheckInStr($str, $value)
    {
        $arr = explode(",", $str);
        if (in_array($value, $arr))
            return true;
        else
            return false;
    }

    // 디비의 수자로 입력되고 반점으로 구분된 자료로부터 반점으로 구분된 문자렬을 얻는 함수
    public static function getHealthPurposeStr($val, $type)
    {
        $str = "";

        $arr = explode(",", $val);
        for ($i = 0; $i < count($arr); $i++){
            if ($type == 1) // 운동목적인경우
                $str.= Common::$health_purpose_str_arr[$arr[$i]];
            else if ($type == 2) // 병력사항인경우
                $str.= Common::$disease_info_str_arr[$arr[$i]];
            else if ($type == 3)
                $str .= Common::$health_his_str_arr[$arr[$i]];

            if ($i != count($arr)-1)
                $str.= ",";
        }

        return $str;
    }

    public static function get_category_code($uid) {
        if(strlen($uid) >= 8) {
            return $uid;
        } else {
            $str_head = "";
            for($i=0; $i<8-strlen($uid); $i++) {
                $str_head .="0";
            }
            return $str_head.$uid;
        }
    }

    public function get_privilege($val, $pos){
        return substr($val, $pos, 1);
    }

    public static function getDiffDateStr($now, $custom) {
        if($custom == "") {
            return "";
        }

        $todayObj	= new DateTime(date('Y-m-d', strtotime($now)));
        $evtEndObj	= new DateTime(date('Y-m-d',strtotime($custom)));
        $result = date_diff($todayObj, $evtEndObj);

        $return_str = "";

        if($result->y > 0) {
            $return_str .= $result->y."년 ";
        }

        if($result->m > 0) {
            $return_str .= $result->m."개월 ";
        }

        if($return_str == "") {
            $return_str = $result->d."일";
        }

        return $return_str;
    }

    public static function getLevelByPoint($point) {
        if($point > 200) {
            $level = floor($point / 100) + 10;
        } else {
            if($point > 20) {
                $level = floor($point / 20) + 2;
            } else if($point > 10) {
                $level = 2;
            } else {
                $level = 1;
            }
        }

        return $level;
    }

    public static function dumpExcelFile($title, $data, $filename){
        $objPHPExcel = new PHPExcel();

        $objPHPExcel->getProperties()->setCreator("Administrator")
            ->setLastModifiedBy("Administrator")
            ->setTitle($filename);

        for ($idx = 0; $idx < count($title); $idx++){
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue(Common::$excel_title[$idx]."1", $title[$idx]);
        }

        for ($idx = 0; $idx < count($data); $idx++){
            for ($jdx = 0; $jdx < count($title); $jdx++){
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue(Common::$excel_title[$jdx].($idx + 2), $data[$idx][$jdx]);
            }
        }

        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');

        $filepath = Common::getServerPath(Common::getFilePath("excel", 0, $filename));
        $objWriter->save($filepath);
    }

    public static function get_diff_minute($left_time, $cu_time) {
        $minute = round(($left_time - $cu_time)/60, 0);

        $str = $minute.'분';

        if ($minute >= 60) {
            $hour = floor($minute / 60);
            $minute = $minute - ($hour*60);
            $str = $hour.'시간 '.$minute.'분';
        }

        return $str;
    }
}