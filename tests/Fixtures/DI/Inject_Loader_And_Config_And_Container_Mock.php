<?php

declare(strict_types=1);
/**
 * Dependency that receives DI Container and Hook_Loader as method injectable.
 *
 * @since 2.0.0
 * @author Glynn Quelch <glynn.quelch@gmail.com>
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 * @package PinkCrab\Perique
 */

namespace PinkCrab\Perique\Tests\Fixtures\DI;

use PinkCrab\Perique\Interfaces\Inject_App_Config;
use PinkCrab\Perique\Interfaces\Inject_Hook_Loader;
use PinkCrab\Perique\Interfaces\Inject_DI_Container;
use PinkCrab\Perique\Services\Container_Aware_Traits\Inject_App_Config_Aware;
use PinkCrab\Perique\Services\Container_Aware_Traits\Inject_Hook_Loader_Aware;
use PinkCrab\Perique\Services\Container_Aware_Traits\Inject_DI_Container_Aware;

class Inject_Loader_And_Config_And_Container_Mock implements Inject_App_Config, Inject_DI_Container  , Inject_Hook_Loader {

	use Inject_DI_Container_Aware;
	use Inject_Hook_Loader_Aware;
	use Inject_App_Config_Aware;

	/**
	 * Check if loader injected.
	 *
	 * @return bool
	 */
	public function has_loader(): bool {
		return null !== $this->loader;
	}

	/**
	 * Get the loader instance.
	 *
	 * @return \PinkCrab\Loader\Hook_Loader
	 */
	public function get_loader(): \PinkCrab\Loader\Hook_Loader {
		return $this->loader;
	}

		/**
	 * Check if container injected.
	 *
	 * @return bool
	 */
	public function has_container(): bool {
		return null !== $this->di_container;
	}

	/**
	 * Get the container instance.
	 *
	 * @return \PinkCrab\Perique\Services\DI_Container
	 */
	public function get_container(): \PinkCrab\Perique\Interfaces\DI_Container {
		return $this->di_container;
	}

	/**
	 * Check if app_config injected.
	 *
	 * @return bool
	 */
	public function has_app_config(): bool {
		return null !== $this->app_config;
	}

	/**
	 * Get the app config instance.
	 * 
	 * @return \PinkCrab\Perique\Application\App_Config
	 */
	public function get_app_config(): \PinkCrab\Perique\Application\App_Config {
		return $this->app_config;
	}
}
