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

namespace PinkCrab\Perique\Tests\Integration\Application;

use Dice\Dice;
use Exception;
use WP_UnitTestCase;
use PinkCrab\Loader\Hook_Loader;
use PinkCrab\Perique\Application\App;
use PinkCrab\Perique\Application\Hooks;
use PinkCrab\Perique\Interfaces\Renderable;
use PinkCrab\Perique\Application\App_Config;
use PinkCrab\Perique\Application\App_Factory;
use PinkCrab\Perique\Interfaces\DI_Container;
use PinkCrab\Perique\Services\View\PHP_Engine;
use PinkCrab\Perique\Tests\Application\App_Helper_Trait;
use PinkCrab\Perique\Exceptions\Module_Manager_Exception;
use PinkCrab\Perique\Services\Registration\Module_Manager;
use PinkCrab\Perique\Exceptions\App_Initialization_Exception;
use PinkCrab\Perique\Tests\Fixtures\Mock_Objects\Sample_Class;
use PinkCrab\Perique\Tests\Fixtures\Mock_Objects\Parent_Dependency;
use PinkCrab\Perique\Tests\Fixtures\Mock_Objects\Hookable\Hookable_Mock;

/**
 * @group integration
 * @group app
 * @group app_factory
 *
 */
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
									Renderable::class => new PHP_Engine( FIXTURES_PATH . '/views' ),
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

		$this->assertArrayHasKey( 'module_manager', $debug );
		$this->assertInstanceOf( Module_Manager::class, $debug['module_manager'] );

		$this->assertArrayHasKey( 'base_path', $debug );
		$this->assertEquals( FIXTURES_PATH, $debug['base_path'] );

		$this->assertArrayHasKey( 'view_path', $debug );
		$this->assertEquals( FIXTURES_PATH . '/views', $debug['view_path'] );
	}

	/** @testdox When attempting to pass a non registration middleware class name to be constructed an exception should be thrown if invalid type. */
	public function test_registration_middleware_as_string_throws_invalid_middleware_exception(): void {

		$this->expectException( Module_Manager_Exception::class );
		$this->expectExceptionCode( 20 );
		$app = $this->pre_populated_app_provider()
			->boot()
			->module( Sample_Class::class );
	}

	/** @testdox When creating a new App instance using the App Factory, the base path should be reflected in App Configs default values. */
	public function test_app_config_paths_based_on_app_factory_base_path() {
		$path = \FIXTURES_PATH;
		$app  = ( new App_Factory( $path ) )
			->set_base_view_path( $path )
			->default_setup( true )->boot();

		$this->assertEquals(
			rtrim( $path, \DIRECTORY_SEPARATOR ),
			rtrim( $app::config( 'path', 'plugin' ), \DIRECTORY_SEPARATOR )
		);
	}

	/** @testdox When the app is booted, the Hookable_Middleware should be included automatically */
	public function test_hookable_middleware_is_included_automatically() {
		( new App_Factory( __DIR__ ) )
			->with_wp_dice( true )
			->registration_classes( array( Hookable_Mock::class ) )
			->boot();

		// Simulate booting the app.
		do_action( 'init' );
		do_action( 'plugins_loaded' );

		// Check the hook as been added.
		$this->assertTrue( \has_action( 'Hookable_Mock' ) );

		// Remove the action so it doesn't affect other tests.
		\remove_all_actions( 'Hookable_Mock' );
	}

	/** @testdox When the App Init Pre Boot hook is called, the current instances of App_Config, Hook_Loader and DI_Container should be passed to the actions callback. */
	public function test_app_init_pre_boot_hook() {
		\add_action(
			Hooks::APP_INIT_PRE_BOOT,
			function ( App_Config $config, Hook_Loader $hook_loader, DI_Container $container ) {
				$this->assertInstanceOf( App_Config::class, $config );
				$this->assertInstanceOf( Hook_Loader::class, $hook_loader );
				$this->assertInstanceOf( DI_Container::class, $container );
			},
			10,
			3
		);

		( new App_Factory( __DIR__ ) )
			->with_wp_dice( true )
			->boot();
		do_action( 'init' );
		do_action( 'plugins_loaded' );

		// Remove the action so it doesn't affect other tests.
		\remove_all_actions( Hooks::APP_INIT_PRE_BOOT );
	}

	/** @testdox When the App Init Pre Registration hook is called, the current instances of App_Config, Hook_Loader and DI_Container should be passed to the actions callback. */
	public function test_app_init_pre_registration_hook() {
		\add_action(
			Hooks::APP_INIT_PRE_REGISTRATION,
			function ( App_Config $config, Hook_Loader $hook_loader, DI_Container $container ) {
				$this->assertInstanceOf( App_Config::class, $config );
				$this->assertInstanceOf( Hook_Loader::class, $hook_loader );
				$this->assertInstanceOf( DI_Container::class, $container );
			},
			10,
			3
		);

		( new App_Factory( __DIR__ ) )
			->with_wp_dice( true )
			->boot();
		do_action( 'init' );
		do_action( 'plugins_loaded' );

		// Remove the action so it doesn't affect other tests.
		\remove_all_actions( Hooks::APP_INIT_PRE_REGISTRATION );
	}

	/** @testdox When the App Init Post Registration hook is called, the current instances of App_Config, Hook_Loader and DI_Container should be passed to the actions callback. */
	public function test_app_init_post_registration_hook() {
		\add_action(
			Hooks::APP_INIT_POST_REGISTRATION,
			function ( App_Config $config, Hook_Loader $hook_loader, DI_Container $container ) {
				$this->assertInstanceOf( App_Config::class, $config );
				$this->assertInstanceOf( Hook_Loader::class, $hook_loader );
				$this->assertInstanceOf( DI_Container::class, $container );
			},
			10,
			3
		);

		( new App_Factory( __DIR__ ) )
			->with_wp_dice( true )
			->boot();
		do_action( 'init' );
		do_action( 'plugins_loaded' );

		// Remove the action so it doesn't affect other tests.
		\remove_all_actions( Hooks::APP_INIT_POST_REGISTRATION );
	}

	/** @testdox The App should be intialised on the init hook with a priority of 1 */
	public function test_app_init_on_init_hook() {
		// Backup and clear all init @ 1 hooks.
		$backup                          = $GLOBALS['wp_filter']['init'][1];
		$GLOBALS['wp_filter']['init'][1] = array();

		$app = ( new App_Factory( __DIR__ ) )
			->with_wp_dice( true )
			->boot();

		// Get all hooks that are closures.
		$all_hooks = function() use ( $app ) {
			return array_filter(
				$GLOBALS['wp_filter']['init'][1],
				function ( $hook ) use ( $app ) {
					return $hook['function'] instanceof \Closure
					&& $hook['function']->bindTo( $app, $app ) instanceof \Closure;
				}
			);
		};

		$this->assertCount( 1, $all_hooks() );

		// Restore the backup.
		$GLOBALS['wp_filter']['init'][1] = $backup;
	}

}
