<?php

use HTML_Forms\Form;
use HTML_Forms\Submission;

/**
 * @param $form_id_or_slug int|string
 * @return Form
 * @throws Exception
 */
function hf_get_form( $form_id_or_slug ) {

    if( is_numeric( $form_id_or_slug ) ) {
        $post = get_post( $form_id_or_slug );

        if( ! $post || $post->post_type !== 'html-form' ) {
            throw new Exception( "Invalid form ID" );
        }
    } else {
        $posts = get_posts(
            array(
                'post_type' => 'html-form',
                'name' => $form_id_or_slug,
                'post_status' => 'publish',
                'numberposts' => 1,
            )
        );

        if( empty( $posts ) ) {
            throw new Exception( 'Invalid form slug' );
        }
        $post = $posts[0];
    }

    // get all post meta in a single call for performance
    $post_meta = get_post_meta( $post->ID );

    // grab & merge form settings
    $default_settings = array(
        'hide_after_success' => 0,
        'redirect_url' => '',
        'required_fields' =>'',
        'email_fields' => '',
    );
    $default_settings = apply_filters( 'hf_form_default_settings', $default_settings );
    $settings = array();
    if( ! empty( $post_meta['_hf_settings'][0] ) ) {
        $settings = (array) maybe_unserialize( $post_meta['_hf_settings'][0] );
    }
    $settings = array_merge( $default_settings, $settings );

    // grab & merge form messages
    $default_messages = array(
        'success' => __('Thank you! We will be in touch soon.', 'html-forms'),
        'invalid_email' => __( 'Sorry, that email address looks invalid.', 'html-forms' ),
        'required_field_missing' => __( "Please fill in the required fields.", "html-forms" ),
        'error' => __( 'Oops. An error occurred.', 'html-forms' ),
    );
    $messages = array();
    foreach( $post_meta as $meta_key => $meta_values ) {
        if( strpos( $meta_key, 'hf_message_' ) === 0 ) {
            $message_key = substr( $meta_key, strlen( 'hf_message_' ) );
            $messages[$message_key] = (string) $meta_values[0];
        }
    }
    $messages = array_merge( $default_messages, $messages );

    // finally, create form instance
    $form = new Form( $post->ID );
    $form->title = $post->post_title;
    $form->slug = $post->post_name;
    $form->markup = $post->post_content;
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
        'limit' => 1000,
    );
    $args = array_merge( $default_args, $args );

    global $wpdb;
    $table = $wpdb->prefix .'hf_submissions';
    $results = $wpdb->get_results( $wpdb->prepare( "SELECT s.* FROM {$table} s WHERE s.form_id = %d ORDER BY s.submitted_at DESC LIMIT %d, %d;", $form_id, $args['offset'], $args['limit'] ), OBJECT_K );
    $submissions = array();
    foreach( $results as $key => $object ) {
        $submission = Submission::from_object( $object );
        $submissions[$key] = $submission;
    }
    return $submissions;
}

/**
 * @param int $submission_id
 * @return Submission
 */
function hf_get_form_submission( $submission_id ) {
    global $wpdb;
    $table = $wpdb->prefix .'hf_submissions';
    $object = $wpdb->get_row( $wpdb->prepare( "SELECT s.* FROM {$table} s WHERE s.id = %d;", $submission_id ), OBJECT );
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

    $settings = get_option( 'hf_settings', array() );

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

    if ( isset( $array[$key] ) ) {
        return $array[$key];
    }

    foreach (explode( '.', $key ) as $segment) {
        if ( ! is_array( $array ) || ! array_key_exists( $segment, $array ) ) {
            return $default;
        }

        $array = $array[$segment];
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
    $tags = array(
        'user'          => array( $replacers, 'user' ),
        'post'          => array( $replacers, 'post'),
        'url_params'    => array( $replacers, 'url_params' ),
    ); 

    /**
    * Filters the available tags in HTML Forms templates, like {{user.user_email}}.
    *
    * Can be used to add simple scalar replacements or more advanced replacement functions that accept a parameter.
    *
    * @param array $tags
    */
    $tags = apply_filters( 'hf_template_tags', $tags );

    $template = preg_replace_callback( '/\{\{ *(\w+)(?:\.([\w\.]+))? *(?:\|\| *(\w+))? *\}\}/', function( $matches ) use ( $tags ) {
        $tag = $matches[1];
        $param = ! isset( $matches[2] ) ? "" : $matches[2];
        $default = ! isset( $matches[3] ) ? "" : $matches[3];
        $value = "";

        // do not change anything if we have no replacer with that key, could be custom user logic or another plugin.
        if( ! isset( $tags[ $tag] ) ) {
            return $matches[0];
        }

        $replacement = $tags[$tag];
        $value = is_callable( $replacement ) ? call_user_func_array( $replacement, array( $param ) ) : $replacement;
        return ! empty( $value ) ? $value : $default;
    }, $template );

    return $template;    
}

/**
 * @param string $string
 * @param array $data
 *
 * @return string
 */
function hf_replace_data_variables( $string, $data = array() ) {
    $string = preg_replace_callback( '/\[([a-zA-Z0-9\-\._]+)\]/', function( $matches ) use ( $data ) {
        $key = $matches[1];
        $replacement = hf_array_get( $data, $key, '' );
        $replacement = is_array( $replacement ) ? join( ', ', $replacement ) : $replacement;
        return $replacement;
    }, $string );
    return $string;
}

/**
* Poor man's file object, compatible with serialization..
*
* @return bool
*/
function hf_is_file( $file ) {
    return isset( $file['name'] ) && isset( $file['size'] ) && isset( $file['type'] );
}

/** 
* @return string
*/
function hf_human_filesize($size, $precision = 2) {
    for($i = 0; ($size / 1024) > 0.9; $i++, $size /= 1024) {}
    return round($size, $precision).['B','kB','MB','GB','TB','PB','EB','ZB','YB'][$i];
}
