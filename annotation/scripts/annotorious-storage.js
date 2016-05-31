// This file contains functions related to Image annotations.

var annotations = []; // Used to keep track of existing annotations for filtering support.

// Called when the page has loaded.
// Sends a POST request to get all relevant annotations for the image currently being viewed.
// The url of the current page is sent and is used to load the relevant annotations.
require(['jquery'], function($) {
    $(document).ready(function() {
        var post_data = {
            url: getQueryVariables("id")
        };
        $.post("./annotorious/load.php", post_data, function(data) {
            data = JSON.parse(data);
            var editable = data.shift();
            if (!editable) {
                anno.hideSelectionWidget();
            }

            annotations = data.slice(); // Store a copy of all annotation object in 'annotations'.

            // The relevant annotations are stored in data (an array).
            for (var i = 0; i < data.length; i++) {
                // Create a new annotation obj for each.
                var annotation = data[i];
                annotation.text = annotation.annotation;
                delete annotation.annotation;
                annotation.shapes = JSON.parse(annotation.shapes);
                annotation.src = "http://image.to.annotate"; // Base64 workaround.
                annotation.timecreated = timeConverter(annotation.timecreated);

                // Process tags.
                if(annotation.tags) {
                    annotation._tags = annotation.tags.split(/[ ,]+/); // Store as array for processing.
                    annotation._tags = stripHashtags(annotation._tags);
                }

                anno.addAnnotation(annotation);
            }

            updateAnnotationList();
        });
    });

    function updateAnnotationList() {
        data = anno.getAnnotations();
        data.sort(function(a, b) {
            return a.shapes[0].geometry.y - b.shapes[0].geometry.y;
        });

        // Empty the side panel.
        $('#annotation-list .annotation').remove();

        // Add the annotations to the side panel.
        for (var i = 0; i < data.length; i++) {
            // Don't display long annotations in full.
            annotation = data[i];
            if (annotation.text.length > 125) {
                var text = annotation.text.substring(0, 125) + "...";
            } else {
                var text = annotation.text;
            }

            var annotation_insert = '<div class="annotation" id="' + annotation.id + '" title="' + annotation.timecreated + '"><a href="#">';
            annotation_insert += '<p class="text">' + htmlEntities(text) + '</p>';
            annotation_insert += '<p class="username">';
            if (annotation.groupname) {
                annotation_insert += '[' + annotation.groupname + '] ';
            }
            annotation_insert += annotation.username + '</p>';

            // Add commenting section.
            annotation_insert += '<div class="comment-section" id="comment-section-' + annotation.id + '"><p class="comment-count" data-annotation-id="' + data[i].id + '">';
            annotation_insert += '<span id="comment-count-' + annotation.id + '">' + "" + ' </span>';
            annotation_insert += '<span id="comments-word-' + annotation.id + '">' + "Comments" + '</span> ';
            annotation_insert += '<img class="comments-button annotation-icon" src="./styles/comments.png"></p>';
            annotation_insert += '<div style="display:none" class="comments" id="comments-' + data[i].id + '">';

            // Comments will be inserted in order into this following div.
            annotation_insert += '<div class="comments-region" id="comments-region-' + annotation.id + '"></div> ';

            annotation_insert += '<p><textarea class="comment-box" id="comment-box-' + annotation.id + '" placeholder="Enter a comment..."></textarea>';
            annotation_insert += '<img data-annotation-id="' + annotation.id + '" class="comment-button annotation-comment-icon" src="./styles/comment.png">';
            annotation_insert += '</p></div>';

            annotation_insert += '<hr></a></div>';

            $('#annotation-list').append(annotation_insert);
        }

        get_comments();
    }

    // Display the comment section when the user clicks on it.
        $('body').on('click', '.comment-count', function(e) {
            e.preventDefault();
            var annotation_id = $(this).data('annotation-id');
            var target = "#comments-" + annotation_id;
            $(target).toggle(400);

        });

        // Called when an annotation has been created. Sends the data.
        // to the server in a POST request for it to process and save it.
        anno.addHandler('onAnnotationCreated', function(annotation) {
            delete annotation.src; // Waste of data transfer so delete.
            delete annotation.context; // Use annotation.url instead.
            annotation.url = getQueryVariables("id"); // Used to associate annotation with file/doc.

            annotation.tags = $('#annotorious-editor-tag').val();
            $('#annotorious-editor-tag').val(''); // Empty the tag field to avoid conflict with other annotations.

            // Send AJAX request to server to store new annotation.
            $.post("./annotorious/create.php", annotation, function(data) {
                if(trim(data) == "false") {
                    // Time has run out, force a reload. Annotataion also blocked client side.
                    window.location.reload();
                }
                data = JSON.parse(data);
                var editable = data.shift(); // Not used.
                data = data.shift();

                annotation.id = data.id; // Set id to that assigned by the server.
                annotation.username = data.username;
                annotation.userid = data.userid;
                annotation.timecreated = timeConverter(data.timecreated);
                annotation.groupname = data.groupname;

                // Add the new annotation to the 'annotations' array.
                annotations.push(annotation);

                window.location.reload();
            });
        });
});

// Called when the user updates an annotation.
// Sends the new data in a POST request for the server to store it.
anno.addHandler('onAnnotationUpdated', function(annotation) {
    require(['jquery'], function($) {
        annotation.tags = $('#annotorious-editor-tag').val();
        annotation._tags = annotation.tags.split(/[ ,]+/);
        annotation._tags = stripHashtags(annotation._tags);

        $('#annotorious-editor-tag').val(''); // Empty the tag field to avoid conflict with other annotations.

        $.post("./annotorious/update.php", annotation, function(data) {
            if (data === 0) {
                alert("Error! Could not update annotation!");
            } else {
                data = JSON.parse(data);
                annotation.id = data.id; // Set id to that assigned by the server.
                annotation.username = data.username;
                annotation.timecreated = timeConverter(data.timecreated);

                // Update the annotation in the side panel.
                var annotation_to_update = "#" + annotation.id;
                if (annotation.text.length > 125) { // Check if the annotation is too long to display.
                    var text = annotation.text.substring(0, 125) + "...";
                }
                else {
                    var text = annotation.text;
                }

                $(annotation_to_update).find('.text').text(text);

                // Update 'annotations' array also.
                var index = findAnnotation(annotations, annotation.id);
                annotations[index] = annotation;
                clearFilter();
            }
        });
    });
});

// Called when the user clicks the delete button on an annotation.
// Asks the user to confirm deletion.
anno.addHandler('beforeAnnotationRemoved', function(annotation) {
    var r = confirm("Are you sure you want to delete this annotation?");
    if (r === false) {
        // User clicked cancel, prevent annotation deletion.
        return false;
    }
});

// Called when the user confirms that they want to delete an annotation.
// Sends POST request to server to delete the annotation.
// Sends the id of the annotation to be deleted.
anno.addHandler('onAnnotationRemoved', function(annotation) {
    require(['jquery'], function($) {
        var post_data = {
            id: annotation.id
        };
        $.post("./annotorious/delete.php", post_data, function(data) {
            if (data === 0) { // 0 indicates an error occurred (normally wrong user logged in).
                alert("Error! Could not delete annotation!");
            } else {
                var annotation_to_delete = "#" + annotation.id;
                $(annotation_to_delete).remove();

                // Remove from 'annotations' array also.
                index = findAnnotation(annotations, annotation.id);
                annotations.splice(index, 1);
            }
        });
    });
});


// Custom plugin to display extra data on the annotation popups.
annotorious.plugin.ExtraData = function(opt_config_options) {};
annotorious.plugin.ExtraData.prototype.initPlugin = function(anno) {};
annotorious.plugin.ExtraData.prototype.onInitAnnotator = function(annotator) {
    var self = this,
        container = document.createElement('div');
    container.className = "annotorious-editor-text";

    annotator.popup.addField(function(annotation) {
        if (annotation.groupname) {
            return annotation.username + ' [' + annotation.groupname + ']';
        } else {
            return annotation.username;
        }
    });
    annotator.popup.addField(function(annotation) {
        return annotation.timecreated;
    });
};
anno.addPlugin('ExtraData', {});

// Custom plugin to add tag support to annotorious.
annotorious.plugin.Tags = function(opt_config_options) {};
annotorious.plugin.Tags.prototype.initPlugin = function(annotator) {};
annotorious.plugin.Tags.prototype._extendPopup = function(annotator) {
    annotator.popup.addField(function(annotation) {
        var popupContainer = document.createElement('div');
        if (annotation.tags) {
            annotation._tags = annotation.tags.split(/[ ,]+/);
            annotation._tags = stripHashtags(annotation._tags);
            for(var i = 0; i < annotation._tags.length; i++) {
                var el = document.createElement('span');
                el.className = 'annotation-tag';
                el.innerHTML = annotation._tags[i];
                popupContainer.appendChild(el);
            }
        }
        return popupContainer;
    });
};
annotorious.plugin.Tags.prototype._extendEditor = function(annotator) {
    var self = this, container = document.createElement('div');

    annotator.editor.addField(function(annotation) {
        container = document.createElement('textarea');
        container.innerHTML = '';
        container.setAttribute('id', 'annotorious-editor-tag');
        container.setAttribute('placeholder', 'Add some tags...');
        container.setAttribute('rows', '1');
        container.setAttribute('style', 'height:auto'); // Overwrite.
        container.setAttribute('tabindex', '1');
        container.className = 'annotorious-editor-text annotorious-editor-tag-field goog-textarea';

        if (annotation && annotation.tags) {
            container.innerHTML = annotation.tags.split(/[ ,]+/).join(' ');
        }

        return container;
    });
};
annotorious.plugin.Tags.prototype.onInitAnnotator = function(annotator) {
    this._extendPopup(annotator);
    this._extendEditor(annotator);
};

anno.addPlugin('Tags', {});



function findAnnotation(annotations, id) {
    for (var i = 0; i < annotations.length; i++) {
        if (annotations[i].id == id) {
            return i;
        }
    }
    return -1;
}

require(['jquery'], function($) {
    // Highlight the annotation being hovered over.
    $('body').on('mouseenter', '.annotation', function(e) {
        var id = this.id;
        var annotation = findAnnotation(anno.getAnnotations(), id);
        anno.highlightAnnotation(anno.getAnnotations()[annotation]);
    });

    // Stop annotation the highlighted annotation.
    $('body').on('mouseleave', '.annotation', function(e) {
        var id = this.id;
        anno.highlightAnnotation(); // Removes highlight.

    });

    // This fixes an annotorious bug where annotations are moved incorrectly after resizing the window.
    var resizeTimer;
    $(window).on('resize', function(e) {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(function() {
            resetAnnotations();
        });
    });
});


// Filter Support.

// Clears the four filter input fields which will trigger all annotations to be shown.
function clearFilter() {
    require(['jquery'], function($) {
        $('#filter-group').val("");
        $('#filter-user').val("");
        $('#filter-annotation').val("");
        $('#filter-tag').val("");

        resetAnnotations();
    });
}

// Resets the annotations displayed (shows all existing annotations).
function resetAnnotations() {
    anno.removeAll();
    for(var i = 0; i < annotations.length; i++) {
        anno.addAnnotation(annotations[i]);
    }
}

require(['jquery'], function($) {
    $(document).on('keyup', '.filter-item', function() {
        resetAnnotations();

        filter_annotation = $('#filter-annotation').val().trim();
        filter_groupname = $('#filter-group').val().trim();
        filter_user = $('#filter-user').val().trim();
        filter_tag = $('#filter-tag').val().trim();

        for(var i = 0; i < annotations.length; i++) {
            // Hide the annotation if it doesn't match the filters.

            // Check annotation text.
            if(!containsSubstring(annotations[i].text, filter_annotation)) {
                anno.removeAnnotation(annotations[i]);
            }

            // Check username.
            if(!containsSubstring(annotations[i].username, filter_user)) {
                anno.removeAnnotation(annotations[i]);
            }

            // Check groupname, teacher's annotations will always show.
            if(annotations[i].groupname) {
                if(!containsSubstring(annotations[i].groupname, filter_groupname)) {
                    anno.removeAnnotation(annotations[i]);
                }
            }

            // Check tags.
            if(filter_tag.length > 0) {
                if(!annotations[i].tags) {
                    anno.removeAnnotation(annotations[i]);
                }
                else if(filter_tag.split(/[ ,]+/).length > annotations[i]._tags.length) {
                    // More tags have been entered than this annotation has so hide.
                    anno.removeAnnotation(annotations[i]);
                }
                else if(!arrayContains(annotations[i]._tags, stripHashtags(filter_tag.split(/[ ,]+/)))) {
                    anno.removeAnnotation(annotations[i]);
                }
            }

        }
    });

    // Returns true if arr1 contains ALL of the elements in arr2 (or a substring of every element).
    // Returns false otherwise.
    function arrayContains(arr1, arr2) {
        for(var i = 0; i < arr2.length; i++) {
            flag = false;
            for(var j = 0; j < arr1.length; j++) {
                if(containsSubstring(arr1[j], arr2[i])) {
                    flag = true;
                }
            }
            if(flag === false) {
                return false;
            }
        }
        return true;
    }

    // Returns true if a contains the string a contains the (case insensitive) substring b.
    // Returns false otherwise.
    function containsSubstring(a, b) {
        if(a.toLowerCase().trim().indexOf(b.toLowerCase().trim()) > -1) {
            return true;
        }
        else {
            return false;
        }
    }

});

// Strips hashtags from tags.
function stripHashtags(arr) {
    for(var i = 0; i < arr.length; i++) {
        if(arr[i].charAt(0) == "#") {
            arr[i] = arr[i].substr(1);
        }
    }
    return arr;
}