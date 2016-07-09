<?php 
/**
 * Rating result percentage rating template
 */
$generate_microdata = isset( $generate_microdata ) && $generate_microdata;
?>
<span class="percentage-result">
	<?php 
	if ( $generate_microdata ) {
		echo '<span itemprop="ratingValue">';
	}
	echo $rating_result['adjusted_percentage_result'];
	if ( $generate_microdata ) {
		echo '</span>';
	}
	echo '%';
	if ( $generate_microdata ) {
		echo '<meta itemprop="bestRating" content="100">';
	}
	?>
</span>