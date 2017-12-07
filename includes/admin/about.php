<?php
/**
 * Shows the about screen
 */
function mr_about_screen() {

	// if version is less than 3.8 then manually add the necessary css missing from about.css
	if ( ! version_compare( get_bloginfo( 'version' ), '3.8', '>=' ) ) {
		?>
		<style type="text/css">
			.about-wrap .changelog .feature-section { overflow: hidden; }
			.about-wrap .feature-section { margin-top: 20px; }
			.about-wrap .feature-section.two-col > div { position: relative; width: 47.5%; margin-right: 4.999999999%; float: left; }
			.about-wrap .feature-section.col .last-feature { margin-right: 0; }
			.about-wrap hr { border: 0; border-top: 1px solid #DFDFDF; }
			.about-wrap { position: relative; margin: 25px 40px 0 20px; max-width: 1050px; font-size: 15px; }
			.about-wrap img { margin: 0; max-width: 100%; vertical-align: middle; }
			.about-wrap .changelog h2.about-headline-callout { margin: 1.1em 0 0.2em; font-size: 2.4em; font-weight: 300; line-height: 1.3; text-align: center; }
			.about-wrap .feature-section img { margin-bottom: 20px !important; }
			.about-wrap h3 { margin: 1em 0 .6em; font-size: 1.5em; line-height: 1.5em; }
			.about-wrap .feature-section.three-col div { width: 29.75%; }
			.about-wrap .feature-section.two-col > div { margin-right: 4.8%; }
		</style>
		<?php
	} else {
		?>
		<style type="text/css">
			.about-wrap .wp-people-group { margin-top: 10px !important; }
		</style>
		<?php
	} ?>

	<div class="wrap about-wrap mr-about">

		<div id="mr-header">
			<img class="mr-badge" src="<?php echo plugins_url( '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . 'img' . DIRECTORY_SEPARATOR . 'vert_coloured white(border).svg' , __FILE__ ); ?>" alt="<?php _e( 'Multi Rating', 'multi-rating-pro' ); ?>" / >
			<h1><?php printf( __( 'Multi Rating v%s', 'multi-rating' ), Multi_Rating::VERSION ); ?></h1>
			<p class="about-text">
				<?php _e( 'A powerful rating system and review plugin for WordPress; with a niche of doing multi-ratings better than anyone else.', 'multi-rating' ); ?>
			</p>
		</div>

		<h2 class="nav-tab-wrapper">
			<?php
			$current_tab = isset( $_GET['tab'] ) ? $_GET['tab'] : 'about';
			$page = Multi_Rating::ABOUT_PAGE_SLUG;
			$tabs = array (
					'about'	 			=> __( 'Getting Started', 'multi-rating' ),
					'changelog'			=> __( 'Changelog', 'multi-rating' ),
					'credits'			=> __( 'Credits', 'multi-rating' ),
					'upgrade-to-pro'				=> __( 'Upgrade to Pro <span class="dashicons dashicons-star-filled"></span>', 'multi-rating' )
			);

			foreach ( $tabs as $tab_key => $tab_caption ) {
				$active = $current_tab == $tab_key ? 'nav-tab-active' : '';
				$href = '?page=' . $page . '&tab=' . $tab_key;
				$target = '';
				if ( $tab_key == 'upgrade-to-pro' ) {
					$href = 'https://multiratingpro.com?utm_source=about&utm_medium=free-plugin&utm_campaign=wp-admin';
					$target = '__blank';
				}

				echo '<a class="nav-tab ' . $active . ' tab-' . $tab_key . '" href="'. $href . '" target="' . $target . '">' . $tab_caption . '</a>';
			}
			?>
		</h2>

		<?php
		if ( $current_tab == 'about' ) {
			mr_about_tab_content();
		} else if ( $current_tab == 'changelog' ) {
			echo mr_parse_readme();
		} else if ( $current_tab == 'credits' ) {
			echo mr_contributors();
		}
		?>

	</div>
	<?php
}


/**
 * Displays the about tab content
 */
function mr_about_tab_content() {
	?>
	<div class="changelog">


		<div class="feature-section col three-col">
			<div class="col">
				<img src="<?php echo plugins_url( '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . 'img' . DIRECTORY_SEPARATOR . 'rating-items.png' , __FILE__ ); ?>" />
				<h4><?php _e( 'Add Rating Items', 'multi-rating' ); ?></h4>
				<p><?php printf( __( 'Setup your criteria and questions by <a href="admin.php?page=%s">adding new rating items</a>.', 'multi-rating' ), Multi_Rating::RATING_ITEMS_PAGE_SLUG . '&rating-item-id=' ); ?></p>
			</div>
			<div class="col">
				<img src="<?php echo plugins_url( '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . 'img' . DIRECTORY_SEPARATOR . 'auto-placement.png' , __FILE__ ); ?>" />
				<h4><?php _e( 'Auto Placement Settings' ); ?></h4>
				<p><?php printf( __( 'Configure <a href="admin.php?page=%s">automatic placement</a> settings to display the rating form and or rating results on posts or pages (e.g. after the post content).', 'multi-rating' ), Multi_Rating::SETTINGS_PAGE_SLUG ); ?></p>
			</div>
			<div class="col last-feature">
				<img src="<?php echo plugins_url( '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . 'img' . DIRECTORY_SEPARATOR . 'shortcodes.png' , __FILE__ ); ?>" />
				<h4><?php _e( 'Add Shortcodes', 'multi-rating' ); ?></h4>
				<p>
					<?php printf( __( '<a href="%s">[mr_rating_form]</a> - displays the rating form.', 'multi-rating' ), 'http://multiratingpro.com/demo-page-free#mr_rating_form?utm_source=about&utm_medium=free-plugin&utm_campaign=wp-admin&utm_content=documentation"' ); ?>
					<br />

					<?php printf( __( '<a href="%s">[mr_rating_result]</a> - displays the rating result.', 'multi-rating' ), 'http://multiratingpro.com/demo-page-free#mr_rating_result?utm_source=about&utm_medium=free-plugin&utm_campaign=wp-admin&utm_content=documentation"' ); ?>
					<br />

					<?php printf( __( '<a href="%s">[mr_rating_results_list]</a> - displays a list of rating results.', 'multi-rating' ), 'http://multiratingpro.com/demo-page-free#mr_rating_results_list?utm_source=about&utm_medium=free-plugin&utm_campaign=wp-admin&utm_content=documentation"' ); ?></p>
			</div>
		</div>

		<br />

		<hr />

		<br />

		<div class="under-the-hood col three-col">

			<div class="col">
				<h4><?php _e( 'SERP Rich Snippets', 'multi-rating' ); ?></h4>
				<p><?php _e( 'schema.org microdata is added for ratings displayed via the automatic placement setttings.', 'multi-rating' ); ?></p>
				<p><?php _e( 'For shortcodes you need to add attribute generate_microdata e.g. [mr_rating_result generate_microdata=true].', 'multi-rating' ); ?></p>

				<h4><?php _e( 'WordPress Star Ratings', 'multi-rating' ); ?></h4>
				<p><?php _e( 'You can change the icon font libaries used for star ratings (e.g. Font Awesome and Dashicons). Or you can upload custom star rating images to use instead of icon fonts.', 'multi-rating' ); ?></p>

				<h4><?php _e( 'Export Rating to a CSV File', 'multi-rating' ); ?></h4>
				<p><?php _e( 'There\'s a tool which allows you to export ratings to a CSV file.', 'multi-rating' ); ?></p>

				<h4><?php _e( 'Edit Rating Entries', 'multi-rating' ); ?></h4>
				<p><?php _e( 'You can edit selected rating item values for rating entries.', 'multi-rating' ); ?></p>

			</div>

			<div class="col">
				<h4><?php _e( 'Custom Post Type Support', 'multi-rating' ); ?></h4>
				<p><?php _e( 'Remember to enable custom post types under the auto placement settings.', 'multi-rating' ); ?></p>

				<h4><?php _e( 'Taxonomy Support', 'multi-rating' ); ?></h4>
				<p><?php _e( 'You can filter ratings by taxonomies and terms e.g. [mr_rating_result_list taxonomy="post_tag" term_id="1"].', 'multi-rating' ); ?></p>

				<h4><?php _e( 'i18n Translation Ready', 'multi-rating' ); ?></h4>
				<p><?php _e( 'The plugin is translation ready (.pot file in the languages directory).', 'multi-rating' ); ?></p>

				<h4><?php _e( 'In-built Template System', 'multi-rating' ); ?></h4>
				<p><?php _e( 'Customize the HTML presentation using the in-built template system.', 'multi-rating' ); ?></p>

				<h4><?php _e( 'Rating Item Types', 'multi-rating' ); ?></h4>
				<p><?php _e( 'You can choose from select dropdown lists, star ratings and radio buttons.', 'multi-rating' ); ?></p>
			</div>

			<div class="col last-feature">

				<h4><?php _e( 'Rating Result Types', 'multi-rating' ); ?></h4>
				<p><?php _e( 'You can specify star ratings, a score (e.g. out of 100) or percentage result types e.g. [mr_rating_result result_type="percentage"].', 'multi-rating' ); ?></p>

				<h4><?php _e( 'Weighted Rating Items', 'multi-rating' ); ?></h4>
				<p><?php _e( 'You can assign different weights to rating items which are factored into rating calculations.', 'multi-rating' ); ?></p>

				<h4><?php _e( 'Meta-box', 'multi-rating' ); ?></h4>
				<p><?php _e( 'There\'s a meta-box on the edit post page so that you can override the auto placement settings.', 'multi-rating' ); ?></p>

				<h4><?php _e( 'Developer Friendly', 'multi-rating' ); ?></h4>
				<p><?php _e( 'Excellent code quality, static API class and plenty of extensible WordPress action hooks & filters.', 'multi-rating' ); ?></p>
				<h4><?php _e( 'Duplicate Checks', 'multi-rating' ); ?></h4>
				<p><?php _e( 'Perform duplicate check using IP address and or cookies.', 'multi-rating' ); ?></p>
			</div>
		</div>
	</div>
	<?php
}


/**
 * Parse the Multi Rating plugin readme.txt file
 *
 * @return string $readme HTML formatted readme file
 */
function mr_parse_readme() {
	$plugin_path = dirname( __FILE__ ) . '/../../';
	$file = file_exists( $plugin_path . 'readme.txt' ) ? $plugin_path . 'readme.txt' : null;

	if ( ! $file ) {
		$readme = '<p>' . __( 'No valid changelog was found.', 'my-chatbot' ) . '</p>';
	} else {
		$readme = file_get_contents( $file );
		$readme = nl2br( esc_html( $readme ) );
		$readme = explode( '== Changelog ==', $readme );
		$readme = end( $readme );

		$readme = preg_replace( '/`(.*?)`/', '<code>\\1</code>', $readme );
		$readme = preg_replace( '/[\040]\*\*(.*?)\*\*/', ' <strong>\\1</strong>', $readme );
		$readme = preg_replace( '/[\040]\*(.*?)\*/', ' <em>\\1</em>', $readme );
		$readme = preg_replace( '/= (.*?) =/', '<h4>\\1</h4>', $readme );
		$readme = preg_replace( '/\[(.*?)\]\((.*?)\)/', '<a href="\\2">\\1</a>', $readme );
	}

	return $readme;
}


/**
 * Render Contributors List
 *
 * @return string $contributor_list HTML formatted list of all the contributors for MYC
 */
function mr_contributors() {
	$contributors = mr_get_contributors();

	if ( empty( $contributors ) )
		return '';

	$contributor_list = '<ul class="wp-people-group">';

	foreach ( $contributors as $contributor ) {
		$contributor_list .= '<li class="wp-person">';
		$contributor_list .= sprintf( '<a href="%s">',
			esc_url( 'https://github.com/' . $contributor->login ),
			esc_html( sprintf( __( 'View %s', 'my-chatbot' ), $contributor->login ) )
		);
		$contributor_list .= sprintf( '<img src="%s" width="64" height="64" class="gravatar" alt="%s" />', esc_url( $contributor->avatar_url ), esc_html( $contributor->login ) );
		$contributor_list .= '</a>';
		$contributor_list .= sprintf( '<a class="web" href="%s">%s</a>', esc_url( 'https://github.com/' . $contributor->login ), esc_html( $contributor->login ) );
		$contributor_list .= '</a>';
		$contributor_list .= '</li>';
	}

	$contributor_list .= '</ul>';

	return $contributor_list;
}

/**
 * Retreive list of contributors from GitHub.

 * @return array $contributors List of contributors
 */
function mr_get_contributors() {
	$contributors = get_transient( 'mr_contributors' );

	if ( false !== $contributors )
		return $contributors;

	$response = wp_remote_get( 'https://api.github.com/repos/danielpowney/multi-rating/contributors?per_page=999', array( 'sslverify' => false ) );

	if ( is_wp_error( $response ) || 200 != wp_remote_retrieve_response_code( $response ) )
		return array();

	$contributors = json_decode( wp_remote_retrieve_body( $response ) );

	if ( ! is_array( $contributors ) )
		return array();

	set_transient( 'mr_contributors', $contributors, 3600 );

	return $contributors;
}
