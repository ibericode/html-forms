<?php

/*
* This example replaces {{replace_me}} with "replaced!" using a simple string replacement.
*
* Example: {{replace_me}}
*/
add_filter(
	'hf_template_tags',
	function( $tags ) {
		$tags['replace_me'] = 'replaced!';
		return $tags;
	}
);

/*
* This example uses a function accepting a single parameter to determine the replacement value
*
* Example: {{replace_me.foo}}
*/
add_filter(
	'hf_template_tags',
	function( $tags ) {
		$tags['replace_me'] = function( $key ) {
			if ( $key === 'foo' ) {
				return 'bar';
			}

			return 'not bar';
		};
		return $tags;
	}
);
