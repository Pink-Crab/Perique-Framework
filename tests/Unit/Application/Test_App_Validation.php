<?php

declare(strict_types=1);

/**
 * Unit tests for the App_Validation class.
 *
 * @since 0.4.0
 * @author Glynn Quelch <glynn.quelch@gmail.com>
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 * @package PinkCrab\Perique
 */

namespace PinkCrab\Perique\Tests\Unit\Application;

use WP_UnitTestCase;
use PinkCrab\Perique\Application\App;
use PinkCrab\Perique\Application\App_Validation;
use PinkCrab\Perique\Tests\Application\App_Helper_Trait;

/**
 * @group unit
 * @group app
 * @group app_validation
 */
class Test_App_Validation extends WP_UnitTestCase {

	use App_Helper_Trait;

	public function tear_down(): void {
		parent::tear_down();
		self::unset_app_instance();
	}


	/** @testdox Binding the DI Container to the App is required to setup */
	public function test_validation_failed_with_no_container() : void {
		// Clear any existing app instances as this randomly fails.
		self::unset_app_instance();

		$app       = new App( FIXTURES_PATH );
		$validator = new App_Validation( $app );
		$validator->validate();
		$this->assertNotEmpty( $validator->errors );
		$this->assertContains(
			sprintf( App_Validation::ERROR_MESSAGE_TEMPLATE, 'container' ),
			$validator->errors
		);
	}

	/** @testdox Binding the Hook Loader to the App is required to setup */
	public function test_validation_failed_with_no_loader() : void {
		// Clear any existing app instances as this randomly fails.
		self::unset_app_instance();

		$app       = new App( FIXTURES_PATH );
		$validator = new App_Validation( $app );
		$validator->validate();
		$this->assertNotEmpty( $validator->errors );
		$this->assertContains(
			sprintf( App_Validation::ERROR_MESSAGE_TEMPLATE, 'loader' ),
			$validator->errors
		);
	}

	 /** @testdox Binding the App_Config to the App is required to setup */
	public function test_validation_failed_with_no_app_config() : void {
		// Clear any existing app instances as this randomly fails.
		self::unset_app_instance();

		$app       = new App( FIXTURES_PATH );
		$validator = new App_Validation( $app );
		$validator->validate();
		$this->assertNotEmpty( $validator->errors );
		$this->assertContains(
			sprintf( App_Validation::ERROR_MESSAGE_TEMPLATE, 'app_config' ),
			$validator->errors
		);
	}

	  /** @testdox Binding the Module_Manager to the App is required to setup */
	public function test_validation_failed_with_no_module_manager() : void {
		// Clear any existing app instances as this randomly fails.
		self::unset_app_instance();

		$app       = new App( FIXTURES_PATH );
		$validator = new App_Validation( $app );
		$validator->validate();
		$this->assertNotEmpty( $validator->errors );
		$this->assertContains(
			sprintf( App_Validation::ERROR_MESSAGE_TEMPLATE, 'module_manager' ),
			$validator->errors
		);
	}

}
