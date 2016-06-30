<?php 
/**
 * Rating result star rating template
 */
$generate_microdata = isset( $generate_microdata ) && $generate_microdata;
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
	
	if ( $generate_microdata ) {
		echo '<span itemprop="ratingValue">';
	}
	echo $star_result;
	if ( $generate_microdata ) {
		echo '</span>';
	}
	
	echo esc_html( $out_of_text );
	
	if ( $generate_microdata ) {
		echo '<span itemprop="bestRating">';
	}
	echo $max_stars;
	if ( $generate_microdata ) {
		echo '</span>';
	}
	?>
</span>