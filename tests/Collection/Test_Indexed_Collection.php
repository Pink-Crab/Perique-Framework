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

use stdClass;
use WP_UnitTestCase;
use OutOfRangeException;
use PinkCrab\Core\Tests\Fixtures\Mock_Objects\Indexed_Collection;

class Test_Indexed_Collection_Trait extends WP_UnitTestCase {

	/**
	 * Test that has() can be used to check if a key ecists.
	 *
	 * @return void
	 */
	public function test_can_check_if_collection_has_index(): void {
		$collection = new Indexed_Collection(
			array(
				'a' => 1,
				'b' => 2,
			)
		);
		$this->assertTrue( $collection->has( 'b' ) );
		$this->assertFalse( $collection->has( 'f' ) );
	}

	/**
	 * Test can get value using index value.
	 *
	 * @return void
	 */
	public function test_can_find_value_key_index(): void {
		$collection = new Indexed_Collection(
			array(
				'a' => 1,
				'b' => 2,
			)
		);
		$this->assertEquals( 2, $collection->get( 'b' ) );
	}

	/**
	 * Test that OutOfRange is thrown is key isnt set.
	 *
	 * @return void
	 */
	public function test_throws_exception_if_keys_inst_set(): void {
		$this->expectException( OutOfRangeException::class );
		$collection = new Indexed_Collection(
			array(
				'a' => 1,
				'b' => 2,
			)
		);
		$collection->get( 'c' );
	}

	/**
	 * Test a value can be set at an index and overwrite at index.
	 *
	 * @return void
	 */
	public function test_can_set_at_index(): void {
		$collection = new Indexed_Collection(
			array(
				'a' => 1,
				'b' => 2,
			)
		);
		$collection->set( 'c', 3 );
		$this->assertEquals( 3, $collection->get( 'c' ) );

		// Test can overwrite.
		$collection->set( 'c', 4 );
		$this->assertEquals( 4, $collection->get( 'c' ) );
	}

	/**
	 * Test can find first instance of a value in the collection.
	 *
	 * @return void
	 */
	public function test_can_find_by_value(): void {
		$collection = new Indexed_Collection(
			array(
				'a' => 1,
				'b' => 2,
				'c' => 1,
				'd' => 1,
			)
		);

		$this->assertEquals( 'a', $collection->find( 1 ) );

		// Usuing objects.
		$obj_a = new class(){
			public $property = 'value';
		};
		$obj_b = (object) array( 'property' => 'value' );

		$collection_objects = new Indexed_Collection(
			array(
				'a' => $obj_a,
				'b' => $obj_b,
				'c' => $obj_a,
			)
		);

		$this->assertEquals( 'a', $collection_objects->find( $obj_a ) );
		$this->assertEquals( 'b', $collection_objects->find( $obj_b ) );

		// Should fail as not same instance.
		$this->assertFalse( $collection_objects->find( (object) array( 'property' => 'value' ) ) );
	}

	/**
	 * Test a value can be removed and returned.
	 *
	 * @return void
	 */
	public function test_can_remove_by_index(): void {
		$collection = new Indexed_Collection(
			array(
				'a' => 1,
				'b' => 2,
				'c' => 3,
				'd' => 4,
			)
		);
		$this->assertEquals( 3, $collection->remove( 'c' ) );
		$this->assertFalse( $collection->has( 'c' ) );
	}

    /**
	 * Test that OutOfRange is thrown is key isnt set.
	 *
	 * @return void
	 */
	public function test_throws_exception_if_keys_isnt_set_for_remove(): void {
		$this->expectException( OutOfRangeException::class );
		$collection = new Indexed_Collection(
			array(
				'a' => 1,
				'b' => 2,
			)
		);
		$collection->remove( 'c' );
	}
}
