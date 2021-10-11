<?php

// validate form request (all forms)
// this will validate that the field with name "BEST_VEGETABLE" is set and has a value of "carrot"
add_filter(
	'hf_validate_form',
	function( $error_code, $form, $data ) {
		if ( ! isset( $data['BEST_VEGETABLE'] ) || $data['BEST_VEGETABLE'] !== 'carrot' ) {
			$error_code = 'wrong_answer';
		}

		return $error_code;
	},
	10,
	3
);

// validate form request (form with ID 60 only)
add_filter(
	'hf_validate_form',
	function( $error_code, $form, $data ) {
		if ( $form->ID != 60 ) {
			return $error_code;
		}

		if ( ! isset( $data['BEST_VEGETABLE'] ) || $data['BEST_VEGETABLE'] !== 'carrot' ) {
			$error_code = 'wrong_answer';
		}

		return $error_code;
	},
	10,
	3
);

// register error message for our custom error code
add_filter(
	'hf_form_message_wrong_answer',
	function( $message ) {
		return 'Sorry, but the best vegetable is a carrot!';
	}
);
