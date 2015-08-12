/**
  *	This 'main.js' file contains shared code for annotating images and text
  */
require(['jquery'], function($) {
	$('.nav-side .nav-toggle').on('click', function(e) {
	    e.preventDefault();
	    $(this).parent().toggleClass('nav-open');
	});
});
