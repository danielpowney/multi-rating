<?php

/**
 * Filters the_content()
 *
 * @param $content
 * @return filtered content
 */
function mr_filter_the_content( $content ) {

	// get the post id
	global $post;

	$post_id = null;
	if ( !isset( $post_id ) && isset( $post ) ) {
		$post_id = $post->ID;
	} else if ( !isset($post) && !isset( $post_id ) ) {
		return $content; // No post id available to display rating form
	}

	$can_apply_filter = ! ( is_feed() || ! in_the_loop() || ( is_admin() && ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX ) ) );
	if ( ! apply_filters( 'mr_can_apply_filter', $can_apply_filter, 'the_content', $content, $post_id ) ) {
		return $content;
	}

	$position_settings = ( array ) get_option( Multi_Rating::POSITION_SETTINGS );

	$rating_form_html = null;
	$rating_results_html = null;

	$rating_form_position = get_post_meta( $post->ID, Multi_Rating::RATING_FORM_POSITION_POST_META, true );
	$rating_results_position = get_post_meta( $post->ID, Multi_Rating::RATING_RESULTS_POSITION_POST_META, true );

	if ( $rating_form_position != Multi_Rating::DO_NOT_SHOW ) {

		// use default rating form position
		if ( $rating_form_position == '' ) {
			$rating_form_position = $position_settings[ Multi_Rating::RATING_FORM_POSITION_OPTION ];
		}

		if ( $rating_form_position == 'before_content' || $rating_form_position == 'after_content' ) {
			$rating_form_html = Multi_Rating_API::display_rating_form( array(
					'post_id' => $post_id,
					'echo' => false,
					'class' => $rating_form_position . ' mr-filter'
			) );
		}
	}

	if ( $rating_results_position != Multi_Rating::DO_NOT_SHOW ) {

		// use default rating results position
		if ( $rating_results_position == '' ) {
			$rating_results_position = $position_settings[ Multi_Rating::RATING_RESULTS_POSITION_OPTION ];
		}

		if ( $rating_results_position == 'before_content' || $rating_results_position == 'after_content' ) {
			$rating_results_html = Multi_Rating_API::display_rating_result( array(
					'post_id' => $post_id,
					'echo' => false,
					'show_date' => false,
					'generate_microdata' => is_singular(),
					'class' => $rating_results_position . ' mr-filter'
			) );
		}
	}

	$filtered_content = '';

	if ( $rating_results_position == 'before_content' && $rating_results_html != null ) {
		$filtered_content .= $rating_results_html;
	}

	if ( $rating_form_position == 'before_content' && $rating_form_html != null ) {
		$filtered_content .= $rating_form_html;
	}

	$filtered_content .= $content;

	if ( $rating_results_position == 'after_content' && $rating_results_html != null ) {
		$filtered_content .= $rating_results_html;
	}

	if ( $rating_form_position == 'after_content' && $rating_form_html != null ) {
		$filtered_content .= $rating_form_html;
	}

	do_action( 'mr_after_auto_placement', 'the_content', $post_id );

	return $filtered_content;
}
add_filter( 'the_content', 'mr_filter_the_content' );




/**
 * Filters the_title()
 *
 * @param $title
 * @return filtered title
 */
function mr_filter_the_title( $title ) {

	// get the post id
	global $post;

	$post_id = null;
	if ( ! isset( $post_id ) && isset( $post ) ) {
		$post_id = $post->ID;
	} else if ( !isset( $post ) && ! isset( $post_id ) ) {
		return $title; // No post id available to display rating result
	}

	$can_apply_filter = ! ( is_feed() || ! in_the_loop() || ( is_admin() && ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX ) ) );
	if ( ! apply_filters( 'mr_can_apply_filter', $can_apply_filter, 'the_title', $title, $post_id ) ) {
		return $title;
	}

	$rating_results_position = get_post_meta( $post->ID, Multi_Rating::RATING_RESULTS_POSITION_POST_META, true );
	if ( $rating_results_position == Multi_Rating::DO_NOT_SHOW ) {
		return $title;
	}

	$position_settings = (array) get_option( Multi_Rating::POSITION_SETTINGS );

	// use default rating results position
	if ( $rating_results_position == '' ) {
		$rating_results_position = $position_settings[ Multi_Rating::RATING_RESULTS_POSITION_OPTION ];
	}

	$rating_results_html = null;

	if ( $rating_results_position == 'before_title' || $rating_results_position == 'after_title' ) {
		$rating_results_html = Multi_Rating_API::display_rating_result( array(
				'post_id' => $post_id,
				'echo' => false,
				'show_date' => false,
				'generate_microdata' => is_singular(),
				'class' => $rating_results_position . ' mr-filter'
		) );
	}

	$filtered_title = '';

	if ( $rating_results_position == 'before_title' && $rating_results_html != null ) {
		$filtered_title .= $rating_results_html;
	}

	$filtered_title .= $title;

	if ( $rating_results_position == 'after_title' && $rating_results_html != null ) {
		$filtered_title .= $rating_results_html;
	}

	do_action( 'mr_after_auto_placement', 'the_title', $post_id );

	return $filtered_title;
}
add_filter( 'the_title', 'mr_filter_the_title' );


/**
 * Checks settings to determine whether auto placement can be applied
 *
 * @param boolean $can_apply_filter
 * @param string $filter_name
 * @param string $value
 * @param int $post_id
 * @return $can_apply_filter
 */
function mr_can_apply_filter( $can_apply_filter, $filter_name, $value, $post_id ) {

	if ( $can_apply_filter ) {
		$can_apply_filter = MR_Utils::check_post_type_enabled( $post_id );
	}

	return $can_apply_filter;
}
add_filter( 'mr_can_apply_filter', 'mr_can_apply_filter', 10, 4 );


/**
 * Makes sure filter is only called once per post. Otherwise the rating results or rating form could be displayed
 * multiple times depending on the theme compatibility. This filter can be removed eaisly if needed to suit your theme needs
 *
 * @param $filter
 * @param $post_id
 */
function mr_check_auto_placement( $filter, $post_id )  {
	// only apply filter once... hopefully, this is the post title...
	if ( in_the_loop() && ( is_single() || is_page() || is_attachment() ) ) {
		remove_filter( $filter, 'mr_filter_' . $filter );
	}
}
add_action( 'mr_after_auto_placement', 'mr_check_auto_placement', 10, 3);
