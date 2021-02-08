
function getSessionItems(itemName) {
    let sessionItems = {};
    let storage = sessionStorage.getItem(itemName);
    if (storage !== null) {
        let elements = storage.split('&');
        for (let i = 0; i < elements.length; i++) {
            let strParts = elements[i].split("=");
            strParts[0] = decodeURIComponent(strParts[0]);
            strParts[1] = decodeURIComponent(strParts[1]);
            sessionItems[strParts[0]] = strParts[1];
        }
    }
    return sessionItems;
}
function getData(url, cache) {
    if (cache === undefined)
        cache = true;
    return $.ajax({
        type: 'GET',
        url: url,
        cache: cache,
        dataType: 'json'
    }).done(function (response) {
        return response.data;
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
function fillForm(items) {
    let keys = Object.keys(items);
    for (let i = 0; i < keys.length; i++) {
        if ($("form [name='" + keys[i] + "']").attr('type') === "radio") {
            $("form [name='" + keys[i] + "'][value='" + items[keys[i]] + "']").removeAttr("disabled");
            $("form [name='" + keys[i] + "'][value='" + items[keys[i]] + "']")[0].checked = true;
            $("form [name='" + keys[i] + "']:checked").change();
        } else if ($("form [name='" + keys[i] + "']").attr('type') === "select") {
            $("form [name='" + keys[i] + "']>option[value='" + items[keys[i]] + "']").removeAttr("disabled");
            $("form [name='" + keys[i] + "']").val(items[keys[i]]).change();
        } else
            $("form [name='" + keys[i] + "']").val(items[keys[i]]).change();
    }
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
            window.location.replace('./');
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
        $("#username").text(decodeURIComponent(getCookie('username')));
        $("#loginInfo").css('display', '');
    });
}