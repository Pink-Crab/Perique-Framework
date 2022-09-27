<?php

declare(strict_types=1);

/**
 * The base component class.
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
 * @since 1.2.0
 */

namespace PinkCrab\Perique\Services\View\Component;

use PinkCrab\Perique\Services\View\View_Model;
use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\AnnotationRegistry;
use PinkCrab\Perique\Services\View\Component\Component;

class Component_Compiler {

	/**
	 * The base path for components.
	 *
	 * If blank will assume views/components
	 *
	 * @var string
	 */
	private $component_base_path;

	/**
	 * All component aliases..
	 *
	 * @var array<string, string>
	 */
	private $component_aliases = array();

	/** @param array<string, string> $component_aliases */
	public function __construct( string $component_base_path = '', array $component_aliases = array() ) {
		$this->component_base_path = $component_base_path;
		$this->component_aliases   = $component_aliases;
	}

	/**
	 * Compiles the component into a view model.
	 *
	 * @param Component $component
	 * @return View_Model
	 */
	public function compile( Component $component ): View_Model {
		return new View_Model( $this->get_component_path( $component ), $component->get_variables() );
	}

	/**
	 * Returns the path to the component.
	 *
	 * @param Component $component
	 * @return string
	 */
	private function get_component_path( Component $component ): string {

		// Check aliases.
		if ( isset( $this->component_aliases[ get_class( $component ) ] ) ) {
			return esc_html( $this->component_aliases[ get_class( $component ) ] );
		}

		$from_annotation = $this->get_annotation( 'view', $component );
		// If it does have a path defined, use that.
		if ( ! empty( $from_annotation ) ) {
			return \trailingslashit( $this->component_base_path ) . $from_annotation;
		}

		// If the component has a defined path
		if ( $component->template() ) {
			return \trailingslashit( $this->component_base_path ) . $component->template();
		}

		// Get path based on class name.
		$reflect    = new \ReflectionClass( $component );
		$short_name = $reflect->getShortName();
		// Add space between capitals, make lowercase and replace underscores with dashes.
		$short_name = strtolower( preg_replace( '/(?<!^)[A-Z]/', '-$0', $short_name ) ?? '' );
		$short_name = str_replace( '_', '-', $short_name );

		return \trailingslashit( $this->component_base_path ) . $short_name;
	}

	/**
	 * Attempts to extract a defined Annotation from component class doc block.
	 *
	 * @param string $annotation
	 * @param Component $component
	 * @return string|null
	 */
	private function get_annotation( string $annotation, Component $component ): ?string {
		$reflect = new \ReflectionClass( $component );
		$comment = $reflect->getDocComment();

		// if no comment, return null.
		if ( empty( $comment ) ) {
			return null;
		}

		// If comment contains the annotation, return the value.
		return strpos( $comment, "@{$annotation}" ) !== false
			? $this->extract_annotation_value( $comment, $annotation )
			: null;
	}

	/**
	 * Extracts the value from the annotation.
	 *
	 * @param string $comment
	 * @param string $annotation
	 * @return string|null
	 */
	private function extract_annotation_value( string $comment, string $annotation ): ?string {
		$pattern = "/@{$annotation}\s+(.*)/";
		preg_match( $pattern, $comment, $matches );
		return $matches[1] ?? null;
	}

}
