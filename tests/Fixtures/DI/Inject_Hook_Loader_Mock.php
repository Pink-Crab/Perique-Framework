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

use PinkCrab\Perique\Interfaces\Inject_Hook_Loader;
use PinkCrab\Perique\Services\Container_Aware_Traits\Inject_Hook_Loader_Aware;

class Inject_Hook_Loader_Mock implements Inject_Hook_Loader {

	use Inject_Hook_Loader_Aware;

	/**
	 * Check if loader injected.
	 *
	 * @return bool
	 */
	public function has_loader(): bool {
		return null !== $this->loader;
	}
}
