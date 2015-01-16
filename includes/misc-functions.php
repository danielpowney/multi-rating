<?php

/**
 * Add plugin footer to admin dashboard
 *
 * @param       string $footer_text The existing footer text
 * @return      string
 */
function mr_plugin_footer( $footer_text ) {

	$current_screen = get_current_screen();

	if ( $current_screen->parent_base == Multi_Rating::RATING_RESULTS_PAGE_SLUG ) {
		$plugin_footer = sprintf( __( 'Please <a href="%1$s" target="_blank">rate this plugin</a> on WordPress.org | '
				. 'Check out <a href="%2$s" target="_blank">Multi Rating Pro</a>!', 'multi-rating' ),
				'http://wordpress.org/support/view/plugin-reviews/multi-rating?filter=5#postform',
				'http://danielpowney.com/downloads/multi-rating-pro'
		);

		return $plugin_footer . '<br />' . $footer_text;

	} else {
		return $footer_text;
	}
}
add_filter( 'admin_footer_text', 'mr_plugin_footer' );

/**
 * Add to the WordPress version
 *
 * @param $default
 */
function mr_footer_version ( $default ) {

	$current_screen = get_current_screen();

	if ( $current_screen->parent_base == Multi_Rating::RATING_RESULTS_PAGE_SLUG ) {
		return 'Multi Rating v' . Multi_Rating::VERSION . '<br />' . $default;
	}

	return $default;
}
add_filter ('update_footer', 'mr_footer_version', 999);
 
 ?>