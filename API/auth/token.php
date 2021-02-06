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
    setcookie('token', $Token->refresh(), $cookie_options_httponly);
    setcookie('username', $_COOKIE['username'], $cookie_options);
    $result['status'] = $payload['status'];
    $result['authority'] = $payload['authority'];
} catch (Exception $e) {
    @oci_rollback($conn);
    setHeader($e->getCode());
    $result = array();
    $result['code'] = $e->getCode();
    $result['message'] = $e->getMessage();
    //$e->getMessage() . " on line " . $e->getLine()
}
@oci_close($conn);

echo json_encode($result);
