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

/*$SCHOOL_ID = "3"; // what is this?

$ACT_YEAR_NO = (int)date("Y") - 1911;
if ((int)date("m") < 8)
    $ACT_YEAR_NO = $ACT_YEAR_NO - 1;
$PSELL_DL_START_DATE = "108-12-20 09:00:00";
$PSELL_DL_END_DATE = "109-09-14 23:59:59";
$ACC_START_DATE = "108-12-20 09:00:00";
$ACC_END_DATE = "110-07-08 17:00:00";
$ACC2_START_DATE = "108-12-20";
$ACC2_END_DATE = "109-01-08";
$SU_START_DATE = "109-12-20 09:00:00";
$SU_END_DATE = "110-07-09 17:00:00";
$CARD_START_DATE = "109-01-22 09:00:00";
$CARD_END_DATE = "109-03-27 23:59:59";
$SCORE_START_DATE = "109-03-27 17:00:00";
$SCORE_END_DATE = "109-09-14 23:59:59";
$SCORE2_START_DATE = "109-04-13 17:00:00";
$SCORE2_END_DATE = "109-09-14 23:59:59";
$UBI_START_DATE = "109-03-27 17:00:00";
$UBI_END_DATE = "109-04-20 17:00:00";
$UBI2_START_DATE = "109-04-13 17:00:00";
$UBI2_END_DATE = "109-04-20 17:00:00";
$FSTAT_START_DATE = "111-04-21 13:00:00";
$FSTAT_END_DATE = "111-09-14 23:59:59";

$LOW_INCOME_END_DATE = "109年1月3日";*/
