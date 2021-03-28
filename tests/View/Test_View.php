<?php

declare(strict_types=1);

/**
 * Tests for the view service class
 *
 * @since 0.1.0
 * @author Glynn Quelch <glynn.quelch@gmail.com>
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 * @package PinkCrab\Core
 */

namespace PinkCrab\Core\Tests\View;

use WP_UnitTestCase;
use Gin0115\WPUnit_Helpers\Objects;
use PinkCrab\Core\Services\View\View;
use PinkCrab\Core\Services\View\PHP_Engine;

class Test_View extends WP_UnitTestCase {

	/**
	 * Holds a temp instance to the PHP_ENgine.
	 *
	 * @var Renderable
	 */
	protected $php_engine;

	public function setUp(): void {
		parent::setUp();

		$this->php_engine = new PHP_Engine( \dirname( __DIR__, 1 ) . '/Fixtures/Views/' );
	}


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

	/**
	 * Test view hold instnaces of a render engine.
	 *
	 * @return void
	 */
	public function test_can_be_constructed_with_render_engine(): void {

		$view = new View( $this->php_engine );

		$this->assertSame(
			$this->php_engine,
			Objects::get_property( $view, 'engine' )
		);
	}

	/**
	 * Test can return the view as a string.
	 *
	 * @return void
	 */
	public function test_return_single_template(): void {
		$this->assertEquals(
			'Hello World',
			( new View( $this->php_engine ) )->render(
				'hello',
				array( 'hello' => 'Hello World' ),
				View::RETURN_VIEW
			)
		);
	}

	/**
	 * Test can render partial template from parent.
	 *
	 * @return void
	 */
	public function test_render_partial_template(): void {
		$this->expectOutputString( 'partial_value' );
		( new View( $this->php_engine ) )->render(
			'layout',
			array( 'partial_data' => array( 'partial' => 'partial_value' ) ),
			View::PRINT_VIEW // Optional as print view is default.
		);
	}


}
