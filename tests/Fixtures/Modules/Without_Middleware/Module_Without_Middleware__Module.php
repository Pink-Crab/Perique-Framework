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

namespace PinkCrab\Perique\Tests\Fixtures\Modules\Without_Middleware;

use PinkCrab\Loader\Hook_Loader;
use PinkCrab\Perique\Interfaces\Module;
use PinkCrab\Perique\Application\App_Config;
use PinkCrab\Perique\Interfaces\DI_Container;

class Module_Without_Middleware__Module implements Module {

	/**
	 * Sets a log which can be used to check hooks are fired.
	 *
	 * @var array<string,array<string, object>>
	 */
	public $log = array();


	/**
	 * Returns the middleware to be used for the module.
	 *
	 * @return string
	 */
	public function get_middleware(): ?string {
		return null;
	}

	/**
	 * Callback fired before the Application is booted.
	 *
	 * @pram App_Config $config
	 * @pram Hook_Loader $loader
	 * @pram DI_Container $di_container
	 * @return void
	 */
	public function pre_boot( App_Config $config, Hook_Loader $loader, DI_Container $di_container ): void {
		$this->log[ __FUNCTION__ ] = array(
			'config'       => $config,
			'loader'       => $loader,
			'di_container' => $di_container,
		);
		echo __FUNCTION__;
	}

	/**
	 * Callback fired before registration is started.
	 *
	 * @pram App_Config $config
	 * @pram Hook_Loader $loader
	 * @pram DI_Container $di_container
	 * @return void
	 */
	public function pre_register( App_Config $config, Hook_Loader $loader, DI_Container $di_container ): void {
		$this->log[ __FUNCTION__ ] = array(
			'config'       => $config,
			'loader'       => $loader,
			'di_container' => $di_container,
		);
		echo __FUNCTION__;
	}

	/**
	 * Callback fired after registration is completed.
	 *
	 * @pram App_Config $config
	 * @pram Hook_Loader $loader
	 * @pram DI_Container $di_container
	 * @return void
	 */
	public function post_register( App_Config $config, Hook_Loader $loader, DI_Container $di_container ): void {
		$this->log[ __FUNCTION__ ] = array(
			'config'       => $config,
			'loader'       => $loader,
			'di_container' => $di_container,
		);
		echo __FUNCTION__;
	}
}
