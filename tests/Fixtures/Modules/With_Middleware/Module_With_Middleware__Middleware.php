<?php

declare(strict_types=1);
/**
 * Stub Module for testing.
 *
 * Uses the Foo_Middleware
 *
 * @since 2.0.0
 * @author Glynn Quelch <glynn.quelch@gmail.com>
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 * @package PinkCrab\Perique
 */

namespace PinkCrab\Perique\Tests\Fixtures\Modules\With_Middleware;

use PinkCrab\Loader\Hook_Loader;
use PinkCrab\Perique\Interfaces\DI_Container;
use PinkCrab\Perique\Interfaces\Registration_Middleware;
use PinkCrab\Perique\Services\Container_Aware_Traits\Inject_Hook_Loader_Aware;
use PinkCrab\Perique\Services\Container_Aware_Traits\Inject_DI_Container_Aware;


class Module_With_Middleware__Middleware implements Registration_Middleware {
	use Inject_Hook_Loader_Aware, Inject_DI_Container_Aware;

	/**
	 * Sets a log which can be used to check hooks are fired.
	 *
	 * @var array<string,array<string, object>>
	 */
	public static $log = array();

	/**
	 * Holds a log of all actions carried out.
	 *
	 * @var array<string>
	 */
	public static $actions = array();

	/** @inheritDoc */
	public function setup(): void {
		self::$log[] = __FUNCTION__;
	}

	/** @inheritDoc */
	public function tear_down(): void {
		self::$log[] = __FUNCTION__;
	}

	/** @inheritDoc */
	public function process( $class ) {
		self::$log[] = __FUNCTION__;

		return $class;
	}

		/**
	 * Override the set_di_container method to allow for checking it was set.
	 *
	 * @param DI_Container $di_container
	 * @return void
	 */
	public function set_di_container( DI_Container $di_container ): void {
		self::$log[]                   = __FUNCTION__;
		self::$actions[__FUNCTION__] = $di_container;
		$this->di_container            = $di_container;
	}

	/**
	 * Override the set_loader method to allow for checking it was set.
	 *
	 * @param Hook_Loader $loader
	 * @return void
	 */
	public function set_hook_loader( Hook_Loader $loader ): void {
		self::$log[]             = __FUNCTION__;
		self::$actions[__FUNCTION__] = $loader;
		$this->loader            = $loader;
	}
}
