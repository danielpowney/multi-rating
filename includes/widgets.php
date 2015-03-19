<?php

/**
 * Rating Results List Widget for Multi Rating plugin
 */
class MR_Rating_Results_List_Widget extends WP_Widget {

	/**
	 * Constructor
	 */
	
	function __construct( ) {
	
		$id_base = 'mr_rating_results_list';
		$name = __( 'Rating Results List Widget', 'multi-rating' );
		$widget_opts = array(
				'classname' => 'rating-results-list-widget',
				'description' => __('Rating Results List Widget', 'multi-rating' )
		);
		$control_ops = array( 'width' => 400, 'height' => 350 );
	
		parent::__construct( $id_base, $name, $widget_opts, $control_ops );
	}
	
	/**
	 * (non-PHPdoc)
	 * @see WP_Widget::widget()
	 */
	function widget( $args, $instance ) {
	
		extract($args);
	
		$title = apply_filters( 'widget_title', empty( $instance['title'] ) ? '' : $instance['title'], $instance, $this->id_base );
		$limit = empty( $instance['limit'] ) ? 10 : intval( $instance['limit'] );
		$taxonomy =  empty( $instance['taxonomy'] ) ? '' : $instance['taxonomy'];
		$term_id = 0;
		if ( ! empty( $instance['term_id'] ) && is_numeric( $instance['term_id'] ) ) {
			$term_id = intval( $instance['term_id'] );
		}
		$show_filter = empty( $instance['show_filter'] ) ? false : $instance['show_filter'];
		$show_featured_img = empty( $instance['show_featured_img'] ) ? false : $instance['show_featured_img'];
		$image_size = empty( $instance['image_size'] ) ? 'thumbnail' : $instance['image_size'];
		$header = empty( $instance['header'] ) ? 'h3' : $instance['header'];
		$sort_by =  empty( $instance['sort_by'] ) ? 'highest_rated' : $instance['sort_by'];
		$filter_label_text =  $instance['filter_label_text'];
		$show_rank = empty( $instance['show_rank'] ) ? false : $instance['show_rank'];
		$result_type = empty( $instance['result_type'] ) ? 'star_rating' : $instance['result_type'];
	
		$before_title = '<' . $header . ' class="widget-title">';
		$after_title = '</' . $header . '>';
		
		$title = apply_filters( 'widget_title', $title );
	
		$custom_text_settings = (array) get_option( Multi_Rating::CUSTOM_TEXT_SETTINGS );
	
		echo $before_widget;
	
		Multi_Rating_API::display_rating_results_list( array(
			'limit' => $limit, 'title' => $title,
			'show_filter' => $show_filter,
			'taxonomy' => $taxonomy,
			'term_id' => $term_id,
			'class' => 'mr-widget',
			'before_title' => $before_title,
			'after_title' => $after_title,
			'show_featured_img' => $show_featured_img,
			'image_size' => $image_size,
			'sort_by' => $sort_by,
			'show_rank' => $show_rank,
			'filter_label_text' => $filter_label_text,
			'result_type' => $result_type
		) );
	
		echo $after_widget;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see WP_Widget::update()
	 */
	function update( $new_instance, $old_instance ) {
	
		$instance = $old_instance;
	
		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['limit'] = intval( $new_instance['limit'] );
		$instance['taxonomy'] = $new_instance['taxonomy'];
		$instance['term_id'] = 0;
		if ( ! empty($new_instance['term_id'] ) && is_numeric( $new_instance['term_id'] ) ) {
			$instance['term_id'] = intval( $new_instance['term_id'] );
		}
		$instance['show_filter'] = false;
		if ( isset( $new_instance['show_filter'] ) && ( $new_instance['show_filter'] == 'true' ) ) {
			$instance['show_filter'] = true;
		}
		$instance['show_featured_img'] = false;
		if ( isset( $new_instance['show_featured_img'] ) && ( $new_instance['show_featured_img'] == 'true' ) ) {
			$instance['show_featured_img'] = true;
		}
		$instance['show_rank'] = false;
		if ( isset( $new_instance['show_rank'] ) && ( $new_instance['show_rank'] == 'true' ) ) {
			$instance['show_rank'] = true;
		}
		$instance['image_size'] = $new_instance['image_size'];
		$instance['header'] = $new_instance['header'];
		$instance['sort_by'] = $new_instance['sort_by'];
		$instance['filter_label_text'] = $new_instance['filter_label_text'];
		$instance['result_type'] = $new_instance['result_type'];
	
		return $instance;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see WP_Widget::form()
	 */
	function form( $instance ) {
	
		$custom_text_settings = (array) get_option( Multi_Rating::CUSTOM_TEXT_SETTINGS );
		$general_settings = (array) get_option( Multi_Rating::GENERAL_SETTINGS );
	
		$instance = wp_parse_args( (array) $instance, array(
				'title' => $custom_text_settings[Multi_Rating::RATING_RESULTS_LIST_TITLE_TEXT_OPTION],
				'limit' => 10,
				'show_featured_img' => true,
				'image_size' => 'thumbnail',
				'header' => 'h3',
				'sort_by' => 'highest_rated',
				'show_filter' => true,
				'taxonomy' => '',
				'term_id' => 0,
				'show_rank' => true,
				'filter_label_text' => $custom_text_settings[Multi_Rating::FILTER_LABEL_TEXT_OPTION],
				'result_type' => 'star_rating'
		) );
	
		$title = strip_tags( $instance['title'] );
		$limit = intval( $instance['limit'] );
		$taxonomy =  isset($instance['taxonomy']) ? trim($instance['taxonomy']) : '';
		if ( ! empty( $instance['term_id'] ) && is_numeric( $instance['term_id'] ) ) {
			$term_id = intval( $instance['term_id'] );
		}
		$show_filter = empty( $instance['show_filter'] ) ? false : $instance['show_filter'];
		$show_featured_img = empty( $instance['show_featured_img'] ) ? false : $instance['show_featured_img'];
		$image_size = $instance['image_size'];
		$header = $instance['header'];
		$sort_by = $instance['sort_by'];
		$show_rank = empty( $instance['show_rank'] ) ? false : $instance['show_rank'];
		$filter_label_text = $instance['filter_label_text'];
		$result_type = $instance['result_type'];

		?>
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title', 'multi-rating' ); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'limit'); ?>"><?php _e( 'Limit', 'multi-rating' ); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id( 'limit' ); ?>" name="<?php echo $this->get_field_name( 'limit' ); ?>" type="number" min="0" value="<?php echo esc_attr( $limit ); ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'result_type' ); ?>"><?php _e( 'Result Type', 'multi-rating' ); ?></label>
			<select class="widefat" name="<?php echo $this->get_field_name( 'result_type' ); ?>" id="<?php echo $this->get_field_id( 'result_type' ); ?>">
				<?php 
				$result_type_options = array(
						'star_rating' => __( 'Star Rating'),
						'percentage' => __( 'Percentage' ),
						'score' => __( 'Score' )
				);
				
				foreach ( $result_type_options as $result_type_option_value => $result_type_option_label ) {
					$selected = '';
					if ( $result_type_option_value == $result_type ) {
						$selected = ' selected="selected"';
					}
					echo '<option value="' . $result_type_option_value . '" ' . $selected . '>' . $result_type_option_label . '</option>';
				}
				?>
			</select>
		</p>
		
		<p>
			<label for="<?php echo $this->get_field_id( 'taxonomy' ); ?>"><?php _e( 'Taxonomy', 'multi-rating' ); ?></label>
			<select class="widefat mr-rating-results-widget-taxonomy" name="<?php echo $this->get_field_name( 'taxonomy' ); ?>" id="<?php echo $this->get_field_id( 'taxonomy' ); ?>">				
				<?php
				//$selected = '';
				if ( $taxonomy === '' || $taxonomy == null ) {
					$selected = ' selected="selected"';
				}
				echo '<option value=""' . $selected . '></option>';
				
				$taxonomies = get_taxonomies( array( 'public' => true ), 'objects', 'and' );
				foreach ( $taxonomies  as $current_taxonomy ) {
					$selected = '';
					if ( $current_taxonomy->name === $taxonomy ) {
						$selected = ' selected="selected"';
					}
					echo '<option value="' . $current_taxonomy->name . '"' . $selected . '>' . $current_taxonomy->labels->name . '</option>';
				} ?>
			</select>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'term_id' ); ?>"><?php _e( 'Terms', 'multi-rating' ); ?></label>
			<select class="widefat" name="<?php echo $this->get_field_name( 'term_id' ); ?>" id="<?php echo $this->get_field_id( 'term_id' ); ?>">
			<?php
			$selected = '';
			if ( $taxonomy === '' || $taxonomy == null ) {
				echo '<option value="" selected="selected"></option>';
			} else {
				if ($term_id == 0) {
					$selected = ' selected="selected"';
				}
				echo '<option value="0"' . $selected . '>' . __( 'All', 'multi-rating' ) . '</option>';
				$terms = get_terms( $taxonomy );
				foreach ( $terms  as $current_term ) {
					$selected = '';
					if ( $current_term->term_id == $term_id ) {
						$selected = ' selected="selected"';
					}
					echo '<option value="' . $current_term->term_id . '" ' . $selected . '>' . $current_term->name . '</option>';
				} 
			} ?>
			</select>
		</p>

		<p>
			<input id="<?php echo $this->get_field_id( 'show_filter' ); ?>" name="<?php echo $this->get_field_name( 'show_filter' ); ?>" type="checkbox" value="true" <?php checked( true, $show_filter, true ); ?>/>
			<label for="<?php echo $this->get_field_id( 'show_filter' ); ?>"><?php _e( 'Show Filter', 'multi-rating' ); ?></label>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'filter_label_text' ); ?>"><?php _e( 'Filter Label', 'multi-rating' ); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id( 'filter_label_text' ); ?>" name="<?php echo $this->get_field_name( 'filter_label_text' ); ?>" type="text" value="<?php echo esc_attr( $filter_label_text ); ?>" />
		</p>
		<p>
			<input id="<?php echo $this->get_field_id( 'show_featured_img' ); ?>" name="<?php echo $this->get_field_name( 'show_featured_img' ); ?>" type="checkbox" value="true" <?php checked( true, $show_featured_img, true ); ?>/>
			<label for="<?php echo $this->get_field_id( 'show_featured_img' ); ?>"><?php _e( 'Show Featured Image', 'multi-rating' ); ?></label>
		</p>
		<p>
			<input id="<?php echo $this->get_field_id( 'show_rank' ); ?>" name="<?php echo $this->get_field_name( 'show_rank' ); ?>" type="checkbox" value="true" <?php checked( true, $show_rank, true ); ?>/>
			<label for="<?php echo $this->get_field_id( 'show_rank' ); ?>"><?php _e( 'Show Rank', 'multi-rating' ); ?></label>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'image_size' ); ?>"><?php _e('Image Size', 'multi-rating' ); ?></label>
			<select class="widefat" name="<?php echo $this->get_field_name( 'image_size' ); ?>" id="<?php echo $this->get_field_id( 'image_size' ); ?>">
				<?php 
				$img_sizes = MR_Utils::get_image_sizes();
				
				foreach ( $img_sizes as $img_size_name => $img_size_meta ) {
					$selected = '';
					if ( $img_size_name == $image_size ) {
						$selected = ' selected="selected"';
					}
					echo '<option value="' . $img_size_name . '" ' . $selected . '>' . $img_size_name . ' (' .  $img_size_meta['width'] . 'x' . $img_size_meta['height'] . ')</option>';
				}
				?>
			</select>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'header' ); ?>"><?php _e( 'Header', 'multi-rating' ); ?></label>
			<select class="widefat" name="<?php echo $this->get_field_name( 'header' ); ?>" id="<?php echo $this->get_field_id( 'header' ); ?>">
				<?php 
				$header_options = array( 'h1', 'h2', 'h3', 'h4', 'h5', 'h6' );
				
				foreach ( $header_options as $header_option ) {
					$selected = '';
					if ( $header_option == $header ) {
						$selected = ' selected="selected"';
					}
					echo '<option value="' . $header_option . '" ' . $selected . '>' . strtoupper( $header_option ) . '</option>';
				}
				?>
			</select>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'sort_by' ); ?>"><?php _e( 'Sort By', 'multi-rating' ); ?></label>
			<select class="widefat" name="<?php echo $this->get_field_name( 'sort_by' ); ?>" id="<?php echo $this->get_field_id( 'sort_by' ); ?>">
				<?php 
				$sort_by_options = array( 
						'highest_rated' => __( 'Highest Rated', 'multi-rating' ),
						'lowest_rated' => __( 'Lowest Rated', 'multi-rating'),
						'most_entries' => __( 'Most Entries', 'multi-rating' ),
						'post_title_asc' => __( 'Post Title Ascending', 'multi-rating' ),
						'post_title_desc' => __( 'Post Title Descending', 'multi-rating' )
				);
				
				foreach ( $sort_by_options as $sort_by_options_value => $sort_by_options_name ) {
					$selected = '';
					if ( $sort_by_options_value == $sort_by ) {
						$selected = ' selected="selected"';
					}
					echo '<option value="' . $sort_by_options_value . '" ' . $selected . '>' . $sort_by_options_name . '</option>';
				}
				?>
			</select>
		</p>
		<?php	
	}
}


/**
 * AJAX function for retrieving terms by taxonomy
 */
function mr_retrieve_terms_by_taxonomy() {
	$ajax_nonce = $_POST['nonce'];

	$response = array();

	if ( wp_verify_nonce( $ajax_nonce, Multi_Rating::ID.'-nonce' ) ) {
		$taxonomy = isset( $_POST['taxonomy'] ) ? $_POST['taxonomy'] : 'category';
			
		$terms = get_terms( $taxonomy );
			
		array_push( $response, array( 'name' => __( 'All', 'multi-rating' ), 'term_id' => 0 ) );
			
		foreach ( $terms as $term ) {
			array_push( $response, array( 'name' => $term->name, 'term_id' => $term->term_id ) );
		}
			
		echo json_encode( $response );
	}

	die();
}

/**
 * Registers widgets
 */
function mr_register_widgets() {
	register_widget( 'MR_Rating_Results_List_Widget' );
}
add_action( 'widgets_init', 'mr_register_widgets' );
?>