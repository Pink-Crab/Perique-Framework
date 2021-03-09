<?php

declare(strict_types=1);
/**
 * App Boot instance tests.
 *
 * @since 0.3.10
 * @author Glynn Quelch <glynn.quelch@gmail.com>
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 * @package PinkCrab\Core
 */

namespace PinkCrab\Core\Tests\Application;

use WP_UnitTestCase;
use PinkCrab\Loader\Loader;
use PinkCrab\Core\Application\App;
use PinkCrab\Core\Application\Boot;
use PinkCrab\PHPUnit_Helpers\Objects;
use PinkCrab\Core\Services\Dice\WP_Dice;
use PinkCrab\Core\Application\App_Config;
use PinkCrab\Core\Services\ServiceContainer\Container;
use PinkCrab\Core\Tests\Fixtures\Mock_Objects\Sample_Class;
use PinkCrab\Core\Tests\Fixtures\Mock_Objects\Interface_Get;
use PinkCrab\Core\Tests\Fixtures\Mock_Objects\Registerable\Registerable_Mock;

class Test_Boot extends WP_UnitTestCase {

	public function boot_provider(): Boot {
		$base_path = dirname( __DIR__, 1 ) . '/Fixtures/Application/';
		return new Boot(
			$base_path . 'settings.php',
			$base_path . 'dependencies.php',
			$base_path . 'registerables.php',
		);
	}

	/**
	 * Ensure the headers are cleared on each test.
	 * @var bool
	 */
	protected $preserveGlobalState = false;

	/** @runInSeparateProcess */
	public function test_settings_added_at_initalise() {
		$boot = $this->boot_provider()->initialise();
		/** @var App_Config */
		$settings = Objects::get_private_property( $boot, 'app_settings' );
		$this->assertInstanceOf( App_Config::class, $settings );
		$this->assertEquals( 'test_value', $settings->additional( 'test_key' ) );

		// Ensure Container, Loader and DI are also contrstucted.
		$this->assertInstanceOf( Container::class, Objects::get_private_property( $boot, 'container' ) );
		$this->assertInstanceOf( WP_Dice::class, Objects::get_private_property( $boot, 'wp_di' ) );
		$this->assertInstanceOf( Loader::class, Objects::get_private_property( $boot, 'loader' ) );
	}

	/** @runInSeparateProcess */
	public function test_can_use_settings_filter() {
		add_filter(
			'PinkCrab/Boot/app_config',
			function( array $config, Boot $boot ): array {
				$config['additional']['by_filter'] = 'SUCCESS';
				return $config;
			},
			10,
			2
		);

		$boot = $this->boot_provider()->initialise();
		/** @var App_Config */
		$settings = Objects::get_private_property( $boot, 'app_settings' );
		$this->assertEquals( 'SUCCESS', $settings->additional( 'by_filter' ) );
	}

	/** @runInSeparateProcess */
	public function test_can_bind_to_boot() {
		$service = new \stdClass();
		$boot    = $this->boot_provider()
			->initialise()
			->bind_to_container( 'test', $service );

		/** @var Container */
		$container = Objects::get_private_property( $boot, 'container' );
		$this->assertTrue( $container->has( 'test' ) );
		$this->assertSame( $service, $container->get( 'test' ) );
	}

	/** @runInSeparateProcess */
	public function test_finalise_builds_app_and_binds_intrernal_services() {
		$boot = $this->boot_provider()->initialise()->finalise();

		/** @var App */
		$app = Objects::get_private_property( $boot, 'app' );

		$this->assertInstanceOf( App::class, $app );
		$this->assertInstanceOf( WP_Dice::class, $app->get( 'di' ) );
		$this->assertInstanceOf( App_Config::class, $app->get( 'config' ) );
	}

	/** @runInSeparateProcess */
	public function test_pre_initiase_hook_called() {
		add_action(
			'PinkCrab/Boot/pre_app_init',
			function( Boot $boot ): void {
				print get_class( $boot );
			}
		);
		$this->expectOutputString( Boot::class );
		$this->boot_provider()->initialise()->finalise();
	}

	/** @runInSeparateProcess */
	public function test_dependencies_filter(): void {
		$boot = $this->boot_provider()->initialise();

		add_filter(
			'PinkCrab/Boot/dependencies',
			function( array $dependencies, Boot $boot ): array {
				$dependencies[ Interface_Get::class ] = array(
					'instanceOf' => Sample_Class::class,
				);
				return $dependencies;
			},
			10,
			2
		);

		// Finalise boot and run init.
		$boot->finalise();
		do_action( 'init' );

		/** @var App */
		$app = Objects::get_private_property( $boot, 'app' );

		$this->assertInstanceOf( Sample_Class::class, $app::make( Interface_Get::class ) );
	}

	/** @runInSeparateProcess */
	public function test_registerables_filter(): void {
		$boot = $this->boot_provider()->initialise();

		add_filter(
			'PinkCrab/Boot/registerables',
			function( array $registerables, Boot $boot ): array {
				$registerables[] = Registerable_Mock::class;
				return $registerables;
			},
			10,
			2
		);

		// Finalise boot and run init.
		$boot->finalise();
		do_action( 'init' );

		$this->assertTrue( \has_action( 'Registerable_Mock' ) );
	}

	/** @runInSeparateProcess */
	public function test_pre_initalisation_hook() {
		add_action(
			'PinkCrab/Boot/pre_registration',
			function( $boot ) {
				echo 'pre_registration';
			}
		);
		add_action(
			'PinkCrab/Boot/post_registration',
			function( $boot ) {
				echo 'post_registration';
			}
		);
        
        $this->boot_provider()->initialise()->finalise();

        $this->expectOutputRegex("/pre_registration/i");
        $this->expectOutputRegex("/post_registration/i");
		do_action( 'init' );

	}

}
