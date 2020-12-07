<?php

declare(strict_types=1);
/**
 * Primary App container.
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
 * @package PinkCrab\Core
 */

namespace PinkCrab\Core\Application;

use Exception;
use OutOfBoundsException;
use PC_Vendor\Psr\Container\ContainerInterface;

final class App {

	/**
	 * Holds the isntance of its self.
	 *
	 * @var \PinkCrab\Core\Application\App
	 */
	public static $instance;

	/**
	 * The service container.
	 *
	 * @var \PC_Vendor\Psr\Container\ContainerInterface
	 */
	protected $service_container;

	protected function __construct( ContainerInterface $service_container ) {
		$this->service_container = $service_container;
		self::$instance          = $this;
	}

	/**
	 * Do not allow cloning.
	 */
	protected function __clone() {  }

	/**
	 * Prevent wakeup.
	 */
	public function __wakeup() {
		throw new Exception( 'App can only be initialised directly.' );
	}

	/**
	 * Creates a static instance of the app container.
	 *
	 * @return self
	 */
	public static function init( ContainerInterface $service_container ): self {
		return self::$instance ?? self::$instance = new static( $service_container );
	}

	/**
	 * Gets the current instance of the service container.
	 *
	 * @throws Exception Will throw if not already initialised.
	 * @return self
	 */
	public static function get_instance(): self {
		try {
			if ( ! self::$instance ) {
				throw new Exception( 'PinkCrab Core not loaded' );
			}
		} catch ( \Throwable $th ) {
			\wp_die( $th->getMessage() );
		}
		return self::$instance;
	}

	/**
	 * Binds a class or value to the app container.
	 *
	 * @param string $key
	 * @param mixed $service
	 * @return self
	 */
	public function bind( string $key, $service ): self {
		$this->service_container->set( $key, $service );
		return $this;
	}

	/**
	 * Retrives data from the service container.
	 *
	 * @param string $key
	 * @return mixed
	 * @throws OutOfBoundsException If key not set.
	 */
	public function get( string $key ) {

		// Throw exception if not set.
		if ( ! self::$instance->service_container->has( $key ) ) {
			throw new OutOfBoundsException( sprintf( '%s has not been bound to container.', $key ) );
		}

		return $this->service_container->get( $key );
	}

	/**
	 * Binds a class or value to the app container.
	 *
	 * @param string $key
	 * @param mixed $service
	 * @return self
	 */
	public function set( string $key, $service ): self {
		$this->service_container->set( $key, $service );
		return $this;
	}

	/**
	 * Magic static getter.
	 *
	 * @param string $key
	 * @param array $params
	 * @return void
	 * @throws OutOfBoundsException If key not set.
	 */
	public static function __callStatic( string $key, $params ) {
		return self::$instance->get( $key );
	}

	/**
	 * A static way to call a value.
	 *
	 * @param string $key
	 * @return void
	 * @throws OutOfBoundsException If key not set.
	 */
	public static function retreive( string $key ) {
		return self::$instance->get( $key );
	}

	/**
	 * Creates an instance using Dice.
	 *
	 * @param string $class
	 * @param array $args
	 * @return object|null
	 * @throws OutOfBoundsException If di not set.
	 */
	public static function make( string $class, array $args = array() ) {
		return self::$instance->get( 'di' )->create( $class, $args );
	}

	/**
	 * Creates an instance using Dice.
	 *
	 * @param string $method The config key to call
	 * @param array $args Additional params passed.
	 * @return mixed
	 * @throws OutOfBoundsException If config is not set, or can buggle up from App_Config.
	 */
	public static function config( string $method, ...$args ) {
		return  self::$instance->get( 'config' )->{$method}( ...$args );
	}
}
