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
        }
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
                    :::取得繳費帳號 <span style="color:red">(申請單)</span>
                </h3>
            </div>
            <form class="border p-4 bg-white shadow rounded">
                <div class="form-group row">
                    <label for="inputName" class="col-sm-3">姓名</label>
                    <input type="text" class="form-control col-sm-4" id="inputName" name="name" required>
                </div>
                <fieldset class="form-group row">
                    <legend class="col-form-label col-sm-3 float-sm-left">性別</legend>
                    <div class="col-sm-4">
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="gender" id="inlineRadio1" value="male" required>
                            <label class="form-check-label" for="inlineRadio1">男</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="gender" id="inlineRadio2" value="female" required>
                            <label class="form-check-label" for="inlineRadio2">女</label>
                        </div>
                    </div>
                </fieldset>
                <div class="form-group row">
                    <label for="inputStatus" class="col-sm-3">繳費身分別</label>
                    <select id="inputStatus" class="form-control col-sm-4" name="status" required>
                        <option selected hidden disabled></option>
                        <option value="0">一般考生</option>
                        <option value="1">特殊考生</option>
                    </select>
                </div>
                <div class="form-group row">
                    <label for="inputIDNumber" class="col-sm-3">身分證字號</label>
                    <input type="text" class="form-control col-sm-4" id="inputIDNumber" aria-describedby="IDNumberHelp" pattern="[A-Z]\d{9}" name="IDNumber" required>
                    <small id="IDNumberHelp" class="form-text text-muted col-sm-4">*僑外生請填寫居留證號碼</small>
                </div>
                <div class="form-group row">
                    <label for="inputTel" class="col-sm-3">電話</label>
                    <input type="tel" class="form-control col-sm-4" id="inputTel" name="tel" required>
                </div>
                <div class="form-group row">
                    <label for="inputEmail" class="col-sm-3">Email信箱</label>
                    <input type="email" class="form-control col-sm-4" id="inputEmail" name="email" required>
                </div>
                <div class="form-group row">
                    <label for="inputDep" class="col-sm-3">報考系所</label>
                    <select id="inputDep" class="form-control col-sm-4" name="dep" required>
                        <option selected hidden disabled></option>
                        <option value="0">英語系、美術系藝教班、兒英所、翻譯所報名費 1800元</option>
                        <option value="1">其他系所 1300元</option>
                    </select>
                </div>
                <hr />
                <div class="line-height-1">
                    注意事項：<br>
                    <ol style="list-style-type:upper-roman;">
                        <li><span style="color:red">凡本校畢業校友(含應屆畢業生)，報名費用一律以八折計算。</span></li>
                        <li><span style="color:red">曾報考本校110學年度碩士班推薦甄試生報考本次招生考試者，免繳報名費；序號及密碼將於繳費帳號取得完成後直接由系統寄發至所留電子信箱。</span></li>
                        <li></li>
                        <li></li>
                        <li></li>
                    </ol>
                    一、<br>
                    二、<br>
                    三、資料送出後，即無法修改，請仔細檢查您所填之各項資料。<br>
                    四、若資料送出後始查覺錯誤者請勿繳費，並請重新上網填寫本表單，取得新的繳費帳號。<br>
                    五、請確認您所填的Emai信箱是正確可用的，系統銷帳後，將依您所填之資料寄件。<br>
                </div>
                <div class="row justify-content-center">
                    <button type="reset" style="min-width:4rem" class="btn btn-danger btn-sm col-1 mx-1">重填</button>
                    <button type="submit" style="min-width:4rem" class="btn btn-primary btn-sm col-1 mx-1">下一步</button>
                </div>
            </form>
        </div>
    </section>


    <?php require_once("./module/footer.php") ?>

    <script>
        $(function() {

            if (sessionStorage.hasOwnProperty('order') && sessionStorage.getItem('order') !== null) {
                let array = sessionStorage.getItem('order').split('&');
                for (let i = 0; i < array.length; i++) {
                    let strParts = array[i].split("=");
                    strParts[0] = decodeURIComponent(strParts[0]);
                    strParts[1] = decodeURIComponent(strParts[1]);
                    if (strParts[0] === "gender") {
                        $("form [name=" + strParts[0] + "][value='" + strParts[1] + "']").attr('checked', true)
                        $("form [name=" + strParts[0] + "][value='" + strParts[1] + "']").removeAttr("disabled");
                    } else {
                        if (strParts[0] === "status" || strParts[0] === "dep")
                            $("form [name='" + strParts[0] + "']>option[value='" + strParts[1] + "']").removeAttr("disabled");
                        $("form [name='" + strParts[0] + "']").val(strParts[1]).change();

                    }
                }
                sessionStorage.removeItem('order');

            }
        });
        $("form").on('submit', function(e) {
            e.preventDefault();

            sessionStorage.setItem("order", $("form").serialize());
            window.location.replace('./order.php?step=2');

        });

        $("form button[type='reset']").on('click', function(e) {
            e.preventDefault();
            if (confirm('確定清除嗎？')) {
                $("form")[0].reset();
                sessionStorage.removeItem('order');
            }

        });
    </script>

</body>

</html>