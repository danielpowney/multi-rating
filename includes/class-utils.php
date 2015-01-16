<?php 

/**
 * Utils class
 * 
 * @author dpowney
 */
class MR_Utils {
	
	/** 
	 * Gets the client ip address
	 * 
	 * @return IP address
	 */
	public static function get_ip_address() {
		
		$client_IP_address = '';
		
		if ( isset( $_SERVER['HTTP_CLIENT_IP'] ) ) {
			$client_IP_address = $_SERVER['HTTP_CLIENT_IP'];
		} else if ( isset( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) {
			$client_IP_address = $_SERVER['HTTP_X_FORWARDED_FOR'];
		} else if ( isset( $_SERVER['HTTP_X_FORWARDED'] ) ) {
			$client_IP_address = $_SERVER['HTTP_X_FORWARDED'];
		} else if ( isset( $_SERVER['HTTP_FORWARDED_FOR'] ) ) {
			$client_IP_address = $_SERVER['HTTP_FORWARDED_FOR'];
		} else if ( isset( $_SERVER['HTTP_FORWARDED'] ) ) {
			$client_IP_address = $_SERVER['HTTP_FORWARDED'];
		} else if ( isset( $_SERVER['REMOTE_ADDR'] ) ) {
			$client_IP_address = $_SERVER['REMOTE_ADDR'];
		}
		
		return $client_IP_address;
	}
	
	/**
	 * Gets the Font Awesome icon classes based on version
	 * 
	 * @param $font_awesome_version
	 * @return array icon classes
	 */
	public static function get_icon_classes( $font_awesome_version ) {
		
		$icon_classes = array();
		
		if ( $font_awesome_version == '4.0.3' || $font_awesome_version == '4.1.0' || $font_awesome_version == '4.2.0' ) {
			$icon_classes['star_full'] = 'fa fa-star mr-star-full';
			$icon_classes['star_half'] = 'fa fa-star-half-o mr-star-half';
			$icon_classes['star_empty'] = 'fa fa-star-o mr-star-empty';
			$icon_classes['minus'] = 'fa fa-minus-circle mr-minus';
			$icon_classes['spinner'] = 'fa fa-spinner fa-spin mr-spinner';
		} else if ( $font_awesome_version == '3.2.1' ) {
			$icon_classes['star_full'] = 'icon-star mr-star-full';
			$icon_classes['star_half'] = 'icon-star-half-full mr-star-half';
			$icon_classes['star_empty'] = 'icon-star-empty mr-star-empty';
			$icon_classes['minus'] = 'icon-minus-sign mr-minus';
			$icon_classes['spinner'] = 'icon-spinner icon-spin mr-spinner';
		}
		
		return $icon_classes;
	}
	
	/**
	 * Checks if post type is enabled
	 *
	 * @param $post_id
	 */
	public static function check_post_type_enabled( $post_id ) {
		$general_settings = (array) get_option( Multi_Rating::GENERAL_SETTINGS );
	
		$post_types = $general_settings[ Multi_Rating::POST_TYPES_OPTION ];
		if ( ! isset( $post_types ) ) {
			return false;
		}
	
		if ( ! is_array( $post_types ) && is_string( $post_types ) ) {
			$post_types = array( $post_types );
		}
	
		$post_type = get_post_type( $post_id );
		if ( ! in_array( $post_type, $post_types ) ) {
			return false;
		}
	
		return true;
	}
	
	/**
	 * Perform cookie and IP address restriction type checks
	 *
	 * @param array validation_results
	 * @param int $post_id
	 */
	public static function validate_save_rating_restricton( $validation_results, $post_id ) {
	
		$general_settings = (array) get_option( Multi_Rating::GENERAL_SETTINGS );
		
		$hours = $general_settings[Multi_Rating::SAVE_RATING_RESTRICTION_HOURS_OPTION];
		$ip_address = MR_Utils::get_ip_address();
		$save_rating_restriction_types = $general_settings[Multi_Rating::SAVE_RATING_RESTRICTION_TYPES_OPTION];
		
		foreach ( $save_rating_restriction_types as $save_rating_restriction_type ) {
			
			if ( ( $save_rating_restriction_type == 'ip_address' && MR_Utils::ip_address_validation_check( $ip_address, $post_id, $hours ) == true )
					|| ( $save_rating_restriction_type == 'cookie' && MR_Utils::cookie_validation_check( $post_id ) == true ) ) {
					
				$custom_text_settings = (array) get_option( Multi_Rating::CUSTOM_TEXT_SETTINGS );
				
				array_push( $validation_results, array(
							'severity' => 'error',
							'name' => 'save_rating_restriction_error',
							'message' => $custom_text_settings[ Multi_Rating::SAVE_RATING_RESTRICTION_ERROR_MESSAGE_OPTION ]
					) );
			}
		} 
	
		return $validation_results;
	}
	
	/**
	 * Checks whether save rating cookie exists for a post
	 * 
	 * @param $post_id
	 */
	public static function cookie_validation_check( $post_id ) {
		return isset($_COOKIE[Multi_Rating::POST_SAVE_RATING_COOKIE . '-' . $post_id]);
	}

	/**
	 * Check IP address has not saved a rating form with the post ID within specified hours
	 */
	public static function ip_address_validation_check( $ip_address, $post_id, $hours ) {
		global $wpdb;

		$entry_date_mysql = current_time('mysql');
		
		$previous_day_date = strtotime( $entry_date_mysql ) - ( 1 * 1 * 60 * 60 * $hours );
		$previous_day_date_mysql = date( 'Y-m-d H:i:s', $previous_day_date );
		
		$ip_address_check_query = 'SELECT * FROM ' . $wpdb->prefix . Multi_Rating::RATING_ITEM_ENTRY_TBL_NAME . ' WHERE ip_address = "'
				. $ip_address . '" AND post_id =' . $post_id . ' AND entry_date >= "' . $previous_day_date_mysql . '"';
		$rows = $wpdb->get_results( $ip_address_check_query );
		
		return ( count( $rows ) > 0 );
	}
	
	/**
	 * Helper function to iterate validation results for errors
	 *
	 * @param $validation_results
	 */
	public static function has_validation_error( $validation_results ) {
		foreach ( $validation_results as $validation_result ) {
			if ( $validation_result['severity'] == 'error' ) {
				return true;
			}
		}
		return false;
	}
	
	/**
	 * Substitute the message
	 *
	 * @param unknown_type $message
	 * @param unknown_type $user
	 * @param unknown_type $rating_result
	 */
	public static function substitute_message( $message, $user, $rating_result ) {
	
		if ($rating_result != null) {
			$rating_result_substitutions = array(
					'%adjusted_percentage_result%' => 'adjusted_percentage_result',
					'%percentage_result%' => 'percentage_result',
					'%adjusted_score_result%' => 'adjusted_score_result',
					'%score_result%' => 'score_result',
					'%adjusted_star_result%' => 'adjusted_star_result',
					'%star_result%' => 'star_result',
					'%total_max_option_value%' => 'total_max_option_value'
			);
			foreach ( $rating_result_substitutions as $string => $index ) {
				$message = str_replace( $string, $rating_result[$index], $message );
			}
		}
	
		return $message;
	}
}
?>