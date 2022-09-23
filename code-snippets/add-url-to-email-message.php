<?php
/*
* It's possible to filter the email action message with PHP.
* This example will add the URL the form was submitted from to the end of the email notification message.
*/
add_filter(
	'hf_action_email_message',
	function ( $message ) {
		return sprintf( '%s <br />Referrer URL: %s', $message, $_SERVER['REQUEST_URI'] );
	}
);
