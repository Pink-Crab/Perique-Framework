<?php

declare(strict_types=1);

/**
 * Mock object that implements Registerable
 *
 * @since 0.2.3
 * @author Glynn Quelch <glynn.quelch@gmail.com>
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 * @package PinkCrab\Core
 */

namespace PinkCrab\Core\Tests\Fixtures\Mock_Objects\Registerable;

use PinkCrab\Core\Interfaces\Registerable;
use PinkCrab\Loader\Loader;

class Registerable_Mock implements Registerable {

	/**
	 * Registers a single hook (Registerable_Mock) echos Registerable_Mock
	 *
	 * @param \PinkCrab\Loader\Loader $loader
	 * @return void
	 */
	public function register( Loader $loader ): void {
		$loader->action(
			'Registerable_Mock',
			function() {
				echo 'Registerable_Mock';
			}
		);
	}
}
