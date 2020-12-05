<?php

declare(strict_types=1);
/**
 * OOP Action loader
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

use PinkCrab\Core\Collection\Collection;

if ( ! defined( 'WPINC' ) ) {
	die;
}

class Loader {


	protected $global;
	protected $admin;
	protected $front;
	protected $shortcode;
	protected $ajax;
	public static $instance;

	/**
	 * Creates an instance of the loader.
	 */
	public function __construct() {
		$this->global    = new Collection();
		$this->admin     = new Collection();
		$this->front     = new Collection();
		$this->shortcode = new Collection();
		$this->ajax      = new Collection();
	}

	/**
	 * Boots the loader as a static instance.
	 *
	 * @return void
	 */
	public static function boot(): self {
		return self::$instance ?? self::$instance = new Loader();
	}

	/**
	 * Adds and admin hook to the collection.
	 *
	 * @param string                $handle
	 * @param string|array|callable $method
	 * @param integer               $priority
	 * @param integer               $args
	 * @return void
	 */
	public function admin_action( string $handle, $method, int $priority = 10, int $args = 1 ): void {
		$this->admin->push(
			array(
				'type'     => 'action',
				'handle'   => $handle,
				'method'   => $method,
				'priority' => $priority,
				'args'     => $args,
			)
		);
	}

	/**
	 * Adds an admin filter to the admin collection.
	 *
	 * @param string                $handle
	 * @param string|array|callable $method
	 * @param integer               $priority
	 * @param integer               $args
	 * @return void
	 */
	public function admin_filter( string $handle, $method, int $priority = 10, int $args = 1 ) {
		$this->admin->push(
			array(
				'type'     => 'filter',
				'handle'   => $handle,
				'method'   => $method,
				'priority' => $priority,
				'args'     => $args,
			)
		);
	}

	/**
	 * Adds an action for the front end.
	 *
	 * @param string                $handle
	 * @param string|array|callable $method
	 * @param integer               $priority
	 * @param integer               $args
	 * @return void
	 */
	public function front_action( string $handle, $method, int $priority = 10, int $args = 1 ): void {
		$this->front->push(
			array(
				'type'     => 'action',
				'handle'   => $handle,
				'method'   => $method,
				'priority' => $priority,
				'args'     => $args,
			)
		);
	}

	/**
	 * Adds an front filter to the front collection.
	 *
	 * @param string                $handle
	 * @param string|array|callable $method
	 * @param integer               $priority
	 * @param integer               $args
	 * @return void
	 */
	public function front_filter( string $handle, $method, int $priority = 10, int $args = 1 ) {
		$this->front->push(
			array(
				'type'     => 'filter',
				'handle'   => $handle,
				'method'   => $method,
				'priority' => $priority,
				'args'     => $args,
			)
		);
	}

	/**
	 * Adds an action for the global end.
	 *
	 * @param string                $handle
	 * @param string|array|callable $method
	 * @param integer               $priority
	 * @param integer               $args
	 * @return void
	 */
	public function action( string $handle, $method, int $priority = 10, int $args = 1 ): void {
		$this->global->push(
			array(
				'type'     => 'action',
				'handle'   => $handle,
				'method'   => $method,
				'priority' => $priority,
				'args'     => $args,
			)
		);
	}

	/**
	 * Adds an global filter to the global collection.
	 *
	 * @param string                $handle
	 * @param string|array|callable $method
	 * @param integer               $priority
	 * @param integer               $args
	 * @return void
	 */
	public function filter( string $handle, $method, int $priority = 10, int $args = 1 ) {
		$this->global->push(
			array(
				'type'     => 'filter',
				'handle'   => $handle,
				'method'   => $method,
				'priority' => $priority,
				'args'     => $args,
			)
		);
	}

	/**
	 * Adds a shortcode to the loader.
	 *
	 * @param string                $handle
	 * @param string|array|callable $method
	 * @return void
	 */
	public function shortcode( string $handle, $method ) {
		$this->shortcode->push(
			array(
				'handle' => $handle,
				'method' => $method,
			)
		);
	}

	public function ajax( string $handle, $method, $public = true, $private = true ) {
		$this->ajax->push(
			array(
				'handle'  => $handle,
				'method'  => $method,
				'public'  => $public,
				'private' => $private,
			)
		);
	}

	/**
	 * Registers all the added hooks.
	 *
	 * @return void
	 */
	public function register_hooks() {


		// Register shortcodes.
		$this->shortcode->apply(
			function ( $shortcode ) {
				if ( is_array( $shortcode ) ) {
					add_shortcode( $shortcode['handle'], $shortcode['method'] );
				}
			}
		);
		// Register Ajax Calls.
		$this->ajax->apply(
			function ( $ajax ) {
				if ( is_array( $ajax ) ) {
					// If public (none logged in users)
					if ( $ajax['public'] ) {
						$this->global->push(
							array(
								'type'     => 'action',
								'handle'   => 'wp_ajax_nopriv_' . $ajax['handle'],
								'method'   => $ajax['method'],
								'priority' => 10,
								'args'     => 1,
							)
						);
					}
					// If private (logged in users only)
					if ( $ajax['private'] ) {
						$this->global->push(
							array(
								'type'     => 'action',
								'handle'   => 'wp_ajax_' . $ajax['handle'],
								'method'   => $ajax['method'],
								'priority' => 10,
								'args'     => 1,
							)
						);
					}
				}
			}
		);

		/**
		 * Register all globals hooks.
		 */
		$this->global->apply(
			function ( $hook ) {
				if ( is_array( $hook ) ) {
					$this->registerHookCallback( $hook );
				}
			}
		);

		/**
		 * Register all admin only hooks.
		 */
		if ( is_admin() ) {
			$this->admin->apply(
				function ( $hook ) {
					if ( is_array( $hook ) ) {
						$this->registerHookCallback( $hook );
					}
				}
			);
		}

		/**
		 * Register all admin only hooks.
		 */
		if ( ! is_admin() ) {
			$this->front->apply(
				function ( $hook ) {
					if ( is_array( $hook ) ) {
						$this->registerHookCallback( $hook );
					}
				}
			);
		}
	}

	/**
	 * Registers both hooks and filters.
	 *
	 * @param array $hook
	 * @return void
	 */
	private function registerHookCallback( array $hook ) {

		switch ( $hook['type'] ) {
			case 'action':
				add_action( $hook['handle'], $hook['method'], $hook['priority'], $hook['args'] );
				break;

			case 'filter':
				add_filter( $hook['handle'], $hook['method'], $hook['priority'], $hook['args'] );
				break;
		}
	}
}
