<?php

declare(strict_types=1);

/**
 * Main App Container Test.
 *
 * @since 0.4.0
 * @author Glynn Quelch <glynn.quelch@gmail.com>
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 * @package PinkCrab\Perique
 */

namespace PinkCrab\Perique\Tests\Application;

use WP_UnitTestCase;
use PinkCrab\Perique\Application\App;
use PinkCrab\Perique\Application\App_Validation;
use PinkCrab\Perique\Tests\Application\App_Helper_Trait;

class Test_App_Validation extends WP_UnitTestCase {

	/**
	 * @method self::unset_app_instance();
	 */
	use App_Helper_Trait;

	public function tearDown(): void {
		self::unset_app_instance();
	}

    /** @testdox Binding the DI Container to the App is required to setup */
    public function test_validation_failed_with_no_container() : void
    {
        $app = new App();
        $validator = new App_Validation($app);
        $validator->validate();
        $this->assertNotEmpty($validator->errors);
        $this->assertContains(
            sprintf(App_Validation::ERROR_MESSAGE_TEMPLATE, 'container'),
            $validator->errors
        );
    }

    /** @testdox Binding the Hook Loader to the App is required to setup */
    public function test_validation_failed_with_no_loader() : void
    {
        $app = new App();
        $validator = new App_Validation($app);
        $validator->validate();
        $this->assertNotEmpty($validator->errors);
        $this->assertContains(
            sprintf(App_Validation::ERROR_MESSAGE_TEMPLATE, 'loader'),
            $validator->errors
        );
    }

     /** @testdox Binding the App_Config to the App is required to setup */
     public function test_validation_failed_with_no_app_config() : void
     {
         $app = new App();
         $validator = new App_Validation($app);
         $validator->validate();
         $this->assertNotEmpty($validator->errors);
         $this->assertContains(
             sprintf(App_Validation::ERROR_MESSAGE_TEMPLATE, 'app_config'),
             $validator->errors
         );
     }

      /** @testdox Binding the Registration_Service to the App is required to setup */
      public function test_validation_failed_with_no_registration() : void
      {
          $app = new App();
          $validator = new App_Validation($app);
          $validator->validate();
          $this->assertNotEmpty($validator->errors);
          $this->assertContains(
              sprintf(App_Validation::ERROR_MESSAGE_TEMPLATE, 'registration'),
              $validator->errors
          );
      }

      /** @testdox An app which has Loader, Registration, DI Container and App_Config bound, should pass validation. */
      public function test_can_validate_with_all_services_bound(): void
      {
        $app = $this->pre_populated_app_provider();
          $validator = new App_Validation($app);
          $this->assertEmpty($validator->errors);
      }

      /** @testdox The apps intialise process should not allow the app to be booted again. */
      public function test_alread_booted_app_fails_validataion(): void
      {
        $app = $this->pre_populated_app_provider()->boot();
        $validator = new App_Validation($app);
        $validator->validate();

        $this->assertNotEmpty($validator->errors);
        $this->assertContains(
            sprintf(App_Validation::ERROR_MESSAGE_APP_BOOTED, 'registration'),
            $validator->errors
        );
      }
 
}