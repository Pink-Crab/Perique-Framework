<?php

declare(strict_types=1);
/**
 * Validation class for an app instance.
 * Called before booting, to ensure all required properties are set.
 *
 * @author Glynn Quelch <glynn.quelch@gmail.com>
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 * @package PinkCrab\Core
 * @since 0.4.0
 */

namespace PinkCrab\Core\Application;

use PinkCrab\Core\Application\App;
use Reflection;
use ReflectionProperty;

class App_Validation {

	/**
	 * Required properties
	 *
	 * @var array{property:string,is_static:bool}
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
		$this->app = clone $app;
	}

	/**
	 * Checks all properties
	 *
	 * @return bool
	 */
	public function validate(): bool {
		$this->validate_properties_set();
		$this->already_booted();
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
			if ( $property_reflection->isDefault() ) {
				$this->errors[] = \sprintf( '%s was not set in App', $property );
			}
		}
	}

	/**
	 * Checks if the app has already been booted.
	 *
	 * @return void
	 */
	public function already_booted(): void {
		$property_reflection = new ReflectionProperty( $this->app, 'booted' );
		$property_reflection->setAccessible( true );
		if ( $property_reflection->getValue( $this->app ) === true ) {
			$this->errors[] = 'App already booted';
		}
	}
}
