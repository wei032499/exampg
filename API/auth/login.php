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
$result = array();
$payload = array('iss' => 'ncue', 'iat' => time(), 'exp' => time() + 1800);
try {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        require_once('../common/db.php');
        $sql = "SELECT ID,NAME FROM SN_DB WHERE SCHOOL_ID='$SCHOOL_ID' AND YEAR='$ACT_YEAR_NO' AND SN=:sn AND PWD=:pwd ";
        $stmt = oci_parse($conn, $sql);
        oci_bind_by_name($stmt, ':sn',  $_POST['serial_no']);
        oci_bind_by_name($stmt, ':pwd',  $_POST['pwd']);

        if (!oci_execute($stmt, OCI_DEFAULT)) {
            $error = analyzeError(oci_error()['message']);
            throw new Exception($error['message'], $error['code']);
        }

        if (oci_fetch($stmt)) {
            $payload['authority'] = 0;
            $payload['sn'] = $_POST['serial_no'];
            $payload['pwd'] = hash('sha256', $_POST['pwd']);

            $username = oci_result($stmt, "NAME");


            if (isset($_POST['IDNumber'])) {
                if (oci_result($stmt, "ID") === $_POST['IDNumber'])
                    $payload['authority'] = 1;
                else
                    throw new Exception("登入失敗！", 401);
            }
        } else
            throw new Exception("登入失敗！", 401);

        oci_free_statement($stmt);

        $Token = new Token($conn, JWT::getToken($payload));
        $token = $Token->refresh();
        $result['access_token'] = $token;
        $result['token_type'] = "Bearer";
        $result['expires_in'] = 1800;
        header("Cache-Control: private");


        setcookie('token', $token, $cookie_options_httponly);
        setcookie('username', $username, $cookie_options);
    } else throw new Exception("Method Not Allowed", 405);
} catch (Exception $e) {

    @oci_rollback($conn);
    setHeader($e->getCode());
    $result = array();
    $result['code'] = $e->getCode();
    $result['message'] = $e->getMessage();
    $result['line'] = $e->getLine();



    //$e->getMessage() . " on line " . $e->getLine()
}
@oci_close($conn);

echo json_encode($result);
