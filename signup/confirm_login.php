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
    <script src="./js/common.js"></script>
    <script>
        if (sessionStorage === undefined) {
            alert("未支援Web Storage！\n請更換瀏覽器再試。");
            window.location.replace('./');
        } else
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
                    :::報名資料確認 <span style="color:red">(使用者登入)</span>
                </h3>
            </div>
            <form class="border p-4 bg-white shadow rounded ">
                <div class="form-group row justify-content-center">
                    <label for="inputIDNumber" class="col-sm-1" style="min-width:7rem">身分證字號</label>
                    <input type="text" class="form-control col-sm-4" id="inputIDNumber" name="IDNumber" pattern="[A-Z]\d{9}" required>
                </div>
                <div class="form-group row justify-content-center">
                    <label for="inputSerialNo" class="col-sm-1" style="min-width:7rem">序號</label>
                    <input type="text" class="form-control col-sm-4" pattern="[A-Z0-9]{10}" id="inputSerialNo" name="serial_no" required>
                </div>
                <div class="form-group row justify-content-center">
                    <label for="inputPwd" class="col-sm-1" style="min-width:7rem">密碼</label>
                    <input type="password" class="form-control col-sm-4" pattern="[A-Z0-9]{10}" id="inputPwd" name="pwd" required>
                </div>
                <p style="text-align: center;">
                    <span style="color:red">※序號及密碼皆為10碼，所有的英文字母皆為大寫※</span><br>
                </p>
                <div class="row justify-content-center mt-2">
                    <button type="button" style="min-width:8.5rem" class="btn btn-warning btn-sm col-1 mx-1" onclick="window.location.assign('./query_pwd.php');">忘記序號、密碼</button>
                    <button type="submit" style="min-width:4rem" class="btn btn-primary btn-sm col-1 mx-1">下一步</button>
                </div>

            </form>
        </div>
    </section>


    <?php require_once("./module/footer.php") ?>

    <script>
        $("form").on('submit', function(e) {
            e.preventDefault();

            $.ajax({
                    type: 'POST',
                    url: './API/auth/login.php',
                    data: $("form").serialize(),
                    dataType: 'json'
                }).done(function(response) {
                    toastr.clear();
                    toastr.success("登入成功！");
                    window.location.replace('./confirm.php?step=2')
                })
                .fail(function(jqXHR, exception) {
                    // toastr.remove();
                    toastr.clear();
                    var response = jqXHR.responseJSON;
                    var msg = '';
                    if (response === undefined)
                        msg = exception + "\n" + './API/auth/login.php' + "\n" + jqXHR.responseText;
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