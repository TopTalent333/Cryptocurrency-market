function cmcLoadTweets(lastIDData, shortcodeData, $cmc, $cmcMore, numNeeded, persistentIndex) {

    //Display loader
    $cmcMore.addClass('ctf-loading').append('<div class="ctf-loader"></div>');
    $cmcMore.find('.ctf-loader').css('background-color', $cmcMore.css('color'));

    var feedID = typeof $cmc.attr('data-feed-id') ? $cmc.attr('data-feed-id') : 'ctf-single',
        postID = typeof $cmc.attr('data-postid') ? $cmc.attr('data-postid') : '';

    jQuery.ajax({
        url: ctf.ajax_url,
        type: 'post',
        data: {
            action: 'ctf_get_more_posts',
            last_id_data: lastIDData,
            shortcode_data: shortcodeData,
            num_needed: numNeeded,
            persistent_index: persistentIndex,
            feed_id: feedID,
            location: cmcLocationGuess($cmc),
            post_id: postID,
        },
        success: function (data) {
            if (lastIDData !== '') {
                // appends the html echoed out in ctf_get_new_posts() to the last post element
                if (data.indexOf('<meta charset') == -1) {
                    $cmc.find('.ctf-item').removeClass('ctf-new').last().after(data);
                }

                if ($cmc.find('.ctf-out-of-tweets').length) {
                    $cmcMore.hide();
                    //Fade in the no more tweets message
                    $cmc.find('.ctf-out-of-tweets p').eq(0).fadeIn().end().eq(1).delay(500).fadeIn();
                }
            } else {
                $cmc.find('.ctf-tweets').append(data);
            }


            //Remove loader
            $cmcMore.removeClass('ctf-loading').find('.ctf-loader').remove();

            //Re-run JS code
            //  ctfScripts($cmc);

        }
    }); // ajax call
}

function cmcLocationGuess($feed) {
    var location = 'content';

    if ($feed.closest('footer').length) {
        location = 'footer';
    } else if ($feed.closest('.header').length
        || $feed.closest('header').length) {
        location = 'header';
    } else if ($feed.closest('.sidebar').length
        || $feed.closest('aside').length) {
        location = 'sidebar';
    }

    return location;
}


    //tweats load more code end here