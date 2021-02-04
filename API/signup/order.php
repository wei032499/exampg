<?php
header('Content-Type:application/json');
$result = array();
try {
    require_once('../db.php');
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $result['data'] = array('email' => 'example@gmail.com', 'account_no' => 'account_noTEST');
        $result['status'] = 'success';
    } else
        throw new Exception("Method Not Allowed", 405);
} catch (Exception $e) {
    @oci_rollback($conn);
    @oci_close($conn);
    setHeader($e->getCode());
    $result['code'] = $e->getCode(); //$e->getCode();
    $result['message'] = $e->getMessage();

    //$e->getMessage() . " on line " . $e->getLine()
}

echo json_encode($result);
