<?php 

/**
 * View class for rating results
 * 
 * @author dpowney
 *
 */
class MR_Rating_Result {
	
	/**
	 * Returns the HTML for the Top Rating Results
	 *
	 * @param unknown_type $top_rating_result_rows
	 * @param unknown_type $params
	 */
	public static function do_top_rating_results_html( $top_rating_result_rows, $params = array() ) {
	
		extract( wp_parse_args($params, array(
				'show_title' => true,
				'show_count' => false,
				'show_category_filter' => true,
				'category_id' => 0,
				'before_title' => '<h4>',
				'after_title' => '</h4>',
				'title' => null,
				'show_rank' => true,
				'no_rating_results_text' => '',
				'result_type' => Multi_Rating::STAR_RATING_RESULT_TYPE,
				'class' => '',
				'taxononmy' => null,
				'term_id' => 0,
				'filter_button_text' => '',
				'category_label_text' => '',
				'show_featured_img' => true,
				'image_size' => 'thumbnail'
		) ) );
	
		if ( $category_id == null ) {
			if ( $taxonomy == 'category' ) {
				$category_id = $term_id;
			} else {
				$category_id = 0; // so that all categories are returned
			}
		}
	
		$html = '<div class="top-rating-results ' . $class . '">';
	
		if ( ! empty( $title ) ) {
			$html .=  "$before_title" . $title . "$after_title";
		}
	
		if ( $show_category_filter == true ) {
			$html .= '<form action="" class="category-id-filter" method="POST">';
			$html .= '<label for="category-id">' . $category_label_text . '</label>';
			$html .= wp_dropdown_categories( array( 'echo' => false, 'class' => 'category-id', 'name' => 'category-id', 'id' => 'category-id', 'selected' => $category_id, 'show_option_all' => 'All' ) );
			$html .= '<input type="submit" value="' . $filter_button_text . '" />';
			$html .= '</form>';
		}
	
		if ( count( $top_rating_result_rows ) == 0 ) {
			$html .= '<p class="mr">' . $no_rating_results_text . '</p>';
		} else {
			$html .= '<table>';
			$index = 1;
			
			foreach ( $top_rating_result_rows as $rating_result ) {
				
				$post_id = $rating_result['post_id'];
				$post = get_post( $post_id );
				$html .= '<tr>';
				
				// Rank
				if ( $show_rank ) {
					$html .= '<td>';
					$html .= '<span class="rank">' . $index . '</span>';
					$html .= '</td>';
				}
				
				$rating_result_html = MR_Rating_Result::get_rating_result_type_html( $rating_result, array(
						'show_date' => false,
						'show_title' => false,
						'show_count' => true,
						'result_type' => $result_type
				) );
				
				$post_title_html = '<a class="title" href="' . get_permalink( $post_id ) . '">' . $post->post_title . '</a>';
				
				// If a thumbnail exists, it has a different layout
				$featured_img_shown = false;
				if ( $show_featured_img == true ) {
					if ( has_post_thumbnail( $post_id ) ) {
						$html .= '<td class=" mr-featured-img">' . get_the_post_thumbnail( $post_id, $image_size ) . '</td>';
						$featured_img_shown = true;
					}
				}
				$html .= '<td';
				if ( ! $featured_img_shown ) {
					$html .= ' colspan="2"';
				}
				$html .= '>';
				$html .= $post_title_html;
				if ( $featured_img_shown == true ) {
					$html .= '<br />';
				}
				$html .= $rating_result_html;
				$html .= '</td>';
				
				
				$html .= '</tr>';
	
				$index++;
			}
	
			$html .= '</table>';
		}
	
		$html .= '</div>';
	
		echo $html;
	
	}
	
	/**
	 * Return the HTML for a Rating Result
	 *
	 * @param unknown_type $rating_result
	 * @param unknown_type $params
	 */
	public static function do_rating_results_html( $rating_result, $params = array() ) {
	
		$html = MR_Rating_Result::get_rating_result_type_html( $rating_result, $params );
	
		echo $html;
	}
	
	/**
	 * Helper method for returning the HTML for the rating result type
	 *
	 * @param unknown_type $rating_result
	 * @param unknown_type $params
	 */
	public static function get_rating_result_type_html( $rating_result, $params = array() ) {
		 
		extract( wp_parse_args( $params, array(
				'show_title' => false,
				'show_date' => false,
				'show_rich_snippets' => false,
				'show_count' => true,
				'date' => null,
				'before_date' => '(',
				'after_date' => ')',
				'result_type' => Multi_Rating::STAR_RATING_RESULT_TYPE,
				'no_rating_results_text' => '',
				'ignore_count' => false,
				'class' => ''
		) ) );
		 
		$html = '<span class="rating-result ' . $class . '"';
		 
		$count = isset( $rating_result['count'] ) ? $rating_result['count'] : 0;
		 
		if ( ( $count == null || $count == 0 ) && $ignore_count == false ) {
			$html .= '><span class="no-rating-results-text">' . $no_rating_results_text . '</span>';
		} else {
	
			if ( $show_rich_snippets && $result_type == Multi_Rating::STAR_RATING_RESULT_TYPE ) {
				$html .= ' itemscope itemtype="http://schema.org/Article"';
			}
			$html .= '>';
	
			if ( $show_title == true ) {
				$post_id = $rating_result['post_id'];
				$post = get_post( $post_id );
				$html .= '<a href="' . get_permalink( $post_id ) . '">' . $post->post_title . '</a>';
			}
				
			if ( $result_type == Multi_Rating::SCORE_RESULT_TYPE ) {
				$html .= '<span class="score-result">' . $rating_result['adjusted_score_result'] . '/' . $rating_result['total_max_option_value'] . '</span>';
			} else if ( $result_type == Multi_Rating::PERCENTAGE_RESULT_TYPE ) {
				$html .= '<span class="percentage-result">' . $rating_result['adjusted_percentage_result'] . '%</span>';
			} else { // star rating				
				$html .= MR_Rating_Result::get_star_rating_html( 5, $rating_result['adjusted_star_result'] );
			}
				
			if ( $show_count && $count != null ) {
				$html .= '<span class="count">(' . $count . ')</span>';
			}
				
			if ( $show_date == true && $date != null ) {
				$html .= '<span class="date">' . $before_date . mysql2date( get_option( 'date_format' ), $date ) . $after_date . '</span>';
			}
				
			if ( is_singular() && $show_rich_snippets == true ) {
				$html .= '<span itemprop="aggregateRating" itemscope itemtype="http://schema.org/AggregateRating" class="rating-result-summary" style="display: none;">';
				$html .= '<span itemprop="ratingValue">' . $rating_result['adjusted_star_result'] . '</span>/<span itemprop="bestRating">5</span>';
				$html .= '<span itemprop="ratingCount" style="display:none;">' . $count . '</span>';
				$html .= '</span>';
			}
		}
	
		$html .= '</span>';
		 
		return $html;
	}
	
	/**
	 * Returns star rating HTML
	 *
	 * @param $max_stars
	 * @param $star_result
	 */
	public static function get_star_rating_html( $max_stars = 5, $star_result = 0) {
	
		$style_settings = (array) get_option( Multi_Rating::STYLE_SETTINGS );
		$star_rating_colour = $style_settings[Multi_Rating::STAR_RATING_COLOUR_OPTION];
		$font_awesome_version = $style_settings[Multi_Rating::FONT_AWESOME_VERSION_OPTION];
		$icon_classes = MR_Utils::get_icon_classes( $font_awesome_version );
		
		$use_custom_star_images = $style_settings[Multi_Rating::USE_CUSTOM_STAR_IMAGES];
		$image_width = $style_settings[Multi_Rating::CUSTOM_STAR_IMAGE_WIDTH];
		$image_height = $style_settings[Multi_Rating::CUSTOM_STAR_IMAGE_HEIGHT];
		 
		$html = '<span class="mr-star-rating" style="color: ' . $star_rating_colour . ' !important;">';
		$index = 0;
		 
		for ( $index; $index < $max_stars; $index++ ) {
			
			if ( $use_custom_star_images == true ) { // custom star images
    			$class = 'mr-custom-full-star';
    		
    			if ( $star_result < $index+1 ) {
    		
    				$diff = $star_result - $index;
    				if ( $diff > 0 ) {
    					if ( $diff >= 0.3 && $diff <= 0.7 ) {
    						$class ='mr-custom-half-star';
    					} else if ( $diff < 0.3 ) {
    						$class = 'mr-custom-empty-star';
    					} else {
    						$class = 'mr-custom-full-star';
    					}
    				} else {
    					$class = 'mr-custom-empty-star';
    				}
    		
    			} else {
    				$class = 'mr-custom-full-star';
    			}
    			
    			$html .= '<span class="' . $class . '"  width="' . $image_width . 'px" height="' . $image_height . 'px"></span>';
			} else { // Font Awesome star icons
				$class = $icon_classes['star_full'];
				 
				if ( $star_result < $index+1 ) {
		
					$diff = $star_result - $index;
					if ( $diff > 0 ) {
						if ( $diff >= 0.3 && $diff <= 0.7 ) {
							$class = $icon_classes['star_half'];
						} else if ( $diff < 0.3 ) {
							$class = $icon_classes['star_empty'];
						} else {
							$class = $icon_classes['star_full'];
						}
					} else {
						$class = $icon_classes['star_empty'];
					}
		
				} else {
					$class = $icon_classes['star_full'];
				}
				 
				$html .= '<i class="' . $class . '"></i>';
			}
		}
		 
		$html .= '</span>';
		$html .= '<span class="star-result">' . round(doubleval( $star_result ), 2)  . '/' . $max_stars . '</span>';
		 
		return $html;
	}
}

?>