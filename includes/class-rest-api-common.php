<?php
/**
 * 
 * @author dpowney
 *
 */
class MR_REST_API_Common extends WP_REST_Controller {
	
	/**
	 * Constructor
	 */
	function __construct() {
		$this->register_routes();
	}

	/**
	 * Check if a given request has access to get items
	 *
	 * @param WP_REST_Request $request Full data about the request.
	 * @return WP_Error|bool
	 */
	public function get_items_permissions_check( $request ) {
		return true;
	}
	
	/**
	 * Check if a given request has access to get a specific item
	 *
	 * @param WP_REST_Request $request Full data about the request.
	 * @return WP_Error|bool
	 */
	public function get_item_permissions_check( $request ) {
		return true;
	}
	
	/**
	 * is_numeric()
	 * 
	 * @param unknown $value
	 * @param unknown $param
	 * @param unknown $request
	 * @return boolean
	 */
	public function is_numeric_value( $value, $param, $request ) {
		return is_numeric( $value );
	}
	
	/**
	 * Checks date format
	 *
	 * @param unknown $value
	 * @param unknown $param
	 * @param unknown $request
	 * @return boolean
	 */
	public function is_date_value( $value, $param, $request ) {
		
		if ( ! $this->is_not_empty_value( $value ) ) {
			return false;
		}
		
		if ( $value != null && strlen( $value ) > 0 ) {
			list( $year, $month, $day ) = explode( '-', $value ); // default yyyy-mm-dd format
			if ( ! checkdate( $month , $day , $year ) ) {
				return false;
			}
		}
		
		// if all passes
		return true;
	}
	
	/**
	 * is_numeric() on array values
	 *
	 * @param unknown $value
	 * @param unknown $param
	 * @param unknown $request
	 * @return boolean
	 */
	public function is_numeric_array_values( $values, $param, $request ) {
		
		if ( empty( $values ) || ! is_array( $values ) ) {
			return false;
		}
		
		foreach ( $values as $value ) {
			if ( ! is_numeric( $value ) ) {
				return false;
			}
		}
		
		// if all passes
		return true;
	}

	
	/**
	 * strlen > 0 on array values
	 *
	 * @param unknown $value
	 * @param unknown $param
	 * @param unknown $request
	 * @return boolean
	 */
	public function is_string_array_values( $values, $param, $request ) {
	
		if ( empty( $values ) || ! is_array( $values ) ) {
			return false;
		}
	
		foreach ( $values as $value ) {
			if ( strlen( trim( $value ) ) == 0 ) {
				return false;
			}
		}
	
		// if all passes
		return true;
	}
	
	/**
	 * ! empty() and checks for whitespace string
	 *
	 * @param unknown $value
	 * @param unknown $param
	 * @param unknown $request
	 * @return boolean
	 */
	public function is_not_empty_value( $value, $param, $request ) {
	
		if ( empty( $value ) || strlen( trim( $value ) ) == 0 ) {
			return false;
		}
	
		// if all passes
		return true;
	}
	
	/**
	 * ! empty() and checks for whitespace string
	 *
	 * @param unknown $value
	 * @param unknown $param
	 * @param unknown $request
	 * @return boolean
	 */
	public function is_entry_status_value( $value, $param, $request ) {
		
		if ( $value != 'approved' && $value != 'pending' && ! ( empty( $value ) 
				|| strlen( trim( $value ) ) == 0 ) ) {
			return false;
		}
	
		// if all passes
		return true;
	}
	
	/**
	 * checks value is true or false
	 *
	 * @param unknown $value
	 * @param unknown $param
	 * @param unknown $request
	 * @return boolean
	 */
	public function is_boolean_value( $value, $param, $request ) {
	
		if ( ! ( ( is_string( $value ) && $value == 'true' || $value == 'false' ) 
				|| is_bool( $value ) ) ) {
			return false;
		}
	
		// if all passes
		return true;
	}
	
	/**
	 * Only keeps allowed parameters
	 * 
	 * @param unknown $parameters
	 * @param unknown $allowed_parameters
	 * @return unknown
	 */
	public function keep_allowed_parameters( $parameters, $allowed_parameters ) {
		
		// limit to only allowed parameters
		foreach ( $parameters as $name => $value ) {
			if ( ! in_array( $name, $allowed_parameters ) ) {
				unset( $parameters[$name] );
			}
		}
		
		return $parameters;
	}
	
	/**
	 * Sanitize parameters before use
	 *
	 * @param array $parameters
	 * @param array $allowed_parameters
	 */
	public function sanitize_parameters( $parameters, $allowed_parameters ) {
		
		$default_parameter_values = array(
				'limit' => 10,
				'taxonomy' => null,
				'term_id' => null, // 0 = All
				'sort_by' => 'highest_rated',
				'to_date' => null,
				'from_date' => null,
				'offset' => 0,
				'post_ids' => null,
				'entry_status' => null,
				'published_posts_only' => true,
				'post_id' => null, 
				'user_id' => null
		);
		
		$parameters = $this->keep_allowed_parameters( $parameters, $allowed_parameters );
		
		// go through and sanitize each parameter, set default values if necessary
		
		foreach ( $parameters as $param_name => $value ) {
			if ( isset( $parameters[$param_name]) ) {
			
				switch ( $param_name ) {	
					case 'taxonomy' :
						if ( empty( $value ) || strlen( trim( $value ) ) == 0 ) {
							$parameters[$param_name] = $default_parameter_values[$param_name];
						}
						break;
					case 'term_id' :
					case 'user_id' :
					case 'limit' :
						if ( empty( $value ) || $value == 0 || ! is_numeric( $value ) ) {
							$parameters[$param_name] = $default_parameter_values[$param_name];
						} else {
							$parameters[$param_name] = intval( $value );
						}
						break;
					case 'offset' :
						if ( empty( $value ) || ! is_numeric( $value ) ) {
							$parameters[$param_name] = $default_parameter_values[$param_name];
						} else {
							$parameters[$param_name] = intval( $value );
						}
						break;
					case 'post_id' :
						if ( empty( $value ) ) {
							$parameters[$param_name] = $default_parameter_values[$param_name];
						} else {
							$parameters[$param_name] = intval( apply_filters( 'mr_object_id', $value, apply_filters( 'mr_default_language', null ) ) );
						}
						break;
					case 'sort_by' :
						if ( ! ( $value == 'highest_rated' || $value == 'most_recent' || $value == 'lowest_rated' 
								|| $value == 'post_title_asc' || $value == 'post_title_desc' || $value == 'most_entries'
								|| $value == 'oldest' ) ) {
							$parameters[$param_name] = $default_parameter_values[$param_name];
						}
						break;
					case 'to_date' :
					case 'from_date' :
						if ( $value != null && strlen( $value ) > 0 ) {
							list( $year, $month, $day ) = explode( '-', $value ); // default yyyy-mm-dd format
							if ( ! checkdate( $month , $day , $year ) ) {
								$parameters[$param_name] = $default_parameter_values[$param_name];
							}
						} else {
							$parameters[$param_name] = $default_parameter_values[$param_name];
						}
						break;
						
					// TODO terms
						
					default;
						break;
				}
			}
		}

		return $parameters;
	}
}