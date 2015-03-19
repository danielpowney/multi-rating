 	<?php 
/**
 * Rating form template for custom star images
 */

$include_minus = apply_filters( 'mr_rating_form_include_minus', true);
?>

<span class="mr-star-rating mr-star-rating-select">
	<?php
	// add star icons
	$index = 0;
	for ( $index; $index <= $max_option_value; $index++ ) {
		
		if ( $index == 0  && $include_minus ) {
			$class = $icon_classes['minus'] . ' index-' . $index . '-' . $element_id;
			
			?>
			<i id="index-<?php echo $index; ?>-<?php echo $element_id; ?>" class="<?php echo $class; ?>"></i>
			<?php
			
			continue;
		}
				
		$class = 'mr-star-full mr-custom-full-star';
			
		// if default is less than current index, it must be empty
		if ( $default_option_value < $index ) {
			$class = 'mr-star-empty mr-custom-empty-star';
		}
		
		$class .= ' index-' . $index . '-' . $element_id;
			
		?>
		<span title="<?php echo esc_attr( $index ); ?>" id="index-<?php echo $index; ?>-<?php echo $element_id; ?>" class="<?php echo $class; ?>" style="text-align: left; display: inline-block;"></span>
		<?php
	} ?>
</span>	
			
<!-- hidden field for storing selected star rating value -->
<input type="hidden" name="<?php echo $element_id; ?>" id="<?php echo $element_id; ?>" value="<?php echo $default_option_value; ?>">
