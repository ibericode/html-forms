<?php

namespace HTML_Forms\Migrations;

use HTML_Forms\Actions\SaveSubmission;
use HTML_Forms\Forms;

defined( 'ABSPATH' ) or exit;

$settings = hf_get_settings();
if ( ! empty( $settings['save_submissions'] ) ) {

	$forms = new \WP_Query( array(
		'post_type' => 'html-form',
		'numberposts' => -1,
		'update_post_post_cache' => false,
		'update_post_term_cache' => false,
		'no_found_rows' => true,

	) );

	// Update each form to add the 'Save' action
	foreach ( $forms->get_posts() as $post ) {
		$form = hf_get_form( $post->ID );

		$settings = $form->settings;

		if ( ! isset( $form->settings['actions'] ) ) {
			$form->settings = array();
		}

		$form->settings['actions'][] = array(
			'type' => 'save',
		);

		update_post_meta( $post->ID, '_hf_settings', $form->settings );
	}

	// Remove from settings
	unset( $settings['save_submissions'] );
	update_option( 'hf_settings', $settings );
}