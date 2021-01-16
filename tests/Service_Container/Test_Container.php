<?php

declare(strict_types=1);

/**
 * Tests the container used to hold the app.
 *
 * @since 0.3.1
 * @author Glynn Quelch <glynn.quelch@gmail.com>
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 * @package PinkCrab\Core
 */

namespace PinkCrab\Core\Tests\Registration;

use Exception;
use WP_UnitTestCase;
use PinkCrab\Core\Services\ServiceContainer\Container;
use PinkCrab\Core\Services\ServiceContainer\ServiceNotRegisteredException;



class Test_Container extends WP_UnitTestCase {

	/**
	 * Test can set(), get() and has()
	 *
	 * @return void
	 */
	public function test_has_get_set_runner(): void {
		$container = new Container();

		$service = (object) array( 'bar' => 'baz' );

		// Set
		$container->set( 'foo', $service );

		// Has
		$this->assertTrue( $container->has( 'foo' ) );
		$this->assertFalse( $container->has( 'bar' ) );

		// Get
		$this->assertSame( $service, $container->get( 'foo' ) );
	}

	/**
	 * Prevent overwriting.
	 *
	 * @return void
	 */
	public function test_throws_exception_if_trying_overwrite(): void {
		$this->expectException( Exception::class );

		$container = new Container();
		$service   = (object) array( 'bar' => 'baz' );
		$container->set( 'foo', $service );
		$container->set( 'foo', $service );
	}

	/**
	 * Throw ServiceNotRegisteredException if key not set.
	 *
	 * @return void
	 */
	public function test_exception_thrown_if_getting_unser(): void {
		$this->expectException( ServiceNotRegisteredException::class );

		$container = new Container();
		$container->get( 'foo' );
	}
}
