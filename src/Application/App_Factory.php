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
use PinkCrab\Core\Application\App;
use PinkCrab\Core\Services\View\PHP_Engine;
use PinkCrab\Core\Services\Dice\PinkCrab_WP_Dice_Adaptor;
use PinkCrab\Core\Services\Registration\Registration_Service;
use PinkCrab\Core\Services\Registration\Middleware\Registerable_Middleware;

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
	public static function with_wp_di( bool $include_default_rules = false ): App {
		$app    = new App();
		$loader = new Loader();

		// Setup DI Container
		$container = PinkCrab_WP_Dice_Adaptor::constructWith( new Dice() );

		$app->set_container( $container );

		// Set registration middleware
		$app->define_registration_services( new Registration_Service(), $loader );

		// Include Registerables.
		$app->registration_middleware( new Registerable_Middleware( $loader, $container ) );

		return $app;
	}

	/**
	 * Returns the basic DI rules which are used to set.
	 * WPDB
	 * Renderable with PHP_Engine implementation
	 * WP_Cache Access
	 *
	 * @return array
	 */
	protected static function default_di_rules( App $app ): array {
		return array(
			'*' => array(
				'substitutions' => array(
					Renderable::class => new PHP_Engine( __DIR__ ),
					\WPDB::class      => $GLOBALS['wpdb'],
					\WP_Cache::class  => $GLOBALS['wp_cache'],
					App_Config::class => $app::make( App_Config::class ),
				),
			),
		);
	}
}
