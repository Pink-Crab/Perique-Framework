<?php

declare(strict_types=1);

/**
 * Basic PHP engine for using the Renderable interface.
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

use Exception;
use PinkCrab\Perique\Interfaces\Renderable;
use PinkCrab\Perique\Services\View\View_Model;
use PinkCrab\Perique\Services\View\Component\Component;
use PinkCrab\Perique\Services\View\Component\Component_Compiler;

class PHP_Engine implements Renderable {

	/**
	 * The path to base of templates.
	 *
	 * @var string
	 */
	protected $base_view_path;

	/**
	 * Access to the component compiler.
	 *
	 * @var Component_Compiler
	 */
	protected $component_compiler;

	/**
	 * Creates an instance of the PHP_Engine
	 *
	 * @param string $base_view_path
	 */
	public function __construct( string $base_view_path ) {
		$this->base_view_path = $this->verify_view_path( $base_view_path );
	}

	/**
	 * Sets the component compiler.
	 *
	 * @param Component_Compiler $compiler
	 * @return void
	 */
	public function set_component_compiler( Component_Compiler $compiler ): void {
		$this->component_compiler = $compiler;
	}

	/**
	 * Renders a template with data.
	 *
	 * @param string $view
	 * @param iterable<string, mixed> $data
	 * @param bool $print
	 * @return string|void
	 */
	public function render( string $view, iterable $data, bool $print = true ) {
		$view = $this->resolve_file_path( $view );
		if ( $print ) {
			print( $this->render_buffer( $view, $data ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		} else {
			return $this->render_buffer( $view, $data );
		}
	}

	/**
	 * Renders a component.
	 *
	 * @param Component $component
	 * @return string|void
	 */
	public function component( Component $component, bool $print = true ) {

		// Throw exception of no compiler passed.
		if ( ! is_a( $this->component_compiler, Component_Compiler::class ) ) {
			throw new Exception( 'No component compiler passed to PHP_Engine' );
		}

		// Compile the component.
		$compiled = $this->component_compiler->compile( $component );
		$view     = sprintf( '%s%s.php', \DIRECTORY_SEPARATOR, $this->clean_filename( $compiled->template() ) );
		if ( $print ) {
			print( $this->render_buffer( $view, $compiled->data() ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		} else {
			return $this->render_buffer( $view, $compiled->data() );
		}
	}


	/**
	 * Renders a view Model
	 *
	 * @param View_Model $view_model
	 * @return string|void
	 */
	public function view_model( View_Model $view_model, bool $print = true ) {
		return $this->render( $view_model->template(), $view_model->data(), $print );
	}

	/**
	 * Include a partial sub template
	 *
	 * @param string $view
	 * @param iterable<string, mixed> $data
	 * @param bool $print
	 * @return string|void
	 */
	public function partial( string $view, iterable $data = array(), bool $print = true ) {
		if ( $print ) {
			$this->render( $view, $data, $print );
		} else {
			return $this->render( $view, $data, $print );
		}
	}

	/**
	 * Builds the view.
	 *
	 * @param string $view
	 * @param iterable<string, mixed> $__data
	 * @return string
	 * @throws Exception
	 */
	protected function render_buffer( string $view, iterable $__data ): string {

		if ( ! file_exists( $view ) ) {
			throw new Exception( "{$view} doesn't exist" );
		}

		$output = '';
		ob_start();

		// Set all the data values a parameters.
		foreach ( $__data as $__key => $__value ) {
			if ( is_string( $__key ) ) {
				${\wp_strip_all_tags( $__key )} = $__value;
			}

			// Unset the key and value.
			unset( $__key, $__value, $__data );
		}

		include $view;
		$output = ob_get_contents();
		ob_end_clean();
		return $output ?: '';
	}

	/**
	 * Trims any leading slash and removes .php
	 *
	 * @param string $file
	 * @return string
	 */
	protected function clean_filename( string $file ): string {
		$file = ltrim( $file, '/' );
		return substr( $file, -4 ) === '.php'
			? substr( $file, 0, -4 )
			: $file;

	}

	/**
	 * Resolves the filepath from a filenane.
	 *
	 * @param string $filename
	 * @return string
	 */
	protected function resolve_file_path( string $filename ): string {
		return sprintf(
			'%s%s.php',
			$this->base_view_path,
			$this->clean_filename( $filename )
		);
	}


	/**
	 * Verifies the view path exists and it has the trailing slash.
	 *
	 * @param string $path
	 * @return string
	 * @throws Exception
	 */
	protected function verify_view_path( string $path ): string {

		$path = rtrim( $path, '/' ) . '/';

		if ( ! \is_dir( $path ) ) {
			throw new Exception( "{$path} doesn't exist and cant be used as the base view path." );
		}

		return $path;
	}
}
