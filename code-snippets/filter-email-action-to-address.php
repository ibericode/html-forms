<?php

add_filter(
	'hf_action_email_to',
	function( $to, $submission ) {
		if ( $submission->data['FOO'] === 'BAR' ) {
			return 'support@acmeinc.com';
		}

		return $to; // default "To" as specified in action settings
	},
	10,
	2
);
