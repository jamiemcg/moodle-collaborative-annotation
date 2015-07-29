/**
  * Called when the page has loaded.
  * Sends a GET request to get all relevant annotations
  * for the image currently being viewed.
  */
require(['jquery'], function($) {
	$(document).ready(function() {
		/* TODO
		 * AJAX request, with curr page id
		 * get all results, loop through creating and 
		 * displaying the new annotation objects
		 * check if the annotation.userid = current.userid, 
		 * if not, disable edit [editable: false]
		*/
		console.log('called');
		var myAnnotation = {
		    src : 'http://image.to.annotate',
		    text : 'My annotation',
		    username: 'Jamie McGowan',
		    userid: 1
		    timecreated: new Date().toLocaleString(),
		    shapes : [{
		        type : 'rect',
		        geometry : { x : 0.1, y: 0.1, width : 0.5, height: 0.3 }
		    }]
		};
		anno.addAnnotation(myAnnotation);
		console.log(myAnnotation);
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
    annotation.username = "Jamie McGowan"; //TODO

    //TODO this should be handled by server
    var d = new Date();


    annotation.timecreated = d.getDate() + "/" + (d.getMonth() + 1) + "/" + d.getFullYear();
    annotation.timecreated += " " + d.getHours() + ":" + d.getMinutes();

    console.log(annotation);
    //Send AJAX request to server to store new annotation
    require(['jquery'], function($) {
        $.post("./annotorious/create.php", annotation, function(data) {
            console.log(data);
            annotation.id = data.id; //Set id to that assigned by the server

            var d = data.timecreated;
            annotation.timecreated = d.toLocaleTimeString() + " " + d.toLocaleDateString(); //change the
        });
    });
});

anno.addHandler('onAnnotationUpdated', function(annotation) {
    console.log(annotation);
    annotation.url = document.location.href;
});

/**
 *	Called when the user clicks the delete button on an annotation.
 * Asks the user to confirm deletion.
 */

anno.addHandler('beforeAnnotationRemoved', function(annotation) {
    var r = confirm("Are you sure you want to delete this annotation?");
    if (r == false) 
    {
    	//User clicked cancel, prevent annotation deletion
        return false;
    }
});




/**
 * Called when the user confirms that they want to delete an annotation.
 * Sends request to server to delete the annotation.
 */
anno.addHandler('onAnnotationRemoved', function(annotation) {
    annotation.url = document.location.href;
    console.log(annotation);
});


//Custom plugin to display extra data on the annotation popups
//Displayed when a user hovers over an annoation
annotorious.plugin.ExtraData = function(opt_config_options) { }
annotorious.plugin.ExtraData.prototype.initPlugin = function(anno) {
	//initialisation code goes here
}

annotorious.plugin.ExtraData.prototype.onInitAnnotator = function(annotator)	 {
	annotator.popup.addField(function(annotation) {
		return '<em>' + annotation.username + '</em>'
	});
	annotator.popup.addField(function(annotation) {
		return '<em>' + annotation.timecreated + '<em>';
	});
}

anno.addPlugin('ExtraData', {});