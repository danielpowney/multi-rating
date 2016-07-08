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
								<input id="weight" name="weight" type="number" value="1" min="0" placeholder="<?php _e( 'Enter weight', 'multi-rating' ); ?>" class="small-text" required />
								<p class="description"><?php _e( 'All rating items are rated equally by default. Modifying the weight of a rating item will adjust the rating results accordingly.', 'multi-rating' ); ?></p>
							</td>
						</tr>
					</tbody>
				</table>
				
				<input id="add-new-rating-item-btn" class="button button-primary" value="<?php _e( 'Save Changes', 'multi-rating' ); ?>" type="submit" />
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
function mr_add_new_rating_item_screen() {

	if ( isset( $_POST['form-submitted'] ) && $_POST['form-submitted'] == 'true' ) {
		$error_message = '';
		$success_message = '';
			
		if ( isset($_POST['desciption'] ) && isset( $_POST['max-option-value'] )
				&& isset( $_POST['default-option-value'] ) ) {

			$description = $_POST['desciption'];
			if ( strlen( trim( $description ) ) == 0) {
				$error_message .= __( 'Description cannot be empty. ', 'multi-rating' );
			}

			$type = $_POST['type'];
			if ( strlen( trim( $type ) ) == 0) {
				$type = Multi_Rating::SELECT_ELEMENT;
			}

			if ( is_numeric( $_POST['max-option-value'] ) == false ) {
				$error_message .= __( 'Max option value cannot be empty and must be a whole number. ', 'multi-rating' );
			}

			if ( is_numeric( $_POST['default-option-value'] ) == false ) {
				$error_message .= __( 'Default option value cannot be empty and must be a whole number. ', 'multi-rating' );
			}

			if (strlen( $error_message) == 0) {
					
				global $wpdb;
					
				$max_option_value = intval($_POST['max-option-value']);
				$default_option_value = intval($_POST['default-option-value']);
				$weight = doubleval($_POST['weight']);
					
				$results = $wpdb->insert( $wpdb->prefix.Multi_Rating::RATING_ITEM_TBL_NAME, array(
						'description' => $description,
						'max_option_value' => $max_option_value,
						'default_option_value' => $default_option_value,
						'weight' => $weight,
						'type' => $type
				) );
				
				$rating_item_id = $wpdb->insert_id;
				
				// WPML register string
				if ( function_exists( 'icl_register_string' ) ) {
					icl_register_string( 'multi-rating', 'rating-item-' . $rating_item_id . '-description', $description);
				}
					
				$success_message .= __('Rating item added successfully.', 'multi-rating' );
			}
		} else {
			$error_message .= __( 'An error occured. Rating item could not be added.', 'multi-rating' );
		}
			
		if ( strlen( $error_message ) > 0) {
			echo '<div class="error"><p>' . $error_message . '</p></div>';
		}
		if ( strlen( $success_message ) > 0) {
			echo '<div class="updated"><p>' . $success_message . '</p></div>';
		}
	}
}
?>