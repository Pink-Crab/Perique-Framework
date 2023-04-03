<?php

declare(strict_types=1);

/**
 * Unit tests for the App_Initialization_Exception.
 *
 * @since 2.0.0
 * @author Glynn Quelch <glynn.quelch@gmail.com>
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 * @package PinkCrab\Perique
 */

namespace PinkCrab\Perique\Tests\Unit\View;

use PinkCrab\Perique\Exceptions\App_Initialization_Exception;

/**
 * @group exception
 * @group unit
 */
class Test_App_Initialization_Exception extends \WP_UnitTestCase {

	/** @testdox It should be possible to create an exception with an error code of 1 for App not being populated with a Di Container */
	public function test_can_throw_app_not_populated_with_di_container(): void {
		$this->expectException( App_Initialization_Exception::class );
		$this->expectExceptionCode( 1 );
		$this->expectExceptionMessage( 'The Application must be populated with a DI_Container before booting.' );

		throw App_Initialization_Exception::requires_di_container();
	}

	/** @testdox It should be possible to create an exception with an error code of 2 for attempting to reset the DI Container */
	public function test_can_throw_attempting_to_reset_di_container(): void {
		$this->expectException( App_Initialization_Exception::class );
		$this->expectExceptionCode( 2 );
		$this->expectExceptionMessage( 'App already contains a DI Container, can not redeclare.' );

		throw App_Initialization_Exception::di_container_exists();
	}

	/** @testdox It should be possible to create an exception with an error code of 3 for not setting the module manager in the APP */
	public function test_can_throw_not_setting_module_manager(): void {
		$this->expectException( App_Initialization_Exception::class );
		$this->expectExceptionCode( 3 );
		$this->expectExceptionMessage( 'App has not defined Registration Service, this must be set before use.' );

		throw App_Initialization_Exception::requires_module_manager();
	}

	/** @testdox It should be possible to create an exception with an error code of 4 for trying to call a service, before App is booted. */
	public function test_can_throw_calling_service_before_boot(): void {
		$this->expectException( App_Initialization_Exception::class );
		$this->expectExceptionCode( 4 );
		$this->expectExceptionMessage( 'App must be initialised before calling My_Service' );

		throw App_Initialization_Exception::app_not_initialized('My_Service');
	}

	/** @testdox It should be possible to create an exception with an error code of 5 for trying set App_Config a second time */
	public function test_can_throw_setting_app_config_twice(): void {
		$this->expectException( App_Initialization_Exception::class );
		$this->expectExceptionCode( 5 );
		$this->expectExceptionMessage( 'Can not redeclare App_Config as its already set to the application' );

		throw App_Initialization_Exception::app_config_exists();
	}

	/** @testdox It should be possible to create an exception when App fails validation, with an error code of 6 and show the errors */
	public function test_can_throw_app_validation_fail(): void {
		$this->expectException( App_Initialization_Exception::class );
		$this->expectExceptionCode( 6 );
		$this->expectExceptionMessage( 'App failed boot validation : Missing App_Config, Missing Registration_Service, Missing DI_Container' );

		throw App_Initialization_Exception::failed_boot_validation( array(
			'Missing App_Config',
			'Missing Registration_Service',
			'Missing DI_Container',
		) );
	}

	/** @testdox It should be possible to create an exception with an error code of 7 for trying to set the Registration_Service again */
	public function test_can_throw_setting_registration_service_twice(): void {
		$this->expectException( App_Initialization_Exception::class );
		$this->expectExceptionCode( 7 );
		$this->expectExceptionMessage( 'Can not redeclare Registration_Service as its already set to the application' );

		throw App_Initialization_Exception::registration_exists();
	}

	/** @testdox It should be possible to create an exception with an error code of 8 for attempting to redeclare the Hook_Loader */
	public function test_can_throw_attempting_to_redeclare_hook_loader(): void {
		$this->expectException( App_Initialization_Exception::class );
		$this->expectExceptionCode( 8 );
		$this->expectExceptionMessage( 'Can not redeclare Loader as its already set to the application' );

		throw App_Initialization_Exception::loader_exists();
	}


	/** @testdox It should be possible to create an exception for attempting to redeclare the module manager. */
	public function test_can_throw_attempting_to_redeclare_module_manager(): void {
		$this->expectException( App_Initialization_Exception::class );
		$this->expectExceptionCode( 10 );
		$this->expectExceptionMessage( 'Can not redeclare Module_Manager as its already set to the application' );

		throw App_Initialization_Exception::module_manager_exists();
	}

}
