<?php
/**
 * Shows the about screen
 */
function mr_about_screen() {
			
	// if version is less than 3.8 then manually add the necessary css missing from about.css
	if ( ! version_compare( get_bloginfo( 'version' ), '3.8', '>=' ) ) {
		?>
		<style type="text/css">
			.about-wrap .changelog .feature-section {
			    overflow: hidden;
			}
			.about-wrap .feature-section {
			    margin-top: 20px;
			}
			.about-wrap .feature-section.two-col > div {
			    position: relative;
			    width: 47.5%;
			    margin-right: 4.999999999%;
			    float: left;
			}
			.about-wrap .feature-section.col .last-feature {
			    margin-right: 0;
			}
			 .about-wrap hr {
			  	border: 0;
				border-top: 1px solid #DFDFDF;
			}
			.about-wrap {
				position: relative;
				margin: 25px 40px 0 20px;
				max-width: 1050px;
				font-size: 15px;
			}
			.about-wrap img {
				margin: 0;
				max-width: 100%;
				vertical-align: middle;
			}
			.about-wrap .changelog h2.about-headline-callout {
				margin: 1.1em 0 0.2em;
				font-size: 2.4em;
				font-weight: 300;
				line-height: 1.3;
				text-align: center;
			}
			.about-wrap .feature-section img {
			    margin-bottom: 20px !important;
			}
			.about-wrap h3 {
				margin: 1em 0 .6em;
				font-size: 1.5em;
				line-height: 1.5em;
			}
			.about-wrap .feature-section.three-col div {
				width: 29.75%;
			}
			.about-wrap .feature-section.two-col > div {
				margin-right: 4.8%;
			}
		</style>
	<?php 
	
	
	}
	?>
	
	<div class="wrap about-wrap">
	
		<div id="mr-header">
			<img class="mr-badge" src="<?php echo plugins_url( '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . 'img' . DIRECTORY_SEPARATOR . 'badge.png' , __FILE__ ); ?>" alt="<?php _e( 'Multi Rating', 'multi-rating-pro' ); ?>" / >
			<h1><?php printf( __( 'Multi Rating v%s', 'multi-rating' ), Multi_Rating::VERSION ); ?></h1>
			<p class="about-text">
				<?php _e( 'The best rating system plugin for WordPress. This is a simple plugin which allows users to rate posts based on multiple criteria and questions.', 'multi-rating' ); ?>
			</p>
		</div>
		
		<h2 class="nav-tab-wrapper">
			<?php
			$current_tab = isset( $_GET['tab'] ) ? $_GET['tab'] : 'getting_started';
			$page = Multi_Rating::ABOUT_PAGE_SLUG;
			$tabs = array (
					'getting_started' => __( 'Getting Started', 'multi-rating' ),
					'support' => __( 'Support', 'multi-rating' ),
					'multi_rating_pro' => __( 'Pro version available!', 'multi-rating' )
			);
			
			foreach ( $tabs as $tab_key => $tab_caption ) {
				$active = $current_tab == $tab_key ? 'nav-tab-active' : '';
				
				echo '<a class="nav-tab ' . $active . '" href="?page=' . $page . '&tab=' . $tab_key . '">' . $tab_caption . '</a>';
			} ?>
		</h2>
		
		<?php 
		if ( $current_tab == 'getting_started' ) { ?>	
		
			<div class="changelog">
					
				<p class="about-description"><?php _e( 'Use the tips below to help you get started.', 'multi-rating' ); ?></p>
				
				<div class="feature-section col three-col">
					<div class="col">
						<img src="<?php echo plugins_url( '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . 'img' . DIRECTORY_SEPARATOR . 'add-new-rating-items.png' , __FILE__ ); ?>" />
						<h4><?php _e( 'Add New Rating Items', 'multi-rating' ); ?></h4>
						<p><?php printf( __( 'Setup your criteria and questions by <a href="admin.php?page=%s">adding new rating items</a>.', 'multi-rating' ), Multi_Rating::RATING_ITEMS_PAGE_SLUG . '&rating-item-id=' ); ?></p>
					</div>
					<div class="col">
						<img src="<?php echo plugins_url( '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . 'img' . DIRECTORY_SEPARATOR . 'auto-placement.png' , __FILE__ ); ?>" />
						<h4><?php _e( 'Auto Placement Settings' ); ?></h4>
						<p><?php printf( __( 'Use the <a href="admin.php?page=%s">automatic placement settings</a> to set the rating form and rating results to display on every post or page in default positions.', 'multi-rating' ), Multi_Rating::SETTINGS_PAGE_SLUG ); ?></p>						
					</div>
					<div class="col last-feature">
						<img src="<?php echo plugins_url( '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . 'img' . DIRECTORY_SEPARATOR . 'view-frontend.png' , __FILE__ ); ?>" />
						<h4><?php _e( 'View the Frontend', 'multi-rating' ); ?></h4>
						<p><?php _e( 'If everything is setup correctly, the rating form and rating results should appear on your website!', 'multi-rating' ); ?></p>
					</div>
				</div>
			
				<div class="feature-section col two-col">
					<div class="col">
						<img src="<?php echo plugins_url( '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . 'img' . DIRECTORY_SEPARATOR . 'shortcodes.png' , __FILE__ ); ?>" />
						<h4><?php _e( 'Shortcodes', 'multi-rating' ); ?></h4>
						<p><?php _e( '[mr_rating_form] - displays the rating form</i>.', 'multi-rating' ); ?><br />
						<?php _e( '[mr_rating_results_list] - displays a list of rating results.', 'multi-rating' ); ?><br />
						<?php _e( '[mr_rating_result] - displays the rating result', 'multi-rating' ); ?></p>
						<p><?php printf( __( 'Refer to the <a href="%s">documentation</a> for more information on the attributes available.', 'multi-rating' ), 'http://multiratingpro.com/demo-page-free?utm_source=about&utm_medium=free-plugin&utm_campaign=wp-admin&utm_content=documentation"' ); ?></p>
					</div>
					<div class="col last-feature">
						<img src="<?php echo plugins_url( '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . 'img' . DIRECTORY_SEPARATOR . 'view-rating-results.png' , __FILE__ ); ?>" />
						<h4><?php _e( 'View Rating Results', 'multi-rating' ); ?></h4>
						<p><?php printf( __( 'View all <a href="admin.php?page=%s">rating results</a> and entries from the WP-admin.', 'multi-rating' ), Multi_Rating::RATING_RESULTS_PAGE_SLUG ); ?></p>
					</div>
				</div>
			</div>
			
			<div class="changelog">
				<div class="under-the-hood col three-col">
					<div class="col">
						<h4><?php _e( 'Custom Post Types', 'multi-rating' ); ?></h4>
						<p><?php _e( 'If you want to use the plugin for pages and other post types you\'ll need to be enable them in the plugin settings.', 'multi-rating' ); ?></p>
								
						<h4><?php _e( 'Custom Taxonomy', 'multi-rating' ); ?></h4>
						<p><?php _e( 'Shortcodes and API support custom taxonomies e.g. [mr_rating_resultlist taxonomy="post_tag" term_id="1"]. The category_id attribute is a shortcut to taxonomy="category" and term_id="category_id".', 'multi-rating' ); ?></p>
					
						<h4><?php _e( 'I18n, WPML & Custom Text', 'multi-rating' ); ?></h4>
						<p><?php _e( 'The plugin has been internationalized and is translation ready (.pot file in the languages directory). The plugin is fully WPML compatible. You can also modify the default text and messages.', 'multi-rating' ); ?></p>

						<h4><?php _e( 'Template System', 'multi-rating' ); ?></h4>
						<p><?php _e( 'The plugin has an in-built template system.', 'multi-rating' ); ?></p>
						
					</div>
					<div class="col">
					
						<h4><?php _e( 'Rich Snippets', 'multi-rating' ); ?></h4>
						<p><?php _e( '<a href="http://schema.org">schema.org</a> structured markup can be be added to every page which allows search engines to display the aggregated rating results as rich snippets.', 'multi-rating' ); ?></p>															
						
						
						<h4><?php _e( 'Icon Font Libraries', 'multi-rating' ); ?></h4>
						<p><?php _e( 'Font Awesome and Dashicon icon font libraries are supported by the plugin.', 'multi-rating' ); ?></p>	
						
						<h4><?php _e( 'Upload Custom Star Images', 'multi-rating' ); ?></h4>
						<p><?php _e( 'You can <a href="http://multiratingpro.com/documentation/custom-star-images?utm_source=about&utm_medium=free-plugin&utm_campaign=wp-admin&utm_content=custom-images">upload your own custom star images</a> to use instead of the using icon fonts.', 'multi-rating' ); ?></p>				
					
						<h4><?php _e( 'Export Rating Results', 'multi-rating' ); ?></h4>
						<p><?php _e( 'You can export the rating results to a CSV file.', 'multi-rating' ); ?></p>
						
						<h4><?php _e( 'Edit Ratings', 'multi-rating' ); ?></h4>
						<p><?php _e( 'Administrator and Editor user roles have the capability to edit ratings.', 'multi-rating' ); ?></p>
						
					</div>
					<div class="col last-feature">	
					
						<h4><?php _e( 'Reports' ); ?></h4>
						<p><?php _e( 'You can view the number of rating entries per day over time.', 'multi-rating' ); ?></p>
						
										
						<h4><?php _e( 'Meta-box', 'multi-rating' ); ?></h4>
						<p><?php _e( 'There\'s a meta-box on the edit post page so that you can override the default settings (e.g. auto placement settings).', 'multi-rating' ); ?></p>
					
						<h4><?php _e( 'Developer API', 'multi-rating' ); ?></h4>
						<p><?php _e( 'The API functions are located in the class-api.php file which contains a static class called Multi_Rating_API.', 'multi-rating' ); ?></p>
						
						<h4><?php _e( 'Action Hooks & Filters', 'multi-rating' ); ?></h4>
						<p><?php _e( 'Developers can extend the plugin functionality using action hooks and filters.', 'multi-rating' ); ?></p>		
						
							<h4><?php _e( 'GitHub', 'multi-rating' ); ?></h4>
						<p><?php printf( __( 'We\'re on <a href="%s">GitHub</a>. Contributions welcome.', 'multi-rating' ), 'https://github.com/danielpowney/multi-rating' ); ?></p>										
					
					</div>
			</div>
			
		</div>
	<?php } else if ( $current_tab == 'support') {
		?>
		<p><?php printf( __( 'All support for the free Multi Rating plugin should use the <a href="%s">WordPress.org support forum</a>.', 'multi-rating' ), 'https://wordpress.org/support/plugin/multi-rating' ); ?></p>
		<p><?php printf( __( 'Please use the <a href="%s">contact form</a> to send translation files or to contact me directly.', 'multi-rating' ), 'http://danielpowney.com/contact/' ); ?></p>
		<p><?php printf( __( 'Please <a href="%1$s" target="_blank">rate this plugin</a> on WordPress.org.', 'multi-rating' ), 'http://wordpress.org/support/view/plugin-reviews/multi-rating?filter=5#postform' ); ?></p>
		
		<?php
	} else { // Multi Rating Pro
		?>
		<p><?php printf( __( 'The <a href="%s">Multi Rating Pro</a> version provides a significant additional feature set, including:', 'multi-rating' ), 'http://multiratingpro.com?utm_source=about&utm_medium=free-plugin&utm_campaign=wp-admin&utm_content=top' ); ?></p>
		
		<p><strong><?php _e( 'The following key features are available in the Pro version:', 'multi-rating' ); ?></strong></p>
		<ol style="list-style-type:disc; margin-left: 2.5em;">
			<li><?php _e( 'Ratings moderation (approve or unapprove rating entries)', 'multi-rating' ); ?></li>
			<li><?php _e( 'WordPress comment system integration', 'multi-rating' ); ?></li>
			<li><?php _e( 'Logged in users can update or delete their existing ratings', 'multi-rating' ); ?></li>
			<li><?php _e( 'Bayesian average ratings', 'multi-rating' ); ?></li>
			<li><?php _e( 'Add common review fields to the rating form including title, name, e-mail and comments', 'multi-rating' ); ?></li>
			<li><?php _e( 'Add custom fields to the rating form to collect additional information', 'multi-rating' ); ?></li>
			<li><?php _e( 'Show a list of rating entry details in a review layout', 'multi-rating' ); ?></li>
			<li><?php _e( 'Show a breakdown of rating item results in a poll layout', 'multi-rating' ); ?></li>
			<li><?php _e( 'Show text labels for rating item options instead of numbers', 'multi-rating' ); ?></li>
			<li><?php _e( 'Extra shortcodes and widgets (e.g. reviews, user ratings dashboard and rating item results)', 'multi-rating' ); ?></li>
			<li><?php _e( 'Filters to set different rating forms and override auto placement settings for specific taxonomies, terms, post types, post id\'s and page URL\'s', 'multi-rating' ); ?></li>
			<li><?php _e( 'Options to exclude the home page, search page and archive pages (e.g. category)', 'multi-rating' ); ?></li>
			<li><?php _e( 'Thumbs up/down rating item type (e.g. like or dislike)', 'multi-rating' ); ?></li>
			<li><?php _e( 'Option to disallow anonymous ratings', 'multi-rating' ); ?></li>
			<li><?php _e( 'Embed reCAPTCHA in the rating form (add-on)', 'multi-rating' ); ?></li>
			<li><?php _e( 'Gravity Forms integration (add-on)', 'multi-rating' ); ?></li>
			<li><?php _e( 'And much more.', 'multi-rating' ); ?></li>
		</ol>
		
		<p>Check out <a href="http://multiratingpro.com?utm_source=about&utm_medium=free-plugin&utm_campaign=wp-admin&utm_content=bottom">Multi Rating Pro</a>.</p>
		<?php
	}
}