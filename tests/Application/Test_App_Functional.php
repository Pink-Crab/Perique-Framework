<?php

declare(strict_types=1);
/**
 * Functional tests using the App
 *
 * @since 0.4.0
 * @author Glynn Quelch <glynn.quelch@gmail.com>
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 * @package PinkCrab\Perique
 */

namespace PinkCrab\Perique\Tests\Application;

use Dice\Dice;
use Exception;
use WP_UnitTestCase;
use PinkCrab\Loader\Hook_Loader;
use Gin0115\WPUnit_Helpers\Objects;
use PinkCrab\Perique\Application\App;
use PinkCrab\Perique\Application\Hooks;
use PinkCrab\Perique\Interfaces\Renderable;
use PinkCrab\Perique\Application\App_Config;
use PinkCrab\Perique\Interfaces\DI_Container;
use PinkCrab\Perique\Services\View\PHP_Engine;
use PinkCrab\Perique\Services\Dice\PinkCrab_Dice;
use PinkCrab\Perique\Interfaces\Registration_Middleware;
use PinkCrab\Perique\Tests\Application\App_Helper_Trait;
use PinkCrab\Perique\Exceptions\App_Initialization_Exception;
use PinkCrab\Perique\Tests\Fixtures\Mock_Objects\Sample_Class;
use PinkCrab\Perique\Services\Registration\Registration_Service;
use PinkCrab\Perique\Tests\Fixtures\Mock_Objects\Parent_Dependency;
use PinkCrab\Perique\Tests\Fixtures\Mock_Objects\Mock_Registration_Middleware;

class Test_App_Functional extends WP_UnitTestCase {


	/**
	 * @method self::unset_app_instance();
	 */
	use App_Helper_Trait;

	public function tearDown(): void {
		self::unset_app_instance();
	}

	/** @testdox When running the applications setup, hooks should be triggered to allow external codeabases to interact and piggyback into the app initialisation process. */
	public function test_all_hooks_fire_on_finalise_during_boot(): void {

		// Pre boot hook.
		$this->expectOutputRegex( '/Pre Boot Hook/' );
		\add_action(
			Hooks::APP_INIT_PRE_BOOT,
			function ( App_Config $config, Hook_Loader $loader, DI_Container $container ) {
				echo 'Pre Boot Hook';
			},
			10,
			3
		);

		// Pre registration hook.
		$this->expectOutputRegex( '/Pre Registration Hook/' );
		\add_action(
			Hooks::APP_INIT_PRE_REGISTRATION,
			function ( App_Config $config, Hook_Loader $loader, DI_Container $container ) {
				echo 'Pre Registration Hook';
			},
			10,
			3
		);

		// Post registration.
		$this->expectOutputRegex( '/Post Registration Hook/' );
		\add_action(
			Hooks::APP_INIT_POST_REGISTRATION,
			function ( App_Config $config, Hook_Loader $loader, DI_Container $container ) {
				echo 'Post Registration Hook';
			},
			10,
			3
		);

		// Boot app.
		$app = $this->pre_populated_app_provider()->boot();

		// Run init
		do_action( 'init' );

		// Cleanup
		remove_all_actions( Hooks::APP_INIT_PRE_BOOT );
		remove_all_actions( Hooks::APP_INIT_PRE_REGISTRATION );
		remove_all_actions( Hooks::APP_INIT_POST_REGISTRATION );
	}

	/** @testdox Once the App is booted, it should be possible to create an instance of an object, using the DI Container without acess to an actual instnace of the App. Via a static method */
	public function test_can_use_static_make_method_to_use_di_container(): void {
		$app = $this->pre_populated_app_provider()->boot();

		// Fxiture class, has Sample_Class injected as a dependency.
		$parent = App::make( Parent_Dependency::class );

		$this->assertInstanceOf( Parent_Dependency::class, $parent );
		$this->assertInstanceOf( Sample_Class::class, $parent->get_sample_class() );
	}

	/** @testdox When trying to use the DI Container from App as a static instance, an error should be thrown and the request aborted */
	public function test_cant_use_make_before_app_booted(): void {
		$this->expectException( App_Initialization_Exception::class );
		$this->expectExceptionCode( 4 );

		App::make( Parent_Dependency::class );
	}

	/** @testdox Once the app is booted, the static method config() should act as a proxy for the internal App_Config settings */
	public function test_can_use_static_config_to_access_app_config(): void {
		$app = $this->pre_populated_app_provider()->boot();

		$version = App::config( 'version' );
		$this->assertEquals( '0.1.0', $version );
	}

	/** @testdox When trying to call config from app, before app has been booted should result in an error and abort the current request. */
	public function test_cant_use_config_before_app_has_been_booted(): void {
		$this->expectException( App_Initialization_Exception::class );
		$this->expectExceptionCode( 4 );

		App::config( 'version' );
	}

	/** @testdox Once the app is booted, it should be possible to call the current view with it engine, using a static method on the app. */
	public function test_can_user_static_view_from_app_once_booted(): void {
		$app = $this->pre_populated_app_provider()
			->container_config(
				function ( $di ) {
					$di->addRules(
						array(
							'*' => array(
								'substitutions' => array(
									Renderable::class => new PHP_Engine( FIXTURES_PATH . '/Views' ),
								),
							),
						)
					);
				}
			)->boot();

		$this->expectOutputString( 'HI' );
		App::view()->render( 'hello', array( 'hello' => 'HI' ) );
	}

	/** @testdox When calling var_dump on the apps instance, the internal static properties should be included to help with debugging. */
	public function test_debugInfo_dumps_static_properties_values(): void {
		$app   = $this->pre_populated_app_provider()->boot();
		$debug = $app->__debugInfo();

		$this->assertArrayHasKey( 'container', $debug );
		$this->assertInstanceOf( DI_Container::class, $debug['container'] );

		$this->assertArrayHasKey( 'app_config', $debug );
		$this->assertInstanceOf( App_Config::class, $debug['app_config'] );

		$this->assertArrayHasKey( 'booted', $debug );
		$this->assertTrue( $debug['booted'] );
	}

	/** @testdox Additional functionality should be added at boot up through the means of middleware */
	public function test_registration_middleware_as_string(): void {
		$app = $this->pre_populated_app_provider()->boot();
		$app->construct_registration_middleware( Mock_Registration_Middleware::class );
		$registration = Objects::get_property( $app, 'registration' );
		$this->assertArrayHasKey( Mock_Registration_Middleware::class, Objects::get_property( $registration, 'middleware' ) );
	}

	/** @testdox When attempting to pass a non registration middleware class name to be constructed an exception should be thrown if invalid type. */
	public function test_registration_middleware_as_string_throws_invalid_middleware_exception(): void {

		$this->expectException( App_Initialization_Exception::class );
		$this->expectExceptionCode( 9 );
		$app = $this->pre_populated_app_provider()
			->boot()
			->construct_registration_middleware( Sample_Class::class );
	}
}
