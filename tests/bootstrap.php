<?php 

define('ABSPATH', '');
define('HTML_FORMS_VERSION', '1.0');

require __DIR__ . '/../vendor/autoload.php';

// mocked functions
function esc_attr( $a ) { 
	return $a; 
}

function esc_html($a) {
   return $a;
}

function __( $a, $text_domain = '' ) { 
	return $a; 
}
