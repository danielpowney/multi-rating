<?php

/**
 * Shows the rating entries screen
 *
 * @since 4.2
 */
function mr_rating_entries_screen() {
	?>
	<div class="wrap">
		<h2><?php _e( 'Rating Entries', 'multi-rating' ); ?></h2>
		<form method="get" id="rating-entries-table-form" action="<?php echo admin_url( 'admin.php?page=' . Multi_Rating::RATING_ENTRIES_PAGE_SLUG ); ?>">
			<?php
			$rating_entry_table = new MR_Rating_Entry_Table();
			$rating_entry_table->prepare_items();
			$rating_entry_table->views();
			$rating_entry_table->display();
			?>
			<input type="hidden" name="page" value="<?php echo Multi_Rating::RATING_ENTRIES_PAGE_SLUG; ?>" />
		</form>
	</div>
	<?php
}
