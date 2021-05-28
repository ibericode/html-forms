<?php

namespace HTML_Forms\Admin;

use HTML_Forms\Form;
use HTML_Forms\Submission;

class Admin {

	/**
	 * @var string
	 */
	private $plugin_file;

	/**
	 * Admin constructor.
	 *
	 * @param string $plugin_file
	 */
	public function __construct( $plugin_file ) {
		$this->plugin_file = $plugin_file;
	}

	public function hook() {
		add_action( 'admin_menu', array( $this, 'menu' ) );
		add_action( 'init', array( $this, 'register_settings' ) );
		add_action( 'admin_init', array( $this, 'run_migrations' ) );
		add_action( 'admin_init', array( $this, 'listen' ) );
		add_action( 'admin_print_styles', array( $this, 'assets' ) );
		add_action( 'admin_head', array( $this, 'add_screen_options' ) );
		add_action( 'hf_admin_action_create_form', array( $this, 'process_create_form' ) );
		add_action( 'hf_admin_action_save_form', array( $this, 'process_save_form' ) );
		add_action( 'hf_admin_action_bulk_delete_submissions', array( $this, 'process_bulk_delete_submissions' ) );

		add_action( 'hf_admin_output_form_tab_fields', array( $this, 'tab_fields' ) );
		add_action( 'hf_admin_output_form_tab_messages', array( $this, 'tab_messages' ) );
		add_action( 'hf_admin_output_form_tab_settings', array( $this, 'tab_settings' ) );
		add_action( 'hf_admin_output_form_tab_actions', array( $this, 'tab_actions' ) );
		add_action( 'hf_admin_output_form_tab_submissions', array( $this, 'tab_submissions_list' ) );
		add_action( 'hf_admin_output_form_tab_submissions', array( $this, 'tab_submissions_detail' ) );
		add_action( 'enqueue_block_editor_assets', array( $this, 'enqueue_gutenberg_assets' ) );
	}

	public function enqueue_gutenberg_assets() {
		wp_enqueue_script( 'html-forms-block', plugins_url( 'assets/js/gutenberg-block.js', $this->plugin_file ), array( 'wp-blocks', 'wp-i18n', 'wp-element', 'wp-components' ) );
		$forms = hf_get_forms();
		$data  = array();
		foreach ( $forms as $form ) {
			$data[] = array(
				'title' => $form->title,
				'slug'  => $form->slug,
				'id'    => $form->ID,
			);
		}
		wp_localize_script( 'html-forms-block', 'html_forms', $data );
	}

	public function register_settings() {
		// register settings
		register_setting( 'hf_settings', 'hf_settings', array( $this, 'sanitize_settings' ) );
	}

	public function run_migrations() {
		$version_from = get_option( 'hf_version', '0.0' );
		$version_to   = HTML_FORMS_VERSION;

		if ( version_compare( $version_from, $version_to, '>=' ) ) {
			return;
		}

		$migrations = new Migrations( $version_from, $version_to, dirname( $this->plugin_file ) . '/migrations' );
		$migrations->run();
		update_option( 'hf_version', HTML_FORMS_VERSION );
	}

	/**
	 * @param array $dirty
	 * @return array
	 */
	public function sanitize_settings( $dirty ) {
		return $dirty;
	}

	public function listen() {
		if ( isset( $_GET['_hf_admin_action'] ) ) {
			$action = (string) $_GET['_hf_admin_action'];
		} elseif ( isset( $_POST['_hf_admin_action'] ) ) {
			$action = (string) $_POST['_hf_admin_action'];
		} else {
			return;
		}

		// verify nonce
		if ( ! isset( $_REQUEST['_wpnonce'] ) || ! wp_verify_nonce( $_REQUEST['_wpnonce'], '_hf_admin_action' ) ) {
			wp_nonce_ays( $action );
			exit;
		}

		// do nothing if logged in user is not of role administrator
		if ( ! current_user_can( 'edit_forms' ) ) {
			return;
		}

		/**
		 * Allows you to hook into requests containing `_hf_admin_action` => action name.
		 *
		 * The dynamic portion of the hook name, `$action`, refers to the action name.
		 *
		 * By the time this hook is fired, the user is already authorized. After processing all the registered hooks,
		 * the request is redirected back to the referring URL.
		 *
		 * @since 3.0
		 */
		do_action( 'hf_admin_action_' . $action );

		// redirect back to where we came from
		$redirect_url = ! empty( $_REQUEST['_redirect_to'] ) ? $_REQUEST['_redirect_to'] : remove_query_arg( '_hf_admin_action' );
		wp_safe_redirect( $redirect_url );
		exit;
	}

	public function assets() {
		if ( empty( $_GET['page'] ) || strpos( $_GET['page'], 'html-forms' ) !== 0 ) {
			return;
		}

		wp_enqueue_style( 'html-forms-admin', plugins_url( 'assets/css/admin.css', $this->plugin_file ), array(), HTML_FORMS_VERSION );
		wp_enqueue_script( 'html-forms-admin', plugins_url( 'assets/js/admin.js', $this->plugin_file ), array(), HTML_FORMS_VERSION, true );
		wp_localize_script(
			'html-forms-admin',
			'hf_options',
			array(
				'page'    => $_GET['page'],
				'view'    => empty( $_GET['view'] ) ? '' : $_GET['view'],
				'form_id' => empty( $_GET['form_id'] ) ? 0 : (int) $_GET['form_id'],
			)
		);
	}

	public function menu() {
		$capability = 'edit_forms';
		$svg_icon   = '<svg version="1.0" xmlns="http://www.w3.org/2000/svg" width="256.000000pt" height="256.000000pt" viewBox="0 0 256.000000 256.000000" preserveAspectRatio="xMidYMid meet"><g transform="translate(0.000000,256.000000) scale(0.100000,-0.100000)"
			fill="#000000" stroke="none"><path d="M0 1280 l0 -1280 1280 0 1280 0 0 1280 0 1280 -1280 0 -1280 0 0 -1280z m2031 593 c8 -8 9 -34 4 -78 -6 -56 -9 -65 -23 -60 -43 16 -98 15 -132 -2 -50 -26 -72 -72 -78 -159 l-5 -74 92 0 91 0 0 -70 0 -70 -90 0 -90 0 0 -345 0 -345 -90 0 -90 0 0 345 0 345 -55 0 -55 0 0 70 0 70 55 0 55 0 0 38 c0 63 20 153 45 202 54 105 141 152 273 147 45 -2 87 -8 93 -14z m-1291 -288 l0 -235 230 0 230 0 0 235 0 235 90 0 90 0 0 -575 0 -575 -90 0 -90 0 0 260 0 260 -230 0 -230 0 0 -260 0 -260 -90 0 -90 0 0 575 0 575 90 0 90 0 0 -235z"/></g></svg>';
		add_menu_page( 'HTML Forms', 'HTML Forms', $capability, 'html-forms', array( $this, 'page_overview' ), 'data:image/svg+xml;base64,' . base64_encode( $svg_icon ), '99.88491' );
		add_submenu_page( 'html-forms', __( 'Forms', 'html-forms' ), __( 'All Forms', 'html-forms' ), $capability, 'html-forms', array( $this, 'page_overview' ) );
		add_submenu_page( 'html-forms', __( 'Add new form', 'html-forms' ), __( 'Add New', 'html-forms' ), $capability, 'html-forms-add-form', array( $this, 'page_new_form' ) );
		add_submenu_page( 'html-forms', __( 'Settings', 'html-forms' ), __( 'Settings', 'html-forms' ), $capability, 'html-forms-settings', array( $this, 'page_settings' ) );

		if ( ! defined( 'HF_PREMIUM_VERSION' ) ) {
			add_submenu_page( 'html-forms', 'Premium', '<span style="color: #ea6ea6;">Premium</span>', $capability, 'html-forms-premium', array( $this, 'page_premium' ) );
		}
	}

	public function add_screen_options() {
		// only run on the submissions overview page (not detail)
		if ( empty( $_GET['page'] ) || $_GET['page'] !== 'html-forms' || empty( $_GET['view'] ) || $_GET['view'] !== 'edit' || empty( $_GET['form_id'] ) || ! empty( $_GET['submission_id'] ) ) {
			return;
		}

		// don't run if form does not have submissions enabled
		$form = hf_get_form( $_GET['form_id'] );
		if ( ! $form->settings['save_submissions'] ) {
			return;
		}

		// tell screen options to show columns option
		$submissions = hf_get_form_submissions( $_GET['form_id'] );
		$columns     = $this->get_submission_columns( $submissions );
		add_filter(
			'manage_toplevel_page_html-forms_columns',
			function( $unused ) use ( $columns ) {
				return $columns;
			}
		);
		add_screen_option( 'layout_columns' );
	}

	public function page_overview() {
		if ( ! empty( $_GET['view'] ) && $_GET['view'] === 'edit' ) {
			$this->page_edit_form();
			return;
		}

		$settings = hf_get_settings();

		require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
		$table = new Table( $settings );

		require dirname( $this->plugin_file ) . '/views/page-overview.php';
	}

	public function page_new_form() {
		require dirname( $this->plugin_file ) . '/views/page-add-form.php';
	}

	public function page_settings() {
		$settings = hf_get_settings();
		require dirname( $this->plugin_file ) . '/views/page-global-settings.php';
	}

	public function page_premium() {
		require dirname( $this->plugin_file ) . '/views/page-premium.php';
	}

	public function page_edit_form() {
		$active_tab = ! empty( $_GET['tab'] ) ? $_GET['tab'] : 'fields';
		$form_id    = (int) $_GET['form_id'];
		$form       = hf_get_form( $form_id );
		$settings   = hf_get_settings();
		require dirname( $this->plugin_file ) . '/views/page-edit-form.php';
	}

	public function tab_fields( Form $form ) {
		$form_preview_url = add_query_arg(
			array(
				'hf_preview_form' => $form->ID,
			),
			site_url( '/', 'admin' )
		);
		require dirname( $this->plugin_file ) . '/views/tab-fields.php';
	}

	public function tab_messages( Form $form ) {
		require dirname( $this->plugin_file ) . '/views/tab-messages.php';
	}


	public function tab_settings( Form $form ) {
		require dirname( $this->plugin_file ) . '/views/tab-settings.php';
	}


	public function tab_actions( Form $form ) {
		require dirname( $this->plugin_file ) . '/views/tab-actions.php';
	}

	public function get_submission_columns( array $submissions ) {
		$columns = array();
		foreach ( $submissions as $s ) {
			if ( ! is_array( $s->data ) ) {
				continue;
			}

			foreach ( $s->data as $field => $value ) {
				if ( ! isset( $columns[ $field ] ) ) {
					$columns[ $field ] = esc_html( ucfirst( strtolower( str_replace( '_', ' ', $field ) ) ) );
				}
			}
		}
		return $columns;
	}

	public function tab_submissions_list( Form $form ) {
		if ( ! empty( $_GET['submission_id'] ) ) {
			return;
		}

		$submissions    = hf_get_form_submissions( $form->ID );
		$columns        = $this->get_submission_columns( $submissions );
		$hidden_columns = get_hidden_columns( get_current_screen() );

		require dirname( $this->plugin_file ) . '/views/tab-submissions-list.php';
	}

	public function tab_submissions_detail( Form $form ) {
		if ( empty( $_GET['submission_id'] ) ) {
			return;
		}

		$submission = hf_get_form_submission( (int) $_GET['submission_id'] );
		require dirname( $this->plugin_file ) . '/views/tab-submissions-detail.php';
	}


	public function process_create_form() {
		// Fix for MultiSite stripping KSES for roles other than administrator
		remove_all_filters( 'content_save_pre' );

		$data       = $_POST['form'];
		$form_title = sanitize_text_field( $data['title'] );
		$form_id    = wp_insert_post(
			array(
				'post_type'    => 'html-form',
				'post_status'  => 'publish',
				'post_title'   => $form_title,
				'post_content' => $this->get_default_form_content(),
			)
		);

		wp_safe_redirect( admin_url( 'admin.php?page=html-forms&view=edit&form_id=' . $form_id ) );
		exit;
	}

	public function process_save_form() {
		$form_id = (int) $_POST['form_id'];
		$form    = hf_get_form( $form_id );
		$data    = $_POST['form'];

		// Fix for MultiSite stripping KSES for roles other than administrator
		remove_all_filters( 'content_save_pre' );

		// strip <form> tag from markup
		$data['markup'] = preg_replace( '/<\/?form(.|\s)*?>/i', '', $data['markup'] );

		$form_id = wp_insert_post(
			array(
				'ID'           => $form_id,
				'post_type'    => 'html-form',
				'post_status'  => 'publish',
				'post_title'   => sanitize_text_field( $data['title'] ),
				'post_content' => $data['markup'],
				'post_name'    => sanitize_title_with_dashes( $data['slug'] ),
			)
		);

		if ( ! empty( $data['settings'] ) ) {
			update_post_meta( $form_id, '_hf_settings', $data['settings'] );
		}

		// save form messages in individual meta keys
		foreach ( $data['messages'] as $key => $message ) {
			update_post_meta( $form_id, 'hf_message_' . $key, $message );
		}

		$redirect_url_args = array(
			'form_id' => $form_id,
			'saved'   => 1,
		);
		$redirect_url      = add_query_arg( $redirect_url_args, admin_url( 'admin.php?page=html-forms&view=edit' ) );
		wp_safe_redirect( $redirect_url );
		exit;
	}

	/**
	 * Get URL for a tab on the current page.
	 *
	 * @since 3.0
	 * @internal
	 * @param $tab
	 * @return string
	 */
	public function get_tab_url( $tab ) {
		return add_query_arg( array( 'tab' => $tab ), remove_query_arg( 'tab' ) );
	}

	/**
	 * @return array
	 */
	public function get_available_form_actions() {
		$actions = array();

		/**
		 * Filters the available form actions
		 *
		 * @param array $actions
		 */
		$actions = apply_filters( 'hf_available_form_actions', $actions );

		return $actions;
	}

	public function process_bulk_delete_submissions() {
		global $wpdb;

		if ( empty( $_POST['id'] ) ) {
			return;
		}

		$ids   = $_POST['id'];
		$table = $wpdb->prefix . 'hf_submissions';
		$ids   = join( ',', array_map( 'esc_sql', $ids ) );
		$wpdb->query( sprintf( "DELETE FROM {$table} WHERE id IN( %s );", $ids ) );
		$wpdb->query( sprintf( "DELETE FROM {$wpdb->postmeta} WHERE post_id IN ( %s ) AND meta_key LIKE '_hf_%%';", $ids ) );
	}

	private function get_default_form_content() {
		$html  = '';
		$html .= sprintf( "<p>\n\t<label>%1\$s</label>\n\t<input type=\"text\" name=\"NAME\" placeholder=\"%1\$s\" required />\n</p>", __( 'Your name', 'html-forms' ) ) . PHP_EOL;
		$html .= sprintf( "<p>\n\t<label>%1\$s</label>\n\t<input type=\"email\" name=\"EMAIL\" placeholder=\"%1\$s\" required />\n</p>", __( 'Your email', 'html-forms' ) ) . PHP_EOL;
		$html .= sprintf( "<p>\n\t<label>%1\$s</label>\n\t<input type=\"text\" name=\"SUBJECT\" placeholder=\"%1\$s\" required />\n</p>", __( 'Subject', 'html-forms' ) ) . PHP_EOL;
		$html .= sprintf( "<p>\n\t<label>%1\$s</label>\n\t<textarea name=\"MESSAGE\" placeholder=\"%1\$s\" required></textarea>\n</p>", __( 'Message', 'html-forms' ) ) . PHP_EOL;
		$html .= sprintf( "<p>\n\t<input type=\"submit\" value=\"%s\" />\n</p>", __( 'Send', 'html-forms' ) );
		return $html;
	}

}
