<?php
header('Content-Type:application/json');
$result = array('data' => array('dept' => array(), 'group' => array(), 'status' => array()));
try {
    require_once('../common/db.php');
    require('../common/variables.php');
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        if (!isset($_COOKIE['token']))
            throw new Exception("Unauthorized", 401);
        $Token = new Token($conn, $_COOKIE['token']);
        $payload = $Token->verify();
        if ($payload === false)
            throw new Exception("Unauthorized", 401);
        if (substr($payload['account_no'], 7, 1) <= "4") {
            $sql_add = " oral_flag ='1' ";
        } else if (substr($payload['account_no'], 7, 1) <= "8") {
            $sql_add = " oral_flag ='2' ";
        } else if (substr($payload['account_no'], 7, 1) == "9") {
            $sql_add = " oral_flag in ('1','2') ";
        } else {
            $sql_add = " oral_flag ='9' ";
        }

        $sql = "SELECT ID,NAME FROM DEPARTMENT where  school_id='" . $SCHOOL_ID . "' and year='" . $ACT_YEAR_NO . "' and $sql_add  ORDER BY ID";
        $stmt = oci_parse($conn, $sql);
        if (!oci_execute($stmt, OCI_DEFAULT)) {
            $error = analyzeError(oci_error()['message']);
            throw new Exception($error['message'], $error['code']);
        }
        while (oci_fetch($stmt))
            $result['data']['dept'][] = array('dept_id' => oci_result($stmt, 'ID'), 'name' => oci_result($stmt, 'NAME'));
        oci_free_statement($stmt);

        $stmt = oci_parse($conn, "SELECT DEPT_ID,ID,NAME FROM ORGANIZE WHERE school_id='" . $SCHOOL_ID . "' and year='" . $ACT_YEAR_NO . "'");
        if (!oci_execute($stmt, OCI_DEFAULT)) {
            $error = analyzeError(oci_error()['message']);
            throw new Exception($error['message'], $error['code']);
        }
        while (oci_fetch($stmt))
            $result['data']['group'][oci_result($stmt, 'DEPT_ID')][] = array('group_id' => oci_result($stmt, 'ID'), 'name' => oci_result($stmt, 'NAME'));
        oci_free_statement($stmt);
        $stmt = oci_parse($conn, "SELECT ORGANIZE.DEPT_ID,ORASTATUS.ORGANIZE_ID,ORASTATUS.ID,ORASTATUS.NAME FROM ORASTATUS INNER JOIN ORGANIZE ON ORASTATUS.ORGANIZE_ID=ORGANIZE.ID AND ORASTATUS.school_id=ORGANIZE.school_id AND ORASTATUS.year=ORGANIZE.year WHERE ORASTATUS.school_id='" . $SCHOOL_ID . "' and ORASTATUS.year='" . $ACT_YEAR_NO . "'");
        if (!oci_execute($stmt, OCI_DEFAULT)) {
            $error = analyzeError(oci_error()['message']);
            throw new Exception($error['message'], $error['code']);
        }
        while (oci_fetch($stmt))
            $result['data']['status'][oci_result($stmt, 'DEPT_ID')][oci_result($stmt, 'ORGANIZE_ID')][] = array('status_id' => oci_result($stmt, 'ID'), 'name' => oci_result($stmt, 'NAME'));
        oci_free_statement($stmt);
    } else
        throw new Exception("Method Not Allowed", 405);
} catch (Exception $e) {
    @oci_rollback($conn);
    setHeader($e->getCode());
    $result = array();
    $result['code'] = $e->getCode(); //$e->getCode();
    $result['message'] = $e->getMessage();
    $result['line'] = $e->getLine();
}
@oci_close($conn);

echo json_encode($result);
