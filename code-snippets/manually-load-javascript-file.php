<?php

/**
* If you're having issues with the HTML Forms JavaScript file not being loaded,
*  you can use the following snippet to load it manually.
*/
add_action(
	'wp_enqueue_scripts',
	function() {
		wp_enqueue_script( 'html-forms' );
	},
	90
);
