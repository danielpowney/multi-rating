<div class="rating-form <?php echo esc_attr( $class ); ?>">

	<?php
	if ( ! empty( $title ) ) {
		
		$before_title = apply_filters( 'mr_rating_form_before_title', $before_title, $post_id );
		$after_title = apply_filters( 'mr_rating_form_after_title', $after_title, $post_id );
		
		echo "$before_title" . esc_html( $title ) . "$after_title";
	}
	
	?>
	<form id="rating-form-<?php echo $post_id; ?>-<?php echo MR_Rating_Form::$sequence; ?>" action="#">
	<?php
	
		do_action( 'mr_rating_form_before_rating_items', $post_id, $rating_items );
		/**
		 * Rating Items
		 */
		foreach ( (array) $rating_items as $rating_item ) {
			
			$rating_item_id = $rating_item['rating_item_id'];
			$element_id = 'rating-item-' . $rating_item_id . '-' . MR_Rating_Form::$sequence ;
			$description = $rating_item['description'];
			$rating_item_type = $rating_item['type'];
			$max_option_value =  $rating_item['max_option_value'];
			$default_option_value = $rating_item['default_option_value'];
			$rating_item_type = $rating_item['type'];
			
			mr_get_template_part( 'rating-form', 'rating-item', true, array(
				'rating_item_id' => $rating_item_id,
				'element_id' => $element_id,
				'description' => $description,
				'max_option_value' => $max_option_value,
				'default_option_value' => $default_option_value,
				'class' => null,
				'style' => null,
				'icon_classes' => $icon_classes,
				'use_custom_star_images' => $use_custom_star_images,
				'element_id' => $element_id,
				'rating_item_type' => $rating_item_type,
			) );
			
			?>
			<!-- hidden field to get rating item id -->
			<input type="hidden" value="<?php echo $rating_item_id; ?>" class="rating-item-<?php echo $post_id; ?>-<?php echo MR_Rating_Form::$sequence; ?>" id="hidden-rating-item-id-<?php echo $rating_item_id; ?>" />
			<?php
		}
		
		do_action( 'mr_rating_form_before_buttons' );
		
		?>
		<input type="button" class="btn btn-default save-rating" id="saveBtn-<?php echo $post_id; ?>-<?php echo MR_Rating_Form::$sequence; ?>" value="<?php echo esc_attr( $submit_button_text ); ?>"></input>
		<input type="hidden" name="sequence" value="<?php echo MR_Rating_Form::$sequence; ?>" />
		
		<?php 
		do_action( 'mr_rating_form_after_buttons' );
		?>
	</form>
</div>