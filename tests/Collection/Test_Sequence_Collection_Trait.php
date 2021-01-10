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
use PinkCrab\Core\Tests\Fixtures\Mock_Objects\Sequence_Collection;

class Test_Sequence_Collection_Trait extends WP_UnitTestCase {

	/**
	 * Test that the internal array can be reversed.
	 *
	 * @return void
	 */
	public function test_can_reverse(): void {
		$collection = new Sequence_Collection( array( 1, 2, 3, 4, 5 ) );
		$collection->reverse();
		$this->assertEquals( 5, $collection->to_array()[0] );
		$this->assertEquals( 4, $collection->to_array()[1] );
		$this->assertEquals( 3, $collection->to_array()[2] );
		$this->assertEquals( 2, $collection->to_array()[3] );
		$this->assertEquals( 1, $collection->to_array()[4] );
	}

	/**
	 * Test that a new instance is created with contentes reversed when using
	 * reversed()
	 *
	 * @return void
	 */
	public function test_can_reversed(): void {
		$collection = new Sequence_Collection( array( 1, 2, 3, 4, 5 ) );

		$reversed = $collection->reversed();
		$this->assertEquals( 5, $reversed->to_array()[0] );
		$this->assertEquals( 4, $reversed->to_array()[1] );
		$this->assertEquals( 3, $reversed->to_array()[2] );
		$this->assertEquals( 2, $reversed->to_array()[3] );
		$this->assertEquals( 1, $reversed->to_array()[4] );

		// Check inital collection remains untouched.
		$this->assertEquals( 2, $collection->to_array()[1] );
	}

	/**
	 * Test that the collection can be rotated in either direction.
	 *
	 * @return void
	 */
	public function test_can_rotate(): void {
		$collection = new Sequence_Collection( array( 1, 2, 3, 4, 5 ) );
		$this->assertEquals( 1, $collection->to_array()[0] );
		$collection->rotate( 1 );
		$this->assertEquals( 2, $collection->to_array()[0] );
		$collection->rotate( 1 );
		$this->assertEquals( 3, $collection->to_array()[0] );

		// In reverse.
		$collection->rotate( -2 );
		$this->assertEquals( 1, $collection->to_array()[0] );

		// No change if 0 passed.
		$collection->rotate( 0 );
		$this->assertEquals( 1, $collection->to_array()[0] );
	}

	/**
	 * Test that the first value can be got from collection.
	 *
	 * @return void
	 */
	public function test_can_get_first_value() {
		$collection = new Sequence_Collection( array( 1, 2, 3, 4, 5 ) );
		$this->assertEquals( 1, $collection->first() );
	}

	/**
	 * Test thors UnderflowException if empty array on first().
	 *
	 * @return void
	 */
	public function test_throws_if_getting_first_of_empty_collection(): void {
		$this->expectException( UnderflowException::class );
		$collection = Sequence_Collection::from( array() );
		$collection->first();
	}

	/**
	 * Test that the last value can be got from collection.
	 *
	 * @return void
	 */
	public function test_can_get_last_value() {
		$collection = new Sequence_Collection( array( 1, 2, 3, 4, 5 ) );
		$this->assertEquals( 5, $collection->last() );
	}

	/**
	 * Test thors UnderflowException if empty array on last().
	 *
	 * @return void
	 */
	public function test_throws_if_getting_last_of_empty_collection(): void {
		$this->expectException( UnderflowException::class );
		$collection = Sequence_Collection::from( array() );
		$collection->last();
	}

	/**
	 * Test can do sum of collection contentws.
	 *
	 * @return void
	 */
	public function test_can_sum_collection(): void {
		$collection = new Sequence_Collection( array( 1, 2, 3, 4, 5 ) );
		$this->assertEquals( 15, $collection->sum() );
	}

    /**
     * Test that sum ignore none
     *
     * @return void
     */
	public function test_ignores_none_numerical_values(): void {
		$collection = new Sequence_Collection( array( 1, 'tree', '2', 5, 9.9 ) );
		$this->assertEquals( 17.9, $collection->sum() );
	}

    /**
	 * Function can join a collection
	 *
	 * @return void
	 */
	public function test_can_join_collection_to_string(): void {
		$inital_data = array( 1, 2, 3, 4 );
		$collection  = Collection::from( $inital_data );

		$this->assertEquals( '1234', $collection->join() );
		$this->assertEquals( '1-2-3-4', $collection->join( '-' ) );

	}
}
