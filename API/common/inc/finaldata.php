<?php
require_once(dirname(__FILE__) . '/../db.php');
require_once(dirname(__FILE__) . '/../config.php');


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

$u_name = $data['name'];
$id = $data['id'];
$dept_id = $data['dept'];
$organize_id = $data['organize_id'];

$cripple_name = array(1 => "聽覺障礙", "視覺障礙", "腦性麻庳", "自閉症", "學習障礙", "其它障礙");
$CRIPPLE = $data['disabled'];
$cripple_type = $data['disabled_type'];
$comments = $data['comments'];
$address = $data['address'];
$e_place = $data['place'];

$nrows_union = count($data['union_priority']);
if ($nrows_union > 1) {
  $union_flag = 1;
  for ($i = 0; $i < $nrows_union; $i++) {
    $stmt = oci_parse($conn, "SELECT NAME FROM DEPARTMENT WHERE  SCHOOL_ID='$SCHOOL_ID' AND YEAR='$ACT_YEAR_NO' AND ID=:dept_id");
    oci_bind_by_name($stmt, ':dept_id',  $data['union_priority'][$i]);
    oci_execute($stmt, OCI_DEFAULT);
    oci_fetch($stmt);
    $results_union['DEPT_NAME'][] = oci_result($stmt, 'NAME');
    oci_free_statement($stmt);
  }
} else
  $union_flag = 0;

$name = $data['name'];
$sex_array = array("1" => "男", "0" => "女");
$sex_name = $sex_array[$data['sex']];
$id = $data['id'];
$dept_id = $data['dept'];
$organize_id = $data['organize_id'];
$orastatus_id = $data['orastatus_id'];
$birthday_f = $data['birthday'];
$address_f = $data['zipcode'] . " " . $data['address'];
$address2_f = $data['zipcode2'] . " " . $data['address2'];
$tel_h = $data['tel_h_a'] . "-" . $data['tel_h'];
$tel_o = "";
if ($data['tel_o_a'] !== null)
  $tel_o = $data['tel_o_a'] . "-" . $data['tel_o'];
$tel_m = $data['tel_m'];
$email = $data['email'];
$liaisoner = $data['conn_name'];
$liaison_tel  = $data['conn_tel'];
$liaison_rel = $data['conn_rel'];

$prove_name = array("1" => "學士學位", "2" => "同等學力", "3" => "國家考試及格", "4" => "技能檢定合格");
$prove_type = $data['prove_type'];
$prove_content = "";
if ($data['prove_type'] === "1") {
  $prove_content = $data['grad_schol']  . " " . $data['grad_dept'] . ", 於 " . $data['grad_date'] . " 畢業";
} else if ($data['prove_type'] === "2") {
  $school_type = array("1" => "大學", "2" => "三專", "3" => "二專或五專");
  $graduate = array("1" => "畢業", "2" => "肄業");
  $prove_content = $data['ac_school'] . " (" . $school_type[$data['ac_school_type']] . "), " . $data['ac_dept'] . ", " . $data['ac_date'] . " " . $graduate[$data['ac_g']] . ", 修業 " . $data['ac_m_y'] . "年, 已離校 " . $data['ac_leave_y'] . " 年";
}

$stmt = oci_parse($conn, "SELECT NAME,LOCATION,UPLOAD_TYPE FROM DEPARTMENT WHERE  SCHOOL_ID='$SCHOOL_ID' AND YEAR='$ACT_YEAR_NO' AND ID=:dept_id");
oci_bind_by_name($stmt, ':dept_id',  $dept_id);
oci_execute($stmt, OCI_DEFAULT);
if (!oci_fetch($stmt))
  throw new Exception("No Data");
$dept_name = oci_result($stmt, 'NAME');
$location = oci_result($stmt, 'LOCATION');
$upload_type = oci_result($stmt, 'UPLOAD_TYPE');
oci_free_statement($stmt);


$stmt = oci_parse($conn, "SELECT NAME FROM ORGANIZE WHERE SCHOOL_ID='$SCHOOL_ID' AND YEAR='$ACT_YEAR_NO' AND ID=:organize_id");
oci_bind_by_name($stmt, ':organize_id',  $organize_id);
oci_execute($stmt, OCI_DEFAULT);
if (!oci_fetch($stmt))
  throw new Exception("No Data");
$organize = oci_result($stmt, 'NAME');
oci_free_statement($stmt);
$subj_id = substr($data['subject'][0], 6, 1);
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
$status_name = oci_result($stmt, 'NAME');
oci_free_statement($stmt);

$location = dirname(__FILE__) . "/../../../upload/";
$attachment_location = $location . $ACT_YEAR_NO . "-" . $data['sn'] . ".pdf";
if (file_exists($attachment_location))
  $upload_flag = 1;
else
  $upload_flag = 0;


?>


<table width="95%" border="0" cellpadding="0" cellspacing="0" bordercolor="#009900">
  <tr>
    <td width="1" bgcolor="#009900"><img src="BASEURL/images/space.gif" width="1" height="1"></td>
    <td>
      <table width="100%" border="0" cellspacing="0" cellpadding="0">
        <tr onMouseOver="this.style.backgroundColor='#CCFFCC'" onMouseOut="this.style.backgroundColor=''">
          <td bgcolor="#009900"><img src="BASEURL/images/space.gif" width="1" height="1"></td>
        </tr>
        <tr>
          <td></td>
        </tr>
        <tr>
          <td>
            <table width="100%" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td bgcolor="#EEFFEE">
                  <table width="100%" border="0" cellspacing="0" cellpadding="2">
                    <tr>
                      <td width="19%">&nbsp;報考系所：</td>
                      <td width="34%"><U>
                          <FONT COLOR="#000000"><?= $dept_name ?></FONT>
                        </U> </td>
                      <td width="16%">報考組(科)別：</td>
                      <td width="31%"><U>
                          <FONT COLOR="#000000"><?= $organize ?></FONT>
                        </U>
                      </td>
                    </tr>
                    <?php
                    if ($nrows_union > 0) { ?>
                      <tr>
                        <td> <?= ($union_flag == 1) ? "志願順序：" : "" ?></td>
                        <td colspan="3" style="color:blue">
                          <?php
                          for ($i = 0; $i < $nrows_union; $i++) {
                            echo ($i + 1) . "." . $results_union['DEPT_NAME'][$i] . "<br/>";
                          }
                          ?>
                        </td>
                      </tr>
                    <?php } ?>
                    <tr>
                      <td> &nbsp;報考身分：</td>
                      <td colspan="3"><U>
                          <FONT COLOR="#000000"><?= $status_name ?></FONT>
                        </U></td>
                    </tr>
                    <?php if ($CRIPPLE) { ?>
                      <tr>
                        <td valign="top">&nbsp;身心障礙類型：</td>
                        <td colspan="3" nowrap><U>
                            <FONT COLOR="#000000"><?= $cripple_name[$cripple_type] ?> <?= ($comments == "") ? "" : "(" . $comments . ")" ?></FONT>
                          </U></td>
                      </tr>
                    <?php } ?>
                    <tr>
                      <td colspan="4">
                        <div style="border:2px dashed pink"><span style="color:#FF0000">報名考區：　　</span><span style="color:blue">
                            <?php
                            if ($e_place == 2) {
                              echo "台北考區";
                            } else {
                              echo "彰化考區";
                            }
                            ?>
                          </span></div>
                      </td>
                    </tr>
                    <tr>
                      <td colspan="4">
                        <table width="100%" border="0" cellpadding="0" cellspacing="0">
                          <tr>
                            <td>
                              <hr />
                            </td>
                          </tr>
                        </table>
                      </td>
                    </tr>
                    <tr>
                      <td>&nbsp;姓　　名：</td>
                      <td><U>
                          <FONT COLOR="#000000"><?= $u_name ?></FONT>
                        </U></td>
                      <td nowrap>性　　別：</td>
                      <td><U>
                          <FONT COLOR="#000000"><?= $sex_name ?></FONT>
                        </U></td>
                    </tr>
                    <tr>
                      <td>&nbsp;出生日期：</td>
                      <td><U>
                          <FONT COLOR="#000000"><?= $birthday_f ?></FONT>
                        </U></td>
                      <td>身分證字號：</td>
                      <td><U>
                          <FONT COLOR="#000000"><?= $id ?></FONT>
                        </U></td>
                    </tr>
                    <tr>
                      <td valign="top">&nbsp;通訊地址：</td>
                      <td colspan="3"><U>
                          <FONT COLOR="#000000"><?= $address_f ?></FONT>
                        </U></td>
                    </tr>
                    <tr>
                      <td valign="top">&nbsp;戶籍地址：</td>
                      <td colspan="3"><U>
                          <FONT COLOR="#000000"><?= $address2_f ?></FONT>
                        </U></td>
                    </tr>
                    <tr>
                      <td>&nbsp;聯絡電話：</td>
                      <td colspan="3">宅: <U>
                          <FONT COLOR="#000000"><?= $tel_h ?></FONT>
                        </U>, 公: <U>
                          <FONT COLOR="#000000"><?= $tel_o ?></FONT>
                        </U>, 手機: <U>
                          <FONT COLOR="#000000"><?= $tel_m ?></FONT>
                        </U></td>
                    </tr>
                    <tr>
                      <td nowrap>&nbsp;Email信箱：</td>
                      <td colspan="3"><U>
                          <FONT COLOR="#000000"><?= $email ?></FONT>
                        </U></td>
                    </tr>
                    <tr>
                      <td nowrap>&nbsp;緊急聯絡人：</td>
                      <td colspan="3">姓名: <U>
                          <FONT COLOR="#000000"><?= $liaisoner ?></FONT>
                        </U>, 電話: <U>
                          <FONT COLOR="#000000"><?= $liaison_tel ?></FONT>
                        </U>, 關係: <U>
                          <FONT COLOR="#000000"><?= $liaison_rel ?></FONT>
                        </U></td>
                    </tr>
                    <tr>
                      <td colspan="4">
                        <table width="100%" border="0" cellpadding="0" cellspacing="0">
                          <tr>
                            <td>
                              <hr />
                            </td>
                          </tr>
                        </table>
                      </td>
                    </tr>
                    <tr>
                      <td nowrap>&nbsp;應考學歷：</td>
                      <td colspan="3"><U>
                          <FONT COLOR="#000000"><?= $prove_name[$prove_type] ?></FONT>
                        </U></td>
                    </tr>
                    <?php if ($prove_type <= 2) { ?>
                      <tr>
                        <td nowrap>&nbsp;</td>
                        <td colspan="3"><U>
                            <FONT COLOR="#000000"><?= $prove_content ?></FONT>
                          </U></td>
                      </tr>
                    <?php } ?>
                    <tr>
                      <td valign="top" nowrap>
                      </td>
                      <td colspan="3" valign="top" nowrap></td>
                    </tr>

                    <?php if ($upload_type > 1 && !($dept_id == "334" && $subj_id < 8)) //upload_type 審查資料繳交方式:  1:郵寄  2:上傳  3:郵寄+上傳
                    { ?>
                      <tr>
                        <td nowrap>備審資料上傳:</td>
                        <td colspan="3">
                          <?php
                          if ($upload_flag == 1) {
                            echo "<span style='font-size:15px;color:blue'>備審資料檔案已上傳</span>";
                          } else {
                            echo "<span style='font-size:15px;color:red'>備審資料檔案尚未上傳</span>";
                          }

                          ?>
                        </td>
                      </tr>
                    <?php } ?>
                    <tr>
                      <td nowrap>&nbsp;繳驗證件：</td>
                      <td colspan="3">
                        <ol>
                          <li>
                            <FONT COLOR="#0000FF">以同等學力報考者，須將同等學力證件影本於報名截止前寄送達本校招生委員會審查。未繳驗證件者，如獲錄取，於報到時學力證件審查不合格，須撤銷錄取資格，不得異議。
                          </li>
                          <!--<li><FONT COLOR="#0000FF">報考特殊教育學系公費生及特殊教育學系資賦優異教育碩士班公費生者，須將學歷證件及合格教師證影本於報名截止日前寄(送)達至本校特殊教育學系審查，審查未通過者不受理報名，如未繳驗證件，則視同報考資格不符。</font></li>-->
                          <li>招生系所如須郵寄或上傳備審資料，請依招生簡章【重點項目一覽表】之「資料審查繳交方式」辦理。</li>
                        </ol>
                        </font>
                      </td>

                  </table>
                </td>
              </tr>
            </table>
          </td>
        </tr>
        <tr>
          <td width="1" bgcolor="#009900"><img src="BASEURL/images/space.gif" width="1" height="1"></td>
        </tr>
      </table>
    </td>
    <td width="1" bgcolor="#009900"><img src="BASEURL/images/space.gif" width="1" height="1"></td>
  </tr>
</table>