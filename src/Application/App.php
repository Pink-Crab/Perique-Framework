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
 * @package PinkCrab\Perique
 * @since 0.4.0
 */

namespace PinkCrab\Perique\Application;

use Closure;
use PinkCrab\Loader\Hook_Loader;
use PinkCrab\Perique\Application\Hooks;
use PinkCrab\Perique\Services\View\View;
use PinkCrab\Perique\Application\App_Config;
use PinkCrab\Perique\Interfaces\DI_Container;
use PinkCrab\Perique\Interfaces\Registration_Middleware;
use PinkCrab\Perique\Exceptions\App_Initialization_Exception;
use PinkCrab\Perique\Services\Registration\Registration_Service;

final class App {

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
	 * @var Hook_Loader
	 */
	protected $loader;

	/**
	 * Checks if the app has already been booted.
	 *
	 * @return bool
	 */
	public static function is_booted(): bool {
		return self::$booted;
	}


	/**
	 * Sets the DI Constainer.
	 *
	 * @param \PinkCrab\Perique\Interfaces\DI_Container $container
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
	 * @param array<string, mixed> $settings
	 * @return self
	 * @throws App_Initialization_Exception Code 5
	 */
	public function set_app_config( array $settings ): self {
		if ( self::$app_config !== null ) {
			throw App_Initialization_Exception::app_config_exists();
		}

		self::$app_config = new App_Config( apply_filters( Hooks::APP_INIT_CONFIG_VALUES, $settings ) );
		return $this;
	}

	/**
	 * Sets the Registration service and loader.
	 *
	 * @param \PinkCrab\Perique\Services\Registration\Registration_Service $registration
	 * @return self
	 */
	public function set_registration_services( Registration_Service $registration ): self {
		if ( $this->registration !== null ) {
			throw App_Initialization_Exception::registation_exists();
		}
		$this->registration = $registration;
		return $this;
	}

	/**
	 * Sets the loader to the app
	 *
	 * @param \PinkCrab\Loader\Hook_Loader $loader
	 * @return self
	 */
	public function set_loader( Hook_Loader $loader ): self {
		if ( $this->loader !== null ) {
			throw App_Initialization_Exception::loader_exists();
		}
		$this->loader = $loader;
		return $this;
	}

	/**
	 * Interace with the container using a callable.
	 *
	 * @param callable(DI_Container):void $callback
	 * @return self
	 * @throws App_Initialization_Exception Code 1
	 */
	public function container_config( callable $callback ): self {
		if ( self::$container === null ) {
			throw App_Initialization_Exception::requires_di_container();
		}
		$callback( self::$container );
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
	 * @return self
	 * @throws App_Initialization_Exception Code 3
	 */
	public function registration_classses( array $class_list ): self {
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

		// Validate.
		$validate = new App_Validation( $this );
		if ( $validate->validate() === false || $this->registration === null ) {
			throw App_Initialization_Exception::failed_boot_validation(
				$validate->errors
			);
		}

		// Process registration
		$this->registration->set_container( self::$container );

		// Run the final process, where all are loaded in via
		$this->finalise();
		self::$booted = true;
		return $this;
	}

	/**
	 * Finialises all settings and boots the app on init hook call (pritority 1)
	 *
	 * @return self
	 */
	protected function finalise(): self {

		// Bind self to container.
		self::$container->addRule(
			'*',
			array(
				'substitutions' => array(
					self::class       => $this,
					App_Config::class => self::$app_config,
				),
			)
		);

		/** @hook{string, App_Config, Loader, DI_Container} */
		do_action( Hooks::APP_INIT_PRE_BOOT, self::$app_config, $this->loader, self::$container ); // phpcs:disable WordPress.NamingConventions.ValidHookName.*

		// Initialise on init
		add_action(
			'init',
			function() {
				do_action( Hooks::APP_INIT_PRE_REGISTRATION, self::$app_config, $this->loader, self::$container );
				$this->registration->process();
				do_action( Hooks::APP_INIT_POST_REGISTRATION, self::$app_config, $this->loader, self::$container );
				$this->loader->register_hooks();
			},
			1
		);

		return $this;
	}

	// Magic Helpers.

	/**
	 * Creates an instance of class using the DI Container.
	 *
	 * @param string $class
	 * @param array<string, mixed> $args
	 * @return object|null
	 * @throws App_Initialization_Exception Code 4
	 */
	public static function make( string $class, array $args = array() ) {
		if ( self::$booted === false ) {
			throw App_Initialization_Exception::app_not_initialized( DI_Container::class );
		}
		return self::$container->create( $class, $args );
	}

	/**
	 * Gets a value from the internal App_Config
	 *
	 * @param string $key The config key to call
	 * @param string ...$child Additional params passed.
	 * @return mixed
	 * @throws App_Initialization_Exception Code 4
	 */
	public static function config( string $key, string ...$child ) {
		if ( self::$booted === false ) {
			throw App_Initialization_Exception::app_not_initialized( App_Config::class );
		}
		return self::$app_config->{$key}( ...$child );
	}

	/**
	 * Returns the View helper, populated with current Renderable engine.
	 *
	 * @return View|null
	 * @throws App_Initialization_Exception Code 4
	 */
	public static function view(): ?View {
		if ( self::$booted === false ) {
			throw App_Initialization_Exception::app_not_initialized( View::class );
		}
		/** @var ?View */
		return self::$container->create( View::class );
	}

	/** @return array{container:DI_Container,app_config:App_Config,booted:bool} */
	public function __debugInfo() {
		return array(
			'container'  => self::$container,
			'app_config' => self::$app_config,
			'booted'     => self::$booted,
		);
	}

	/**
	 * Checks if app config set.
	 *
	 * @return bool
	 */
	public function has_app_config(): bool {
		return is_a( self::$app_config, App_Config::class );
	}
}
