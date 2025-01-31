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
use PinkCrab\Perique\Exceptions\Module_Manager_Exception;

class Registration_Service {

	/**
	 * Holds all the defined registration middlewares
	 *
	 * @var array<Registration_Middleware>
	 */
	protected array $middleware = array();

	/**
	 * Holds all classes that are to be registered.
	 *
	 * @var array<string>
	 */
	protected array $class_list = array();

	/**
	 * Access to the DI Container
	 *
	 * @var DI_Container
	 */
	protected DI_Container $di_container;

	public function __construct( DI_Container $di_container ) {
		$this->di_container = $di_container;
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
	 * Adds a class to the list of classes to be registered.
	 *
	 * @template Class_Name of object
	 * @param class-string<Class_Name> $class_string
	 */
	public function push_class( string $class_string ): self {
		// If the class is already in the list, skip.
		if ( \in_array( $class_string, $this->class_list, true ) ) {
			return $this;
		}

		// If $class_string is not a class, throw exception.
		if ( ! \class_exists( $class_string ) ) {
			throw Module_Manager_Exception::none_class_string_passed_to_registration( esc_html( $class_string ) );
		}

		$this->class_list[] = $class_string;
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

		// If class list is empty, skip.
		if ( empty( $class_list ) ) {
			return;
		}

		foreach ( $this->middleware as $middleware ) {
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
