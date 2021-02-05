<!doctype html>
<html lang="zh-Hant-TW">

<head>
    <meta charset="utf-8">
    <title>網路報名</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/x-icon" href="./images/favicon.ico">
    <!--http://www.csie.ncue.edu.tw/csie/resources/images/ncue-logo.png-->
    <!-- bootstrap 5.0.x
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-giJF6kkoqNQ00vy+HMDP7azOuL0xtbfIcaT9wjKHr8RbDVddVHyTfAAsrekwKmP1" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/js/bootstrap.bundle.min.js" integrity="sha384-ygbV9kiqUc6oa4msXn9868pTtWMgiQaeYH7/t7LECLbyPA2x65Kgf80OJFdroafW" crossorigin="anonymous"></script>
    -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css" integrity="sha384-B0vP5xmATw1+K9KRQjQERJvTumQW0nPEzvF6L/Z6nronJ3oUOFUFpCjEUQouq2+l" crossorigin="anonymous">
    <link rel="stylesheet" href="./css/custom.css" />
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-Piv4xVNRyMGpqkS2by6br4gNJ7DXjqk09RmUpJ8jgGtD7zP9yug3goQfGII0yAns" crossorigin="anonymous"></script>
    <script src="./js/custom.js"></script>

    <script>
        if (sessionStorage === undefined) {
            alert("未支援Web Storage！\n請更換瀏覽器再試。");
        } else sessionStorage.clear();
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
                        :::系統公告
                    </h3>
                    <div id="loginInfo" class="col row justify-content-end mx-0 align-items-center" style="display: none !important;">
                        <div>Hi~ <span id="username"></span> </div>
                        <button type="button" id="logout" style="min-width:4rem" class="btn btn-info btn-sm ml-3">登出</button>
                    </div>
                </div>
                <table class="table table-success table-hover table-bordered">
                    <thead>
                        <tr class="table-primary">
                            <th scope="col" style="width: 90%;">公告事項</th>
                            <th scope="col" style="width: 10%;">公告日期</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                <span style="color:#000000"><b>本系統僅供報考『110學年度碩士班』考試，欲報考其他招生考試者<span style="color:red">(如在職進修專班碩士學位班等)</span>請勿使用，<span style="color:red">若已誤繳報名費，請勿填寫報名表並依簡章規定申請退費。</span></b>
                                </span>
                            </td>
                            <td>12月18日</td>
                        </tr>
                        <tr>
                            <td><span class="style3"><u>部分電子郵件信箱(如:Gmail、Hotmail..等)可能會將系統寄發之郵件攔截為垃圾郵件，繳費後如未收到序號密碼通知信，請先檢查是否在垃圾郵件匣。</u></span></td>
                            <td>12月18日</td>
                        </tr>
                        <tr>
                            <td>
                                <span style="color:#000000">成績下載開放日期：【
                                    <span style="color:#FF0000">
                                        110-03-26 17:00:00 </span>】 至 【
                                    <span style="color:#FF0000">
                                        110-09-13 23:59:59 </span>】 止。
                                    <br>輔諮系、輔諮系婚家班面試成績查詢：【
                                    <span style="color:#FF0000">
                                        110-04-12 17:00:00 </span>】 至 【
                                    <span style="color:#FF0000">
                                        110-09-13 23:59:59 </span>】 止。
                                </span>
                            </td>
                            <td>12月18日</td>
                        </tr>
                        <tr>
                            <td>
                                <span style="color:#000000">正(備)取生申明就讀(遞補)意願開放日期：【
                                    <span style="color:#FF0000">
                                        110-03-26 17:00:00 </span>】 至 【
                                    <span style="color:#FF0000">
                                        110-04-19 17:00:00 </span>】 止(招生系所另有規定，從其規定，請詳見簡章)。
                                    <br> 輔諮系、輔諮系婚家班正(備)取生申請就讀(遞補)意願開放日期：【
                                    <span style="color:#FF0000">
                                        110-04-12 17:00:00 </span>】 至 【
                                    <span style="color:#FF0000">
                                        110-04-19 17:00:00 </span>】 止。
                                </span>
                            </td>
                            <td>12月18日</td>
                        </tr>
                        <tr>
                            <td>
                                <span style="color:#000000"><b>本系統僅供報考『110學年度碩士班』考試，欲報考其他招生考試者<span style="color:red">(如在職進修專班碩士學位班等)</span>請勿使用，<span style="color:red">若已誤繳報名費，請勿填寫報名表並依簡章規定申請退費。</span></b>
                                </span>
                            </td>
                            <td>12月18日</td>
                        </tr>
                        <tr>
                            <td><span class="style3"><u>部分電子郵件信箱(如:Gmail、Hotmail..等)可能會將系統寄發之郵件攔截為垃圾郵件，繳費後如未收到序號密碼通知信，請先檢查是否在垃圾郵件匣。</u></span></td>
                            <td>12月18日</td>
                        </tr>
                        <tr>
                            <td>
                                <span style="color:#000000">成績下載開放日期：【
                                    <span style="color:#FF0000">
                                        110-03-26 17:00:00 </span>】 至 【
                                    <span style="color:#FF0000">
                                        110-09-13 23:59:59 </span>】 止。
                                    <br>輔諮系、輔諮系婚家班面試成績查詢：【
                                    <span style="color:#FF0000">
                                        110-04-12 17:00:00 </span>】 至 【
                                    <span style="color:#FF0000">
                                        110-09-13 23:59:59 </span>】 止。
                                </span>
                            </td>
                            <td>12月18日</td>
                        </tr>
                        <tr>
                            <td>
                                <span style="color:#000000">正(備)取生申明就讀(遞補)意願開放日期：【
                                    <span style="color:#FF0000">
                                        110-03-26 17:00:00 </span>】 至 【
                                    <span style="color:#FF0000">
                                        110-04-19 17:00:00 </span>】 止(招生系所另有規定，從其規定，請詳見簡章)。
                                    <br> 輔諮系、輔諮系婚家班正(備)取生申請就讀(遞補)意願開放日期：【
                                    <span style="color:#FF0000">
                                        110-04-12 17:00:00 </span>】 至 【
                                    <span style="color:#FF0000">
                                        110-04-19 17:00:00 </span>】 止。
                                </span>
                            </td>
                            <td>12月18日</td>
                        </tr>
                        <tr>
                            <td>
                                <span style="color:#000000"><b>本系統僅供報考『110學年度碩士班』考試，欲報考其他招生考試者<span style="color:red">(如在職進修專班碩士學位班等)</span>請勿使用，<span style="color:red">若已誤繳報名費，請勿填寫報名表並依簡章規定申請退費。</span></b>
                                </span>
                            </td>
                            <td>12月18日</td>
                        </tr>
                        <tr>
                            <td><span class="style3"><u>部分電子郵件信箱(如:Gmail、Hotmail..等)可能會將系統寄發之郵件攔截為垃圾郵件，繳費後如未收到序號密碼通知信，請先檢查是否在垃圾郵件匣。</u></span></td>
                            <td>12月18日</td>
                        </tr>
                        <tr>
                            <td>
                                <span style="color:#000000">成績下載開放日期：【
                                    <span style="color:#FF0000">
                                        110-03-26 17:00:00 </span>】 至 【
                                    <span style="color:#FF0000">
                                        110-09-13 23:59:59 </span>】 止。
                                    <br>輔諮系、輔諮系婚家班面試成績查詢：【
                                    <span style="color:#FF0000">
                                        110-04-12 17:00:00 </span>】 至 【
                                    <span style="color:#FF0000">
                                        110-09-13 23:59:59 </span>】 止。
                                </span>
                            </td>
                            <td>12月18日</td>
                        </tr>
                        <tr>
                            <td>
                                <span style="color:#000000">正(備)取生申明就讀(遞補)意願開放日期：【
                                    <span style="color:#FF0000">
                                        110-03-26 17:00:00 </span>】 至 【
                                    <span style="color:#FF0000">
                                        110-04-19 17:00:00 </span>】 止(招生系所另有規定，從其規定，請詳見簡章)。
                                    <br> 輔諮系、輔諮系婚家班正(備)取生申請就讀(遞補)意願開放日期：【
                                    <span style="color:#FF0000">
                                        110-04-12 17:00:00 </span>】 至 【
                                    <span style="color:#FF0000">
                                        110-04-19 17:00:00 </span>】 止。
                                </span>
                            </td>
                            <td>12月18日</td>
                        </tr>
                        <tr>
                            <td>
                                <span style="color:#000000"><b>本系統僅供報考『110學年度碩士班』考試，欲報考其他招生考試者<span style="color:red">(如在職進修專班碩士學位班等)</span>請勿使用，<span style="color:red">若已誤繳報名費，請勿填寫報名表並依簡章規定申請退費。</span></b>
                                </span>
                            </td>
                            <td>12月18日</td>
                        </tr>
                        <tr>
                            <td><span class="style3"><u>部分電子郵件信箱(如:Gmail、Hotmail..等)可能會將系統寄發之郵件攔截為垃圾郵件，繳費後如未收到序號密碼通知信，請先檢查是否在垃圾郵件匣。</u></span></td>
                            <td>12月18日</td>
                        </tr>
                        <tr>
                            <td>
                                <span style="color:#000000">成績下載開放日期：【
                                    <span style="color:#FF0000">
                                        110-03-26 17:00:00 </span>】 至 【
                                    <span style="color:#FF0000">
                                        110-09-13 23:59:59 </span>】 止。
                                    <br>輔諮系、輔諮系婚家班面試成績查詢：【
                                    <span style="color:#FF0000">
                                        110-04-12 17:00:00 </span>】 至 【
                                    <span style="color:#FF0000">
                                        110-09-13 23:59:59 </span>】 止。
                                </span>
                            </td>
                            <td>12月18日</td>
                        </tr>
                        <tr>
                            <td>
                                <span style="color:#000000">正(備)取生申明就讀(遞補)意願開放日期：【
                                    <span style="color:#FF0000">
                                        110-03-26 17:00:00 </span>】 至 【
                                    <span style="color:#FF0000">
                                        110-04-19 17:00:00 </span>】 止(招生系所另有規定，從其規定，請詳見簡章)。
                                    <br> 輔諮系、輔諮系婚家班正(備)取生申請就讀(遞補)意願開放日期：【
                                    <span style="color:#FF0000">
                                        110-04-12 17:00:00 </span>】 至 【
                                    <span style="color:#FF0000">
                                        110-04-19 17:00:00 </span>】 止。
                                </span>
                            </td>
                            <td>12月18日</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </section>

    <?php require_once("./module/footer.php") ?>




</body>

</html>