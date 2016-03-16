/*  
    This contains functions related to annotating text.
    TODO: rewrite _all_ of the subscriptions as functions as they are currently very bad readability
*/

require(['jquery'], function(jQuery) {
    Annotator.Plugin.Storage = function(element) {
        return {
            pluginInit: function() {
                this.annotator.subscribe("beforeAnnotationCreated", function(annotation) {
                        annotation.url = getQueryVariables("id");
                    })
                    .subscribe("annotationCreated", function(annotation) {
                        if (annotation.quote.length > 0 && annotation.text.length > 0) {
                            jQuery.post("./annotator/create.php", JSON.parse(JSON.stringify(annotation)), function(data) {
                                if(data == "false") {
                                    //Time has run out, force a reload. Annotation blocked client side.
                                    window.location.reload();
                                }

                                data = JSON.parse(data);
                                var editable = data.shift(); //Not used
                                data = data.shift();
                                annotation.username = data.username;
                                annotation.timecreated = timeConverter(data.timecreated);
                                annotation.id = data.id;
                                annotation.groupname = data.groupname;

                                //Add the annotation to the side pane
                                var text = annotation.text;
                                if (annotation.text.length > 125) {
                                    //Don't display full annotation if its long
                                    text = annotation.text.substring(0, 125) + "...";
                                }
                                var annotation_insert = '<div class="annotation" id="' + annotation.id + '" title="' + annotation.timecreated +
                                    '"><a href="#" class="annotation-link">';
                                annotation_insert += '<p class="text">' + htmlEntities(text) + '</p>';
                                annotation_insert += '<p class="username">'
                                if (annotation.groupname) {
                                    annotation_insert += '[' + annotation.groupname + '] ';
                                }
                                annotation_insert += annotation.username + '</p>';

                                //Section for comments
                                annotation_insert += '<div class="comment-section" id="comment-section-' + annotation.id +'"><p class="comment-count" data-annotation-id="' + annotation.id + '">';
                                annotation_insert += '<span id="comment-count-' + annotation.id +  '">' + "" + ' </span>';
                                annotation_insert += '<span id="comments-word-' + annotation.id + '">' + /* comment_word */ "Comments" + '</span> ';
                                annotation_insert += '<img class="comments-button annotation-icon" src="./styles/comments.png"></p>';
                                annotation_insert += '<div style="display:none" class="comments" id="comments-' + annotation.id + '">';

                                //Comments will be inserted in order into this following div
                                annotation_insert += '<div class="comments-region" id="comments-region-' + annotation.id + '"></div> ';

                                //Textarea for new comments
                                annotation_insert += '<p><textarea class="comment-box" id="comment-box-' + annotation.id + '" placeholder="Enter a comment..."></textarea>';
                                annotation_insert += '<img data-annotation-id="' + annotation.id + '" class="comment-button annotation-comment-icon" src="./styles/comment.png">';
                                annotation_insert += '</p></div>'

                                annotation_insert += '<hr></a></div>';

                                jQuery(annotation.highlights).attr("data-annotation-id", annotation.id);
                                jQuery(annotation.highlights).attr("id", "annotation_" + annotation.id);
                                jQuery(annotation.highlights).addClass("annotation_" + annotation.id);

                                jQuery('#annotation-list').append(annotation_insert);
                            });
                        }
                    })
                    .subscribe("annotationUpdated", function(annotation) {
                        jQuery.post("./annotator/update.php", JSON.parse(JSON.stringify(annotation)), function(data) {
                            if (data == 0) {
                                //Incorrect user logged in
                                console.error("The annotation couldn't be updated");
                                alert("Warning: You cannot edit annotations created by others!  Any changes you make won't be saved!");
                            } else {
                                annotation.timecreated = timeConverter(data); //Update the time displayed

                                var annotation_to_update = "#" + annotation.id;
                                if (annotation.text.length > 125) { //Check if the annotation is too long to display
                                    var text = annotation.text.substring(0, 125) + "...";
                                } 
                                else {
                                    var text = annotation.text;
                                }

                                jQuery(annotation_to_update).find('.text').text(text);
                            }
                        });
                    })
                    .subscribe("annotationDeleted", function(annotation) {
                        //Check if the annotation actually exists (workaround annotatorjs bug #258)
                        if (annotation.id) {
                            var post_data = {
                                id: annotation.id
                            };
                            jQuery.post("./annotator/delete.php", post_data, function(data) {
                                if (data == 1) {
                                    var annotation_to_delete = "#" + annotation.id;
                                    jQuery(annotation_to_delete).remove();
                                } else {
                                    console.error("The annotation couldn't be deleted");
                                    alert("Warning: You cannot delete annotations created by others! Any changes you make won't be saved!");
                                }
                            });
                        } else {
                            //Event was called when user clicked cancel. Do nothing.
                        }
                    });
            }
        }
    };

    /**
     * Simple plugin that displays the time an annotation was created and the user who created it
     */
    Annotator.Plugin.ExtraData = function(element) {
        var plugin = {};
        plugin.pluginInit = function() {
            this.annotator.viewer.addField({
                load: function(field, annotation) {
                    if (annotation.groupname) {
                        field.innerHTML = annotation.username + " [" + annotation.groupname + "]";
                    } else {
                        field.innerHTML = annotation.username;
                    }
                }
            })
            this.annotator.viewer.addField({
                load: function(field, annotation) {
                    field.innerHTML = annotation.timecreated;
                }
            })
        }
        return plugin;
    }

    //Load the existing annotations when the page is loaded
    jQuery(document).ready(function() {
        var post_data = {
            url: getQueryVariables("id")
        };
        jQuery.post("./annotator/load.php", post_data, function(data) {
            //Load the annotations from the database
            data = JSON.parse(data);
            var editable = data.shift();

            if (editable) {
                var annotator_content = jQuery("#annotator-content").annotator();
            } else {
                var annotator_content = jQuery("#annotator-content").annotator({
                    readOnly: true
                });
            }

            annotator_content.annotator('addPlugin', 'Filter', {
                filters: [{
                    //TODO: only add group filter if group mode && group.visibility, how?
                    label: 'Group',
                    property: 'groupname'
                }, {
                    label: 'User',
                    property: 'username',
                    isFiltered: function(input, username) {
                        if (input && username && username.length) {
                            var keywords = input.split(/\s+/g);
                            username = username.split(" "); //Split first and second name
                            for (var i = 0; i < keywords.length; i += 1) {
                                for (var j = 0; j < username.length; j += 1) {
                                    if (username[j].toUpperCase().indexOf(keywords[i].toUpperCase()) !== -1) { //bad formatting
                                        return true;
                                    }
                                }
                            }
                        }
                        return false;
                    }
                }]
            });

            annotator_content.annotator('addPlugin', 'ExtraData');
            annotator_content.annotator('addPlugin', 'Storage');
            annotator_content.annotator('addPlugin', 'Tags');
            annotator_content.annotator('addPlugin', 'Unsupported');
            annotator_content.annotator("addPlugin", "Touch");
            for (var i = 0; i < data.length; i++) {
                data[i].text = data[i].annotation;
                
                //Changing the name to text to avoid confusion [from annotation to text]
                delete data[i].annotation; 

                data[i].ranges = JSON.parse(data[i].ranges);
                data[i].highlights = JSON.parse(data[i].highlights);
                data[i].tags = JSON.parse(data[i].tags);
                data[i].timecreated = timeConverter(data[i].timecreated);
            }

            data.sort(function(a, b) {
                return a.ranges[0].startOffset - b.ranges[0].startOffset;
            });

            //Add annotations to the side panel
            for (var i = 0; i < data.length; i++) {
                var text = data[i].text;
                if (data[i].text.length > 125) {
                    //Don't display full annotation if its long
                    text = data[i].text.substring(0, 125) + "...";
                }

                //All of this repeated concatenation is bad, improve (templates?)
                var annotation_insert = '<div class="annotation" id="' + data[i].id + '" title="';
                annotation_insert += data[i].timecreated + '"><a href="#" class="annotation-link">';
                annotation_insert += '<p class="text">' + text + '</p>';
                annotation_insert += '<p class="username">' + data[i].username + '</p>'
                
                var comment_count = 1; //Count how many comments have a particular annotation_id

                //Ensure it says 1 comment or multiple comments
                var comment_word = "comments";
                if(comment_count == 1) {
                    comment_word = "comment";
                }

                annotation_insert += '<div class="comment-section" id="comment-section-' + data[i].id +'"><p class="comment-count" data-annotation-id="' + data[i].id + '">';
                annotation_insert += '<span id="comment-count-' + data[i].id +  '">' + "" + ' </span>';
                annotation_insert += '<span id="comments-word-' + data[i].id + '">' + /* comment_word */ "Comments" + '</span> ';
                annotation_insert += '<img class="comments-button annotation-icon" src="./styles/comments.png"></p>';
                annotation_insert += '<div style="display:none" class="comments" id="comments-' + data[i].id + '">';

                //Comments will be inserted in order into this following div
                annotation_insert += '<div class="comments-region" id="comments-region-' + data[i].id + '"></div> ';


                annotation_insert += '<p><textarea class="comment-box" id="comment-box-' + data[i].id + '" placeholder="Enter a comment..."></textarea>';
                annotation_insert += '<img data-annotation-id="' + data[i].id + '" class="comment-button annotation-comment-icon" src="./styles/comment.png">';
                annotation_insert += '</p></div>'

                annotation_insert += '<hr></a></div>';
                jQuery('#annotation-list').append(annotation_insert);
            }

            //Load them to the screen[display them as highlights]
            annotator_content.annotator('loadAnnotations', data);
            get_comments();

        });

        //Scrolls to the relevant annotation when clicked on
        jQuery('body').on('click', '.annotation-link', function(e) {
            e.preventDefault();
            var id = jQuery(this).parent().attr('id');
            var target = "annotation_" + id;
            var position = document.getElementById(target).offsetTop;

            //Check user browser
            if (navigator.userAgent.indexOf("Chrome") != -1) {
                //User is using Chrome
                jQuery(document.body).animate({
                    scrollTop: position
                }, 750);
            } else {
                jQuery('html').animate({
                    scrollTop: position
                }, 750);
            }
        });

        //Display the comment section when the user clicks on it
        jQuery('body').on('click', '.comment-count', function(e) {
            e.preventDefault();            
            var annotation_id = jQuery(this).data('annotation-id');
            var target = "#comments-" + annotation_id;
            jQuery(target).toggle(400);

        });

        //Highlight the annotation being hovered over
        jQuery('body').on('mouseenter', '.annotation', function(e) {
            var id = this.id;
            var target = "annotation_" + id;
            jQuery('.' + target).toggleClass('annotator-hl-active');
        })

        //Stop annotation the highlighted annotation
        jQuery('body').on('mouseleave', '.annotation', function(e) {
            
            var id = this.id;
            var target = "annotation_" + id;
            jQuery('.' + target).toggleClass('annotator-hl-active');
        })
    });
});
