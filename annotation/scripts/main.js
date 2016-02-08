/**
 *	This 'main.js' file contains code for both text and image annotating
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
    Driver section for testing the commenting backend.
    =================================================
*/


/*
    When an annotation in the side bar is clicked a popup is revealed allowing the user to enter
    a comment. Comments are not yet displayed.
*/
require(['jquery'], function(jQuery) {
    jQuery(document).ready(function() {
        jQuery('body').on('click', '.annotation', function(e) {
            e.preventDefault();
            var id = this.id;
            var text = "Enter your comment for annotation_id: " + id;
            var comment = prompt(text, "");

            //Only process comment if length > 0
            if(comment.length > 0) {
                //Length is > 0 so we post the comment
                //Create a JSON encoded 'comment' object that can be posted via AJAX

                var post_data = {
                    url: getQueryVariables("id"), //page url = activity_id
                    annotation_id: id,
                    comment: comment
                };

                console.log(post_data); //TODO: Delete this
                
                //Post this
                postComment(post_data);
            }
        })
    })
});



/*
    Posts the comment and corresponding data to the server.
    Returns True if the comment was successfully stored.
*/
function postComment(comment) {
    alert(comment);
    //TODO complete!
}