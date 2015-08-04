require(['jquery'], function(jQuery) {
    //TODO: Rename the plugin after it is completed
    //TODO: Rewrite the subscriptions as functions to improve readability
    Annotator.Plugin.StoreLogger = function(element) {
        return {
            pluginInit: function() {
                this.annotator.subscribe("beforeAnnotationCreated", function(annotation) {
                        annotation.url = document.location.href;
                    })
                    .subscribe("annotationCreated", function(annotation) {
                        jQuery.post("./annotator/create.php", JSON.parse(JSON.stringify(annotation)), function(data) {
                            data = JSON.parse(data);
                            annotation.username = data.username;
                            annotation.timecreated = timeConverter(data.timecreated);
                            annotation.id = data.id;
                            console.info("The annotation: %o has just been created!", annotation);
                        });
                    })
                    .subscribe("annotationUpdated", function(annotation) {
                        jQuery.post("./annotator/update.php", JSON.parse(JSON.stringify(annotation)), function(data) {
                            if (data == 0) {
                                //Incorrect user logged in
                                console.error("The annotation couldn't be updated");
                                alert("Warning: You cannot edit annotations created by others!");
                            } else {
                                data = JSON.parse(data);
                                annotation.timecreated = timeConverter(data); //Update the time displayed
                                console.info("The annotation: %o has just been updated!", annotation);
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
                                //TODO: bug, always returns 1 if annotation exists
                                if (data == 1) {
                                    console.info("The annotation: %o has just been deleted!", annotation);
                                } else {
                                    console.error("The annotation couldn't be deleted");
                                    alert("Warning: You cannot delete annotations created by others!");
                                }
                            });
                        } else {
                            //Event was called when user clicked cancel. Do nothing.
                        }
                    })
                    .subscribe("annotationViewerTextField", function(field, annotation) {
                        field.innerHTML += "<br>";
                        field.innerHTML += "<span style='text-align:right'>" + annotation.username + "</span><br>";
                        field.innerHTML += "<span style='text-align:right'>" + annotation.timecreated + "</span>";
                    });
            }
        }
    };


    var annotator_content = jQuery("#annotator-content").annotator();
    annotator_content.annotator('addPlugin', 'StoreLogger');
    //annotator_content.annotator('addPlugin', 'Filter'); //Need to rewrite the Filter plugin
    annotator_content.annotator('addPlugin', 'Tags');
    annotator_content.annotator('addPlugin', 'Unsupported');

    //Load the existing annotations when the page is loaded
    jQuery(document).ready(function() {
        var post_data = {
            url: document.location.href
        }
        jQuery.post("./annotator/load.php", post_data, function(data) {
            //Load the annotations from the database
            data = JSON.parse(data);
            console.log('Data loaded from server: %o', data);
            for (var i = 0; i < data.length; i++) {
                var annotation = data[i];
                annotation.text = annotation.annotation;
                delete annotation.annotation;
                annotation.ranges = JSON.parse(annotation.ranges);
                annotation.highlights = JSON.parse(annotation.highlights);
                annotation.timecreated = timeConverter(annotation.timecreated);

                //Loads them one by one
                annotator_content.annotator('loadAnnotations', [annotation]);
            }
        });
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
