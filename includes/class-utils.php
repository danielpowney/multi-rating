<?php 

/**
 * Utils class
 * 
 * @author dpowney
 */
class MR_Utils {
	
	/**
	 * Gets the current URL
	 *
	 * @return current URL
	 */
	public static function get_current_url() {
		$url = 'http';
	
		if ( isset( $_SERVER['HTTPS'] ) && $_SERVER['HTTPS'] == 'on') {
			$url .= "s";
		}
	
		$url .= '://';
	
		if ( $_SERVER['SERVER_PORT'] != '80') {
			$url .= $_SERVER['SERVER_NAME'] . ':' . $_SERVER['SERVER_PORT'] . $_SERVER['REQUEST_URI'];
		} else {
			$url .= $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
		}
	
		return MR_Utils::normalize_url( $url );
	}
	
	/**
	 * Normalizes the URL (some of the best parts of RFC 3986)
	 *
	 * @param unknown_type $url
	 * @return string
	 */
	public static function normalize_url( $url ) {
	
		// TODO return error for bad URLs
	
		// Process from RFC 3986 http://en.wikipedia.org/wiki/URL_normalization
	
		// Limiting protocols.
		if ( ! parse_url( $url, PHP_URL_SCHEME ) ) {
			$url = 'http://' . $url;
		}
	
		$parsed_url = parse_url( $url );
		if ( $parsed_url === false ) {
			return '';
		}
	
		// user and pass components are ignored
	
		// TODO Removing or adding �www� as the first domain label.
		$host = preg_replace( '/^www\./', '', $parsed_url['host'] );
	
		// Converting the scheme and host to lower case
		$scheme = strtolower( $parsed_url['scheme'] );
		$host = strtolower( $host );
	
		$path = $parsed_url['path'];
		// TODO Capitalizing letters in escape sequences
		// TODO Decoding percent-encoded octets of unreserved characters
	
		// Removing the default port
		$port = '';
		if ( isset( $parsed_url['port'] ) ) {
			$port = $parsed_url['port'];
		}
		if ( $port == 80 ) {
			$port = '';
		}
	
		// Removing the fragment # (do not get fragment component)
	
		// Removing directory index (i.e. index.html, index.php)
		$path = str_replace( 'index.html', '', $path );
		$path = str_replace( 'index.php', '', $path );
	
		// Adding trailing /
		$path_last_char = $path[strlen( $path ) -1];
		if ( $path_last_char != '/' ) {
			$path = $path . '/';
		}
	
		// TODO Removing dot-segments.
	
		// TODO Replacing IP with domain name.
	
		// TODO Removing duplicate slashes
		$path = preg_replace( "~\\\\+([\"\'\\x00\\\\])~", "$1", $path );
	
		// construct URL
		$url =  $scheme . '://' . $host . $path;
	
		// Add query params if they exist
		// Sorting the query parameters.
		// Removing unused query variables
		// Removing default query parameters.
		// Removing the "?" when the query is empty.
		$query = '';
		if ( isset( $parsed_url['query'] ) ) {
			$query = $parsed_url['query'];
		}
		if ( $query ) {
			$query_parts = explode( '&', $query );
			$params = array();
			foreach ( $query_parts as $param ) {
				$items = explode( '=', $param, 2 );
				$name = $items[0];
				$value = '';
				if ( count( $items ) == 2 ) {
					$value = $items[1];
				}
				$params[$name] = $value;
			}
			ksort( $params );
			$count_params = count( $params );
			if ( $count_params > 0 ) {
				$url .= '?';
				$index = 0;
				foreach ( $params as $name => $value ) {
					$url .= $name;
					if ( strlen( $value ) != 0 ) {
						$url .= '=' . $value;
					}
					if ( $index++ < ( $count_params - 1 ) ) {
						$url .= '&';
					}
				}
			}
		}
	
		// Remove some query params which we do not want
		$url = MR_Utils::remove_query_string_params( $url, array() );
	
		return $url;
	}
	
	/**
	 * Removes query string parameters from URL
	 * @param $url
	 * @param $param
	 * @return string
	 *
	 * @since 1.2
	 */
	public static function remove_query_string_params( $url, $params ) {
		foreach ( $params as $param ) {
			$url = preg_replace( '/(.*)(\?|&)' . $param . '=[^&]+?(&)(.*)/i', '$1$2$4', $url . '&' );
			$url = substr( $url, 0, -1 );
		}
		return $url;
	}
	
	/**
	 * Gets the Font Awesome icon classes based on version
	 * 
	 * @param $icon_font_library
	 * @return array icon classes
	 */
	public static function get_icon_classes( $icon_font_library ) {
		
		$icon_classes = array();
		
		if ( $icon_font_library == 'font-awesome-4.0.3' || $icon_font_library == 'font-awesome-4.1.0'
				|| $icon_font_library == 'font-awesome-4.2.0' || $icon_font_library == 'font-awesome-4.3.0'
				|| $icon_font_library == 'font-awesome-4.5.0' || $icon_font_library == 'font-awesome-4.6.3'
				|| $icon_font_library == 'font-awesome-4.7.0' ) {
			$icon_classes['star_full'] = 'fa fa-star mr-star-full';
			$icon_classes['star_hover'] = 'fa fa-star mr-star-hover';
			$icon_classes['star_half'] = 'fa fa-star-half-o mr-star-half';
			$icon_classes['star_empty'] = 'fa fa-star-o mr-star-empty';
			$icon_classes['minus'] = 'fa fa-minus-circle mr-minus';
			$icon_classes['spinner'] = 'fa fa-spinner fa-spin mr-spinner';
		} else if ( $icon_font_library == 'font-awesome-3.2.1' ) {
			$icon_classes['star_full'] = 'icon-star mr-star-full';
			$icon_classes['star_hover'] = 'icon-star mr-star-hover';
			$icon_classes['star_half'] = 'icon-star-half-full mr-star-half';
			$icon_classes['star_empty'] = 'icon-star-empty mr-star-empty';
			$icon_classes['minus'] = 'icon-minus-sign mr-minus';
			$icon_classes['spinner'] = 'icon-spinner icon-spin mr-spinner';
		} else if ( $icon_font_library == 'dashicons' ) {
			$icon_classes['star_full'] = 'dashicons dashicons-star-filled mr-star-full';
			$icon_classes['star_hover'] = 'dashicons dashicons-star-filled mr-star-hover';
			$icon_classes['star_half'] = 'dashicons dashicons-star-half mr-star-half';
			$icon_classes['star_empty'] = 'dashicons dashicons-star-empty mr-star-empty';
		}
		
		return apply_filters( 'mr_icon_classes', $icon_classes, $icon_font_library );
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
	 * Perform save rating restriction type checks
	 *
	 * @param array validation_results
	 * @param int $post_id
	 */
	public static function validate_save_rating_restricton( $validation_results, $post_id ) {
	
		$general_settings = (array) get_option( Multi_Rating::GENERAL_SETTINGS );
		
		$hours = $general_settings[Multi_Rating::SAVE_RATING_RESTRICTION_HOURS_OPTION];
		$save_rating_restriction_types = $general_settings[Multi_Rating::SAVE_RATING_RESTRICTION_TYPES_OPTION];
		
		foreach ( $save_rating_restriction_types as $save_rating_restriction_type ) {
			
			if ( $save_rating_restriction_type == 'cookie' && isset( $_COOKIE[Multi_Rating::POST_SAVE_RATING_COOKIE . '-' . $post_id] ) ) {
					
				$custom_text_settings = (array) Multi_Rating::instance()->settings->custom_text_settings;
				
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
	 * Checks if any ratings items are required which means zero cannot be selected
	 */
	public static function validate_rating_item_required( $validation_results, $rating_items ) {
	
		foreach ( $rating_items as $rating_item ) {
			
			$rating_item_id = $rating_item['id'];
			$rating_item_value = $rating_item['value'];
	
			if ( $rating_item_value == 0 ) {
				global $wpdb;
	
				$query = 'SELECT required FROM ' . $wpdb->prefix . Multi_Rating::RATING_ITEM_TBL_NAME . ' WHERE rating_item_id = %d';
				$required = $wpdb->get_col( $wpdb->prepare( $query, $rating_item_id ), 0 );
	
				if ( $required[0] == true ) {
					$custom_text_settings = (array) Multi_Rating::instance()->settings->custom_text_settings;
	
					array_push( $validation_results, array(
							'severity' => 'error',
							'name' => 'rating_item_required_error',
							'field' => 'rating-item-' . $rating_item_id,
							'message' => $custom_text_settings[ Multi_Rating::FIELD_REQUIRED_ERROR_MESSAGE_OPTION ]
					) );
				}
			}
		}
	
		return $validation_results;
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
	
	/**
	 * Helper function to retrieve list of image sizes and dimensions
	 *
	 * @param $size
	 * @return
	 */
	public static function get_image_sizes( $size = '' ) {
	
		global $_wp_additional_image_sizes;
	
		$sizes = array();
		$get_intermediate_image_sizes = get_intermediate_image_sizes();
	
		// Create the full array with sizes and crop info
		foreach( $get_intermediate_image_sizes as $_size ) {
	
			if ( in_array( $_size, array( 'thumbnail', 'medium', 'large' ) ) ) {
	
				$sizes[ $_size ]['width'] = get_option( $_size . '_size_w' );
				$sizes[ $_size ]['height'] = get_option( $_size . '_size_h' );
				$sizes[ $_size ]['crop'] = (bool) get_option( $_size . '_crop' );
	
			} elseif ( isset( $_wp_additional_image_sizes[ $_size ] ) ) {
	
				$sizes[ $_size ] = array(
						'width' => $_wp_additional_image_sizes[ $_size ]['width'],
						'height' => $_wp_additional_image_sizes[ $_size ]['height'],
						'crop' =>  $_wp_additional_image_sizes[ $_size ]['crop']
				);
			}
		}
	
		// Get only 1 size if found
		if ( $size ) {
			if( isset( $sizes[ $size ] ) ) {
				return $sizes[ $size ];
			} else {
				return false;
			}
		}
	
		return $sizes;
	}
	
	/**
	 * Helper to sort by top rating results by percentage
	 *
	 * @param unknown_type $a
	 * @param unknown_type $b
	 */
	public static function sort_highest_rated_by_percentage_result_type( $a, $b ) {
		
		$a = isset( $a['rating_result'] ) ? $a['rating_result'] : $a;
		$b = isset( $b['rating_result'] ) ? $b['rating_result'] : $b;
	
		if ( $a['adjusted_percentage_result'] == $b['adjusted_percentage_result'] ) {
			
			if ( ! isset( $a['count'] ) ) {
				return 0;
			}
			
			if ( $a['count'] == $b['count'] ) {
				return 0;
			} else {
				return (  $a['count'] > $b['count'] ) ? -1 : 1;
			}
		}
	
		return ( $a['adjusted_percentage_result'] > $b['adjusted_percentage_result'] ) ? -1 : 1;
	}
	
	/**
	 * Helper to sort by top rating results by score
	 *
	 * @param unknown_type $a
	 * @param unknown_type $b
	 */
	public static function sort_highest_rated_by_score_result_type( $a, $b ) {
		
		$a = isset( $a['rating_result'] ) ? $a['rating_result'] : $a;
		$b = isset( $b['rating_result'] ) ? $b['rating_result'] : $b;
	
		if ( $a['adjusted_score_result'] == $b['adjusted_score_result'] ) {
			
			if ( ! isset( $a['count'] ) ) {
				return 0;
			}
			
			if ( $a['count'] == $b['count'] ) {
				return 0;
			} else {
				return (  $a['count'] > $b['count'] ) ? -1 : 1;
			}
		}
	
		return ( $a['adjusted_score_result'] > $b['adjusted_score_result'] ) ? -1 : 1;
	}
	
	/**
	 * Helper to sort by lowest rated percentage
	 *
	 * @param unknown_type $a
	 * @param unknown_type $b
	 */
	public static function sort_lowest_rated_by_percentage_result_type( $a, $b ) {
		
		$a = isset( $a['rating_result'] ) ? $a['rating_result'] : $a;
		$b = isset( $b['rating_result'] ) ? $b['rating_result'] : $b;
	
		if ( $a['adjusted_percentage_result'] == $b['adjusted_percentage_result'] ) {
			
			if ( ! isset( $a['count'] ) ) {
				return 0;
			}
			
			if ( $a['count'] == $b['count'] ) {
				return 0;
			} else {
				return (  $a['count'] < $b['count'] ) ? -1 : 1;
			}
		}
	
		return ( $a['adjusted_percentage_result'] < $b['adjusted_percentage_result'] ) ? -1 : 1;
	}
	
	/**
	 * Helper to sort by lowest rated score
	 *
	 * @param unknown_type $a
	 * @param unknown_type $b
	 */
	public static function sort_lowest_rated_by_score_result_type( $a, $b ) {
		
		$a = isset( $a['rating_result'] ) ? $a['rating_result'] : $a;
		$b = isset( $b['rating_result'] ) ? $b['rating_result'] : $b;
	
		if ( $a['adjusted_score_result'] == $b['adjusted_score_result'] ) {
			
			if ( ! isset( $a['count'] ) ) {
				return 0;
			}
			
			if ( $a['count'] == $b['count'] ) {
				return 0;
			} else {
				return (  $a['count'] < $b['count'] ) ? -1 : 1;
			}
		}
	
		return ( $a['adjusted_score_result'] < $b['adjusted_score_result'] ) ? -1 : 1;
	}
	
	/**
	 * Helper to sort by most entries
	 *
	 * @param unknown_type $a
	 * @param unknown_type $b
	 */
	public static function sort_most_entries( $a, $b ) {
		
		$a = isset( $a['rating_result'] ) ? $a['rating_result'] : $a;
		$b = isset( $b['rating_result'] ) ? $b['rating_result'] : $b;
	
		if ( $a['count'] == $b['count'] ) {
			return 0;
		}
	
		return ( $a['count'] > $b['count'] ) ? -1 : 1;
	}
	
	/**
	 * Helper to sort the rating results by latest entry date if it exists
	 *
	 * @param unknown_type $a
	 * @param unknown_type $b
	 */
	public static function sort_most_recent_by_entry_date( $a, $b ) {
		
		$entry_date_a = isset( $a['rating_result']['entry_date'] ) ? $a['rating_result']['entry_date'] : $a['entry_date']; 
		$entry_date_b = isset( $b['rating_result']['entry_date'] ) ? $b['rating_result']['entry_date'] : $b['entry_date']; 
	
		// sort by entry date
		if ( $entry_date_a == $entry_date_b ) {
			return 0;
		}
		return ( $entry_date_a > $entry_date_b ) ? -1 : 1;
	
		return 0;
	}
	
	/**
	 * Sorts rating results
	 *
	 * @param unknown $rating_results
	 * @param unknown $sort_by
	 * @return $rating_results
	 */
	public static function sort_rating_results( $rating_results, $sort_by, $result_type = 'star_rating' ) {
	
		if ( $sort_by == 'highest_rated' ) {
				
			if ( $result_type == Multi_Rating::SCORE_RESULT_TYPE) {
				uasort( $rating_results, array( 'MR_Utils' , 'sort_highest_rated_by_score_result_type' ) );
			} else {
				uasort( $rating_results, array( 'MR_Utils' , 'sort_highest_rated_by_percentage_result_type' ) );
			}
				
		} else if ( $sort_by == 'lowest_rated' ) {
				
			if ( $result_type == Multi_Rating::SCORE_RESULT_TYPE) {
				uasort( $rating_results, array( 'MR_Utils' , 'sort_lowest_rated_by_score_result_type' ) );
			} else {
				uasort( $rating_results, array( 'MR_Utils' , 'sort_lowest_rated_by_percentage_result_type' ) );
			}
				
		} else if ( $sort_by == 'most_entries' ) {
				
			uasort( $rating_results, array( 'MR_Utils' , 'sort_most_entries' ) );
				
		} else if ( $sort_by == 'most_recent' ) {
				
			uasort( $rating_results, array( 'MR_Utils' , 'sort_most_recent_by_entry_date' ) );
				
		}
	
		return $rating_results;
	}
}
?>