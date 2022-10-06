<?php

declare(strict_types=1);

/**
 * Tests the default PHP Engine for the view/renderable interface.
 *
 * @since 1.2.0
 * @author Glynn Quelch <glynn.quelch@gmail.com>
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 * @package PinkCrab\Perique
 */

namespace PinkCrab\Perique\Tests\View;

use PinkCrab\Perique\Application\Hooks;
use PinkCrab\Perique\Services\View\View;
use PinkCrab\Perique\Services\View\PHP_Engine;
use PinkCrab\Perique\Services\View\Component\Component_Compiler;
use PinkCrab\Perique\Tests\Fixtures\Mock_Objects\View_Components\P;
use PinkCrab\Perique\Tests\Fixtures\Mock_Objects\View_Components\Span;
use PinkCrab\Perique\Tests\Fixtures\Mock_Objects\View_Components\Input;
use PinkCrab\Perique\Tests\Fixtures\Mock_Objects\View_Components\Input_Attribute_Path;
use PinkCrab\Perique\Tests\Fixtures\Mock_Objects\View_Components\Input_Template_Method;

/**
 * @group view
 * @group components
 */
class Test_Components extends \WP_UnitTestCase {

	private static $component_path;
	private static $php_engine;

	public static function setUpBeforeClass(): void {
		self::$component_path = 'components/';
		self::$php_engine     = new PHP_Engine( \dirname( __DIR__, 1 ) . '/Fixtures/Views/' );
	}

	/** @testdox It should be possible to assume the path of a component based on its name in relationship to the base path when using the compiler.. */
	public function test_can_assume_path(): void {

		$compiler = new Component_Compiler( self::$component_path );

		// With a component assumed from filename (1st letter uppercase)
		$model = $compiler->compile( new Input( 'input', 'the_id', 'value', 'number' ) );
		$this->assertEquals( self::$component_path . 'input', $model->template() );
		$this->assertEquals( 'input', $model->data()['name'] );
		$this->assertEquals( 'the_id', $model->data()['id'] );
		$this->assertEquals( 'value', $model->data()['value'] );
		$this->assertEquals( 'number', $model->data()['type'] );

		// With a component based on method.
		$model = $compiler->compile( new Input_Template_Method( 'input' ) );
		$this->assertEquals( self::$component_path . 'path/to/template', $model->template() );
		$this->assertEquals( 'input', $model->data()['name'] );

		// With attribute.
		$model = $compiler->compile( new Input_Attribute_Path( 'input' ) );
		$this->assertEquals( self::$component_path . 'from/attribute/path', $model->template() );
		$this->assertEquals( 'input', $model->data()['name'] );
	}

	/** @testdox It should be possible to use the component aliases to set a custom path for a component. */
	public function test_can_use_component_aliases(): void {
		$compiler = new Component_Compiler( self::$component_path, array( Input::class => 'custom/path' ) );

		$model = $compiler->compile( new Input( 'input', 'the_id', 'value', 'number' ) );
		$this->assertEquals( 'custom/path', $model->template() );
	}

	/** @testdox It should be possible to print a component to the screen. */
	public function test_can_print_component(): void {
		$compiler = new Component_Compiler( self::$component_path );
		// $model = $compiler->compile( new Input( 'input', 'the_id', 'value', 'number' ) );
		$this->expectOutputString( '<input name="input" id="the_id" value="value" type="number" />' );

		$view = new View( self::$php_engine, $compiler );
		$view->component( new Input( 'input', 'the_id', 'value', 'number' ) );
	}

	/** @testdox It should be create the HTML for a component. */
	public function test_can_create_html(): void {
		$compiler = new Component_Compiler( self::$component_path );
		$view     = new View( self::$php_engine, $compiler );
		$this->assertEquals(
			'<input name="input" id="the_id" value="value" type="number" />',
			$view->component( new Input( 'input', 'the_id', 'value', 'number' ), false )
		);
	}

	/** @testdox It should be possible to render a component inside another component. */
	public function test_can_render_component_inside_component(): void {
		$compiler = new Component_Compiler( self::$component_path );
		$view     = new View( self::$php_engine, $compiler );
		$this->assertEquals(
			'<p class="class_p"><span class="class_s">value_s</span></p>',
			$view->component( new P( 'class_p', new Span( 'class_s', 'value_s' ) ), false )
		);
	}

	/** @testdox It should be possible to add additional Component Aliases after setup and still have this reflected in paths. */
	public function test_can_add_component_aliases_after_setup(): void {
		$compiler = new Component_Compiler( self::$component_path );
		$view     = new View( self::$php_engine, $compiler );

		// Add alias.
		\add_filter(
			Hooks::COMPONENT_ALIASES,
			function( array $aliases ): array {
				$aliases[ Input::class ] = self::$component_path . '/other/other.php';
				return $aliases;
			}
		);

		$this->assertEquals(
			'input--the_id--value--number',
			$view->component( new Input( 'input', 'the_id', 'value', 'number' ), false )
		);
	}
}
