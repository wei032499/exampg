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
        if (sessionStorage === undefined)
            alert("未支援Web Storage！\n請更換瀏覽器再試。");
        else
            sessionStorage.clear();
    </script>
</head>

<body>
    <?php require_once("./module/header.php") ?>

    <section class="py-4 bg-light">
        <div class="container">
            <div id="title" class="my-3">
                <div class="row container">
                    <div style='width: 8px;height: 8px;display: block;background: #c84c37;'></div>
                    <div style='width: 8px;height: 8px;display: block;background: #3a7eb8;'></div>
                </div>
                <div class="row ">
                    <h3 class="col" style="letter-spacing: 0.2rem;">
                        :::流程說明
                    </h3>
                    <div id="loginInfo" class="col-sm row justify-content-end mx-0 align-items-center" style="display: none !important;">
                        <div>Hi~ <span id="username"></span> </div>
                        <button type="button" id="logout" style="min-width:4rem" class="btn btn-info btn-sm ml-3">登出</button>
                    </div>
                </div>
            </div>

            <div class="shadow">
                <ul class="nav nav-tabs" id="myTab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <a class="nav-link " id="download-tab" data-toggle="tab" href="#download" role="tab" aria-controls="download" aria-selected="false">下載電子簡章</a>
                        <!--active-->
                    </li>
                    <li class="nav-item" role="presentation">
                        <a class="nav-link" id="account_order-tab" data-toggle="tab" href="#account_order" role="tab" aria-controls="account_order" aria-selected="false">取得繳費帳號</a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a class="nav-link" id="signup_form-tab" data-toggle="tab" href="#signup_form" role="tab" aria-controls="signup_form" aria-selected="false">填寫報名表</a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a class="nav-link" id="alter_form-tab" data-toggle="tab" href="#alter_form" role="tab" aria-controls="alter_form" aria-selected="false">修改報名資料</a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a class="nav-link" id="confirm_form-tab" data-toggle="tab" href="#confirm_form" role="tab" aria-controls="confirm_form" aria-selected="false">資料確認</a>
                    </li>
                </ul>




                <div class="tab-content" id="prointroTabContent">
                    <div class="tab-pane fade " id="download" role="tabpanel" aria-labelledby="download-tab">
                        <!--active-->
                        <div class="card p-4">
                            <p>在網路報名前，您可以先利用「<a href="https://acadaff.ncue.edu.tw/files/11-1021-2399-1.php?Lang=zh-tw">招生簡章</a>」功能，免費下載簡章。</p>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="account_order" role="tabpanel" aria-labelledby="account_order-tab">
                        <div class="card p-4">
                            <p class="line-height-1">
                                欲報名的考生，請先上網填寫報名費繳費帳號申請單，取得報名費繳費帳號， 以下為<a href="./order.php">取得繳費帳號</a>之圖例。<br>
                                <br>
                                圖一、填寫申請單：請務必留下正確的資料。<br>
                                <br>
                                <img src="https://aps.ncue.edu.tw/exampg_m/images/account_1.jpg" /><br>(點擊圖片放大)<br>
                                <br>
                                圖二、資料確認：若因資料錯誤而造成權益的損失，概由填表人自負。<br>
                                <br>
                                <img src="https://aps.ncue.edu.tw/exampg_m/images/account_2.jpg" /><br>(點擊圖片放大)<br>
                                <br>
                                圖三、申請作業完成：請記下系統給您的繳費帳號，進行繳費並確認銷帳完成，請參考「流程說明」功能裡的「<a href="./intro_payment.php">報名費繳費方式及銷帳查詢方式說明</a>」。<br>
                                <br>
                                <img src="https://aps.ncue.edu.tw/exampg_m/images/account_3.jpg" /><br>(點擊圖片放大)<br>
                                <br>
                                ◆系統會在確認您繳費後，自動將序號、密碼傳送至您所登錄之E-mail信箱；當您收到序號密碼通知後，即可登入「<a href="./signup.php">填寫報名表</a>」，填寫您的報名資料。<br>
                            </p>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="signup_form" role="tabpanel" aria-labelledby="signup_form-tab">
                        <div class="card p-4">
                            <p class="line-height-1">
                                完成報名費繳費後約一小時，系統確認入帳後，將會自動傳送序號及密碼至您所填的電子郵件信箱。在您收到系統寄送給您的電子郵件後，即可利用「網路報名」功能裡的「<a href="./signup.php">填寫報名表</a>」，輸入序號及密碼進行填寫報名表作業。<br>
                                <br><br>
                                注意：報名表初填完成即完成報名，不可再申請退還報名費(除同等學力審查不合格外)，惟在報名截止前仍可進行資料修改或直接進行資料確認 。<br>
                                <br><br>
                                您亦可以利用「資料查詢」功能裡的「<a href="./query_acc.php">報名費銷帳查詢</a>」，來確認是否完成銷帳。以下為登入「<a href="./signup.php">填寫報名表</a>」之圖例。<br>
                                <br>
                                圖一、輸入序號及密碼：請輸入序號及密碼，請注意所有英文字母皆為大寫。<br>
                                <br>
                                <img src="https://aps.ncue.edu.tw/exampg_m/images/sign1.jpg" /><br>(點擊圖片放大)<br>
                                <br>
                                圖二、個人資料提供同意書：請詳細閱讀，若您同意請勾選「我已詳閱本同意書，瞭解並同意」進行下一步。<br>
                                <br>
                                <img src="https://aps.ncue.edu.tw/exampg_m/images/sign2.jpg" /><br>(點擊圖片放大)<br>
                                <br>
                                圖三、選擇您所要報考的系所。<br>
                                <br>
                                <img src="https://aps.ncue.edu.tw/exampg_m/images/sign3.jpg" /><br>(點擊圖片放大)<br>
                                <br>
                                圖四、填寫報名資料：請確實填寫各項資料或上傳審查資料，並詳為校核，以免權益受損。<br>
                                若報考系所備審資料係採「上傳至網路報名系統」，報考考生可於填寫報名表時一併上傳或暫時先不上傳，完成報名作業後，欲再上傳或修改者，可至網路報名系統/網路報名/修改報名資料進行上傳作業。<br>
                                <br>
                                <img src="https://aps.ncue.edu.tw/exampg_m/images/sign4.jpg" /><br>(點擊圖片放大)<br>
                                <br>
                                圖五、確認報名資料：請仔細檢查資料是否正確。<br>
                                <br>
                                <img src="https://aps.ncue.edu.tw/exampg_m/images/sign5.jpg" /><br>(點擊圖片放大)<br>
                                <br>
                                圖六、初步報名完成，請在報名期間內完成「<a href="./confirm.php">資料確認</a>」，在完成「<a href="./confirm.php">資料確認</a>」前，您的資料可以不限次數進行修改、上傳或更新已上傳的備審資料等。<br>
                                <br>
                                <img src="https://aps.ncue.edu.tw/exampg_m/images/sign6.jpg" /><br>(點擊圖片放大)<br>
                                <br>
                                ◆每組序號、密碼僅可填寫一次報名表，若資料需修改/上傳備審資料/更新已上傳備審資料，請利用網路報名功能裡的「<a href="./alter.php">修改報名資料</a>」。<br>
                            </p>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="alter_form" role="tabpanel" aria-labelledby="alter_form-tab">
                        <div class="card p-4">
                            <p class="line-height-1">
                                若您的資料有異動時，您可以利用網路報名功能裡的「<a href="./alter.php">修改報名資料</a>」，來更新您的資料。<br>
                                <br>
                                以下為「<a href="./alter.php">修改報名資料</a>」之圖例。<br>
                                <br>
                                圖一、登入：「<a href="./alter.php">修改報名資料</a>」除了需輸入序號及密碼外，還需輸入您於報名表中所填的身分證字號。<br>
                                <br>
                                <img src="https://aps.ncue.edu.tw/exampg_m/images/alter1.jpg" /><br>(點擊圖片放大)<br>
                                <br>
                                圖二、變更報名系所：選擇要報考的系所。<br>
                                <br>
                                <img src="https://aps.ncue.edu.tw/exampg_m/images/alter2.jpg" /><br>(點擊圖片放大)<br>
                                <br>
                                圖三、變更報名資料：直接把原有資料清除，填入新的資料即可。<br>
                                <br>
                                <img src="https://aps.ncue.edu.tw/exampg_m/images/alter3.jpg" /><br>(點擊圖片放大)<br>
                                <br>
                                圖四、確認報名資料：請仔細檢查資料是否正確。<br>
                                <br>
                                <img src="https://aps.ncue.edu.tw/exampg_m/images/alter4.jpg" /><br>(點擊圖片放大)<br>
                                <br>
                                圖五、報名資料修改完成：請在報名期間完成「<a href="./confirm.php">資料確認</a>」。<br>
                                <br>
                                <img src="https://aps.ncue.edu.tw/exampg_m/images/alter5.jpg" /><br>(點擊圖片放大)<br>
                            </p>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="confirm_form" role="tabpanel" aria-labelledby="confirm_form-tab">
                        <div class="card p-4">
                            <p class="line-height-1">
                                資料確認後：<br>
                                １、系統會將您的報名資料鎖定，保護您的資料不被有心人士竄改。<br>
                                ２、您修改資料的權利也會關閉。<br>
                                ３、您將無法登入使用「網路報名部份」的功能（其它功能仍可使用）。<br>
                                <br>
                                請注意：一旦完成資料確認後，即不可再更改報名資料。<br>
                                <br>
                                以下為「<a href="./confirm.php">資料確認</a>」之圖例。<br>
                                <br>
                                圖一、登入：「<a href="./confirm.php">資料確認</a>」除了需輸入序號及密碼外，還需輸入您於報名表中所填的身分證字號。<br>
                                <br>
                                <img src="https://aps.ncue.edu.tw/exampg_m/images/confirm1.jpg" /><br>(點擊圖片放大)<br>
                                <br>
                                圖二、請仔細檢視所有資料，並確定無誤後，按下"確認"，即完成「<a href="./confirm.php">資料確認</a>」；若發現資料有誤可按"取消"，進行「<a href="./alter.php">修改報名資料</a>」。<br>
                                <br>
                                <img src="https://aps.ncue.edu.tw/exampg_m/images/confirm2.jpg" /><br>(點擊圖片放大)<br>
                                <br>
                                ◆當您完成修改後，若要進行資料確認，請重新登入「<a href="./confirm.php">資料確認</a>」。<br>
                                <br>
                                圖三、資料確認完成，日後請密切注意網頁上的相關資料。<br>
                                <br>
                                <img src="https://aps.ncue.edu.tw/exampg_m/images/confirm3.jpg" /><br>(點擊圖片放大)<br>
                                <br>
                                ◆進行「填寫報名表」、「<a href="./alter.php">修改報名資料</a>」、「<a href="./confirm.php">資料確認</a>」等動作後，系統會自動寄送E-mail通知信至您登錄之電子郵件信箱。<br>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!--<div id="imgModal" class="modal" tabindex="-1">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>

                    <div class="modal-body row justify-content-center">
                        <img src="" style="max-width:100%;" />
                    </div>
                </div>
            </div>
        </div>-->
    </section>

    <?php require_once("./module/footer.php") ?>

    <script>
        $(function() {
            if (window.location.hash === "")
                $('#myTab a[href="#download"]').tab('show');
            else
                $('#myTab a[href="' + window.location.hash + '"]').tab('show');
        });
        $('a[data-toggle="tab"]').on('show.bs.tab', function(e) {
            window.location.hash = e.target.hash;
            window.scroll(0, 0); //$("#title").offset()
        });

        $("#prointroTabContent img").on('click', function() {
            window.location.assign($(this).attr('src'));
            /*$("#imgModal img").attr('src', $(this).attr('src'));
            $('#imgModal').modal('show')*/
        });
    </script>


</body>

</html>