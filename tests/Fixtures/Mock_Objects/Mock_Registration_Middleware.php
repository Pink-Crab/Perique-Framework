<?php

declare(strict_types=1);
/**
 * Mock Hookable_Middleware implementation
 *
 * Can pass custom message to be echoed on process
 * if no message passed, echos the name of the class passed
 *
 * @since 0.4.0
 * @author Glynn Quelch <glynn.quelch@gmail.com>
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 * @package PinkCrab\Perique
 */

namespace PinkCrab\Perique\Tests\Fixtures\Mock_Objects;

use PinkCrab\Perique\Interfaces\Registration_Middleware;
use PinkCrab\Perique\Services\Container_Aware_Traits\Inject_Hook_Loader_Aware;
use PinkCrab\Perique\Services\Container_Aware_Traits\Inject_DI_Container_Aware;


class Mock_Registration_Middleware implements Registration_Middleware {
	use Inject_Hook_Loader_Aware, Inject_DI_Container_Aware;

	public $message;

	/** Used for testing setup and teardown */
	public $message_log = array();

	public function __construct( ?string $message = null ) {
		$this->message = $message;
	}

	public function process( object $class ): object {
		$this->message_log[] = $this->message ?? \get_class( $class );
		echo $this->message ?? \get_class( $class );
		return $class;

	}


	/**
	 * Used to for any middleware setup before process is called
	 *
	 * @return void
	 */
	public function setup(): void {
		$this->message_log[] = 'setup';
	}

	/**
	 * Used after all classes have been passed through process.
	 *
	 * @return void
	 */
	public function tear_down(): void {
		$this->message_log[] = 'tear_down';
	}

}
