<?php

namespace HTML_Forms;

class Submission {
    public $id;
    public $form_id;
    public $data = array();
    public $ip_address = '';
    public $user_agent = '';
    public $referer_url = '';
    public $submitted_at;

    public function save() {
        global $wpdb;
        $table = $wpdb->prefix .'hf_submissions';

        $data = array(
            'data' => json_encode( $this->data ),
            'form_id' => $this->form_id,
        );

        foreach( array( 'ip_address', 'user_agent', 'submitted_at', 'referer_url' ) as $prop ) {
            $data[ $prop ] = $this->$prop;
        }

        if( ! empty( $this->id ) ) {
            $wpdb->update( $table, $data, array( 'id' => $this->id ) );
            return;
        }

        // insert new row
        $num_rows = $wpdb->insert( $table, $data );
        if( $num_rows > 0 ) {
            $this->id = $wpdb->insert_id;
        }
    }

    /**
     * @param $object
     * @return Submission
     */
    public static function from_object( $object ) {
        $data = empty( $object->data ) ? array() : (array) json_decode( $object->data, true );

        $submission = new Submission();
        $submission->id = (int) $object->id;
        $submission->form_id = (int) $object->form_id;
        $submission->data = $data;
        $submission->ip_address = (string) $object->ip_address;
        $submission->user_agent = (string) $object->user_agent;
        $submission->referer_url = (string) $object->referer_url;
        $submission->submitted_at = $object->submitted_at;
        return $submission;
    }

}
