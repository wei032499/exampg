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

$SCHOOL_ID = "3"; ///?
$ACT_YEAR_NO = (int)date("Y") - 1911;
if ((int)date("m") < 8)
    $ACT_YEAR_NO = $ACT_YEAR_NO - 1;
$ACT_YEAR_NO = $ACT_YEAR_NO . "";

function DynamicBindVariables($stmt, $params)
{
    if ($params != null) {
        // Generate the Type String (eg: 'issisd')
        $types = '';
        foreach ($params as $param) {
            if (is_int($param))
                $types .= 'i'; // Integer
            elseif (is_float($param))
                $types .= 'd'; // Double
            elseif (is_string($param))
                $types .= 's'; // String
            else
                $types .= 'b'; // Blob and Unknown
        }

        // Add the Type String as the first Parameter
        $bind_names[] = $types;

        // Loop thru the given Parameters
        for ($i = 0; $i < count($params); $i++) {
            // Create a variable Name
            $bind_name = 'bind' . $i;
            // Add the Parameter to the variable Variable
            $$bind_name = $params[$i];
            // Associate the Variable as an Element in the Array
            $bind_names[] = &$$bind_name;
        }

        // Call the Function bind_param with dynamic Parameters
        call_user_func_array(array($stmt, 'bind_param'), $bind_names);
    }
    return $stmt;
}

function DynamicInsert($conn, $table, $params)
{
    $paramList = array();
    $sql = "INSERT INTO " . $table . " ( ";
    $sqlValues = "(";
    foreach ($params as $key => $value) {
        $sql .= ($key . " ,");
        $paramList[] = $value;

        $sqlValues .= ":" . $key . ",";
    }
    $sqlValues[strlen($sqlValues) - 1] = ")";
    $sql[strlen($sql) - 1] = ")";
    $sql .= " VALUES " . $sqlValues;

    $stmt = oci_parse($conn, $sql);
    foreach ($params as $key => $value) {
        oci_bind_by_name($stmt, $key, $params[$key]);
    }
    if (!oci_execute($stmt, OCI_NO_AUTO_COMMIT)) {
        $error = analyzeError(oci_error()['message']);
        throw new Exception($error['message'], $error['code']);
    }
    oci_free_statement($stmt);
}

function DynamicUpdate($conn, $table, $params, $where)
{
    $paramList = array();
    $sql = "UPDATE " . $table . " SET ";
    foreach ($params as $key => $value) {
        $sql .= ($key . "=:" . $key . ",");
        $paramList[] = $value;
    }
    $sql[strlen($sql) - 1] = " ";

    $sql .= " WHERE 1 ";
    foreach ($where as $key => $value) {
        $sql .= (" AND " . $key . "=:" . $key . " ");
        $paramList[] = $value;
    }


    $stmt = oci_parse($conn, $sql);
    foreach ($params as $key => $value) {
        oci_bind_by_name($stmt, $key, $params[$key]);
    }
    if (!oci_execute($stmt, OCI_NO_AUTO_COMMIT)) {
        $error = analyzeError(oci_error()['message']);
        throw new Exception($error['message'], $error['code']);
    }

    oci_free_statement($stmt);
}
