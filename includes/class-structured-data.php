<?php

/**
 * Strutured data class
 * 
 * @author dpowney
 *
 */
class MR_Structured_Data {

	/**
	 * Constructor
	 */
	public function __construct() {

		$general_settings = (array) get_option( Multi_Rating::GENERAL_SETTINGS );
		$add_structured_data = $general_settings[ Multi_Rating::ADD_STRUCTURED_DATA_OPTION ];
	
		if ( isset( $add_structured_data ) ) {
			
			if ( ! is_array( $add_structured_data ) && is_string( $add_structured_data ) ) {
				$add_structured_data = array( $add_structured_data );
			}
			
			// Create new type
			if ( in_array( 'create_type', $add_structured_data ) ) {
				add_action( 'wp_head', array( $this, 'create_new_type' ) );
			}

			// WordPress SEO by Yoast
			if ( in_array( 'wpseo', $add_structured_data ) ) {
				add_filter( 'wpseo_schema_graph_pieces', array( $this, 'wpseo_hijack_pieces' ), 99, 2  );
			}

			// WooCommerce products
			if ( in_array( 'woocommerce', $add_structured_data ) ) {
				add_filter( 'woocommerce_structured_data_product', array( $this, 'woocommerce_product' ), 10, 2);
			}

		}

	}

	/**
	 * Hijack Yoast SEO pieces and inject aggregateRating or Review to main entity where supported
	 */
	public function wpseo_hijack_pieces( $pieces, $context ) {
	    
	    foreach( $pieces as $index => $piece ) {
			$path = explode('\\', get_class( $piece) );
			$className = array_pop( $path );
	    	add_filter( 'wpseo_schema_' . strtolower( $className ), array( $this, 'wpseo_structured_data' ), 99, 1 );
	    }

	    return $pieces;
	}


	/**
	 * Creates new type on the post with AggregateRating structured data
	 */
	public function create_new_type() {

		if ( is_page() || is_single() ) {

	    	$post_id = get_queried_object_id();
	    	if ($post_id == null) {
	    		return;
	    	}

			$structured_data_type = get_post_meta( $post_id, Multi_Rating::STRUCTURED_DATA_TYPE_POST_META, true );

			$structured_data_type = apply_filters( 'mr_structured_data_item_type', $structured_data_type, $post_id );

			if ( $structured_data_type == '' ) {
				return;
			}

			/*
			 * Don't add piece if WooCommerce structured data is enabled
			 */
			$general_settings = (array) get_option( Multi_Rating::GENERAL_SETTINGS );
			$add_structured_data = $general_settings[ Multi_Rating::ADD_STRUCTURED_DATA_OPTION ];
			if ( class_exists( 'woocommerce' ) && in_array( 'woocommerce', $add_structured_data ) 
				&& get_post_type( $post_id ) === 'product' ) { 
				return;
			}

			/*
			 * Get data to create aggregate rating structured data
			 */
	    	$rating_result = Multi_Rating_API::get_rating_result( $post_id );

			if ($rating_result == null 
				|| ($rating_result !== null && $rating_result['count'] === 0)) {
				return;
			}

	    	$post_title = get_the_title( $post_id );
	    	$post_thumbnail_url = get_the_post_thumbnail_url( $post_id );
	    	$post_excerpt = get_the_excerpt( $post_id );

			?>
    <script type="application/ld+json">
    {
	    "@context": "https://schema.org/",
        "@type": "<?php echo $structured_data_type; ?>",
        "name": "<?php echo htmlspecialchars( addslashes( $post_title ), ENT_NOQUOTES ); ?>",
<?php if ($post_thumbnail_url) { ?>
        "image": [
       	    "<?php echo $post_thumbnail_url; ?>"
        ],
<?php } if ($post_excerpt) { ?>
        "description": "<?php echo htmlspecialchars( addslashes( $post_excerpt ), ENT_NOQUOTES ); ?>",
<?php }
echo apply_filters( 'mr_structured_data_type', '', $post_id ); ?>
        "aggregateRating": {
            "@type": "AggregateRating",
            "ratingValue": "<?php echo $rating_result['adjusted_star_result']; ?>",
            "reviewCount": "<?php echo $rating_result['count']; ?>"
        }
    }
	</script>
			<?php
		}
	}


	/**
	 * Adds AggregateRating structured data to WP SEO plugin main entity schema. I
	 * Note if another plugin or theme changes the Article schema type to another 
	 * type such as Product or Book, this will carry over :)
	 *
	 * @parameter 	schema
	 */
	public function wpseo_structured_data( $schema ) {

		// Check if article type has been overriden to a supported type for 
		// aggregate rating and review. Note each schema type has different 
		// requirements. The name attribute is quite important too.

	    $supported_types = [ 'Book', 'Course', 'CreativeWorkSeason', 
	    	'CreativeWorkSeries', 'Episode', 'Event', 'Game', 'HowTo', 
	    	'LocalBusiness', 'MediaObject', 'Movie', 'MusicPlaylist', 
	    	'MusicRecording', 'Organization', 'Product', 'Recipe', 
	    	'SoftwareApplication' ];

	    if ( ! in_array( $schema['@type'], $supported_types ) || ! isset( $schema['mainEntityOfPage']) ){
			return $schema;
		}

		$post_id = get_queried_object_id();
	    $rating_result = Multi_Rating_API::get_rating_result( $post_id );

	    if ($rating_result == null 
				|| ($rating_result !== null && $rating_result['count'] === 0)) {
			return $schema;
		}
		
		$schema['aggregateRating'] = array(
		   	'@type' => "AggregateRating",
		   	'reviewCount' => $rating_result['count'],
		   	'ratingValue' => $rating_result['adjusted_star_result']
		);

	    return $schema;
	}


	/**
	 * Adds AggregateRating structured data for WooCommerce products
	 *
	 * @parameter 	schema
	 * @parameter 	product
	 */
	function woocommerce_product( $schema, $product ) {	

		$rating_result = Multi_Rating_API::get_rating_result( $product->get_id() );

		if ($rating_result == null 
				|| ($rating_result !== null && $rating_result['count'] === 0)) {
			return $schema;
		}

		$schema['aggregateRating'] = array(
		   	'@type' => "AggregateRating",
		   	'reviewCount' => $rating_result['count'],
		   	'ratingValue' => $rating_result['adjusted_star_result']
		);

	    return $schema;
	}

}
