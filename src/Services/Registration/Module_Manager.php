<?php

declare(strict_types=1);

/**
 * Module loader
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
 * @package PinkCrab\Perique\Module
 * @since 2.0.0
 */

namespace PinkCrab\Perique\Services\Registration;

use Exception;
use PinkCrab\Loader\Hook_Loader;
use PinkCrab\Perique\Application\Hooks;
use PinkCrab\Perique\Interfaces\Module;
use PinkCrab\Perique\Interfaces\DI_Container;
use PinkCrab\Perique\Interfaces\Inject_Hook_Loader;
use PinkCrab\Perique\Interfaces\Registration_Middleware;
use PinkCrab\Perique\Exceptions\Module_Manager_Exception;
use PinkCrab\Perique\Services\Registration\Registration_Service;

final class Module_Manager {

	/**
	 * Modules
	 *
	 * @since 2.0.0
	 * @var array{
	 *  0:class-string<Module>,
	 *  1:?callable(Module, ?Registration_Middleware):Module
	 * }[]
	 */
	protected array $modules = array();

	/**
	 * Access to the DI Container
	 *
	 * @var DI_Container
	 */
	private DI_Container $di_container;

	/**
	 * Manages all the Registration Middleware.
	 *
	 * @var Registration_Service
	 */
	private Registration_Service $registration_service;

	/**
	 * Creates a new instance of the registration service.
	 *
	 * @param DI_Container $di_container
	 */
	public function __construct( DI_Container $di_container, Registration_Service $registration_service ) {
		$this->di_container = $di_container;

		// Create the registration service.
		$this->registration_service = $registration_service;
	}

	/**
	 * Adds a module to the manager.
	 *
	 * @template Module_Instance of Module
	 * @param class-string<Module_Instance> $module_name
	 * @param ?callable(Module, ?Registration_Middleware):Module $config
	 * @return void
	 */
	public function push_module( string $module_name, ?callable $config = null ): void {
		$this->modules[] = array( $module_name, $config );
	}

	/**
	 * Adds a class to the Registration Service.
	 *
	 * @param class-string $class
	 */
	public function register_class( string $class ): void {
		$this->registration_service->push_class( $class );
	}

	/**
	 * Creates and registers all modules.
	 *
	 * @throws Module_Manager_Exception If invalid module class name provided (Code 20)
	 * @throws Module_Manager_Exception If module does not implement Module (Code 21)
	 * @throws Module_Manager_Exception If module does not implement Registration_Middleware (Code 22)
	 */
	public function register_modules(): void {
		// Allow for additional apps to hook into the Module Manager.
		do_action( Hooks::MODULE_MANAGER, $this );

		foreach ( $this->modules as list($module_name, $config) ) {
			// Create the instance.
			$module = $this->create_module( $module_name );

			// Create the middleware.
			$middleware = $this->create_middleware( $module );

			// If a config is provided, call it.
			if ( ! is_null( $config ) ) {
				$module = $config( $module, $middleware );
			}

			// Add to the modules and register all hooks.
			$this->register_hooks( $module );

			// Add to the middleware, if provided.
			if ( ! is_null( $middleware ) ) {
				$this->registration_service->push_middleware( $middleware );
			}
		}
	}

	/**
	 * Creates the module from its class name.
	 *
	 * @param class-string<Module> $module
	 * @return Module
	 */
	private function create_module( string $module ): Module {
		$instance = $this->di_container->create( $module );

		// If not an object or not an instance of the module interface, throw.
		if ( ! is_object( $instance )
		|| ! is_a( $instance, Module::class, true )
		) {
			throw Module_Manager_Exception::invalid_module_class_name( $module );
		}

		return $instance;
	}

	/**
	 * Create the middleware from a module.
	 *
	 * @param Module $module
	 * @return Registration_Middleware|null
	 */
	private function create_middleware( Module $module ): ?Registration_Middleware {
		$middleware = $module->get_middleware();

		// If no middleware is provided, return null.
		if ( is_null( $middleware ) ) {
			return null;
		}

		// If not an object or not an instance of the module interface, throw.
		if ( ! is_a( $middleware, Registration_Middleware::class, true ) ) {
			throw Module_Manager_Exception::invalid_registration_middleware( $middleware );
		}

		// Create the middleware.
		$middleware = $this->di_container->create( $middleware );

		// If the middleware is not an object, throw.
		if ( ! is_object( $middleware )
		|| ! is_a( $middleware, Registration_Middleware::class, true )
		) {
			throw Module_Manager_Exception::failed_to_create_registration_middleware( $middleware );
		}

		return $middleware;
	}

	/**
	 * Register all hooks for modules.
	 *
	 * @param Module $module
	 * @return void
	 */
	private function register_hooks( Module $module ): void {
		add_action( Hooks::APP_INIT_PRE_BOOT, array( $module, 'pre_boot' ), 10, 3 );
		add_action( Hooks::APP_INIT_PRE_REGISTRATION, array( $module, 'pre_register' ), 10, 3 );
		add_action( Hooks::APP_INIT_POST_REGISTRATION, array( $module, 'post_register' ), 10, 3 );
	}

	/**
	 * Process all the middleware using Registration Service.
	 *
	 * @return void
	 */
	public function process_middleware(): void {

		// Register all modules.
		// $this->register_modules();

		// Process all middleware.
		$this->registration_service->process();
	}

}
