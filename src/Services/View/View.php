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
	 * Creates an instance of view with the passed engine.
	 *
	 * @param Renderable $engine
	 */
	public function __construct( Renderable $engine ) {
		$this->engine = $engine;
	}

	/**
	 * Renders a view with passed data.
	 *
	 * @param string $view
	 * @param iterable<string, mixed> $view_data
	 * @param bool $print
	 * @return string|void
	 */
	public function render( string $view, iterable $view_data = array(), bool $print = true ) {
		if ( $print ) {
			$this->engine->render( $view, $view_data, self::PRINT_VIEW );
		} else {
			return $this->engine->render( $view, $view_data, self::RETURN_VIEW );
		}
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

}
