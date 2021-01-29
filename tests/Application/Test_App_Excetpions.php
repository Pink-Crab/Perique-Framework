<?php

declare(strict_types=1);
/**
 * Test for exceptions thrown from App.
 *
 * @since 0.1.0
 * @author Glynn Quelch <glynn.quelch@gmail.com>
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 * @package PinkCrab\Core
 */

namespace PinkCrab\Core\Tests\Application;

use Exception;
use WP_UnitTestCase;
use OutOfBoundsException;
use PinkCrab\Core\Application\App;
use PinkCrab\PHPUnit_Helpers\Reflection;
use PinkCrab\Core\Services\ServiceContainer\Container;


class Test_App_Excetpions extends WP_UnitTestCase {

	protected static $app_backup;

	public static function setUpBeforeClass(): void {
		static::$app_backup = App::get_instance();
	}

	public function tearDown(): void {
		// code
	}


	/**
	 * Ensure instance cant be called if not instanced.
	 *
	 * Unsets the global App::$instance.
	 *
	 * @return void
	 */
	public function test_exception_thorwn_if_getting_instance_before_initalised() {
		$this->expectException( \WPDieException::class );
		$app            = App::get_instance();
		$app::$instance = null;
		// Throw exception and then replace the interal app.
		try {
			App::get_instance();
		} finally {
			$app::$instance = static::$app_backup;
		}
	}

	/**
	 * Test calling get with no internal instance throws and exception.
	 *
	 * @return void
	 */
	public function test_exception_thrown_if_get_with_no_instance(): void {
		$this->expectException( OutOfBoundsException::class );
		$app            = App::get_instance();
		$app::$instance = null;
		// Throw exception and then replace the interal app.
		try {
			$app->get( 'test' );
		} finally {
			$app::$instance = static::$app_backup;
		}
	}

	/**
	 * Test calling get with no internal instance throws and exception.
	 *
	 * @return void
	 */
	public function test_exception_thrown_if_callstatic_with_no_instance(): void {
		$this->expectException( OutOfBoundsException::class );
		$app            = App::get_instance();
		$app::$instance = null;
		// Throw exception and then replace the interal app.
		try {
			$app::failure();
		} finally {
			$app::$instance = static::$app_backup;
		}
	}

	/**
	 * Test calling get with no internal instance throws and exception.
	 *
	 * @return void
	 */
	public function test_exception_thrown_if_retreive_with_no_instance(): void {
		$this->expectException( OutOfBoundsException::class );
		$app            = App::get_instance();
		$app::$instance = null;
		// Throw exception and then replace the interal app.
		try {
			$app::retreive( 'foo' );
		} finally {
			$app::$instance = static::$app_backup;
		}
	}

	/**
	 * Test calling get with no internal instance throws and exception.
	 *
	 * @return void
	 */
	public function test_exception_thrown_if_make_with_no_instance(): void {
		$this->expectException( OutOfBoundsException::class );
		$app            = App::get_instance();
		$app::$instance = null;
		// Throw exception and then replace the interal app.
		try {
			$app::make( 'foo' );
		} finally {
			$app::$instance = static::$app_backup;
		}
	}

	/**
	 * Test calling get with no internal instance throws and exception.
	 *
	 * @return void
	 */
	public function test_exception_thrown_if_config_with_no_instance(): void {
		$this->expectException( OutOfBoundsException::class );
		$app            = App::get_instance();
		$app::$instance = null;
		// Throw exception and then replace the interal app.
		try {
			$app::config( 'foo', 'a', 'b' );
		} finally {
			$app::$instance = static::$app_backup;
		}
	}

    /**
     * Test the app can not woken up
     * This uses a string version as it cant be serialised either.
     *
     * @return void
     */
	public function test_exception_throw_serialised(): void {
		$this->expectException( Exception::class );
		\unserialize( 'O:29:"PinkCrab\Core\Application\App":0:{}' );
	}
}
