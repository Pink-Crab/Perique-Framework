<?php

declare(strict_types=1);
/**
 * Validation class for an app instance.
 * Called before booting, to ensure all required properties are set.
 *
 * @author Glynn Quelch <glynn.quelch@gmail.com>
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 * @package PinkCrab\Perique
 * @since 0.4.0
 */

namespace PinkCrab\Perique\Application;

use PinkCrab\Perique\Application\App;
use Reflection;
use ReflectionProperty;

class App_Validation {

	/** @var string */
	public const ERROR_MESSAGE_TEMPLATE = '%s was not set in App';
	/** @var string */
	public const ERROR_MESSAGE_APP_BOOTED = 'App already booted';

	/**
	 * Required properties
	 * Key is the property and value is if "static"
	 *
	 * @var array<string,bool>
	 */
	protected $required_properties = array(
		'container'    => true,
		'app_config'   => true,
		'registration' => false,
		'loader'       => false,
	);

	/** @var array<string> */
	public $errors = array();

	/** @var App */
	protected $app;

	public function __construct( App $app ) {
		$this->app = $app;
	}

	/**
	 * Checks all properties are set and app isn't already booted
	 *
	 * @return bool
	 */
	public function validate(): bool {
		$this->already_booted();
		$this->validate_properties_set();
		return count( $this->errors ) === 0;
	}

	/**
	 * Check all properties are not default values
	 * Sets an entry in the error array if not set.
	 *
	 * @return void
	 */
	protected function validate_properties_set(): void {
		foreach ( $this->required_properties as $property => $is_static ) {
			$property_reflection = new ReflectionProperty( $this->app, $property );
			$property_reflection->setAccessible( true );
			if ( empty( $property_reflection->getValue( $this->app ) ) ) {
				$this->errors[] = \sprintf( self::ERROR_MESSAGE_TEMPLATE, $property );
			}
		}
	}

	/**
	 * Checks if the app has already been booted.
	 *
	 * @return void
	 */
	protected function already_booted(): void {
		if ( $this->app->is_booted() === true ) {
			$this->errors[] = self::ERROR_MESSAGE_APP_BOOTED;
		}
	}
}
