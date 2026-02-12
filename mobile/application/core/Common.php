<?php

/**
 * Created by PhpStorm.
 * User: Gambler
 * Date: 2016-09-11
 * Time: 오후 9:10
 */
class Common
{
    function __construct()
    {
        date_default_timezone_set("Asia/Seoul");
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
        $path = "upload";
        if (!file_exists(Common::getServerPath($path))) {
            mkdir(Common::getServerPath($path), 0777, true);
        }

        if ($strType != "") {
            $path .= "/" . $strType;

            if (!file_exists(Common::getServerPath($path))) {
                mkdir(Common::getServerPath($path), 0777, true);
            }
        }

        if ($id != 0) {
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
        if(self::fileExists(self::getServerPath($path))) {
            $url = UPLOAD_URL . $path;
        } else {
            $url = base_url()."assets/images/img_default.png";
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

    // 반점으로 구분된 문자로부터 checked를 얻는 함수
    public static function getCheckInStr($str, $value)
    {
        $arr = explode(",", $str);
        if (in_array($value, $arr))
            return true;
        else
            return false;
    }

    public function get_privilege($val, $pos){
        return substr($val, $pos, 1);
    }

    public static function get_encrypt_nickname($nickname){
        $return_str = "";
        if(mb_strlen($nickname) >= 3) {
            $return_str = substr($nickname, 0,3);
            for($i=0; $i<mb_strlen($nickname) - 3; $i++) {
                $return_str = $return_str."*";
            }
        } else {
            $return_str = $nickname;
        }
        return $return_str;
    }

    public static function get_event_request_point($price){
        if($price < 100000)
            return 17000;

        if($price >= 100000 && $price < 300000)
            return 20000;

        if($price >= 300000 && $price < 500000)
            return 25000;

        if($price >= 500000 && $price < 1000000)
            return 30000;

        if($price >= 1000000 && $price < 1500000)
            return 35000;

        if($price >= 1500000 && $price < 2000000)
            return 40000;

        if($price >= 2000000 && $price < 3000000)
            return 45000;

        if($price >= 3000000 && $price < 4000000)
            return 50000;

        if($price >= 4000000 && $price < 5000000)
            return 55000;

        return 60000;
    }
}
