<?php

declare(strict_types=1);
/**
 * Tests the App Factory
 *
 * @since 0.4.0
 * @author Glynn Quelch <glynn.quelch@gmail.com>
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 * @package PinkCrab\Perique
 */

namespace PinkCrab\Perique\Tests\Integration\Application;

use TypeError;
use WP_UnitTestCase;
use PinkCrab\Loader\Hook_Loader;
use Gin0115\WPUnit_Helpers\Objects;
use PinkCrab\Perique\Application\App;
use PinkCrab\Perique\Application\Hooks;
use PinkCrab\Perique\Application\App_Factory;
use PinkCrab\Perique\Interfaces\DI_Container;
use PinkCrab\Perique\Services\View\PHP_Engine;
use PinkCrab\Perique\Tests\Fixtures\DI\Class_G;
use PinkCrab\Perique\Tests\Fixtures\DI\Interface_A;
use PinkCrab\Perique\Tests\Fixtures\DI\Dependency_E;
use PinkCrab\Perique\Interfaces\Registration_Middleware;
use \PinkCrab\Perique\Tests\Application\App_Helper_Trait;
use PinkCrab\Perique\Tests\Fixtures\Mock_Objects\Has_DI_Container;
use PinkCrab\Perique\Services\Registration\Modules\Hookable_Module;
use PinkCrab\Perique\Tests\Fixtures\Mock_Objects\Hookable\Hookable_Mock;
use PinkCrab\Perique\Tests\Fixtures\Mock_Objects\Mock_Registration_Middleware;
use PinkCrab\Perique\Tests\Fixtures\Modules\With_Middleware\Module_With_Middleware__Module;
use PinkCrab\Perique\Tests\Fixtures\Modules\Without_Middleware\Module_Without_Middleware__Module;

/**
 * @group integration
 * @group app
 * @group app_factory
 */
class Test_App_Factory extends WP_UnitTestCase {


	/**
	 * @method self::unset_app_instance();
	 */
	use App_Helper_Trait;

	public function tearDown(): void {
		self::unset_app_instance();
	}

	/** @testdox When requested the App Factory can create an instance of App populated with WP_Dice, Hookables Middleware, Loader and Registration Service. */
	public function test_can_create_with_default_setup(): void {
		$app = ( new App_Factory( FIXTURES_PATH ) )
			->default_setup( true )
			->app();

		$this->assertInstanceOf( App::class, $app );
		$this->assertInstanceOf(
			DI_Container::class,
			Objects::get_property( $app, 'container' )
		);
		$this->assertInstanceOf(
			Hook_Loader::class,
			Objects::get_property( $app, 'loader' )
		);
	}

	/** @testdox A classes which need to be registered, should be passable at setup. Allowing plugins to register hooks with WordPress */
	// public function test_can_set_registration_classes(): void {
	// 	$app = ( new App_Factory( FIXTURES_PATH ) )
	// 		->default_setup( true )
	// 		->registration_classes( array( Hookable_Mock::class ) )->app();

	// 	$registration_service = Objects::get_property( $app, 'registration' );
	// 	$this->assertContains(
	// 		Hookable_Mock::class,
	// 		Objects::get_property( $registration_service, 'class_list' )
	// 	);
	// }

	/** @testdox It should be possible to pass custom rules to the Dependency Injection container to handle classes whos depenedencies cant be inferred. */
	public function test_can_set_di_rule() {
		$app = ( new App_Factory( FIXTURES_PATH ) )
			->default_setup( true )
			->di_rules( include FIXTURES_PATH . '/Application/dependencies.php' )
			->app();

		$container = Objects::get_property( $app, 'container' );
		$this->assertTrue( $container->has( Interface_A::class ) );
	}

	/** @testdox It should be possible to set custom settings to the apps config. */
	public function test_can_set_config(): void {
		$app = ( new App_Factory( FIXTURES_PATH ) )
			->default_setup( true )
			->app_config( include FIXTURES_PATH . '/Application/settings.php' )
			->app();

		$app_config = Objects::get_property( $app, 'app_config' );
		$this->assertEquals( 'test_value', $app_config->test_key );
	}

	/** @testdox It should be possible to boot the app from a chained factory call. If no config is set, the defaults should be used. */
	public function test_can_boot_app_from_factory_chain(): void {
		$app = ( new App_Factory( FIXTURES_PATH ) )
			->default_setup( true )
			->boot();
		$this->assertTrue( $app::is_booted() );
	}

	/** @testdox It shoud be possble to pass the DI_Container interface as a depenedcy and have it populated with the current DI_Container implementation at initialisation.  */
	public function test_di_container_rule_defined_at_init(): void {
		$app              = ( new App_Factory( FIXTURES_PATH ) )
			->default_setup( true )
			->boot();
		$has_di_container = $app::make( Has_DI_Container::class );
		$this->assertTrue( $has_di_container->di_set() );

	}

	/** @testdox It should be possible to create and instance of the App Factory and have the file instance created as the plugin base path for the App. */
	public function test_detect_base_path(): void {
		$this->assertEquals( \trailingslashit( __DIR__ ), ( new App_Factory() )->get_base_path() );
	}

	/** @testdox It should be possible to create and instance of the App Factory and be able to define the base path used for the App. */
	public function test_custom_base_path(): void {
		$dir = \trailingslashit( dirname( __DIR__, 1 ) );
		$this->assertEquals( $dir, ( new App_Factory( $dir ) )->get_base_path() );
	}

	/** @testdox It should be possible to set and get the base view path */
	public function test_set_and_get_base_view_path(): void {
		$path    = '/test/path/';
		$factory = new App_Factory();
		$factory->set_base_view_path( $path );
		$this->assertEquals( $path, $factory->get_base_view_path() );
	}

	/** @testdox The base view path should be appended with a slash automatically */
	public function test_base_view_path_has_trailing_slash(): void {
		$path    = '/test/path';
		$factory = new App_Factory();
		$factory->set_base_view_path( $path );
		$this->assertEquals( \trailingslashit( $path ), $factory->get_base_view_path() );
	}

	/** @testdox Not setting the base view path, should see it set as the default app config value */
	public function test_base_view_path_default(): void {
		$factory = new App_Factory( __DIR__ );
		$this->assertEquals( __DIR__ . '/views/', $factory->get_base_view_path() );
	}

	/** @testdox When using with_wp_dice() the base view path should be set to the project base*/
	public function test_base_view_path_default_with_wp_dice(): void {
		$path    = __DIR__ . '/';
		$factory = new App_Factory( $path );
		$factory->with_wp_dice();
		$this->assertEquals( $path, $factory->get_base_view_path() );
	}

	/** When using with_wp_dice() the base view path should not be set to project base if already defined. */
	public function test_base_view_path_not_changed_if_using_with_wp_dice(): void {
		$path    = '/test/path/';
		$factory = new App_Factory( __DIR__ );
		$factory->set_base_view_path( $path );
		$factory->with_wp_dice();
		$this->assertEquals( $path, $factory->get_base_view_path() );
	}

	/** @testdox When using the default_setup the default_di_rules should be inclued, this will set Renderable to use the PHP_Engine */
	public function test_default_setup_includes_default_di_rules(): void {
		$factory = new App_Factory( \FIXTURES_PATH );
		$factory->default_setup()->boot();

		$engine = $factory->app()->view()->engine();
		$this->assertInstanceOf( PHP_Engine::class, $engine );
	}

	/** @testdox Attempting to use the default_config without setting the default_di_rules, should result in a TypeError being thrown as Renderable implementation is not defined. */
	public function test_default_config_without_default_di_rules_throws_error(): void {
		$this->expectException( \TypeError::class );
		$factory = new App_Factory( \FIXTURES_PATH );
		$factory->default_setup( false )->boot();
		$factory->app()->view();
	}

	/** @testdox It should be possible to extend the factory and define a set of custom DI rules. */
	public function test_extend_app_factory(): void {
		$factory = new class() extends App_Factory {
			public function __construct() {
				parent::__construct( FIXTURES_PATH );
			}
			protected function default_di_rules(): array {
				return array(
					'*' => array(
						'substitutions' => array(
							Interface_A::class => new Dependency_E(),
						),
					),
				);
			}
		};
		$factory->default_setup()->boot();
		$dependency = $factory->app()->make( Class_G::class );
		$this->assertSame( Dependency_E::class, $dependency->test() );
	}

	/** @testdox When adding the App_Config array, this should be merged with the default, so any values not present will be populate from the defaults. */
	public function test_add_app_config_uses_defaults_for_undefined(): void {
		$factory = new App_Factory( FIXTURES_PATH );
		$factory
			->default_setup()
			->app_config(
				array(
					'path' => array(
						'assets' => '/some/path/',
					),
					'url'  => array(
						'assets' => 'https://some.url/',
					),
				)
			);

		// Get the config from using using the debug info.
		/** @var \PinkCrab\Perique\Application\App_Config $config */
		$config = $factory->boot()->__debugInfo()['app_config'];

		// Has changed values.
		$this->assertEquals( '/some/path/', $config->path( 'assets' ) );
		$this->assertEquals( 'https://some.url/', $config->url( 'assets' ) );

		// Uses defaults.
		$this->assertEquals( FIXTURES_PATH . '/', $config->path( 'plugin' ) );
		$this->assertEquals( FIXTURES_PATH . '/views/', $config->path( 'view' ) );

		// WP Uploads
		$this->assertEquals( \trailingslashit( \wp_upload_dir()['basedir'] ), $config->path( 'upload_root' ) );
		$this->assertEquals( \trailingslashit( \wp_upload_dir()['path'] ), $config->path( 'upload_current' ) );
		$this->assertEquals( \trailingslashit( \wp_upload_dir()['baseurl'] ), $config->url( 'upload_root' ) );
		$this->assertEquals( \trailingslashit( \wp_upload_dir()['url'] ), $config->url( 'upload_current' ) );

		// Namespaces
		$this->assertEquals( 'pinkcrab', $config->rest() );
		$this->assertEquals( 'pc_cache', $config->cache() );

		// Version
		$this->assertEquals( '0.1.0', $config->version() );
	}

	/** @testdox When no App_Config is defined, a set of defaults should be used, based on the base path defined in the App_Factory */
	public function test_default_app_config(): void {
		$factory = new App_Factory( FIXTURES_PATH );

		// Use reflection to get the default config.
		$default_config = Objects::invoke_method( $factory, 'default_config_paths', array() );

		// Get base paths from WP.
		$uploads    = \wp_upload_dir();
		$plugin_url = \get_option( 'siteurl' ) . '/wp-content/plugins/Fixtures';
		$base_path  = FIXTURES_PATH;

		// Check the paths.
		$this->assertEquals( $base_path, $default_config['path']['plugin'] );
		$this->assertEquals( $base_path . '/views', $default_config['path']['view'] );
		$this->assertEquals( $base_path . '/assets', $default_config['path']['assets'] );
		$this->assertEquals( $uploads['basedir'], $default_config['path']['upload_root'] );
		$this->assertEquals( $uploads['path'], $default_config['path']['upload_current'] );

		// Check the urls.
		$this->assertEquals( $plugin_url, $default_config['url']['plugin'] );
		$this->assertEquals( $plugin_url . '/views', $default_config['url']['view'] );
		$this->assertEquals( $plugin_url . '/assets', $default_config['url']['assets'] );
		$this->assertEquals( $uploads['baseurl'], $default_config['url']['upload_root'] );
		$this->assertEquals( $uploads['url'], $default_config['url']['upload_current'] );
	}

	/** @testdox Once the app has been booted and finalise has been run, the default object instances should be added as rules to the DI Container */
	public function test_default_object_instances_are_added_to_di_container(): void {
		$factory   = new App_Factory( FIXTURES_PATH );
		$app       = $factory->default_setup()->boot();
		$container = $app->__debugInfo()['container'];
		$dice      = Objects::get_property( $container, 'dice' );
		$rules     = Objects::get_property( $dice, 'rules' );

		$base_substitution_rules = $rules['*']['substitutions'];

		$this->assertArrayHasKey( App::class, $base_substitution_rules );
		$this->assertArrayHasKey( \wpdb::class, $base_substitution_rules );
		$this->assertArrayHasKey( DI_Container::class, $base_substitution_rules );

		// Check instances.
		$this->assertSame( $app, $base_substitution_rules[ App::class ] );
		$this->assertSame( $container, $base_substitution_rules[ DI_Container::class ] );
	}

	/** @testdox It should not be possible to overwrite the plugin or view path or urls by setting in app_config array */
	public function test_cant_overwrite_plugin_or_view_url_or_path(): void {
		$factory = new App_Factory( FIXTURES_PATH );
		$factory
			->default_setup()
			->app_config(
				array(
					'path' => array(
						'plugin' => '/some/path/',
						'view'   => '/some/path/',
					),
					'url'  => array(
						'plugin' => 'https://some.url/',
						'view'   => 'https://some.url/view',
					),
				)
			);

		// Get the config from using using the debug info.
		/** @var \PinkCrab\Perique\Application\App_Config $config */
		$config = $factory->boot()->__debugInfo()['app_config'];

		// Has changed values.
		$this->assertEquals( FIXTURES_PATH . '/', $config->path( 'plugin' ) );
		$this->assertEquals( FIXTURES_PATH . '/views/', $config->path( 'view' ) );

		$this->assertEquals( 'http://example.org/wp-content/plugins/Fixtures/', $config->url( 'plugin' ) );
		$this->assertEquals( 'http://example.org/wp-content/plugins/Fixtures/views/', $config->url( 'view' ) );
	}

	/** @testdox It should be possible to define modules both before and after the Module_Manager is defined in the App instance */
	public function test_can_define_modules_before_and_after_module_manager_is_set(): void {
		// Clear any existing hooks.
		if ( array_key_exists( Hooks::APP_INIT_PRE_BOOT, $GLOBALS['wp_filter'] ) ) {
			$GLOBALS['wp_filter'][ Hooks::APP_INIT_PRE_BOOT ][10] = array();
		}
		if ( array_key_exists( Hooks::APP_INIT_PRE_REGISTRATION, $GLOBALS['wp_filter'] ) ) {
			$GLOBALS['wp_filter'][ Hooks::APP_INIT_PRE_REGISTRATION ][10] = array();
		}
		if ( array_key_exists( Hooks::APP_INIT_POST_REGISTRATION, $GLOBALS['wp_filter'] ) ) {
			$GLOBALS['wp_filter'][ Hooks::APP_INIT_POST_REGISTRATION ][10] = array();
		}

		( new App_Factory( FIXTURES_PATH ) )
			->module( Module_With_Middleware__Module::class )// Before set.
			->default_setup()
			->module( Module_Without_Middleware__Module::class )// After set.
			->boot();

		\do_action( 'init' );
		\do_action( 'plugins_loaded' );

		// Look for hook and class name.
		$find_hook = function( $hook, $classname ) {
			foreach ( $GLOBALS['wp_filter'][ $hook ][10] as $callback ) {
				if ( \array_key_exists( 'function', $callback ) ) {
					if ( $callback['function'][0] instanceof $classname ) {
						return $callback;
					}
				}
			}
			return null;
		};

		// Pre boot with Module with Middleware.
		$pre_boot_with_mware = $find_hook( Hooks::APP_INIT_PRE_BOOT, Module_With_Middleware__Module::class );
		$this->assertNotNull( $pre_boot_with_mware );
		$this->assertInstanceOf( Module_With_Middleware__Module::class, $pre_boot_with_mware['function'][0] );
		$this->assertEquals( 3, $pre_boot_with_mware['accepted_args'] );

		// Pre register with Module with Middleware.
		$pre_reg_with_mware = $find_hook( Hooks::APP_INIT_PRE_REGISTRATION, Module_With_Middleware__Module::class );
		$this->assertNotNull( $pre_reg_with_mware );
		$this->assertInstanceOf( Module_With_Middleware__Module::class, $pre_reg_with_mware['function'][0] );
		$this->assertEquals( 3, $pre_reg_with_mware['accepted_args'] );

		// Post register with Module with Middleware.
		$post_reg_with_mware = $find_hook( Hooks::APP_INIT_POST_REGISTRATION, Module_With_Middleware__Module::class );
		$this->assertNotNull( $post_reg_with_mware );
		$this->assertInstanceOf( Module_With_Middleware__Module::class, $post_reg_with_mware['function'][0] );
		$this->assertEquals( 3, $post_reg_with_mware['accepted_args'] );

		// Pre boot with Module without Middleware.
		$pre_boot_without_mware = $find_hook( Hooks::APP_INIT_PRE_BOOT, Module_Without_Middleware__Module::class );
		$this->assertNotNull( $pre_boot_without_mware );
		$this->assertInstanceOf( Module_Without_Middleware__Module::class, $pre_boot_without_mware['function'][0] );
		$this->assertEquals( 3, $pre_boot_without_mware['accepted_args'] );

		// Pre register with Module without Middleware.
		$pre_reg_without_mware = $find_hook( Hooks::APP_INIT_PRE_REGISTRATION, Module_Without_Middleware__Module::class );
		$this->assertNotNull( $pre_reg_without_mware );
		$this->assertInstanceOf( Module_Without_Middleware__Module::class, $pre_reg_without_mware['function'][0] );
		$this->assertEquals( 3, $pre_reg_without_mware['accepted_args'] );

		// Post register with Module without Middleware.
		$post_reg_without_mware = $find_hook( Hooks::APP_INIT_POST_REGISTRATION, Module_Without_Middleware__Module::class );
		$this->assertNotNull( $post_reg_without_mware );
		$this->assertInstanceOf( Module_Without_Middleware__Module::class, $post_reg_without_mware['function'][0] );
		$this->assertEquals( 3, $post_reg_without_mware['accepted_args'] );

		// Clear the hooks.
		$GLOBALS['wp_filter'][ Hooks::APP_INIT_PRE_BOOT ][10]          = array();
		$GLOBALS['wp_filter'][ Hooks::APP_INIT_PRE_REGISTRATION ][10]  = array();
		$GLOBALS['wp_filter'][ Hooks::APP_INIT_POST_REGISTRATION ][10] = array();
	}

}
