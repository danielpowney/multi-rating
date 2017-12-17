<?php

$_tests_dir = getenv( 'WP_TESTS_DIR' );
if ( ! $_tests_dir ) {
	$_tests_dir = '/tmp/wordpress-tests-lib';
}

require_once $_tests_dir . '/includes/functions.php';

function _manually_load_plugin() {

	// Add your theme …
	switch_theme('twentyfifteen');

	// Update array with plugins to include ...
	$plugins_to_active = array(
			'multi-rating/multi-rating.php'
	);

	update_option( 'active_plugins', $plugins_to_active );

	// uncomment this to create db tables
	require dirname( dirname( __FILE__ ) ) . '/multi-rating.php';
	Multi_Rating::activate_plugin();
}
tests_add_filter( 'muplugins_loaded', '_manually_load_plugin' );

$GLOBALS['wp_tests_options'] = array(
		'active_plugins' => array( 'multi-rating/multi-rating.php' )
);

require $_tests_dir . '/includes/bootstrap.php';
