<?php
header('Content-Type:application/json');
$result = array();
try {

	if ($_SERVER['REQUEST_METHOD'] === 'POST') {
		$id = strtoupper($_POST['id']);
		if (strlen($id) != 10) {
			$result['graduated'] = 0;
		} else {
			require_once('../db.php');

			//曾報考當年度碩推者(29x為博推),免繳報名費
			$sql = "SELECT count(*) from signupdata WHERE  id=:id and school_id='2' and year=:ACT_YEAR_NO and substr(dept_id,1,2)<>'29'";
			$stmt = oci_parse($conn, $sql);
			$params = array(':id' => $id, ':ACT_YEAR_NO' => $ACT_YEAR_NO);
			foreach ($params as $key => $val)
				oci_bind_by_name($stmt, $key, $params[$key]);
			oci_execute($stmt, OCI_DEFAULT);
			oci_fetch($stmt);
			$nrows = oci_result($stmt, 1); //$nrows -->總筆數
			if ($nrows > 0) {
				$result['graduated'] = 2;
			} else {
				//96年以前在職專班畢業生(尚未電腦化)
				//需再修改account_c.php
				if ($id == "L120210922" || $id == "Q220098486" || $id == "N121851563" || $id == "N220261405" || $id == "N122009956") {
					$result['graduated'] = 1;
				}
				//畢業生報名費打八折(106/12/6淑琬確認--限在學,畢業生,延長修業 , 108/09/19 淑琬要求排除學分班(94))
				$sql = "SELECT count(*) from dean.s30_student WHERE  stu_idno=:id and stu_status in ('1','8','29')  and substr(stu_id,4,2)<>'94' ";
				$stmt = oci_parse($conn, $sql);
				$params = array(':id' => $id);
				foreach ($params as $key => $val)
					oci_bind_by_name($stmt, $key, $params[$key]);
				oci_execute($stmt, OCI_DEFAULT);
				oci_fetch($stmt);
				$nrows = oci_result($stmt, 1); //$nrows -->總筆數
				if ($nrows > 0) {
					$result['graduated'] = 1;
				} else {
					$result['graduated'] = 0;
				}
			}
		}
	} else
		throw new Exception("Method Not Allowed", 405);
} catch (Exception $e) {
	@oci_rollback($conn);

	setHeader($e->getCode());
	$result['code'] = $e->getCode(); //$e->getCode();
	$result['message'] = $e->getMessage();
}
@oci_close($conn);

echo json_encode($result);
