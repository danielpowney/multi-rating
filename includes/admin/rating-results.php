<?php 

/**
 * Shows the rating results screen
 */
function mr_rating_results_screen() {
	?>
	<div class="wrap">
		<h2 class="nav-tab-wrapper">
			<?php
			$current_tab = isset( $_GET['tab'] ) ? $_GET['tab'] : Multi_Rating::RATING_RESULTS_TAB;
			$page = Multi_Rating::RATING_RESULTS_PAGE_SLUG;
			$tabs = array (
					Multi_Rating::RATING_RESULTS_TAB 		=> __( 'Rating Results', 'multi-rating' ),
					Multi_Rating::ENTRIES_TAB 		=> __( 'Entries', 'multi-rating' )
			);
			
			foreach ( $tabs as $tab_key => $tab_caption ) {
				$active = $current_tab == $tab_key ? 'nav-tab-active' : '';
				echo '<a class="nav-tab ' . $active . '" href="?page=' . $page . '&tab=' . $tab_key . '">' . $tab_caption . '</a>';
			} 
			?>
		</h2>
		<?php 
		
		if ( $current_tab == Multi_Rating::RATING_RESULTS_TAB ) {
			?>
			<form method="post" id="rating-results-table-form">
				<?php 
				$rating_results_table = new MR_Rating_Results_Table();
				$rating_results_table->prepare_items();
				$rating_results_table->display();
				?>
			</form>
			<?php 
		} else if ( $current_tab == Multi_Rating::ENTRIES_TAB ) {
			?>
			<form method="post" id="rating-entry-table-form">
				<?php 
				$rating_entry_table = new MR_Rating_Entry_Table();
				$rating_entry_table->prepare_items();
				$rating_entry_table->display();
				?>
			</form>
			<?php 
		}
		?>
		
	</div>
	<?php 
}
?>