<?php

add_filter(
	'hf_form_html',
	function( $html ) {
		$html = str_replace( '<form ', '<form ms-signup="true" ', $html );
		return $html;
	}
);
