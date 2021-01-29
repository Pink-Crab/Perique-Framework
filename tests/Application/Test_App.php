<?php

declare(strict_types=1);
/**
 * Main App Container Test.
 *
 * @since 0.1.0
 * @author Glynn Quelch <glynn.quelch@gmail.com>
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 * @package PinkCrab\Core
 */

namespace PinkCrab\Core\Tests\Application;

use Exception;
use WP_UnitTestCase;
use OutOfBoundsException;
use PinkCrab\Core\Application\App;
use PinkCrab\PHPUnit_Helpers\Reflection;
use PinkCrab\Core\Interfaces\Service_Container;
use PinkCrab\Core\Services\ServiceContainer\Container;
use PinkCrab\Core\Tests\Fixtures\Mock_Objects\Sample_Class;
use PinkCrab\Core\Tests\Fixtures\Mock_Objects\Parent_Dependency;
use PinkCrab\Core\Services\ServiceContainer\ServiceNotRegisteredException;


class Test_App extends WP_UnitTestCase {

	protected $app;

	public function setup() {
		$serviceContainer = new Container();
		$this->app        = App::init( $serviceContainer );
	}

	/**
	 * Test that singleton instance and container are both set on construct
	 * Obviously messy as singleton. But creates, unsets internal instance, recreates and checks contents.
	 * Uses reflection.
	 * 
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 * @backupStaticAttributes disabled
	 * @return void
	 */
	public function test_properties_set(): void {
		$app = App::init( new Container() );
		Reflection::set_private_static_property( $app, 'instance', null );
		$this->assertNull( Reflection::get_private_static_property( $app, 'instance' ) );
		$app::init(new Container() );
		$this->assertInstanceOf( App::class, Reflection::get_private_static_property( $app, 'instance' ) );
		$this->assertInstanceOf( Container::class, Reflection::get_private_property( $app, 'service_container' ) );
	}

	/**
	 * Ensure core is loaded.
	 *
	 * @test
	 */
	function test_core_included() {
		$this->assertTrue( class_exists( App::class ) );
	}

	/**
	 * Test that you can bind services
	 *
	 * @return void
	 */
	public function test_can_bind_service(): void {
		$this->app->set(
			'Test2',
			(object) array(
				'key1' => 1,
				'key2' => 2,
				'key3' => 3,
			)
		);
		$this->assertEquals( 2, $this->app->get( 'Test2' )->key2 );

		$this->app->set(
			'Test3',
			(object) array(
				'key1' => 1,
				'key2' => 2,
				'key3' => 3,
			)
		);
		$this->assertEquals( 3, App::retreive( 'Test3' )->key3 );
	}

	/**
	 * Test that container exceptions bubble up when calling undefined key
	 *
	 * @return void
	 */
	public function test_throws_container_exception_for_unbound_key(): void {
		$this->expectException( OutOfBoundsException::class );
		App::retreive( 'UNBOUND_KEY' );
	}

	/**
	 * Test that container exceptions bubble up when trying to rebind a
	 * previsouly bound value
	 *
	 * @return void
	 */
	public function test_throws_container_exception_for_attempting_dual_key_bin(): void {
		$this->expectException( Exception::class );
		// First attempt
		$this->app->set( 'foo', (object) array( 'key1' => 1 ) );
		// Second, should throw.
		$this->app->set( 'foo', (object) array( 'key1' => 1 ) );
	}

	/**
	 * Test we can create instance using Dice from App.
	 *
	 * @return void
	 */
	public function test_can_use_make_static_method_to_use_di_container() {
		// Test can make a simple class using container.
		$sample = App::make( Sample_Class::class );
		$this->assertInstanceOf( Sample_Class::class, $sample );

		// Test can create a class with nested dependencies.
		$nested = App::make( Parent_Dependency::class );
		$this->assertInstanceOf( Sample_Class::class, $nested->get_sample_class() );
	}

	/**
	 * Check that App:config() calls out the passed
	 *
	 * @return void
	 */
	public function test_can_use_config_helper(): void {
		$namespace = App::config( 'namespace', 'rest' );
		$this->assertTrue( is_string( $namespace ) );
	}

	/**
	 * Test the __callStatic can be used.
	 *
	 * @return void
	 */
	public function test_can_use_callstatic_for_services(): void {
		$this->app->set( 'test_call_static', (object) array( 'key1' => 'yes' ) );
		$this->assertTrue( is_object( $this->app::test_call_static() ) );
	}
}
