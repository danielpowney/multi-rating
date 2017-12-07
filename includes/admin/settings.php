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

		<div id="mr-settings-main">

			<div id="mr-settings-content">

				<form method="post" name="<?php echo Multi_Rating::GENERAL_SETTINGS; ?>" action="options.php">
					<?php
					wp_nonce_field( 'update-options' );
					settings_fields( Multi_Rating::GENERAL_SETTINGS );
					do_settings_sections( Multi_Rating::SETTINGS_PAGE_SLUG . '&setting=' . Multi_Rating::GENERAL_SETTINGS );
					submit_button(null, 'primary', 'submit', true, null);
					?>
				</form>

				<form method="post" name="<?php echo Multi_Rating::POSITION_SETTINGS; ?>" action="options.php">
					<?php
					wp_nonce_field( 'update-options' );
					settings_fields( Multi_Rating::POSITION_SETTINGS );
					do_settings_sections( Multi_Rating::SETTINGS_PAGE_SLUG . '&setting=' . Multi_Rating::POSITION_SETTINGS );
					submit_button(null, 'primary', 'submit', true, null);
					?>
				</form>

				<form method="post" name="<?php echo Multi_Rating::CUSTOM_TEXT_SETTINGS; ?>" action="options.php">
					<?php
					wp_nonce_field( 'update-options' );
					settings_fields( Multi_Rating::CUSTOM_TEXT_SETTINGS );
					do_settings_sections( Multi_Rating::SETTINGS_PAGE_SLUG . '&setting=' . Multi_Rating::CUSTOM_TEXT_SETTINGS );
					submit_button(null, 'primary', 'submit', true, null);
					?>
				</form>

				<form method="post" name="<?php echo Multi_Rating::STYLE_SETTINGS; ?>" action="options.php">
					<?php
					wp_nonce_field( 'update-options' );
					settings_fields( Multi_Rating::STYLE_SETTINGS );
					do_settings_sections( Multi_Rating::SETTINGS_PAGE_SLUG . '&setting=' . Multi_Rating::STYLE_SETTINGS );
					submit_button(null, 'primary', 'submit', true, null);
					?>
				</form>

				<form method="post" name="<?php echo Multi_Rating::CUSTOM_IMAGES_SETTINGS; ?>" action="options.php">
					<?php
					wp_nonce_field( 'update-options' );
					settings_fields( Multi_Rating::CUSTOM_IMAGES_SETTINGS );
					do_settings_sections( Multi_Rating::SETTINGS_PAGE_SLUG . '&setting=' . Multi_Rating::CUSTOM_IMAGES_SETTINGS );
					submit_button(null, 'primary', 'submit', true, null);
					?>
				</form>
			</div>

			<div id="mr-settings-sidebar">

				<a href="https://multiratingpro.com/?utm_source=settings-sidebar&utm_medium=free-plugin&utm_campaign=wp-admin">
					<div id="mr-upgrade-logo">
						<img src="<?php echo plugins_url( '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . 'img' . DIRECTORY_SEPARATOR . 'pro_hori_coloured_white(border).svg' , __FILE__ ); ?>" />
					</div>
					<div id="mr-upgrade-content">
						<ul>
							<li><span class="dashicons dashicons-yes"></span> <?php _e( 'Unlimited rating forms', 'multi-rating' ); ?></li>
							<li><span class="dashicons dashicons-yes"></span> <?php _e( 'Common review fields (title, name, e-mail and comments)', 'multi-rating' ); ?></li>
							<li><span class="dashicons dashicons-yes"></span> <?php _e( 'Custom input or textarea fields', 'multi-rating' ); ?></li>
							<li><span class="dashicons dashicons-yes"></span> <?php _e( 'WordPress comment ratings integration', 'multi-rating' ); ?></li>
							<li><span class="dashicons dashicons-yes"></span> <?php _e( 'Moderate rating entries', 'multi-rating' ); ?></li>
							<li><span class="dashicons dashicons-yes"></span> <?php _e( 'E-mail notifications', 'multi-rating' ); ?></li>
							<li><span class="dashicons dashicons-yes"></span> <?php _e( 'Display rating item results in a poll layout', 'multi-rating' ); ?></li>
							<li><span class="dashicons dashicons-yes"></span> <?php _e( 'Display rating entries in a review layout', 'multi-rating' ); ?></li>
							<li><?php _e( 'And much much more!', 'multi-rating' ); ?></li>
						</ul>
					</div>
				</a>
			</div>

		</div>
	</div>
	<?php
}

?>
