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
        }
        $.post("./annotorious/load.php", post_data, function(data) {
            data = JSON.parse(data);
            var editable = data.shift();
            if (!editable) {
                anno.hideSelectionWidget();
            }
            console.log('data from server');
            console.log(data);

            //The relevant annotations are stored in data (an array)
            for (var i = 0; i < data.length; i++) {
                //Create a new annotation obj for each
                var annotation = data[i];
                annotation.text = annotation.annotation;
                delete annotation.annotation;
                annotation.shapes = JSON.parse(annotation.shapes);
                annotation.src = "http://image.to.annotate"; //Base64 workaround
                annotation.timecreated = timeConverter(annotation.timecreated);
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
            annotation_insert += '<p class="text">' + text + '</p>';
            annotation_insert += '<p class="username">';
            if (annotation.groupname) {
                annotation_insert += '[' + annotation.groupname + '] ';
            }
            annotation_insert += annotation.username + '</p>'
            annotation_insert += '<hr></a></div>';

            $('#annotation-list').append(annotation_insert);
        }
    }


    /** 
     * Called when an annotation has been created. Sends the data
     * to the server in a POST request for it to process and save it.
     */
    anno.addHandler('onAnnotationCreated', function(annotation) {
        delete annotation.src; //Waste of data transfer so delete
        delete annotation.context; //Use annotation.url instead
        annotation.url = getQueryVariables("id"); //Used to associate annotation with file/doc
        annotation.tags = "";

        //TODO HTML text?

        //Send AJAX request to server to store new annotation
        $.post("./annotorious/create.php", annotation, function(data) {
            data = JSON.parse(data);
            console.log('data from server');
            console.log(data);
            annotation.id = data.id; //Set id to that assigned by the server
            annotation.username = data.username;
            annotation.userid = data.userid;
            annotation.timecreated = timeConverter(data.timecreated);

            updateAnnotationList();
        });
    });
});

/**
 *  Called when the user updates an annotation.
 *  Sends the new data in a POST request for the server to store it.
 */
anno.addHandler('onAnnotationUpdated', function(annotation) {
    console.log(annotation);
    require(['jquery'], function($) {
        $.post("./annotorious/update.php", annotation, function(data) {
            if (data == 0) {
                alert("Error! Could not update annotation!");
            } else {
                data = JSON.parse(data);
                annotation.id = data.id; //Set id to that assigned by the server
                annotation.username = data.username;
                annotation.timecreated = timeConverter(data.timecreated);

                //Update the annotation in the side panel
                var annotation_to_update = "#" + annotation.id;
                $(annotation_to_update).find('.text').text(annotation.text);
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
    if (r == false) {
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
    console.log(annotation);
    require(['jquery'], function($) {
        var post_data = {
            id: annotation.id,
            userid: annotation.userid
        }
        $.post("./annotorious/delete.php", post_data, function(data) {
            console.log('data from server');
            console.log(data);
            if (data == 0) {
                alert("Error! Could not delete annotation!");
            } else {
                var annotation_to_delete = "#" + annotation.id;
                $(annotation_to_delete).remove();
            }
        });
    });
});


//Custom plugin to display extra data on the annotation popups
//Displayed when a user hovers over an annoation
annotorious.plugin.ExtraData = function(opt_config_options) {}
annotorious.plugin.ExtraData.prototype.initPlugin = function(anno) {}
annotorious.plugin.ExtraData.prototype.onInitAnnotator = function(annotator) {
    var self = this,
        container = document.createElement('div');
    container.className = "annotorious-editor-text";

    annotator.popup.addField(function(annotation) {
        if (annotation.groupname) {
            return '<em>' + annotation.username + ' [' + annotation.groupname + ']</em>'
        } else {
            return '<em>' + annotation.username + '</em>'
        }
    });
    annotator.popup.addField(function(annotation) {
        return '<em>' + annotation.timecreated + '</em>';
    });
}
anno.addPlugin('ExtraData', {});

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
    })

    //Stop annotation the highlighted annotation
    $('body').on('mouseleave', '.annotation', function(e) {
        var id = this.id;
        anno.highlightAnnotation(); //Removes highlight

    })
})

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

