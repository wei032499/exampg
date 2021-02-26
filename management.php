<?php
require_once('./API/common/db.php');

try {
    if (!isset($_COOKIE['token']))
        require_once('./management/login.php');
    else {
        $payload = JWT::verifyToken($_COOKIE['token']);
        if ($payload === false || !isset($payload['admin']) || $payload['admin'] !== 0)
            require_once('./management/login.php');
        else if (isset($_GET['page'])) {
            if ($_GET['page'] === "news")
                require_once("./management/news.php");
        } else
            require_once("./management/home.php");
    }
} catch (Exception $e) {
    setHeader($e->getCode());
    $result = array();
    $result['code'] = $e->getCode();
    $result['message'] = $e->getMessage();
    // header('Content-Type:application/json');
    // echo json_encode($result);
    header("Location: ./management.php");
}


exit();
