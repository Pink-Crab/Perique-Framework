<?php

declare(strict_types=1);

/**
 * Mock object that implements Hookable
 *
 * @since 0.2.3
 * @author Glynn Quelch <glynn.quelch@gmail.com>
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 * @package PinkCrab\Perique
 */

namespace PinkCrab\Perique\Tests\Fixtures\Mock_Objects\Hookable;

use PinkCrab\Loader\Hook_Loader;
use PinkCrab\Perique\Interfaces\Hookable;

class Hookable_Mock implements Hookable {

	/**
	 * Registers a single hook (Hookable_Mock) echos Hookable_Mock
	 *
	 * @param \PinkCrab\Loader\Hook_Loader $loader
	 * @return void
	 */
	public function register( Hook_Loader $loader ): void {
		$loader->action(
			'Hookable_Mock',
			function() {
				echo 'Hookable_Mock';
			}
		);
	}
}
