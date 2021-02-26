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
                <h3 style="letter-spacing: 0.2rem;min-width:14rem">
                    :::網站管理 <span style="color:red">(登入)</span>
                </h3>
            </div>
            <form class="border p-4 bg-white shadow rounded ">
                <div class="form-group row justify-content-center">
                    <label for="inputAccount" class="col-sm-1" style="min-width:4rem">帳號</label>
                    <input type="text" class="form-control col-sm-4" id="inputAccount" name="account" required>
                </div>
                <div class="form-group row justify-content-center">
                    <label for="inputPwd" class="col-sm-1" style="min-width:4rem">密碼</label>
                    <input type="password" class="form-control col-sm-4" id="inputPwd" name="pwd" required>
                </div>
                <div class="row justify-content-center mt-2">
                    <button type="submit" style="min-width:4rem" class="btn btn-primary btn-sm col-1 mx-1">登入</button>
                </div>
            </form>
        </div>
    </section>


    <?php require_once("./module/footer.php") ?>

    <!--toastr-->
    <link rel="stylesheet" href="./css/toastr.min.css" />
    <script src="./js/toastr.min.js"></script>

    <script>
        $("form").on('submit', function(e) {
            e.preventDefault();

            $.ajax({
                    type: 'POST',
                    url: './API/auth/admin.php',
                    data: $("form").serialize(),
                    dataType: 'json'
                }).done(function(response) {
                    toastr.clear();
                    toastr.success("登入成功！");
                    window.location.replace('./management.php')

                })
                .fail(function(jqXHR, exception) {
                    let response = jqXHR.responseJSON;
                    let msg = '';
                    if (response === undefined)
                        msg = exception + "\n" + './API/auth/admin.php' + "\n" + jqXHR.responseText;
                    else if (response.hasOwnProperty('message')) {
                        msg = response.message;
                    } else {
                        msg = 'Uncaught Error.\n' + jqXHR.responseText;
                    }
                    toastr.clear();
                    toastr.error(msg);
                });
        });
    </script>



</body>

</html>