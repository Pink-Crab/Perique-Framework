<?php

declare(strict_types=1);
/**
 * Base config object.
 *
 * @since 0.2.0
 * @author Glynn Quelch <glynn.quelch@gmail.com>
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 * @package PinkCrab\Core
 */

namespace PinkCrab\Core\Tests\Application;

use WP_UnitTestCase;
use PinkCrab\Core\Application\App;
use PinkCrab\Core\Application\Config;
use PinkCrab\Core\Application\App_Config;

class Test_App_Config extends WP_UnitTestCase {

	/**
	 * Sample set of settings paths, would be passed on start up.
	 */
	public const SAMPLE_SETTINGS = array(
		'additional' => array(
			'array'  => array( 1, 2, 3, 4 ),
			'string' => 'HI',
		),
		'namespaces' => array( 'rest' => 'fake_rest' ),
	);

	/**
	 * Test that an instance of App Config can be created.
	 *
	 * @return void
	 */
	public function test_can_create(): void {
		// With additional settings
		$this->assertInstanceOf( App_Config::class, new App_Config( self::SAMPLE_SETTINGS ) );
		// Without additional settings
		$this->assertInstanceOf( App_Config::class, new App_Config );
	}

	/**
	 * Test that the config can be accessed
	 *
	 * This doesnt check the value, only that values are returned.
	 * The values are set in App setup, so cant be checked from here.
	 *
	 * @return void
	 */
	public function test_can_call_with_config_proxy(): void {
		$this->assertTrue( is_string( Config::namespace( 'rest' ) ) );
		$this->assertTrue( is_array( Config::path() ) );
		$this->assertTrue( is_array( Config::url() ) );
	}

	/**
	 * Tests that the additional keys can be called out.
	 *
	 * @return void
	 */
	public function test_can_add_additional_keys() {
		$app_config = new App_Config( self::SAMPLE_SETTINGS );

		$this->assertEquals( 'HI', $app_config->string );
		$this->assertEquals( 'HI', $app_config->additional( 'string' ) );

		$this->assertTrue( is_array( $app_config->array ) );
		$this->assertEquals( 1, $app_config->array[0] );
		$this->assertEquals( 3, $app_config->additional( 'array' )[2] );
	}

	/**
	 * Test that defaults defined can be overwritten by the values in the
	 * settings.php file.
	 *
	 * @return void
	 */
	public function test_default_values_can_be_overwritten_via_settings() {
		$no_override   = App::make( App_Config::class );
		$with_override = new App_Config( self::SAMPLE_SETTINGS );

		$this->assertNotEquals(
			$no_override->namespace( 'rest' ),
			$with_override->namespace( 'rest' )
		);
	}
}
