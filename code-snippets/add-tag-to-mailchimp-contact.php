<?php
// Note: requires MC4WP plugin to be active and configured, see https://wordpress.org/plugins/mailchimp-for-wp/

// Give users a link to a form, and include "?hfmuc_email={{their_email}}" as a querystring parameter.
// Then make sure the form has a hidden field named "EMAIL" whose value is "{{hfmuc_email}}",
// eg `<input type="hidden" name="EMAIL" placeholder="Your email" required value="{{hfmuc_email}}" />`
// Find your list ID (see https://mailchimp.com/help/find-audience-id/) and put it in $list_id below
// Find the HTML Forms slug (seen when editing the form) and put it in $form_slug
// Add any tags you want to $tags_data

// Now the user will get a link to the form, fill it out (no need to re-enter their email)
// and upon successful submission, those tags will be added to their MailChimp contact.

use \HTML_Forms\Submission;
use HTML_Forms\Form;

// Get the user's email from the query parameters
add_filter(
	'hf_form_html',
	function ( $html ) {
		$email = isset( $_GET['hfmuc_email'] ) ? $_GET['hfmuc_email'] : '';
		return str_replace( '{{hfmuc_email}}', $email, $html );
	}
);
// Adds tags to the given user.
add_action(
	'hf_form_success',
	function ( $submission, $form ) {
		// Check that it's the right form.
		$form_slug = 'survey-1';
		if ( $form->slug !== $form_slug ) {
			return;
		}

		$email_address = $submission->data['EMAIL'];
		$api           = mc4wp_get_api_v3();

		// aka "audience ID". See https://mailchimp.com/help/find-audience-id/
		$list_id = '32ccd044c3';

		// see https://mailchimp.com/developer/reference/lists/list-members/list-member-tags/#read-get_lists_list_id_members_subscriber_hash_tags
		$tags_data = array(
			'tags' => array(
				array(
					'name'   => 'Test',
					'status' => 'active',
				),
			),
		);

		$result = $api->update_list_member_tags(
			$list_id,
			$email_address,
			$tags_data
		);
	},
	10,
	2
);
