<?php 

namespace HTML_Forms\Admin;

use HTML_Forms\Submission;

class GDPR {
   public function hook() {
      add_filter( 'wp_privacy_personal_data_exporters', array( $this, 'register_exporter' ), 90 );
      add_filter( 'wp_privacy_personal_data_erasers', array( $this, 'register_eraser' ), 90 );
   }

    public function register_exporter( $exporters ) {
      $exporters['html-forms'] = array(
         'exporter_friendly_name' => 'HTML Forms',
         'callback'               => array( $this, 'export' ),
      );
      return $exporters;
    }

     public function register_eraser( $erasers ) {
      $erasers['html-forms'] = array(
         'eraser_friendly_name' => 'HTML Forms',
         'callback'             => array( $this, 'erase' ),
      );
      return $erasers;
    }

    public function export( $email_address, $page = 1 ) {
      $submissions = $this->find_submissions_for_email_address( $email_address );
      $data_to_export = array();

      foreach( $submissions as $s ) {
         $data_to_export[] = array(
            'group_id'    => 'html_forms_submissions',
            'group_label' => __( 'Form submissions', 'html-forms' ),
            'item_id'     => sprintf( 'html-forms-submission-%d', $s->id ),
            'data'        => $this->export_submission( $s ),
         );
      }
      
      return array(
         'data' => $data_to_export,
         'done' => true,
      );
    }

    public function export_submission( Submission $submission ) {
      $data = array(
         array( 
            'name' => 'Submitted at',
            'value' => $submission->submitted_at,
         )
      );

      if( ! empty( $submission->ip_address ) ) {
         $data[] = array(
            'name' => 'IP address',
            'value' => $submission->ip_address,
         );
      }

      if( ! empty( $submission->user_agent ) ) {
         $data[] = array(
            'name' => 'User agent',
            'value' => $submission->user_agent,
         );
      }

      foreach( $submission->data as $field => $value ) {
         $data[] = array(
            'name' => $field,
            'value' => is_array( $value ) ? join( ', ', $value ) : $value,
         );
      }

      return $data;
    }

    public function erase( $email_address, $page = 1 ) {
      global $wpdb;
      $table = $wpdb->prefix .'hf_submissions';

      $items_removed = false;
      $submissions = $this->find_submissions_for_email_address( $email_address );
      foreach( $submissions as $submission ) {
         $wpdb->delete( $table, array( 'id' => $submission->id ) );
         $items_removed = true;
      }

      return array(
            'items_removed'  => $items_removed,
            'items_retained' => false,
            'messages'       => array(),
            'done'           => true,
         );
    }

    private function find_submissions_for_email_address( $email_address ) {
      global $wpdb;
      $table = $wpdb->prefix .'hf_submissions';
      $like = '%"' . $email_address . '"%';
      $results = $wpdb->get_results( $wpdb->prepare( "SELECT s.* FROM {$table} s WHERE s.data LIKE %s ORDER BY s.submitted_at DESC", $like ), OBJECT_K );

      $submissions = array();
      foreach( $results as $key => $object ) {
         $submission = Submission::from_object( $object );
         $submissions[$key] = $submission;
      }

      return $submissions;
    }
}
