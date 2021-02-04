<!doctype html>
<html lang="zh-Hant-TW">

<head>
    <meta charset="utf-8">
    <title>網路報名</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/x-icon" href="http://www.csie.ncue.edu.tw/csie/resources/images/ncue-logo.png">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css" integrity="sha384-B0vP5xmATw1+K9KRQjQERJvTumQW0nPEzvF6L/Z6nronJ3oUOFUFpCjEUQouq2+l" crossorigin="anonymous">
    <link rel="stylesheet" href="./css/custom.css" />
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-Piv4xVNRyMGpqkS2by6br4gNJ7DXjqk09RmUpJ8jgGtD7zP9yug3goQfGII0yAns" crossorigin="anonymous"></script>
    <script src="./js/custom.js"></script>
    <script>
        const username = getCookie('username');
        if (username !== null) {
            $(function() {
                $("#username").text(username);
                $("#loginInfo").css('display', '');
            });
        }
    </script>
    <script>
        if (sessionStorage === undefined) {
            alert("未支援Web Storage！\n請更換瀏覽器再試。");
            window.location.replace('./');
        } else if (!sessionStorage.hasOwnProperty('signup') || sessionStorage.getItem('signup') === null)
            window.location.replace('./signup.php?step=3');
        else if (sessionStorage.hasOwnProperty('signup') && sessionStorage.getItem('signup') !== null)
            fillByStorage('signup');
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
                    <h3 class="col-lg" style="letter-spacing: 0.2rem;">
                        :::填寫報名表 <span style="color:red">(初填完成)</span>
                    </h3>
                    <div id="loginInfo" class="col row justify-content-end mx-0 align-items-center">
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
                <p class="line-height-1">
                    【請下載以下兩項表格】<br>
                </p>

            </div>

        </div>
    </section>

    <?php require_once("./module/footer.php") ?>

    <script>
        $(function() {
            $("#email").text(sessionItems['email']);
        });
    </script>



</body>

</html>