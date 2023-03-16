<?php

declare(strict_types=1);
/**
 * Using Modules and Registration Middleware
 *
 * @since 2.0.0
 * @author Glynn Quelch <glynn.quelch@gmail.com>
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 * @package PinkCrab\Perique
 */

namespace PinkCrab\Perique\Tests\Registration;

use WP_UnitTestCase;
use PinkCrab\Perique\Tests\Application\App_Helper_Trait;
use PinkCrab\Perique\Tests\Fixtures\Mock_Objects\Sample_Class;
use PinkCrab\Perique\Tests\Fixtures\Modules\With_Middleware\Module_With_Middleware__Module;
use PinkCrab\Perique\Tests\Fixtures\Modules\With_Middleware\Module_With_Middleware__Middleware;
use PinkCrab\Perique\Tests\Fixtures\Modules\Without_Middleware\Module_Without_Middleware__Module;

/**
 * @group integration
 * @group registration
 * @group modules
 *
 */
class Test_Use_Modules extends WP_UnitTestCase {


	/**
	 * @method self::unset_app_instance();
	 */
	use App_Helper_Trait;

	public function tear_down(): void {
		parent::tear_down();
		self::unset_app_instance();

	}

	public function set_up() {
		parent::set_up();
		self::unset_app_instance();

		// Clear the module and middleware logs.
		Module_With_Middleware__Module::$log     = array();
		Module_With_Middleware__Middleware::$log = array();
		Module_Without_Middleware__Module::$log  = array();
	}

	/** @testdox It should be possible to add modules to the app and have the event hooks fired during the boot of the application (module with middleware)*/
	public function test_modules_run_events_in_order(): void {
		$app = $this->pre_populated_app_provider()
			->module(
				Module_With_Middleware__Module::class,
				function( $mod, $middleware ) {
					// Populated instance of the module and middleware should be passed.
					$this->assertInstanceOf( Module_With_Middleware__Module::class, $mod );
					$this->assertInstanceOf( Module_With_Middleware__Middleware::class, $middleware );

					// No events should have fired yet.
					$this->assertEmpty( $mod::$log );
					return $mod;
				}
			)
			->registration_classes( array( Sample_Class::class ) );

		// No events should have fired yet.
		$this->assertEmpty( Module_With_Middleware__Module::$log );

		// Boot the app.
		$app->boot();

		// "pre_boot" event should now have fired.
		$this->assertArrayHasKey( 'pre_boot', Module_With_Middleware__Module::$log );
		$this->assertCount( 1, Module_With_Middleware__Module::$log );

		// Trigger init to load the app.
		do_action( 'init' );

		// "pre_register" and "post_register" events should now have fired.
		$this->assertArrayHasKey( 'pre_register', Module_With_Middleware__Module::$log );
		$this->assertArrayHasKey( 'post_register', Module_With_Middleware__Module::$log );
		$this->assertCount( 3, Module_With_Middleware__Module::$log );

		// Check the registration classes have been registered.
		$event_order = array_keys( Module_With_Middleware__Middleware::$log );
		$this->assertEquals( 'setup', $event_order[0] );
		$this->assertEquals( 'process', $event_order[1] );
		$this->assertEquals( 'tear_down', $event_order[2] );

		$this->assertEquals( 'setup called', Module_With_Middleware__Middleware::$log['setup'][0] );
		$this->assertEquals(
			'process called with ' . Sample_Class::class . ' passed to process.',
			Module_With_Middleware__Middleware::$log['process'][0]
		);
		$this->assertEquals( 'tear_down called', Module_With_Middleware__Middleware::$log['tear_down'][0] );
	}

	/** @testdox It should be possible to add modules to the app and have the event hooks fired during the boot of the application (module without middleware) */
	public function test_modules_run_events_in_order_without_middleware(): void {
		$app = $this->pre_populated_app_provider()
			->module(
				Module_Without_Middleware__Module::class,
				function( $mod, $middleware ) {
					// Populated instance of the module should be passed.
					$this->assertInstanceOf( Module_Without_Middleware__Module::class, $mod );
					$this->assertNull( $middleware );
					// No events should have fired yet.
					$this->assertEmpty( $mod::$log );
					return $mod;
				}
			);

		// No events should have fired yet.
		$this->assertEmpty( Module_Without_Middleware__Module::$log );

		// Boot the app.
		$app->boot();

		// "pre_boot" event should now have fired.
		$this->assertArrayHasKey( 'pre_boot', Module_Without_Middleware__Module::$log );
		$this->assertCount( 1, Module_Without_Middleware__Module::$log );

		// Trigger init to load the app.
		do_action( 'init' );

		// "pre_register" and "post_register" events should now have fired.
		$this->assertArrayHasKey( 'pre_register', Module_Without_Middleware__Module::$log );
		$this->assertArrayHasKey( 'post_register', Module_Without_Middleware__Module::$log );
		$this->assertCount( 3, Module_Without_Middleware__Module::$log );
	}


}
