<?php

require_once(dirname(__FILE__) . '/db.php');

$sql = "SELECT NAME,VALUE FROM SCHEDULE WHERE SCHOOL_ID='3'";
$stmt = oci_parse($conn, $sql);

oci_execute($stmt, OCI_DEFAULT);


$nrows = oci_fetch_all($stmt, $results); //$nrows -->總筆數
oci_free_statement($stmt);

for ($i = 0; $i < $nrows; $i++) {
    $name = $results['NAME'][$i];
    $value = $results['VALUE'][$i];
    $$name = $value;
}
