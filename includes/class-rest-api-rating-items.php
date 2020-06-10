<?php

/**
 * 
 * REST API controller for rating items
 * @author dpowney
 *
 */
class MR_REST_API_Rating_Items extends WP_REST_Controller {
	
	/**
	 * Constructor
	 */
	function __construct() {
		$this->register_routes();
	}
	
	/**
	 * Register the routes for the objects of the controller.
	 */
	public function register_routes() {
		
		$version = '1';
		$namespace = 'mr/v' . $version;
		$base = 'rating-items';
		
		register_rest_route( $namespace, '/' . $base, array(
				array(
						'methods'         => WP_REST_Server::READABLE,
						'callback'        => array( $this, 'get_items' ),
						'permission_callback' => array( $this, 'get_items_permissions_check' ),
						'args'            => array(
		
						),
				)
		) );
		register_rest_route( $namespace, '/' . $base . '/(?P<id>[\d]+)', array(
				array(
						'methods'         => WP_REST_Server::READABLE,
						'callback'        => array( $this, 'get_item' ),
						'permission_callback' => array( $this, 'get_item_permissions_check' ),
						'args'            => array(
								'context'          => array(
										'default'      => 'view',
								),
						),
				) 
		));
		
	}
	
	/**
	 * Get a collection of items
	 *
	 * @param WP_REST_Request $request Full data about the request.
	 * @return WP_Error|WP_REST_Response
	 */
	public function get_items( $request ) {
		
		$rating_items = Multi_Rating_API::get_rating_items();
		
		return new WP_REST_Response( $rating_items, 200 );
	}
	
	/**
	 * Get one item from the collection
	 *
	 * @param WP_REST_Request $request Full data about the request.
	 * @return WP_Error|WP_REST_Response
	 */
	public function get_item( $request ) {
	
		$parameters = $request->get_url_params();
	
		$rating_items = Multi_Rating_API::get_rating_items();
		
		$rating_item = isset( $rating_items[$parameters['id']] ) ? $rating_items[$parameters['id']] : null;
	
		return new WP_REST_Response( $rating_item, 200 );
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
		
}