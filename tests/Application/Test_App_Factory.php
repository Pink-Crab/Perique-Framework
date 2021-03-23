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
use PinkCrab\Core\Application\App_Factory;
use PinkCrab\Core\Interfaces\DI_Container;

class Test_App_Factory extends WP_UnitTestCase {

	/** @testdox When requested the App Factory can create an instance of App popualted with WP_Dice, Registerables Middleware, Loader and Registration Service. */
	public function test_can_create_with_wp_dice(): void {
		$app = App_Factory::with_wp_di();
		dump( $app );

		// Set some extra rules.
		$app->container_config(
			function( DI_Container $container ) {
                dump($container);
			}
		);
	}
}
