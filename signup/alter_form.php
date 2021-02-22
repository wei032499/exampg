<!doctype html>
<html lang="zh-Hant-TW">

<head>
    <meta charset="utf-8">
    <title>網路報名</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/x-icon" href="./images/favicon.ico">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css" integrity="sha384-B0vP5xmATw1+K9KRQjQERJvTumQW0nPEzvF6L/Z6nronJ3oUOFUFpCjEUQouq2+l" crossorigin="anonymous">
    <link rel="stylesheet" href="./css/custom.css" />
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-Piv4xVNRyMGpqkS2by6br4gNJ7DXjqk09RmUpJ8jgGtD7zP9yug3goQfGII0yAns" crossorigin="anonymous"></script>
    <script src="./js/common.js"></script>

    <script>
        $.holdReady(true);
        var deptObj, formData = getSessionItems('alter');
        if (sessionStorage === undefined) {
            alert("未支援Web Storage！\n請更換瀏覽器再試。");
            window.location.replace('./');
        } else {
            $.when(getData("./API/signup/form.php", false), getData("./API/dept/list.php")).done(function(_formData, _deptObj) {
                deptObj = _deptObj[0].data;
                if (sessionStorage.getItem("alter") === null)
                    formData = _formData[0].data;

                $.holdReady(false);

            });

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
                <div class="row ">
                    <h3 class="col-sm" style="letter-spacing: 0.2rem">
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
                        <select id="inputDept" class="form-control" name="dept" required>
                            <option selected disabled hidden></option>
                        </select>
                    </div>
                    <div class="form-group col-md-6">
                        <label for="inputOrganize">報考組(科)別</label>
                        <select id="inputOrganize" class="form-control" name="organize_id" required>
                            <option selected disabled hidden></option>
                        </select>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="inputStatus">報考身分</label>
                        <select id="inputStatus" class="form-control" name="orastatus_id" required>
                            <option selected disabled hidden></option>
                        </select>
                    </div>
                    <div class="form-group col-md-6" id="subject" style="visibility: hidden;">
                        <label>選考科目
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
                        <div class="col-sm " style="max-width: 10rem;padding-left:0px">
                            <div class="form-check form-check-inline form-group">
                                <input class="form-check-input" type="radio" id="disabled1" name="disabled" value="1" required>
                                <label class="form-check-label" for="disabled1"><span style="color:red">是</span></label>
                            </div>
                            <div class="form-check form-check-inline form-group">
                                <input class="form-check-input" type="radio" id="disabled2" name="disabled" value="0" checked required>
                                <label class="form-check-label font-weight-bold" for="disabled2"><span style="color:red"><u>否</u></span></label>
                            </div>
                        </div>
                        <div class="col-sm " id="disabled_extra" style="display: none;">
                            <select class="form-control form-group" name="disabled_type">
                                <option selected hidden disabled></option>
                                <option value="1">聽覺障礙</option>
                                <option value="2">視覺障礙</option>
                                <option value="3">腦性麻庳</option>
                                <option value="4">自閉症</option>
                                <option value="5">學習障礙</option>
                                <option value="6">其他障礙</option>
                            </select>
                            <input class="form-control form-group" type="text" name="comments" placeholder="請填入說明" style="display: none;">
                        </div>
                    </div>
                </fieldset>
                <fieldset class="form-group row">
                    <legend class="col-form-label col-sm-3 float-sm-left" style="min-width: 9rem;"><span style="color:red">報名考區</span></legend>
                    <div class="col-sm-5 row mx-0">
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" id="place1" name="place" value="1" checked required>
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
                    <div class="col-sm-5">
                        <input type="text" class="form-control " id="inputName" name="name" required>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="inputIDNumber" class="col-sm-3">
                        身分證字號<br>
                        <small id="IDNumberHelp" class="form-text text-muted ">*僑外生居留證號碼</small>
                    </label>
                    <div class="col-sm-5">
                        <input type="text" class="form-control " id="inputIDNumber" aria-describedby="IDNumberHelp" pattern="[A-Z]\d{9}" name="id" required>
                    </div>

                </div>
                <fieldset class="form-group row">
                    <legend class="col-form-label col-sm-3 float-sm-left">性別</legend>
                    <div class="col-sm-5">
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="sex" id="sex1" value="1" aria-describedby="sexErrMsg" required>
                            <label class="form-check-label" for="sex1">男</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="sex" id="sex2" value="0" aria-describedby="sexErrMsg" required>
                            <label class="form-check-label" for="sex2">女</label>
                        </div>
                        <span class="error" id="sexErrMsg"></span>
                    </div>
                </fieldset>
                <div class="form-group row">
                    <label for="inputBirthday" class="col-sm-3">出生日期<br>
                        <small id="birthdayHelp" class="form-text text-muted">*西元年 = 民國年 + 1911</small>
                    </label>
                    <div class="col-sm-5">
                        <input type="date" class="form-control " id="inputBirthday" aria-describedby="birthdayHelp" name="birthday" placeholder="yyyy-mm-dd" pattern="\d{4}-\d{2}-\d{2}" required>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-3">通訊地址</label>
                    <div class="col-sm-9">
                        <div class="row form-group">
                            <label for="inputZipcode" class="col-sm-2" style="min-width: 7rem;">郵遞區號：</label>
                            <div class="col-sm-3">
                                <input type="text" class="form-control " id="inputZipcode" aria-describedby="zipcodeHelp" pattern="\d{5}\d{0,1}" name="zipcode" required>
                            </div>
                        </div>
                        <div class="row form-group">
                            <label for="inputAddress" class="col-sm-2" style="min-width: 7rem;">地址：</label>
                            <div class="col-xl">
                                <input type="text" class="form-control " id="inputAddress" name="address" required>
                            </div>
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
                            <div class="col-sm-3">
                                <input type="text" class="form-control " id="inputZipcode2" aria-describedby="zipcode2Help" pattern="\d{5}\d{0,1}" name="zipcode2" required>
                            </div>
                        </div>
                        <div class="row form-group">
                            <label for="inputAddress2" class="col-sm-2" style="min-width: 7rem;">地址：</label>
                            <div class="col-xl">
                                <input type="text" class="form-control " id="inputAddress2" name="address2" required>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="inputTel" class="col-sm-3">聯絡電話</label>
                    <div class="col-xl">
                        <div class="row form-group">
                            <label for="inputTel_h" class="col-sm-2" style="min-width: 7rem;">住家：</label>
                            <div class="row col-sm align-items-center" style="margin-left: 0px;">
                                (&nbsp;<input type="text" class="form-control col-sm-2" style="max-width: 3rem;" name="tel_h_a" pattern="\d+" required>&nbsp;)&nbsp;
                                <input type="text" class="form-control col-sm-3" style="max-width: 10rem;" id="inputTel_h" name="tel_h" pattern="\d+" required>
                            </div>
                        </div>
                        <div class="row form-group">
                            <label for="inputTel_o" class="col-sm-2" style="min-width: 7rem;">公司：</label>
                            <div class="row col-sm align-items-center" style="margin-left: 0px;">
                                (&nbsp;<input type="text" class="form-control col-sm-2" style="max-width: 3rem;" name="tel_o_a" pattern="\d+">&nbsp;)&nbsp;
                                <input type="text" class="form-control col-sm-3" style="max-width: 10rem;" id="inputTel_o" name="tel_o" pattern="\d+">
                            </div>
                        </div>
                        <div class="row form-group">
                            <label for="inputTel_m" class="col-sm-2" style="min-width: 7rem;">手機：</label>
                            <div class="col-sm-4">
                                <input type="tel" class="form-control " id="inputTel_m" pattern="09\d{8}" placeholder="09xxxxxxxx" name="tel_m" required>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="inputEmail" class="col-sm-3">Email信箱</label>
                    <div class="col-md-5">
                        <input type="email" class="form-control " id="inputEmail" name="email" placeholder="example@gmail.com" required>
                    </div>
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
                    <div class="col-xl">
                        <div class="row col form-group">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="prove_type" id="prove1" value="1" href="#tab_prove1" aria-describedby="proveErrMsg" required>
                                <label class="form-check-label" for="prove1">學士學位</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="prove_type" id="prove2" value="2" href="#tab_prove2" aria-describedby="proveErrMsg" required>
                                <label class="form-check-label" for="prove2">同等學力</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="prove_type" id="prove3" value="3" href="#tab_prove3" aria-describedby="proveErrMsg" required>
                                <label class="form-check-label" for="prove3">國家考試及格</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="prove_type" id="prove4" value="4" href="#tab_prove4" aria-describedby="proveErrMsg" required>
                                <label class="form-check-label" for="prove4">技能檢定合格</label>
                            </div>
                            <span class="error" id="proveErrMsg"></span>
                        </div>
                        <span style="color:red">※應屆畢業生請點選「學士學位」</span>
                        <div class="row col form-group tab-content" id="proveTabContent">
                            <div class="tab-pane fade" id="tab_prove1" role="tabpanel" aria-labelledby="prove1" style="width: 100%;">
                                <div class="card p-4">
                                    <div class="row form-group">
                                        <label for="inputGrad_schol" class="col-sm-2" style="min-width: 7rem;">學校名稱：</label>
                                        <div class=" col-sm align-items-center">
                                            <input type="text" class="form-control " id="inputGrad_schol" name="grad_schol" required>
                                            <span style="color:red">※學校名稱及科系請填寫全銜</span>
                                        </div>
                                    </div>
                                    <div class="row form-group">
                                        <label for="inputGrad_dept" class="col-sm-2" style="min-width: 7rem;">科系：</label>
                                        <div class=" col-sm align-items-center">
                                            <input type="text" class="form-control " id="inputGrad_dept" name="grad_dept" required>
                                        </div>
                                    </div>
                                    <div class="row form-group">
                                        <label for="inputGrad_date" class="col-sm-2" style="min-width: 7rem;">畢業年月：</label>
                                        <div class=" col-sm-3 align-items-center" style="min-width: 10rem;">
                                            <input type="month" class="form-control " aria-describedby="grad_dateHelp" id="inputGrad_date" placeholder="yyyy-mm" pattern="(1\d{3}|2\d{3})-(0[1-9]|1[0-2])" name="grad_date" required>
                                        </div>
                                        <small id="grad_dateHelp" class="form-text text-muted col-sm">(yyyy-mm)<br>*西元年 = 民國年 + 1911</small>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="tab_prove2" role="tabpanel" aria-labelledby="prove2" style="width: 100%;">
                                <div class="card p-4">
                                    <div class="row form-group">
                                        <label for="inputac_school" class="col-sm-2" style="min-width: 7rem;">學校名稱：</label>
                                        <div class=" col-sm align-items-center">
                                            <input type="text" class="form-control " id="inputac_school" name="ac_school" required>
                                            <span style="color:red">※學校名稱及科系請填寫全銜</span>
                                        </div>
                                    </div>
                                    <div class="row form-group">
                                        <label for="inputac_school_type" class="col-sm-2" style="min-width: 7rem;">類型：</label>
                                        <div class=" col-sm align-items-center">
                                            <select id="inputac_school_type" class="form-control " name="ac_school_type" required>
                                                <option selected hidden disabled></option>
                                                <option value="1">大學</option>
                                                <option value="2">三專</option>
                                                <option value="3">二專或五專</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="row form-group">
                                        <label for="inputAc_dept" class="col-sm-2" style="min-width: 7rem;">科系：</label>
                                        <div class=" col-sm align-items-center">
                                            <input type="text" class="form-control " id="inputAc_dept" name="ac_dept" required>
                                        </div>
                                    </div>
                                    <div class="  form-group align-items-center" style="padding-left: 15px;">
                                        <div class="row form-group align-items-center">
                                            於&nbsp;<input type="month" style="max-width:7.5rem;min-width:7.5rem" class="form-control col-sm-4" aria-describedby="ac_dateHelp" placeholder="yyyy-mm" pattern="(1\d{3}|2\d{3})-(0[1-9]|1[0-2])" name="ac_date" required>&nbsp;
                                            <select style="max-width:5rem;min-width:5rem" class="form-control col-sm-4" name="ac_g" required>
                                                <option value="1">畢業</option>
                                                <option value="2">肄業</option>
                                            </select>，
                                            <small id="ac_dateHelp" style="max-width: 11.5rem;" class="form-text text-muted col-sm">(yyyy-mm)<br>*西元年 = 民國年 + 1911</small>
                                        </div>
                                    </div>
                                    <div class="row  form-group align-items-center " style="padding-left: 15px;">
                                        <div class="col form-group row align-items-center" style="min-width: 12rem;max-width: 12rem;">
                                            修業&nbsp;<input type="number" style="min-width: 5rem;max-width: 5rem;" class="form-control col-sm-3" min="0" step="1" pattern="\d" name="ac_m_y" aria-describedby="acErrMsg" required>&nbsp;年，
                                        </div>
                                        <div class="col form-group row align-items-center" style="min-width: 13rem;max-width: 13rem;">
                                            已離校&nbsp;<input type="number" style="min-width: 5rem;max-width: 5rem;" class="form-control col-sm-3" min="0" step="1" pattern="\d" name="ac_leave_y" aria-describedby="acErrMsg" required>&nbsp;年。
                                        </div>
                                        <div class="col form-group row align-items-center" style="min-width: 6rem;"><span class="error" id="acErrMsg"></span></div>

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
                    <input type="file" class="form-control-file col-sm-4" id="inputData" name="file" disabled>
                    <div class="form-control-file col-sm-5"><a id="fileLink" target="_blank" style="color:red"></a></div>
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
                <div class="row justify-content-center">
                    <button type="reset" style="min-width:4rem" class="btn btn-danger btn-sm col-1 mx-1">清除</button>
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
    <script src="./js/signup.js"></script>
    <script>
        $(function() {
            fillForm(formData);
        });
        $("form").validate({
            errorElement: "span",
            messages: {
                birthday: {
                    pattern: "格式錯誤。西元年四碼-月份兩碼-日期兩碼(yyyy-mm-dd)"
                },
                zipcode: {
                    pattern: "格式錯誤。請填寫3+2或3+3郵遞區號"
                },
                zipcode2: {
                    pattern: "格式錯誤。請填寫3+2或3+3郵遞區號"
                },
                tel_m: {
                    pattern: "格式錯誤。09xxxxxxxx"
                },
                grad_date: {
                    pattern: "格式錯誤。西元年四碼-月份兩碼(yyyy-mm)"
                },
                ac_date: {
                    pattern: "格式錯誤。西元年四碼-月份兩碼(yyyy-mm)"
                }
            },
            submitHandler: function(form) {
                for (let i = 0; i < $("form [name='union_priority[]']").length; i++)
                    if ($("form [name='union_priority[]']").eq(i).val() === "-1")
                        if (confirm("有放棄的志願序，確定繼續嗎？"))
                            break;
                        else
                            return false;
                for (let i = 0; i < $("form [name='union_priority[]']").length; i++)
                    if ($("form [name='union_priority[]']").eq(i).val() !== "-1")
                        for (let j = i + 1; j < $("form [name='union_priority[]']").length; j++)
                            if ($("form [name='union_priority[]']").eq(i).val() === $("form [name='union_priority[]']").eq(j).val()) {
                                alert("不可選填重複的志願！");
                                return false;
                            }

                sessionStorage.setItem("alter", $("form").serialize());
                // sessionStorage.setItem("dept", $("form [name='dept']>option:checked").prop("outerHTML"));
                // sessionStorage.setItem("organize_id", $("form [name='organize_id']>option:checked").prop("outerHTML"));
                // sessionStorage.setItem("orastatus_id", $("form [name='orastatus_id']>option:checked").prop("outerHTML"));
                // sessionStorage.setItem("subject", $("#subject").prop("outerHTML"));
                // sessionStorage.setItem("union", $("#union").prop("outerHTML"));
                // sessionStorage.setItem("upload_row", $("#upload_row").prop("outerHTML"));



                window.location.replace('./alter.php?step=3');
            }

        });
    </script>


</body>

</html>