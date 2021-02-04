<?php

header('Content-Type:application/json');
$result = array();
try {
    require_once('../db.php');
    if (!isset($_COOKIE['token']))
        throw new Exception("Unauthorized", 401);
    $Token = new Token($conn, $_COOKIE['token']);
    $payload = $Token->verify();
    if ($payload === false)
        throw new Exception("Unauthorized", 401);

    $location = "../../upload/";

    if ($_SERVER['REQUEST_METHOD'] === 'GET') {

        $params = array();

        $attachment_location = $location . $payload['sn'] . ".pdf";
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

        /*$stmt = $conn->prepare("SELECT * FROM  signupdata  WHERE sn=? "); //oci_parse($conn, $sql);
        $params[] =  $payload['sn'];
        DynamicBindVariables($stmt, $params);

        if (!oci_execute($stmt)) //oci_execute($stmt) 
        {
            $error = analyzeError(oci_error()['message']);
            throw new Exception($error['message'], $error['code']);
        }
        if ($row = oci_fetch_assoc($stmt)) //$row = oci_fetch_assoc($stmt)
        {
            $attachment_location = $location . $row['file'];
            if (file_exists($attachment_location)) {

                header($_SERVER["SERVER_PROTOCOL"] . " 200 OK");
                header("Cache-Control: public"); // needed for internet explorer
                header("Content-Type: application/pdf");
                header("Content-Transfer-Encoding: Binary");
                header("Content-Length:" . filesize($attachment_location));
                header("Content-Disposition: attachment; filename=" . $payload['sn'] . ".pdf");
                readfile($attachment_location);
            } else
                throw new Exception("Not Found", 404);
        } else
            throw new Exception("尚未上傳備審資料", 404);
        $stmt->close();*/
    } else if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        if ($payload['status'] !== 0)
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
                if (!move_uploaded_file($_FILES['file']['tmp_name'], $location . $payload['sn'] . "." . $fileType))
                    throw new Exception("檔案上傳錯誤");
                $result['filename'] = $payload['sn'] . "." . $fileType;
            } else
                throw new Exception("請上傳正確的檔案類型", 400);
        }
        throw new Exception("Bad Request", 400);
    }

    setcookie('token', $Token->refresh(), $cookie_options_httponly);
} catch (Exception $e) {
    @oci_rollback($conn);
    @oci_close($conn);
    setHeader($e->getCode());
    $result['code'] = $e->getCode(); //$e->getCode();
    $result['message'] = $e->getMessage();

    //$e->getMessage() . " on line " . $e->getLine()
}

echo json_encode($result);
