<?php 
/**
 * Rating result custom star images template
 */
?>
<span class="mr-star-rating">
	
	<?php
    $index = 0;			
    for ( $index; $index < $max_stars; $index++ ) {
    		
    	$class = 'mr-custom-full-star';
    	
    	if ( $star_result < $index+1 ) {
    	    
    		$diff = $star_result - $index;
    		
    		if ( $diff > 0 ) {
    			
    			if ( $diff >= 0.3 && $diff <= 0.7 ) {
    				$class ='mr-custom-half-star';
    			} else if ( $diff < 0.3 ) {
    				$class = 'mr-custom-empty-star';
    			} else {
    				$class = 'mr-custom-full-star';
    			}
    			
    		} else {
    			$class = 'mr-custom-empty-star';
    		}
    	
    	} else {
    		$class = 'mr-custom-full-star';
    	}
    	
    	?>
    	<span class="<?php echo $class; ?>"  width="<?php echo $image_width; ?>px" height="<?php echo $image_height; ?>px"></span>
    	<?php
   	} 			
?>
</span>

<span class="star-result">
	<?php 
	$out_of_text = apply_filters( 'mr_out_of_text', '/' );
	echo $star_result . esc_html( $out_of_text ) . $max_stars; 
	?>
</span>