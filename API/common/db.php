<?php

require(dirname(__FILE__) . '/db_account.php');

date_default_timezone_set("Asia/Taipei");


$conn = oci_pconnect($username, $password, "//" . $servername . "/" . $dbname, "AL32UTF8");

if (!$conn) {
    $e = oci_error();
    throw new Exception($e['message']);
}

require_once(dirname(__FILE__) . '/functions.php');
