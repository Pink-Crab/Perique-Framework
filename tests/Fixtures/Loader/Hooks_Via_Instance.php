<?php

declare(strict_types=1);
/**
 * Test class for registering hooks via a class
 *
 * @since 0.3.6
 * @author Glynn Quelch <glynn.quelch@gmail.com>
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 * @package PinkCrab\Core
 */

namespace PinkCrab\Core\Tests\Fixtures\Loader;

class Hooks_Via_Instance {

	public const ACTION_HANDLE = 'some_action_handle_instance';
	public const FILTER_HANDLE = 'some_filter_handle_instance';

	public function register_filter() {
		add_filter( self::FILTER_HANDLE, array( $this, 'filter_callback_instance' ) );
	}

	public function register_action() {
		add_action( self::ACTION_HANDLE, array( $this, 'action_callback_instance' ) );
	}

	public function action_callback_instance(): void {
		//
	}

	public function filter_callback_instance( string $var ): string {
		return self::class;
	}

}
