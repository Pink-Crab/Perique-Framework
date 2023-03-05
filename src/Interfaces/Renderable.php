<?php declare(strict_types=1);
/**
 * The view engine interface.
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
 * @package PinkCrab\Perique
 */

namespace PinkCrab\Perique\Interfaces;

use PinkCrab\Perique\Services\View\View_Model;
use PinkCrab\Perique\Services\View\Component\Component;
use PinkCrab\Perique\Services\View\Component\Component_Compiler;

interface Renderable {

	/**
	 * Display a view and its context.
	 *
	 * @param string $view
	 * @param iterable<string, mixed> $data
	 * @param bool $print
	 * @return void|string
	 */
	public function render( string $view, iterable $data, bool $print = true );

	/**
	 * Renders a component.
	 *
	 * @param Component $component
	 * @return string|void
	 */
	public function component( Component $component, bool $print = true );

	/**
	 * Renders a view Model
	 *
	 * @param View_Model $view_model
	 * @return string|void
	 */
	public function view_model( View_Model $view_model, bool $print = true );

	/**
	 * Sets the component compiler.
	 *
	 * @param Component_Compiler $compiler
	 * @return void
	 */
	public function set_component_compiler( Component_Compiler $compiler ): void;

	/**
	 * Returns the base view path.
	 *
	 * @return string
	 * @since 1.4.0
	 */
	public function base_view_path(): string;

}
