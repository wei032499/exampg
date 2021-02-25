<?php
session_start();
if (!isset($_SESSION['username']))
    header("Location: ./management_login.php");

?>
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
        var formData = null;
        let urlParams = new URLSearchParams(window.location.search);
        if (urlParams.get('id') !== null) {
            $.holdReady(true);
            $.when(getData("./API/news/news.php?id=" + urlParams.get('id'), false)).done(function(data) {
                if (data['data'].length > 0) {
                    formData = data['data'][0];
                    $.holdReady(false);
                } else
                    alert("此公告不存在！");

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
                <div class="row">
                    <h3 class="col" style="letter-spacing: 0.2rem;min-width:14rem">
                        :::網站管理 <span style="color:red">(公告)</span>
                    </h3>
                    <div id="loginInfo" class="col-sm row justify-content-end mx-0 align-items-center">
                        <!--<div>Hi~ <span id="username"></span> </div>-->
                        <button type="button" id="mLogout" style="min-width:4rem" class="btn btn-info btn-sm ml-3">登出</button>
                    </div>
                </div>
            </div>
            <form class="border p-4 bg-white shadow rounded ">
                <div class="form-group row">
                    <label for="selectPosition" class="col-sm-2">位置</label>
                    <div class="col-sm-3">
                        <select class="form-control " id="selectPosition" name="position" required>
                            <option value="2" selected>預設</option>
                            <option value="1">置頂</option>
                            <option value="3">置底</option>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label for="editor1">公告內容：</label>
                    <textarea id="editor1" rows="10" cols="80"></textarea>
                    <input type="hidden" name="content">
                </div>
                <div class="row justify-content-center">
                    <button type="reset" style="min-width:4rem" class="btn btn-danger btn-sm col-1 mx-1">清除</button>
                    <button type="submit" style="min-width:4rem" class="btn btn-primary btn-sm col-1 mx-1">確定</button>
                </div>
            </form>

        </div>
    </section>


    <?php require_once("./module/footer.php") ?>

    <!--toastr-->
    <link rel="stylesheet" href="./css/toastr.min.css" />
    <script src="./js/toastr.min.js"></script>

    <!--management-->
    <script src="./js/management.js"></script>

    <!--CKEditor 4-->
    <script src="./ckeditor/ckeditor.js"></script>

    <script>
        var ckeditor = CKEDITOR.replace('editor1');
        $("form").on('reset', function() {
            ckeditor.setData('');
        });
        $("form").on('submit', function(e) {
            e.preventDefault();
            $("form [name='content']").val(ckeditor.getData());

            $("form [type='submit']").attr('disabled', true);
            $.ajax({
                    type: $("form").attr('method'),
                    url: "./API/news/news.php",
                    data: $("form").serialize(),
                    dataType: 'json'
                }).done(function(response) {
                    toastr.clear();
                    toastr.success("公告成功！");
                    window.location.replace('./management_home.php');

                })
                .fail(function(jqXHR, exception) {
                    $("form [type='submit']").removeAttr('disabled');
                    let response = jqXHR.responseJSON;
                    let msg = '';
                    if (response === undefined)
                        msg = exception + "\n" + "./API/news/news.php" + "\n" + jqXHR.responseText;
                    else if (response.hasOwnProperty('message')) {
                        msg = response.message;
                    } else {
                        msg = 'Uncaught Error.\n' + jqXHR.responseText;
                    }
                    toastr.clear();
                    toastr.error(msg);

                });
        });
        $(function() {
            if (formData !== null) {
                $("form").append("<input type='hidden' name='id' value='" + formData.id + "'>")
                $("form").attr('method', 'PUT');
                $("form [type='submit']").text("修改");
                fillForm(formData);
                ckeditor.setData(formData.content);
            } else
                $("form").attr('method', 'POST');
        });
    </script>



</body>

</html>