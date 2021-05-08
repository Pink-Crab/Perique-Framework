<?php

declare(strict_types=1);

/**
 * Mock object that implements Registerable
 *
 * @since 0.2.3
 * @author Glynn Quelch <glynn.quelch@gmail.com>
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 * @package PinkCrab\Perique
 */

namespace PinkCrab\Perique\Tests\Fixtures\Mock_Objects\Registerable;

use PinkCrab\Loader\Hook_Loader;
use PinkCrab\Perique\Interfaces\Registerable;

class Registerable_Mock implements Registerable {

	/**
	 * Registers a single hook (Registerable_Mock) echos Registerable_Mock
	 *
	 * @param \PinkCrab\Loader\Hook_Loader $loader
	 * @return void
	 */
	public function register( Hook_Loader $loader ): void {
		$loader->action(
			'Registerable_Mock',
			function() {
				echo 'Registerable_Mock';
			}
		);
	}
}
