<?php
require_once('./API/db.php');
try {

    if (!isset($_COOKIE['token']))
        require_once('./ticket/login.php');
    else {
        $Token = new Token($conn, $_COOKIE['token']);
        $payload = $Token->verify();
        setcookie('token', $Token->refresh(), $cookie_options_httponly);
        setcookie('username', $_COOKIE['username'], $cookie_options);
        if ($payload === false || $payload['authority'] !== 1)
            require_once('./ticket/login.php');
        else if ($payload['status'] === 0)
            echo "<script>alert('請先填寫報名表！');window.location.replace('./signup.php');</script>";
        else if ($payload['status'] === 1)
            echo "<script>alert('請先確認資料！');window.location.replace('./complete.php');</script>";
        else if (!isset($_GET['step']))
            header("Location: ./ticket.php?step=2");
        else
            header("Location: ./ticket.php");
    }
} catch (Exception $e) {
    setHeader($e->getCode());
    $result['code'] = $e->getCode();
    $result['message'] = $e->getMessage();
    header('Content-Type:application/json');
    echo json_encode($result);
    header("Location: ./ticket.php");
}
