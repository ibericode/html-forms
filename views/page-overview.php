<?php

defined( 'ABSPATH' ) or exit;

/**
 * @var HTML_Forms\Admin\Table $table
 */
?>
<div class="wrap hf">

    <p class="breadcrumbs">
        <span class="prefix"><?php echo __( 'You are here: ', 'html-forms' ); ?></span>
        <a href="<?php echo admin_url( 'admin.php?page=html-forms' ); ?>">HTML Forms</a> &rsaquo;
        <span class="current-crumb"><strong><?php _e( 'Forms', 'html-forms' ); ?></strong></span>
    </p>

    <h1 class="page-title"><?php _e( 'Forms', 'html-forms' ); ?>
        <a href="<?php echo admin_url( 'admin.php?page=html-forms-add-form' ); ?>" class="page-title-action">
            <span class="dashicons dashicons-plus-alt" style="vertical-align: middle; line-height: 16px; margin: 0 4px 0 0; "></span>
            <?php _e( 'Add new form', 'html-forms' ); ?>
        </a>

        <?php if ( ! empty( $_GET['s'] ) ) {
            printf(' <span class="subtitle">' . __('Search results for &#8220;%s&#8221;') . '</span>', sanitize_text_field( $_GET['s'] ) );
        } ?>
    </h1>


    <?php $table->views(); ?>

    <form method="get" action="<?php echo admin_url( 'admin.php' ); ?>">
        <input type="hidden" name="page" value="<?php echo esc_attr( $_GET['page'] ); ?>" />
        <?php if( ! empty( $_GET['post_status'] ) ) { ?>
            <input type="hidden" name="post_status" value="<?php echo esc_attr( $_GET['post_status'] ); ?>" />
        <?php } ?>
        <?php $table->search_box( 'search', 'html-forms-search' ); ?>
    </form>

    <form method="post">
        <?php $table->display(); ?>
    </form>

    <?php require __DIR__ . '/admin-footer.php'; ?>
</div>
