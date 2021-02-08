<?php

header('Content-Type:application/json');
$result = array();
try {
    require_once('../common/db.php');
    if (!isset($_COOKIE['token']))
        throw new Exception("Unauthorized", 401);
    $Token = new Token($conn, $_COOKIE['token']);
    $payload = $Token->verify();
    if ($payload === false)
        throw new Exception("Unauthorized", 401);

    if ($_SERVER['REQUEST_METHOD'] === 'GET') {


        $sql = "SELECT ID,NAME,SEX,DEPT_ID,ORGANIZE_ID,ORASTATUS_ID, to_char(BIRTHDAY, 'yyyy-mm-dd') as BIRTHDAY,EMAIL,ZIP,ADDRESS,TEL_H,TEL_O,TEL_M,LIAISONER,LIAISON_TEL,LIAISON_REL,CRIPPLE_TYPE,COMMENTS,PROVE_TYPE,SUBJECT_ID,ZIP_O,ADDRESS_O,E_PLACE,
        AC_SCHOOL_NAME,AC_DEPT_NAME,AC_SCHOOL_TYPE,AC_GRADUATED,to_char(BIRTHDAY, 'yyyy-mm') as AC_DATE,AC_YEAR_OF_LEAVE,AC_YEAR_OF_STUDY FROM SIGNUPDATA WHERE SIGNUP_SN=:sn AND YEAR='$ACT_YEAR_NO' AND SCHOOL_ID='$SCHOOL_ID'";
        $stmt = oci_parse($conn, $sql);
        oci_bind_by_name($stmt, ':sn',  $payload['sn']);


        if (!oci_execute($stmt, OCI_DEFAULT)) {
            $error = analyzeError(oci_error()['message']);
            throw new Exception($error['message'], $error['code']);
        }
        if (!oci_fetch($stmt))
            throw new Exception("No Data");

        $tel_h = explode("-", oci_result($stmt, 'TEL_H'));
        $tel_o = explode("-", oci_result($stmt, 'TEL_O'));
        if (count($tel_o) === 1) {
            if ($tel_o !== "") {
                $tel_o[1] = $tel_o[0];
                $tel_o[0] = null;
            } else {
                $tel_o[0] = null;
                $tel_o[1] = null;
            }
        }
        $disabled = 0;
        if (oci_result($stmt, 'CRIPPLE_TYPE') !== "0")
            $disabled = 1;

        if (oci_result($stmt, 'PROVE_TYPE') === "1") {
            $result['data'] = array(
                'id' => oci_result($stmt, 'ID'), 'name' => oci_result($stmt, 'NAME'),
                'gender' => oci_result($stmt, 'SEX'),
                'dept' => oci_result($stmt, 'DEPT_ID'), 'organize_id' => oci_result($stmt, 'ORGANIZE_ID'),
                'orastatus_id' => oci_result($stmt, 'ORASTATUS_ID'), 'birthday' => oci_result($stmt, 'BIRTHDAY'),
                'email' => oci_result($stmt, 'EMAIL'), 'zipcode' => oci_result($stmt, 'ZIP'),
                'address' => oci_result($stmt, 'ADDRESS'),
                'tel_h_a' => $tel_h[0], 'tel_h' => $tel_h[1],
                'tel_o_a' => $tel_o[0], 'tel_o' => $tel_o[1], 'tel_m' => oci_result($stmt, 'TEL_M'),
                'conn_name' => oci_result($stmt, 'LIAISONER'), 'conn_tel' => oci_result($stmt, 'LIAISON_TEL'),
                'conn_rel' => oci_result($stmt, 'LIAISON_REL'), 'disabled' => $disabled, 'disabled_type' => oci_result($stmt, 'CRIPPLE_TYPE'),
                'comments' => oci_result($stmt, 'COMMENTS'), 'prove_type' => oci_result($stmt, 'PROVE_TYPE'),
                'subject_id' => oci_result($stmt, 'SUBJECT_ID'),
                'zipcode2' => oci_result($stmt, 'ZIP_O'), 'address2' => oci_result($stmt, 'ADDRESS_O'),
                'place' => oci_result($stmt, 'E_PLACE'), 'grad_date' => oci_result($stmt, 'AC_DATE'),
                'grad_schol' => oci_result($stmt, 'AC_SCHOOL_NAME'),
                'grad_dept' => oci_result($stmt, 'AC_DEPT_NAME'),
            );
        } else if (oci_result($stmt, 'PROVE_TYPE') === "2") {

            $result['data'] = array(
                'id' => oci_result($stmt, 'ID'), 'name' => oci_result($stmt, 'NAME'),
                'gender' => oci_result($stmt, 'SEX'),
                'dept' => oci_result($stmt, 'DEPT_ID'), 'organize_id' => oci_result($stmt, 'ORGANIZE_ID'),
                'orastatus_id' => oci_result($stmt, 'ORASTATUS_ID'), 'birthday' => oci_result($stmt, 'BIRTHDAY'),
                'email' => oci_result($stmt, 'EMAIL'), 'zipcode' => oci_result($stmt, 'ZIP'),
                'address' => oci_result($stmt, 'ADDRESS'),
                'tel_h_a' => $tel_h[0], 'tel_h' => $tel_h[1],
                'tel_o_a' => $tel_o[0], 'tel_o' => $tel_o[1], 'tel_m' => oci_result($stmt, 'TEL_M'),
                'conn_name' => oci_result($stmt, 'LIAISONER'), 'conn_tel' => oci_result($stmt, 'LIAISON_TEL'),
                'conn_rel' => oci_result($stmt, 'LIAISON_REL'), 'disabled' => $disabled, 'disabled_type' => oci_result($stmt, 'CRIPPLE_TYPE'),
                'comments' => oci_result($stmt, 'COMMENTS'), 'prove_type' => oci_result($stmt, 'PROVE_TYPE'),
                'subject_id' => oci_result($stmt, 'SUBJECT_ID'),
                'zipcode2' => oci_result($stmt, 'ZIP_O'), 'address2' => oci_result($stmt, 'ADDRESS_O'),
                'place' => oci_result($stmt, 'E_PLACE'),
                'ac_school' => oci_result($stmt, 'AC_SCHOOL_NAME'), 'ac_school_type' => oci_result($stmt, 'AC_SCHOOL_TYPE'),
                'ac_dept' => oci_result($stmt, 'AC_DEPT_NAME'), 'ac_date' => oci_result($stmt, 'AC_DATE'),
                'ac_g' => oci_result($stmt, 'AC_GRADUATED'), 'ac_m_y' => oci_result($stmt, 'AC_YEAR_OF_STUDY'), 'ac_leave_y' => oci_result($stmt, 'AC_YEAR_OF_LEAVE')
            );
        } else {
            $result['data'] = array(
                'id' => oci_result($stmt, 'ID'), 'name' => oci_result($stmt, 'NAME'),
                'gender' => oci_result($stmt, 'SEX'),
                'dept' => oci_result($stmt, 'DEPT_ID'), 'organize_id' => oci_result($stmt, 'ORGANIZE_ID'),
                'orastatus_id' => oci_result($stmt, 'ORASTATUS_ID'), 'birthday' => oci_result($stmt, 'BIRTHDAY'),
                'email' => oci_result($stmt, 'EMAIL'), 'zipcode' => oci_result($stmt, 'ZIP'),
                'address' => oci_result($stmt, 'ADDRESS'),
                'tel_h_a' => $tel_h[0], 'tel_h' => $tel_h[1],
                'tel_o_a' => $tel_o[0], 'tel_o' => $tel_o[1], 'tel_m' => oci_result($stmt, 'TEL_M'),
                'conn_name' => oci_result($stmt, 'LIAISONER'), 'conn_tel' => oci_result($stmt, 'LIAISON_TEL'),
                'conn_rel' => oci_result($stmt, 'LIAISON_REL'), 'disabled' => $disabled, 'disabled_type' => oci_result($stmt, 'CRIPPLE_TYPE'),
                'comments' => oci_result($stmt, 'COMMENTS'), 'prove_type' => oci_result($stmt, 'PROVE_TYPE'),
                'subject_id' => oci_result($stmt, 'SUBJECT_ID'),
                'zipcode2' => oci_result($stmt, 'ZIP_O'), 'address2' => oci_result($stmt, 'ADDRESS_O'),
                'place' => oci_result($stmt, 'E_PLACE')
            );
        }

        oci_free_statement($stmt);
    } else if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if ($payload['status'] !== 1)
            throw new Exception("Forbidden", 403);


        $ac_school = null;
        $ac_dept = null;
        $ac_school_type = null;
        $ac_date = null;
        $ac_graduated = null;
        $ac_year_study = null;
        $ac_year_leave = null;
        if ($_POST['prove_type'] === "1") {
            $ac_school = $_POST['grad_schol'];
            $ac_dept = $_POST['grad_dept'];
            $ac_date = $_POST['grad_date'] . "-01";
        } else if ($_POST['prove_type'] === "2") {
            $ac_school = $_POST['ac_school'];
            $ac_school_type = $_POST['ac_school_type'];
            $ac_dept = $_POST['ac_dept'];
            $ac_date = $_POST['ac_date'] . "-01";
            $ac_graduated = $_POST['ac_g'];
            $ac_year_study = $_POST['ac_m_y'];
            $ac_year_leave = $_POST['ac_leave_y'];
        }

        $tel_o = null;
        if (isset($_POST['tel_o']) && isset($_POST['tel_o_a']) && $_POST['tel_o_a'] !== "" && $_POST['tel_o'] !== "")
            $tel_o = $_POST['tel_o_a'] . "-" . $_POST['tel_o'];
        else if (isset($_POST['tel_o']))
            $tel_o =  $_POST['tel_o'];



        $comments = null;
        if ($_POST['disabled'] === "0") {
            $_POST['disabled_type'] = "0";
        } else if ($_POST['disabled_type'] === "6")
            $comments = $_POST['comments'];

        $time = date("Y-m-d H:i:s");


        $stmt = oci_parse($conn, "SELECT ID,EMAIL FROM SN_DB WHERE SCHOOL_ID='$SCHOOL_ID' AND YEAR='$ACT_YEAR_NO' AND SN=:sn");
        oci_bind_by_name($stmt, ':sn',  $payload['sn']);
        if (!oci_execute($stmt, OCI_DEFAULT)) {
            $error = analyzeError(oci_error()['message']);
            throw new Exception($error['message'], $error['code']);
        }
        oci_fetch($stmt);
        $email = oci_result($stmt, 'EMAIL');
        $id = oci_result($stmt, 'ID');
        oci_free_statement($stmt);

        $sql = "INSERT INTO SIGNUPDATA (ID,NAME,SEX,DEPT_ID,ORGANIZE_ID,ORASTATUS_ID,BIRTHDAY,EMAIL,ZIP,ADDRESS,TEL_H,TEL_O,TEL_M,LIAISONER,LIAISON_TEL,LIAISON_REL,CRIPPLE_TYPE,COMMENTS,PROVE_TYPE,SIGNUP_SN,SCHOOL,LOCK_UP,SUBJECT_ID,FILL_DATE,L_ALT_DATE,ZIP_O,ADDRESS_O,E_PLACE,AC_SCHOOL_NAME,AC_DEPT_NAME,AC_SCHOOL_TYPE,AC_GRADUATED,AC_DATE,AC_YEAR_OF_LEAVE,AC_YEAR_OF_STUDY,SCHOOL_ID,YEAR) VALUES (:id,:name,:gender,:dept_id,:organize_id,:orastatus_id,to_date(:birthday,'yyyy-mm-dd'),:email,:zip,:address,:tel_h,:tel_o,:tel_m,:conn_name,:conn_tel,:conn_rel,:disabled_type,:comments,:prove_type,:sn,'$SCHOOL_ID',0,0,to_date('$time','yyyy-mm-dd HH24:MI:SS'),to_date('$time','yyyy-mm-dd HH24:MI:SS'),:zip2,:address2,:place,:ac_school,:ac_dept,:ac_school_type,:ac_graduated,to_date(:ac_date,'yyyy-mm-dd'),:year_leave,:year_study,'$SCHOOL_ID','$ACT_YEAR_NO')";
        $stmt = oci_parse($conn, $sql);
        $params = array($id, $_POST['name'], $_POST['gender'], $_POST['dept'], $_POST['organize_id'], $_POST['orastatus_id'], $_POST['birthday'], $email, $_POST['zipcode'], $_POST['address'], $_POST['tel_h_a'] . "-" . $_POST['tel_h'], $tel_o, $_POST['tel_m'], $_POST['conn_name'], $_POST['conn_tel'], $_POST['conn_rel'], $_POST['disabled_type'], $comments, $_POST['prove_type'], $payload['sn'], $_POST['zipcode2'], $_POST['address2'], $_POST['place'], $ac_school, $ac_dept, $ac_school_type, $ac_graduated, $ac_date, $ac_year_leave, $ac_year_study);
        bind_by_array($stmt, $sql, $params);


        if (!oci_execute($stmt, OCI_DEFAULT)) {
            $error = analyzeError(oci_error()['message']);
            throw new Exception($error['message'], $error['code']);
        }

        $result['data'] = array('email' => $email, 'card_start_date' => $CARD_START_DATE, 'card_end_date' => $CARD_END_DATE);

        $stmt = oci_parse($conn, "UPDATE SN_DB SET LOCK_UP='1' WHERE SCHOOL_ID='$SCHOOL_ID' AND YEAR='$ACT_YEAR_NO' AND SN=:sn");
        oci_bind_by_name($stmt, ':sn',  $payload['sn']);
        oci_execute($stmt, OCI_DEFAULT);

        /**
         * 寄發通知信
         */
        $msg_type = 3;
        $email = sendMail($msg_type, $conn, $payload);


        /**
         * 寫入log
         */
        $fp = fopen("../logs/dbg_msg.log", "a+");
        fwrite($fp, "報名資料初填通知 - API/signup/form.php - $msg_type - $email - \n");
        fclose($fp);
    } else if ($_SERVER['REQUEST_METHOD'] === 'PUT') {

        if ($payload['status'] !== 2 || $payload['authority'] !== 1)
            throw new Exception("Forbidden", 403);

        parse_str(file_get_contents("php://input"), $post_vars);


        $ac_school = null;
        $ac_dept = null;
        $ac_school_type = null;
        $ac_date = null;
        $ac_graduated = null;
        $ac_year_study = null;
        $ac_year_leave = null;
        if ($post_vars['prove_type'] === "1") {
            $ac_school = $post_vars['grad_schol'];
            $ac_dept = $post_vars['grad_dept'];
            $ac_date = $post_vars['grad_date'] . "-01";
        } else if ($post_vars['prove_type'] === "2") {
            $ac_school = $post_vars['ac_school'];
            $ac_school_type = $post_vars['ac_school_type'];
            $ac_dept = $post_vars['ac_dept'];
            $ac_date = $post_vars['ac_date'] . "-01";
            $ac_graduated = $post_vars['ac_g'];
            $ac_year_study = $post_vars['ac_m_y'];
            $ac_year_leave = $post_vars['ac_leave_y'];
        }

        $tel_o = null;
        if (isset($post_vars['tel_o']) && isset($post_vars['tel_o_a']) && $post_vars['tel_o_a'] !== "" && $post_vars['tel_o'] !== "")
            $tel_o = $post_vars['tel_o_a'] . "-" . $post_vars['tel_o'];
        else if (isset($post_vars['tel_o']))
            $tel_o =  $post_vars['tel_o'];

        $comments = null;
        if ($post_vars['disabled'] === "0") {
            $post_vars['disabled_type'] = "0";
        } else if ($post_vars['disabled_type'] === "6")
            $comments = $post_vars['comments'];
        $time = date("Y-m-d H:i:s");


        $stmt = oci_parse($conn, "SELECT ID,EMAIL FROM SN_DB WHERE SCHOOL_ID='$SCHOOL_ID' AND YEAR='$ACT_YEAR_NO' AND SN=:sn");
        oci_bind_by_name($stmt, ':sn',  $payload['sn']);
        if (!oci_execute($stmt, OCI_DEFAULT)) {
            $error = analyzeError(oci_error()['message']);
            throw new Exception($error['message'], $error['code']);
        }
        oci_fetch($stmt);
        $email = oci_result($stmt, 'EMAIL');
        $id = oci_result($stmt, 'ID');
        oci_free_statement($stmt);


        $sql = "UPDATE SIGNUPDATA SET ID=:id,NAME=:name,SEX=:gender,DEPT_ID=:dept_id,ORGANIZE_ID=:organize_id,ORASTATUS_ID=:orastatus_id,BIRTHDAY=to_date(:birthday,'yyyy-mm-dd'),EMAIL=:email,ZIP=:zip,ADDRESS=:address,TEL_H=:tel_h,TEL_O=:tel_o,TEL_M=:tel_m,LIAISONER=:conn_name,LIAISON_TEL=:conn_tel,LIAISON_REL=:conn_rel,CRIPPLE_TYPE=:disabled_type,COMMENTS=:comments,PROVE_TYPE=:prove_type,L_ALT_DATE=to_date('$time','yyyy-mm-dd HH24:MI:SS'),ZIP_O=:zip2,ADDRESS_O=:address2,E_PLACE=:e_place,AC_SCHOOL_NAME=:ac_school,AC_DEPT_NAME=:ac_dept,AC_SCHOOL_TYPE=:ac_type,AC_GRADUATED=:ac_graduated,AC_DATE=to_date(:ac_date,'yyyy-mm-dd'),AC_YEAR_OF_LEAVE=:ac_year_leave,AC_YEAR_OF_STUDY=:ac_year_study WHERE SCHOOL_ID='$SCHOOL_ID' AND YEAR='$ACT_YEAR_NO' AND SIGNUP_SN=:sn";
        $stmt = oci_parse($conn, $sql);
        $params = array($id, $post_vars['name'], $post_vars['gender'], $post_vars['dept'], $post_vars['organize_id'], $post_vars['orastatus_id'], $post_vars['birthday'], $email, $post_vars['zipcode'], $post_vars['address'], $post_vars['tel_h_a'] . "-" . $post_vars['tel_h'], $tel_o, $post_vars['tel_m'], $post_vars['conn_name'], $post_vars['conn_tel'], $post_vars['conn_rel'], $post_vars['disabled_type'], $comments, $post_vars['prove_type'], $post_vars['zipcode2'], $post_vars['address2'], $post_vars['place'], $ac_school, $ac_dept, $ac_school_type, $ac_graduated, $ac_date, $ac_year_leave, $ac_year_study, $payload['sn']);
        bind_by_array($stmt, $sql, $params);


        if (!oci_execute($stmt, OCI_DEFAULT)) {
            $error = analyzeError(oci_error()['message']);
            throw new Exception($error['message'], $error['code']);
        }

        $result['data'] = array('email' => $email, 'card_start_date' => $CARD_START_DATE, 'card_end_date' => $CARD_END_DATE);

        /**
         * 寄發通知信
         */
        $msg_type = 4;
        $email = sendMail($msg_type, $conn, $payload);


        /**
         * 寫入log
         */
        $fp = fopen("../logs/dbg_msg.log", "a+");
        fwrite($fp, "資料修改通知 - API/signup/form.php - $msg_type - $email - \n");
        fclose($fp);
    } else
        throw new Exception("Method Not Allowed", 405);

    setcookie('token', $Token->refresh(), $cookie_options_httponly);
    setcookie('username', $_COOKIE['username'], $cookie_options);

    oci_commit($conn); //無發生任何錯誤，將資料寫進資料庫

} catch (Exception $e) {
    @oci_rollback($conn);
    setHeader($e->getCode());
    $result = array();
    $result['code'] = $e->getCode(); //$e->getCode();
    $result['message'] = $e->getMessage();
    $result['line'] = $e->getLine();
    //$e->getMessage() . " on line " . $e->getLine()
}
@oci_close($conn);

echo json_encode($result);
