(function( $ ) {
	'use strict';

	var Obj = {
		// All pages
		'common' : {
			init : function() {
				// JavaScript to be fired on all pages

				// Post formats
				$(function() {
					$("#video_metabox").hide();

					// post formats
					$('#post-format-0').click(function()
					{
						$("#video_metabox").hide();
					});
					$('#post-format-image').click(function()
					{
						$("#video_metabox").hide();
					});
					$('#post-format-video').click(function()
					{
						$("#video_metabox").show();
					});
					$('#post-format-gallery').click(function()
					{
						$("#video_metabox").hide();
					});
					$('#post-format-link').click(function()
					{
						$("#video_metabox").hide();
					});
					$('#post-format-audio').click(function()
					{
						$("#video_metabox").hide();
					});
					if ( $("#post-format-video").attr("checked") == "checked" )
					{
						$("#video_metabox").show();
					}

				});
				// End post formats

			},
		}
	};

	// The routing fires all common scripts, followed by the page specific scripts.
	// Add additional events for more control over timing e.g. a finalize event
	var UTIL = {
		fire : function( func, funcname, args ) {
			var fire;
			var namespace = Obj;
			funcname      = (funcname === undefined) ? 'init' : funcname;
			fire          = func !== '';
			fire          = fire && namespace[ func ];
			fire          = fire && typeof namespace[ func ][ funcname ] === 'function';

			if ( fire ) {
				namespace[ func ][ funcname ]( args );
			}
		},
		loadEvents : function() {
			// Fire common init JS
			UTIL.fire( 'common' );

			// Fire page-specific init JS, and then finalize JS
			$.each( document.body.className.replace( /-/g, '_' ).split( /\s+/ ), function( i, classnm ) {
				UTIL.fire( classnm );
				UTIL.fire( classnm, 'finalize' );
			} );

			// Fire common finalize JS
			UTIL.fire( 'common', 'finalize' );
		}
	};

	// Load Events
	$( document ).ready( UTIL.loadEvents );

})( jQuery ); // Fully reference jQuery after this point.