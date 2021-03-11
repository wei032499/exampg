<?php
header('Content-Type:application/json');
header("Cache-Control: no-cache");
$result = array();

try {
    require_once('../common/db.php');
    $acttime = date("Y/m/d H:i:s");
    $actip = getClientIP();
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {

        $token = $_GET['token'];
        //token長度固定32個
        if (strlen($token) == 32) {
            //推薦人資料
            $sql = "select * from recom_letter where year='$ACT_YEAR_NO' and school_id='$SCHOOL_ID' and token=:token";
            $stmt = oci_parse($conn, $sql);
            oci_bind_by_name($stmt, ':token',  $token);
            oci_execute($stmt, OCI_DEFAULT);
            $nrows = oci_fetch_all($stmt, $result1); //$nrows -->總筆數
            oci_free_statement($stmt);
        }
        if (strlen($token) != 32 || count($result1['R_NAME']) == 0) {
            $mail_msg =   $sql . "<br>token=" . $token;
            sendMail(0, array('title' => "推薦函連結錯誤(letter_fill.php)", 'content' => $mail_msg));
            throw new Exception('推薦函連結錯誤，請重新點選Email中的連結');
        } else {
            if ($result1['APPLY_REL'][0] != "") {
                throw new Exception('推薦函已填寫完成，謝謝您的協助!!!', 403);
            }
            $signup_sn = $result1['SIGNUP_SN'][0];
            $result['data']['signup_sn'] = $result1['SIGNUP_SN'][0];
            $result['data']['r_name'] = $result1['R_NAME'][0];
            $result['data']['r_title'] = $result1['R_TITLE'][0];
            $result['data']['r_org'] = $result1['R_ORG'][0];
            $result['data']['r_email'] = $result1['R_EMAIL'][0];

            //第一次讀取寫入的取時間
            if (strlen($result1['R_READTIME'][0]) < 5) {
                $sql = "update recom_letter set R_READTIME='$acttime',r_readip='$actip'  where year='$ACT_YEAR_NO' and school_id='$SCHOOL_ID' and token=:token";
                $stmt = oci_parse($conn, $sql);
                oci_bind_by_name($stmt, ':token',  $token);
                oci_execute($stmt, OCI_DEFAULT);
                oci_free_statement($stmt);
            }

            //考生基本資料
            $sql = "select a.name stud_name,b.name dept_name,tel_m,research,ac_school_name,ac_dept_name from signupdata a,department b where a.year='$ACT_YEAR_NO' and a.school_id='$SCHOOL_ID' and a.signup_sn=:sn and a.school_id=b.school_id and a.year=b.year and a.dept_id=b.id ";
            $stmt = oci_parse($conn, $sql);
            oci_bind_by_name($stmt, ':sn',  $signup_sn);
            oci_execute($stmt, OCI_DEFAULT);
            $nrows = oci_fetch_all($stmt, $result2); //$nrows -->總筆數
            oci_free_statement($stmt);
            if (count($result2['STUD_NAME']) == 0) {
                $mail_msg = $sql . "<br>sn=" . $signup_sn;
                sendMail(0, array('title' => "考生基本資料錯誤(查無考生資料)", 'content' => $mail_msg));
                throw new Exception('考生基本資料錯誤！');
            } else {
                $result['data']['stud_name'] = $result2['STUD_NAME'][0];
                $result['data']['dept_name'] = $result2['DEPT_NAME'][0];
                $result['data']['tel_m'] = $result2['TEL_M'][0];
                $result['data']['research'] = $result2['RESEARCH'][0];
                $result['data']['ac_school_name'] = $result2['AC_SCHOOL_NAME'][0];
                $result['data']['ac_dept_name'] = $result2['AC_DEPT_NAME'][0];
            }
        }
    } else if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        $token = $_POST['token'];
        //token長度固定32個
        if (strlen($token) == 32) {
            $sql = "select * from recom_letter where year='$ACT_YEAR_NO' and school_id='$SCHOOL_ID' and token=:token ";
            $stmt = oci_parse($conn, $sql);
            oci_bind_by_name($stmt, ':token',  $token);
            oci_execute($stmt, OCI_DEFAULT);
            $nrows = oci_fetch_all($stmt, $result1); //$nrows -->總筆數
            oci_free_statement($stmt);
        }
        if (strlen($token) != 32 || count($result1['R_NAME']) == 0) {
            $mail_msg = $_SERVER['PHP_SELF'] . "<br>" . $sql . "<br>token=" . $token . "<br>推薦函網址錯誤";
            sendMail(0, array('title' => "推薦函資料寫入失敗!!", 'content' => $mail_msg));
            throw new Exception('推薦函網址錯誤，請重新點選Email中的連結！');
        } else {
            $signup_sn = $result1['SIGNUP_SN'][0];
        }

        if ($result1['APPLY_REL'][0] != "") {
            $mail_msg = $_SERVER['PHP_SELF'] . "<br>" . $sql . "<br>" . "推薦函已填寫 " . $token;
            sendMail(0, array('title' => "推薦函資料寫入失敗!!", 'content' => $mail_msg));
            throw new Exception('推薦函已填寫，不可重複填寫！');
        }
        //存檔
        if ($_POST['oper'] == "saveall") {
            extract($_POST);
            $apply_desc = $apply_desc_1 . $apply_desc_2 . $apply_desc_3 . $apply_desc_4 . $apply_desc_5 . $apply_desc_6 . $apply_desc_7 . $apply_desc_8;
            if (strlen($apply_desc) != 8) {
                $mail_msg = $_SERVER['PHP_SELF'] . "<br>" . $sql . "<br>" . "描述申請人的選項數量錯誤 " . $apply_desc;
                sendMail(0, array('title' => "推薦函資料寫入失敗!!", 'content' => $mail_msg));
                throw new Exception('描述申請人的選項數量錯誤！');
            }
            $arr_apply_manner = $_POST['apply_manner'];
            $apply_manner_all = "";
            for ($i = 0; $i < count($arr_apply_manner); $i++) {
                $apply_manner_all .= $arr_apply_manner[$i];
            }
            if (strlen($apply_manner_all) < 1) {
                $mail_msg = $_SERVER['PHP_SELF'] . "<br>" . $sql . "<br>" . "申請人在學期間的求學態度資料錯誤 " . $apply_manner_all;
                sendMail(0, array('title' => "推薦函資料寫入失敗!!", 'content' => $mail_msg));
                throw new Exception('申請人在學期間的求學態度資料錯誤！');
            }
            $sql = "update recom_letter set apply_rel='$apply_rel',apply_years='$apply_years',apply_desc='$apply_desc',apply_manner='$apply_manner_all',apply_course='$apply_course',apply_special='$apply_special',apply_notice='$apply_notice',apply_agree='$apply_agree',apply_remark='$apply_remark',r_filltime='$acttime',r_fillip='$actip'  where year='$ACT_YEAR_NO' and school_id='$SCHOOL_ID' and signup_sn='$signup_sn' and  token=:token ";
            // echo $sql;
            $stmt = oci_parse($conn, $sql);
            oci_bind_by_name($stmt, ':token',  $token);
            oci_execute($stmt, OCI_DEFAULT);
            oci_free_statement($stmt);
            if (!empty($result["message"]) || $result == 0) {
                $mail_msg = $_SERVER['PHP_SELF'] . "<br>" . $sql . "<br>token=" . $token . "<br>推薦函資料寫入失敗 " . $result["message"];
                sendMail(0, array('title' => "推薦函資料寫入失敗!!", 'content' => $mail_msg));
                throw new Exception('推薦函資料寫入失敗！');
            } else {
                $result['message'] = "資料存檔成功！";
            }
        } else
            throw new Exception("Bad Request", 400);
    } else
        throw new Exception("Method Not Allowed", 405);

    oci_commit($conn); //無發生任何錯誤，將資料寫進資料庫

} catch (Exception $e) {
    oci_rollback($conn);
    setHeader($e->getCode());
    $result = array();
    $result['code'] = $e->getCode();
    //$result['line'] = $e->getLine();
    $result['message'] = $e->getMessage();
}



oci_close($conn);
echo json_encode($result);
exit();
