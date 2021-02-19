<?php
header('Content-Type:application/json');
$result = array('data' => array());
$post_processing = array();
try {
    require_once('../common/db.php');
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        if (isset($_GET['id'])) {
            $sql = "SELECT ID,POSITION,CONTENT,to_char(POST_DATE,'yyyy-mm-dd') AS POST_DATE FROM NEWS WHERE SCHOOL_ID='$SCHOOL_ID' AND ID=:id ORDER BY POSITION ASC, POST_DATE DESC";
            $stmt = oci_parse($conn, $sql);
            oci_bind_by_name($stmt, ':id',  $_GET['id']);
            oci_execute($stmt, OCI_DEFAULT);
            $nrows = oci_fetch_all($stmt, $results); //$nrows -->總筆數
            oci_free_statement($stmt);
            for ($i = 0; $i < $nrows; $i++) {
                $result['data'][] = array('id' => $results['ID'][$i], 'date' => $results['POST_DATE'][$i], "content" => $results['CONTENT'][$i], "position" => $results['POSITION'][$i]);
            }
        } else {
            $sql = "SELECT ID,POSITION,CONTENT,to_char(POST_DATE,'yyyy-mm-dd') AS POST_DATE FROM NEWS WHERE SCHOOL_ID='$SCHOOL_ID' ORDER BY POSITION ASC, POST_DATE DESC";
            $stmt = oci_parse($conn, $sql);
            oci_execute($stmt, OCI_DEFAULT);
            $nrows = oci_fetch_all($stmt, $results); //$nrows -->總筆數
            oci_free_statement($stmt);
            for ($i = 0; $i < $nrows; $i++) {
                $result['data'][] = array('id' => $results['ID'][$i], 'date' => $results['POST_DATE'][$i], "content" => $results['CONTENT'][$i]);
            }
        }
        // $result[] = array('date' => "12月18日", "content" => "<span class='font-weight-bold'>本系統僅供報考『$ACT_YEAR_NO 學年度碩士班』考試，欲報考其他招生考試者<span style='color:red'>(如在職進修專班碩士學位班等)</span>請勿使用，<span style='color:red'>若已誤繳報名費，請勿填寫報名表並依簡章規定申請退費。</span></span>");
        // $result[] = array('date' => "12月18日", "content" => "<span class='font-weight-bold' style='color:red'>部分電子郵件信箱(如:Gmail、Hotmail..等)可能會將系統寄發之郵件攔截為垃圾郵件，繳費後如未收到序號密碼通知信，請先檢查是否在垃圾郵件匣。</span>");
        // $result[] = array('date' => "12月18日", "content" => "本招生考試電子檔簡章免費下載，歡迎多加使用；<u>一律採網路報名，請先上網取得報名費繳費帳號並完成繳費後再行上網填寫報名表；低收入戶/中低收入戶若欲申請免繳報名費須提前作業，請詳閱簡章相關規定。</u>");
        // $result[] = array('date' => "12月18日", "content" => "<span class='font-weight-bold'>本校今年部分系所採聯合招生方案，請考生於報名前詳閱簡章相關規定。如有任何問題，請洽詢各承辦人員。</span>");
        // $result[] = array('date' => "12月18日", "content" => "<span class='font-weight-bold'>本校畢業校友（含應屆畢業生）報考本次招生考試，報名費用一律以八折計算。<br>曾報考本校110學年度碩士班推薦甄試生報考本次招生考試者，免繳報名費。</span>");
        // $result[] = array('date' => "12月18日", "content" => "以同等學力報考者，須將同等學力證件影本於<span style='color:red'>報名截止前</span>寄送達本校招生委員會審查。未繳驗證件者，如獲錄取，於報到時學力證件審查不合格，須撤銷錄取資格，不得異議。");
        // $result[] = array('date' => "12月18日", "content" => "電子簡章開放下載日期：【 <span class='font-weight-bold' style='color:red'>" . $PSELL_DL_START_DATE . "</span> 】 至 【 <span class='font-weight-bold' style='color:red'>" . $PSELL_DL_END_DATE . "</span> 】 止。 ");
        // $result[] = array('date' => "12月18日", "content" => "報名費繳費帳號取得日期：【 <span class='font-weight-bold' style='color:red'>" . $ACC_START_DATE . "</span> 】 至 【 <span class='font-weight-bold' style='color:red'>" . $ACC_END_DATE . "</span> 】 止。<br>
        // (若您遺失繳費帳號，請重新<a href='./order.php'>取得繳費帳號</a>，並確實記下您的繳費帳號進行繳費。)<br>
        // 曾報考本校110學年度碩士班推薦甄試生仍須上網取得報名費繳費帳號，取得完成後將由系統直接寄發序號及密碼至考生電子信箱。");
        // $result[] = array('date' => "12月18日", "content" => "報名費繳費日期：【 <span class='font-weight-bold' style='color:red'>" . $ACC2_START_DATE . "</span> 】 至 【 <span class='font-weight-bold' style='color:red'>" . $ACC2_END_DATE . "</span> 】 止。<br>
        // (若您的報名費用已繳款且銷帳成功，卻一直未收到電子郵件通知，請檢查信件是否在垃圾信件匣中，或利用系統自動重寄功能(資料查詢--><a href='./query_pwd.php'>查詢序號密碼</a>)重新寄送。)");
        // $result[] = array('date' => "12月18日", "content" => "網路填寫報名表日期：【 <span class='font-weight-bold' style='color:red'>" . $SU_START_DATE . "</span> 】 至 【 <span class='font-weight-bold' style='color:red'>" . $SU_END_DATE . "</span> 】 止。");
        // $result[] = array('date' => "12月18日", "content" => "審查資料繳交日期：<br>
        // 上傳：【 <span class='font-weight-bold' style='color:red'>$SU_START_DATE</span> 】 至 【 <span class='font-weight-bold' style='color:red'>$SU_END_DATE</span> 】 止。<br>
        // 郵寄或親自繳交：【 <span class='font-weight-bold' style='color:red'>$ACC3_START_DATE</span> 】 至 【 <span class='font-weight-bold' style='color:red'>$ACC3_END_DATE</span> 】 止(郵戳為憑)。");
        // $result[] = array('date' => "12月18日", "content" => "准考證自行下載日期：【 <span class='font-weight-bold' style='color:red'>" . $CARD_START_DATE . "</span> 】 至 【 <span class='font-weight-bold' style='color:red'>" . $CARD_END_DATE . "</span> 】 止。");
        // $result[] = array('date' => "12月18日", "content" => "成績下載開放日期：【 <span class='font-weight-bold' style='color:red'>" . $SCORE_START_DATE . "</span> 】 至 【 <span class='font-weight-bold' style='color:red'>" . $SCORE_END_DATE . "</span> 】 止。<br>
        // 輔諮系、輔諮系婚家班面試成績查詢：【 <span class='font-weight-bold' style='color:red'>" . $SCORE2_START_DATE . "</span> 】 至 【 <span class='font-weight-bold' style='color:red'>" . $SCORE2_END_DATE . "</span> 】 止。");
        // $result[] = array('date' => "12月18日", "content" => "正(備)取生申明就讀(遞補)意願開放日期：【 <span class='font-weight-bold' style='color:red'>" . $UBI_START_DATE . "</span> 】 至 【 <span class='font-weight-bold' style='color:red'>" . $UBI_END_DATE . "</span> 】 止(招生系所另有規定，從其規定，請詳見簡章)。<br>
        // 輔諮系、輔諮系婚家班正(備)取生申請就讀(遞補)意願開放日期：【 <span class='font-weight-bold' style='color:red'>" . $UBI2_START_DATE . "</span> 】 至 【 <span class='font-weight-bold' style='color:red'>" . $UBI2_END_DATE . "</span> 】 止。");
        // $result[] = array('date' => "12月18日", "content" => "正備取生報到狀況查詢開放日期：【 <span class='font-weight-bold' style='color:red'>" . $FSTAT_START_DATE . "</span> 】至【 <span class='font-weight-bold' style='color:red'>" . $FSTAT_END_DATE . "</span> 】 止 。 ");
        // $result[] = array('date' => "12月18日", "content" => "電子簡章為PDF檔案，請先<a href='http://www.tw.adobe.com/products/acrobat/readstep2.html' target='_BLANK'>下載安裝Adobe Reader</a>");
    } else if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        session_start();
        if (!isset($_SESSION['username']))
            throw new Exception("Unauthorized", 401);

        $timestamp = time();
        $time = date("Y-m-d H:i:s");
        $sql = "INSERT INTO NEWS (ID,POSITION,CONTENT,POST_DATE,SCHOOL_ID) VALUES ('$timestamp',:position,:content,to_date('$time','yyyy-mm-dd HH24:MI:SS'),'$SCHOOL_ID')";
        $stmt = oci_parse($conn, $sql);
        $params = array($_POST['position'], $_POST['content']);
        bind_by_array($stmt, $sql, $params);
        oci_execute($stmt, OCI_DEFAULT);
    } else if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
        session_start();
        if (!isset($_SESSION['username']))
            throw new Exception("Unauthorized", 401);

        parse_str(file_get_contents("php://input"), $post_vars);
        $time = date("Y-m-d H:i:s");
        $sql = "UPDATE NEWS SET POSITION=:position,CONTENT=:content,POST_DATE=to_date('$time','yyyy-mm-dd HH24:MI:SS') WHERE ID=:id AND SCHOOL_ID=$SCHOOL_ID";
        $stmt = oci_parse($conn, $sql);
        $params = array($post_vars['position'], $post_vars['content'], $post_vars['id']);
        bind_by_array($stmt, $sql, $params);
        oci_execute($stmt, OCI_DEFAULT);
    } else if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
        session_start();
        if (!isset($_SESSION['username']))
            throw new Exception("Unauthorized", 401);

        parse_str(file_get_contents("php://input"), $post_vars);
        $sql = "DELETE FROM NEWS WHERE ID=:id AND SCHOOL_ID=$SCHOOL_ID";
        $stmt = oci_parse($conn, $sql);
        oci_bind_by_name($stmt, ':id',  $post_vars['id']);
        oci_execute($stmt, OCI_DEFAULT);
    } else
        throw new Exception("Method Not Allowed", 405);

    oci_commit($conn); //無發生任何錯誤，將資料寫進資料庫
} catch (Exception $e) {
    oci_rollback($conn);
    setHeader($e->getCode());
    $result = array();
    $result['code'] = $e->getCode();
    $result['message'] = $e->getMessage();
    $result['line'] = $e->getLine();
}

register_shutdown_function("shutdown_function", $post_processing);

echo json_encode($result);
exit(); // You need to call this to send the response immediately
