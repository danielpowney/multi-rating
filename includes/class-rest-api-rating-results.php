<?php

/**
 * 
 * REST API controller for rating results
 * @author dpowney
 *
 */
class MR_REST_API_Rating_Results extends MR_REST_API_Common {
	
	/**
	 * Constructor
	 */
	function __construct() {
		add_filter( 'mr_rest_api_rating_results_sanitize_params', array( $this, 'sanitize_parameters' ), 10, 2 );
		parent::__construct();
	}
	
	/**
	 * Register the routes for the objects of the controller.
	 */
	public function register_routes() {
		
		$version = '1';
		$namespace = 'mr/v' . $version;
		$base = 'rating-results';
		
		register_rest_route( $namespace, '/' . $base, array(
				array(
						'methods'         => WP_REST_Server::READABLE,
						'callback'        => array( $this, 'get_items' ),
						'permission_callback' => array( $this, 'get_items_permissions_check' ),
						'args'            => array(
								'limit' => array(
										'validate_callback' => array( $this, 'is_numeric_value' )
								),
								'taxonony' => array(
										'validate_callback' => array( $this, 'is_not_empty_value' )
								),
								'term_id' => array(
										'validate_callback' => array( $this, 'is_numeric_value' )
								),
								'sort_by' => array(
										'validate_callback' => array( $this, 'is_not_empty_value' )
								),
								'offset' => array(
										'validate_callback' => array( $this, 'is_numeric_value' )
								),
								'post_id' => array(
										'validate_callback' => array( $this, 'is_numeric_value' )
								)
						)
				)
		) );
		
	}
	
	/**
	 * Get a collection of items
	 *
	 * @param WP_REST_Request $request Full data about the request.
	 * @return WP_Error|WP_REST_Response
	 */
	public function get_items( $request ) {
		
		$allowed_parameters = array( 'taxonomy', 'term_id', 'limit', 'sort_by', /* 'offset', */ 'post_id' );
		
				$parameters = apply_filters( 'mr_rest_api_rating_results_sanitize_params', $request->get_query_params(), $allowed_parameters );
		
		$rating_result_list = Multi_Rating_API::get_rating_results( $parameters );
		
		return new WP_REST_Response( $rating_result_list, 200 );
	}
		
}