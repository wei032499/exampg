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


        $stmt = oci_parse($conn, "SELECT ACCOUNT_NO FROM SN_DB WHERE SCHOOL_ID='$SCHOOL_ID' AND YEAR='$ACT_YEAR_NO' AND SN=:sn");
        oci_bind_by_name($stmt, ':sn',  $payload['sn']);
        if (!oci_execute($stmt, OCI_DEFAULT)) {
            $error = analyzeError(oci_error()['message']);
            throw new Exception($error['message'], $error['code']);
        }
        oci_fetch($stmt);
        $account_no = oci_result($stmt, 'ACCOUNT_NO');
        oci_free_statement($stmt);
        if (substr($account_no, 7, 1) <= "4") {
            $sql_add = " oral_flag ='1' ";
        } else if (substr($account_no, 7, 1) <= "8") {
            $sql_add = " oral_flag ='2' ";
        } else if (substr($account_no, 7, 1) == "9") {
            $sql_add = " oral_flag in ('1','2') ";
        } else {
            $sql_add = " oral_flag ='9' ";
        }

        //department
        //E_PLACE：1=>有面試，限彰化考區、2=>可選彰化(1)或台北(2)考區
        //upload_type 審查資料繳交方式:  1:郵寄  2:上傳  3:郵寄+上傳
        $interview = "('面試','複試','面試(含資料審查)')";
        $sql = "SELECT ID,NAME,UPLOAD_TYPE,(SELECT CASE WHEN COUNT(1) > 0 THEN 1 ELSE 2 END FROM SUBJECT WHERE SCHOOL_ID='$SCHOOL_ID' AND YEAR='$ACT_YEAR_NO' AND substr(id,1,3)=DEPARTMENT.ID AND NAME IN $interview) AS E_PLACE FROM DEPARTMENT WHERE  SCHOOL_ID='$SCHOOL_ID' AND YEAR='$ACT_YEAR_NO' AND $sql_add  ORDER BY ID";

        // $sql = "SELECT ID,NAME,UPLOAD_TYPE FROM DEPARTMENT where  school_id='$SCHOOL_ID' and year='$ACT_YEAR_NO' and $sql_add  ORDER BY ID";
        $stmt = oci_parse($conn, $sql);
        if (!oci_execute($stmt, OCI_DEFAULT)) {
            $error = analyzeError(oci_error()['message']);
            throw new Exception($error['message'], $error['code']);
        }
        while (oci_fetch($stmt))
            $result['data']['dept'][] = array('dept_id' => oci_result($stmt, 'ID'), 'name' => oci_result($stmt, 'NAME'), 'upload_type' => intval(oci_result($stmt, 'UPLOAD_TYPE')), 'e_place' => intval(oci_result($stmt, 'E_PLACE')));
        oci_free_statement($stmt);


        //group
        $stmt = oci_parse($conn, "SELECT DEPT_ID,ID,NAME FROM ORGANIZE WHERE school_id='$SCHOOL_ID' and year='$ACT_YEAR_NO'");
        if (!oci_execute($stmt, OCI_DEFAULT)) {
            $error = analyzeError(oci_error()['message']);
            throw new Exception($error['message'], $error['code']);
        }
        while (oci_fetch($stmt))
            $result['data']['group'][oci_result($stmt, 'DEPT_ID')][] = array('group_id' => oci_result($stmt, 'ID'), 'name' => oci_result($stmt, 'NAME'));
        oci_free_statement($stmt);

        //status
        // $sql = "SELECT ORGANIZE.DEPT_ID,ORASTATUS.ORGANIZE_ID,ORASTATUS.ID,ORASTATUS.NAME FROM ORASTATUS INNER JOIN ORGANIZE ON ORASTATUS.ORGANIZE_ID=ORGANIZE.ID AND ORASTATUS.school_id=ORGANIZE.school_id AND ORASTATUS.year=ORGANIZE.year WHERE ORASTATUS.school_id='$SCHOOL_ID' and ORASTATUS.year='$ACT_YEAR_NO'";
        $sql = "SELECT ID, Name, ORGANIZE_ID, substr(ID,1,3) as DEPT_ID FROM ORASTATUS WHERE SCHOOL_ID='$SCHOOL_ID' and YEAR='$ACT_YEAR_NO'";
        $stmt = oci_parse($conn, $sql);
        if (!oci_execute($stmt, OCI_DEFAULT)) {
            $error = analyzeError(oci_error()['message']);
            throw new Exception($error['message'], $error['code']);
        }
        while (oci_fetch($stmt))
            $result['data']['status'][oci_result($stmt, 'DEPT_ID')][oci_result($stmt, 'ORGANIZE_ID')][] = array('status_id' => oci_result($stmt, 'ID'), 'name' => oci_result($stmt, 'NAME'));
        oci_free_statement($stmt);

        //subect
        $stmt = oci_parse($conn, "SELECT ID, NAME, ORASTATUS_ID, substr(ID,6,1) as SECTION, substr(ID,1,4) as GROUP_ID, substr(ID,1,3) as DEPT_ID FROM SUBJECT WHERE SCHOOL_ID='$SCHOOL_ID' and YEAR='$ACT_YEAR_NO'");
        if (!oci_execute($stmt, OCI_DEFAULT)) {
            $error = analyzeError(oci_error()['message']);
            throw new Exception($error['message'], $error['code']);
        }
        while (oci_fetch($stmt))
            $result['data']['subject'][oci_result($stmt, 'DEPT_ID')][oci_result($stmt, 'GROUP_ID')][oci_result($stmt, 'ORASTATUS_ID')][oci_result($stmt, 'SECTION')][] = array('subject_id' => oci_result($stmt, 'ID'), 'name' => oci_result($stmt, 'NAME'));
        oci_free_statement($stmt);

        /*
        //特定系所中，選出同組(4)同身分(5)同SECTION(6)中 科目>1(可選科目)的所有科目
        $sql = "select * from subject where  school_id='$SCHOOL_ID' and year='$ACT_YEAR_NO' and substr(id,1,6) in (select substr(id,1,6) from subject where  school_id='$SCHOOL_ID' and year='$ACT_YEAR_NO' and substr(id,1,3)='dept_id' group by substr(id,1,6) having count(*)>1)  order by id";
        $sql = "select * from subject where  school_id='$SCHOOL_ID' and year='$ACT_YEAR_NO' and substr(id,1,6) in (select substr(id,1,6) from subject where  school_id='$SCHOOL_ID' and year='$ACT_YEAR_NO' and substr(id,1,4)='" . $_SESSION['alter_dept_id'] . $_SESSION['o_org_id'] . "' and substr(id,6,1)!='0' and substr(id,7,1)!='0' group by substr(id,1,6) having count(*)>=1)  order by id";
        */
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
