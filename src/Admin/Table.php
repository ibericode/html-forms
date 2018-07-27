<?php

namespace HTML_Forms\Admin;
use WP_List_Table, WP_Post;

// Check if WP Core class exists so that we can keep testing rest of HTML Forms in isolation..
if( class_exists( 'WP_List_Table' ) ) {

    class Table extends WP_List_Table {

        /**
         * @var bool
         */
        public $is_trash = false;

        /**
        * @var array
        */
        private $settings = array();

        /**
         * Constructor
         */
        public function __construct( array $settings ) {
            parent::__construct(
                array(
                    'singular' => 'form',
                    'plural'   => 'forms',
                    'ajax'     => false
                )
            );

            $this->settings = $settings;

            $columns  = $this->get_columns();
            $sortable = $this->get_sortable_columns();
            $hidden   = array();
            $this->_column_headers = array( $columns, $hidden, $sortable );

            $this->is_trash = isset( $_REQUEST['post_status'] ) && $_REQUEST['post_status'] === 'trash';
            $this->process_bulk_action();
            $this->prepare_items();
            $this->set_pagination_args(
                array(
                    'per_page' => 50,
                    'total_items' => count( $this->items )
                )
            );
        }


        public function prepare_items() {
            $this->items = $this->get_items();
        }
        /**
         * Get an associative array ( id => link ) with the list
         * of views available on this table.
         *
         * @since 3.1.0
         * @access protected
         *
         * @return array
         */
        public function get_views() {
            $counts = wp_count_posts( 'html-form' );
            $current = isset( $_GET['post_status'] ) ? $_GET['post_status'] : '';
            $count_any = $counts->publish + $counts->draft + $counts->future + $counts->pending;
            
            return array(
                '' => sprintf( '<a href="%s" class="%s">%s</a> (%d)', remove_query_arg( 'post_status' ), $current == '' ? 'current' : '', __( 'All' ), $count_any ),
                'trash' => sprintf( '<a href="%s" class="%s">%s</a> (%d)', add_query_arg( array( 'post_status' => 'trash' ) ), $current == 'trash' ? 'current' : '', __( 'Trash' ), $counts->trash ),
            );
        }

        /**
         * @return array
         */
        public function get_bulk_actions() {

            $actions = array();

            if( $this->is_trash ) {
                $actions['untrash'] = __( 'Restore' );
                $actions['delete'] = __( 'Delete Permanently' );
                return $actions;
            }

            $actions['trash'] = __( 'Move to Trash' );
            $actions['duplicate'] = __( 'Duplicate' );
            return $actions;
        }

        public function get_default_primary_column_name() {
            return 'form_name';
        }

        /**
         * @return array
         */
        public function get_table_classes() {
            return array( 'widefat', 'fixed', 'striped', 'html-forms-table' );
        }

        /**
         * @return array
         */
        public function get_columns() {
            return array(
                'cb'       => '<input type="checkbox" />',
                'form_name'    => __( 'Form', 'html-forms' ),
                'shortcode'     => __( 'Shortcode', 'html-forms' ),
            );
        }

        /**
         * @return array
         */
        public function get_sortable_columns() {
            return array();
        }

        /**
         * @return array
         */
        public function get_items() {
            $args = array(
                'post_type' => 'html-form',
                'post_status' =>  array( 'publish', 'draft', 'pending', 'future' ),
                'numberposts' => -1,
            );

            if( ! empty( $_GET['s'] ) ) {
                $args['s'] = sanitize_text_field( $_GET['s'] );
            }

            if( ! empty( $_GET['post_status' ] ) ) {
                $args['post_status'] = sanitize_text_field( $_GET['post_status'] );
            }


            $items = get_posts( $args );

            return $items;
        }

        /**
         * @param $item
         *
         * @return string
         */
        public function column_cb( $item ) {
            return sprintf( '<input type="checkbox" name="forms[]" value="%s" />', $item->ID );
        }

        /**
         * @param WP_Post $post
         *
         * @return mixed
         */
        public function column_ID( WP_Post $post ) {
            return $post->ID;
        }

        /**
         * @param WP_Post $post
         * @return string
         */
        public function column_form_name( WP_Post $post ) {
            if( $this->is_trash ) {
                return sprintf( '<strong>%s</strong>', esc_html( $post->post_title ) );
            }

            $form = hf_get_form( $post );

            $edit_link = admin_url( 'admin.php?page=html-forms&view=edit&form_id=' . $post->ID );
            $title      = '<strong><a class="row-title" href="' . $edit_link . '">' . esc_html( $post->post_title ) . '</a></strong>';

            $actions = array();
            $tabs = array(
                'fields'        => __( 'Fields', 'html-forms' ),
                'messages'      => __( 'Messages', 'html-forms' ),
                'settings'      => __( 'Settings', 'html-forms' ),
                'actions'       => __( 'Actions', 'html-forms' ),
            );

            if( $form->settings['save_submissions'] ) {
                $tabs['submissions'] = __( 'Submissions', 'html-forms' );
            }

            foreach( $tabs as $tab_slug => $tab_title ) {
                $actions[$tab_slug] = '<a href="'. esc_attr( add_query_arg( array( 'tab' => $tab_slug ), $edit_link ) ) .'">'. $tab_title . '</a>';
            }

            return $title . $this->row_actions( $actions );
        }

        /**
         * @param WP_Post $post
         *
         * @return string
         */
        public function column_shortcode( WP_Post $post ) {
            if( $this->is_trash ) {
                return '';
            }

            return sprintf( '<input style="width: 260px;" type="text" onfocus="this.select();" readonly="readonly" value="%s">', esc_attr( '[hf_form slug="' . $post->post_name . '"]' ) );
        }

        /**
         * The text that is shown when there are no items to show
         */
        public function no_items() {
            echo sprintf( __( 'No forms found. <a href="%s">Would you like to create one now</a>?', 'html-forms' ), admin_url( 'admin.php?page=html-forms-add-form') );
        }

        /**
         *
         */
        public function process_bulk_action() {
            $action = $this->current_action();
            if( empty( $action ) ) {
                return false;
            }

            $method = 'process_bulk_action_' . $action;
            $forms = (array) $_REQUEST['forms'];
            if( method_exists( $this, $method ) ) {
                return call_user_func_array( array( $this, $method ), array( $forms ) );
            }

            return false;
        }

        public function process_bulk_action_duplicate( $forms ) {
            foreach( $forms as $form_id ) {
                $post = get_post( $form_id );
                $post_meta = get_post_meta( $form_id );

                $new_post_id = wp_insert_post(
                    array(
                        'post_title' => $post->post_title,
                        'post_content' => $post->post_content,
                        'post_type' => 'html-form',
                        'post_status' => 'publish'
                    )
                );
                foreach( $post_meta as $meta_key => $meta_value ) {
                    $meta_value = maybe_unserialize( $meta_value[0] );
                    update_post_meta( $new_post_id, $meta_key, $meta_value );
                }
            }
        }

        public function process_bulk_action_trash( $forms ) {
            return array_map( 'wp_trash_post', $forms );
        }

        public function process_bulk_action_delete( $forms ) {
            return array_map( 'wp_delete_post', $forms );
        }

        public function process_bulk_action_untrash( $forms ) {
            return array_map( 'wp_untrash_post', $forms );
        }

        /**
         * Generates content for a single row of the table
         *
         * @since 3.1.0
         *
         * @param object $item The current item
         */
        public function single_row( $item ) {
            echo sprintf( '<tr id="hf-forms-item-%d">',$item->ID );
            $this->single_row_columns( $item );
            echo '</tr>';
        }

    }

}
