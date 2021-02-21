<?php
$post_processing = array();
try {
	require_once('./API/common/db.php');

	if (!isset($_COOKIE['token']))
		header("Location: ./query_signup.php");
	else {

		$Token = new Token($conn, $_COOKIE['token']);
		$payload = $Token->verify();
		if ($payload === false || $payload['authority'] !== 1)
			header("Location: ./query_signup.php");
		else if ($payload['status'] !== 2 && $payload['status'] !== 3) {
			if ($payload['status'] === 0)
				echo "<script>alert('您尚未繳費或繳交的費用尚未入帳，若您已繳費，請30分鐘後再試一次。');window.location.replace('./');</script>";
			else if ($payload['status'] === 1)
				echo "<script>alert('尚未填寫報名表！');window.location.replace('./signup.php');</script>";
			else if ($payload['status'] === 2)
				echo "<script>alert('請先完成資料確認，方可查詢報名資料。');window.location.replace('./confirm.php?step=2');</script>";
			else if ($payload['status'] === 3)
				echo "<script>alert('報名完成，資料已鎖定！');window.location.replace('./');</script>";
			else
				echo "<script>alert('ERROR！');window.location.replace('./');</script>";
		} else {

			$ch = curl_init();
			$actual_link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]/" . explode("/", $_SERVER['REQUEST_URI'])[1];
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_URL, $actual_link . "/API/signup/form.php");
			curl_setopt($ch, CURLOPT_HTTPHEADER, array("Cookie: token=" . $_COOKIE['token'] . ";username=" . $_COOKIE['username']));
			// curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); //規避ssl的證書檢查
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE); // 跳過host驗證
			$data = json_decode(curl_exec($ch), true);
			$data = $data['data'];
			$httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
			curl_close($ch);
			if ($httpcode !== 200)
				throw new Exception($data['message'], $data['code']);

			require_once('./API/common/chinese.php');

			class PDF extends PDF_Unicode
			{
				function Header()
				{
				} //end Header

				function printRow($row, $x, $y, $size)
				{
					$this->SetXY($x, $y);
					$this->SetFont('font1', '', $size);
					//echo "go".$row;
					//system.exit();
					$this->Cell(40, 4, "$row", 0, 0, 'L');
					$this->Ln();
				} //end printRow

				function drawStamp($row, $x, $y, $size) //畫右上角的郵票處
				{
					$this->SetXY($x, $y);
					$this->SetFont('font1', '', $size);
					//echo "go".$row;
					//system.exit();
					$this->MultiCell(37, 10, "$row", 1, 'L');
					$this->Ln();
				} //end printRow
			} //end PDF class


			$pdf = new PDF();
			$pdf->SetMargins(5, 5); //設定邊界(需在第一頁建立以前)

			$pdf->AddUniCNShwFont('font1', 'DFKaiShu-SB-Estd-BF');
			$pdf->SetFont('font1');
			$pdf->AddPage('L', 'A4');

			// //-------------------printData start------------------------

			$name = $data['name'];
			$id = $data['id'];
			$dept_id = $data['dept'];
			$organize_id = $data['organize_id'];
			$address = $data['address'];


			$stmt = oci_parse($conn, "SELECT NAME,LOCATION FROM DEPARTMENT WHERE  SCHOOL_ID='$SCHOOL_ID' AND YEAR='$ACT_YEAR_NO' AND ID=:dept_id");
			oci_bind_by_name($stmt, ':dept_id',  $dept_id);
			oci_execute($stmt, OCI_DEFAULT);
			if (!oci_fetch($stmt))
				throw new Exception("No Data");
			$dept_name = oci_result($stmt, 'NAME');
			$location = oci_result($stmt, 'LOCATION');
			oci_free_statement($stmt);

			$stmt = oci_parse($conn, "SELECT NAME FROM ORGANIZE WHERE SCHOOL_ID='$SCHOOL_ID' AND YEAR='$ACT_YEAR_NO' AND ID=:organize_id");
			oci_bind_by_name($stmt, ':organize_id',  $organize_id);
			oci_execute($stmt, OCI_DEFAULT);
			if (!oci_fetch($stmt))
				throw new Exception("No Data");
			$organize = oci_result($stmt, 'NAME');
			oci_free_statement($stmt);

			foreach ($data['subject'] as $key => $val) {
				$stmt = oci_parse($conn, "SELECT NAME FROM SUBJECT WHERE SCHOOL_ID='$SCHOOL_ID' AND YEAR='$ACT_YEAR_NO' AND ID=:subject_id");
				oci_bind_by_name($stmt, ':subject_id',  $data['subject'][$key]);
				oci_execute($stmt, OCI_DEFAULT);
				if (!oci_fetch($stmt))
					throw new Exception("No Data");
				$organize .= " " . oci_result($stmt, 'NAME');
				oci_free_statement($stmt);
			}

			//寄
			$send_code = $data['zipcode']; //郵遞區號
			$cell = $data['tel_m']; //電話
			$term = $dept_name; //報考系所

			//收
			if ($location === "1") //寶山校區
			{
				$schl_addr = "彰化市師大路二號";
				$rec_code = "500208";
			} else //進德校區 
			{
				$schl_addr = "彰化市進德路一號";
				$rec_code = "500207";
			}



			//注意事項
			$att1 = "一、請仔細檢查報名表件是否備齊，依序整理放置。";
			$att2_1 = "二、請將本頁填妥黏貼於報名專用資料袋(請自備)，並依簡章網路報名說明事項於報名截止日前投郵。";
			$att3 = "三、每一封面，僅限一人使用。";

			//郵票
			$stamp = "貼郵票處        (請自行貼足郵票)  ";

			//顯示內容
			$pdf->printRow($send_code . "　　　　　　　       ", 30, 20, 18);
			$pdf->printRow($address, 30, 30, 18);
			$pdf->printRow("$name 寄", 30, 40, 14);
			$pdf->printRow("電    話：" . $cell, 30, 50, 14);
			$pdf->printRow("報考系所：" . $term . "(碩士班)", 30, 60, 14);
			$pdf->printRow("報考組(科)別：" . $organize, 30, 70, 14);

			$pdf->drawStamp($stamp, 230, 16, 12);
			$pdf->printRow($rec_code, 110, 100, 18);
			$pdf->printRow($schl_addr, 110, 110, 18);
			$pdf->printRow("國立彰化師範大學" . $term . "　收", 110, 120, 18);
			//$pdf->printRow("國立彰化師範大學招生委員會  收", 110, 120, 18);
			$pdf->printRow("注意事項：" . $att1, 27, 150, 12);
			$pdf->printRow("          " . $att2_1, 27, 160, 12);
			//$pdf->printRow("          ".$att2_2,37,170,12);
			$pdf->printRow("          " . $att3, 27, 170, 12);

			//-------------------printData end------------------------



			$pdf->SetDisplayMode('real');
			$pdf->Output();
		}
	}
} catch (Exception $e) {
	setHeader($e->getCode());
	$result = array();
	$result['code'] = $e->getCode();
	$result['message'] = $e->getMessage();
	$result['line'] = $e->getLine();
	echo "<script>alert('" . $e->getMessage() . "');window.location.replace('./');</script>";
}

register_shutdown_function("shutdown_function", $post_processing);
exit(); // You need to call this to send the response immediately