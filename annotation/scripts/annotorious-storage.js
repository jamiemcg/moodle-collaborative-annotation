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
		 * do the above checking serverside
		*/

		var post_data = {
			url: document.location.href
		}

		require(['jquery'], function($) {
	        $.post("./annotorious/load.php", post_data, function(data) {
	            console.log('data form server');
	    		console.log(data);
	    	});
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

    //Send AJAX request to server to store new annotation
    require(['jquery'], function($) {
        $.post("./annotorious/create.php", annotation, function(data) {
        	data = JSON.parse(data);
            console.log('data form server');
    		console.log(data);
    		console.log(data.username);
            annotation.id = data.id; //Set id to that assigned by the server
            annotation.username = data.username;
            var d = timeConverter(data.timecreated);
            annotation.timecreated = d;
        });
    });
    console.log('annotation after server');
    console.log(annotation);
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


function timeConverter(UNIX_timestamp){
  var a = new Date(UNIX_timestamp * 1000);
  var months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
  var year = a.getFullYear();
  var month = months[a.getMonth()];
  var date = a.getDate();
  var hour = a.getHours();
  var min = a.getMinutes();
  var sec = a.getSeconds();
  var time = date + ' ' + month + ' ' + year + ' ' + hour + ':' + min + ':' + sec ;
  return time;
}
