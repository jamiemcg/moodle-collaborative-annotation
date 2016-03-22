/*
    This file contains functions related to Image annotations
*/

var annotations = []; //Used to keep track of existing annotations for filtering support

/**
 * Called when the page has loaded.
 * Sends a POST request to get all relevant annotations
 * for the image currently being viewed. The url of the
 * current page is sent and is used to load the relevant
 * annotations.
 */
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

            annotations = data; //Store a copy of all annotation object in 'annotations'

            //The relevant annotations are stored in data (an array)
            for (var i = 0; i < data.length; i++) {
                //Create a new annotation obj for each
                var annotation = data[i];
                annotation.text = annotation.annotation;
                delete annotation.annotation;
                annotation.shapes = JSON.parse(annotation.shapes);
                annotation.src = "http://image.to.annotate"; //Base64 workaround
                annotation.timecreated = timeConverter(annotation.timecreated);

                //Process tags
                if(annotation.tags) {
                    annotation._tags = annotation.tags.split(/[ ,]+/); //Store as array for processing
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

        //Empty the side panel
        $('#annotation-list .annotation').remove();

        //Add the annotations to the side panel 
        for (var i = 0; i < data.length; i++) {
            //Don't display long annotations in full
            annotation = data[i];
            if (annotation.text.length > 125) {
                var text = annotation.text.substring(0, 125) + "...";
            } else {
                var text = annotation.text;
            }


            var annotation_insert = '<div class="annotation" id="' + annotation.id + '" title="' + annotation.timecreated +
                '"><a href="#">';
            annotation_insert += '<p class="text">' + htmlEntities(text) + '</p>';
            annotation_insert += '<p class="username">';
            if (annotation.groupname) {
                annotation_insert += '[' + annotation.groupname + '] ';
            }
            annotation_insert += annotation.username + '</p>';

            //Add commenting section
            annotation_insert += '<div class="comment-section" id="comment-section-' + annotation.id +'"><p class="comment-count" data-annotation-id="' + data[i].id + '">';
            annotation_insert += '<span id="comment-count-' + annotation.id +  '">' + "" + ' </span>';
            annotation_insert += '<span id="comments-word-' + annotation.id + '">' + /* comment_word */ "Comments" + '</span> ';
            annotation_insert += '<img class="comments-button annotation-icon" src="./styles/comments.png"></p>';
            annotation_insert += '<div style="display:none" class="comments" id="comments-' + data[i].id + '">';

            //Comments will be inserted in order into this following div
            annotation_insert += '<div class="comments-region" id="comments-region-' + annotation.id + '"></div> ';


            annotation_insert += '<p><textarea class="comment-box" id="comment-box-' + annotation.id + '" placeholder="Enter a comment..."></textarea>';
            annotation_insert += '<img data-annotation-id="' + annotation.id + '" class="comment-button annotation-comment-icon" src="./styles/comment.png">';
            annotation_insert += '</p></div>';            

            annotation_insert += '<hr></a></div>';

            $('#annotation-list').append(annotation_insert);
        }
        
        get_comments();
    }

    //Display the comment section when the user clicks on it
        $('body').on('click', '.comment-count', function(e) {
            e.preventDefault();            
            var annotation_id = $(this).data('annotation-id');
            var target = "#comments-" + annotation_id;
            $(target).toggle(400);

        });


    /** 
     * Called when an annotation has been created. Sends the data
     * to the server in a POST request for it to process and save it.
     */
    anno.addHandler('onAnnotationCreated', function(annotation) {
        delete annotation.src; //Waste of data transfer so delete
        delete annotation.context; //Use annotation.url instead
        annotation.url = getQueryVariables("id"); //Used to associate annotation with file/doc
        
        annotation.tags = $('#annotorious-editor-tag').val();
        $('#annotorious-editor-tag').val(''); //Empty the tag field to avoid conflict with other annotations



        //Send AJAX request to server to store new annotation
        $.post("./annotorious/create.php", annotation, function(data) {
            if(trim(data) == "false") {
                //Time has run out, forace a reload. Annotataion also blocked client side
                window.location.reload();
            }
            data = JSON.parse(data);
            var editable = data.shift(); //Not used
            data = data.shift();
            
            annotation.id = data.id; //Set id to that assigned by the server
            annotation.username = data.username;
            annotation.userid = data.userid;
            annotation.timecreated = timeConverter(data.timecreated);
            annotation.groupname = data.groupname;

            //Add the new annotation to the 'annotations' array
            annotations.push(annotation);
            updateAnnotationList();
        });
    });
});

/**
 *  Called when the user updates an annotation.
 *  Sends the new data in a POST request for the server to store it.
 */
anno.addHandler('onAnnotationUpdated', function(annotation) {
    require(['jquery'], function($) {
        annotation.tags = $('#annotorious-editor-tag').val();
        $('#annotorious-editor-tag').val(''); //Empty the tag field to avoid conflict with other annotations

        $.post("./annotorious/update.php", annotation, function(data) {
            if (data === 0) {
                alert("Error! Could not update annotation!");
            } else {
                data = JSON.parse(data);
                annotation.id = data.id; //Set id to that assigned by the server
                annotation.username = data.username;
                annotation.timecreated = timeConverter(data.timecreated);

                //Update the annotation in the side panel
                var annotation_to_update = "#" + annotation.id;
                if (annotation.text.length > 125) { //Check if the annotation is too long to display
                    var text = annotation.text.substring(0, 125) + "...";
                } 
                else {
                    var text = annotation.text;
                }

                $(annotation_to_update).find('.text').text(text);

                //Update 'annotations' array also
                var index = findAnnotation(annotations, annotation.id);
                annotations[index] = annotation;
            }
        });
    });
});

/**
 * Called when the user clicks the delete button on an annotation.
 * Asks the user to confirm deletion.
 */
anno.addHandler('beforeAnnotationRemoved', function(annotation) {
    var r = confirm("Are you sure you want to delete this annotation?");
    if (r === false) {
        //User clicked cancel, prevent annotation deletion
        return false;
    }
});


/**
 * Called when the user confirms that they want to delete an annotation.
 * Sends POST request to server to delete the annotation. Sends the id of 
 * the annotation to be deleted.
 */
anno.addHandler('onAnnotationRemoved', function(annotation) {
    require(['jquery'], function($) {
        var post_data = {
            id: annotation.id
        };
        $.post("./annotorious/delete.php", post_data, function(data) {
            if (data === 0) { //0 indicates an error occurred (normally wrong user logged in)
                alert("Error! Could not delete annotation!");
            } else {
                var annotation_to_delete = "#" + annotation.id;
                $(annotation_to_delete).remove();

                //Remove from 'annotations' array also
                index = findAnnotation(annotations, annotation.id);
                annotations.splice(index, 1);
            }
        });
    });
});


//Custom plugin to display extra data on the annotation popups
//Displayed when a user hovers over an annoation
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

//Custom plugin to add tag support to annotorious
annotorious.plugin.Tags = function(opt_config_options) {};
annotorious.plugin.Tags.prototype.initPlugin = function(annotator) {};
annotorious.plugin.Tags.prototype._extendPopup = function(annotator) {
    annotator.popup.addField(function(annotation) {
        var popupContainer = document.createElement('div');
        if (annotation.tags) {
            annotation._tags = annotation.tags.split(/[ ,]+/);
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
        container.setAttribute('style', 'height:auto'); //Overwrite
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
    //Highlight the annotation being hovered over
    $('body').on('mouseenter', '.annotation', function(e) {
        var id = this.id;
        var annotation = findAnnotation(anno.getAnnotations(), id);
        anno.highlightAnnotation(anno.getAnnotations()[annotation]);
    });

    //Stop annotation the highlighted annotation
    $('body').on('mouseleave', '.annotation', function(e) {
        var id = this.id;
        anno.highlightAnnotation(); //Removes highlight

    });
});


//Filter Support

/*
    Clears the four filter input fields which will trigger all annotations to be shown
*/
function clear_filter() {
    require(['jquery'], function($) {
        $('#filter-group').val("");
        $('#filter-user').val("");
        $('#filter-annotation').val("");
        $('#filter-tag').val("");
    });
}
