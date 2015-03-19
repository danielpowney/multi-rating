<span class="score-result">
	<?php
	$out_of_text = apply_filters( 'mr_out_of_text', '/' );
	echo $rating_result['adjusted_score_result'] . esc_html( $out_of_text ) . $rating_result['total_max_option_value']; 
	?>
</span>