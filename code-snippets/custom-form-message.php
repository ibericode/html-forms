<?php

/*
Default message keys:
	success'
	invalid_email'
	required_field_missing
	error
*/

add_filter(
	'hf_form_message_success',
	function( $message ) {
		return 'Your custom success message here';
	}
);
