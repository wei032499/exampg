//用於部分未定義"URLSearchParams"的瀏覽器
if (typeof URLSearchParams === "undefined") {
    function URLSearchParams(url) {
        this.url = url;
    }
    URLSearchParams.prototype.get = function (name) {
        var results = new RegExp('[\?&]' + name + '=([^&#]*)').exec(this.url);
        if (results == null) {
            return null;
        } else {
            return decodeURI(results[1]) || 0;
        }
    }
}

//將儲存於sessionStorage的serialized form轉換為object
function getSessionItems(itemName) {
    let sessionItems = {};
    let storage = sessionStorage.getItem(itemName);
    if (storage !== null) {
        let elements = storage.split('&');
        for (let i = 0; i < elements.length; i++) {
            let strParts = elements[i].split("=");
            strParts[0] = decodeURIComponent(strParts[0]);
            strParts[1] = decodeURIComponent(strParts[1]);
            let key = strParts[0];
            if (key.substr(-2) === "[]") //arrayObj
            {
                key = key.slice(0, -2);
                if (sessionItems[key] === undefined)
                    sessionItems[key] = [];
                sessionItems[key].push(strParts[1]);
            }
            else
                sessionItems[key] = strParts[1];
        }

    }
    return sessionItems;
}

//從指定url取得data object
function getData(url, cache, payload) {
    if (cache === undefined)
        cache = false;
    if (payload === undefined)
        payload = null;
    return $.ajax({
        type: 'GET',
        url: url,
        cache: cache,
        dataType: 'json',
        data: payload
    }).done(function (response) {
        return response.data;
    })
        .fail(function (jqXHR, exception) {
            let response = jqXHR.responseJSON;
            let msg = '';
            if (response === undefined)
                msg = exception + "\n" + url + "\n" + jqXHR.responseText;
            else if (response.hasOwnProperty('message')) {
                msg = response.message;
            } else {
                msg = 'Uncaught Error.\n' + jqXHR.responseText;
            }
            alert(msg);
        });
}

//將data object寫回form
function fillForm(items) {
    let keys = Object.keys(items);
    for (let i = 0; i < keys.length; i++) {
        if (Array.isArray(items[keys[i]])) {
            for (let j = 0; j < items[keys[i]].length; j++) {
                let end = "";
                if (keys[i].substr(-2) !== "[]")
                    end = "[]";
                if ($("form [name='" + keys[i] + end + "']:eq(" + j + ")").prop("tagName") === "SELECT") {
                    $("form [name='" + keys[i] + end + "']:eq(" + j + ") >option[value='" + items[keys[i]][j] + "']").removeAttr("disabled");
                    $("form [name='" + keys[i] + end + "']:eq(" + j + ") ").val(items[keys[i]][j]).change();
                }
                else if ($("form [name='" + keys[i] + end + "']").attr('type') === "radio" || $("form [name='" + keys[i] + end + "']").attr('type') === "checkbox") {
                    $("form [name='" + keys[i] + end + "'][value='" + items[keys[i]][j] + "']").removeAttr("disabled");
                    $("form [name='" + keys[i] + end + "'][value='" + items[keys[i]][j] + "']")[0].checked = true;
                    $("form [name='" + keys[i] + end + "']:checked").change();
                } else if ($("form [name='" + keys[i] + end + "']:eq(" + j + ")").length > 0)
                    $("form [name='" + keys[i] + end + "']:eq(" + j + ") ").val(items[keys[i]][j]).change();
            }
        }
        else {
            if ($("form [name='" + keys[i] + "']").prop("tagName") === "SELECT") {
                $("form [name='" + keys[i] + "']>option[value='" + items[keys[i]] + "']").removeAttr("disabled");
                $("form [name='" + keys[i] + "']").val(items[keys[i]]).change();
            }
            else if ($("form [name='" + keys[i] + "']").attr('type') === "radio" || $("form [name='" + keys[i] + "']").attr('type') === "checkbox") {
                $("form [name='" + keys[i] + "'][value='" + items[keys[i]] + "']").removeAttr("disabled");
                $("form [name='" + keys[i] + "'][value='" + items[keys[i]] + "']")[0].checked = true;
                $("form [name='" + keys[i] + "']:checked").change();
            } else if ($("form [name='" + keys[i] + "']").length > 0)
                $("form [name='" + keys[i] + "']").val(items[keys[i]]).change();
        }
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
                msg = exception + "\n" + "./API/auth/logout.php" + "\n" + jqXHR.responseText;
            else if (response.hasOwnProperty('message')) {
                msg = response.message;
            } else {
                msg = 'Uncaught Error.\n' + jqXHR.responseText;
            }
            alert(msg);
        });

}

//取得cookie中指定key的value
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