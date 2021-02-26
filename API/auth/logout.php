<?php
header('Content-Type:application/json');
$result = array();

try {
    require_once('../common/functions.php');
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($_POST['oper']) && $_POST['oper'] === 'queue' && isset($_COOKIE['token'])) {
            $payload = JWT::verifyToken($_COOKIE['token']);
            if ($payload !== false && isset($payload['id']) && isset($payload['sid'])) {
                unset($payload['id']);
                unset($payload['sid']);
                $token = JWT::getToken($payload);
                // setcookie('token', $token, $cookie_options_httponly);
                $cookieOpt = "token=" . $token . ";" . getCookieOptions($cookie_options_httponly);
                header("Set-Cookie: " . $cookieOpt, false);
            }
        } else
            clearCookie();
        $result['result'] = "success";
    } else
        throw new Exception("Method Not Allowed", 405);
} catch (Exception $e) {
    setHeader($e->getCode());
    $result = array();
    $result['code'] = $e->getCode();
    $result['message'] = $e->getMessage();
}

echo json_encode($result);
