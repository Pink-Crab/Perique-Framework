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
use PinkCrab\Core\Services\Registration\Middleware\Registerable_Middleware;

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
		$app                  = ( new App_Factory )->with_wp_di( true );
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
	}

	/** @testdox A classes which need to be registered, should be passable at setup. Allowing plugins to register hooks with WordPress */
	public function test_can_set_registration_classes(): void
	{
		# code...
	}

}
