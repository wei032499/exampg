<?php
header('Content-Type:application/json');
$result = array();

try {
    require_once('../common/functions.php');
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        $result[] = array('date' => "12月18日", "content" => "<span class='font-weight-bold'>本系統僅供報考『$ACT_YEAR_NO 學年度碩士班』考試，欲報考其他招生考試者<span style='color:red'>(如在職進修專班碩士學位班等)</span>請勿使用，<span style='color:red'>若已誤繳報名費，請勿填寫報名表並依簡章規定申請退費。</span></span>");
        $result[] = array('date' => "12月18日", "content" => "<span class='font-weight-bold' style='color:red'>部分電子郵件信箱(如:Gmail、Hotmail..等)可能會將系統寄發之郵件攔截為垃圾郵件，繳費後如未收到序號密碼通知信，請先檢查是否在垃圾郵件匣。</span>");
        $result[] = array('date' => "12月18日", "content" => "本招生考試電子檔簡章免費下載，歡迎多加使用；<u>一律採網路報名，請先上網取得報名費繳費帳號並完成繳費後再行上網填寫報名表；低收入戶/中低收入戶若欲申請免繳報名費須提前作業，請詳閱簡章相關規定。</u>");
        $result[] = array('date' => "12月18日", "content" => "<span class='font-weight-bold'>本校今年部分系所採聯合招生方案，請考生於報名前詳閱簡章相關規定。如有任何問題，請洽詢各承辦人員。</span>");
        $result[] = array('date' => "12月18日", "content" => "<span class='font-weight-bold'>本校畢業校友（含應屆畢業生）報考本次招生考試，報名費用一律以八折計算。<br>曾報考本校110學年度碩士班推薦甄試生報考本次招生考試者，免繳報名費。</span>");
        $result[] = array('date' => "12月18日", "content" => "以同等學力報考者，須將同等學力證件影本於<span style='color:red'>報名截止前</span>寄送達本校招生委員會審查。未繳驗證件者，如獲錄取，於報到時學力證件審查不合格，須撤銷錄取資格，不得異議。");
        $result[] = array('date' => "12月18日", "content" => "電子簡章開放下載日期：【 <span class='font-weight-bold' style='color:red'>" . $PSELL_DL_START_DATE . "</span> 】 至 【 <span class='font-weight-bold' style='color:red'>" . $PSELL_DL_END_DATE . "</span> 】 止。 ");
        $result[] = array('date' => "12月18日", "content" => "報名費繳費帳號取得日期：【 <span class='font-weight-bold' style='color:red'>" . $ACC_START_DATE . "</span> 】 至 【 <span class='font-weight-bold' style='color:red'>" . $ACC_END_DATE . "</span> 】 止。<br>
        (若您遺失繳費帳號，請重新<a href='./order.php'>取得繳費帳號</a>，並確實記下您的繳費帳號進行繳費。)<br>
        曾報考本校110學年度碩士班推薦甄試生仍須上網取得報名費繳費帳號，取得完成後將由系統直接寄發序號及密碼至考生電子信箱。");
        $result[] = array('date' => "12月18日", "content" => "報名費繳費日期：【 <span class='font-weight-bold' style='color:red'>" . $ACC2_START_DATE . "</span> 】 至 【 <span class='font-weight-bold' style='color:red'>" . $ACC2_END_DATE . "</span> 】 止。<br>
        (若您的報名費用已繳款且銷帳成功，卻一直未收到電子郵件通知，請檢查信件是否在垃圾信件匣中，或利用系統自動重寄功能(資料查詢--><a href='./query_pwd.php'>查詢序號密碼</a>)重新寄送。)");
        $result[] = array('date' => "12月18日", "content" => "網路填寫報名表日期：【 <span class='font-weight-bold' style='color:red'>" . $SU_START_DATE . "</span> 】 至 【 <span class='font-weight-bold' style='color:red'>" . $SU_END_DATE . "</span> 】 止。");
        $result[] = array('date' => "12月18日", "content" => "審查資料繳交日期：<br>
        上傳：【109-12-20 09:00:00</span> 】 至 【110-07-09 17:00:00】 止。<br>
        郵寄或親自繳交：【108-12-20</span> 】 至 【109-01-09】 止(郵戳為憑)。");
        $result[] = array('date' => "12月18日", "content" => "准考證自行下載日期：【 <span class='font-weight-bold' style='color:red'>" . $CARD_START_DATE . "</span> 】 至 【 <span class='font-weight-bold' style='color:red'>" . $CARD_END_DATE . "</span> 】 止。");
        $result[] = array('date' => "12月18日", "content" => "成績下載開放日期：【 <span class='font-weight-bold' style='color:red'>" . $SCORE_START_DATE . "</span> 】 至 【 <span class='font-weight-bold' style='color:red'>" . $SCORE_END_DATE . "</span> 】 止。<br>
        輔諮系、輔諮系婚家班面試成績查詢：【 <span class='font-weight-bold' style='color:red'>" . $SCORE2_START_DATE . "</span> 】 至 【 <span class='font-weight-bold' style='color:red'>" . $SCORE2_END_DATE . "</span> 】 止。");
        $result[] = array('date' => "12月18日", "content" => "正(備)取生申明就讀(遞補)意願開放日期：【 <span class='font-weight-bold' style='color:red'>" . $UBI_START_DATE . "</span> 】 至 【 <span class='font-weight-bold' style='color:red'>" . $UBI_END_DATE . "</span> 】 止(招生系所另有規定，從其規定，請詳見簡章)。<br>
        輔諮系、輔諮系婚家班正(備)取生申請就讀(遞補)意願開放日期：【 <span class='font-weight-bold' style='color:red'>" . $UBI2_START_DATE . "</span> 】 至 【 <span class='font-weight-bold' style='color:red'>" . $UBI2_END_DATE . "</span> 】 止。");
        $result[] = array('date' => "12月18日", "content" => "正備取生報到狀況查詢開放日期：【 <span class='font-weight-bold' style='color:red'>" . $FSTAT_START_DATE . "</span> 】至【 <span class='font-weight-bold' style='color:red'>" . $FSTAT_END_DATE . "</span> 】 止 。 ");
        $result[] = array('date' => "12月18日", "content" => "電子簡章為PDF檔案，請先<a href='http://www.tw.adobe.com/products/acrobat/readstep2.html' target='_BLANK'>下載安裝Adobe Reader</a>");
    } else
        throw new Exception("Method Not Allowed", 405);
} catch (Exception $e) {
    setHeader($e->getCode());
    $result = array();
    $result['code'] = $e->getCode();
    $result['message'] = $e->getMessage();
}

echo json_encode($result);
