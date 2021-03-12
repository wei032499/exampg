<?php

/**
 *  $payload['status']：
 *      0 => 尚未銷帳
 *      1 => 尚未填寫報名表
 *      2 => 已填寫報名表，資料尚未確認
 *      3 => 資料已確認(已鎖定)
 * 
 *  $payload['authority']：
 *      0 => 一般登入
 *      1 => 身分證登入
 */
header('Content-Type:application/json');
header("Cache-Control: private");
$result = array();
$payload = array('iss' => 'ncue');

try {
    require_once('../common/db.php');

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        if (isset($_POST['sid'])) //就讀意願登入 
        {
            if (isset($_COOKIE['token'])) {
                $Token = new Token($conn, $_COOKIE['token']);
                $payload = $Token->verify();
                if ($payload === false)
                    $payload = array('iss' => 'ncue');
            }

            // I don't know why !!
            //BACKNUMBER：備取名次(0表示為正取)
            $sql = "SELECT ID,BACKNUMBER,MAINNUMBER,REPAIR,CHECK_DATE FROM PERSON  WHERE  school_id='$SCHOOL_ID' and year='$ACT_YEAR_NO' and 
            STUDENT_ID = :sid  AND ( MAINNUMBER > 0 OR BACKNUMBER > 0 ) AND ID=:id
            union all 
            SELECT ID,BACKNUMBER,MAINNUMBER,REPAIR,CHECK_DATE FROM union_priority_all  WHERE  school_id='$SCHOOL_ID' and year='$ACT_YEAR_NO' and 
            STUDENT_ID = :sid AND ( MAINNUMBER > 0 OR BACKNUMBER > 0 ) AND ID=:id
            union all 
            SELECT person_ID,BACKNUMBER,MAINNUMBER,REPAIR,CHECK_DATE FROM add_enroll  WHERE  school_id='$SCHOOL_ID' and year='$ACT_YEAR_NO' and 
            STUDENT_ID = :sid AND ( MAINNUMBER > 0 OR BACKNUMBER > 0 ) AND PERSON_ID=:id";

            $stmt = oci_parse($conn, $sql);
            oci_bind_by_name($stmt, ':id',  $_POST['IDNumber']);
            oci_bind_by_name($stmt, ':sid',  $_POST['sid']);
            oci_execute($stmt, OCI_DEFAULT);
            if (!oci_fetch($stmt))
                throw new Exception("身分證或准考証號錯誤", 401);
            oci_free_statement($stmt);

            $payload['id'] = $_POST['IDNumber'];
            $payload['sid'] = $_POST['sid'];
            $token = JWT::getToken($payload);
        } else {
            $sql = "SELECT NAME FROM SN_DB WHERE SCHOOL_ID='$SCHOOL_ID' AND YEAR='$ACT_YEAR_NO' AND SN=:sn AND PWD=:pwd ";
            $stmt = oci_parse($conn, $sql);
            oci_bind_by_name($stmt, ':sn',  $_POST['serial_no']);
            oci_bind_by_name($stmt, ':pwd',  $_POST['pwd']);

            oci_execute($stmt, OCI_DEFAULT);

            if (oci_fetch($stmt)) {
                $payload['authority'] = 0;
                $payload['sn'] = $_POST['serial_no'];

                $username = oci_result($stmt, "NAME");
                oci_free_statement($stmt);

                if (isset($_POST['IDNumber'])) //身分證登入
                {
                    $sql = "SELECT ID,NAME FROM SIGNUPDATA WHERE SCHOOL_ID='$SCHOOL_ID' AND YEAR='$ACT_YEAR_NO' AND SIGNUP_SN=:sn ";
                    $stmt = oci_parse($conn, $sql);
                    oci_bind_by_name($stmt, ':sn',  $_POST['serial_no']);
                    oci_execute($stmt, OCI_DEFAULT);

                    if (oci_fetch($stmt) && oci_result($stmt, "ID") === $_POST['IDNumber']) {
                        $payload['authority'] = 1;
                        $username = oci_result($stmt, "NAME");
                    } else
                        throw new Exception("帳號密碼或身分證錯誤", 401);
                    oci_free_statement($stmt);
                }
            } else
                throw new Exception("帳號或密碼錯誤", 401);

            $Token = new Token($conn, JWT::getToken($payload));
            $token = $Token->refresh();
            setcookie('username', $username, 0, ROOTDIR);
        }


        $result['access_token'] = $token;
        $result['token_type'] = "Bearer";
        // $result['expires_in'] = 1800;

        // setcookie('token', $token, $cookie_options_httponly);
        $cookieOpt = "token=" . $token . ";" . getCookieOptions($cookie_options_httponly);
        header("Set-Cookie: " . $cookieOpt, false);
    } else throw new Exception("Method Not Allowed", 405);
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
