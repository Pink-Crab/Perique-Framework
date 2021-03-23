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

use PinkCrab\Core\Services\Dice\WP_Dice;
use PinkCrab\Core\Interfaces\DI_Container;

class WP_Dice_DI_Container_Bridge implements DI_Container {

	/** @var WP_Dice */
	protected $wp_dice;

	public function __construct( WP_Dice $wp_dice ) {
		$this->wp_dice = $wp_dice;
	}

	/**
	 * Add a single rule.
	 *
	 * @param string $id
	 * @param array<string, array> $rule
	 * @return DI_Container
	 */
	public function addRule( string $id, array $rule ): DI_Container {
		return $this;
	}

	/**
	 * Add multiple rules
	 *
	 * @param array<string, array> $rules
	 * @return DI_Container
	 */
	public function addRules( array $rules ): DI_Container {
		$this->wp_dice->addRules($rules);
		dump($rules);
		return $this;
	}

	/**
	 * Create an instance of a class, with optional parameters.
	 *
	 * @param string $id
	 * @param array<mixed> $args
	 * @return object|null
	 */
	public function create( string $id, array $args = array() ) {
		return $this->wp_dice->create($id, $args);
	}

	public function get( $id ) {

	}

	public function has( $id ) {

	}
}
