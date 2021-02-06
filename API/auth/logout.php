<?php
header('Content-Type:application/json');
$result = array();

try {
    require_once('../common/functions.php');
    if ($_SERVER['REQUEST_METHOD'] !== 'POST')
        throw new Exception("Method Not Allowed", 405);
    clearCookie();
    $result['result'] = "success";
} catch (Exception $e) {
    setHeader($e->getCode());
    $result = array();
    $result['code'] = $e->getCode();
    $result['message'] = $e->getMessage();
    //$e->getMessage() . " on line " . $e->getLine()
}

echo json_encode($result);
