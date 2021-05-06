<?php

declare(strict_types=1);
/**
 * Tests for a typed collection
 *
 * @since 0.5.0
 * @author Glynn Quelch <glynn.quelch@gmail.com>
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 * @package PinkCrab\Core
 */

namespace PinkCrab\Core\Tests\Collection;

use WP_UnitTestCase;
use PinkCrab\Core\Tests\Fixtures\Mock_Objects\Sample_Class;
use PinkCrab\Core\Tests\Fixtures\Mock_Objects\Parent_Dependency;
use PinkCrab\Core\Tests\Fixtures\Mock_Objects\Sample_Class_Collection;

class Test_Typed_Collection extends WP_UnitTestCase {

	/** @testdox It should only be possible to pass valid class to a typed collection. */
	public function test_only_populated_with_simple_classes(): void {
		$collection = new Sample_Class_Collection(
			array(
				$this->createMock( Sample_Class::class ),
				$this->createMock( Sample_Class::class ),
				$this->createMock( Parent_Dependency::class ),
			)
		);

		// Should only have 2 classes
		$this->assertCount( 2, $collection );

		// Check all Sample_Class types.
		foreach ( $collection->to_array() as $class ) {
			$this->assertInstanceOf( Sample_Class::class, $class );
		}
	}

	/** @testdox It should not be possible to push none typed data to a typed collection. */
    public function test_can_only_push_valid_types_to_typed_collection(): void {
		$collection = new Sample_Class_Collection();

		$collection->push( $this->createMock( Sample_Class::class ) );
		$collection->push( $this->createMock( Parent_Dependency::class ) );

		// Should only have 2 classes
		$this->assertCount( 1, $collection );

		// Check all Sample_Class types.
		foreach ( $collection->to_array() as $class ) {
			$this->assertInstanceOf( Sample_Class::class, $class );
		}
	}

    /** @testdox It should not be possible to unshift none typed data to a typed collection. */
    public function test_can_only_unshift_valid_types_to_typed_collection(): void {
		$collection = new Sample_Class_Collection();

		$collection->unshift( $this->createMock( Sample_Class::class ) );
		$collection->unshift( $this->createMock( Parent_Dependency::class ) );

		// Should only have 2 classes
		$this->assertCount( 1, $collection );

		// Check all Sample_Class types.
		foreach ( $collection->to_array() as $class ) {
			$this->assertInstanceOf( Sample_Class::class, $class );
		}
	}
}
