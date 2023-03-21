<?php

declare(strict_types=1);

/**
 * Unit Tests for the module manager.
 *
 * @since 2.0.0
 * @author Glynn Quelch <glynn.quelch@gmail.com>
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 * @package PinkCrab\Perique
 */

namespace PinkCrab\Perique\Tests\Unit\Modules;

use Dice\Dice;
use WP_UnitTestCase;
use PinkCrab\Loader\Hook_Loader;
use PinkCrab\Perique\Services\View\PHP_Engine;
use PinkCrab\Perique\Services\Dice\PinkCrab_Dice;
use PinkCrab\Perique\Exceptions\Module_Manager_Exception;
use PinkCrab\Perique\Services\Registration\Module_Manager;
use PinkCrab\Perique\Services\Registration\Registration_Service;
use PinkCrab\Perique\Tests\Fixtures\Modules\Invalid\With_None_Class_Middleware;
use PinkCrab\Perique\Tests\Fixtures\Modules\Invalid\With_Invalid_Class_Middleware;
use PinkCrab\Perique\Tests\Fixtures\Modules\With_Middleware\Module_With_Middleware__Module;
use PinkCrab\Perique\Tests\Fixtures\Modules\With_Middleware\Module_With_Middleware__Middleware;
use PinkCrab\Perique\Tests\Fixtures\Modules\Without_Middleware\Module_Without_Middleware__Module;

/**
 * @group unit
 * @group registration
 * @group module
 */
class Test_Module_Manager extends WP_UnitTestCase {

	/** @testdox When adding a module (with Middleware) to the module manager, the Module and Middleware should be passed, to the config class, constructed. */
	public function test_can_add_module_with_middleware_to_manager(): void {
		$loader    = new Hook_Loader();
		$container = new PinkCrab_Dice( new Dice() );

		$module_manager = new Module_Manager(
			$container,
			$this->createMock( Registration_Service::class )
		);

		$module_instance     = null;
		$middleware_instance = null;

		$module_manager->push_module(
			Module_With_Middleware__Module::class,
			function( $module, $middleware ) use ( &$module_instance, &$middleware_instance ) {
				$module_instance     = $module;
				$middleware_instance = $middleware;
				return $module;
			}
		);
		$module_manager->register_modules();

		$this->assertInstanceOf( Module_With_Middleware__Module::class, $module_instance );
		$this->assertInstanceOf( Module_With_Middleware__Middleware::class, $middleware_instance );
	}

	/** @testdox When adding a module (without Middleware) to the module manager, the Module and Middleware should be passed, to the config class, constructed. */
	public function test_can_add_module_without_middleware_to_manager(): void {
		$loader    = new Hook_Loader();
		$container = new PinkCrab_Dice( new Dice() );

		$module_manager = new Module_Manager(
			$container,
			$this->createMock( Registration_Service::class )
		);

		$module_instance     = null;
		$middleware_instance = null;

		$module_manager->push_module(
			Module_Without_Middleware__Module::class,
			function( $module, $middleware ) use ( &$module_instance, &$middleware_instance ) {
				$module_instance     = $module;
				$middleware_instance = $middleware;
				return $module;
			}
		);
		$module_manager->register_modules();

		$this->assertNull( $middleware_instance );
		$this->assertInstanceOf( Module_Without_Middleware__Module::class, $module_instance );
	}

	/** @testdox It should be possible to register a class which will be passed to the registration service. */
	public function test_can_register_class(): void {
		$classes_passed = [];
        
        $loader    = new Hook_Loader();
		$container = new PinkCrab_Dice( new Dice() );
        $registration_service = $this->createMock( Registration_Service::class );
            
        $registration_service->method( 'push_class' )->will(
                $this->returnCallback(
                    function( $e ) use ( &$classes_passed, $registration_service ) {
                        $classes_passed[] = $e;
                        return $registration_service;
                    }
                )
            );


		$module_manager = new Module_Manager( 
            $container, 
			$registration_service );

		$module_manager->register_class( 'test' );
        $module_manager->register_class( 'test2' );

		$module_manager->process_middleware();

        $this->assertCount( 2, $classes_passed );
        $this->assertContains( 'test', $classes_passed );
        $this->assertContains( 'test2', $classes_passed );
	}

    /** @testdox It should be possible to trigger the processing of the Registration_Service from the Module_Manager */
    public function test_can_trigger_registration_service(): void {
        $loader    = new Hook_Loader();
        $container = new PinkCrab_Dice( new Dice() );

        $registration_service = $this->createMock( Registration_Service::class );
        $registration_service->expects( $this->once() )->method( 'process' );

        $module_manager = new Module_Manager( 
            $container, 
            $registration_service );

        $module_manager->register_class( 'test' );

        $module_manager->process_middleware();
    }

}
