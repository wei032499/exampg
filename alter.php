<?php
require_once('./API/db.php');
try {

    if (!isset($_COOKIE['token']))
        require_once('./signup/alter_login.php');
    else {
        $Token = new Token($conn, $_COOKIE['token']);
        $payload = $Token->verify();
        setcookie('token', $Token->refresh(), $cookie_options_httponly);
        setcookie('username', $_COOKIE['username'], $cookie_options);
        if ($payload === false || $payload['authority'] !== 1)
            require_once('./signup/alter_login.php');
        else if ($payload['status'] === 0)
            echo "<script>alert('請先填寫報名表！');window.location.replace('./signup.php');</script>";
        else if (!isset($_GET['step']))
            header("Location: ./alter.php?step=2");
        else if ($_GET['step'] === "2")
            require_once('./signup/alter_form.php');
        else if ($_GET['step'] === "3")
            require_once('./signup/alter_confirm.php');
        else
            header("Location: ./alter.php");
    }
} catch (Exception $e) {
    setHeader($e->getCode());
    $result['code'] = $e->getCode();
    $result['message'] = $e->getMessage();
    header('Content-Type:application/json');
    echo json_encode($result);
    header("Location: ./alter.php");
}
