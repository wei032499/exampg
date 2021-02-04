<?php
header('Content-Type:application/json');
$result = array();

try {
    require_once('../functions.php');
    if ($_SERVER['REQUEST_METHOD'] !== 'POST')
        throw new Exception("Method Not Allowed", 405);
    clearCookie();
    $result['result'] = "success";
} catch (Exception $e) {
    @oci_rollback($conn);
    @oci_close($conn);
    setHeader($e->getCode());
    $result['code'] = $e->getCode();
    $result['message'] = $e->getMessage();
    //$e->getMessage() . " on line " . $e->getLine()
}

echo json_encode($result);
