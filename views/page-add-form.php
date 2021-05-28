<?php  defined( 'ABSPATH' ) or exit; ?>

<div class="wrap hf">

    <style type="text/css" scoped>
        label{ display: block; font-weight: bold; font-size: 18px; }
    </style>

    <p class="breadcrumbs">
        <span class="prefix"><?php echo __( 'You are here: ', 'html-forms' ); ?></span>
        <a href="<?php echo admin_url( 'admin.php?page=html-forms' ); ?>">HTML Forms</a> &rsaquo;
        <span class="current-crumb"><strong><?php _e( 'Add new form', 'html-forms' ); ?></strong></span>
    </p>

    <h1 class="page-title"><?php _e( 'Add new form', 'html-forms' ); ?></h1>

    <form method="post" style="max-width: 600px;">
        <input type="hidden" name="_hf_admin_action" value="create_form" />
		<input type="hidden" name="_wpnonce" value="<?php echo esc_attr( wp_create_nonce('_hf_admin_action') ); ?>" />

        <p>
            <label>Form title</label>
            <input type="text" name="form[title]" value="" placeholder="<?php esc_attr_e( 'Your form title..', 'html-forms' ); ?>" class="widefat" required />
        </p>

        <?php submit_button( __( 'Create form', 'html-forms' ) ); ?>
    </form>

    <?php require __DIR__ . '/admin-footer.php'; ?>
</div>

