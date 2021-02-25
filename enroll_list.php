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

    <link rel="stylesheet" href="./css/datatables.min.css" />

    <script src="./js/datatables.min.js"></script>

    <script>
        var deptObj;
        $.when(getData("./API/dept/list.php")).done(function(_deptObj) {
            deptObj = _deptObj.data;
            $(function() {
                // fill department list
                $("form [name='dept']").find('option').remove();
                for (let i = 0; i < deptObj.dept.length; i++)
                    $("form [name='dept']").append("<option value='" + deptObj.dept[i].dept_id + "'>" + deptObj.dept[i].name + "</option>");
            });
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
                    :::報到狀況
                </h3>
            </div>
            <div class=" p-4 bg-white shadow rounded ">
                <div class="row px-2 mb-3">
                    <select class="form-control col m-1" name="dept">
                        <option selected disabled hidden>報考系所</option>
                    </select>
                    <select class="form-control col m-1">
                        <option selected disabled hidden>報考組別</option>
                    </select>
                </div>
                <table id="example" class="table table-striped table-bordered" style="width:100%">
                    <thead>
                        <tr>
                            <th>考生編號</th>
                            <th>正備取</th>
                            <th>報考類別</th>
                            <th>報到狀態</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                    <tfoot>
                        <tr>
                            <th>考生編號</th>
                            <th>正備取</th>
                            <th>報考類別</th>
                            <th>報到狀態</th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </section>


    <?php require_once("./module/footer.php") ?>

    <script>
        $(function() {
            $('#example').DataTable({
                "ordering": false,
                "lengthMenu": [
                    [10, 25, 50, -1],
                    [10, 25, 50, "All"]
                ],
                "ajax": "./API/enroll/status.php",
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
    </script>

</body>

</html>