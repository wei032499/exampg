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
                    :::罕用字說明
                </h3>
            </div>

            <div class="card shadow  px-5 py-4">
                <p class="line-height-1">
                    一、網路報名姓名或地址如有罕用字，致電腦無法顯示者，請填妥罕用字回覆表，列印後傳真至本校處理。<br>
                    二、請於報名截止日前回覆，逾期恕不受理。<br>
                    三、一律以傳真方式辦理，傳真電話：(04)7211154；請在傳真後，立即來電確認，電話：(04)7232105 轉分機 5632 ~ 5637。<br>
                </p>
            </div>

        </div>
    </section>


    <?php require_once("./module/footer.php") ?>


</body>

</html>