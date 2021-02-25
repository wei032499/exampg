<!doctype html>
<html lang="zh-Hant-TW">

<head>
    <meta charset="utf-8">
    <title>網路報名</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/x-icon" href="./images/favicon.ico">

    <link rel="stylesheet" href="./css/bootstrap.min.css" />
    <link rel="stylesheet" href="./css/custom.css" />
    <script src="./js/jquery.min.js"></script>
    <script src="./js/bootstrap.bundle.min.js"></script>
    <script src="./js/common.js"></script>
    <script>
        if (sessionStorage === undefined) {
            alert("未支援Web Storage！\n請更換瀏覽器再試。");
            window.location.replace('./');
        } else if (!sessionStorage.hasOwnProperty('email') || !sessionStorage.hasOwnProperty('card_start_date') || !sessionStorage.hasOwnProperty('card_end_date'))
            window.location.replace('./signup.php?step=3');
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
                <div class="row ">
                    <h3 class="col-lg" style="letter-spacing: 0.2rem;min-width:14rem">
                        :::填寫報名表 <span style="color:red">(初填完成)</span>
                    </h3>
                    <div id="loginInfo" class="col-sm row justify-content-end mx-0 align-items-center">
                        <div>Hi~ <span id="username">test</span> </div>
                        <button type="button" id="logout" style="min-width:4rem" class="btn btn-info btn-sm ml-3">登出</button>
                    </div>
                </div>
            </div>
            <div class="border p-4 bg-white shadow rounded">
                <p style="text-align: center;" class="fs-4 line-height-1">
                    報名資料初填完成<br>
                    (<span style="color:red" id="email"></span>)<br>
                </p>
                <hr />
                <div class="line-height-1">
                    <ol style="list-style-type:upper-roman;">
                        <li>◆身心障礙考生如欲申請各項應考需求，請另寄交申請表及證明文件至本校招生委員會。</li>
                        <li>◆報名資料如有罕用字，致電腦無法正常顯示者，請另填<a href='http://project.ncue.edu.tw/exampg_m/code_reply.doc' target=_blank>罕用字回覆表</a>傳真本校處理。</li>
                        <li>若您未收到初填通知電子郵件，可至<b>網路報名系統 / 資料查詢 / 報名資料查詢</b>，查詢個人報名資料，俾確認報名資料已登錄完成。</li>
                        <li>准考證由考生自行下載列印，請於開放列印日期：<span id="card_start_date"></span> 起至 <span id="card_end_date"></span> 止，至<b>網路報名系統 / 網路報名 / 准考證列印</b> 下載(不限次數)後，以A4白紙單面紙張自行列印，並妥為保存，本校不再另行寄發。</li>
                        <li>以同等學力報考者，須將同等學力證件影本於報名截止前寄送達本校招生委員會審查。未繳驗證件者，如獲錄取，於報到時學力證件審查不合格，須撤銷錄取資格，不得異議。</li>
                        <li>招生系所如須備審資料，其繳交方式：
                            <ul>
                                <li>網路上傳：請依限完成上傳作業，欲於報名截止前再修正或重新上傳者，請至網路報名→修改報名資料進行上傳作業。</li>
                                <li>郵寄或親自繳交：請填妥後依簡章規定將審查資料依限寄送至招生系所(詳招生簡章各系所「資料審查繳交方式」規定)。</li>
                            </ul>
                        </li>
                        <li>【請下載以下兩項表格】<br>
                            <a target="_blank" href="./download_review.php">下載審查資料一覽表(PDF檔)</a><br>
                            <a target="_blank" href="./download_evelope.php">下載信封封面(PDF檔)</a><br>
                            (您亦可利用資料查詢功能裡的「<span class='font-weight-bold'>報名資料查詢、下載審查資料一覽表及信封封面</span>」下載表格)
                        </li>
                    </ol>
                </div>

            </div>

        </div>
    </section>

    <?php require_once("./module/footer.php") ?>

    <script>
        $(function() {
            $("#email").text(sessionStorage.getItem('email'));
            $("#card_start_date").text(sessionStorage.getItem('card_start_date'));
            $("#card_end_date").text(sessionStorage.getItem('card_end_date'));

        });
    </script>



</body>

</html>