var sessionItems = {};

function fillByStorage(item) {
    sessionItems = {};
    let storage = sessionStorage.getItem(item);
    if (storage !== null) {
        let elements = storage.split('&');
        for (let i = 0; i < elements.length; i++) {
            let strParts = elements[i].split("=");
            strParts[0] = decodeURIComponent(strParts[0]);
            strParts[1] = decodeURIComponent(strParts[1]);
            sessionItems[strParts[0]] = strParts[1];
        }

        $(function () {
            let keys = Object.keys(sessionItems);
            for (let i = 0; i < keys.length; i++) {
                if ($("form [name='" + keys[i] + "']").attr('type') === "radio") {
                    $("form [name='" + keys[i] + "'][value='" + sessionItems[keys[i]] + "']").removeAttr("disabled");
                    $("form [name='" + keys[i] + "'][value='" + sessionItems[keys[i]] + "']")[0].checked = true;

                    $("form [name='" + keys[i] + "']:checked").change();
                } else if ($("form [name='" + keys[i] + "']").attr('type') === "select") {
                    $("form [name='" + keys[i] + "']>option[value='" + sessionItems[keys[i]] + "']").removeAttr("disabled");
                    $("form [name='" + keys[i] + "']").val(sessionItems[keys[i]]).change();
                } else
                    $("form [name='" + keys[i] + "']").val(sessionItems[keys[i]]).change();
            }

        });
    }


}

function fillByData(url) {
    $.ajax({
        type: 'GET',
        url: url,
        dataType: 'json'
    }).done(function (response) {
        $(function () {
            console.log(response);
            if (response.hasOwnProperty('data')) {
                /*if (Array.isArray(response.data)) {
                    for (let i = 0; i < response.data.length; i++) {
                        let keys = Object.keys(response.data[i]);
                        for (let i = 0; i < keys.length; i++) {
                            if ($("form [name='" + keys[i] + "']").attr('type') === "radio") {
                                $("form [name='" + keys[i] + "'][value='" + response.data[i][keys[i]] + "']").removeAttr("disabled");
                                $("form [name='" + keys[i] + "'][value='" + response.data[i][keys[i]] + "']")[0].checked = true;

                                $("form [name='" + keys[i] + "']:checked").change();
                            } else if ($("form [name='" + keys[i] + "']").attr('type') === "select") {
                                $("form [name='" + keys[i] + "']>option[value='" + response.data[i][keys[i]] + "']").removeAttr("disabled");
                                $("form [name='" + keys[i] + "']").val(response.data[i][keys[i]]).change();
                            } else
                                $("form [name='" + keys[i] + "']").val(response.data[i][keys[i]]).change();
                        }

                    }
                } else {*/
                let keys = Object.keys(response.data);
                for (let i = 0; i < keys.length; i++) {
                    if ($("form [name='" + keys[i] + "']").attr('type') === "radio") {
                        $("form [name='" + keys[i] + "'][value='" + response.data[keys[i]] + "']").removeAttr("disabled");
                        $("form [name='" + keys[i] + "'][value='" + response.data[keys[i]] + "']")[0].checked = true;
                        $("form [name='" + keys[i] + "']:checked").change();
                    } else if ($("form [name='" + keys[i] + "']").attr('type') === "select") {
                        $("form [name='" + keys[i] + "']>option[value='" + response.data[keys[i]] + "']").removeAttr("disabled");
                        $("form [name='" + keys[i] + "']").val(response.data[keys[i]]).change();
                    } else
                        $("form [name='" + keys[i] + "']").val(response.data[keys[i]]).change();
                }
                //}
            }

        });

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

function logout(redirect) {
    if (redirect === undefined)
        redirect = true;
    $.ajax({
        type: 'POST',
        url: "./API/auth/logout.php",
        dataType: 'json'
    }).done(function (response) {
        sessionStorage.clear();
        if (redirect)
            window.location.replace('./')
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

function getCookie(name) {
    const cookieArray = document.cookie.split('; ');
    for (let i = 0; i < cookieArray.length; i++) {
        let cookieName = cookieArray[i].split('=')[0];
        let cookieValue = cookieArray[i].split('=')[1];
        if (cookieName === name)
            return cookieValue;
    }
    return null;
}

$(function () {
    $("form button[type='reset']").on('click', function (e) {
        e.preventDefault();
        if (confirm('確定清除嗎？'))
            $("form")[0].reset();

    });

    $("#logout").on('click', function () {
        if (confirm('確定登出嗎？')) {
            logout();
        }

    });

    $("form .btn-cancel").on('click', function (e) {
        e.preventDefault();
        if (confirm('確定取消嗎？'))
            window.location.replace('./');

    });


});


if (getCookie('username') !== null) {
    $(function () {
        $("#username").text(getCookie('username'));
        $("#loginInfo").css('display', '');
    });
}