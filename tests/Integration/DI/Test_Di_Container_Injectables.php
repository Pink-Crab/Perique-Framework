<?php

declare(strict_types=1);
/**
 * Application test for injecting dependencies via helper interfaces
 *
 * @since 1.0.6
 * @author Glynn Quelch <glynn.quelch@gmail.com>
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 * @package PinkCrab\Perique
 */

namespace PinkCrab\Perique\Tests\Application\DI;

use WP_UnitTestCase;
use Gin0115\WPUnit_Helpers\Objects;
use PinkCrab\Perique\Tests\Application\App_Helper_Trait;
use PinkCrab\Perique\Tests\Fixtures\DI\Inject_App_Config_Mock;
use PinkCrab\Perique\Tests\Fixtures\DI\Inject_Hook_Loader_Mock;
use PinkCrab\Perique\Tests\Fixtures\DI\Inject_DI_Container_Mock;
use PinkCrab\Perique\Tests\Fixtures\DI\Inject_Loader_And_Config_And_Container_Mock;

/**
 * @group integration
 * @group di
 */
class Test_Di_Container_Injectables extends WP_UnitTestCase {

	/**
	 * @method self::unset_app_instance();
	 * @method self::pre_populated_app_provider();
	 */
	use App_Helper_Trait;

	public function tearDown(): void {
		self::unset_app_instance();
	}

	/** @testdox It should be possible to pass the DI Container to a dependency using an interface. */
	public function test_inject_di_container(): void {
		// Populate the APP
		$app = $this->pre_populated_app_provider()->boot();
		do_action( 'init' );

		$mock = $app::make( Inject_DI_Container_Mock::class );
		$this->assertTrue( $mock->has_container() );

		// Check the container is the same as the app.
		$this->assertSame( $app->__debugInfo()['container'], $mock->get_container() );
	}

	/** @testdox It should be possible to pass the Hook Loader to a dependency using an interface. */
	public function test_inject_hook_loader(): void {
		// Populate the APP
		$app = $this->pre_populated_app_provider()->boot();
		do_action( 'init' );

		$mock = $app::make( Inject_Hook_Loader_Mock::class );
		$this->assertTrue( $mock->has_loader() );

		$loader = Objects::get_property( $app, 'loader' );
		$this->assertSame( $loader, $mock->get_loader() );
	}

	/** @testdox It should be possible to pass theApp Config to a dependency using an interface. */
	public function test_inject_app_config(): void {
		// Populate the APP
		$app = $this->pre_populated_app_provider()->boot();
		do_action( 'init' );

		$mock = $app::make( Inject_App_Config_Mock::class );
		$this->assertTrue( $mock->has_app_config() );

		// Check the config is the same as the app.
		$this->assertSame( $app->__debugInfo()['app_config'], $mock->get_app_config() );
	}

	/** @testdox It should be possible to inject both Hook_Loader and DI_Container via the container */
	public function test_inject_hook_loader_and_di_container(): void {
		// Populate the APP
		$app = $this->pre_populated_app_provider()->boot();
		do_action( 'init' );

		$mock = $app::make( Inject_Loader_And_Config_And_Container_Mock::class );

		// Check hook loader was added
		$this->assertTrue( $mock->has_loader() );
		$loader = Objects::get_property( $app, 'loader' );
		$this->assertSame( $loader, $mock->get_loader() );

		// Check DI was added
		$this->assertTrue( $mock->has_container() );
		$this->assertSame( $app->__debugInfo()['container'], $mock->get_container() );

		// Check config was added
		$this->assertTrue( $mock->has_app_config() );
		$this->assertSame( $app->__debugInfo()['app_config'], $mock->get_app_config() );
	}
}
