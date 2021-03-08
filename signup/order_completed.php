<!doctype html>
<html lang="zh-Hant-TW">

<head>
    <meta charset="utf-8">
    <title>網路報名</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/x-icon" href="./images/favicon.ico">

    <link rel="stylesheet" href="./css/bootstrap.min.css" />
    <link rel="stylesheet" href="./css/custom.css" />
    <link rel="stylesheet" href="./css/toastr.min.css" />
    <script src="./js/jquery.min.js"></script>
    <script src="./js/bootstrap.bundle.min.js"></script>
    <script src="./js/toastr.min.js"></script>
    <script src="./js/jquery.print.min.js"></script>
    <script src="./js/common.js"></script>
    <script>
        if (sessionStorage === undefined) {
            alert("未支援Web Storage！\n請更換瀏覽器再試。");
            window.location.replace('./');
        } else if (!sessionStorage.hasOwnProperty('account_no') || !sessionStorage.hasOwnProperty('pay_money') || !sessionStorage.hasOwnProperty('email') || !sessionStorage.hasOwnProperty('low_income_end_date') || !sessionStorage.hasOwnProperty('acc2_end_date'))
            window.location.replace('./order.php');
    </script>
</head>

<body>
    <?php require_once("./module/header.php") ?>

    <section class="py-4 bg-light">
        <div class="container">
            <div class="my-3">
                <div class="row container">
                    <div style='width: 8px;height: 8px;display: block;background: #c84c37;'></div>
                    <div style='width: 8px;height: 8px;display: block;background: #3a7eb8;'></div>
                </div>
                <h3 style="letter-spacing: 0.2rem;">
                    :::取得繳費帳號 <span style="color:red">(資料登錄完成)</span>
                </h3>
            </div>
            <div id="content" class="p-4 bg-white shadow rounded">
                <p style="text-align: center;" class="fs-4 line-height-1">
                    您的<span style="color:red">報名費</span>繳費帳號為 <u class="font-weight-bold" id="account_no"></u><br>
                    您應繳之金額為 新台幣 <u class="font-weight-bold" id="pay_money"></u> 元整<br>
                    玉山銀行行庫代碼：<u class="font-weight-bold">808</u><br>
                </p>
                <hr />
                <div class="line-height-1">
                    注意事項：<br>
                    <ol style="list-style-type:upper-roman;">
                        <li>一組「序號」及「密碼」，僅能選擇一個系所班（組）別報名。</li>
                        <li>低收入戶/中低收入戶考生<span style="color:red">請勿先行繳費</span>，並於<span id="low_income_end_date"></span>前(以郵戳為憑)將相關資料寄(送)達本校招生委員會審查，通過後始可免繳報名費，其相關規定請詳見簡章。</li>
                        <li>除低收入戶／中低收入戶考生及曾報考本校110學年度碩士班推薦甄試生外，其餘考生請持上列繳費帳號至各金融機構繳費(<span style="color:red">入帳行：玉山銀行彰化分行；收款人：「國立彰化師範大學招生專戶」</span>)，或使用ＡＴＭ轉帳繳費。繳費截止當日，請勿以臨櫃跨行匯款方式繳費，以免因各行庫人工入帳作業延誤，影響報名。</li>
                        <li>繳費截止日期為：<span id="acc2_end_date"></span>，請儘早繳費，以免延誤報名。本校教務處招生及教學資源組於上班期間上午 8:00 ~ 下午5:00 提供諮詢服務。</li>
                        <li> 系統會在確認您繳費後，自動將序號、密碼傳送至您所登錄之E-mail信箱(<span style="color:red" id="email"></span>)；當您收到序號密碼通知後，即可登入「<a href="./signup.php">填寫報名表</a>」，填寫報名資料。</li>
                        <li>日後若要查詢您繳費是否入帳時，請直接輸入您的繳費帳號即可。</li>
                        <li>有關繳費及銷帳查詢方式，請參考「流程說明」功能裡的「<a href="./intro_payment.php">報名費繳費方式、銷帳查詢方式說明</a>」。</li>
                    </ol>

                </div>

            </div>
            <div class="row justify-content-center my-2">
                <button id="print_btn" class="btn btn-primary">列印</button>
            </div>
        </div>
    </section>

    <?php require_once("./module/footer.php") ?>

    <script>
        $(function() {
            $('#account_no').text(sessionStorage.getItem('account_no'));
            $('#pay_money').text(sessionStorage.getItem('pay_money'));
            $('#email').text(sessionStorage.getItem('email'));
            $('#low_income_end_date').text(sessionStorage.getItem('low_income_end_date'));
            $('#acc2_end_date').text(sessionStorage.getItem('acc2_end_date'));
        });
        $("#print_btn").on('click', function() {
            var print_area = $("<div></div>").html("<h1 style='text-align:center;margin-bottom:1.5rem'>國立彰化師範大學 網路報名系統</h1>" + $("#content").prop("outerHTML"));
            $(print_area).print({
                globalStyles: true,
                title: "國立彰化師範大網路報名繳費帳號",
            });
        });
    </script>

</body>

</html>