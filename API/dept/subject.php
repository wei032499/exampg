<?php
header('Content-Type:application/json');
$result = array('data' => array('dept' => array(), 'group' => array(), 'status' => array()));

try {
    require_once('../common/db.php');
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        if (!isset($_COOKIE['token']))
            throw new Exception("Unauthorized", 401);
        $Token = new Token($conn, $_COOKIE['token']);
        $payload = $Token->verify();
        if ($payload === false)
            throw new Exception("Unauthorized", 401);

        $stmt = oci_parse($conn, "SELECT ID, NAME FROM SUBJECT WHERE ID=:id AND SCHOOL_ID='$SCHOOL_ID' AND YEAR='$ACT_YEAR_NO'");
        oci_bind_by_name($stmt, ':id',  $_GET['id']);
        oci_execute($stmt, OCI_DEFAULT);
        if (oci_fetch($stmt))
            $result['data'] = array('id' => oci_result($stmt, 'ID'), 'name' => oci_result($stmt, 'NAME'));
        oci_free_statement($stmt);
    } else
        throw new Exception("Method Not Allowed", 405);
} catch (Exception $e) {
    oci_rollback($conn);
    setHeader($e->getCode());
    $result = array();
    $result['code'] = $e->getCode();
    $result['message'] = $e->getMessage();
    //$result['line'] = $e->getLine();
}



oci_close($conn);
echo json_encode($result);
exit();
