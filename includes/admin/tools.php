<?php 

/**
 * Shows the tools screen
 */
function mr_tools_screen() {
	?>
	<div class="wrap">
		<h2><?php _e( 'Tools', 'multi-rating' ); ?></h2>
		
		<div class="metabox-holder">
			<div class="postbox">
				<h3><span><?php _e( 'Export Rating Results', 'multi-rating' ); ?></span></h3>
				<div class="inside">
					<p><?php _e( 'Export Rating Results to a CSV file.', 'multi-rating' ); ?></p>
					
					<form method="post" id="export-rating-results-form">
						<p>
							<input type="text" name="username" id="username" class="" autocomplete="off" placeholder="Username">
							<input type="text" class="date-picker" autocomplete="off" name="from-date1" placeholder="From - dd/MM/yyyy" id="from-date1">
							<input type="text" class="date-picker" autocomplete="off" name="to-date1" placeholder="To - dd/MM/yyyy" id="to-date1">
							
							<select name="post-id" id="post-id">
								<option value=""><?php _e( 'All posts / pages', 'multi-rating' ); ?></option>
								<?php	
								global $wpdb;
								$query = 'SELECT DISTINCT post_id FROM ' . $wpdb->prefix . Multi_Rating::RATING_ITEM_ENTRY_TBL_NAME;
								
								$rows = $wpdb->get_results( $query, ARRAY_A );
			
								foreach ( $rows as $row ) {
									$post_id = $row['post_id'];
									$temp_post_id = $post_id;
									
									// WPML get adjusted post id for active language, just for the string translation
									if ( function_exists( 'icl_object_id' ) ) {
										$temp_post_id = icl_object_id ( $post_id , get_post_type( $post_id ), true, ICL_LANGUAGE_CODE );
									}
									
									?>
									<option value="<?php echo $post_id; ?>">
										<?php echo get_the_title( $temp_post_id ); ?>
									</option>
								<?php } ?>
							</select>
						</p>
						
						<p>
							<input type="hidden" name="export-rating-results" id="export-rating-results" value="false" />
							<?php 
							submit_button( __( 'Export', 'multi-rating' ), 'secondary', 'export-btn', false, null );
							?>
						</p>
					</form>
				</div><!-- .inside -->
			</div>
		</div>
		
		<?php 
		
		if ( current_user_can( 'manage_options' ) ) {
			?>
		
			<div class="metabox-holder">
				<div class="postbox">
					<h3><span><?php _e( 'Clear Database', 'multi-rating' ); ?></span></h3>
					<div class="inside">
						<p><?php _e( 'Delete rating results from the database.', 'multi-rating' ); ?></p>
						
						<form method="post" id="clear-database-form">
							<p>
								<input type="text" name="username" id="username" class="" autocomplete="off" placeholder="Username">
								<input type="text" class="date-picker" autocomplete="off" name="from-date2" placeholder="From - dd/MM/yyyy" id="from-date2">
								<input type="text" class="date-picker" autocomplete="off" name="to-date2" placeholder="To - dd/MM/yyyy" id="to-date2">
								
								<select name="post-id" id="post-id">
									<option value=""><?php _e( 'All posts / pages', 'multi-rating' ); ?></option>
									<?php	
									global $wpdb;
									$query = 'SELECT DISTINCT post_id FROM ' . $wpdb->prefix . Multi_Rating::RATING_ITEM_ENTRY_TBL_NAME;
									
									$rows = $wpdb->get_results( $query, ARRAY_A );
									foreach ( $rows as $row ) {
										$post_id = $row['post_id'];
										$temp_post_id = $post_id;
										
										// WPML get adjusted post id for active language, just for the string translation
										if ( function_exists( 'icl_object_id' ) ) {
											$temp_post_id = icl_object_id ( $post_id , get_post_type( $post_id ), true, ICL_LANGUAGE_CODE );
										}
										
										?>
										<option value="<?php echo $post_id; ?>">
											<?php echo get_the_title( $temp_post_id ); ?>
										</option>
									<?php } ?>
								</select>
							</p>
						
							<p>
								<input type="hidden" name="clear-database" id="clear-database" value="false" />
								<?php 
								submit_button( $text = __('Clear Database', 'multi-rating' ), $type = 'delete', $name = 'clear-database-btn', $wrap = false, $other_attributes = null );
								?>
							</p>
						</form>
					</div>
				</div>
			</div>
			
					<div class="metabox-holder">
				<div class="postbox">
					<h3><span><?php _e( 'Clear Cache', 'multi-rating' ); ?></span></h3>
					<div class="inside">
						<p><?php _e( 'Clear the cached rating results stored in the WordPress post meta table.', 'multi-rating' ); ?></p>
						
						<form method="post" id="clear-cache-form">
							<p>
								<select name="post-id" id="post-id">
									<option value=""><?php _e( 'All posts / pages', 'multi-rating' ); ?></option>
									<?php	
									global $wpdb;
									$query = 'SELECT DISTINCT post_id FROM ' . $wpdb->prefix . Multi_Rating::RATING_ITEM_ENTRY_TBL_NAME;
									
									$rows = $wpdb->get_results( $query, ARRAY_A );
				
									foreach ( $rows as $row ) {
										$post_id = $row['post_id'];
										$temp_post_id = $post_id;
										
										// WPML get adjusted post id for active language, just for the string translation
										if ( function_exists( 'icl_object_id' ) ) {
											$temp_post_id = icl_object_id ( $post_id , get_post_type( $post_id ), true, ICL_LANGUAGE_CODE );
										}
										
										?>
										<option value="<?php echo $post_id; ?>">
											<?php echo get_the_title( $temp_post_id ); ?>
										</option>
									<?php } ?>
								</select>
							</p>
							
							<p>
								<input type="hidden" name="clear-cache" id="clear-cache" value="false" />
								<?php 
								submit_button( __( 'Clear Cache', 'multi-rating' ), 'secondary', 'clear-cache-btn', false, null );
								?>
							</p>
						</form>
					</div><!-- .inside -->
				</div>
			</div>
		</div>
		<?php
	}
}

/**
 * Exports the rating results to a CSV file
 */
function mr_export_rating_results() {

	$file_name = 'rating-results-' . date( 'YmdHis' ) . '.csv';
		
	$username = isset( $_POST['username'] ) ? $_POST['username'] : null;
	$from_date = isset( $_POST['from-date1'] ) ? $_POST['from-date1'] : null;
	$to_date = isset( $_POST['to-date1'] ) ? $_POST['to-date1'] : null;
	$post_id = isset( $_POST['post-id'] ) ? $_POST['post-id'] : null;
		
	$filters = array();
	
	$filters['user_id'] = null;
	if ( $username != null && strlen( $username ) > 0 ) {
		// get user id
		$user = get_user_by( 'login', $username );
		if ( $user && $user->ID ) {
			$filters['user_id'] = $user->ID;
		}
	}
	
	if ( $post_id != null && strlen( $post_id ) > 0 ) {
		$filters['post_id'] = $post_id;
	}
	
	if ( $from_date != null && strlen( $from_date ) > 0 ) {
		list( $year, $month, $day ) = explode( '/', $from_date ); // default yyyy/mm/dd format
			if ( checkdate( $month , $day , $year )) {
			$filters['from_date'] = $from_date;
		}
	}
	
	if ( $to_date != null && strlen($to_date) > 0 ) {
		list( $year, $month, $day ) = explode( '/', $to_date );// default yyyy/mm/dd format
			if ( checkdate( $month , $day , $year )) {
			$filters['to_date'] = $to_date;
		}
	}
		
	if ( mr_generare_csv_report( $file_name, $filters ) ) {
			
		header('Content-type: text/csv');
		header('Content-Disposition: attachment; filename="' . $file_name . '"');
		readfile($file_name);
			// delete file
		unlink($file_name);
	}
		
	die();
}

/**
 * Generates rating results in CSV format.
 *
 * @param $file_name the file_name to save
 * @param $filters used to filter the report e.g. from_date, to_date, user_id etc...
 * @returns true if report successfully generated and written to file
 */
function mr_generare_csv_report( $file_name, $filters ) {

	$rating_item_entries = Multi_Rating_API::get_rating_item_entries( $filters );
		
	$header_row = __('Entry Id', 'multi-rating') . ', '
			. __('Entry Date', 'multi-rating') . ', '
					. __('Post Id', 'multi-rating') . ', '
							. __('Post Title', 'multi-rating') . ', '
									. __('Score Rating Result', 'multi-rating') . ', '
											. __('Adjusted Score Rating Result', 'multi-rating') . ', '
													. __('Total Max Option Value', 'multi-rating') . ', '
															. __('Percentage Rating Result', 'multi-rating') . ', '
																	. __('Adjusted Percentage Rating Result', 'multi-rating') . ', '
																			. __('Star Rating Result', 'multi-rating') . ', '
																					. __('Adjusted Star Rating Result', 'multi-rating') . ', '
																							. __('User Id', 'multi-rating' );

	$export_data_rows = array( $header_row );

	// iterate all found rating item entries and create row in report
	if ( count( $rating_item_entries ) > 0 ) {
			
		foreach ( $rating_item_entries as $rating_item_entry ) {

			$post_id = $rating_item_entry['post_id'];
			$rating_item_entry_id = $rating_item_entry['rating_item_entry_id'];

			$rating_items = Multi_Rating_API::get_rating_items( array(
					'post' => $post_id,
					'rating_item_entry_id' => $rating_item_entry_id
			) );

			$rating_result = Multi_Rating_API::calculate_rating_item_entry_result( $rating_item_entry_id,  $rating_items );

			// FIXME some fields may have a comma (e.g. post title, comment etc..) causing formatting issues

			$current_row = $rating_item_entry_id .', ' . $rating_item_entry['entry_date'] . ', '
					. $post_id . ', ' . get_the_title($post_id) . ', ' . $rating_result['score_result'] . ', '
							. $rating_result['adjusted_score_result'] . ', ' . $rating_result['total_max_option_value'] . ', '
									. $rating_result['percentage_result'] . ', ' . $rating_result['adjusted_percentage_result'] . ', '
											. $rating_result['star_result'] . ', ' . $rating_result['adjusted_star_result'] . ', '
													. $rating_item_entry['user_id'];

			array_push( $export_data_rows, $current_row );
		}
	}

	// write to file
	$file = null;
	try {
		$file = fopen( $file_name, 'w' );
			
		foreach ( $export_data_rows as $row ) {
			fputcsv( $file, explode(',', $row ) );
		}

		fclose($file);
	} catch (Exception $e) {
		return false;
	}

	return true;
}

/**
 * Clears all rating results from the database
 */
function mr_clear_database() {
	
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}
	
	$username = isset( $_POST['username'] ) ? $_POST['username'] : null;
	$from_date = isset( $_POST['from-date2'] ) ? $_POST['from-date2'] : null;
	$to_date = isset( $_POST['to-date2'] ) ? $_POST['to-date2'] : null;
	$post_id = isset( $_POST['post-id'] ) ? $_POST['post-id'] : null;
	
	$user_id = null;
	if ( $username ) {
		$user = get_user_by( 'login', $username );
		if ( $user && $user->ID ) {
			$user_id = $user->ID;
		}
	}
	
	$entries = Multi_Rating_API::get_rating_item_entries( array(
			'user_id' => $user_id,
			'from_date' => $from_date,
			'to_date' => $to_date,
			'post_id' => $post_id,
	) );
	
	if ( count( $entries) > 0 ) {
	
		$entry_id_array = array();
		foreach ($entries as $entry) {
			array_push($entry_id_array, $entry['rating_item_entry_id']);
			
			// rating results cache will be refreshed next time it's needed
			delete_post_meta( $entry['post_id'], Multi_Rating::RATING_RESULTS_POST_META_KEY );
		}
		
		global $wpdb;
		
		$entry_id_list = implode( ',', $entry_id_array );
	
		try {
			$rows = $wpdb->get_results( 'DELETE FROM ' . $wpdb->prefix . Multi_Rating::RATING_ITEM_ENTRY_TBL_NAME . ' WHERE rating_item_entry_id IN ( ' . $entry_id_list . ')' );
			$rows = $wpdb->get_results( 'DELETE FROM ' . $wpdb->prefix . Multi_Rating::RATING_ITEM_ENTRY_VALUE_TBL_NAME . ' WHERE rating_item_entry_id IN ( ' . $entry_id_list . ')' );
			
			echo '<div class="updated"><p>' . __( 'Database cleared successfully.', 'multi-rating' ) . '</p></div>';
		} catch ( Exception $e ) {
			echo '<div class="error"><p>' . sprintf( __('An error has occured. %s', 'multi-rating' ), $e->getMessage() ) . '</p></div>';
		}
	} else {
		echo '<div class="error"><p>' . __('No entries found', 'multi-rating' ) . '</p></div>';
	}
}


/**
 * Clears rating results cache stored in the WordPress post meta table
 */
function mr_clear_cache() {
	
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	$post_id = isset( $_POST['post-id'] ) ? $_POST['post-id'] : null;

	global $wpdb;

	$query = 'SELECT post_id FROM ' . $wpdb->prefix . Multi_Rating::RATING_ITEM_ENTRY_TBL_NAME;

	if ( $post_id != '' ) {
		$query .= ' WHERE post_id = "' . $post_id . '"';
	}

	$query .= ' GROUP BY post_id';

	$results = $wpdb->get_results( $query );

	foreach ( $results as $result ) {
		delete_post_meta( $result->post_id, Multi_Rating::RATING_RESULTS_POST_META_KEY );
	}
	echo '<div class="updated"><p>' . __( 'Cache cleared successfully.', 'multi-rating' ) . '</p></div>';
}


if ( isset( $_POST['export-rating-results'] ) && $_POST['export-rating-results'] == 'true' ) {
	add_action( 'admin_init', 'mr_export_rating_results' );
}

if ( isset( $_POST['clear-database'] ) && $_POST['clear-database'] === "true" ) {
	add_action( 'admin_init', 'mr_clear_database' );
}

if ( isset( $_POST['clear-cache'] ) && $_POST['clear-cache'] === "true" ) {
	add_action( 'admin_init', 'mr_clear_cache' );
}
?>