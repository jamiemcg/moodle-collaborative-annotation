require(['jquery'], function(jQuery) {
	jQuery(document).ready(function() {

		var type = getQueryVariables("type");

		var post_data = {
            url: getQueryVariables("id")
        };

        // Load annotations from the database
		if(type == 2) {
			// Image annotation mode
			jQuery.post("./annotorious/load.php", post_data, function(data) {
	            var data = JSON.parse(data);
	            data.shift(); // Don't care about editable

	            // Display annotations
	        });
		}
		else {
			// Text annotation mode
			jQuery.post("./annotator/load.php", post_data, function(data) {
	            var data = JSON.parse(data);
	            data.shift(); // Don't care about editable

	            // Display annotations
	            for(var i = 0; i < data.length; i++) {
	            	var annotation = data[i];
	            	var section = jQuery('#discussion-area');
	            	
	            	var insert = '<div class="annotation-item forumpost" id="annotation-' + annotation.id + '">';

	            	if(type == 1) {
	            		insert += '<pre><code>';
	            	}
	            	else {
	            		insert += '<blockquote>';
	            	}

	            	insert += annotation.quote;

	            	if(type == 1) {
	            		insert += '</code></pre>';
	            	}
	            	else {
	            		insert += '</blockquote>';
	            	}

	            	insert += '<p><strong>' + annotation.username + ': </strong>' + annotation.annotation + '</p>';
	            	insert += '<p class="annotation-details">' + timeConverter(annotation.timecreated) + '</p>';
					
					insert += '<p class="comment-count" data-annotation-id="' + annotation.id + '">';
					insert += '<span id="comment-count-"' + annotation.id + '">' + '</span>';
					insert += '<img class="comments-button annotation-icon" src="./styles/comments_alt.png"> Comments';
					insert += '</p>';
					insert += '<div style="display:none" class="comments-discussion" id="comments-' + annotation.id + '">';

					insert += '<div class="comments-region" id="comments-region-' + data[i].id + '"></div> ';

					insert += '<p><textarea class="comment-box-discussion" id="comment-box-' + annotation.id + '" placeholder="Enter a comment..."></textarea>';
					insert += '<img data-annotation-id="' + annotation.id + '" class="comment-button annotation-comment-icon" src="./styles/comment_alt.png">';
					insert += '</p></div></a></div>'

	            	insert += '</div>';
	            	section.append(insert);
	            	section.append('<hr>');


	            	// Highlight source code
	            	if(type == 1) {
	            		hljs.initHighlighting.called = false;
						hljs.initHighlighting();
	            	}
	            }
				
				get_comments();
	        });
		}
	});

	//Display the comment section when the user clicks on it
    jQuery('body').on('click', '.comment-count', function(e) {
        e.preventDefault();            
        var annotation_id = jQuery(this).data('annotation-id');
        var target = "#comments-" + annotation_id;
        jQuery(target).toggle(400);

    });
});