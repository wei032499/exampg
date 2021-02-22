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
        var formData;
        var urlParams = new URLSearchParams(window.location.search);

        $.when(getData("./API/signup/letter_fill.php", false, {
            token: urlParams.get('token')
        })).then(function(_formData) {
            formData = _formData.data;
            $.holdReady(false);
        }, function() {
            window.location.replace('./');
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
                    <h3 class="col" style="letter-spacing: 0.2rem;min-width:14rem">
                        :::招生考試入學推薦函
                    </h3>
                </div>
            </div>

            <div class="border p-4 bg-white shadow rounded">
                <div class="col-md-12">
                    <table class="table table-hover table-bordered">
                        <thead>
                            <tr class="bg-warning-light">
                                <th colspan="4" class="text-center">申請人基本資料</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="col-md-2 bg-info-light text-center">考生姓名</td>
                                <td class="col-md-4 text-primary text-center">
                                    <strong><span id="stud_name"></span></strong>
                                </td>
                                <td class="col-md-2 bg-info-light text-center">畢業學校及系所</td>
                                <td class="col-md-4 text-primary text-center">
                                    <strong><span id="ac_school_name"></span><span id="ac_dept_name"></span></strong>
                                </td>
                            </tr>
                            <tr>
                                <td class="col-md-2 bg-info-light text-center">報考系所</td>
                                <td class="col-md-4 text-primary text-center">
                                    <strong><span id="dept_name"></span></strong>
                                </td>
                                <td class="col-md-2 bg-info-light text-center">電話</td>
                                <td class="col-md-4 text-primary text-center">
                                    <strong><span id="tel_m"></span></strong>
                                </td>
                            </tr>
                            <tr>
                                <td class="col-md-2 bg-info-light text-center">擬研讀方向</td>
                                <td class="col-md-10 text-primary" colspan="3">
                                    <strong><span id="research"></span></strong>
                                </td>
                            </tr>

                        </tbody>
                    </table>
                </div>

                <hr style="border: 1px solid green;">

                <div class="col-md-12">
                    <div class="alert alert-warning">
                        <strong>說明：</strong> 本推薦函之目的在協助本系瞭解申請人過去的求學概況，以作為評審的參考。此項資料將不對外公開，感謝您的填寫。
                    </div>
                    <div class="">
                        <table class="table table-bordered">
                            <thead>
                                <tr class="bg-success-light">
                                    <th colspan="4" class="text-center">推薦人</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="col-md-2 bg-info-light text-center">推薦人姓名</td>
                                    <td class="col-md-4 text-center text-center">
                                        <strong><span id="r_name"></span></strong>
                                    </td>
                                    <td class="col-md-2 bg-info-light text-center">服務單位</td>
                                    <td class="col-md-4 text-center text-center">
                                        <strong><span id="r_org"></span></strong>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="col-md-2 bg-info-light text-center">E-mail</td>
                                    <td class="col-md-4 text-center text-center">
                                        <strong><span id="r_email"></span></strong>
                                    </td>
                                    <td class="col-md-2 bg-info-light text-center">職稱</td>
                                    <td class="col-md-4 text-center text-center">
                                        <strong><span id="r_title"></span></strong>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <h6 class="text-danger">(打 * 表示為必填)</h6>
                </div>
                <form method="POST" name="form1" id="form1" class="form-horizontal" action="">
                    <input type="hidden" id="token" name="token" value="">
                    <div class="form-group col-xs-12 col-sm-12 col-md-12 col-lg-12">
                        <div style='padding:5px;margin:5px' class='bg-info-light'><span class="text-danger">*
                            </span>1. <span style="font-weight:bold">您與申請人的關係：</span>
                        </div>
                        <label class="radio-inline">
                            <input name="apply_rel" type="radio" value='1' />
                            導師
                        </label>
                        <label class="radio-inline">
                            <input name="apply_rel" type="radio" value='2' />
                            大學部授課教師
                        </label>
                        <label class="radio-inline">
                            <input name="apply_rel" type="radio" value='3' />
                            申請人選修研究所課程的授課教師
                        </label>
                        <label class="radio-inline">
                            <input name="apply_rel" type="radio" value='4' />
                            申請人選修專題研究的指導教師
                        </label>
                        <label class="radio-inline">
                            <input name="apply_rel" type="radio" value='5' />
                            其他
                        </label>
                        <label for="apply_rel" class="error radio-inline"></label>
                    </div>
                    <div class="form-group col-xs-12 col-sm-12 col-md-12 col-lg-12">
                        <div style='padding:5px;margin:5px' class='bg-info-light'><span class="text-danger">*
                            </span>2. <span style="font-weight:bold">您認識申請人的時間：</span>
                            <label class="radio-inline">
                                <input name="apply_years" type="text" value='' size="2" id="apply_years" />
                                年
                            </label>
                            <label for="apply_years" class="error radio-inline"></label>
                        </div>
                    </div>

                    <div class="form-group col-xs-12 col-sm-12 col-md-12 col-lg-12">
                        <div style='padding:5px;margin:5px' class='bg-info-light'><span class="text-danger">*
                            </span>3. <span style="font-weight:bold">在您所教過的學生中，請就以下所列項目，勾選您認為最適合描述申請人的選項：</span>
                        </div>
                        <div class="form-group" style='padding:5px;margin:0px 3px'>
                            <div class="text-left story text-indent_2nd"> 　<span class="text-danger">*
                                </span>(1) <strong>品&nbsp;&nbsp;德&nbsp;&nbsp;操&nbsp;&nbsp;守&nbsp;</strong>：
                                　<input id="desc_1_1" name="apply_desc_1" type="radio" value="1" /><label for="desc_1_1">前10％以內</label>
                                　<input id="desc_1_2" name="apply_desc_1" type="radio" value="2" /><label for="desc_1_2">10～25％</label>
                                　<input id="desc_1_3" name="apply_desc_1" type="radio" value="3" /><label for="desc_1_3">25～50％</label>
                                　<input id="desc_1_4" name="apply_desc_1" type="radio" value="4" /><label for="desc_1_4">50～70％</label>
                                　<input id="desc_1_5" name="apply_desc_1" type="radio" value="5" /><label for="desc_1_5">70％以後</label>
                                　<input id="desc_1_6" id="desc_1_6" name="apply_desc_1" type="radio" value="6" /><label for="desc_1_6">無從評估</label>
                                <label for="apply_desc_1" class="error radio-inline"></label>
                            </div>
                            <div class=" text-left story text-indent_2nd"> 　<span class="text-danger">*
                                </span>(2) <strong>一般學業成績</strong>：
                                　<input id="desc_2_1" name="apply_desc_2" type="radio" value="1" /><label for="desc_2_1">前10％以內</label>
                                　<input id="desc_2_2" name="apply_desc_2" type="radio" value="2" /><label for="desc_2_2">10～25％</label>
                                　<input id="desc_2_3" name="apply_desc_2" type="radio" value="3" /><label for="desc_2_3">25～50％</label>
                                　<input id="desc_2_4" name="apply_desc_2" type="radio" value="4" /><label for="desc_2_4">50～70％</label>
                                　<input id="desc_2_5" name="apply_desc_2" type="radio" value="5" /><label for="desc_2_5">70％以後</label>
                                　<input id="desc_2_6" name="apply_desc_2" type="radio" value="6" /><label for="desc_2_6">無從評估</label>
                                <label for="apply_desc_2" class="error radio-inline"></label>
                            </div>
                            <div class=" text-left story text-indent_2nd"> 　<span class="text-danger">*
                                </span>(3) <strong>原&nbsp;&nbsp;創&nbsp;&nbsp;能&nbsp;&nbsp;力&nbsp;</strong>：
                                　<input id="desc_3_1" name="apply_desc_3" type="radio" value="1" /><label for="desc_3_1">前10％以內</label>
                                　<input id="desc_3_2" name="apply_desc_3" type="radio" value="2" /><label for="desc_3_2">10～25％</label>
                                　<input id="desc_3_3" name="apply_desc_3" type="radio" value="3" /><label for="desc_3_3">25～50％</label>
                                　<input id="desc_3_4" name="apply_desc_3" type="radio" value="4" /><label for="desc_3_4">50～70％</label>
                                　<input id="desc_3_5" name="apply_desc_3" type="radio" value="5" /><label for="desc_3_5">70％以後</label>
                                　<input id="desc_3_6" name="apply_desc_3" type="radio" value="6" /><label for="desc_3_6">無從評估</label>
                                <label for="apply_desc_3" class="error radio-inline"></label>
                            </div>
                            <div class=" text-left story text-indent_2nd"> 　<span class="text-danger">*
                                </span>(4) <strong>寫&nbsp;&nbsp;作&nbsp;&nbsp;能&nbsp;&nbsp;力&nbsp;</strong>：
                                　<input id="desc_4_1" name="apply_desc_4" type="radio" value="1" /><label for="desc_4_1">前10％以內</label>
                                　<input id="desc_4_2" name="apply_desc_4" type="radio" value="2" /><label for="desc_4_2">10～25％</label>
                                　<input id="desc_4_3" name="apply_desc_4" type="radio" value="3" /><label for="desc_4_3">25～50％</label>
                                　<input id="desc_4_4" name="apply_desc_4" type="radio" value="4" /><label for="desc_4_4">50～70％</label>
                                　<input id="desc_4_5" name="apply_desc_4" type="radio" value="5" /><label for="desc_4_5">70％以後</label>
                                　<input id="desc_4_6" name="apply_desc_4" type="radio" value="6" /><label for="desc_4_6">無從評估</label>
                                <label for="apply_desc_4" class="error radio-inline"></label>
                            </div>
                            <div class=" text-left story text-indent_2nd"> 　<span class="text-danger">*
                                </span>(5) <strong>口頭表達能力</strong>：
                                　<input id="desc_5_1" name="apply_desc_5" type="radio" value="1" /><label for="desc_5_1">前10％以內</label>
                                　<input id="desc_5_2" name="apply_desc_5" type="radio" value="2" /><label for="desc_5_2">10～25％</label>
                                　<input id="desc_5_3" name="apply_desc_5" type="radio" value="3" /><label for="desc_5_3">25～50％</label>
                                　<input id="desc_5_4" name="apply_desc_5" type="radio" value="4" /><label for="desc_5_4">50～70％</label>
                                　<input id="desc_5_5" name="apply_desc_5" type="radio" value="5" /><label for="desc_5_5">70％以後</label>
                                　<input id="desc_5_6" name="apply_desc_5" type="radio" value="6" /><label for="desc_5_6">無從評估</label>
                                <label for="apply_desc_5" class="error radio-inline"></label>
                            </div>
                            <div class=" text-left story
                                                text-indent_2nd"> 　<span class="text-danger">*
                                </span>(6) <strong>團隊合作能力</strong>：
                                　<input id="desc_6_1" name="apply_desc_6" type="radio" value="1" /><label for="desc_6_1">前10％以內</label>
                                　<input id="desc_6_2" name="apply_desc_6" type="radio" value="2" /><label for="desc_6_2">10～25％</label>
                                　<input id="desc_6_3" name="apply_desc_6" type="radio" value="3" /><label for="desc_6_3">25～50％</label>
                                　<input id="desc_6_4" name="apply_desc_6" type="radio" value="4" /><label for="desc_6_4">50～70％</label>
                                　<input id="desc_6_5" name="apply_desc_6" type="radio" value="5" /><label for="desc_6_5">70％以後</label>
                                　<input id="desc_6_6" name="apply_desc_6" type="radio" value="6" /><label for="desc_6_6">無從評估</label>
                                <label for="apply_desc_6" class="error radio-inline"></label>
                            </div>
                            <div class=" text-left story text-indent_2nd"> 　<span class="text-danger">*
                                </span>(7) <strong>問題解決能力</strong>：
                                　<input id="desc_7_1" name="apply_desc_7" type="radio" value="1" /><label for="desc_7_1">前10％以內</label>
                                　<input id="desc_7_2" name="apply_desc_7" type="radio" value="2" /><label for="desc_7_2">10～25％</label>
                                　<input id="desc_7_3" name="apply_desc_7" type="radio" value="3" /><label for="desc_7_3">25～50％</label>
                                　<input id="desc_7_4" name="apply_desc_7" type="radio" value="4" /><label for="desc_7_4">50～70％</label>
                                　<input id="desc_7_5" name="apply_desc_7" type="radio" value="5" /><label for="desc_7_5">70％以後</label>
                                　<input id="desc_7_6" name="apply_desc_7" type="radio" value="6" /><label for="desc_7_6">無從評估</label>
                                <label for="apply_desc_7" class="error radio-inline"></label>
                            </div>
                            <div class=" text-left story text-indent_2nd"> 　<span class="text-danger">*
                                </span>(8) <strong>情緒控管能力</strong>：
                                　<input id="desc_8_1" name="apply_desc_8" type="radio" value="1" /><label for="desc_8_1">前10％以內</label>
                                　<input id="desc_8_2" name="apply_desc_8" type="radio" value="2" /><label for="desc_8_2">10～25％</label>
                                　<input id="desc_8_3" name="apply_desc_8" type="radio" value="3" /><label for="desc_8_3">25～50％</label>
                                　<input id="desc_8_4" name="apply_desc_8" type="radio" value="4" /><label for="desc_8_4">50～70％</label>
                                　<input id="desc_8_5" name="apply_desc_8" type="radio" value="5" /><label for="desc_8_5">70％以後</label>
                                　<input id="desc_8_6" name="apply_desc_8" type="radio" value="6" /><label for="desc_8_6">無從評估</label>
                                <label for="apply_desc_8" class="error radio-inline"></label>
                            </div>
                        </div>

                    </div>
                    <div class=" form-group col-xs-12 col-sm-12 col-md-12 col-lg-12">
                        <div style='padding:5px;margin:5px' class='bg-info-light'><span class="text-danger">*
                            </span>4. <span style="font-weight:bold">您認為申請人在學期間的求學態度如何？(可複選)</span>
                        </div>
                        <label class="radio-inline">
                            <input name="apply_manner[]" type="checkbox" value='1' />
                            自動自發
                        </label>
                        <label class="radio-inline">
                            <input name="apply_manner[]" type="checkbox" value='2' />
                            態度嚴謹
                        </label>
                        <label class="radio-inline">
                            <input name="apply_manner[]" type="checkbox" value='3' />
                            一般
                        </label>
                        <label class="radio-inline">
                            <input name="apply_manner[]" type="checkbox" value='4' />
                            不求甚解
                        </label>
                        <label class="radio-inline">
                            <input name="apply_manner[]" type="checkbox" value='5' />
                            勉強應付
                        </label>
                        <label for="apply_manner[]" class="error radio-inline"></label>
                    </div>
                    <div class="form-group col-xs-12 col-sm-12 col-md-12 col-lg-12">
                        <div style='padding:5px;margin:5px' class='bg-info-light'><span class="text-danger">*
                            </span>5. <span style="font-weight:bold">針對申請人的研讀方向，您認為他對需要的基本課程及相關課程的準備及認識如何？</span>
                        </div>
                        <label class="radio-inline">
                            <input name="apply_course" type="radio" value='1' />
                            非常符合
                        </label>
                        <label class="radio-inline">
                            <input name="apply_course" type="radio" value='2' />
                            符合，但尚需加強
                        </label>
                        <label class="radio-inline">
                            <input name="apply_course" type="radio" value='3' />
                            不符合，但入學後應可達到
                        </label>
                        <label class="radio-inline">
                            <input name="apply_course" type="radio" value='4' />不太可能達到
                        </label>
                        <label for="apply_course" class="error radio-inline"></label>
                    </div>
                    <div class="form-group col-xs-12 col-sm-12 col-md-12 col-lg-12">
                        <div style='padding:5px;margin:5px' class='bg-info-light'>6. <span style="font-weight:bold">申請人如有其他潛力或有特殊表現，請說明：</span></div>
                        <label class="radio-inline">
                            <textarea class="form-control" name="apply_special" rows="5" cols="80"></textarea>
                        </label>
                        <label for="apply_special" class="error radio-inline"></label>
                    </div>
                    <div class="form-group col-xs-12 col-sm-12 col-md-12 col-lg-12">
                        <div style='padding:5px;margin:5px' class='bg-info-light'>7. <span style="font-weight:bold">申請人如有值得注意的問題，請說明：</span></div>
                        <label class="radio-inline">
                            <textarea class="form-control" name="apply_notice" rows="5" cols="80"></textarea>
                        </label>
                        <label for="apply_notice" class="error radio-inline"></label>
                    </div>
                    <div class="form-group col-xs-12 col-sm-12 col-md-12 col-lg-12">
                        <div style='padding:5px;margin:5px' class='bg-info-light'><span class="text-danger">*
                            </span>8. <span style="font-weight:bold">您願意推薦申請人來念本系碩士班嗎？</span>
                        </div>
                        <label class="radio-inline">
                            <input name="apply_agree" type="radio" value='1' />
                            極力推薦
                        </label>
                        <label class="radio-inline">
                            <input name="apply_agree" type="radio" value='2' />
                            推薦
                        </label>
                        <label class="radio-inline">
                            <input name="apply_agree" type="radio" value='2' />
                            勉強推薦
                        </label>
                        <label class="radio-inline">
                            <input name="apply_agree" type="radio" value='2' />
                            不推薦
                        </label>
                        <label for="apply_agree" class="error radio-inline"></label>
                    </div>
                    <div class="form-group col-xs-12 col-sm-12 col-md-12 col-lg-12">
                        <div style='padding:5px;margin:5px' class='bg-info-light'>9. <span style="font-weight:bold">其他補充說明：</span></div>
                        <label class="radio-inline">
                            <textarea class="form-control" name="apply_remark" rows="5" cols="80"></textarea>
                        </label>
                        <label for="apply_remark" class="error radio-inline"></label>
                    </div>
                    </p>
                    <div class="form-group">
                        <div class="col-lg-7 col-lg-offset-5 col-md-7 col-md-offset-5 col-sm-7 col-sm-offset-5 col-xs-7 col-xs-offset-5">
                            <button type="submit" class="btn btn-primary" id="send">送出問卷</button>
                        </div>
                    </div>
                </form>
                <div class="alert alert-warning">
                    <span class="text-danger"><b>注意事項：</b></span>
                    <ol>
                        <li>..<span class="text-danger">.......</span>.....<span class="text-danger">.....</span>、
                        </li>
                        <li>.....................。</li>
                        <li>...........。</li>
                    </ol>
                </div>
            </div>
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
    <script src="./js/letter_fill.js"></script>



</body>

</html>