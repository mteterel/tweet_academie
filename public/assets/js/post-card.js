$(document).on('click', '.post-card__like-btn', function() {
    var self = $(this);
    $postCard = self.parents('.post-card');
    postId = $($postCard).attr('data-post-id');

    $.ajax('/post/' + postId + '/like', {
        dataType: 'json',
        success: function(data) {
            $(self).find('.post-card__like-count')
                .text(data.count || 0);

            if (data.favorite === true)
                $(self).removeClass('btn-outline-secondary')
                    .addClass('btn-outline-danger');
            else
                $(self).removeClass('btn-outline-danger')
                    .addClass('btn-outline-secondary');
        }
    });
});

$(document).on('click', '.post-card__repost-btn', function() {
    $postCard = $(this).parents('.post-card');
    postId = $($postCard).attr('data-post-id');
    var self = $(this);

    $.ajax('/post/' + postId + '/repost', {
        dataType: 'json',
        success: function(data) {
            if (data.success === false)
                alert("Broken duck");
            else
            {
                $(self).find('.post-card__repost-count')
                    .text(data.count || 0);

                $('.timeline').prepend(data.htmlTemplate);
                $($('.card-timeline')[0]).hide();
                $($('.card-timeline')[0]).css('opacity', '0');

                animate_timeline();
            }
        }
    });
});

$(document).on('click', '.post-card__delete-btn', function() {
    $postCard = $(this).parents('.post-card');
    postId = $($postCard).attr('data-post-id');

    $.ajax('/post/' + postId + '/delete', {
        dataType: 'json',
        success: function(data) {
            if (data.success === true)
            {
                $postCard.fadeTo("fast", 0);
                $postCard.slideUp("fast");
            }
        }
    });
});