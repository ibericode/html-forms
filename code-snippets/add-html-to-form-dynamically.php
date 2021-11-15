<?php

add_filter(
	'hf_form_markup',
	function( $markup ) {
		$markup .= '<input type="hidden" name="HIDDEN_FIELD" value="My value" />';
		return $markup;
	}
);


// HTML forms will check the number of configured fields versus the number of submitted fields, and ignore the submission if it's mismatching.
// For that reason, if you like to dynamically add more than 1 field dynamically, you need to disable he field counter with the following filter:

add_filter( 'hf_validate_form_request_size', false );

