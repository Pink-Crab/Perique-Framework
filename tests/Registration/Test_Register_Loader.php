<?php

declare(strict_types=1);

/**
 * Tests the register loader service.
 *
 * @since 0.1.0
 * @author Glynn Quelch <glynn.quelch@gmail.com>
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 * @package PinkCrab\Core
 */

namespace PinkCrab\Core\Tests\Registration;

use stdClass;
use WP_UnitTestCase;
use PinkCrab\Loader\Loader;
use PinkCrab\Core\Application\App;
use PinkCrab\PHPUnit_Helpers\Reflection;
use PinkCrab\Core\Services\Registration\Register_Loader;
use PinkCrab\Core\Tests\Fixtures\Mock_Objects\Registerable\Registerable_Mock;

class Test_Register_Loader extends WP_UnitTestCase {

	/**
	 * Undocumented function

	 * @return void
	 */
	public function test_only_registers_registerable_bjects(): void {
		$objects = array(
			'pass' => Registerable_Mock::class,
			'fail' => stdClass::class,
		);

		$loader = new Loader();

		Register_Loader::initalise( App::get_instance(), $objects, $loader );

		$global_hooks = Reflection::get_private_property( $loader, 'global' );
		$this->assertCount( 1, $global_hooks );
		$this->assertEquals( 'Registerable_Mock', $global_hooks->pop()['handle'] );
	}
}
