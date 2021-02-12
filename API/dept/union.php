<?php
header('Content-Type:application/json');
$result = array('data' => array());
try {
    require_once('../common/db.php');
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        if (!isset($_COOKIE['token']))
            throw new Exception("Unauthorized", 401);
        $Token = new Token($conn, $_COOKIE['token']);
        $payload = $Token->verify();
        if ($payload === false)
            throw new Exception("Unauthorized", 401);

        //目前是以單一考科決定可選聯招系所
        //是否有 多選考科目決定可選聯招系所??? 若無，可否直接以該系所是否為聯招決定?
        //1:一般聯合 2:同系聯合 3:擇優 5:不須選考科組別之聯合
        if (isset($_GET['dept_id']) && !isset($_GET['subject_id'])) {
            $stmt = oci_parse($conn, "SELECT ID,NAME FROM DEPARTMENT WHERE SCHOOL_ID='$SCHOOL_ID' AND YEAR='$ACT_YEAR_NO' AND substr(UNION_FLAG,1,1)='5' AND UNION_FLAG=(SELECT UNION_FLAG FROM DEPARTMENT WHERE SCHOOL_ID='$SCHOOL_ID' AND YEAR='$ACT_YEAR_NO' AND ID=:dept_id)");
            oci_bind_by_name($stmt, ':dept_id',  $_GET['dept_id']);
            if (!oci_execute($stmt, OCI_DEFAULT)) {
                $error = analyzeError(oci_error()['message']);
                throw new Exception($error['message'], $error['code']);
            }
            while (oci_fetch($stmt))
                $result['data'][] = array('dept_id' => oci_result($stmt, 'ID'), 'name' => oci_result($stmt, 'NAME'));
            oci_free_statement($stmt);
        } else if (!isset($_GET['dept_id']) && isset($_GET['subject_id'])) {
            $stmt = oci_parse($conn, "SELECT ID,NAME FROM DEPARTMENT WHERE SCHOOL_ID='$SCHOOL_ID' AND YEAR='$ACT_YEAR_NO' AND substr(UNION_FLAG,1,1)!='5' AND UNION_FLAG=(SELECT UNION_FLAG FROM SUBJECT WHERE SCHOOL_ID='$SCHOOL_ID' AND YEAR='$ACT_YEAR_NO' AND ID=:subject_id)"); // ID=substr(:subject_id,1,3)
            oci_bind_by_name($stmt, ':subject_id',  $_GET['subject_id']);
            if (!oci_execute($stmt, OCI_DEFAULT)) {
                $error = analyzeError(oci_error()['message']);
                throw new Exception($error['message'], $error['code']);
            }
            while (oci_fetch($stmt))
                $result['data'][] = array('dept_id' => oci_result($stmt, 'ID'), 'name' => oci_result($stmt, 'NAME'));
            oci_free_statement($stmt);
        } else
            throw new Exception("Bad Request", 400);
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
oci_close($conn);

echo json_encode($result);
