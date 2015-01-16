<?php 

/**
 * API functions for multi rating
 * 
 * @author dpowney
 */
class Multi_Rating_API {
	
	/**
	 * Get rating items
	 * 
	 * @param array $params	rating_item_entry_id, post_id and rating_form_id
	 * @return rating items
	 */
	public static function get_rating_items( $params = array() ) {
		
		global $wpdb;
		
		// base query
		$rating_items_query = 'SELECT ri.rating_item_id, ri.rating_id, ri.description, ri.default_option_value, '
				. 'ri.max_option_value, ri.weight, ri.active, ri.type FROM '
				. $wpdb->prefix . Multi_Rating::RATING_ITEM_TBL_NAME . ' as ri';
		
		if ( isset( $params['rating_item_entry_id'] ) || isset($params['post_id'] ) ) {
			
			$rating_items_query .= ', ' . $wpdb->prefix . Multi_Rating::RATING_ITEM_ENTRY_TBL_NAME . ' AS rie, '
					. $wpdb->prefix.Multi_Rating::RATING_ITEM_ENTRY_VALUE_TBL_NAME
					. ' AS riev';
		}
		
		$added_to_query = false;
		if ( isset( $params['rating_item_entry_id'] ) || isset( $params['post_id'] ) ) {
			
			$rating_items_query .= ' WHERE';
			$rating_items_query .= ' riev.rating_item_entry_id = rie.rating_item_entry_id AND ri.rating_item_id = riev.rating_item_id';
			$added_to_query = true;
		}
		
		// rating_item_entry_id
		if ( isset( $params['rating_item_entry_id'] ) ) {
			
			if ( $added_to_query == true ) {
				$rating_items_query .= ' AND';
				$added_to_query = false;
			}
			
			$rating_items_query .= ' rie.rating_item_entry_id =  "' . $params['rating_item_entry_id'] . '"';
			$added_to_query = true;
		}
		
		// post_id
		if ( isset( $params['post_id'] ) ) {
			
			if ( $added_to_query == true ) {
				$rating_items_query .= ' AND';
				$added_to_query = false;
			}
			
			$rating_items_query .= ' rie.post_id = "' . $params['post_id'] . '"';
			$added_to_query = true;
			
			//$post_type = get_post_type( $params['post_id'] );
		}
		
		$rating_items_query .= ' GROUP BY ri.rating_item_id';
		
		$rating_item_rows = $wpdb->get_results($rating_items_query);
		
		// construct rating items array
		$rating_items = array();
		foreach ( $rating_item_rows as $rating_item_row ) {
			$rating_item_id = $rating_item_row->rating_item_id;
			$weight = $rating_item_row->weight;
			$description = $rating_item_row->description;
			$default_option_value = $rating_item_row->default_option_value;
			$max_option_value = $rating_item_row->max_option_value;
			$type = $rating_item_row->type;
			
			$rating_items[$rating_item_id] = array(
					'max_option_value' => $max_option_value,
					'weight' => $weight,
					'rating_item_id' => $rating_item_id,
					'description' => $description,
					'default_option_value' => $default_option_value,
					'type' => $type
			);
		}
		
		return $rating_items;
	}

	
	/**
	 * Calculates the total weight of rating items
	 * 
	 * @param $rating_items
	 * @return total weight
	 */
	public static function get_total_weight( $rating_items ) {
		
		$total_weight = 0;
	
		foreach ( $rating_items as $rating_item => $rating_item_array ) {
			$total_weight += $rating_item_array['weight'];
		}
		
		return $total_weight;
	}
	
	/**
	 * Gets the rating result for a post id. If the rating results cache is enabled,  it retrieve the 
	 * rating results from the WordPress postmeta table
	 * 
	 * @param unknown_type $post_id
	 */
	public static function get_rating_result( $post_id ) {
		$rating_result = null;
		
		$general_settings = (array) get_option( Multi_Rating::GENERAL_SETTINGS );
		$rating_results_cache = $general_settings[Multi_Rating::RATING_RESULTS_CACHE_OPTION];
			
		if ($rating_results_cache == true) {
			// retrieve from cache if exists, otherwise calculate and save to cache
			$rating_result = get_post_meta( $post_id, Multi_Rating::RATING_RESULTS_POST_META_KEY, true );
		}
			
		if ( $rating_result == null ) {
			$rating_items = Multi_Rating_API::get_rating_items( array() );
		
			$rating_result = Multi_Rating_API::calculate_rating_result( array(
					'post_id' => $post_id,
					'rating_items' => $rating_items
			) );
		
			if ($rating_results_cache == true) {
				// update rating results cache
				update_post_meta( $post_id, Multi_Rating::RATING_RESULTS_POST_META_KEY, $rating_result );
			}
		}
		
		return $rating_result;
	}
	
	/**
	 * Calculates the rating result of a rating form for a post with filters for user_id
	 *
	 * @param array $params post_id, rating_items
	 * @return rating result
	 */
	public static function calculate_rating_result( $params = array() ) {
	
		if ( ! isset($params['rating_items'] ) || ! isset($params['post_id'] ) ) {
			return;
		}
	
		$rating_items = $params['rating_items'];
		$post_id = $params['post_id'];

		$rating_item_entries = Multi_Rating_API::get_rating_item_entries( array(
				'post_id' => $post_id 
		) );
			
		$score_result_total = 0;
		$adjusted_score_result_total = 0;
		$star_result_total = 0;
		$adjusted_star_result_total = 0;
		$percentage_result_total = 0;
		$adjusted_percentage_result_total = 0;
		$total_max_option_value = 0;
		
		$count_entries = count($rating_item_entries);
		
		// get max option value
		$total_max_option_value = 0;
		foreach ($rating_items as $rating_item) {
			$total_max_option_value += $rating_item['max_option_value'];
		}
		
		$count_entries = count($rating_item_entries);
	
		// process all entries for the post and construct a rating result for each post
		foreach ( $rating_item_entries as $rating_item_entry ) {
			$total_value = 0;
	
			// retrieve the entry values for each rating item
			$rating_item_entry_id = $rating_item_entry['rating_item_entry_id'];
				
			// do not pass rating items as not all entries may have all rating items 
			// (e.g. rating items added or deleted)
			$rating_result = Multi_Rating_API::calculate_rating_item_entry_result( $rating_item_entry_id, null );
				
			// this mean total max option value may also be different, so adjust the score result if it is as
			// this is out of the total max option value
			$adjustment = 1.0;
			if ($rating_result['total_max_option_value'] != $total_max_option_value) {
				$adjustment = $total_max_option_value / $rating_result['total_max_option_value'];
			}
				
			$score_result_total += ($rating_result['score_result'] * $adjustment);
			$adjusted_score_result_total += ($rating_result['adjusted_score_result'] * $adjustment);
			
			$star_result_total += $rating_result['star_result'];
			$adjusted_star_result_total += $rating_result['adjusted_star_result'];
				
			$percentage_result_total += $rating_result['percentage_result'];
			$adjusted_percentage_result_total += $rating_result['adjusted_percentage_result'];
		}
		
		$score_result = 0;
		$adjusted_score_result = 0;
		$star_result = 0;
		$adjusted_star_result = 0;
		$percentage_result = 0;
		$adjusted_percentage_result = 0;
		$overall_rating_result = 0;
		$overall_adjusted_rating_result = 0;
		
		if ($count_entries > 0) {
			// calculate 5 star result
			$score_result = round( doubleval($score_result_total ) / $count_entries, 2 );
			$adjusted_score_result =round(doubleval($adjusted_score_result_total ) / $count_entries, 2 );
				
			// calculate star result
			$star_result = round( doubleval( $star_result_total ) / $count_entries, 2 );
			$adjusted_star_result = round( doubleval( $adjusted_star_result_total ) / $count_entries, 2 );
				
			// calculate percentage result
			$percentage_result = round( doubleval( $percentage_result_total ) / $count_entries, 2 );
			$adjusted_percentage_result = round( doubleval( $adjusted_percentage_result_total ) / $count_entries, 2 );
		}
	
		return array(
				'adjusted_star_result' => $adjusted_star_result,
				'star_result' => $star_result,
				'total_max_option_value' => $total_max_option_value,
				'adjusted_score_result' => $adjusted_score_result,
				'score_result' => $score_result,
				'percentage_result' => $percentage_result,
				'adjusted_percentage_result' => $adjusted_percentage_result,
				'count' => $count_entries,
				'post_id' => $post_id
		);
	}
	
	/**
	 * Gets the rating form entry values based on post id
	 *
	 * @param $params post id
	 * @return rating item entries
	 */
	public static function get_rating_item_entry_values( $params = array() ) {
	
		$rating_item_entries = Multi_Rating_API::get_rating_item_entries($params);
		
		$rating_item_entries_array = array();
		
		foreach ( $rating_item_entries as $rating_item_entry ) {
			
			global $wpdb;
	
			$query = 'SELECT ri.description AS description, riev.value AS value, ri.max_option_value AS max_option_value, '
			. 'riev.rating_item_entry_id AS rating_item_entry_id, ri.rating_item_id AS rating_item_id '
			. 'FROM '.$wpdb->prefix.Multi_Rating::RATING_ITEM_ENTRY_VALUE_TBL_NAME . ' AS riev, '
			. $wpdb->prefix.Multi_Rating::RATING_ITEM_TBL_NAME . ' AS ri WHERE ri.rating_item_id = riev.rating_item_id '
			. 'AND riev.rating_item_entry_id = "' . $rating_item_entry['rating_item_entry_id'] . '"';
				
			$rating_item_entry_value_rows = $wpdb->get_results( $query, ARRAY_A );
				
			foreach ( $rating_item_entry_value_rows as &$rating_item_entry_value_row ) {
				
				$value = intval( $rating_item_entry_value_row['value'] );
				$rating_item_entry_value_row['value_text'] = $value;
			}
				
			array_push( $rating_item_entries_array, array(
					'rating_item_entry' => $rating_item_entry,
					'rating_item_entry_values' => $rating_item_entry_value_rows
			) );
		}
	
		return $rating_item_entries_array;
	
	}
	
	
	/**
	 * Gets rating item entries.
	 *
	 * @param array $params post_id, limit, from_date and to_date
	 * @return rating item entries
	 */
	public static function get_rating_item_entries( $params = array() ) {
		
		extract( wp_parse_args( $params, array(
				'post_id' => null,
				'user_id' => null,
				'limit' => null,
				'from_date' => null,
				'to_date' => null,
				'taxonomy' => null,
				'term_id' => 0,
				
				// new
				'published_posts_only' => true
		) ) );	
	
		global $wpdb;
	
		$query = 'SELECT rie.rating_item_entry_id, rie.user_id, rie.post_id, rie.entry_date';
		
		if ( $published_posts_only ) {
			$query .= ', p.post_status ';
		}

		$query .= ' FROM '.$wpdb->prefix . Multi_Rating::RATING_ITEM_ENTRY_TBL_NAME . ' as rie';

		if ( $taxonomy != null || $published_posts_only ) {
			$query .= ', ' . $wpdb->prefix . 'posts as p';
		}
		
		if ( $taxonomy != null ) {
			$query .= ' LEFT JOIN ' . $wpdb->prefix . 'term_relationships rel ON rel.object_id = p.ID';
			$query .= ' LEFT JOIN ' . $wpdb->prefix . 'term_taxonomy tax ON tax.term_taxonomy_id = rel.term_taxonomy_id';
			$query .= ' LEFT JOIN ' . $wpdb->prefix . 'terms t ON t.term_id = tax.term_id';
		}
		
		$added_to_query = false;
		// is a WHERE clause required?
		if ( $post_id || $user_id ||$from_date || $to_date || $taxonomy || $published_posts_only ) {

			$query .= ' WHERE';
		}
	
		if ( $post_id ) {	
			if ( $added_to_query ) {
				$query .= ' AND';
			}
			
			$query .= ' rie.post_id = "' . $post_id . '"';
			$added_to_query = true;
		}
		
		if ( $user_id ) {
			if ( $added_to_query ) {
				$query .= ' AND';
			}
			
			$query .= ' rie.user_id = "' . $user_id . '"';
			$added_to_query = true;
		}
		
		if ( $taxonomy ) {
			if ($added_to_query) {
				$query .= ' AND';
			}
			
			$query .= ' p.ID = rie.post_id AND tax.taxonomy = "' . $taxonomy . '"';

			if ( $term_id ) {
			 	$query .= ' AND t.term_id IN (' . $term_id . ')';
			}
			 
			$added_to_query = true;
		}

		if ( $from_date ) {
			if ( $added_to_query ) {
				$query .= ' AND';
			}
			
			$query .= ' rie.entry_date >= "' . $from_date . '"';
			$added_to_query = true;
		}
		
		if ( $to_date ) {
			if ( $added_to_query ) {
				$query .= ' AND';
			}
			
			$query .= ' rie.entry_date <= "' . $to_date . '"';
			$added_to_query = true;
		}
		
		// only return published posts
		if ( $published_posts_only ) {
			if ($added_to_query) {
				$query .= ' AND';
			}
				
			$query .= ' p.ID = rie.post_id AND p.post_status = "publish"';
			$added_to_query = true;
		}
		
		if ( $limit && is_numeric( $limit ) ) {
			if ( intval( $limit ) > 0 ) {
				$query .= ' LIMIT 0, ' . intval( $limit );
			}
		}
	
		$rows = $wpdb->get_results( $query );
		
		$rating_item_entries = array();
		foreach ( $rows as $row ) {
			$rating_item_entry = array(
					'rating_item_entry_id' => $row->rating_item_entry_id,
					'user_id' => $row->user_id,
					'post_id' => $row->post_id,
					'entry_date' => $row->entry_date
			);
			
			array_push( $rating_item_entries, $rating_item_entry );
		}
	
		return $rating_item_entries;
	}
		
	/**
	 * Calculates the rating item entry result.
	 *
	 * @param int $rating_item_entry_id
	 * @param array $rating_items optionally used to save an additional call to the database if the
	 * rating items have already been loaded
	 */
	public static function calculate_rating_item_entry_result( $rating_item_entry_id, $rating_items = null ) {
	
		if ( $rating_items == null ) {
			$rating_items = Multi_Rating_API::get_rating_items(array(
					'rating_item_entry_id' => $rating_item_entry_id
			) );
		}
	
		global $wpdb;
	
		$query = 'SELECT * FROM ' . $wpdb->prefix . Multi_Rating::RATING_ITEM_ENTRY_VALUE_TBL_NAME
				. ' WHERE rating_item_entry_id = ' . $rating_item_entry_id;
		$rating_item_entry_value_rows = $wpdb->get_results( $query );
	
		$total_max_option_value = 0;
		$total_rating_item_result = 0;
		$total_adjusted_rating_item_result = 0;
		$star_result = 0;
		$adjusted_star_result = 0;
		$score_result = 0;
		$adjusted_score_result = 0;
		$percentage_result = 0;
		$adjusted_percentage_result = 0;
		$rating_result = 0;
		$total_weight = Multi_Rating_API::get_total_weight( $rating_items );
	
		// use the rating items to determine total max option value
		// we do not use the entry values in case some rating items can be added/deleted
		$count_rating_items = 0;
		$total_adjusted_max_option_value = 0;
		foreach ( $rating_items as $rating_item ) {

			//if ($rating_item['exclude_result'] == false) {
				$total_max_option_value += $rating_item['max_option_value'];
				$total_adjusted_max_option_value += ( $rating_item['max_option_value'] * $rating_item['weight'] );
				$count_rating_items++;
			//}
		}
	
		foreach ( $rating_item_entry_value_rows as $rating_item_entry_value_row ) {
			
			$rating_item_id = $rating_item_entry_value_row->rating_item_id;
		
			// check rating item is available, if it's been deleted it wont be included in rating result
			if ( isset( $rating_items[$rating_item_id] ) && isset( $rating_items[$rating_item_id]['max_option_value'] ) ) {

				//if ($rating_items[$rating_item_id]['exclude_result'] == true) {
				//	continue;
				//}
		
				// add value and max option values
				$value = $rating_item_entry_value_row->value;
				$max_option_value = $rating_items[$rating_item_id]['max_option_value'];
		
				if ( $value > $max_option_value ) {
					$value = $max_option_value;
				}
				
				// make adjustments to the rating for weights
				$weight = $rating_items[$rating_item_id]['weight'];

				// score result
				$score_result += intval( $value ) ;
				$adjusted_score_result += ($value * $weight);
			} else {
				continue; // skip
			}
		}
	
		if ( count( $rating_item_entry_value_rows ) > 0 ) {
			// calculate 5 star result
			$star_result = ( doubleval( $score_result ) / doubleval( $total_max_option_value ) ) * 5;
			$adjusted_star_result = ( doubleval( $adjusted_score_result ) / doubleval( $total_adjusted_max_option_value ) ) * 5;
		
			// calculate percentage result
			$percentage_result = ( doubleval( $score_result ) / doubleval( $total_max_option_value ) ) * 100;
			$adjusted_percentage_result = ( doubleval( $adjusted_score_result ) / doubleval( $total_adjusted_max_option_value ) ) * 100;
		
			// calculate adjusted score result relative to max value
			$adjusted_score_result = ( doubleval( $adjusted_score_result ) / doubleval( $total_adjusted_max_option_value ) ) * $total_max_option_value;

		}
	
		return array(
				'adjusted_star_result' => $adjusted_star_result,
				'star_result' => $star_result,
				'total_max_option_value' => $total_max_option_value,
				'adjusted_score_result' => $adjusted_score_result,
				'score_result' => $score_result,
				'percentage_result' => $percentage_result,
				'adjusted_percentage_result' => $adjusted_percentage_result
		);
	}
	
	/**
	 * Sorts top rating results by score result type
	 * 
	 * @param $a
	 * @param $b
	 */
	private static function sort_top_rating_results_by_score_result_type( $a, $b ) {
		
		if ( $a['adjusted_score_result'] == $b['adjusted_score_result'] ) {
			return 0;
		}
		
		return ( $a['adjusted_score_result'] > $b['adjusted_score_result'] ) ? -1 : 1;
	}
	
	/**
	 * Sorts top rating results by percentage or star rating result type
	 *
	 * @param $a
	 * @param $b
	 */
	private static function sort_top_rating_results_by_percentage_result_type( $a, $b ) {
	
		if ( $a['adjusted_percentage_result'] == $b['adjusted_percentage_result'] ) {
			return 0;
		}
	
		return ( $a['adjusted_percentage_result'] > $b['adjusted_percentage_result'] ) ? -1 : 1;
	}
	
	
	/**
	 * Get the top rating results
	 *
	 * @param $limit the count of top rating results to return
	 * @param $category_id
	 * @return top rating results
	 */
	public static function get_top_rating_results( $params = array() ) {
	
		$general_settings = (array) get_option( Multi_Rating::GENERAL_SETTINGS );
		
		extract( wp_parse_args( $params, array(
				'taxonomy' => null,
				'term_id' => 0,
				'limit' => 10,
				'result_type' => Multi_Rating::STAR_RATING_RESULT_TYPE,
		) ) );
		
		if ( $term_id == 0 && $taxonomy != null ){
			// get all terms in the taxonomy
			$tax_terms = get_terms( $taxonomy );
			
			// convert array of term objects to array of term Id's
			$term_id = wp_list_pluck( $tax_terms, 'term_id' );
		}
		
		$post_query_args = array( 
				'numberposts' => -1,
				'post_type' => $general_settings[Multi_Rating::POST_TYPES_OPTION]
		);
		
		if ( $taxonomy != null) {
			$post_query_args = array_merge( $post_query_args, array( 'tax_query' => array( array(
							'taxonomy' => $taxonomy,
							'field' => 'term_id',
							'terms' => $term_id
			) ) ) );
		}
		$posts = get_posts( $post_query_args );
	
		// iterate the post types and calculate rating results
		$rating_results = array();
		foreach ( $posts as $current_post ) {
			
			$post_id = $current_post->ID;
			
			if( ! is_array( $term_id ) ) {
				//skip if not in that term
				$terms_objects = wp_get_object_terms( $post_id, $taxonomy );
			
				if ( !empty( $terms_objects ) ) {
					foreach ( $terms_objects as $current_taxonomy ) {
						if( $current_taxonomy->term_id != $term_id ){
							continue;
						}
					}
				}
			}
			
			$rating_result = Multi_Rating_API::get_rating_result( $post_id );
				
			if ( intval( $rating_result['count'] ) > 0 ) {
				array_push( $rating_results, $rating_result );
			}
		}
	
		if ( $result_type == Multi_Rating::SCORE_RESULT_TYPE ) {
			uasort( $rating_results, array( 'Multi_Rating_API' , 'sort_top_rating_results_by_score_result_type' ) );
		} else {
			uasort( $rating_results, array( 'Multi_Rating_API' , 'sort_top_rating_results_by_percentage_result_type' ) );
		}
	
		$rating_results = array_slice( $rating_results, 0, $limit );
		
		return $rating_results;
	}
	
	/**
	 * Displays the rating form.
	 *
	 * @param unknown_type $params
	 * @return html
	 */
	public static function display_rating_form( $params = array()) {
	
		$general_settings = (array) get_option( Multi_Rating::GENERAL_SETTINGS );
		$custom_text_settings = (array) get_option( Multi_Rating::CUSTOM_TEXT_SETTINGS );
		$position_settings = (array) get_option( Multi_Rating::POSITION_SETTINGS );
	
		extract( wp_parse_args( $params, array(
				'post_id' => null,
				'title' => $custom_text_settings[Multi_Rating::RATING_FORM_TITLE_TEXT_OPTION],
				'before_title' => '<h4>',
				'after_title' => '</h4>',
				'submit_button_text' => $custom_text_settings[Multi_Rating::SUBMIT_RATING_FORM_BUTTON_TEXT_OPTION],
				'echo' => true,
				'class' => ''
		) ) );
	
		// get post id
		global $post;
	
		if ( !isset( $post_id ) && isset( $post ) ) {
			$post_id = $post->ID;
		} else if ( ! isset( $post ) && ! isset( $post_id ) ) {
			return; // No post Id available to display rating form
		}
	
		$rating_items = Multi_Rating_API::get_rating_items( array() );
	
		$params = array(
				'title' => $title,
				'before_title' => $before_title,
				'after_title' => $after_title,
				'submit_button_text' => $submit_button_text,
				'class' => $class
		);
		
		ob_start();
		do_action( 'mr_display_rating_form', $rating_items, $post_id, $params );
		$html = ob_get_contents();
		ob_end_clean();
	
		if ( $echo == true ) {
			echo $html;
		}
	
		return $html;
	}
	
	/**
	 * Displays the rating result
	 *
	 * @param unknown_type $atts
	 * @return void|string
	 */
	public static function display_rating_result( $params = array()) {
	
		$general_settings = (array) get_option( Multi_Rating::GENERAL_SETTINGS );
		$custom_text_settings = (array) get_option( Multi_Rating::CUSTOM_TEXT_SETTINGS );
	
		extract( wp_parse_args( $params, array(
				'post_id' => null,
				'no_rating_results_text' => $custom_text_settings[Multi_Rating::NO_RATING_RESULTS_TEXT_OPTION],
				'show_rich_snippets' => false,
				'show_title' => false,
				'show_date' => true,
				'show_count' => true,
				'echo' => true,
				'result_type' => Multi_Rating::STAR_RATING_RESULT_TYPE,
				'class' => ''
		) ) );
	
		if ( is_string($show_rich_snippets ) ) {
			$show_rich_snippets = $show_rich_snippets == 'true' ? true : false;
		}
		if ( is_string( $show_title ) ) {
			$show_title = $show_title == 'true' ? true : false;
		}
		if ( is_string( $show_date ) ) {
			$show_date = $show_date == 'true' ? true : false;
		}
		if ( is_string( $show_count ) ) {
			$show_count = $show_count == 'true' ? true : false;
		}
		if ( is_string($echo ) ) {
			$echo = $echo == 'true' ? true : false;
		}
	
		// get post id
		global $post;
	
		if ( !isset( $post_id ) && isset( $post ) ) {
			$post_id = $post->ID;
		} else if ( ! isset($post) && ! isset( $post_id ) ) {
			return; // No post Id available to display rating form
		}
	
		$rating_result = Multi_Rating_API::get_rating_result( $post_id );
		
		$params = array(
				'no_rating_results_text' => $no_rating_results_text,
				'show_rich_snippets' => $show_rich_snippets,
				'show_title' => $show_title,
				'show_date' => $show_date,
				'show_count' => $show_count,
				'no_rating_results_text' => $no_rating_results_text,
				'result_type' => $result_type,
				'class' => $class . ' rating-result-' . $post_id
		);
		
		ob_start();
		do_action( 'mr_display_rating_results', $rating_result, $params );
		$html = ob_get_contents();
		ob_end_clean();
	
		if ( $echo == true ) {
			echo $html;
		}
	
		return $html;
	}
	
	/**
	 * Displays the top rating results
	 * 
	 * @param $params
	 * @return html
	 */
	public static function display_top_rating_results( $params = array()) {
	
		$general_settings = (array) get_option( Multi_Rating::GENERAL_SETTINGS );
	
		$custom_text_settings = (array) get_option( Multi_Rating::CUSTOM_TEXT_SETTINGS );
	
		extract( wp_parse_args( $params, array(
				'title' => $custom_text_settings[Multi_Rating::TOP_RATING_RESULTS_TITLE_TEXT_OPTION],
				'before_title' => '<h4>',
				'after_title' => '</h4>',
				'no_rating_results_text' => $custom_text_settings[Multi_Rating::NO_RATING_RESULTS_TEXT_OPTION ],
				'show_count' => true,
				'echo' => true,
				'show_category_filter' => true,
				'category_id' => 0, // 0 = All,
				'limit' => 10, // modified was count
				'show_rank' => true,
				'result_type' => Multi_Rating::STAR_RATING_RESULT_TYPE,
		        'show_title' => true,
				'class' => '',
				'taxonomy' => null,
				'term_id' => 0, // 0 = All
				'filter_button_text' => $custom_text_settings[Multi_Rating::FILTER_BUTTON_TEXT_OPTION ],
				'category_label_text' => $custom_text_settings[Multi_Rating::CATEGORY_LABEL_TEXT_OPTION ],
				'show_featured_img' => false,
				'image_size' => 'thumbnail'
		) ) );
	
		if ( is_string( $show_count ) ) {
			$show_count = $show_count == 'true' ? true : false;
		}
		if ( is_string( $echo ) ) {
			$echo = $echo == 'true' ? true : false;
		}
		if ( is_string( $show_category_filter ) ) {
			$show_category_filter = $show_category_filter == 'true' ? true : false;
		}
		if ( is_string( $show_rank ) ) {
			$show_rank = $show_rank == 'true' ? true : false;
		}
		if ( is_string( $show_title ) ) {
			$show_title = $show_title == 'true' ? true : false;
		}
		if ( is_string( $show_featured_img ) ) {
			$show_featured_img = $show_featured_img == 'true' ? true : false;
		}
		
		// show the filter for categories
		if ( $show_category_filter == true ) {
			
			// override category id if set in HTTP request
			if ( isset( $_REQUEST['category-id'] ) ) {
				$category_id = $_REQUEST['category-id'];
			}
		}
		
		if ($category_id != 0) {
			$term_id = $category_id;
			$taxonomy = 'category';
		}
	
		if ( $term_id == 0 ) {
			$term_id = null; // so that all categories are returned
		}
		
		$top_rating_results = Multi_Rating_API::get_top_rating_results( array(
				'limit' => $limit,
				'taxonomy' => $taxonomy,
				'term_id' => $term_id,
				'result_type' => $result_type
		) );
		
		$params = array(
				'show_title' => $show_title,
				'show_count' => $show_count,
				'show_category_filter' => $show_category_filter,
				'category_id' => $category_id,
				'before_title' => $before_title,
				'after_title' => $after_title,
				'title' => $title,
				'show_rank' => $show_rank,
				'no_rating_results_text' => $no_rating_results_text,
				'result_type' => $result_type,
				'class' => $class,
				'term_id' => $term_id,
				'taxonomy' => $taxonomy,
				'filter_button_text' => $filter_button_text,
				'category_label_text' => $category_label_text,
				'show_featured_img' => $show_featured_img,
				'image_size' => $image_size
		);
		
		ob_start();
		do_action( 'mr_display_top_rating_results', $top_rating_results, $params );
		$html = ob_get_contents();
		ob_end_clean();
		
		if ( $echo == true ) {
			echo $html;
		}
	
		return $html;
	}
	
	/**
	 * Generates rating results in CSV format.
	 *
	 * @param $file_name the file_name to save
	 * @param $filters used to filter the report e.g. from_date, to_date, user_id etc...
	 * @returns true if report successfully generated and written to file
	 */
	public static function generate_rating_results_csv_file( $file_name, $filters ) {
	
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
}
?>