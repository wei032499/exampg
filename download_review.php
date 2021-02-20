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
			require_once('./signup/alter_login.php');
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

				function printRow($row, $align, $h, $size) //印出一般資料
				{
					$this->SetFont('font1', '', $size);
					$this->Cell(0, $h, "$row", 0, 0, $align);
					$this->Ln();
				} //end printRow

				function printRow_S($row_1, $row_2, $align, $h, $size) //印出較龜毛的資料
				{
					$this->SetFont('font1', '', $size);
					$this->Cell(70, $h, "$row_1", 0, 0, $align);
					$this->Cell(70, $h, "$row_2", 0, 0, $align);
					$this->Ln();
				} //end printRow_S

				function drawTable() //畫出備審資料項目
				{
					$this->SetFont('font1', '', 14);
					for ($j = 0; $j < 2; $j++) //draw title
					{
						$this->Cell(16, 12, "項目", 1, 0, 'C');
						$this->Cell(60, 12, "名          稱", 1, 0, 'C');
						$this->Cell(16, 12, "份數", 1, 0, 'C');
					}
					$this->Ln();
					for ($k = 1; $k <= 5; $k++) //draw contents
					{
						for ($j = 0; $j < 2; $j++) {
							if ($j == 1) {
								$this->Cell(16, 12, sprintf("%d", $k + 5), 1, 0, 'C');
							} else {
								$this->Cell(16, 12, $k, 1, 0, 'C');
							}

							$this->Cell(60, 12, "", 1, 0, 'C');
							$this->Cell(16, 12, "", 1, 0, 'C');
						}
						$this->Ln();
					}
					//備註
					$this->Cell(16, 10, "備註：", 'LT', 0, 'C');
					$this->Cell(168, 10, "", 'RT', 0, 'C');
					$this->Ln();
					$this->Cell(184, 10, "", 'LRB', 0, 'C');
					$this->Ln();
				} //end drawTable

				function drawExamSign($x, $y) //畫出簽名的地方
				{
					$this->SetXY($x, $y);
					$this->SetFont('font1', '', 11);
					$this->MultiCell(12, 8, "考生親簽", 1, 'L');
					$this->SetXY($x + 12, $y);
					$this->Cell(50, 16, "", 1, 0, 'C');
				} //end drawExamSign
			} //end PDF class


			$pdf = new PDF();
			$pdf->SetMargins(5, 5); //設定邊界(需在第一頁建立以前)

			$pdf->AddUniCNShwFont('font1', 'DFKaiShu-SB-Estd-BF');
			$pdf->SetFont('font1');
			$pdf->AddPage('P', 'A4');

			// //-------------------printData start------------------------

			$name = $data['name'];
			$gender_array = array("1" => "男", "2" => "女");
			$gender = $gender_array[$data['gender']];
			$id = $data['id'];
			$dept_id = $data['dept'];
			$organize_id = $data['organize_id'];
			$orastatus_id = $data['orastatus_id'];
			$birthday = $data['birthday'];
			$addr_h = $data['zipcode'] . " " . $data['address'];
			$addr_c = $data['zipcode2'] . " " . $data['address2'];
			$call_home = $data['tel_h_a'] . "-" . $data['tel_h'];
			$call_comp = "";
			if ($data['tel_o_a'] !== null)
				$call_comp = $data['tel_o_a'] . "-" . $data['tel_o'];
			$cell = $data['tel_m'];
			$email = $data['email'];
			$name_r = $data['conn_name'];
			$cell_r  = $data['conn_tel'];
			$rela = $data['conn_rel'];

			$prove_type = array("1" => "學士學位", "2" => "同等學力", "3" => "國家考試及格", "4" => "技能檢定合格");
			$background = $prove_type[$data['prove_type']];
			$grade_from = "";
			if ($data['prove_type'] === "1") {
				$grade_from = $data['grad_schol']  . " " . $data['grad_dept'] . ", 於 " . $data['grad_date'] . " 畢業";
			} else if ($data['prove_type'] === "2") {
				$school_type = array("1" => "大學", "2" => "三專", "3" => "二專或五專");
				$graduate = array("1" => "畢業", "2" => "肄業");
				$grade_from = $data['grad_schol'] . " (" . $school_type[$data['ac_school_type']] . "), " . $data['grad_dept'] . "), " . $data['grad_date'] . " " . $graduate[$data['ac_g']] . ", 修業 " . $data['ac_m_y'] . "年, 已離校 " . $data['ac_leave_y'] . " 年";
			}

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

			$stmt = oci_parse($conn, "SELECT NAME FROM ORASTATUS WHERE SCHOOL_ID='$SCHOOL_ID' AND YEAR='$ACT_YEAR_NO' AND ID=:orastatus_id");
			oci_bind_by_name($stmt, ':orastatus_id',  $orastatus_id);
			oci_execute($stmt, OCI_DEFAULT);
			if (!oci_fetch($stmt))
				throw new Exception("No Data");
			$ident = oci_result($stmt, 'NAME');
			oci_free_statement($stmt);

			//寄
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
			$att1 = "註：1.本表填妥請親自簽名後置於備審資料最上頁並依簡章規定依限郵寄(或親自送達)至本校招生系所收，逾期(郵戳為憑)";
			$att2 = "　　　不予受理。";
			$att3 = "　　2.未簽名者，所交備審資料項目以本校招生系所收到資料為準，考生不得異議。";

			//顯示內容
			$pdf->Ln();
			$pdf->Ln();
			$pdf->printRow("國立彰化師範大學", 'C', 7, 14);
			$pdf->SetFont('font1', 'B', 15);
			$pdf->Cell(0, 7, $ACT_YEAR_NO . " 學 年 度 碩 士 班 研 究 生 招 生 考 試", 0, 0, 'C');
			$pdf->Ln();
			$pdf->printRow("審 查 資 料 一 覽 表", 'C', 9, 22);
			$pdf->printRow("壹、 考生基本資料：", 'L', 9, 15);

			$pdf->printRow_S("\t報考系所：" . $term, "　　　　　　　　報考組(科)別：" . $organize, 'L', 8, 12);

			$pdf->printRow("\t報考身分：" . $ident, 'L', 8, 12);
			$pdf->printRow_S("\t姓    名：" . $name, "　　性     別：" . $gender, 'L', 8, 12);
			$pdf->printRow_S("\t出生日期：" . $birthday, "　　身分證字號：" . $id, 'L', 8, 12);
			$pdf->printRow("\t通訊地址：" . $addr_h, 'L', 8, 12);
			$pdf->printRow("\t戶籍地址：" . $addr_c, 'L', 8, 12);
			$pdf->printRow("\t聯絡電話：\t宅：" . $call_home . "\t公：" . $call_comp . "\t手機：" . $cell, 'L', 8, 12);
			$pdf->printRow("\tEmail 信箱：" . $email, 'L', 8, 12);
			$pdf->printRow("\t緊急聯絡人：\t姓名：" . $name_r . "\t電話：" . $cell_r . "\t關係：" . $rela, 'L', 8, 12);
			$pdf->printRow("\t應考學歷：" . $background, 'L', 8, 12);
			$pdf->printRow("   \t\t" . $grade_from, 'L', 8, 12);
			$pdf->printRow("貳、 備審資料項目：", 'L', 9, 15);
			$pdf->SetFont('font1', '', 10);

			$pdf->drawTable(30, 10);
			$pdf->printRow($att1, 'L', 8, 10);
			$pdf->printRow($att2, 'L', 8, 10);
			$pdf->printRow($att3, 'L', 8, 10);
			$pdf->drawExamSign(130, 256);

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