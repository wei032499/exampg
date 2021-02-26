<?php
header('Content-Type:application/json');
$result = array();

try {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        require_once('../common/db.php');
        $sql = "SELECT SN,EMAIL,ACCOUNT_NO,PWD FROM SN_DB WHERE SCHOOL_ID='$SCHOOL_ID' AND year='$ACT_YEAR_NO' and EMAIL=:email and checked='1' ORDER BY ORDER_NO DESC";
        $stmt = oci_parse($conn, $sql);
        oci_bind_by_name($stmt, ':email',  $_POST['email']);

        oci_execute($stmt, OCI_DEFAULT);
        $nrows = oci_fetch_all($stmt, $result1); //$nrows -->總筆數
        oci_free_statement($stmt);

        if ($nrows === 0)
            throw new Exception("查無資料！", 404);

        for ($i = 0; $i < $nrows; $i++) {
            $sm_payload = array('sn' => $result1['SN'][$i], 'pwd' => $result1['PWD'][$i], 'email' => $result1['EMAIL'][$i], 'account_no' => $result1['ACCOUNT_NO'][$i]);
            $post_processing[] = function () use ($sm_payload) {
                /**
                 * 寄發通知信
                 */
                $to = sendMail(6, $sm_payload);


                /**
                 * 寫入log
                 */
                $fp = fopen(dirname(__FILE__) . "/../logs/dbg_msg.log", "a+");
                fwrite($fp, "查詢序號密碼回覆 - API/auth/forget.php - $to - \n");
                fclose($fp);
            };
        }
    } else throw new Exception("Method Not Allowed", 405);
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
