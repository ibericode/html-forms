<?php

namespace HTML_Forms\Actions;

use HTML_Forms\Form;
use HTML_Forms\Submission;

abstract class Action {

    public $type = '';
    public $label = '';

    public function hook() {
        add_filter( 'hf_available_form_actions', array( $this, 'register' ) );
        add_action( 'hf_output_form_action_' . $this->type . '_settings', array( $this, 'page_settings' ), 10, 2 );
        add_action( 'hf_process_form_action_' . $this->type, array( $this, 'process' ), 10, 3 );
    }

    /**
     * Renders the settings for this action.
     *
     * @param array $settings
     * @param string $index
     */
    abstract function page_settings( $settings, $index );

    abstract function process( array $settings, Submission $submission, Form $form );

    /**
     * @param array $actions
     * @return array
     */
    public function register( array $actions ) {
        $actions[$this->type] = $this->label;
        return $actions;
    }
}
