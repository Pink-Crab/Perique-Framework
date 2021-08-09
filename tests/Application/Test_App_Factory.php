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

namespace PinkCrab\Perique\Tests\Application;

use WP_UnitTestCase;
use PinkCrab\Loader\Hook_Loader;
use Gin0115\WPUnit_Helpers\Objects;
use PinkCrab\Perique\Application\App;
use PinkCrab\Perique\Application\App_Factory;
use PinkCrab\Perique\Interfaces\DI_Container;
use PinkCrab\Perique\Tests\Fixtures\DI\Interface_A;
use PinkCrab\Perique\Interfaces\Registration_Middleware;
use PinkCrab\Perique\Tests\Fixtures\Mock_Objects\Has_DI_Container;
use PinkCrab\Perique\Tests\Fixtures\Mock_Objects\Hookable\Hookable_Mock;
use PinkCrab\Perique\Tests\Fixtures\Mock_Objects\Mock_Registration_Middleware;

class Test_App_Factory extends WP_UnitTestCase {


	/**
	 * @method self::unset_app_instance();
	 */
	use App_Helper_Trait;

	public function tearDown(): void {
		self::unset_app_instance();
	}

	/** @testdox When requested the App Factory can create an instance of App popualted with WP_Dice, Hookables Middleware, Loader and Registration Service. */
	public function test_can_create_with_wp_dicece(): void {
		$app = ( new App_Factory )
			->with_wp_dice( true )
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
	public function test_can_set_registration_classes(): void {
		$app = ( new App_Factory )
			->with_wp_dice( true )
			->registration_classes( array( Hookable_Mock::class ) )->app();

		$registration_service = Objects::get_property( $app, 'registration' );
		$this->assertContains(
			Hookable_Mock::class,
			Objects::get_property( $registration_service, 'class_list' )
		);
	}

	/** @testdox It should be possible to pass custom rules to the Dependency Injection container to handle classes whos depenedencies cant be inferred. */
	public function test_can_set_di_rule() {
		$app = ( new App_Factory )
			->with_wp_dice( true )
			->di_rules( include FIXTURES_PATH . '/Application/dependencies.php' )
			->app();

		$container = Objects::get_property( $app, 'container' );
		$this->assertTrue( $container->has( Interface_A::class ) );
	}

	/** @testdox It should be possible to set custom settings to the apps config. */
	public function test_can_set_config(): void {
		$app = ( new App_Factory )
			->with_wp_dice( true )
			->app_config( include FIXTURES_PATH . '/Application/settings.php' )
			->app();

		$app_config = Objects::get_property( $app, 'app_config' );
		$this->assertEquals( 'test_value', $app_config->test_key );
	}

	/** @testdox It should be possible to boot the app from a chained factory call. If no config is set, the defaults should be used. */
	public function test_can_boot_app_from_factory_chain(): void {
		$app = ( new App_Factory )
			->with_wp_dice( true )
			->boot();
		$this->assertTrue( $app::is_booted() );
	}

	/** @testdox It shoud be possble to pass the DI_Container interface as a depenedcy and have it populated with the current DI_Container implementation at initialisation.  */
	public function test_di_container_rule_defined_at_init(): void {
		$app              = ( new App_Factory )
			->with_wp_dice( true )
			->boot();
		$has_di_container = $app::make( Has_DI_Container::class );
		$this->assertTrue( $has_di_container->di_set() );
	}

	/** @testdox It should be possible to define additional registration middleware during the factory chained called. */
	public function test_pass_registration_middleware_during_factory_init(): void {
		$mock_middleware = $this->createMock( Registration_Middleware::class );

		$app = ( new App_Factory )
			->with_wp_dice( true )
			->registration_middleware( $mock_middleware )
			->boot();

		$registration    = Objects::get_property( $app, 'registration' );
		$middleware_list = Objects::get_property( $registration, 'middleware' );
		$this->assertContains( $mock_middleware, $middleware_list );
	}

	/** @testdox It should be possible to defined additional registration middleware during the factory chained called, but as a class name, not as an instance. */
	public function test_pass_registration_middleware_as_string_during_factory_init(): void {
		$app = ( new App_Factory )
			->with_wp_dice( true )
			->construct_registration_middleware( Mock_Registration_Middleware::class )
			->boot();

		$registration    = Objects::get_property( $app, 'registration' );
		$middleware_list = Objects::get_property( $registration, 'middleware' );
		$this->assertArrayHasKey( Mock_Registration_Middleware::class, $middleware_list );
	}
}
