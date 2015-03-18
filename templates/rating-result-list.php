<?php 
/**
 * Rating Result List template
 */
?>
<div class="rating-results-list <?php if ( isset( $class ) ) { echo esc_attr( $class ); } ?>">
	<?php
	if ( ! empty( $title ) ) {
		
		$before_title = apply_filters( 'mr_rating_results_list_before_title', $before_title );
		$after_title = apply_filters( 'mr_rating_results_list_after_title', $after_title );
		
		echo "$before_title" . esc_html( $title ) . "$after_title";
	}
	
	if ( $show_filter == true && $taxonomy ) {
			
		?>
		<form action="" class="mr-filter" method="POST">
			
			<label for="term-id"><?php echo esc_html( $filter_label_text ); ?></label>
			<select id="term-id" name="term-id" class="term-id">
			
			<?php
			$selected = '';
			if ( $term_id == 0) {
				$selected = 'selected="selected"';
			}
			?>
			
			<option value="" <?php echo $selected; ?>><?php _e( 'All', 'multi-rating-pro' ); ?></option>
			
			<?php
			$terms = get_terms( $taxonomy );
			foreach ( $terms  as $current_term ) {
				$selected = '';
				if ( $current_term->term_id === $term_id ) {
					$selected = 'selected="selected"';
				}
				?>
				
				<option value="<?php echo $current_term->term_id; ?>" <?php echo $selected; ?>><?php echo esc_html( $current_term->name ); ?></option>
				<?php
			}
			?>
			</select>
			
			<input type="submit" value="<?php echo esc_attr( $filter_button_text ); ?>" />
		</form>
	<?php
	
	do_action( 'mr_rating_results_list_after_filter' );
	}
	
	if ( count( $rating_results ) == 0 ) {
		
		$no_rating_results_text = apply_filters( 'mr_no_rating_results_text', $no_rating_results_text );
		
		?>
		<p class="mr"><?php echo esc_html( $no_rating_results_text ); ?></p>
		<?php
	} else {	
		?>
		<table>
		<?php
		
		$index = 1;
			
		foreach ( $rating_results as $rating_result ) {
				
			$post_id = $rating_result['post_id'];
			$post_obj = get_post( $post_id );

			?>
			<tr>
			<?php
			
			do_action( 'mr_rating_results_list_row_before_first_td', $post_id, $rating_result );
				
			// Rank
			if ( $show_rank ) {
				?>
				<td>
					<span class="rank"><?php echo $index; ?></span>
				</td>
				<?php
			}
			
			// If a thumbnail exists, it has a different layout
			$featured_img_shown = false;
			if ( $show_featured_img == true ) {
				if ( has_post_thumbnail( $post_id ) ) {
					?>
					<td class=" mr-featured-img">
						<?php
						do_action( 'mr_rating_results_list_before_featured_img', $post_id );
						echo get_the_post_thumbnail( $post_id, $image_size );
						do_action( 'mr_rating_results_list_after_featured_img', $post_id );
						?>
					</td>
					<?php
					$featured_img_shown = true;
				}
			}
			?>
			<td<?php
			if ( ! $featured_img_shown ) {
				?> colspan="2"<?php
			}
			?>>
			
			
			<a class="title" href="<?php echo esc_attr( get_the_permalink( $post_id ) ); ?>"><?php echo esc_html( $post_obj->post_title ); ?></a>
			
			<?php
			if ( $featured_img_shown == true ) {
				?>
				<br />
				<?php
			}
			
			mr_get_template_part( 'rating-result', null, true, array(
				'no_rating_results_text' => '',
				'ignore_count' => true,
				'show_rich_snippets' => false,
				'show_title' => false,
				'before_title' => $before_title,
				'after_title' => $after_title,
				'show_date' => false,
				'show_count' => $show_count,
				'result_type' => $result_type,
				'class' => $class . ' rating-result-list-' . $post_id,
				'rating_result' => $rating_result,
				'before_count' => $before_count,
				'after_count' => $after_count,
				'post_id' => $post_id,
				'preserve_max_option' => false,
				'before_date' => $before_date,
				'after_date' => $after_date,
				'icon_classes' => $icon_classes,
				'use_custom_star_images' => $use_custom_star_images,
				'image_width' => $image_width,
				'image_height' => $image_height
			) );
			
			if ( $featured_img_shown && isset( $show_date ) && $show_date == true && isset( $rating_result['entry_date'] ) ) {
				?>
				<br />
				<span class="entry-date"><?php echo "$before_date" . mysql2date( get_option( 'date_format' ), $rating_result['entry_date'] ) . "$after_date"; ?></span>
				<?php
			}
			
			?>
			</td>
			<?php
			
			if ( ! $featured_img_shown && isset( $show_date ) && $show_date == true && isset( $rating_result['entry_date'] ) ) {
				?>
				<td>
					<span class="entry-date"><?php echo "$before_date" . mysql2date( get_option( 'date_format' ), $rating_result['entry_date'] ) . "$after_date"; ?></span>
				</td>
				<?php
			}
			
			do_action( 'mr_rating_results_list_row_after_last_td', $post_id, $rating_result );
			
			?>
			</tr>
			<?php

			$index++;
		}
			
		?>
	</table>
	<?php
	}
?>
</div>