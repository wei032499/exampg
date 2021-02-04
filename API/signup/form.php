<?php

header('Content-Type:application/json');
$result = array();
try {
    require_once('../db.php');
    if (!isset($_COOKIE['token']))
        throw new Exception("Unauthorized", 401);
    $Token = new Token($conn, $_COOKIE['token']);
    $payload = $Token->verify();
    if ($payload === false)
        throw new Exception("Unauthorized", 401);

    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        $params = array();
        $stmt = oci_parse($conn, "SELECT * FROM  signupdata  WHERE sn=? ");
        $params[] =  $payload['sn'];
        DynamicBindVariables($stmt, $params);

        if (!oci_execute($stmt)) //oci_execute($stmt) 
        {
            $error = analyzeError(oci_error()['message']);
            throw new Exception($error['message'], $error['code']);
        }
        if ($row = oci_fetch_assoc($stmt)) //$row = oci_fetch_assoc($stmt)
            $result['data'] = $row;
        oci_free_statement($stmt);
    } else if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        if ($payload['status'] !== 0)
            throw new Exception("Forbidden", 403);


        /*$params = array();
            $params['name'] = $_POST['name'];
            DynamicInsert($conn, 'signupdata', $params);*/
    } else if ($_SERVER['REQUEST_METHOD'] === 'PUT') {

        if ($payload['status'] !== 1 || $payload['authority'] !== 1)
            throw new Exception("Forbidden", 403);
        $params = array();


        // DynamicUpdate($conn, $table, $params, $where);
    }

    setcookie('token', $Token->refresh(), $cookie_options_httponly);
} catch (Exception $e) {
    @oci_rollback($conn);
    @oci_close($conn);
    setHeader($e->getCode());
    $result['code'] = $e->getCode(); //$e->getCode();
    $result['message'] = $e->getMessage();

    //$e->getMessage() . " on line " . $e->getLine()
}

echo json_encode($result);
