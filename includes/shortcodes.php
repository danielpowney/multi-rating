<?php 

/**
 * Shortcode to display the rating form
 */
function mr_rating_form( $atts = array(), $content = null, $tag ) {
	
	$can_do_shortcode = ! ( is_admin() && ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX ) );
	if ( ! apply_filters( 'mr_can_do_shortcode', $can_do_shortcode, 'mr_rating_form', $atts ) ) {
		return;
	}
	
	// get the post id
	global $post;
	
	$post_id = null;
	if (isset( $post ) ) {
		$post_id = $post->ID;
	}
	
	$custom_text_settings = (array) get_option( Multi_Rating::CUSTOM_TEXT_SETTINGS );
	
	extract( shortcode_atts( array(
			'post_id' => $post_id,
			'title' => $custom_text_settings[Multi_Rating::RATING_FORM_TITLE_TEXT_OPTION],
			'before_title' => '<h4>',
			'after_title' => '</h4>',
			'submit_button_text' => $custom_text_settings[Multi_Rating::SUBMIT_RATING_FORM_BUTTON_TEXT_OPTION],
			'class' => '',
	), $atts ) );
	
	if ( $post_id == null ) {
		return; // No post Id available
	}

	return Multi_Rating_API::display_rating_form( array(
			'post_id' => $post_id,
			'title' => $title,
			'before_title' => $before_title,
			'after_title' => $after_title,
			'submit_button_text' => $submit_button_text,
			'echo' => false,
			'class' => $class . ' mr-shortcode',
	) );
}
add_shortcode( 'mr_rating_form', 'mr_rating_form' );


/**
 * Shortcode to display the rating result
 */
function mr_rating_result( $atts = array(), $content = null, $tag ) {
	
	$can_do_shortcode = ! ( is_admin() && ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX ) );
	if ( ! apply_filters( 'mr_can_do_shortcode', $can_do_shortcode, 'mr_rating_result', $atts ) ) {
		return;
	}
	
	// get the post id
	global $post;
	
	$post_id = null;
	if ( isset( $post ) ) {
		$post_id = $post->ID;
	}
	
	$general_settings = (array) get_option( Multi_Rating::GENERAL_SETTINGS );
	$custom_text_settings = (array) get_option( Multi_Rating::CUSTOM_TEXT_SETTINGS );
	
	extract( shortcode_atts( array(
			'post_id' => $post_id,
			'no_rating_results_text' => $custom_text_settings[Multi_Rating::NO_RATING_RESULTS_TEXT_OPTION],
			'show_rich_snippets' => false,
			'show_title' => false,
			'show_count' => true,
			'result_type' => Multi_Rating::STAR_RATING_RESULT_TYPE,
			'class' => '',
			'before_count' => '(',
			'after_count' => ')'
	), $atts ) );
	
	if ( $post_id == null ) {
		return; // No post Id available
	}
	
	if ( is_string( $show_rich_snippets ) ) {
		$show_rich_snippets = $show_rich_snippets == 'true' ? true : false;
	}
	if ( is_string( $show_title) ) {
		$show_title = $show_title == 'true' ? true : false;
	}
	if ( is_string( $show_count ) ) {
		$show_count = $show_count == 'true' ? true : false;
	}
	
	return Multi_Rating_API::display_rating_result( array(
			'post_id' => $post_id,
			'no_rating_results_text' => $no_rating_results_text,
			'show_rich_snippets' => $show_rich_snippets,
			'show_title' => $show_title,
			'show_date' => false,
			'show_count' => $show_count,
			'echo' => false,
			'result_type' => $result_type,
			'class' => $class . ' mr-shortcode',
			'before_count' => $before_count,
			'after_count' => $after_count
	) );
}
add_shortcode( 'mr_rating_result', 'mr_rating_result' );


/**
 * Shortcode function for displaying rating results list
 *
 * @param $atts
 * @return string
 */
function mr_rating_results_list( $atts = array(), $content = null, $tag ) {
	
	$can_do_shortcode = ! ( is_admin() && ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX ) );
	if ( ! apply_filters( 'mr_can_do_shortcode', $can_do_shortcode, 'mr_rating_results_list', $atts ) ) {
		return;
	}
	
	$general_settings = (array) get_option( Multi_Rating::GENERAL_SETTINGS );
	$custom_text_settings = (array) get_option( Multi_Rating::CUSTOM_TEXT_SETTINGS );
	
	extract( shortcode_atts( array(
			'title' => $custom_text_settings[Multi_Rating::RATING_RESULTS_LIST_TITLE_TEXT_OPTION],
			'before_title' => '<h4>',
			'after_title' => '</h4>',
			'no_rating_results_text' => $custom_text_settings[Multi_Rating::NO_RATING_RESULTS_TEXT_OPTION],
			'show_count' => true,
			'show_category_filter' => true, // @deprecated
			'limit' => 10,
			'result_type' => Multi_Rating::STAR_RATING_RESULT_TYPE,
			'show_rank' => true,
			'show_title' => true,
			'class' => '',
			'category_id' => 0, // 0 = All,
			'taxonomy' => null,
			'term_id' => 0, // 0 = All,
			'filter_button_text' => $custom_text_settings[Multi_Rating::FILTER_BUTTON_TEXT_OPTION],
			'category_label_text' => $custom_text_settings[Multi_Rating::FILTER_LABEL_TEXT_OPTION], // @deprecated
			'show_featured_img' => true,
			'image_size' => 'thumbnail',
			
			'show_filter' => false,
			'filter_label_text' => $custom_text_settings[Multi_Rating::FILTER_LABEL_TEXT_OPTION],
			'sort_by' => 'highest_rated',
	), $atts ) );
	
	// temp
	if ( is_string( $show_category_filter ) ) {
		$show_category_filter = $show_category_filter == 'true' ? true : false;
		$show_filter = $show_filter; 
	}
	
	if ( is_string( $show_filter ) ) {
		$show_filter = $show_filter == 'true' ? true : false;
	}
	if ( is_string( $show_count ) ) {
		$show_count = $show_count == 'true' ? true : false;
	}
	if ( is_string( $show_title ) ) {
		$show_title = $show_title == 'true' ? true : false;
	}
	if ( is_string( $show_featured_img ) ) {
		$show_featured_img = $show_featured_img == 'true' ? true : false;
	}
	
	if ( $category_id != 0 ) {
		$term_id = $category_id;
		$taxonomy = 'category';
	}
	
	return Multi_Rating_API::display_rating_results_list( array(
			'no_rating_results_text' => $no_rating_results_text,
			'show_count' => $show_count,
			'echo' => false,
			'title' => $title,
			//'show_category_filter' => $show_category_filter,
			'show_filter' => $show_filter,
			'limit' => $limit,
			'result_type' => $result_type,
			'show_rank' => $show_rank,
			'show_title' => $show_title,
			'class' => $class . ' mr-shortcode',
			'before_title' => $before_title,
			'after_title' => $after_title,
			'taxonomy' => $taxonomy,
			'term_id' => $term_id, // 0 = All
			'filter_button_text' => $filter_button_text,
			//'category_label_text' => $category_label_text,
			'filter_label_text' => $filter_label_text,
			'show_featured_img' => $show_featured_img,
			'image_size' => $image_size,
			'sort_by' => $sort_by
	) );
}
add_shortcode( 'mr_rating_results_list', 'mr_rating_results_list' );
?>