<?php

declare(strict_types=1);

/**
 * Tests the default PHP Engine for the view/renderable interface.
 *
 * @since 0.1.0
 * @author Glynn Quelch <glynn.quelch@gmail.com>
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 * @package PinkCrab\Perique
 */

namespace PinkCrab\Perique\Tests\View;

use Exception;
use WP_UnitTestCase;
use PinkCrab\Perique\Services\View\View;
use PinkCrab\Perique\Services\View\PHP_Engine;



class Test_PHP_Engine extends WP_UnitTestCase {

	/**
	 * View
	 *
	 * @var View
	 */
	public $view;

	/**
	 * Setup tests with an instance of the loader.
	 *
	 * @return void
	 */
	public function setUp() {
		parent::setUp();
		$this->view = new PHP_Engine( \dirname( __DIR__, 1 ) . '/Fixtures/Views/' );
	}

	/**
	 * Test a single template can be rendered.
	 *
	 * @return void
	 */
	public function test_render_single_template(): void {
		$this->expectOutputString( 'Hello World' );
		$this->view->render( 'hello', array( 'hello' => 'Hello World' ) );
	}

	/**
	 * Test can return the view as a string.
	 *
	 * @return void
	 */
	public function test_return_single_template(): void {
		$this->assertEquals(
			'Hello World',
			$this->view->render(
				'hello',
				array( 'hello' => 'Hello World' ),
				View::RETURN_VIEW
			)
		);
	}

	/**
	 * Test can print partial template from parent.
	 *
	 * @return void
	 */
	public function test_print_partial_template(): void {
		$this->expectOutputString( 'partial_value' );
		$this->view->render(
			'layout',
			array( 'partial_data' => array( 'partial' => 'partial_value' ) ),
			View::PRINT_VIEW // Optional as print view is default.
		);
	}

	/**
	 * The partial returns HTML then echos.
	 *
	 * @return void
	 */
	public function test_returns_partial_from_template(): void {
		$this->expectOutputString( 'partial_value' );
		$this->view->render(
			'returns_partial',
			array( 'partial_data' => array( 'partial' => 'partial_value' ) ),
			View::PRINT_VIEW // Optional as print view is default.
		);
	}

	/**
	 * Test can return the view as a string.
	 *
	 * @return void
	 */
	public function test_return_partial_template(): void {
		$this->assertEquals(
			'partial_value',
			$this->view->render(
				'layout',
				array( 'partial_data' => array( 'partial' => 'partial_value' ) ),
				View::RETURN_VIEW
			)
		);
	}

	/**
	 * Test that files that done exist, throw exception.
	 *
	 * @return void
	 */
	public function test_throws_exception_if_view_not_found(): void {
		$this->expectException( Exception::class );
		$this->view->render( 'DOESNT_EXIST', array( 'data' => 42 ) );
	}

	/**
	 * Test .php removed from view if used.
	 *
	 * @return void
	 */
	public function test_strips_php_from_view_path(): void {
		$this->expectOutputString( 'Hello World' );
		$this->view->render( 'hello.php', array( 'hello' => 'Hello World' ) );
	}

	/**
	 * Test removes leading slash.
	 *
	 * @return void
	 */
	public function test_strips_leading_slash_view_path(): void {
		$this->expectOutputString( 'Hello World' );
		$this->view->render( '/hello', array( 'hello' => 'Hello World' ) );
	}

	/**
	 * Test throws exception if view path doesnt exist.
	 *
	 * @return void
	 */
	public function test_throws_exception_view_dir_not_exists(): void {
		$this->expectException( Exception::class );
		new PHP_Engine( \dirname( __DIR__, 1 ) . '/Fixtures/Fake_Views/' );
	}

	/**
	 * Test the view path has the leading slash added if not set.
	 * Also additonal view path cleanup tests.
	 *
	 * @return void
	 */
	public function test_adds_trailing_slash_to_view_path(): void {
		$this->expectOutputString( 'Hello World' );
		$view = new PHP_Engine( \dirname( __DIR__, 1 ) . '/Fixtures/Views' );
		$view->render( '/hello.php', array( 'hello' => 'Hello World' ) );
	}


}
