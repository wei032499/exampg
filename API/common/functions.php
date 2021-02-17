<?php
require_once(dirname(__FILE__) . '/jwt.php');
require_once(dirname(__FILE__) . '/config.php'); //set config variables from database
$cookie_options_httponly = array(
    'expires' => time() + 1800,
    'path' => explode("/API", substr(str_replace('\\', '/',  __DIR__ . "/"), str_replace('\\', '/', strlen($_SERVER['DOCUMENT_ROOT']))))[0] . "/",
    'httponly' => true,    // or false
    /*'domain' => '.example.com', // leading dot for compatibility or use subdomain
    'secure' => true,     // or false*/
    'samesite' => 'Lax' // None || Lax  || Strict
);
$cookie_options = array(
    'expires' => time() + 1800,
    'path' => explode("/API", substr(str_replace('\\', '/',  __DIR__ . "/"), str_replace('\\', '/', strlen($_SERVER['DOCUMENT_ROOT']))))[0] . "/",
    'httponly' => false,    // or false
    /*'domain' => '.example.com', // leading dot for compatibility or use subdomain
    'secure' => true,     // or false*/
    'samesite' => 'Lax' // None || Lax  || Strict
);
function analyzeError($message)
{
    $error = array();
    $error['code'] = 0;
    $error['message'] = $message;
    if (strpos($message, 'Undefined index') !== false) {
        $error['code'] = 400;
        $error['message'] = "Bad Request";
    } else if (strpos($message, 'Duplicate') !== false) {
        $error['code'] = 409;
        $error['message'] = "Duplicate";
    }

    return $error;
}
set_error_handler(function ($errno, $errstr, $errfile, $errline, $errcontext) {
    // error was suppressed with the @-operator
    if (0 === error_reporting()) {
        return false;
    }

    $error = analyzeError($errstr);

    throw new ErrorException($error['message'], $error['code'], $errno, $errfile, $errline);
});

function clearCookie()
{
    foreach ($_COOKIE as $name => $value) {
        setcookie($name, null, time() - 1800, explode("/API", substr(str_replace('\\', '/', __DIR__ . "/"), str_replace('\\', '/', strlen($_SERVER['DOCUMENT_ROOT']))))[0] . "/");
    }
}

function setHeader($code)
{

    if ($code === 400)
        header($_SERVER["SERVER_PROTOCOL"] . " 400 Bad Request");
    else if ($code === 401) {
        clearCookie();
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

        /*$sql = "SELECT SIGNUP_ENABLE,LOCK_UP,CHECKED FROM SN_DB WHERE SCHOOL_ID='$SCHOOL_ID' AND YEAR='$ACT_YEAR_NO' AND SN=:sn";
        $stmt = oci_parse($this->conn, $sql);
        oci_bind_by_name($stmt, ':sn', $this->payload['sn']);
        oci_execute($stmt, OCI_DEFAULT);
        oci_fetch($stmt);
        $signup_enable = oci_result($stmt, "SIGNUP_ENABLE"); // 是否已入帳
        $lockup = oci_result($stmt, "LOCK_UP"); // 是否已填寫報名表
        $checked = oci_result($stmt, "CHECKED"); // 資料是否已確認
        oci_free_statement($stmt);
        if ($signup_enable === "0")
            return 0; //尚未銷帳
        else if ($checked === "1")
            return 3; //資料已確認(已鎖定)
        else if ($lockup === "0")
            return 1; //尚未填寫報名表
        else if ($lockup === "1")
            return 2; //已填寫報名表，資料尚未確認
        else
            return -1; //error*/
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
            $this->payload['exp'] =  time() + 1800;
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

        if ($this->payload === false || !isset($_COOKIE['username'])) {
            clearCookie();
            return false;
        }

        $stmt = oci_parse($this->conn, "SELECT SN_DB.PWD, to_char(SIGNUPDATA.L_ALT_DATE,'yyyy-mm-dd HH24:MI:SS') as L_ALT_DATE FROM SN_DB LEFT JOIN SIGNUPDATA ON SN_DB.SCHOOL_ID=SIGNUPDATA.SCHOOL_ID AND SN_DB.YEAR=SIGNUPDATA.YEAR AND SN_DB.SN=SIGNUPDATA.SIGNUP_SN WHERE SN_DB.SCHOOL_ID='$SCHOOL_ID' AND SN_DB.YEAR='$ACT_YEAR_NO' AND SN_DB.SN=:sn");
        oci_bind_by_name($stmt, ':sn', $this->payload['sn']);
        if (!oci_execute($stmt, OCI_DEFAULT) || !oci_fetch($stmt) || $this->payload['last_modified'] !== oci_result($stmt, "L_ALT_DATE") || $this->payload['pwd'] !== hash('sha256', oci_result($stmt, "PWD"))) {
            clearCookie();
            return false;
        }
        oci_free_statement($stmt);
        $token = $this->refresh();
        return $this->payload;
    }
}

function sendMail($msg_type, $conn, $payload)
{
    global $SCHOOL_ID, $ACT_YEAR_NO;
    global  $SIGNUP_FEE_1, $SIGNUP_FEE_2, $SIGNUP_FEE_3, $SIGNUP_FEE_4, $SIGNUP_FEE_5, $SIGNUP_FEE_6, $SIGNUP_FEE_7, $SIGNUP_FEE_8, $SIGNUP_FEE_9;
    global $CARD_START_DATE, $CARD_END_DATE, $EXAM_DATE, $CARD_SEND_DATE, $SU_END_DATE, $EXAMDATA_SEND_DATE;



    set_time_limit("3600");

    $from = "=?UTF-8?B?" . base64_encode("彰化師大網路報名系統") . "?="; //郵件來源(轉換編碼)
    $headers = "MIME-Version: 1.0\r\n";
    $headers .= "From: $from <edoc@cc2.ncue.edu.tw>\r\n";
    $headers .= "Reply-To: wan@cc.ncue.edu.tw\r\n"; //970310 add!寄給招生承辦單位承辦人
    $headers .= "Content-type: text/html; charset=utf-8\r\n";
    $headers .= "X-Priority: 1\n";
    $headers .= "X-MSMail-Priority: High\n";


    //通知
    //mail('wei032499@gmail.com', '已成功發送招生mail通知', $msg_type, $headers);

    switch ($msg_type) {
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
            if (!oci_execute($stmt, OCI_DEFAULT))
                throw new Exception(oci_error()['message'], oci_error()['code']);
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
            mail($to, $subject, $mail_msg, $headers);
            fclose($finc);

            return $to;
            break;
        case '4': //修改報名表資料通知
            $sql = "SELECT EMAIL,NAME,to_char(L_ALT_DATE,'yyyy-mm-dd HH24:MI:SS') as L_ALT_DATE FROM SIGNUPDATA WHERE SCHOOL_ID='$SCHOOL_ID' AND YEAR='$ACT_YEAR_NO' AND SIGNUP_SN=:sn ";
            $stmt = oci_parse($conn, $sql);
            oci_bind_by_name($stmt, ':sn',  $payload['sn']);
            if (!oci_execute($stmt, OCI_DEFAULT))
                throw new Exception(oci_error()['message'], oci_error()['code']);
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
            mail($to, $subject, $mail_msg, $headers);
            fclose($finc);

            return $to;
            break;
        case '5': //資料確認通知
            $sql = "SELECT EMAIL,NAME,to_char(LOCK_DATE,'yyyy-mm-dd HH24:MI:SS') as LOCK_DATE FROM SIGNUPDATA WHERE SCHOOL_ID='$SCHOOL_ID' AND YEAR='$ACT_YEAR_NO' AND SIGNUP_SN=:sn ";
            $stmt = oci_parse($conn, $sql);
            oci_bind_by_name($stmt, ':sn',  $payload['sn']);
            if (!oci_execute($stmt, OCI_DEFAULT))
                throw new Exception(oci_error()['message'], oci_error()['code']);
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
            mail($to, $subject, $mail_msg, $headers);
            fclose($finc);

            return $to;
            break;
        case '6': //查詢序號密碼
            $sql = "SELECT SN,EMAIL,ACCOUNT_NO,PWD FROM SN_DB WHERE SCHOOL_ID='$SCHOOL_ID' AND YEAR='$ACT_YEAR_NO' AND EMAIL=:email AND checked='1' ORDER BY ORDER_NO DESC";
            $stmt = oci_parse($conn, $sql);
            oci_bind_by_name($stmt, ':email',  $payload['email']);
            if (!oci_execute($stmt, OCI_DEFAULT))
                throw new Exception(oci_error()['message'], oci_error()['code']);
            oci_fetch($stmt);
            $sn = oci_result($stmt, "SN");;
            $pwd = oci_result($stmt, "PWD");
            $to = oci_result($stmt, "EMAIL");
            $account_no = oci_result($stmt, "ACCOUNT_NO");
            oci_free_statement($stmt);

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
            if (!oci_execute($stmt, OCI_DEFAULT))
                throw new Exception(oci_error()['message'], oci_error()['code']);
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
            mail($to, $subject, $mail_msg, $headers);
            fclose($finc);

            return $to;
            break;
    }
}