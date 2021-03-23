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
 * @package PinkCrab\Core\Registration
 */

namespace PinkCrab\Core\Services\Registration;

use PinkCrab\Core\Interfaces\DI_Container;
use PinkCrab\Core\Services\Registration\Middleware\Registration_Middleware;

class Registration_Service {

	/**
	 * Holds all the defined registration middlewares
	 *
	 * @var Registration_Middleware[]
	 */
	protected $middleware = array();

	/**
	 * Holds all classes that are to be registered.
	 *
	 * @var array
	 */
	protected $class_list = array();

	/**
	 * Access to the DI Container
	 *
	 * @var DI_Container
	 */
	protected $di_container;

	/**
	 * Sets the DI Container.
	 *
	 * @param \PinkCrab\Core\Interfaces\DI_Container $di_container
	 * @return self
	 */
	public function set_container( DI_Container $di_container ): self {
		$this->di_container = $di_container;
		return $this;
	}

	/**
	 * Pushes a peice of middleware to the collection.
	 *
	 * @param \PinkCrab\Core\Services\Registration\Registration_Middleware $middleware
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
	 * @param object $class
	 * @return self
	 */
	public function push_class( $class ): self {
		$this->class_list[] = $class;
		return $this;
	}

	/**
	 * Runs all the defined classes through the middleware stack.
	 *
	 * @return void
	 */
	public function process(): void {
		foreach ( $this->middleware as $middleware ) {
			// Pass each class to the middleware.
			foreach ( $this->class_list as $class ) {
				$middleware->process( $class );
			}
		}
	}

}
