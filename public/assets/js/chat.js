$(function () {
    $('.chat_conversation').scrollTop(1E10);
});
$('#send').click(function( event ) {
    event.preventDefault();

    $.ajax(window.location.href + '/submit', {
        type: 'POST',
        data: new FormData(document.getElementById("formMessages")),
        dataType: 'json',
        processData: false,
        contentType: false,
        success: function (data) {
            if (data.success === true)
            {
                $('.chat_conversation').append(data.htmlTemplate);
                $('#chat_message_content').val('');
                $('.chat_conversation').scrollTop(1E10);
            }
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
        window.location.href + '/refresh',
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