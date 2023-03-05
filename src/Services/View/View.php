<?php

declare(strict_types=1);

/**
 * The main view class.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR
 * A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT
 * OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL,
 * SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT
 * LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,
 * DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY
 * THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE
 * OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * @author Glynn Quelch <glynn.quelch@gmail.com>
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 * @package PinkCrab\Perique\View
 */

namespace PinkCrab\Perique\Services\View;

use PinkCrab\Perique\Interfaces\Renderable;
use PinkCrab\Perique\Services\View\View_Model;
use PinkCrab\Perique\Services\View\Component\Component;
use PinkCrab\Perique\Services\View\Component\Component_Compiler;

class View {

	/**
	 * IF the view should be returned as a string.
	 */
	public const RETURN_VIEW = false;

	/**
	 * If the view should be printed.
	 */
	public const PRINT_VIEW = true;

	/**
	 * The current view engine.
	 *
	 * @var Renderable
	 */
	protected $engine;

	/**
	 * The component compiler
	 *
	 * @var Component_Compiler
	 */
	protected $component_compiler;


	/**
	 * Creates an instance of view with the passed engine.
	 *
	 * @param Renderable $engine
	 */
	public function __construct( Renderable $engine, Component_Compiler $component_compiler ) {
		$this->engine             = $engine;
		$this->component_compiler = $component_compiler;

		// Populate engine with compiler.
		$this->engine->set_component_compiler( $component_compiler );
	}

	/**
	 * Renders a view with passed data.
	 *
	 * @param string $view
	 * @param iterable<string, mixed> $view_data
	 * @param bool $print Print or Return the HTML
	 * @return string|void
	 */
	public function render( string $view, iterable $view_data = array(), bool $print = self::PRINT_VIEW ) {
		return $this->engine->render( $view, $view_data, $print );
	}

	/**
	 * Renders a component.
	 *
	 * @param Component $component
	 * @param bool $print Print or Return the HTML
	 * @return string|void
	 */
	public function component( Component $component, bool $print = self::PRINT_VIEW ) {
		return $this->engine->component( $component, $print );
	}

	/**
	 * Renders a view model
	 *
	 * @param View_Model $view_model
	 * @param bool $print Print or Return the HTML
	 * @return string|void
	 */
	public function view_model( View_Model $view_model, bool $print = self::PRINT_VIEW ) {
		return $this->engine->view_model( $view_model, $print );
	}

	/**
	 * Buffer for use with WordPress functions that display directly.
	 *
	 * @param callable $to_buffer
	 * @return string
	 */
	public static function print_buffer( callable $to_buffer ): string {
		ob_start();
		$to_buffer();
		$output = ob_get_contents();
		ob_end_clean();
		return $output ?: '';
	}

	/**
	 * Returns access to the internal rendering engine.
	 *
	 * @return Renderable
	 */
	public function engine(): Renderable {
		return $this->engine;
	}

	/**
	 * Returns the base path for the view.
	 *
	 * @return string
	 * @since 1.4.0
	 */
	public function base_path(): string {
		return $this->engine->base_view_path();
	}

}
