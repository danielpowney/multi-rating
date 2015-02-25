=== Multi Rating ===
Contributors: dpowney
Donate link: http://www.danielpowney.com/donate
Tags: rating, multi-rating, post rating, star, multi, criteria, rich snippet, testimonial, review, hReview, multi rating, feedback, user rating
Requires at least: 3.0.1
Tested up to: 4.1
Stable tag: trunk
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

The best rating system plugin for WordPress. Multi Rating allows visitors to rate a post based on multiple criteria and questions.

== Description ==

The best rating system plugin for WordPress. Multi Rating allows visitors to rate a post based on multiple criteria and questions.

= Features =

* 5 star ratings, percentage and score results
* Multuple rating criteria and questions using star ratings, select drop-down lists and radio buttons to choose answers from
* Font Awesome used for star rating icons or upload your own star rating images to use instead
* Shortcodes to display the rating form and rating results
* Shortcode and widget to display a list of rating results (sort by highest rated, lowest rated, most entries, post title ascending or post title descending)
* Ability to add schema.org microdata to show the aggregate ratings in search engine results as rich snippets
* View the rating results, entries and selected values in the WP-admin
* Enable for custom post types and pages
* Add custom weights to each rating item to adjust the overall rating results
* Automatic placement settings to display the rating form and rating results on every post in different positions
* Meta-box on the edit post page to override the default automatic placement settings
* Settings to restrict post types, turn on validation, modify text, apply different styles and clear the database etc...
* Graphical reports on number of entries per day
* Export rating results to a CSV file
* Custom taxonomy support
* Developer API functions and template tags to use in your theme
* Edit ratings in WP-admin (Editor & Administrator user roles can only do this)
* In-built template system for customization
* All data stoted in your own WordPress database - no signup required!

Here's a live demo: http://danielpowney.com/multi-rating/

The plugin is i18n translation ready (.pot file in the languages directory). Different versions of Font Awesome are supported as well to help prevent any theme or plugin conflicts. For WPML plugin support, there is a wpml-config.xml file in the languages directory of the plugin.

= Shortcode Examples =

* [mr_rating_form]
* [mr_rating_form title="Please rate this" submit_button_text="Submit"]
* [mr_rating_result]
* [mr_rating_result post_id="100" no_rating_results_text="No rating result yet" show_rich_snippets="false"]
* [mr_rating_results_list]
* [mr_rating_results_list title="Top Ratings" sort_by="highest_rated" limit="10"]
* [mr_rating_results_list title="Ratings" sort_by="most_entries" limit="5" before_title="<h3>" after_title="</h3>" ]

Github Repositpory: http://github.com/danielpowney/multi-rating

= Multi Rating Pro =

The following key features are available in the Pro version:

* Multiple rating forms with different rating items
* WordPress comments system integration
* Add custom fields to collect additional information
* Ratings moderation (approve or unapprove rating entries)
* Logged in users can update or delete their existing ratings
* New shortcodes, API functions and widgets (i.e. reviews and rating item results)
* Rating forms can optionally include a name, e-mail and comment fields
* Ability to use text descriptions for select and radio options instead of numbers
* Post, category and specific page filters to include (whitelist) or exclude (blacklist) automatic placement of the rating form and rating results
* Options to exclude the home page and archive pages (i.e. Category, Tag, Author or a Date based pages)
* Thumbs up/thumbs down rating item type
* Display a breakdown of rating item results in 3 layouts
* Allow/disallow anonymous user ratings option

Check it out here http://danielpowney.com/downloads/multi-rating-pro/

== Installation ==

1. Install plugin via the WordPress.org plugin directory. Unzip and place plugin folder in /wp-content/plugins/ directory for manual installation
1. Activate the plugin through the 'Plugins' menu in WordPress admin
1. Go to 'Settings' menu 'Multi Rating' option in WordPress admin

== Frequently Asked Questions ==

Full documentation available here http://danielpowney.com/multi-rating/

== Screenshots ==
1. Demo of rating results after page title, rating form and top rating results
2. View rating results in WP-admin
3. Edit post page in WP-admin showing Multi Rating meta box and shortcode sample in visual editor
4. Rating items
5. Add a new rating item
6. Top Rating Results widget
7. Entries tab
8. Settings page
9. Reports page

== Changelog ==

= 4.0 =
* New template system
* Added lots of new actions & filters
* Added CSS cursor pointer on hover of star rating icons
* Renamed the Top Rating Results widget to Rating Results List widget and added more options
* API & shortcode changes

= 3.2.1 =
* Added loading spinner when saving rating form
* Improved styles in plugin settings page

= 3.2 =
* Refactored save rating restrictions to allow using cookies and or an IP address within a specified time in hours
* Added edit_ratings capability to allow Editor role to be able to edit ratings

= 3.1.5 =
* Removed undefined PHP variable notice in plugin settings page

= 3.1.4 =
* Fixed auto placement issue - undefined content in mr_can_apply_filter

= 3.1.3 =
* Added settings to upload your own star rating images to use instead of Font Awesome star icons
* Added after_auto_placement action hook
* Added mrp_can_apply_filter and mr_can_do_shortcode filters
* Modified the Top Rating Results widget, [display_top_rating_results] shortcode and the display_top_rating_results() API function to be able to display the featured image of a post
* Added more options to show feature image and thumbnail size to the Top Rating Results widget
* Added Font Awesome 4.2.0 support

= 3.1.2 =
* Fixed show_count parameter not set correctly when displaying the top rating results
* Fixed filter button text callback defect

= 3.1.1 =
* Fixed rounding of star result to 2 decimals
* Fixed is_admin() checks to also check AJAX requests to support plugins such as FacetWP
* added filter button custom text and category label text
* Added ability to sort rating results in WP-admin by post title asc, post title desc, top rating results and most entries
* Added to delete all associated rating results and entry data when a post is deleted
* Added fix to only show published posts in the Top Rating Results and to recalculate the rating results when a post changes status

= 3.1 =
* Added edit rating feature in WP-admin
* Replaced storing username with user id
* Refactored star rating html generation
* Added WP filter for custom rating form validation
* Some CSS changes
* Improved usability of WP-admin tables

= 3.0.2 =
* Fixed weight issue calculting rating results

= 3.0.1 =
* Performance impovements. Added rating results cache.

= 3.0 =
* Major plugin refactor.
* Several bug fixes
* Added more filters in WP-admin tables
* Added new Tools menu
* Added rating results table in WP-admin
* Hide rating form on submit
* Replaced JS alery after submitting rating form with HTML message
* Added action hooks

= 2.3.1 =
* Fixed bug calculating raitng results if a new rating item is added
* Modified how rating results are calculated
* Sorting of rating results by result type
* Fixed bug missing before_title after_title in display_top_rating_results shortcode
* Fixed bug in JS where trim is not supported in IE8
* Added support for custom taxonomies

= 2.3 =
* Support for different versions of Font Awesome added
* Plugin now i18n translation ready
* Added About page

= 2.2.4 =
* New report which shows number of entries per day
* Ability to export rating results to a CSV file

= 2.2.3 (07/07/2014) =
* Added on hover color for star rating select

= 2.2.2 (29/05/2014) =
* Fixed missing category_id attribute to display_top_rating_results shortcode

= 2.2.1 (29/05/2014) =
* Fixed category filter

= 2.2 (28/05/2014) =
* Added Fontawesome star icons instead of using image sprites
* Added radio options and star ratings along with the select drop down to select rating item values
* Some template and style changes to improve the layout
* Fixed a couple of misc bugs

= 2.1 (07/05/2014) =
* Refactored HTML for rating form and rating results including CSS styles
* Added Multi Rating meta box in edit page to override default settings for automatic placements of rating form and rating results per post or page
* Added class shortcode attribute
* Refactored how rating results are returned in API

= 2.0.4 (12/04/2014) =
* Fixed rich snippets bug
* Refactored API functions and added more params that can be used in shortcodes

= 2.0.3 =
* Information on multi-rating-pro plugin features added

= 2.0.2 =
* Rating results table in WP-admin query updated to order by entry_date desc

= 2.0.1 =
* Fixed top rating results widget bug

= 2.0 =
* Major refactor of plugin.
* Old Shortcodes deprecated and replaced with new shortcodes. Old settings and API functions renamed and may not backward compatible.
* Old settings have been renamed
* Old rating result will not be migrated. If you wish to keep your rating results, you must continue to use the version 1.1.8.
* Improved WP admin including view rating result entries and values

= 1.1.8 (15/01/2014) =
* Allow removing title from rating form and top rating results

= 1.1.7 (13/01/2014) =
* Added settings for default rating form title and default top rating results title

= 1.1.6 (7/01/2014) =
* Fixed bug in displaying top results for multiple post types

= 1.1.5 (6/01/2014) =
* Fixed custom title for widgets

= 1.1.4 (19/12/2013) =
* Added support for character encoding

= 1.1.3 = (14/12/2013)
* Fixed post title on top rating results widget

= 1.1.2 = (14/12/2013)
* Removed debugging comment accidentally left behind

= 1.1.1 (12/12/2013) =
* Changed shortcode parameter for post id from id to post_id
* Fixed default values in API functions for themes
* Fixed bug which caused only 5 top rating results being displayed

= 1.1 =
* Added weight rating for multi criteria

= 1.0.3=
* Fixed activation for older versions of PHP

= 1.0.2 =
* Added option to change rating results stars to small, medium or large size
* Fixed some CSS issues

= 1.0.1 =
* Added check is_singular() to add rich snippets to rating results

= 1.0 =
* Initial release