<?php
header('Content-Type:application/json');
$result = array();

try {
	require_once('../common/db.php');
	require_once('./functions.php');
	if ($_SERVER['REQUEST_METHOD'] === 'POST') {
		$id = strtoupper($_POST['id']);
		$result['graduated'] = checkGraduated($conn, $id, $ACT_YEAR_NO);
		if ($result['graduated'] === 1)
			$result['message'] = "校友";
		else if ($result['graduated'] === 2)
			$result['message'] = "曾報考當年度碩推，免繳報名費";
		else
			$result['message'] = "";
	} else
		throw new Exception("Method Not Allowed", 405);
} catch (Exception $e) {
	oci_rollback($conn);

	setHeader($e->getCode());
	$result = array();
	$result['code'] = $e->getCode();
	$result['message'] = $e->getMessage();
}

oci_close($conn);
echo json_encode($result);
exit();
