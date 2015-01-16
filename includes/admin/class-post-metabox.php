<?php 
/**
 * Post metabox class
 * 
 * @author dpowney
 */
class MR_Post_Metabox {
	
	/**
	 * Constructor
	 */
	public function __construct() {
	
		add_action( 'add_meta_boxes', array( $this, 'add_meta_box' ) );
		add_action( 'save_post', array( $this, 'save_post_meta' ) );
	
	}

	/**
	 * Adds the meta box container
	 */
	public function add_meta_box( $post_type ) {
		
		$general_settings = (array) get_option( Multi_Rating::GENERAL_SETTINGS );
		$post_types = $general_settings[Multi_Rating::POST_TYPES_OPTION];
		
		if ( ! is_array( $post_types ) && is_string( $post_types ) ) {
			$post_types = array($post_types);
		}
		if ( $post_types != null && in_array( $post_type, $post_types )) {
			add_meta_box( 'mr_meta_box', __('Multi Rating', 'multi-rating'), array( $this, 'display_meta_box_content' ), $post_type, 'side', 'high');
		}
	}
	
	/**
	 * Save the meta when the post is saved.
	 *
	 * @param int $post_id The ID of the post being saved.
	 */
	public function save_post_meta( $post_id ) {
			
		if ( ! isset( $_POST['meta_box_nonce_action'] ) )
			return $post_id;
	
		if ( ! wp_verify_nonce( $_POST['meta_box_nonce_action'], 'meta_box_nonce' ) )
			return $post_id;
	
		// If this is an autosave, our form has not been submitted, so we don't want to do anything.
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
			return $post_id;
	
		// Check the user's permissions.
		if ( 'page' == $_POST['post_type'] ) {
			if ( ! current_user_can( 'edit_page', $post_id ) )
				return $post_id;
	
		} else {
			if ( ! current_user_can( 'edit_post', $post_id ) )
				return $post_id;
		}
	
		$rating_form_position = $_POST['rating-form-position'];
		$rating_results_position = $_POST['rating-results-position'];
	
		// Update the meta field.
		update_post_meta( $post_id, Multi_Rating::RATING_FORM_POSITION_POST_META, $rating_form_position );
		update_post_meta( $post_id, Multi_Rating::RATING_RESULTS_POSITION_POST_META, $rating_results_position );
	}
	
	
	/**
	 * Displays the meta box content
	 *
	 * @param WP_Post $post The post object.
	 */
	public function display_meta_box_content( $post ) {
	
		wp_nonce_field( 'meta_box_nonce', 'meta_box_nonce_action' );
	
		$rating_form_position = get_post_meta( $post->ID, Multi_Rating::RATING_FORM_POSITION_POST_META, true );
		$rating_results_position = get_post_meta( $post->ID, Multi_Rating::RATING_RESULTS_POSITION_POST_META, true );
	
		?>
		<p>
			<label for="rating-form-position"><?php _e( 'Rating form position', 'multi-rating' ); ?></label>
			<select class="widefat" name="rating-form-position">
				<option value="<?php echo Multi_Rating::DO_NOT_SHOW; ?>" <?php selected('do_not_show', $rating_form_position, true );?>><?php _e( 'Do not show', 'multi-rating' ); ?></option>
				<option value="" <?php selected('', $rating_form_position, true );?>><?php _e( 'Use default settings', 'multi-rating' ); ?></option>
				<option value="before_content" <?php selected('before_content', $rating_form_position, true );?>><?php _e( 'Before content', 'multi-rating' ); ?></option>
				<option value="after_content" <?php selected('after_content', $rating_form_position, true );?>><?php _e( 'After content', 'multi-rating' ); ?></option>
			</select>
		</p>
		
		<p>
			<label for="rating-results-position"><?php _e( 'Rating result position', 'multi-rating' ); ?></label>
			<select class="widefat" name="rating-results-position">
				<option value="<?php echo Multi_Rating::DO_NOT_SHOW; ?>" <?php selected('do_not_show', $rating_results_position, true );?>><?php _e('Do not show', 'multi-rating' ); ?></option>
				<option value="" <?php selected('', $rating_results_position, true );?>><?php _e( 'Use default settings', 'multi-rating' ); ?></option>
				<option value="before_title" <?php selected('before_title', $rating_results_position, true );?>><?php _e( 'Before title', 'multi-rating' ); ?></option>
				<option value="after_title" <?php selected('after_title', $rating_results_position, true );?>><?php _e( 'After title', 'multi-rating' ); ?></option>
			</select>
		</p>
		<?php
	}
}
?>