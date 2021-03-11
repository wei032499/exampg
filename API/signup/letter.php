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
    if ($payload === false  || $payload['authority'] !== 1 || ($payload['status'] !== 2 && $payload['status'] !== 3))
        throw new Exception("Unauthorized", 401);


    $sql = "SELECT ID FROM SIGNUPDATA WHERE SIGNUP_SN=:sn AND YEAR='$ACT_YEAR_NO' AND SCHOOL_ID='$SCHOOL_ID'";
    $stmt = oci_parse($conn, $sql);
    oci_bind_by_name($stmt, ':sn',  $payload['sn']);

    oci_execute($stmt, OCI_DEFAULT);
    if (!oci_fetch($stmt))
        throw new Exception("No Data");

    $data['code'] = 0;
    $signup_sn = $payload['sn'];
    $id = oci_result($stmt, 'ID');
    oci_free_statement($stmt);



    $acttime = date("Y/m/d H:i:s");
    $actip = getClientIP();


    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        //研究方向
        $sql = "select research from signupdata where year='$ACT_YEAR_NO' and school_id='$SCHOOL_ID' and signup_sn=:sn";
        //echo $sql ;
        $stmt = oci_parse($conn, $sql);
        oci_bind_by_name($stmt, ':sn',  $payload['sn']);
        oci_execute($stmt, OCI_DEFAULT);
        $nrows = oci_fetch_all($stmt, $data); //$nrows -->總筆數
        oci_free_statement($stmt);
        $a['research'] = [];
        for ($i = 0; $i < count($data['RESEARCH']); $i++) {
            $a['research'][] = array($data['RESEARCH'][$i]);
        }
        // echo $sql;
        //推薦人
        $sql = "select * from recom_letter where year='$ACT_YEAR_NO' and school_id='$SCHOOL_ID' and signup_sn=:sn order by r_seq";
        $stmt = oci_parse($conn, $sql);
        oci_bind_by_name($stmt, ':sn',  $payload['sn']);
        oci_execute($stmt, OCI_DEFAULT);
        $nrows = oci_fetch_all($stmt, $data); //$nrows -->總筆數
        oci_free_statement($stmt);
        $a['member'] = [];
        //echo count($data['R_NAME']) ;
        for ($i = 0; $i < count($data['R_NAME']); $i++) {
            //echo $data['R_NAME'][0] ;
            // $data['SENDEMAILTIME'][$i], $data['R_READTIME'][$i], $data['R_FILLTIME'][$i]
            $sendemailtime = ($data['SENDEMAILTIME'][$i] == "") ? "<span class='text-danger'><b>尚未寄Email通知推薦人!</b></span>" : "<span class='text-success'><b>" . $data['SENDEMAILTIME'][$i] . "</b></span>";
            $r_readtime = ($data['R_READTIME'][$i] == "") ? "<span class='text-danger'><b>推薦人尚未讀取Email通知!</b></span>" : "<span class='text-success'><b>" . $data['R_READTIME'][$i] . "</b></span>";
            $r_filltime = ($data['R_FILLTIME'][$i] == "") ? "<span class='text-danger'><b>推薦人尚未填寫推薦函!</b></span>" : "<span class='text-success'><b>" . $data['R_FILLTIME'][$i] . "</b></span>";
            $a['member'][] = array($data['R_SEQ'][$i], $data['R_NAME'][$i], $data['R_ORG'][$i], $data['R_TITLE'][$i], $data['R_EMAIL'][$i], $sendemailtime, $r_readtime, $r_filltime, $data['SENDEMAILTIME'][$i], $data['R_READTIME'][$i], $data['R_FILLTIME'][$i]);
        }
        $result = $a;
    } else if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        extract($_POST);
        //寄發Email通知
        if ($_POST['oper'] == "sendmail") {
            //推薦人基本資料
            $r_seq = $_POST['r_seq'];
            $sql = "select * from recom_letter where year='$ACT_YEAR_NO' and school_id='$SCHOOL_ID' and signup_sn=:sn and r_seq='$r_seq' order by r_seq";
            $stmt = oci_parse($conn, $sql);
            oci_bind_by_name($stmt, ':sn',  $payload['sn']);
            oci_execute($stmt, OCI_DEFAULT);
            $nrows = oci_fetch_all($stmt, $result1); //$nrows -->總筆數
            oci_free_statement($stmt);

            if (count($result1['R_NAME']) == 0) {
                $mail_msg = $sql;
                $post_processing[] = function () use ($mail_msg) {
                    sendMail(0, array('title' => "寄發Email通知失敗(查無推薦人資料)", 'content' => $mail_msg));
                };
                throw new Exception("寄發Email通知失敗(查無推薦人資料)");
            } else {
                //sendmail
                $to = $result1['R_EMAIL'][0];
                $r_name = $result1['R_NAME'][0];
                $r_token = $result1['TOKEN'][0];

                $sql = "select a.name stud_name,b.name dept_name from signupdata a,department b where a.year='$ACT_YEAR_NO' and a.school_id='$SCHOOL_ID' and a.signup_sn=:sn and a.school_id=b.school_id and a.year=b.year and a.dept_id=b.id ";
                $stmt = oci_parse($conn, $sql);
                oci_bind_by_name($stmt, ':sn',  $payload['sn']);
                oci_execute($stmt, OCI_DEFAULT);
                $nrows = oci_fetch_all($stmt, $result2); //$nrows -->總筆數
                oci_free_statement($stmt);
                if (count($result2['STUD_NAME']) == 0) {
                    $mail_msg = $sql;
                    $post_processing[] = function () use ($mail_msg) {
                        sendMail(0, array('title' => "寄發Email通知失敗(查無考生資料)", 'content' => $mail_msg));
                    };
                    throw new Exception("寄發Email通知失敗(查無考生資料)");
                } else {

                    $payload['to'] = $to;
                    $payload['stud_name'] = $result2['STUD_NAME'][0];
                    $payload['dept_name'] = $result2['DEPT_NAME'][0];
                    $payload['r_name'] = $result1['R_NAME'][0];
                    $payload['r_token'] = $result1['TOKEN'][0];

                    $post_processing[] = function () use ($payload) {
                        /**
                         * 寄發通知信
                         */
                        $msg_type = 9;
                        $email = sendMail($msg_type, $payload);


                        /**
                         * 寫入log
                         */
                        $fp = fopen(dirname(__FILE__) . "/../logs/dbg_msg.log", "a+");
                        fwrite($fp, "推薦函填寫通知 - API/signup/letter.php - $msg_type - " . $payload['sn'] . " - $email - \n");
                        fclose($fp);
                    };
                }

                //update data
                $sql = "update recom_letter set SENDEMAILTIME='$acttime' where year='$ACT_YEAR_NO' and school_id='$SCHOOL_ID' and signup_sn=:sn and  r_seq='$r_seq' ";
                //echo $sql;
                $stmt = oci_parse($conn, $sql);
                oci_bind_by_name($stmt, ':sn',  $payload['sn']);
                oci_execute($stmt, OCI_DEFAULT);
                oci_free_statement($stmt);
            }
        } else if ($_POST['oper'] == "saveall") {
            //update 研究方向
            $research = $_POST["research"];
            $sql = "update signupdata set research='$research' where year='$ACT_YEAR_NO' and school_id='$SCHOOL_ID' and signup_sn=:sn";

            $stmt = oci_parse($conn, $sql);
            oci_bind_by_name($stmt, ':sn',  $payload['sn']);
            oci_execute($stmt, OCI_DEFAULT);
            oci_free_statement($stmt);

            for ($i = 0; $i < sizeof($_POST['r_seq']); $i++) {
                $token = md5($signup_sn . $r_email[$i]);
                $sql = "select * from recom_letter where year='$ACT_YEAR_NO' and school_id='$SCHOOL_ID' and signup_sn=:sn and r_seq='" . $r_seq[$i] . "' ";
                $stmt = oci_parse($conn, $sql);
                oci_bind_by_name($stmt, ':sn',  $payload['sn']);
                oci_execute($stmt, OCI_DEFAULT);
                $nrows = oci_fetch_all($stmt, $data); //$nrows -->總筆數
                oci_free_statement($stmt);
                if (count($data['R_NAME']) > 0) {
                    if (strlen($data['R_READTIME'][0]) > 0 || strlen($data['R_FILLTIME'][0]) > 0) {
                        //推薦人已讀取或已填寫,都不可修改
                        continue;
                    }
                    if (strlen($data['SENDEMAILTIME'][0]) == 0) {
                        //尚未寄Email,所有欄位皆可修改
                        $sql = "update recom_letter set R_NAME='" . $r_name[$i] . "',R_ORG='" . $r_org[$i] . "',R_TITLE='" . $r_title[$i] . "',R_EMAIL='" . $r_email[$i] . "',acttime='$acttime',actip='$actip',token='$token'  where year='$ACT_YEAR_NO' and school_id='$SCHOOL_ID' and signup_sn=:sn and  r_seq='" . $r_seq[$i] . "' ";
                    } else {
                        //已寄發Email,只可修改email
                        $sql = "update recom_letter set R_EMAIL='" . $r_email[$i] . "',acttime='$acttime',actip='$actip',token='$token'  where year='$ACT_YEAR_NO' and school_id='$SCHOOL_ID' and signup_sn=:sn and  r_seq='" . $r_seq[$i] . "' ";
                    }
                } else {
                    $sql = "insert into recom_letter(ID,SIGNUP_SN,R_SEQ,R_NAME,R_ORG,R_TITLE,R_EMAIL,acttime,actip,SCHOOL_ID,YEAR,token)
                values('$id',:sn,'" . ($i + 1) . "','" . $r_name[$i] . "','" . $r_org[$i] . "','" . $r_title[$i] . "','" . $r_email[$i] . "','$acttime','$actip' ,'$SCHOOL_ID','$ACT_YEAR_NO','$token')";
                }
                //echo $sql;

                $stmt = oci_parse($conn, $sql);
                oci_bind_by_name($stmt, ':sn',  $payload['sn']);
                oci_execute($stmt, OCI_DEFAULT);
                oci_free_statement($stmt);
                //echo "r=" . $result;

                $result['message'] = "資料存檔成功!";
            }
        } else
            throw new Exception("Bad Request", 400);
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
    //$result['line'] = $e->getLine();

    if ($e->getCode() === 409)
        $result['message'] = "ERROR！相同資料已存在。";
    else
        $result['message'] = $e->getMessage();
}



oci_close($conn);
echo json_encode($result);
exit();
