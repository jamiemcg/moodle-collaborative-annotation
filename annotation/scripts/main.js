require(['jquery'], function($) {
	$('.nav-side .nav-toggle').on('click', function(e) {
	    e.preventDefault();
	    $(this).parent().toggleClass('nav-open');
	});

	$('.annotation').on('click', function(e) {
	    e.preventDefault();
	});

	$('.annotation').hover(
	    function() {
	        //Start hover, highlight relevant annotation
	    },
	    function() {
	        //End hover, stop highlighting the annotation
	    }
	);
});