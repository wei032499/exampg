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

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css" integrity="sha384-B0vP5xmATw1+K9KRQjQERJvTumQW0nPEzvF6L/Z6nronJ3oUOFUFpCjEUQouq2+l" crossorigin="anonymous">
    <link rel="stylesheet" href="./css/custom.css" />
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-Piv4xVNRyMGpqkS2by6br4gNJ7DXjqk09RmUpJ8jgGtD7zP9yug3goQfGII0yAns" crossorigin="anonymous"></script>

    <!--DataTables-->
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/bs4/dt-1.10.23/datatables.min.css" />
    <script type="text/javascript" src="https://cdn.datatables.net/v/bs4/dt-1.10.23/datatables.min.js"></script>
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
                <div class="row">
                    <h3 class="col" style="letter-spacing: 0.2rem;">
                        :::網站管理
                    </h3>
                    <div id="loginInfo" class="col row justify-content-end mx-0 align-items-center">
                        <!--<div>Hi~ <span id="username"></span> </div>-->
                        <button type="button" id="mLogout" style="min-width:4rem" class="btn btn-info btn-sm ml-3">登出</button>
                    </div>
                </div>
            </div>

            <div class="shadow">
                <ul class="nav nav-tabs" id="myTab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <a class="nav-link " id="news-tab" data-toggle="tab" href="#news" role="tab" aria-controls="news" aria-selected="false">公告</a>
                    </li>
                </ul>




                <div class="tab-content" id="prointroTabContent">
                    <div class="tab-pane fade " id="news" role="tabpanel" aria-labelledby="news-tab">
                        <div class="card p-4">
                            <div class="mb-3">
                                <button type="button" class="btn btn-info btn-sm" onclick="window.location.assign('./management_news.php')">新增公告</button>
                            </div>
                            <table id="newsTable" class="table table-hover table-bordered" style="width:100%">
                                <thead>
                                    <tr>
                                        <th>公告事項</th>
                                        <th>公告日期</th>
                                        <th>操作</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </section>

    <?php require_once("./module/footer.php") ?>

    <!--toastr-->
    <link rel="stylesheet" href="./css/toastr.min.css" />
    <script src="./js/toastr.min.js"></script>

    <!--management-->
    <script src="./js/management.js"></script>

    <script>
        $(function() {
            if (window.location.hash === "")
                $('#myTab a[href="#news"]').tab('show');
            else
                $('#myTab a[href="' + window.location.hash + '"]').tab('show');
        });
        $('a[data-toggle="tab"]').on('show.bs.tab', function(e) {
            window.location.hash = e.target.hash;
            window.scroll(0, 0);
        });


        $(function() {
            $('#newsTable').DataTable({
                "ordering": false,
                "lengthMenu": [
                    [10, 25, 50, -1],
                    [10, 25, 50, "All"]
                ],
                "ajax": "./API/news/news.php",
                "columns": [{
                        "data": "content"
                    },
                    {
                        "data": "date"
                    },
                    {
                        "render": function(data, type, row, meta) {
                            data = '<button type="button" class="btn btn-warning btn-sm m-1" style="min-width:3rem" onclick="window.location.assign(\'./management_news.php?id=' + row.id + '\')">修改</button>';
                            data += '<button type="button" class="btn btn-danger btn-sm m-1" style="min-width:3rem" onclick="deleteNews(' + row.id + ')">刪除</button>';
                            return data;
                        }
                    }
                ],
                "language": {
                    "decimal": "",
                    "emptyTable": "無資料",
                    "info": "顯示 _START_ 到 _END_ 共 _TOTAL_ 筆資料",
                    "infoEmpty": "顯示 0 到 0 共 0 筆資料",
                    "infoFiltered": "(filtered from _MAX_ total entries)",
                    "infoPostFix": "",
                    "thousands": ",",
                    "lengthMenu": "顯示 _MENU_ 筆資料",
                    "loadingRecords": "載入中...",
                    "processing": "處理中...",
                    "search": "搜尋:",
                    "zeroRecords": "查無符合的資料",
                    "paginate": {
                        "first": "首頁",
                        "last": "尾頁",
                        "next": "下一頁",
                        "previous": "上一頁"
                    },
                    "aria": {
                        "sortAscending": ": activate to sort column ascending",
                        "sortDescending": ": activate to sort column descending"
                    }
                }

            });
        });

        function deleteNews(id) {
            if (confirm("確定刪除此公告？")) {
                $.ajax({
                        type: "DELETE",
                        url: "./API/news/news.php",
                        data: {
                            id: id
                        },
                        dataType: 'json'
                    }).done(function(response) {
                        $('#newsTable').DataTable().ajax.reload();
                        toastr.clear();
                        toastr.success("刪除成功！");

                    })
                    .fail(function(jqXHR, exception) {
                        let response = jqXHR.responseJSON;
                        let msg = '';
                        if (response === undefined)
                            msg = exception;
                        else if (response.hasOwnProperty('message')) {
                            msg = response.message;
                        } else {
                            msg = 'Uncaught Error.\n' + jqXHR.responseText;
                        }
                        toastr.clear();
                        toastr.error(msg);

                    });
            }
        }
    </script>


</body>

</html>