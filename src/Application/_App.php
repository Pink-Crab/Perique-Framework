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
 * @since 0.4.0
 */

namespace PinkCrab\Core\Application;

use PinkCrab\Loader\Loader;
use PinkCrab\Core\Services\View\View;
use PinkCrab\Core\Services\Registration\{
use PinkCrab\Core\Application\{App, App_Config};
use PinkCrab\Core\Interfaces\{Renderable,DI_Container};
use PinkCrab\Core\Exceptions\App_Initialization_Exception;
	Registration_Service,
	Middleware\Registration_Middleware
};

final class _App extends App {

	/**
	 * Defines if the app has already been booted.
	 *
	 * @var bool
	 */
	protected static $booted = false;

	/**
	 * Dependency Injection Container
	 *
	 * @var DI_Container
	 */
	protected static $container;

	/**
	 * The Apps Config
	 *
	 * @var App_Config
	 */
	protected static $app_config;

	/**
	 * Handles all registration of all registerable and custom middlewares.
	 *
	 * @var Registration_Service
	 */
	protected $registration;

	/**
	 * Hook Loader
	 *
	 * @var Loader
	 */
	protected $loader;

	public function __construct() {

	}

	/**
	 * Sets the DI Constainer.
	 *
	 * @param \PinkCrab\Core\Interfaces\DI_Container $container
	 * @return self
	 * @throws App_Initialization_Exception Code 2
	 */
	public function set_container( DI_Container $container ): self {
		if ( self::$container !== null ) {
			throw App_Initialization_Exception::di_container_exists();
		}
		self::$container = $container;
		return $this;
	}

	/**
	 * Define the app condfig.
	 *
	 * @param array<mixed> $settings
	 * @return self
	 * @throws App_Initialization_Exception Code 5
	 */
	public function app_config( array $settings ): self {
		if ( self::$app_config !== null ) {
			throw App_Initialization_Exception::app_config_exists();
		}
		self::$app_config = new App_Config( $settings );
		return $this;
	}

	/**
	 * Sets the Registration service and loader.
	 *
	 * @param \PinkCrab\Core\Services\Registration\Registration_Service $registration
	 * @param \PinkCrab\Loader\Loader $loader
	 * @return self
	 */
	public function define_registration_services(
		Registration_Service $registration,
		Loader $loader
	): self {
		$this->registration = $registration;
		$this->loader = $loader;
		return $this;
	}

	/**
	 * Interace with the container using a callable.
	 *
	 * @param callable(DI_Containter):void $callback
	 * @return self
	 * @throws App_Initialization_Exception Code 1
	 */
	public function container_config( callable $callback ): self {
		if ( $this->registration === null ) {
			throw App_Initialization_Exception::requires_di_container();
		}
		$callback( $this->container );
		return $this;
	}

	/**
	 * Add registration middleware
	 *
	 * @param Registration_Middleware $middleware
	 * @return self
	 * @throws App_Initialization_Exception Code 3
	 */
	public function registration_middleware( Registration_Middleware $middleware ): self {
		if ( $this->registration === null ) {
			throw App_Initialization_Exception::requires_registration_service();
		}

		$this->registration->push_middleware( $middleware );
		return $this;
	}

	/**
	 * Sets the class list.
	 *
	 * @param array<string> $class_list
	 * @return void
	 * @throws App_Initialization_Exception Code 3
	 */
	public function registration_classses( array $class_list ) {
		if ( $this->registration === null ) {
			throw App_Initialization_Exception::requires_registration_service();
		}
		$this->registration->set_classes( $class_list );
		return $this;
	}

	/**
	 * Boots the populated app.
	 *
	 * @return self
	 */
	public function boot(): self {
		// Process registration
		$this->registration->set_container( self::$container );
		$this->registration->process
		self::$booted = true;
		return $this;
	}

	// Magic Helpers.

	/**
	 * Creates an instance of class using the DI Container.
	 *
	 * @param string $class
	 * @param array $args<string, mixed>
	 * @return object
	 * @throws App_Initialization_Exception Code 4
	 */
	public static function make( string $class, array $args = array() ) {
		if ( self::$booted === false ) {
			throw App_Initialization_Exception::app_not_initialized( DI_Container::class );
		}
		return self::$container->create( $class, $args );
	}

	/**
	 * Creates an instance using Dice.
	 *
	 * @param string $key The config key to call
	 * @param array<int, mixed> $child Additional params passed.
	 * @return mixed
	 * @throws App_Initialization_Exception Code 4
	 */
	public static function config( string $key, ...$child ) {
		if ( self::$booted === false ) {
			throw App_Initialization_Exception::app_not_initialized( App_Config::class );
		}
		return self::$app_config->{$key}( ...$child );
	}

	/**
	 * Returns the View helper, populated with current Renderable engine.
	 *
	 * @return View
	 */
	public static function view(): View {
		if ( self::$booted === false ) {
			throw App_Initialization_Exception::app_not_initialized( View::class );
		}
		return self::$container->create( View::class );
	}

	/** @return array{container:Container,app_config:App_Config,booted:bool} */
	public function __debugInfo() {
		return array(
			'container'  => self::$container,
			'app_config' => self::$app_config,
			'booted'     => self::$booted,
		);
	}
}
