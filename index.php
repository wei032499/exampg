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
        } else sessionStorage.clear();

        $.ajax({
                type: 'GET',
                url: "./API/news/news.php",
                dataType: 'json',
                cache: false
            }).done(function(response) {
                $(function() {
                    for (var i = 0; i < response['data'].length; i++)
                        $("table tbody").append("<tr><td>" + response['data'][i]['content'] + "</td><td>" + response['data'][i]['date'] + "</td></tr>")
                });
            })
            .fail(function(jqXHR, exception) {
                var response = jqXHR.responseJSON;
                var msg = '';
                if (response === undefined)
                    msg = exception + "\n" + "./API/news/news.php" + "\n" + jqXHR.responseText;
                else if (response.hasOwnProperty('message')) {
                    msg = response.message;
                } else {
                    msg = 'Uncaught Error.\n' + jqXHR.responseText;
                }
                alert(msg);
            });
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
                    <h3 class="col" style="letter-spacing: 0.2rem;">
                        :::系統公告
                    </h3>
                    <div id="loginInfo" class="col-sm row justify-content-end mx-0 align-items-center" style="display: none !important;">
                        <div>Hi~ <span id="username"></span> </div>
                        <button type="button" id="logout" style="min-width:4rem" class="btn btn-info btn-sm ml-3">登出</button>
                    </div>
                </div>
                <table class="shadow table-md table-hover table-bordered" style="width:100%">
                    <thead>
                        <tr class="table-primary">
                            <th scope="col" style="width: 90%;">公告事項</th>
                            <th scope="col" style="width: 10%;">公告日期</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </section>

    <?php require_once("./module/footer.php") ?>




</body>

</html>