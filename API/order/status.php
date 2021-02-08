<?php


header('Content-Type:application/json');
$result = array();
try {
    require_once('../common/db.php');
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        $sql = "SELECT SIGNUP_ENABLE,LOCK_UP,CHECKED FROM SN_DB WHERE SCHOOL_ID='$SCHOOL_ID' AND YEAR='$ACT_YEAR_NO' AND ACCOUNT_NO=:account_no";
        $stmt = oci_parse($conn, $sql);
        oci_bind_by_name($stmt, ':account_no', $_POST['account_no']);
        oci_execute($stmt, OCI_DEFAULT);
        if (oci_fetch($stmt)) {
            $signup_enable = oci_result($stmt, "SIGNUP_ENABLE"); // 是否已入帳
            $lockup = oci_result($stmt, "LOCK_UP"); // 是否已填寫報名表
            $checked = oci_result($stmt, "CHECKED"); // 資料是否已確認
            if ($signup_enable === "0")
                $result['message'] = "您應繳的費用尚未入帳，若您已經繳費，請30分鐘後再查詢一次；若您尚未繳費，請利用ATM或至附近金融機構轉帳繳費，謝謝您。"; //尚未銷帳
            else if ($checked === "1")
                $result['message'] = "本校已收到您所繳交的費用，謝謝您。"; //資料已確認(已鎖定)
            else if ($lockup === "0")
                $result['message'] = "本校已收到您所繳交的費用，謝謝您。\n您現在可用您的序號及密碼進行填寫報名表作業。"; //尚未填寫報名表
            else if ($lockup === "1")
                $result['message'] = "本校已收到您所繳交的費用，謝謝您。\n您已填報名表，尚未資料確認，請您務必在報名截止日期前完成資料確認。"; //已填寫報名表，資料尚未確認
            else
                $result['message'] = "ERROR";; //error
        } else
            $result['message'] = "資料庫查無此繳費帳號。";

        oci_free_statement($stmt);
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
