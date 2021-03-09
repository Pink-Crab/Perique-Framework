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
 */

namespace PinkCrab\Core\Application;

use Dice\Dice;
use PinkCrab\Loader\Loader;
use PinkCrab\Core\Services\Dice\WP_Dice;
use PinkCrab\Core\Services\ServiceContainer\Container;

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
	protected $wp_di;
	protected $app;
	protected $container;
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
	 * Populates the settings.
	 *
	 * @return self
	 */
	protected function populate_settings(): self {
		$settings = file_exists( $this->settings )
			? require_once $this->settings
			: array();

		$this->app_settings = new App_Config( $filter ? $filter( $settings ) : $settings );
		return $this;
	}

	/**
	 * Adds all the rules to DI.
	 *
	 * @return void
	 */
	protected function build_di_rules(): void {
		if ( file_exists( 'config/dependencies.php' ) ) {
			$dependencies = include 'config/dependencies.php';
			$this->wp_di->addRules( $dependencies );
		}
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
		$this->populate_settings();
		$this->build_di_rules();
		$this->bind_internal_services();
		return $this;
	}

	/**
	 * Binds DI and Config to the apps internal container.
	 *
	 * @return void
	 */
	public function bind_internal_services(): void {
		$this->container->set( 'di', $this->wp_di );
		$this->container->set( 'config', $this->app_settings );
	}

	/**
	 * Checks all essentials services are created and bound.
	 *
	 * @return bool
	 */
	protected function verfiy(): bool {
		# code...
	}

	public function finalise(): App {
		$this->app = App::init( $this->container );
	}
}
