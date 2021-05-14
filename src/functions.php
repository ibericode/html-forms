<?php

use HTML_Forms\Form;
use HTML_Forms\Submission;

/**
 * @param array $args
 * @return array
 */
function hf_get_forms( array $args = array() ) {
	$default_args = array(
		'post_type'           => 'html-form',
		'post_status'         => array( 'publish', 'draft', 'pending', 'future' ),
		'posts_per_page'      => -1,
		'ignore_sticky_posts' => true,
		'no_found_rows'       => true,
	);
	$args         = array_merge( $default_args, $args );
	$query        = new WP_Query;
	$posts        = $query->query( $args );
	$forms        = array_map( 'hf_get_form', $posts );
	return $forms;
}

/**
 * @param $form_id_or_slug int|string|WP_Post
 * @return Form
 * @throws Exception
 */
function hf_get_form( $form_id_or_slug ) {

	if ( is_numeric( $form_id_or_slug ) || $form_id_or_slug instanceof WP_Post ) {
		$post = get_post( $form_id_or_slug );

		if ( ! $post instanceof WP_Post || $post->post_type !== 'html-form' ) {
			throw new Exception( 'Invalid form ID' );
		}
	} else {

		$query = new WP_Query;
		$posts = $query->query(
			array(
				'post_type'           => 'html-form',
				'name'                => $form_id_or_slug,
				'post_status'         => 'publish',
				'posts_per_page'      => 1,
				'ignore_sticky_posts' => true,
				'no_found_rows'       => true,
			)
		);
		if ( empty( $posts ) ) {
			throw new Exception( 'Invalid form slug' );
		}
		$post = $posts[0];
	}

	// get all post meta in a single call for performance
	$post_meta = get_post_meta( $post->ID );

	// grab & merge form settings
	$default_settings = array(
		'save_submissions'   => 1,
		'hide_after_success' => 0,
		'redirect_url'       => '',
		'required_fields'    => '',
		'email_fields'       => '',
	);
	$default_settings = apply_filters( 'hf_form_default_settings', $default_settings );
	$settings         = array();
	if ( ! empty( $post_meta['_hf_settings'][0] ) ) {
		$settings = (array) maybe_unserialize( $post_meta['_hf_settings'][0] );
	}
	$settings = array_merge( $default_settings, $settings );

	// grab & merge form messages
	$default_messages = array(
		'success'                => __( 'Thank you! We will be in touch soon.', 'html-forms' ),
		'invalid_email'          => __( 'Sorry, that email address looks invalid.', 'html-forms' ),
		'required_field_missing' => __( 'Please fill in the required fields.', 'html-forms' ),
		'error'                  => __( 'Oops. An error occurred.', 'html-forms' ),
	);
	$default_messages = apply_filters( 'hf_form_default_messages', $default_messages );
	$messages         = array();
	foreach ( $post_meta as $meta_key => $meta_values ) {
		if ( strpos( $meta_key, 'hf_message_' ) === 0 ) {
			$message_key              = substr( $meta_key, strlen( 'hf_message_' ) );
			$messages[ $message_key ] = (string) $meta_values[0];
		}
	}
	$messages = array_merge( $default_messages, $messages );

	// finally, create form instance
	$form           = new Form( $post->ID );
	$form->title    = $post->post_title;
	$form->slug     = $post->post_name;
	$form->markup   = $post->post_content;
	$form->settings = $settings;
	$form->messages = $messages;
	return $form;
}

/**
 * @param $form_id
 * @param array $args
 * @return Submission[]
 */
function hf_get_form_submissions( $form_id, array $args = array() ) {
	$default_args = array(
		'offset' => 0,
		'limit'  => 1000,
	);
	$args         = array_merge( $default_args, $args );

	global $wpdb;
	$table       = $wpdb->prefix . 'hf_submissions';
	$results     = $wpdb->get_results( $wpdb->prepare( "SELECT s.* FROM {$table} s WHERE s.form_id = %d ORDER BY s.submitted_at DESC LIMIT %d, %d;", $form_id, $args['offset'], $args['limit'] ), OBJECT_K );
	$submissions = array();
	foreach ( $results as $key => $object ) {
		$submission          = Submission::from_object( $object );
		$submissions[ $key ] = $submission;
	}
	return $submissions;
}

/**
 * @param int $submission_id
 * @return Submission
 */
function hf_get_form_submission( $submission_id ) {
	global $wpdb;
	$table      = $wpdb->prefix . 'hf_submissions';
	$object     = $wpdb->get_row( $wpdb->prepare( "SELECT s.* FROM {$table} s WHERE s.id = %d;", $submission_id ), OBJECT );
	$submission = Submission::from_object( $object );
	return $submission;
}
/**
 * @return array
 */
function hf_get_settings() {
	$default_settings = array(
		'load_stylesheet' => 0,
	);

	$settings = get_option( 'hf_settings', null );

	// prevent a SQL query when option does not yet exist
	if ( $settings === null ) {
		update_option( 'hf_settings', array(), true );
		$settings = array();
	}

	// merge with default settings
	$settings = array_merge( $default_settings, $settings );

	/**
	* Filters the global HTML Forms hf_settings
	*
	* @param array $settings
	*/
	$settings = apply_filters( 'hf_settings', $settings );

	return $settings;
}

/**
* Get element from array, allows for dot notation eg: "foo.bar"
*
* @param array $array
* @param string $key
* @param mixed $default
* @return mixed
*/
function hf_array_get( $array, $key, $default = null ) {
	if ( is_null( $key ) ) {
		return $array;
	}

	if ( isset( $array[ $key ] ) ) {
		return $array[ $key ];
	}

	foreach ( explode( '.', $key ) as $segment ) {
		if ( ! is_array( $array ) || ! array_key_exists( $segment, $array ) ) {
			return $default;
		}

		$array = $array[ $segment ];
	}

	return $array;
}

/**
 * Processes template tags like {{user.user_email}}
 *
 * @param string $template
 *
 * @return string
 */
function hf_template( $template ) {
	$replacers = new HTML_Forms\TagReplacers();
	$tags      = array(
		'user'       => array( $replacers, 'user' ),
		'post'       => array( $replacers, 'post' ),
		'url_params' => array( $replacers, 'url_params' ),
	);

	/**
	* Filters the available tags in HTML Forms templates, like {{user.user_email}}.
	*
	* Can be used to add simple scalar replacements or more advanced replacement functions that accept a parameter.
	*
	* @param array $tags
	*/
	$tags = apply_filters( 'hf_template_tags', $tags );

	$template = preg_replace_callback(
		'/\{\{ *(\w+)(?:\.([\w\.]+))? *(?:\|\| *(\w+))? *\}\}/',
		function( $matches ) use ( $tags ) {
			$tag     = $matches[1];
			$param   = ! isset( $matches[2] ) ? '' : $matches[2];
			$default = ! isset( $matches[3] ) ? '' : $matches[3];

			// do not change anything if we have no replacer with that key, could be custom user logic or another plugin.
			if ( ! isset( $tags[ $tag ] ) ) {
				return $matches[0];
			}

			$replacement = $tags[ $tag ];
			$value       = is_callable( $replacement ) ? call_user_func_array( $replacement, array( $param ) ) : $replacement;
			return ! empty( $value ) ? $value : $default;
		},
		$template
	);

	return $template;
}

/**
 * @param string $string
 * @param array $data
 * @param Closure|string $escape_function
 * @return string
 */
function hf_replace_data_variables( $string, $data = array(), $escape_function = null ) {
	return preg_replace_callback(
		'/\[(.+?)\]/',
		function( $matches ) use ( $data, $escape_function ) {
			$key = $matches[1];
			// replace spaces in name with underscores to match PHP requirement for keys in $_POST superglobal
			$key         = str_replace( ' ', '_', $key );
			$replacement = hf_array_get( $data, $key, '' );
			$replacement = hf_field_value( $replacement, 0, $escape_function );
			return $replacement;
		},
		$string
	);
}

/**
* Returns a formatted & HTML-escaped field value. Detects file-, array- and date-types.
*
* Caveat: if value is a file, an HTML string is returned (which means email action should use "Content-Type: html" when it includes a file field).
*
* @param string|array $value
* @param int $limit
* @param Closure|string $escape_function
* @return string
* @since 1.3.1
*/
function hf_field_value( $value, $limit = 0, $escape_function = 'esc_html' ) {
	if ( $value === '' ) {
		return $value;
	}

	if ( hf_is_file( $value ) ) {
		$file_url = isset( $value['url'] ) ? $value['url'] : '';
		if ( isset( $value['attachment_id'] ) && apply_filters( 'hf_file_upload_use_direct_links', false ) === false ) {
			$file_url = admin_url( sprintf( 'post.php?action=edit&post=%d', $value['attachment_id'] ) );
		}
		$short_name = substr( $value['name'], 0, 20 );
		$suffix     = strlen( $value['name'] ) > 20 ? '...' : '';
		return sprintf( '<a href="%s">%s%s</a> (%s)', esc_attr( $file_url ), esc_html( $short_name ), esc_html( $suffix ), hf_human_filesize( $value['size'] ) );
	}

	if ( hf_is_date( $value ) ) {
		$date_format = get_option( 'date_format' );
		return gmdate( $date_format, strtotime( $value ) );
	}

	// join array-values with comma
	if ( is_array( $value ) ) {
		$value = join( ', ', $value );
	}

	// limit string to certain length
	if ( $limit > 0 ) {
		$limited = strlen( $value ) > $limit;
		$value   = substr( $value, 0, $limit );

		if ( $limited ) {
			$value .= '...';
		}
	}

	// escape value
	if ( $escape_function !== null && is_callable( $escape_function ) ) {
		$value = $escape_function( $value );
	}

	return $value;
}

/**
* Returns true if value is a "file"
*
* @param mixed $value
* @return bool
*/
function hf_is_file( $value ) {
	return is_array( $value )
		&& isset( $value['name'] )
		&& isset( $value['size'] )
		&& isset( $value['type'] );
}

/**
* Returns true if value looks like a date-string submitted from a <input type="date">
* @param mixed $value
* @return bool
* @since 1.3.1
*/
function hf_is_date( $value ) {
	if ( ! is_string( $value )
			|| strlen( $value ) !== 10
			|| (int) preg_match( '/\d{2,4}[-\/]\d{2}[-\/]\d{2,4}/', $value ) === 0 ) {
		return false;
	}

	$timestamp = strtotime( $value );
	return $timestamp != false;
}

/**
 * @param int $size
 * @param int $precision
 * @return string
*/
function hf_human_filesize( $size, $precision = 2 ) {
	for ( $i = 0; ( $size / 1024 ) > 0.9; $i++, $size /= 1024 ) {
		// nothing, loop logic contains everything
	}
	$steps = array( 'B', 'kB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB' );
	return round( $size, $precision ) . $steps[ $i ];
}

/**
 * Gets all the form tabs to show in the admin.
 * @param Form $form
 * @return array
 */
function hf_get_admin_tabs( Form $form ) {
	$tabs = array(
		'fields'   => __( 'Fields', 'html-forms' ),
		'messages' => __( 'Messages', 'html-forms' ),
		'settings' => __( 'Settings', 'html-forms' ),
		'actions'  => __( 'Actions', 'html-forms' ),
	);

	if ( $form->settings['save_submissions'] ) {
		$tabs['submissions'] = __( 'Submissions', 'html-forms' );
	}
	return apply_filters( 'hf_admin_tabs', $tabs, $form );
}

function _hf_on_plugin_activation() {
	if ( is_multisite() ) {
		_hf_on_plugin_activation_multisite();
		return;
	}

	// install table for regular wp install
	_hf_create_submissions_table();

	// add "edit_forms" cap to user that activated the plugin
	$user = wp_get_current_user();
	$user->add_cap( 'edit_forms', true );
}

function _hf_on_plugin_activation_multisite() {
	$added_caps = array();

	foreach ( get_sites( array( 'number' => PHP_INT_MAX ) ) as $site ) {
		switch_to_blog( (int) $site->blog_id );

		// install table for current blog
		_hf_create_submissions_table();

		// iterate through current blog admins
		foreach ( get_users(
			array(
				'blog_id' => (int) $site->blog_id,
				'role'    => 'administrator',
				'fields'  => 'ID',
			)
		) as $admin_id ) {
			if ( ! (int) $admin_id || in_array( $admin_id, $added_caps ) ) {
				continue;
			}

			// add "edit_forms" cap to site admin
			$user = new \WP_User( (int) $admin_id );
			$user->add_cap( 'edit_forms', true );

			$added_caps[] = $admin_id;
		}

		restore_current_blog();
	}
}

// install table for main site on regular installs, or active site for multisite
function _hf_create_submissions_table() {
	/** @var wpdb */
	global $wpdb;

	// create table for storing submissions
	$table = $wpdb->prefix . 'hf_submissions';
	$wpdb->query(
		"CREATE TABLE IF NOT EXISTS {$table}(
        `id` INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
        `form_id` INT UNSIGNED NOT NULL,
        `data` TEXT NOT NULL,
        `user_agent` TEXT NULL,
        `ip_address` VARCHAR(255) NULL,
        `referer_url` TEXT NULL,
        `submitted_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=INNODB CHARACTER SET={$wpdb->charset};"
	);
}

function _hf_on_add_user_to_blog( $user_id, $role, $blog_id ) {
	if ( 'administrator' !== $role ) {
		return;
	}

	// add "edit_forms" cap to site admin
	$user = new \WP_User( (int) $user_id );
	$user->add_cap( 'edit_forms', true );
}

function _hf_on_wp_insert_site( \WP_Site $site ) {
	switch_to_blog( (int) $site->blog_id );
	_hf_create_submissions_table();
	restore_current_blog();
}
