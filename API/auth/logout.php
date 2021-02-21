<?php
header('Content-Type:application/json');
$result = array();

try {
    require_once('../common/functions.php');
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($_POST['oper']) && $_POST['oper'] === 'queue') {
            if (isset($_COOKIE['token'])) {
                $Token = new Token($conn, $_COOKIE['token']);
                $payload = $Token->verify();
                if ($payload !== false && isset($payload['id']) && isset($payload['sid'])) {
                    unset($payload['id']);
                    unset($payload['sid']);
                    $token = JWT::getToken($payload);
                    header("Cache-Control: private");
                    setcookie('token', $token, $cookie_options_httponly);
                }
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
    //$e->getMessage() . " on line " . $e->getLine()
}

echo json_encode($result);
