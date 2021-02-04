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
        } else if (!sessionStorage.hasOwnProperty('agree') || sessionStorage.getItem('agree') !== "true")
            window.location.replace('./signup.php?step=2');
        else if (sessionStorage.hasOwnProperty('signup') && sessionStorage.getItem('signup') !== null)
            $(function() {
                fillByStorage('signup');
            });
    </script>
    <script>
        $.holdReady(true);
        var deptObj = null;
        $.ajax({
                type: 'GET',
                url: "./API/dept/list.php",
                dataType: 'json'
            }).done(function(response) {
                deptObj = response['data'];
                $.holdReady(false);
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
                        :::填寫報名表
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
                        <select id="inputDept" class="form-control" name="dept" required>
                            <option selected hidden disabled></option>
                        </select>
                    </div>
                    <div class="form-group col-md-6">
                        <label for="inputOrganize">報考組(科)別</label>
                        <select id="inputOrganize" class="form-control" name="dept_group" required>
                            <option selected hidden disabled></option>
                        </select>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="inputStatus">報考身分</label>
                        <select id="inputStatus" class="form-control" name="dept_status" required>
                            <option selected hidden disabled></option>
                        </select>
                    </div>
                    <div class="form-group col-md-6">
                    </div>
                </div>
                <fieldset class="form-group row">
                    <legend class="col-form-label col-sm-3 float-sm-left" style="min-width: 9rem;"><span style="color:red">身心障礙考生</span></legend>
                    <div class="col-sm-5 row mx-0">
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" id="cripple1" name="cripple" value="true" required>
                            <label class="form-check-label" for="cripple1"><span style="color:red">是</span></label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" id="cripple2" name="cripple" value="false" checked required>
                            <label class="form-check-label font-weight-bold" for="cripple2"><span style="color:red"><u>否</u></span></label>
                        </div>
                    </div>
                </fieldset>
                <fieldset class="form-group row">
                    <legend class="col-form-label col-sm-3 float-sm-left" style="min-width: 9rem;"><span style="color:red">報名考區</span></legend>
                    <div class="col-sm-5 row mx-0">
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" id="area1" name="area" value="true" checked required>
                            <label class="form-check-label color-info" for="area1">彰化考區</label>
                        </div>
                    </div>
                </fieldset>
                <hr />
                <div class="form-group row">
                    <label for="inputName" class="col-sm-3">姓名</label>
                    <input type="text" class="form-control col-sm-5" id="inputName" name="name" required>
                </div>
                <div class="form-group row">
                    <label for="inputIDNumber" class="col-sm-3">身分證字號</label>
                    <input type="text" class="form-control col-sm-5" id="inputIDNumber" aria-describedby="IDNumberHelp" pattern="[A-Z]\d{9}" name="id" required>
                    <small id="IDNumberHelp" class="form-text text-muted col-sm-4">*僑外生請填寫居留證號碼</small>
                </div>
                <fieldset class="form-group row">
                    <legend class="col-form-label col-sm-3 float-sm-left">性別</legend>
                    <div class="col-sm-5">
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="gender" id="gender1" value="1" required>
                            <label class="form-check-label" for="gender1">男</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="gender" id="gender2" value="2" required>
                            <label class="form-check-label" for="gender2">女</label>
                        </div>
                    </div>
                </fieldset>
                <div class="form-group row">
                    <label for="inputBirthday" class="col-sm-3">出生日期</label>
                    <input type="date" class="form-control col-sm-5" id="inputBirthday" aria-describedby="birthdayHelp" name="birthday" placeholder="yyyy-mm-dd" pattern="\d{4}-\d{2}-\d{2}" required>
                    <small id="birthdayHelp" class="form-text text-muted col-sm-4">*西元年 = 民國年 + 1911</small>
                </div>
                <div class="form-group row">
                    <label class="col-sm-3">通訊地址</label>
                    <div class="col-sm-9">
                        <div class="row form-group">
                            <label for="inputZipcode" class="col-sm-2" style="min-width: 7rem;">郵遞區號：</label>
                            <input type="text" class="form-control col-sm-3" id="inputZipcode" aria-describedby="zipcodeHelp" pattern="\d{5}\d{0,1}" name="zipcode" required>
                            <small id="zipcodeHelp" class="form-text text-muted col-sm-5">
                                <span style="color:red">(請輸入半形數字)</span>
                                <a href="https://www.post.gov.tw/post/internet/Postal/index.jsp?ID=208" target="_blank" style="word-break:keep-all">郵遞區號查詢</a>
                            </small>
                        </div>
                        <div class="row form-group">
                            <label for="inputAddress" class="col-sm-2" style="min-width: 7rem;">地址：</label>
                            <input type="text" class="form-control col-xl" id="inputAddress" name="address" required>
                        </div>
                    </div>
                </div>
                <div class="form-group row">
                    <div class="col-sm-3">
                        <label>戶籍地址</label>
                        <button type="button" class="btn btn-sm btn-secondary ml-1" id="address2_btn">同上</button>
                    </div>
                    <div class="col-sm-9">
                        <div class="row form-group">
                            <label for="inputZipcode2" class="col-sm-2" style="min-width: 7rem;">郵遞區號：</label>
                            <input type="text" class="form-control col-sm-3" id="inputZipcode2" aria-describedby="zipcode2Help" pattern="\d{5}\d{0,1}" name="zipcode2" required>
                            <small id="zipcode2Help" class="form-text text-muted col-sm-5">
                                <span style="color:red">(請輸入半形數字)</span>
                                <a href="https://www.post.gov.tw/post/internet/Postal/index.jsp?ID=208" target="_blank" style="word-break:keep-all">郵遞區號查詢</a>
                            </small>
                        </div>
                        <div class="row form-group">
                            <label for="inputAddress2" class="col-sm-2" style="min-width: 7rem;">地址：</label>
                            <input type="text" class="form-control col-xl" id="inputAddress2" name="address2" required>
                        </div>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="inputTel" class="col-sm-3">聯絡電話</label>
                    <div class="col-xl">
                        <div class="row form-group">
                            <label for="inputTel_h" class="col-sm-2" style="min-width: 7rem;">住家：</label>
                            <div class="row col-sm align-items-center">
                                (&nbsp;<input type="text" class="form-control col-sm-2" style="max-width: 3rem;" name="tel_h_a" required>&nbsp;)&nbsp;
                                <input type="text" class="form-control col-sm-3" style="max-width: 10rem;" id="inputTel_h" name="tel_h" required>
                            </div>
                        </div>
                        <div class="row form-group">
                            <label for="inputTel_o" class="col-sm-2" style="min-width: 7rem;">公司：</label>
                            <div class="row col-sm align-items-center">
                                (&nbsp;<input type="text" class="form-control col-sm-2" style="max-width: 3rem;" name="tel_o_a" required>&nbsp;)&nbsp;
                                <input type="text" class="form-control col-sm-3" style="max-width: 10rem;" id="inputTel_o" name="tel_o" required>
                            </div>
                        </div>
                        <div class="row form-group">
                            <label for="inputTel_m" class="col-sm-2" style="min-width: 7rem;">手機：</label>
                            <input type="tel" class="form-control col-sm-3" id="inputTel_m" pattern="09\d{8}" placeholder="09xxxxxxxx" name="tel_m" required>
                        </div>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="inputEmail" class="col-sm-3">Email信箱</label>
                    <input type="email" class="form-control col-sm-5" id="inputEmail" name="email" placeholder="example@gmail.com" required>
                </div>
                <div class="form-group row">
                    <label class="col-sm-3">緊急連絡人</label>
                    <div class="row col-xl">
                        <div class="form-group col-sm-4" style="min-width: 10rem;">
                            <label for="inputConn_name" class="">姓名：</label>
                            <input type="text" class="form-control col-sm-11" id="inputConn_name" name="conn_name" required>
                        </div>
                        <div class="form-group col-sm-4" style="min-width: 10rem;">
                            <label for="inputConn_tel" class="">電話：</label>
                            <input type="tel" class="form-control col-sm-11" id="inputConn_tel" name="conn_tel" required>
                        </div>
                        <div class="form-group col-sm-4" style="min-width: 10rem;">
                            <label for="inputConn_rel" class="">關係：</label>
                            <input type="text" class="form-control col-sm-11" id="inputConn_rel" name="conn_rel" required>
                        </div>
                    </div>
                </div>
                <hr />
                <fieldset class="form-group row">
                    <legend class="col-form-label col-sm-3 float-sm-left">應考學歷</legend>
                    <div class="col-sm-8">
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="prove_type" id="prove1" value="1" required>
                            <label class="form-check-label" for="prove1">學士學位</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="prove_type" id="prove2" value="2" required>
                            <label class="form-check-label" for="prove2">同等學力</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="prove_type" id="prove3" value="3" required>
                            <label class="form-check-label" for="prove3">國家考試及格</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="prove_type" id="prove4" value="4" required>
                            <label class="form-check-label" for="prove4">技能檢定合格</label>
                        </div>
                    </div>
                </fieldset>
                <!--<div class="form-group row">
                    <label for="inputData" class="col-sm-3">備審資料上傳</label>
                    <input type="file" class="form-control-file col-sm-4" id="inputData" name="file">
                    <div class="form-control-file col-sm-5"><a id="fileLink" target="_blank" style="color:red"></a></div>
                </div>-->
                <div class="form-group row">
                    <label class="col-sm-3">繳驗證件</label>
                    <div class="col-sm-9 color-info line-height-1">
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
                        <li>一組「序號」及「密碼」，僅能選擇一個系所班（組）別報名。</li>
                        <li>公司電話可自行決定填寫與否，其餘欄位皆為必填。</li>
                        <li>報名時，除同等學力要繳驗相關證明外，其他報考資格均無須繳驗學歷（力）證件。錄取生於報到時須繳驗簡章規定之所有證件資料正本，資格不符者將被取消錄取資格，由備取生遞補。考生請詳閱招生簡章規定。</li>
                        <li>請詳細填寫核對資料，如因輸入錯誤致影響權益或錄取資格，概由考生自行負責。</li>
                        <li>年資年數計算至 入學年度當學期本校行事曆所訂之註冊截止日止。</li>
                    </ol>
                </div>
                <div class="row justify-content-end">
                    <button type="reset" style="min-width:4rem" class="btn btn-danger btn-sm col-1 mx-1">清除</button>
                    <button type="submit" style="min-width:4rem" class="btn btn-primary btn-sm col-1 mx-1">下一步</button>
                </div>
            </form>
        </div>
    </section>


    <?php require_once("./module/footer.php") ?>

    <script>
        $(function() {
            $("form [name='dept']").find('option').remove().end().append('<option selected hidden disabled></option>');
            for (let i = 0; i < deptObj.dept.length; i++)
                $("form [name='dept']").append("<option value='" + deptObj.dept[i].dept_id + "'>" + deptObj.dept[i].name + "</option>");
        });
        $("form [name='dept']").on('change', function() {
            $("form [name='dept_group']").find('option').remove().end().append('<option selected hidden disabled></option>');
            $("form [name='dept_status']").find('option').remove().end().append('<option selected hidden disabled></option>');
            for (let i = 0; i < deptObj.group[$("form [name='dept']").val()].length; i++)
                $("form [name='dept_group']").append("<option value='" + deptObj.group[$("form [name='dept']").val()][i].group_id + "'>" + deptObj.group[$("form [name='dept']").val()][i].name + "</option>");

        });
        $("form [name='dept_group']").on('change', function() {
            $("form [name='dept_status']").find('option').remove().end().append('<option selected hidden disabled></option>');
            for (let i = 0; i < deptObj.status[$("form [name='dept']").val()][$("form [name='dept_group']").val()].length; i++)
                $("form [name='dept_status']").append("<option value='" + deptObj.status[$("form [name='dept']").val()][$("form [name='dept_group']").val()][i].group_id + "'>" + deptObj.status[$("form [name='dept']").val()][$("form [name='dept_group']").val()][i].name + "</option>");

        });
        $(function() {
            $('form #fileLink').text('');
            $.ajax({
                type: 'GET',
                url: './API/signup/file.php',
                dataType: 'text'
            }).done(function(response) {
                $('form #fileLink').css('color', '');
                $('form #fileLink').addClass('color-info');
                $('form #fileLink').text('檔案已上傳');
                $('form #fileLink').attr('href', './API/signup/file.php?export=download'); //'./upload/' + sessionItems['filename']

            }).fail(function(jqXHR, exception) {
                if (jqXHR.status === 404) {
                    $('form #fileLink').removeClass('color-info');
                    $('form #fileLink').css('color', 'red');
                    $('form #fileLink').text('備審資料檔案尚未上傳');
                    $('form #fileLink').removeAttr('href');
                } else {
                    toastr.clear();
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
                }


            });
            /*if (Object.keys(sessionItems).indexOf('filename') !== -1) {
                $('form #fileLink').css('color', '');
                $('form #fileLink').addClass('color-info');
                $('form #fileLink').text('檔案已上傳');
                $('form #fileLink').attr('href', './API/signup/file.php'); //'./upload/' + sessionItems['filename']
            }*/
        });

        var isUploading = false;
        $("form").on('submit', function(e) {
            e.preventDefault();
            if (!isUploading) {
                sessionStorage.setItem("signup", $("form").serialize());
                sessionStorage.setItem("dept", $("form [name='dept']>option[value=" + $("form [name='dept']").val() + "]").text());
                sessionStorage.setItem("dept_group", $("form [name='dept_group']>option[value=" + $("form [name='dept_group']").val() + "]").text());
                sessionStorage.setItem("dept_status", $("form [name='dept_status']>option[value=" + $("form [name='dept_status']").val() + "]").text());
                window.location.replace('./signup.php?step=4');
            }


        });

        $("form button[type='reset']").on('click', function(e) {
            e.preventDefault();
            if (confirm('確定清除嗎？')) {
                $("form")[0].reset();
                /*$('form #fileLink').removeClass('color-info');
                $('form #fileLink').css('color', 'red');
                $('form #fileLink').text('備審資料檔案尚未上傳');
                $('form #fileLink').removeAttr('href');*/
                sessionStorage.removeItem('signup');
            }

        });


        $("form [name='file']").on('change', function() {

            // $("form [name='filename']").val('');
            $('form #fileLink').removeClass('color-info');
            $('form #fileLink').css('color', 'red');
            $('form #fileLink').text('備審資料檔案尚未上傳');
            $('form #fileLink').removeAttr('href');

            var fd = new FormData();
            var files = $(this)[0].files;

            // Check file selected or not
            if (files.length > 0) {
                isUploading = true;
                toastr.clear();
                toastr.info("檔案上傳中");
                fd.append('file', files[0]);

                $.ajax({
                        url: './API/signup/form.php',
                        type: 'POST',
                        data: fd,
                        contentType: false,
                        processData: false
                    }).done(function(response) {
                        toastr.clear();
                        toastr.success("檔案上傳成功成功！");
                        // $("form [name='filename']").val(response.filename);
                        $('form #fileLink').css('color', '');
                        $('form #fileLink').addClass('color-info');
                        $('form #fileLink').text('檔案已上傳');
                        $('form #fileLink').attr('href', './API/signup/file.php'); //'./upload/' + response.filename
                        isUploading = false;
                    })
                    .fail(function(jqXHR, exception) {
                        // toastr.remove();
                        toastr.clear();
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
                        isUploading = false;
                    });
            }
        });

        $("#address2_btn").on('click', function() {
            $("form [name='zipcode2']").val($("form [name='zipcode']").val());
            $("form [name='address2']").val($("form [name='address']").val());
        });
    </script>



</body>

</html>