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

namespace PinkCrab\Perique\Tests\Unit\View;

use Exception;
use WP_UnitTestCase;
use PinkCrab\Perique\Services\View\View;
use PinkCrab\Perique\Services\View\PHP_Engine;
use PinkCrab\Perique\Services\View\View_Model;
use PinkCrab\Perique\Tests\Fixtures\Mock_Objects\View_Components\Span;

/**
 * @group view
 * @group unit
 */
class Test_PHP_Engine extends WP_UnitTestCase {

	/**
	 * View
	 *
	 * @var PHP_Engine
	 */
	public $view;

	/**
	 * Setup tests with an instance of the loader.
	 *
	 * @return void
	 */
	public function setUp() : void {
		parent::setUp();
		$this->view = new PHP_Engine( FIXTURES_PATH . '/views/' );
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
			array( 'partial_data' => array( 'partial' => 'partial_value' ) )
		);
	}

	/**
	 * The partial returns HTML then echos.
	 *
	 * @return void
	 */
	public function test_returns_partial_from_template_using_render_in_view(): void {
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
		new PHP_Engine( \dirname( __DIR__, 1 ) . '/Fixtures/Fake_views/' );
	}

	/**
	 * Test the view path has the leading slash added if not set.
	 * Also additonal view path cleanup tests.
	 *
	 * @return void
	 */
	public function test_adds_trailing_slash_to_view_path(): void {
		$this->expectOutputString( 'Hello World' );
		$view = new PHP_Engine( FIXTURES_PATH . '/views' );
		$view->render( '/hello.php', array( 'hello' => 'Hello World' ) );
	}

	/** @testdox An exception should be thrown if the engine attempts to render a component with setting the compiler. */
	public function test_throws_exception_if_component_not_set(): void {
		$this->expectException( Exception::class );
		$this->expectExceptionMessage( 'No component compiler passed to PHP_Engine' );
		$this->view->component( new Span( 'class', 'value' ) );
	}

	/* @testdox A test where a template can be loaded from a sub directory using dot notation without file extension. */
	public function test_can_render_path_using_dot_notation(): void {
		$this->expectOutputString( 'foo' );
		$this->view->render(
			'sub_path.template',
			array( 'variable' => 'foo' ),
			View::PRINT_VIEW // Optional as print view is default.
		);
	}

	/* @testdox A test where a template can be loaded from a sub directory using dot notation with file extension. */
	public function test_can_render_path_using_dot_notation_with_extension(): void {
		$this->expectOutputString( 'foo' );
		$this->view->render(
			'sub_path.php.bar.php',
			array( 'variable' => 'foo' ),
			View::PRINT_VIEW // Optional as print view is default.
		);
	}

	/** @testdox It should be possible to define the base path for view using dot notation. */
	public function test_can_set_base_path_using_dot_notation(): void {
		$this->expectOutputString( 'foo' );
		$view = new PHP_Engine( \dirname( __DIR__, 2 ) . '.Fixtures.views.' );
		$view->render(
			'sub_path.template',
			array( 'variable' => 'foo' ),
			View::PRINT_VIEW // Optional as print view is default.
		);
	}

	/** @testdox It should be possible to use filepaths with or without the .php extensions */
	public function test_can_render_path_with_or_without_php_extension(): void {
		function() {
			$this->expectOutputString( 'foo' );
			$this->view->render(
				'sub_path.template',
				array( 'variable' => 'foo' ),
				View::PRINT_VIEW // Optional as print view is default.
			);
		};

		$this->expectOutputString( 'foo' );
		$this->view->render(
			'sub_path.template.php',
			array( 'variable' => 'foo' ),
			View::PRINT_VIEW // Optional as print view is default.
		);
	}

	/** @testdox It should be possible to access the base_path from the engine. */
	public function test_can_get_base_path(): void {
		$path = FIXTURES_PATH . '/views/';
		$this->assertEquals(
			$path,
			( new PHP_Engine( $path ) )->base_view_path()
		);
	}

	/** @testdox By default view_models should be printed, unless false is passed as the param for $print */
	public function test_view_models_print_by_default(): void {
		$this->expectOutputString( 'partial_value' );
		$this->view->view_model(
			new View_Model(
				'layout',
				array( 'partial_data' => array( 'partial' => 'partial_value' ) )
			)
		);
	}

	/** @testdox By default the partial() method should print the view, unless false is passed as the param for $prinr */
	public function test_returns_partial_from_template(): void {
		$this->expectOutputString( 'rendered partial using PHPEngine->partial()' );
		$this->view->partial(
			'returns_partial',
			array( 'partial_data' => array( 'partial' => 'rendered partial using PHPEngine->partial()' ) )
		);
	}

	/** @testdox Any path passed as a view, should be trimmed for all whitespace. */
	public function test_view_path_is_trimmed(): void {
		$this->expectOutputString( 'Hello World' );
		$this->view->render( ' hello ', array( 'hello' => 'Hello World' ) );
	}


}
