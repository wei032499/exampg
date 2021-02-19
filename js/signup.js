

//initail
$(function () {
    if (typeof deptObj !== 'undefined') {
        // fill department list
        $("form [name='dept']").empty().append('<option selected hidden disabled></option>');
        for (let i = 0; i < deptObj.dept.length; i++)
            $("form [name='dept']").append("<option value='" + deptObj.dept[i].dept_id + "'>" + deptObj.dept[i].name + "</option>");

        //報考系所
        $("form [name='dept']").on('change', function () {
            $("#subject").css('visibility', 'hidden');
            $("#subject>div").empty();
            $("#union").css('display', 'none');
            $("#union>div").empty();
            $("form [name='orastatus_id']").empty().append('<option selected hidden disabled></option>');
            $("form [name='organize_id']").empty();

            for (let i = 0; i < deptObj.group[$("form [name='dept']").val()].length; i++)
                $("form [name='organize_id']").append("<option value='" + deptObj.group[$("form [name='dept']").val()][i].group_id + "'>" + deptObj.group[$("form [name='dept']").val()][i].name + "</option>");
            if (deptObj.group[$("form [name='dept']").val()].length > 1)
                $("form [name='organize_id']").append('<option selected hidden disabled></option>');
            else
                $("form [name='organize_id']>option:selected").change().blur();
            let index = deptObj.dept.map(function (e) {
                return e.dept_id;
            }).indexOf($("form [name='dept']").val());

            if (deptObj.dept[index].upload_type > 1) //upload_type 審查資料繳交方式:  1:郵寄  2:上傳  3:郵寄+上傳
            {
                checkUploadStatus();
                $("#upload_row").css('display', '');
                $("form [name='file']").removeAttr('disabled');
            } else {
                $("#upload_row").css('display', 'none');
                $("form [name='file']").attr('disabled', true);
            }

            if (deptObj.dept[index].e_place === 1) //限彰化考區
            {
                $("form [name='place'][value='1']")[0].checked = true;
                $("form [name='place'][value='2']").parent().css('display', 'none');
                $("form [name='place'][value='2']").attr('disabled', true);
            } else {
                $("form [name='place'][value='2']").parent().css('display', '');
                $("form [name='place'][value='2']").removeAttr('disabled');
            }


            if (deptObj.dept[index].union_type === "5") //不須選考科組別之聯合
            {

                $.when(getData("./API/dept/union.php?dept_id=" + this.value)).done(function (_deptObj) {
                    let options = "<option value='" + $("form [name='dept']>option:selected").val() + "' selected>" + $("form [name='dept']>option:selected").text() + "</option>";
                    $("#union>div").append('<select class="form-control form-group" name="union_priority[]" readonly required>' + options + '</select>');

                    let unionDepts = _deptObj.data;
                    options = "<option value='-1' selected>放棄志願</option>";
                    for (let i = 0; i < unionDepts.length; i++)
                        if (unionDepts[i].dept_id !== $("form [name='dept']>option:selected").val())
                            options += "<option value='" + unionDepts[i].dept_id + "'>" + unionDepts[i].name + "</option>";
                    for (let i = 0; i < unionDepts.length - 1; i++) //生成剩下可選聯合招生系所
                        $("#union>div").append('<select class="form-control form-group" name="union_priority[]" required>' + options + '</select>');

                    if (typeof formData !== 'undefined' && typeof formData['union_priority'] !== 'undefined') {
                        for (let i = 0; i < formData['union_priority'].length; i++) {
                            $("form [name='union_priority[]']:eq(" + i + ") >option[value='" + formData['union_priority'][i] + "']").removeAttr("disabled");
                            $("form [name='union_priority[]']:eq(" + i + ") ").val(formData['union_priority'][i]).change();
                        }
                        delete formData['union_priority'];
                    }
                    if (typeof isConfirmForm !== "undefined" && isConfirmForm === true) {
                        $("form .form-control").addClass('form-control-plaintext').removeClass('form-control');
                        $("form select option").not(":selected").remove();
                        $("form [type='radio']:not(:checked)").parent().remove();
                        $("form [type='radio']").parent().css('color', '#00008b');
                        $("form [type='radio']").attr('type', 'hidden');
                        $("form input").css('color', '#00008b');
                        $("form select").css('color', '#00008b');
                    }
                    $("#union").css('display', '');

                });

            }

            if (deptObj.dept[index].test_type === "3") //3選2
                $("#subject_msg").text("*請選擇 " + deptObj.dept[index].choose + " 項考科");
            else
                $("#subject_msg").text("");

        });
        $("form [name='organize_id']").on('change', function () {
            $("form [name='orastatus_id']").empty();
            for (let i = 0; i < deptObj.status[$("form [name='dept']").val()][$("form [name='organize_id']").val()].length; i++)
                $("form [name='orastatus_id']").append("<option value='" + deptObj.status[$("form [name='dept']").val()][$("form [name='organize_id']").val()][i].status_id + "'>" + deptObj.status[$("form [name='dept']").val()][$("form [name='organize_id']").val()][i].name + "</option>");
            if (deptObj.status[$("form [name='dept']").val()][$("form [name='organize_id']").val()].length > 1)
                $("form [name='orastatus_id']").append('<option selected hidden disabled></option>');
            else
                $("form [name='orastatus_id']>option:selected").change().blur();
        });
        $("form [name='orastatus_id']").on('change', function () {
            $("#subject").css('visibility', 'hidden');
            $("#subject>div").empty();

            let isOptional = false;
            let index = deptObj.dept.map(function (e) {
                return e.dept_id;
            }).indexOf($("form [name='dept']").val());
            if (deptObj.dept[index].union_type !== "5") //不為不須選考科組別之聯合
            {
                $("#union").css('display', 'none');
                $("#union>div").empty();
            }
            if (deptObj.dept[index].test_type === "3") //3選2
            {
                isOptional = true;

                let keys = Object.keys(deptObj.subject[$("form [name='dept']").val()][$("form [name='organize_id']").val()][$("form [name='orastatus_id']").val()]);
                for (let i = 0; i < keys.length; i++) {
                    let options = "";
                    let subject_count = deptObj.subject[$("form [name='dept']").val()][$("form [name='organize_id']").val()][$("form [name='orastatus_id']").val()][keys[i]].length;
                    for (let j = 0; j < subject_count; j++) {
                        let subject_id = deptObj.subject[$("form [name='dept']").val()][$("form [name='organize_id']").val()][$("form [name='orastatus_id']").val()][keys[i]][j].subject_id;
                        let subject_name = deptObj.subject[$("form [name='dept']").val()][$("form [name='organize_id']").val()][$("form [name='orastatus_id']").val()][keys[i]][j].name;
                        options += "<option value='" + subject_id + "'>" + subject_name + "</option>";

                    }
                    $("#subject>div").append('<div class="row form-group align-items-center" style="margin-left:2rem"><input type="checkbox" value="' + keys[i] + '" class="form-check-input" name="section[]" ><select class="form-control" name="subject[]" disabled>' + options + '</select></div>');
                }
                $("form [name='section[]']").on('change', function () {
                    if ($(this).prop("checked")) {
                        $(this).siblings("select").attr('required', true);
                        $(this).siblings("select").removeAttr('disabled');
                    } else {
                        $(this).siblings("select").attr('disabled', true);
                        $(this).siblings("select").removeAttr('required');
                    }
                    $("form [name='subject[]']").change();


                });
            } else {
                //同一section或有多個subjects，即表示可選考科

                let keys = Object.keys(deptObj.subject[$("form [name='dept']").val()][$("form [name='organize_id']").val()][$("form [name='orastatus_id']").val()]);
                for (let i = 0; i < keys.length; i++) {
                    let options = "<option selected hidden disabled></option>";
                    let subject_count = deptObj.subject[$("form [name='dept']").val()][$("form [name='organize_id']").val()][$("form [name='orastatus_id']").val()][keys[i]].length;

                    if (subject_count > 1) //有1個以上考科才顯示選擇
                    {
                        isOptional = true;
                        for (let j = 0; j < subject_count; j++) {
                            let subject_id = deptObj.subject[$("form [name='dept']").val()][$("form [name='organize_id']").val()][$("form [name='orastatus_id']").val()][keys[i]][j].subject_id;
                            let subject_name = deptObj.subject[$("form [name='dept']").val()][$("form [name='organize_id']").val()][$("form [name='orastatus_id']").val()][keys[i]][j].name;
                            options += "<option value='" + subject_id + "'>" + subject_name + "</option>";

                        }
                        $("#subject>div").append('<select class="form-control form-group" name="subject[]" required>' + options + '</select>');
                    }
                }

            }
            if (isOptional)
                $("#subject").css('visibility', '');


            $("form [name='subject[]']").on('change', function () {
                if (this.value === "")
                    return false;
                let index = deptObj.dept.map(function (e) {
                    return e.dept_id;
                }).indexOf($("form [name='dept']").val());
                if (deptObj.dept[index].union_type !== "5") //不為不須選考科組別之聯合
                {
                    $.when(getData("./API/dept/union.php?subject_id=" + this.value)).done(function (_deptObj) {
                        $("#union>div").empty();
                        let options = "<option value='" + $("form [name='dept']>option:selected").val() + "' selected>" + $("form [name='dept']>option:selected").text() + "</option>";
                        $("#union>div").append('<select class="form-control form-group" name="union_priority[]" readonly required>' + options + '</select>');

                        let unionDepts = _deptObj.data;
                        options = "<option value='-1' selected>放棄志願</option>";
                        for (let i = 0; i < unionDepts.length; i++)
                            if (unionDepts[i].dept_id !== $("form [name='dept']>option:selected").val())
                                options += "<option value='" + unionDepts[i].dept_id + "'>" + unionDepts[i].name + "</option>";

                        for (let i = 0; i < unionDepts.length - 1; i++) //生成剩下可選聯合招生系所
                            $("#union>div").append('<select class="form-control form-group" name="union_priority[]" required>' + options + '</select>');

                        if (typeof formData !== 'undefined' && typeof formData['union_priority'] !== 'undefined') {
                            for (let i = 0; i < formData['union_priority'].length; i++) {
                                $("form [name='union_priority[]']:eq(" + i + ") >option[value='" + formData['union_priority'][i] + "']").removeAttr("disabled");
                                $("form [name='union_priority[]']:eq(" + i + ") ").val(formData['union_priority'][i]).change();
                            }
                            delete formData['union_priority'];
                        }
                        if (typeof isConfirmForm !== "undefined" && isConfirmForm === true) {
                            $("form .form-control").addClass('form-control-plaintext').removeClass('form-control');
                            $("form select option").not(":selected").remove();
                            $("form [type='radio']:not(:checked)").parent().remove();
                            $("form [type='radio']").parent().css('color', '#00008b');
                            $("form [type='radio']").attr('type', 'hidden');
                            $("form input").css('color', '#00008b');
                            $("form select").css('color', '#00008b');
                        }
                        $("#union").css('display', '');
                    });
                }
                let section = this.value.substr(5, 1);
                index = deptObj.subject[$("form [name='dept']").val()][$("form [name='organize_id']").val()][$("form [name='orastatus_id']").val()][section].map(function (e) {
                    return e.subject_id;
                }).indexOf(this.value);
                let subject = deptObj.subject[$("form [name='dept']").val()][$("form [name='organize_id']").val()][$("form [name='orastatus_id']").val()][section][index];
                if (subject.upload !== undefined && subject.upload === false) {
                    $("#upload_row").css('display', 'none');
                    $("form [name='file']").attr('disabled', true);
                } else {
                    let index = deptObj.dept.map(function (e) {
                        return e.dept_id;
                    }).indexOf($("form [name='dept']").val());
                    if (deptObj.dept[index].upload_type > 1) //upload_type 審查資料繳交方式:  1:郵寄  2:上傳  3:郵寄+上傳
                    {
                        checkUploadStatus();
                        $("#upload_row").css('display', '');
                        $("form [name='file']").removeAttr('disabled');
                    }
                }
            });
        });
    }

    /*$("form [name='dept']>option:checked").change();
    $("form [name='organize_id']>option:checked").change();
    $("form [name='orastatus_id']>option:checked").change();*/
    $("form [name='disabled']:checked").change();
    $("form [name='disabled_type']:checked").change();
    $("form [name='prove_type']:checked").change();

});
//身心障礙
$("form [name='disabled']").on('change', function () {
    if (this.value === '1') {
        $("#disabled_extra").css('display', '');
        $("form [name='disabled_type']").attr('required', true);
        $("form [name='disabled_type']").removeAttr('disabled');
    } else {
        $("#disabled_extra").css('display', 'none');
        $("form [name='disabled_type']").removeAttr('required');
        $("form [name='disabled_type']").attr('disabled', true);
    }
});

$("form [name='disabled_type']").on('change', function () {
    if (this.value === '6') {
        $("form [name='comments']").css('display', '');
        $("form [name='comments']").attr('required', true);
        $("form [name='comments']").removeAttr('disabled');
    } else {
        $("form [name='comments']").css('display', 'none');
        $("form [name='comments']").removeAttr('required');
        $("form [name='comments']").attr('disabled', true);
    }
});

//應考學歷
$("form [name='prove_type']").on('change', function () {
    $("#proveTabContent .tab-pane").removeClass("active");
    $("#proveTabContent .tab-pane").removeClass("show");
    $("form [name='prove_type']").removeClass("active");
    $("form [name='prove_type']").removeClass("active");
    $(this).tab('show');

    $("form #proveTabContent input").removeAttr('required')
    $("form #proveTabContent input").attr('disabled', true);
    $("form #proveTabContent select").removeAttr('required')
    $("form #proveTabContent select").attr('disabled', true);

    $("form #proveTabContent .active input").removeAttr('disabled')
    $("form #proveTabContent .active input").attr('required', true);
    $("form #proveTabContent .active select").removeAttr('disabled')
    $("form #proveTabContent .active select").attr('required', true);
});

//"同上"按鈕
$("#address2_btn").on('click', function () {
    $("form [name='zipcode2']").val($("form [name='zipcode']").val()).blur();
    $("form [name='address2']").val($("form [name='address']").val()).blur();
});

//備審資料上傳狀態
function checkUploadStatus() {
    $('form #fileLink').text('');
    $.ajax({
        type: 'GET',
        url: './API/signup/file.php',
        dataType: 'text'
    }).done(function (response) {
        $('form #fileLink').css('color', '');
        $('form #fileLink').addClass('color-info');
        $('form #fileLink').text('檔案已上傳');
        $('form #fileLink').attr('href', './API/signup/file.php?export=download');

    }).fail(function (jqXHR, exception) {
        if (jqXHR.status === 404) {
            $('form #fileLink').removeClass('color-info');
            $('form #fileLink').css('color', 'red');
            $('form #fileLink').text('備審資料檔案尚未上傳');
            $('form #fileLink').removeAttr('href');
        } else {
            toastr.clear();
            let response = jqXHR.responseJSON;
            let msg = '';
            if (response === undefined)
                msg = exception;
            else if (response.hasOwnProperty('message')) {
                msg = response.message;
            } else {
                msg = 'Uncaught Error.\n' + jqXHR.responseText;
            }
            toastr.error(msg);
        }


    });
}



//備審資料上傳
$("form [name='file']").on('change', function () {

    $('form #fileLink').removeClass('color-info');
    $('form #fileLink').css('color', 'red');
    $('form #fileLink').text('備審資料檔案尚未上傳');
    $('form #fileLink').removeAttr('href');

    var fd = new FormData();
    var files = $(this)[0].files;

    // Check file selected or not
    if (files.length > 0) {
        $("form [name='file']").attr('disabled', true);
        $("form [type='submit']").attr('disabled', true);
        $(window).on('beforeunload', function () {
            return confirm('資料上傳中，您確定要離開此網頁嗎？');
        });

        toastr.clear();
        toastr.info("檔案上傳中");
        fd.append('file', files[0]);

        $.ajax({
            url: './API/signup/file.php',
            type: 'POST',
            data: fd,
            contentType: false,
            processData: false
        }).done(function (response) {
            toastr.clear();
            toastr.success("檔案上傳成功成功！");
            $('form #fileLink').css('color', '');
            $('form #fileLink').addClass('color-info');
            $('form #fileLink').text('檔案已上傳');
            $('form #fileLink').attr('href', './API/signup/file.php?export=download');
            $(window).off('beforeunload');
            $("form [name='file']").removeAttr('disabled');
            $("form [type='submit']").removeAttr('disabled');

        })
            .fail(function (jqXHR, exception) {
                // toastr.remove();
                toastr.clear();
                let response = jqXHR.responseJSON;
                let msg = '';
                if (response === undefined)
                    msg = exception;
                else if (response.hasOwnProperty('message')) {
                    msg = response.message;
                } else {
                    msg = 'Uncaught Error.\n' + jqXHR.responseText;
                }
                toastr.error(msg);
                $(window).off('beforeunload');
                $("form [name='file']").removeAttr('disabled');
                $("form [type='submit']").removeAttr('disabled');

            });
    }
});
