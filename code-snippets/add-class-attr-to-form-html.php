<?php

add_filter(
	'hf_form_element_class_attr',
	function( $class_attr ) {
		return $class_attr . ' my-class';
	}
);
