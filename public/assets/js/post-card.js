$(document).on('click', '.post-card__like-btn', function() {
    var self = $(this);
    $postCard = self.parents('.post-card');
    postId = $($postCard).attr('data-post-id');

    $.ajax('/post/' + postId + '/like', {
        dataType: 'json',
        success: function(data) {
            if (data.favorite === true)
                $(self).removeClass('btn-secondary')
                    .addClass('btn-danger');
            else
                $(self).removeClass('btn-danger')
                    .addClass('btn-secondary');
        }
    });
});

$(document).on('click', '.post-card__repost-btn', function() {
    $postCard = $(this).parents('.post-card');
    postId = $($postCard).attr('data-post-id');

    $.ajax('/post/' + postId + '/repost', {
        dataType: 'json',
        success: function(data) {

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
                console.log("TODO: transition");
        }
    });
});