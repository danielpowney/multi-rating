<?php

/**
 * Top Rating Results Widget for Multi Rating plugin
 */
class Top_Rating_Results_Widget extends WP_Widget {
	
	/**
	 * Constructor
	 */
	function __construct() {
		
		$widget_ops = array( 'classname' => 'top-rating-results-widget', 'description' => __( 'Displays the Top Rating Results.', 'multi-rating' ) );
		$control_ops = array( 'width' => 400, 'height' => 350 );

		parent::__construct( 'top_rating_results_widget', __('Top Rating Results Widget', 'multi-rating' ), $widget_ops, $control_ops );
	}

	/**
	 * (non-PHPdoc)
	 * @see WP_Widget::widget()
	 */
	function widget( $args, $instance ) {
		
		extract($args);
		
		$title = apply_filters( 'widget_title', empty( $instance['title'] ) ? '' : $instance['title'], $instance, $this->id_base );
		$limit = empty( $instance['limit'] ) ? 10 : intval( $instance['limit'] );
		$category_id = 0;
		if ( ! empty( $instance['category_id'] ) && is_numeric( $instance['category_id'] ) ) {
			$category_id = intval( $instance['category_id'] );
		}
		$show_category_filter = empty( $instance['show_category_filter'] ) ? false : $instance['show_category_filter'];
		$show_featured_img = empty( $instance['show_featured_img'] ) ? false : $instance['show_featured_img'];
		$image_size = empty( $instance['image_size'] ) ? 'thumbnail' : $instance['image_size'];
		
		$custom_text_settings = (array) get_option( Multi_Rating::CUSTOM_TEXT_SETTINGS );
		
		echo $before_widget;
		
		Multi_Rating_API::display_top_rating_results( array(
				'limit' => $limit,
				'title' => $title,
				'show_category_filter' => $show_category_filter,
				'category_id' => $category_id,
				'class' => 'widget',
				'show_featured_img' => $show_featured_img,
				'image_size' => $image_size
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
		$instance['category_id'] = 0;
		if ( ! empty( $new_instance['category_id'] ) && is_numeric( $new_instance['category_id'] ) ) {
			$instance['category_id'] = intval( $new_instance['category_id'] );
		}
		$instance['show_category_filter'] = false;
		if (isset( $new_instance['show_category_filter'] ) && ( $new_instance['show_category_filter'] == 'true' ) ) {
			$instance['show_category_filter'] = true;
		}
		$instance['show_featured_img'] = false;
		if ( isset( $new_instance['show_featured_img'] ) && ( $new_instance['show_featured_img'] == 'true' ) ) {
			$instance['show_featured_img'] = true;
		}
		$instance['image_size'] = $new_instance['image_size'];
		
		return $instance;
	}

	/**
	 * (non-PHPdoc)
	 * @see WP_Widget::form()
	 */
	function form( $instance ) {
		
		$custom_text_settings = (array) get_option( Multi_Rating::CUSTOM_TEXT_SETTINGS );
		$instance = wp_parse_args( (array) $instance, array( 
				'title' => $custom_text_settings[Multi_Rating::TOP_RATING_RESULTS_TITLE_TEXT_OPTION], 
				'limit' => 10,
				'show_featured_img' => true,
				'image_size' => 'thumbnail'
		) );
		
		$title = strip_tags( $instance['title'] );
		$limit = intval( $instance['limit'] );
		$category_id = 0;
		if ( ! empty( $instance['category_id'] ) && is_numeric( $instance['category_id' ] ) ) {
			$category_id = intval( $instance['category_id'] );
		}
		$show_category_filter = empty( $instance['show_category_filter'] ) ? false : $instance['show_category_filter'];
		$show_featured_img = empty( $instance['show_featured_img'] ) ? false : $instance['show_featured_img'];
		$image_size = $instance['image_size'];
		
		?>
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title', 'multi-rating' ); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'limit' ); ?>"><?php _e( 'Limit', 'multi-rating' ); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id( 'limit' ); ?>" name="<?php echo $this->get_field_name( 'limit' ); ?>" type="text" value="<?php echo esc_attr( $limit ); ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'category_id' ); ?>"><?php _e( 'Category', 'multi-rating' ); ?></label>
			<?php wp_dropdown_categories( array( 'true' => false, 'class' => 'widefat', 'name' => $this->get_field_name( 'category_id' ), 'id' => $this->get_field_id( 'category_id' ), 'selected' => $category_id, 'show_option_all' => 'All' ) ); ?>
		</p>
		<p>
			<input id="<?php echo $this->get_field_id( 'show_category_filter' ); ?>" name="<?php echo $this->get_field_name( 'show_category_filter' ); ?>" type="checkbox" value="true" <?php checked( true, $show_category_filter, true ); ?>/>
			<label for="<?php echo $this->get_field_id( 'show_category_filter' ); ?>"><?php _e( 'Show category filter', 'multi-rating' ); ?></label>
		</p>
		<p>
			<input id="<?php echo $this->get_field_id( 'show_featured_img' ); ?>" name="<?php echo $this->get_field_name( 'show_featured_img' ); ?>" type="checkbox" value="true" <?php checked( true, $show_featured_img, true ); ?>/>
			<label for="<?php echo $this->get_field_id( 'show_featured_img' ); ?>"><?php _e( 'Show featured image', 'multi-rating' ); ?></label>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'image_size' ); ?>"><?php _e('Image size', 'multi-rating' ); ?></label>
			<select class="widefat" name="<?php echo $this->get_field_name( 'image_size' ); ?>" id="<?php echo $this->get_field_id( 'image_size' ); ?>">
				<?php 
				$img_sizes = $this->get_image_sizes();
				
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
		<?php
	}
	
	/**
	 * Helper function to retrieve list of image sizes and dimensions
	 * @param unknown_type $size
	 * @return Ambigous <multitype:NULL >|boolean|Ambigous <boolean, multitype:multitype:NULL  >
	 */
	private function get_image_sizes( $size = '' ) {
	
		global $_wp_additional_image_sizes;
	
		$sizes = array();
		$get_intermediate_image_sizes = get_intermediate_image_sizes();
	
		// Create the full array with sizes and crop info
		foreach( $get_intermediate_image_sizes as $_size ) {
	
			if ( in_array( $_size, array( 'thumbnail', 'medium', 'large' ) ) ) {
	
				$sizes[ $_size ]['width'] = get_option( $_size . '_size_w' );
				$sizes[ $_size ]['height'] = get_option( $_size . '_size_h' );
				$sizes[ $_size ]['crop'] = (bool) get_option( $_size . '_crop' );
	
			} elseif ( isset( $_wp_additional_image_sizes[ $_size ] ) ) {
	
				$sizes[ $_size ] = array(
						'width' => $_wp_additional_image_sizes[ $_size ]['width'],
						'height' => $_wp_additional_image_sizes[ $_size ]['height'],
						'crop' =>  $_wp_additional_image_sizes[ $_size ]['crop']
				);
			}
		}
	
		// Get only 1 size if found
		if ( $size ) {
	
			if( isset( $sizes[ $size ] ) ) {
				return $sizes[ $size ];
			} else {
				return false;
			}
		}
	
		return $sizes;
	}
}

/**
 * Registers widgets
 */
function mr_register_widgets() {
	register_widget( 'Top_Rating_Results_Widget' );
}
add_action( 'widgets_init', 'mr_register_widgets' );
?>