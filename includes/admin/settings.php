<?php 

/**
 * Shows the settings screen
 */
function mr_settings_screen() {
	?>
	<div class="wrap">
	
		<h2><?php _e( 'Settings', 'multi-rating' ); ?></h2>
		
		<?php 
		
		settings_errors();
		
		if ( isset( $_GET['updated'] ) && isset( $_GET['page'] ) ) {
			add_settings_error('general', 'settings_updated', __( 'Settings saved.', 'multi-rating' ), 'updated');
		}
		
		?>
			<form method="post" name="<?php echo Multi_Rating::GENERAL_SETTINGS; ?>" action="options.php">
			<?php
			wp_nonce_field( 'update-options' );
			settings_fields( Multi_Rating::GENERAL_SETTINGS );
			do_settings_sections( Multi_Rating::GENERAL_SETTINGS );
			submit_button(null, 'primary', 'submit', true, null);
			?>
		</form>
		
		<form method="post" name="<?php echo Multi_Rating::POSITION_SETTINGS; ?>" action="options.php">
			<?php
			wp_nonce_field( 'update-options' );
			settings_fields( Multi_Rating::POSITION_SETTINGS );
			do_settings_sections( Multi_Rating::POSITION_SETTINGS );
			submit_button(null, 'primary', 'submit', true, null);
			?>
		</form>
		
		<form method="post" name="<?php echo Multi_Rating::CUSTOM_TEXT_SETTINGS; ?>" action="options.php">
			<?php
			wp_nonce_field( 'update-options' );
			settings_fields( Multi_Rating::CUSTOM_TEXT_SETTINGS );
			do_settings_sections( Multi_Rating::CUSTOM_TEXT_SETTINGS );
			submit_button(null, 'primary', 'submit', true, null);
			?>
		</form>
		
		<form method="post" name="<?php echo Multi_Rating::STYLE_SETTINGS; ?>" action="options.php">
			<?php
			wp_nonce_field( 'update-options' );
			settings_fields( Multi_Rating::STYLE_SETTINGS );
			do_settings_sections( Multi_Rating::STYLE_SETTINGS );
			submit_button(null, 'primary', 'submit', true, null);
			?>
		</form>		
	</div>
	<?php 
}

?>