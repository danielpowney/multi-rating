<?php

/**
 * Settings class
 *
 * @author dpowney
 */
class MR_Settings {

	public $custom_text_settings = array();
	public $style_settings = array();
	public $position_settings = array();
	public $general_settings = array();
	public $custom_images_settings = array();

	/**
	 * Constructor
	 */
	function __construct() {

		if ( is_admin() && ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX ) ) {
			add_action('admin_init', array( &$this, 'register_settings' ) );
		}

		$this->load_settings();
	}

	/**
	 * Reisters settings
	 */
	function register_settings() {

		$this->register_general_settings();
		$this->register_position_settings();
		$this->register_custom_text_settings();
		$this->register_style_settings();
		$this->register_custom_images_settings();
	}

	/**
	 * Retrieve settings and applies default option values if not set
	 */
	function load_settings() {

		$this->general_settings 		= (array) get_option( Multi_Rating::GENERAL_SETTINGS );
		$this->position_settings 		= (array) get_option( Multi_Rating::POSITION_SETTINGS );
		$this->custom_text_settings 	= (array) get_option( Multi_Rating::CUSTOM_TEXT_SETTINGS );
		$this->style_settings 			= (array) get_option( Multi_Rating::STYLE_SETTINGS );
		$this->custom_images_settings 	= (array) get_option( Multi_Rating::CUSTOM_IMAGES_SETTINGS );

		// Merge with defaults

		$this->general_settings = array_merge( array(
				Multi_Rating::SAVE_RATING_RESTRICTION_TYPES_OPTION 		=> array( 'cookie' ),
				Multi_Rating::SAVE_RATING_RESTRICTION_HOURS_OPTION 		=> 24,
				Multi_Rating::POST_TYPES_OPTION 						=> 'post',
				Multi_Rating::RATING_RESULTS_CACHE_OPTION 				=> true,
				Multi_Rating::HIDE_RATING_FORM_AFTER_SUBMIT_OPTION 		=> true,
				Multi_Rating::TEMPLATE_STRIP_NEWLINES_OPTION 			=> true
		), $this->general_settings );

		$this->position_settings = array_merge( array(
				Multi_Rating::RATING_RESULTS_POSITION_OPTION 			=> 'after_title',
				Multi_Rating::RATING_FORM_POSITION_OPTION 				=> 'after_content'
		), $this->position_settings );

		$default_custom_text = array(
				Multi_Rating::RATING_FORM_TITLE_TEXT_OPTION 			=> __( 'Please rate this', 'multi-rating' ),
				Multi_Rating::RATING_RESULTS_LIST_TITLE_TEXT_OPTION		=> __( 'Rating Results', 'multi-rating' ),
				Multi_Rating::SUBMIT_RATING_FORM_BUTTON_TEXT_OPTION		=> __( 'Submit Rating', 'multi-rating' ),
				Multi_Rating::FILTER_BUTTON_TEXT_OPTION					=> __( 'Filter', 'multi-rating' ),
				Multi_Rating::FILTER_LABEL_TEXT_OPTION 					=> __( 'Category', 'multi-rating' ),
				Multi_Rating::RATING_FORM_SUBMIT_SUCCESS_MESSAGE_OPTION => __( 'Your rating was %adjusted_star_result%/5.', 'multi-rating'),
				Multi_Rating::SAVE_RATING_RESTRICTION_ERROR_MESSAGE_OPTION => __( 'You cannot submit a rating for the same post multiple times.', 'multi-rating' ),
				Multi_Rating::NO_RATING_RESULTS_TEXT_OPTION 			=> __( 'No ratings yet.', 'multi-rating' ),
				Multi_Rating::FIELD_REQUIRED_ERROR_MESSAGE_OPTION 		=> __( 'Field is required.', 'multi-rating' )
		);

		$this->custom_text_settings = array_merge( $default_custom_text, $this->custom_text_settings );

		// If custom text is disabled, always use defaults
		if ( apply_filters( 'mr_disable_custom_text', false ) ) {
			$this->custom_text_settings = $default_custom_text;
		}

		$this->style_settings = array_merge( array(
				Multi_Rating::CUSTOM_CSS_OPTION 						=> '',
				Multi_Rating::STAR_RATING_COLOUR_OPTION 				=> '#ffd700',
				Multi_Rating::STAR_RATING_HOVER_COLOUR_OPTION 			=> '#ffba00',
				Multi_Rating::INCLUDE_FONT_AWESOME_OPTION 				=> true,
				Multi_Rating::FONT_AWESOME_VERSION_OPTION 				=> 'font-awesome-4.7.0',
				Multi_Rating::ERROR_MESSAGE_COLOUR_OPTION 				=> '#EC6464',
				Multi_Rating::DISABLE_STYLES_OPTION 					=> false
		), $this->style_settings );

		$this->custom_images_settings = array_merge( array(
				Multi_Rating::USE_CUSTOM_STAR_IMAGES 					=> false,
				Multi_Rating::CUSTOM_FULL_STAR_IMAGE 					=> '',
				Multi_Rating::CUSTOM_HALF_STAR_IMAGE 					=> '',
				Multi_Rating::CUSTOM_EMPTY_STAR_IMAGE 					=> '',
				Multi_Rating::CUSTOM_HOVER_STAR_IMAGE 					=> '',
				Multi_Rating::CUSTOM_STAR_IMAGE_WIDTH 					=> 32,
				Multi_Rating::CUSTOM_STAR_IMAGE_HEIGHT 					=> 32,
		), $this->custom_images_settings );

		// TODO only update if different...
		update_option( Multi_Rating::GENERAL_SETTINGS, $this->general_settings );
		update_option( Multi_Rating::POSITION_SETTINGS, $this->position_settings );
		update_option( Multi_Rating::CUSTOM_TEXT_SETTINGS, $this->custom_text_settings );
		update_option( Multi_Rating::STYLE_SETTINGS, $this->style_settings );
		update_option( Multi_Rating::CUSTOM_IMAGES_SETTINGS, $this->custom_images_settings );
	}

	/**
	 * Register general settings
	 */
	function register_general_settings() {

		register_setting( Multi_Rating::GENERAL_SETTINGS, Multi_Rating::GENERAL_SETTINGS, array( &$this, 'sanitize_general_settings' ) );

		add_settings_section( 'section_general', __( 'General Settings', 'multi-rating' ), array( &$this, 'section_general_desc' ), Multi_Rating::SETTINGS_PAGE_SLUG . '&setting=' . Multi_Rating::GENERAL_SETTINGS);

		$post_types = $post_types = get_post_types( array(
				'public' => true,
				'show_ui' => true
		), 'objects' );

		$post_type_checkboxes = array();
		foreach ( $post_types as $post_type ) {
			array_push( $post_type_checkboxes, array(
					'name' => $post_type->name,
					'label' => $post_type->labels->name
			) );
		}

		$setting_fields = array(
				Multi_Rating::POST_TYPES_OPTION => array(
						'title' 	=> __( 'Post Types', 'rating-pro' ),
						'callback' 	=> 'field_checkboxes',
						'page' 		=> Multi_Rating::SETTINGS_PAGE_SLUG . '&setting=' . Multi_Rating::GENERAL_SETTINGS,
						'section' 	=> 'section_general',
						'args' => array(
								'option_name' 	=> Multi_Rating::GENERAL_SETTINGS,
								'setting_id' 	=> Multi_Rating::POST_TYPES_OPTION,
								'description' 	=> __( 'Enable post types for auto placement of the rating form and rating results.', 'multi-rating' ),
								'checkboxes' 	=> $post_type_checkboxes
						)
				),
				Multi_Rating::SAVE_RATING_RESTRICTION_TYPES_OPTION => array(
						'title' 	=> __( 'Duplicate Check Method', 'multi-rating' ),
						'callback' 	=> 'field_duplicate_check_method',
						'page' 		=> Multi_Rating::SETTINGS_PAGE_SLUG . '&setting=' . Multi_Rating::GENERAL_SETTINGS,
						'section' 	=> 'section_general'
				),
				Multi_Rating::RATING_RESULTS_CACHE_OPTION => array(
						'title' 	=> __( 'Store Calculated Ratings', 'multi-rating' ),
						'callback' 	=> 'field_checkbox',
						'page' 		=> Multi_Rating::SETTINGS_PAGE_SLUG . '&setting=' . Multi_Rating::GENERAL_SETTINGS,
						'section' 	=> 'section_general',
						'args' => array(
								'option_name' 	=> Multi_Rating::GENERAL_SETTINGS,
								'setting_id' 	=> Multi_Rating::RATING_RESULTS_CACHE_OPTION,
								'label' 		=> __( 'Check this box if you want to store calculated ratings as a cache in the database so you don\'t need to recalculate ratings on each page load.', 'multi-rating' )
						)
				),
				Multi_Rating::HIDE_RATING_FORM_AFTER_SUBMIT_OPTION => array(
						'title' 	=> __( 'Hide Rating Form Submit', 'multi-rating' ),
						'callback' 	=> 'field_checkbox',
						'page' 		=> Multi_Rating::SETTINGS_PAGE_SLUG . '&setting=' . Multi_Rating::GENERAL_SETTINGS,
						'section' 	=> 'section_general',
						'args' => array(
								'option_name' 	=> Multi_Rating::GENERAL_SETTINGS,
								'setting_id' 	=> Multi_Rating::HIDE_RATING_FORM_AFTER_SUBMIT_OPTION,
								'label' 		=> __( 'Check this box if you want to hide the rating form on submit.', 'multi-rating' )
						)
				),
				Multi_Rating::TEMPLATE_STRIP_NEWLINES_OPTION => array(
						'title' 	=> __( 'Template Strip Newlines', 'multi-rating' ),
						'callback' 	=> 'field_checkbox',
						'page' 		=> Multi_Rating::SETTINGS_PAGE_SLUG . '&setting=' . Multi_Rating::GENERAL_SETTINGS,
						'section' 	=> 'section_general',
						'args' => array(
								'option_name' 	=> Multi_Rating::GENERAL_SETTINGS,
								'setting_id' 	=> Multi_Rating::TEMPLATE_STRIP_NEWLINES_OPTION,
								'label' 		=> sprintf( __( 'Some plugins convert newlines to HTML paragraphs similar to <a href="%s">wpautop</a> (e.g. Visual Composer). Check this box if you want to prevent this from happening by stripping the newlines from the Multi Rating templates.', 'multi-rating' ), 'https://codex.wordpress.org/Function_Reference/wpautop' )
						)
				)
		);


		foreach ( $setting_fields as $setting_id => $setting_data ) {
			// $id, $title, $callback, $page, $section, $args
			add_settings_field( $setting_id, $setting_data['title'], array( $this, $setting_data['callback'] ), $setting_data['page'], $setting_data['section'], isset( $setting_data['args'] ) ? $setting_data['args'] : array() );
		}

	}

	/**
	 * General section desciption
	 */
	function section_general_desc() {
	}


	/**
	 * Duplicate check method field
	 */
	function field_duplicate_check_method() {

		$save_rating_restrictions_types = array(
				'cookie' => __( 'Cookie', 'multi-rating')
		);

		$save_rating_restriction_types_checked = $this->general_settings[Multi_Rating::SAVE_RATING_RESTRICTION_TYPES_OPTION];
		foreach ( $save_rating_restrictions_types as $save_rating_restrictions_type => $save_rating_restrictions_label) {
			echo '<input type="checkbox" name="' . Multi_Rating::GENERAL_SETTINGS . '[' . Multi_Rating::SAVE_RATING_RESTRICTION_TYPES_OPTION . '][]" value="' . $save_rating_restrictions_type . '"';
			if ( is_array($save_rating_restriction_types_checked ) ) {
				if ( in_array( $save_rating_restrictions_type, $save_rating_restriction_types_checked ) ) {
					echo 'checked="checked"';
				}
			} else {
				checked( $save_rating_restrictions_type, $save_rating_restriction_types_checked, true );
			}
			echo ' />&nbsp;<label class="checkbox-label">' . $save_rating_restrictions_label . '</label><br />';
		}
		?>

		<label><?php _e('Hours', 'multi-rating'); ?></label>&nbsp;<input class="small-text" type="number" min="1" name="<?php echo Multi_Rating::GENERAL_SETTINGS; ?>[<?php echo Multi_Rating::SAVE_RATING_RESTRICTION_HOURS_OPTION; ?>]" value="<?php echo $this->general_settings[Multi_Rating::SAVE_RATING_RESTRICTION_HOURS_OPTION]; ?>" />
		<p><?php _e( 'Choose a method to prevent ratings for the same post multiple times. This only applies for anonymous users.', 'multi-rating' ); ?></p>
		<?php
	}


	/**
	 * Sanitize the general settings
	 *
	 * @param $input
	 * @return boolean
	 */
	function sanitize_general_settings( $input ) {

		if ( ! isset( $input[Multi_Rating::SAVE_RATING_RESTRICTION_TYPES_OPTION] ) ) {
			$input[Multi_Rating::SAVE_RATING_RESTRICTION_TYPES_OPTION] = array();
		}

		if ( count($input[Multi_Rating::SAVE_RATING_RESTRICTION_TYPES_OPTION] ) > 0 ) {
			if ( ! is_numeric( $input[Multi_Rating::SAVE_RATING_RESTRICTION_HOURS_OPTION] ) ) {
				add_settings_error(Multi_Rating::GENERAL_SETTINGS, 'non_numeric_save_rating_restriction_hours', __( 'Save rating restriction hours must be numeric.', 'multi-rating' ) );
			} else if ( $input[Multi_Rating::SAVE_RATING_RESTRICTION_HOURS_OPTION] <= 0 ){
				add_settings_error(Multi_Rating::GENERAL_SETTINGS, 'invalid_save_rating_restriction_hours', __( 'Save rating restriction hours must be greater than 0.', 'multi-rating' ) );
			}
		}

		if ( ! isset( $input[Multi_Rating::POST_TYPES_OPTION] ) ) {
			$input[Multi_Rating::POST_TYPES_OPTION] = array();
		}

		// rating reulsts cache
		if ( isset( $input[Multi_Rating::RATING_RESULTS_CACHE_OPTION] )
				&& $input[Multi_Rating::RATING_RESULTS_CACHE_OPTION] == 'true' ) {
			$input[Multi_Rating::RATING_RESULTS_CACHE_OPTION] = true;
		} else {
			$input[Multi_Rating::RATING_RESULTS_CACHE_OPTION] = false;
		}

		// shortcode strip newlines
		if ( isset( $input[Multi_Rating::TEMPLATE_STRIP_NEWLINES_OPTION] )
				&& $input[Multi_Rating::TEMPLATE_STRIP_NEWLINES_OPTION] == 'true' ) {
			$input[Multi_Rating::TEMPLATE_STRIP_NEWLINES_OPTION] = true;
		} else {
			$input[Multi_Rating::TEMPLATE_STRIP_NEWLINES_OPTION] = false;
		}

		// hide rating form after submit
		if ( isset( $input[Multi_Rating::HIDE_RATING_FORM_AFTER_SUBMIT_OPTION] )
				&& $input[Multi_Rating::HIDE_RATING_FORM_AFTER_SUBMIT_OPTION] == 'true' ) {
			$input[Multi_Rating::HIDE_RATING_FORM_AFTER_SUBMIT_OPTION] = true;
		} else {
			$input[Multi_Rating::HIDE_RATING_FORM_AFTER_SUBMIT_OPTION] = false;
		}

		return $input;
	}

	/**
	 * Register position settings
	 */
	function register_position_settings() {

		register_setting( Multi_Rating::POSITION_SETTINGS, Multi_Rating::POSITION_SETTINGS, array( &$this, 'sanitize_position_settings' ) );

		add_settings_section( 'section_position', __( 'Auto Placement Settings', 'multi-rating' ), array( &$this, 'section_position_desc' ), Multi_Rating::SETTINGS_PAGE_SLUG . '&setting=' . Multi_Rating::POSITION_SETTINGS );

		$setting_fields = array(
				Multi_Rating::RATING_FORM_POSITION_OPTION => array(
						'title' 	=> __( 'Rating Form Position', 'multi-rating' ),
						'callback' 	=> 'field_select',
						'page' 		=> Multi_Rating::SETTINGS_PAGE_SLUG . '&setting=' . Multi_Rating::POSITION_SETTINGS,
						'section' 	=> 'section_position',
						'args' => array(
								'option_name' 	=> Multi_Rating::POSITION_SETTINGS,
								'setting_id' 	=> Multi_Rating::RATING_FORM_POSITION_OPTION,
								'label' 		=> __( 'Default rating form position on a post.', 'multi-rating' ),
								'select_options' => array(
										'do_not_show' 		=> __( 'Do not show', 'multi-rating' ),
										'before_content'	=> __( 'Before content', 'multi-rating' ),
										'after_content'		=> __( 'After content', 'multi-rating' )
								)
						)
				),
				Multi_Rating::RATING_RESULTS_POSITION_OPTION => array(
						'title' 	=> __( 'Rating Result Position', 'multi-rating' ),
						'callback' 	=> 'field_select',
						'page' 		=> Multi_Rating::SETTINGS_PAGE_SLUG . '&setting=' . Multi_Rating::POSITION_SETTINGS,
						'section' 	=> 'section_position',
						'args' => array(
								'option_name' 	=> Multi_Rating::POSITION_SETTINGS,
								'setting_id' 	=> Multi_Rating::RATING_RESULTS_POSITION_OPTION,
								'label' 		=> __( 'Default rating results position on a post.', 'multi-rating' ),
								'select_options' => array(
										'do_not_show' 		=> __( 'Do not show', 'multi-rating' ),
										'before_title'	=> __( 'Before title', 'multi-rating' ),
										'after_title'		=> __( 'After title', 'multi-rating' ),
										'before_content'	=> __( 'Before content', 'multi-rating' ),
										'after_content'		=> __( 'After content', 'multi-rating' ),
								)
						)
				)
		);

		foreach ( $setting_fields as $setting_id => $setting_data ) {

			// $id, $title, $callback, $page, $section, $args
			add_settings_field( $setting_id, $setting_data['title'], array( $this, $setting_data['callback'] ), $setting_data['page'], $setting_data['section'], $setting_data['args'] );
		}
	}

	/**
	 * Position section description
	 */
	function section_position_desc() {

	}

	/**
	 * Sanitize auto placement settings
	 *
	 * @param $input
	 * @return unknown
	 */
	function sanitize_position_settings( $input ) {
		return $input;
	}


	/**
	 * Register style settings
	 */
	function register_style_settings() {
		register_setting( Multi_Rating::STYLE_SETTINGS, Multi_Rating::STYLE_SETTINGS, array( &$this, 'sanitize_style_settings' ) );

		add_settings_section( 'section_styles', __( 'Style Settings', 'multi-rating' ), array( &$this, 'section_style_desc' ), Multi_Rating::SETTINGS_PAGE_SLUG . '&setting=' . Multi_Rating::STYLE_SETTINGS );

		$icon_font_library_options = array(
				'font-awesome-4.7.0'		=> __( 'Font Awesome 4.7.0', 'multi-rating' ),
				'font-awesome-4.6.3'		=> __( 'Font Awesome 4.6.3', 'multi-rating' ),
				'font-awesome-4.5.0'		=> __( 'Font Awesome 4.5.0', 'multi-rating' ),
				'font-awesome-4.3.0' 		=> __( 'Font Awesome 4.3.0', 'multi-rating' ),
				'font-awesome-4.2.0'		=> __( 'Font Awesome 4.2.0', 'multi-rating' ),
				'font-awesome-4.1.0' 		=> __( 'Font Awesome 4.1.0', 'multi-rating' ),
				'font-awesome-4.0.3' 		=> __( 'Font Awesome 4.0.3', 'multi-rating' ),
				'font-awesome-3.2.1' 		=> __( 'Font Awesome 3.2.1', 'multi-rating' ),
				'dashicons' 				=> __( 'Dashicons', 'multi-rating' )
		);
		$icon_font_library_options = apply_filters( 'mr_icon_font_library_options', $icon_font_library_options );

		$setting_fields = array(
				Multi_Rating::STAR_RATING_COLOUR_OPTION => array(
						'title' 	=> __( 'Primary Color', 'multi-rating' ),
						'callback' 	=> 'field_color_picker',
						'page' 		=> Multi_Rating::SETTINGS_PAGE_SLUG . '&setting=' . Multi_Rating::STYLE_SETTINGS,
						'section' 	=> 'section_styles',
						'args' => array(
								'option_name' 	=> Multi_Rating::STYLE_SETTINGS,
								'setting_id' 	=> Multi_Rating::STAR_RATING_COLOUR_OPTION,
								'label'			=> __( 'Choose a color for selection.', 'multi-rating' )
						)
				),
				Multi_Rating::STAR_RATING_HOVER_COLOUR_OPTION => array(
						'title' 	=> __( 'Secondary Color', 'multi-rating' ),
						'callback' 	=> 'field_color_picker',
						'page' 		=> Multi_Rating::SETTINGS_PAGE_SLUG . '&setting=' . Multi_Rating::STYLE_SETTINGS,
						'section' 	=> 'section_styles',
						'args' => array(
								'option_name' 	=> Multi_Rating::STYLE_SETTINGS,
								'setting_id' 	=> Multi_Rating::STAR_RATING_HOVER_COLOUR_OPTION,
								'label'			=> __( 'Choose a color for on hover.', 'multi-rating' )
						)
				),
				Multi_Rating::ERROR_MESSAGE_COLOUR_OPTION => array(
						'title' 	=> __( 'Error Color', 'multi-rating' ),
						'callback' 	=> 'field_color_picker',
						'page' 		=> Multi_Rating::SETTINGS_PAGE_SLUG . '&setting=' . Multi_Rating::STYLE_SETTINGS,
						'section' 	=> 'section_styles',
						'args' => array(
								'option_name' 	=> Multi_Rating::STYLE_SETTINGS,
								'setting_id' 	=> Multi_Rating::ERROR_MESSAGE_COLOUR_OPTION,
								'label'			=> __( 'Choose a color to highlight errors.', 'multi-rating' )
						)
				),
				Multi_Rating::FONT_AWESOME_VERSION_OPTION => array(
						'title' 	=> __( 'Icon Font Library', 'multi-rating' ),
						'callback' 	=> 'field_select',
						'page' 		=> Multi_Rating::SETTINGS_PAGE_SLUG . '&setting=' . Multi_Rating::STYLE_SETTINGS,
						'section' 	=> 'section_styles',
						'args' => array(
								'option_name' 	=> Multi_Rating::STYLE_SETTINGS,
								'setting_id' 	=> Multi_Rating::FONT_AWESOME_VERSION_OPTION,
								'label' 		=> null,
								'select_options' => $icon_font_library_options
						)
				),
				Multi_Rating::INCLUDE_FONT_AWESOME_OPTION => array(
						'title' 	=> __( 'Load Icon Font Library from CDN', 'multi-rating' ),
						'callback' 	=> 'field_checkbox',
						'page' 		=> Multi_Rating::SETTINGS_PAGE_SLUG . '&setting=' . Multi_Rating::STYLE_SETTINGS,
						'section' 	=> 'section_styles',
						'args' => array(
								'option_name' 	=> Multi_Rating::STYLE_SETTINGS,
								'setting_id' 	=> Multi_Rating::INCLUDE_FONT_AWESOME_OPTION,
								'label' 		=> __( 'Check this box if you want to load the font icon library from a CDN.', 'multi-rating' )
						)
				),
				Multi_Rating::CUSTOM_CSS_OPTION => array(
						'title' 	=> __( 'Custom CSS', 'multi-rating' ),
						'callback' 	=> 'field_textarea',
						'page' 		=> Multi_Rating::SETTINGS_PAGE_SLUG . '&setting=' . Multi_Rating::STYLE_SETTINGS,
						'section' 	=> 'section_styles',
						'args' => array(
								'option_name' 	=> Multi_Rating::STYLE_SETTINGS,
								'setting_id' 	=> Multi_Rating::CUSTOM_CSS_OPTION,
								'footer' 		=> __( 'Enter custom CSS styles above.', 'multi-rating' )
						)
				),
				Multi_Rating::DISABLE_STYLES_OPTION => array(
						'title' 	=> __( 'Disable Styles', 'multi-rating' ),
						'callback' 	=> 'field_checkbox',
						'page' 		=> Multi_Rating::SETTINGS_PAGE_SLUG . '&setting=' . Multi_Rating::STYLE_SETTINGS,
						'section' 	=> 'section_styles',
						'args' => array(
								'option_name' 	=> Multi_Rating::STYLE_SETTINGS,
								'setting_id' 	=> Multi_Rating::DISABLE_STYLES_OPTION,
								'label' 		=> __( 'Check this box to disable loading the plugin\'s CSS file.', 'multi-rating' )
						)
				),
		);

		foreach ( $setting_fields as $setting_id => $setting_data ) {

			// $id, $title, $callback, $page, $section, $args
			add_settings_field( $setting_id, $setting_data['title'], array( $this, $setting_data['callback'] ), $setting_data['page'], $setting_data['section'], $setting_data['args'] );
		}
	}

	/**
	 * Style section description
	 */
	function section_style_desc() {

	}

	/**
	 * Sanitize style settings
	 *
	 * @param $input
	 * @return string
	 */
	function sanitize_style_settings( $input ) {

		if ( isset( $input[Multi_Rating::INCLUDE_FONT_AWESOME_OPTION] ) && $input[Multi_Rating::INCLUDE_FONT_AWESOME_OPTION] == 'true' ) {
			$input[Multi_Rating::INCLUDE_FONT_AWESOME_OPTION] = true;
		} else {
			$input[Multi_Rating::INCLUDE_FONT_AWESOME_OPTION] = false;
		}

		if ( isset( $input[Multi_Rating::DISABLE_STYLES_OPTION] ) && $input[Multi_Rating::DISABLE_STYLES_OPTION] == 'true' ) {
			$input[Multi_Rating::DISABLE_STYLES_OPTION] = true;
		} else {
			$input[Multi_Rating::DISABLE_STYLES_OPTION] = false;
		}

		$input[Multi_Rating::CUSTOM_CSS_OPTION] = addslashes($input[Multi_Rating::CUSTOM_CSS_OPTION]);

		return $input;
	}


	/**
	 * Register custom images settings
	 */
	function register_custom_images_settings() {

		register_setting( Multi_Rating::CUSTOM_IMAGES_SETTINGS, Multi_Rating::CUSTOM_IMAGES_SETTINGS, array( &$this, 'sanitize_custom_images_settings' ) );

		add_settings_section( 'section_custom_images', __( 'Custom Images', 'multi-rating' ), array( &$this, 'section_custom_images_desc' ), Multi_Rating::SETTINGS_PAGE_SLUG . '&setting=' . Multi_Rating::CUSTOM_IMAGES_SETTINGS );

		$setting_fields = array(
				Multi_Rating::USE_CUSTOM_STAR_IMAGES => array(
						'title' 	=> __( 'Enable Custom Images', 'multi-rating' ),
						'callback' 	=> 'field_checkbox',
						'page' 		=> Multi_Rating::SETTINGS_PAGE_SLUG . '&setting=' . Multi_Rating::CUSTOM_IMAGES_SETTINGS,
						'section' 	=> 'section_custom_images',
						'args' => array(
								'option_name' 	=> Multi_Rating::CUSTOM_IMAGES_SETTINGS,
								'setting_id' 	=> Multi_Rating::USE_CUSTOM_STAR_IMAGES,
								'label' 		=> __( 'Check this box if you want to enable custom images.', 'multi-rating' )
						)
				),
				Multi_Rating::CUSTOM_FULL_STAR_IMAGE => array(
						'title' 	=> __( 'Full Star Image', 'multi-rating' ),
						'callback' 	=> 'field_upload',
						'page' 		=> Multi_Rating::SETTINGS_PAGE_SLUG . '&setting=' . Multi_Rating::CUSTOM_IMAGES_SETTINGS,
						'section' 	=> 'section_custom_images',
						'args' => array(
								'option_name' 	=> Multi_Rating::CUSTOM_IMAGES_SETTINGS,
								'setting_id' 	=> Multi_Rating::CUSTOM_FULL_STAR_IMAGE,
								'input_id'		=> 'custom-full-star-img',
								'button_id' 	=> 'custom-full-star-img-upload-btn',
								'preview_img_id' => 'custom-full-star-img-preview'
						)
				),
				Multi_Rating::CUSTOM_HALF_STAR_IMAGE => array(
						'title' 	=> __( 'Half Star Image', 'multi-rating' ),
						'callback' 	=> 'field_upload',
						'page' 		=> Multi_Rating::SETTINGS_PAGE_SLUG . '&setting=' . Multi_Rating::CUSTOM_IMAGES_SETTINGS,
						'section' 	=> 'section_custom_images',
						'args' => array(
								'option_name' 	=> Multi_Rating::CUSTOM_IMAGES_SETTINGS,
								'setting_id' 	=> Multi_Rating::CUSTOM_HALF_STAR_IMAGE,
								'input_id'		=> 'custom-half-star-img',
								'button_id' 	=> 'custom-half-star-img-upload-btn',
								'preview_img_id' => 'custom-half-star-img-preview'
						)
				),
				Multi_Rating::CUSTOM_EMPTY_STAR_IMAGE => array(
						'title' 	=> __( 'Empty Star Image', 'multi-rating' ),
						'callback' 	=> 'field_upload',
						'page' 		=> Multi_Rating::SETTINGS_PAGE_SLUG . '&setting=' . Multi_Rating::CUSTOM_IMAGES_SETTINGS,
						'section' 	=> 'section_custom_images',
						'args' => array(
								'option_name' 	=> Multi_Rating::CUSTOM_IMAGES_SETTINGS,
								'setting_id' 	=> Multi_Rating::CUSTOM_EMPTY_STAR_IMAGE,
								'input_id'		=> 'custom-empty-star-img',
								'button_id' 	=> 'custom-empty-star-img-upload-btn',
								'preview_img_id' => 'custom-empty-star-img-preview'
						)
				),
				Multi_Rating::CUSTOM_HOVER_STAR_IMAGE => array(
						'title' 	=> __( 'Hover Star Image', 'multi-rating' ),
						'callback' 	=> 'field_upload',
						'page' 		=> Multi_Rating::SETTINGS_PAGE_SLUG . '&setting=' . Multi_Rating::CUSTOM_IMAGES_SETTINGS,
						'section' 	=> 'section_custom_images',
						'args' => array(
								'option_name' 	=> Multi_Rating::CUSTOM_IMAGES_SETTINGS,
								'setting_id' 	=> Multi_Rating::CUSTOM_HOVER_STAR_IMAGE,
								'input_id'		=> 'custom-hover-star-img',
								'button_id' 	=> 'custom-hover-star-img-upload-btn',
								'preview_img_id' => 'custom-hover-star-img-preview'
						)
				),
				Multi_Rating::CUSTOM_STAR_IMAGE_WIDTH => array(
						'title' 	=> __( 'Star Image Width', 'multi-rating' ),
						'callback' 	=> 'field_input',
						'page' 		=> Multi_Rating::SETTINGS_PAGE_SLUG . '&setting=' . Multi_Rating::CUSTOM_IMAGES_SETTINGS,
						'section' 	=> 'section_custom_images',
						'args' => array(
								'option_name' 	=> Multi_Rating::CUSTOM_IMAGES_SETTINGS,
								'setting_id' 	=> Multi_Rating::CUSTOM_STAR_IMAGE_WIDTH,
								'label' 		=> __( 'pixels', 'multi-rating' ),
								'class'			=> 'small-text',
								'type'			=> 'number'
						)
				),
				Multi_Rating::CUSTOM_STAR_IMAGE_HEIGHT => array(
						'title' 	=> __( 'Star Image Height', 'multi-rating' ),
						'callback' 	=> 'field_input',
						'page' 		=> Multi_Rating::SETTINGS_PAGE_SLUG . '&setting=' . Multi_Rating::CUSTOM_IMAGES_SETTINGS,
						'section' 	=> 'section_custom_images',
						'args' => array(
								'option_name' 	=> Multi_Rating::CUSTOM_IMAGES_SETTINGS,
								'setting_id' 	=> Multi_Rating::CUSTOM_STAR_IMAGE_HEIGHT,
								'label' 		=> __( 'pixels', 'multi-rating' ),
								'class'			=> 'small-text',
								'type'			=> 'number'
						)
				)
		);

		foreach ( $setting_fields as $setting_id => $setting_data ) {

			// $id, $title, $callback, $page, $section, $args
			add_settings_field( $setting_id, $setting_data['title'], array( $this, $setting_data['callback'] ), $setting_data['page'], $setting_data['section'], $setting_data['args'] );
		}
	}

	/**
	 * Custom images section description
	 */
	function section_custom_images_desc() {

	}

	/**
	 * Sanitize custom images settings
	 *
	 * @param $input
	 * @return string
	 */
	function sanitize_custom_images_settings( $input ) {

		if ( isset( $input[Multi_Rating::USE_CUSTOM_STAR_IMAGES] ) && $input[Multi_Rating::USE_CUSTOM_STAR_IMAGES] == 'true' ) {
			$input[Multi_Rating::USE_CUSTOM_STAR_IMAGES] = true;
		} else {
			$input[Multi_Rating::USE_CUSTOM_STAR_IMAGES] = false;
		}

		// make sure at least full, half and empty star images exist and are valid URL's
		if ( filter_var( $input[Multi_Rating::CUSTOM_FULL_STAR_IMAGE], FILTER_VALIDATE_URL ) === false ||
				filter_var( $input[Multi_Rating::CUSTOM_HALF_STAR_IMAGE], FILTER_VALIDATE_URL ) === false ||
				filter_var( $input[Multi_Rating::CUSTOM_EMPTY_STAR_IMAGE], FILTER_VALIDATE_URL ) === false ) {
			add_settings_error( Multi_Rating::CUSTOM_IMAGES_SETTINGS, 'validation_error_custom_images', __( 'Full star, half star and empty star custom images are required.', 'multi-rating' ), 'error' );
		}

		// check file types
		$valid_file_mime_types = array(
				'image/jpeg',
				'image/gif',
				'image/png',
				'image/bmp',
				'image/tiff',
				'image/x-icon'
		);

		if ( isset( $input[Multi_Rating::CUSTOM_FULL_STAR_IMAGE] ) ) {
			$file_mime_type = wp_check_filetype( $input[Multi_Rating::CUSTOM_FULL_STAR_IMAGE] );

			if ( ! in_array( $file_mime_type['type'], $valid_file_mime_types) ) {
				add_settings_error( Multi_Rating::CUSTOM_IMAGES_SETTINGS, 'invalid_mime_type', __( 'Invalid image format. Valid mime types: image/jpeg, image/png, image/bmp, image/tiff and image/x-icon', 'multi-rating' ), 'error' );
			}
		}

		// check image height and width are valid numbers within 1 and 128
		$custom_image_height = $input[Multi_Rating::CUSTOM_STAR_IMAGE_HEIGHT];
		$custom_image_width = $input[Multi_Rating::CUSTOM_STAR_IMAGE_WIDTH];

		if ( ! is_numeric( $custom_image_height) ) {
			add_settings_error( Multi_Rating::CUSTOM_IMAGES_SETTINGS, 'non_numeric_custom_image_height', __( 'Custom image height must be numeric.', 'multi-rating' ), 'error' );
		} else if ( intval( $custom_image_height ) < 1 || intval( $custom_image_height ) > 128 ) {
			add_settings_error( Multi_Rating::CUSTOM_IMAGES_SETTINGS, 'range_error_custom_image_height', __( 'Custom image height cannot be less than 1 or greater than 128.', 'multi-rating' ), 'error' );
		}

		if ( ! is_numeric($custom_image_width) ) {
			add_settings_error( Multi_Rating::CUSTOM_IMAGES_SETTINGS, 'non_numeric_custom_image_width', __( 'Custom image width must be numeric.', 'multi-rating' ), 'error' );
		} else if ( $custom_image_width < 1 || $custom_image_width > 128 ) {
			add_settings_error( Multi_Rating::CUSTOM_IMAGES_SETTINGS, 'range_error_custom_image_width', __( 'Custom image width cannot be less than 1 or greater than 128.', 'multi-rating' ), 'error' );
		}

		return $input;
	}

	/**
	 * Register custom text settings
	 */
	function register_custom_text_settings() {

		register_setting( Multi_Rating::CUSTOM_TEXT_SETTINGS, Multi_Rating::CUSTOM_TEXT_SETTINGS, array( &$this, 'sanitize_custom_text_settings' ) );

		add_settings_section( 'section_custom_text', __( 'Custom Text Settings', 'multi-rating' ), array( &$this, 'section_custom_text_desc' ), Multi_Rating::SETTINGS_PAGE_SLUG . '&setting=' . Multi_Rating::CUSTOM_TEXT_SETTINGS );

		$setting_fields = array(
				Multi_Rating::RATING_FORM_TITLE_TEXT_OPTION => array(
						'title' 	=> __( 'Rating Form Title', 'multi-rating' ),
						'callback' 	=> 'field_input',
						'page' 		=> Multi_Rating::SETTINGS_PAGE_SLUG . '&setting=' . Multi_Rating::CUSTOM_TEXT_SETTINGS,
						'section' 	=> 'section_custom_text',
						'args' => array(
								'option_name' 	=> Multi_Rating::CUSTOM_TEXT_SETTINGS,
								'setting_id' 	=> Multi_Rating::RATING_FORM_TITLE_TEXT_OPTION,
								'readonly' 		=> apply_filters( 'mr_disable_custom_text', false )
						)
				),
				Multi_Rating::RATING_RESULTS_LIST_TITLE_TEXT_OPTION => array(
						'title' 	=> __( 'Ratings List Title', 'multi-rating' ),
						'callback' 	=> 'field_input',
						'page' 		=> Multi_Rating::SETTINGS_PAGE_SLUG . '&setting=' . Multi_Rating::CUSTOM_TEXT_SETTINGS,
						'section' 	=> 'section_custom_text',
						'args' => array(
								'option_name' 	=> Multi_Rating::CUSTOM_TEXT_SETTINGS,
								'setting_id' 	=> Multi_Rating::RATING_RESULTS_LIST_TITLE_TEXT_OPTION,
								'readonly' 		=> apply_filters( 'mr_disable_custom_text', false )
						)
				),
				Multi_Rating::SUBMIT_RATING_FORM_BUTTON_TEXT_OPTION => array(
						'title' 	=> __( 'Submit Button Text', 'multi-rating' ),
						'callback' 	=> 'field_input',
						'page' 		=> Multi_Rating::SETTINGS_PAGE_SLUG . '&setting=' . Multi_Rating::CUSTOM_TEXT_SETTINGS,
						'section' 	=> 'section_custom_text',
						'args' => array(
								'option_name' 	=> Multi_Rating::CUSTOM_TEXT_SETTINGS,
								'setting_id' 	=> Multi_Rating::SUBMIT_RATING_FORM_BUTTON_TEXT_OPTION,
								'readonly' 		=> apply_filters( 'mr_disable_custom_text', false )
						)
				),
				Multi_Rating::FILTER_BUTTON_TEXT_OPTION => array(
						'title' 	=> __( 'Filter Button Text', 'multi-rating' ),
						'callback' 	=> 'field_input',
						'page' 		=> Multi_Rating::SETTINGS_PAGE_SLUG . '&setting=' . Multi_Rating::CUSTOM_TEXT_SETTINGS,
						'section' 	=> 'section_custom_text',
						'args' => array(
								'option_name' 	=> Multi_Rating::CUSTOM_TEXT_SETTINGS,
								'setting_id' 	=> Multi_Rating::FILTER_BUTTON_TEXT_OPTION,
								'readonly' 		=> apply_filters( 'mr_disable_custom_text', false )
						)
				),
				Multi_Rating::FILTER_LABEL_TEXT_OPTION => array(
						'title' 	=> __( 'Filter Label Text', 'multi-rating' ),
						'callback' 	=> 'field_input',
						'page' 		=> Multi_Rating::SETTINGS_PAGE_SLUG . '&setting=' . Multi_Rating::CUSTOM_TEXT_SETTINGS,
						'section' 	=> 'section_custom_text',
						'args' => array(
								'option_name' 	=> Multi_Rating::CUSTOM_TEXT_SETTINGS,
								'setting_id' 	=> Multi_Rating::FILTER_LABEL_TEXT_OPTION,
								'readonly' 		=> apply_filters( 'mr_disable_custom_text', false )
						)
				),
				Multi_Rating::FIELD_REQUIRED_ERROR_MESSAGE_OPTION => array(
						'title' 	=> __( 'Field Required Error Message', 'multi-rating' ),
						'callback' 	=> 'field_input',
						'page' 		=> Multi_Rating::SETTINGS_PAGE_SLUG . '&setting=' . Multi_Rating::CUSTOM_TEXT_SETTINGS,
						'section' 	=> 'section_custom_text',
						'args' => array(
								'option_name' 	=> Multi_Rating::CUSTOM_TEXT_SETTINGS,
								'setting_id' 	=> Multi_Rating::FIELD_REQUIRED_ERROR_MESSAGE_OPTION,
								'class'			=> 'large-text',
								'readonly' 		=> apply_filters( 'mr_disable_custom_text', false )
						)
				),
				Multi_Rating::SAVE_RATING_RESTRICTION_ERROR_MESSAGE_OPTION => array(
						'title' 	=> __( 'Duplicate Check Error Message', 'multi-rating' ),
						'callback' 	=> 'field_input',
						'page' 		=> Multi_Rating::SETTINGS_PAGE_SLUG . '&setting=' . Multi_Rating::CUSTOM_TEXT_SETTINGS,
						'section' 	=> 'section_custom_text',
						'args' => array(
								'option_name' 	=> Multi_Rating::CUSTOM_TEXT_SETTINGS,
								'setting_id' 	=> Multi_Rating::SAVE_RATING_RESTRICTION_ERROR_MESSAGE_OPTION,
								'class'			=> 'large-text',
								'readonly' 		=> apply_filters( 'mr_disable_custom_text', false )
						)
				),
				Multi_Rating::NO_RATING_RESULTS_TEXT_OPTION => array(
						'title' 	=> __( 'No Ratings Information Message', 'multi-rating' ),
						'callback' 	=> 'field_input',
						'page' 		=> Multi_Rating::SETTINGS_PAGE_SLUG . '&setting=' . Multi_Rating::CUSTOM_TEXT_SETTINGS,
						'section' 	=> 'section_custom_text',
						'args' => array(
								'option_name' 	=> Multi_Rating::CUSTOM_TEXT_SETTINGS,
								'setting_id' 	=> Multi_Rating::NO_RATING_RESULTS_TEXT_OPTION,
								'class'			=> 'large-text',
								'readonly' 		=> apply_filters( 'mr_disable_custom_text', false )
						)
				),
				Multi_Rating::RATING_FORM_SUBMIT_SUCCESS_MESSAGE_OPTION => array(
						'title' 	=> __( 'Submit Rating Success Message', 'multi-rating' ),
						'callback' 	=> 'field_input',
						'page' 		=> Multi_Rating::SETTINGS_PAGE_SLUG . '&setting=' . Multi_Rating::CUSTOM_TEXT_SETTINGS,
						'section' 	=> 'section_custom_text',
						'args' => array(
								'option_name' 	=> Multi_Rating::CUSTOM_TEXT_SETTINGS,
								'setting_id' 	=> Multi_Rating::RATING_FORM_SUBMIT_SUCCESS_MESSAGE_OPTION,
								'class'			=> 'large-text',
								'readonly' 		=> apply_filters( 'mr_disable_custom_text', false )
						)
				)
		);

		foreach ( $setting_fields as $setting_id => $setting_data ) {

			// $id, $title, $callback, $page, $section, $args
			add_settings_field( $setting_id, $setting_data['title'], array( $this, $setting_data['callback'] ), $setting_data['page'], $setting_data['section'], $setting_data['args'] );
		}
	}

	/**
	 * Custom text section description
	 */
	public function section_custom_text_desc() {

	}

	/**
	 * Sanitize custom text settings
	 *
	 * @param $input
	 * @return unknown
	 */
	function sanitize_custom_text_settings( $input ) {
		return $input;
	}


	/**
	 * Checkbox setting
	 */
	function field_checkbox( $args ) {
		$settings = (array) get_option( $args['option_name' ] );
		?>
		<input type="checkbox" name="<?php echo $args['option_name']; ?>[<?php echo $args['setting_id']; ?>]" value="true" <?php checked( true, isset( $settings[$args['setting_id']] ) ? $settings[$args['setting_id']] : false , true ); ?> />
		<?php
		if ( isset( $args['label'] ) ) { ?>
			<label><?php echo $args['label']; ?></label>
		<?php }
	}

	/**
	 * Checkbox setting
	 */
	function field_input( $args ) {
		$settings = (array) get_option( $args['option_name' ] );
		$class = isset( $args['class'] ) ? $args['class'] : 'regular-text';
		$type = isset( $args['type'] ) ? $args['type'] : 'text';
		$min = isset( $args['min'] ) && is_numeric( $args['min'] ) ? intval( $args['min'] ) : null;
		$max = isset( $args['max'] ) && is_numeric( $args['max'] ) ? intval( $args['max'] ) : null;
		$readonly = isset( $args['readonly'] ) && $args['readonly'] ? ' readonly' : '';
		?>
		<input class="<?php echo $class; ?>" type="<?php echo $type; ?>" name="<?php echo $args['option_name']; ?>[<?php echo $args['setting_id']; ?>]"
				value="<?php echo $settings[$args['setting_id']]; ?>" <?php if ( $min !== null ) { echo ' min="' . $min . '"'; } ?>
				<?php if ( $max !== null) { echo ' max="' . $max . '"'; } echo $readonly; ?>/>
		<?php
		if ( isset( $args['label'] ) ) { ?>
			<label><?php echo $args['label']; ?></label>
		<?php }
	}

	/**
	 * Upload setting
	 */
	function field_upload( $args ) {
		$settings = (array) get_option( $args['option_name' ] );
		$button_id = isset( $args['button_id'] ) ? $args['button_id'] : '';
		$input_id = isset( $args['input_id'] ) ? $args['input_id'] : '';
		$preview_img_id = isset( $args['preview_img_id'] ) ? $args['preview_img_id'] : '';

		?>
		<input type="url" id="<?php echo $input_id; ?>" name="<?php echo $args['option_name']; ?>[<?php echo $args['setting_id']; ?>]" value="<?php echo $settings[$args['setting_id']]; ?>" readonly class="regular-text" />
		<input type="submit" id="<?php echo $button_id; ?>" class="button" value="<?php _e( 'Upload', 'multi-rating' ); ?>">
		<img src="<?php if ( strlen( $settings[$args['setting_id']] ) > 0 ) echo $settings[$args['setting_id']]; ?>" id="<?php echo $preview_img_id; ?>" style="margin-top: 5px; <?php if ( strlen( $settings[$args['setting_id']] ) == 0 ) { echo 'display: none;'; } else { echo 'display: block;'; } ?>" />
		<?php
	}

	/**
	 *
	 */
	function field_textarea( $args ) {
		$settings = (array) get_option( $args['option_name' ] );

		if ( isset( $args['label'] ) ) { ?>
			<p><?php echo $args['label']; ?></p><br />
		<?php } ?>
		<textarea name="<?php echo $args['option_name']; ?>[<?php echo $args['setting_id']; ?>]" rows="5" cols="75"><?php echo $settings[$args['setting_id']]; ?></textarea>
		<?php
		if ( isset( $args['footer'] ) ) { ?>
			<p><?php echo $args['footer']; ?></p><br />
		<?php }
	}

	/**
	 * Editor field
	 *
	 * @param unknown $args
	 */
	function field_editor( $args ) {

		$settings = (array) get_option( $args['option_name' ] );

		if ( ! empty( $args['label' ] ) ) {
			?>
			<p><?php echo $args['label']; ?></p><br />
			<?php
		}

		wp_editor( $settings[$args['setting_id']], $args['setting_id'], array(
				'textarea_name' => $args['option_name' ] . '[' . $args['setting_id'] . ']',
				'editor_class' => ''
		) );

		echo ( ! empty( $args['footer'] ) ) ? '<br/><p class="description">' . $args['footer'] . '</p>' : '';
	}

	/**
	 * Color picker field
	 *
	 * @param unknown $args
	 */
	function field_color_picker( $args ) {
		$settings = (array) get_option( $args['option_name' ] );
		?>
		<input type="text" class="color-picker" name="<?php echo $args['option_name']; ?>[<?php echo $args['setting_id']; ?>]" value="<?php echo $settings[$args['setting_id']]; ?>" />
		<?php if ( isset( $args['label' ] ) ) { ?>
			<p><?php echo $args['label']; ?></p>
		<?php }
	}

	/**
	 * Color picker field
	 *
	 * @param unknown $args
	 */
	function field_select( $args ) {
		$settings = (array) get_option( $args['option_name' ] );
		$value = $settings[$args['setting_id']];
		?>
		<select name="<?php echo $args['option_name']; ?>[<?php echo $args['setting_id']; ?>]">
			<?php
			foreach ( $args['select_options'] as $option_value => $option_label ) {
				$selected = '';
				if ( $value == $option_value ) {
					$selected = 'selected="selected"';
				}
				echo '<option value="' . $option_value . '" ' . $selected . '>' . $option_label . '</option>';
			}
			?>
		</select>
		<?php
		if ( isset( $args['label'] ) ) { ?>
			<label><?php echo $args['label']; ?></label>
		<?php }
	}

	/**
	 * Checkboxes field
	 *
	 * @param unknown $args
	*/
	function field_checkboxes( $args ) {
		$settings = (array) get_option( $args['option_name' ] );
		$value = $settings[$args['setting_id']];

		foreach ( $args['checkboxes'] as $checkbox ) {

			$checked = '';
			if ( is_array( $value ) ) {
				if ( in_array( $checkbox['name'], $value ) ) {
					$checked = 'checked="checked"';
				}
			} else if ( $checkbox['name'] == $value ) {
				$checked = 'checked="checked"';
			}

			?>
			<input type="checkbox" name="<?php echo $args['option_name']; ?>[<?php echo $args['setting_id']; ?>][]" value="<?php echo $checkbox['name']; ?>" <?php echo $checked; ?> />
			<label class="checkbox-label"><?php echo $checkbox['label']; ?></label>
			<?php
		}

		if ( isset( $args['description'] ) ) {
			?>
			<p><?php echo $args['description']; ?></p>
			<?php
		}
	}
}
?>
