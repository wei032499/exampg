<!doctype html>
<html lang="zh-Hant-TW">

<head>
    <meta charset="utf-8">
    <title>網路報名</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/x-icon" href="./images/favicon.ico">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css" integrity="sha384-B0vP5xmATw1+K9KRQjQERJvTumQW0nPEzvF6L/Z6nronJ3oUOFUFpCjEUQouq2+l" crossorigin="anonymous">
    <link rel="stylesheet" href="./css/custom.css" />
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-Piv4xVNRyMGpqkS2by6br4gNJ7DXjqk09RmUpJ8jgGtD7zP9yug3goQfGII0yAns" crossorigin="anonymous"></script>
    <script src="./js/jquery.print.min.js"></script>
    <script>
        if (sessionStorage === undefined)
            alert("未支援Web Storage！\n請更換瀏覽器再試。");
        else
            sessionStorage.clear();
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
                    :::報名費繳費方式及銷帳查詢方式說明
                </h3>
            </div>
            <script>
                $(function() {
                    let urlParams = new URLSearchParams(window.location.search);
                    if (urlParams.get('tab') === null)
                        $('#myTab a[href="#download"]').tab('show');
                    else
                        $('#myTab a[href="#' + urlParams.get('tab') + '"]').tab('show');
                });
            </script>
            <div id="content" class="card shadow p-4">
                <p class="line-height-1">
                    一、繳費方式：<br>
                    　（一）自動提款機(ATM)轉帳繳費（請先確認金融卡是否具有轉帳功能再進行轉帳繳費，非玉山銀行金融卡須另扣手續費）<br>
                    <br>
                    　　金融卡插入ATM，選擇「跨行轉帳服務」→「非約定帳號」<br>
                    　　※若使用玉山銀行的金融卡在玉山銀行的提款機上，請選擇本行轉帳<br>
                    　　輸入玉山銀行行庫代碼「808」（本行轉帳無此項）<br>
                    　　輸入本校網路報名系統產生之「繳費帳號(14碼)」<br>
                    　　輸入「轉帳金額」<br>
                    　　完成繳費，列印「ATM交易明細表」<br>
                    <br>
                    　（二）網路ATM轉帳（請先確認金融卡是否具有網路轉帳功能再進行轉帳繳費，請依各網路銀行系統說明及提示操作）<br>
                    <br>
                    　　輸入玉山銀行行庫代碼「808」<br>
                    　　輸入本校網路報名系統產生之「繳費帳號(14碼)」<br>
                    　　輸入「轉帳金額」<br>
                    　　完成繳費，列印「網路ATM交易明細表」<br>
                    <br>
                    　　※繳費完成後，請檢查交易明細表，如『交易金額』及『手續費』欄沒有扣款紀錄，即表示轉帳未成功，請依繳費方式再次繳費。<br>
                    　（三）其他行庫（玉山銀行除外）匯款（須另收手續費）<br>
                    <br>
                    　　受款行：玉山銀行彰化分行<br>
                    　　戶名：國立彰化師範大學招生專戶<br>
                    　　帳號：本校網路報名系統產生之「繳費帳號（14碼）」<br>
                    <br>
                    　　※「繳費金額」及「繳費帳號」錯誤無法銷帳，臨櫃匯款於填寫時請確實查核，以免影響報名。<br>
                    二、銷帳查詢方式：<br>
                    　（一）系統自動E-mail通知：報名費繳費成功後一小時，系統自動mail通知銷帳成功。<br>
                    　（二）自行上網查詢：<br>
                    　　　　繳費後一小時，可上網查詢繳費入帳完成與否。<br>
                    　　　　(「網路報名系統-->「資料查詢」-->「報名費銷帳查詢」，執行報名費「銷帳查詢」功能。)<br>
                    　（三）臨櫃跨行匯款因係人工作業，入帳時間不定。繳費截止當日，請勿以臨櫃跨行匯款方式繳費，<br>
                    　　　　以免由於各行庫人工入帳作業延誤，致來不及銷帳而影響報名。<br>
                    三、繳費後請保留交易明細表，供日後有需要時備查。<br>
                    四、低收入戶/中低收入戶考生須先通過資格審查，始得免繳報名費。<br>
                </p>
            </div>

            <div class="row justify-content-center my-2">
                <button id="print_btn" class="btn btn-primary">列印</button>
            </div>

        </div>
    </section>

    <?php require_once("./module/footer.php") ?>

    <script>
        $("#print_btn").on('click', function() {
            let print_area = $("<div></div>").html("<h1 style='text-align:center;margin-bottom:1.5rem'>國立彰化師範大學 網路報名系統</h1>" + $("#content").prop("outerHTML"));
            $(print_area).print({
                globalStyles: true,
                title: "報名費繳費方式及銷帳查詢方式說明",
            });
        });
    </script>

</body>

</html>