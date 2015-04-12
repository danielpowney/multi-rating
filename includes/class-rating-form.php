<?php 

/**
 * Rating form class
 * 
 * @author dpowney
 *
 */
class MR_Rating_Form {
	
	/**
	 * Used to uniquely identify a rating form
	 */
	public static $sequence = 0;
	
	/**
	 * Saves a rating form entry.
	 */
	public static function save_rating() {
	
		$ajax_nonce = $_POST['nonce'];
		if ( wp_verify_nonce($ajax_nonce, Multi_Rating::ID.'-nonce' ) ) {
				
			global $wpdb;
	
			$rating_items = $_POST['ratingItems'];
			$post_id = $_POST['postId'];
			$ip_address = MR_Utils::get_ip_address();
			$entry_date_mysql = current_time( 'mysql' );
			$sequence = isset($_POST['sequence']) ? $_POST['sequence'] : '';
			
			// WPML get original pst id for default language
			if ( function_exists( 'icl_object_id' ) ) {
				global $sitepress;
				$post_id = icl_object_id ( $post_id , get_post_type( $post_id ), false, $sitepress->get_default_language() );
			}
	
			$data = array(
					'sequence' => $sequence,
					'post_id' => $post_id
			);				
	
			$general_settings = (array) get_option( Multi_Rating::GENERAL_SETTINGS );
			$custom_text_settings = (array) get_option( Multi_Rating::CUSTOM_TEXT_SETTINGS );
	
			// get user id
			global $wp_roles;
			$user = wp_get_current_user();
			$user_id = $user->ID;
	
			// stores any validation results, custom validation results can be added through filters
			$validation_results = array();
			
			$validation_results = MR_Utils::validate_save_rating_restricton( $validation_results, $post_id );
			
			$validation_results = MR_Utils::validate_rating_item_required( $validation_results, $rating_items );
			
			$validation_results = apply_filters( 'mr_after_rating_form_validation_save', $validation_results, $data );
			
			if ( MR_Utils::has_validation_error( $validation_results ) ) {
				echo json_encode( array (
						'status' => 'error',
						'data' => $data,
						'validation_results' => $validation_results
				) );
				die();
			}
	
			// everything is OK so now insert the rating form entry and entry values into the database tables
			$wpdb->insert( $wpdb->prefix . Multi_Rating::RATING_ITEM_ENTRY_TBL_NAME, array(
					'post_id' => $post_id,
					'entry_date' => $entry_date_mysql,
					'ip_address' => $ip_address,
					'user_id' => $user_id,
			), array('%d', '%s', '%s', '%d') );
	
			$rating_item_entry_id = $wpdb->insert_id;
	
			foreach ( $rating_items as $rating_item ) {
	
				$rating_item_id = $rating_item['id'];
				$rating_item_value = $rating_item['value'];
	
				$wpdb->insert( $wpdb->prefix . Multi_Rating::RATING_ITEM_ENTRY_VALUE_TBL_NAME, array(
						'rating_item_entry_id' => $rating_item_entry_id,
						'rating_item_id' => $rating_item_id,
						'value' => $rating_item_value
				), array('%d', '%d', '%d') );
			}
			
			// Set cookie if restriction type is used
			foreach ( $general_settings[Multi_Rating::SAVE_RATING_RESTRICTION_TYPES_OPTION] as $save_rating_restriction_type ) {
				if ( $save_rating_restriction_type == 'cookie' ) {
					if( ! headers_sent() ) {
						$save_rating_restriction_hours = $general_settings[Multi_Rating::SAVE_RATING_RESTRICTION_HOURS_OPTION];
						setcookie(Multi_Rating::POST_SAVE_RATING_COOKIE . '-' . $post_id, true, time() + ( 60 * 60 * $save_rating_restriction_hours ), 
								COOKIEPATH, COOKIE_DOMAIN, false, true);
					}
					break;
				}
			}
			
			$rating_items = Multi_Rating_API::get_rating_items( array( 'post_id' => $post_id ) );
			
			$rating_result  = Multi_Rating_API::calculate_rating_result( array(
					'post_id' => $post_id,
					'rating_items' => $rating_items
			) );

			$rating_results_cache = $general_settings[Multi_Rating::RATING_RESULTS_CACHE_OPTION];
			if ($rating_results_cache == true) {
				// update rating results cache
				update_post_meta( $post_id, Multi_Rating::RATING_RESULTS_POST_META_KEY, $rating_result );
			}
			
			$style_settings = (array) get_option( Multi_Rating::STYLE_SETTINGS );
			$font_awesome_version = $style_settings[Multi_Rating::FONT_AWESOME_VERSION_OPTION];
			$icon_classes = MR_Utils::get_icon_classes( $font_awesome_version );
			$use_custom_star_images = $style_settings[Multi_Rating::USE_CUSTOM_STAR_IMAGES];
			$image_width = $style_settings[Multi_Rating::CUSTOM_STAR_IMAGE_WIDTH];
			$image_height = $style_settings[Multi_Rating::CUSTOM_STAR_IMAGE_HEIGHT];
			
			$rating_results_position = get_post_meta( $post_id, Multi_Rating::RATING_RESULTS_POSITION_POST_META, true );
			
			$position_settings = (array) get_option( Multi_Rating::POSITION_SETTINGS );
			// use default rating results position
			if ( $rating_results_position == '' ) {
				$rating_results_position = $position_settings[Multi_Rating::RATING_RESULTS_POSITION_OPTION ];
			}
			
			ob_start();
			mr_get_template_part( 'rating-result', null, true, array(
				'no_rating_results_text' => '',
				'show_rich_snippets' => false,
				'show_title' => false,
				'show_date' => false,
				'show_count' => true,
				'result_type' => Multi_Rating::STAR_RATING_RESULT_TYPE,
				'class' =>  'rating-result-' . $post_id . ' ' . $rating_results_position . ' mr-filter',
				'rating_result' => $rating_result,
				'before_count' => '(',
				'after_count' => ')',
				'post_id' => $post_id,
				'ignore_count' => false,
				'preserve_max_option' => false,
				'before_date' => '',
				'after_date' => '',
				'icon_classes' => $icon_classes,
				'use_custom_star_images' => $use_custom_star_images,
				'image_width' => $image_width,
				'image_height' => $image_height
			) );
			$html = ob_get_contents();
			ob_end_clean();
			
			$data['html'] = $html;
	
			// if the custom text does not contain %, then there's no need to substitute the message
			$message = $custom_text_settings[ Multi_Rating::RATING_FORM_SUBMIT_SUCCESS_MESSAGE_OPTION];
			if (strpos($message, '%') !== false) {
				$message = MR_Utils::substitute_message( $message, $user, 
						Multi_Rating_API::calculate_rating_item_entry_result( $rating_item_entry_id, $rating_items ) );
			}
	
			$data['rating_result'] = $rating_result;
				
			$data['hide_rating_form'] = $general_settings[Multi_Rating::HIDE_RATING_FORM_AFTER_SUBMIT_OPTION];
			
			echo json_encode( array(
					'status' => 'success',
					'data' => $data,
					'message' => $message,
					'validation_results' => $validation_results
			) );
		}
			
		die();
	}
}
?>