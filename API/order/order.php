<?php


header('Content-Type:application/json');
$result = array();
$post_processing = array();
try {
    require_once('../common/db.php');
    require_once('./functions.php');
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        $ip = $_SERVER["REMOTE_ADDR"];
        $sql = "SELECT count(*)  from sn_db where  SCHOOL_ID='$SCHOOL_ID' and year='$ACT_YEAR_NO' and from_ip=:ip";
        $stmt = oci_parse($conn, $sql);
        oci_bind_by_name($stmt, ":ip", $ip);
        oci_execute($stmt, OCI_DEFAULT);
        oci_fetch($stmt);
        $ip_cnt = oci_result($stmt, 1);
        if ($ip_cnt > 30) {
            $post_processing[] = function () use ($ip) {
                $mail_msg = $ip . '申請次數超過上限';
                sendMail(0, array('title' => "招生報名費帳號申請次數異常通知(碩士班推薦甄試)", 'content' => $mail_msg));
            };
            throw new Exception("申請次數超過上限！如有問題請與本校招生事務人員聯絡", 429);
        }
        $id = strtoupper($_POST['id']);
        // graduated
        $graduated = checkGraduated($conn, $id, $ACT_YEAR_NO);
        $order = genOrder($graduated);
        $account_no = $order['account_no'];
        $pay_money = $order['pay_money'];

        //*****************************************************
        //產生序號

        $sn = genSN($conn);


        //*****************************************************
        //產生密碼
        $pwd = genPassword();

        //*****************************************************
        //以下寫入資料庫(sn_db)
        $time = date("Y-m-d H:i:s");

        if ($graduated === 2) //曾報考當年度碩推者,直接銷帳
        {
            $signup_enable = 1; //可進行報名
            $checked = 1; //已入帳
        } else {
            $signup_enable = 0;
            $checked = 0;
        }
        $sql = "INSERT INTO SN_DB VALUES (:sn,:signup_enable,'0',:pwd,:email,null,:account_no,:checked,'',to_date(:time,'yyyy-mm-dd HH24:MI:SS'),:name,:sex,:id,:tel,:dept_id,:ip,'$SCHOOL_ID','$ACT_YEAR_NO')";
        $stmt = oci_parse($conn, $sql);
        $params = array(':sn' => $sn, ':signup_enable' => $signup_enable, ':pwd' => $pwd, ':email' => $_POST['email'], ':account_no' => $account_no, ':checked' => $checked, ':time' => $time, ':name' => $_POST['name'], ':sex' => $_POST['sex'], ':id' => $id, ':tel' => $_POST['tel'], ':dept_id' => $_POST['dept_id'], ':ip' => $ip);
        foreach ($params as $key => $val)
            oci_bind_by_name($stmt, $key, $params[$key]);

        oci_execute($stmt, OCI_DEFAULT);


        $fp = fopen($order['acc_file'], "w+");
        fwrite($fp, $order['p_acc']);
        fclose($fp);

        if ($graduated === 2) //曾報考當年度碩推者,直接銷帳寄發Email
        {
            $to = $_POST['email'];
            $pay_money = 0;
            $post_processing[] = function () use ($to, $account_no, $sn, $pwd) {
                $headers = "MIME-Version: 1.0\r\n";
                $headers .= "From: <edoc@cc2.ncue.edu.tw>\r\n";
                $headers .= "Reply-To: wan@cc.ncue.edu.tw\r\n"; //970310 add!寄給招生承辦單位承辦人
                $headers .= "Content-type: text/html; charset=utf-8\r\n";
                $headers .= "X-Priority: 1\n";
                $headers .= "X-MSMail-Priority: High\n";

                $subject = "國立彰化師範大學 網路報名系統::報名專用序號密碼通知";
                $subject = "=?UTF-8?B?" . base64_encode($subject) . "?="; //轉換編碼
                $finc = fopen("../common/inc/case_6.inc", "r");
                $mail_msg = "";

                while (!feof($finc)) {
                    $mail_msg .= str_replace("account_no", $account_no, str_replace("pay_money", 0, str_replace("snum", $sn, str_replace("pswd", $pwd, (fgets($finc, 4096))))));
                }
                mail($to, $subject, $mail_msg, $headers);
                fclose($finc);
            };
        }
        $result['data'] = array("account_no" => $account_no, "pay_money" => $pay_money, "email" => $_POST['email'], "low_income_end_date" => $LOW_INCOME_END_DATE, "acc2_end_date" => $ACC2_END_DATE,);
    } else
        throw new Exception("Method Not Allowed", 405);

    oci_commit($conn);
} catch (Exception $e) {
    oci_rollback($conn);

    setHeader($e->getCode());
    $result = array();
    $result['code'] = $e->getCode(); //$e->getCode();
    $result['message'] = $e->getMessage();
    $result['line'] = $e->getLine();
}

register_shutdown_function("shutdown_function", $post_processing);

oci_close($conn);
echo json_encode($result);
exit(); // You need to call this to send the response immediately