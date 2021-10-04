<?php

declare(strict_types=1);

/**
 * Tests for the WP_Dice wrapper.
 *
 * @since 0.4.0
 * @author Glynn Quelch <glynn.quelch@gmail.com>
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 * @package PinkCrab\Perique
 */

namespace PinkCrab\Perique\Tests\Registration;

use Dice\Dice;
use WP_UnitTestCase;
use PinkCrab\Loader\Hook_Loader;
use Gin0115\WPUnit_Helpers\Objects;
use PinkCrab\Perique\Application\Hooks;
use PinkCrab\Perique\Interfaces\DI_Container;
use PinkCrab\Perique\Services\Dice\PinkCrab_Dice;
use PinkCrab\Perique\Interfaces\Registration_Middleware;
use PinkCrab\Perique\Tests\Fixtures\Mock_Objects\Sample_Class;
use PinkCrab\Perique\Services\Registration\Registration_Service;
use PinkCrab\Perique\Tests\Fixtures\Mock_Objects\Parent_Dependency;
use PinkCrab\Perique\Services\Registration\Middleware\Hookable_Middleware;
use PinkCrab\Perique\Tests\Fixtures\Mock_Objects\Mock_Registration_Middleware;

class Test_Registration_Service extends WP_UnitTestCase {

	/** @testdox The registration service must be populated with a DI Container*/
	public function test_can_set_di_container(): void {
		$container            = $this->createMock( DI_Container::class );
		$registration_service = new Registration_Service;

		$registration_service->set_container( $container );

		$this->assertSame( $container, Objects::get_property( $registration_service, 'di_container' ) );
	}

	/** @testdox It should be possible to add as many peices of registration middleware as desired to the registration process. */
	public function test_push_middleware_to_internal_stack(): void {
		$registration_service = new Registration_Service;

		$middleware1 = $this->createMock( Hookable_Middleware::class );
		$registration_service->push_middleware( $middleware1 );

		$middleware2 = $this->createMock( Registration_Middleware::class );
		$registration_service->push_middleware( $middleware2 );

		$this->assertContains(
			$middleware1,
			Objects::get_property( $registration_service, 'middleware' )
		);
		$this->assertContains(
			$middleware2,
			Objects::get_property( $registration_service, 'middleware' )
		);
	}

	/** @testdox You should be able to set a full array of classes to the registration service to be used during the process. */
	public function test_can_set_classes_to_registation_service(): void {
		$registration_service = new Registration_Service;

		$classes = array( Sample_Class::class, Parent_Dependency::class );

		$registration_service->set_classes( $classes );

		$this->assertSame(
			$classes,
			Objects::get_property( $registration_service, 'class_list' )
		);

	}

	/** @testdox You should be able to add single classes to be added to the registration service class list */
	public function test_can_push_class_to_registration_service(): void {
		$registration_service = new Registration_Service;

		$registration_service->push_class( Sample_Class::class );
		$registration_service->push_class( Parent_Dependency::class );

		$this->assertSame(
			array( Sample_Class::class, Parent_Dependency::class ),
			Objects::get_property( $registration_service, 'class_list' )
		);
	}

	/** @testdox A populate registation service should be able to process all classes against all middleware. */
	public function test_process_registation_middleware(): void {
		$registration_service = new Registration_Service;
		$container            = new PinkCrab_Dice( new Dice );

		$registration_service->set_container( $container );

		$registration_service->push_middleware( new Mock_Registration_Middleware() );

		$registration_service->set_classes( array( Sample_Class::class, Parent_Dependency::class ) );

		$this->expectOutputRegex( '/Sample_Class/' );
		$this->expectOutputRegex( '/Parent_Dependency/' );
		$registration_service->process();
	}

	/** @testdox External codebase should be able to use a filter to add additional classes to the classlist. */
	public function test_process_registation_middleware_using_filter(): void {

		$this->expectOutputRegex( '/Sample_Class/' );
		$this->expectOutputRegex( '/Parent_Dependency/' );

		$container = new PinkCrab_Dice( new Dice );

		add_filter(
			Hooks::APP_INIT_REGISTRATION_CLASS_LIST,
			function( $e ) {
				$e[] = Parent_Dependency::class;
				return $e;
			}
		);

		( new Registration_Service )->push_middleware( new Mock_Registration_Middleware() )
			->set_container( $container )
			->push_class( Sample_Class::class )
			->process();

		// $registration_service->set_classes( array( Sample_Class::class, Parent_Dependency::class ) );
	}

	/** @testdox It should be possible to setup and teardown internal state within the middleware, around processing classes passed */
	public function test_middleware_setup_and_tear_down(): void {
		$registration_service = new Registration_Service;
		$container            = new PinkCrab_Dice( new Dice );
		$middleware           = new Mock_Registration_Middleware();
		$loader               = new Hook_Loader();

		$registration_service->set_container( $container );
		$registration_service->set_loader( $loader );

		$registration_service->push_middleware( $middleware );

		$registration_service->set_classes( array( Sample_Class::class, Parent_Dependency::class ) );

		$this->setOutputCallback( function() {} );
		$registration_service->process();

		$this->assertContains( 'setup', $middleware->message_log );
		$this->assertContains( 'tear_down', $middleware->message_log );
		$this->assertContains( Sample_Class::class, $middleware->message_log );
		$this->assertContains( Parent_Dependency::class, $middleware->message_log );
		$this->assertContains( PinkCrab_Dice::class, $middleware->message_log );
		$this->assertContains( Hook_Loader::class, $middleware->message_log );
	}
}
