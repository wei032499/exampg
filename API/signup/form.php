<?php
header('Content-Type:application/json');
header("Cache-Control: no-cache");
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

        $sql = "SELECT substr(OPTION_ID,1,3) as DEPT FROM union_priority_all WHERE SN=:sn AND YEAR='$ACT_YEAR_NO' AND SCHOOL_ID='$SCHOOL_ID' ORDER BY PRIORITY";
        $stmt = oci_parse($conn, $sql);
        oci_bind_by_name($stmt, ':sn',  $payload['sn']);
        oci_execute($stmt, OCI_DEFAULT);
        $union_priority = array();
        while (oci_fetch($stmt)) {
            $union_priority[] = oci_result($stmt, 'DEPT');
        }
        oci_free_statement($stmt);

        $sql = "SELECT ID,NAME,SEX,DEPT_ID,ORGANIZE_ID,ORASTATUS_ID, to_char(BIRTHDAY, 'yyyy-mm-dd') as BIRTHDAY,EMAIL,ZIP,ADDRESS,TEL_H,TEL_O,TEL_M,LIAISONER,LIAISON_TEL,LIAISON_REL,CRIPPLE_TYPE,COMMENTS,PROVE_TYPE,SUBJECT_ID,ZIP_O,ADDRESS_O,E_PLACE,
        AC_SCHOOL_NAME,AC_DEPT_NAME,AC_SCHOOL_TYPE,AC_GRADUATED,to_char(AC_DATE, 'yyyy-mm') as AC_DATE,AC_YEAR_OF_LEAVE,AC_YEAR_OF_STUDY FROM SIGNUPDATA WHERE SIGNUP_SN=:sn AND YEAR='$ACT_YEAR_NO' AND SCHOOL_ID='$SCHOOL_ID'";
        $stmt = oci_parse($conn, $sql);
        oci_bind_by_name($stmt, ':sn',  $payload['sn']);


        oci_execute($stmt, OCI_DEFAULT);
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

        // full subject id
        $dept_id = oci_result($stmt, 'DEPT_ID');
        $sql = "SELECT TEST_TYPE FROM DEPARTMENT WHERE SCHOOL_ID='$SCHOOL_ID' AND YEAR='$ACT_YEAR_NO' AND ID=:dept_id";
        $stmt_dept = oci_parse($conn, $sql);
        oci_bind_by_name($stmt_dept, ':dept_id',  $dept_id);
        oci_execute($stmt_dept, OCI_DEFAULT);
        oci_fetch($stmt_dept);
        $test_type = oci_result($stmt_dept, 'TEST_TYPE');
        oci_free_statement($stmt_dept);

        $subjects = array();
        $section = array();
        if ($test_type === "3") {
            $stmt_subject = oci_parse($conn, "SELECT DISTINCT substr(ID,6,1) as SECTION FROM SUBJECT WHERE SCHOOL_ID='$SCHOOL_ID' AND YEAR='$ACT_YEAR_NO' AND substr(ID,1,5)=:orastatus_id ORDER BY SECTION");
            $orastatus_id = oci_result($stmt, 'ORASTATUS_ID');
            oci_bind_by_name($stmt_subject, ':orastatus_id',  $orastatus_id);
            oci_execute($stmt_subject, OCI_DEFAULT);
            $index = 0;

            foreach (str_split(oci_result($stmt, 'SUBJECT_ID'), 1) as $value) {
                if (!oci_fetch($stmt_subject))
                    throw new Exception("選科數量錯誤");
                else if ($value === "-")
                    continue;

                $subjects[] = $orastatus_id . oci_result($stmt_subject, 'SECTION') . $value;
                $section[] = oci_result($stmt_subject, 'SECTION');
            }
            oci_free_statement($stmt_subject);
        } else {
            if (oci_result($stmt, 'SUBJECT_ID') !== null) {
                $orastatus_id = oci_result($stmt, 'ORASTATUS_ID');
                $stmt_subject = oci_parse($conn, "SELECT substr(ID,1,6) as FSECTION FROM SUBJECT WHERE SCHOOL_ID='$SCHOOL_ID' AND YEAR='$ACT_YEAR_NO' AND substr(ID,1,5)=:orastatus_id  GROUP BY substr(ID,1,6) HAVING count(*)>1 ");
                oci_bind_by_name($stmt_subject, ':orastatus_id',  $orastatus_id);
                oci_execute($stmt_subject, OCI_DEFAULT);
                foreach (str_split(oci_result($stmt, 'SUBJECT_ID'), 1) as $value) {
                    oci_fetch($stmt_subject);
                    $subjects[] =  oci_result($stmt_subject, 'FSECTION') . $value;
                }
                oci_free_statement($stmt_subject);
            }
        }
        sort($subjects);
        if (oci_result($stmt, 'PROVE_TYPE') === "1") {
            $result['data'] = array(
                'id' => oci_result($stmt, 'ID'), 'name' => oci_result($stmt, 'NAME'),
                'sex' => oci_result($stmt, 'SEX'),
                'dept' => oci_result($stmt, 'DEPT_ID'), 'organize_id' => oci_result($stmt, 'ORGANIZE_ID'),
                'orastatus_id' => oci_result($stmt, 'ORASTATUS_ID'), 'birthday' => oci_result($stmt, 'BIRTHDAY'),
                'email' => oci_result($stmt, 'EMAIL'), 'zipcode' => oci_result($stmt, 'ZIP'),
                'address' => oci_result($stmt, 'ADDRESS'),
                'tel_h_a' => $tel_h[0], 'tel_h' => $tel_h[1],
                'tel_o_a' => $tel_o[0], 'tel_o' => $tel_o[1], 'tel_m' => oci_result($stmt, 'TEL_M'),
                'conn_name' => oci_result($stmt, 'LIAISONER'), 'conn_tel' => oci_result($stmt, 'LIAISON_TEL'),
                'conn_rel' => oci_result($stmt, 'LIAISON_REL'), 'disabled' => $disabled, 'disabled_type' => oci_result($stmt, 'CRIPPLE_TYPE'),
                'comments' => oci_result($stmt, 'COMMENTS'), 'prove_type' => oci_result($stmt, 'PROVE_TYPE'),
                'subject' => $subjects, 'section' => $section,
                'zipcode2' => oci_result($stmt, 'ZIP_O'), 'address2' => oci_result($stmt, 'ADDRESS_O'),
                'place' => oci_result($stmt, 'E_PLACE'), 'grad_date' => oci_result($stmt, 'AC_DATE'),
                'grad_schol' => oci_result($stmt, 'AC_SCHOOL_NAME'),
                'grad_dept' => oci_result($stmt, 'AC_DEPT_NAME'),
                'union_priority' => $union_priority
            );
        } else if (oci_result($stmt, 'PROVE_TYPE') === "2") {

            $result['data'] = array(
                'id' => oci_result($stmt, 'ID'), 'name' => oci_result($stmt, 'NAME'),
                'sex' => oci_result($stmt, 'SEX'),
                'dept' => oci_result($stmt, 'DEPT_ID'), 'organize_id' => oci_result($stmt, 'ORGANIZE_ID'),
                'orastatus_id' => oci_result($stmt, 'ORASTATUS_ID'), 'birthday' => oci_result($stmt, 'BIRTHDAY'),
                'email' => oci_result($stmt, 'EMAIL'), 'zipcode' => oci_result($stmt, 'ZIP'),
                'address' => oci_result($stmt, 'ADDRESS'),
                'tel_h_a' => $tel_h[0], 'tel_h' => $tel_h[1],
                'tel_o_a' => $tel_o[0], 'tel_o' => $tel_o[1], 'tel_m' => oci_result($stmt, 'TEL_M'),
                'conn_name' => oci_result($stmt, 'LIAISONER'), 'conn_tel' => oci_result($stmt, 'LIAISON_TEL'),
                'conn_rel' => oci_result($stmt, 'LIAISON_REL'), 'disabled' => $disabled, 'disabled_type' => oci_result($stmt, 'CRIPPLE_TYPE'),
                'comments' => oci_result($stmt, 'COMMENTS'), 'prove_type' => oci_result($stmt, 'PROVE_TYPE'),
                'subject' => $subjects, 'section' => $section,
                'zipcode2' => oci_result($stmt, 'ZIP_O'), 'address2' => oci_result($stmt, 'ADDRESS_O'),
                'place' => oci_result($stmt, 'E_PLACE'),
                'ac_school' => oci_result($stmt, 'AC_SCHOOL_NAME'), 'ac_school_type' => oci_result($stmt, 'AC_SCHOOL_TYPE'),
                'ac_dept' => oci_result($stmt, 'AC_DEPT_NAME'), 'ac_date' => oci_result($stmt, 'AC_DATE'),
                'ac_g' => oci_result($stmt, 'AC_GRADUATED'), 'ac_m_y' => oci_result($stmt, 'AC_YEAR_OF_STUDY'), 'ac_leave_y' => oci_result($stmt, 'AC_YEAR_OF_LEAVE'),
                'union_priority' => $union_priority
            );
        } else {
            $result['data'] = array(
                'id' => oci_result($stmt, 'ID'), 'name' => oci_result($stmt, 'NAME'),
                'sex' => oci_result($stmt, 'SEX'),
                'dept' => oci_result($stmt, 'DEPT_ID'), 'organize_id' => oci_result($stmt, 'ORGANIZE_ID'),
                'orastatus_id' => oci_result($stmt, 'ORASTATUS_ID'), 'birthday' => oci_result($stmt, 'BIRTHDAY'),
                'email' => oci_result($stmt, 'EMAIL'), 'zipcode' => oci_result($stmt, 'ZIP'),
                'address' => oci_result($stmt, 'ADDRESS'),
                'tel_h_a' => $tel_h[0], 'tel_h' => $tel_h[1],
                'tel_o_a' => $tel_o[0], 'tel_o' => $tel_o[1], 'tel_m' => oci_result($stmt, 'TEL_M'),
                'conn_name' => oci_result($stmt, 'LIAISONER'), 'conn_tel' => oci_result($stmt, 'LIAISON_TEL'),
                'conn_rel' => oci_result($stmt, 'LIAISON_REL'), 'disabled' => $disabled, 'disabled_type' => oci_result($stmt, 'CRIPPLE_TYPE'),
                'comments' => oci_result($stmt, 'COMMENTS'), 'prove_type' => oci_result($stmt, 'PROVE_TYPE'),
                'subject' => $subjects, 'section' => $section,
                'zipcode2' => oci_result($stmt, 'ZIP_O'), 'address2' => oci_result($stmt, 'ADDRESS_O'),
                'place' => oci_result($stmt, 'E_PLACE'),
                'union_priority' => $union_priority
            );
        }

        oci_free_statement($stmt);
    } else if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if ($payload['status'] !== 1)
            throw new Exception("Forbidden", 403);

        // 若有取得報名費優惠，需檢查繳費人與報名人是否相同(同一身分證字號)
        $sql = "SELECT substr(ACCOUNT_NO,7,2) AS ACCOUNT_TYPE, ID FROM SN_DB WHERE SCHOOL_ID='$SCHOOL_ID' AND YEAR='$ACT_YEAR_NO' AND SN=:sn ";
        $stmt = oci_parse($conn, $sql);
        oci_bind_by_name($stmt, ':sn',  $payload['sn']);
        oci_execute($stmt, OCI_DEFAULT);
        $nrows = oci_fetch_all($stmt, $result1); //$nrows -->總筆數
        oci_free_statement($stmt);
        if ($result1['ACCOUNT_TYPE'][0] !== '31' && $_POST['id'] !== $result1['ID'][0])
            throw new Exception("已取得報名優惠，繳費人需與報名人相同(需為同一身分證字號)");

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

        $subjects = null;
        if (isset($_POST['section'])) {
            if (count($_POST['section']) !== count($_POST['subject']))
                throw new Exception("勾選考科與選考考科數不相符", 400);

            $stmt = oci_parse($conn, "SELECT DISTINCT substr(ID,6,1) as SECTION FROM SUBJECT WHERE SCHOOL_ID='$SCHOOL_ID' AND YEAR='$ACT_YEAR_NO' AND substr(ID,1,5)=:orastatus_id ORDER BY SECTION");
            oci_bind_by_name($stmt, ':orastatus_id',  $_POST['orastatus_id']);
            oci_execute($stmt, OCI_DEFAULT);
            $index = 0;
            while (oci_fetch($stmt)) {
                $section = oci_result($stmt, 'SECTION');
                if ($_POST['section'][$index] === $section) {
                    $subjects .= $section; //subject_id
                    $index++;
                } else
                    $subjects .= "-";
            }
            oci_free_statement($stmt);
        } else if (isset($_POST['subject'])) {
            sort($_POST['subject']);
            $subjects = "";
            foreach ($_POST['subject'] as $value) {
                $subjects .= substr($value, 6, 1); //subject_id
            }
        }


        $email = $_POST['email'];
        $id = $_POST['id'];



        $sql = "INSERT INTO SIGNUPDATA (ID,NAME,SEX,DEPT_ID,ORGANIZE_ID,ORASTATUS_ID,BIRTHDAY,EMAIL,ZIP,ADDRESS,TEL_H,TEL_O,TEL_M,LIAISONER,LIAISON_TEL,LIAISON_REL,CRIPPLE_TYPE,COMMENTS,PROVE_TYPE,SIGNUP_SN,SCHOOL,LOCK_UP,SUBJECT_ID,FILL_DATE,L_ALT_DATE,ZIP_O,ADDRESS_O,E_PLACE,AC_SCHOOL_NAME,AC_DEPT_NAME,AC_SCHOOL_TYPE,AC_GRADUATED,AC_DATE,AC_YEAR_OF_LEAVE,AC_YEAR_OF_STUDY,SCHOOL_ID,YEAR) VALUES (:id,:name,:sex,:dept_id,:organize_id,:orastatus_id,to_date(:birthday,'yyyy-mm-dd'),:email,:zip,:address,:tel_h,:tel_o,:tel_m,:conn_name,:conn_tel,:conn_rel,:disabled_type,:comments,:prove_type,:sn,'$SCHOOL_ID',0,:subjects,to_date('$time','yyyy-mm-dd HH24:MI:SS'),to_date('$time','yyyy-mm-dd HH24:MI:SS'),:zip2,:address2,:place,:ac_school,:ac_dept,:ac_school_type,:ac_graduated,to_date(:ac_date,'yyyy-mm-dd'),:year_leave,:year_study,'$SCHOOL_ID','$ACT_YEAR_NO')";
        $stmt = oci_parse($conn, $sql);
        $params = array($id, $_POST['name'], $_POST['sex'], $_POST['dept'], $_POST['organize_id'], $_POST['orastatus_id'], $_POST['birthday'], $email, $_POST['zipcode'], $_POST['address'], $_POST['tel_h_a'] . "-" . $_POST['tel_h'], $tel_o, $_POST['tel_m'], $_POST['conn_name'], $_POST['conn_tel'], $_POST['conn_rel'], $_POST['disabled_type'], $comments, $_POST['prove_type'], $payload['sn'], $subjects, $_POST['zipcode2'], $_POST['address2'], $_POST['place'], $ac_school, $ac_dept, $ac_school_type, $ac_graduated, $ac_date, $ac_year_leave, $ac_year_study);
        bind_by_array($stmt, $sql, $params);


        oci_execute($stmt, OCI_DEFAULT);

        $result['data'] = array('email' => $email, 'card_start_date' => $CARD_START_DATE, 'card_end_date' => $CARD_END_DATE);


        // union priority
        $sql = "SELECT NAME,UNION_FLAG FROM DEPARTMENT where  school_id='$SCHOOL_ID' and year='$ACT_YEAR_NO' and ID=:dept_id";
        $stmt = oci_parse($conn, $sql);
        oci_bind_by_name($stmt, ':dept_id',  $_POST['dept']);
        oci_execute($stmt, OCI_DEFAULT);
        oci_fetch($stmt);
        $dept_name = oci_result($stmt, 'NAME');
        $union_flag = oci_result($stmt, 'UNION_FLAG');
        oci_free_statement($stmt);
        if ($union_flag !== null) {
            $sql = "INSERT INTO union_priority_all(id,organize_id,option_id,option_name,priority,sn,school_id,year) values(:id,:orastatus_id,:option_id,:option_name,'1',:sn,'$SCHOOL_ID','$ACT_YEAR_NO')";
            $stmt = oci_parse($conn, $sql);
            $params = array($id, $_POST['orastatus_id'], $_POST['orastatus_id'], $dept_name, $payload['sn']);
            bind_by_array($stmt, $sql, $params);
            oci_execute($stmt, OCI_DEFAULT);
            oci_free_statement($stmt);

            if (isset($_POST['union_priority'])) {
                //檢查重複的志願
                for ($i = 0; $i < count($_POST['union_priority']); $i++)
                    if ($_POST['union_priority'][$i] !== "-1")
                        for ($j = $i + 1; $j < count($_POST['union_priority']); $j++)
                            if ($_POST['union_priority'][$i] === $_POST['union_priority'][$j])
                                throw new Exception("重複的志願");

                $key = array_search($_POST['dept'], $_POST['union_priority']); //報名系所
                if ($key !== false)
                    array_splice($_POST['union_priority'], $key, 1);

                for ($i = 0; $i < count($_POST['union_priority']); $i++) {
                    /*if ($_POST['union_priority'][$i] === "-1")
                        continue;*/
                    $sql = "SELECT NAME FROM DEPARTMENT where school_id='$SCHOOL_ID' and year='$ACT_YEAR_NO' and ID=:dept_id";
                    $stmt = oci_parse($conn, $sql);
                    oci_bind_by_name($stmt, ':dept_id',  $_POST['union_priority'][$i]);
                    oci_execute($stmt, OCI_DEFAULT);
                    oci_fetch($stmt);
                    $option_name = oci_result($stmt, 'NAME');
                    oci_free_statement($stmt);

                    $sql = "INSERT INTO union_priority_all(id,organize_id,option_id,option_name,priority,sn,school_id,year) values(:id,:organize_id,:option_id,:option_name,:priority,:sn,'$SCHOOL_ID','$ACT_YEAR_NO')";
                    $stmt = oci_parse($conn, $sql);
                    $params = array($id, $_POST['organize_id'], $_POST['union_priority'][$i] . substr($_POST['orastatus_id'], -2), $option_name, $i + 2, $payload['sn']);
                    bind_by_array($stmt, $sql, $params);
                    oci_execute($stmt, OCI_DEFAULT);
                    oci_free_statement($stmt);
                }
            }
        }




        // lockup
        $stmt = oci_parse($conn, "UPDATE SN_DB SET LOCK_UP='1' WHERE SCHOOL_ID='$SCHOOL_ID' AND YEAR='$ACT_YEAR_NO' AND SN=:sn");
        oci_bind_by_name($stmt, ':sn',  $payload['sn']);
        oci_execute($stmt, OCI_DEFAULT);

        $post_processing[] = function () use ($payload) {
            /**
             * 寄發通知信
             */
            $email = sendMail(3, $payload);


            /**
             * 寫入log
             */
            $fp = fopen(dirname(__FILE__) . "/../logs/dbg_msg.log", "a+");
            fwrite($fp, "報名資料初填通知 - API/signup/form.php  - $email - \n");
            fclose($fp);
        };
    } else if ($_SERVER['REQUEST_METHOD'] === 'PUT') {

        if ($payload['status'] !== 2 || $payload['authority'] !== 1)
            throw new Exception("Forbidden", 403);

        parse_str(file_get_contents("php://input"), $post_vars);

        // 若有取得報名費優惠，需檢查繳費人與報名人是否相同(同一身分證字號)
        $sql = "SELECT substr(ACCOUNT_NO,7,2) AS ACCOUNT_TYPE, ID FROM SN_DB WHERE SCHOOL_ID='$SCHOOL_ID' AND YEAR='$ACT_YEAR_NO' AND SN=:sn ";
        $stmt = oci_parse($conn, $sql);
        oci_bind_by_name($stmt, ':sn',  $payload['sn']);
        oci_execute($stmt, OCI_DEFAULT);
        $nrows = oci_fetch_all($stmt, $result1); //$nrows -->總筆數
        oci_free_statement($stmt);
        if ($result1['ACCOUNT_TYPE'][0] !== '31' && $post_vars['id'] !== $result1['ID'][0])
            throw new Exception("已取得報名優惠，繳費人需與報名人相同(需為同一身分證字號)");

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


        $subjects = null;
        if (isset($post_vars['section'])) {
            if (count($post_vars['section']) !== count($post_vars['subject']))
                throw new Exception("勾選考科與選考考科數不相符", 400);

            $stmt = oci_parse($conn, "SELECT DISTINCT substr(ID,6,1) as SECTION FROM SUBJECT WHERE SCHOOL_ID='$SCHOOL_ID' AND YEAR='$ACT_YEAR_NO' AND substr(ID,1,5)=:orastatus_id ORDER BY SECTION");
            oci_bind_by_name($stmt, ':orastatus_id',  $post_vars['orastatus_id']);
            oci_execute($stmt, OCI_DEFAULT);
            $index = 0;
            while (oci_fetch($stmt)) {
                $section = oci_result($stmt, 'SECTION');
                if ($post_vars['section'][$index] === $section) {
                    $subjects .= $section; //subject_id
                    $index++;
                } else
                    $subjects .= "-";
            }
            oci_free_statement($stmt);
        } else if (isset($post_vars['subject'])) {
            sort($post_vars['subject']);
            $subjects = "";
            foreach ($post_vars['subject'] as $value) {
                $subjects .= substr($value, 6, 1); //subject_id
            }
        }

        $email = $post_vars['email'];
        $id = $post_vars['id'];

        $sql = "UPDATE SIGNUPDATA SET ID=:id,NAME=:name,SEX=:sex,DEPT_ID=:dept_id,ORGANIZE_ID=:organize_id,ORASTATUS_ID=:orastatus_id,BIRTHDAY=to_date(:birthday,'yyyy-mm-dd'),EMAIL=:email,ZIP=:zip,ADDRESS=:address,TEL_H=:tel_h,TEL_O=:tel_o,TEL_M=:tel_m,LIAISONER=:conn_name,LIAISON_TEL=:conn_tel,LIAISON_REL=:conn_rel,CRIPPLE_TYPE=:disabled_type,COMMENTS=:comments,PROVE_TYPE=:prove_type,L_ALT_DATE=to_date('$time','yyyy-mm-dd HH24:MI:SS'),ZIP_O=:zip2,ADDRESS_O=:address2,E_PLACE=:e_place,AC_SCHOOL_NAME=:ac_school,AC_DEPT_NAME=:ac_dept,AC_SCHOOL_TYPE=:ac_type,AC_GRADUATED=:ac_graduated,AC_DATE=to_date(:ac_date,'yyyy-mm-dd'),AC_YEAR_OF_LEAVE=:ac_year_leave,AC_YEAR_OF_STUDY=:ac_year_study,SUBJECT_ID=:subject_id WHERE SCHOOL_ID='$SCHOOL_ID' AND YEAR='$ACT_YEAR_NO' AND SIGNUP_SN=:sn";
        $stmt = oci_parse($conn, $sql);
        $params = array($id, $post_vars['name'], $post_vars['sex'], $post_vars['dept'], $post_vars['organize_id'], $post_vars['orastatus_id'], $post_vars['birthday'], $email, $post_vars['zipcode'], $post_vars['address'], $post_vars['tel_h_a'] . "-" . $post_vars['tel_h'], $tel_o, $post_vars['tel_m'], $post_vars['conn_name'], $post_vars['conn_tel'], $post_vars['conn_rel'], $post_vars['disabled_type'], $comments, $post_vars['prove_type'], $post_vars['zipcode2'], $post_vars['address2'], $post_vars['place'], $ac_school, $ac_dept, $ac_school_type, $ac_graduated, $ac_date, $ac_year_leave, $ac_year_study, $subjects, $payload['sn']);
        bind_by_array($stmt, $sql, $params);


        oci_execute($stmt, OCI_DEFAULT);

        $result['data'] = array('email' => $email, 'card_start_date' => $CARD_START_DATE, 'card_end_date' => $CARD_END_DATE);


        // union priority
        $sql = "DELETE FROM union_priority_all WHERE SCHOOL_ID='$SCHOOL_ID' and YEAR='$ACT_YEAR_NO' and SN=:sn";
        $stmt = oci_parse($conn, $sql);
        // oci_bind_by_name($stmt, ':id',  $id);
        oci_bind_by_name($stmt, ':sn',  $payload['sn']);
        oci_execute($stmt, OCI_DEFAULT);
        oci_free_statement($stmt);


        $sql = "SELECT NAME,UNION_FLAG FROM DEPARTMENT where  school_id='$SCHOOL_ID' and year='$ACT_YEAR_NO' and ID=:dept_id";
        $stmt = oci_parse($conn, $sql);
        oci_bind_by_name($stmt, ':dept_id',  $post_vars['dept']);
        oci_execute($stmt, OCI_DEFAULT);
        oci_fetch($stmt);
        $dept_name = oci_result($stmt, 'NAME');
        $union_flag = oci_result($stmt, 'UNION_FLAG');
        oci_free_statement($stmt);
        if ($union_flag !== null) {
            $sql = "INSERT INTO union_priority_all(id,organize_id,option_id,option_name,priority,sn,school_id,year) values(:id,:orastatus_id,:option_id,:option_name,'1',:sn,'$SCHOOL_ID','$ACT_YEAR_NO')";
            $stmt = oci_parse($conn, $sql);
            $params = array($id, $post_vars['orastatus_id'], $post_vars['orastatus_id'], $dept_name, $payload['sn']);
            bind_by_array($stmt, $sql, $params);
            oci_execute($stmt, OCI_DEFAULT);
            oci_free_statement($stmt);

            if (isset($post_vars['union_priority'])) {
                //檢查重複的志願
                for ($i = 0; $i < count($post_vars['union_priority']); $i++)
                    if ($post_vars['union_priority'][$i] !== "-1")
                        for ($j = $i + 1; $j < count($post_vars['union_priority']); $j++)
                            if ($post_vars['union_priority'][$i] === $post_vars['union_priority'][$j])
                                throw new Exception("重複的志願");

                $key = array_search($post_vars['dept'], $post_vars['union_priority']); //報名系所
                if ($key !== false)
                    array_splice($post_vars['union_priority'], $key, 1);

                for ($i = 0; $i < count($post_vars['union_priority']); $i++) {
                    if ($post_vars['union_priority'][$i] === "-1")
                        continue;
                    $sql = "SELECT NAME FROM DEPARTMENT where  school_id='$SCHOOL_ID' and year='$ACT_YEAR_NO' and ID=:dept_id";
                    $stmt = oci_parse($conn, $sql);
                    oci_bind_by_name($stmt, ':dept_id',  $post_vars['union_priority'][$i]);
                    oci_execute($stmt, OCI_DEFAULT);
                    oci_fetch($stmt);
                    $option_name = oci_result($stmt, 'NAME');
                    oci_free_statement($stmt);

                    $sql = "INSERT INTO union_priority_all(id,organize_id,option_id,option_name,priority,sn,school_id,year) values(:id,:orastatus_id,:option_id,:option_name,:priority,:sn,'$SCHOOL_ID','$ACT_YEAR_NO')";
                    $stmt = oci_parse($conn, $sql);
                    $params = array($id, $post_vars['orastatus_id'], $post_vars['union_priority'][$i] . substr($post_vars['orastatus_id'], -2), $option_name, $i + 2, $payload['sn']);
                    bind_by_array($stmt, $sql, $params);
                    oci_execute($stmt, OCI_DEFAULT);
                    oci_free_statement($stmt);
                }
            }
        }






        $post_processing[] = function () use ($payload) {
            /**
             * 寄發通知信
             */
            $email = sendMail(4, $payload);

            /**
             * 寫入log
             */
            $fp = fopen(dirname(__FILE__) . "/../logs/dbg_msg.log", "a+");
            fwrite($fp, "資料修改通知 - API/signup/form.php - $email - \n");
            fclose($fp);
        };
    } else
        throw new Exception("Method Not Allowed", 405);

    // setcookie('token', $Token->refresh(), $cookie_options_httponly);
    $cookieOpt = "token=" . $Token->refresh() . ";" . getCookieOptions($cookie_options_httponly);
    header("Set-Cookie: " . $cookieOpt, false);
    oci_commit($conn); //無發生任何錯誤，將資料寫進資料庫

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
