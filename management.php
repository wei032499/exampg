<?php
require_once('./API/common/db.php');
$post_processing = array();
try {
    require_once('./management/news.php');
} catch (Exception $e) {
    setHeader($e->getCode());
    $result = array();
    $result['code'] = $e->getCode();
    $result['message'] = $e->getMessage();
    // header('Content-Type:application/json');
    // echo json_encode($result);
    header("Location: ./management.php");
}

register_shutdown_function("shutdown_function", $post_processing);
exit(); // You need to call this to send the response immediately
