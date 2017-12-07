<?php

/**
 * Shows the Edit Rating screen
 */
function mr_edit_rating_screen() {
	?>
	<div class="wrap">
		<?php

		if ( ! current_user_can( 'manage_options' ) ) {
			echo '<div class="error"><p>' . __( 'An error has occured.', 'multi-rating' ) . '</p></div>';
			return;
		}

		// get the entry id
		$rating_entry_id = null;
		if ( isset( $_GET['entry-id'] ) ) {
			$rating_entry_id = $_GET['entry-id'];
		} else if ( isset ( $_POST['entry-id'] ) ) {
			$rating_entry_id = $_POST['entry-id'];
		}

		if ( $rating_entry_id == null ) {
			echo '<p>' . __( 'An error has occured', 'multi-rating' ) . '</p>';
			echo '</div>';
			return;
		}

		?>
		<h2><?php printf( __( 'Edit Rating Entry #%d', 'multi-rating' ), $rating_entry_id ); ?></h2>
		<?php

		$rating_items = Multi_Rating_API::get_rating_items( array(
				'rating_item_entry_id' => $rating_entry_id
		) );

		global $wpdb;

		// get the rating entry values
		$rating_entry_values = array();
		if ( $rating_entry_id != null ) {
			$query = 'SELECT riev.value AS value, riev.rating_item_entry_id AS rating_item_entry_id, riev.rating_item_id AS rating_item_id FROM ' . $wpdb->prefix . Multi_Rating::RATING_ITEM_ENTRY_VALUE_TBL_NAME . ' AS riev WHERE riev.rating_item_entry_id = %d';
			$rating_entry_values = $wpdb->get_results( $wpdb->prepare( $query, $rating_entry_id ) );
		}

		if ( count( $rating_entry_values ) == 0 ) {
			echo '<p>' . sprintf( __( 'An error has occured', 'multi-rating' ), $rating_entry_id ) . '</p>';
		} else {

			$selected_option_lookup = array();
			foreach ( $rating_entry_values as $entry_value ) {
				$selected_option_lookup[$entry_value->rating_item_id] = $entry_value->value;
			}

			?>

			<form name="edit-rating-form" id="edit-rating-form" method="post" action="#">

				<table class="form-table">
					<?php

					// rating items
					foreach ( $rating_items as $rating_item ) {

						$description = $rating_item['description'];
						$rating_item_id = $rating_item['rating_item_id'];

						// WPML translate string
						if ( function_exists( 'icl_translate' ) && strlen( $description ) > 0 ) {
							$description = icl_translate( 'multi-rating', 'rating-item-' . $rating_item_id . '-description', $description );
						}

						?>
						<tr class="form-field">
							<th scope="row"><label for="rating-item-<?php echo $rating_item_id; ?>"><?php echo $description; ?></label></td>
							<td>
								<?php
								echo '<select name="rating-item-' . $rating_item_id . '" id="rating-item-' . $rating_item_id . '">';

								$index = 0;
								for ( $index; $index <= $rating_item['max_option_value']; $index++ ) {
									$is_selected = false;
									if ( $selected_option_lookup[$rating_item_id] == $index ) {
										$is_selected = true;
									}

									echo '<option value="' . $index . '"';
									if ( $is_selected ) {
										echo ' selected="selected"';
									}
									echo '>' . $index . '</option>';
								}

								echo '</select>';
								?>
							</td>
						</tr>
					<?php } ?>
				</table>

				<input type="hidden" name="entry-id" id="entry-id" value="<?php echo $rating_entry_id; ?>" />
				<input type="hidden" name="edit-rating" id="edit-rating" value="true" />
				<?php
				submit_button( __( 'Update', 'multi-rating' ), 'primary', 'update-rating-btn', true, null );
				?>
			</form>
		<?php } ?>
	</div>
	<?php
}


/**
 * Edits a rating and redirect back to the entries page if successful
 */
function mr_edit_rating() {

	// get the entry id
	$rating_entry_id = null;
	if ( isset( $_GET['entry-id'] ) ) {
		$rating_entry_id = $_GET['entry-id'];
	} else if ( isset ( $_POST['entry-id'] ) ) {
		$rating_entry_id = $_POST['entry-id'];
	}

	$rating_items = Multi_Rating_API::get_rating_items( array(
			'rating_item_entry_id' => $rating_entry_id
	) );

	global $wpdb;

	// get post id
	$query = 'SELECT rie.post_id as post_id FROM ' . $wpdb->prefix . Multi_Rating::RATING_ITEM_ENTRY_TBL_NAME . ' AS rie WHERE rating_item_entry_id = %d';
	$post_id = $wpdb->get_var( $wpdb->prepare( $query, $rating_entry_id ), 0, 0 );

	if ( $post_id == null ) {
		echo '<div class="error"><p>' . __( 'An error occured', 'multi-rating' ) . '</p></div>';
		return;
	}

	foreach ( $rating_items as $rating_item ) {
		$rating_item_id = $rating_item['rating_item_id'];
		$rating_item_value = isset( $_POST['rating-item-' . $rating_item_id] ) ? $_POST['rating-item-' . $rating_item_id]  : null;

		if ( $rating_item_value != null ) {

			$query = 'SELECT COUNT(*) FROM ' . $wpdb->prefix . Multi_Rating::RATING_ITEM_ENTRY_VALUE_TBL_NAME . ' WHERE rating_item_entry_id = %d AND rating_item_id = %d';
			$rows = $wpdb->get_col( $wpdb->prepare( $query, $rating_entry_id, $rating_item_id ) );

			if ( $rows[0] == 0) {

				$wpdb->insert( $wpdb->prefix . Multi_Rating::RATING_ITEM_ENTRY_VALUE_TBL_NAME,
						array( 'rating_item_entry_id' => $rating_entry_id, 'rating_item_id' => $rating_item_id, 'value' => $rating_item_value ),
						array('%d', '%d', '%d')
				);

			} else {

				$wpdb->update( $wpdb->prefix . Multi_Rating::RATING_ITEM_ENTRY_VALUE_TBL_NAME,
						array( 'value' => $rating_item_value ),
						array( 'rating_item_entry_id' => $rating_entry_id, 'rating_item_id' => $rating_item_id ),
						array( '%d' ),
						array( '%d', '%d' )
				);

			}
		}
	}

	$general_settings = (array) get_option( Multi_Rating::GENERAL_SETTINGS );
	$rating_results_cache = $general_settings[Multi_Rating::RATING_RESULTS_CACHE_OPTION];
	if ($rating_results_cache == true) {

		// delete rating results cache
		delete_post_meta( $post_id, Multi_Rating::RATING_RESULTS_POST_META_KEY );
		delete_post_meta( $post_id, Multi_Rating::RATING_RESULTS_POST_META_KEY . '_star_rating' );
		delete_post_meta( $post_id, Multi_Rating::RATING_RESULTS_POST_META_KEY . '_count_entries' );

	}

	// redirect back to entries page
	$entries_page = 'admin.php?page=' . Multi_Rating::RATING_ENTRIES_PAGE_SLUG;

	if ( isset( $_REQUEST['post-id'] ) ) {
		$entries_page .= '&post-id=' . $_REQUEST['post-id'];
	}
	if ( isset( $_REQUEST['username'] ) ) {
		$entries_page .= '&username=' . $_REQUEST['username'];
	}
	if ( isset( $_REQUEST['to-date'] ) )  {
		$entries_page .= '&to-date=' . $_REQUEST['to-date'];
	}
	if ( isset( $_REQUEST['from-date'] ) )  {
		$entries_page .= '&from-date=' . $_REQUEST['from-date'];
	}
	if ( isset( $_REQUEST['comments-only'] ) )  {
		$entries_page .= '&comments-only=' . $_REQUEST['comments-only'];
	}
	if ( isset( $_REQUEST['paged'] ) )  {
		$entries_page .= '&paged=' . $_REQUEST['paged'];
	}

	wp_redirect( $entries_page );
	exit();
}
if ( isset( $_POST['edit-rating'] ) && $_POST['edit-rating'] == 'true' ) {
	add_action( 'admin_init', 'mr_edit_rating' );
}

?>
