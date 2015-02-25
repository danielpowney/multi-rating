<?php 

/**
 * Rating form rating item template
 */
?>
<p class="rating-item mr <?php if ( isset( $class ) ) { echo $class; } ?>" <?php if ( isset( $style ) ) { echo 'style="' . $style . '"'; } ?>>
	<label class="description" for="<?php echo $element_id; ?>"><?php echo $description; ?></label>
			
	<?php
	if ( $rating_item_type == "star_rating" ) {
		
		$template_part_name = 'star-rating';
		if ( $use_custom_star_images ) {
			$template_part_name = 'custom-star-images';
		}
		
		mr_get_template_part( 'rating-form', $template_part_name, true, array(
			'max_option_value' => $max_option_value,
			'default_option_value' => $default_option_value,
			'element_id' => $element_id,
			'icon_classes' => $icon_classes,
			'rating_item_type' => $rating_item_type
		) );
		
	} else if ( $rating_item_type == 'select' ){
		
		mr_get_template_part( 'rating-form', 'select', true, array(
			'element_id' => $element_id,
			'max_option_value' => $max_option_value,
			'default_option_value' => $default_option_value,
			'rating_item_type' => $rating_item_type
		) );
	
	} else { // radio
			
		mr_get_template_part( 'rating-form', 'radio', true, array(
			'default_option_value' => $default_option_value,
			'element_id' => $element_id,
			'max_option_value' => $max_option_value,
			'rating_item_type' => $rating_item_type
		) );
		
	}	
?>
</p>