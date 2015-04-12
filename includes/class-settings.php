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
	
		$this->register_custom_text_settings();
		$this->register_style_settings();
		$this->register_general_settings();
		$this->register_position_settings();
	
	}
	
	/**
	 * Retrieve settings and applies default option values if not set
	 */
	function load_settings() {
	
		$this->style_settings 			= (array) get_option( Multi_Rating::STYLE_SETTINGS );
		$this->custom_text_settings 	= (array) get_option( Multi_Rating::CUSTOM_TEXT_SETTINGS );
		$this->position_settings 		= (array) get_option( Multi_Rating::POSITION_SETTINGS );
		$this->general_settings 		= (array) get_option( Multi_Rating::GENERAL_SETTINGS );
	
		$default_css = addslashes(".rating-results-list .rank { font-weight: bold; }");
	
	
		// Merge with defaults
		$this->style_settings = array_merge( array(
				Multi_Rating::CUSTOM_CSS_OPTION 				=> $default_css,
				Multi_Rating::STAR_RATING_COLOUR_OPTION 		=> '#ffd700',
				Multi_Rating::STAR_RATING_HOVER_COLOUR_OPTION 	=> '#ffba00',
				Multi_Rating::INCLUDE_FONT_AWESOME_OPTION 		=> true,
				Multi_Rating::FONT_AWESOME_VERSION_OPTION 		=> '4.0.3',
				Multi_Rating::USE_CUSTOM_STAR_IMAGES			=> false,
				Multi_Rating::CUSTOM_FULL_STAR_IMAGE			=> '',
				Multi_Rating::CUSTOM_HALF_STAR_IMAGE			=> '',
				Multi_Rating::CUSTOM_EMPTY_STAR_IMAGE			=> '',
				Multi_Rating::CUSTOM_HOVER_STAR_IMAGE			=> '',
				Multi_Rating::CUSTOM_STAR_IMAGE_WIDTH			=> 32,
				Multi_Rating::CUSTOM_STAR_IMAGE_HEIGHT			=> 32,
				Multi_Rating::ERROR_MESSAGE_COLOUR_OPTION		=> '#EC6464'
		), $this->style_settings );
	
	
		$this->position_settings = array_merge( array(
				Multi_Rating::RATING_RESULTS_POSITION_OPTION 	=> 'after_title',
				Multi_Rating::RATING_FORM_POSITION_OPTION 		=> 'after_content'
		), $this->position_settings );
	
	
		$this->custom_text_settings = array_merge( array(
				Multi_Rating::CHAR_ENCODING_OPTION => '',
				Multi_Rating::RATING_FORM_TITLE_TEXT_OPTION 			=> __( 'Please rate this', 'multi-rating' ),
				Multi_Rating::RATING_RESULTS_LIST_TITLE_TEXT_OPTION 	=> __( 'Rating Results', 'multi-rating' ),
				Multi_Rating::SUBMIT_RATING_FORM_BUTTON_TEXT_OPTION		=> __( 'Submit Rating', 'multi-rating' ),
				Multi_Rating::FILTER_BUTTON_TEXT_OPTION					=> __( 'Filter', 'multi-rating' ),
				Multi_Rating::FILTER_LABEL_TEXT_OPTION					=> __( 'Category', 'multi-rating' ),
				Multi_Rating::RATING_FORM_SUBMIT_SUCCESS_MESSAGE_OPTION => __( 'Your rating was %adjusted_star_result%/5.', 'multi-rating'),
				Multi_Rating::SAVE_RATING_RESTRICTION_ERROR_MESSAGE_OPTION => __( 'You cannot submit a rating form for the same post multiple times.', 'multi-rating' ),
				Multi_Rating::NO_RATING_RESULTS_TEXT_OPTION 			=> __( 'No ratings yet.', 'multi-rating' ),
				Multi_Rating::FIELD_REQUIRED_ERROR_MESSAGE_OPTION		=> __( 'Field is required.', 'multi-rating' )
		), $this->custom_text_settings );
	
	
		$this->general_settings = array_merge( array(
				Multi_Rating::SAVE_RATING_RESTRICTION_TYPES_OPTION		=> array( 'ip_address' ),
				Multi_Rating::SAVE_RATING_RESTRICTION_HOURS_OPTION		=> 24,
				Multi_Rating::POST_TYPES_OPTION 						=> 'post',
				Multi_Rating::RATING_RESULTS_CACHE_OPTION				=> true,
				Multi_Rating::HIDE_RATING_FORM_AFTER_SUBMIT_OPTION 		=> true,
				Multi_Rating::DEFAULT_HIDE_POST_META_BOX_OPTION			=> false,
				Multi_Rating::TEMPLATE_STRIP_NEWLINES_OPTION			=> false
		), $this->general_settings );
	
	
		update_option( Multi_Rating::STYLE_SETTINGS, $this->style_settings);
		update_option( Multi_Rating::POSITION_SETTINGS, $this->position_settings);
		update_option( Multi_Rating::CUSTOM_TEXT_SETTINGS, $this->custom_text_settings);
		update_option( Multi_Rating::GENERAL_SETTINGS, $this->general_settings);
	}

	/**
	 * Register general settings
	 */
	function register_general_settings() {
		
		register_setting( Multi_Rating::GENERAL_SETTINGS, Multi_Rating::GENERAL_SETTINGS, array( &$this, 'sanitize_general_settings' ) );
	
		add_settings_section( 'section_general', __( 'General Settings', 'multi-rating' ), array( &$this, 'section_general_desc' ), Multi_Rating::GENERAL_SETTINGS );
	
		add_settings_field( Multi_Rating::POST_TYPES_OPTION, __( 'Enabled Post Types', 'multi-rating' ), array( &$this, 'field_post_types' ), Multi_Rating::GENERAL_SETTINGS, 'section_general' );
		add_settings_field( Multi_Rating::SAVE_RATING_RESTRICTION_TYPES_OPTION, __( 'Rating Restriction', 'multi-rating' ), array( &$this, 'field_save_rating_restriction' ), Multi_Rating::GENERAL_SETTINGS, 'section_general' );
		add_settings_field( Multi_Rating::RATING_RESULTS_CACHE_OPTION, __( 'Rating Results Cache', 'multi-rating' ), array( &$this, 'field_rating_results_cache' ), Multi_Rating::GENERAL_SETTINGS, 'section_general' );
		add_settings_field( Multi_Rating::HIDE_RATING_FORM_AFTER_SUBMIT_OPTION, __( 'Hide Rating Form', 'multi-rating' ), array( &$this, 'field_hide_rating_form_after_submit' ), Multi_Rating::GENERAL_SETTINGS, 'section_general' );
		add_settings_field( Multi_Rating::DEFAULT_HIDE_POST_META_BOX_OPTION, __( 'Hide Multi Rating Post Meta Box?', 'multi-rating' ), array( &$this, 'field_hide_post_meta_box' ), Multi_Rating::GENERAL_SETTINGS, 'section_general' );
		add_settings_field( Multi_Rating::TEMPLATE_STRIP_NEWLINES_OPTION, __( 'Template Strip Newlines?', 'multi-rating' ), array( &$this, 'field_template_strip_newlines' ), Multi_Rating::GENERAL_SETTINGS, 'section_general' );
		
	}
	
	/**
	 * General section desciption
	 */
	function section_general_desc() {
	}
	
	/**
	 * Save rating restriction
	 */
	function field_save_rating_restriction() {
		
		$save_rating_restrictions_types = array(
				'ip_address' => __( 'IP Address', 'multi-rating' ), 
				'cookie' => __( 'Cookie', 'multi-rating'
		) );

		$save_rating_restriction_types_checked = $this->general_settings[Multi_Rating::SAVE_RATING_RESTRICTION_TYPES_OPTION];
		foreach ( $save_rating_restrictions_types as $save_rating_restrictions_type => $save_rating_restrictions_label) {
			echo '<input type="checkbox" name="' . Multi_Rating::GENERAL_SETTINGS . '[' . Multi_Rating::SAVE_RATING_RESTRICTION_TYPES_OPTION . '][]" value="' . $save_rating_restrictions_type . '"';
			if ( is_array($save_rating_restriction_types_checked ) ) {
				if ( in_array($save_rating_restrictions_type, $save_rating_restriction_types_checked)) {
					echo 'checked="checked"';
				}
			} else {
				checked( $save_rating_restrictions_type, $save_rating_restriction_types_checked, true );
			}
			echo ' />&nbsp;<label class="checkbox-label">' . $save_rating_restrictions_label . '</label><br />';
		}
		?>
		<label><?php _e('Hours', 'multi-rating'); ?></label>&nbsp;<input class="small-text" type="number" min="1" name="<?php echo Multi_Rating::GENERAL_SETTINGS; ?>[<?php echo Multi_Rating::SAVE_RATING_RESTRICTION_HOURS_OPTION; ?>]" value="<?php echo $this->general_settings[Multi_Rating::SAVE_RATING_RESTRICTION_HOURS_OPTION]; ?>" />
		<p><?php _e( 'Restrict saving a rating form for the same post multiple times.', 'multi-rating' ); ?></p>
		
		<?php 
	}
	/**
	 * Post types enabled setting
	 */
	function field_post_types() {
		$post_types = get_post_types( '', 'names' );
		$post_types_checked = $this->general_settings[Multi_Rating::POST_TYPES_OPTION];
	
		foreach ( $post_types as $post_type ) {
			echo '<input type="checkbox" name="' . Multi_Rating::GENERAL_SETTINGS . '[' . Multi_Rating::POST_TYPES_OPTION . '][]" value="' . $post_type . '"';
			if ( is_array( $post_types_checked ) ) {
				if ( in_array( $post_type, $post_types_checked ) ) {
					echo 'checked="checked"';
				}
			} else {
				checked( $post_type, $post_types_checked, true );
			}
			echo ' />&nbsp;<label class="checkbox-label">' . $post_type . '</label>';
		}
	}
	/**
	 * Rating results cache
	 */
	function field_rating_results_cache() {
	?>
		<input type="checkbox" name="<?php echo Multi_Rating::GENERAL_SETTINGS;?>[<?php echo Multi_Rating::RATING_RESULTS_CACHE_OPTION; ?>]" value="true" <?php checked(true, $this->general_settings[Multi_Rating::RATING_RESULTS_CACHE_OPTION], true); ?> />
		<label><?php printf( __( 'Enable the rating results to be cached in the WordPress post meta table. The cache is refreshed whenever the rating form is submitted. You can also use the <a href="admin.php?page=%s">Tools</a> to clear the rating results cache.', 'multi-rating' ), Multi_Rating::TOOLS_PAGE_SLUG ); ?></label>
		<?php 
	}
	
	/**
	 * Hide rating form after submit
	 */
	function field_hide_rating_form_after_submit() {
		?>
		<input type="checkbox" name="<?php echo Multi_Rating::GENERAL_SETTINGS;?>[<?php echo Multi_Rating::HIDE_RATING_FORM_AFTER_SUBMIT_OPTION; ?>]" value="true" <?php checked( true, $this->general_settings[Multi_Rating::HIDE_RATING_FORM_AFTER_SUBMIT_OPTION], true ); ?> />
		<?php 
	}
	
	/**
	 * Template strip newlines
	 */
	function field_template_strip_newlines() {
		?>
		<input type="checkbox" name="<?php echo Multi_Rating::GENERAL_SETTINGS;?>[<?php echo Multi_Rating::TEMPLATE_STRIP_NEWLINES_OPTION; ?>]" value="true" <?php checked(true, $this->general_settings[Multi_Rating::TEMPLATE_STRIP_NEWLINES_OPTION], true); ?> />
		<label><?php printf( __( 'Some plugins convert newlines to HTML paragraphs similar to <a href="%s">wpautop</a> (e.g. Visual Composer). Turn this option on if you want to prevent this from happening by stripping the newlines from the Multi Rating templates prior to display. This has no effect on the presentation.', 'multi-rating' ), 'https://codex.wordpress.org/Function_Reference/wpautop' ); ?></label>
		<?php 
	}
	
	/**
	 * Default visibility of the Multi Rating post meta box
	 */
	function field_hide_post_meta_box() {
		?>
		<input type="checkbox" name="<?php echo Multi_Rating::GENERAL_SETTINGS;?>[<?php echo Multi_Rating::DEFAULT_HIDE_POST_META_BOX_OPTION; ?>]" value="true" <?php checked(true, $this->general_settings[Multi_Rating::DEFAULT_HIDE_POST_META_BOX_OPTION], true); ?> />
		<label><?php _e( 'Do you want the Multi Rating post meta box to be hidden by default. You can set the meta box to visible in the Screen Options.', 'multi-rating' ); ?></label>
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
		
		// default hide post meta box
		if ( isset( $input[Multi_Rating::DEFAULT_HIDE_POST_META_BOX_OPTION] )
				&& $input[Multi_Rating::DEFAULT_HIDE_POST_META_BOX_OPTION] == 'true' ) {
			$input[Multi_Rating::DEFAULT_HIDE_POST_META_BOX_OPTION] = true;
		} else {
			$input[Multi_Rating::DEFAULT_HIDE_POST_META_BOX_OPTION] = false;
		}
	
		return $input;
	}
	
	/**
	 * Register position settings
	 */
	function register_position_settings() {
		register_setting( Multi_Rating::POSITION_SETTINGS, Multi_Rating::POSITION_SETTINGS, array( &$this, 'sanitize_position_settings' ) );
	
		add_settings_section( 'section_position', __( 'Auto Placement Settings', 'multi-rating' ), array( &$this, 'section_position_desc' ), Multi_Rating::POSITION_SETTINGS );
	
		add_settings_field( Multi_Rating::RATING_RESULTS_POSITION_OPTION, __( 'Rating Results Position', 'multi-rating' ), array( &$this, 'field_rating_results_position' ), Multi_Rating::POSITION_SETTINGS, 'section_position' );
		add_settings_field( Multi_Rating::RATING_FORM_POSITION_OPTION, __( 'Rating Form Position', 'multi-rating' ), array( &$this, 'field_rating_form_position' ), Multi_Rating::POSITION_SETTINGS, 'section_position' );
	}
	
	/**
	 * Position section description
	 */
	function section_position_desc() {
		?>
		<p class="description"><?php _e( 'These settings allow you to automatically place the rating form and rating results on every post or page in default positions.', 'multi-rating' ); ?></p>
		<?php
	}
	
	/**
	 * Rating results auto placement setting
	 */
	function field_rating_results_position() {
		?>
		<select name="<?php echo Multi_Rating::POSITION_SETTINGS; ?>[<?php echo Multi_Rating::RATING_RESULTS_POSITION_OPTION; ?>]">
			<option value="" <?php selected( '', $this->position_settings[Multi_Rating::RATING_RESULTS_POSITION_OPTION], true ); ?>><?php _e( 'None', 'multi-rating' ); ?></option>
			<option value="before_title" <?php selected( 'before_title', $this->position_settings[Multi_Rating::RATING_RESULTS_POSITION_OPTION], true ); ?>><?php _e( 'Before title', 'multi-rating' ); ?></option>
			<option value="after_title" <?php selected( 'after_title', $this->position_settings[Multi_Rating::RATING_RESULTS_POSITION_OPTION], true ); ?>><?php _e( 'After title', 'multi-rating' ); ?></option>
		</select>
		<label><?php _e( 'Choose to automatically display the rating result before or after the post title for all enabled post types.', 'multi-rating' ); ?></label>
		<?php
	}
	
	/**
	 * Rating form auto placement settings
	 */
	function field_rating_form_position() {
		?>
		<select name="<?php echo Multi_Rating::POSITION_SETTINGS; ?>[<?php echo Multi_Rating::RATING_FORM_POSITION_OPTION; ?>]">
			<option value="" <?php selected( '', $this->position_settings[Multi_Rating::RATING_FORM_POSITION_OPTION], true); ?>><?php _e( 'None', 'multi-rating' ); ?></option>
			<option value="before_content" <?php selected('before_content', $this->position_settings[Multi_Rating::RATING_FORM_POSITION_OPTION], true); ?>><?php _e( 'Before content', 'multi-rating' ); ?></option>
			<option value="after_content" <?php selected('after_content', $this->position_settings[Multi_Rating::RATING_FORM_POSITION_OPTION], true); ?>><?php _e( 'After content', 'multi-rating' ); ?></option>
		</select>
		<label><?php _e( 'Choose to automatically display the rating form before or after the post content for all enabled post types.', 'multi-rating' ); ?></label>
		<?php
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
	
		add_settings_section( 'section_style', __( 'Style Settings', 'multi-rating' ), array( &$this, 'section_style_desc' ), Multi_Rating::STYLE_SETTINGS );

		add_settings_field( Multi_Rating::CUSTOM_CSS_OPTION, __( 'Custom CSS', 'multi-rating' ), array( &$this, 'field_custom_css' ), Multi_Rating::STYLE_SETTINGS, 'section_style' );
		add_settings_field( Multi_Rating::STAR_RATING_COLOUR_OPTION, __( 'Star Rating Color', 'multi-rating' ), array( &$this, 'field_star_rating_colour' ), Multi_Rating::STYLE_SETTINGS, 'section_style' );
		add_settings_field( Multi_Rating::STAR_RATING_HOVER_COLOUR_OPTION, __( 'Star Rating Hover Color', 'multi-rating' ), array( &$this, 'field_star_rating_hover_colour' ), Multi_Rating::STYLE_SETTINGS, 'section_style' );
		add_settings_field( Multi_Rating::ERROR_MESSAGE_COLOUR_OPTION, __( 'Error Message Color', 'multi-rating' ), array( &$this, 'field_error_message_colour' ), Multi_Rating::STYLE_SETTINGS, 'section_style' );
		add_settings_field( Multi_Rating::INCLUDE_FONT_AWESOME_OPTION, __( 'Load Font Awesome Library?', 'multi-rating' ), array( &$this, 'field_include_font_awesome' ), Multi_Rating::STYLE_SETTINGS, 'section_style' );
		add_settings_field( Multi_Rating::FONT_AWESOME_VERSION_OPTION, __( 'Font Awesome Version', 'multi-rating' ), array( &$this, 'field_font_awesome_version' ), Multi_Rating::STYLE_SETTINGS, 'section_style' );
		add_settings_field( Multi_Rating::USE_CUSTOM_STAR_IMAGES, __( 'Use Custom Star Images', 'multi-rating' ), array( &$this, 'field_use_custom_star_images' ), Multi_Rating::STYLE_SETTINGS, 'section_style' );
	}
	
	/**
	 * Style section description
	 */
	function section_style_desc() {
	}
	
	/**
	 * Include plugin loading Font Awesome CSS
	 */
	function field_include_font_awesome() {
		?>
		<input type="checkbox" name="<?php echo Multi_Rating::STYLE_SETTINGS; ?>[<?php echo Multi_Rating::INCLUDE_FONT_AWESOME_OPTION; ?>]" value="true" <?php checked(true, $this->style_settings[Multi_Rating::INCLUDE_FONT_AWESOME_OPTION], true); ?> />
		<label><?php _e( 'Do you want the plugin to include loading of the Font Awesome CSS?', 'multi-rating' ); ?></label>
		<?php
	}
	
	/**
	 * Use custom star images
	 */
	function field_use_custom_star_images() {
		?>
		<input type="checkbox" id="use-custom-star-images" name="<?php echo Multi_Rating::STYLE_SETTINGS; ?>[<?php echo Multi_Rating::USE_CUSTOM_STAR_IMAGES; ?>]" value="true" <?php checked(true, $this->style_settings[Multi_Rating::USE_CUSTOM_STAR_IMAGES], true); ?> />
		<label><?php _e( 'You can upload your own star images to use instead of the using the default Font Awesome star icons.', 'multi-rating' ); ?></label>

		<div id="custom-star-images-details" <?php 
		if ( $this->style_settings[Multi_Rating::USE_CUSTOM_STAR_IMAGES] == false ) {
		 echo ' class="hidden"';
		}
		?>>
			<br />		
			<table>
				<tbody>
					<tr>
						<td style="padding-left: 0px !important;"><label for="custom-full-star-img"><?php _e( 'Full Star', 'multi-rating'); ?></label></td>
						<td><input type="url" id="custom-full-star-img" name="<?php echo Multi_Rating::STYLE_SETTINGS; ?>[<?php echo Multi_Rating::CUSTOM_FULL_STAR_IMAGE; ?>]" value="<?php echo $this->style_settings[Multi_Rating::CUSTOM_FULL_STAR_IMAGE]; ?>" readonly class="regular-text" /></td>
						<td><input type="submit" name="custom-full-star-img-upload-btn" id="custom-full-star-img-upload-btn" class="button" value="<?php _e('Upload', 'multi-rating' ); ?>"></td>
						<td><img src="<?php if ( strlen( $this->style_settings[Multi_Rating::CUSTOM_FULL_STAR_IMAGE] ) > 0 ) echo $this->style_settings[Multi_Rating::CUSTOM_FULL_STAR_IMAGE]; ?>" id="custom-full-star-img-preview" 
								width="<?php echo $this->style_settings[Multi_Rating::CUSTOM_STAR_IMAGE_WIDTH]; ?>px" height="<?php echo $this->style_settings[Multi_Rating::CUSTOM_STAR_IMAGE_HEIGHT]; ?>px";"/></td>
					</tr>
					<tr>
						<td style="padding-left: 0px !important;"><label for="custom-half-star-img"><?php _e( 'Half Star', 'multi-rating'); ?></label></td>
						<td><input type="url" id="custom-half-star-img" name="<?php echo Multi_Rating::STYLE_SETTINGS; ?>[<?php echo Multi_Rating::CUSTOM_HALF_STAR_IMAGE; ?>]" value="<?php echo $this->style_settings[Multi_Rating::CUSTOM_HALF_STAR_IMAGE]; ?>" readonly class="regular-text" /></td>
						<td><input type="submit" name="custom-half-star-img-upload-btn" id="custom-half-star-img-upload-btn" class="button" value="<?php _e('Upload', 'multi-rating' ); ?>"></td>
						<td><img src="<?php if ( strlen( $this->style_settings[Multi_Rating::CUSTOM_HALF_STAR_IMAGE] ) > 0 ) echo $this->style_settings[Multi_Rating::CUSTOM_HALF_STAR_IMAGE]; ?>" id="custom-half-star-img-preview" 
								width="<?php echo $this->style_settings[Multi_Rating::CUSTOM_STAR_IMAGE_WIDTH]; ?>px" height="<?php echo $this->style_settings[Multi_Rating::CUSTOM_STAR_IMAGE_HEIGHT]; ?>px";"/></td>						
					</tr>
					<tr>
						<td style="padding-left: 0px !important;"><label for="custom-empty-star-img"><?php _e( 'Empty Star', 'multi-rating'); ?></label></td>
						<td><input type="url" id="custom-empty-star-img" name="<?php echo Multi_Rating::STYLE_SETTINGS; ?>[<?php echo Multi_Rating::CUSTOM_EMPTY_STAR_IMAGE; ?>]" value="<?php echo $this->style_settings[Multi_Rating::CUSTOM_EMPTY_STAR_IMAGE]; ?>" readonly class="regular-text" /></td>
						<td><input type="submit" name="custom-empty-star-img-upload-btn" id="custom-empty-star-img-upload-btn" class="button" value="<?php _e('Upload', 'multi-rating' ); ?>"></td>
						<td><img src="<?php if ( strlen( $this->style_settings[Multi_Rating::CUSTOM_EMPTY_STAR_IMAGE] ) > 0 ) echo $this->style_settings[Multi_Rating::CUSTOM_EMPTY_STAR_IMAGE]; ?>" id="custom-empty-star-img-preview" 
								width="<?php echo $this->style_settings[Multi_Rating::CUSTOM_STAR_IMAGE_WIDTH]; ?>px" height="<?php echo $this->style_settings[Multi_Rating::CUSTOM_STAR_IMAGE_HEIGHT]; ?>px";"/></td>
					</tr>
					<tr>
						<td style="padding-left: 0px !important;"><label for="custom-hover-star-img"><?php _e( 'Hover Star', 'multi-rating'); ?></label></td>
						<td><input type="url" id="custom-hover-star-img" name="<?php echo Multi_Rating::STYLE_SETTINGS; ?>[<?php echo Multi_Rating::CUSTOM_HOVER_STAR_IMAGE; ?>]" value="<?php echo $this->style_settings[Multi_Rating::CUSTOM_HOVER_STAR_IMAGE]; ?>" readonly class="regular-text" /></td>
						<td><input type="submit" name="custom-hover-star-img-upload-btn" id="custom-hover-star-img-upload-btn" class="button" value="<?php _e('Upload', 'multi-rating' ); ?>"></td>
						<td><img src="<?php if ( strlen( $this->style_settings[Multi_Rating::CUSTOM_HOVER_STAR_IMAGE] ) > 0 ) echo $this->style_settings[Multi_Rating::CUSTOM_HOVER_STAR_IMAGE]; ?>" id="custom-hover-star-img-preview" 
								width="<?php echo $this->style_settings[Multi_Rating::CUSTOM_STAR_IMAGE_WIDTH]; ?>px" height="<?php echo $this->style_settings[Multi_Rating::CUSTOM_STAR_IMAGE_HEIGHT]; ?>px";"/></td>
					</tr>
				</tbody>
			</table>
			
			<table>
				<tbody>
					<tr>
						<td style="padding-left: 0px !important;"><label for="custom-star-img-width"><?php _e( 'Image width', 'multi-rating' ); ?></label></td>
						<td><input type="number" min="1" max="128" size="3" maxlength="3" id="custom-star-img-width" name="<?php echo Multi_Rating::STYLE_SETTINGS; ?>[<?php echo Multi_Rating::CUSTOM_STAR_IMAGE_WIDTH; ?>]" value="<?php echo $this->style_settings[Multi_Rating::CUSTOM_STAR_IMAGE_WIDTH]; ?>" class="small-text" />&nbsp;<?php _e( 'pixels', 'multi-rating'); ?></td>		
						<td><label for="custom-star-img-height"><?php _e( 'Image height', 'multi-rating' ); ?></label></td>
						<td><input type="number" min="1" max="128" size="3" maxlength="3" id="custom-star-img-height" name="<?php echo Multi_Rating::STYLE_SETTINGS; ?>[<?php echo Multi_Rating::CUSTOM_STAR_IMAGE_HEIGHT; ?>]" value="<?php echo $this->style_settings[Multi_Rating::CUSTOM_STAR_IMAGE_HEIGHT]; ?>" class="small-text" />&nbsp;<?php _e( 'pixels', 'multi-rating'); ?></td>	
					</tr>
				</tbody>
			</table>
			
			<br />
			<p><?php _e( 'Each image must be one star of the same size. Valid mime types are image/jpeg, image/png, image/bmp, image/tiff and image/x-icon.', 'multi-rating-pro' ); ?><br />
			<?php _e('Preview e.g. 2.5/5:', 'multi-rating'); ?>
				<img src="<?php echo $this->style_settings[Multi_Rating::CUSTOM_FULL_STAR_IMAGE]; ?>" width="<?php echo $this->style_settings[Multi_Rating::CUSTOM_STAR_IMAGE_WIDTH]; ?>px" height="<?php echo $this->style_settings[Multi_Rating::CUSTOM_STAR_IMAGE_HEIGHT]; ?>px"/>
				<img src="<?php echo $this->style_settings[Multi_Rating::CUSTOM_FULL_STAR_IMAGE]; ?>" width="<?php echo $this->style_settings[Multi_Rating::CUSTOM_STAR_IMAGE_WIDTH]; ?>px" height="<?php echo $this->style_settings[Multi_Rating::CUSTOM_STAR_IMAGE_HEIGHT]; ?>px"/>
				<img src="<?php echo $this->style_settings[Multi_Rating::CUSTOM_HALF_STAR_IMAGE]; ?>" width="<?php echo $this->style_settings[Multi_Rating::CUSTOM_STAR_IMAGE_WIDTH]; ?>px" height="<?php echo $this->style_settings[Multi_Rating::CUSTOM_STAR_IMAGE_HEIGHT]; ?>px"/>
				<img src="<?php echo $this->style_settings[Multi_Rating::CUSTOM_EMPTY_STAR_IMAGE]; ?>" width="<?php echo $this->style_settings[Multi_Rating::CUSTOM_STAR_IMAGE_WIDTH]; ?>px" height="<?php echo $this->style_settings[Multi_Rating::CUSTOM_STAR_IMAGE_HEIGHT]; ?>px"/>
				<img src="<?php echo $this->style_settings[Multi_Rating::CUSTOM_EMPTY_STAR_IMAGE]; ?>" width="<?php echo $this->style_settings[Multi_Rating::CUSTOM_STAR_IMAGE_WIDTH]; ?>px" height="<?php echo $this->style_settings[Multi_Rating::CUSTOM_STAR_IMAGE_HEIGHT]; ?>px"/>
			</p>
		</div>
		<p><?php printf( __( '<a href="%1$s" target="_blank">Learn how to setup your own Custom Star Rating images.</a>', 'multi-rating' ), 'http://danielpowney.com/docs/add-custom-star-rating-images/' ); ?></p>
		<?php
	}
	
	/**
	 * Which version of Font Awesome to use
	 */
	function field_font_awesome_version() {
		?>
		<select name="<?php echo Multi_Rating::STYLE_SETTINGS; ?>[<?php echo Multi_Rating::FONT_AWESOME_VERSION_OPTION; ?>]">
			<option value="4.3.0" <?php selected( '4.3.0', $this->style_settings[Multi_Rating::FONT_AWESOME_VERSION_OPTION], true); ?>>4.3.0</option>
			<option value="4.2.0" <?php selected( '4.2.0', $this->style_settings[Multi_Rating::FONT_AWESOME_VERSION_OPTION], true); ?>>4.2.0</option>
			<option value="4.1.0" <?php selected( '4.1.0', $this->style_settings[Multi_Rating::FONT_AWESOME_VERSION_OPTION], true); ?>>4.1.0</option>
			<option value="4.0.3" <?php selected( '4.0.3', $this->style_settings[Multi_Rating::FONT_AWESOME_VERSION_OPTION], true); ?>>4.0.3</option>
			<option value="3.2.1" <?php selected( '3.2.1', $this->style_settings[Multi_Rating::FONT_AWESOME_VERSION_OPTION], true); ?>>3.2.1</option>
		</select>
		<?php
	}
	
	/**
	 * Customer CSS settings
	 */
	function field_custom_css() {
		?>
		<textarea cols="50" rows="10" class="large-text" name="<?php echo Multi_Rating::STYLE_SETTINGS; ?>[<?php echo Multi_Rating::CUSTOM_CSS_OPTION; ?>]"><?php echo stripslashes($this->style_settings[Multi_Rating::CUSTOM_CSS_OPTION]); ?></textarea>
		<?php 
	}	
	
	/**
	 * Star rating colour setting
	 */
	function field_star_rating_colour() {	
		$star_rating_colour = $this->style_settings[Multi_Rating::STAR_RATING_COLOUR_OPTION];
		?>
   	 	<input class="color-picker" type="text" id="mr-star-rating-colour" name="<?php echo Multi_Rating::STYLE_SETTINGS; ?>[<?php echo Multi_Rating::STAR_RATING_COLOUR_OPTION; ?>]; ?>" value="<?php echo $star_rating_colour; ?>" />
		<?php 
	}
	
	/**
	 * Star rating on hover colour
	 */
	function field_star_rating_hover_colour() {
		$star_rating_hover_colour = $this->style_settings[Multi_Rating::STAR_RATING_HOVER_COLOUR_OPTION];
		?>
	 	 	<input class="color-picker" type="text" id="mr-star-rating-hover-colour" name="<?php echo Multi_Rating::STYLE_SETTINGS; ?>[<?php echo Multi_Rating::STAR_RATING_HOVER_COLOUR_OPTION; ?>]; ?>" value="<?php echo $star_rating_hover_colour; ?>" />
		<?php 
	}
	
	/**
	 * Error message colour
	 */
	function field_error_message_colour() {
		$error_message_colour = $this->style_settings[Multi_Rating::ERROR_MESSAGE_COLOUR_OPTION];
		?>
		<input class="color-picker" type="text" id="mr-error-message-colour" name="<?php echo Multi_Rating::STYLE_SETTINGS; ?>[<?php echo Multi_Rating::ERROR_MESSAGE_COLOUR_OPTION; ?>]; ?>" value="<?php echo $error_message_colour; ?>" />
		<?php 
	}
	
	/**
	 * Sanitize style settings
	 * 
	 * @param $input
	 * @return string
	 */
	function sanitize_style_settings( $input ) {
		
		$input[Multi_Rating::CUSTOM_CSS_OPTION] = addslashes($input[Multi_Rating::CUSTOM_CSS_OPTION]);
		
		if ( isset( $input[Multi_Rating::INCLUDE_FONT_AWESOME_OPTION] ) && $input[Multi_Rating::INCLUDE_FONT_AWESOME_OPTION] == 'true' ) {
			$input[Multi_Rating::INCLUDE_FONT_AWESOME_OPTION] = true;
		} else {
			$input[Multi_Rating::INCLUDE_FONT_AWESOME_OPTION] = false;
		}
		
		if ( isset( $input[Multi_Rating::USE_CUSTOM_STAR_IMAGES] ) && $input[Multi_Rating::USE_CUSTOM_STAR_IMAGES] == 'true' ) {
			$input[Multi_Rating::USE_CUSTOM_STAR_IMAGES] = true;
		} else {
			$input[Multi_Rating::USE_CUSTOM_STAR_IMAGES] = false;
		}
		
		if ( $input[Multi_Rating::USE_CUSTOM_STAR_IMAGES] == true ) {
		
			$style_settings = get_option( Multi_Rating::STYLE_SETTINGS);
			
			// make sure at least full, half and empty star images exist and are valid URL's
			if ( filter_var( $input[Multi_Rating::CUSTOM_FULL_STAR_IMAGE], FILTER_VALIDATE_URL ) === false || 
					filter_var( $input[Multi_Rating::CUSTOM_HALF_STAR_IMAGE], FILTER_VALIDATE_URL ) === false ||
					filter_var( $input[Multi_Rating::CUSTOM_EMPTY_STAR_IMAGE], FILTER_VALIDATE_URL ) === false ) {
				add_settings_error( Multi_Rating::STYLE_SETTINGS, 'validation_error_custom_images', __( 'Full star, half star and empty star custom images are required.', 'multi-rating' ), 'error' );
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
					add_settings_error( Multi_Rating::STYLE_SETTINGS, 'invalid_mime_type', __( 'Invalid image format. Valid mime types: image/jpeg, image/png, image/bmp, image/tiff and image/x-icon', 'multi-rating' ), 'error' );
				}
			}
			
			// check image height and width are valid numbers within 1 and 128
			$custom_image_height = $input[Multi_Rating::CUSTOM_STAR_IMAGE_HEIGHT];
			$custom_image_width = $input[Multi_Rating::CUSTOM_STAR_IMAGE_WIDTH];
			
			if ( ! is_numeric( $custom_image_height) ) {
				add_settings_error( Multi_Rating::STYLE_SETTINGS, 'non_numeric_custom_image_height', __( 'Custom image height must be numeric.', 'multi-rating' ), 'error' );
			} else if ( intval($custom_image_height) < 1 || intval($custom_image_height) > 128 ) {
				add_settings_error( Multi_Rating::STYLE_SETTINGS, 'range_error_custom_image_height', __( 'Custom image height cannot be less than 1 or greater than 128.', 'multi-rating' ), 'error' );
			}
		
			if ( ! is_numeric($custom_image_width) ) {
				add_settings_error( Multi_Rating::STYLE_SETTINGS, 'non_numeric_custom_image_width', __( 'Custom image width must be numeric.', 'multi-rating' ), 'error' );
			} else if ( $custom_image_width < 1 || $custom_image_width > 128 ) {
				add_settings_error( Multi_Rating::STYLE_SETTINGS, 'range_error_custom_image_width', __( 'Custom image width cannot be less than 1 or greater than 128.', 'multi-rating' ), 'error' );
			}
		}
		
		return $input;
	}

	/**
	 * Register custom text settings
	 */
	function register_custom_text_settings() {
		
		register_setting( Multi_Rating::CUSTOM_TEXT_SETTINGS, Multi_Rating::CUSTOM_TEXT_SETTINGS, array( &$this, 'sanitize_custom_text_settings' ) );
	
		add_settings_section( 'section_custom_text', __('Custom Text Settings', 'multi-rating' ), array( &$this, 'section_custom_text_desc' ), Multi_Rating::CUSTOM_TEXT_SETTINGS );
	
		add_settings_field( Multi_Rating::RATING_FORM_TITLE_TEXT_OPTION, __( 'Rating Form Title', 'multi-rating' ), array( &$this, 'field_rating_form_title_text' ), Multi_Rating::CUSTOM_TEXT_SETTINGS, 'section_custom_text' );
		add_settings_field( Multi_Rating::RATING_RESULTS_LIST_TITLE_TEXT_OPTION, __( 'Rating Results List Title', 'multi-rating' ), array( &$this, 'field_rating_results_list_title_text' ), Multi_Rating::CUSTOM_TEXT_SETTINGS, 'section_custom_text' );
		add_settings_field( Multi_Rating::SUBMIT_RATING_FORM_BUTTON_TEXT_OPTION, __( 'Rating Form Submit Button Text', 'multi-rating' ), array( &$this, 'field_rating_form_submit_button_text' ), Multi_Rating::CUSTOM_TEXT_SETTINGS, 'section_custom_text' );
		add_settings_field( Multi_Rating::FILTER_BUTTON_TEXT_OPTION, __( 'Filter Button Text', 'multi-rating' ), array( &$this, 'field_filter_button_text' ), Multi_Rating::CUSTOM_TEXT_SETTINGS, 'section_custom_text' );
		add_settings_field( Multi_Rating::FILTER_LABEL_TEXT_OPTION, __( 'Filter Label Text', 'multi-rating' ), array( &$this, 'field_filter_label_text' ), Multi_Rating::CUSTOM_TEXT_SETTINGS, 'section_custom_text' );
		add_settings_field( Multi_Rating::RATING_FORM_SUBMIT_SUCCESS_MESSAGE_OPTION, __( 'Rating Form Submit Success Message', 'multi-rating' ), array( &$this, 'field_rating_form_submit_message' ), Multi_Rating::CUSTOM_TEXT_SETTINGS, 'section_custom_text' );
		add_settings_field( Multi_Rating::SAVE_RATING_RESTRICTION_ERROR_MESSAGE_OPTION, __( 'Rating Restriction Error Message', 'multi-rating' ), array( &$this, 'field_save_rating_restriction_error_message' ), Multi_Rating::CUSTOM_TEXT_SETTINGS, 'section_custom_text' );
		add_settings_field( Multi_Rating::FIELD_REQUIRED_ERROR_MESSAGE_OPTION, __( 'Field Required Error Message', 'multi-rating' ), array( &$this, 'field_required_error_message' ), Multi_Rating::CUSTOM_TEXT_SETTINGS, 'section_messages' );
		add_settings_field( Multi_Rating::NO_RATING_RESULTS_TEXT_OPTION, __( 'No Rating Results Text' , 'multi-rating' ), array( &$this, 'field_no_rating_results_text' ), Multi_Rating::CUSTOM_TEXT_SETTINGS, 'section_custom_text' );
		add_settings_field( Multi_Rating::CHAR_ENCODING_OPTION, __( 'Character Encoding', 'multi-rating' ), array( &$this, 'field_char_encoding' ), Multi_Rating::CUSTOM_TEXT_SETTINGS, 'section_custom_text' );
		
	}
	
	/**
	 * Custom text section description
	 */
	public function section_custom_text_desc() {
		echo '<p class="description">' . __( 'Modify the default text and messages.' , 'multi-rating' ) . '</p>';
	}
	
	/**
	 * Rating form submit button text setting
	 */
	public function field_rating_form_submit_button_text() {
		?>
		<input type="text" name="<?php echo Multi_Rating::CUSTOM_TEXT_SETTINGS; ?>[<?php echo Multi_Rating::SUBMIT_RATING_FORM_BUTTON_TEXT_OPTION; ?>]" class="regular-text" value="<?php echo $this->custom_text_settings[Multi_Rating::SUBMIT_RATING_FORM_BUTTON_TEXT_OPTION]; ?>" />
		<?php
	}

	/**
	 * Filter button text setting
	 */
	public function field_filter_button_text() {
		?>
		<input type="text" name="<?php echo Multi_Rating::CUSTOM_TEXT_SETTINGS; ?>[<?php echo Multi_Rating::FILTER_BUTTON_TEXT_OPTION; ?>]" class="regular-text" value="<?php echo $this->custom_text_settings[Multi_Rating::FILTER_BUTTON_TEXT_OPTION]; ?>" />
		<?php
	}
		
	/**
	 * Filter label text setting
	 */
	public function field_filter_label_text() {
		?>
		<input type="text" name="<?php echo Multi_Rating::CUSTOM_TEXT_SETTINGS; ?>[<?php echo Multi_Rating::FILTER_LABEL_TEXT_OPTION; ?>]" class="regular-text" value="<?php echo $this->custom_text_settings[Multi_Rating::FILTER_LABEL_TEXT_OPTION]; ?>" />
		<?php
	}
	
	/**
	 * Rating form submit message setting
	 */
	public function field_rating_form_submit_message() {
		?>
		<input type="text" name="<?php echo Multi_Rating::CUSTOM_TEXT_SETTINGS; ?>[<?php echo Multi_Rating::RATING_FORM_SUBMIT_SUCCESS_MESSAGE_OPTION; ?>]" class="large-text" value="<?php echo $this->custom_text_settings[Multi_Rating::RATING_FORM_SUBMIT_SUCCESS_MESSAGE_OPTION]; ?>" />
		<p class="description"><?php _e( 'Substitutions: %star_result%, %adjusted_star_result%, %score_result%, %adjusted_score_result%, %percentage_result%, %adjusted_percentage_result% and %total_max_option_value%. e.g. "Your rating was %adjusted_star_result%/5".', 'multi-rating' ); ?>
		<?php
	}
	
	/**
	 * Error message for the save rating restrictiong option
	 */
	public function field_save_rating_restriction_error_message() {
		?>
		<input type="text" name="<?php echo Multi_Rating::CUSTOM_TEXT_SETTINGS; ?>[<?php echo Multi_Rating::SAVE_RATING_RESTRICTION_ERROR_MESSAGE_OPTION; ?>]" class="large-text" value="<?php echo $this->custom_text_settings[Multi_Rating::SAVE_RATING_RESTRICTION_ERROR_MESSAGE_OPTION]; ?>" />
		<?php
	}
	
	/**
	 * Field required error message
	 */
	function field_required_error_message() {
		?>
		<input type="text" name="<?php echo Multi_Rating::CUSTOM_TEXT_SETTINGS; ?>[<?php echo Multi_Rating::FIELD_REQUIRED_ERROR_MESSAGE_OPTION; ?>]" class="large-text" value="<?php echo $this->custom_text_settings[Multi_Rating::FIELD_REQUIRED_ERROR_MESSAGE_OPTION]; ?>" />
		<p><?php _e( 'Applies to rating items if zero is selected.', 'multi-rating' ); ?></p>
		<?php
	}
		
	/**
	 * Rating form title text setting
	 */
	function field_rating_form_title_text() {
		?>
		<input type="text" name="<?php echo Multi_Rating::CUSTOM_TEXT_SETTINGS; ?>[<?php echo Multi_Rating::RATING_FORM_TITLE_TEXT_OPTION; ?>]" class="regular-text" value="<?php echo $this->custom_text_settings[Multi_Rating::RATING_FORM_TITLE_TEXT_OPTION]; ?>" />
		<?php
	}
	
	/**
	 * Rating results list title
	 */
	function field_rating_results_list_title_text() {
		?>
		<input type="text" name="<?php echo Multi_Rating::CUSTOM_TEXT_SETTINGS; ?>[<?php echo Multi_Rating::RATING_RESULTS_LIST_TITLE_TEXT_OPTION; ?>]" class="regular-text" value="<?php echo $this->custom_text_settings[Multi_Rating::RATING_RESULTS_LIST_TITLE_TEXT_OPTION]; ?>" />
		<?php
	}	
	
	/**
	 * No rating results text setting
	 */
	function field_no_rating_results_text() {
		?>
		<input type="text" name="<?php echo Multi_Rating::CUSTOM_TEXT_SETTINGS; ?>[<?php echo Multi_Rating::NO_RATING_RESULTS_TEXT_OPTION; ?>]" class="regular-text" value="<?php echo $this->custom_text_settings[Multi_Rating::NO_RATING_RESULTS_TEXT_OPTION]; ?>" />
		<?php
	}	

	/**
	 * Char encoding setting
	 */
	function field_char_encoding() {
		?>	
		<select name="<?php echo Multi_Rating::CUSTOM_TEXT_SETTINGS; ?>[<?php echo Multi_Rating::CHAR_ENCODING_OPTION; ?>]">
			<option value="" <?php selected( '', $this->custom_text_settings[Multi_Rating::CHAR_ENCODING_OPTION], true); ?>><?php _e('Keep current charset (Recommended)', 'multi-rating' ); ?></option>
	        <option value="utf8_general_ci" <?php selected('utf8_general_ci', $this->custom_text_settings[Multi_Rating::CHAR_ENCODING_OPTION], true); ?>><?php _e( 'UTF-8 (try this first)', 'multi-rating' ); ?></option>
	        <option value="latin1_swedish_ci" <?php selected('latin1_swedish_ci', $this->custom_text_settings[Multi_Rating::CHAR_ENCODING_OPTION], true); ?>><?php _e( 'latin1_swedish_ci' , 'multi-rating' ); ?></option>
		</select>
		<?php
	}
	
	/**
	 * Sanitize custom text settings
	 * 
	 * @param $input
	 * @return unknown
	 */
	function sanitize_custom_text_settings( $input ) {
		
		global $wpdb;
		
		$character_encoding = $input[Multi_Rating::CHAR_ENCODING_OPTION];
		$old_character_set = $this->general_settings[Multi_Rating::CHAR_ENCODING_OPTION];
		
		if ($character_encoding != $old_character_set) {
			
			$tables = array( $wpdb->prefix.Multi_Rating::RATING_ITEM_TBL_NAME );
			
			foreach ( $tables as $table ) {
				$rows = $wpdb->get_results( "DESCRIBE {$table}" );
				
				foreach ( $rows as $row ) {
					
					$name = $row->Field;
					$type = $row->Type;
					
					if ( preg_match( "/^varchar\((\d+)\)$/i", $type, $mat ) || ! strcasecmp( $type, "CHAR" )
							|| !strcasecmp( $type, "TEXT" ) || ! strcasecmp( $type, "MEDIUMTEXT" ) ) {
						$wpdb->query( 'ALTER TABLE ' . $table .' CHANGE ' . $name . ' ' . $name . ' ' . $type . ' COLLATE ' . $character_encoding );
					}
				}
			}
		}
	
		return $input;
	}
}
?>