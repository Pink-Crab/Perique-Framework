<?php

declare(strict_types=1);

/**
 * Integration tests for the App_Validation class.
 *
 * @since 0.4.0
 * @author Glynn Quelch <glynn.quelch@gmail.com>
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 * @package PinkCrab\Perique
 */

namespace PinkCrab\Perique\Tests\Integration\Application;

use WP_UnitTestCase;
use PinkCrab\Perique\Application\App;
use PinkCrab\Perique\Application\App_Validation;
use PinkCrab\Perique\Tests\Application\App_Helper_Trait;

/**
 * @group integration
 * @group app
 * @group app_validation
 */
class Test_App_Validation extends WP_UnitTestCase {

	/**
	 * @method self::unset_app_instance();
	 */
	use App_Helper_Trait;

	public function tearDown(): void {
		self::unset_app_instance();
	}

	/** @testdox An app which has Loader, Registration, DI Container and App_Config bound, should pass validation. */
	public function test_can_validate_with_all_services_bound(): void {
		$app       = $this->pre_populated_app_provider();
		$validator = new App_Validation( $app );
		$this->assertEmpty( $validator->errors );
	}

	/** @testdox The apps initialise process should not allow the app to be booted again. */
	public function test_already_booted_app_fails_validation(): void {
		$app       = $this->pre_populated_app_provider()->boot();
		$validator = new App_Validation( $app );
		$validator->validate();

		$this->assertNotEmpty( $validator->errors );
		$this->assertContains(
			App_Validation::ERROR_MESSAGE_APP_BOOTED,
			$validator->errors
		);
	}

}
