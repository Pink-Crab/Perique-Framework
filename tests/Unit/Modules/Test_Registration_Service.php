<?php

declare(strict_types=1);

/**
 * Tests for Registration service.
 *
 * @since 0.4.0
 * @author Glynn Quelch <glynn.quelch@gmail.com>
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 * @package PinkCrab\Perique
 */

namespace PinkCrab\Perique\Tests\Unit\Modules;

use Dice\Dice;
use WP_UnitTestCase;
use PinkCrab\Loader\Hook_Loader;
use Gin0115\WPUnit_Helpers\Objects;
use PinkCrab\Perique\Application\Hooks;
use PinkCrab\Perique\Interfaces\DI_Container;
use PinkCrab\Perique\Services\Dice\PinkCrab_Dice;
use PinkCrab\Perique\Exceptions\Module_Manager_Exception;
use PinkCrab\Perique\Tests\Fixtures\Mock_Objects\Sample_Class;
use PinkCrab\Perique\Services\Registration\Registration_Service;
use PinkCrab\Perique\Tests\Fixtures\Mock_Objects\Parent_Dependency;
use PinkCrab\Perique\Tests\Fixtures\Mock_Objects\Mock_Registration_Middleware;

/**
 * @group unit
 * @group registration
 */
class Test_Registration_Service extends WP_UnitTestCase {

	// /** @testdox The registration service must be populated with a DI Container*/
	// public function test_can_set_di_container(): void {
	// 	$container            = $this->createMock( DI_Container::class );
	// 	$registration_service = new Registration_Service;

	// 	$registration_service->set_container( $container );

	// 	$this->assertSame( $container, Objects::get_property( $registration_service, 'di_container' ) );
	// }

	/** @testdox It should be possible to add as many peices of registration middleware as desired to the registration process. */
	public function test_push_middleware_to_internal_stack(): void {

		// Set middleware to log if actions are called.
		$middleware = new Mock_Registration_Middleware( 'log actions' );

		// Run the service.
		$registration_service = new Registration_Service( $this->createMock( DI_Container::class ) );
		$registration_service->push_middleware( $middleware )
			->push_class(Sample_Class::class)
			->process();

		$log = $middleware->message_log;
		$this->assertCount( 2, $log );
		$this->assertContains( 'setup', $log );
		$this->assertContains( 'tear_down', $log );

	}

	/** @testdox If not classes are pushed to the registration service, then the process will be skipped. */
	public function test_process_skipped_if_no_classes(): void {

		// Set middleware to log if actions are called.
		$middleware = new Mock_Registration_Middleware( 'log actions' );

		// Run the service.
		$registration_service = new Registration_Service( $this->createMock( DI_Container::class ) );
		$registration_service->push_middleware( $middleware )
			->process();

		$log = $middleware->message_log;
		$this->assertCount( 0, $log );
	}

	/** @testdox It should be possible to add a single class to be processed by middleware */
	public function test_can_add_single_class_to_registaration_service(): void {
		// Set middleware to catch the classes being passed through.
		$middleware = new Mock_Registration_Middleware();

		// Mock container to create the instance.
		$container = $this->createMock( DI_Container::class );
		$container->method( 'create' )->willReturn( new Sample_Class() );

		$registration_service = new Registration_Service( $container );
		$registration_service->push_middleware( $middleware );

		// Add a single class to the registration service.
		$registration_service->push_class( Sample_Class::class );

		// Process the registration service.
		$registration_service->process();

		// Check the middleware has the class.
		$log = $middleware->message_log;
		$this->assertCount( 3, $log );
		$this->assertContains( 'setup', $log );
		$this->assertContains( 'tear_down', $log );
		$this->assertContains( Sample_Class::class, $log );
	}

	/** @testdox It should only be possible to add a class once, adding multiple will not force a second iteration. */
	public function test_can_add_single_class_to_registaration_service_multiple_times(): void {
		// Set middleware to catch the classes being passed through.
		$middleware = new Mock_Registration_Middleware();

		// Mock container to create the instance.
		$container = $this->createMock( DI_Container::class );
		$container->method( 'create' )->willReturn( new Sample_Class() );

		$registration_service = new Registration_Service( $container );
		$registration_service->push_middleware( $middleware );

		// Add a single class to the registration service.
		$registration_service->push_class( Sample_Class::class );
		$registration_service->push_class( Sample_Class::class );
		$registration_service->push_class( Sample_Class::class );

		// Process the registration service.
		$registration_service->process();

		// Check the middleware has the class.
		$log = $middleware->message_log;
		$this->assertCount( 3, $log );
		$this->assertContains( 'setup', $log );
		$this->assertContains( 'tear_down', $log );
		$this->assertContains( Sample_Class::class, $log );
	}

	/** @testdox An class that can not be constructed by the DI container (null) will not be processed. */
	public function test_can_add_single_class_to_registaration_service_that_can_not_be_constructed(): void {
		// Set middleware to catch the classes being passed through.
		$middleware = new Mock_Registration_Middleware();

		// Mock container to create the instance.
		$container = $this->createMock( DI_Container::class );
		$container->method( 'create' )->willReturn( null );

		$registration_service = new Registration_Service( $container );
		$registration_service->push_middleware( $middleware );

		// Add a single class to the registration service.
		$registration_service->push_class( Sample_Class::class );

		// Process the registration service.
		$registration_service->process();

		// Check the middleware has the class.
		$log = $middleware->message_log;
		$this->assertCount( 2, $log );
		$this->assertContains( 'setup', $log );
		$this->assertContains( 'tear_down', $log );
		$this->assertNotContains( Sample_Class::class, $log );
	}

	/** @testdox It should be possible to filter the class list before it is processed by the middleware */
	public function test_can_filter_class_list_before_processing(): void {
		// Set fitler to return a different class.
		add_filter(
			Hooks::APP_INIT_REGISTRATION_CLASS_LIST,
			function( $e ) {
				return array( \stdClass::class );
			}
		);

		// Set middleware to catch the classes being passed through.
		$middleware = new Mock_Registration_Middleware();

		// Mock container to create the instance.
		$container = $this->createMock( DI_Container::class );
		$container->method( 'create' )->will(
			$this->returnCallback(
				function( $e ) {
					return new $e();
				}
			)
		);

		$registration_service = new Registration_Service( $container );
		$registration_service->push_middleware( $middleware );

		// Add the filter

		// Add a single class to the registration service.
		$registration_service->push_class( Sample_Class::class )->process();

		// Check the middleware has the class.
		$log = $middleware->message_log;
		$this->assertNotContains( Sample_Class::class, $log );
		$this->assertContains( \stdClass::class, $log );
	}

	/** @testdox Passing a none class string to the registration service class list, should result in throwing an exception. */
	public function test_throw_exception_if_none_class_string_passed(): void {
		$this->expectException( Module_Manager_Exception::class );
		$this->expectExceptionMessage( 'None class-string "not a class" passed to the registration class list' );
		$this->expectExceptionCode( 23 );

		$registration_service = new Registration_Service( $this->createMock( DI_Container::class ) );
		$registration_service->push_class( 'not a class' );
	}

}
