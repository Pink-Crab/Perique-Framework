<?php

declare(strict_types=1);
/**
 * Mock Registerable_Middleware implementation
 * 
 * Can pass custom message to be echoed on process
 * if no message passed, echos the name of the class passed
 *
 * @since 0.4.0
 * @author Glynn Quelch <glynn.quelch@gmail.com>
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 * @package PinkCrab\Core
 */

namespace PinkCrab\Core\Tests\Fixtures\Mock_Objects;

use PinkCrab\Core\Interfaces\Registration_Middleware;

class Mock_Registation_Middleware implements Registration_Middleware {

	public $message;

	public function __construct( ?string $message = null ) {
		$this->message = $message;
	}

	public function process( $class ) {
		echo $this->message ?? \get_class( $class );
	}
}
