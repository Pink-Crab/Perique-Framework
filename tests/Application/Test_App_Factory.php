<?php

declare(strict_types=1);
/**
 * Tests the App Factory
 *
 * @since 0.4.0
 * @author Glynn Quelch <glynn.quelch@gmail.com>
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 * @package PinkCrab\Core
 */

namespace PinkCrab\Core\Tests\Application;

use WP_UnitTestCase;
use PinkCrab\Loader\Loader;
use PinkCrab\Core\Application\App;
use Gin0115\WPUnit_Helpers\Objects;
use PinkCrab\Core\Application\_App;
use PinkCrab\Core\Application\App_Factory;
use PinkCrab\Core\Interfaces\DI_Container;
use PinkCrab\Core\Tests\Fixtures\DI\Interface_A;
use PinkCrab\Core\Tests\Fixtures\Mock_Objects\Interface_Get;
use PinkCrab\Core\Services\Registration\Registration_Service;
use PinkCrab\Core\Tests\Fixtures\Mock_Objects\Parent_Dependency;
use PinkCrab\Core\Services\Registration\Middleware\Registerable_Middleware;
use PinkCrab\Core\Tests\Fixtures\Mock_Objects\Registerable\Registerable_Mock;

class Test_App_Factory extends WP_UnitTestCase {

	/**
	 * @method self::unset_app_instance();
	 */
	use App_Helper_Trait;

	public function tearDown(): void {
		self::unset_app_instance();
	}

	/** @testdox When requested the App Factory can create an instance of App popualted with WP_Dice, Registerables Middleware, Loader and Registration Service. */
	public function test_can_create_with_wp_dice(): void {
		$app = ( new App_Factory )
			->with_wp_di( true )
			->app();

		$this->assertInstanceOf( App::class, $app );
		$this->assertInstanceOf(
			DI_Container::class,
			Objects::get_property( $app, 'container' )
		);
		$this->assertInstanceOf(
			Loader::class,
			Objects::get_property( $app, 'loader' )
		);
	}

	/** @testdox A classes which need to be registered, should be passable at setup. Allowing plugins to register hooks with WordPress */
	public function test_can_set_registration_classes(): void {
		$app = ( new App_Factory )
			->with_wp_di( true )
			->registration_classses( array( Registerable_Mock::class ) )->app();

		$registration_service = Objects::get_property( $app, 'registration' );
		$this->assertContains(
			Registerable_Mock::class,
			Objects::get_property( $registration_service, 'class_list' )
		);
	}

	/** @testdox It should be possible to pass custom rules to the Dependency Injection container to handle classes whos depenedencies cant be inferred. */
	public function test_can_set_di_rule() {
		$app = ( new App_Factory )
			->with_wp_di( true )
			->di_rules( include FIXTURES_PATH . '/Application/dependencies.php' )
			->app();

		$container = Objects::get_property( $app, 'container' );
		$this->assertTrue( $container->has( Interface_A::class ) );
	}

	/** @testdox It should be possible to set custom settings to the apps config. */
	public function test_can_set_config(): void {
		$app = ( new App_Factory )
			->with_wp_di( true )
			->app_config( include FIXTURES_PATH . '/Application/settings.php' )
			->app();

		$app_config = Objects::get_property( $app, 'app_config' );
		$this->assertEquals( 'test_value', $app_config->test_key );
	}

	/** @testdox It should be possible to boot the app from a chained factory call. If no config is set, the defaults should be used. */
	public function test_can_boot_app_from_factory_chain(): void {
		$app = ( new App_Factory )
			->with_wp_di( true )
			->boot();
		$this->assertTrue( $app::is_booted() );
	}

}
