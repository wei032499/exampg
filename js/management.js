$("#mLogout").on('click', function () {
    if (confirm('確定登出嗎？')) {
        $.ajax({
            type: 'POST',
            url: "./API/auth/admin.php",
            data: {
                oper: "logout"
            },
            dataType: 'json'
        }).done(function (response) {
            window.location.replace('./');
        })
            .fail(function (jqXHR, exception) {
                var response = jqXHR.responseJSON;
                var msg = '';
                if (response === undefined)
                    msg = exception + "\n" + "./API/auth/admin.php" + "\n" + jqXHR.responseText;
                else if (response.hasOwnProperty('message')) {
                    msg = response.message;
                } else {
                    msg = 'Uncaught Error.\n' + jqXHR.responseText;
                }
                alert(msg);
            });
    }

});