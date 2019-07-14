$(function () {
    $('.chat_conversation').scrollTop(1E10);
});
$('#send').click(function( event ) {
    event.preventDefault();
    var url = window.location.href;

    $.ajax({
        type: 'POST',
        url: url,
        data: new FormData(document.getElementById("formMessages")),
        dataType: 'json',
        processData: false,
        contentType: false,
        success: function (data) {
            $(data.htmlTemplate).appendTo(".chat_conversation");
            $('#chat_message_content').val('');
            $('.chat_conversation').scrollTop(1E10);
        }
    });
});
$('#chat_message_content').keydown(function (e) {
    if (e.which == 13 && !e.shiftKey) {
        e.preventDefault();
        $('#send').trigger("click");
    }
});
/*
$('.chat_conversation').append("<div class='" +
    "new_msg'></div>");
*/

(async function loopRefresh(){
    await sleep(5);
    $.post(
        window.location + '/refresh',
        function (data) {
            if (data.success === true)
            {
                for (let value of data.htmlTemplate) {
                    $('.chat_conversation').append(value);
                }
                $('.chat_conversation').scrollTop(1E10);
            }
        },
        'json'
    );
    loopRefresh();
})();

function sleep(secs)
{
    var ms = secs * 1000;
    return new Promise(resolve =>
        setTimeout(resolve, ms));
}