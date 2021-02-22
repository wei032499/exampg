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
                        :::推薦函作業
                    </h3>
                    <div id="loginInfo" class="col row justify-content-end mx-0 align-items-center" style="display: none !important;">
                        <div>Hi~ <span id="username"></span> </div>
                        <button type="button" id="logout" style="min-width:4rem" class="btn btn-info btn-sm ml-3">登出</button>
                    </div>
                </div>
            </div>


            <form id="form1" class="border p-4 bg-white shadow rounded" method="POST">

                <div><strong>研究所擬研讀方向：</strong>
                    <input class="form-control" type="text" name="research" value="" size="30" required="required" title="" maxlength="30" /><br>
                </div>
                <div class="card ">
                    <div class="card-header font-weight-bold text-center fs-5">
                        推薦人資料 </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover table-bordered ">
                                <thead>
                                    <tr>
                                        <th class="text-center" style="min-width: 5rem;">序號</th>
                                        <th class="text-center" style="min-width:10rem;">姓名
                                            <label for="r_name[]" class="error"></label>
                                        </th>

                                        <th class="text-center" style="min-width:10rem;">服務單位
                                            <label for="r_org[]" class="error"></label>
                                        </th>
                                        <th class="text-center" style="min-width:10rem;">職稱
                                            <label for="r_title[]" class="error"></label>
                                        </th>
                                        <th class="text-center" style="min-width:10rem;">Email
                                            <label for="r_email[]" class="error"></label>
                                        </th>

                                    </tr>
                                </thead>
                                <tbody id="mybody">
                                    <tr class="text-center" id="addrow1">
                                        <td class="col-md-1">1
                                            <input type="hidden" name="r_seq[]" value="1">
                                        </td>
                                        <td class="col-md-2">
                                            <input type="text" name="r_name[]" class="form-control " value="" required="required" />
                                        </td>
                                        <td class="col-md-2">
                                            <input type="text" name="r_org[]" class="form-control " value="" required="required" />
                                        </td>
                                        <td class="col-md-2">
                                            <input type="text" name="r_title[]" class="form-control " value="" required="required" />
                                        </td>
                                        <td class="col-md-4">
                                            <input type="email" name="r_email[]" class="form-control " value="" required="required" />
                                        </td>
                                    </tr>
                                    <tr class="text-center" id="addrow2" style="display: none;">
                                        <td class="col-md-1">2
                                            <input type="hidden" name="r_seq[]" value="2">
                                            <button type="button" class="btn btn-danger btn-sm" title="移除推薦人">移除</button>
                                        </td>
                                        <td class="col-md-2">
                                            <input type="text" name="r_name[]" class="form-control " value="" disabled />
                                        </td>
                                        <td class="col-md-2">
                                            <input type="text" name="r_org[]" class="form-control " value="" disabled />
                                        </td>
                                        <td class="col-md-2">
                                            <input type="text" name="r_title[]" class="form-control " value="" disabled />
                                        </td>
                                        <td class="col-md-4">
                                            <input type="email" name="r_email[]" class="form-control " value="" disabled />
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="row justify-content-center my-1">
                    <button type="button" class="btn btn-success mx-1" id="addnew">新增一筆</button>
                    <button type="button" class="btn btn-warning mx-1" id="reset">取消修改</button>
                    <button type="submit" class="btn btn-primary mx-1">儲　 存</button>
                </div>
                <div class="alert alert-warning my-1">
                    　<div class="text-danger "><b>注意事項：</b></div>
                    <ol>
                        <li>最多只能填寫<span class="text-danger"> 2 </span>位推薦人資料，<span class="text-danger"><b>請務必確認所有資料都填寫正確再送出</b></span>。</span></li>
                        <li><span class="text-danger"><b>「寄發Email」</b>後，推薦人「姓名」、「服務單位」及「職稱」等欄位即不可再修改。</span></li>
                        <li><span class="text-danger"><b>「推薦人讀取」</b>後，推薦人「Email」欄位不可再修改。</span></li>

                    </ol>
                </div>

                <!--<hr />
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
                </div>-->
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
    <script src="./js/letter.js"></script>

</body>

</html>