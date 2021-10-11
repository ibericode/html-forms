<?php

/**
* Change the max filesize for file uploads in HTML Forms Premium to 1MB
*/
add_filter(
	'hf_upload_max_filesize',
	function( $size ) {
		// size in bytes, 1000000 = 1MB
		return 1000000;
	}
);
