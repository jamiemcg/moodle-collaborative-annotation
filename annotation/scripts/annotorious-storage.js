/**
  * Called when an annotation has been created. Send the data
  * to the server for it to process and save it.
  */
anno.addHandler('onAnnotationCreated', function(annotation) {
	delete annotation.src; //Waste of data so delete. Not required?
	delete annotation.context; //Use annotation.url instead
	annotation.url = document.location.href; //Used to associate annotation with file/doc

	console.log(annotation);
	require(['jquery'], function($) {
		$.post("./annotorious/create.php", annotation, function(data) {
		console.log(data);
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
	var r=confirm("Are you sure you want to delete this annotation?");
	if (r==false) {	return false;}
});


/**
  * Called when the user confirms that they want to delete an annotation.
  * Sends request to server to delete the annotation.
  */
anno.addHandler('onAnnotationRemoved', function(annotation) {
	annotation.url = document.location.href;
	console.log(annotation);
});
