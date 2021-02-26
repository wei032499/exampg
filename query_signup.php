<?php
try {
    require_once('./API/common/db.php');

    if (!isset($_COOKIE['token']))
        require_once('./query/signup_login.php');
    else {
        $Token = new Token($conn, $_COOKIE['token']);
        $payload = $Token->verify();
        // setcookie('token', $Token->refresh(), $cookie_options_httponly);
        $cookieOpt = "token=" . $Token->refresh() . ";" . getCookieOptions($cookie_options_httponly);
        header("Set-Cookie: " . $cookieOpt, false);
        if ($payload === false || $payload['authority'] !== 1)
            require_once('./query/signup_login.php');
        else if ($payload['status'] !== 2 && $payload['status'] !== 3) {
            header("Content-Type:text/html; charset=utf-8");
            if ($payload['status'] === 0)
                echo "<script>alert('您尚未繳費或繳交的費用尚未入帳，若您已繳費，請30分鐘後再試一次。');window.location.replace('./');</script>";
            else if ($payload['status'] === 1)
                echo "<script>alert('尚未填寫報名表！');window.location.replace('./signup.php');</script>";
            else if ($payload['status'] === 2)
                echo "<script>alert('請先完成資料確認，方可查詢報名資料。');window.location.replace('./confirm.php?step=2');</script>";
            else if ($payload['status'] === 3)
                echo "<script>alert('報名完成，資料已鎖定！');window.location.replace('./');</script>";
            else
                echo "<script>alert('ERROR！');window.location.replace('./');</script>";
        } else if (!isset($_GET['step']))
            header("Location: ./query_signup.php?step=2");
        else if ($_GET['step'] === "2")
            require_once('./query/signup_form.php');
        else
            header("Location: ./query_signup.php");
    }
} catch (Exception $e) {
    setHeader($e->getCode());
    $result = array();
    $result['code'] = $e->getCode();
    $result['message'] = $e->getMessage();
    // header('Content-Type:application/json');
    // echo json_encode($result);
    header("Location: ./query_signup.php");
}
