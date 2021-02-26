<?php

header('Content-Type:application/json');
$result = array();

try {
    require_once('../common/db.php');
    if (!isset($_COOKIE['token']))
        throw new Exception("Unauthorized", 401);
    $Token = new Token($conn, $_COOKIE['token']);
    $payload = $Token->verify();
    if ($payload === false)
        throw new Exception("Unauthorized", 401);
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        if ($payload['status'] !== 2 || $payload['authority'] !== 1)
            throw new Exception("Forbidden", 403);



        $stmt = oci_parse($conn, "SELECT ACCOUNT_NO FROM SN_DB WHERE SCHOOL_ID='$SCHOOL_ID' AND YEAR='$ACT_YEAR_NO' AND SN=:sn");
        oci_bind_by_name($stmt, ':sn',  $payload['sn']);
        oci_execute($stmt, OCI_DEFAULT);
        oci_fetch($stmt);
        $account_no = oci_result($stmt, 'ACCOUNT_NO');
        oci_free_statement($stmt);
        if (substr($account_no, 7, 1) <= "4") {
            $sql_add = " oral_flag ='1' ";
        } else if (substr($account_no, 7, 1) <= "8") {
            $sql_add = " oral_flag ='2' ";
        } else if (substr($account_no, 7, 1) == "9") {
            $sql_add = " oral_flag in ('1','2') ";
        } else {
            $sql_add = " oral_flag ='9' ";
        }

        $sql = "SELECT DEPT_ID,SUBJECT_ID FROM SIGNUPDATA WHERE SIGNUP_SN=:sn AND YEAR='$ACT_YEAR_NO' AND SCHOOL_ID='$SCHOOL_ID'";
        $stmt = oci_parse($conn, $sql);
        oci_bind_by_name($stmt, ':sn',  $payload['sn']);
        oci_execute($stmt, OCI_DEFAULT);
        if (!oci_fetch($stmt))
            throw new Exception("找不到資料");
        $dept_id =  oci_result($stmt, 'DEPT_ID');
        $subject_id = intval(oci_result($stmt, 'SUBJECT_ID'));
        oci_free_statement($stmt);

        $sql = "SELECT UPLOAD_TYPE FROM DEPARTMENT WHERE school_id='$SCHOOL_ID' AND year='$ACT_YEAR_NO' AND $sql_add AND ID=:dept";
        $stmt = oci_parse($conn, $sql);
        oci_bind_by_name($stmt, ':dept',  $dept_id);
        oci_execute($stmt, OCI_DEFAULT);
        oci_fetch($stmt);
        $upload_type = intval(oci_result($stmt, 'UPLOAD_TYPE'));
        oci_free_statement($stmt);
        //upload_type 審查資料繳交方式:  1:郵寄  2:上傳  3:郵寄+上傳
        if ($upload_type >= 2 && ($dept_id !== "334" || $subject_id === "8")) //車輛所 考科選擇"資料審查(3349118)"時才需上傳檔案
        {
            $location = "../../upload/";
            $attachment_location = $location . $ACT_YEAR_NO . "-" . $payload['sn'] . ".pdf";
            if (!file_exists($attachment_location))
                throw new Exception("審查資料尚未上傳！", 400);

            $stmt = oci_parse($conn, "UPDATE SIGNUPDATA SET DOC_UPLOAD='1' WHERE SCHOOL_ID='$SCHOOL_ID' AND YEAR='$ACT_YEAR_NO' AND SIGNUP_SN=:sn");
            oci_bind_by_name($stmt, ':sn',  $payload['sn']);
            oci_execute($stmt, OCI_DEFAULT);
            oci_free_statement($stmt);
        }

        $time = date("Y-m-d H:i:s");

        $sql = "UPDATE SIGNUPDATA SET LOCK_DATE=to_date('$time','yyyy-mm-dd HH24:MI:SS'),LOCK_UP='1' WHERE SCHOOL_ID='$SCHOOL_ID' AND YEAR='$ACT_YEAR_NO' AND SIGNUP_SN=:sn";
        $stmt = oci_parse($conn, $sql);
        oci_bind_by_name($stmt, ':sn',  $payload['sn']);
        oci_execute($stmt, OCI_DEFAULT);
        oci_free_statement($stmt);

        $post_processing[] = function () use ($payload) {
            /**
             * 寄發通知信
             */
            $email = sendMail(5, $payload);


            /**
             * 寫入log
             */
            $fp = fopen(dirname(__FILE__) . "/../logs/dbg_msg.log", "a+");
            fwrite($fp, "資料確認通知 - API/signup/confirm.php - $email - \n");
            fclose($fp);
        };
    } else
        throw new Exception("Method Not Allowed", 405);

    // setcookie('token', $Token->refresh(), $cookie_options_httponly);
    $cookieOpt = "token=" . $Token->refresh() . ";" . getCookieOptions($cookie_options_httponly);
    header("Set-Cookie: " . $cookieOpt, false);
    oci_commit($conn); //無發生任何錯誤，將資料寫進資料庫

} catch (Exception $e) {
    oci_rollback($conn);
    setHeader($e->getCode());
    $result = array();
    $result['code'] = $e->getCode();
    $result['message'] = $e->getMessage();
    //$result['line'] = $e->getLine();
}



oci_close($conn);
echo json_encode($result);
exit();
