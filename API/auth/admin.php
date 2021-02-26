<?php
header('Content-Type:application/json');
$payload = array('iss' => 'ncue', 'iat' => time(), 'exp' => time() + 3600);
$result = array();
try {
    require_once('../common/functions.php');
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($_POST['oper']) && $_POST['oper'] === "logout") {
            if (isset($_COOKIE['token'])) {
                $payload = JWT::verifyToken($_COOKIE['token']);
                if (isset($payload['admin'])) {
                    unset($payload['admin']);

                    $token = JWT::getToken($payload);
                    $cookieOpt = "token=" . $token . ";" . getCookieOptions($cookie_options_httponly);
                    header("Set-Cookie: " . $cookieOpt);
                }
            }
        } else if (!isset($_POST['oper'])) {
            if ($_POST['account'] === "exampg" && $_POST['pwd'] === "exampg_ncue") {
                if (isset($_COOKIE['token'])) {
                    $payload = JWT::verifyToken($_COOKIE['token']);
                    if ($payload === false)
                        $payload = array('iss' => 'ncue', 'iat' => time(), 'exp' => time() + 3600);
                }
                $payload['admin'] = 0;
                $token = JWT::getToken($payload);
                $cookieOpt = "token=" . $token . ";" . getCookieOptions($cookie_options_httponly);
                header("Set-Cookie: " . $cookieOpt);
            } else
                throw new Exception("登入失敗！", 401);
        } else
            throw new Exception("Bad Request", 400);
    } else
        throw new Exception("Method Not Allowed", 405);
} catch (Exception $e) {
    setHeader($e->getCode());
    $result = array();
    $result['code'] = $e->getCode();
    $result['message'] = $e->getMessage();
    //$result['line'] = $e->getLine();
}



oci_close($conn);
echo json_encode($result);
exit();
