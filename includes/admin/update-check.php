<?php 

/**
 * Performs a check if plugin upgrade requires some changes
 */
function mr_update_check() {
	
	// Check if we need to do an upgrade from a previous version
	$previous_plugin_version = get_option( Multi_Rating::VERSION_OPTION );
	
	if ( $previous_plugin_version != Multi_Rating::VERSION && $previous_plugin_version < 3 ) {
		mr_upgrade_to_3_0();
	}	
	
	if ( $previous_plugin_version != Multi_Rating::VERSION && $previous_plugin_version < 3.1 ) {
		mr_upgrade_from_3_0_to_3_1();
	}
	
	if ( $previous_plugin_version != Multi_Rating::VERSION && $previous_plugin_version < 3.2 ) {
		mr_upgrade_to_3_2();
	}
	
	if ( $previous_plugin_version != Multi_Rating::VERSION && $previous_plugin_version < 4 ) {
		mr_upgrade_to_4_0();
	}
	
	if ( $previous_plugin_version != Multi_Rating::VERSION && $previous_plugin_version < 4.2 ) {
		mr_upgrade_to_4_2();
	}

	if ( $previous_plugin_version != Multi_Rating::VERSION && $previous_plugin_version < 4.3 ) {
		mr_upgrade_to_4_3();
		update_option( Multi_Rating::VERSION_OPTION, Multi_Rating::VERSION ); // latest version upgrade complete
	}
}


/**
 * Make Font Awesome icons local and move post types option to auto placement settings
 */
function mr_upgrade_to_5() {
	
	$styles_settings = (array) get_option( Multi_Rating::STYLE_SETTINGS );	
	$icon_font_library = $styles_settings[Multi_Rating::FONT_AWESOME_VERSION_OPTION];
	
	if ( isset( $icon_font_library ) ) {
		
		if ( $icon_font_library == 'font-awesome-4.0.3' || $icon_font_library == 'font-awesome-4.1.0' 
				|| $icon_font_library == 'font-awesome-4.2.0' || $icon_font_library == 'font-awesome-4.3.0'
				|| $icon_font_library == 'font-awesome-4.5.0' || $icon_font_library == 'font-awesome-4.6.3'
				|| $icon_font_library == 'font-awesome-4.7.0' ) {
			$styles_settings[Multi_Rating::FONT_AWESOME_VERSION_OPTION] = 'font-awesome-v4';
		} else if ( $icon_font_library == 'font-awesome-3.2.1' ) {
			$styles_settings[Multi_Rating::FONT_AWESOME_VERSION_OPTION] = 'font-awesome-v3';
		}
	}
	
	update_option( Multi_Rating::STYLE_SETTINGS,  $styles_settings );

	$general_settings = (array) get_option( Multi_Rating::GENERAL_SETTINGS );	
	$auto_placement_settings = (array) get_option( Multi_Rating::POSITION_SETTINGS );	

	if ( isset( $general_settings[Multi_Rating::POST_TYPES_OPTION] ) ) {
		$auto_placement_settings[Multi_Rating::POST_TYPES_OPTION] = $general_settings[Multi_Rating::POST_TYPES_OPTION];
		unset( $general_settings[Multi_Rating::POST_TYPES_OPTION] );
	}

	update_option( Multi_Rating::GENERAL_SETTINGS, $general_settings);
	update_option( Multi_Rating::POSITION_SETTINGS, $auto_placement_settings);

}

/**
 * Remove IP address db column in rating entries table. This is no longer used for duplicate 
 * checks to ensure GDPR compliance.
 */
function mr_upgrade_to_4_3() {
	
	global $wpdb;
	$query = 'ALTER TABLE ' . $wpdb->prefix . Multi_Rating::RATING_ITEM_ENTRY_TBL_NAME . ' DROP COLUMN ip_address';
	$wpdb->query( $query );

	// if the duplicate checking currently uses IP addresses, change the option to use cookies instead
	$general_settings = (array) get_option( Multi_Rating::GENERAL_SETTINGS );
	$duplicate_check_methods = $general_settings[Multi_Rating::SAVE_RATING_RESTRICTION_TYPES_OPTION];
	if ( in_array( 'ip_address', $duplicate_check_methods ) ) {
		$general_settings[Multi_Rating::SAVE_RATING_RESTRICTION_TYPES_OPTION] = array( 'cookie' );
	}

	update_option( Multi_Rating::GENERAL_SETTINGS,  $general_settings );
}

/**
 * 
 */
function mr_upgrade_to_4_2() {
	
	$styles_settings = (array) get_option( Multi_Rating::STYLE_SETTINGS );	
	$custom_images_settings = (array) get_option( Multi_Rating::CUSTOM_IMAGES_SETTINGS );

	if ( isset( $styles_settings[Multi_Rating::USE_CUSTOM_STAR_IMAGES] ) ) {
		$custom_images_settings[Multi_Rating::USE_CUSTOM_STAR_IMAGES] = $styles_settings[Multi_Rating::USE_CUSTOM_STAR_IMAGES];
	}
	if ( isset( $styles_settings[Multi_Rating::CUSTOM_FULL_STAR_IMAGE] ) ) { 
		$custom_images_settings[Multi_Rating::CUSTOM_FULL_STAR_IMAGE] = $styles_settings[Multi_Rating::CUSTOM_FULL_STAR_IMAGE];
	}
	if ( isset( $styles_settings[Multi_Rating::CUSTOM_HALF_STAR_IMAGE] ) ) {
		$custom_images_settings[Multi_Rating::CUSTOM_HALF_STAR_IMAGE] = $styles_settings[Multi_Rating::CUSTOM_HALF_STAR_IMAGE];
	}
	if ( isset( $styles_settings[Multi_Rating::CUSTOM_EMPTY_STAR_IMAGE] ) ) {
		$custom_images_settings[Multi_Rating::CUSTOM_EMPTY_STAR_IMAGE] = $styles_settings[Multi_Rating::CUSTOM_EMPTY_STAR_IMAGE];
	}
	if ( isset( $styles_settings[Multi_Rating::CUSTOM_HOVER_STAR_IMAGE] ) ) {
		$custom_images_settings[Multi_Rating::CUSTOM_HOVER_STAR_IMAGE] = $styles_settings[Multi_Rating::CUSTOM_HOVER_STAR_IMAGE];
	}
	if ( isset( $styles_settings[Multi_Rating::CUSTOM_STAR_IMAGE_WIDTH]) ) {
		$custom_images_settings[Multi_Rating::CUSTOM_STAR_IMAGE_WIDTH] = $styles_settings[Multi_Rating::CUSTOM_STAR_IMAGE_WIDTH];
	}
	if ( isset( $styles_settings[Multi_Rating::CUSTOM_STAR_IMAGE_HEIGHT] ) ) {
		$custom_images_settings[Multi_Rating::CUSTOM_STAR_IMAGE_HEIGHT] = $styles_settings[Multi_Rating::CUSTOM_STAR_IMAGE_HEIGHT];
	}
	if ( isset( $styles_settings[Multi_Rating::FONT_AWESOME_VERSION_OPTION] ) ) {
		$styles_settings[Multi_Rating::FONT_AWESOME_VERSION_OPTION] = 'font-awesome-' . $styles_settings[Multi_Rating::FONT_AWESOME_VERSION_OPTION];
	}

	update_option( Multi_Rating::CUSTOM_IMAGES_SETTINGS, $custom_images_settings );
	update_option( Multi_Rating::STYLE_SETTINGS, $styles_settings );
	
}

/**
 * Upgrade to 4.0
 */
function mr_upgrade_to_4_0() {

	try {
		$sidebar_widgets = wp_get_sidebars_widgets();

		foreach ( $sidebar_widgets as &$widgets ) {
			foreach ( $widgets as $widget_key => $widget_id ) {
				if ( strpos( $widget_id, 'top_rating_results_widget' ) !== false) {
						
					$instance = substr( $widget_id, 26 );
					$widget_id = 'mr_rating_results_list-' . $instance;

					$widget_options = get_option( 'widget_top_rating_results_widget' );
					$show_filter = $widget_options[$instance]['show_category_filter'];
					$term_id = $widget_options[$instance]['category_id'];
						
					unset( $widget_options[$instance]['show_category_filter'] );
					$widget_options[$instance]['show_filter'] = $show_filter;
					unset( $widget_options[$instance]['category_id'] );
					$widget_options[$instance]['term_id'] = $term_id;
					$widget_options[$instance]['taxonomy'] = 'category';

					add_option( 'widget_mr_rating_results_list' , $widget_options );
					delete_option( 'widget_top_rating_results_widget' );
						
					$widgets[$widget_key] = $widget_id;
				}
			}
		}

		// custom settings
		$custom_text_settings = (array) get_option( Multi_Rating::CUSTOM_TEXT_SETTINGS );
			
		if ( isset( $custom_text_settings['mr_category_label_text'] ) ) {
			$custom_text_settings[Multi_Rating::FILTER_LABEL_TEXT_OPTION] = $custom_text_settings['mr_category_label_text'];
			unset( $custom_text_settings['mr_category_label_text'] );
		}

		if ( isset( $custom_text_settings['mr_top_rating_results_title_text'] ) ) {
			$custom_text_settings[Multi_Rating::RATING_RESULTS_LIST_TITLE_TEXT_OPTION] = $custom_text_settings['mr_top_rating_results_title_text'];
			unset( $custom_text_settings['mr_top_rating_results_title_text'] );
		}

		update_option( Multi_Rating::CUSTOM_TEXT_SETTINGS, $custom_text_settings);

		// PHP files

		if ( file_exists( dirname(__FILE__ ) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'class-rating-result.php' ))
			unlink( dirname(__FILE__ ) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'class-rating-result.php' );

	} catch (Exception $e) {
		die( __( 'An error occured.', 'multi-rating' ) );
	}


	wp_set_sidebars_widgets( $sidebar_widgets );
	
}


/**
 * Upgrade to 3.2
 */
function mr_upgrade_to_3_2() {
	
	Multi_Rating::activate_plugin();
	
	try {
		$general_settings = (array) get_option( Multi_Rating::GENERAL_SETTINGS );
	
		if ( isset( $general_settings['mr_ip_address_date_validation'] ) ) {
			
			$ip_address_date_validation = $general_settings['mr_ip_address_date_validation'];
			$save_rating_restriction_types = array();
			if ( $ip_address_date_validation == true ) {
				$save_rating_restriction_types = array( 'ip_address' );
			}
			
			$general_settings[Multi_Rating::SAVE_RATING_RESTRICTION_TYPES_OPTION] = $save_rating_restriction_types;
			unset( $general_settings['mr_ip_address_date_validation'] );
		}
		
		update_option( Multi_Rating::GENERAL_SETTINGS, $general_settings);
	
	} catch (Exception $e) {
		die( __( 'An error occured.', 'multi-rating' ) );
	}
}

/**
 * Upgrade to v3.x
 */
function mr_upgrade_to_3_0() {
	
	// activate plugin and db updates will occur
	Multi_Rating::activate_plugin();
	
	try {
		/**
		 * Delete old files that are no longer used from previous version
		 */
			
		$root = dirname(__FILE__ ) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..';
			
		// PHP files
		if ( file_exists( $root . DIRECTORY_SEPARATOR . 'filters.php' ) )
			unlink( $root . DIRECTORY_SEPARATOR . 'filters.php' );
		if ( file_exists( $root . DIRECTORY_SEPARATOR . 'multi-rating-api.php' ) )
			unlink( $root . DIRECTORY_SEPARATOR . 'multi-rating-api.php' );
		if ( file_exists( $root . DIRECTORY_SEPARATOR . 'rating-item-entry-table.php' ) )
			unlink( $root . DIRECTORY_SEPARATOR . 'rating-item-entry-table.php' );
		if ( file_exists( $root . DIRECTORY_SEPARATOR . 'rating-item-table.php' ) )
			unlink( $root . DIRECTORY_SEPARATOR . 'rating-item-table.php' );
		if ( file_exists( $root . DIRECTORY_SEPARATOR . 'rating-result-view.php' ) )
			unlink( $root . DIRECTORY_SEPARATOR . 'rating-result-view.php' );
		if ( file_exists( $root . DIRECTORY_SEPARATOR . 'rating-form-view.php' ) )
			unlink( $root . DIRECTORY_SEPARATOR . 'rating-form-view.php' );
		if ( file_exists( $root . DIRECTORY_SEPARATOR . 'rating-item-entry-value-table.php' ) )
			unlink( $root . DIRECTORY_SEPARATOR . 'rating-item-entry-value-table.php' );
		if ( file_exists( $root . DIRECTORY_SEPARATOR . 'shortcodes.php' ) )
			unlink( $root . DIRECTORY_SEPARATOR . 'shortcodes.php' );
		if ( file_exists( $root . DIRECTORY_SEPARATOR . 'update-check.php' ) )
			unlink( $root . DIRECTORY_SEPARATOR . 'update-check.php' );
		if ( file_exists( $root . DIRECTORY_SEPARATOR . 'utils.php' ) )
			unlink( $root . DIRECTORY_SEPARATOR . 'utils.php' );
		if ( file_exists( $root . DIRECTORY_SEPARATOR . 'widgets.php' ) )
			unlink( $root . DIRECTORY_SEPARATOR . 'widgets.php' );
			
		// Dirs
		if ( file_exists( $root . DIRECTORY_SEPARATOR . 'js' ) )
			mr_recursive_rmdir_and_unlink( $root . DIRECTORY_SEPARATOR . 'js' );
		if ( file_exists( $root . DIRECTORY_SEPARATOR . 'css' ) )
			mr_recursive_rmdir_and_unlink( $root . DIRECTORY_SEPARATOR . 'css' );
		if ( file_exists( $root . DIRECTORY_SEPARATOR . 'img' ) )
			mr_recursive_rmdir_and_unlink( $root . DIRECTORY_SEPARATOR . 'img' );
			
		// JS
			
		// Images
			
		// CSS
			
		/**
		 * Migrate options that have been renamed
		 */
			
	} catch ( Exception $e ) {
		die( __( 'An error occured.', 'multi-rating' ) );
	}
}

/**
 * Upgrade from 3.0.x to 3.1
 */
function mr_upgrade_from_3_0_to_3_1() {
	
	// activate plugin and db updates will occur
	Multi_Rating::activate_plugin();
	
	// replace username with user ID
	global $wpdb;
	$num_column_exists = $wpdb->query( 'SHOW COLUMNS FROM ' . $wpdb->prefix . Multi_Rating::RATING_ITEM_ENTRY_TBL_NAME . ' LIKE "username"' );
	
	if ( $num_column_exists > 0) { // if username column exists
		
		$query = 'SELECT username, rating_item_entry_id FROM ' . $wpdb->prefix . Multi_Rating::RATING_ITEM_ENTRY_TBL_NAME . ' WHERE username != ""';
		$rows = $wpdb->get_results( $query );
			
		foreach ( $rows as $row ) {
			$query = 'SELECT ID FROM ' . $wpdb->users . ' WHERE user_login = %s';
			$user_id = $wpdb->get_var( $wpdb->prepare( $query, $row->username ) );
			
			if ( $user_id ) {
				
				$wpdb->update( $wpdb->prefix . Multi_Rating::RATING_ITEM_ENTRY_TBL_NAME, 
						array( 'user_id' => $user_id ), 
						array( 'rating_item_entry_id' => $row->rating_item_entry_id ),
						array( '%d' ),
						array( '%d' )
				);
			}
		}
	
		$wpdb->query( 'ALTER TABLE ' . $wpdb->prefix . Multi_Rating::RATING_ITEM_ENTRY_TBL_NAME . ' DROP COLUMN username' );
	}
	
}

/**
 * Recursive function to remove a directory and all it's sub-directories and contents
 * @param  $dir
 */
function mr_recursive_rmdir_and_unlink( $dir ) {
	
	if ( is_dir( $dir ) ) {
		
		$objects = scandir( $dir );
		
		foreach ( $objects as $object ) {
			if ( $object != '.' && $object != '..' ) {
				
				if ( filetype($dir . DIRECTORY_SEPARATOR . $object ) == 'dir' ) {
					mr_recursive_rmdir_and_unlink( $dir. DIRECTORY_SEPARATOR . $object );
				} else {
					unlink( $dir . DIRECTORY_SEPARATOR . $object );
				}
				
			}
		}
		
		reset( $objects );
		rmdir( $dir );
	}
}
?>