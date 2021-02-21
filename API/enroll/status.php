<?php
header('Content-Type:application/json');
$result = array();
$post_processing = array();
try {
    require_once('../common/db.php');
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        if (!isset($_COOKIE['token']))
            throw new Exception("Unauthorized", 401);
        $payload = JWT::verifyToken($_COOKIE['token']);
        if ($payload === false || !isset($payload['id']) || !isset($payload['sid']))
            throw new Exception("Unauthorized", 401);

        $id = $payload['id'];
        $sid = $payload['sid'];
        $dept_id = substr($sid, 0, 3);

        // I don't know why !!
        $sql = "SELECT A.NAME,A.SEX,A.ORGANIZE_ID,A.ORASTATUS_ID,B.NAME AS DEPT_NAME,C.MAINNUMBER ,C.BACKNUMBER ,C.REPAIR,C.INCOMPLETE,substr(c.student_id,1,5) option_id
                FROM SIGNUPDATA A,DEPARTMENT B,PERSON C
                WHERE  a.school_id='$SCHOOL_ID' and a.year='$ACT_YEAR_NO' and a.school_id=b.school_id and a.year=b.year and
                c.school_id=a.school_id and c.year=a.year and A.ID = :id AND A.DEPT_ID=:dept_id AND C.ID=A.ID
                AND C.STUDENT_ID=:sid AND SUBSTR(C.STUDENT_ID,1,5)=A.ORASTATUS_ID AND B.ID = A.DEPT_ID AND C.ID=:id
                AND (MAINNUMBER>0 OR BACKNUMBER>0)
                union all
                SELECT A.NAME,A.SEX,A.ORGANIZE_ID,A.ORASTATUS_ID,B.NAME AS DEPT_NAME,C.MAINNUMBER ,C.BACKNUMBER ,C.REPAIR,C.INCOMPLETE,c.option_id
                FROM SIGNUPDATA A,DEPARTMENT B,union_priority_all C
                WHERE  a.school_id='$SCHOOL_ID' and a.year='$ACT_YEAR_NO' and a.school_id=b.school_id and a.year=b.year and c.school_id=a.school_id
                and c.year=a.year and A.ID = :id and a.ORASTATUS_ID=substr(c.student_id,1,5) AND C.ID=A.ID AND C.STUDENT_ID=:sid
                AND SUBSTR(C.STUDENT_ID,1,5)=A.ORASTATUS_ID AND B.ID = substr(option_id,1,3) AND C.ID=:id AND (MAINNUMBER>0 OR BACKNUMBER>0)
                union all
                SELECT A.NAME,A.SEX,A.ORGANIZE_ID,A.ORASTATUS_ID,B.NAME AS DEPT_NAME,C.MAINNUMBER ,C.BACKNUMBER ,C.REPAIR,C.INCOMPLETE,c.id option_id
                FROM SIGNUPDATA A,DEPARTMENT B,add_enroll C
                WHERE  a.school_id='$SCHOOL_ID' and a.year='$ACT_YEAR_NO' and a.school_id=b.school_id and a.year=b.year and c.school_id=a.school_id
                and c.year=a.year and A.ID = :id and a.ORASTATUS_ID=substr(c.student_id,1,5) AND C.person_ID=A.ID AND C.STUDENT_ID=:sid
                AND B.ID = substr(c.id,1,3) AND C.person_ID=:id AND (MAINNUMBER>0 OR BACKNUMBER>0) ";


        $stmt = oci_parse($conn, $sql);
        oci_bind_by_name($stmt, ':id', $id);
        oci_bind_by_name($stmt, ':sid', $sid);
        oci_bind_by_name($stmt, ':dept_id', $dept_id);
        oci_execute($stmt, OCI_DEFAULT);
        $nrows = oci_fetch_all($stmt, $results); //$nrows -->總筆數
        oci_free_statement($stmt);

        if ($nrows == 0) {
            $post_processing[] = function () use ($sql) {
                $mail_msg = $sql;
                sendMail(0, array('title' => "無符合條件的資料！(queue_anno.php)", 'content' => $mail_msg));
            };
            throw new  Exception('無符合條件的資料');
        }
        $result['name'] = $results['NAME'][0];
        $result['sex'] = $results['SEX'][0] ? '男' : '女';
        // $u_group = SUBSTR($results['ORGANIZE_ID'][0], -1);
        // $u_status = substr($results['ORASTATUS_ID'][0], -1);
        // $u_dept_name = $results['DEPT_NAME'][0];
        $repair_tmp = $results['REPAIR'][0]; //備取生遞補狀態
        // $mainnumber_tmp = $results['MAINNUMBER'][0]; //正取名次
        // $backnumber_tmp = $results['BACKNUMBER'][0]; //備取名次
        $incomplete_tmp = $results['INCOMPLETE'][0]; //正取生報到狀態

        if ($repair_tmp == '0' || $incomplete_tmp == '1') {
            $result['intention'] = 1; //已申明就讀(遞補)意願
        } else if ($repair_tmp == '3' || $incomplete_tmp == '2') {
            $result['intention'] = -1; //已申請放棄就讀(遞補)意願
        } else {
            $result['intention'] = 0; //尚未登錄就讀(遞補)意願
        }



        for ($i = 0; $i < $nrows; $i++) {
            $stmt = oci_parse($conn, "SELECT NAME FROM ORGANIZE WHERE SCHOOL_ID='$SCHOOL_ID' AND YEAR='$ACT_YEAR_NO' AND ID=:organize_id");
            $organize_id = substr($results['OPTION_ID'][$i], 0, 4);
            oci_bind_by_name($stmt, ':organize_id',  $organize_id);
            oci_execute($stmt, OCI_DEFAULT);
            oci_fetch($stmt);
            $organize = oci_result($stmt, 'NAME');
            oci_free_statement($stmt);

            if ($results['MAINNUMBER'][$i] > 0) {
                $main_back = 0; //正取
            } else {
                $main_back = $results['BACKNUMBER'][$i]; //備取 第?名
            }

            $result['result'][] = array('dept' => $results['DEPT_NAME'][$i], 'organize' => $organize, 'ranking' => $main_back);
        }

        $result['link']['url'] = "https://forms.gle/7UNPgZu86amN9HKd6";
        $result['link']['title'] = $ACT_YEAR_NO . "學年度碩士班入學獎勵金問卷";
    } else if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (!isset($_COOKIE['token']))
            throw new Exception("Unauthorized", 401);
        $payload = JWT::verifyToken($_COOKIE['token']);
        if ($payload === false || !isset($payload['id']) || !isset($payload['sid']))
            throw new Exception("Unauthorized", 401);

        $id = $payload['id'];
        $sid = $payload['sid'];
        $dept_id = substr($sid, 0, 3);

        $sql = "SELECT 1 FROM AUTH_CODE WHERE  school_id='$SCHOOL_ID' and year='$ACT_YEAR_NO' and STUDENT_ID = :sid AND ID = :id AND AUTH_CODE=:code";
        $stmt = oci_parse($conn, $sql);
        oci_bind_by_name($stmt, ':sid',  $sid);
        oci_bind_by_name($stmt, ':id',  $id);
        oci_bind_by_name($stmt, ':code',  $_POST['code']);
        oci_execute($stmt, OCI_DEFAULT);
        if (!oci_fetch($stmt))
            throw new Exception("您輸入的認證碼不正確, 請重新輸入。");
        oci_free_statement($stmt);


        if ($_POST['item'] == 1) //申明
        {
            //更新正取(聯合招生)
            $sql = "UPDATE union_priority_all SET INCOMPLETE = '1' WHERE  school_id='$SCHOOL_ID' and year='$ACT_YEAR_NO' and STUDENT_ID = :sid AND ID = :id and mainnumber>0  AND EXISTS (SELECT AUTH_CODE FROM AUTH_CODE WHERE  school_id='$SCHOOL_ID' and year='$ACT_YEAR_NO' and STUDENT_ID = :sid AND ID = :id AND AUTH_CODE = :code)";
            $stmt = oci_parse($conn, $sql);
            oci_bind_by_name($stmt, ':sid',  $sid);
            oci_bind_by_name($stmt, ':id',  $id);
            oci_bind_by_name($stmt, ':code',  $_POST['code']);
            oci_execute($stmt, OCI_DEFAULT);
            oci_free_statement($stmt);

            //更新備取(聯合招生)
            $sql = "UPDATE union_priority_all SET repair = '0' WHERE school_id='$SCHOOL_ID' and year='$ACT_YEAR_NO' and  STUDENT_ID = :sid AND ID = :id and backnumber>0  AND EXISTS (SELECT AUTH_CODE FROM AUTH_CODE WHERE  school_id='$SCHOOL_ID' and year='$ACT_YEAR_NO' and STUDENT_ID = :sid AND ID = :id AND AUTH_CODE = :code)";
            $stmt = oci_parse($conn, $sql);
            oci_bind_by_name($stmt, ':sid',  $sid);
            oci_bind_by_name($stmt, ':id',  $id);
            oci_bind_by_name($stmt, ':code',  $_POST['code']);
            oci_execute($stmt, OCI_DEFAULT);
            oci_free_statement($stmt);


            //更新正取
            $sql = "UPDATE PERSON SET INCOMPLETE = '1' WHERE  school_id='$SCHOOL_ID' and year='$ACT_YEAR_NO' and STUDENT_ID = :sid AND ID = :id  and mainnumber>0 AND EXISTS (SELECT AUTH_CODE FROM AUTH_CODE WHERE  school_id='$SCHOOL_ID' and year='$ACT_YEAR_NO' and STUDENT_ID = :sid AND ID = :id AND AUTH_CODE = :code)";
            $stmt = oci_parse($conn, $sql);
            oci_bind_by_name($stmt, ':sid',  $sid);
            oci_bind_by_name($stmt, ':id',  $id);
            oci_bind_by_name($stmt, ':code',  $_POST['code']);
            oci_execute($stmt, OCI_DEFAULT);
            oci_free_statement($stmt);

            //更新備取
            $sql = "UPDATE PERSON SET REPAIR = '0' WHERE  school_id='$SCHOOL_ID' and year='$ACT_YEAR_NO' and STUDENT_ID = :sid AND ID = :id and backnumber>0 AND EXISTS (SELECT AUTH_CODE FROM AUTH_CODE WHERE  school_id='$SCHOOL_ID' and year='$ACT_YEAR_NO' and STUDENT_ID = :sid AND ID = :id AND AUTH_CODE = :code)";
            $stmt = oci_parse($conn, $sql);
            oci_bind_by_name($stmt, ':sid',  $sid);
            oci_bind_by_name($stmt, ':id',  $id);
            oci_bind_by_name($stmt, ':code',  $_POST['code']);
            oci_execute($stmt, OCI_DEFAULT);
            oci_free_statement($stmt);


            //更新正取(擇優)
            $sql = "UPDATE ADD_ENROLL SET  INCOMPLETE = '1' WHERE  school_id='$SCHOOL_ID' and year='$ACT_YEAR_NO' and STUDENT_ID = :sid AND PERSON_ID = :id  and mainnumber>0 AND EXISTS (SELECT AUTH_CODE FROM AUTH_CODE WHERE school_id='$SCHOOL_ID' and year='$ACT_YEAR_NO' and STUDENT_ID = :sid AND ID = :id AND AUTH_CODE = :code)";
            $stmt = oci_parse($conn, $sql);
            oci_bind_by_name($stmt, ':sid',  $sid);
            oci_bind_by_name($stmt, ':id',  $id);
            oci_bind_by_name($stmt, ':code',  $_POST['code']);
            oci_execute($stmt, OCI_DEFAULT);
            oci_free_statement($stmt);

            //更新備取(擇優)
            $sql = "UPDATE ADD_ENROLL SET  REPAIR = '0' WHERE  school_id='$SCHOOL_ID' and year='$ACT_YEAR_NO' and STUDENT_ID = :sid AND PERSON_ID = :id  and backnumber>0 AND EXISTS (SELECT AUTH_CODE FROM AUTH_CODE WHERE school_id='$SCHOOL_ID' and year='$ACT_YEAR_NO' and STUDENT_ID = :sid AND ID = :id AND AUTH_CODE = :code)";
            $stmt = oci_parse($conn, $sql);
            oci_bind_by_name($stmt, ':sid',  $sid);
            oci_bind_by_name($stmt, ':id',  $id);
            oci_bind_by_name($stmt, ':code',  $_POST['code']);
            oci_execute($stmt, OCI_DEFAULT);
            oci_free_statement($stmt);

            $result['intention'] = 1;
        } else //放棄
        {
            //更新正取(聯合招生)
            $sql = "UPDATE union_priority_all SET INCOMPLETE = '2' WHERE  school_id='$SCHOOL_ID' and year='$ACT_YEAR_NO' and STUDENT_ID = :sid AND ID = :id and mainnumber>0  AND EXISTS (SELECT AUTH_CODE FROM AUTH_CODE WHERE  school_id='$SCHOOL_ID' and year='$ACT_YEAR_NO' and STUDENT_ID = :sid AND ID = :id AND AUTH_CODE = :code)";
            $stmt = oci_parse($conn, $sql);
            oci_bind_by_name($stmt, ':sid',  $sid);
            oci_bind_by_name($stmt, ':id',  $id);
            oci_bind_by_name($stmt, ':code',  $_POST['code']);
            oci_execute($stmt, OCI_DEFAULT);
            oci_free_statement($stmt);

            //更新備取(聯合招生)
            $sql = "UPDATE union_priority_all SET repair = '3' WHERE school_id='$SCHOOL_ID' and year='$ACT_YEAR_NO' and  STUDENT_ID = :sid AND ID = :id and backnumber>0  AND EXISTS (SELECT AUTH_CODE FROM AUTH_CODE WHERE  school_id='$SCHOOL_ID' and year='$ACT_YEAR_NO' and STUDENT_ID = :sid AND ID = :id AND AUTH_CODE = :code)";
            $stmt = oci_parse($conn, $sql);
            oci_bind_by_name($stmt, ':sid',  $sid);
            oci_bind_by_name($stmt, ':id',  $id);
            oci_bind_by_name($stmt, ':code',  $_POST['code']);
            oci_execute($stmt, OCI_DEFAULT);
            oci_free_statement($stmt);


            //更新正取
            $sql = "UPDATE PERSON SET INCOMPLETE = '2' WHERE  school_id='$SCHOOL_ID' and year='$ACT_YEAR_NO' and STUDENT_ID = :sid AND ID = :id  and mainnumber>0 AND EXISTS (SELECT AUTH_CODE FROM AUTH_CODE WHERE  school_id='$SCHOOL_ID' and year='$ACT_YEAR_NO' and STUDENT_ID = :sid AND ID = :id AND AUTH_CODE = :code)";
            $stmt = oci_parse($conn, $sql);
            oci_bind_by_name($stmt, ':sid',  $sid);
            oci_bind_by_name($stmt, ':id',  $id);
            oci_bind_by_name($stmt, ':code',  $_POST['code']);
            oci_execute($stmt, OCI_DEFAULT);
            oci_free_statement($stmt);

            //更新備取
            $sql = "UPDATE PERSON SET REPAIR = '3' WHERE  school_id='$SCHOOL_ID' and year='$ACT_YEAR_NO' and STUDENT_ID = :sid AND ID = :id and backnumber>0 AND EXISTS (SELECT AUTH_CODE FROM AUTH_CODE WHERE  school_id='$SCHOOL_ID' and year='$ACT_YEAR_NO' and STUDENT_ID = :sid AND ID = :id AND AUTH_CODE = :code)";
            $stmt = oci_parse($conn, $sql);
            oci_bind_by_name($stmt, ':sid',  $sid);
            oci_bind_by_name($stmt, ':id',  $id);
            oci_bind_by_name($stmt, ':code',  $_POST['code']);
            oci_execute($stmt, OCI_DEFAULT);
            oci_free_statement($stmt);


            //更新正取(擇優)
            $sql = "UPDATE ADD_ENROLL SET  INCOMPLETE = '2' WHERE  school_id='$SCHOOL_ID' and year='$ACT_YEAR_NO' and STUDENT_ID = :sid AND PERSON_ID = :id  and mainnumber>0 AND EXISTS (SELECT AUTH_CODE FROM AUTH_CODE WHERE school_id='$SCHOOL_ID' and year='$ACT_YEAR_NO' and STUDENT_ID = :sid AND ID = :id AND AUTH_CODE = :code)";
            $stmt = oci_parse($conn, $sql);
            oci_bind_by_name($stmt, ':sid',  $sid);
            oci_bind_by_name($stmt, ':id',  $id);
            oci_bind_by_name($stmt, ':code',  $_POST['code']);
            oci_execute($stmt, OCI_DEFAULT);
            oci_free_statement($stmt);

            //更新備取(擇優)
            $sql = "UPDATE ADD_ENROLL SET  REPAIR = '3' WHERE  school_id='$SCHOOL_ID' and year='$ACT_YEAR_NO' and STUDENT_ID = :sid AND PERSON_ID = :id  and backnumber>0 AND EXISTS (SELECT AUTH_CODE FROM AUTH_CODE WHERE school_id='$SCHOOL_ID' and year='$ACT_YEAR_NO' and STUDENT_ID = :sid AND ID = :id AND AUTH_CODE = :code)";
            $stmt = oci_parse($conn, $sql);
            oci_bind_by_name($stmt, ':sid',  $sid);
            oci_bind_by_name($stmt, ':id',  $id);
            oci_bind_by_name($stmt, ':code',  $_POST['code']);
            oci_execute($stmt, OCI_DEFAULT);
            oci_free_statement($stmt);

            $result['intention'] = -1;
        }


        // $ip = $_SERVER["REMOTE_ADDR"]; //使用者IP
        // $a_date = date("Ymd");
        // $a_time = date("His");
        // $sql = "insert into exampg_log(user_id,a_ip,a_date,a_time,a_type,mark,school_id,year) values(:sid,'$ip','$a_date','$a_time',:type,'" . $_SERVER['PHP_SELF'] . "','$SCHOOL_ID','$ACT_YEAR_NO')";
        // $stmt = oci_parse($conn, $sql);
        // oci_bind_by_name($stmt, ':sid',  $sid);
        // oci_bind_by_name($stmt, ':type',  $_POST['item']);
        // oci_execute($stmt, OCI_DEFAULT);
        // oci_free_statement($stmt);
    } else
        throw new Exception("Method Not Allowed", 405);

    oci_commit($conn); //無發生任何錯誤，將資料寫進資料庫

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