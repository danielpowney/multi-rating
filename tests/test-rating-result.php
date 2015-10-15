<?php

/**
 * Tests for rating results.
 * 
 * @author dpowney
 *
 */
class MR_Rating_Result_Test extends WP_UnitTestCase {
	
	/**
	 * Tests rating result for a single entry
	 * 
	 * @group func
	 */
	function test_rating_result1() {
		
		global $wpdb;
		
		$results = $wpdb->insert(  $wpdb->prefix . Multi_Rating::RATING_ITEM_TBL_NAME, array(
				'description' => 'Testing',
				'max_option_value' => 5
		) );
			
		$rating_item_id = $wpdb->insert_id;
		
		$user_id = $this->factory->user->create();
		
		$post_id = $this->factory->post->create( array( 'post_title' => 'Test Post' ) );
				
		$wpdb->insert( $wpdb->prefix . Multi_Rating::RATING_ITEM_ENTRY_TBL_NAME, array(
				'post_id' => $post_id,
				'user_id' => $user_id,
		), array( '%d', '%d' ) );
		
		$rating_entry_id = $wpdb->insert_id;
		
		$wpdb->insert( $wpdb->prefix . Multi_Rating::RATING_ITEM_ENTRY_VALUE_TBL_NAME, array(
				'rating_item_entry_id' => $rating_entry_id,
				'rating_item_id' => $rating_item_id,
				'value' => 4
		), array( '%d', '%d', '%d' ) );
		
		$rating_result = Multi_Rating_API::get_rating_result( $post_id );
		
		$this->assertEquals( 1, $rating_result['count'] ); // FIXME count_entries
		$this->assertEquals( 4, $rating_result['adjusted_star_result'] );
		$this->assertEquals( 4, $rating_result['adjusted_score_result'] );
		$this->assertEquals( 5, $rating_result['total_max_option_value'] );
		$this->assertEquals( 80, $rating_result['adjusted_percentage_result'] );
		$this->assertEquals( $post_id, $rating_result['post_id'] );
	}
	
	/**
	 * Tests rating result for 10 entries
	 * 
	 * @group func
	 */
	public function test_rating_result2() {
		
		global $wpdb;
		
		$results = $wpdb->insert(  $wpdb->prefix . Multi_Rating::RATING_ITEM_TBL_NAME, array(
				'description' => 'Testing',
				'max_option_value' => 5
		) );
			
		$rating_item_id = $wpdb->insert_id;
		
		$user_id = $this->factory->user->create();
		
		$rating_item_values = array( 5, 4, 5, 4, 3, 1, 5, 3, 5, 5 ); // = total = 40. 80%, 4/5 or 4/5 stars
		$post_id = $this->factory->post->create( array( 'post_title' => 'Test Post' ) );
		
		foreach ( $rating_item_values as $rating_item_value ) {
			$wpdb->insert( $wpdb->prefix . Multi_Rating::RATING_ITEM_ENTRY_TBL_NAME, array(
					'post_id' => $post_id,
					'user_id' => $user_id,
			), array( '%d', '%d' ) );
			
			$rating_entry_id = $wpdb->insert_id;
			
			$wpdb->insert( $wpdb->prefix . Multi_Rating::RATING_ITEM_ENTRY_VALUE_TBL_NAME, array(
					'rating_item_entry_id' => $rating_entry_id,
					'rating_item_id' => $rating_item_id,
					'value' => $rating_item_value
			), array( '%d', '%d', '%d' ) );
		}
		
		$rating_result = Multi_Rating_API::get_rating_result( $post_id );

		$this->assertEquals( 10, $rating_result['count'] ); // FIXME count_entries
		$this->assertEquals( 4, $rating_result['adjusted_star_result'] );
		$this->assertEquals( 4, $rating_result['adjusted_score_result'] );
		$this->assertEquals( 5, $rating_result['total_max_option_value'] );
		$this->assertEquals( 80, $rating_result['adjusted_percentage_result'] );
		$this->assertEquals( $post_id, $rating_result['post_id'] );
	}
	
	/**
	 * Tests rating result for two rating items in a rating form. Checks for 1 entry 
	 * and then 2 entries
	 * 
	 * @group func
	 */
	public function test_rating_result3() {
		
		global $wpdb;
		
		$results = $wpdb->insert(  $wpdb->prefix . Multi_Rating::RATING_ITEM_TBL_NAME, array(
				'description' => 'Testing 1',
				'max_option_value' => 5
		) );
			
		$rating_item_id1 = $wpdb->insert_id;
		
		$results = $wpdb->insert(  $wpdb->prefix . Multi_Rating::RATING_ITEM_TBL_NAME, array(
				'description' => 'Testing 2',
				'max_option_value' => 3
		) );
			
		$rating_item_id2 = $wpdb->insert_id;
		
		$user_id = $this->factory->user->create();
		
		$post_id = $this->factory->post->create( array( 'post_title' => 'Test Post' ) );
		
		/*
		 * Entry 1
		 * 3.125 stars, 5/8 score, 62.5% (0.625)
		 */
		$wpdb->insert( $wpdb->prefix . Multi_Rating::RATING_ITEM_ENTRY_TBL_NAME, array(
				'post_id' => $post_id,
				'user_id' => $user_id,
		), array( '%d', '%d' ) );
		
		$rating_entry_id = $wpdb->insert_id;
		
		$wpdb->insert( $wpdb->prefix . Multi_Rating::RATING_ITEM_ENTRY_VALUE_TBL_NAME, array(
				'rating_item_entry_id' => $rating_entry_id,
				'rating_item_id' => $rating_item_id1,
				'value' => 4
		), array( '%d', '%d', '%d' ) );
		
		$wpdb->insert( $wpdb->prefix . Multi_Rating::RATING_ITEM_ENTRY_VALUE_TBL_NAME, array(
				'rating_item_entry_id' => $rating_entry_id,
				'rating_item_id' => $rating_item_id2,
				'value' => 1
		), array( '%d', '%d', '%d' ) );
		
		$rating_result = Multi_Rating_API::get_rating_result( $post_id );
		
		// 3.125 stars, 5/8 score, 62.5% (.625)
		$this->assertEquals( 1, $rating_result['count'] ); // FIXME count_entries
		$this->assertEquals( 3.13, $rating_result['adjusted_star_result'] );
		$this->assertEquals( 5, $rating_result['adjusted_score_result'] );
		$this->assertEquals( 8, $rating_result['total_max_option_value'] );
		$this->assertEquals( 62.5, $rating_result['adjusted_percentage_result'] );
		$this->assertEquals( $post_id, $rating_result['post_id'] );
	
		/*
		 * Entry 2 anonymous
		 * 5 stars, 8/8 score and 100%
		 */
		$wpdb->insert( $wpdb->prefix . Multi_Rating::RATING_ITEM_ENTRY_TBL_NAME, array(
				'post_id' => $post_id,
				'user_id' => 0, // anonymous
		), array( '%d', '%d' ) );
		
		$rating_entry_id = $wpdb->insert_id;
		
		$wpdb->insert( $wpdb->prefix . Multi_Rating::RATING_ITEM_ENTRY_VALUE_TBL_NAME, array(
				'rating_item_entry_id' => $rating_entry_id,
				'rating_item_id' => $rating_item_id1,
				'value' => 5
		), array( '%d', '%d', '%d' ) );
		
		$wpdb->insert( $wpdb->prefix . Multi_Rating::RATING_ITEM_ENTRY_VALUE_TBL_NAME, array(
				'rating_item_entry_id' => $rating_entry_id,
				'rating_item_id' => $rating_item_id2,
				'value' => 3
		), array( '%d', '%d', '%d' ) );

		delete_post_meta( $post_id, Multi_Rating::RATING_RESULTS_POST_META_KEY );
		
		/*
		 * Rating result
		 */
		$rating_result = Multi_Rating_API::get_rating_result( $post_id );
		
		// 4.06 stars ( ( 3.13 + 5 )  / 2 = 4.065 ), 6.5 ( ( 5 + 8 ) / 2 ), 81.25 ( 0.8125) 
		$this->assertEquals( 2, $rating_result['count'] ); // FIXME count_entries
		$this->assertEquals( 4.07, $rating_result['adjusted_star_result'] );
		$this->assertEquals( 6.5, $rating_result['adjusted_score_result'] );
		$this->assertEquals( 8, $rating_result['total_max_option_value'] );
		$this->assertEquals( 81.25, $rating_result['adjusted_percentage_result'] );
		$this->assertEquals( $post_id, $rating_result['post_id'] );
	}

	/**
	 * Tests rating result list
	 * 
	 * @group func2
	 */
	public function test_rating_result_list1() {
		
		global $wpdb;
		
		$results = $wpdb->insert(  $wpdb->prefix . Multi_Rating::RATING_ITEM_TBL_NAME, array(
				'description' => 'Testing',
				'max_option_value' => 5
		) );
			
		$rating_item_id = $wpdb->insert_id;
		
		$post_ids = $this->factory->post->create_many( 5 );
		
		$user_id1 = $this->factory->user->create( array( 'role' => 'administrator' ) );
		$user_id2 = $this->factory->user->create( array( 'role' => 'subscribor' ));		
				
		$post_ratings = array(
				// post_ids[0] 5/5
				array( 
						array( 5, '2015/01/01 00:00:00', $user_id1, null ),
						array( 5, '2015/02/01 00:00:00', $user_id2, null ),
						array( 5, '2015/03/01 00:00:00', null, null ),
				),
				// post_ids[1] 6/20 = 1.5/5
				array( 
						array( 1, '2015/01/01 00:00:00', $user_id1 ),
						array( 2, '2015/02/01 00:00:00', $user_id2 ),
						array( 1, '2015/03/01 00:00:00', null, null ),
						array( 2, '2015/04/01 00:00:00', null, null ),
				),
				// post_ids[2] 5/15 = 1.66/5
				array(
						array( 3, '2015/01/01 00:00:00', $user_id1 ),
						array( 1, '2015/02/01 00:00:00', null ),
						array( 1, '2015/03/01 00:00:00', null, null ),
				),
				// post_ids[3] 9/10 = 4.5/5
				array(
						array( 5, '2015/01/01 00:00:00', null, null ),
						array( 4, '2015/02/01 00:00:00', null, null ),
				),
				// post_ids[4] 5/5
				array(
						array( 5, '2015/01/01 00:00:00', $user_id2, null ),
				)
		);
		
		$rating_entry_ids = array();
		
		$index = 0;
		foreach ( $post_ids as $post_id ) {
			
			$post_ratings_data = $post_ratings[$index];
			
			foreach ( $post_ratings_data as $post_ratings_data ) {
				
				$data = array(
						'post_id' => $post_id,
						'entry_date' => $post_ratings_data[1]
				);
					
				$data_format = array( '%d', '%s' );
					
				if ( is_numeric( $post_ratings_data[2] ) ) {
					$data['user_id'] = $post_ratings_data[2];
					array_push( $data_format, '%d' );
				}
					
				$wpdb->insert( $wpdb->prefix . Multi_Rating::RATING_ITEM_ENTRY_TBL_NAME, $data, $data_format );
			
				$rating_entry_id = $wpdb->insert_id;
					
				array_push( $rating_entry_ids, $rating_entry_id );
			
				$wpdb->insert( $wpdb->prefix . Multi_Rating::RATING_ITEM_ENTRY_VALUE_TBL_NAME, array(
						'rating_item_entry_id' => $rating_entry_id,
						'rating_item_id' => $rating_item_id,
						'value' => $post_ratings_data[0]
				), array( '%d', '%d', '%d' ) );
			}
			
			$index++;
		}
		
		// highest rated
		$rating_result_list = Multi_Rating_API::get_rating_results( array() );

		$this->assertEquals( 5, count( $rating_result_list ) );
	}
	
	public function setUp() {
	
		parent::setUp();
	
		// delete any sample rating items
		global $wpdb;
	
		$wpdb->query( 'DELETE FROM ' . $wpdb->prefix . Multi_Rating::RATING_ITEM_TBL_NAME . ' WHERE 1' );
	}
	
	public function tearDown() {
		
		parent::tearDown();
		
		global $wpdb;
		
		$wpdb->query( 'DELETE FROM ' . $wpdb->prefix . Multi_Rating::RATING_ITEM_ENTRY_TBL_NAME . ' WHERE 1' );
		$wpdb->query( 'DELETE FROM ' . $wpdb->prefix . Multi_Rating::RATING_ITEM_ENTRY_VALUE_TBL_NAME . ' WHERE 1' );
		$wpdb->query( 'DELETE FROM ' . $wpdb->prefix . Multi_Rating::RATING_ITEM_TBL_NAME . ' WHERE 1' );
		$wpdb->query( 'DELETE FROM ' . $wpdb->prefix . Multi_Rating::RATING_SUBJECT_TBL_NAME . ' WHERE 1' );
	
	}
}

