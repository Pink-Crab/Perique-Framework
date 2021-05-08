<?php

declare(strict_types=1);
/**
 * Dependency C
 * Extends Abstract_B
 *
 * @since 0.2.0
 * @author Glynn Quelch <glynn.quelch@gmail.com>
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 * @package PinkCrab\Perique
 */

namespace PinkCrab\Perique\Tests\Fixtures\DI;

use PinkCrab\Perique\Tests\Fixtures\DI\Abstract_B;

class Dependency_C extends Abstract_B {
	public function foo() {
		return self::class;
	}
}
