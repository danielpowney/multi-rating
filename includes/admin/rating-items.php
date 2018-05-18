<?php

/**
 * Shows the rating items screen
 */
function mr_rating_items_screen() {
	?>
	<div class="wrap">
		<?php if ( isset( $_REQUEST['rating-item-id'] ) ) { ?>
			<h2><?php _e( 'Add New Rating Item', 'multi-rating' ); ?></h2>

			<form method="post" id="add-new-rating-item-form">
				<table class="form-table">
					<tbody>
						<tr valign="top">
							<th scope="row"><?php _e( 'Label', 'multi-rating' ); ?></th>
							<td>
								<input id="desciption" name="desciption" type="text" maxlength="255" cols="100" placeholder="<?php _e( 'Enter a description...' , 'multi-rating' ); ?>" required class="regular-text" value="<?php _e( 'Sample rating item', 'multi-rating' ); ?>" />
							</td>
						</tr>
						<tr valign="top">
							<th scope="row"><?php _e( 'Type', 'multi-rating' ); ?></th>
							<td>
								<select name="type" id="type">
									<option value="select"><?php _e( 'Select', 'multi-rating' ); ?></option>
									<option value="radio"><?php _e( 'Radio', 'multi-rating' ); ?></option>
									<option value="star_rating"><?php _e( 'Stars', 'multi-rating' ); ?></option>
								</select>
							</td>
						</tr>
					<tr valign="top">
							<th scope="row"><?php _e( 'Max Option', 'multi-rating' ); ?></th>
							<td>
								<input id="max-option-value" name="max-option-value" type="number" value="5" min="0" class="small-text" required />
								<p class="description"><?php _e( 'If the max option is set to 5, then the rating item options would be 0, 1, 2, 3, 4 and 5.', 'multi-rating' ); ?></p>
							</td>
						</tr>
						<tr valign="top">
							<th scope="row"><?php _e( 'Default Option', 'multi-rating' ); ?></th>
							<td>
								<input id="default-option-value" name="default-option-value" type="number" value="5" min="0" class="small-text" required />
							</td>
						</tr>
						<tr valign="top">
							<th scope="row"><?php _e( 'Weight', 'multi-rating' ); ?></th>
							<td>
								<input id="weight" name="weight" type="number" value="1" min="0" step="0.01" placeholder="<?php _e( 'Enter weight', 'multi-rating' ); ?>" class="small-text" required />
								<p class="description"><?php _e( 'All rating items are rated equally by default. Modifying the weight of a rating item will adjust the rating results accordingly.', 'multi-rating' ); ?></p>
							</td>
						</tr>
					</tbody>
				</table>
				<p><input id="add-new-rating-item-btn" class="button button-primary" value="<?php _e( 'Save Changes', 'multi-rating' ); ?>" type="submit" /></p>
				<input type="hidden" id="add-rating-item-form-submitted" name="add-rating-item-form-submitted" value="true" />
			</form>
			<?php
		} else {
			?>
			<h2><?php _e( 'Rating Items', 'multi-rating' ); ?><a class="add-new-h2" href="admin.php?page=<?php echo Multi_Rating::RATING_ITEMS_PAGE_SLUG; ?>&rating-item-id="><?php _e( 'Add New', 'multi-rating' ); ?></a></h2>
			<form method="post" id="rating-item-table-form">
				<?php
				$rating_item_table = new MR_Rating_Item_Table();
				$rating_item_table->prepare_items();
				$rating_item_table->display();
				?>
			</form>
			<?php
		} ?>
	</div>
	<?php
}

/**
 * Show add new rating item screen
 */
function mr_save_rating_item() {

	$error_messages = array();

	if ( isset($_POST['desciption'] ) && isset( $_POST['max-option-value'] )
			&& isset( $_POST['default-option-value'] ) ) {

		$description = $_POST['desciption'];

		if ( strlen( trim( $description ) ) == 0) {
			array_push( $error_messages, __( 'Description cannot be empty. ', 'multi-rating' ) );
		}

		$type = $_POST['type'];
		if ( strlen( trim( $type ) ) == 0) {
			$type = Multi_Rating::SELECT_ELEMENT;
		}

		if ( is_numeric( $_POST['max-option-value'] ) == false ) {
			array_push( $error_messages, __( 'Max option value cannot be empty and must be a whole number. ', 'multi-rating' ) );
		}

		if ( is_numeric( $_POST['default-option-value'] ) == false ) {
			array_push( $error_messages, __( 'Default option value cannot be empty and must be a whole number. ', 'multi-rating' ) );
		}

		if ( count( $error_messages ) == 0) {

			global $wpdb;

			$max_option_value = intval( $_POST['max-option-value'] );
			$default_option_value = intval( $_POST['default-option-value'] );
			$weight = floatval( $_POST['weight'] );

			if ( $default_option_value > $max_option_value ) {
				array_push( $error_messages, __( 'Default option cannot be greater than max option.', 'multi-rating' ) );
			} else {

				$results = $wpdb->insert( $wpdb->prefix.Multi_Rating::RATING_ITEM_TBL_NAME,
						array( 'description' => $description, 'max_option_value' => $max_option_value, 'default_option_value' => $default_option_value,
								'weight' => $weight, 'type' => $type ),
						array( '%s', '%d', '%d', '%f', '%s' )
				);

				$rating_item_id = intval( $wpdb->insert_id );

				// WPML register string
				if ( function_exists( 'icl_register_string' ) ) {
					icl_register_string( 'multi-rating', 'rating-item-' . $rating_item_id . '-description', $description);
				}
			}

		}
	} else {
		array_push( $error_messages, __( 'An error occured. Rating item could not be added.', 'multi-rating' ) );
	}

	if ( count( $error_messages ) > 0) {
		echo '<div class="error">';

		foreach ( $error_messages as $error_message ) {
			echo '<p>' . $error_message . '</p>';
		}
		echo '</div>';

		return;
	}

	wp_redirect( 'admin.php?page=' . Multi_Rating::RATING_ITEMS_PAGE_SLUG );
	exit();
}
if ( isset( $_POST['add-rating-item-form-submitted'] ) && $_POST['add-rating-item-form-submitted'] == 'true' ) {
	add_action( 'admin_init', 'mr_save_rating_item' );
}
?>
