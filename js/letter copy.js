
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
    $("table").on("click", "button#reset", function () {
        loadData();
    });

    //修改Email時,要先移除寄mail的按鈕,避免未存檔即寄
    $("td").on("keyup", "input[type='email']", function () {
        // alert("in");
        $(this).closest("tr").find("button").remove();
        //$(this).closest("button").remove();
    });

    //移除推薦人
    $("table").on("click", "button.btn-danger", function () {
        // alert("in");
        $(this).closest("tr").css('display', 'none');
        $(this).closest("tr").children("input").each(function () {
            $(this).removeAttr('required');
            $(this).attr('disabled', true);
        });
    });

    //新增推薦人
    $("#addnew").click(function () {
        if ($("#addrow2").css('display') !== 'none') {
            alert("最多可填二位推薦人資料");
            return false;
        }
        $("#addrow2 input").each(function () {
            $(this).attr('required', true);
            $(this).removeAttr('disabled');
        });
        $("#addrow2").css('display', '');

    });

    /*$("#form1").validate({
        submitHandler: function (form) {
            if (!confirm("部分資料送出後即不可修改(請參閱注意事項說明)，是否確定要送出??"))
                return false;
            $.ajax({
                type: "POST",
                url: "./API/signup/letter.php",
                dataType: "json",
                data: $("#form1").serialize() + "&oper=saveall",
                success: function (JData) {
                    console.log(JData);
                    if (JData.error_code == 9) {
                        alert("連線已中斷!");
                        window.top.location.href = "index.php";
                        return "";
                    } else if (JData.error_code > 0) {
                        alert(JData.error_message + "(錯誤代碼 :" + JData.error_code + ")");
                        loadData();
                        return "";
                    } else {
                        alert("資料儲存成功!");
                        loadData();
                        return "";
                    }
                },
                beforeSend: function () {
                    //$("#btn_upload").attr("disabled", true);
                    //$("#loading").show();
                },
                complete: function () {
                    //$("#loading").hide();
                    //$("#btn_upload").attr("disabled", false);
                },
                error: function (jqXHR, exception) {
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
                },
            });
        },
        rules: {
            "r_name[]": {
                required: true,
            },
            "r_org[]": {
                required: true,
            },
            "r_title[]": {
                required: true,
            },
            "r_email[]": {
                required: true,
            },
        },
        messages: {},
    });*/
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
                for (var i = 0; i < response.member.length; i++) {
                    $("form #addrow" + (i + 1)).css('display', '');
                    $("form #addrow" + (i + 1) + " input").each(function () {
                        $(this).attr('required', true);
                        $(this).removeAttr('disabled');
                    });
                    $("form [name='r_seq[]']").eq(i).val(response.member[i][0]);
                    $("form [name='r_name[]']").eq(i).val(response.member[i][1]);
                    $("form [name='r_org[]']").eq(i).val(response.member[i][2]);
                    $("form [name='r_title[]']").eq(i).val(response.member[i][3]);
                    $("form [name='r_email[]']").eq(i).val(response.member[i][4]);
                    $("form [name='r_email[]']").eq(i).after('<h6>寄發 E-mail：' + response.member[i][5] + '<br>推薦人讀取：' + response.member[i][6] + '<br>推薦人填寫：' + response.member[i][7] + '</h6>');

                    if (response.member[i][8] != null && response.member[i][8].length > 10) //已寄發
                    {
                        $("form [name='r_name[]']").eq(i).attr('readonly', true);
                        $("form [name='r_org[]']").eq(i).attr('readonly', true);
                        $("form [name='r_title[]']").eq(i).attr('readonly', true);
                    }
                    //推薦人已讀取或已填寫,不可修改任何蘭位
                    if ((response.member[i][9] != null && response.member[i][9].length > 10) ||
                        (response.member[i][10] != null && response.member[i][10].length > 10)) {
                        $("form [name='r_name[]']").eq(i).attr('readonly', true);
                        $("form [name='r_org[]']").eq(i).attr('readonly', true);
                        $("form [name='r_title[]']").eq(i).attr('readonly', true);
                        $("form [name='r_email[]']").eq(i).attr('readonly', true);
                    } else
                        $("form [name='r_email[]']").eq(i).after('<button type="button" class="btn btn-xs btn-success m-1"> 寄發Email通知 (請先存檔) </button>');

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
            $(this).removeAttr('disabled');
        });

}