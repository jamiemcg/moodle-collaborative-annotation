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
            url: document.location.href
        }
        $.post("./annotorious/load.php", post_data, function(data) {
            console.log('data from server');
            data = JSON.parse(data);
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
        });
    });
});


/**
 * Called when an annotation has been created. Send the data
 * to the server for it to process and save it.
 */
anno.addHandler('onAnnotationCreated', function(annotation) {
    delete annotation.src; //Waste of data so delete. Not required?
    delete annotation.context; //Use annotation.url instead
    annotation.url = document.location.href; //Used to associate annotation with file/doc
    annotation.tags = "";

    //TODO allow input of html text

    //Send AJAX request to server to store new annotation
    require(['jquery'], function($) {
        $.post("./annotorious/create.php", annotation, function(data) {
            data = JSON.parse(data);
            console.log('data from server');
            console.log(data);
            console.log(data.username);
            annotation.id = data.id; //Set id to that assigned by the server
            annotation.username = data.username;
            annotation.timecreated = timeConverter(data.timecreated);
        });
    });
    console.log('annotation after server');
    console.log(annotation);
});

anno.addHandler('onAnnotationUpdated', function(annotation) {
    console.log(annotation);
    require(['jquery'], function($) {
    	$.post("./annotorious/update.php", annotation, function(data) {
    		data = JSON.parse(data);
            annotation.id = data.id; //Set id to that assigned by the server
            annotation.username = data.username;
            annotation.timecreated = timeConverter(data.timecreated);
    	});
    });
});

/**
 *	Called when the user clicks the delete button on an annotation.
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
 * Sends request to server to delete the annotation. Sends the id of the
 * annotation to be deleted.
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
        });

    });
});


//Custom plugin to display extra data on the annotation popups
//Displayed when a user hovers over an annoation
annotorious.plugin.ExtraData = function(opt_config_options) {}
annotorious.plugin.ExtraData.prototype.initPlugin = function(anno) {
    //initialisation code goes here
}

annotorious.plugin.ExtraData.prototype.onInitAnnotator = function(annotator) {
    annotator.popup.addField(function(annotation) {
        return '<em>' + annotation.username + '</em>'
    });
    annotator.popup.addField(function(annotation) {
        return '<em>' + annotation.timecreated + '<em>';
    });
}

anno.addPlugin('ExtraData', {});


function timeConverter(UNIX_timestamp) {
    var a = new Date(UNIX_timestamp * 1000);
    var months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
    var year = a.getFullYear();
    var month = months[a.getMonth()];
    var date = a.getDate();
    var hour = a.getHours();
    if(hour < 10) {
    	hour = "0" + hour;
    }
    var min = a.getMinutes();
    if(min < 10) {
    	min = "0" + min;
    }
    var time = date + ' ' + month + ' ' + year + ' ' + hour + ':' + min;
    return time;
}
