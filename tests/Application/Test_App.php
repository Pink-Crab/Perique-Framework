<?php

declare(strict_types=1);
/**
 * Main App Container Test.
 *
 * @since 0.4.0
 * @author Glynn Quelch <glynn.quelch@gmail.com>
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 * @package PinkCrab\Perique
 */

namespace PinkCrab\Perique\Tests\Application;

use stdClass;
use Dice\Dice;
use Exception;
use WP_UnitTestCase;
use PinkCrab\Loader\Hook_Loader;
use Gin0115\WPUnit_Helpers\Objects;
use PinkCrab\Perique\Application\App;
use PinkCrab\Perique\Application\Config;
use PinkCrab\Perique\Application\App_Config;
use PinkCrab\Perique\Interfaces\DI_Container;
use PinkCrab\Perique\Interfaces\Registration_Middleware;
use PinkCrab\Perique\Tests\Application\App_Helper_Trait;
use PinkCrab\Perique\Tests\Fixtures\DI\Has_DI_Dependency;
use PinkCrab\Perique\Exceptions\App_Initialization_Exception;
use PinkCrab\Perique\Tests\Fixtures\DI\With_WPDB_As_Dependency;
use PinkCrab\Perique\Services\Registration\Registration_Service;
use PinkCrab\Perique\Tests\Fixtures\Mock_Objects\Mock_Registration_Middleware;

class Test_App extends WP_UnitTestCase {


	/**
	 * @method self::unset_app_instance();
	 */
	use App_Helper_Trait;

	public function tearDown(): void {
		self::unset_app_instance();
	}

	/** @testdox When a container is passed to the application, it should be set as an internal property of the app. */
	public function test_set_container(): void {
		$app       = new App();
		$container = $this->createMock( DI_Container::class );
		$app->set_container( $container );
		$this->assertSame( $container, Objects::get_property( $app, 'container' ) );
	}

	/** @testdox The app should only allow one container to set, attempting to set another should cause the process to fail. */
	public function test_set_container_exception(): void {
		$this->expectException( App_Initialization_Exception::class );
		$this->expectExceptionCode( 2 );

		$app       = new App();
		$container = $this->createMock( DI_Container::class );
		$app->set_container( $container );
		$app->set_container( $container );
	}

	/** @testdox A set of configs for the application can be bound as App_Config */
	public function test_set_app_config(): void {
		$app = new App();
		$app->set_app_config( array() );
		$this->assertInstanceOf( App_Config::class, Objects::get_property( $app, 'app_config' ) );
	}

	/** @testdox The applications config should only be settable once attempting to set another should cause the process to fail. */
	public function test_set_app_config_exception(): void {
		$this->expectException( App_Initialization_Exception::class );
		$this->expectExceptionCode( 5 );

		$app = new App();
		$app->set_app_config( array() );
		$app->set_app_config( array() );
	}

	/** @testdox The registration service should be setable and bound to the registarion property */
	public function test_set_registration_services(): void {
		$app          = new App();
		$registration = $this->createMock( Registration_Service::class );
		$app->set_registration_services( $registration );
		$this->assertSame( $registration, Objects::get_property( $app, 'registration' ) );
	}

	/** @testdox The applications registration service should only be settable once, attempting to set another should cause the process to fail. */
	public function test_set_registration_services_exception(): void {
		$this->expectException( App_Initialization_Exception::class );
		$this->expectExceptionCode( 7 );

		$app          = new App();
		$registration = $this->createMock( Registration_Service::class );
		$app->set_registration_services( $registration );
		$app->set_registration_services( $registration );
	}

	/** @testdox The loader should be setable and bound to the loader property */
	public function test_set_loader(): void {
		$app    = new App();
		$loader = $this->createMock( Hook_Loader::class );
		$app->set_loader( $loader );
		$this->assertSame( $loader, Objects::get_property( $app, 'loader' ) );
	}

	/** @testdox The applications loader should only be settable once, attempting to set another should cause the process to fail. */
	public function test_set_loader_exception(): void {
		$this->expectException( App_Initialization_Exception::class );
		$this->expectExceptionCode( 8 );

		$app    = new App();
		$loader = $this->createMock( Hook_Loader::class );
		$app->set_loader( $loader );
		$app->set_loader( $loader );
	}

	/** @testdox The applications container should have an access point so custom rules can be added before the app is booted. */
	public function test_container_config(): void {
		$app       = new App();
		$container = $this->createMock( DI_Container::class );
		$app->set_container( $container );
		$app->container_config(
			function ( DI_Container $container ): void {
				$this->assertInstanceOf( DI_Container::class, $container );
			}
		);
	}

	/** @testdox Trying to configure the container before its set should result in an error and ending the intialisation. */
	public function test_container_config_exception(): void {
		$this->expectException( App_Initialization_Exception::class );
		$this->expectExceptionCode( 1 );

		$app = new App();
		$app->container_config(
			function ( DI_Container $container ): void {
				$this->assertInstanceOf( DI_Container::class, $container );
			}
		);
	}

	/** @testdox Additional functionality should be added at boot up through the means of middleware */
	public function test_registration_middleware(): void {
		$app          = new App();
		$registration = new Registration_Service();
		$middleware   = $this->createMock( Registration_Middleware::class );

		$app->set_registration_services( $registration );
		$app->registration_middleware( $middleware );
		$this->assertContains( $middleware, Objects::get_property( $registration, 'middleware' ) );
	}

	/** @testdox If middleware is added before the registration service has been bound to the app, the system should return an error. */
	public function test_registration_middleware_exception(): void {
		$this->expectException( App_Initialization_Exception::class );
		$this->expectExceptionCode( 3 );

		$app        = new App();
		$middleware = $this->createMock( Registration_Middleware::class );
		$app->registration_middleware( $middleware );
	}

	/** @testdox A list of classes which should be run through the registration process, should be able to stacked up ready to go. */
	public function test_registration_classes(): void {
		$app          = new App();
		$registration = new Registration_Service();
		$app->set_registration_services( $registration );
		$app->registration_classes( array( Sample_Class::class ) );
		$this->assertContains( Sample_Class::class, Objects::get_property( $registration, 'class_list' ) );
	}

	/** @testdox If classes are set for registration before the service has been bound to the application, it should error and abort initialisation. */
	public function test_registration_classes_exception(): void {
		$this->expectException( App_Initialization_Exception::class );
		$this->expectExceptionCode( 3 );

		$app = new App();
		$app->registration_classes( array( Sample_Class::class ) );
	}

	/** @testdox When a fully populated app is booted, it should pass validation and run all internal setups. */
	public function test_boot(): void {
		$app = $this->pre_populated_app_provider();

		// Ensure app is not marked as booted before calling boot()
		$this->assertFalse( $app::is_booted() );

		$app->boot();

		// Check the app has been booted and container is bound to registration.
		$this->assertTrue( $app::is_booted() );
		$registration = Objects::get_property( $app, 'registration' );
		$this->assertInstanceOf(
			DI_Container::class,
			Objects::get_property( $registration, 'di_container' )
		);
	}

	/** @testdox The app should only be bootable only once, trying to reboot should cause an error and abort the request. */
	public function test_throws_exception_if_trying_to_boot_twice(): void {
		$this->expectException( App_Initialization_Exception::class );
		$this->expectExceptionCode( 6 );
		$app = $this->pre_populated_app_provider();
		$app->boot();
		$app->boot();
	}

	/** @testdox The apps internal services (View, DI & App_Config) can only be used once the application has been booted. */
	public function test_throws_exception_if_view_is_called_before_app_booted(): void {
		$this->expectException( App_Initialization_Exception::class );
		$this->expectExceptionCode( 4 );
		$app = $this->pre_populated_app_provider();
		$app::view();
	}

	/** @testdox It should be possible to access the current DI container from the App instance, for use in additional libraies that need access. */
	public function test_can_get_container_from_app(): void {
		$app = $this->pre_populated_app_provider();
		$this->assertInstanceOf( DI_Container::class, $app->get_container() );
	}

	/** @testdox Attempting to access the DI Container before it has been defiend, should resutl in an Application Intialization exception. */
	public function test_throws_exception_if_attempting_to_access_undefined_di_container(): void {
		$this->expectException( App_Initialization_Exception::class );
		$this->expectExceptionCode( 1 );
		$app = new App();
		$app->get_container();
	}


	/** @testdox It should be possible to access App_Config using the Config Facade */
	public function test_config_facade(): void {
		$app = $this->pre_populated_app_provider();
		$app->boot();
		$this->assertEquals( FIXTURES_PATH . '/Views/', Config::path( 'view' ) );
		$this->assertEquals( 'test_value', Config::additional( 'test_key' ) );
	}

	/** @testdox When trying to pass the class name for Registration Middleware, an exception should be thrown if container not defined. */
	public function test_throws_exception_if_trying_to_construct_middleware_before_container_initialised(): void {
		$this->expectException( App_Initialization_Exception::class );
		$this->expectExceptionCode( 1 );
		$app = ( new App() )->set_registration_services( new Registration_Service() );
		$app->construct_registration_middleware( Mock_Registration_Middleware::class );
	}

	/** @testdox When trying to pass a classname for Registration Middleware an exception should be thrown if its not a valid Middleware Class. */
	public function test_exception_thrown_if_non_valid_middleware_class_passed(): void {
		$this->expectException( App_Initialization_Exception::class );
		$this->expectExceptionCode( 9 );
		$app = $this->pre_populated_app_provider();
		$app->construct_registration_middleware( stdClass::class );
		$app->boot();
	}

	/** @testdox When the app is booted, it should be possible to pass DI_Container as a dependency and have acess to container */
	public function test_it_should_be_possible_to_pass_di_as_dependency() {
		$app = $this->pre_populated_app_provider();
		$app->boot();
		$this->assertSame( $app->get_container(), $app::make( Has_DI_Dependency::class )->di );
	}

	/** @testdox The WPDB DI rule should be defined when the app is finalised. */
	public function test_can_pass_global_wpdb_from_base_di_rules(): void {
		$app = $this->pre_populated_app_provider();
		$app->boot();

		$instance = $app::make( With_WPDB_As_Dependency::class );
		$this->assertSame( $GLOBALS['wpdb'], $instance->wpdb );
	}
}
