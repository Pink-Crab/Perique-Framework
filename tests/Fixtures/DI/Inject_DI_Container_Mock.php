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

use PinkCrab\Perique\Interfaces\Inject_DI_Container;
use PinkCrab\Perique\Services\Container_Aware_Traits\Inject_DI_Container_Aware;

class Inject_DI_Container_Mock implements Inject_DI_Container {

	use Inject_DI_Container_Aware;

	/**
	 * Check if container injected.
	 *
	 * @return bool
	 */
	public function has_container(): bool {
		return null !== $this->di_container;
	}
}
