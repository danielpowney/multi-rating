<?php
if( ! class_exists( 'WP_List_Table' ) ) {
	require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

/**
 * MR_Rating_Results_Table class
 * 
 * @author dpowney
 *
 */
class MR_Rating_Results_Table extends WP_List_Table {

	const
	CHECKBOX_COLUMN = 'cb',
	POST_ID_COLUMN = 'post_id',
	TITLE_COLUMN = 'title',
	RATING_RESULT_COLUMN = 'rating_result',
	SHORTCODE_COLUMN = 'shortcode',
	ENTRIES_COUNT_COLUMN = 'entries_count',
	ACTION_COLUMN = 'action',
	DELETE_CHECKBOX = 'delete[]';

	/**
	 * Constructor
	 */
	function __construct() {
		
		parent::__construct( array(
				'singular'=> __( 'Rating Results', 'multi-rating' ),
				'plural' => __( 'Rating Results', 'multi-rating' ),
				'ajax'	=> false
		) );
	}

	/**
	 * (non-PHPdoc)
	 * @see WP_List_Table::extra_tablenav()
	 */
	function extra_tablenav( $which ) {
		
		if ( $which == "top" ){
			
			$post_id = '';
			if ( isset( $_REQUEST['post-id'] ) ) {
				$post_id = $_REQUEST['post-id'];
			}
			
			$sort_by = '';
			if ( isset( $_REQUEST['sort-by'] ) ) {
				$sort_by = $_REQUEST['sort-by'];
			}
			
			global $wpdb;
			?>
			
			<div class="alignleft filters">							
				<select name="post-id" id="post-id">
					<option value=""><?php _e( 'All posts / pages', 'multi-rating' ); ?></option>
					<?php	
					global $wpdb;
					$query = 'SELECT DISTINCT post_id FROM ' . $wpdb->prefix . Multi_Rating::RATING_ITEM_ENTRY_TBL_NAME;
					
					$rows = $wpdb->get_results( $query, ARRAY_A );
					foreach ( $rows as $row ) {
						$temp_post_id = $row['post_id'];
						
						$selected = '';
						if ( intval( $row['post_id'] ) == intval( $post_id ) ) {
							$selected = ' selected="selected"';
						}
						
						// WPML get adjusted post id for active language, we need to use the default language though as the selected value
						if ( function_exists( 'icl_object_id' ) ) {
							$temp_post_id = icl_object_id ( $temp_post_id , get_post_type( $temp_post_id ), true, ICL_LANGUAGE_CODE );
						}
						
						?>
						<option value="<?php echo $row['post_id']; ?>" <?php echo $selected; ?>>
							<?php echo get_the_title( $temp_post_id ); ?>
						</option>
					<?php } ?>
				</select>
				
				<label for="sort-by"><?php _e('Sort', 'multi-rating' ); ?></label>
				<select id="sort-by" name="sort-by">
				<option value=""></option>
					<option value="post_title_asc" <?php if ( $sort_by == 'post_title_asc' ) { echo 'selected="selected"'; } ?>><?php _e( 'Post Title Ascending', 'multi-rating' ); ?></option>
					<option value="post_title_desc" <?php if ( $sort_by == 'post_title_desc' ) { echo 'selected="selected"'; } ?>><?php _e( 'Post Title Descending', 'multi-rating' ); ?></option>
					<option value="top_rating_results" <?php if ( $sort_by == 'top_rating_results' ) { echo 'selected="selected"'; } ?>><?php _e( 'Top Rating Results', 'multi-rating' ); ?></option>
					<option value="most_entries" <?php if ( $sort_by == 'most_entries' ) { echo 'selected="selected"'; } ?>><?php _e( 'Most Entries', 'multi-rating' ); ?></option>
				</select>
				
				<input type="submit" class="button" value="<?php _e( 'Filter', 'multi-rating' ); ?>"/>
			</div>
						
			<?php
		}
		
		if ( $which == "bottom" ){
			
		}
	}

	/**
	 * (non-PHPdoc)
	 * @see WP_List_Table::get_columns()
	 */
	function get_columns() {
		
		$columns= array(
				MR_Rating_Results_Table::POST_ID_COLUMN => __( 'Post', 'multi-rating' ),
				MR_Rating_Results_Table::RATING_RESULT_COLUMN => __( 'Rating Result', 'multi-rating' ),
				MR_Rating_Results_Table::ENTRIES_COUNT_COLUMN => __( 'Entries', 'multi-rating' ),
				MR_Rating_Results_Table::ACTION_COLUMN => __( 'Action', 'multi-rating' ),
				MR_Rating_Results_Table::SHORTCODE_COLUMN => __( 'Shortcode', 'multi-rating' )
		);
		
		if ( current_user_can( 'manage_options' ) ) {
			$columns = array_merge( array( MR_Rating_Results_Table::CHECKBOX_COLUMN => '<input type="checkbox" />' ), $columns );
		}
		
		return $columns;
	}

	/**
	 * (non-PHPdoc)
	 * @see WP_List_Table::prepare_items()
	 */
	function prepare_items() {
		global $wpdb;
		
		// Process any bulk actions first
		if ( current_user_can( 'manage_options' ) ) {
			$this->process_bulk_action();
		}
		
		// Register the columns
		$columns = $this->get_columns();
		$hidden = array( );
		$sortable = $this->get_sortable_columns();
		$this->_column_headers = array($columns, $hidden, $sortable);
		
		$post_id = isset( $_REQUEST['post-id'] ) ? $_REQUEST['post-id'] : null;
		$sort_by = isset( $_REQUEST['sort-by'] ) ? $_REQUEST['sort-by'] : null;
		
		// get table data
		$query = 'SELECT rie.post_id AS post_id';
		if ( $sort_by == 'post_title_asc' || $sort_by == 'post_title_desc' ) {
			$query .= ', p.post_title AS post_title';
		}
		
		$query .= ' FROM ' . $wpdb->prefix . Multi_Rating::RATING_ITEM_ENTRY_TBL_NAME . ' as rie';
		if ( $sort_by == 'post_title_asc' || $sort_by == 'post_title_desc' ) {
			$query .= ', ' . $wpdb->posts . ' as p';
		}
		
		$added_to_query = false;
		if ( $post_id || $sort_by == 'post_title_asc' || $sort_by == 'post_title_desc' ) {
			$query .= ' WHERE';
		}
		
		if ( $post_id ) {
			if ($added_to_query) {
				$query .= ' AND';
			}
		
			$query .= ' rie.post_id = "' . $post_id . '"';
			$added_to_query = true;
		}
		
		if ( $sort_by == 'post_title_asc' || $sort_by == 'post_title_desc' ) {
			if ($added_to_query) {
				$query .= ' AND';
			}
				
			$query .= ' rie.post_id = p.ID';
			$added_to_query = true;
		}
		
		$query .= ' GROUP BY rie.post_id';
		
		if ( $sort_by == 'post_title_asc' ) {
			$query .= ' ORDER BY post_title ASC';
		} else if ( $sort_by == 'post_title_desc' ) {
			$query .= ' ORDER BY post_title DESC';
		}
		
		// pagination
		$item_count = $wpdb->query( $query ); //return the total number of affected rows
		$items_per_page = 10;
		$page_num = ! empty( $_GET["paged"] ) ? mysql_real_escape_string( $_GET["paged"] ) : '';
		if ( empty( $page_num ) || ! is_numeric( $page_num ) || $page_num <= 0 ) {
			$page_num = 1;
		}
		$total_pages = ceil( $item_count / $items_per_page );
		// adjust the query to take pagination into account
		if ( ! empty( $page_num ) && ! empty( $items_per_page ) ) {
			$offset = ( $page_num -1 ) * $items_per_page;
			$query .= ' LIMIT ' . ( int ) $offset. ',' . ( int ) $items_per_page;
		}
		
		$this->set_pagination_args( array( 
				'total_items' => $item_count,
				'total_pages' => $total_pages,
				'per_page' => $items_per_page
		) );
		
		$results =  $wpdb->get_results( $query, ARRAY_A );
		
		if ( $sort_by == 'top_rating_results' ) {
			
			$this->items = array();

			foreach ( $results as $row ) {
				$post_id = $row['post_id'];
				
				$rating_items = Multi_Rating_API::get_rating_items( array(
						'post_id' => $post_id
				) );
				$rating_result = Multi_Rating_API::calculate_rating_result( array(
						'post_id' => $post_id,
						'rating_items' => $rating_items
				) );
				
				$row['rating_result'] = $rating_result;
				
				array_push($this->items, $row);
			}
			
			uasort( $this->items, array( 'MR_Rating_Results_Table' , 'sort_top_rating_results' ) );
		} else if ( $sort_by == 'most_entries' ) {
			
			$this->items = array();
			
			foreach ( $results as $row ) {
				$post_id = $row['post_id'];
				
				global $wpdb;
				$query = $query = 'SELECT COUNT(*) FROM ' . $wpdb->prefix . Multi_Rating::RATING_ITEM_ENTRY_TBL_NAME 
						. ' WHERE post_id = "' . $post_id . '"';
				$count = $wpdb->get_col( $query, 0 );
				
				$row['entries_count'] = $count[0];
				
				array_push( $this->items, $row);
			}
			
			uasort( $this->items, array( 'MR_Rating_Results_Table' , 'sort_most_entries' ) );
		} else {
			$this->items = $results;
		}
	}
	
	/**
	 * Helper to sort by top rating results
	 *
	 * @param unknown_type $a
	 * @param unknown_type $b
	 */
	private static function sort_top_rating_results( $a, $b ) {
	
		$rating_result_a = $a['rating_result'];
		$rating_result_b = $b['rating_result'];
		
		if ( $rating_result_a['adjusted_percentage_result'] == $rating_result_b['adjusted_percentage_result'] ) {
			return 0;
		}
	
		return ( $rating_result_a['adjusted_percentage_result'] > $rating_result_b['adjusted_percentage_result'] ) ? -1 : 1;
	}
	
	/**
	 * Helper to sort by most entries
	 *
	 * @param unknown_type $a
	 * @param unknown_type $b
	 */
	private static function sort_most_entries( $a, $b ) {
		
		if ( $a['entries_count'] == $b['entries_count'] ) {
			return 0;
		}
	
		return ( $a['entries_count'] > $b['entries_count'] ) ? -1 : 1;
	}

	/**
	 * Default column
	 * @param unknown_type $item
	 * @param unknown_type $column_name
	 * @return unknown|mixed
	 */
	function column_default( $item, $column_name ) {
		
		$post_id =  $item[MR_Rating_Results_Table::POST_ID_COLUMN];
		
		switch( $column_name ) {
			case MR_Rating_Results_Table::SHORTCODE_COLUMN : {
				
				echo '[mr_rating_result post_id="' . $post_id . '"]';
				break;
			}
			
			case MR_Rating_Results_Table::POST_ID_COLUMN : {
				$temp_post_id = $post_id;
				
				// WPML get adjusted post id for active language, just for the string translation
				if ( function_exists( 'icl_object_id' ) ) {
					$temp_post_id = icl_object_id ( $post_id , get_post_type( $post_id ), true, ICL_LANGUAGE_CODE );
				}	
			
				echo '<a href="' . get_permalink( $temp_post_id ) . '">' . get_the_title( $temp_post_id ) . '</a> (Id=' . $post_id . ')';
				break;
			}
			
			case MR_Rating_Results_Table::ACTION_COLUMN : {
							
				?>
				<a class="view-rating-result-entries-anchor" href="?page=<?php echo Multi_Rating::RATING_RESULTS_PAGE_SLUG; ?>&tab=<?php 
						echo Multi_Rating::ENTRIES_TAB; ?>&post-id=<?php echo $post_id ?>"><?php 
						_e( 'View Entries', 'multi-rating' ); ?></a>
				<?php
				break;
			}
			
			case MR_Rating_Results_Table::ENTRIES_COUNT_COLUMN : {
				global $wpdb;
				
				$query = $query = 'SELECT COUNT(*) FROM ' . $wpdb->prefix . Multi_Rating::RATING_ITEM_ENTRY_TBL_NAME . ' WHERE post_id = "' 
						. $post_id . '"';
				$rows = $wpdb->get_col( $query, 0 );
				
				echo $rows[0];
				
				break;
			}
			
			case MR_Rating_Results_Table::RATING_RESULT_COLUMN : {
				
				$rating_items = Multi_Rating_API::get_rating_items( array( 
						'post_id' => $post_id
				) );
				$rating_result = Multi_Rating_API::calculate_rating_result( array(
						'post_id' => $post_id,
						'rating_items' => $rating_items
				) );
				
				$entries = $rating_result['count'];
				if ($entries != 0) {
					echo __('Star: ', 'multi-rating' ) . '<span style="color: #0074a2;">' . round( $rating_result['adjusted_star_result'], 2 ) . '/5</span><br />'
							. __('Score: ', 'multi-rating' ) . '<span style="color: #0074a2;">' . round( $rating_result['adjusted_score_result'], 2) . '/' . $rating_result['total_max_option_value'] . '</span><br />' 
							. __('Percentage: ', 'multi-rating' ) . '<span style="color: #0074a2;">' . round( $rating_result['adjusted_percentage_result'], 2) . '%</span>';				
				} else {
					_e( 'None', 'multi-rating' );	
				}
				break;
			}
			
			case Rating_Item_Entry_Table::CHECKBOX_COLUMN :
				return $item[ $column_name ];
				break;
			default:
				return print_r( $item, true ) ;
		}
	}
	
	/**
	 * checkbox column
	 * @param unknown_type $item
	 * @return string
	 */
	function column_cb($item) {
		
		return sprintf(
				'<input type="checkbox" name="' . MR_Rating_Results_Table::DELETE_CHECKBOX . '" value="%s" />', $item[MR_Rating_Results_Table::POST_ID_COLUMN]
		);
	}
	
	/**
	 * (non-PHPdoc)
	 * @see WP_List_Table::get_bulk_actions()
	 */
	function get_bulk_actions() {
		
		$bulk_actions = array();
		
		if ( current_user_can( 'manage_options' ) ) {
			$bulk_actions = array(
					'delete'    => __( 'Delete', 'multi-rating' )
			);
		}
		
		return $bulk_actions;
	}
	
	/**
	 * Handles bulk actions
	 */
	function process_bulk_action() {
		
		if ( ! current_user_can( 'manage_options' ) ) {
			return; // should not get here
		}
		
		if ( $this->current_action() ==='delete' ) {
			global $wpdb;
				
			$checked = ( is_array( $_REQUEST['delete'] ) ) ? $_REQUEST['delete'] : array( $_REQUEST['delete'] );
				
			foreach( $checked as $post_id ) {
				
				/*
				 * delete rating item entry values as well
				 */ 
				$entries = Multi_Rating_API::get_rating_item_entries( array( 
						'post_id' => $post_id
				) );
				
				foreach ( $entries as $entry ) {
					$rating_item_entry_id = $entry['rating_item_entry_id'];
					
					$entry_values_query = 'DELETE FROM '. $wpdb->prefix.Multi_Rating::RATING_ITEM_ENTRY_VALUE_TBL_NAME . '  WHERE ' .  MR_Rating_Entry_Table::RATING_ITEM_ENTRY_ID_COLUMN . ' = "' . $rating_item_entry_id . '"';
					$results = $wpdb->query($entry_values_query);
					
					$entries_query = 'DELETE FROM '. $wpdb->prefix.Multi_Rating::RATING_ITEM_ENTRY_TBL_NAME . '  WHERE ' .  MR_Rating_Entry_Table::RATING_ITEM_ENTRY_ID_COLUMN . ' = "' . $rating_item_entry_id . '"';	
					$results = $wpdb->query($entries_query);
				}
				
				/* 
				 * delete rating results cache in WordPress postmeta table
				 */
				delete_post_meta( $post_id, Multi_Rating::RATING_RESULTS_POST_META_KEY );
			}
				
			echo '<div class="updated"><p>' . __( 'Rating results deleted successfully.', 'multi-rating' ) . '</p></div>';
		}
	}
}