<?php

$settings = get_option( 'hf_settings', array() );

if( isset( $settings['save_submissions'] ) && ! $settings['save_submissions'] ) {
	$forms = get_posts(
        array(
            'post_type' => 'html-form',
            'post_status' => 'publish',
            'numberposts' => -1,
        )
    );

    foreach( $forms as $form ) {
    	$form_settings = (array) get_post_meta( $form->ID, '_hf_settings', true );
    	$form_settings['save_submissions'] = "0";
    	update_post_meta( $form->ID, '_hf_settings', $form_settings );
    }
}
