<?php

declare(strict_types=1);

/**
 * Tests for the view service class
 *
 * @since 0.1.0
 * @author Glynn Quelch <glynn.quelch@gmail.com>
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 * @package PinkCrab\Perique
 */

namespace PinkCrab\Perique\Tests\View;

use WP_UnitTestCase;
use Gin0115\WPUnit_Helpers\Objects;
use PinkCrab\Perique\Services\View\View;
use PinkCrab\Perique\Services\View\PHP_Engine;
use PinkCrab\Perique\Services\View\View_Model;
use PinkCrab\Perique\Services\View\Component\Component_Compiler;

class Test_View extends WP_UnitTestCase {

	/**
	 * Holds a temp instance to the PHP_ENgine.
	 *
	 * @var Renderable
	 */
	protected $php_engine;

	/**
	 * Holds a temp instance of the component compiler.
	 *
	 * @var Component_Compiler
	 */
	protected $component_compiler;

	public function setUp(): void {
		parent::setUp();

		$this->php_engine         = new PHP_Engine( \dirname( __DIR__, 1 ) . '/Fixtures/Views/' );
		$this->component_compiler = new Component_Compiler();
	}


	/** @testdox When a function usually prints to the output, it should be possible to caputure this output and return as a string. */
	public function test_print_buffer(): void {
		$result = View::print_buffer(
			function() {
				echo 'ECHO...ECHO';
			}
		);

		$this->assertEquals( 'ECHO...ECHO', $result );
	}

	/** @testdox It should be possible to render(print) a template direct to the output, either CLI or in a reposnse. */
	public function test_can_be_constructed_with_render_engine(): void {

		$view = new View( $this->php_engine, $this->component_compiler );

		$this->assertSame(
			$this->php_engine,
			Objects::get_property( $view, 'engine' )
		);
	}

	/** @testdox A template should be returnable as a string for priting elsewhere */
	public function test_return_single_template(): void {
		$this->assertEquals(
			'Hello World',
			( new View( $this->php_engine, $this->component_compiler ) )->render(
				'hello',
				array( 'hello' => 'Hello World' ),
				View::RETURN_VIEW
			)
		);
	}

	/** @testdox Partial tempaltes should be renderable within an existsing template. */
	public function test_render_partial_template(): void {
		$this->expectOutputString( 'partial_value' );
		( new View( $this->php_engine, $this->component_compiler ) )->render(
			'layout',
			array( 'partial_data' => array( 'partial' => 'partial_value' ) ),
			View::PRINT_VIEW // Optional as print view is default.
		);
	}

	/** @testdox You should be able to get access to the internal rendering engine, for binding additional directives or access internal functionality */
	public function test_get_internal_engine(): void {
		$this->assertInstanceOf(
			PHP_Engine::class,
			( new View( $this->php_engine, $this->component_compiler ) )->engine()
		);
	}

	/** @testdox It should be possible to return the output of a view model. */
	public function test_return_view_model(): void {
		$this->assertEquals(
			'Hello World',
			( new View( $this->php_engine, $this->component_compiler ) )->view_model(
				new View_Model( 'hello', array( 'hello' => 'Hello World' ) ),
				View::RETURN_VIEW
			)
		);
	}

	/** @testdox It should be possible to output a view model */
	public function test_print_view_model(): void {
		$this->expectOutputString( 'Hello World' );
		( new View( $this->php_engine, $this->component_compiler ) )->view_model(
			new View_Model( 'hello', array( 'hello' => 'Hello World' ) ),
			View::PRINT_VIEW
		);
	}

	/** @testdox It should be possible to access the base path used by the renderable instance */
	public function test_get_base_path(): void {
		$path       = \dirname( __DIR__, 1 ) . '/Fixtures/Views/';
		$renderable = new PHP_Engine( $path );

		$this->assertEquals(
			$path,
			( new View( $renderable, $this->createMock( Component_Compiler::class ) ) )->base_path()
		);
	}


}
