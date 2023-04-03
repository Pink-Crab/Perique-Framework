<?php

declare(strict_types=1);

/**
 * Unit Tests for the module manager.
 *
 * @since 2.0.0
 * @author Glynn Quelch <glynn.quelch@gmail.com>
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 * @package PinkCrab\Perique
 */

namespace PinkCrab\Perique\Tests\Unit\Modules;

use Dice\Dice;
use WP_UnitTestCase;
use PinkCrab\Loader\Hook_Loader;
use PinkCrab\Perique\Services\View\PHP_Engine;
use PinkCrab\Perique\Services\Dice\PinkCrab_Dice;
use PinkCrab\Perique\Exceptions\Module_Manager_Exception;
use PinkCrab\Perique\Services\Registration\Module_Manager;
use PinkCrab\Perique\Services\Registration\Registration_Service;
use PinkCrab\Perique\Tests\Fixtures\Modules\Invalid\With_None_Class_Middleware;
use PinkCrab\Perique\Tests\Fixtures\Modules\Invalid\With_Invalid_Class_Middleware;
use PinkCrab\Perique\Tests\Fixtures\Modules\With_Middleware\Module_With_Middleware__Module;
use PinkCrab\Perique\Tests\Fixtures\Modules\With_Middleware\Module_With_Middleware__Middleware;
use PinkCrab\Perique\Tests\Fixtures\Modules\Without_Middleware\Module_Without_Middleware__Module;

/**
 * @group unit
 * @group registration
 * @group module
 */
class Test_Module_Manager_Exceptions extends WP_UnitTestCase {


	/** @testdox Attempting to pass a class which is not a Module to the module manager, should result in Module_Manager_Exception being thrown */
	public function test_throws_exception_if_not_module(): void {
		$this->expectException( Module_Manager_Exception::class );
		$this->expectExceptionCode( 20 );
		$this->expectExceptionMessage( 'stdClass must be an instance of the Module interface' );

		$module_manager = new Module_Manager(
			$this->createMock( PinkCrab_Dice::class ),
			$this->createMock( Registration_Service::class )
		);

		$module_manager->push_module( 'stdClass' );
		$module_manager->register_modules();
	}

	/** @testdox When a module is create by the container, if it doesn't create an object an Module_Manager_Exception should be thrown */
	public function test_throws_exception_if_module_not_created(): void {

		$module = Module_Without_Middleware__Module::class;

		$this->expectException( Module_Manager_Exception::class );
		$this->expectExceptionCode( 20 );
		$this->expectExceptionMessage( $module . ' must be an instance of the Module interface' );

		// Create a mock of the container, and force it to return null.
		$container = $this->createMock( PinkCrab_Dice::class );
		$container->method( 'create' )->willReturn( null );

		$module_manager = new Module_Manager(
			$container,
			$this->createMock( Registration_Service::class )
		);
		$module_manager->push_module( Module_Without_Middleware__Module::class );
		$module_manager->register_modules();
	}

	/** @testdox When a module is create by the container, if it create an object that is not an isntance of Module a Module_Manager_Exception should be thrown */
	public function test_throws_exception_if_module_not_instance_of_module(): void {

		$module = Module_Without_Middleware__Module::class;

		$this->expectException( Module_Manager_Exception::class );
		$this->expectExceptionCode( 20 );
		$this->expectExceptionMessage( $module . ' must be an instance of the Module interface' );

		// Create a mock of the container, and force it to return a stdClass.
		$container = $this->createMock( PinkCrab_Dice::class );
		$container->method( 'create' )->willReturn( new \stdClass() );

		$module_manager = new Module_Manager(
			$container,
			$this->createMock( Registration_Service::class )
		);
		$module_manager->push_module( Module_Without_Middleware__Module::class );
		$module_manager->register_modules();
	}

	/** @testdox It the created middleware, results in a class which is not a class which implements Registration_Middleware and exception will be thrown. */
	public function test_throws_exception_if_middleware_not_instance_of_registration_middleware(): void {

		$module = Module_With_Middleware__Module::class;

		$this->expectException( Module_Manager_Exception::class );
		$this->expectExceptionCode( 21 );
		$this->expectExceptionMessage( 'Failed to create Registration_Middleware, invalid instance created. Created: stdClass' );

		// Create a mock of the container, and force it to return a stdClass.
		$container = $this->createMock( PinkCrab_Dice::class );
		$container->method( 'create' )->will(
			$this->returnCallback(
				function( $object ) {
					// Create actual module.
					if ( $object === Module_With_Middleware__Module::class ) {
						return new Module_With_Middleware__Module();
					}

					if ( $object === Module_With_Middleware__Middleware::class ) {
						return new \stdClass();
					}
					return $this->createMock( $object );
				}
			)
		);

		$module_manager = new Module_Manager(
			$container,
			$this->createMock( Registration_Service::class )
		);
		$module_manager->push_module( Module_With_Middleware__Module::class );
		$module_manager->register_modules();
	}

	/** @testdox If a module returns a string which is not a valid class string, will result in an exception being thrown. */
	public function test_throws_exception_if_module_returns_invalid_class(): void {

		$this->expectException( Module_Manager_Exception::class );
		$this->expectExceptionCode( 22 );
		$this->expectExceptionMessage( 'None class was returned as the modules Middleware, but this does not implement Registration_Middleware interface' );

		// Create a mock of the container, and force it to return a stdClass.
		$module_manager = new Module_Manager(
			new PinkCrab_Dice( new Dice() ),
			$this->createMock( Registration_Service::class )
		);
		$module_manager->push_module( With_None_Class_Middleware::class );
		$module_manager->register_modules();
	}

	/** @testdox If a module returns a string which is not a valid class instance, will result in an exception being thrown. */
	public function test_throws_exception_if_module_returns_invalid_class_instance(): void {

		$this->expectException( Module_Manager_Exception::class );
		$this->expectExceptionCode( 22 );
		$this->expectExceptionMessage( PHP_Engine::class . ' was returned as the modules Middleware, but this does not implement Registration_Middleware interface' );

		// Create a mock of the container, and force it to return a stdClass.
		$module_manager = new Module_Manager(
			new PinkCrab_Dice( new Dice() ),
			$this->createMock( Registration_Service::class )
		);
		$module_manager->push_module( With_Invalid_Class_Middleware::class );
		$module_manager->register_modules();
	}
}
