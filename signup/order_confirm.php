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
        } else if (!sessionStorage.hasOwnProperty('order') || sessionStorage.getItem('order') === null)
            window.location.replace('./order.php');
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
                    <div class="col-sm-4">
                        <input type="text" class="form-control-plaintext" id="inputName" name="name" readonly required>
                    </div>
                </div>
                <fieldset class="form-group row">
                    <legend class="col-form-label col-sm-3 float-sm-left">性別</legend>
                    <div class="col-sm-4">
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="sex" id="inlineRadio1" value="1" disabled required>
                            <label class="form-check-label" for="inlineRadio1">男</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="sex" id="inlineRadio2" value="0" disabled required>
                            <label class="form-check-label" for="inlineRadio2">女</label>
                        </div>
                    </div>
                </fieldset>
                <div class="form-group row">
                    <label for="inputIdentity" class="col-sm-3">繳費身分別</label>
                    <div class="col-sm-4">
                        <select id="inputIdentity" class="form-control-plaintext" name="identity" readonly required>
                            <option selected hidden disabled></option>
                            <option value="1">一般考生</option>
                            <option value="2">中低收入戶考生</option>
                            <option value="3">低收入戶考生</option>
                        </select>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="inputId" class="col-sm-3">身分證字號<br>
                        <small id="inputIdHelp" class="form-text text-muted ">*僑外生請填寫居留證號碼</small>
                    </label>
                    <div class="col-sm-4">
                        <input type="text" class="form-control-plaintext" id="inputId" pattern="[A-Z]\d{9}" aria-describedby="inputIdHelp" name="id" readonly required>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="inputTel" class="col-sm-3">電話</label>
                    <div class="col-sm-4">
                        <input type="tel" class="form-control-plaintext" id="inputTel" name="tel" readonly required>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="inputEmail" class="col-sm-3">Email信箱</label>
                    <div class="col-sm-4">
                        <input type="email" class="form-control-plaintext" id="inputEmail" name="email" readonly required>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="inputDep" class="col-sm-3">報考系所</label>
                    <div class="col-sm-4">
                        <select id="inputDep" class="form-control-plaintext " name="dept_id" readonly required>
                            <option selected hidden disabled></option>
                            <option value="1">英語系、美術系藝教班、兒英所、翻譯所報名費 1800元</option>
                            <option value="2">其他系所 1300元</option>
                        </select>
                    </div>
                </div>
                <hr />
                <p class="line-height-1">
                    <span style="color:red">請確認您的資料，正確請按"下一步"繼續，修改資料請按"上一步"。</span>
                </p>
                <div class="row justify-content-center">
                    <button type="button" style="min-width:4rem" class="btn btn-danger btn-sm col-1 mx-1 btn-cancel">取消</button>
                    <button type="button" style="min-width:4rem" class="btn btn-warning btn-sm col-1 mx-1" onclick="window.location.replace('./order.php?step=2')">上一步</button>
                    <button type="submit" style="min-width:4rem" class="btn btn-primary btn-sm col-1 mx-1">下一步</button>
                </div>
            </form>
        </div>
    </section>

    <?php require_once("./module/footer.php") ?>

    <!--toastr-->
    <link rel="stylesheet" href="./css/toastr.min.css" />
    <script src="./js/toastr.min.js"></script>

    <!--jQuery Validation Plugin-->
    <script src="./js/jquery.validate.min.js"></script>
    <script src="./js/additional-methods.min.js"></script>
    <script src="./js/messages_zh_TW.min.js"></script>

    <!--custom-->
    <script src="./js/order.js"></script>
    <script>
        $(function() {
            var formData = getSessionItems('order');
            fillForm(formData);
            formReadOnly();
        });

        $("form").on('submit', function(e) {
            e.preventDefault();

            $("form [type='submit']").attr('disabled', true);

            $.ajax({
                    type: 'POST',
                    url: "./API/order/order.php",
                    data: $("form").serialize(),
                    dataType: 'json'
                }).done(function(response) {
                    sessionStorage.clear();
                    sessionStorage.setItem('account_no', response['data']['account_no']);
                    sessionStorage.setItem('pay_money', response['data']['pay_money']);
                    sessionStorage.setItem('email', response['data']['email']);
                    sessionStorage.setItem('low_income_end_date', response['data']['low_income_end_date']);
                    sessionStorage.setItem('acc2_end_date', response['data']['acc2_end_date']);
                    window.location.replace('./order.php?step=4');
                })
                .fail(function(jqXHR, exception) {
                    $("form [type='submit']").removeAttr('disabled');

                    var response = jqXHR.responseJSON;
                    var msg = '';
                    if (response === undefined)
                        msg = exception + "\n" + "./API/order/order.php" + "\n" + jqXHR.responseText;
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