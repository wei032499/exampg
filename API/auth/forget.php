<?php
header('Content-Type:application/json');
$result = array();
try {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        require_once('../common/db.php');
        $sql = "SELECT 1 FROM SN_DB WHERE  school_id='$SCHOOL_ID' and year='$ACT_YEAR_NO' and EMAIL=:email and checked='1' ORDER BY ORDER_NO DESC";
        $stmt = oci_parse($conn, $sql);
        oci_bind_by_name($stmt, ':email',  $_POST['email']);

        oci_execute($stmt, OCI_DEFAULT);

        if (oci_fetch($stmt)) {
            /**
             * 寄發通知信
             */
            sendMail(6, $conn, array('email' => $_POST['email']));


            /**
             * 寫入log
             */
            $to = $_POST['email'];
            $fp = fopen("../logs/dbg_msg.log", "a+");
            fwrite($fp, "查詢序號密碼回覆 - API/auth/forget.php - $to - \n");
            fclose($fp);
        } else
            throw new Exception("查無資料！", 404);

        oci_free_statement($stmt);
    } else throw new Exception("Method Not Allowed", 405);
} catch (Exception $e) {

    oci_rollback($conn);
    setHeader($e->getCode());
    $result = array();
    $result['code'] = $e->getCode();
    $result['message'] = $e->getMessage();
    $result['line'] = $e->getLine();



    //$e->getMessage() . " on line " . $e->getLine()
}
oci_close($conn);

echo json_encode($result);
