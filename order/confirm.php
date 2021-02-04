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
                    :::取得繳費帳號 <span style="color:red">(資料確認)</span>
                </h3>
            </div>
            <form class="border p-4 bg-white shadow rounded">
                <div class="form-group row">
                    <label for="inputName" class="col-sm-3">姓名</label>
                    <input type="text" class="form-control-plaintext col-sm-4" id="inputName" name="name" readonly required>
                </div>
                <fieldset class="form-group row">
                    <legend class="col-form-label col-sm-3 float-sm-left">性別</legend>
                    <div class="col-sm-4">
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="gender" id="inlineRadio1" value="male" disabled required>
                            <label class="form-check-label" for="inlineRadio1">男</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="gender" id="inlineRadio2" value="female" disabled required>
                            <label class="form-check-label" for="inlineRadio2">女</label>
                        </div>
                    </div>
                </fieldset>
                <div class="form-group row">
                    <label for="inputStatus" class="col-sm-3">繳費身分別</label>
                    <select id="inputStatus" class="form-control-plaintext col-sm-4" name="status" readonly required>
                        <option selected hidden disabled></option>
                        <option value="0" hidden disabled>一般考生</option>
                        <option value="1" hidden disabled>特殊考生</option>
                    </select>
                </div>
                <div class="form-group row">
                    <label for="inputIDNumber" class="col-sm-3">身分證字號</label>
                    <input type="text" class="form-control-plaintext col-sm-4" id="inputIDNumber" pattern="[A-Z]\d{9}" aria-describedby="IDNumberHelp" name="IDNumber" readonly required>
                    <small id="IDNumberHelp" class="form-text text-muted col-sm-4">*僑外生請填寫居留證號碼</small>
                </div>
                <div class="form-group row">
                    <label for="inputTel" class="col-sm-3">電話</label>
                    <input type="tel" class="form-control-plaintext col-sm-4" id="inputTel" name="tel" readonly required>
                </div>
                <div class="form-group row">
                    <label for="inputEmail" class="col-sm-3">Email信箱</label>
                    <input type="email" class="form-control-plaintext col-sm-4" id="inputEmail" name="email" readonly required>
                </div>
                <div class="form-group row">
                    <label for="inputDep" class="col-sm-3">報考系所</label>
                    <select id="inputDep" class="form-control-plaintext col-sm-4" name="dep" readonly required>
                        <option selected hidden disabled></option>
                        <option value="0" hidden disabled>英語系、美術系藝教班、兒英所、翻譯所報名費 1800元</option>
                        <option value="1" hidden disabled>其他系所 1300元</option>
                    </select>
                </div>
                <hr />
                <p class="line-height-1">
                    <span style="color:red">請確認您的資料，正確請按"下一步"繼續，修改資料請按"上一步"。</span>
                </p>
                <div class="row justify-content-center">
                    <button type="reset" style="min-width:4rem" class="btn btn-danger btn-sm col-1 mx-1">取消</button>
                    <button type="button" style="min-width:4rem" class="btn btn-warning btn-sm col-1 mx-1" onclick="window.location.replace('./order.php')">上一步</button>
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
                    url: "./API/signup/order.php",
                    dataType: 'json'
                }).done(function(response) {
                    let str = "email=" + response['data']['email'] + "&account_no=" + response['data']['account_no'];
                    sessionStorage.setItem('order', str);
                    window.location.replace('./order.php?step=3');
                })
                .fail(function(jqXHR, exception) {
                    let response = jqXHR.responseJSON;
                    let msg = '';
                    if (response === undefined)
                        msg = exception;
                    else if (response.hasOwnProperty('message')) {
                        msg = response.message;
                    } else {
                        msg = 'Uncaught Error.\n' + jqXHR.responseText;
                    }
                    toastr.error(msg);
                });

        });
        $("form button[type='reset']").on('click', function(e) {
            e.preventDefault();
            if (confirm('確定取消嗎？')) {
                $("form")[0].reset();
                sessionStorage.removeItem('order');
                window.location.replace('./');
            }

        });
    </script>


</body>

</html>