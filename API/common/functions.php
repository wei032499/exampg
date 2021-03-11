<?php
define("ROOTDIR", "/" . basename(dirname(dirname(dirname(__FILE__)))) . "/");
if ($_SERVER['SERVER_NAME'] === "localhost")
    define("BASEURL", "https://aps.ncue.edu.tw/exampg_m_new");
else
    define("BASEURL", "https://" . $_SERVER['SERVER_NAME'] . "/" . basename(dirname(dirname(dirname(__FILE__)))));
require_once(dirname(__FILE__) . '/jwt.php');
require_once(dirname(__FILE__) . '/config.php'); //set config variables from database
$cookie_options_httponly = array(
    // 'expires' => time() + 3600,
    'path' => ROOTDIR,
    'httponly' => true,    // or false
    // 'domain' => '.example.com', // leading dot for compatibility or use subdomain
    // 'secure' => true,     // or false
    'samesite' => 'Lax' // None || Lax  || Strict
);
$cookie_options = array(
    // 'expires' => time() + 3600,
    'path' => ROOTDIR,
    'httponly' => false,    // or false
    // 'domain' => '.example.com', // leading dot for compatibility or use subdomain
    // 'secure' => true,     // or false
    'samesite' => 'Lax' // None || Lax  || Strict
);

set_error_handler(function ($err_severity, $err_msg, $err_file, $err_line, array $err_context) {
    // error was suppressed with the @-operator
    if (0 === error_reporting()) {
        return false;
    }

    global $post_processing;
    $mail_msg = $err_file . "<br>" . $err_msg . " on line " . $err_line;
    sendMail(0, array('title' => "招生系統錯誤", 'content' => $mail_msg));

    if (strpos($err_msg, 'Undefined index') !== false)
        throw new ErrorException("Bad Request", 400, $err_severity, $err_file, $err_line);
    else if (strpos($err_msg, 'ORA-') !== false) {
        preg_match('/(?<=ORA-)\w+(?=:)/', $err_msg, $matches);
        $error_code = 0;
        if ($matches[0] === "00001")
            $error_code = 409;
        else if ($matches[0] === "01400")
            $error_code = 400;
        throw new ErrorException("系統發生錯誤，請聯繫系統管理員。錯誤代碼：" . $matches[0], $error_code, $err_severity, $err_file, $err_line);
    } else
        throw new ErrorException("系統發生錯誤，請聯繫系統管理員。", 0, $err_severity, $err_file, $err_line);
}, E_ALL);

/**
 * 將cookie options array轉換為string (用於header的Set-Cookie)
 * @param array cookie options array
 * @return string
 */
function getCookieOptions($array)
{
    $cookieOpt = "";
    foreach ($array as $key => $value) {
        if ($key === "httpOnly") {
            if ($value === true)
                $cookieOpt .=  "httpOnly;";
        } else
            $cookieOpt .= $key . "=" . $value . ";";
    }
    return $cookieOpt;
}

function clearCookie()
{
    foreach ($_COOKIE as $name => $value) {
        setcookie($name, null, time() - 3600, ROOTDIR);
    }
}

function setHeader($code)
{
    if ($code === 400)
        header($_SERVER["SERVER_PROTOCOL"] . " 400 Bad Request");
    else if ($code === 401) {
        header($_SERVER["SERVER_PROTOCOL"] . " 401 Unauthorized");
    } else if ($code === 403) {
        header($_SERVER["SERVER_PROTOCOL"] . " 403 Forbidden");
    } else if ($code === 404) {
        header($_SERVER["SERVER_PROTOCOL"] . " 404 Not Found");
    } else if ($code === 409) {
        header($_SERVER["SERVER_PROTOCOL"] . " 409 Conflict");
    } else if ($code === 429) {
        header($_SERVER["SERVER_PROTOCOL"] . " 429 Too Many Requests");
    } else
        header($_SERVER["SERVER_PROTOCOL"] . " 500 Internal Server Error");
}


function bind_by_array($stmt, $sql, $array)
{
    $sql .= " "; //fix變數在結尾
    preg_match_all("/(?<=[( ,=]):\w+(?=[) ,])/", $sql, $matches);

    if (count($matches[0]) !== count($array))
        return false;

    foreach ($matches[0] as $key => $value)
        oci_bind_by_name($stmt, $value,  $array[$key]);
    return true;
}

/**
 * 需與資料庫狀態同步的token
 */
class Token
{
    /**
     * mysqli
     * @var mysqli 
     */
    private  $conn;

    /**
     * JWT payload
     * @var bool|array 
     */
    private $payload;

    /**
     * constructor
     * @param mysqli $conn 資料庫連線
     * @param string $token JWT token
     */
    function __construct($conn, $token)
    {
        $this->conn = $conn;
        $this->payload = JWT::verifyToken($token);
    }

    /**
     * 取得目前帳號狀態：
     *  0 => 尚未銷帳
     *  1 => 尚未填寫報名表
     *  2 => 已填寫報名表，資料尚未確認
     *  3 => 資料已確認(已鎖定)
     */
    private function getStatus()
    {
        global $SCHOOL_ID, $ACT_YEAR_NO;

        $sql = "SELECT SN_DB.SIGNUP_ENABLE,SN_DB.LOCK_UP,SN_DB.CHECKED,SIGNUPDATA.LOCK_UP as FORM_LOCK FROM SN_DB LEFT JOIN SIGNUPDATA ON SN_DB.SCHOOL_ID=SIGNUPDATA.SCHOOL_ID AND SN_DB.YEAR=SIGNUPDATA.YEAR AND SN_DB.SN=SIGNUPDATA.SIGNUP_SN WHERE SN_DB.SCHOOL_ID='$SCHOOL_ID' AND SN_DB.YEAR='$ACT_YEAR_NO' AND SN_DB.SN=:sn";
        $stmt = oci_parse($this->conn, $sql);
        oci_bind_by_name($stmt, ':sn', $this->payload['sn']);
        oci_execute($stmt, OCI_DEFAULT);
        oci_fetch($stmt);
        $signup_enable = oci_result($stmt, "SIGNUP_ENABLE"); // 是否可進行報名
        $lockup = oci_result($stmt, "LOCK_UP"); // 是否已填寫報名表
        $checked = oci_result($stmt, "CHECKED"); // 是否已入帳
        $form_lock = oci_result($stmt, "FORM_LOCK"); // 資料是否已確認
        oci_free_statement($stmt);
        if ($checked !== "1")
            return 0; //尚未銷帳
        else if ($signup_enable === "1" && $form_lock === "1")
            return 3; //資料已確認(已鎖定)
        else if ($signup_enable === "1" && $lockup === "0")
            return 1; //尚未填寫報名表
        else if ($signup_enable === "1" && $lockup === "1")
            return 2; //已填寫報名表，資料尚未確認
        else
            return -1; //error
    }

    /**
     * 更新token
     * @return bool|string 最新狀態的token
     */
    public function refresh()
    {
        global $SCHOOL_ID, $ACT_YEAR_NO;
        if ($this->payload === false)
            return false;
        $stmt = oci_parse($this->conn, "SELECT SN_DB.PWD, to_char(SIGNUPDATA.L_ALT_DATE,'yyyy-mm-dd HH24:MI:SS') as L_ALT_DATE FROM SN_DB LEFT JOIN SIGNUPDATA ON SN_DB.SCHOOL_ID=SIGNUPDATA.SCHOOL_ID AND SN_DB.YEAR=SIGNUPDATA.YEAR AND SN_DB.SN=SIGNUPDATA.SIGNUP_SN WHERE SN_DB.SCHOOL_ID='$SCHOOL_ID' AND SN_DB.YEAR='$ACT_YEAR_NO' AND SN_DB.SN=:sn");
        oci_bind_by_name($stmt, ':sn', $this->payload['sn']);
        if (!oci_execute($stmt, OCI_DEFAULT)) //oci_execute($stmt) 
            return false;
        if (oci_fetch($stmt)) {
            $this->payload['pwd'] = hash('sha256', oci_result($stmt, "PWD"));
            $this->payload['last_modified'] = oci_result($stmt, "L_ALT_DATE");
            $this->payload['iat'] = time();
            $this->payload['exp'] =  time() + 3600;
            $this->payload['status'] = $this->getStatus();
        } else
            return false;
        oci_free_statement($stmt);

        return JWT::getToken($this->payload);;
    }

    /**
     * 驗證token與資料庫狀態
     * @return bool|array 最新狀態的payload
     */
    public function verify()
    {
        global $SCHOOL_ID, $ACT_YEAR_NO;
        if ($this->payload === false || !isset($this->payload['authority']) || !isset($_COOKIE['username'])) {
            return false;
        }

        $stmt = oci_parse($this->conn, "SELECT SN_DB.PWD, to_char(SIGNUPDATA.L_ALT_DATE,'yyyy-mm-dd HH24:MI:SS') as L_ALT_DATE FROM SN_DB LEFT JOIN SIGNUPDATA ON SN_DB.SCHOOL_ID=SIGNUPDATA.SCHOOL_ID AND SN_DB.YEAR=SIGNUPDATA.YEAR AND SN_DB.SN=SIGNUPDATA.SIGNUP_SN WHERE SN_DB.SCHOOL_ID='$SCHOOL_ID' AND SN_DB.YEAR='$ACT_YEAR_NO' AND SN_DB.SN=:sn");
        oci_bind_by_name($stmt, ':sn', $this->payload['sn']);
        if (!oci_execute($stmt, OCI_DEFAULT) || !oci_fetch($stmt) || $this->payload['last_modified'] !== oci_result($stmt, "L_ALT_DATE") || $this->payload['pwd'] !== hash('sha256', oci_result($stmt, "PWD"))) {
            return false;
        }
        oci_free_statement($stmt);
        $token = $this->refresh();
        return $this->payload;
    }
}

function getClientIP()
{
    if (array_key_exists('HTTP_X_FORWARDED_FOR', $_SERVER)) {
        return  $_SERVER["HTTP_X_FORWARDED_FOR"];
    } else if (array_key_exists('REMOTE_ADDR', $_SERVER)) {
        return $_SERVER["REMOTE_ADDR"];
    } else if (array_key_exists('HTTP_CLIENT_IP', $_SERVER)) {
        return $_SERVER["HTTP_CLIENT_IP"];
    }

    return '';
}

function sendMail($msg_type, $payload)
{
    global $SCHOOL_ID, $ACT_YEAR_NO;
    global  $SIGNUP_FEE_1, $SIGNUP_FEE_2, $SIGNUP_FEE_3, $SIGNUP_FEE_4, $SIGNUP_FEE_5, $SIGNUP_FEE_6, $SIGNUP_FEE_7, $SIGNUP_FEE_8, $SIGNUP_FEE_9;
    global $CARD_START_DATE, $CARD_END_DATE, $EXAM_DATE, $CARD_SEND_DATE, $SU_END_DATE, $EXAMDATA_SEND_DATE;

    require(dirname(__FILE__) . '/db.php'); //$conn

    set_time_limit("3600");

    $from = "=?UTF-8?B?" . base64_encode("彰化師大網路報名系統") . "?="; //郵件來源(轉換編碼)
    $headers = "MIME-Version: 1.0\r\n";
    $headers .= "From: $from <edoc@cc2.ncue.edu.tw>\r\n";
    $headers .= "Reply-To: wan@cc.ncue.edu.tw\r\n"; //970310 add!寄給招生承辦單位承辦人
    $headers .= "Content-type: text/html; charset=utf-8\r\n";
    $headers .= "X-Priority: 1\n";
    $headers .= "X-MSMail-Priority: High\n";


    //通知

    switch ($msg_type) {
        case '0':
            mail("s0654017@gm.ncue.edu.tw", $payload['title'], $payload['content'], $headers);
            if ($_SERVER['SERVER_NAME'] !== "localhost")
                mail("bob@cc.ncue.edu.tw", $payload['title'], $payload['content'], $headers);
            break;
        case '1':
            // $num = count($stmt1_email) - 1;
            // $subject = "國立彰化師範大學 網路報名系統::簡章費繳費入帳及專用序號密碼通知";
            // while ($num >= 0) {
            //     $finc = fopen(dirname(__FILE__) ."/inc/case_1.inc", "r");
            //     $mail_msg = '';
            //     while (!feof($finc)) {
            //         //960116 update :  trim(fgets($finc,4096)) ->(fgets($finc,4096))
            //         //去掉trim,否則會有!或亂碼出現
            //         $mail_msg .= str_replace("enddate", $SU_END_DATE, str_replace("startdate", $SU_START_DATE, str_replace("snum", $stmt1_sn[$num], str_replace("pswd", $stmt1_pwd[$num], (fgets($finc, 4096))))));
            //     }

            //     mail($stmt1_email[$num], $subject, $mail_msg, $headers);
            //     $num--;
            // }
            // fclose($finc);
            // break;
        case '2':
            //     $num = count($su_email) - 1;
            //     $subject = "國立彰化師範大學 網路報名系統::報名費繳費入帳及專用序號密碼通知";
            //     $subject = "=?UTF-8?B?" . base64_encode("$subject") . "?="; //郵件主旨(轉換編碼)
            //     while ($num >= 0) {
            //         $finc = fopen(dirname(__FILE__) ."/inc/case_2.inc", "r");
            //         $mail_msg = '';
            //         if (substr($account_no, 6, 2) == "31") {
            //             $pay_money = $SIGNUP_FEE_1;
            //         }

            //         if (substr($account_no, 6, 2) == "32") {
            //             $pay_money = $SIGNUP_FEE_2;
            //         }

            //         if (substr($account_no, 6, 2) == "33") {
            //             $pay_money = $SIGNUP_FEE_3;
            //         }

            //         if (substr($account_no, 6, 2) == "34") {
            //             $pay_money = $SIGNUP_FEE_4;
            //         }

            //         if (substr($account_no, 6, 2) == "35") {
            //             $pay_money = $SIGNUP_FEE_5;
            //         }

            //         if (substr($account_no, 6, 2) == "36") {
            //             $pay_money = $SIGNUP_FEE_6;
            //         }

            //         if (substr($account_no, 6, 2) == "37") {
            //             $pay_money = $SIGNUP_FEE_7;
            //         }

            //         if (substr($account_no, 6, 2) == "38") {
            //             $pay_money = $SIGNUP_FEE_8;
            //         }

            //         if (substr($account_no, 6, 2) == "39") {
            //             $pay_money = $SIGNUP_FEE_9;
            //         }

            //         while (!feof($finc)) {
            //             //960116 update :  trim(fgets($finc,4096)) ->(fgets($finc,4096))
            //             //去掉trim,否則會有!或亂碼出現
            //             $mail_msg .= str_replace("account_no", $account_no, str_replace("pay_money", $pay_money, str_replace("enddate", $SU_END_DATE, str_replace("startdate", $SU_START_DATE, str_replace("snum", $su_sn[$num], str_replace("pswd", $su_pwd[$num], (fgets($finc, 4096))))))));
            //         }

            //         mail($su_email[$num], $subject, $mail_msg, $headers);
            //         $num--;
            //     }
            //     fclose($finc);
            //     break;
        case '3': //報名表初填通知
            $sql = "SELECT EMAIL,NAME FROM SIGNUPDATA WHERE SCHOOL_ID='$SCHOOL_ID' AND YEAR='$ACT_YEAR_NO' AND SIGNUP_SN=:sn ";
            $stmt = oci_parse($conn, $sql);
            oci_bind_by_name($stmt, ':sn',  $payload['sn']);
            oci_execute($stmt, OCI_DEFAULT);
            oci_fetch($stmt);
            $to = oci_result($stmt, "EMAIL");
            $name = oci_result($stmt, "NAME");
            oci_free_statement($stmt);

            $subject = "國立彰化師範大學 網路報名系統::報名表初填完成通知";
            $subject = "=?UTF-8?B?" . base64_encode("$subject") . "?="; //郵件主旨(轉換編碼)
            $finc = fopen(dirname(__FILE__) . "/inc/case_3.inc", "r");
            $mail_msg = "";
            while (!feof($finc)) {
                $mail_msg .= str_replace("card_start_date", $CARD_START_DATE, str_replace("card_end_date", $CARD_END_DATE, str_replace("card_send_date", $CARD_SEND_DATE, str_replace("deadline", $SU_END_DATE, str_replace("usrname", $name, (fgets($finc, 4096)))))));
            }
            $mail_msg = str_replace("BASEURL", BASEURL, $mail_msg);
            mail($to, $subject, $mail_msg, $headers);
            fclose($finc);

            return $to;
            break;
        case '4': //修改報名表資料通知
            $sql = "SELECT EMAIL,NAME,to_char(L_ALT_DATE,'yyyy-mm-dd HH24:MI:SS') as L_ALT_DATE FROM SIGNUPDATA WHERE SCHOOL_ID='$SCHOOL_ID' AND YEAR='$ACT_YEAR_NO' AND SIGNUP_SN=:sn ";
            $stmt = oci_parse($conn, $sql);
            oci_bind_by_name($stmt, ':sn',  $payload['sn']);
            oci_execute($stmt, OCI_DEFAULT);
            oci_fetch($stmt);
            $to = oci_result($stmt, "EMAIL");
            $name = oci_result($stmt, "NAME");
            $alt_date = oci_result($stmt, "L_ALT_DATE");
            oci_free_statement($stmt);

            $subject = "國立彰化師範大學 網路報名系統::資料修改通知";
            $subject = "=?UTF-8?B?" . base64_encode("$subject") . "?="; //郵件主旨(轉換編碼)
            $finc = fopen(dirname(__FILE__) . "/inc/case_4.inc", "r");
            $mail_msg = "";
            while (!feof($finc)) {
                $mail_msg .= str_replace("card_start_date", $CARD_START_DATE, str_replace("card_end_date", $CARD_END_DATE, str_replace("examdata_send_date", $EXAMDATA_SEND_DATE, str_replace("card_send_date", $CARD_SEND_DATE, str_replace("alt_date", $alt_date, str_replace("deadline", $SU_END_DATE, str_replace("usrname", $name, (fgets($finc, 4096)))))))));
            }
            $mail_msg = str_replace("BASEURL", BASEURL, $mail_msg);
            mail($to, $subject, $mail_msg, $headers);
            fclose($finc);

            return $to;
            break;
        case '5': //資料確認通知
            $sql = "SELECT EMAIL,NAME,to_char(LOCK_DATE,'yyyy-mm-dd HH24:MI:SS') as LOCK_DATE FROM SIGNUPDATA WHERE SCHOOL_ID='$SCHOOL_ID' AND YEAR='$ACT_YEAR_NO' AND SIGNUP_SN=:sn ";
            $stmt = oci_parse($conn, $sql);
            oci_bind_by_name($stmt, ':sn',  $payload['sn']);
            oci_execute($stmt, OCI_DEFAULT);
            oci_fetch($stmt);
            $to = oci_result($stmt, "EMAIL");
            $name = oci_result($stmt, "NAME");
            $con_date = oci_result($stmt, "LOCK_DATE");
            oci_free_statement($stmt);

            $subject = "國立彰化師範大學 網路報名系統::資料確認通知";
            $subject = "=?UTF-8?B?" . base64_encode("$subject") . "?="; //郵件主旨(轉換編碼)

            $finc = fopen(dirname(__FILE__) . "/inc/case_5.inc", "r");

            $mail_msg = "";
            while (!feof($finc)) {
                $mail_msg .= str_replace("card_start_date", $CARD_START_DATE, str_replace("card_end_date", $CARD_END_DATE, str_replace("con_date", $con_date, str_replace("usrname", $name, (fgets($finc, 4096))))));
            }
            $mail_msg = str_replace("BASEURL", BASEURL, $mail_msg);
            ob_start();
            require(dirname(__FILE__) . '/inc/finaldata.php');
            $finaldata = ob_get_clean();
            $mail_msg = str_replace("finaldata", $finaldata, $mail_msg);
            mail($to, $subject, $mail_msg, $headers);
            fclose($finc);

            return $to;
            break;
        case '6': //查詢序號密碼
            $sn = $payload['sn'];;
            $pwd = $payload['pwd'];
            $to = $payload['email'];
            $account_no = $payload['account_no'];

            $subject = "國立彰化師範大學 網路報名系統::查詢序號密碼回覆";
            $subject = "=?UTF-8?B?" . base64_encode("$subject") . "?="; //郵件主旨(轉換編碼)
            $finc = fopen(dirname(__FILE__) . "/inc/case_6.inc", "r");
            $mail_msg = "";
            if (substr($account_no, 6, 2) == "31") {
                $pay_money = $SIGNUP_FEE_1;
            } else if (substr($account_no, 6, 2) == "32") {
                $pay_money = $SIGNUP_FEE_2;
            } else if (substr($account_no, 6, 2) == "33") {
                $pay_money = $SIGNUP_FEE_3;
            } else if (substr($account_no, 6, 2) == "34") {
                $pay_money = $SIGNUP_FEE_4;
            } else if (substr($account_no, 6, 2) == "35") {
                $pay_money = $SIGNUP_FEE_5;
            } else if (substr($account_no, 6, 2) == "36") {
                $pay_money = $SIGNUP_FEE_6;
            } else if (substr($account_no, 6, 2) == "37") {
                $pay_money = $SIGNUP_FEE_7;
            } else if (substr($account_no, 6, 2) == "38") {
                $pay_money = $SIGNUP_FEE_8;
            } else if (substr($account_no, 6, 2) == "39") {
                $pay_money = $SIGNUP_FEE_9;
            }

            while (!feof($finc)) {
                $mail_msg .= str_replace("account_no", $account_no, str_replace("pay_money", $pay_money, str_replace("snum", $sn, str_replace("pswd", $pwd, (fgets($finc, 4096))))));
            }
            $mail_msg = str_replace("BASEURL", BASEURL, $mail_msg);
            mail($to, $subject, $mail_msg, $headers);
            fclose($finc);

            return $to;
            break;
        case '7': //紙本簡章費通知
            // $i = count($stmt2_email) - 1;
            // $subject = "國立彰化師範大學 網路報名系統::簡章費繳費入帳通知";
            // $subject = "=?UTF-8?B?" . base64_encode("$subject") . "?="; //郵件主旨(轉換編碼)
            // $finc = fopen(dirname(__FILE__) ."/inc/case_7.inc", "r");
            // $mail_msg = "";
            // while (!feof($finc)) {
            //     $mail_msg .= fgets($finc, 4096);
            // }
            // while ($i >= 0) {
            //     mail($stmt2_email[$i], $subject, $mail_msg, $headers);
            //     $i--;
            // }
            // fclose($finc);
            // break;
        case '8': //准考證下載批次通知
            $sql = "SELECT EMAIL,NAME FROM SIGNUPDATA WHERE SCHOOL_ID='$SCHOOL_ID' AND YEAR='$ACT_YEAR_NO' AND SIGNUP_SN=:sn ";
            $stmt = oci_parse($conn, $sql);
            oci_bind_by_name($stmt, ':sn',  $payload['sn']);
            oci_execute($stmt, OCI_DEFAULT);
            oci_fetch($stmt);
            $name = oci_result($stmt, "NAME");
            $to = oci_result($stmt, "EMAIL");

            oci_free_statement($stmt);

            $subject = "國立彰化師範大學 網路報名系統::准考證下載通知";
            $subject = "=?UTF-8?B?" . base64_encode("$subject") . "?="; //郵件主旨(轉換編碼)
            $finc = fopen(dirname(__FILE__) . "/inc/case_8.inc", "r");
            $mail_msg = "";
            while (!feof($finc)) {
                $mail_msg .= str_replace("exam_date", $EXAM_DATE, str_replace("card_start_date", $CARD_START_DATE, str_replace("card_end_date", $CARD_END_DATE, str_replace("act_year_no", $ACT_YEAR_NO, str_replace("usrname", $name, (fgets($finc, 4096)))))));
            }
            $mail_msg = str_replace("BASEURL", BASEURL, $mail_msg);
            mail($to, $subject, $mail_msg, $headers);
            fclose($finc);

            return $to;
            break;

        case '9': //推薦函填寫通知
            $subject = "國立彰化師範大學 網路報名系統::推薦函填寫通知";
            $subject = "=?UTF-8?B?" . base64_encode("$subject") . "?="; //郵件主旨(轉換編碼)
            $finc = fopen(dirname(__FILE__) . "/inc/case_letter.inc", "r");
            $to = $payload['to'];
            $mail_msg = "";
            while (!feof($finc)) {
                $mail_msg .=  str_replace("act_year_no", $ACT_YEAR_NO, str_replace("su_end_date", $SU_END_DATE, str_replace("stud_name", $payload['stud_name'], str_replace("dept_name", $payload['dept_name'], str_replace("r_name", $payload['r_name'], str_replace("r_token", $payload['r_token'], (fgets($finc, 4096))))))));
            }
            $mail_msg = str_replace("BASEURL", BASEURL, $mail_msg);
            mail($to, $subject, $mail_msg, $headers);
            fclose($finc);
            return $to;
            break;
    }
}

/**
 * 程式結束時執行的function
 */
$post_processing = array();
register_shutdown_function("shutdown_function");

function shutdown_function()
{
    global $post_processing;
    $last_error = error_get_last();
    if ($last_error['type'] === E_ERROR) {
        $mail_msg = $last_error['file'] . "<br>" . $last_error['message'] . " on line " . $last_error['line'];
        sendMail(0, array('title' => "招生系統錯誤", 'content' => $mail_msg));
        header($_SERVER["SERVER_PROTOCOL"] . " 500 Internal Server Error");
        $result = array();
        $result['message'] = "系統發生錯誤，請聯繫系統管理員。\n" . $last_error['message'];
        echo json_encode($result);
    } else {
        foreach ($post_processing as $function) {
            $function();
        }
    }
}
