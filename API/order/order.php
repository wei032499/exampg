<?php


header('Content-Type:application/json');
$result = array();
try {
    require_once('../db.php');
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $ORG_NO = "99216";

        $ip = $_SERVER["REMOTE_ADDR"];
        $sql = "SELECT count(*)  from sn_db where  SCHOOL_ID=:SCHOOL_ID and year=:ACT_YEAR_NO and from_ip=:ip";
        $stmt = oci_parse($conn, $sql);
        $params = array(':SCHOOL_ID' => $SCHOOL_ID, ':ACT_YEAR_NO' => $ACT_YEAR_NO, ':ip' => $ip);
        foreach ($params as $key => $val)
            oci_bind_by_name($stmt, $key, $params[$key]);
        oci_execute($stmt, OCI_DEFAULT);
        oci_fetch($stmt);
        $ip_cnt = oci_result($stmt, 1);
        if ($ip_cnt > 30) {
            mail('bob@cc.ncue.edu.tw', '招生報名費帳號申請次數異常通知(碩士班推薦甄試)', $ip . '申請次數超過上限', $headers);
            throw new Exception("申請次數超過上限！如有問題請與本校招生事務人員聯絡", 429);
        }

        // graduated
        $graduated = 0;
        $id = strtoupper($_POST['id']);
        if (strlen($id) != 10) {
            $graduated = 0;
        } else {
            //曾報考當年度碩推者(29x為博推),免繳報名費
            $sql = "SELECT count(*) from signupdata WHERE  id=:id and SCHOOL_ID='2' and year=:ACT_YEAR_NO and substr(dept_id,1,2)<>'29'";
            $stmt = oci_parse($conn, $sql);
            $params = array(':id' => $id, ':ACT_YEAR_NO' => $ACT_YEAR_NO);
            foreach ($params as $key => $val)
                oci_bind_by_name($stmt, $key, $params[$key]);
            oci_execute($stmt, OCI_DEFAULT);
            oci_fetch($stmt);
            $nrows = oci_result($stmt, 1); //$nrows -->總筆數
            if ($nrows > 0) {
                $graduated = 2;
            } /*else {
                //96年以前在職專班畢業生(尚未電腦化)
                //需再修改account_c.php
                if ($id === "L120210922" || $id === "Q220098486" || $id === "N121851563" || $id === "N220261405" || $id === "N122009956") {
                    $graduated = 1;
                }
                //畢業生報名費打八折(106/12/6淑琬確認--限在學,畢業生,延長修業 , 108/09/19 淑琬要求排除學分班(94))
                $sql = "SELECT count(*) from dean.s30_student WHERE  stu_idno=:id and stu_status in ('1','8','29')  and substr(stu_id,4,2)<>'94' ";
                $stmt = oci_parse($conn, $sql);
                $params = array(':id' => $id);
                foreach ($params as $key => $val)
                    oci_bind_by_name($stmt, $key, $params[$key]);
                oci_execute($stmt, OCI_DEFAULT);
                oci_fetch($stmt);
                $nrows = oci_result($stmt, 1); //$nrows -->總筆數
                if ($nrows > 0) {
                    $graduated = 1;
                } else {
                    $graduated = 0;
                }
            }*/
        }
        if ($graduated === 2) //曾報考當年度碩推者
        {
            $SIGNUP_FEE = null; //免繳報名費
            $acc_file = "./acc/" .  "signup_acc_9.txt"; //帳號為99216-39xxxxxx-x
        } else if ($_POST['dept_id'] === "2") //1300
        {
            if ($_POST['identity'] === "1") //一般考生
            {
                if ($graduated === 1) //校友
                {
                    $SIGNUP_FEE = "1040"; //一般考生(校友)1040
                    $acc_file = "./acc/" .  "signup_acc_2.txt"; //帳號為99216-32xxxxxx-x

                } else {
                    $SIGNUP_FEE = "1300"; //一般考生1300
                    $acc_file = "./acc/" .  "signup_acc_1.txt"; //帳號為99216-31xxxxxx-x

                }
            } else if ($_POST['identity'] === "2") {
                $SIGNUP_FEE = "0"; //中低收入戶0
                $acc_file = "./acc/" .  "signup_acc_3.txt"; //帳號為99216-33xxxxxx-x
            } else if ($_POST['identity'] === "3") {
                $SIGNUP_FEE = "0"; //低收入戶0
                $acc_file = "./acc/" .  "signup_acc_4.txt"; //帳號為99216-34xxxxxx-x
            }
        } else if ($_POST['dept_id'] === "1") //1800
        {
            if ($_POST['identity'] === "1") //一般考生
            {
                if ($graduated === 1) //校友
                {
                    $SIGNUP_FEE = "1440"; //一般考生(校友)1440
                    $acc_file = "./acc/" .  "signup_acc_6.txt"; //帳號為99216-36xxxxxx-x
                } else {
                    $SIGNUP_FEE = "1800"; //一般考生1800
                    $acc_file = "./acc/" .  "signup_acc_5.txt"; //帳號為99216-35xxxxxx-x

                }
            } else if ($_POST['identity'] === "2") {
                $SIGNUP_FEE = "0"; //中低收入戶0
                $acc_file = "./acc/" .  "signup_acc_7.txt"; //帳號為99216-37xxxxxx-x
            } else if ($_POST['identity'] === "3") {
                $SIGNUP_FEE = "0"; //低收入戶0
                $acc_file = "./acc/" .  "signup_acc_8.txt"; //帳號為99216-38xxxxxx-x
            }
        } else {
            $dept_id = $_POST['dept_id'];
            mail('bob@cc.ncue.edu.tw', 'dept_id(碩士班)', $dept_id, $headers);
            throw new Exception("報考系所資料錯誤，請重新填寫！", 400);
        }

        //$acc_file="signup_acc.txt"; //帳號為99216-310xxxxx-x

        $fp = fopen($acc_file, "r");
        $p_acc = trim(fgets($fp, 2048)) + 1;
        fclose($fp);

        $acc = $ORG_NO . $p_acc;

        //檢查碼運算
        $checksum_1 = 0;
        $checksum_2 = 0;
        $times = 1;
        $len_1 = strlen($acc) - 1;
        $len_2 = strlen($SIGNUP_FEE) - 1;
        while ($len_1 >= 0) {
            $bit_1 = substr($acc, $len_1, 1);
            $checksum_1 += ($bit_1 * $times);
            if ($len_2 >= 0) {
                $bit_2 = substr($SIGNUP_FEE, $len_2, 1);
                $checksum_2 += ($bit_2 * $times);
                $len_2--;
            }
            $times++;
            if ($times == 10) {
                $times = 1;
            }
            $len_1--;
        }
        $checksum = $checksum_1 + $checksum_2;
        $checksum = substr($checksum, (strlen($checksum) - 1), 1);
        $account_f = $ORG_NO . "-" . $p_acc . "-" . $checksum;

        //*****************************************************
        //產生序號



        do {
            $sn = chr(rand(65, 90));
            $sn .= substr(time(), 1, 9);

            $stmt = oci_parse($conn, "SELECT SN FROM SN_DB WHERE SN=:sn");
            oci_bind_by_name($stmt, ':sn', $sn);
            oci_execute($stmt, OCI_DEFAULT);
            $data = oci_fetch($stmt);
            oci_free_statement($stmt);
        } while ($data); //若已存在相同序號,則重新產生


        //*****************************************************
        //產生密碼
        require("./pwd_gen.php");

        //*****************************************************
        //以下寫入資料庫(sn_db)
        $time = date("Y-m-d H:i:s");

        if ($graduated === 2) //曾報考當年度碩推者,直接銷帳
        {
            $signup_enable = 1;
            $checked = 1;
        } else {
            $signup_enable = 0;
            $checked = 0;
        }
        //$sql = "INSERT INTO SN_DB VALUES ('$sn','$signup_enable','0','$pwd','$_POST['email']','','$account_f','$checked','',TO_DATE('$time','yyyy-mm-dd HH24:MI:SS'),'$_POST[b_name]','$_POST[b_sex]','$_POST[b_id]','$_POST[b_tel]','$_POST['dept_id']','$ip','$SCHOOL_ID','$ACT_YEAR_NO')";
        $sql = "INSERT INTO SN_DB VALUES (:sn,:signup_enable,'0',:pwd,:email,null,:account_f,:checked,'',to_date(:time,'yyyy-mm-dd HH24:MI:SS'),:name,:gender,:id,:tel,:dept_id,:ip,:SCHOOL_ID,:ACT_YEAR_NO)";
        $stmt = oci_parse($conn, $sql);
        $params = array(':sn' => $sn, ':signup_enable' => $signup_enable, ':pwd' => $pwd, ':email' => $_POST['email'], ':account_f' => $account_f, ':checked' => $checked, ':time' => $time, ':name' => $_POST['name'], ':gender' => $_POST['gender'], ':id' => $id, ':tel' => $_POST['tel'], ':dept_id' => $_POST['dept_id'], ':ip' => $ip, ':SCHOOL_ID' => $SCHOOL_ID, ':ACT_YEAR_NO' => $ACT_YEAR_NO);
        foreach ($params as $key => $val) {
            // oci_bind_by_name($stmt, $key, $val) does not work
            // because it binds each placeholder to the same location: $val
            // instead use the actual location of the data: $params[$key]
            oci_bind_by_name($stmt, $key, $params[$key]);
        }

        if (!oci_execute($stmt, OCI_DEFAULT)) {
            $oci_err = oci_error();
            mail('bob@cc.ncue.edu.tw', "碩士班招生寫入資料庫(sn_db)錯誤-- " . $oci_err['message'], $sql, $headers);
            throw new Exception("寫入資料庫錯誤！請檢查資料是否輸入正確", 500);
        }


        $fp = fopen($acc_file, "w+");
        fwrite($fp, $p_acc);
        fclose($fp);
        $account_no = $account_f;
        $pay_money = $SIGNUP_FEE;
        if ($graduated === 2) //曾報考當年度碩推者,直接銷帳寄發Email
        {
            $to = $_POST['email'];
            $pay_money = 0;

            $headers = "MIME-Version: 1.0\r\n";
            $headers .= "From: <edoc@cc2.ncue.edu.tw>\r\n";
            $headers .= "Reply-To: wan@cc.ncue.edu.tw\r\n"; //970310 add!寄給招生承辦單位承辦人
            $headers .= "Content-type: text/html; charset=utf-8\r\n";
            $headers .= "X-Priority: 1\n";
            $headers .= "X-MSMail-Priority: High\n";

            $subject = "國立彰化師範大學 網路報名系統::報名專用序號密碼通知";
            $subject = "=?UTF-8?B?" . base64_encode($subject) . "?="; //轉換編碼
            $finc = fopen("./inc/case_6.inc", "r");
            $mail_msg = "";

            while (!feof($finc)) {
                $mail_msg .= str_replace("account_no", $account_no, str_replace("pay_money", $pay_money, str_replace("snum", $sn, str_replace("pswd", $pwd, (fgets($finc, 4096))))));
            }
            mail($to, $subject, $mail_msg, $headers);
            fclose($finc);
        }
        $result['data'] = array("account_no" => $account_no, "pay_money" => $pay_money, "email" => $_POST['email']);
    } else
        throw new Exception("Method Not Allowed", 405);

    oci_commit($conn);
} catch (Exception $e) {
    @oci_rollback($conn);

    setHeader($e->getCode());
    $result['code'] = $e->getCode(); //$e->getCode();
    $result['message'] = $e->getMessage();
    $result['line'] = $e->getLine();
}
@oci_close($conn);

echo json_encode($result);
