<div class="hf-small-margin">
    <div id="hf-field-builder"></div>
</div>

<div class="hf-small-margin">

	<div class="hf-row">
		<div class="hf-col" style="min-width: 600px;">
		    <h4 style="margin-bottom: 0;"><label for="hf-form-editor"><?php _e( 'Form code', 'html-forms' ); ?> <span class="dashicons dashicons-editor-help" style="visibility: hidden;"></span></label></h4>
		    <textarea id="hf-form-editor" class="widefat" name="form[markup]" cols="160" rows="20" autocomplete="false" autocorrect="false" autocapitalize="false" spellcheck="false"><?php echo htmlspecialchars( $form->markup, ENT_QUOTES, get_option( 'blog_charset' ) ); ?></textarea>
		    <?php submit_button(); ?>
		</div>
		<div class="hf-col" style="min-width: 400px;">
			<h4 style="margin-bottom: 0;"><label><?php _e( 'Form preview', 'html-forms' ); ?> <span class="dashicons dashicons-editor-help hf-tooltip" title="<?php esc_attr_e( 'The form may look slightly different than this when shown in a post, page or widget area.', 'html-forms' ); ?>"></span></label></h4>
			<iframe id="hf-form-preview" src="<?php echo esc_attr( $form_preview_url ); ?>"></iframe>
		</div>
	</div>

</div>

<input type="hidden" id="hf-required-fields" name="form[settings][required_fields]" value="<?php echo esc_attr( $form->settings['required_fields'] ); ?>" />
<input type="hidden" id="hf-email-fields" name="form[settings][email_fields]" value="<?php echo esc_attr( $form->settings['email_fields'] ); ?>" />
