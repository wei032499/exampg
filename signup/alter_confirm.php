<!doctype html>
<html lang="zh-Hant-TW">

<head>
    <meta charset="utf-8">
    <title>網路報名</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/x-icon" href="./images/favicon.ico">

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
        } else if (!sessionStorage.hasOwnProperty('alter') || sessionStorage.getItem('alter') === null)
            window.location.replace('./alter.php?step=2');
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
                        :::修改報名資料 <span style="color:red">(資料確認)</span>
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
                        <select id="inputDept" class="form-control-plaintext" name="dept" readonly>
                            <option selected disabled hidden></option>
                        </select>
                    </div>
                    <div class="form-group col-md-6">
                        <label for="inputOrganize">報考組(科)別</label>
                        <select id="inputOrganize" class="form-control-plaintext" name="organize_id" readonly>
                            <option selected disabled hidden></option>
                        </select>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="inputStatus">報考身分</label>
                        <select id="inputStatus" class="form-control-plaintext" name="orastatus_id" readonly>
                            <option selected disabled hidden></option>
                        </select>
                    </div>
                    <div class="form-group col-md-6" id="subject" style="visibility: hidden;">
                        <label>選考科目<br>
                            <span id="subject_msg" style="color:red;"></span>
                        </label>
                        <div></div>
                    </div>
                </div>
                <div class="form-group row" id="union" style="display: none;">
                    <label class="col-sm-3" style="min-width: 10rem;">聯招志願序<br>(志願序由上到下)</label>
                    <div class="col-sm-6" style="min-width: 20rem">
                    </div>
                </div>
                <hr>
                <fieldset class="form-group row">
                    <legend class="col-form-label col-sm-3 float-sm-left" style="min-width: 9rem;"><span style="color:red">身心障礙考生</span></legend>
                    <div class="col-xl row mx-0">
                        <div style="max-width: 10rem;">
                            <div class="form-check form-check-inline form-group">
                                <input class="form-check-input" type="radio" id="disabled1" name="disabled" value="1" disabled readonly required>
                                <label class="form-check-label" for="disabled1"><span style="color:red">是</span></label>
                            </div>
                            <div class="form-check form-check-inline form-group">
                                <input class="form-check-input" type="radio" id="disabled2" name="disabled" value="0" disabled readonly checked required>
                                <label class="form-check-label font-weight-bold" for="disabled2"><span style="color:red"><u>否</u></span></label>
                            </div>
                        </div>
                        <div class="col-sm " id="disabled_extra" style="display: none;">
                            <select class="form-control-plaintext form-group" name="disabled_type">
                                <option selected hidden disabled></option>
                                <option value="1">聽覺障礙</option>
                                <option value="2">視覺障礙</option>
                                <option value="3">腦性麻庳</option>
                                <option value="4">自閉症</option>
                                <option value="5">學習障礙</option>
                                <option value="6">其他障礙</option>
                            </select>
                            <input class="form-control-plaintext form-group" type="text" name="comments" placeholder="請填入說明" style="display: none;" readonly>
                        </div>
                    </div>
                </fieldset>
                <fieldset class="form-group row">
                    <legend class="col-form-label col-sm-3 float-sm-left" style="min-width: 9rem;"><span style="color:red">報名考區</span></legend>
                    <div class="col-sm-5 row mx-0">
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" id="place1" name="place" value="1" disabled checked readonly required>
                            <label class="form-check-label color-info" for="place1">彰化考區</label>
                        </div>
                        <div class="form-check form-check-inline" style="display: none;">
                            <input class="form-check-input" type="radio" id="place2" name="place" value="2" required>
                            <label class="form-check-label color-info" for="place2">台北考區</label>
                        </div>
                    </div>
                </fieldset>
                <hr />
                <div class="form-group row">
                    <label for="inputName" class="col-sm-3">姓名</label>
                    <input type="text" class="form-control-plaintext col-sm-5" id="inputName" name="name" readonly required>
                </div>
                <div class="form-group row">
                    <label for="inputIDNumber" class="col-sm-3">身分證字號</label>
                    <input type="text" class="form-control-plaintext col-sm-5" id="inputIDNumber" aria-describedby="IDNumberHelp" pattern="[A-Z]\d{9}" name="id" readonly disabled>
                    <small id="IDNumberHelp" class="form-text text-muted col-sm-4">*僑外生居留證號碼</small>
                </div>
                <fieldset class="form-group row">
                    <legend class="col-form-label col-sm-3 float-sm-left">性別</legend>
                    <div class="col-sm-5">
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="gender" id="gender1" value="1" disabled readonly required>
                            <label class="form-check-label" for="gender1">男</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="gender" id="gender2" value="2" disabled readonly required>
                            <label class="form-check-label" for="gender2">女</label>
                        </div>
                    </div>
                </fieldset>
                <div class="form-group row">
                    <label for="inputBirthday" class="col-sm-3">出生日期</label>
                    <input type="date" class="form-control-plaintext col-sm-5" id="inputBirthday" aria-describedby="birthdayHelp" name="birthday" placeholder="yyyy-mm-dd" pattern="\d{4}-\d{2}-\d{2}" readonly required>
                    <small id="birthdayHelp" class="form-text text-muted col-sm-4">*西元年 = 民國年 + 1911</small>
                </div>
                <div class="form-group row">
                    <label class="col-sm-3">通訊地址</label>
                    <div class="col-sm-9">
                        <div class="row form-group">
                            <label for="inputZipcode" class="col-sm-2" style="min-width: 7rem;">郵遞區號：</label>
                            <input type="text" class="form-control-plaintext col-sm-3" id="inputZipcode" aria-describedby="zipcodeHelp" pattern="\d{5}\d{0,1}" name="zipcode" readonly required>
                            <small id="zipcodeHelp" class="form-text text-muted col-sm-5">
                                <span style="color:red">(請輸入半形數字)</span>
                                <a href="https://www.post.gov.tw/post/internet/Postal/index.jsp?ID=208" target="_blank" style="word-break:keep-all">郵遞區號查詢</a>
                            </small>
                        </div>
                        <div class="row form-group">
                            <label for="inputAddress" class="col-sm-2" style="min-width: 7rem;">地址：</label>
                            <input type="text" class="form-control-plaintext col-xl" id="inputAddress" name="address" readonly required>
                        </div>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-3">戶籍地址</label>
                    <div class="col-sm-9">
                        <div class="row form-group">
                            <label for="inputZipcode2" class="col-sm-2" style="min-width: 7rem;">郵遞區號：</label>
                            <input type="text" class="form-control-plaintext col-sm-3" id="inputZipcode2" aria-describedby="zipcode2Help" pattern="\d{5}\d{0,1}" name="zipcode2" readonly required>
                            <small id="zipcode2Help" class="form-text text-muted col-sm-5">
                                <span style="color:red">(請輸入半形數字)</span>
                                <a href="https://www.post.gov.tw/post/internet/Postal/index.jsp?ID=208" target="_blank" style="word-break:keep-all">郵遞區號查詢</a>
                            </small>
                        </div>
                        <div class="row form-group">
                            <label for="inputAddress2" class="col-sm-2" style="min-width: 7rem;">地址：</label>
                            <input type="text" class="form-control-plaintext col-xl" id="inputAddress2" name="address2" readonly required>
                        </div>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="inputTel" class="col-sm-3">聯絡電話</label>
                    <div class="col-xl">
                        <div class="row form-group">
                            <label for="inputTel_h" class="col-sm-2" style="min-width: 7rem;">住家：</label>
                            <div class="row col-sm align-items-center">
                                (&nbsp;<input type="text" class="form-control-plaintext col-sm-2" style="max-width: 3rem;" name="tel_h_a" readonly required>&nbsp;)&nbsp;
                                <input type="text" class="form-control-plaintext col-sm-3" style="max-width: 10rem;" id="inputTel_h" name="tel_h" readonly required>
                            </div>
                        </div>
                        <div class="row form-group">
                            <label for="inputTel_o" class="col-sm-2" style="min-width: 7rem;">公司：</label>
                            <div class="row col-sm align-items-center">
                                (&nbsp;<input type="text" class="form-control-plaintext col-sm-2" style="max-width: 3rem;" name="tel_o_a" readonly>&nbsp;)&nbsp;
                                <input type="text" class="form-control-plaintext col-sm-3" style="max-width: 10rem;" id="inputTel_o" name="tel_o" readonly>
                            </div>
                        </div>
                        <div class="row form-group">
                            <label for="inputTel_m" class="col-sm-2" style="min-width: 7rem;">手機：</label>
                            <input type="tel" class="form-control-plaintext col-sm-3" id="inputTel_m" pattern="09\d{8}" placeholder="09xxxxxxxx" name="tel_m" readonly required>
                        </div>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="inputEmail" class="col-sm-3">Email信箱</label>
                    <input type="email" class="form-control-plaintext col-sm-5" id="inputEmail" name="email" placeholder="example@gmail.com" readonly disabled>
                </div>
                <div class="form-group row">
                    <label class="col-sm-3">緊急連絡人</label>
                    <div class="row col-xl">
                        <div class="form-group col-sm-4" style="min-width: 10rem;">
                            <label for="inputConn_name" class="">姓名：</label>
                            <input type="text" class="form-control-plaintext col-sm-11" id="inputConn_name" name="conn_name" readonly required>
                        </div>
                        <div class="form-group col-sm-4" style="min-width: 10rem;">
                            <label for="inputConn_tel" class="">電話：</label>
                            <input type="tel" class="form-control-plaintext col-sm-11" id="inputConn_tel" name="conn_tel" readonly required>
                        </div>
                        <div class="form-group col-sm-4" style="min-width: 10rem;">
                            <label for="inputConn_rel" class="">關係：</label>
                            <input type="text" class="form-control-plaintext col-sm-11" id="inputConn_rel" name="conn_rel" readonly required>
                        </div>
                    </div>
                </div>
                <hr />
                <fieldset class="form-group row">
                    <legend class="col-form-label col-sm-3 float-sm-left">應考學歷</legend>
                    <div class="col-xl">
                        <div class="row col form-group">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="prove_type" id="prove1" value="1" href="#tab_prove1" disabled readonly required>
                                <label class="form-check-label" for="prove1">學士學位</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="prove_type" id="prove2" value="2" href="#tab_prove2" disabled readonly required>
                                <label class="form-check-label" for="prove2">同等學力</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="prove_type" id="prove3" value="3" href="#tab_prove3" disabled readonly required>
                                <label class="form-check-label" for="prove3">國家考試及格</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="prove_type" id="prove4" value="4" href="#tab_prove4" disabled readonly required>
                                <label class="form-check-label" for="prove4">技能檢定合格</label>
                            </div>
                        </div>
                        <div class="row col form-group tab-content" id="proveTabContent">
                            <div class="tab-pane fade" id="tab_prove1" role="tabpanel" aria-labelledby="prove1" style="width: 100%;">
                                <div class="card p-4">
                                    <div class="row form-group">
                                        <label for="inputGrad_schol" class="col-sm-2" style="min-width: 7rem;">學校名稱：</label>
                                        <div class="row col-sm align-items-center">
                                            <input type="text" class="form-control-plaintext col-sm" id="inputGrad_schol" name="grad_schol" readonly required>
                                        </div>
                                    </div>
                                    <div class="row form-group">
                                        <label for="inputGrad_dept" class="col-sm-2" style="min-width: 7rem;">科系：</label>
                                        <div class="row col-sm align-items-center">
                                            <input type="text" class="form-control-plaintext col-sm" id="inputGrad_dept" name="grad_dept" readonly required>
                                        </div>
                                    </div>
                                    <div class="row form-group">
                                        <label for="inputGrad_date" class="col-sm-2" style="min-width: 7rem;">畢業年月：</label>
                                        <input style="min-width: 10rem;" type="month" class="form-control-plaintext col-sm-3" aria-describedby="grad_dateHelp" id="inputGrad_date" placeholder="yyyy-mm" pattern="(1\d{3}|2\d{3})-(0[1-9]|1[0-2])" name="grad_date" readonly required>
                                        <small id="grad_dateHelp" class="form-text text-muted col-sm">(yyyy-mm)<br>*西元年 = 民國年 + 1911</small>
                                    </div>
                                    <div class="form-group">
                                        <span style="color:red">※學校名稱及科系請填寫全銜</span><br>
                                        <span style="color:red">※應屆畢業生（109年6月畢業）請點選「學士學位」</span>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="tab_prove2" role="tabpanel" aria-labelledby="prove2" style="width: 100%;">
                                <div class="card p-4">
                                    <div class="row form-group">
                                        <label for="inputac_school" class="col-sm-2" style="min-width: 7rem;">學校名稱：</label>
                                        <div class="row col-sm align-items-center">
                                            <input type="text" class="form-control-plaintext col-sm" id="inputac_school" name="ac_school" readonly required>
                                        </div>
                                    </div>
                                    <div class="row form-group">
                                        <label for="inputac_school_type" class="col-sm-2" style="min-width: 7rem;">類型：</label>
                                        <select id="inputac_school_type" class="form-control-plaintext col-sm" name="ac_school_type" readonly>
                                            <option selected hidden disabled></option>
                                            <option value="1">大學</option>
                                            <option value="2">三專</option>
                                            <option value="3">二專或五專</option>
                                        </select>
                                    </div>

                                    <div class="row form-group">
                                        <label for="inputAc_dept" class="col-sm-2" style="min-width: 7rem;">科系：</label>
                                        <div class="row col-sm align-items-center">
                                            <input type="text" class="form-control-plaintext col-sm" id="inputAc_dept" name="ac_dept" readonly required>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <span style="color:red">※學校名稱及科系請填寫全銜</span><br>
                                    </div>
                                    <div class="  form-group align-items-center" style="padding-left: 15px;">
                                        <div class="row form-group align-items-center">
                                            於&nbsp;<input type="month" style="max-width: 80%;min-width:10rem" class="form-control-plaintext col-sm-4" aria-describedby="ac_dateHelp" placeholder="yyyy-mm" pattern="(1\d{3}|2\d{3})-(0[1-9]|1[0-2])" name="ac_date" readonly required>&emsp;
                                        </div>
                                        <div class="row form-group align-items-center">
                                            &emsp;&nbsp;
                                            <select style="max-width: 80%;" class="form-control-plaintext col-sm-4" name="ac_g" readonly required>
                                                <option value="1">畢業</option>
                                                <option value="2">肄業</option>
                                            </select>，
                                        </div>
                                        <div class=" form-group align-items-center">
                                            <small id="ac_dateHelp" style="max-width: 11.5rem;" class="form-text text-muted col-sm">(yyyy-mm)<br>*西元年 = 民國年 + 1911</small>
                                        </div>
                                    </div>
                                    <div class="row  form-group align-items-center " style="padding-left: 15px;">
                                        <div class="col form-group row align-items-center" style="min-width: 12rem;max-width: 12rem;">
                                            修業&nbsp;<input type="number" style="max-width: 5rem;" class="form-control-plaintext col-sm-3" min="0" step="1" pattern="\d" name="ac_m_y" readonly required>&nbsp;年，
                                        </div>
                                        <div class="col form-group row align-items-center" style="min-width: 13rem;">
                                            已離校&nbsp;<input type="number" style="max-width: 5rem;" class="form-control-plaintext col-sm-3" min="0" step="1" pattern="\d" name="ac_leave_y" readonly required>&nbsp;年。
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </fieldset>
                <div class="form-group row">
                    <label class="col-sm-3">繳驗證件</label>
                    <div class="col-sm-9 color-info line-height-1">
                        <ol>
                            <li>以同等學力報考者，須將同等學力證件影本於報名截止前寄送達本校招生委員會審查。未繳驗證件者，如獲錄取，於報到時學力證件審查不合格，須撤銷錄取資格，不得異議。</li>
                            <li>招生系所如須郵寄或上傳備審資料，請依招生簡章【重點項目一覽表】之「資料審查繳交方式」辦理。</li>
                        </ol>
                    </div>
                </div>
                <div class="form-group row" id="upload_row" style="display: none;">
                    <label for="inputData" class="col-sm-3">備審資料上傳</label>
                    <div class="form-control-file col-sm-4"><a id="fileLink" target="_blank" style="color:red"></a></div>
                </div>
                <hr />
                <div class="line-height-1">
                    <span style="color:red">請確認您的資料，正確請按"下一步"繼續，修改資料請按"上一步"。</span>
                </div>
                <div class="row justify-content-center">
                    <button type="reset" style="min-width:4rem" class="btn btn-danger btn-sm col-1 mx-1">清除</button>
                    <button type="button" style="min-width:4rem" class="btn btn-warning btn-sm col-1 mx-1" onclick="window.location.replace('./alter.php?step=2');">上一步</button>
                    <button type="submit" style="min-width:4rem" class="btn btn-primary btn-sm col-1 mx-1">下一步</button>
                </div>
            </form>
        </div>
    </section>


    <?php require_once("./module/footer.php") ?>

    <script>
        $(function() {
            $("form [name='dept']").empty().append(sessionStorage.getItem('dept'));
            $("form [name='organize_id']").empty().append(sessionStorage.getItem('organize_id'));
            $("form [name='orastatus_id']").empty().append(sessionStorage.getItem('orastatus_id'));
            $("#subject").replaceWith(sessionStorage.getItem('subject'));
            $("#union").replaceWith(sessionStorage.getItem('union'));
            $("#upload_row").replaceWith(sessionStorage.getItem('upload_row'));
            $("form [name='file']").remove();
            fillForm(getSessionItems('alter'));
            $("form select option").not(":selected").remove().end();
            if ($("form [name='place']:checked").val() === "2")
                $("form [name='place'][value='2']").parent().css('display', '');
            $("form .form-control").addClass('form-control-plaintext').removeClass('form-control');
        });

        //應考學歷
        $("form [name='prove_type']").on('change', function() {
            $("#proveTabContent .tab-pane").removeClass("active");
            $("#proveTabContent .tab-pane").removeClass("show");
            $("form [name='prove_type']").removeClass("active");
            $("form [name='prove_type']").removeClass("active");
            $(this).tab('show');

            $("form #proveTabContent input").removeAttr('required')
            $("form #proveTabContent input").attr('disabled', true);
            $("form #proveTabContent select").removeAttr('required')
            $("form #proveTabContent select").attr('disabled', true);

            $("form #proveTabContent .active input").removeAttr('disabled')
            $("form #proveTabContent .active input").attr('required', true);
            $("form #proveTabContent .active select").removeAttr('disabled')
            $("form #proveTabContent .active select").attr('required', true);
        });

        //"同上"按鈕
        $("#address2_btn").on('click', function() {
            $("form [name='zipcode2']").val($("form [name='zipcode']").val());
            $("form [name='address2']").val($("form [name='address']").val());
        });

        //initail
        $(function() {
            if ($("form [name='disabled']:checked").val() === "1") {
                $("#disabled_extra").css('display', '');
                $("form [name='disabled_type']").attr('required', true);
                $("form [name='disabled_type']").removeAttr('disabled');
            } else {
                $("#disabled_extra").css('display', 'none');
                $("form [name='disabled_type']").removeAttr('required');
                $("form [name='disabled_type']").attr('disabled', true);
            }

            if ($("form [name='disabled_type']").val() === "6") {
                $("form [name='comments']").css('display', '');
                $("form [name='comments']").attr('required', true);
                $("form [name='comments']").removeAttr('disabled');
            } else {
                $("form [name='comments']").css('display', 'none');
                $("form [name='comments']").removeAttr('required');
                $("form [name='comments']").attr('disabled', true);
            }
        });


        $("form").on('submit', function(e) {
            e.preventDefault();
            $("form [type='submit']").attr('disabled', true);
            $(window).on('beforeunload', function() {
                return confirm('資料上傳中，您確定要離開此網頁嗎？');
            });
            $.ajax({
                    type: 'PUT',
                    url: "./API/signup/form.php",
                    data: $("form").serialize(),
                    dataType: 'json'
                }).done(function(response) {
                    alert("修改成功！");
                    $(window).off('beforeunload');
                    window.location.replace('./');

                })
                .fail(function(jqXHR, exception) {
                    $("form [type='submit']").removeAttr('disabled');
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
                    $(window).off('beforeunload');

                });

        });
    </script>

</body>

</html>