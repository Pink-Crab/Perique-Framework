<?php

declare(strict_types=1);

/**
 * Test for Registerable Middleware
 *
 * @since 0.4.0
 * @author Glynn Quelch <glynn.quelch@gmail.com>
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 * @package PinkCrab\Perique
 */

namespace PinkCrab\Perique\Tests\Registration;

use WP_UnitTestCase;
use PinkCrab\Loader\Hook_Loader;
use Gin0115\WPUnit_Helpers\Objects;
use PinkCrab\Perique\Tests\Fixtures\Mock_Objects\Sample_Class;
use PinkCrab\Perique\Services\Registration\Middleware\Registerable_Middleware;
use PinkCrab\Perique\Tests\Fixtures\Mock_Objects\Registerable\Registerable_Mock;

class Test_Registerable_Middleware extends WP_UnitTestCase {

	/** @testdox Registerable classes must have access to the current loader, for them to register all filter and action hooks. */
	public function test_can_be_constructed_with_loader(): void {
		$loader       = new Hook_Loader();
		$registerable = new Registerable_Middleware( $loader );

		$this->assertSame( $loader, Objects::get_property( $registerable, 'loader' ) );
	}

	/** @testdox When processes only classes which implement the Registerable class will be passed the loader for subscribing all hook calls. */
	public function test_only_processes_classes_that_implement_registerable(): void {
		$loader       = new Hook_Loader();
		$registerable = new Registerable_Middleware( $loader );

		// Process registerable class
		$registerable->process( new Registerable_Mock() );

		// Process none registerable class
		$registerable->process( new Sample_Class() );

		// Should only be the Registerable_Mock hook added.
		$hooks = Objects::get_property( $loader, 'hooks' );
		$this->assertCount( 1, $hooks );

		$this->assertEquals( 'Registerable_Mock', $hooks->pop()->get_handle() );
	}
}
