<?php
header('Content-Type:application/json');
$result = array();

try {
    require_once('../common/functions.php');
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($_POST['oper']) && $_POST['oper'] === "logout") {
            session_start();
            session_destroy();
        } else {
            if ($_POST['account'] === "exampg" && $_POST['pwd'] === "exampg_ncue") {
                session_start();
                $_SESSION['username'] = 'Admin';
            } else
                throw new Exception("登入失敗！", 401);
        }
    } else
        throw new Exception("Method Not Allowed", 405);
} catch (Exception $e) {
    setHeader($e->getCode());
    $result = array();
    $result['code'] = $e->getCode();
    $result['message'] = $e->getMessage();
    $result['line'] = $e->getLine();
}



oci_close($conn);
echo json_encode($result);
exit(); // You need to call this to send the response immediately