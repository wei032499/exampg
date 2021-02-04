<!doctype html>
<html lang="zh-Hant-TW">

<head>
    <meta charset="utf-8">
    <title>網路報名</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/x-icon" href="http://www.csie.ncue.edu.tw/csie/resources/images/ncue-logo.png">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css" integrity="sha384-B0vP5xmATw1+K9KRQjQERJvTumQW0nPEzvF6L/Z6nronJ3oUOFUFpCjEUQouq2+l" crossorigin="anonymous">
    <link rel="stylesheet" href="./css/custom.css" />
    <link rel="stylesheet" href="./css/toastr.min.css" />
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-Piv4xVNRyMGpqkS2by6br4gNJ7DXjqk09RmUpJ8jgGtD7zP9yug3goQfGII0yAns" crossorigin="anonymous"></script>
    <script src="./js/toastr.min.js"></script>
    <script src="./js/custom.js"></script>
    <script>
        if (sessionStorage === undefined) {
            alert("未支援Web Storage！\n請更換瀏覽器再試。");
            window.location.replace('./');
        } else if (!sessionStorage.hasOwnProperty('order') || sessionStorage.getItem('order') === null)
            window.location.replace('./order.php');
        else fillByStorage('order');
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
                    您的報名繳費帳號為 <span style="color:red" id="account_no"></span><br>
                    您應繳之金額為 新台幣 元整<br>
                    玉山銀行行庫代碼：808<br>
                </p>
                <hr />
                <div class="line-height-1">
                    注意事項：<br>
                    <ol style="list-style-type:upper-roman;">
                        <li>一組 「序號」及「密碼」，僅能選擇一個樂所班(組)別報名。</li>
                        <li>低收入戶/中低收入戶考生請勿先行繳費，並於110年1月4日前(以郵戳為憑)將相關資料寄(送)達本校招生委員會審查，通過後始可免繳報名費，其相關規定請詳見簡章。</li>
                        <li>除低收入戶/中低收入戶考生及曾報考本校110學年度碩士班推薦甄試生外，其餘考生請持上列繳費帳號至各金融機構繳費(入帳行:玉山銀行彰化分行;收款人:「國立彰化師範大學招生專戶」)或使用ATM轉帳繳費(低收入户中低收入戶考生除外)繳費截止當日，請勿以臨櫃跨行匯款方式繳費，以免因各行庫人工入帳作業延誤，影響報名。</li>
                        <li></li>
                        <li></li>
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
            $('#account_no').text(sessionItems['account_no']);
            $('#email').text(sessionItems['email']);
            sessionStorage.removeItem('order');
        });
        $("#print_btn").on('click', function() {
            let print_area = $("<div></div>").html("<h1 style='text-align:center;margin-bottom:1.5rem'>國立彰化師範大學 網路報名系統</h1>" + $("#content").prop("outerHTML"));
            $(print_area).print({
                globalStyles: true,
                title: "繳費帳號",
            });
        });
    </script>

</body>

</html>