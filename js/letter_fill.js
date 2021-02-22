$(function () {
  $("form [name='token']").val(urlParams.get('token'));

  let keys = Object.keys(formData);
  for (let i = 0; i < keys.length; i++) {
    if ($("#" + keys[i]).prop("tagName") === "INPUT")
      $("#" + keys[i]).val(formData[keys[i]]);
    else
      $("#" + keys[i]).text(formData[keys[i]]);
  }
});


//for select
$.validator.addMethod(
  "notEqualsto",
  function (value, element, arg) {
    return arg != value;
  },
  "您尚未選擇!"
);

$("#form1").validate({
  submitHandler: function (form) {
    if (!confirm("請注意 :\r\n資料送出後即不可修改，是否確定要送出??"))
      return false;
    $.ajax({
      type: "POST",
      url: "./API/signup/letter_fill.php",
      dataType: "json",
      data: $("#form1").serialize() + "&oper=saveall"
    })
      .done(function (response) {
        alert(response.message);
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
  },
  rules: {
    apply_rel: {
      required: true,
    },
    apply_years: {
      required: true,
      minlength: 1,
      maxlength: 2,
      number: true,
    },
    apply_desc_1: {
      required: true,
    },
    apply_desc_2: {
      required: true,
    },
    apply_desc_3: {
      required: true,
    },
    apply_desc_4: {
      required: true,
    },
    apply_desc_5: {
      required: true,
    },
    apply_desc_6: {
      required: true,
    },
    apply_desc_7: {
      required: true,
    },
    apply_desc_8: {
      required: true,
    },
    apply_agree: {
      required: true,
    },
    apply_agree: {
      required: true,
    },
    apply_agree: {
      required: true,
    },
    apply_agree: {
      required: true,
    },
    apply_agree: {
      required: true,
    },
    "apply_manner[]": {
      required: true,
      minlength: 1,
      maxlength: 3,
    },
    apply_course: {
      required: true,
    },
    apply_agree: {
      required: true,
    },
    apply_notice: {
      maxlength: 500,
    },
    apply_special: {
      maxlength: 500,
    },
    apply_remark: {
      maxlength: 500,
    },
  },
  messages: {
    apply_manner: {
      required: "至少選擇一個選項",
      maxlength: "最多選擇三個選項",
    },
  },
});