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
    // setcookie('token', $Token->refresh(), $cookie_options_httponly);
    $cookieOpt = "token=" . $Token->refresh() . ";" . getCookieOptions($cookie_options_httponly);
    header("Set-Cookie: " . $cookieOpt, false);
    $result['status'] = $payload['status'];
    $result['authority'] = $payload['authority'];
} catch (Exception $e) {
    oci_rollback($conn);
    setHeader($e->getCode());
    $result = array();
    $result['code'] = $e->getCode();
    $result['message'] = $e->getMessage();
}



oci_close($conn);
echo json_encode($result);
exit();
