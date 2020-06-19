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
		
		$current_screen = get_current_screen();
		if ( function_exists( 'is_gutenberg_page' ) && is_gutenberg_page() || method_exists( $current_screen, 'is_block_editor' ) && $current_screen->is_block_editor() ) {
			return;
		}
		
		add_meta_box( 'mr_meta_box', __('Multi Rating', 'multi-rating'), array( $this, 'display_meta_box_content' ), $post_type, 'side', 'default');
	}
	
	/**
	 * Save the meta when the post is saved.
	 *
	 * @param int $post_id The ID of the post being saved.
	 */
	public function save_post_meta( $post_id ) {

		if ( ! function_exists('get_current_screen')) {
			return;
		}
		$current_screen = get_current_screen();
		if ( function_exists( 'is_gutenberg_page' ) && is_gutenberg_page() || method_exists( $current_screen, 'is_block_editor' ) && $current_screen->is_block_editor() ) {
			return;
		}
			
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
		$structured_data_type = $_POST['mr-structured-data-type'];
	
		// Update the meta field.
		update_post_meta( $post_id, Multi_Rating::RATING_FORM_POSITION_POST_META, $rating_form_position );
		update_post_meta( $post_id, Multi_Rating::RATING_RESULTS_POSITION_POST_META, $rating_results_position );
		update_post_meta( $post_id, Multi_Rating::STRUCTURED_DATA_TYPE_POST_META, $structured_data_type );
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
		$structured_data_type = get_post_meta( $post->ID, Multi_Rating::STRUCTURED_DATA_TYPE_POST_META, true );
	
		?>
		<p><strong><?php _e( 'Auto Placement Settings', 'multi-rating' ); ?></strong></p>
		<p><label for="rating-form-position"><?php _e( 'Rating Form Position', 'multi-rating' ); ?></label></p>
		<p>
			<select class="widefat" name="rating-form-position">
				<option value="<?php echo Multi_Rating::DO_NOT_SHOW; ?>" <?php selected('do_not_show', $rating_form_position, true );?>><?php _e( 'Do not show', 'multi-rating' ); ?></option>
				<option value="" <?php selected('', $rating_form_position, true );?>><?php _e( 'Use default settings', 'multi-rating' ); ?></option>
				<option value="before_content" <?php selected('before_content', $rating_form_position, true );?>><?php _e( 'Before content', 'multi-rating' ); ?></option>
				<option value="after_content" <?php selected('after_content', $rating_form_position, true );?>><?php _e( 'After content', 'multi-rating' ); ?></option>
			</select>
			<span class="mr-help"><?php _e( 'Auto placement position for the rating form on the post', 'multi-rating' ); ?></p>
		</p>
		
		<p><label for="rating-results-position"><?php _e( 'Rating Result Position', 'multi-rating' ); ?></label></p>
		<p>
			<select class="widefat" name="rating-results-position">
				<option value="<?php echo Multi_Rating::DO_NOT_SHOW; ?>" <?php selected('do_not_show', $rating_results_position, true );?>><?php _e('Do not show', 'multi-rating' ); ?></option>
				<option value="" <?php selected('', $rating_results_position, true );?>><?php _e( 'Use default settings', 'multi-rating' ); ?></option>
				<option value="before_title" <?php selected('before_title', $rating_results_position, true );?>><?php _e( 'Before title', 'multi-rating' ); ?></option>
				<option value="after_title" <?php selected('after_title', $rating_results_position, true );?>><?php _e( 'After title', 'multi-rating' ); ?></option>
				<option value="before_content" <?php selected('before_content', $rating_results_position, true );?>><?php _e( 'Before content', 'multi-rating' ); ?></option>
				<option value="after_content" <?php selected('after_content', $rating_results_position, true );?>><?php _e( 'After content', 'multi-rating' ); ?></option>
			</select>
			<span class="mr-help"><?php _e( 'Auto placement position for the rating result on the post', 'multi-rating' ); ?></p>
		</p>
		<hr style="margin-top: 1em; margin-bottom:1em;" />
		<p><strong><?php _e( 'Structured Data Type', 'multi-rating' ); ?></strong></p>
		<p><label for="mr-structured-data-type"><?php _e( 'Create New Type', 'multi-rating' ); ?></label></p>
		<p>
			<select class="widefat" name="mr-structured-data-type">
				<option value="" <?php selected( '', $structured_data_type, true );?>></option>
				<option value="Book" <?php selected( 'Book', $structured_data_type, true );?>><?php _e( 'Book', 'multi-rating' ); ?></option>
				<option value="Course" <?php selected( 'Course', $structured_data_type, true );?>><?php _e( 'Course', 'multi-rating' ); ?></option>
				<option value="CreativeWorkSeason" <?php selected( 'CreativeWorkSeason', $structured_data_type, true );?>><?php _e( 'CreativeWorkSeason', 'multi-rating' ); ?></option>
				<option value="CreativeWorkSeries" <?php selected( 'CreativeWorkSeries', $structured_data_type, true );?>><?php _e( 'CreativeWorkSeries', 'multi-rating' ); ?></option>
				<option value="Episode" <?php selected( 'Episode', $structured_data_type, true );?>><?php _e( 'Episode', 'multi-rating' ); ?></option>
				<option value="Event" <?php selected( 'Event', $structured_data_type, true );?>><?php _e( 'Event', 'multi-rating' ); ?></option>
				<option value="Game" <?php selected( 'Game', $structured_data_type, true );?>><?php _e( 'Game', 'multi-rating' ); ?></option>
				<option value="HowTo" <?php selected( 'HowTo', $structured_data_type, true );?>><?php _e( 'HowTo', 'multi-rating' ); ?></option>
				<option value="LocalBusiness" <?php selected( 'LocalBusiness', $structured_data_type, true );?>><?php _e( 'LocalBusiness', 'multi-rating' ); ?></option>
				<option value="MediaObject" <?php selected( 'MediaObject', $structured_data_type, true );?>><?php _e( 'MediaObject', 'multi-rating' ); ?></option>
				<option value="Movie" <?php selected( 'Movie', $structured_data_type, true );?>><?php _e( 'Movie', 'multi-rating' ); ?></option>
				<option value="MusicPlaylist" <?php selected( 'MusicPlaylist', $structured_data_type, true );?>><?php _e( 'MusicPlaylist', 'multi-rating' ); ?></option>
				<option value="MusicRecording" <?php selected( 'MusicRecording', $structured_data_type, true );?>><?php _e( 'MusicRecording', 'multi-rating' ); ?></option>
				<option value="Organization" <?php selected( 'Organization', $structured_data_type, true );?>><?php _e( 'Organization', 'multi-rating' ); ?></option>
				<option value="Product" <?php selected( 'Product', $structured_data_type, true );?>><?php _e( 'Product', 'multi-rating' ); ?></option>
				<option value="Recipe" <?php selected( 'Recipe', $structured_data_type, true );?>><?php _e( 'Recipe', 'multi-rating' ); ?></option>
				<option value="SoftwareApplication" <?php selected( 'SoftwareApplication', $structured_data_type, true );?>><?php _e( 'SoftwareApplication', 'multi-rating' ); ?></option>
			</select>
			<span class="mr-help"><?php _e( 'Schema.org item type for post. If you have the WordPress SEO or WooCommerce plugins adding structured data for the type already, do not set. Note some types may require additional structured data.', 'multi-rating' ); ?></span>
		</p>
		<?php
	}
}
?>