<?php

declare(strict_types=1);
/**
 * Functional tests using the App
 *
 * @since 0.4.0
 * @author Glynn Quelch <glynn.quelch@gmail.com>
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 * @package PinkCrab\Core
 */

namespace PinkCrab\Core\Tests\Application;

use Dice\Dice;
use Exception;
use WP_UnitTestCase;
use PinkCrab\Loader\Loader;
use PinkCrab\Core\Application\App;
use Gin0115\WPUnit_Helpers\Objects;
use PinkCrab\Core\Application\Hooks;
use PinkCrab\Core\Application\App_Config;
use PinkCrab\Core\Interfaces\DI_Container;
use PinkCrab\Core\Tests\Application\App_Helper_Trait;
use PinkCrab\Core\Services\Dice\PinkCrab_WP_Dice_Adaptor;
use PinkCrab\Core\Exceptions\App_Initialization_Exception;
use PinkCrab\Core\Tests\Fixtures\Mock_Objects\Sample_Class;
use PinkCrab\Core\Services\Registration\Registration_Service;
use PinkCrab\Core\Tests\Fixtures\Mock_Objects\Parent_Dependency;
use PinkCrab\Core\Services\Registration\Middleware\Registration_Middleware;

class Test_App_Functional extends WP_UnitTestCase {

	/**
	 * @method self::unset_app_instance();
	 */
	use App_Helper_Trait;

	public function tearDown(): void {
		self::unset_app_instance();
	}

    /** @testdox When running the applications setup, hooks should be triggered to allow external codeabases to interact and piggyback into the app initalisation process. */
    public function test_all_hooks_fire_on_finalise_during_boot(): void
    {
        
        // Pre boot hook.
        $this->expectOutputRegex('/Pre Boot Hook/');
        \add_action(Hooks::APP_INIT_PRE_BOOT, function(App_Config $config, Loader $loader, DI_Container $container){
            echo 'Pre Boot Hook';
        }, 10, 3);

        // Pre registration hook.
        $this->expectOutputRegex('/Pre Registration Hook/');
        \add_action(Hooks::APP_INIT_PRE_REGISTRATION, function(App_Config $config, Loader $loader, DI_Container $container){
            echo 'Pre Registration Hook';
        }, 10, 3);

        // Post registration.
        $this->expectOutputRegex('/Post Registration Hook/');
        \add_action(Hooks::APP_INIT_POST_REGISTRATION, function(App_Config $config, Loader $loader, DI_Container $container){
            echo 'Post Registration Hook';
        }, 10, 3);
        
        // Boot app.
        $app = $this->pre_populated_app_provider()->boot();

        // Run init
        do_action('init');

        // Cleanup
        remove_all_actions(Hooks::APP_INIT_PRE_BOOT);
        remove_all_actions(Hooks::APP_INIT_PRE_REGISTRATION);
        remove_all_actions(Hooks::APP_INIT_POST_REGISTRATION);
    }

    /** @testdox Once the App is booted, it should be possible to create an instance of an object, using the DI Container without acess to an actual instnace of the App. Via a static method */
    public function test_can_use_static_make_method_to_use_di_container(): void
    {
        $app = $this->pre_populated_app_provider()->boot();

        // Fxiture class, has Sample_Class injected as a dependency.
        $parent = App::make(Parent_Dependency::class);       
        
        $this->assertInstanceOf(Parent_Dependency::class, $parent);
        $this->assertInstanceOf(Sample_Class::class, $parent->get_sample_class());
    }

    public function test_cant_use_make_before_app_booted(): void
    {
        # code...
    }

}