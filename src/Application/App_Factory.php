<?php

declare(strict_types=1);
/**
 * Factory for creating standard instances of the App.
 *
 * @author Glynn Quelch <glynn.quelch@gmail.com>
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 * @package PinkCrab\Core
 * @since 0.4.0
 */

namespace PinkCrab\Core\Application;

use Dice\Dice;
use PinkCrab\Loader\Loader;
use PinkCrab\Core\Services\Dice\WP_Dice;
use PinkCrab\Core\Services\Dice\WP_Dice_DI_Container_Bridge;
use PinkCrab\Core\Services\Registration\Registration_Service;
use PinkCrab\Core\Services\Registration\Middleware\Registerable_Middleware;
use PinkCrab\Core\Tests\Fixtures\Mock_Objects\Registerable\Registerable_Mock;

class App_Factory {

	/**
	 * Pre populates a standard isntance of the App
	 * Uses the WP_Dice container
	 * Sets up registration and loader instances.
	 * Adds Registerable Middleware
	 *
	 * Just requires Class List, Config and DI Rules.
	 *
	 * @return App
	 */
	public static function with_wp_di( array $di_rules = array() ): _App {
		$app       = new _App();
		$loader    = new Loader();
		$container = WP_Dice::constructWith( new Dice() );
		$container->addRules( $di_rules );

		// Bind the container.
		$app->set_container(
			new WP_Dice_DI_Container_Bridge(
				$container
			)
		);

		$app->define_registration_services(
			new Registration_Service(),
			$loader
		);

		$app->registration_middleware( new Registerable_Middleware( $loader, $app ) );

		return $app;
	}
}
