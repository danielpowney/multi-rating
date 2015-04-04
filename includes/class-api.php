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
	 * @param array $params	rating_item_entry_id and post_id
	 * @return rating items
	 */
	public static function get_rating_items( $params = array() ) {
		
		$rating_item_ids = isset( $params['rating_item_ids'] ) ? $params['rating_item_ids'] : null;
		$rating_entry_id = isset( $params['rating_item_entry_id'] ) ? esc_sql( $params['rating_item_entry_id'] ) : null;
		$post_id = isset( $params['post_id'] ) ? esc_sql( $params['post_id'] ) : null;
		
		global $wpdb;
		
		// base query
		$rating_items_query = 'SELECT ri.rating_item_id, ri.rating_id, ri.description, ri.default_option_value, '
				. 'ri.max_option_value, ri.weight, ri.active, ri.type FROM '
				. $wpdb->prefix . Multi_Rating::RATING_ITEM_TBL_NAME . ' as ri';
		
		if ( $rating_entry_id || $post_id ) {
			
			$rating_items_query .= ', ' . $wpdb->prefix . Multi_Rating::RATING_ITEM_ENTRY_TBL_NAME . ' AS rie, '
					. $wpdb->prefix.Multi_Rating::RATING_ITEM_ENTRY_VALUE_TBL_NAME
					. ' AS riev';
		}
		
		$added_to_query = false;
		if ( $rating_entry_id || $post_id ) {
			
			$rating_items_query .= ' WHERE';
			$rating_items_query .= ' riev.rating_item_entry_id = rie.rating_item_entry_id AND ri.rating_item_id = riev.rating_item_id';
			$added_to_query = true;
		}
		
		// rating_item_entry_id
		if ( $rating_entry_id ) {
			
			if ( $added_to_query == true ) {
				$rating_items_query .= ' AND';
				$added_to_query = false;
			}
			
			$rating_items_query .= ' rie.rating_item_entry_id =  ' . $rating_entry_id;
			$added_to_query = true;
		}
		
		// post_id
		if ( $post_id ) {
			
			if ( $added_to_query == true ) {
				$rating_items_query .= ' AND';
				$added_to_query = false;
			}
			
			$rating_items_query .= ' rie.post_id = ' . $post_id;
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
			
			// WPML translate string
			if ( function_exists( 'icl_translate' ) && strlen( $description ) > 0 ) {
				$description = icl_translate( 'multi-rating', 'rating-item-' . $rating_item_id . '-description', $description );
			}
			
			$rating_items[$rating_item_id] = array(
					'max_option_value' => intval( $max_option_value ),
					'weight' => floatval( $weight ),
					'rating_item_id' => intval( $rating_item_id ),
					'description' => stripslashes( $description ),
					'default_option_value' => intval( $default_option_value ),
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
	
		if ( ! isset( $params['rating_items'] ) || ! isset( $params['post_id'] ) ) {
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
		
		$count_entries = count( $rating_item_entries );
		
		// get max option value
		$total_max_option_value = 0;
		foreach ($rating_items as $rating_item) {
			$total_max_option_value += $rating_item['max_option_value'];
		}
		
		$count_entries = count( $rating_item_entries );
	
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
					. 'AND riev.rating_item_entry_id = ' . esc_sql( $rating_item_entry['rating_item_entry_id'] );
				
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
				'published_posts_only' => true,
				'group_by' => array(),
				'order_by' => array()
		) ) );	
	
		global $wpdb;
	
		/*
		 * Select
		 */
		$query_select = 'SELECT rie.rating_item_entry_id, rie.user_id, rie.post_id, rie.entry_date';
		
		if ( $published_posts_only ) {
			$query_select .= ', p.post_status ';
		}
		
		$query_select = apply_filters( 'mr_entries_query_select', $query_select, $params );

		/*
		 * From
		 */
		$query_from = ' FROM '.$wpdb->prefix . Multi_Rating::RATING_ITEM_ENTRY_TBL_NAME . ' as rie';
		
		if ( $taxonomy != null || $published_posts_only ) {
			$query_from .= ', ' . $wpdb->prefix . 'posts as p';
		}
		
		$query_from = apply_filters( 'mr_entries_query_from', $query_from, $params );
		
		/*
		 * Join
		 */
		$query_join = '';
		
		if ( $taxonomy != null ) {
			$query_join .= ' LEFT JOIN ' . $wpdb->prefix . 'term_relationships rel ON rel.object_id = p.ID';
			$query_join .= ' LEFT JOIN ' . $wpdb->prefix . 'term_taxonomy tax ON tax.term_taxonomy_id = rel.term_taxonomy_id';
			$query_join .= ' LEFT JOIN ' . $wpdb->prefix . 'terms t ON t.term_id = tax.term_id';
		}
		
		$query_join = apply_filters( 'mr_entries_query_join', $query_join, $params );
		
		/*
		 * Where
		 */
		$query_where = '';
		
		$added_to_query = false;
		// is a WHERE clause required?
		if ( $post_id || $user_id ||$from_date || $to_date || $taxonomy || $published_posts_only ) {

			$query_where .= ' WHERE';
		}
	
		if ( $post_id ) {	
			if ( $added_to_query ) {
				$query_where .= ' AND';
			}
			
			$query_where .= ' rie.post_id = ' . esc_sql( $post_id );
			$added_to_query = true;
		}
		
		if ( $user_id ) {
			if ( $added_to_query ) {
				$query_where .= ' AND';
			}
			
			$query_where .= ' rie.user_id = ' . esc_sql( $user_id );
			$added_to_query = true;
		}
		
		if ( $taxonomy ) {
			if ($added_to_query) {
				$query .= ' AND';
			}
			
			$query_where .= ' p.ID = rie.post_id AND tax.taxonomy = "' . esc_sql( $taxonomy ) . '"';

			if ( $term_id ) {
			 	$query_where .= ' AND t.term_id IN (' . esc_sql( $term_id ) . ')';
			}
			 
			$added_to_query = true;
		}

		if ( $from_date ) {
			if ( $added_to_query ) {
				$query_where .= ' AND';
			}
			
			$query_where .= ' rie.entry_date >= "' . esc_sql( $from_date ) . '"';
			$added_to_query = true;
		}
		
		if ( $to_date ) {
			if ( $added_to_query ) {
				$query_where .= ' AND';
			}
			
			$query_where .= ' rie.entry_date <= "' . esc_sql( $to_date ) . '"';
			$added_to_query = true;
		}
		
		// only return published posts
		if ( $published_posts_only ) {
			if ( $added_to_query ) {
				$query_where .= ' AND';
			}
				
			$query_where .= ' p.ID = rie.post_id AND p.post_status = "publish"';
			$added_to_query = true;
		}
		
		$query_where = apply_filters( 'mr_entries_query_where', $query_where, $params );
		
		/*
		 * Group by
		 */
		$added_to_query = false;
		$query_group_by = '';
		foreach ( $group_by as $temp_group_by ) {
			if ( strlen( $query_group_by ) == 0 ) {
				$query_group_by .= ' GROUP BY ';
			}
				
			if ( $added_to_query ) {
				$query_group_by .= ', ';
			}
				
			$query_group_by .= esc_sql( $temp_group_by );
			$added_to_query = true;
		}
		$query_group_by = apply_filters( 'mr_entries_query_group_by', $query_group_by, $params );
		
		/*
		 * Order by
		*/
		$query_order_by = '';
		$added_to_query = false;
		foreach ( $order_by as $temp_order_by ) {
			if ( strlen( $query_order_by ) == 0 ) {
				$query_order_by .= ' ORDER BY ';
			}
		
			if ( $added_to_query ) {
				$query_order_by .= ', ';
			}
		
			$query_order_by .= esc_sql( $temp_order_by );
			$added_to_query = true;
		}
		$query_order_by = apply_filters( 'mr_entries_query_order_by', $query_order_by, $params );
		
		/*
		 * Limit
		 */
		$query_limit = '';
		
		if ( $limit && is_numeric( $limit ) ) {
			if ( intval( $limit ) > 0 ) {
				$query_limit .= ' LIMIT 0, ' . intval( $limit );
			}
		}
		$query_limit = apply_filters( 'mr_entries_query_limit', $query_limit, $params );
	
		$query = $query_select . $query_from . $query_join . $query_where . $query_group_by . $query_order_by .  $query_limit;
		$query = apply_filters( 'mr_entries_query', $query, $params );
		
		$rows = $wpdb->get_results($query );
		
		$rating_item_entries = array();
		foreach ( $rows as $row ) {
			$rating_item_entry = array(
					'rating_item_entry_id' => intval( $row->rating_item_entry_id ),
					'user_id' => intval( $row->user_id ),
					'post_id' => intval( $row->post_id ),
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
				. ' WHERE rating_item_entry_id = ' . esc_sql( $rating_item_entry_id );
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
		
		// FIXME if rating item has been deleted, this returns division by zero error
	
		if ( count( $rating_item_entry_value_rows ) > 0 ) {
			// calculate 5 star result
			$star_result = round( ( doubleval( $score_result ) / doubleval( $total_max_option_value ) ) * 5, 2 );
			$adjusted_star_result = round( ( doubleval( $adjusted_score_result ) / doubleval( $total_adjusted_max_option_value ) ) * 5, 2);
		
			// calculate percentage result
			$percentage_result = round( ( doubleval( $score_result ) / doubleval( $total_max_option_value ) ) * 100, 2);
			$adjusted_percentage_result = round( ( doubleval( $adjusted_score_result ) / doubleval( $total_adjusted_max_option_value ) ) * 100, 2);
		
			// calculate adjusted score result relative to max value
			$adjusted_score_result = round( ( doubleval( $adjusted_score_result ) / doubleval( $total_adjusted_max_option_value ) ) * $total_max_option_value, 2);
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
	 * Gets rating results
	 *
	 * @param unknown_type $params
	 */
	public static function get_rating_results( $params = array() ) {
	
		extract( wp_parse_args( $params, array(
			'taxonomy' => null,
			'term_id' => 0,
			'limit' => 10,
			'result_type' => Multi_Rating::STAR_RATING_RESULT_TYPE,
			'sort_by' => 'highest_rated',
			'post_id' => null
		) ) );
		
		$order_by = array();
		if ( $sort_by == 'post_title_asc' ) {
			$order_by = array( 'post_title ASC' );
		} else if ( $sort_by == 'post_title_desc' ) {
			$order_by = array( 'post_title DESC' );
		}
		
		$group_by = array( 'rie.post_id' );
		
		$rating_entries = Multi_Rating_API::get_rating_item_entries( array(
				'taxonomy' => $taxonomy,
				'term_id' => $term_id,
				'post_id' => $post_id,
				'order_by' => $order_by,
				'group_by' => $group_by
		) );
		
		$rating_results = array();
		
		foreach ( $rating_entries as $rating_entry ) {
				
			$temp_post_id = $rating_entry['post_id'];
			$rating_result = Multi_Rating_API::get_rating_result( $temp_post_id );
			
			// WPML get adjusted post id for active language and override
			if ( function_exists( 'icl_object_id' ) ) {
				$rating_result['post_id'] = icl_object_id( $temp_post_id , get_post_type( $temp_post_id ), true, ICL_LANGUAGE_CODE );
			}
			
			array_push( $rating_results, $rating_result);
		}
		
		// TODO pagination
	
		$rating_results = array_slice( MR_Utils::sort_rating_results( $rating_results, $sort_by, $result_type ), 0, $limit );
	
		return $rating_results;
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
		$style_settings = (array) get_option( Multi_Rating::STYLE_SETTINGS );
		
		$font_awesome_version = $style_settings[Multi_Rating::FONT_AWESOME_VERSION_OPTION];
		$icon_classes = MR_Utils::get_icon_classes( $font_awesome_version );
		$use_custom_star_images = $style_settings[Multi_Rating::USE_CUSTOM_STAR_IMAGES];
		$image_width = $style_settings[Multi_Rating::CUSTOM_STAR_IMAGE_WIDTH];
		$image_height = $style_settings[Multi_Rating::CUSTOM_STAR_IMAGE_HEIGHT];
		
		extract( wp_parse_args( $params, array(
				'post_id' => null,
				'no_rating_results_text' => $custom_text_settings[Multi_Rating::NO_RATING_RESULTS_TEXT_OPTION],
				'show_rich_snippets' => false,
				'show_title' => false,
				'show_count' => true,
				'echo' => true,
				'result_type' => Multi_Rating::STAR_RATING_RESULT_TYPE,
				'class' => '',
				'before_count' => '(',
				'after_count' => ')'
		) ) );
		
		if ( is_string( $show_rich_snippets ) ) {
			$show_rich_snippets = $show_rich_snippets == 'true' ? true : false;
		}
		if ( is_string( $show_title ) ) {
			$show_title = $show_title == 'true' ? true : false;
		}
		if ( is_string( $show_count ) ) {
			$show_count = $show_count == 'true' ? true : false;
		}
		if ( is_string( $echo ) ) {
			$echo = $echo == 'true' ? true : false;
		}
		
		// get the post id
		global $post;
		
		if ( ! isset( $post_id ) && isset( $post ) ) {
			$post_id = $post->ID;
		} else if ( ! isset( $post ) && ! isset( $post_id ) ) {
			return; // No post Id available to display rating form
		}
		
		// WPML get original post id for default language to get rating results
		$temp_post_id = $post_id;
		if ( function_exists( 'icl_object_id' ) ) {
			global $sitepress;
			$temp_post_id = icl_object_id( $post_id , get_post_type( $post_id ), false, $sitepress->get_default_language() );
		}
		
		$rating_result = Multi_Rating_API::get_rating_result( $temp_post_id );
		$rating_result['post_id'] = $post_id; // set back to adjusted for WPML
	
		ob_start();
		mr_get_template_part( 'rating-result', null, true, array(
				'no_rating_results_text' => $no_rating_results_text,
				'show_rich_snippets' => $show_rich_snippets,
				'show_title' => $show_title,
				'show_date' => false,
				'show_count' => $show_count,
				'no_rating_results_text' => $no_rating_results_text,
				'result_type' => $result_type,
				'class' => $class . ' rating-result-' . $post_id,
				'rating_result' => $rating_result,
				'before_count' => $before_count,
				'after_count' => $after_count,
				'post_id' => $post_id,
				'ignore_count' => false,
				'preserve_max_option' => false, 
				'icon_classes' => $icon_classes,
				'use_custom_star_images' => $use_custom_star_images,
				'image_width' => $image_width,
				'image_height' => $image_height
		) );
		$html = ob_get_contents();
		ob_end_clean();
		
		$html = apply_filters( 'mr_template_html', $html );
		
		if ( $echo == true ) {
			echo $html;
		}
		
		return $html;
	}
	
	/**
	 * Displays the rating form
	 * 
	 * @param unknown_type $params
	 */
	public static function display_rating_form( $params = array()) {
		
		$general_settings = (array) get_option( Multi_Rating::GENERAL_SETTINGS );
		$custom_text_settings = (array) get_option( Multi_Rating::CUSTOM_TEXT_SETTINGS );
		$position_settings = (array) get_option( Multi_Rating::POSITION_SETTINGS );
		
		$style_settings = (array) get_option( Multi_Rating::STYLE_SETTINGS );
		$font_awesome_version = $style_settings[Multi_Rating::FONT_AWESOME_VERSION_OPTION];
		$icon_classes = MR_Utils::get_icon_classes( $font_awesome_version );
		$use_custom_star_images = $style_settings[Multi_Rating::USE_CUSTOM_STAR_IMAGES];

		extract( wp_parse_args($params, array(
				'post_id' => null,
				'title' => $custom_text_settings[Multi_Rating::RATING_FORM_TITLE_TEXT_OPTION],
				'before_title' => '<h4>',
				'after_title' => '</h4>',
				'submit_button_text' => $custom_text_settings[Multi_Rating::SUBMIT_RATING_FORM_BUTTON_TEXT_OPTION],
				'echo' => true,
				'class' => '',
		) ) );
		
		// get the post id
		global $post;
	
		if ( ! isset( $post_id ) && isset( $post ) ) {
			$post_id = $post->ID;
		} else if ( !isset($post) && !isset( $post_id ) ) {
			return; // No post Id available to display rating form
		}
		
		// WPML get original post id for default language
		if ( function_exists( 'icl_object_id' ) ) {
			global $sitepress;
			$post_id = icl_object_id ( $post_id , get_post_type( $post_id ), false, $sitepress->get_default_language() );
		}
		
		MR_Rating_Form::$sequence++;
	
		$rating_items = Multi_Rating_API::get_rating_items( array( ) );
		
		ob_start();
		mr_get_template_part( 'rating-form', null, true, array(
			'title' => $title,
			'before_title' => $before_title,
			'after_title' => $after_title,
			'submit_button_text' => $submit_button_text,
			'class' => $class,
			'post_id' => $post_id,
			'rating_items' => $rating_items,
			'icon_classes' => $icon_classes,
			'use_custom_star_images' => $use_custom_star_images
		) );
		$html = ob_get_contents();
		ob_end_clean();
		
		$html = apply_filters( 'mr_template_html', $html );
		
		if ( $echo == true ) {
			echo $html;
		}
		
		return $html;
	}
	
	/**
	 * Displays a rating results list. This is used by the Rating Result List widet and shortcode.
	 * 
	 * @param unknown_type $params
	 * @return string
	 */
	public static function display_rating_results_list( $params = array() ) {
		
		$general_settings = (array) get_option( Multi_Rating::GENERAL_SETTINGS );
		$custom_text_settings = (array) get_option( Multi_Rating::CUSTOM_TEXT_SETTINGS );
		$style_settings = (array) get_option( Multi_Rating::STYLE_SETTINGS );
		
		$font_awesome_version = $style_settings[Multi_Rating::FONT_AWESOME_VERSION_OPTION];
		$icon_classes = MR_Utils::get_icon_classes( $font_awesome_version );
		$use_custom_star_images = $style_settings[Multi_Rating::USE_CUSTOM_STAR_IMAGES];
		$image_width = $style_settings[Multi_Rating::CUSTOM_STAR_IMAGE_WIDTH];
		$image_height = $style_settings[Multi_Rating::CUSTOM_STAR_IMAGE_HEIGHT];
		
		extract( wp_parse_args( $params, array(
				'title' => $custom_text_settings[Multi_Rating::RATING_RESULTS_LIST_TITLE_TEXT_OPTION],
				'before_title' => '<h4>',
				'after_title' => '</h4>',
				'no_rating_results_text' => $custom_text_settings[Multi_Rating::NO_RATING_RESULTS_TEXT_OPTION ],
				'show_count' => true,
				'echo' => true,
				'show_category_filter' => true, // @deprecated
				'category_id' => 0, // 0 = All, // uses the category taxonomy
				'limit' => 10, // modified was count
				'show_rank' => true,
				'result_type' => Multi_Rating::STAR_RATING_RESULT_TYPE,
				'show_title' => true,
				'class' => '',
				'taxonomy' => null,
				'term_id' => 0, // 0 = All
				'filter_button_text' => $custom_text_settings[Multi_Rating::FILTER_BUTTON_TEXT_OPTION],
				'category_label_text' => $custom_text_settings[Multi_Rating::FILTER_LABEL_TEXT_OPTION], // @deprecated
				'show_featured_img' => true,
				'image_size' => 'thumbnail',
				
				// new
				'sort_by' => 'highest_rated',
				'filter_label_text' => $custom_text_settings[Multi_Rating::FILTER_LABEL_TEXT_OPTION],
				'show_filter' => false
		) ) );
		
		// temp
		if ( is_string( $show_category_filter ) ) {
			$show_category_filter = $show_category_filter == 'true' ? true : false;
			$show_filter = $show_category_filter; 
		}
		
		if ( is_string( $show_filter ) ) {
			$show_filter = $show_filter == 'true' ? true : false;
		}
		if ( is_string($show_count) ) {
			$show_count = $show_count == 'true' ? true : false;
		}
		if ( is_string( $echo ) ) {
			$echo = $echo == 'true' ? true : false;
		}
		if ( is_string($show_rank ) ) {
			$show_rank = $show_rank == 'true' ? true : false;
		}
		if ( is_string( $show_title ) ) {
			$show_title = $show_title == 'true' ? true : false;
		}
		if ( is_string( $show_featured_img ) ) {
			$show_featured_img = $show_featured_img == 'true' ? true : false;
		}
		
		// show the filter for taxonomy
		if ( $show_filter == true && isset( $_REQUEST['term-id'] ) ) {
			// override category id if set in HTTP request
			$term_id = $_REQUEST['term-id'];
		}
		
		if ( $show_filter && $taxonomy == null ) {
			$taxonomy = 'category';
		}
		
		if ( $category_id != 0) {
			$term_id = $category_id;
			$taxonomy = 'category';
		}
		
		if ( $term_id == 0 ) {
			$term_id = null; // so that all terms are returned
		}
		
		$rating_results = Multi_Rating_API::get_rating_results( array(
				'limit' => $limit,
				'taxonomy' => $taxonomy,
				'term_id' => $term_id,
				'result_type' => $result_type,
				'sort_by' => $sort_by
		) );
		
		ob_start();
		mr_get_template_part( 'rating-result', 'list', true, array(
			'show_title' => $show_title,
			'show_count' => $show_count,
			//'show_category_filter' => $show_category_filter,
			'show_filter' => $show_filter,
			'category_id' => $category_id,
			'before_title' => $before_title,
			'after_title' => $after_title,
			'title' => $title,
			'show_rank' => $show_rank,
			'no_rating_results_text' => $no_rating_results_text,
			'result_type' => $result_type,
			'taxonomy' => $taxonomy,
			'term_id' => $term_id,
			'filter_button_text' => $filter_button_text,
			//'category_label_text' => $category_label_text,
			'filter_label_text' => $filter_label_text,
			'show_featured_img' => $show_featured_img,
			'image_size' => $image_size,
			'show_rich_snippets' => false,
			'class' => $class . ' rating-results-list',
			'rating_results' => $rating_results,
			'before_count' => '(',
			'after_count' => ')',
			'ignore_count' => false,
			'preserve_max_option' => false, 
			'before_date' => '',
			'after_date' => '',
			'icon_classes' => $icon_classes,
			'use_custom_star_images' => $use_custom_star_images,
			'image_width' => $image_width,
			'image_height' => $image_height
		) );
		$html = ob_get_contents();
		ob_end_clean();
		
		$html = apply_filters( 'mr_template_html', $html );
		
		if ( $echo == true ) {
			echo $html;
		}
		
		return $html;
	}
	
	/** @deprecated */
	public static function display_top_rating_results( $params = array()) { return Multi_Rating_API::display_rating_results_list( $params ); }
	/** @deprecated */
	private static function sort_top_rating_results_by_score_result_type( $a, $b ) { return MR_Utils::sort_highest_rated_by_score_result_type($a, $b); }
	/** @deprecated */
	private static function sort_top_rating_results_by_percentage_result_type( $a, $b ) {  return MR_Utils::sort_highest_rated_by_percentage_result_type($a, $b); }
}
?>