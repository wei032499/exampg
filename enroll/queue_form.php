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
        $.holdReady(true);
        var formData = null;
        $.when(getData("./API/enroll/status.php")).done(function(_formData) {
            formData = _formData;
            $.holdReady(false);

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
                <h3 style="letter-spacing: 0.2rem;min-width:14rem">
                    :::申明就讀(遞補)意願 <span style="color:red">(使用者登入)</span>
                </h3>
            </div>
            <div class="border p-4 bg-white shadow rounded row justify-content-center">
                <div class=" col-md-9">
                    <div class="row">
                        <div class="col-sm-2 my-2" style="min-width:8rem">姓名：</div>
                        <div class="col-sm my-2" id="name"></div>
                    </div>
                    <div class="row">
                        <div class="col-sm-2 my-2" style="min-width:8rem">性別：</div>
                        <div class="col-sm my-2" id="sex"></div>
                    </div>
                    <div class="row ">
                        <div class="col-sm-2 my-2" style="min-width:8rem">錄取結果：</div>
                        <div class="col-sm  align-items-center px-4 py-2 border border-success rounded shadow-sm my-2" id="result">

                        </div>
                    </div>

                    <hr>

                    <div class="d-flex justify-content-center col my-4">
                        <h5>申請狀態：<span class="font-weight-bold" id="intention"></span></h5>
                    </div>
                    <form>
                        <fieldset class="d-flex justify-content-center col  p-2 border border-primary  shadow-sm">
                            <legend>就讀(遞補)意願</legend>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="item" id="item1" value="1" required>
                                <label class="form-check-label " for="item1"><span class="font-weight-bold"><u>申明</u></span>就讀(遞補)意願</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="item" id="item2" value="0" required>
                                <label class="form-check-label " for="item2"><span class="font-weight-bold" style="color:red"><u>放棄</u></span>就讀(遞補)意願</label>
                            </div>
                        </fieldset>
                        <small>※聯合招生系所錄取之正(備)取生，僅須申明一次就讀(遞補)意願；如未申明，則視同放棄就讀(遞補)意願資格</small>


                        <div class="row mx-2 mt-5 justify-content-center ">
                            <label class="col" style="min-width: 12rem;max-width: 15rem;" for="code">請輸入通知單上之認證碼：</label>
                            <div class="col" style="min-width: 12rem;max-width: 12rem;">
                                <input type="password" class="form-control " id="code" name="code" required>
                            </div>
                        </div>

                        <div class="row mt-4 justify-content-center">
                            <button type="button" id="logout2" style="min-width:4rem" class="btn btn-danger btn-sm col-1 mx-1">離開</button>
                            <button type="submit" style="min-width:4rem" class="btn btn-primary btn-sm col-1 mx-1">送出</button>
                        </div>

                    </form>
                    <div class="d-flex justify-content-center col my-2" style="color:red">
                        (※按『送出』後，請務必再次確認是否已登錄成功！)
                    </div>
                    <div class="d-flex justify-content-center col my-2">
                        ※ <a target="_blank" id="link"></a> ※
                    </div>


                </div>
            </div>
        </div>
    </section>


    <?php require_once("./module/footer.php") ?>
    <!--toastr-->
    <link rel="stylesheet" href="./css/toastr.min.css" />
    <script src="./js/toastr.min.js"></script>


    <script>
        $(function() {
            $("#name").text(formData.name);
            $("#sex").text(formData.sex);
            for (var i = 0; i < formData.result.length; i++) {
                var row = "";
                if (formData.result[i].ranking === 0)
                    row = '<div class="row"><div class="col" >' + formData.result[i].dept + '</div><div class="col-5" id="rank">' + "正取" + '</div></div>';
                else
                    row = '<div class="row"><div class="col" >' + formData.result[i].dept + '</div><div class="col-5" id="rank">' + "備取第 " + formData.result[i].ranking + "名" + '</div></div>';

                $("#result").append(row);
            };
            $("#intention").removeClass();
            if (formData.intention === 0) {
                $("#intention").addClass('text-danger');
                $("#intention").addClass('font-weight-bold');
                $("#intention").text('尚未登錄就讀(遞補)意願！');
            } else if (formData.intention === 1) {
                $("#intention").addClass('text-primary');
                $("#intention").addClass('font-weight-bold');
                $("#intention").text('您已申明就讀(遞補)意願！');
            } else if (formData.intention === -1) {
                $("#intention").addClass('text-warning');
                $("#intention").addClass('font-weight-bold');
                $("#intention").text('您已放棄就讀(遞補)意願！');
            } else {
                alert("error");
                window.location.replace('./');
            }

            if (typeof formData.link !== "undefined") {
                $("#link").attr('href', formData.link.url);
                $("#link").text(formData.link.title);
            }
        });
        $("#logout2").on('click', function() {
            if (confirm("確定離開嗎？"))
                $.ajax({
                    type: 'POST',
                    url: "./API/auth/logout.php",
                    dataType: 'json',
                    data: {
                        oper: 'queue'
                    }
                }).done(function(response) {
                    sessionStorage.clear();
                    window.location.replace('./');
                })
                .fail(function(jqXHR, exception) {
                    var response = jqXHR.responseJSON;
                    var msg = '';
                    if (response === undefined)
                        msg = exception + "\n" + "./API/auth/logout.php" + "\n" + jqXHR.responseText;
                    else if (response.hasOwnProperty('message')) {
                        msg = response.message;
                    } else {
                        msg = 'Uncaught Error.\n' + jqXHR.responseText;
                    }
                    alert(msg);
                });
        });

        $("form").on('submit', function(e) {
            e.preventDefault();
            $("form [type='submit']").attr('disabled', true);
            $.ajax({
                    type: 'POST',
                    url: "./API/enroll/status.php",
                    dataType: 'json',
                    data: $("form").serialize()
                }).done(function(response) {
                    toastr.clear();

                    $("form [type='submit']").removeAttr('disabled');
                    $("#intention").removeClass();
                    if (response.intention === 0) {
                        $("#intention").addClass('text-danger');
                        $("#intention").addClass('font-weight-bold');
                        $("#intention").text('尚未登錄就讀(遞補)意願！');
                        toastr.success('尚未登錄就讀(遞補)意願！');
                    } else if (response.intention === 1) {
                        $("#intention").addClass('text-primary');
                        $("#intention").addClass('font-weight-bold');
                        $("#intention").text('您已申明就讀(遞補)意願！');
                        toastr.success('您已申明就讀(遞補)意願！');
                    } else if (response.intention === -1) {
                        $("#intention").addClass('text-warning');
                        $("#intention").addClass('font-weight-bold');
                        $("#intention").text('您已放棄就讀(遞補)意願！');
                        toastr.success('您已放棄就讀(遞補)意願！');
                    }
                    $("form")[0].reset();
                })
                .fail(function(jqXHR, exception) {
                    $("form [type='submit']").removeAttr('disabled');

                    toastr.clear();
                    var response = jqXHR.responseJSON;
                    var msg = '';
                    if (response === undefined)
                        msg = exception + "\n" + "./API/enroll/status.php" + "\n" + jqXHR.responseText;
                    else if (response.hasOwnProperty('message')) {
                        msg = response.message;
                    } else {
                        msg = 'Uncaught Error.\n' + jqXHR.responseText;
                    }
                    toastr.error(msg);
                });
        });
    </script>



</body>

</html>