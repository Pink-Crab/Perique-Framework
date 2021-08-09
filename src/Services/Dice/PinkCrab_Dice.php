<?php

declare(strict_types=1);

/**
 * Bridge for using the WP_Dice container with the PinkCrab App.
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
 * @package PinkCrab\Perique\Dice
 */

namespace PinkCrab\Perique\Services\Dice;

use Dice\Dice;
use PinkCrab\Perique\Application\Hooks;
use PinkCrab\Perique\Interfaces\DI_Container;
use PinkCrab\Perique\Exceptions\DI_Container_Exception;

class PinkCrab_Dice implements DI_Container {

	/**
	 * Holds the instance of DICE to work with.
	 *
	 * @var Dice;
	 */
	protected $dice;

	/**
	 * Passes in the initial dice instance.
	 *
	 * @param Dice $dice
	 */
	public function __construct( Dice $dice ) {
		$this->dice = $dice;
	}

	/**
	 * Lazy stack instancing.
	 *
	 * @param Dice $dice
	 * @return self
	 */
	public static function withDice( Dice $dice ): self { // phpcs:disable WordPress.NamingConventions.ValidFunctionName.MethodNameInvalid
		return new PinkCrab_Dice( $dice );
	}

	/**
	 * ContainerInterface implementation of get.
	 * Will attempt to construct autowired.
	 *
	 * @param string $id Class name (fully namespaced.)
	 * @return object|null
	 */
	public function get( $id ) {
		if ( ! $this->has( $id ) ) {
			throw new DI_Container_Exception( "{$id} not defined in container", 1 );
		}
		return $this->create( $id );
	}

	/**
	 * Checks if a specific class is registered or exists.
	 * Doesn't take into account the ability to autowire.
	 *
	 * @param string $id Class name (fully namespaced.)
	 * @return bool
	 */
	public function has( $id ) {
		$from_dice = $this->dice->getRule( $id );
		// If set in global rules.
		if ( array_key_exists( 'substitutions', $from_dice )
		&& array_key_exists( $id, $from_dice['substitutions'] ) ) {
			return true;
		}

		// If set with a replacement instance.
		if ( array_key_exists( 'instanceOf', $from_dice ) ) {
			return true;
		}

		// Checks if the class exists
		return class_exists( $id );
	}

	/**
	 * Proxy for addRule.
	 *
	 * @param string $name
	 * @param array<string, string|object|mixed[]> $rule
	 * @return PinkCrab_Dice
	 */
	public function addRule( string $name, array $rule ): DI_Container { // phpcs:disable WordPress.NamingConventions.ValidFunctionName.MethodNameInvalid
		$this->dice = $this->dice->addRule( $name, $rule );
		return $this;
	}

	/**
	 * Proxy for addRules
	 *
	 * @param array<string, mixed[]> $rules
	 * @return PinkCrab_Dice
	 */
	public function addRules( array $rules ): DI_Container { // phpcs:disable WordPress.NamingConventions.ValidFunctionName.MethodNameInvalid
		$this->dice = $this->dice->addRules( apply_filters( Hooks::APP_INIT_SET_DI_RULES, $rules ) );
		return $this;
	}

	/**
	 * Proxy for create, but with third param removed (see dice code comments)
	 *
	 * @param string $name
	 * @param array<mixed> $args
	 * @return object|null
	 */
	public function create( string $name, array $args = array() ) {
		return $this->dice->create( $name, $args );
	}
}
