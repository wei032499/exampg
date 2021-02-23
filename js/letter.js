
$(function () {

    loadData();
    //for select
    $.validator.addMethod(
        "notEqualsto",
        function (value, element, arg) {
            return arg != value;
        },
        "您尚未選擇!"
    );

    //寄發Email通知
    $("table>#mybody").on("click", "button.btn-success", function () {
        // alert("in");
        var seq = $(this).closest("tr").find('[name="r_seq[]"]').val();
        if (seq <= 0 || seq > 2) return false;
        else {
            if (!confirm("是否確定要寄發Email通知推薦人??")) return false;
        }
        $(this).attr('disabled', true);
        $.ajax({
            url: "./API/signup/letter.php",
            data: {
                oper: "sendmail",
                r_seq: $(this).closest("tr").find('[name="r_seq[]"]').val(),
            },
            type: "POST",
            dataType: "json"
        })
            .done(function (response) {
                alert("寄發Email通知完成");
                loadData();
                $(this).removeAttr('disabled');
            })
            .fail(function (jqXHR, exception) {
                let response = jqXHR.responseJSON;
                let msg = '';
                if (response === undefined)
                    msg = exception;
                else if (response.hasOwnProperty('message')) {
                    msg = response.message;
                } else {
                    msg = 'Uncaught Error.\n' + jqXHR.responseText;
                }
                alert(msg);
                $(this).removeAttr('disabled');
            });

    });

    //取消消改.重新載入
    $("form #reset").on("click", function () {
        loadData();
    });

    //修改Email時,要先移除寄mail的按鈕,避免未存檔即寄
    $("input[type='email']").on("keyup", function () {
        // alert("in");
        $(this).closest("button").remove();
        //$(this).closest("button").remove();
    });

    //移除推薦人
    $("table").on("click", "button.btn-danger", function () {
        // alert("in");
        $(this).closest("tr").remove();
        var count = 1;
        $("tbody#mybody>tr").each(function (e, r) {
            if (count == 1) $(this).find("td:eq(0)").html(count + '<input type="hidden" name="r_seq[]" value="' + count + '">');
            else
                $(this)
                    .find("td:eq(0)")
                    .html(
                        '<button type="button" class="btn btn-danger btn-sm" data-placement="right" title="移除推薦人">移除</button><br> ' +
                        count +
                        '<input type="hidden" name="r_seq[]" value="' +
                        count +
                        '">'
                    );
            count++;
            //console.log($(this).find("td:eq(0)").html());
        });
    });

    //新增推薦人
    $("#addnew").click(function () {
        var count = $("tbody#mybody>tr").length;
        if (count >= 2) {
            alert("最多可填二位推薦人資料");
            return false;
        }
        $("tbody#mybody").append(
            '<tr class="text-center" id="addrow"><td ><button type="button" class="btn btn-danger btn-sm" data-placement="right" title="移除推薦人">移除</button><br>' +
            (count + 1) + '<input type="hidden" name="r_seq[]" value="' + (count + 1) + '"></td><td ><input type="text" name="r_name[]" class="form-control valid" aria-invalid="false" value=""  required="required" title="" /></td><td ><input type="text" name="r_org[]" class="form-control valid" aria-invalid="false" value=""  required="required" title="" /></td><td ><input type="text" name="r_title[]" class="form-control valid" aria-invalid="false" value=""  required="required" title="" /></td><td class="col-md-4"><input type="email" name="r_email[]" class="form-control valid" aria-invalid="false" value="" required="required" title="" /></td></tr>'
        );
    });

    $("#form1").validate({
        submitHandler: function (form) {
            if (!confirm("部分資料送出後即不可修改(請參閱注意事項說明)，是否確定要送出??"))
                return false;
            $("#form1 [type='submit']").attr('disabled', true);
            $.ajax({
                type: "POST",
                url: "./API/signup/letter.php",
                dataType: "json",
                data: $("#form1").serialize() + "&oper=saveall"
            })
                .done(function (response) {
                    alert("資料儲存成功!");
                    $("#form1 [type='submit']").removeAttr('disabled');
                    loadData();
                })
                .fail(function (jqXHR, exception) {
                    let response = jqXHR.responseJSON;
                    let msg = '';
                    if (response === undefined)
                        msg = exception;
                    else if (response.hasOwnProperty('message')) {
                        msg = response.message;
                    } else {
                        msg = 'Uncaught Error.\n' + jqXHR.responseText;
                    }
                    alert(msg);
                    $("#form1 [type='submit']").removeAttr('disabled');
                });
        }
    });
});

function loadData() {
    $.ajax({
        url: "./API/signup/letter.php",
        type: "GET",
        cache: false,
        dataType: "json"
    })
        .done(function (response) {
            $("input[name='research']").val(response.research[0]);
            if (response.member.length > 0) {
                $("#mybody").children().remove();
                for (var i = 0; i < response.member.length; i++) {
                    let disabled_name = "";
                    let disabled_org = "";
                    let disabled_title = "";
                    let disabled_email = "";
                    if (response.member[i][8] != null &&
                        response.member[i][8].length > 10) {
                        //使用"disabled"無法傳送表單欄位,readonly可以
                        disabled_name = '  readonly="readonly" ';
                        disabled_org = '  readonly="readonly" ';
                        disabled_title = '  readonly="readonly" ';
                        //  disabled_email =" disabled";
                    }
                    //推薦人已讀取或已填寫,不可修改任何蘭位
                    if ((response.member[i][9] != null &&
                        response.member[i][9].length > 10) ||
                        (response.member[i][10] != null && response.member[i][10].length > 10)) {
                        disabled_name = '  readonly="readonly" ';
                        disabled_org = '  readonly="readonly" ';
                        disabled_title = '  readonly="readonly" ';
                        disabled_email = '  readonly="readonly" ';
                    }
                    $("#mybody").append(
                        '<tr class="text-center" id="addrow"><td >' + (response.member[i][0]) + '<input type="hidden" name="r_seq[]" value="' + (response.member[i][0]) + '"></td><td ><input type="text" name="r_name[]" class="form-control valid" aria-invalid="false" value="' + (response.member[i][1]) + '"' + (disabled_name) + ' required="required" title="" /></td><td ><input type="text" name="r_org[]" class="form-control valid" aria-invalid="false" value="' + (response.member[i][2]) + '"  ' + (disabled_org) + ' required="required" title="" /></td><td ><input type="text" name="r_title[]" class="form-control valid" aria-invalid="false" value="' + (response.member[i][3]) + '"  ' + (disabled_title) + ' required="required" title="" /></td><td class="col-md-4"><input type="email" name="r_email[]" class="form-control valid" aria-invalid="false" value="' + (response.member[i][4]) + '"  ' + (disabled_email) + ' required="required" title="" /><button type="button" class="btn btn-sm btn-success m-1"> 寄發Email通知 (請先存檔) </button><div class="text-left"><small>寄發 E-mail：' + (response.member[i][5]) + '<br>推薦人讀取：' + (response.member[i][6]) + '<br> 推薦人填寫：' + (response.member[i][7]) + '</small></div></td></tr>'
                    );
                    //修改Email時,要先移除寄mail的按鈕,避免未存檔即寄
                    $("#mybody tr").last().find("input[type='email']").on("keyup", function () {
                        $(this).siblings("button").remove();
                    });
                }
            }
        })
        .fail(function (jqXHR, exception) {
            let response = jqXHR.responseJSON;
            let msg = '';
            if (response === undefined)
                msg = exception;
            else if (response.hasOwnProperty('message')) {
                msg = response.message;
            } else {
                msg = 'Uncaught Error.\n' + jqXHR.responseText;
            }
            alert(msg);
        });

}