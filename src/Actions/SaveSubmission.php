<?php

namespace HTML_Forms\Actions;

use HTML_Forms\Form;
use HTML_Forms\Submission;

class SaveSubmission extends Action {

   public $type = 'save';
   public $label = 'Save submission';

   public function __construct() {
       $this->label = __( 'Save Submission', 'html-forms' );
   }

   /**
   * @return array
   */
   private function get_default_settings() {
       $defaults = array();
       return $defaults;
   }

   /**
   * @param array $settings
   * @param string|int $index
   */
   public function page_settings( $settings, $index ) {
       $settings = array_merge( $this->get_default_settings(), $settings );

       // None (yet)
   }

    /**
     * Processes this action
     *
     * @param array $settings
     * @param Submission $submission
     * @param Form $form
     */
    public function process( array $settings, Submission $submission, Form $form ) {
       $submission->save();
    }
}
