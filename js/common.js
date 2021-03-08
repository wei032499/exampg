var broswerV = getBroswer();
if ((broswerV.broswer === "Chrome" && broswerV.version < 45) ||
    (broswerV.broswer === "Firefox" && broswerV.version < 38) ||
    (broswerV.broswer === "Edge" && broswerV.version < 12) ||
    (broswerV.broswer === "IE" && broswerV.version < 10) ||
    (broswerV.broswer === "Safari" && broswerV.version < 9) ||
    (broswerV.broswer === "Opera" && broswerV.version < 30)) {
    alert("不支援此瀏覽器版本！請改用下列瀏覽器版本：\nChrome >= 45\nFirefox >= 38\nEdge >= 12\nIE >= 10\nSafari >= 9\nOpera >= 30");
}

function getBroswer() {
    var sys = {};
    var ua = navigator.userAgent.toLowerCase();
    var s;
    (s = ua.match(/edge\/([\d.]+)/)) ? sys.edge = s[1] :
        (s = ua.match(/rv:([\d.]+)\) like gecko/)) ? sys.ie = s[1] :
            (s = ua.match(/msie ([\d.]+)/)) ? sys.ie = s[1] :
                (s = ua.match(/firefox\/([\d.]+)/)) ? sys.firefox = s[1] :
                    (s = ua.match(/chrome\/([\d.]+)/)) ? sys.chrome = s[1] :
                        (s = ua.match(/opera.([\d.]+)/)) ? sys.opera = s[1] :
                            (s = ua.match(/version\/([\d.]+).*safari/)) ? sys.safari = s[1] : 0;

    if (sys.edge) return { broswer: "Edge", version: parseFloat(sys.edge) };
    if (sys.ie) return { broswer: "IE", version: parseFloat(sys.ie) };
    if (sys.firefox) return { broswer: "Firefox", version: parseFloat(sys.firefox) };
    if (sys.chrome) return { broswer: "Chrome", version: parseFloat(sys.chrome) };
    if (sys.opera) return { broswer: "Opera", version: parseFloat(sys.opera) };
    if (sys.safari) return { broswer: "Safari", version: parseFloat(sys.safari) };

    return { broswer: "", version: parseFloat("0") };
}

//用於部分未定義"URLSearchParams"的瀏覽器
if (typeof URLSearchParams === "undefined") {
    var URLSearchParams = function (url) {
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
    var sessionItems = {};
    var storage = sessionStorage.getItem(itemName);
    if (storage !== null) {
        var elements = storage.split('&');
        for (var i = 0; i < elements.length; i++) {
            var strParts = elements[i].split("=");
            strParts[0] = decodeURIComponent(strParts[0]);
            strParts[1] = decodeURIComponent(strParts[1]);
            var key = strParts[0];
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
            var response = jqXHR.responseJSON;
            var msg = '';
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
    var keys = Object.keys(items);
    for (var i = 0; i < keys.length; i++) {
        if (Array.isArray(items[keys[i]])) {
            for (var j = 0; j < items[keys[i]].length; j++) {
                var end = "";
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
            var response = jqXHR.responseJSON;
            var msg = '';
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
    var cookieArray = document.cookie.split('; ');
    for (var i = 0; i < cookieArray.length; i++) {
        var cookieName = cookieArray[i].split('=')[0];
        var cookieValue = cookieArray[i].split('=')[1];
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