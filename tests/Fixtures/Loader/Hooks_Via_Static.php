<?php

declare(strict_types=1);
/**
 * Test class for registering hooks via a class (static methods.)
 *
 * @since 0.3.6
 * @author Glynn Quelch <glynn.quelch@gmail.com>
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 * @package PinkCrab\Core
 */

namespace PinkCrab\Core\Tests\Fixtures\Loader;

class Hooks_Via_Static {

	public const ACTION_HANDLE = 'some_action_handle_static';
	public const FILTER_HANDLE = 'some_filter_handle_static';

	public function register_action() {
		add_action( self::ACTION_HANDLE, array( self::class, 'action_callback_static' ) );
	}

	public function register_filter() {
		add_filter( self::FILTER_HANDLE, array( self::class, 'filter_callback_static' ) );
	}

	public static function action_callback_static(): void {
		//
	}

	public static function filter_callback_static( string $var ): string {
		return self::class;
	}

}
