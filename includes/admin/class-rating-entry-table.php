<?php
if(!class_exists('WP_List_Table')){
	require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

/**
 * MR_Rating_Entry_Table class
 * @author dpowney
 *
 */
class MR_Rating_Entry_Table extends WP_List_Table {

	const
	CHECKBOX_COLUMN 				= 'cb',
	RATING_ITEM_ENTRY_ID_COLUMN 	= 'rating_item_entry_id',
	POST_ID_COLUMN 					= 'post_id',
	ENTRY_DATE_COLUMN 				= 'entry_date',
	USER_ID_COLUMN 					= 'user_id',
	RATING_RESULT_COLUMN 			= 'rating_result',
	SHORTCODE_COLUMN 				= 'shortcode',
	ACTION_COLUMN 					= 'action',
	DELETE_CHECKBOX 				= 'delete[]';

	/**
	 * Constructor
	 */
	function __construct() {

		parent::__construct( array(
				'singular' => __( 'Rating Result', 'multi-rating' ),
				'plural' => __( 'Rating Results', 'multi-rating' ),
				'ajax'	=> false
		) );

	}

	/**
	 * (non-PHPdoc)
	 * @see WP_List_Table::extra_tablenav()
	 */
	function extra_tablenav( $which ) {

		if ( $which == "top" ) {

			$post_id = '';
			if ( isset( $_REQUEST['post-id'] ) ) {
				$post_id = $_REQUEST['post-id'];
			}

			$to_date = '';
			if (isset( $_REQUEST['to-date'] ) ) {
				$to_date = $_REQUEST['to-date'];
			}

			$from_date = '';
			if (isset( $_REQUEST['from-date'] ) ) {
				$from_date = $_REQUEST['from-date'];
			}

			global $wpdb;
			?>

			<div class="alignleft filters">
				<input type="text" class="date-picker" autocomplete="off" name="from-date" placeholder="From - yyyy-MM-dd" id="from-date" value="<?php echo $from_date; ?>" />
				<input type="text" class="date-picker" autocomplete="off" name="to-date" placeholder="To - yyyy-MM-dd" id="to-date" value="<?php echo $to_date; ?>" />

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
							$temp_post_id = icl_object_id ( $temp_post_id, get_post_type( $temp_post_id ), true, ICL_LANGUAGE_CODE );
						}

						?>
						<option value="<?php echo $row['post_id']; ?>" <?php echo $selected; ?>>
							<?php echo get_the_title( $temp_post_id ); ?>
						</option>
					<?php } ?>
				</select>

				<input type="submit" class="button" value="<?php _e( 'Filter', 'multi-rating' ); ?>"/>
			</div>
			<?php
		}

		if ( $which == "bottom" ) {
			echo '';
		}

	}

	/**
	 * (non-PHPdoc)
	 * @see WP_List_Table::get_columns()
	 */
	function get_columns() {

		return array(
				MR_Rating_Entry_Table::CHECKBOX_COLUMN => '<input type="checkbox" />',
				MR_Rating_Entry_Table::RATING_ITEM_ENTRY_ID_COLUMN =>__( 'ID', 'multi-rating' ),
				MR_Rating_Entry_Table::POST_ID_COLUMN => __( 'Post', 'multi-rating' ),
				MR_Rating_Entry_Table::ENTRY_DATE_COLUMN =>__( 'Date', 'multi-rating' ),
				MR_Rating_Entry_Table::USER_ID_COLUMN => __( 'User ID', 'multi-rating' ),
				MR_Rating_Entry_Table::RATING_RESULT_COLUMN => __( 'Rating Details', 'multi-rating' )
		);
	}

	/**
	 * (non-PHPdoc)
	 * @see WP_List_Table::prepare_items()
	 */
	function prepare_items() {

		global $wpdb;

		// Process any bulk actions first
		$this->process_bulk_action();

		// Register the columns
		$columns = $this->get_columns();
		$hidden = array( MR_Rating_Entry_Table::USER_ID_COLUMN );
		$sortable = $this->get_sortable_columns();
		$this->_column_headers = array( $columns, $hidden, $sortable );

		$from_date = isset( $_REQUEST['from-date'] ) ? $_REQUEST['from-date'] : null;
		$to_date = isset( $_REQUEST['to-date'] ) ? $_REQUEST['to-date'] : null;
		$post_id = isset( $_REQUEST['post-id'] ) ? $_REQUEST['post-id'] : null;

		if ( $from_date != null && strlen( $from_date ) > 0 ) {
			list( $year, $month, $day ) = explode( '-', $from_date ); // default yyyy-mm-dd format
			if ( ! checkdate( $month , $day , $year )) {
				$from_date = null;
			}
		}

		if ( $to_date != null && strlen( $to_date ) > 0 ) {
			list( $year, $month, $day ) = explode( '-', $to_date );// default yyyy-mm-dd format
			if ( ! checkdate( $month , $day , $year ) ) {
				$to_date = null;
			}
		}

		// get table data
		$query = 'SELECT * FROM ' . $wpdb->prefix . Multi_Rating::RATING_ITEM_ENTRY_TBL_NAME . ' as rie';
		$query_args = array();
		$added_to_query = false;
		if ( $post_id || $from_date || $to_date ) {
			$query .= ' WHERE';
		}

		if ( $post_id ) {
			if ( $added_to_query ) {
				$query .= ' AND';
			}

			$query .= ' rie.post_id = %d';
			array_push( $query_args, $post_id );
			$added_to_query = true;
		}

		if ( $from_date ) {
			if ( $added_to_query ) {
				$query .= ' AND';
			}

			$query .= ' rie.entry_date >= %s';
			array_push( $query_args, $from_date );
			$added_to_query = true;
		}

		if ( $to_date ) {
			if ( $added_to_query ) {
				$query .= ' AND';
			}

			$query .= ' rie.entry_date <= %s';
			array_push( $query_args, $to_date );
			$added_to_query = true;
		}

		$query .= ' ORDER BY rie.entry_date DESC';

		if ( count( $query_args ) > 0 ) {
			$query = $wpdb->prepare( $query, $query_args );
		}

		// pagination
		$item_count = $wpdb->query( $query ); //return the total number of affected rows
		$items_per_page = 10;
		$page_num = ! empty( $_GET['paged'] ) ? $_GET['paged'] : '';
		if ( empty( $page_num ) || !is_numeric( $page_num ) || $page_num <= 0 ) {
			$page_num = 1;
		}
		$total_pages = ceil( $item_count / $items_per_page );
		// adjust the query to take pagination into account
		if ( !empty( $page_num ) && !empty( $items_per_page ) ) {
			$offset = ( $page_num -1 ) * $items_per_page;
			$query .= ' LIMIT ' .(int) $offset. ',' . (int) $items_per_page;
		}
		$this->set_pagination_args( array( "total_items" => $item_count, "total_pages" => $total_pages, "per_page" => $items_per_page ) );

		$this->items = $wpdb->get_results( $query, ARRAY_A );

	}

	/**
	 * Column default
	 *
	 * @param $item
	 * @param $column_name
	 * @return
	 */
	function column_default( $item, $column_name ) {

		switch( $column_name ) {

			case MR_Rating_Entry_Table::ENTRY_DATE_COLUMN :
				echo date( 'F j, Y, g:i A', strtotime( $item[ $column_name ] ) );
				break;

			case MR_Rating_Entry_Table::CHECKBOX_COLUMN :
				return $item[ $column_name ];
				break;

			case MR_Rating_Results_Table::POST_ID_COLUMN : {
				$post_id = $item[ MR_Rating_Entry_Table::POST_ID_COLUMN];
				$temp_post_id = $post_id;

				// WPML get adjusted post id for active language, just for the string translation
				if ( function_exists( 'icl_object_id' ) ) {
					$temp_post_id = icl_object_id ( $post_id , get_post_type( $post_id ), true, ICL_LANGUAGE_CODE );
				}

				?>
				<a href="<?php echo get_the_permalink( $temp_post_id ); ?>"><?php echo get_the_title( $temp_post_id ); ?></a>
				<div class="row-actions">
					<span class="id"><?php printf( __( 'ID: %d', 'multi-rating' ), $temp_post_id ) ; ?> | </span>
					<?php
					if ( current_user_can( 'edit_post', $temp_post_id ) ) {
						?>
						<span class="edit"><a href="<?php echo esc_url( get_edit_post_link( $temp_post_id ) ); ?>"><?php _e( 'Edit', 'multi-rating'); ?></a></span>
						<?php
					}
					?>
				</div>
				<?php
				break;
			}

			case MR_Rating_Entry_Table::RATING_ITEM_ENTRY_ID_COLUMN :
			case MR_Rating_Entry_Table::USER_ID_COLUMN :
				echo $item[ $column_name ];
				break;

			case MR_Rating_Entry_Table::RATING_RESULT_COLUMN :
				$rating_result = Multi_Rating_API::calculate_rating_item_entry_result( $item[ MR_Rating_Entry_Table::RATING_ITEM_ENTRY_ID_COLUMN ], null );


				echo '<span class="mrp-rating-details">';

				$icon_classes = MR_Utils::get_icon_classes( 'dashicons' ); // dashicons for admin

				ob_start();
				mr_get_template_part( 'rating-result', 'star-rating', true, array(
						'icon_classes' => $icon_classes,
						'max_stars' => 5,
						'star_result' => $rating_result['adjusted_star_result'],
						'generate_microdata' => false,
				) );
				$overall_rating = ob_get_contents();
				ob_end_clean();

				printf( __( 'Overall Rating: %s', 'multi-rating'), $overall_rating );
				echo '</span>';

				// do not need to pass post id and rating form id
				$edit_rating_url = '?page=mr_edit_rating&entry-id=' . $item[ MR_Rating_Entry_Table::RATING_ITEM_ENTRY_ID_COLUMN ];
				if ( isset( $_REQUEST['post-id'] ) ) {
					$edit_rating_url .= '&post-id=' . $_REQUEST['post-id'];
				}
				if ( isset( $_REQUEST['to-date'] ) )  {
					$edit_rating_url .= '&to-date=' . $_REQUEST['to-date'];
				}
				if ( isset( $_REQUEST['from-date'] ) )  {
					$edit_rating_url .= '&from-date=' . $_REQUEST['from-date'];
				}
				if ( isset( $_REQUEST['paged'] ) )  {
					$edit_rating_url .= '&paged=' . $_REQUEST['paged'];
				}

				?>
				<div class="row-actions">
					<?php
					if ( current_user_can( 'manage_options' ) ) {
						?>
						<span class="edit">
							<a href="<?php echo $edit_rating_url; ?>"><?php _e( 'Edit Rating', 'multi-rating' ); ?></a>
						</span>
						<?php
					}
					?>
				</div>
				<?php

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
	function column_cb( $item ) {

		return sprintf( '<input type="checkbox" name="' . MR_Rating_Entry_Table::DELETE_CHECKBOX . '" value="%s" />', $item[ MR_Rating_Entry_Table::RATING_ITEM_ENTRY_ID_COLUMN ] );

	}

	/**
	 * (non-PHPdoc)
	 * @see WP_List_Table::get_bulk_actions()
	 */
	function get_bulk_actions() {

		$bulk_actions = array();

		if ( current_user_can( 'manage_options' ) ) {
			$bulk_actions = array_merge( array( 'delete' => __( 'Delete', 'multi-rating' ) ), $bulk_actions);
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

		if ( $this->current_action() === 'delete') {

			global $wpdb;

			$checked = ( is_array( $_REQUEST[ 'delete' ] ) ) ? $_REQUEST[ 'delete' ] : array( $_REQUEST[ 'delete' ] );

			foreach( $checked as $rating_entry_id ) {

				$query = 'SELECT post_id FROM ' . $wpdb->prefix . Multi_Rating::RATING_ITEM_ENTRY_TBL_NAME . ' WHERE '
						. MR_Rating_Entry_Table::RATING_ITEM_ENTRY_ID_COLUMN . ' = %d';
				$row = $wpdb->get_row( $wpdb->prepare( $query, $rating_entry_id ) );

				// rating results cache will be refreshed next time it's needed
				delete_post_meta( $row->post_id, Multi_Rating::RATING_RESULTS_POST_META_KEY );
				delete_post_meta( $row->post_id, Multi_Rating::RATING_RESULTS_POST_META_KEY . '_star_rating' );
				delete_post_meta( $row->post_id, Multi_Rating::RATING_RESULTS_POST_META_KEY . '_count_entries' );

				$wpdb->delete( $wpdb->prefix . Multi_Rating::RATING_ITEM_ENTRY_TBL_NAME, array( 'rating_item_entry_id' => $rating_entry_id), array( '%d' ) );
				$wpdb->delete( $wpdb->prefix . Multi_Rating::RATING_ITEM_ENTRY_VALUE_TBL_NAME, array( 'rating_item_entry_id' => $rating_entry_id), array( '%d' ) );
			}

			echo '<div class="updated"><p>' . __( 'Entries deleted successfully', 'multi-rating' ) . '</p></div>';
		}
	}

}
