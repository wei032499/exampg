<?php
header('Content-Type:application/json');
header("Cache-Control: no-cache");
$result = array('data' => array());
try {
    require_once('../common/db.php');
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        if (isset($_GET['id'])) {
            $sql = "SELECT ID,POSITION,PRIORITY,CONTENT,to_char(POST_DATE,'yyyy-mm-dd') AS POST_DATE, to_char(POST_DATE,'yyyy-mm-dd HH24:MI:SS') AS POST_TIMESTAMP FROM NEWS WHERE SCHOOL_ID='$SCHOOL_ID' AND ID=:id ORDER BY POSITION ASC, PRIORITY DESC, POST_TIMESTAMP DESC";
            $stmt = oci_parse($conn, $sql);
            oci_bind_by_name($stmt, ':id',  $_GET['id']);
            oci_execute($stmt, OCI_DEFAULT);
            $nrows = oci_fetch_all($stmt, $results); //$nrows -->總筆數
            oci_free_statement($stmt);
            for ($i = 0; $i < $nrows; $i++) {
                $result['data'][] = array('id' => $results['ID'][$i], 'date' => $results['POST_DATE'][$i], "content" => $results['CONTENT'][$i], "position" => $results['POSITION'][$i], 'priority' => $results['PRIORITY'][$i]);
            }
        } else {
            $sql = "SELECT ID,POSITION,PRIORITY,CONTENT,to_char(POST_DATE,'yyyy-mm-dd') AS POST_DATE, to_char(POST_DATE,'yyyy-mm-dd HH24:MI:SS') AS POST_TIMESTAMP FROM NEWS WHERE SCHOOL_ID='$SCHOOL_ID' ORDER BY POSITION ASC, PRIORITY DESC, POST_TIMESTAMP DESC";
            $stmt = oci_parse($conn, $sql);
            oci_execute($stmt, OCI_DEFAULT);
            $nrows = oci_fetch_all($stmt, $results); //$nrows -->總筆數
            oci_free_statement($stmt);
            for ($i = 0; $i < $nrows; $i++) {
                $result['data'][] = array('id' => $results['ID'][$i], 'date' => $results['POST_DATE'][$i], "content" => $results['CONTENT'][$i]);
            }
        }
    } else if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $payload = JWT::verifyToken($_COOKIE['token']);
        if ($payload === false || !isset($payload['admin']) || $payload['admin'] !== 0)
            throw new Exception("Unauthorized", 401);

        $timestamp = time();
        $time = date("Y-m-d H:i:s");
        $sql = "INSERT INTO NEWS (ID,POSITION,PRIORITY,CONTENT,POST_DATE,SCHOOL_ID) VALUES ('$timestamp',:position,:priority,:content,to_date('$time','yyyy-mm-dd HH24:MI:SS'),'$SCHOOL_ID')";
        $stmt = oci_parse($conn, $sql);
        $params = array($_POST['position'], $_POST['priority'], $_POST['content']);
        bind_by_array($stmt, $sql, $params);
        oci_execute($stmt, OCI_DEFAULT);
    } else if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
        $payload = JWT::verifyToken($_COOKIE['token']);
        if ($payload === false || !isset($payload['admin']) || $payload['admin'] !== 0)
            throw new Exception("Unauthorized", 401);

        parse_str(file_get_contents("php://input"), $post_vars);
        $time = date("Y-m-d H:i:s");
        $sql = "UPDATE NEWS SET POSITION=:position,PRIORITY=:priority,CONTENT=:content,POST_DATE=to_date('$time','yyyy-mm-dd HH24:MI:SS') WHERE ID=:id AND SCHOOL_ID=$SCHOOL_ID";
        $stmt = oci_parse($conn, $sql);
        $params = array($post_vars['position'], $post_vars['priority'], $post_vars['content'], $post_vars['id']);
        bind_by_array($stmt, $sql, $params);
        oci_execute($stmt, OCI_DEFAULT);
    } else if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
        $payload = JWT::verifyToken($_COOKIE['token']);
        if ($payload === false || !isset($payload['admin']) || $payload['admin'] !== 0)
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
    //$result['line'] = $e->getLine();
}


echo json_encode($result);
exit();
