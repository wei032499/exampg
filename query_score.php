<?php
require_once('./API/common/db.php');

try {
    if (!isset($_COOKIE['token']))
        require_once('./query/score_login.php');
    else {
        $payload = JWT::verifyToken($_COOKIE['token']);
        if ($payload === false || !isset($payload['id']) || !isset($payload['sid']))
            require_once('./query/score_login.php');
        // else
        //     require_once("./query/");
    }
} catch (Exception $e) {
    setHeader($e->getCode());
    $result = array();
    $result['code'] = $e->getCode();
    $result['message'] = $e->getMessage();

    header("Location: ./query_score.php");
}


exit();
