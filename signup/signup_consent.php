<!doctype html>
<html lang="zh-Hant-TW">

<head>
    <meta charset="utf-8">
    <title>網路報名</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/x-icon" href="./images/favicon.ico">

    <link rel="stylesheet" href="./css/bootstrap.min.css" />
    <link rel="stylesheet" href="./css/custom.css" />
    <link rel="stylesheet" href="./css/toastr.min.css" />
    <script src="./js/jquery.min.js"></script>
    <script src="./js/bootstrap.bundle.min.js"></script>
    <script src="./js/toastr.min.js"></script>
    <script src="./js/common.js"></script>
    <script>
        if (sessionStorage === undefined) {
            alert("未支援Web Storage！\n請更換瀏覽器再試。");
            window.location.replace('./');
        } else {
            sessionStorage.removeItem('signup');
            sessionStorage.removeItem('agree');
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
                    <h3 class="col-xl" style="letter-spacing: 0.2rem;min-width:14rem">
                        :::填寫報名表 <span style="color:red">(個人資料提供同意書)</span>
                    </h3>
                    <div id="loginInfo" class="col-sm row justify-content-end mx-0 align-items-center" style="display: none !important;">
                        <div>Hi~ <span id="username"></span> </div>
                        <button type="button" id="logout" style="min-width:4rem" class="btn btn-info btn-sm ml-3">登出</button>
                    </div>
                </div>
            </div>

            <div class="card shadow px-5 pt-4">
                <p class="line-height-1">
                    一、國立彰化師範大學（以下簡稱本校）取得您的個人資料，目的在於進行招生等教務相關工作，蒐集、處理及使用您的個人資料是受到個人資料保護法及相關法令之規範。<br>
                    二、本次蒐集與使用您的個人資料，包含姓名、國民身分證統一編號（居留證號）、性別、出生年月日、通訊地址、戶籍地址、住宅電話、公司電話、行動電話、E-mail、緊急聯絡人姓名、緊急聯絡人電話、與緊急聯絡人關係、及應考學歷等。<br>
                    三、您同意本校因教務所需，以您所提供的個人資料確認您的身分、與您進行聯絡及同意本校於您報名錄取後繼續使用您的個人資料並永久保存。<br>
                    四、依據個人資料保護法，您可就您的個人資料向本校：(1)請求查詢或閱覽；(2)請求製給複製本；(3)請求補充或更正；(4)請求停止蒐集、處理及利用；(5)請求刪除。但因本校執行職務或業務所必需者及受其他法律所規範者，本校得拒絕之。<br>
                    五、您可以自由選擇是否提供相關個人資料，若您所提供之個人資料，經檢舉或本校發現不足以確認您的身分真實性，或發生其他個人資料冒用、盜用、資料不實等情形，本校有權停止您的報名資格、錄取資格等相關權利，若有不便之處敬請見諒。<br>
                    六、本同意書如有未盡事宜，依個人資料保護法或其他相關法規之規定辦理。<br>
                    七、您已瞭解此一同意書符合個人資料保護法及相關法規之要求，同意本校蒐集、處理及使用您的個人資料之效果。<br>
                </p>
                <form>
                    <div class="row justify-content-center">
                        <div class="form-group form-check ">
                            <input type="checkbox" class="form-check-input" name="agree" id="agreeCheck" required>
                            <label class="form-check-label" for="agreeCheck">我已詳閱本同意書，瞭解並同意(請打勾)</label>
                        </div>
                    </div>
                    <div class="row justify-content-center my-2">
                        <button type="submit" class="btn btn-primary btn-sm">下一步</button>
                    </div>
                </form>
            </div>

        </div>
    </section>


    <?php require_once("./module/footer.php") ?>

    <script>
        $("form").on('submit', function(e) {
            e.preventDefault();

            sessionStorage.setItem("agree", "true");
            window.location.replace('./signup.php?step=3');

        });
    </script>



</body>

</html>