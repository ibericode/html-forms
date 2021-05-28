<?php

namespace HTML_Forms;

class Forms {


	/**
	 * @var string
	 */
	private $plugin_file;

	/**
	 * @var array
	 */
	private $settings;

	/**
	 * Forms constructor.
	 *
	 * @param string $plugin_file
	 * @param array $settings
	 */
	public function __construct( $plugin_file, array $settings ) {
		$this->plugin_file = $plugin_file;
		$this->settings    = $settings;
	}

	public function hook() {
		add_action( 'init', array( $this, 'register' ) );
		add_action( 'init', array( $this, 'listen_for_submit' ) );
		add_action( 'parse_request', array( $this, 'listen_for_preview' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'assets' ) );
		add_filter( 'hf_form_markup', 'hf_template' );
	}

	public function register() {
		// register post type
		register_post_type(
			'html-form',
			array(
				'labels'          => array(
					'name'          => 'HTML Forms',
					'singular_name' => 'HTML Form',
				),
				'public'          => false,
				'capability_type' => 'form',
			)
		);

		if ( function_exists( 'register_block_type' ) ) {
			register_block_type(
				'html-forms/form',
				array(
					'render_callback' => array( $this, 'shortcode' ),
				)
			);
		}

		add_shortcode( 'hf_form', array( $this, 'shortcode' ) );
	}

	public function assets() {
		$assets_url = plugins_url( 'assets/', $this->plugin_file );

		wp_register_script( 'html-forms', $assets_url . 'js/public.js', array(), HTML_FORMS_VERSION, true );
		wp_localize_script(
			'html-forms',
			'hf_js_vars',
			array(
				'ajax_url' => admin_url( 'admin-ajax.php?action=hf_form_submit' ),
			)
		);

		if ( $this->settings['load_stylesheet'] ) {
			wp_enqueue_style( 'html-forms', $assets_url . 'css/forms.css', array(), HTML_FORMS_VERSION );
		}
	}

	/**
	 * @param Form $form
	 * @param array $data
	 * @return string
	 */
	public function validate_form( Form $form, array $data ) {
		// validate honeypot field
		$honeypot_key = sprintf( '_hf_h%d', $form->ID );
		if ( ! isset( $data[ $honeypot_key ] ) || $data[ $honeypot_key ] !== '' ) {
			return 'spam';
		}

		// validate size of POST array
		if ( count( $data ) > $form->get_field_count() && apply_filters( 'hf_validate_form_request_size', true ) ) {
			return 'spam';
		}

		$was_required    = (array) hf_array_get( $data, '_was_required', array() );
		$required_fields = $form->get_required_fields();
		foreach ( $required_fields as $field_name ) {
			$value = hf_array_get( $data, $field_name );
			if ( empty( $value ) && ! in_array( $field_name, $was_required ) ) {
				return 'required_field_missing';
			}
		}

		$email_fields = $form->get_email_fields();
		foreach ( $email_fields as $field_name ) {
			$value = hf_array_get( $data, $field_name );
			if ( ! empty( $value ) && ! is_email( $value ) ) {
				return 'invalid_email';
			}
		}

		$error_code = '';

		/**
		 * This filter allows you to perform your own form validation. The dynamic portion of the hook refers to the form slug.
		 *
		 * Return a non-empty string if you want to raise an error.
		 * Error codes with a specific error message are: "required_field_missing", "invalid_email", and "error"
		 *
		 * @param string $error_code
		 * @param Form $form
		 * @param array $data
		 */
		$error_code = apply_filters( 'hf_validate_form_' . $form->slug, $error_code, $form, $data );

		/**
		 * This filter allows you to perform your own form validation.
		 *
		 * Return a non-empty string if you want to raise an error.
		 * Error codes with a specific error message are: "required_field_missing", "invalid_email", and "error"
		 *
		 * @param string $error_code
		 * @param Form $form
		 * @param array $data
		 */
		$error_code = apply_filters( 'hf_validate_form', $error_code, $form, $data );
		if ( ! empty( $error_code ) ) {
			return $error_code;
		}

		// all good: no errors!
		return '';
	}

	/**
	* Sanitize array with values before saving. Can be called recursively.
	*
	* @param mixed $value
	* @return mixed
	*/
	public function sanitize( $value ) {
		if ( is_string( $value ) ) {
			// do nothing if empty string
			if ( $value === '' ) {
				return $value;
			}

			// strip slashes
			$value = stripslashes( $value );

			// strip all whitespace
			$value = trim( $value );

			// convert &amp; back to &
			$value = html_entity_decode( $value, ENT_NOQUOTES );
		} elseif ( is_array( $value ) || is_object( $value ) ) {
			$new_value = array();
			$vars      = is_array( $value ) ? $value : get_object_vars( $value );

			// do nothing if empty array or object
			if ( count( $vars ) === 0 ) {
				return $value;
			}

			foreach ( $vars as $key => $sub_value ) {
				// strip all whitespace & HTML from keys (!)
				$key = trim( strip_tags( $key ) );

				// sanitize sub value
				$new_value[ $key ] = $this->sanitize( $sub_value );
			}

			$value = is_object( $value ) ? (object) $new_value : $new_value;
		}

		return $value;
	}

	/**
	* @return array
	*/
	public function get_request_data() {
		$data = $_POST;

		if ( ! empty( $_FILES ) ) {
			foreach ( $_FILES as $field_name => $file ) {
				// only add non-empty files so that required field validation works as expected
				// upload could still have errored at this point
				if ( $file['error'] !== UPLOAD_ERR_NO_FILE ) {
					$data[ $field_name ] = $file;
				}
			}
		}

		return $data;
	}

	public function listen_for_submit() {
		// only respond to AJAX requests with _hf_form_id set.
		if ( empty( $_POST['_hf_form_id'] )
			|| empty( $_SERVER['HTTP_X_REQUESTED_WITH'] )
			|| strtolower( $_SERVER['HTTP_X_REQUESTED_WITH'] ) !== strtolower( 'XMLHttpRequest' ) ) {
			return;
		}

		$data       = $this->get_request_data();
		$form_id    = (int) $data['_hf_form_id'];
		$form       = hf_get_form( $form_id );
		$error_code = $this->validate_form( $form, $data );

		if ( empty( $error_code ) ) {
			/**
			* Filters the field names that should be ignored on the Submission object.
			* Fields starting with an underscore (_) are ignored by default.
			*
			* @param array $names
			*/
			$ignored_field_names = apply_filters( 'hf_ignored_field_names', array() );

			// filter out ignored field names
			foreach ( $data as $key => $value ) {
				if ( $key[0] === '_' || in_array( $key, $ignored_field_names ) ) {
					unset( $data[ $key ] );
					continue;
				}

				// this detects the WPBruiser token field to ensure it isn't stored
				// CAVEAT: this will detect any non-uppercase string with 2 dashes in the field name and no whitespace in the field value
				if ( class_exists( 'GoodByeCaptcha' ) && is_string( $key ) && is_string( $value ) && strtoupper( $key ) !== $key && substr_count( $key, '-' ) >= 2 && substr_count( trim( $value ), ' ' ) === 0 ) {
					unset( $data[ $key ] );
					continue;
				}
			}

			// sanitize data: strip tags etc.
			$data = $this->sanitize( $data );

			// save form submission
			$submission               = new Submission();
			$submission->form_id      = $form_id;
			$submission->data         = $data;
			$submission->ip_address   = ! empty( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( $_SERVER['REMOTE_ADDR'] ) : '';
			$submission->user_agent   = ! empty( $_SERVER['HTTP_USER_AGENT'] ) ? sanitize_text_field( $_SERVER['HTTP_USER_AGENT'] ) : '';
			$submission->referer_url  = ! empty( $_SERVER['HTTP_REFERER'] ) ? sanitize_text_field( $_SERVER['HTTP_REFERER'] ) : '';
			$submission->submitted_at = gmdate( 'Y-m-d H:i:s' );

			// save submission object so that other form processor have an insert ID to work with (eg file upload)
			if ( $form->settings['save_submissions'] ) {
				 $submission->save();
			}

			/**
			* General purpose hook that runs before all form actions, so we can still modify the submission object that is passed to actions.
			*/
			do_action( 'hf_process_form', $form, $submission );

			// re-save submission object for convenience in form processors hooked into hf_process_form
			if ( $form->settings['save_submissions'] ) {
				 $submission->save();
			}

			// process form actions
			if ( isset( $form->settings['actions'] ) ) {
				foreach ( $form->settings['actions'] as $action_settings ) {
					/**
					 * Processes the specified form action and passes related data.
					 *
					 * @param array $action_settings
					 * @param Submission $submission
					 * @param Form $form
					 */
					do_action( 'hf_process_form_action_' . $action_settings['type'], $action_settings, $submission, $form );
				}
			}

			/**
			 * General purpose hook after all form actions have been processed for this specific form. The dynamic portion of the hook refers to the form slug.
			 *
			 * @param Submission $submission
			 * @param Form $form
			 */
			do_action( "hf_form_{$form->slug}_success", $submission, $form );

			/**
			 * General purpose hook after all form actions have been processed.
			 *
			 * @param Submission $submission
			 * @param Form $form
			 */
			do_action( 'hf_form_success', $submission, $form );
		} else {
			/**
			 * General purpose hook for when a form error occurred
			 *
			 * @param string $error_code
			 * @param Form $form
			 * @param array $data
			 */
			do_action( 'hf_form_error', $error_code, $form, $data );
		}

		// Delay response until "wp_loaded" hook to give other plugins a chance to process stuff.
		add_action(
			'wp_loaded',
			function() use ( $error_code, $form, $data ) {
				$response = $this->get_response_for_error_code( $error_code, $form, $data );

				// clear output, some plugin or hooked code might have thrown errors by now.
				if ( ob_get_level() > 0 ) {
					ob_end_clean();
				}

				send_origin_headers();
				send_nosniff_header();
				nocache_headers();

				wp_send_json( $response, 200 );
				exit;
			}
		);
	}

	public function listen_for_preview() {
		if ( empty( $_GET['hf_preview_form'] ) || ! current_user_can( 'edit_forms' ) ) {
			return;
		}

		try {
			$form = hf_get_form( $_GET['hf_preview_form'] );
		} catch ( \Exception $e ) {
			return;
		}

		show_admin_bar( false );
		add_filter( 'pre_handle_404', '__return_true' );
		remove_all_actions( 'template_redirect' );
		add_action(
			'template_redirect',
			function() use ( $form ) {
				// clear output, some plugin or hooked code might have thrown errors by now.
				if ( ob_get_level() > 0 ) {
					ob_end_clean();
				}

				status_header( 200 );
				require dirname( $this->plugin_file ) . '/views/form-preview.php';
				exit;
			}
		);
	}

	private function get_response_for_error_code( $error_code, Form $form, $data = array() ) {
		// return success response for empty error code string or spam (to trick bots)
		if ( $error_code === '' || $error_code === 'spam' ) {
			$response = array(
				'message'   => array(
					'type' => 'success',
					'text' => $form->get_message( 'success' ),
				),
				'hide_form' => (bool) $form->settings['hide_after_success'],
			);

			if ( ! empty( $form->settings['redirect_url'] ) ) {
				$response['redirect_url'] = hf_replace_data_variables( $form->settings['redirect_url'], $data, 'urlencode' );
			}

			return apply_filters( 'hf_form_response', $response, $form, $data );
		}

		// get error message
		$message = $form->get_message( $error_code );
		if ( empty( $message ) ) {
			$message = $form->get_message( 'error' );
		}

		// return error response
		return array(
			'message' => array(
				'type' => 'warning',
				'text' => $message,
			),
			'error'   => $error_code,
		);
	}

	public function shortcode( $attributes = array(), $content = '' ) {
		if ( empty( $attributes['slug'] ) && empty( $attributes['id'] ) ) {
			return '';
		}

		$slug_or_id = empty( $attributes['id'] ) ? $attributes['slug'] : $attributes['id'];
		try {
			$form = hf_get_form( $slug_or_id );
		} catch ( \Exception $e ) {
			if ( ! current_user_can( 'manage_options' ) ) {
				return $content;
			}

			return sprintf( '<p><strong>%s</strong> %s</p>', __( 'Error:', 'html-forms' ), sprintf( __( 'No form found with slug %s', 'html-forms' ), $attributes['slug'] ) );
		}

		return $form . $content;
	}
}
