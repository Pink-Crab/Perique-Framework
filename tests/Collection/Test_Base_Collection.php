<?php

declare(strict_types=1);
/**
 * Base collection tests.
 *
 * @since 0.1.0
 * @author Glynn Quelch <glynn.quelch@gmail.com>
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 * @package PinkCrab\Core
 */

namespace PinkCrab\Core\Tests\Collection;

use TypeError;
use WP_UnitTestCase;
use UnderflowException;
use PinkCrab\Core\Collection\Collection;
use PinkCrab\Core\Tests\Fixtures\Mock_Objects\Sample_Class;

class Test_Base_Collection extends WP_UnitTestCase {

	/**
	 * Test that a collection can be constructed with an array.
	 * Using either constructor or late static binding.
	 *
	 * @return void
	 */
	public function test_can_populate_from_array(): void {

		$inital_data = array( 1, 2, 3, 4 );

		// Using constructor.
		$collection = new Collection( $inital_data );
		$this->assertSame( $inital_data, $collection->to_array() );

		// Using Collection::from()
		$this->assertSame( $inital_data, Collection::from( $inital_data )->to_array() );
	}

	/**
	 * Test that a callback and be applied to the collection
	 *
	 * @return void
	 */
	public function test_can_apply_callback_to_collection(): void {

		$inital_data = array( 1, 2, 3, 4 );
		$collection  = Collection::from( $inital_data );

		$modified_collection = $collection->apply(
			static function( $e ) {
				return $e + 1;
			}
		);

		// Ensure the callback is applied to the initial data
		// Not just the a new collection.
		$this->assertEquals( 2, $collection->to_array()[0] );
		$this->assertEquals( 2, $modified_collection->to_array()[0] );
		$this->assertEquals( 3, $collection->to_array()[1] );
		$this->assertEquals( 3, $modified_collection->to_array()[1] );
		$this->assertEquals( 4, $collection->to_array()[2] );
		$this->assertEquals( 4, $modified_collection->to_array()[2] );
		$this->assertEquals( 5, $collection->to_array()[3] );
		$this->assertEquals( 5, $modified_collection->to_array()[3] );
	}

	/**
	 * Test that a callback and be applied to the collection
	 *
	 * @return void
	 */
	public function test_map_creates_new_collection(): void {

		$inital_data = array( 1, 2, 3, 4 );
		$collection  = Collection::from( $inital_data );

		$modified_collection = $collection->map(
			static function( $e ) {
				return $e + 1;
			}
		);

		// Ensure the callback is applied to the initial data
		// And a new collection is issued (inital collection should be unchanged.)
		$this->assertEquals( 1, $collection->to_array()[0] );
		$this->assertEquals( 2, $modified_collection->to_array()[0] );

		$this->assertEquals( 2, $collection->to_array()[1] );
		$this->assertEquals( 3, $modified_collection->to_array()[1] );

		$this->assertEquals( 3, $collection->to_array()[2] );
		$this->assertEquals( 4, $modified_collection->to_array()[2] );

		$this->assertEquals( 4, $collection->to_array()[3] );
		$this->assertEquals( 5, $modified_collection->to_array()[3] );
	}

	/**
	 * Test that the collection can be filtered.
	 * Is immutable, so should create a new collecion.
	 *
	 * @return void
	 */
	public function test_can_use_filter(): void {
		$inital_data = array( 1, 2, 3, 4, 5, 6, 7, 8, 9, 10 );
		$collection  = Collection::from( $inital_data );

		$modified_collection = $collection->filter(
			static function( $e ) {
				return $e % 2 === 0;
			}
		);

		// Ensure the inital collection remains in tact
		$this->assertCount( 10, $collection->to_array() );
		// Check the filtered array contains 5 (even).
		$this->assertCount( 5, $modified_collection->to_array() );
	}

	/**
	 * Test that apply can be applied to the collection and
	 * the current data is edited.
	 *
	 * @return void
	 */
	public function test_can_use_apply(): void {
		$inital_data = array( 1, 2, 3, 4 );
		$collection  = Collection::from( $inital_data );

		$collection->apply(
			static function( $e ) {
				return $e + 1;
			}
		);

		$this->assertEquals( 2, $collection->to_array()[0] );
		$this->assertEquals( 3, $collection->to_array()[1] );
		$this->assertEquals( 4, $collection->to_array()[2] );
		$this->assertEquals( 5, $collection->to_array()[3] );
	}

	/**
	 * Test that each() can be used without effected the iniital
	 * data.
	 *
	 * @return voic
	 */
	public function test_can_use_each(): void {
		$inital_data = array( 1, 2, 3, 4 );
		$collection  = Collection::from( $inital_data );

		ob_start();
		$collection->each(
			static function( $value, $key ) {
				echo $value;
				return 999;
			}
		);
		$output = ob_get_contents();
		ob_end_clean();

		$this->assertEquals( '1234', $output );
		$this->assertEquals( 1, $collection->to_array()[0] );
		$this->assertEquals( 2, $collection->to_array()[1] );
		$this->assertEquals( 3, $collection->to_array()[2] );
		$this->assertEquals( 4, $collection->to_array()[3] );
	}

	public function test_can_use_reduce(): void {
		$inital_data = array( 1, 2, 3, 4 );
		$collection  = Collection::from( $inital_data );

		$result = $collection->reduce(
			static function( $carry, $value ) {
				$carry .= ( $value * 2 );
				return $carry;
			},
			''
		);

		$this->assertEquals( '2468', $result );
	}

	/**
	 * Test an array can be merged into a collection.
	 * Should return a new instance of the collection.
	 *
	 * @return void
	 */
	public function test_can_merge_with_array(): void {
		$inital_data = array( 1, 2, 3, 4 );
		$collection  = Collection::from( $inital_data );

		$new_collection = $collection->merge( array( 5, 6, 7, 8, 9, 10 ) );
		$this->assertEquals( 1, $new_collection->to_array()[0] );
		$this->assertEquals( 2, $new_collection->to_array()[1] );
		$this->assertEquals( 3, $new_collection->to_array()[2] );
		$this->assertEquals( 4, $new_collection->to_array()[3] );
		$this->assertEquals( 5, $new_collection->to_array()[4] );
		$this->assertEquals( 6, $new_collection->to_array()[5] );
		$this->assertEquals( 7, $new_collection->to_array()[6] );
		$this->assertEquals( 8, $new_collection->to_array()[7] );
		$this->assertEquals( 9, $new_collection->to_array()[8] );
		$this->assertEquals( 10, $new_collection->to_array()[9] );

		// Check inital collection as is.
		$this->assertArrayNotHasKey( 5, $collection->to_array() );
	}

	 /**
	 * Test an array can be merged into a collection.
	 * Should return a new instance of the collection.
	 *
	 * @return void
	 */
	public function test_can_merge_with_collection(): void {
		$inital_data      = array( 1, 2, 3, 4 );
		$collection       = Collection::from( $inital_data );
		$merge_collection = Collection::from( array( 5, 6, 7, 8, 9, 10 ) );

		$new_collection = $collection->merge( $merge_collection );
		$this->assertEquals( 1, $new_collection->to_array()[0] );
		$this->assertEquals( 2, $new_collection->to_array()[1] );
		$this->assertEquals( 3, $new_collection->to_array()[2] );
		$this->assertEquals( 4, $new_collection->to_array()[3] );
		$this->assertEquals( 5, $new_collection->to_array()[4] );
		$this->assertEquals( 6, $new_collection->to_array()[5] );
		$this->assertEquals( 7, $new_collection->to_array()[6] );
		$this->assertEquals( 8, $new_collection->to_array()[7] );
		$this->assertEquals( 9, $new_collection->to_array()[8] );
		$this->assertEquals( 10, $new_collection->to_array()[9] );

		// Check inital collection as is.
		$this->assertArrayNotHasKey( 5, $collection->to_array() );
	}

	/**
	 * Test TypeError is thrown if an invalid type is merged.
	 *
	 * @return void
	 */
	public function test_throws_exception_if_merged_with_incompatible_type(): void {
		$this->expectException( TypeError::class );
		$inital_data = array( 1, 2, 3, 4 );
		$collection  = Collection::from( $inital_data );
		$collection->merge( (object) array( 'A1' => 2 ) );
	}

	/**
	 * Test can push an item to the tail of a collection
	 *
	 * @return void
	 */
	public function test_can_push_to_collection(): void {
		$inital_data = array( 1, 2, 3, 4 );
		$collection  = Collection::from( $inital_data );

		$collection->push( 5 );
		$this->assertEquals( 5, $collection->to_array()[4] );

		$collection->push( 10 );
		$this->assertEquals( 10, $collection->to_array()[5] );

		// Test can push multiple values.
		$collection->push( 11, 12, 13 );
		$this->assertEquals( 11, $collection->to_array()[6] );
		$this->assertEquals( 12, $collection->to_array()[7] );
		$this->assertEquals( 13, $collection->to_array()[8] );

	}

	/**
	 * Tests values can be poped off from the tail
	 *
	 * @return void
	 */
	public function test_can_pop_from_tail(): void {
		$inital_data = array( 1, 2, 3, 4 );
		$collection  = Collection::from( $inital_data );

		// Test we can pop and its removed.
		$this->assertEquals( 4, $collection->pop() );
		$this->assertArrayNotHasKey( 3, $collection->to_array() );

		$this->assertEquals( 3, $collection->pop() );
		$this->assertArrayNotHasKey( 2, $collection->to_array() );
	}

	/**
	 * Test that an underflow exception is thrown is poping an empty value.
	 *
	 * @return void
	 */
	public function test_pop_throws_exception_if_empty(): void {
		$this->expectException( UnderflowException::class );
		$collection = Collection::from( array() );
		$collection->pop();
	}

	/**
	 * Test that shift can be used to add an item to the head
	 *
	 * @return void
	 */
	public function test_can_add_to_head(): void {
		$inital_data = array( 1, 2, 3, 4 );
		$collection  = Collection::from( $inital_data );

		$collection->unshift( 0 );
		$this->assertEquals( 0, $collection->to_array()[0] );

		$collection->unshift( 0.5 );
		$this->assertEquals( 0.5, $collection->to_array()[0] );

		$collection->unshift( 0.4, 0.3, 0.2 );
		$this->assertEquals( 0.4, $collection->to_array()[2] );
		$this->assertEquals( 0.3, $collection->to_array()[1] );
		$this->assertEquals( 0.2, $collection->to_array()[0] );

	}

	/**
	 * Tests values can be poped off from the tail
	 *
	 * @return void
	 */
	public function test_can_shift_from_tail(): void {
		$inital_data = array( 1, 2, 3, 4 );
		$collection  = Collection::from( $inital_data );

		// Test we can pop and its removed.
		$this->assertEquals( 1, $collection->shift() );
		$this->assertCount( 3, $collection->to_array() );

		$this->assertEquals( 2, $collection->shift() );
		$this->assertCount( 2, $collection->to_array() );
	}

	/**
	 * Test that an underflow exception is thrown is shifting an empty value.
	 *
	 * @return void
	 */
	public function test_shift_throws_exception_if_empty(): void {
		$this->expectException( UnderflowException::class );
		$collection = Collection::from( array() );
		$collection->shift();
	}

	/**
	 * Test can use is_empty()
	 *
	 * @return void
	 */
	public function test_can_check_if_empty(): void {
		$collection = Collection::from( array() );
		$this->assertTrue( $collection->is_empty() );

		$collection->push( 1 );
		$this->assertFalse( $collection->is_empty() );
	}

	/**
	 * Test contains can be called.
	 *
	 * @return void
	 */
	public function test_contains(): void {
		$inital_data = array( 1, 2, 3, 4 );
		$collection  = Collection::from( $inital_data );

		$this->assertTrue( $collection->contains( 1, 3, 2 ) );
		$this->assertFalse( $collection->contains( 1, 3, 5 ) );

		// Test can be used with MD array.
		$collection2 = Collection::from(
			array(
				array( 'name' => 'james' ),
				array( 'name' => 'sam' ),
			)
		);
		$this->assertTrue( $collection2->contains( array( 'name' => 'james' ) ) );

		// Test can be used with an array of objects.
		$a           = ( new Sample_Class() )->set_property_a( '3' );
		$collection3 = Collection::from(
			array(
				( new Sample_Class() )->set_property_a( '1' ),
				( new Sample_Class() )->set_property_a( '2' ),
				$a,
			)
		);
		$this->assertTrue( $collection3->contains( $a ) );

		// This will fail as its a different instance.
		$this->assertFalse( $collection3->contains( ( new Sample_Class() )->set_property_a( '1' ) ) );
	}


	/**
	 * Test the collection can be counted
	 * and that it implements the Countable interface.
	 *
	 * @return void
	 */
	public function test_can_count_contents() {
		$inital_data = array( 1, 2, 3, 4 );
		$collection  = Collection::from( $inital_data );
		$this->assertEquals( 4, $collection->count() );
		// Ensure implements countable.
		$this->assertCount( 4, $collection );
	}

	/**
	 * Test the internal collection can be cleared.
	 *
	 * @return void
	 */
	public function test_can_clear_collection(): void {
		$inital_data = array( 1, 2, 3, 4 );
		$collection  = Collection::from( $inital_data );
		$this->assertEquals( 4, $collection->count() );
		$collection->clear();
		$this->assertEmpty( $collection );
	}

	/**
	 * Test that a collection can be copied.
	 *
	 * @return void
	 */
	public function test_can_copy_collection(): void {
		$inital_collection = Collection::from( array( 1, 2, 3 ) );
		$copy_collection   = $inital_collection->copy();

		$this->assertNotSame(
			$inital_collection,
			$copy_collection
		);
		$this->assertSame(
			$inital_collection->to_array(),
			$copy_collection->to_array()
		);
	}

	/**
	 * Test that an existing collection can be sorted, with or without
	 * the use of a comparator callback.
	 *
	 * @return void
	 */
	public function test_can_sort_collection(): void {

		// Sort naturally (no callable)
		$nat_sorted_collection = Collection::from( array( 'a', 'z', 'f', 'y', 'o' ) );
		$nat_sorted_collection->sort();

		$this->assertEquals( 'a', $nat_sorted_collection->shift() );
		$this->assertEquals( 'f', $nat_sorted_collection->shift() );
		$this->assertEquals( 'o', $nat_sorted_collection->shift() );
		$this->assertEquals( 'y', $nat_sorted_collection->shift() );
		$this->assertEquals( 'z', $nat_sorted_collection->shift() );

		// Sort in reverse with callable.
		$revsersed_collection = Collection::from( array( 'a', 'z', 'f', 'y', 'o' ) );
		$revsersed_collection->sort(
			function( $a, $b ) {
				return $b <=> $a;
			}
		);
		$this->assertEquals( 'z', $revsersed_collection->shift() );
		$this->assertEquals( 'y', $revsersed_collection->shift() );
		$this->assertEquals( 'o', $revsersed_collection->shift() );
		$this->assertEquals( 'f', $revsersed_collection->shift() );
		$this->assertEquals( 'a', $revsersed_collection->shift() );

	}

	/**
	 * Test that sorted sorts the array as a new isntance.
	 *
	 * @return void
	 */
	public function test_can_sorted_collection(): void {

		// Sort naturally (no callable)
		$collection            = Collection::from( array( 'a', 'z', 'f', 'y', 'o' ) );
		$nat_sorted_collection = $collection->sorted();

		$this->assertEquals( 'a', $nat_sorted_collection->shift() );
		$this->assertEquals( 'f', $nat_sorted_collection->shift() );
		$this->assertEquals( 'o', $nat_sorted_collection->shift() );
		$this->assertEquals( 'y', $nat_sorted_collection->shift() );
		$this->assertEquals( 'z', $nat_sorted_collection->shift() );

		// Ensure the initial collection is still unsorted.
		$this->assertEquals( 'a', $collection->shift() );
		$this->assertEquals( 'z', $collection->shift() );
		$this->assertEquals( 'f', $collection->shift() );
		$this->assertEquals( 'y', $collection->shift() );
		$this->assertEquals( 'o', $collection->shift() );
	}

	public function test_can_slice(): void {

		$collection = Collection::from( array( 1, 2, 3, 4, 5, 6, 7, 8, 9, 10 ) );

		// Remove last 3.
		$last_3 = $collection->slice( -3 );
		$this->assertEquals( 8, $last_3->to_array()[0] );
		$this->assertEquals( 9, $last_3->to_array()[1] );
		$this->assertEquals( 10, $last_3->to_array()[2] );

		// Get 3rd & 4th.
		$third_4th = $collection->slice( 2, 2 );
		$this->assertEquals( 3, $third_4th->to_array()[0] );
		$this->assertEquals( 4, $third_4th->to_array()[1] );

		// Check inital array has not been changed.
		$this->assertEquals( 2, $collection->to_array()[1] );
		$this->assertEquals( 4, $collection->to_array()[3] );
		$this->assertEquals( 10, $collection->to_array()[9] );

	}

	/**
	 * Test that the difference between 2 arrays can be worked out.
	 *
	 * @return void
	 */
	public function test_can_use_diff(): void {
		$collection = Collection::from( array( 1, 2, 3, 4, 5, 6, 7, 8, 9, 10 ) );

		// Diff using array.
		$diff_array = $collection->diff( array( 1, 2, 3, 4, 5, 6, 7, 8, 9 ) );
		$this->assertCount( 1, $diff_array );
		$this->assertEquals( 10, $diff_array->shift() );

		// Using Collection.
		$diff_colection = $collection->diff( Collection::from( array( 2, 3, 4, 5, 6, 7, 8, 9, 10 ) ) );
		$this->assertCount( 1, $diff_colection );
		$this->assertEquals( 1, $diff_colection->pop() );
	}

	/**
	 * Test that a TypeError is thrown if none array or collection used with diff.
	 *
	 * @return void
	 */
	public function test_throws_exception_if_diff_used_with_invalid_type(): void {
		$this->expectException( TypeError::class );
		$collection = Collection::from( array( 1, 2, 3, 4, 5, 6, 7, 8, 9, 10 ) );
		$collection->diff( 'IM NOT AN ARRAY OR COLLECTION' );
	}

	/**
	 * Test that Intersect can be used with either array or collection.
	 *
	 * @return void
	 */
	public function test_can_use_intersect(): void {
		$collection = Collection::from( array( 1, 2, 3, 4, 5, 6, 7, 8, 9, 10 ) );

		// Diff using array.
		$using_array = $collection->intersect( array( 1, 2, 3, 4, 5 ) );
		$this->assertCount( 5, $using_array );
		$this->assertEquals( 1, $using_array->shift() );
		$this->assertEquals( 2, $using_array->shift() );
		$this->assertEquals( 3, $using_array->shift() );
		$this->assertEquals( 4, $using_array->shift() );
		$this->assertEquals( 5, $using_array->shift() );

		// Using Collection.
		$using_colection = $collection->intersect( Collection::from( array( 6, 7, 8, 9, 10 ) ) );
		$this->assertCount( 5, $using_colection );
		$this->assertEquals( 6, $using_colection->shift() );
		$this->assertEquals( 7, $using_colection->shift() );
		$this->assertEquals( 8, $using_colection->shift() );
		$this->assertEquals( 9, $using_colection->shift() );
		$this->assertEquals( 10, $using_colection->shift() );
	}

	/**
	 * Test that a TypeError is thrown if none array or collection used with intersect.
	 *
	 * @return void
	 */
	public function test_throws_exception_if_intersect_used_with_invalid_type(): void {
		$this->expectException( TypeError::class );
		$collection = Collection::from( array( 1, 2, 3, 4, 5, 6, 7, 8, 9, 10 ) );
		$collection->intersect( 'IM NOT AN ARRAY OR COLLECTION' );
	}
}
