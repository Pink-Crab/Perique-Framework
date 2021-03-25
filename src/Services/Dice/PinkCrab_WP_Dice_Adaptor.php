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
 * @package PinkCrab\Core\Dice
 */

namespace PinkCrab\Core\Services\Dice;

use PinkCrab\Core\Application\Hooks;
use PinkCrab\Core\Services\Dice\WP_Dice;
use PinkCrab\Core\Interfaces\DI_Container;
use PinkCrab\Core\Services\ServiceContainer\ServiceNotRegisteredException;

class PinkCrab_WP_Dice_Adaptor extends WP_Dice implements DI_Container {

	/** @var WP_Dice */
	// protected $wp_dice;

	public function get( $id ) {
		if ( ! $this->has( $id ) ) {
			throw new ServiceNotRegisteredException( "{$id} not defined in container", 1 );
		}
	}

	public function has( $id ) {
		if ( ! $this->has( $id ) ) {
			throw new ServiceNotRegisteredException( "{$id} not defined in container", 1 );
		}

		// If class exists but
		try {
			$instance = $this->create( $id );
		} catch ( \Throwable $th ) {
			$instance = null;
		}
		return is_object( $instance );
	}

	/**
	 * Proxy for addRule.
	 *
	 * @param string $name
	 * @param array<string, string|object|array> $rule
	 * @return self
	 */
	public function addRule( string $name, array $rule ): self { // phpcs:disable WordPress.NamingConventions.ValidFunctionName.MethodNameInvalid
		$this->dice = $this->dice->addRule( $name, $rule );
		return $this;
	}

	/**
	 * Proxy for addRules
	 *
	 * @param array<string, array> $rules
	 * @return self
	 */
	public function addRules( array $rules ): self { // phpcs:disable WordPress.NamingConventions.ValidFunctionName.MethodNameInvalid
		$this->dice = $this->dice->addRules( app_filters(Hooks::s $rules) );
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
