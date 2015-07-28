anno.addHandler('onAnnotationCreated', function(annotation) {
	annotation.context = annotation.context.split("?")[0];
	annotation.url = document.location.href; //The associate the annotation with the file/document
	console.log(annotation);
});

anno.addHandler('onAnnotationUpdated', function(annotation)) {
	annotation.context = annotation.context.split("?")[0];
	annotation.url = document.locaiton.href;
	
}