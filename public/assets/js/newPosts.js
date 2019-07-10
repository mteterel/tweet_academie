let waiting_quacks = 0;
$('.button-div-actu').hide();
$('.timeline').prepend("<div class='" +
    " wrapper_new-posts'></div>");
(async function loopActualisation(){
    await sleep(15);
    $.post(
        '/actualisation',
        function (data) {
            if (data.success === true)
            {
                $('.wrapper_new-posts').hide();
                for (let value of data.htmlTemplate) {
                    $($('.wrapper_new-posts')[0]).prepend(value);
                }
                waiting_quacks += data.htmlTemplate.length;
                let quack = 'Quacks';
                if (waiting_quacks === 1)
                    quack = 'Quack';
                $('.new_posts_btn').html("Show "+waiting_quacks+" new "+ quack);
                $('.button-div-actu').slideDown('fast');
                $('.button-div-actu').css('opacity', '0');
                $('.button-div-actu').fadeTo('fast', '1');
            }
        },
        'json'
    );
    loopActualisation();
})();
$('.button-div-actu').click(async function () {
    console.log('you clicked');
    waiting_quacks = 0;
    $('.button-div-actu').fadeTo('fast', '0');
    $('.button-div-actu').slideUp('fast');
    await sleep(0.3);
    $($('.wrapper_new-posts')[0]).slideDown('fast');
    $($('.wrapper_new-posts')[0]).css('opacity', '0');
    $($('.wrapper_new-posts')[0]).fadeTo('fast', 1);
    $('.timeline').prepend("<div class='" +
        " wrapper_new-posts'></div>");
});
function sleep(secs)
{
    var ms = secs * 1000;
    return new Promise(resolve =>
        setTimeout(resolve, ms));
}