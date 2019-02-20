<?php
if (!function_exists('ShowDebug')) {
    function ShowDebug($object) {
        echo 'Debug Information:';
        echo '<pre>';
        print_r($object);
        echo '</pre>';
    }
}
/**錯誤跳轉**/
if (!function_exists('GoShowMsg')) {
    function GoShowMsg($Message, $Url = '') {
        if (trim($Message) != "") {
            header("content-type: text/html; charset=utf-8");
            echo "<script>";
            echo "alert('" . $Message . "');";
            if ($Url == '') {
                echo "history.back(-1);";
            } else {
                echo "location.href = '" . $Url . "';";
            }

            echo "</script>";
            exit;
        } elseif (trim($Url) != "") {
            header("content-type: text/html; charset=utf-8");
            echo "<script>";
            if ($Url == '') {
                echo "history.back(-1);";
            } else {
                echo "location.href = '" . $Url . "';";
            }

            echo "</script>";
            exit;
        }
    }
}
if (!function_exists('GenerAtorNum')) {
    function GenerAtorNum($password_len = 7) {
        $password = '';

        // remove o,0,1,l
        $word = '0123456789';
        $len = strlen($word);

        for ($i = 0; $i < $password_len; $i++) {
            $password .= $word[rand() % $len];
        }

        return $password;
    }
}
if (!function_exists('GenerAtorEng')) {
    function GenerAtorEng($password_len = 7) {
        $password = '';

        // remove o,0,1,l
        $word = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $len = strlen($word);

        for ($i = 0; $i < $password_len; $i++) {
            $password .= $word[rand() % $len];
        }

        return $password;
    }
}
if (!function_exists('GenerAtorMoreEng')) {
    function GenerAtorMoreEng($password_len = 7) {
        $password = '';

        // remove o,0,1,l
        $word = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $len = strlen($word);

        for ($i = 0; $i < $password_len; $i++) {
            $password .= $word[rand() % $len];
        }

        return $password;
    }
}
#返回幾天前的日期
if (!function_exists('re_day')) {
    function re_day($date = '', $day = 0) {
        $date = (empty($date)) ? date('Y-m-d 00:00:00') : $date;
        return date('Y-m-d', strtotime($date . '-' . $day . ' day'));
    }
}
#返回幾天後的日期
if (!function_exists('add_day')) {
    function add_day($date = '', $day = 0) {
        $date = (empty($date)) ? date('Y-m-d 00:00:00') : $date;
        return date('Y-m-d', strtotime($date . '+' . $day . ' day'));
    }
}
#返回幾小時前的日期
if (!function_exists('re_hour')) {
    function re_hour($date = '', $hour = 0) {
        $date = (empty($date)) ? date('Y-m-d H:i:s') : $date;
        return date('Y-m-d H:i:s', strtotime($date . '-' . $hour . ' hour'));
    }
}
#返回幾小時後的日期
if (!function_exists('add_hour')) {
    function add_hour($date = '', $hour = 0) {
        $date = (empty($date)) ? date('Y-m-d H:i:s') : $date;
        return date('Y-m-d H:i:s', strtotime($date . '+' . $hour . ' hour'));
    }
}
#返回幾秒後的日期
if (!function_exists('add_time')) {
    function add_time($date = '', $seconds = 0) {
        $date = (empty($date)) ? date('Y-m-d H:i:s') : $date;
        return date('Y-m-d H:i:s', strtotime($date . '+' . $seconds . ' seconds'));
    }
}
#驗證日期是否正確
if (!function_exists('verifyDate')) {
    function verifyDate($date, $strict = true) {
        $dateTime = DateTime::createFromFormat('Y-m-d H:i:s', $date);
        if ($strict) {
            $errors = DateTime::getLastErrors();
            if (!empty($errors['warning_count'])) {
                return false;
            }
        }
        return $dateTime !== false;
    }
}
#取得起始日到終止日的天數
if (!function_exists('ps_date_diff')) {
    function ps_date_diff($beginDate = "", $endDate = "") {
        $_beginDate = strtotime($beginDate);
        log_message('error', 'beginDate: ' . $_beginDate);
        $_endDate = strtotime($endDate);
        log_message('error', 'endDate: ' . $_endDate);
        $_diff = abs($_beginDate - $_endDate);
        return round(($_diff) / 3600 / 24);
    }
}
#取得起始日到終止日的天數
if (!function_exists('ps_time_diff')) {
    function ps_time_diff($beginDate = "", $endDate = "") {
        $_beginDate = strtotime($beginDate);
        $_endDate = strtotime($endDate);
        $_diff = abs($_beginDate - $_endDate);
        return round($_diff);
    }
}
#登入狀態確認並強置反彈
if (!function_exists('loginCheck')) {
    function loginCheck($userData = []) {
        if (!empty($userData)) {
            return true;
        } else {
            $data = [
                'error_code' => 8001,
                'message' => 'Not Login',
            ];
            header('Content-Type: application/json');
            print_r(json_encode($data));
            exit();
        }
    }
}
#吐回Json格式資料
if (!function_exists('json_respon')) {
    function json_respon($data, $code = 200) {
        if ($code == 0) {
            http_response_code(200);
        } else {
            http_response_code($code);
        }
        header('Content-Type: application/json');
        print_r(json_encode($data));
    }
}
#判斷瀏覽器
if (!function_exists('my_get_browser')) {
    function my_get_browser($HTTP_USER_AGENT = '') {
        if (empty($HTTP_USER_AGENT)) {
            return '命令行，机器人来了！';
        }
        if (false !== strpos($HTTP_USER_AGENT, 'MSIE 9.0')) {
            return 'Internet Explorer 9.0';
        }
        if (false !== strpos($HTTP_USER_AGENT, 'MSIE 8.0')) {
            return 'Internet Explorer 8.0';
        }
        if (false !== strpos($HTTP_USER_AGENT, 'MSIE 7.0')) {
            return 'Internet Explorer 7.0';
        }
        if (false !== strpos($HTTP_USER_AGENT, 'MSIE 6.0')) {
            return 'Internet Explorer 6.0';
        }
        if (false !== strpos($HTTP_USER_AGENT, 'Firefox')) {
            return 'Firefox';
        }
        if (false !== strpos($HTTP_USER_AGENT, 'Chrome')) {
            return 'Chrome';
        }
        if (false !== strpos($HTTP_USER_AGENT, 'Safari')) {
            return 'Safari';
        }
        if (false !== strpos($HTTP_USER_AGENT, 'Opera')) {
            return 'Opera';
        }
        if (false !== strpos($HTTP_USER_AGENT, '360SE')) {
            return '360SE';
        }
    }
}
#取得POST
if (!function_exists('getPost')) {
    function getPost() {
        $json_string = file_get_contents('php://input');
        $postData = json_decode($json_string, 1);
        return $postData;
    }
}
#讀取指定目錄下的檔案
if (!function_exists('read_path_files')) {
    function read_path_files($_Path = '') {
        if (is_dir($_Path)) {
            $files = [];
            if ($dh = opendir($_Path)) {
                $i = 0;
                while (($file = readdir($dh)) !== false) {
                    if ($file != "." && $file != "..") {
                        if (is_dir($_Path . '/' . $file)) {
                            // echo "### 處理目錄: " . $file . PHP_EOL;
                            // echo "### 偵測到目錄: " . $_Path . '/' . $file . PHP_EOL;
                            $files[$i]['path'] = read_path_files($_Path . '/' . $file);
                            $files[$i]['path_name'] = $file;
                        } else {
                            // echo "### 處理檔案: " . $file . PHP_EOL;
                            // echo "### 偵測到檔案: " . $file . PHP_EOL;
                            $files[$i]["file_path"] = $_Path . '/' . $file;
                            $files[$i]["name"] = $file; //获取文件名称
                            $files[$i]["size"] = round((filesize($_Path . '/' . $file) / 1024), 2); //获取文件大小
                            $files[$i]["time"] = date("Y-m-d H:i:s", filemtime($_Path . '/' . $file)); //获取文件最近修改日期
                        }
                        $i++;
                    }
                }
            }
            closedir($dh);
            // if (empty($files[$i]['path_name'])) {
            foreach ($files as $k => $v) {
                if (!empty($v['name'])) {
                    $size[$k] = $v['size'];
                    $time[$k] = $v['time'];
                    $name[$k] = $v['name'];
                }
            }
            // array_multisort($time, SORT_DESC, SORT_STRING, $files); //按时间排序
            if (!empty($name)) {
                array_multisort($name, SORT_ASC, SORT_STRING, $files); //按名字排序
            }
            //array_multisort($size,SORT_DESC,SORT_NUMERIC, $files);//按大小排序
            // }

            return $files;
        }
        return [];
    }
}
#傳送訊息到 Slack
if (!function_exists('log_forslack')) {
    function log_forslack($msg = "", $channel = "production") {

        if (ENVIRONMENT == 'production') {
            $webhookUrl = 'https://hooks.slack.com/services/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';
        } else {
            // $webhookUrl = 'https://hooks.slack.com/services/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';
        }

        if (!empty($webhookUrl)) {
            $msgStr = ENVIRONMENT . ' [' . ClientModel . ']==> ' . $msg;
            $ch = curl_init($webhookUrl);
            # Setup request to send json via POST.
            $payload = json_encode(array("text" => $msgStr));
            curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
            # Return response instead of printing.
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            # Send request.
            $curl_result = curl_exec($ch);
            curl_close($ch);
        }
    }
}
if (!function_exists('apache_request_headers')) {
    ///
    function apache_request_headers() {
        $arh = array();
        $rx_http = '/\AHTTP_/';
        foreach ($_SERVER as $key => $val) {
            if (preg_match($rx_http, $key)) {
                $arh_key = preg_replace($rx_http, '', $key);
                $rx_matches = array();
                // do some nasty string manipulations to restore the original letter case
                // this should work in most cases
                $rx_matches = explode('_', $arh_key);
                if (count($rx_matches) > 0 and strlen($arh_key) > 2) {
                    foreach ($rx_matches as $ak_key => $ak_val) {
                        $rx_matches[$ak_key] = ucfirst($ak_val);
                    }

                    $arh_key = implode('-', $rx_matches);
                }
                $arh[$arh_key] = $val;
            }
        }
        return ($arh);
    }
    ///
}
?>