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
        } else if (sessionStorage.hasOwnProperty('alter'))
            fillByStorage('alter');
        else
            fillByData("./API/signup/form.php");
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
                        :::修改報名資料
                    </h3>
                    <div id="loginInfo" class="col row justify-content-end mx-0 align-items-center" style="display: none !important;">
                        <div>Hi~ <span id="username"></span> </div>
                        <button type="button" id="logout" style="min-width:4rem" class="btn btn-info btn-sm ml-3">登出</button>
                    </div>
                </div>
            </div>

            <form class="border p-4 bg-white shadow rounded" method="POST">
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="inputDept">報考系所</label>
                        <select id="inputDept" class="form-control">
                            <option selected hidden disabled></option>
                            <option>資訊工程學系碩士班</option>
                        </select>
                    </div>
                    <div class="form-group col-md-6">
                        <label for="inputOrganize">報考組(科)別</label>
                        <select id="inputOrganize" class="form-control">
                            <option selected hidden disabled></option>
                            <option>無分組(科)</option>
                        </select>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="inputStatus">報考身分</label>
                        <select id="inputStatus" class="form-control" name="status">
                            <option selected hidden disabled></option>
                            <option>一般考生</option>
                        </select>
                    </div>
                    <div class="form-group col-md-6">
                    </div>
                </div>
                <fieldset class="form-group row">
                    <legend class="col-form-label col-sm-3 float-sm-left">身心障礙考生</legend>
                    <div class="col-sm-4">
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="inlineRadioOptions" id="cripple1" name="cripple" value="true">
                            <label class="form-check-label" for="cripple1">是</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="inlineRadioOptions" id="cripple2" name="cripple" value="false">
                            <label class="form-check-label" for="cripple2">否</label>
                        </div>
                    </div>
                </fieldset>
                <hr />
                <div class="form-group row">
                    <label for="inputName" class="col-sm-3">姓名</label>
                    <input type="text" class="form-control col-sm-4" id="inputName" name="name">
                </div>
                <div class="form-group row">
                    <label for="inputIDNumber" class="col-sm-3">身分證字號</label>
                    <input type="text" class="form-control col-sm-4" id="inputIDNumber" pattern="[A-Z]\d{9}" aria-describedby="IDNumberHelp">
                    <small id="IDNumberHelp" class="form-text text-muted col-sm-4" name="IDNumber">*僑外生請填寫居留證號碼</small>
                </div>
                <fieldset class="form-group row">
                    <legend class="col-form-label col-sm-3 float-sm-left">性別</legend>
                    <div class="col-sm-4">
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="gender" id="gender1" value="male">
                            <label class="form-check-label" for="gender1">男</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="gender" id="gender2" value="female">
                            <label class="form-check-label" for="gender2">女</label>
                        </div>
                    </div>
                </fieldset>
                <div class="form-group row">
                    <label for="inputBirthday" class="col-sm-3">出生日期</label>
                    <input type="date" class="form-control col-sm-4" id="inputBirthday" aria-describedby="birthdayHelp" name="birthday" placeholder="yyyy-mm-dd" pattern="\d{4}-\d{2}-\d{2}" required>
                    <small id="birthdayHelp" class="form-text text-muted col-sm-4">*西元年 = 民國年 + 1911</small>
                </div>
                <div class="form-group row">
                    <label for="inputTel" class="col-sm-3">電話</label>
                    <input type="tel" class="form-control col-sm-4" id="inputTel" name="tel">
                </div>
                <div class="form-group row">
                    <label for="inputEmail" class="col-sm-3">Email信箱</label>
                    <input type="email" class="form-control col-sm-4" id="inputEmail" name="email">
                </div>
                <hr />
                <fieldset class="form-group row">
                    <legend class="col-form-label col-sm-3 float-sm-left">應考學歷</legend>
                    <div class="col-sm-8">
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="prove" id="prove1" value="0">
                            <label class="form-check-label" for="prove1">學士學位</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="prove" id="prove2" value="1">
                            <label class="form-check-label" for="prove2">同等學力</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="prove" id="prove3" value="2">
                            <label class="form-check-label" for="prove3">國家考試及格</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="prove" id="prove4" value="3">
                            <label class="form-check-label" for="prove4">技能檢定合格</label>
                        </div>
                    </div>
                </fieldset>
                <div class="form-group row">
                    <label for="inputData" class="col-sm-3">備審資料上傳</label>
                    <input type="file" class="form-control-file col-sm-4" id="inputData" name="data">
                </div>
                <div class="form-group row">
                    <label class="col-sm-3">繳驗證件</label>
                    <div class="col-sm-9 color-info">
                        <ol>
                            <li>以同等學力報考者，須將同等學力證件影本於報名截止前寄送達本校招生委員會審查。未繳驗證件者，如獲錄取，於報到時學力證件審查不合格，須撤銷錄取資格，不得異議。</li>
                            <li>招生系所如須郵寄或上傳備審資料，請依招生簡章【重點項目一覽表】之「資料審查繳交方式」辦理。</li>
                        </ol>
                    </div>
                </div>
                <hr />
                <div class="line-height-1">
                    注意事項：<br>
                    <ol style="list-style-type:upper-roman;">
                        <li></li>
                        <li></li>
                        <li></li>
                        <li></li>
                        <li></li>
                    </ol>
                </div>
                <div class="row justify-content-end">
                    <button type="button" id="logout" style="min-width:4rem" class="btn btn-info btn-sm col-1 mx-3">登出</button>
                    <button type="reset" style="min-width:4rem" class="btn btn-danger btn-sm col-1 mx-1">清除</button>
                    <button type="submit" style="min-width:4rem" class="btn btn-primary btn-sm col-1 mx-1">下一步</button>
                </div>
            </form>
        </div>
    </section>


    <?php require_once("./module/footer.php") ?>

    <script>
        $("form").on('submit', function(e) {
            e.preventDefault();

            sessionStorage.setItem("alter", $("form").serialize());
            window.location.replace('./alter.php?step=3');

        });

        $("form button[type='reset']").on('click', function(e) {
            e.preventDefault();
            if (confirm('確定清除嗎？')) {
                $("form")[0].reset();
                sessionStorage.removeItem('alter');
            }

        });
    </script>

</body>

</html>