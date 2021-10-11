<?php

add_filter(
	'hf_form_markup',
	function( $markup ) {
		$markup .= '<input type="hidden" name="HIDDEN_FIELD" value="My value" />';
		return $markup;
	}
);
