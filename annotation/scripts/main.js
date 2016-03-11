/**
 *	This 'main.js' file contains code for both text and image annotating, including commenting
 */

/*
    Adds toggling support to the annotation side-bar
*/
require(['jquery'], function($) {
    $('.nav-side .nav-toggle').on('click', function(e) {
        e.preventDefault();
        $(this).parent().toggleClass('nav-open');
    });


    $('body').on('click', '.annotation', function(e) {
        e.preventDefault();
    });

});

/**
 * Converts a UNIX timestamp into readable format
 */
function timeConverter(UNIX_timestamp) {
    var a = new Date(UNIX_timestamp * 1000);
    var months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
    var year = a.getFullYear();
    var month = months[a.getMonth()];
    var date = a.getDate();
    var hour = a.getHours();
    if (hour < 10) {
        hour = "0" + hour;
    }
    var min = a.getMinutes();
    if (min < 10) {
        min = "0" + min;
    }
    var time = date + ' ' + month + ' ' + year + ' ' + hour + ':' + min;
    return time;
}

/**
 * Converts special characters into their escaped values
 */
function htmlEntities(str) {
    return String(str).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
}


/**
  * Simple function to trim whitespace from strings instead of using jQuery
  */
function trim(s){ 
    return ( s || '' ).replace( /^\s+|\s+$/g, '' ); 
}

/**
 * Gets a GET variable from the current URL
 */
function getQueryVariables(variable) {
    var query = window.location.search.substring(1);
    var vars = query.split("&");
    for (var i = 0; i < vars.length; i++) {
        var pair = vars[i].split("=");
        if (pair[0] == variable) {
            return pair[1];
        }
    }
    return false;
}

/*
 * Load the comments for this activity when the page loads
 * Post the current url to /comments/load.php
 */

function get_comments() {
    require(['jquery'], function(jQuery) {
        var post_data = {
            url: getQueryVariables("id")
        };
        jQuery.post("./comments/load.php", post_data, function(data) {
            var comments = JSON.parse(data);

            for(var i = 0; i < comments.length; i++) {
                var timecreated = timeConverter(comments[i].timecreated);
                var username = comments[i].username;
                var comment = comments[i].comment;
                var comment_id = comments[i].id;
                var annotation_id = comments[i].annotation_id;

                var insert = '<p data-comment-id="' + comment_id + '"><strong title="' + timecreated + '">';
                insert += username + ':</strong> ' + comment + '</p>';

                var target = '#comments-region-' + annotation_id;
                jQuery(target).append(insert);
            }
        });
    });
}

/*
    When user clicks the comment button send the comment to the server if a comment has been
    entered into the comment textarea
*/
require(['jquery'], function(jQuery) {
    jQuery(document).ready(function() {
        jQuery('body').on('click', '.comment-button', function(e) {
            e.preventDefault();

            var annotation_id = jQuery(this).data('annotation-id');
            var target = '#comment-box-' + annotation_id;
            var comment = jQuery(target).val();


            //Only process comment if length > 0
            if(comment.length > 0) {
                //Length is > 0 so we post the comment
                //Create a JSON encoded 'comment' object that can be posted via AJAX

                var post_data = {
                    url: getQueryVariables("id"), //page url = activity_id
                    annotation_id: annotation_id,
                    comment: comment
                };
                
                //Send data to server and store response
                jQuery.post("./comments/create.php", JSON.parse(JSON.stringify(post_data)), function(data) {
                    if(data == "false") {
                        //Comment hasn't been stored, work out why and respond   
                        alert("Error creating comment!"); 
                    }
                    else {
                        //Comment successfully stored, now display it on the page
                        var response = JSON.parse(data);
                        jQuery(target).val(""); //Empty the comment text box

                        target = '#comments-region-' + annotation_id;
                        var username = response.username;
                        var timecreated = timeConverter(response.timecreated);
                        var insert = '<p data-comment-id="' + response.id + '"><strong title="' + timecreated + '">' + response.username + ':</strong> ' + htmlEntities(comment) + '</p>';

                        jQuery(target).append(insert);

                        //TODO Update the number of comments (count)
                    }
                });
                
            }
        })
    })
});
