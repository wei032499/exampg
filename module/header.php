<header class="bg-header">
    <div class="container p-2 fs-3">
        <div style="align-items:center">
            <a href="./">
                <img id="logo1" src="./images/logo1.png" style="max-width:100%;" alt="" />
                <img id="logo2" src="./images/logo2.png" style="max-width:100%;" alt="" />
            </a>
        </div>

    </div>
</header>
<nav class="navbar navbar-expand-lg navbar-dark bg-subnavbar subnavbar">
    <div class="container">
        <!--<a class="navbar-brand" href="#">Navbar</a>-->
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse fw-bold" id="navbarSupportedContent">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link " aria-current="page" href="./">系統公告</a>
                    <!--active-->
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        流程說明
                    </a>
                    <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                        <li><a class="dropdown-item" href="./intro_registration.php">網路報名程序</a></li>
                        <li><a class="dropdown-item" href="./intro_payment.php">報名費繳費方式及銷帳查詢方式說明</a></li>
                    </ul>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="https://acadaff.ncue.edu.tw/files/11-1021-2399-1.php?Lang=zh-tw">招生簡章</a>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        網路報名
                    </a>
                    <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                        <li><a class="dropdown-item" href="./order.php">取得繳費帳號</a></li>
                        <li><a class="dropdown-item" href="./signup.php">填寫報名表</a></li>
                        <li><a class="dropdown-item" href="./alter.php">修改報名資料</a></li>
                        <li><a class="dropdown-item" href="./confirm.php">資料確認</a></li>
                        <li><a class="dropdown-item" href="./letter.php">推薦函作業</a></li>
                        <li><a class="dropdown-item" href="#">*准考證列印</a></li>
                    </ul>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        資料查詢
                    </a>
                    <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                        <li><a class="dropdown-item" href="./query_acc.php">報名費銷帳查詢</a></li>
                        <li><a class="dropdown-item" href="./query_score.php">*成績查詢</a></li>
                        <li><a class="dropdown-item" href="./query_pwd.php">查詢序號密碼</a></li>
                        <li><a class="dropdown-item" href="./query_signup.php">報名資料查詢、下載審查資料一覽表及信封封面</a></li>
                    </ul>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        報到相關
                    </a>
                    <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                        <li><a class="dropdown-item" href="./enroll_list.php">*正備取生報到狀況</a></li>
                        <li><a class="dropdown-item" href="./enroll_queue.php">正(備)取生申明就讀(遞補)意願</a></li>
                    </ul>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        罕用字說明
                    </a>
                    <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                        <li><a class="dropdown-item" href="./intro_sw.php">說明</a></li>
                        <li><a class="dropdown-item" href="https://aps.ncue.edu.tw/exampg_m/code_reply.doc">罕用字回復表</a></li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>
<script>
    $(function() {
        var path_array = window.location.pathname.split('/');
        var filename = path_array[path_array.length - 1];
        $("#navbarSupportedContent>ul>li").find(".active").removeClass("active");
        if (filename === "" || filename === "index.php") {
            $("#navbarSupportedContent>ul>li").eq(0).addClass("active");
        } else if (filename === "intro_registration.php" || filename === "intro_payment.php") {
            $("#navbarSupportedContent>ul>li").eq(1).addClass("active");
        } else if (filename === "order.php" || filename === "signup.php" || filename === "alter.php" || filename === "confirm.php" || filename === "letter.php") {
            $("#navbarSupportedContent>ul>li").eq(3).addClass("active");
        } else if (filename.indexOf("query_") !== -1) {
            $("#navbarSupportedContent>ul>li").eq(4).addClass("active");
        } else if (filename.indexOf("enroll_") !== -1) {
            $("#navbarSupportedContent>ul>li").eq(5).addClass("active");
        } else if (filename === "intro_sw.php") {
            $("#navbarSupportedContent>ul>li").eq(6).addClass("active");
        }
    });
</script>