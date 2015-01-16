<?php 

/**
 * Shows the reports screen
 */
function mr_reports_screen() {
	?>
	<div class="wrap">
		<h2 class="nav-tab-wrapper">
			<?php
			$current_tab = isset( $_GET['tab'] ) ? $_GET['tab'] : Multi_Rating::ENTRIES_PER_DAY_REPORT_TAB;
			$page = Multi_Rating::REPORTS_PAGE_SLUG;
			$tabs = array (
					Multi_Rating::ENTRIES_PER_DAY_REPORT_TAB 		=> __( 'Entries', 'multi-rating' )
			);
			
			foreach ( $tabs as $tab_key => $tab_caption ) {
				$active = $current_tab == $tab_key ? 'nav-tab-active' : '';
				
				echo '<a class="nav-tab ' . $active . '" href="?page=' . $page . '&tab=' . $tab_key . '">' . $tab_caption . '</a>';
			} ?>
		</h2>
		
		<?php 
		if ( $current_tab == Multi_Rating::ENTRIES_PER_DAY_REPORT_TAB ) {?>	
		
			<p><?php _e( 'Number of entries per day', 'multi-rating' ); ?></p>
			
			<form method="post" id="entries-report-form">
			
				<?php 
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
				?>
				<p>
					<input type="text" class="date-picker" autocomplete="off" name="from-date" placeholder="From - dd/MM/yyyy" id="from-date" value="<?php echo $from_date; ?>" />
					<input type="text" class="date-picker" autocomplete="off" name="to-date" placeholder="To - dd/MM/yyyy" id="to-date" value="<?php echo $to_date; ?>" />
								
					<select name="post-id" id="post-id">
						<option value=""><?php _e( 'All posts / pages', 'multi-rating' ); ?></option>
						<?php	
						global $wpdb;
						$query = 'SELECT DISTINCT post_id FROM ' . $wpdb->prefix . Multi_Rating::RATING_ITEM_ENTRY_TBL_NAME;
						
						$rows = $wpdb->get_results( $query, ARRAY_A );
						foreach ( $rows as $row ) {
							$post = get_post( $row['post_id'] );
							
							$selected = '';
							if ( intval( $row['post_id'] ) == intval( $post_id ) ) {
								$selected = ' selected="selected"';
							}
							?>
							<option value="<?php echo $post->ID; ?>" <?php echo $selected; ?>>
								<?php echo get_the_title( $post->ID ); ?>
							</option>
						<?php } ?>
					</select>
					
					
					<input type="submit" class="button" value="<?php _e( 'Filter', 'multi-rating' ); ?>"/>
				</p>
			</form>
			
			<?php 
			
			global $wpdb;
				
			$from_date = isset( $_REQUEST['from-date'] ) ? $_REQUEST['from-date'] : null;
			$to_date = isset( $_REQUEST['to-date'] ) ? $_REQUEST['to-date'] : null;
			$post_id = isset( $_REQUEST['post-id'] ) ? $_REQUEST['post-id'] : null;
				
			if ( $from_date != null && strlen( $from_date ) > 0 ) {
				list( $year, $month, $day ) = explode( '/', $from_date ); // default yyyy/mm/dd format
				if ( ! checkdate( $month , $day , $year )) {
					$from_date = null;
				}
			}
				
			if ( $to_date != null && strlen($to_date) > 0 ) {
				list( $year, $month, $day ) = explode( '/', $to_date );// default yyyy/mm/dd format
				if ( ! checkdate( $month , $day , $year )) {
					$to_date = null;
				}
			}
				
			$query = 'SELECT DISTINCT DATE(entry_date) AS day, count(*) as count FROM ' . $wpdb->prefix . Multi_Rating::RATING_ITEM_ENTRY_TBL_NAME . ' as rie';
			
			$added_to_query = false;
			if ( $post_id || $from_date || $to_date ) {
				$query .= ' WHERE';
			}
				
			if ( $post_id ) {
				if ($added_to_query) {
					$query .= ' AND';
				}
					
				$query .= ' rie.post_id = "' . $post_id . '"';
				$added_to_query = true;
			}
				
			if ( $from_date ) {
				if ($added_to_query) {
					$query .= ' AND';
				}
					
				$query .= ' rie.entry_date >= "' . $from_date . '"';
				$added_to_query = true;
			}
				
			if ( $to_date ) {
				if ($added_to_query) {
					$query .= ' AND';
				}
					
				$query .= ' rie.entry_date <= "' . $to_date . '"';
				$added_to_query = true;
			}
				
			$query .= ' GROUP BY day ORDER BY rie.entry_date DESC';
			
			$rows = $wpdb->get_results($query);
			
			$time_data = array();
			foreach ( $rows as $row ) {
				$day = $row->day;
				$count = $row->count;
				// TODO if a day has no data, then make it 0 visitors.
				// Otherwise, it is not plotted on the graph as 0.
		
				array_push( $time_data, array( ( strtotime( $day ) * 1000 ), intval( $count ) ) );
			}
			?>
			
			<div class="flot-container">
				<div class="report-wrapper" style="height: 300px;">
					<div id="entry-count-placeholder" class="report-placeholder"></div>
				</div>
			</div>
			
			<div class="flot-container">
				<div class="report-wrapper" style="height: 100px;">
					<div id="entry-count-overview-placeholder" class="report-placeholder"></div>
				</div>
			</div>
									
			<script type="text/javascript">
				// Time graph
				jQuery(document).ready(function() {
					// add markers for weekends on grid
					function weekendAreas(axes) {
						var markings = [];
						var d = new Date(axes.xaxis.min);
						// go to the first Saturday
						d.setUTCDate(d.getUTCDate() - ((d.getUTCDay() + 1) % 7))
						d.setUTCSeconds(0);
						d.setUTCMinutes(0);
						d.setUTCHours(0);
						var i = d.getTime();
						// when we don't set yaxis, the rectangle automatically
						// extends to infinity upwards and downwards
						do {
							markings.push({ xaxis: { from: i, to: i + 2 * 24 * 60 * 60 * 1000 } });
							i += 7 * 24 * 60 * 60 * 1000;
						} while (i < axes.xaxis.max);
						return markings;
					}

					var options = {
						xaxis: {
							mode: "time",
							tickLength: 5
						},
						selection: {
							mode: "x"
						},
						grid: {
							markings: weekendAreas,
							hoverable : true,
							show: true,
							aboveData: false,
							color: '#BBB',
							backgroundColor: '#f9f9f9',
							borderColor: '#ccc',
							borderWidth: 2,
						},
						series : {
							lines: {
								show: true,
								lineWidth: 1
							},
							points: { show: true }
						}
					};
					
					var plot = jQuery.plot("#entry-count-placeholder", [<?php echo json_encode( $time_data ); ?>], options);
					
					var overview = jQuery.plot("#entry-count-overview-placeholder", [<?php echo json_encode( $time_data ); ?>], {
						series: {
							lines: {
								show: true,
								lineWidth: 1
							},
							shadowSize: 0
						},
						xaxis: {
							ticks: [],
							mode: "time"
						},
						yaxis: {
							ticks: [],
							min: 0,
							autoscaleMargin: 0.1
						},
						selection: {
							mode: "x"
						},
						grid: {
							markings: weekendAreas,
							hoverable : true,
							show: true,
							aboveData: false,
							color: '#BBB',
							backgroundColor: '#f9f9f9',
							borderColor: '#ccc',
							borderWidth: 2,
							
						},
					});
					function flot_tooltip(x, y, contents) {
						jQuery('<div id="flot-tooltip">' + contents + '</div>').css( {
							position: 'absolute',
							display: 'none',
							top: y + 5,
							left: x + 5,
							border: '1px solid #fdd',
							padding: '2px',
							'background-color': '#fee',
							opacity: 0.80
						}).appendTo("body").fadeIn(200);
					}
						
					jQuery("#entry-count-placeholder").bind("plotselected", function (event, ranges) {
						// do the zooming
								
						plot = jQuery.plot("#entry-count-placeholder", [<?php echo json_encode( $time_data ); ?>], jQuery.extend(true, {}, options, {
							xaxis: {
								min: ranges.xaxis.from,
								max: ranges.xaxis.to
							}
						}));
								
						// don't fire event on the overview to prevent eternal loop
						overview.setSelection(ranges, true);
					});
											
					jQuery("#entry-count-overview-placeholder").bind("plotselected", function (event, ranges) {
						plot.setSelection(ranges);
					});
					jQuery("#entry-count-placeholder").bind("plothover", function (event, pos, item) {
						if (item) {
					   		jQuery("#flot-tooltip").remove();
							var x = item.datapoint[0].toFixed(2),
							y = item.datapoint[1].toFixed(2);
							flot_tooltip( item.pageX - 30, item.pageY - 20, item.datapoint[1] );
					    } else {
					    	jQuery("#flot-tooltip").remove();
					    }
					});
				});
			</script>	
		<?php } ?>
	</div>
	
	<?php
}
?>