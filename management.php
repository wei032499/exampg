<?php
require_once('./API/common/db.php');
$post_processing = array();
try {
    require_once('./management/news.php');

    /*if (!isset($_COOKIE['token']))
        require_once('./signup/alter_login.php');
    else {
        $Token = new Token($conn, $_COOKIE['token']);
        $payload = $Token->verify();
        setcookie('token', $Token->refresh(), $cookie_options_httponly);
        setcookie('username', $_COOKIE['username'], $cookie_options);
        if ($payload === false || $payload['authority'] !== 1)
            require_once('./signup/alter_login.php');
    }*/
} catch (Exception $e) {
    setHeader($e->getCode());
    $result = array();
    $result['code'] = $e->getCode();
    $result['message'] = $e->getMessage();
    header('Content-Type:application/json');
    echo json_encode($result);
    header("Location: ./management.php");
}

register_shutdown_function("shutdown_function", $post_processing);
exit(); // You need to call this to send the response immediately
