<?php
header('Content-Type:application/json');
$result = array();
try {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        require_once('../common/db.php');
        $sql = "SELECT 1 FROM SN_DB WHERE  school_id='$SCHOOL_ID' and year='$ACT_YEAR_NO' and EMAIL=:email and checked='1' ORDER BY ORDER_NO DESC";
        $stmt = oci_parse($conn, $sql);
        oci_bind_by_name($stmt, ':email',  $_POST['email']);

        if (!oci_execute($stmt, OCI_DEFAULT)) {
            $error = analyzeError(oci_error()['message']);
            throw new Exception($error['message'], $error['code']);
        }

        if (oci_fetch($stmt)) {
            /**
             * 寄發通知信
             */
            $msg_type = 6;
            sendMail($msg_type, $conn, array('email' => $_POST['email']));


            /**
             * 寫入log
             */
            $to = $_POST['email'];
            $fp = fopen("../logs/dbg_msg.log", "a+");
            fwrite($fp, "查詢序號密碼回覆 - API/auth/forget.php - $msg_type - $to - \n");
            fclose($fp);
        } else
            throw new Exception("查無資料！", 404);

        oci_free_statement($stmt);
    } else throw new Exception("Method Not Allowed", 405);
} catch (Exception $e) {

    @oci_rollback($conn);
    setHeader($e->getCode());
    $result = array();
    $result['code'] = $e->getCode();
    $result['message'] = $e->getMessage();
    $result['line'] = $e->getLine();



    //$e->getMessage() . " on line " . $e->getLine()
}
@oci_close($conn);

echo json_encode($result);
