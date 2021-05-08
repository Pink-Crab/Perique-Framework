<?php

declare(strict_types=1);
/**
 * Facade style, class proxy.
 *
 * @since 0.2.0
 * @author Glynn Quelch <glynn.quelch@gmail.com>
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 * @package PinkCrab\Perique
 */

namespace PinkCrab\Perique\Application;

use PinkCrab\Perique\Application\App;
use PinkCrab\Perique\Application\App_Config;


class Config {


	/**
	 * Holds the current config object.
	 *
	 * @var App_Config|null
	 */
	protected static $config_cache;

	/**
	 * Calls the static method from the config.
	 * Sets the config cache on first call.
	 *
	 * @param string $method
	 * @param array<int, mixed> $params
	 * @return mixed
	 */
	public static function __callStatic( $method, $params ) {
		if ( ! self::$config_cache ) {
			/** @phpstan-ignore-next-line */
			self::$config_cache = App::make( App_Config::class );
		}

		return self::$config_cache->{$method}( ...$params );
	}
}
