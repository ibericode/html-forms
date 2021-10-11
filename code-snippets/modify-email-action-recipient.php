<?php

add_action(
	'hf_action_email_to',
	function ( $to, \HTML_Forms\Submission $submission ) {
		// $to contains the default recipient, from the action settings
		// you can replace it with a different email addresses or turn it into an array of recipients
		return array( $to, 'john.doe@email.com' );
	}
);
