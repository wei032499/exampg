<?php

require_once(dirname(__FILE__) . '/db_account.php');

date_default_timezone_set("Asia/Taipei");


$conn = oci_pconnect($username, $password, "//" . $servername . "/" . $dbname, "AL32UTF8");

if (!$conn) {
    $e = oci_error();
    throw new Exception($e['message']);
}

require_once(dirname(__FILE__) . '/functions.php');

function bind_by_array($stmt, $sql, $array)
{
    $sql .= " "; //fix變數在結尾
    preg_match_all("/(?<=[( ,=]):\w+(?=[) ,])/", $sql, $matches);

    if (count($matches[0]) !== count($array))
        return false;

    foreach ($matches[0] as $key => $value)
        oci_bind_by_name($stmt, $value,  $array[$key]);
    return true;
}
