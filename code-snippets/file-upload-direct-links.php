<?php

/**
 * Instruct HTML Forms to not link to WP Media attachments for uploaded files, but use a direct link to the actual file.
 */
add_filter( 'hf_file_upload_use_direct_links', '__return_true' );
