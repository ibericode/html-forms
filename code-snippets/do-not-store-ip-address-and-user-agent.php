<?php
add_action(
	'hf_process_form',
	function( $form, $submission ) {
		$submission->ip_address = '';
		$submission->user_agent = '';
	},
	10,
	2
);
