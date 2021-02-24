<!doctype html>
<html lang="zh-Hant-TW">

<head>
    <meta charset="utf-8">
    <title>網路報名</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/x-icon" href="./images/favicon.ico">
    <!--http://www.csie.ncue.edu.tw/csie/resources/images/ncue-logo.png-->
    <!-- bootstrap 5.0.x
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-giJF6kkoqNQ00vy+HMDP7azOuL0xtbfIcaT9wjKHr8RbDVddVHyTfAAsrekwKmP1" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/js/bootstrap.bundle.min.js" integrity="sha384-ygbV9kiqUc6oa4msXn9868pTtWMgiQaeYH7/t7LECLbyPA2x65Kgf80OJFdroafW" crossorigin="anonymous"></script>
    -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css" integrity="sha384-B0vP5xmATw1+K9KRQjQERJvTumQW0nPEzvF6L/Z6nronJ3oUOFUFpCjEUQouq2+l" crossorigin="anonymous">
    <link rel="stylesheet" href="./css/custom.css" />
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-Piv4xVNRyMGpqkS2by6br4gNJ7DXjqk09RmUpJ8jgGtD7zP9yug3goQfGII0yAns" crossorigin="anonymous"></script>
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
                    for (let i = 0; i < response['data'].length; i++)
                        $("table tbody").append("<tr><td>" + response['data'][i]['content'] + "</td><td>" + response['data'][i]['date'] + "</td></tr>")
                });
            })
            .fail(function(jqXHR, exception) {
                let response = jqXHR.responseJSON;
                let msg = '';
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