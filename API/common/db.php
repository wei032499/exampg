<?php
require_once(dirname(__FILE__) . '/functions.php');
require_once(dirname(__FILE__) . '/db_account.php');

date_default_timezone_set("Asia/Taipei");


$conn = oci_pconnect($username, $password, "//" . $servername . "/" . $dbname, "AL32UTF8");
//$conn = new mysqli($servername, $username, $password, $dbname); // Create connection

if (!$conn) {
    $e = oci_error();
    throw new Exception($e['message']);
}


function bind_by_array($stmt, $sql, $array)
{
    preg_match_all("/:[^MI:SS|\d:\d]\w+/", $sql, $matches);

    if (count($matches[0]) !== count($array))
        return false;
    foreach ($matches[0] as $key => $value)
        oci_bind_by_name($stmt, $value,  $array[$key]);
    return true;
}
