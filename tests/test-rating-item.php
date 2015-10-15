<?php

/**
 * Tests for rating items
 * @author dpowney
 *
 */
class MR_Rating_Item_Test extends WP_UnitTestCase {
	
	/**
	 * Simple test for getting a rating item and checking correct values are returned
	 * 
	 * @group func8	
	 */
	function test_get_rating_items() {
		
		global $wpdb;
		
		$description = 'Hello world';
		$max_option_value = 5;
		$default_option_value = 5;
		$weight = 1;
		$type = 'star_rating';
		$required = true;
		
		$results = $wpdb->insert(  $wpdb->prefix . Multi_Rating::RATING_ITEM_TBL_NAME, array(
				'description' => $description,
				'max_option_value' => $max_option_value,
				'default_option_value' => $default_option_value,
				'weight' => $weight,
				'type' => $type,
				'required' => $required
		) );
			
		$rating_item_id = $wpdb->insert_id;
		
		$rating_items = Multi_Rating_API::get_rating_items( array( 'rating_item_ids' => $rating_item_id ) ); 

		$this->assertEquals( count( $rating_items ), 1 );
		
		$rating_item = $rating_items[$rating_item_id];
		
		$this->assertEquals( $rating_item_id, $rating_item['rating_item_id'] );
		$this->assertEquals( $description, $rating_item['description'] );
		$this->assertEquals( $max_option_value, $rating_item['max_option_value'] );
		$this->assertEquals( $default_option_value, $rating_item['default_option_value'] );
		$this->assertEquals( $weight, $rating_item['weight'] );
		$this->assertEquals( $type, $rating_item['type'] );
		//$this->assertEquals( $required, $rating_item['required'] );
	}
	
	/**
	 * Tests getting two rating item for a rating form but three rating items exist in db
	 * 
	 * @group func
	 */
	public function test_get_rating_items2() {
		
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
		
		$results = $wpdb->insert(  $wpdb->prefix . Multi_Rating::RATING_ITEM_TBL_NAME, array(
				'description' => 'Testing 3',
				'max_option_value' => 5
		) );
			
		$rating_item_id3 = $wpdb->insert_id;
		
		$rating_items = Multi_Rating_API::get_rating_items();
		
		$this->assertEquals( 3, count( $rating_items ) );

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

