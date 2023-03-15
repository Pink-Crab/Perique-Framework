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

use PinkCrab\Perique\Interfaces\Registration_Middleware;
use PinkCrab\Perique\Services\Container_Aware_Traits\Inject_Hook_Loader_Aware;
use PinkCrab\Perique\Services\Container_Aware_Traits\Inject_DI_Container_Aware;

class Module_With_Middleware__Middleware implements Registration_Middleware {
	use Inject_Hook_Loader_Aware, Inject_DI_Container_Aware;

	/** @inheritDoc */
	public function setup(): void {

	}

	/** @inheritDoc */
	public function tear_down(): void {

	}

	/** @inheritDoc */
	public function process( $class ) {
		return $class;
	}
}
