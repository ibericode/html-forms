<?php
$available_actions = $this->get_available_form_actions();
?>

<div>
    <h2><?php echo __( 'Form Actions', 'html-forms' ); ?></h2>

    <div id="hf-form-actions">
        <?php
        if( ! empty( $form->settings['actions'] ) ) {
            $index = 0;
            foreach ($form->settings['actions'] as $action_settings ) {
                // skip invalid options (eg from deleted actions)
                if( empty( $available_actions[ $action_settings['type'] ] ) ) {
                    continue;
                }

                ?>
                   <div class="hf-action-settings" data-title="<?php echo esc_attr( $available_actions[ $action_settings['type'] ] ); ?>">
                        <?php

                        /**
                        * Output setting fields for a registered action
                        *
                        * @param array $action_settings
                        * @param int $index
                        */
                        do_action( 'hf_output_form_action_' . $action_settings['type'] . '_settings', $action_settings, $index++ ); 

                        /**
                        * Deprecated action hook. Use the above action (hf_output_form_action_...) instead.
                        */
                        do_action( 'hf_render_form_action_' . $action_settings['type'] . '_settings', $action_settings, $index++ ); ?>
                   </div>
                <?php
            }
        }

        echo '<p id="hf-form-actions-empty">' . __( 'No form actions configured for this form.', 'html-forms' ) . '</p>';
        ?>
    </div>
</div>

<div class="hf-medium-margin">
    <h3><?php echo __( 'Add form action', 'html-forms' ); ?></h3>
    <p><?php _e( 'Use the below button(s) to configure and perform an action whenever this form is successfully submitted.', 'html-forms' ); ?></p>
    <p id="hf-available-form-actions">
        <?php
        foreach( $available_actions as $type => $label ) {
            echo sprintf( '<input type="button" value="%s" data-action-type="%s" class="button" />', esc_html( $label ), esc_attr( $type ) ) . ' &nbsp;';
        };
        ?>
    </p>
</div>

<div>
    <?php submit_button(); ?>
</div>

<div style="display: none;" id="hf-form-action-templates">
    <?php
    foreach( $available_actions as $type => $label ) {
        echo sprintf( '<script type="text/x-template" id="hf-action-type-%s-template">', $type );
        do_action( 'hf_output_form_action_' . $type . '_settings', array(), '$index' );
        echo '</script>';
    }
    ?>
</div>
