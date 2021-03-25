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
use PinkCrab\Core\Services\Registration\Registration_Service;

class Test_App_Factory extends WP_UnitTestCase {

	// public static $app;

	// public static function setUpBeforeClass(): void {
	// 	// self::$app = App_Factory::with_wp_di();
	// }

	// public function setUp(): void {
	// 	parent::setUp();
	// 	self::$app = App_Factory::with_wp_di();
	// }


	public function tearDown(): void {
		$app = new App();
		Objects::set_property( $app, 'app_config', null );
		Objects::set_property( $app, 'container', null );
		Objects::set_property( $app, 'registration', null );
		Objects::set_property( $app, 'loader', null );
		Objects::set_property( $app, 'booted', false );
		$app = null;
	}


	/** @testdox When requested the App Factory can create an instance of App popualted with WP_Dice, Registerables Middleware, Loader and Registration Service. */
	public function test_can_create_with_wp_dice(): void {
		$app                  = App_Factory::with_wp_di();
		$registration_service = new Registration_Service();

		$this->assertInstanceOf( App::class, $app );
		$this->assertInstanceOf(
			DI_Container::class,
			Objects::get_property( $app, 'container' )
		);
		$this->assertInstanceOf(
			Loader::class,
			Objects::get_property( $app, 'loader' )
		);
		$this->assertInstanceOf( Registration_Service::class, $registration_service );
		dump( $registration_service, $app->boot() );

		// Should contain Registerable_
	}

}
