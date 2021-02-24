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
    $cookieOpt = "token=" . $Token->refresh() . ";";
    foreach ($cookie_options_httponly as $key => $value) {
        if ($key === "httpOnly") {
            if ($value === true)
                $cookieOpt .=  "httpOnly;";
        } else
            $cookieOpt .= $key . "=" . $value . ";";
    }
    header("Set-Cookie: " . $cookieOpt, false);
    $result['status'] = $payload['status'];
    $result['authority'] = $payload['authority'];
} catch (Exception $e) {
    oci_rollback($conn);
    setHeader($e->getCode());
    $result = array();
    $result['code'] = $e->getCode();
    $result['message'] = $e->getMessage();
    //$e->getMessage() . " on line " . $e->getLine()
}



oci_close($conn);
echo json_encode($result);
exit(); // You need to call this to send the response immediately