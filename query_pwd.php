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
    <script>
        if (sessionStorage === undefined) {
            alert("未支援Web Storage！\n請更換瀏覽器再試。");
            window.location.replace('./');
        } else
            sessionStorage.clear();
    </script>
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
                    :::查詢序號密碼
                </h3>
            </div>
            <form class="border p-4 bg-white shadow rounded ">
                <div class="form-group row justify-content-center">
                    <label for="inputEmail" class="col-sm-1" style="min-width:6rem">電子信箱</label>
                    <input type="email" class="form-control col-sm-4" id="inputEmail" name="email" required>
                </div>

                <p style="text-align: center;">
                    <span class="color-info">請輸入您申請繳費帳號時所登錄的電子郵件信箱!</span><br>
                    <span style="color:red">(需繳完報名費並銷帳後才可查詢序號密碼！)</span><br>
                </p>
                <div class="row justify-content-center mt-2">
                    <button type="submit" style="min-width:4rem" class="btn btn-primary btn-sm col-1 mx-1">送出</button>
                </div>

            </form>
        </div>
    </section>


    <?php require_once("./module/footer.php") ?>

    <script>
        $("form").on('submit', function(e) {
            e.preventDefault();
            $("form [type='submit']").attr('disabled', true);
            $.ajax({
                    type: 'POST',
                    url: './API/auth/forget.php',
                    data: $("form").serialize(),
                    dataType: 'json'

                }).done(function(response) {
                    toastr.clear();
                    alert("序號及密碼已重新發送至您的E-Mail信箱！");
                    window.location.replace('./');

                })
                .fail(function(jqXHR, exception) {
                    $("form [type='submit']").removeAttr('disabled');
                    toastr.clear();
                    var response = jqXHR.responseJSON;
                    var msg = '';
                    if (response === undefined)
                        msg = exception + "\n" + './API/auth/forget.php' + "\n" + jqXHR.responseText;
                    else if (response.hasOwnProperty('message')) {
                        msg = response.message;
                    } else {
                        msg = 'Uncaught Error.\n' + jqXHR.responseText;
                    }
                    toastr.error(msg);
                });
        });
    </script>

</body>

</html>