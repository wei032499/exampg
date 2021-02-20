<?php
header('Content-Type:application/json');
$result = array('data' => array());
$post_processing = array();
try {
    require_once('../common/db.php');
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        if (isset($_GET['id'])) {
            $sql = "SELECT ID,POSITION,CONTENT,to_char(POST_DATE,'yyyy-mm-dd') AS POST_DATE FROM NEWS WHERE SCHOOL_ID='$SCHOOL_ID' AND ID=:id ORDER BY POSITION ASC, POST_DATE DESC";
            $stmt = oci_parse($conn, $sql);
            oci_bind_by_name($stmt, ':id',  $_GET['id']);
            oci_execute($stmt, OCI_DEFAULT);
            $nrows = oci_fetch_all($stmt, $results); //$nrows -->總筆數
            oci_free_statement($stmt);
            for ($i = 0; $i < $nrows; $i++) {
                $result['data'][] = array('id' => $results['ID'][$i], 'date' => $results['POST_DATE'][$i], "content" => $results['CONTENT'][$i], "position" => $results['POSITION'][$i]);
            }
        } else {
            $sql = "SELECT ID,POSITION,CONTENT,to_char(POST_DATE,'yyyy-mm-dd') AS POST_DATE FROM NEWS WHERE SCHOOL_ID='$SCHOOL_ID' ORDER BY POSITION ASC, POST_DATE DESC";
            $stmt = oci_parse($conn, $sql);
            oci_execute($stmt, OCI_DEFAULT);
            $nrows = oci_fetch_all($stmt, $results); //$nrows -->總筆數
            oci_free_statement($stmt);
            for ($i = 0; $i < $nrows; $i++) {
                $result['data'][] = array('id' => $results['ID'][$i], 'date' => $results['POST_DATE'][$i], "content" => $results['CONTENT'][$i]);
            }
        }
    } else if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        session_start();
        if (!isset($_SESSION['username']))
            throw new Exception("Unauthorized", 401);

        $timestamp = time();
        $time = date("Y-m-d H:i:s");
        $sql = "INSERT INTO NEWS (ID,POSITION,CONTENT,POST_DATE,SCHOOL_ID) VALUES ('$timestamp',:position,:content,to_date('$time','yyyy-mm-dd HH24:MI:SS'),'$SCHOOL_ID')";
        $stmt = oci_parse($conn, $sql);
        $params = array($_POST['position'], $_POST['content']);
        bind_by_array($stmt, $sql, $params);
        oci_execute($stmt, OCI_DEFAULT);
    } else if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
        session_start();
        if (!isset($_SESSION['username']))
            throw new Exception("Unauthorized", 401);

        parse_str(file_get_contents("php://input"), $post_vars);
        $time = date("Y-m-d H:i:s");
        $sql = "UPDATE NEWS SET POSITION=:position,CONTENT=:content,POST_DATE=to_date('$time','yyyy-mm-dd HH24:MI:SS') WHERE ID=:id AND SCHOOL_ID=$SCHOOL_ID";
        $stmt = oci_parse($conn, $sql);
        $params = array($post_vars['position'], $post_vars['content'], $post_vars['id']);
        bind_by_array($stmt, $sql, $params);
        oci_execute($stmt, OCI_DEFAULT);
    } else if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
        session_start();
        if (!isset($_SESSION['username']))
            throw new Exception("Unauthorized", 401);

        parse_str(file_get_contents("php://input"), $post_vars);
        $sql = "DELETE FROM NEWS WHERE ID=:id AND SCHOOL_ID=$SCHOOL_ID";
        $stmt = oci_parse($conn, $sql);
        oci_bind_by_name($stmt, ':id',  $post_vars['id']);
        oci_execute($stmt, OCI_DEFAULT);
    } else
        throw new Exception("Method Not Allowed", 405);

    oci_commit($conn); //無發生任何錯誤，將資料寫進資料庫
} catch (Exception $e) {
    oci_rollback($conn);
    setHeader($e->getCode());
    $result = array();
    $result['code'] = $e->getCode();
    $result['message'] = $e->getMessage();
    $result['line'] = $e->getLine();
}

register_shutdown_function("shutdown_function", $post_processing);

echo json_encode($result);
exit(); // You need to call this to send the response immediately
