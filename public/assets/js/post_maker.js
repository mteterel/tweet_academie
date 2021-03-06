$(document).click(function (e) {
    if (e.target.id === "user_post_content")
    {
        $('#user_post_content').animate({height: 80}, "fast");
        $('.post_maker-wrapper').animate({height: 134}, "fast");
        $('.quacker-btn-displayer').fadeTo("fast", 1);
    }
    else
    {
        $('#user_post_content').animate({height: 36}, "fast");
        $('.post_maker-wrapper').animate({height: 52}, "fast");
        $('.quacker-btn-displayer').fadeTo("fast", 0);
    }
});
$('.post_maker textarea').keyup(function () {
    $('.suggestions').html('');
    let regex = /@([A-Za-z0-9])\w+/g;
    let myVal = $('.post_maker textarea').val();
    let revVal = myVal.split('').reverse().join('');
    let spaceKey = revVal.indexOf((' '));
    let myCutString = revVal.substr(0, spaceKey);
    if (spaceKey === -1)
        myCutString = revVal;
    myCutString = myCutString.split('').reverse().join('');
    console.log(myCutString);
    if (regex.test(myCutString))
    {
        $.post(
            "/completionSuggest",
            {
                'entry': myCutString
            },
            function (data) {
                if (data.success === true)
                {
                    let content = "";
                    for (let value of data.htmlTemplate) {
                        content += value;
                    }
                    $('.suggestions').html(content);
                }
                else
                {
                    console.log("Broken duck");
                }
            },
            'json'
        );
    }
});
$(document).on('click' , '.mini_user_card', function () {
    let username = $(this).find('.username').html();
    let myVal = $('.post_maker textarea').val();
    let revVal = myVal.split('').reverse().join('');
    let spaceKey = revVal.indexOf((' '));
    let cuttedVal = revVal.substr(spaceKey);
    if (spaceKey === -1)
        cuttedVal = '';
    cuttedVal = cuttedVal.split('').reverse().join('');
    $('.post_maker textarea').val(cuttedVal + username + ' ');
    $('.suggestions').html('');
});
$('.submit-post_maker').click(function () {
    if ($('.post_maker textarea').val().length > 140)
    {
        alert("Broken Duck, too many characters in your quack (140 max)");
        return;
    }
    $.ajax({
        type: 'POST',
        url: '/post/submit',
        data: new FormData(document.getElementById("formPostMaker")),
        dataType: 'json',
        processData: false,
        contentType: false,
        success: function (data) {
            if (data.success === false)
                alert("Broken duck");
            else
            {
                $('#user_post_content').val('');
                $('#user_post_media_url').val('');
                $('.timeline').prepend(data.htmlTemplate);
                $($('.card-timeline')[0]).hide();
                $($('.card-timeline')[0]).css('opacity', '0');
                $('.suggestions').html('');
                animate_timeline();
                $('.timeline').prepend("<div class='wrapper_new-posts'>"+
                $($('.wrapper_new-posts')[0]).html() +
                "</div>");
                $($('.wrapper_new-posts')[0]).hide();
                $($('.wrapper_new-posts')[1]).html('');
            }
        }
    });
});
function animate_timeline()
{
    $($('.card-timeline')[0]).slideDown("fast");
    $($('.card-timeline')[0]).fadeTo("fast", 1);
}
$('.add-file-post_maker').click(function () {
    $('.inputUpload').trigger('click');
});
//$('#formPostMaker').keyup(function () {
//    if(is_it_url($('#user_post_content').val()) !== false)
//    {
//        let params = is_it_url($('#user_post_content').val());
//        let my_value = $('#user_post_content').val();
//        let api_bitly =
//        "https://api-ssl.bitly.com/v3/shorten?access_token=c7"+
//            "94cc4274f5e1a6196497276989105fa3f59d56&longUrl="+params[0];
//        $.getJSON(api_bitly, function (datas) {
//            if (datas.data.url == undefined)
//            {
//                return(0);
//            }
//            $('#user_post_content').val(my_value.substr(0, params[1]) +
//                datas.data.url.substr(7) +
//                my_value.substr(params[1]+params[2]));
//        });
//
//    }
//});
//function is_it_url(string)
//{
//    let regex =
//    /(https?:\/\/)?([\w\-])+\.{1}([a-zA-Z]{2,63})([\/\w-]*)*\/?\??([^#\n\r]*)?#?([^\n\r]*)/;
//    if (regex.test(string) === true)
//    {
//        let separator = string.match(regex)[0].indexOf(" ");
//        let url = string.match(regex)[0].substr(0, separator);
//        let url_pos = string.indexOf(url);
//        let params = [url, url_pos, url.length];
//        if (params[2] !== 0 && url.indexOf("bit.ly/") == -1)
//        {
//            params[0] = encodeURIComponent(url);
//            return(params);
//        }
//        return(false);
//    }
//    else
//    {
//        return(false);
//    }
//}