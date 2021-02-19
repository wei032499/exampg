<?php


header('Content-Type:application/json');
$result = array();
$post_processing = array();
try {
    require_once('../common/db.php');
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $sql = "SELECT SN_DB.SIGNUP_ENABLE,SN_DB.LOCK_UP,SN_DB.CHECKED,SIGNUPDATA.LOCK_UP as FORM_LOCK FROM SN_DB LEFT JOIN SIGNUPDATA ON SN_DB.SCHOOL_ID=SIGNUPDATA.SCHOOL_ID AND SN_DB.YEAR=SIGNUPDATA.YEAR AND SN_DB.SN=SIGNUPDATA.SIGNUP_SN WHERE SN_DB.SCHOOL_ID='$SCHOOL_ID' AND SN_DB.YEAR='$ACT_YEAR_NO' AND ACCOUNT_NO=:account_no";
        $stmt = oci_parse($conn, $sql);
        oci_bind_by_name($stmt, ':account_no', $_POST['account_no']);
        oci_execute($stmt, OCI_DEFAULT);
        if (oci_fetch($stmt)) {
            $signup_enable = oci_result($stmt, "SIGNUP_ENABLE"); // 是否可進行報名
            $lockup = oci_result($stmt, "LOCK_UP"); // 是否已填寫報名表
            $checked = oci_result($stmt, "CHECKED"); // 是否已入帳
            $form_lock = oci_result($stmt, "FORM_LOCK"); // 資料是否已確認
            oci_free_statement($stmt);
            if ($checked !== "1")
                $result['message'] = "您應繳的費用尚未入帳，若您已經繳費，請30分鐘後再查詢一次；若您尚未繳費，請利用ATM或至附近金融機構轉帳繳費，謝謝您。"; //尚未銷帳
            else if ($form_lock === "1")
                $result['message'] = "本校已收到您所繳交的費用，謝謝您。"; //資料已確認(已鎖定)
            else if ($lockup === "0")
                $result['message'] = "本校已收到您所繳交的費用，謝謝您。\n您現在可用您的序號及密碼進行填寫報名表作業。"; //尚未填寫報名表
            else if ($lockup === "1")
                $result['message'] = "本校已收到您所繳交的費用，謝謝您。\n您已填報名表，尚未資料確認，請您務必在報名截止日期前完成資料確認。"; //已填寫報名表，資料尚未確認
            else
                $result['message'] = "ERROR";; //error
        } else
            $result['message'] = "資料庫查無此繳費帳號。";
        /*$sql = "SELECT SIGNUP_ENABLE,LOCK_UP,CHECKED FROM SN_DB WHERE SCHOOL_ID='$SCHOOL_ID' AND YEAR='$ACT_YEAR_NO' AND ACCOUNT_NO=:account_no";
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

        oci_free_statement($stmt);*/
    } else
        throw new Exception("Method Not Allowed", 405);
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