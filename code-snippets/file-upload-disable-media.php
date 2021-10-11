<?php
/**
* Configure HTML Forms Premium to not add file uploads to the WP Admin Media
*/
add_filter( 'hf_upload_add_to_media', '__return_false' );
