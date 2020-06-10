<?php

/**
 * Gutenberg class. Registers plugin in Gutenberg Editor sidebar and registers 
 * blocks
 * 
 * @author dpowney
 *
 */
class MR_Gutenberg {

	/**
	 * Constructor
	 */
	public function __construct() {

		add_action( 'enqueue_block_editor_assets', array( $this, 'register_plugin' ) );
		add_action( 'init', array( $this, 'set_script_translations' ) );
		add_action( 'init', array( $this, 'register_blocks' ) );
		//add_filter( 'allowed_block_types', 'allowed_block_types', 10, 2 );
		add_action( 'init', array( $this, 'register_post_meta' ) );
	}

	/**
	 * Registers the plugin sidebar
	 */
	public function register_plugin() {

		wp_register_script( 
			'mr-gutenberg-plugin-script', 
			plugins_url( '../assets/js/plugin.js', __FILE__ ), 
			array( 'wp-blocks', 'wp-i18n', 'wp-element', 'wp-components', 'wp-editor', 'wp-plugins', 
				'wp-edit-post', 'wp-data', 'wp-compose' ) 
		);

		wp_enqueue_script('mr-gutenberg-plugin-script');

	}

	/**
	 * Adds support for script language translations
	 */
	public function set_script_translations() {
		wp_set_script_translations( 'mr-gutenberg-plugin-script', 'multi-rating' );
    	wp_set_script_translations( 'mr-gutenberg-blocks-script', 'multi-rating' );
	}


	/**
	 * Register blocks
	 */
	public function register_blocks() {

		if ( ! function_exists( 'register_block_type' ) ) {
			// Gutenberg is not active.
			return;
		}
	 
	   wp_register_script( 'mr-gutenberg-blocks-script', plugins_url( '../assets/js/blocks.js', __FILE__ ), array( 'wp-blocks', 'wp-i18n', 'wp-element', 'wp-components', 'wp-editor', 'wp-element' ) );

	    $custom_text_settings = (array) Multi_Rating::instance()->settings->custom_text_settings;

	    register_block_type( 'multi-rating/rating-form', array(
	        'editor_script' => 'mr-gutenberg-blocks-script',
	        'render_callback' => array( $this, 'rating_form_block_render' ),
	        'attributes' => [
				'title' => [
					'default' => $custom_text_settings[Multi_Rating::RATING_FORM_TITLE_TEXT_OPTION],
					'type' => 'string'
				],
				'submit_button_text' => [
					'type' => 'string',
					'default' => $custom_text_settings[Multi_Rating::SUBMIT_RATING_FORM_BUTTON_TEXT_OPTION],
				]
			]
	    ) );

	    register_block_type( 'multi-rating/rating-result', array(
	        'editor_script' => 'mr-gutenberg-blocks-script',
	        'render_callback' => array( $this, 'rating_result_block_render' ),
	        'attributes' => [
				'show_title' => [
					'type' => 'boolean',
					'default' => false
				],
				'show_count' => [
					'type' => 'boolean',
					'default' => true
				]
			]
	    ) );

	    register_block_type( 'multi-rating/rating-results-list', array(
	        'editor_script' => 'mr-gutenberg-blocks-script',
	        'render_callback' => array( $this, 'rating_results_list_block_render' ),
	        'attributes' => [
	        	'title' => [
					'default' => $custom_text_settings[Multi_Rating::RATING_RESULTS_LIST_TITLE_TEXT_OPTION],
					'type' => 'string'
				],
				'show_count' => [
					'type' => 'boolean',
					'default' => true
				],
				'show_filter' => [
					'type' => 'boolean',
					'default' => true
				],
				'limit' => [
					'type' => 'integer',
					'default' => 5
				],
				'show_rank' => [
					'type' => 'boolean',
					'default' => true
				],
				'show_featured_img' => [
					'type' => 'boolean',
					'default' => true
				]
			]
	    ) );
	 
	}

	/**
	 * Renders the rating form block
	 */
	public function rating_form_block_render( $attributes ) {
		
		$shortcode_format = '[mr_rating_form title="%s" submit_button_text="%s"]';
		
		$shortcode_text = sprintf( $shortcode_format, $attributes['title'], $attributes['submit_button_text'] );
		
		return do_shortcode( $shortcode_text );
	}

	/**
	 * Renders the rating result block
	 */
	public function rating_result_block_render( $attributes ) {

		$show_count = $attributes['show_count'] === true ? 'true' : 'false';
		$show_title = $attributes['show_title'] === true ? 'true' : 'false';

		$shortcode_format = '[mr_rating_result show_title=%s show_count=%s]';
		$shortcode_text = sprintf( $shortcode_format, $show_title, $show_count );

		return do_shortcode( $shortcode_text );
	}

	/**
	 * Renders the rating results list block
	 */
	public function rating_results_list_block_render( $attributes ) {

		$show_count = $attributes['show_count'] === true ? 'true' : 'false';
		$show_rank = $attributes['show_rank'] === true ? 'true' : 'false';
		$show_featured_img = $attributes['show_featured_img'] === true ? 'true' : 'false';
		$show_filter = $attributes['show_filter'] === true ? 'true' : 'false';

		$shortcode_format = '[mr_rating_results_list title="%s" show_count="%s" show_rank="%s" show_featured_img="%s" limit=%d show_filter="%s"]';
		$shortcode_text = sprintf( $shortcode_format, $attributes['title'], $show_count, $show_rank, $show_featured_img, $attributes['limit'], $show_filter );

		return do_shortcode( $shortcode_text );
	}

	/*
	 * Registers post meta fields with REST API visibility
	 */
	public function register_post_meta() {

		register_post_meta( 'post', Multi_Rating::RATING_FORM_POSITION_POST_META, array(
			'show_in_rest' => true,
	        'single' => true,
	        'type' => 'string',
	        'auth_callback' => function () { return current_user_can('edit_posts'); }
	    ));
		register_post_meta( 'post', Multi_Rating::RATING_RESULTS_POSITION_POST_META, array(
			'show_in_rest' => true,
	        'single' => true,
	        'type' => 'string',
	        'auth_callback' => function () { return current_user_can('edit_posts'); }
		));
		register_post_meta( 'post', Multi_Rating::STRUCTURED_DATA_TYPE_POST_META, array(
			'show_in_rest' => true,
	        'single' => true,
	        'type' => 'string',
	        'auth_callback' => function () { return current_user_can('edit_posts'); }
		));

	}

}