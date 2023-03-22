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

use PinkCrab\Loader\Hook_Loader;
use PinkCrab\Perique\Application\Hooks;
use PinkCrab\Perique\Interfaces\Module;
use PinkCrab\Perique\Services\View\View;
use PinkCrab\Perique\Utils\Object_Helper;
use PinkCrab\Perique\Application\App_Config;
use PinkCrab\Perique\Interfaces\DI_Container;
use PinkCrab\Perique\Application\App_Validation;
use PinkCrab\Perique\Interfaces\Inject_App_Config;
use PinkCrab\Perique\Utils\App_Config_Path_Helper;
use PinkCrab\Perique\Interfaces\Inject_Hook_Loader;
use PinkCrab\Perique\Interfaces\Inject_DI_Container;
use PinkCrab\Perique\Interfaces\Registration_Middleware;
use PinkCrab\Perique\Services\Registration\Module_Manager;
use PinkCrab\Perique\Exceptions\App_Initialization_Exception;

final class App {


	/**
	 * Defines if the app has already been booted.
	 *
	 * @var bool
	 */
	private static bool $booted = false;

	/**
	 * Dependency Injection Container
	 *
	 * @var DI_Container|null
	 */
	private static ?DI_Container $container = null;

	/**
	 * The Apps Config
	 *
	 * @var App_Config|null
	 */
	private static ?App_Config $app_config = null;

	/**
	 * Handles all modules.
	 *
	 * @var Module_Manager|null
	 */
	private ?Module_Manager $module_manager = null;

	/**
	 * Hook Loader
	 *
	 * @var Hook_Loader|null
	 */
	private ?Hook_Loader $loader = null;

	/**
	 * App Base path.
	 *
	 * @var string
	 */
	private string $base_path;

	/**
	 * Apps view path.
	 *
	 * @var ?string
	 */
	private ?string $view_path;

	/**
	 * Checks if the app has already been booted.
	 *
	 * @return bool
	 */
	public static function is_booted(): bool {
		return self::$booted;
	}

	/**
	 * Creates an instance of the app.
	 *
	 * @param string $base_path
	 */
	public function __construct( string $base_path ) {
		$this->base_path = $base_path;

		// Assume the view path.
		$this->view_path = rtrim( $this->base_path, '/\\' ) . \DIRECTORY_SEPARATOR . 'views';
	}

	/**
	 * Set the view path.
	 *
	 * @param string $view_path
	 * @return self
	 */
	public function set_view_path( string $view_path ): self {
		$this->view_path = $view_path;
		return $this;
	}

	/**
	 * Sets the DI Container.
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
	 * Checks if the Module_Manager has been set.
	 *
	 * @return bool
	 */
	public function has_module_manager(): bool {
		return $this->module_manager instanceof Module_Manager;
	}


	/**
	 * Define the app config.
	 *
	 * @param array<string, mixed> $settings
	 * @return self
	 * @throws App_Initialization_Exception Code 5
	 */
	public function set_app_config( array $settings ): self {
		if ( self::$app_config !== null ) {
			throw App_Initialization_Exception::app_config_exists();
		}

		// Run through the filter to allow for config changes.
		$settings = apply_filters( Hooks::APP_INIT_CONFIG_VALUES, $settings );

		// Ensure the base path and url are defined from app.
		$settings['path']           = $settings['path'] ?? array();
		$settings['path']['plugin'] = $this->base_path;
		$settings['path']['view']   = $this->view_path ?? App_Config_Path_Helper::assume_view_path( $this->base_path );

		// Get the url from the base path.
		$settings['url']           = $settings['url'] ?? array();
		$settings['url']['plugin'] = App_Config_Path_Helper::assume_base_url( $this->base_path );
		$settings['url']['view']   = App_Config_Path_Helper::assume_view_url(
			$this->base_path,
			$this->view_path ?? App_Config_Path_Helper::assume_view_path( $this->base_path )
		);

		self::$app_config = new App_Config( $settings );
		return $this;
	}

	/**
	 * Set the module manager.
	 *
	 * @param Module_Manager $module_manager
	 * @return self
	 * @throws App_Initialization_Exception Code 10
	 */
	public function set_module_manager( Module_Manager $module_manager ): self {
		if ( $this->module_manager !== null ) {
			throw App_Initialization_Exception::module_manager_exists();
		}

		$this->module_manager = $module_manager;
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
	 * Interface with the container using a callable.
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
	 * Sets the class list.
	 *
	 * @param array<class-string> $class_list
	 * @return self
	 * @throws App_Initialization_Exception Code 3
	 */
	public function registration_classes( array $class_list ): self {
		if ( $this->module_manager === null ) {
			throw App_Initialization_Exception::requires_module_manager();
		}

		foreach ( $class_list as $class ) {
			$this->module_manager->register_class( $class );
		}
		return $this;
	}

	/**
	 * Adds a module to the app.
	 *
	 * @template Module_Instance of Module
	 * @param class-string<Module_Instance> $module
	 * @param ?callable(Module, ?Registration_Middleware):Module $callback
	 * @return self
	 * @throws App_Initialization_Exception Code 1 If DI container not registered
	 * @throws App_Initialization_Exception Code 3 If module manager not defined.
	 */
	public function module( string $module, ?callable $callback = null ): self {
		// Check if module manager exists.
		if ( $this->module_manager === null ) {
			throw App_Initialization_Exception::requires_module_manager();
		}

		if ( self::$container === null ) {
			throw App_Initialization_Exception::requires_di_container();
		}

		$this->module_manager->push_module( $module, $callback );

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
		if ( $validate->validate() === false ) {
			throw App_Initialization_Exception::failed_boot_validation(
				$validate->errors
			);
		}

		// Run the final process, where all are loaded in via
		$this->finalise();
		self::$booted = true;
		return $this;
	}

	/**
	 * Finialises all settings and boots the app on init hook call (priority 1)
	 *
	 * @return self
	 * @throws App_Initialization_Exception (code 9)
	 */
	private function finalise(): self {

		// As we have passed validation
		/**
		 * @var DI_Container self::$container
		 */

		// Bind self to container.
		self::$container->addRule( // @phpstan-ignore-line, already verified if not null
			'*',
			array(
				'substitutions' => array(
					self::class         => $this,
					DI_Container::class => self::$container,
					\wpdb::class        => $GLOBALS['wpdb'],
				),
			)
		);

		self::$container->addRule( // @phpstan-ignore-line, already verified if not null
			App_Config::class,
			array(
				'constructParams' => array(
					// @phpstan-ignore-next-line, already verified if not null
					self::$app_config->export_settings(),
				),
			)
		);

		// Allow the passing of Hook Loader via interface and method injection.
		self::$container->addRule( // @phpstan-ignore-line, already verified if not null
			Inject_Hook_Loader::class,
			array(
				'call' => array(
					array( 'set_hook_loader', array( $this->loader ) ),
				),
			)
		);

		//Allow the passing of App Config via interface and method injection.
		self::$container->addRule( // @phpstan-ignore-line, already verified if not null
			Inject_App_Config::class,
			array(
				'call' => array(
					array( 'set_app_config', array( self::$app_config ) ),
				),
			)
		);

		// Allow the passing of DI Container via interface and method injection.
		self::$container->addRule( // @phpstan-ignore-line, already verified if not null
			Inject_DI_Container::class,
			array(
				'call' => array(
					array( 'set_di_container', array( self::$container ) ),
				),
			)
		);

		// Build all modules and middleware.
		$this->module_manager->register_modules(); // @phpstan-ignore-line, already verified if not null

		/** @hook{string, App_Config, Loader, DI_Container} */
		do_action( Hooks::APP_INIT_PRE_BOOT, self::$app_config, $this->loader, self::$container ); // phpcs:disable WordPress.NamingConventions.ValidHookName.*

		// Initialise on init
		add_action(
			'init',
			function () {
				do_action( Hooks::APP_INIT_PRE_REGISTRATION, self::$app_config, $this->loader, self::$container );
				$this->module_manager->process_middleware(); // @phpstan-ignore-line, already verified if not null
				do_action( Hooks::APP_INIT_POST_REGISTRATION, self::$app_config, $this->loader, self::$container );
				$this->loader->register_hooks(); // @phpstan-ignore-line, if loader is not defined, exception will be thrown above
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
	public static function make( string $class, array $args = array() ): ?object {
		if ( self::$booted === false ) {
			throw App_Initialization_Exception::app_not_initialized( DI_Container::class );
		}
		return self::$container->create( $class, $args ); // @phpstan-ignore-line, already verified if not null
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
		return self::$container->create( View::class ); // @phpstan-ignore-line, already verified if not null
	}

	/** @return array{
	 *  container:?DI_Container,
	 *  app_config:?App_Config,
	 *  booted:bool,
	 *  module_manager:?Module_Manager,
	 *  base_path:string,
	 *  view_path:?string
	 * } */
	public function __debugInfo() {
		return array(
			'container'      => self::$container,
			'app_config'     => self::$app_config,
			'booted'         => self::$booted,
			'module_manager' => $this->module_manager,
			'base_path'      => $this->base_path,
			'view_path'      => $this->view_path,
		);
	}

	/**
	 * Checks if app config set.
	 *
	 * @return bool
	 */
	public function has_app_config(): bool {
		return Object_Helper::is_a( self::$app_config, App_Config::class );
	}

	/**
	 * Returns the defined container.
	 *
	 * @return DI_Container
	 * @throws App_Initialization_Exception (Code 1)
	 */
	public function get_container(): DI_Container {
		if ( self::$container === null ) {
			// Throw container not set.
			throw App_Initialization_Exception::requires_di_container();
		}
		return self::$container;
	}
}
