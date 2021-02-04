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
    setcookie('token', JWT::getToken($payload), $cookie_options_httponly);
    $result['status'] = $payload['status'];
    $result['authority'] = $payload['authority'];
} catch (Exception $e) {
    @oci_rollback($conn);
    @oci_close($conn);
    setHeader($e->getCode());
    $result['code'] = $e->getCode();
    $result['message'] = $e->getMessage();
    //$e->getMessage() . " on line " . $e->getLine()
}

echo json_encode($result);
