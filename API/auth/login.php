<?php

/**
 *  $payload['status']：
 *      0 => 尚未填寫報名表
 * 
 *  $payload['authority']：
 *      0 => 一般登入
 *      1 => 身分證登入
 */
header('Content-Type:application/json');
$result = array();
$payload = array('iss' => 'ncue', 'iat' => time(), 'exp' => time() + 1800);
try {
    require_once('../db.php');

    $sql = "SELECT * FROM signupdata INNER JOIN sn_db ON sn_db.sn = signupdata.sn WHERE sn_db.sn = :sn AND sn_db.pwd = :pwd ";
    $paramList = array();
    $paramList[] =  $_POST['serial_no'];
    $paramList[] =  $_POST['pwd'];

    // $stmt = $conn->prepare($sql); //oci_parse($conn, $sql);
    $stmt = oci_parse($conn, $sql);
    // DynamicBindVariables($stmt, $paramList);

    oci_bind_by_name($stmt, ':sn', $_POST['serial_no']);
    oci_bind_by_name($stmt, ':pwd', $_POST['pwd']);
    if (!oci_execute($stmt)) // $stmt->execute()
    {
        $error = analyzeError(oci_error()['message']);
        throw new Exception($error['message'], $error['code']);
    }
    // $row = $stmt->get_result()->fetch_assoc(); 
    $row = oci_fetch_assoc($stmt);
    if ($row === false)
        throw new Exception("登入失敗！", 401);
    else {
        $payload['authority'] = 0;
        $payload['status'] = 0;
        $payload['sn'] = $_POST['serial_no'];
        $payload['last_modified'] = $row['LAST_MODIFIED'];
    }
    oci_free_statement($stmt);
    // $stmt->close();
    if (isset($_POST['IDNumber']) && $_POST['IDNumber'] !== "") {

        if ($row['ID'] !== $_POST['IDNumber'])
            throw new Exception("登入失敗！", 401);
        else
            $payload['authority'] = 1;
    }

    $token = JWT::getToken($payload);
    $result['access_token'] = $token;
    $result['token_type'] = "Bearer";
    $result['expires_in'] = 1800;
    header("Cache-Control: private");


    setcookie('token', $token, $cookie_options_httponly);
    setcookie('username', $row['NAME'], $cookie_options);
} catch (Exception $e) {

    @oci_rollback($conn);
    setHeader($e->getCode());
    $result['code'] = $e->getCode();
    $result['message'] = $e->getMessage();
    $result['line'] = $e->getLine();



    //$e->getMessage() . " on line " . $e->getLine()
}
@oci_close($conn);

echo json_encode($result);
