<?php 

/**
 * Top Rating Results action
 */
add_action( 'mr_display_top_rating_results', array( 'MR_Rating_Result', 'do_top_rating_results_html' ), 10, 2);

/**
 * Rating results action
 */
add_action( 'mr_display_rating_results', array( 'MR_Rating_Result', 'do_rating_results_html' ), 10, 2);

/**
 * Rating form action
 */
add_action( 'mr_display_rating_form', array( 'MR_Rating_Form', 'do_rating_form_html' ), 10, 4);

?>