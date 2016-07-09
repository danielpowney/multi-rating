<?php 

$generate_microdata = ( isset( $generate_microdata ) && $generate_microdata ) 
		|| ( isset( $show_rich_snippets ) && $show_rich_snippets == true );
$count = isset( $rating_result['count'] ) ? $rating_result['count'] : 0;
if ( $count == null || $count == 0 ) {
	$generate_microdata = false;
}
?>

<span class="rating-result <?php echo esc_attr( $class ); ?>" <?php 
if ( $generate_microdata ) {
	$microdata = 'itemscope itemtype="http://schema.org/AggregateRating"';
	echo apply_filters( 'mr_microdata_aggregate_rating_attributes', $microdata, $post_id );
} ?>>
	<?php
	if ( ( $count == null || $count == 0 ) && $ignore_count == false ) {
		// ignore count is used to not show this block
		$no_rating_results_text = apply_filters( 'mr_no_rating_results_text', $no_rating_results_text );
		?>
		<span class="no-rating-results-text"><?php echo esc_html( $no_rating_results_text ); ?></span>
		<?php
	} else {
		
		$post_obj = get_post( $post_id );
		
		if ( $show_title == true ) {
			?>
			<a href="<?php echo esc_attr( get_permalink( $post_id ) ); ?>"><?php echo esc_html( $post_obj->post_title ); ?></a>
			<?php
		}
		
		do_action( 'mr_rating_result_before_result_type' );
			
		if ( $result_type == Multi_Rating::SCORE_RESULT_TYPE ) {
			
			mr_get_template_part( 'rating-result', 'score', true, array( 
					'rating_result' => $rating_result,
					'generate_microdata' => $generate_microdata
			 ) );
			
		} else if ( $result_type == Multi_Rating::PERCENTAGE_RESULT_TYPE ) {
			
			mr_get_template_part( 'rating-result', 'percentage', true, array( 
					'rating_result' => $rating_result,
					'generate_microdata' => $generate_microdata
			 ) );
			
		} else { // star rating
			
			$max_stars = 5;
			$star_result = $rating_result['adjusted_star_result'];
			
			if ( $preserve_max_option ) {
				$max_stars = $rating_result['total_max_option_value'];
				$star_result = $rating_result['adjusted_score_result'];
			}
			
			$template_part_name = 'star-rating';
			if ( $use_custom_star_images ) {
				$template_part_name = 'custom-star-images';
			}
			
			mr_get_template_part( 'rating-result', $template_part_name, true, array( 
				'max_stars' => $max_stars, 
				'star_result' => $star_result,
				'icon_classes' => $icon_classes,
				'image_height' => $image_height,
				'image_width' => $image_width,
				'generate_microdata' => $generate_microdata
			) );
		
		}
		
		do_action( 'mr_rating_result_after_result_type' );
			
		if ( $show_count && $count != null ) {
			
			$before_count = apply_filters( 'mr_rating_result_before_count', $before_count, $count );
			$after_count = apply_filters( 'mr_rating_result_after_count', $after_count, $count );
			
			?>
			<span class="count">
				<?php 
				echo $before_count;
				if ( $generate_microdata ) { 
					echo '<span itemprop="ratingCount">'; 
				}
				echo number_format( $count );
				if ( $generate_microdata ) {
					echo '</span>';
				}
				echo $after_count; 
				?>
			</span>
			<?php
		}
			
		if ( $show_date == true && isset( $rating_result['entry_date'] ) ) {
			
			$before_date = apply_filters( 'mr_rating_result_before_date', $before_date, $count );
			$after_date= apply_filters( 'mr_rating_result_after_date', $after_date, $count );
			
			?>
			<span class="date"><?php echo $before_date . mysql2date( get_option('date_format'), $rating_result['entry_date'] ) . $after_date; ?></span>
			<?php
		}
		
		if ( $generate_microdata ) {
			$microdata = '<span itemprop="itemReviewed" itemscope itemtype="http://schema.org/Thing">'
					. '<meta itemprop="name" content="' . $post_obj->post_title . '" />'
					. '</span>';
			echo apply_filters( 'mr_microdata_rating_result_item_reviewed', $microdata, $post_id );
		}
		
	}
	?>
</span>