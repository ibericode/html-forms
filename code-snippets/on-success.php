<?php

/*
* This code runs for every successful form submission
*/
add_action(
	'hf_form_success',
	function( $submission, $form ) {
		// You can do stuff here.
		// $form contains details about the submitted form
		// $submission contains details about the submission, like the form data.
	},
	10,
	2
);
