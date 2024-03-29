<?php

declare(strict_types=1);

/**
 * Helper trait for all App tests
 * Includes clearing the internal state of an existing instance.
 *
 * @since 0.4.0
 * @author Glynn Quelch <glynn.quelch@gmail.com>
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 * @package PinkCrab\Perique
 */

namespace PinkCrab\Perique\Tests\Application;

use PinkCrab\Perique\Application\App;
use Gin0115\WPUnit_Helpers\Objects;
use PinkCrab\Perique\Services\Registration\Module_Manager;
use PinkCrab\Loader\Hook_Loader;
use PinkCrab\Perique\Services\Registration\Registration_Service;
use PinkCrab\Perique\Services\Dice\PinkCrab_Dice;
use Dice\Dice;


trait App_Helper_Trait {

	/**
	 * Resets the any existing App isn'tance with default properties.
	 *
	 * @return void
	 */
	protected static function unset_app_instance(): void {
		$app = new App( \FIXTURES_PATH );
		Objects::set_property( $app, 'app_config', null );
		Objects::set_property( $app, 'container', null );
		Objects::set_property( $app, 'module_manager', null );
		Objects::set_property( $app, 'loader', null );
		Objects::set_property( $app, 'booted', false );
		$app = null;
	}

	/**
	 * Returns an instance of app (not booted) populated with actual
	 * service objects.
	 *
	 * No registration classes are added, di has no rules, loader is empty
	 * but there is the settings from the Fixtures/Application added so we can
	 * use template paths in the App:view() tests.
	 *
	 * Is a plain and basic instance.
	 *
	 * @return App
	 */
	protected function pre_populated_app_provider(): App {
		// For clear.
		self::unset_app_instance();
		
		// Build and populate the app.
		$container    = new PinkCrab_Dice( new Dice() );
		$app          = new App( FIXTURES_PATH );
		$registration = new Registration_Service( $container );
		$loader       = new Hook_Loader();

		$app->set_container( $container );
		$app->set_module_manager( new Module_Manager( $container, $registration ) );
		$app->set_loader( $loader );
		$app->set_app_config( include FIXTURES_PATH . '/Application/settings.php' );

		return $app;
	}

}
