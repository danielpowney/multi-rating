<?php
/*
Plugin Name: Multi Rating
Plugin URI: http://wordpress.org/plugins/multi-rating/
Description: A powerful rating system and review plugin for WordPress.
Version: 4.3
Author: Daniel Powney
Author URI: http://danielpowney.com
License: GPL2
Text Domain: multi-rating
Domain Path: languages
*/


/**
 * Multi_Rating plugin class
 */
class Multi_Rating {

	/** Singleton *************************************************************/

	/**
	 * @var Multi_Rating The one true Multi_Rating
	 */
	private static $instance;

	/**
	 * Settings instance variable
	 */
	public $settings = null;

	/**
	 * Post metabox instance variable
	 */
	public $post_metabox = null;

	/**
	 * Constants
	 */
	const
	VERSION = '4.3',
	ID = 'multi-rating',

	// tables
	RATING_SUBJECT_TBL_NAME 					= 'mr_rating_subject',
	RATING_ITEM_TBL_NAME 						= 'mr_rating_item',
	RATING_ITEM_ENTRY_TBL_NAME					= 'mr_rating_item_entry',
	RATING_ITEM_ENTRY_VALUE_TBL_NAME 			= 'mr_rating_item_entry_value',

	// settings
	CUSTOM_TEXT_SETTINGS 						= 'mr_custom_text_settings',
	STYLE_SETTINGS 								= 'mr_style_settings',
	POSITION_SETTINGS 							= 'mr_position_settings',
	GENERAL_SETTINGS 							= 'mr_general_settings',
	CUSTOM_IMAGES_SETTINGS						= 'mr_custom_images_settings',

	// options
	CUSTOM_CSS_OPTION 							= 'mr_custom_css',
	STAR_RATING_COLOUR_OPTION					= 'mr_star_rating_colour',
	STAR_RATING_HOVER_COLOUR_OPTION				= 'mr_star_rating_hover_colour',
	RATING_RESULTS_POSITION_OPTION				= 'mr_rating_results_position',
	RATING_FORM_POSITION_OPTION 				= 'mr_rating_form',
	RATING_FORM_TITLE_TEXT_OPTION 				= 'mr_rating_form_title_text',
	RATING_RESULTS_LIST_TITLE_TEXT_OPTION 		= 'mr_rating_results_list_title_text',
	POST_TYPES_OPTION							= 'mr_post_types',
	SUBMIT_RATING_FORM_BUTTON_TEXT_OPTION		= 'mr_rating_form_button_text',
	FILTER_BUTTON_TEXT_OPTION					= 'mr_filter_button_text',
	FILTER_LABEL_TEXT_OPTION					= 'mr_filter_label_text',
	RATING_FORM_SUBMIT_SUCCESS_MESSAGE_OPTION 	= 'mr_rating_form_submit_success_message',
	DATE_VALIDATION_FAIL_MESSAGE_OPTION			= 'mr_date_validation_fail_message',
	NO_RATING_RESULTS_TEXT_OPTION				= 'mr_no_rating_results_text',
	FIELD_REQUIRED_ERROR_MESSAGE_OPTION			= 'mrp_field_required_error', // FIXME
	INCLUDE_FONT_AWESOME_OPTION					= 'mr_include_font_awesome',
	FONT_AWESOME_VERSION_OPTION					= 'mr_font_awesome_version',
	VERSION_OPTION								= 'mr_version_option',
	DO_ACTIVATION_REDIRECT_OPTION				= 'mr_do_activiation_redirect',
	RATING_RESULTS_CACHE_OPTION					= 'mr_rating_results_cache',
	HIDE_RATING_FORM_AFTER_SUBMIT_OPTION		= 'mr_hide_rating_form',
	USE_CUSTOM_STAR_IMAGES						= 'mr_use_custom_star_images',
	CUSTOM_FULL_STAR_IMAGE						= 'mr_custom_full_star_img',
	CUSTOM_HALF_STAR_IMAGE						= 'mr_custom_half_star_img',
	CUSTOM_EMPTY_STAR_IMAGE						= 'mr_custom_empty_star_img',
	CUSTOM_HOVER_STAR_IMAGE						= 'mr_custom_hover_star_img',
	CUSTOM_STAR_IMAGE_WIDTH						= 'mr_custom_star_img_width',
	CUSTOM_STAR_IMAGE_HEIGHT					= 'mr_custom_star_img_height',
	SAVE_RATING_RESTRICTION_TYPES_OPTION		= 'mr_save_rating_restriction_types',
	SAVE_RATING_RESTRICTION_HOURS_OPTION		= 'mr_save_rating_restriction_hours',
	SAVE_RATING_RESTRICTION_ERROR_MESSAGE_OPTION = 'mr_save_rating_restriction_error_message',
	TEMPLATE_STRIP_NEWLINES_OPTION				= 'mr_template_strip_newlines',
	ERROR_MESSAGE_COLOUR_OPTION					= 'mr_error_message_colour',
	DISABLE_STYLES_OPTION						= 'mr_disable_styles',

	//values
	SCORE_RESULT_TYPE							= 'score',
	STAR_RATING_RESULT_TYPE						= 'star_rating',
	PERCENTAGE_RESULT_TYPE						= 'percentage',
	DO_NOT_SHOW									= 'do_not_show',
	SELECT_ELEMENT								= 'select',

	// pages
	SETTINGS_PAGE_SLUG							= 'mr_settings',
	ABOUT_PAGE_SLUG								= 'mr_about',
	RATING_ITEMS_PAGE_SLUG						= 'mr_rating_items',
	RATING_RESULTS_PAGE_SLUG					= 'mr_rating_results',
	RATING_ENTRIES_PAGE_SLUG					= 'mr_rating_entries',
	REPORTS_PAGE_SLUG							= 'mr_reports',
	TOOLS_PAGE_SLUG								= 'mr_tools',
	EDIT_RATING_PAGE_SLUG						= 'mr_edit_rating',

	// tabs
	RATING_RESULTS_TAB							= 'mr_rating_results',
	ENTRIES_TAB									= 'mr_entries',
	ENTRY_VALUES_TAB							= 'mr_entry_values',
	ENTRIES_PER_DAY_REPORT_TAB					= 'mr_entries_per_day_report',

	// post meta box
	RATING_FORM_POSITION_POST_META				= 'rating_form_position',
	RATING_RESULTS_POSITION_POST_META			= 'rating_results_position',
	RATING_RESULTS_POST_META_KEY				= 'mr_rating_results',

	// cookies
	POST_SAVE_RATING_COOKIE						= 'mr_post_save_rating';

	/**
	 *
	 * @return Multi_Rating
	 */
	public static function instance() {

		if ( ! isset( self::$instance )
				&& ! ( self::$instance instanceof Multi_Rating ) ) {

			self::$instance = new Multi_Rating;

			add_action( 'admin_enqueue_scripts', array( self::$instance, 'assets' ) );

			if ( is_admin() && ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX ) ) {

				add_action( 'admin_menu', array(self::$instance, 'add_admin_menus') );
				add_action( 'admin_enqueue_scripts', array( self::$instance, 'admin_assets' ) );
				add_action( 'admin_init', array( self::$instance, 'redirect_about_page' ) );

			} else {
				add_action( 'wp_enqueue_scripts', array( self::$instance, 'assets' ) );
			}

			self::$instance->includes();
			self::$instance->settings = new MR_Settings();

			$disable_styles = self::instance()->settings->style_settings[Multi_Rating::DISABLE_STYLES_OPTION];
			if ( ! $disable_styles ) {
				add_action( 'wp_head', array( self::$instance, 'mr_head') );
			}

			add_action( 'init', array( self::$instance, 'load_textdomain' ) );

			if ( is_admin() && ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX ) ) {

				self::$instance->post_metabox = new MR_Post_Metabox();

				add_action( 'delete_user', array( self::$instance, 'delete_user' ), 11, 2 );
				add_action( 'deleted_post', array( self::$instance, 'deleted_post' ) );
			}

			self::$instance->add_ajax_callbacks();
		}

		return Multi_Rating::$instance;
	}

	/**
	 * Delete all associated ratings by user id
	 *
	 * @param $user_id
	 * @param $reassign user id
	 */
	public function delete_user( $user_id, $reassign ) {

		global $wpdb;

		if ( $reassign == null ) {
			// do nothing now has an invalid user id associated to it - oh well... decided not to delete the
			// rating as the user id is not displayed or used
		} else { // reassign ratings to a user
			$wpdb->update( $wpdb->prefix . Multi_Rating::RATING_ITEM_ENTRY_TBL_NAME,
					array( 'user_id' => $reassign ),
					array( 'user_id' => $user_id ),
					array( '%d' ),
					array( '%d' ) );
		}
	}

	/**
	 * Delete all associated ratings by post id
	 *
	 * @param $post_id
	 */
	public function deleted_post( $post_id ) {

		global $wpdb;
		$query = 'SELECT rating_item_entry_id AS rating_entry_id FROM ' . $wpdb->prefix . Multi_Rating::RATING_ITEM_ENTRY_TBL_NAME
				. ' WHERE post_id = %d';
		$entries = $wpdb->get_results( $wpdb->prepare( $query, $post_id ) );

		$this->delete_entries( $entries );
	}

	/**
	 * Deletes entries from database including rating item values
	 *
	 * @param $entries
	 */
	public function delete_entries( $entries ) {

		global $wpdb;

		foreach ( $entries as $entry_row ) {
			$rating_entry_id = $entry_row->rating_entry_id;

			$wpdb->delete( $wpdb->prefix . Multi_Rating::RATING_ITEM_ENTRY_TBL_NAME, array( 'rating_item_entry_id' => $rating_entry_id ), array( '%d' ) );
			$wpdb->delete( $wpdb->prefix . Multi_Rating::RATING_ITEM_ENTRY_VALUE_TBL_NAME, array( 'rating_item_entry_id' => $rating_entry_id ), array( '%d' ) );
		}
	}

	/**
	 * Includes files
	 */
	function includes() {

		require dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . 'shortcodes.php';
		require dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . 'widgets.php';
		require dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . 'class-utils.php';
		require dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . 'class-api.php';
		require dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . 'class-rating-form.php';
		require dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . 'auto-placement.php';
		require dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . 'misc-functions.php';
		require dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . 'class-settings.php';
		require dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . 'actions.php';
		require dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . 'legacy.php';
		require dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . 'template-functions.php';

		if ( is_admin() ) {

			require dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . 'admin' . DIRECTORY_SEPARATOR . 'class-rating-item-table.php';
			require dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . 'admin' . DIRECTORY_SEPARATOR . 'class-rating-entry-table.php';
			require dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . 'admin' . DIRECTORY_SEPARATOR . 'class-rating-results-table.php';
			require dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . 'admin' . DIRECTORY_SEPARATOR . 'class-post-metabox.php';
			require dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . 'admin' . DIRECTORY_SEPARATOR . 'about.php';
			require dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . 'admin' . DIRECTORY_SEPARATOR . 'rating-items.php';
			require dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . 'admin' . DIRECTORY_SEPARATOR . 'rating-results.php';
			require dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . 'admin' . DIRECTORY_SEPARATOR . 'rating-entries.php';
			require dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . 'admin' . DIRECTORY_SEPARATOR . 'reports.php';
			require dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . 'admin' . DIRECTORY_SEPARATOR . 'settings.php';
			require dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . 'admin' . DIRECTORY_SEPARATOR . 'tools.php';
			require dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . 'admin' . DIRECTORY_SEPARATOR . 'edit-rating.php';
		}
	}

	/**
	 * Activates the plugin
	 */
	public static function activate_plugin() {

		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

		try {

			global $wpdb, $charset_collate;

			// subjects can be a post type
			$sql_create_rating_subject_tbl = 'CREATE TABLE ' . $wpdb->prefix . Multi_Rating::RATING_SUBJECT_TBL_NAME . ' (
					rating_id bigint(20) NOT NULL AUTO_INCREMENT,
					post_type varchar(20) NOT NULL,
					PRIMARY KEY  (rating_id)
			) ' . $charset_collate;
			dbDelta( $sql_create_rating_subject_tbl );

			// subjects are rated by multiple rating items
			$sql_create_rating_item_tbl = 'CREATE TABLE '. $wpdb->prefix . Multi_Rating::RATING_ITEM_TBL_NAME . ' (
					rating_item_id bigint(20) NOT NULL AUTO_INCREMENT,
					rating_id bigint(20) NOT NULL,
					description varchar(255) NOT NULL,
					default_option_value int(11),
					max_option_value int(11),
					required tinyint(1) DEFAULT 0,
					active tinyint(1) DEFAULT 1,
					weight double precision DEFAULT 1.0,
					type varchar(20) NOT NULL DEFAULT "select",
					PRIMARY KEY  (rating_item_id)
			) ' . $charset_collate;
			dbDelta( $sql_create_rating_item_tbl );

			// rating item entries and results are saved
			$sql_create_rating_item_entry_tbl = 'CREATE TABLE ' . $wpdb->prefix . Multi_Rating::RATING_ITEM_ENTRY_TBL_NAME . ' (
					rating_item_entry_id bigint(20) NOT NULL AUTO_INCREMENT,
					post_id bigint(20) NOT NULL,
					entry_date datetime NOT NULL,
					user_id bigint(20) DEFAULT 0,
					PRIMARY KEY  (rating_item_entry_id),
					KEY ix_rating_entry (rating_item_entry_id,post_id)
			) ' . $charset_collate;
			dbDelta( $sql_create_rating_item_entry_tbl );

			$sql_create_rating_item_entry_value_tbl = 'CREATE TABLE ' . $wpdb->prefix . Multi_Rating::RATING_ITEM_ENTRY_VALUE_TBL_NAME . ' (
					rating_item_entry_value_id bigint(20) NOT NULL AUTO_INCREMENT,
					rating_item_entry_id bigint(20) NOT NULL,
					rating_item_id bigint(20) NOT NULL,
					value int(11) NOT NULL,
					PRIMARY KEY  (rating_item_entry_value_id),
					KEY ix_rating_entry (rating_item_entry_id)
			) ' . $charset_collate;
			dbDelta( $sql_create_rating_item_entry_value_tbl );

		} catch ( Exception $e ) {
			// do nothing
		}

		// Adds mr_edit_ratings capability which allows the editor and administrator roles to be able to edit / moderate ratings
		$editor_role = get_role( 'editor' );
		$admin_role = get_role( 'administrator' );

		if ( $editor_role ) {
			$editor_role->add_cap( 'mr_edit_ratings' );
		}
		if ( $admin_role ) {
			$admin_role->add_cap( 'mr_edit_ratings' );
		}

		// if no rating items exist, add a sample one :)
		try {

			$count = $wpdb->get_var( 'SELECT COUNT(rating_item_id) FROM ' . $wpdb->prefix . Multi_Rating::RATING_ITEM_TBL_NAME );

			if ( is_numeric( $count ) && $count == 0 ) {
				$wpdb->insert(  $wpdb->prefix . Multi_Rating::RATING_ITEM_TBL_NAME, array(
						'description' => __( 'Sample rating item', 'multi-rating' ),
						'max_option_value' => 5,
						'default_option_value' => 5,
						'weight' => 1,
						'type' => 'star_rating',
						'required' => true
				),
				array( '%s', '%d', '%d', '%f', '%s', '%d' ) );
			}

		} catch ( Exception $e ) {
			// do nothing
		}

	}

	/**
	 * Uninstalls the plugin
	 */
	public static function uninstall_plugin() {

		delete_option( Multi_Rating::GENERAL_SETTINGS );
		delete_option( Multi_Rating::CUSTOM_TEXT_SETTINGS );
		delete_option( Multi_Rating::POSITION_SETTINGS );
		delete_option( Multi_Rating::STYLE_SETTINGS );

		// Drop tables
		global $wpdb;

		$wpdb->query( 'DROP TABLE IF EXISTS ' . $wpdb->prefix . Multi_Rating::RATING_ITEM_ENTRY_TBL_NAME );
		$wpdb->query( 'DROP TABLE IF EXISTS ' . $wpdb->prefix . Multi_Rating::RATING_ITEM_ENTRY_VALUE_TBL_NAME );
		$wpdb->query( 'DROP TABLE IF EXISTS ' . $wpdb->prefix . Multi_Rating::RATING_ITEM_TBL_NAME );
		$wpdb->query( 'DROP TABLE IF EXISTS ' . $wpdb->prefix . Multi_Rating::RATING_SUBJECT_TBL_NAME );
	}

	/**
	 * Redirects to about page on activation
	 */
	function redirect_about_page() {
		if ( ! is_network_admin() && get_option( MULTI_RATING::DO_ACTIVATION_REDIRECT_OPTION, false ) ) {
			delete_option( MULTI_RATING::DO_ACTIVATION_REDIRECT_OPTION );
			wp_redirect( 'admin.php?page=' . MULTI_RATING::ABOUT_PAGE_SLUG );
		}
	}

	/**
	 * Loads plugin text domain
	 */
	public function load_textdomain() {
		load_plugin_textdomain( 'multi-rating', false, dirname( plugin_basename( __FILE__) ) . DIRECTORY_SEPARATOR . 'languages' . DIRECTORY_SEPARATOR );
	}

	/**
	 * Adds admin menus
	 */
	public function add_admin_menus() {

		add_menu_page( __( 'Multi Rating', 'multi-rating' ), __( 'Multi Rating', 'multi-rating' ), 'mr_edit_ratings', Multi_Rating::RATING_RESULTS_PAGE_SLUG, 'mr_rating_results_screen', 'dashicons-star-filled', null );
		add_submenu_page( Multi_Rating::RATING_RESULTS_PAGE_SLUG, '', '', 'mr_edit_ratings', Multi_Rating::RATING_RESULTS_PAGE_SLUG, 'mr_rating_results_screen' );
		add_submenu_page( Multi_Rating::RATING_RESULTS_PAGE_SLUG, __( 'Ratings', 'multi-rating' ), __( 'Ratings', 'multi-rating' ), 'mr_edit_ratings', Multi_Rating::RATING_RESULTS_PAGE_SLUG, 'mr_rating_results_screen' );
		add_submenu_page( Multi_Rating::RATING_RESULTS_PAGE_SLUG, __( 'Entries', 'multi-rating' ), __( 'Entries', 'multi-rating' ), 'mr_edit_ratings', Multi_Rating::RATING_ENTRIES_PAGE_SLUG, 'mr_rating_entries_screen' );
		add_submenu_page( Multi_Rating::RATING_RESULTS_PAGE_SLUG, __( 'Rating Items', 'multi-rating' ), __( 'Rating Items', 'multi-rating' ), 'manage_options', Multi_Rating::RATING_ITEMS_PAGE_SLUG, 'mr_rating_items_screen' );
		add_submenu_page( Multi_Rating::RATING_RESULTS_PAGE_SLUG, __( 'Settings', 'multi-rating' ), __( 'Settings', 'multi-rating' ), 'manage_options', Multi_Rating::SETTINGS_PAGE_SLUG, 'mr_settings_screen' );
		add_submenu_page( Multi_Rating::RATING_RESULTS_PAGE_SLUG, __( 'Reports', 'multi-rating' ), __( 'Reports', 'multi-rating' ), 'mr_edit_ratings', Multi_Rating::REPORTS_PAGE_SLUG, 'mr_reports_screen' );
		add_submenu_page( Multi_Rating::RATING_RESULTS_PAGE_SLUG, __( 'Tools', 'multi-rating' ), __( 'Tools', 'multi-rating' ), 'mr_edit_ratings', Multi_Rating::TOOLS_PAGE_SLUG, 'mr_tools_screen' );
		add_submenu_page( Multi_Rating::RATING_RESULTS_PAGE_SLUG, __( 'About', 'multi-rating' ), __( 'About', 'multi-rating' ), 'mr_edit_ratings', Multi_Rating::ABOUT_PAGE_SLUG, 'mr_about_screen' );
		add_submenu_page( Multi_Rating::RATING_RESULTS_PAGE_SLUG, __( 'Edit Rating', 'multi-rating' ), '', 'mr_edit_ratings', Multi_Rating::EDIT_RATING_PAGE_SLUG, 'mr_edit_rating_screen' );
	}

	/**
	 * Javascript and CSS used by the plugin
	 *
	 * @since 0.1
	 */
	public function admin_assets() {

		$style_settings = (array) get_option( Multi_Rating::STYLE_SETTINGS );

		wp_enqueue_script( 'jquery' );

		$config_array = array(
				'ajax_url' => admin_url( 'admin-ajax.php' ),
				'ajax_nonce' => wp_create_nonce( Multi_Rating::ID.'-nonce' ),
				'confirm_clear_db_message' => __( 'Are you sure you want to permanently delete ratings?', 'multi-rating' )
		);

		wp_enqueue_script( 'mr-admin-script', plugins_url( 'assets' . DIRECTORY_SEPARATOR . 'js' . DIRECTORY_SEPARATOR . 'admin.js', __FILE__), array('jquery'), Multi_Rating::VERSION, true );
		wp_localize_script( 'mr-admin-script', 'mr_admin_data', $config_array );

		wp_enqueue_script( 'mr-frontend-script', plugins_url('assets' . DIRECTORY_SEPARATOR . 'js' . DIRECTORY_SEPARATOR . 'frontend-min.js', __FILE__), array('jquery'), Multi_Rating::VERSION, true );
		wp_localize_script( 'mr-frontend-script', 'mr_frontend_data', $config_array );

		$disable_styles = self::instance()->settings->style_settings[Multi_Rating::DISABLE_STYLES_OPTION];
		if ( ! $disable_styles ) {
			wp_enqueue_style( 'mr-frontend-style', plugins_url( 'assets' . DIRECTORY_SEPARATOR . 'css' . DIRECTORY_SEPARATOR . 'frontend-min.css', __FILE__ ) );
		}
		wp_enqueue_style( 'mr-admin-style', plugins_url( 'assets' . DIRECTORY_SEPARATOR . 'css' . DIRECTORY_SEPARATOR . 'admin.css', __FILE__ ) );

		// flot
		wp_enqueue_script( 'flot', plugins_url( 'assets' . DIRECTORY_SEPARATOR . 'js' . DIRECTORY_SEPARATOR . 'flot' . DIRECTORY_SEPARATOR . 'jquery.flot.js', __FILE__ ), array( 'jquery' ) );
		wp_enqueue_script( 'flot-categories', plugins_url( 'assets' . DIRECTORY_SEPARATOR . 'js' . DIRECTORY_SEPARATOR . 'flot' . DIRECTORY_SEPARATOR . 'jquery.flot.categories.js', __FILE__ ), array( 'jquery', 'flot' ) );
		wp_enqueue_script( 'flot-time', plugins_url( 'assets' . DIRECTORY_SEPARATOR . 'js' . DIRECTORY_SEPARATOR . 'flot' . DIRECTORY_SEPARATOR . 'jquery.flot.time.js', __FILE__ ), array( 'jquery', 'flot' ) );
		wp_enqueue_script( 'flot-selection', plugins_url( 'assets' . DIRECTORY_SEPARATOR . 'js' . DIRECTORY_SEPARATOR . 'flot' . DIRECTORY_SEPARATOR . 'jquery.flot.selection.js', __FILE__ ), array( 'jquery', 'flot', 'flot-time' ) );

		// color picker
		wp_enqueue_style( 'wp-color-picker' );
    	wp_enqueue_script( 'wp-color-picker' );

    	// date picker
		wp_enqueue_script('jquery-ui-datepicker');
		wp_enqueue_style( 'jquery-style', '//ajax.googleapis.com/ajax/libs/jqueryui/1.8.2/themes/smoothness/jquery-ui.css' );

		wp_enqueue_media();
	}

	/**
	 * Javascript and CSS used by the plugin
	 *
	 * @since 0.1
	 */
	public function assets() {

		$style_settings = (array) get_option( Multi_Rating::STYLE_SETTINGS );
		$custom_images_settings = (array) get_option( Multi_Rating::CUSTOM_IMAGES_SETTINGS );

		wp_enqueue_script('jquery');

		// Add simple table CSS for rating form
		$disable_styles = self::instance()->settings->style_settings[Multi_Rating::DISABLE_STYLES_OPTION];
		if ( ! $disable_styles )  {
			wp_enqueue_style( 'mr-frontend-style', plugins_url( 'assets' . DIRECTORY_SEPARATOR . 'css' . DIRECTORY_SEPARATOR . 'frontend-min.css', __FILE__ ) );
		}

		// Allow support for other versions of Font Awesome
		$load_icon_font_library = $style_settings[Multi_Rating::INCLUDE_FONT_AWESOME_OPTION];
		$icon_font_library = $style_settings[Multi_Rating::FONT_AWESOME_VERSION_OPTION];

		$icon_classes = MR_Utils::get_icon_classes( $icon_font_library );

		$protocol = ( isset( $_SERVER['HTTPS'] ) && $_SERVER['HTTPS'] == 'on' ) ? 'https' : 'http';

		if ( $load_icon_font_library ) {
			if ( $icon_font_library == 'font-awesome-4.0.3' ) {
				wp_enqueue_style( 'font-awesome', $protocol . '://netdna.bootstrapcdn.com/font-awesome/4.0.3/css/font-awesome.css' );
			} else if ( $icon_font_library == 'font-awesome-3.2.1' ) {
				wp_enqueue_style( 'font-awesome', $protocol . '://netdna.bootstrapcdn.com/font-awesome/3.2.1/css/font-awesome.css' );
			} else if ( $icon_font_library == 'font-awesome-4.1.0' ) {
				wp_enqueue_style( 'font-awesome', $protocol . '://maxcdn.bootstrapcdn.com/font-awesome/4.1.0/css/font-awesome.min.css' );
			} else if ( $icon_font_library == 'font-awesome-4.2.0' ) {
				wp_enqueue_style( 'font-awesome', $protocol . '://maxcdn.bootstrapcdn.com/font-awesome/4.2.0/css/font-awesome.min.css' );
			} else if ( $icon_font_library == 'font-awesome-4.3.0' ) {
				wp_enqueue_style( 'font-awesome', $protocol . '://maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css' );
			} else if ( $icon_font_library == 'font-awesome-4.5.0' ) {
				wp_enqueue_style( 'font-awesome', $protocol . '://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css' );
			} else if ( $icon_font_library == 'font-awesome-4.6.3' ) {
				wp_enqueue_style( 'font-awesome', $protocol . '://maxcdn.bootstrapcdn.com/font-awesome/4.6.3/css/font-awesome.min.css' );
			} else if ( $icon_font_library == 'font-awesome-4.7.0' ) {
				wp_enqueue_style( 'font-awesome', $protocol . '://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css' );
			} else if ( $icon_font_library == 'dashicons' ) {
				wp_enqueue_style( 'dashicons' );
			}
		}

		$config_array = array(
				'ajax_url' => admin_url( 'admin-ajax.php' ),
				'ajax_nonce' => wp_create_nonce( Multi_Rating::ID.'-nonce' ),
				'icon_classes' => json_encode( $icon_classes ),
				'use_custom_star_images' => ( $custom_images_settings[Multi_Rating::USE_CUSTOM_STAR_IMAGES] == true ) ? "true" : "false"
		);

		wp_enqueue_script( 'mr-frontend-script', plugins_url('assets' . DIRECTORY_SEPARATOR . 'js' . DIRECTORY_SEPARATOR . 'frontend-min.js', __FILE__), array('jquery'), Multi_Rating::VERSION, true );
		wp_localize_script( 'mr-frontend-script', 'mr_frontend_data', $config_array );
	}


	/**
	 * Register AJAX actions
	 */
	public function add_ajax_callbacks() {

		add_action( 'wp_ajax_save_rating', array( 'MR_Rating_Form', 'save_rating' ) );
		add_action( 'wp_ajax_nopriv_save_rating', array( 'MR_Rating_Form', 'save_rating' ) );
		add_action( 'wp_ajax_save_rating_item_table_column', array( 'MR_Rating_Item_Table', 'save_rating_item_table_column' ) );

		add_action( 'wp_ajax_nopriv_get_terms_by_taxonomy', 'mr_get_terms_by_taxonomy' );
		add_action( 'wp_ajax_get_terms_by_taxonomy', 'mr_get_terms_by_taxonomy' );

	}

	/**
	 * WP head
	 */
	function mr_head() {

		if ( apply_filters( 'mr_head_css', true ) ) { // in case you want to move the CSS into your theme instead

			$style_settings = (array) get_option( Multi_Rating::STYLE_SETTINGS );
			$custom_images_settings = (array) get_option( Multi_Rating::CUSTOM_IMAGES_SETTINGS );

			$star_rating_colour = $style_settings[Multi_Rating::STAR_RATING_COLOUR_OPTION];
			$star_rating_hover_colour = $style_settings[Multi_Rating::STAR_RATING_HOVER_COLOUR_OPTION];
			$error_message_colour = $style_settings[Multi_Rating::ERROR_MESSAGE_COLOUR_OPTION];

			$image_width = $custom_images_settings[Multi_Rating::CUSTOM_STAR_IMAGE_WIDTH];
			$image_height = $custom_images_settings[Multi_Rating::CUSTOM_STAR_IMAGE_HEIGHT];

			?>
			<style type="text/css">
				<?php
				echo $style_settings[Multi_Rating::CUSTOM_CSS_OPTION];
				?>
				.mr-custom-full-star {
					background: url(<?php echo $custom_images_settings[Multi_Rating::CUSTOM_FULL_STAR_IMAGE]; ?>) no-repeat;
					width: <?php echo $image_width; ?>px;
					height: <?php echo $image_height; ?>px;
					background-size: <?php echo $image_width; ?>px <?php echo $image_height; ?>px;
					image-rendering: -moz-crisp-edges;
					display: inline-block;
				}
				.mr-custom-half-star {
					background: url(<?php echo $custom_images_settings[Multi_Rating::CUSTOM_HALF_STAR_IMAGE]; ?>) no-repeat;
					width: <?php echo $image_width; ?>px;
					height: <?php echo $image_height; ?>px;
					background-size: <?php echo $image_width; ?>px <?php echo $image_height; ?>px;
					image-rendering: -moz-crisp-edges;
					display: inline-block;
				}
				.mr-custom-empty-star {
					background: url(<?php echo $custom_images_settings[Multi_Rating::CUSTOM_EMPTY_STAR_IMAGE]; ?>) no-repeat;
					width: <?php echo $image_width; ?>px;
					height: <?php echo $image_height; ?>px;
					background-size: <?php echo $image_width; ?>px <?php echo $image_height; ?>px;
					image-rendering: -moz-crisp-edges;
					display: inline-block;
				}
				.mr-custom-hover-star {
					background: url(<?php echo $custom_images_settings[Multi_Rating::CUSTOM_HOVER_STAR_IMAGE]; ?>) no-repeat;
					width: <?php echo $image_width; ?>px;
					height: <?php echo $image_height; ?>px;
					background-size: <?php echo $image_width; ?>px <?php echo $image_height; ?>px;
					image-rendering: -moz-crisp-edges;
					display: inline-block;
				}
				.mr-star-hover {
					color: <?php echo $star_rating_hover_colour; ?> !important;
				}
				.mr-star-full, .mr-star-half, .mr-star-empty {
					color: <?php echo $star_rating_colour; ?>;
				}
				.mr-error {
					color: <?php echo $error_message_colour; ?>;
				}
			</style>
			<?php
		}
	}
}

/**
 * Activate plugin
 */
function mr_activate_plugin() {

	if ( is_admin() && ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX ) ) {
		if ( ! is_network_admin() ) { // is admin network request?
			add_option(MULTI_RATING::DO_ACTIVATION_REDIRECT_OPTION, true);
		}
		Multi_Rating::activate_plugin();
	}

}
register_activation_hook( __FILE__, 'mr_activate_plugin' );


/**
 * Uninstall plugin
 */
function mr_uninstall_plugin() {

	if ( is_admin() && ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX ) ) {
		Multi_Rating::uninstall_plugin();
	}
}
register_uninstall_hook( __FILE__, 'mr_uninstall_plugin' );


// check for updates
if ( is_admin() && ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX ) ) {
	require dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . 'admin' . DIRECTORY_SEPARATOR . 'update-check.php';
	mr_update_check();
}

/**
 * Instantiate plugin main class
 */
function mr_multi_rating() {

	do_action( 'mr_before_init' );

	Multi_Rating::instance();

	do_action( 'mr_after_init' );
}
// Note WPML is initialized in "plugins_loaded" with priority 10, so priority needs > 10
add_action( 'plugins_loaded', 'mr_multi_rating', 11 );
