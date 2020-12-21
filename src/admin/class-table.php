<?php

namespace HTML_Forms\Admin;

use HTML_Forms\Form;
use WP_List_Table, WP_Post;

// Check if WP Core class exists so that we can keep testing rest of HTML Forms in isolation..
if ( class_exists( 'WP_List_Table' ) ) {

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
					'ajax'     => false,
				)
			);

			$this->settings = $settings;
			$this->process_bulk_action();
			$this->prepare_items();
		}

		public function prepare_items() {
			$columns               = $this->get_columns();
			$sortable              = $this->get_sortable_columns();
			$hidden                = array();
			$this->_column_headers = array( $columns, $hidden, $sortable );
			$this->is_trash        = isset( $_REQUEST['post_status'] ) && $_REQUEST['post_status'] === 'trash';
			$this->items           = $this->get_items();
			$this->set_pagination_args(
				array(
					'per_page'    => 50,
					'total_items' => count( $this->items ),
				)
			);
		}

		/**
		 * Get an associative array ( id => link ) with the list
		 * of views available on this table.
		 *
		 * @return array
		 * @since 3.1.0
		 * @access protected
		 *
		 */
		public function get_views() {
			$counts    = wp_count_posts( 'html-form' );
			$current   = isset( $_GET['post_status'] ) ? $_GET['post_status'] : '';
			$count_any = $counts->publish + $counts->draft + $counts->future + $counts->pending;

			return array(
				''      => sprintf( '<a href="%s" class="%s">%s</a> (%d)', remove_query_arg( 'post_status' ), $current == '' ? 'current' : '', __( 'All', 'html-forms' ), $count_any ),
				'trash' => sprintf( '<a href="%s" class="%s">%s</a> (%d)', add_query_arg( array( 'post_status' => 'trash' ) ), $current == 'trash' ? 'current' : '', __( 'Trash', 'html-forms' ), $counts->trash ),
			);
		}

		/**
		 * @return array
		 */
		public function get_bulk_actions() {

			$actions = array();

			if ( $this->is_trash ) {
				$actions['untrash'] = __( 'Restore', 'html-forms' );
				$actions['delete']  = __( 'Delete Permanently', 'html-forms' );

				return $actions;
			}

			$actions['trash']     = __( 'Move to Trash', 'html-forms' );
			$actions['duplicate'] = __( 'Duplicate', 'html-forms' );

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
				'cb'        => '<input type="checkbox" />',
				'form_name' => __( 'Form', 'html-forms' ),
				'shortcode' => __( 'Shortcode', 'html-forms' ),
			);
		}

		/**
		 * @return array
		 */
		public function get_hidden_columns() {
			return array();
		}

		/**
		 * @return array
		 */
		public function get_sortable_columns() {
			return array(
				'form_name' => array( 'post_title', true ),
			);
		}

		/**
		 * @return array
		 */
		public function get_items() {
			$args = array();

			if ( ! empty( $_GET['s'] ) ) {
				$args['s'] = sanitize_text_field( $_GET['s'] );
			}

			if ( ! empty( $_GET['post_status'] ) ) {
				$args['post_status'] = sanitize_text_field( $_GET['post_status'] );
			}

			if ( ! empty( $_GET['order'] ) ) {
				$args['order'] = $_GET['order'];
			}

			if ( ! empty( $_GET['orderby'] ) ) {
				$args['orderby'] = $_GET['orderby'];
			}

			return hf_get_forms( $args );
		}

		/**
		 * @param Form $form
		 *
		 * @return string
		 */
		public function column_cb( $form ) {
			return sprintf( '<input type="checkbox" name="forms[]" value="%s" />', $form->ID );
		}

		/**
		 * @param Form $form
		 *
		 * @return mixed
		 */
		public function column_ID( Form $form ) {
			return $form->ID;
		}

		/**
		 * @param Form $form
		 *
		 * @return string
		 */
		public function column_form_name( Form $form ) {
			if ( $this->is_trash ) {
				return sprintf( '<strong>%s</strong>', esc_html( $form->title ) );
			}

			$edit_link = admin_url( 'admin.php?page=html-forms&view=edit&form_id=' . $form->ID );
			$title     = '<strong><a class="row-title" href="' . $edit_link . '">' . esc_html( $form->title ) . '</a></strong>';

			$actions = array();
			$tabs    = hf_get_admin_tabs( $form );

			foreach ( $tabs as $tab_slug => $tab_title ) {
				$actions[ $tab_slug ] = '<a href="' . esc_attr( add_query_arg( array( 'tab' => $tab_slug ), $edit_link ) ) . '">' . $tab_title . '</a>';
			}

			return $title . $this->row_actions( $actions );
		}

		/**
		 * @param Form $form
		 *
		 * @return string
		 */
		public function column_shortcode( Form $form ) {
			if ( $this->is_trash ) {
				return '';
			}

			return sprintf( '<input style="width: 260px;" type="text" onfocus="this.select();" readonly="readonly" value="%s">', esc_attr( '[hf_form slug="' . $form->slug . '"]' ) );
		}

		/**
		 * The text that is shown when there are no items to show
		 */
		public function no_items() {
			echo sprintf( __( 'No forms found. <a href="%s">Would you like to create one now</a>?', 'html-forms' ), admin_url( 'admin.php?page=html-forms-add-form' ) );
		}

		/**
		 *
		 */
		public function process_bulk_action() {
			$action = $this->current_action();
			if ( empty( $action ) ) {
				return false;
			}

			$method = 'process_bulk_action_' . $action;
			$forms  = (array) $_REQUEST['forms'];
			if ( method_exists( $this, $method ) ) {
				return call_user_func_array( array( $this, $method ), array( $forms ) );
			}

			return false;
		}

		public function process_bulk_action_duplicate( $forms ) {
			foreach ( $forms as $form_id ) {
				$post      = get_post( $form_id );
				$post_meta = get_post_meta( $form_id );

				$new_post_id = wp_insert_post(
					array(
						'post_title'   => $post->post_title,
						'post_content' => $post->post_content,
						'post_type'    => 'html-form',
						'post_status'  => 'publish',
					)
				);
				foreach ( $post_meta as $meta_key => $meta_value ) {
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
		 * @param Form $form The current item
		 *
		 * @since 3.1.0
		 *
		 */
		public function single_row( $form ) {
			echo sprintf( '<tr id="hf-forms-item-%d">', $form->ID );
			$this->single_row_columns( $form );
			echo '</tr>';
		}

	}

}
