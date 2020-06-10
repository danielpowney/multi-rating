<?php
/**
 * Adds Multi Rating post meta custom fields
 * 
 * @param unknown $object
 * @param unknown $field_name
 * @param unknown $request
 */
function mr_rest_api_custom_fields( $object, $field_name, $request ) {
	
	$post_id = $object[ 'id' ];
	$custom_fields = array();
		
	$rating_result = get_post_meta( $post_id, Multi_Rating::RATING_RESULTS_POST_META_KEY );
	if ( isset( $rating_result ) ) {
		$custom_fields[Multi_Rating::RATING_RESULTS_POST_META_KEY] = $rating_result;
	}
	
	return $custom_fields;
}
