<?php

namespace HTML_Forms;

class Forms
{

    private $plugin_file;

    public function __construct($plugin_file)
    {
        $this->plugin_file = $plugin_file;
    }

    public function hook()
    {
        add_action('init', array($this, 'register'));
        add_action('init', array($this, 'listen'));
        add_action('wp_enqueue_scripts', array($this, 'assets'));
    }

    public function register()
    {
        // register post type
        register_post_type('html-form', array(
                'labels' => array(
                    'name' => 'HTML Forms',
                    'singular_name' => 'HTML Form',
                ),
                'public' => false
            )
        );

        add_shortcode('html_form', array($this, 'shortcode'));
    }

    public function assets()
    {
        $suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '.min' : '';
        wp_enqueue_script('html-forms', plugins_url('assets/js/public'. $suffix .'.js', $this->plugin_file), array(), HTML_FORMS_VERSION, true);
        wp_localize_script('html-forms', 'hf_js_vars', array(
            'ajax_url' => admin_url('admin-ajax.php'),
        ));
    }

    /**
     * Access a nested array's value by dot notation
     *
     * @param array $array
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    private function array_get( $array, $key, $default = null ) {
        if ( is_null( $key ) ) {
            return $array;
        }

        if ( isset( $array[$key] ) ) {
            return $array[$key];
        }

        $segments = explode( '.', $key );
        foreach ( $segments as $segment) {
            if ( ! is_array( $array ) || ! array_key_exists( $segment, $array ) ) {
                return $default;
            }
            $array = $array[$segment];
        }

        return $array;
    }

    private function validate_form(Form $form, $data) {
        $required_fields = $form->get_required_fields();
        foreach ($required_fields as $field_name) {
            $value = $this->array_get( $data, $field_name );
            if ( empty( $value ) ) {
                return 'required_field_missing';
            }
        }

        $email_fields = $form->get_email_fields();
        foreach ($email_fields as $field_name) {
            $value = $this->array_get( $data, $field_name );
            if ( ! empty( $value ) && ! is_email( $value ) ) {
                return 'invalid_email';
            }
        }

        return 'success';
    }

    public function sanitize( $value ) {
        if (is_string($value)) {
            // strip all HTML tags & whitespace
            $value = trim(strip_tags($value));

            // convert &amp; back to &
            $value = html_entity_decode($value, ENT_NOQUOTES);
        } elseif (is_array($value)) {
            $value = array_map(array( $this, 'sanitize' ), $value);
        } elseif (is_object($value)) {
            $vars = get_object_vars($value);
            foreach ($vars as $key => $data) {
                $value->{$key} = $this->sanitize($data);
            }
        }

        return $value;
    }

    public function listen() {
        if (empty($_POST['_hf_form_id'])) {
            return;
        }

        $form_id = (int)$_POST['_hf_form_id'];
        $form = hf_get_form($form_id);
        $case = $this->validate_form($form, $_POST);

        if ($case === 'success') {
            // filter out all field names starting with _
            $data = array_filter( $_POST, function( $k ) {
                return ! empty( $k ) && $k[0] !== '_';
            }, ARRAY_FILTER_USE_KEY );

            // strip slashes
            $data = stripslashes_deep( $data );

            // sanitize data: strip tags etc.
            $data = $this->sanitize( $data );

            $submission = new Submission();
            $submission->form_id = $form_id;
            $submission->data = $data;
            $submission->ip_address = sanitize_text_field( $_SERVER['REMOTE_ADDR'] );
            $submission->user_agent = sanitize_text_field( $_SERVER['HTTP_USER_AGENT'] );
            $submission->save();

            // TODO: Process form actions

            $response = array(
                'message' => array(
                    'type' => 'success',
                    'text' => $form->messages['success'],
                ),
                'hide_form' => (bool)$form->settings['hide_after_success'],
            );

            if (!empty($form->settings['redirect_url'])) {
                $response['redirect_url'] = $form->settings['redirect_url'];
            }
        } else {
            $response = array(
                'message' => array(
                    'type' => 'warning',
                    'text' => $form->messages[$case],
                )
            );
        }

        send_origin_headers();
        send_nosniff_header();
        nocache_headers();

        wp_send_json($response, 200);
        exit;
    }

    public function shortcode($attributes = array(), $content = '')
    {
        $form = hf_get_form($attributes['slug']);
        return $form . $content;
    }
}