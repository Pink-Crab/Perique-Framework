<?php

declare(strict_types=1);
/**
 * Dependency that receives DI Container as method injectable.
 *
 * @since 0.2.0
 * @author Glynn Quelch <glynn.quelch@gmail.com>
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 * @package PinkCrab\Perique
 */

namespace PinkCrab\Perique\Tests\Fixtures\DI;

use PinkCrab\Perique\Interfaces\Inject_App_Config;
use PinkCrab\Perique\Services\Container_Aware_Traits\Inject_App_Config_Aware;

class Inject_App_Config_Mock implements Inject_App_Config {

	use Inject_App_Config_Aware;

	/**
	 * Check if app_config injected.
	 *
	 * @return bool
	 */
	public function has_app_config(): bool {
		return null !== $this->app_config;
	}
}
