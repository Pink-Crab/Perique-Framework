<?php

declare(strict_types=1);

/**
 * Tests the default PHP Engine for the view/renderable interface.
 *
 * @since 0.1.0
 * @author Glynn Quelch <glynn.quelch@gmail.com>
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 * @package PinkCrab\Core
 */

namespace PinkCrab\Core\Tests\Registration;

use WP_UnitTestCase;
use PinkCrab\Core\Services\View\View;

class Test_View extends WP_UnitTestCase {

	/**
	 * Simple buffer for calling and catching function calls.
	 *
	 * @return void
	 */
	public function test_print_buffer(): void {
		$result = View::print_buffer(
			function() {
				echo 'ECHO...ECHO';
			}
		);

		$this->assertEquals( 'ECHO...ECHO', $result );
	}


}
