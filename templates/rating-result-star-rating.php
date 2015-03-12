<?php 
/**
 * Rating result star rating template
 */
?>
<span class="mr-star-rating">
	
	<?php
    $index = 0;
    for ( $index; $index < $max_stars; $index++ ) {
    		
		$class = $icon_classes['star_full'];
	    
	    if ( $star_result < $index+1 ) {
	    		
	    	$diff = $star_result - $index;

	    	if ( $diff > 0 ) {
	    		if ( $diff >= 0.3 && $diff <= 0.7 ) {
	    			$class = $icon_classes['star_half'];
	    		} else if ( $diff < 0.3 ) {
	    			$class = $icon_classes['star_empty'];
	    		} else {
	    			$class = $icon_classes['star_full'];
	    		}
	    		
	    	} else {
	    		$class = $icon_classes['star_empty'];
	    	}
	    		
	    } else {
	    	$class = $icon_classes['star_full'];
	    }
	    			
	    ?>
	    <i class="<?php echo $class; ?>"></i>
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