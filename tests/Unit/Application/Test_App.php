<?php

declare(strict_types=1);

/**
 * Unit tests for the App class.
 *
 * @since 2.0.0
 * @author Glynn Quelch <glynn.quelch@gmail.com>
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 * @package PinkCrab\Perique
 */

namespace PinkCrab\Perique\Tests\Unit\Application;

use WP_UnitTestCase;
use PinkCrab\Perique\Application\App;
use PinkCrab\Perique\Application\App_Validation;

/**
 * @group unit
 * @group app
 */
class Test_App extends WP_UnitTestCase {

	/** @testdox When setting the App_Config to the App, it should not be possible to define the base and view paths and urls. These should be implied by the apps base path */
	public function test_cannot_set_base_and_view_paths_and_urls() : void {
		$app = new App( FIXTURES_PATH );
		$app->set_app_config(
			array(
				'path' => array(
					'plugin' => '/path/to/something',
					'view'   => '/path/to/something/views',
				),
				'url'  => array(
					'plugin' => 'https://www.something.else',
					'view'   => 'https://www.something.else/views',
				),
			)
		);

		// Get app config
		$config = $app->__debugInfo()['app_config'];

		// Check the base path and view
		$this->assertEquals( FIXTURES_PATH . \DIRECTORY_SEPARATOR, $config->path( 'plugin' ) );
		$this->assertEquals( FIXTURES_PATH . \DIRECTORY_SEPARATOR . 'views' . \DIRECTORY_SEPARATOR, $config->path( 'view' ) );

		// Check the base url and view
		$this->assertEquals( 'http://example.org/wp-content/plugins/Fixtures/', $config->url( 'plugin' ) );
		$this->assertEquals( 'http://example.org/wp-content/plugins/Fixtures/views/', $config->url( 'view' ) );
	}

	/** @testdox It should be possible to access the base_path and view_path in the App from the debugInfo magic method */
	public function test_can_access_base_and_view_paths_from_debug_info() : void {
		$app = new App( FIXTURES_PATH );

		// Get app config
		$debug = $app->__debugInfo();

		// Check the base path and view
		$this->assertEquals( FIXTURES_PATH, $debug['base_path'] );
		$this->assertEquals( FIXTURES_PATH . \DIRECTORY_SEPARATOR . 'views', $debug['view_path'] );
	}


}
