<?php

declare(strict_types=1);

/**
 * Wrapper for DICE to handle DICE returning a new instance when new rules are added.
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

use PinkCrab\Core\Services\Dice\Dice;

class WP_Dice {

	/**
	 * Holds the instnace of DICE to work with.
	 *
	 * @var PinkCrab\Core\Services\Dice\Dice;
	 */
	protected $dice;

	/**
	 * Passes in the inital dice instance.
	 *
	 * @param \PinkCrab\Core\Services\Dice\Dice
	 */
	public function __construct( Dice $dice ) {
		$this->dice = $dice;
	}


	/**
	 * Lazy stack instancing.
	 *
	 * @param \PinkCrab\Core\Services\Dice\Dice

	 * @return self
	 */
	public static function constructWith( Dice $dice ): self {
		return new self( $dice );
	}

	/**
	 * Proxy for addRule.
	 *
	 * @param string $name
	 * @param array $rule
	 * @return self
	 */
	public function addRule( string $name, array $rule ): self {
		$this->dice = $this->dice->addRule( $name, $rule );
		return $this;
	}

	/**
	 * Proxy for addRules
	 *
	 * @param array $rules
	 * @return self
	 */
	public function addRules( array $rules ): self {
		$this->dice = $this->dice->addRules( $rules );
		return $this;
	}

	/**
	 * Proxy for create, but with third param removed (see dice code comments)
	 *
	 * @param string $name
	 * @param array $args
	 * @return object|null
	 */
	public function create( string $name, array $args = array() ):? object {
		return $this->dice->create( $name, $args );
	}
}
