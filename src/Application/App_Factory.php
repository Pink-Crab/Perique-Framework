<?php

declare(strict_types=1);
/**
 * Factory for creating standard instances of the App.
 *
 * @author Glynn Quelch <glynn.quelch@gmail.com>
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 * @package PinkCrab\Perique
 * @since 0.4.0
 */

namespace PinkCrab\Perique\Application;

use Dice\Dice;
use PinkCrab\Loader\Hook_Loader;
use PinkCrab\Perique\Application\App;
use PinkCrab\Perique\Interfaces\Renderable;
use PinkCrab\Perique\Interfaces\DI_Container;
use PinkCrab\Perique\Services\View\PHP_Engine;
use PinkCrab\Perique\Services\Dice\PinkCrab_Dice;
use PinkCrab\Perique\Interfaces\Registration_Middleware;
use PinkCrab\Perique\Exceptions\App_Initialization_Exception;
use PinkCrab\Perique\Services\Registration\Registration_Service;
use PinkCrab\Perique\Services\Registration\Middleware\Hookable_Middleware;

class App_Factory {

	/**
	 * The app instance.
	 *
	 * @var App
	 */
	protected $app;

	public function __construct() {
		$this->app = new App();
	}

	/**
	 * Pre populates a standard instance of the App
	 * Uses the PinkCrab_Dice container
	 * Sets up registration and loader instances.
	 * Adds Hookable Middleware
	 *
	 * Just requires Class List, Config and DI Rules.
	 *
	 * @return self
	 */
	public function with_wp_dice( bool $include_default_rules = false ): self {
		$loader = new Hook_Loader();

		// Setup DI Container
		$container = PinkCrab_Dice::withDice( new Dice() );

		if ( $include_default_rules === true ) {
			$container->addRules( $this->default_di_rules() );
		}

		$this->app->set_container( $container );

		// Set registration middleware
		$this->app->set_registration_services( new Registration_Service() );

		$this->app->set_loader( $loader );

		// Include Hookable.
		$this->app->registration_middleware( new Hookable_Middleware() );

		return $this;
	}

	/**
	 * Returns the basic DI rules which are used to set.
	 * WPDB
	 * Renderable with PHP_Engine implementation
	 *
	 * @return array<mixed>
	 */
	protected function default_di_rules(): array {
		return array(
			'*' => array(
				'substitutions' => array(
					Renderable::class => new PHP_Engine( __DIR__ ),
				),
			),
		);
	}

	/**
	 * Set the DI rules
	 *
	 * @param array<string,array<string,string|object|callable|null|false|\Closure>> $rules
	 * @return self
	 */
	public function di_rules( array $rules ): self {
		$this->app->container_config(
			function( DI_Container $container ) use ( $rules ): void {
				$container->addRules( $rules );
			}
		);
		return $this;
	}

	/**
	 * Sets the registration class list.
	 *
	 * @param array<int, string> $class_list Array of fully namespaced class names.
	 * @return self
	 */
	public function registration_classes( array $class_list ): self {
		$this->app->registration_classes( $class_list );
		return $this;
	}

	/**
	 * Sets the apps internal config
	 *
	 * @param array<string, mixed> $app_config
	 * @return self
	 */
	public function app_config( array $app_config ): self {
		$this->app->set_app_config( $app_config );
		return $this;
	}

	/**
	 * Returns the populated app.
	 *
	 * @return \PinkCrab\Perique\Application\App
	 */
	public function app(): App {
		return $this->app;
	}

	/**
	 * Returns a booted version of the app.
	 *
	 * @return \PinkCrab\Perique\Application\App
	 */
	public function boot(): App {
		// Sets default settings if not already set.
		if ( ! $this->app->has_app_config() ) {
			$this->app_config( array() );
		}

		return $this->app->boot();
	}

	/**
	 * Add registration middleware
	 *
	 * @param Registration_Middleware $middleware
	 * @return self
	 * @throws App_Initialization_Exception Code 3
	 */
	public function registration_middleware( Registration_Middleware $middleware ): self {
		$this->app->registration_middleware( $middleware );
		return $this;
	}

	/**
	 * Add registration middleware as a class string.
	 * This is constructed via the DI Container before being added.
	 *
	 * @param class-string<Registration_Middleware> $class_name
	 * @return self
	 * @throws App_Initialization_Exception Code 1 If DI container not registered
	 * @throws App_Initialization_Exception Code 9 If class doesn't create as middleware.
	 */
	public function construct_registration_middleware( string $class_name ): self {
		$this->app->construct_registration_middleware( $class_name );
		return $this;
	}
}
