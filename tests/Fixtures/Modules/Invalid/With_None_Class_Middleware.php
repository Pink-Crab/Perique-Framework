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

namespace PinkCrab\Perique\Tests\Fixtures\Modules\Invalid;

use PinkCrab\Loader\Hook_Loader;
use PinkCrab\Perique\Interfaces\Module;
use PinkCrab\Perique\Application\App_Config;
use PinkCrab\Perique\Interfaces\DI_Container;

class With_None_Class_Middleware implements Module {


	/**
	 * Returns the middleware to be used for the module.
	 *
	 * @return string
	 */
	public function get_middleware(): ?string {
		return 'None class';
	}

	/**
	 * Callback fired before the Application is booted.
	 *
	 * @pram App_Config $config
	 * @pram Hook_Loader $loader
	 * @pram DI_Container $di_container
	 * @return void
	 */
	public function pre_boot( App_Config $config, Hook_Loader $loader, DI_Container $di_container ): void {}

	/**
	 * Callback fired before registration is started.
	 *
	 * @pram App_Config $config
	 * @pram Hook_Loader $loader
	 * @pram DI_Container $di_container
	 * @return void
	 */
	public function pre_register( App_Config $config, Hook_Loader $loader, DI_Container $di_container ): void {}

	/**
	 * Callback fired after registration is completed.
	 *
	 * @pram App_Config $config
	 * @pram Hook_Loader $loader
	 * @pram DI_Container $di_container
	 * @return void
	 */
	public function post_register( App_Config $config, Hook_Loader $loader, DI_Container $di_container ): void {}
}
