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
        $.holdReady(true);
        var deptObj;
        if (sessionStorage === undefined) {
            alert("未支援Web Storage！\n請更換瀏覽器再試。");
            window.location.replace('./');
        } else if (!sessionStorage.hasOwnProperty('agree') || sessionStorage.getItem('agree') !== "true")
            window.location.replace('./signup.php?step=2');
        else {
            $.when(getData("./API/dept/list.php")).done(function(_deptObj) {
                $.holdReady(false);
                deptObj = _deptObj.data;
                $(function() {
                    // fill department list
                    $("form [name='dept']").find('option').remove().end().append('<option selected hidden disabled></option>');
                    for (let i = 0; i < deptObj.dept.length; i++)
                        $("form [name='dept']").append("<option value='" + deptObj.dept[i].dept_id + "'>" + deptObj.dept[i].name + "</option>");

                    let formData = getSessionItems('signup');
                    fillForm(formData);
                });
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
                        <select id="inputOrganize" class="form-control" name="organize_id" required>
                            <option selected hidden disabled></option>
                        </select>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="inputStatus">報考身分</label>
                        <select id="inputStatus" class="form-control" name="orastatus_id" required>
                            <option selected hidden disabled></option>
                        </select>
                    </div>
                    <div class="form-group col-md-6">
                    </div>
                </div>
                <div class="form-group row" id="subject" style="display: none;">
                    <label class="col-sm-3">選考科目</label>
                    <div class="col-sm-6">
                    </div>
                </div>

                <fieldset class="form-group row">
                    <legend class="col-form-label col-sm-3 float-sm-left" style="min-width: 9rem;"><span style="color:red">身心障礙考生</span></legend>
                    <div class="col-xl row mx-0">
                        <div style="max-width: 10rem;">
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
                            <input class="form-check-input" type="radio" id="place" name="place" value="1" checked required>
                            <label class="form-check-label color-info" for="place">彰化考區</label>
                        </div>
                        <div class="form-check form-check-inline" style="display: none;">
                            <input class="form-check-input" type="radio" id="place" name="place" value="2" required>
                            <label class="form-check-label color-info" for="place">台北考區</label>
                        </div>
                    </div>
                </fieldset>
                <hr />
                <div class="form-group row">
                    <label for="inputName" class="col-sm-3">姓名</label>
                    <input type="text" class="form-control col-sm-5" id="inputName" name="name" required>
                </div>
                <!--<div class="form-group row">
                    <label for="inputIDNumber" class="col-sm-3">身分證字號</label>
                    <input type="text" class="form-control col-sm-5" id="inputIDNumber" aria-describedby="IDNumberHelp" pattern="[A-Z]\d{9}" name="id" required>
                    <small id="IDNumberHelp" class="form-text text-muted col-sm-4">*僑外生請填寫居留證號碼</small>
                </div>-->
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
                                (&nbsp;<input type="text" class="form-control col-sm-2" style="max-width: 3rem;" name="tel_o_a">&nbsp;)&nbsp;
                                <input type="text" class="form-control col-sm-3" style="max-width: 10rem;" id="inputTel_o" name="tel_o">
                            </div>
                        </div>
                        <div class="row form-group">
                            <label for="inputTel_m" class="col-sm-2" style="min-width: 7rem;">手機：</label>
                            <input type="tel" class="form-control col-sm-3" id="inputTel_m" pattern="09\d{8}" placeholder="09xxxxxxxx" name="tel_m" required>
                        </div>
                    </div>
                </div>
                <!--<div class="form-group row">
                    <label for="inputEmail" class="col-sm-3">Email信箱</label>
                    <input type="email" class="form-control col-sm-5" id="inputEmail" name="email" placeholder="example@gmail.com" required>
                </div>-->
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
                                <input class="form-check-input" type="radio" name="prove_type" id="prove1" value="1" href="#tab_prove1" required>
                                <label class="form-check-label" for="prove1">學士學位</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="prove_type" id="prove2" value="2" href="#tab_prove2" required>
                                <label class="form-check-label" for="prove2">同等學力</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="prove_type" id="prove3" value="3" href="#tab_prove3" required>
                                <label class="form-check-label" for="prove3">國家考試及格</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="prove_type" id="prove4" value="4" href="#tab_prove4" required>
                                <label class="form-check-label" for="prove4">技能檢定合格</label>
                            </div>
                        </div>
                        <div class="row col form-group tab-content" id="proveTabContent">
                            <div class="tab-pane fade" id="tab_prove1" role="tabpanel" aria-labelledby="prove1" style="width: 100%;">
                                <div class="card p-4">
                                    <div class="row form-group">
                                        <label for="inputGrad_schol" class="col-sm-2" style="min-width: 7rem;">學校名稱：</label>
                                        <div class="row col-sm align-items-center">
                                            <input type="text" class="form-control col-sm" id="inputGrad_schol" name="grad_schol" required>
                                        </div>
                                    </div>
                                    <div class="row form-group">
                                        <label for="inputGrad_dept" class="col-sm-2" style="min-width: 7rem;">科系：</label>
                                        <div class="row col-sm align-items-center">
                                            <input type="text" class="form-control col-sm" id="inputGrad_dept" name="grad_dept" required>
                                        </div>
                                    </div>
                                    <div class="row form-group">
                                        <label for="inputGrad_date" class="col-sm-2" style="min-width: 7rem;">畢業年月：</label>
                                        <input style="min-width: 10rem;" type="month" class="form-control col-sm-3" aria-describedby="grad_dateHelp" id="inputGrad_date" placeholder="yyyy-mm" pattern="(1\d{3}|2\d{3})-(0[1-9]|1[0-2])" name="grad_date" required>
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
                                            <input type="text" class="form-control col-sm" id="inputac_school" name="ac_school" required>
                                        </div>
                                    </div>
                                    <div class="row form-group">
                                        <label for="inputac_school_type" class="col-sm-2" style="min-width: 7rem;">類型：</label>
                                        <select id="inputac_school_type" class="form-control col-sm" name="ac_school_type" required>
                                            <option selected hidden disabled></option>
                                            <option value="1">大學</option>
                                            <option value="2">三專</option>
                                            <option value="3">二專或五專</option>
                                        </select>
                                    </div>

                                    <div class="row form-group">
                                        <label for="inputAc_dept" class="col-sm-2" style="min-width: 7rem;">科系：</label>
                                        <div class="row col-sm align-items-center">
                                            <input type="text" class="form-control col-sm" id="inputAc_dept" name="ac_dept" required>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <span style="color:red">※學校名稱及科系請填寫全銜</span><br>
                                    </div>
                                    <div class="  form-group align-items-center" style="padding-left: 15px;">
                                        <div class="row form-group align-items-center">
                                            於&nbsp;<input type="month" style="max-width: 80%;min-width:10rem" class="form-control col-sm-4" aria-describedby="ac_dateHelp" placeholder="yyyy-mm" pattern="(1\d{3}|2\d{3})-(0[1-9]|1[0-2])" name="ac_date" required>&emsp;
                                        </div>
                                        <div class="row form-group align-items-center">
                                            &emsp;&nbsp;
                                            <select style="max-width: 80%;" class="form-control col-sm-4" name="ac_g" required>
                                                <option value="1">畢業</option>
                                                <option value="2">肄業</option>
                                            </select>，
                                        </div>
                                        <div class=" form-group align-items-center">
                                            <small id="ac_dateHelp" style="max-width: 11.5rem;" class="form-text text-muted col-sm">(畢/肄業年月：yyyy-mm)<br>*西元年 = 民國年 + 1911</small>
                                        </div>
                                    </div>
                                    <div class="row  form-group align-items-center " style="padding-left: 15px;">
                                        <div class="col form-group row align-items-center" style="min-width: 12rem;max-width: 12rem;">
                                            修業&nbsp;<input type="number" style="max-width: 5rem;" class="form-control col-sm-3" min="0" step="1" pattern="\d" name="ac_m_y" required>&nbsp;年，
                                        </div>
                                        <div class="col form-group row align-items-center" style="min-width: 13rem;">
                                            已離校&nbsp;<input type="number" style="max-width: 5rem;" class="form-control col-sm-3" min="0" step="1" pattern="\d" name="ac_leave_y" required>&nbsp;年。
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


    <script>
        //報考系所
        $("form [name='dept']").on('change', function() {
            $("#subject").css('display', 'none');
            $("form [name='organize_id']").find('option').remove().end().append('<option selected hidden disabled></option>');
            $("form [name='orastatus_id']").find('option').remove().end().append('<option selected hidden disabled></option>');
            for (let i = 0; i < deptObj.group[$("form [name='dept']").val()].length; i++)
                $("form [name='organize_id']").append("<option value='" + deptObj.group[$("form [name='dept']").val()][i].group_id + "'>" + deptObj.group[$("form [name='dept']").val()][i].name + "</option>");

            //upload_type 審查資料繳交方式:  1:郵寄  2:上傳  3:郵寄+上傳
            for (let i = 0; i < deptObj.dept.length; i++) {
                if (deptObj.dept[i].dept_id === $("form [name='dept']").val()) {
                    if (deptObj.dept[i].upload_type > 1) {
                        $("#upload_row").css('display', '');
                        $("form [name='file']").removeAttr('disabled');
                    } else {
                        $("#upload_row").css('display', 'none');
                        $("form [name='file']").attr('disabled', true);
                    }
                    if (deptObj.dept[i].e_place === 1) //限彰化考區
                    {
                        $("form [name='place'][value='1']")[0].checked = true;
                        $("form [name='place'][value='2']").parent().css('display', 'none');
                        $("form [name='place'][value='2']").attr('disabled', true);
                    } else {
                        $("form [name='place'][value='2']").parent().css('display', '');
                        $("form [name='place'][value='2']").removeAttr('disabled');
                    }
                    break;
                }
            }
        });
        $("form [name='organize_id']").on('change', function() {
            $("form [name='orastatus_id']").find('option').remove().end().append('<option selected hidden disabled></option>');
            for (let i = 0; i < deptObj.status[$("form [name='dept']").val()][$("form [name='organize_id']").val()].length; i++)
                $("form [name='orastatus_id']").append("<option value='" + deptObj.status[$("form [name='dept']").val()][$("form [name='organize_id']").val()][i].status_id + "'>" + deptObj.status[$("form [name='dept']").val()][$("form [name='organize_id']").val()][i].name + "</option>");

        });
        $("form [name='orastatus_id']").on('change', function() {
            //同一section或有多個subjects，即表示可選考科
            let isOptional = false;
            $("#subject").css('display', 'none');
            $("#subject>div").empty();
            let keys = Object.keys(deptObj.subject[$("form [name='dept']").val()][$("form [name='organize_id']").val()][$("form [name='orastatus_id']").val()]);
            for (let i = 0; i < keys.length; i++) {
                let options = "<option selected hidden disabled></option>";
                let subject_count = deptObj.subject[$("form [name='dept']").val()][$("form [name='organize_id']").val()][$("form [name='orastatus_id']").val()][keys[i]].length;

                if (subject_count > 1) //有1個以上考科才顯示選擇
                {
                    isOptional = true;
                    for (let j = 0; j < subject_count; j++) {
                        let subject_id = deptObj.subject[$("form [name='dept']").val()][$("form [name='organize_id']").val()][$("form [name='orastatus_id']").val()][keys[i]][j].subject_id;
                        let subject_name = deptObj.subject[$("form [name='dept']").val()][$("form [name='organize_id']").val()][$("form [name='orastatus_id']").val()][keys[i]][j].name;
                        options += "<option value='" + subject_id + "'>" + subject_name + "</option>";

                    }
                    $("#subject>div").append('<select class="form-control form-group" name="subject" required>' + options + '</select>');
                }
            }
            if (isOptional)
                $("#subject").css('display', '');
        });


        //身心障礙
        $("form [name='disabled']").on('change', function() {
            if (this.value === '1') {
                $("#disabled_extra").css('display', '');
                $("form [name='disabled_type']").attr('required', true);
                $("form [name='disabled_type']").removeAttr('disabled');
            } else {
                $("#disabled_extra").css('display', 'none');
                $("form [name='disabled_type']").removeAttr('required');
                $("form [name='disabled_type']").attr('disabled', true);
            }
        });

        $("form [name='disabled_type']").on('change', function() {
            if (this.value === '6') {
                $("form [name='comments']").css('display', '');
                $("form [name='comments']").attr('required', true);
                $("form [name='comments']").removeAttr('disabled');
            } else {
                $("form [name='comments']").css('display', 'none');
                $("form [name='comments']").removeAttr('required');
                $("form [name='comments']").attr('disabled', true);
            }
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



        //備審資料上傳狀態
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
                $('form #fileLink').attr('href', './API/signup/file.php?export=download');

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
        });

        //備審資料上傳
        $("form [name='file']").on('change', function() {

            $('form #fileLink').removeClass('color-info');
            $('form #fileLink').css('color', 'red');
            $('form #fileLink').text('備審資料檔案尚未上傳');
            $('form #fileLink').removeAttr('href');

            var fd = new FormData();
            var files = $(this)[0].files;

            // Check file selected or not
            if (files.length > 0) {
                $("form [name='file']").attr('disabled', true);
                $("form [type='submit']").attr('disabled', true);
                $(window).on('beforeunload', function() {
                    return confirm('資料上傳中，您確定要離開此網頁嗎？');
                });

                toastr.clear();
                toastr.info("檔案上傳中");
                fd.append('file', files[0]);

                $.ajax({
                        url: './API/signup/file.php',
                        type: 'POST',
                        data: fd,
                        contentType: false,
                        processData: false
                    }).done(function(response) {
                        toastr.clear();
                        toastr.success("檔案上傳成功成功！");
                        $('form #fileLink').css('color', '');
                        $('form #fileLink').addClass('color-info');
                        $('form #fileLink').text('檔案已上傳');
                        $('form #fileLink').attr('href', './API/signup/file.php');
                        $(window).off('beforeunload');
                        $("form [name='file']").removeAttr('disabled');
                        $("form [type='submit']").removeAttr('disabled');

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
                        $(window).off('beforeunload');
                        $("form [name='file']").removeAttr('disabled');
                        $("form [type='submit']").removeAttr('disabled');

                    });
            }
        });


        $("form").on('submit', function(e) {
            e.preventDefault();


            sessionStorage.setItem("signup", $("form").serialize());
            sessionStorage.setItem("dept", $("form [name='dept']>option[value=" + $("form [name='dept']").val() + "]").text());
            sessionStorage.setItem("organize_id", $("form [name='organize_id']>option[value=" + $("form [name='organize_id']").val() + "]").text());
            sessionStorage.setItem("orastatus_id", $("form [name='orastatus_id']>option[value=" + $("form [name='orastatus_id']").val() + "]").text());
            window.location.replace('./signup.php?step=4');



        });
    </script>


</body>

</html>