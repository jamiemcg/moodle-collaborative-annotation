require(['jquery'], function(jQuery) {
	//Rename the plugin after it is complete
    Annotator.Plugin.StoreLogger = function(element) {
        return {
            pluginInit: function() {
                this.annotator
                    .subscribe("annotationCreated", function(annotation) {
                        console.info("The annotation: %o has just been created!", annotation)

                        //Save the annotation to the database
                    })
                    .subscribe("annotationUpdated", function(annotation) {
                        console.info("The annotation: %o has just been updated!", annotation)
                    })
                    .subscribe("annotationDeleted", function(annotation) {
                      	console.info("The annotation: %o has just been deleted!", annotation)  
                    });
            }
        }
    };

	var annotator_content = jQuery("#annotator-content").annotator();
    
    annotator_content.annotator('addPlugin', 'StoreLogger');
    //annotator_content.annotator('addPlugin', 'Filter'); //Need to rewrite the Filter plugin
    annotator_content.annotator('addPlugin', 'Tags');
    annotator_content.annotator('addPlugin', 'Unsupported'); //Notifies users if their browser is unsupported

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