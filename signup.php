<?php
require_once('./API/db.php');
try {
    if (!isset($_COOKIE['token']))
        require_once('./signup/signup_login.php');
    else {
        $Token = new Token($conn, $_COOKIE['token']);
        $payload = $Token->verify();
        setcookie('token', $Token->refresh(), $cookie_options_httponly);

        if ($payload === false)
            require_once('./signup/signup_login.php');
        else if ($payload['status'] !== 0)
            echo "<script>alert('報名表已填寫完成！');window.location.replace('./intro_registration.php#signup_form');</script>";
        else if (!isset($_GET['step']))
            header("Location: ./signup.php?step=2");
        else if ($_GET['step'] === "2")
            require_once('./signup/signup_consent.php');
        else if ($_GET['step'] === "3")
            require_once('./signup/signup_form.php');
        else if ($_GET['step'] === "4")
            require_once('./signup/signup_confirm.php');
        else if ($_GET['step'] === "5")
            require_once('./signup/signup_completed.php');
        else
            header("Location: ./signup.php");
    }
} catch (Exception $e) {
    setHeader($e->getCode());
    $result['code'] = $e->getCode();
    $result['message'] = $e->getMessage();
    $result['line'] = $e->getLine();
    header('Content-Type:application/json');
    echo json_encode($result);
    // header("Location: ./signup.php");
}
