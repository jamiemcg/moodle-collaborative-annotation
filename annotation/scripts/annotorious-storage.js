/**
  * Called when an annotation has been created. Send the data
  * to the server for it to process and save it.
  */
anno.addHandler('onAnnotationCreated', function(annotation) {
	delete annotation.src;
	delete annotation.context;
	annotation.url = document.location.href;

	console.log(annotation);
	require(['jquery'], function($) {
		$.post("./annotorious/create.php", annotation, function(data) {
		console.log(data);
		});
	});
	
});

anno.addHandler('onAnnotationUpdated', function(annotation) {
 	annotation.context=annotation.context.split("?")[0];
 	console.log(annotation);
 	annotation.url = document.location.href;
});

anno.addHandler('beforeAnnotationRemoved', function(annotation) {
	var r=confirm("Are you sure you want to delete this annotation?");
	if (r==false) {	return false;}
});

anno.addHandler('onAnnotationRemoved', function(annotation) {
	annotation.url = document.location.href;
	console.log(annotation);
});