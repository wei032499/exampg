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
            <form class="border p-4 bg-white shadow rounded ">
                <div class="form-group row justify-content-center">
                    <label for="inputIDNumber" class="col-sm-1" style="min-width:8rem">身分證字號</label>
                    <input type="text" class="form-control col-sm-4" id="inputIDNumber" name="IDNumber" pattern="[A-Z]\d{9}" required>
                </div>
                <div class="form-group row justify-content-center">
                    <label for="inputSid" class="col-sm-1" style="min-width:8rem">准考証號</label>
                    <input type="text" class="form-control col-sm-4" id="inputSid" name="sid" required>
                </div>
                <p style="text-align: center;">
                    ※ 本功能僅提供正(備)取生申明就讀(遞補)意願。
                </p>
                <div class="row justify-content-center mt-2">
                    <button type="submit" style="min-width:4rem" class="btn btn-primary btn-sm col-1 mx-1">下一步</button>
                </div>

            </form>
        </div>
    </section>


    <?php require_once("./module/footer.php") ?>

    <!--toastr-->
    <link rel="stylesheet" href="./css/toastr.min.css" />
    <script src="./js/toastr.min.js"></script>

    <script>
        $("form").on('submit', function(e) {
            e.preventDefault();

            $.ajax({
                    type: 'POST',
                    url: './API/auth/login.php',
                    data: $("form").serialize(),
                    dataType: 'json'

                }).done(function(response) {
                    toastr.clear();
                    toastr.success("登入成功！");
                    window.location.replace('./enroll_queue.php')

                })
                .fail(function(jqXHR, exception) {
                    // toastr.remove();
                    toastr.clear();
                    let response = jqXHR.responseJSON;
                    let msg = '';
                    if (response === undefined)
                        msg = exception + "\n" + './API/auth/login.php' + "\n" + jqXHR.responseText;
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