<?php
header('Content-Type:application/json');
$result = array('data' => array('dept' => array(), 'group' => array(), 'status' => array()));
try {
    require_once('../db.php');
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {

        $stmt = oci_parse($conn, "SELECT * FROM DEPARTMENT");
        if (!oci_execute($stmt)) {
            $error = analyzeError(oci_error()['message']);
            throw new Exception($error['message'], $error['code']);
        }
        while ($row = oci_fetch_assoc($stmt))
            $result['data']['dept'][] = array('dept_id' => $row['DEPT_ID'], 'name' => $row['NAME']);
        oci_free_statement($stmt);

        $stmt = oci_parse($conn, "SELECT * FROM DEPT_GROUP");
        if (!oci_execute($stmt)) {
            $error = analyzeError(oci_error()['message']);
            throw new Exception($error['message'], $error['code']);
        }
        while ($row = oci_fetch_assoc($stmt))
            $result['data']['group'][$row['DEPT_ID']][] = array('group_id' => $row['GROUP_ID'], 'name' => $row['NAME']);
        oci_free_statement($stmt);

        $stmt = oci_parse($conn, "SELECT * FROM DEPT_STATUS");
        if (!oci_execute($stmt)) {
            $error = analyzeError(oci_error()['message']);
            throw new Exception($error['message'], $error['code']);
        }
        while ($row = oci_fetch_assoc($stmt))
            $result['data']['status'][$row['DEPT_ID']][$row['GROUP_ID']][] = array('status_id' => $row['STATUS_ID'], 'name' => $row['NAME']);
        oci_free_statement($stmt);
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
