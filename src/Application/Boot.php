<?php

declare(strict_types=1);

/**
 * Handles the booting of the application.
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
 * @since 0.3.9
 * @phpcs:disable WordPress.NamingConventions.ValidHookName.UseUnderscores
 * @phpcs:disable WordPress.NamingConventions.ValidHookName.NotLowercase
 */

namespace PinkCrab\Core\Application;

use Dice\Dice;
use PinkCrab\Loader\Loader;
use PinkCrab\Core\Application\App;
use PinkCrab\Core\Services\Dice\WP_Dice;
use PinkCrab\Core\Application\App_Config;
use PinkCrab\Core\Services\ServiceContainer\Container;
use PinkCrab\Core\Services\Registration\Register_Loader;

class Boot {

	/**
	 * @var string
	 */
	protected $settings_path = '';
	/**
	 * @var string
	 */
	protected $dependencies_path = '';
	/**
	 * @var string
	 */
	protected $registerables_path = '';
	/**
	 * @var Loader
	 */
	protected $loader;
	/**
	 * @var WP_Dice
	 */
	protected $wp_di;
	/**
	 * @var App
	 */
	protected $app;
	/**
	 * @var Container
	 */
	protected $container;
	/**
	 * @var App_Config
	 */
	protected $app_settings;

	public function __construct(
		string $settings_path = '',
		string $dependencies_path = '',
		string $registerables_path = ''
	) {
		$this->settings_path      = $settings_path;
		$this->dependencies_path  = $dependencies_path;
		$this->registerables_path = $registerables_path;
	}

	/**
	 * Populates the settings.
	 *
	 * @return self
	 */
	protected function populate_app_config(): self {
		$settings = file_exists( $this->settings_path )
			? require_once $this->settings_path
			: array();

		$this->app_settings = new App_Config( apply_filters( 'PinkCrab/Boot/app_config', $settings, $this ) ); /* @phpstan-ignore-line */
		return $this;
	}

	/**
	 * Adds all the rules to DI.
	 *
	 * @uses PinkCrab/Boot/dependencies filter
	 * @return void
	 */
	protected function register_dependencies(): void {
		$dependencies = file_exists( $this->dependencies_path )
			? include_once $this->dependencies_path
			: array();
		$this->app->get( 'di' )->addRules( apply_filters( 'PinkCrab/Boot/dependencies', $dependencies, $this ) ); /* @phpstan-ignore-line */
	}

	/**
	 * Registers all registerable instances.
	 *
	 * @uses PinkCrab/Boot/registerables filter
	 * @return void
	 */
	protected function register_registerables(): void {
		$registerables = file_exists( $this->registerables_path )
			? include_once $this->registerables_path
			: array();

		Register_Loader::initalise(
			$this->app,
			apply_filters( 'PinkCrab/Boot/registerables', $registerables, $this ), /* @phpstan-ignore-line */
			$this->loader
		);
	}

	/**
	 * Binds DI and Config to the apps internal container.
	 *
	 * @return void
	 */
	protected function bind_internal_services(): void {
		$this->container->set( 'di', $this->wp_di );
		$this->container->set( 'config', $this->app_settings );
	}

	/**
	 * Constructs all internal services.
	 *
	 * @return self
	 */
	public function initialise(): self {

		// Construct all services.
		$this->loader    = Loader::boot();
		$this->container = new Container();
		$this->wp_di     = WP_Dice::constructWith( new Dice() );

		// Bind & populate all internal services.
		$this->populate_app_config();
		return $this;
	}

	/**
	 * Binds a service to the apps container.
	 *
	 * @param string $key
	 * @param object $service
	 * @return self
	 */
	public function bind_to_container( string $key, object $service ): self {
		$this->container->set( $key, $service );
		return $this;
	}

	/**
	 * Finialises all settings and boots the app on init hook call (pritority 1)
	 *
	 * @return self
	 */
	public function finalise(): self {
		$this->bind_internal_services();

		do_action( 'PinkCrab/Boot/pre_app_init', $this ); // phpcs:disable WordPress.NamingConventions.ValidHookName.*
		$this->app = App::init( $this->container );

		// Initialise on init
		add_action(
			'init',
			function() {
				do_action( 'PinkCrab/Boot/pre_registration', $this );
				$this->register_dependencies();
				$this->register_registerables();
				do_action( 'PinkCrab/Boot/post_registration', $this );
				$this->loader->register_hooks();
			},
			1
		);

		return $this;
	}
}
