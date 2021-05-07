<h2><?php echo __( 'Form Settings', 'html-forms' ); ?></h2>

<table class="form-table">

    <tr valign="top">
        <th scope="row"><?php _e( 'Save form submissions?', 'html-forms' ); ?></th>
        <td>
            <label><input type="radio" name="form[settings][save_submissions]" value="1" <?php checked( $form->settings['save_submissions'], 1 ); ?>> <?php _e( 'Yes' ); ?></label> &nbsp;
            <label><input type="radio"  name="form[settings][save_submissions]" value="0"  <?php checked( $form->settings['save_submissions'], 0 ); ?>> <?php _e( 'No' ); ?></label>

            <p class="description"><?php _e( 'Select "yes" to store successful form submissions.', 'html-forms' ); ?></p>
        </td>
    </tr>

    <tr valign="top">
        <th scope="row"><?php _e( 'Hide form after a successful sign-up?', 'html-forms' ); ?></th>
        <td class="nowrap">
            <label>
                <input type="radio" name="form[settings][hide_after_success]" value="1" <?php checked( $form->settings['hide_after_success'], 1 ); ?> />&rlm;
                <?php _e( 'Yes' ); ?>
            </label> &nbsp;
            <label>
                <input type="radio" name="form[settings][hide_after_success]" value="0" <?php checked( $form->settings['hide_after_success'], 0 ); ?> />&rlm;
                <?php _e( 'No' ); ?>
            </label>
            <p class="description">
                <?php _e( 'Select "yes" to hide the form fields after a successful sign-up.', 'html-forms' ); ?>
            </p>
        </td>
    </tr>

    <tr valign="top">
        <th scope="row"><label for="hf_form_redirect"><?php _e( 'Redirect to URL after successful sign-ups', 'html-forms' ); ?></label></th>
        <td>
            <input type="text" class="widefat" name="form[settings][redirect_url]" id="hf_form_redirect" placeholder="<?php printf( __( 'Example: %s', 'html-forms' ), esc_attr( site_url( '/thank-you/' ) ) ); ?>" value="<?php echo esc_attr( $form->settings['redirect_url'] ); ?>" />
            <p class="description">
                <?php _e( 'Leave empty or enter <code>0</code> for no redirect. Otherwise, use complete (absolute) URLs, including <code>http://</code>.', 'html-forms' ); ?>
            </p>
            <p class="description">
                <?php _e( 'You can use the following variables in the redirect URL: ', 'html-forms' ); ?><span class="hf-field-names"></span>
            </p>
        </td>
    </tr>

    <?php
    /**
    * Runs after the form settings are printed.
    *
    * @param $form
    */
    do_action( 'hf_output_form_settings', $form ); ?>

</table>

<?php submit_button(); ?>
