=== Multi Rating ===
Contributors: dpowney
Tags: rating, review, star rating, multi rating, post rating, rating criteria, rich snippet
Requires at least: 4.0
Tested up to: 4.8
Stable tag: trunk
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

A powerful post rating / review system plugin for WordPress including 5 star ratings and rich snippets.

== Description ==

A powerful post rating / review system plugin for WordPress with a niche of doing multi ratings better than anyone else.

View [Demo](https://multiratingpro.com/demo-page-free?utm_source=view-demo&utm_medium=free-plugin&utm_campaign=readme).

= Key Features =

* Rate posts based on multiple rating criteria / questions with star ratings, select drop-down lists and radio buttons
* Average ratings can be out of 5 stars, a percentage or an aggregated score
* Add schema.org microdata to show the aggregate rating / reviews in SERP as rich snippets
* Font Awesome and Dashicon support for star icons or upload your own custom star image icons
* Tools to export ratings to a CSV file
* Shortcodes and widgets to display the rating form and overall post ratings
* Shortcode and widget to display a list of ratings (sort by highest rated, lowest rated, most entries, post title ascending or post title descending)
* View / edit all ratings and details in WP-admin
* Apply weights to rating items to adjust the average ratings
* Automatic placement of rating form and average ratings on enabled post types
* Settings to validate duplicates, change default text and apply different styles etc...
* i18n translation ready and WPML plugin support
* In-built template system for customization
* All data stored in your own WordPress database - no signup required!

= Shortcode Examples =

* [mr_rating_form]
* [mr_rating_form title="Please rate this" submit_button_text="Submit"]
* [mr_rating_result]
* [mr_rating_result post_id="100" no_rating_results_text="No rating result yet" show_rich_snippets="false"]
* [mr_rating_results_list]
* [mr_rating_results_list title="Top Ratings" sort_by="highest_rated" limit="10"]
* [mr_rating_results_list title="Ratings" sort_by="most_entries" limit="5"]

= Pro version available! =

The [Multi Rating Pro](https://multiratingpro.com?utm_source=pro-version&utm_medium=free-plugin&utm_campaign=readme&utm_content=top) version provides a significant additional feature set, including:

* Unlimited rating forms
* Ratings moderation (approve or unapprove rating entries)
* WordPress comment system integration
* Bayesian average ratings
* Add common review fields to the rating form (title, name, e-mail and comments)
* Add custom fields to the rating form to collect additional information
* Show a list of rating entry details in a review layout
* Show a breakdown of rating items in a poll layout
* Show text labels for rating item options instead of numbers
* Extra shortcodes and widgets (i.e. reviews, user ratings dashboard and rating item results)
* Logged in users can update or delete their existing ratings
* Filters to set different rating forms and override auto placement settings for specific taxonomies, terms, post types, post id's and page URL's
* Options to exclude the home page, search page and archive pages (e.g. category)
* Thumbs up/down rating item type (e.g. like or dislike)
* Option to disallow anonymous ratings
* Google reCAPTCHA validation (add-on)
* Gravity Forms integration (add-on)
* Readonly REST API (add-on)
* And much more...

Check out [Multi Rating Pro](https://multiratingpro.com?utm_source=pro-version&utm_medium=free-plugin&utm_campaign=readme&utm_content=bottom).

== Screenshots ==

1. Auto placement of rating results after post title and rating form after post content. Twenty Fifteen theme.
2. Rating Results List shortcode. [mr_rating_results_list show_filter="true" title="Top Ratings" sort_by="highest_rated"]
3. Rating results in WP-admin for each post.
4. Every rating entry can be viewed in WP-admin and edited
5. Rating Results List widget options include displaying the post feature image, a rank, changing the result type (star rating out of 5, score or percentage), different sorting mechanisms (highest rated, lowest rated etc...), set widget title and much more.
6. Rating items table.
7. General settings.
8. Style settings include star rating select and on hover colors, load Font Awesome library CDN, version of Font Awesome and the ability to set custom star rating images to use instead of Font Awesome icons.
9. Custom text setting.
10. Graphical report on the number of entries per day.
11. The plugin has several tools including exporting rating results to a CSV file, clearing the rating results cache and deleting rating results in bulk.
12. WP-admin plugin menu.
13. Edit post page. Add shortcodes in the editor. The Multi Rating meta-box can set the auto placement settings per post to override the default settings.
14. Rating Results List Widget. Twenty Fifteen theme.

== Upgrade Notice ==

== Changelog ==

= 4.2.7 (25/07/2017) =
* Tweak: Turned off auto placement in RSS feeds by checking the is_feed() function. Note a simple way to flush an RSS feed cache is to update a post.

= 4.2.6 (15/02/2017) =
* New: Font Awesome 4.7.0 support
* New: Added mr_disable_custom_text filter to turn off custom text settings allowing language translation of strings
* Bug: Fixed incorrect data format used when updating label of a rating item

= 4.2.5 (25/01/2017) =
* Bug: Fixed some potential SQL injection vulnerabilities
* Bug: Fixed a couple of cross site scripting (XSS) vulnerabilities in the rating form
* Bug: Fixed incorrect weight validation for decimals when adding a new rating item
* Bug: Added missing validation of post id when saving a rating entry
* Bug: Check if editor and administrator roles roles exist before adding mr_edit_rating capability

= 4.2.4 (18/10/2016) =
* New: Added badge icon to about page
* New: Added post meta field "mr_rating_result_count_entries" which is a count of rating entries
* New: Added post meta field "mr_rating_result_star_rating" which has the overall star rating

= 4.2.3 (16/07/2016) =
* Bug: Fixed styles for icon font library not defaulting correctly
* Bug: Fixed hide rating form on submit and template strip newlines settings not saving correctly

= 4.2.2 (14/07/2016) =
* Bug: Fixed issue unable to add more than one rating item
* Tweak: Added new error if default option > max option when adding a new rating item

= 4.2.1 (09/07/2016) =
* Bug: Fixed fatal error missing rating-entries.php file

= 4.2 (08/07/2016) = 
* New: Added Dashicon support
* New: Added Font Awesome 4.2.0, 4.5.0 and 4.6.3 support
* New: Added Pro version menu item
* New: Added disable styles option
* New: Changes the font icon options to be generic to allow other font icon libraries to be added
* Tweak: Removed the hide post meta box option to keep the settings minimal and simple in the free version
* Tweak: Moved MR post meta box so that it's not at the top
* Tweak: Changed the rating-form.php template file so that messages are shown just above the buttons
* Bug: The default WordPress charset collate is now used for db table creation and updates (global $charset_collate variable)
* Bug: Fixed some schema.org microdata errors
* Bug: Some JS fixes based on JSLint and also updated some jQuery selectors based on attribute values to be surrounded in quotes
* Tweak: Updated languages files

= 4.1.13 (09/06/2016) =
* Bug: Fixed pagination with filters on rating entries and rating results tables in wp-admin

= 4.1.12 (06/06/2016) =
* Tweak: Minor readme changes and updated some links

= 4.1.11 (02/11/2015) =
* Bug: jQuery UI calls protocol agnostic i.e http or https
* Bug: Removed usage of mysql_real_escape_string() in admin tables

= 4.1.10 (15/10/2015) =
* Bug: Fixed attachments not being able to calculate ratings due to post status inherit instead of published
* Bug: Fixed rating form using mrp_rating_form_include_minus filter
* Tweak: Changed Rating Results List widget show filter default to false
* Tweak: Moved creating sample rating item if none exists to plugin activation

= 4.1.9 (10/09/2015) =
* Bug: Fixed settings section in wp-admin for custom error message text
* Tweak: Fixed plugin about page for WP 4.3

= 4.1.8 (14/08/2015) =
* Tweak: Made it easier to add your own schema.org microdata and override the default "https://schema.org/Article" micordata using new filter mr_rating_result_microdata. The old filters mrp_rating_result_microdata_thing and mrp_rating_result_microdata_thing_properties are no longer supported.

= 4.1.7 (08/07/2015) =
* Bug: Fixed schema.org microdata for itemtype Article (the post) in rating-result.php template file missing required itemprops publishedDate, headline and image 

= 4.1.6 (02/08/2015) =
* Bug: Fixed dbdelta key spacing as per https://core.trac.wordpress.org/ticket/32314

= 4.1.5 (16/07/2015) =
* Tweak: Set template strip newlines option to on by default
* New: Create a sample rating item if none exists

= 4.1.4 (15/07/2015) =
* New: Added auto placement of rating results options before_content and after_content
* New: Added rating form widget
* New: Added rating results widget

= 4.1.3 (06/07/2015) =
* Tweak: Changed post link in rating results and rating entries tables to the edit post page
* Bug: Fixed touch event on minus icon with custom star images in rating form not working properly 

= 4.1.2 (03/07/2015) =
* Bug: Fixed error creating db tables on activation caused by stray comma in SQL statement

= 4.1.1 (30/06/2015) =
* New: Added JS dialog to confirm clearing rating entries in the Tools
* Bug: Fixed several WPML issues (i.e. unable to submit ratings) where the original post in the default language was not always returned calling icl_object_id.
* Bug: Fixed preserve max option value in rating-result.php template
* Bug: Added number_format() to rating-result.php template for showing count of entries
* Bug: Optimized db indexes for better performance

= 4.1 =
* Tweak: Added option for rating form error message color
* New: Added required option for rating items. If enabled and 0 is selected as the rating item value, a field is required error message is displayed
* Tweak: Now using minified JS and CSS files
* Tweak: Improved usability of star ratings by not setting a default value. Star ratings are now more interactive as the on hover state works straight away.

= 4.0.2 =
* Tweak: Improved data sanitization
* Bug: Added template strip newlines option

= 4.0.1 =
* New: Added option to strip newlines from templates prior to display to support plugins such as Visual Composer

= 4.0 =
* New: Fully WPML compatible
* New: Added filters for entries query: select, from, join, where, order by, group by and limit
* New: Added support for Font Awesome 4.3.0
* New: Added sorting by entry count for equal ratings.
* Bug: Updated jQuery .on("hover") to .on("mouseenter mouseleave") as this was removed in jQuery 1.9
* New: Added in-built template system
* New: Added more options to the Rating Results List widget (formally Top Rating Results widget) including taxonomy, terms, result type, show filter, filter label, show rank, header and sort by.
* New: Added a setting to be able to default hide the Multi Rating meta box.
* New: Added more sorting options to rating results in the WP-admin.
* New: Added CSS cursor pointer on hover of star rating icons.
* New: Added dashicons-star-filled as menu icon
* New: Applied widget_title filter for widget titles
* Bug: Fixed security flaw related to name & comment fields. Please update.
* Bug: Improved escaping of SQL queries and output data sanitization.
* Bug: Fixed AJAX returning rating result where rating results position for a post is do not show.
* Tweak: Renamed all shortcodes to have a prefix mr_ (old shortcode names are deprecated but will still work). display_rating_form => mr_rating_form, display_rating_results => mr_rating_results and display_top_rating_results => mr_rating_results_list
* Tweak: Renamed the Top Rating Results widget to Rating Results List widget which is more generic and supports different sorting mechanisms.
* Tweak: Refactored all shortcodes, widgets and the correspnding API functions to use new template system. Renamed some shortcode attributes names, widget options names and API function parameters to improve consistency.
* Tweak: Improved readability of frontend JS
* Tweak: General CSS improvements
* Tweak: Removed the view more functionality. This will added again later utilising AJAX instead of a page refresh.
* Tweak: Renamed the display_top_rating_results() API function to display_rating_results_list() (old API function is deprecated, but will still work).
* Tweak: Renamed the following API parameters and shortcode attributes: show_category_filter => show_filter (deprecated show_category_filter) and category_label_text => filter_label_text (deprecated category_label_text)
* Important: Deleted class-rating-results.php file as it's no longer needed.
* Important: Deleted template functions from class-rating-form.php.
* Important: Deleted actions that no longer make sense due to the new template system. If you've used these actions to modify the template, it will no longer work: mr_display_top_rating_results, mr_display_rating_results and mr_display_rating_form
* Important: Moved common sorting functions from API to utils class.

= 3.2.1 =
* New: Added loading spinner when saving rating form
* Tweak: Improved styles in plugin settings page

= 3.2 =
* New: Refactored save rating restrictions to allow using cookies and or an IP address within a specified time in hours
* New: Added edit_ratings capability to allow Editor role to be able to edit ratings

= 3.1.5 =
* Bug: Removed undefined PHP variable notice in plugin settings page

= 3.1.4 =
* Bug: Fixed auto placement issue - undefined content in mr_can_apply_filter

= 3.1.3 =
* New: Added settings to upload your own star rating images to use instead of Font Awesome star icons
* New: Added after_auto_placement action hook
* New: Added mr_can_apply_filter and mr_can_do_shortcode filters
* New: Modified the Top Rating Results widget, [display_top_rating_results] shortcode and the display_top_rating_results() API function to be able to display the featured image of a post
* New: Added more options to show feature image and thumbnail size to the Top Rating Results widget
* New: Added Font Awesome 4.2.0 support

= 3.1.2 =
* Bug: Fixed show_count parameter not set correctly when displaying the top rating results
* Bug: Fixed filter button text callback defect

= 3.1.1 =
* Bug: Fixed rounding of star result to 2 decimals
* Bug: Fixed is_admin() checks to also check AJAX requests to support plugins such as FacetWP
* New: Added filter button custom text and category label text
* New: Added ability to sort rating results in WP-admin by post title asc, post title desc, top rating results and most entries
* New: Added to delete all associated rating results and entry data when a post is deleted
* Bug: Added fix to only show published posts in the Top Rating Results and to recalculate the rating results when a post changes status

= 3.1 =
* New: Added edit rating feature in WP-admin
* Tweak: Replaced storing username with user id
* Tweak: Refactored star rating html generation
* New: Added WP filter for custom rating form validation
* Tweak: Some CSS changes
* Tweak: Improved usability of WP-admin tables

= 3.0.2 =
* Bug: Fixed weight issue calculting rating results

= 3.0.1 =
* Tweak: Performance impovements. Added rating results cache.

= 3.0 =
* New: Major plugin refactor.
* Bug: Several bug fixes
* New: Added more filters in WP-admin tables
* New: Added new Tools menu
* New: Added rating results table in WP-admin
* New: Hide rating form on submit
* New: Replaced JS alert after submitting rating form with HTML message
* New: Added several action hooks

= 2.3.1 =
* Bug: Fixed bug calculating raitng results if a new rating item is added
* Tweak: Modified how rating results are calculated
* Tweak: Sorting of rating results by result type
* Bug: Fixed bug missing before_title after_title in display_top_rating_results shortcode
* Bug: Fixed bug in JS where trim is not supported in IE8
* New: Added support for custom taxonomies

= 2.3 =
* New: Support for different versions of Font Awesome added
* New: Plugin now i18n translation ready
* New: Added About page

= 2.2.4 =
* New: New report which shows number of entries per day
* New: Ability to export rating results to a CSV file

= 2.2.3 (07/07/2014) =
* New: Added on hover color for star rating select

= 2.2.2 (29/05/2014) =
* Bug: Fixed missing category_id attribute to display_top_rating_results shortcode

= 2.2.1 (29/05/2014) =
* Bug: Fixed category filter

= 2.2 (28/05/2014) =
* Tweak: Added Fontawesome star icons instead of using image sprites
* New: Added radio options and star ratings along with the select drop down to select rating item values
* Tweak: Some template and style changes to improve the layout
* Bug: Fixed a couple of misc bugs

= 2.1 (07/05/2014) =
* Tweak: Refactored HTML for rating form and rating results including CSS styles
* New: Added Multi Rating meta box in edit page to override default settings for automatic placements of rating form and rating results per post or page
* New: Added class shortcode attribute
* Tweak: Refactored how rating results are returned in API

= 2.0.4 (12/04/2014) =
* Bug: Fixed rich snippets bug
* New: Refactored API functions and added more params that can be used in shortcodes

= 2.0.3 =
* New: Information on multi-rating-pro plugin features added

= 2.0.2 =
* New: Rating results table in WP-admin query updated to order by entry_date desc

= 2.0.1 =
* Bug: Fixed top rating results widget bug

= 2.0 =
* New: Major refactor of plugin.
* Important: Old Shortcodes deprecated and replaced with new shortcodes. Old settings and API functions renamed and may not backward compatible.
* New: Old settings have been renamed
* Important: Old rating result will not be migrated. If you wish to keep your rating results, you must continue to use the version 1.1.8.
* New: Improved WP admin including view rating result entries and values

= 1.1.8 (15/01/2014) =
* New: Allow removing title from rating form and top rating results

= 1.1.7 (13/01/2014) =
* New: Added settings for default rating form title and default top rating results title

= 1.1.6 (7/01/2014) =
* Bug: Fixed bug in displaying top results for multiple post types

= 1.1.5 (6/01/2014) =
* Bug: Fixed custom title for widgets

= 1.1.4 (19/12/2013) =
* New: Added support for character encoding of rating items in db

= 1.1.3 (14/12/2013) =
* Bug: Fixed post title on top rating results widget

= 1.1.2 = (14/12/2013) =
* Bug: Removed debugging comment accidentally left behind

= 1.1.1 (12/12/2013) =
* New: Changed shortcode parameter for post id from id to post_id
* Tweak: Fixed default values in API functions for themes
* Bug: Fixed bug which caused only 5 top rating results being displayed

= 1.1 =
* New: Added weight rating for multi criteria

= 1.0.3=
* Bug: Fixed activation for older versions of PHP

= 1.0.2 =
* New: Added option to change rating results stars to small, medium or large size
* Bug: Fixed some CSS issues

= 1.0.1 =
* New: Added check is_singular() to add rich snippets to rating results

= 1.0 =
* Initial release