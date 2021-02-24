<?php
require_once('./API/common/db.php');

try {
    if (!isset($_COOKIE['token']))
        require_once('./enroll/queue_login.php');
    else {
        $payload = JWT::verifyToken($_COOKIE['token']);
        if ($payload === false || !isset($payload['id']) || !isset($payload['sid']))
            require_once('./enroll/queue_login.php');
        else
            require_once("./enroll/queue_form.php");
    }
} catch (Exception $e) {
    setHeader($e->getCode());
    $result = array();
    $result['code'] = $e->getCode();
    $result['message'] = $e->getMessage();
    // header('Content-Type:application/json');
    // echo json_encode($result);
    header("Location: ./enroll_queue.php");
}


exit(); // You need to call this to send the response immediately
