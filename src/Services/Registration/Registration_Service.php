<?php

declare(strict_types=1);

/**
 * Registration loader
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
 * @package PinkCrab\Perique\Registration
 */

namespace PinkCrab\Perique\Services\Registration;

use PinkCrab\Loader\Hook_Loader;
use PinkCrab\Perique\Application\Hooks;
use PinkCrab\Perique\Interfaces\DI_Container;
use PinkCrab\Perique\Interfaces\Registration_Middleware;

class Registration_Service {

	/**
	 * Holds all the defined registration middlewares
	 *
	 * @var array<Registration_Middleware>?
	 */
	protected $middleware = array();

	/**
	 * Holds all classes that are to be registered.
	 *
	 * @var array<string>
	 */
	protected $class_list = array();

	/**
	 * Access to the DI Container
	 *
	 * @var DI_Container
	 */
	protected $di_container;

	/**
	 * Access to the Hook Loader
	 *
	 * @var Hook_Loader|null
	 */
	protected $loader;

	/**
	 * Sets the DI Container.
	 *
	 * @param DI_Container $di_container
	 * @return self
	 */
	public function set_container( DI_Container $di_container ): self {
		$this->di_container = $di_container;
		return $this;
	}

	/**
	 * Sets the DI Container.
	 *
	 * @param Hook_Loader $loader
	 * @return self
	 */
	public function set_loader( Hook_Loader $loader ): self {
		$this->loader = $loader;
		return $this;
	}

	/**
	 * Pushes a piece of middleware to the collection.
	 *
	 * @param Registration_Middleware $middleware
	 * @return self
	 */
	public function push_middleware( Registration_Middleware $middleware ): self {
		$this->middleware[ \get_class( $middleware ) ] = $middleware;
		return $this;
	}

	/**
	 * Used to set the list of classes used.
	 *
	 * @param array<string> $class_list
	 * @return self
	 */
	public function set_classes( array $class_list ): self {
		$this->class_list = $class_list;
		return $this;
	}

	/**
	 * Pushes a single class to the class list.
	 *
	 * @param string $class
	 * @return self
	 */
	public function push_class( string $class ): self {
		$this->class_list[] = $class;
		return $this;
	}

	/**
	 * Runs all the defined classes through the middleware stack.
	 *
	 * @return void
	 */
	public function process(): void {

		// Filter all classes, before processing.
		$class_list = apply_filters( Hooks::APP_INIT_REGISTRATION_CLASS_LIST, $this->class_list );

		foreach ( $this->middleware as $middleware ) {

			// Set the container if requested.
			if ( \method_exists( $middleware, 'set_di_container' ) ) {
				$middleware->set_di_container( $this->di_container );
			}

			// Set the hook loader if requested.
			if ( \method_exists( $middleware, 'set_hook_loader' ) && ! is_null( $this->loader ) ) {
				$middleware->set_hook_loader( $this->loader );
			}

			// Run middleware setup
			$middleware->setup();

			// Pass each class to the middleware.
			foreach ( $class_list as $class ) {

				// Construct class using container,
				$class_instance = $this->di_container->create( $class );

				// if valid object process via current middleware
				if ( is_object( $class_instance ) ) {
					$middleware->process( $class_instance );
				}
			}

			// Run middleware setup
			$middleware->tear_down();
		}
	}

}
