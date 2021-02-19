<?php
header('Content-Type:application/json');
$result = array('data' => array());
$post_processing = array();
try {
    require_once('../common/db.php');
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    } else
        throw new Exception("Method Not Allowed", 405);
} catch (Exception $e) {
    oci_rollback($conn);
    setHeader($e->getCode());
    $result = array();
    $result['code'] = $e->getCode(); //$e->getCode();
    $result['message'] = $e->getMessage();
    $result['line'] = $e->getLine();
}

register_shutdown_function("shutdown_function", $post_processing);

oci_close($conn);
echo json_encode($result);
exit(); // You need to call this to send the response immediately