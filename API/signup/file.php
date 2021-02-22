<?php

header('Content-Type:application/json');
header("Cache-Control: no-cache");
$result = array();
$post_processing = array();
try {
    require_once('../common/db.php');
    if (!isset($_COOKIE['token']))
        throw new Exception("Unauthorized", 401);
    $Token = new Token($conn, $_COOKIE['token']);
    $payload = $Token->verify();
    if ($payload === false)
        throw new Exception("Unauthorized", 401);

    $location = "../../upload/";

    if ($_SERVER['REQUEST_METHOD'] === 'GET') {

        $attachment_location = $location . $ACT_YEAR_NO . "-" . $payload['sn'] . ".pdf";
        if (file_exists($attachment_location)) {
            if (isset($_GET['export']) && $_GET['export'] === "download") {
                header($_SERVER["SERVER_PROTOCOL"] . " 200 OK");
                header("Cache-Control: public"); // needed for internet explorer
                header("Content-Type: application/pdf");
                header("Content-Transfer-Encoding: Binary");
                header("Content-Length:" . filesize($attachment_location));
                header("Content-Disposition: attachment; filename=" . $payload['sn'] . ".pdf");
                readfile($attachment_location);
            }
        } else
            throw new Exception("Not Found", 404);
    } else if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        if ($payload['status'] !== 1 && $payload['status'] !== 2)
            throw new Exception("Forbidden", 403);

        if (isset($_FILES['file'])) {
            /* Getting file name */
            $filename = $_FILES['file']['name'];

            $fileType = pathinfo($filename, PATHINFO_EXTENSION);
            $fileType = strtolower($fileType);

            /* Location */
            if (!file_exists($location)) {
                mkdir($location, 0777, true);
            }

            /* Valid extensions */
            $valid_extensions = array("pdf");

            /* Check file extension */
            if (in_array(strtolower($fileType), $valid_extensions)) {
                /* Upload file */
                if (!move_uploaded_file($_FILES['file']['tmp_name'], $location . $ACT_YEAR_NO . "-" . $payload['sn'] . "." . $fileType))
                    throw new Exception("檔案上傳錯誤");
                $result['filename'] = $payload['sn'] . "." . $fileType;
            } else
                throw new Exception("請上傳正確的檔案類型", 400);


            $sql = "UPDATE SIGNUPDATA SET DOC_UPLOAD='1' WHERE SCHOOL_ID='$SCHOOL_ID' AND YEAR='$ACT_YEAR_NO' AND SIGNUP_SN=:sn";
            $stmt = oci_parse($conn, $sql);
            oci_bind_by_name($stmt, ':sn',  $payload['sn']);
            oci_execute($stmt, OCI_DEFAULT);

            oci_commit($conn);
        } else
            throw new Exception("Bad Request", 400);
    }

    setcookie('token', $Token->refresh(), $cookie_options_httponly);
} catch (Exception $e) {
    oci_rollback($conn);
    setHeader($e->getCode());
    $result = array();
    $result['code'] = $e->getCode(); //$e->getCode();
    $result['message'] = $e->getMessage();

    //$e->getMessage() . " on line " . $e->getLine()
}

register_shutdown_function("shutdown_function", $post_processing);

oci_close($conn);
echo json_encode($result);
exit(); // You need to call this to send the response immediately