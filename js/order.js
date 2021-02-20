function formReadOnly() {
    $("form [name='section[]'").not(":checked").parent().remove();
    $("form [name='section[]'").on('click', function () {
        return false
    });
    $("form .form-control").addClass('form-control-plaintext').removeClass('form-control');
    $("form select option").not(":selected").remove();
    $("form [type='radio']:not(:checked)").parent().remove();
    $("form [type='radio']").parent().css('color', '#00008b');
    $("form [type='radio']").attr('type', 'hidden');
    $("form input").css('color', '#00008b');
    $("form select").css('color', '#00008b');
    $("form [type='number']").attr('type', 'text');
    $("form select").each(function () {
        let input = "<div class='" + $(this).attr('class') + "' style='" + $(this).attr('style') + "' >" + $(this).children("option:selected").text() + "</div>";
        input += "<input type='hidden' class='" + $(this).attr('class') + "' style='" + $(this).attr('style') + "' name='" + $(this).attr('name') + "' value='" + $(this).val() + "'>";
        $(this).replaceWith(input);
    });

}