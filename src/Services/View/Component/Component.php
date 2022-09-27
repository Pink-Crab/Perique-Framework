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

abstract class Component {

	/**
	 * Returns all the variables as an array.
	 *
	 * @return array<string, mixed>
	 */
	public function get_variables(): array {
		// Get all Private, public and protected properties.
		$reflect = new \ReflectionClass( get_class( $this ) );
		$vars    = array();

		foreach ( $reflect->getProperties( \ReflectionProperty::IS_PUBLIC | \ReflectionProperty::IS_PRIVATE | \ReflectionProperty::IS_PROTECTED )
			as $var
		) {
			$var->setAccessible( true );
			$vars[ $var->getName() ] = $var->getValue( $this );
		}

		return $vars;
	}

	/**
	 * Returns the defined template path.
	 *
	 * @return string|null
	 */
	public function template(): ?string {
		return null;
	}
}
