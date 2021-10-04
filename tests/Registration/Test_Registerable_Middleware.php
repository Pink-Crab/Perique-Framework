<?php

declare(strict_types=1);

/**
 * Test for Hookable Middleware
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
use PinkCrab\Perique\Tests\Fixtures\Mock_Objects\Hookable\Hookable_Mock;
use PinkCrab\Perique\Services\Registration\Middleware\Hookable_Middleware;

class Test_Hookable_Middleware extends WP_UnitTestCase {

	/** @testdox Hookable classes must have access to the current loader, for them to register all filter and action hooks. */
	public function test_can_be_constructed_with_loader(): void {
		$loader   = new Hook_Loader();
		$hookable = new Hookable_Middleware( $loader );
		$hookable->set_hook_loader( $loader );

		$this->assertSame( $loader, Objects::get_property( $hookable, 'loader' ) );
	}

	/** @testdox When processes only classes which implement the Hookable class will be passed the loader for subscribing all hook calls. */
	public function test_only_processes_classes_that_implement_hookable(): void {
		$loader   = new Hook_Loader();
		$hookable = new Hookable_Middleware( $loader );
		$hookable->set_hook_loader( $loader );

		// Process hookable class
		$hookable->process( new Hookable_Mock() );

		// Process none hookable class
		$hookable->process( new Sample_Class() );

		// Should only be the Hookable_Mock hook added.
		$hooks = Objects::get_property( $loader, 'hooks' );
		$this->assertCount( 1, $hooks );

		$this->assertEquals( 'Hookable_Mock', $hooks->pop()->get_handle() );
	}
}
