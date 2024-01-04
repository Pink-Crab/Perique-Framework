<?php

declare(strict_types=1);
/**
 * Using the View Service
 *
 * @since 2.0.0
 * @author Glynn Quelch <glynn.quelch@gmail.com>
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 * @package PinkCrab\Perique
 */

namespace PinkCrab\Perique\Tests\Registration;

use WP_UnitTestCase;
use PinkCrab\Loader\Hook_Loader;
use PinkCrab\Perique\Application\Hooks;
use PinkCrab\Perique\Application\App_Factory;
use PinkCrab\Perique\Interfaces\DI_Container;
use PinkCrab\Perique\Services\Dice\PinkCrab_Dice;
use PinkCrab\Perique\Tests\Application\App_Helper_Trait;
use PinkCrab\Perique\Exceptions\Module_Manager_Exception;
use PinkCrab\Perique\Services\Registration\Module_Manager;
use PinkCrab\Perique\Tests\Fixtures\Mock_Objects\Sample_Class;
use PinkCrab\Perique\Services\Registration\Registration_Service;
use PinkCrab\Perique\Tests\Fixtures\Mock_Objects\View_Components\Span;
use PinkCrab\Perique\Tests\Fixtures\Mock_Objects\View_Components\P_Tag_Component;
use PinkCrab\Perique\Tests\Fixtures\Modules\Invalid\With_Invalid_Class_Middleware;
use PinkCrab\Perique\Tests\Fixtures\Modules\With_Middleware\Module_With_Middleware__Module;
use PinkCrab\Perique\Tests\Fixtures\Modules\With_Middleware\Module_With_Middleware__Middleware;
use PinkCrab\Perique\Tests\Fixtures\Modules\Without_Middleware\Module_Without_Middleware__Module;

/**
 * @group integration
 * @group view
 * @group components
 */
class Test_Use_View extends WP_UnitTestCase {


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
	}

	/**
 * @testdox By default the default component path should be {view_path}/component
*/
	public function test_render_component_with_base_path() {
		$app = ( new App_Factory( \FIXTURES_PATH ) )
			->default_setup()
			->boot();

		$output = $app::view()->render(
			'render-component',
			array( 'component' => new P_Tag_Component( 'test', new Span( 'span-class', 'span-content' ) ) ),
			false
		);
		$this->assertEquals( '<p class="test"><span class="span-class">span-content</span></p>', $output );
	}

	/**
	 * @testdox It should be possible to use a component alias and have it use the full path.
	 * @see  https://github.com/Pink-Crab/Perique-Framework/issues/182
	 */
	public function test_render_component_with_alias() {
		add_filter(
			Hooks::COMPONENT_ALIASES,
			function ( array $aliases ): array {
				$aliases[ P_Tag_Component::class ] = \FIXTURES_PATH . '/views/components/alias-dir/some-view.php';
				return $aliases;
			}
		);

		$app = ( new App_Factory( \FIXTURES_PATH ) )
			->default_setup()
			->boot();

		$output = $app::view()->render(
			'render-component',
			array( 'component' => new P_Tag_Component( 'test', new Span( 'span-class', 'span-content' ) ) ),
			false
		);

		// Remove the filter.
		add_filter( Hooks::COMPONENT_ALIASES, fn ( array $aliases ): array  => array() );

		$this->assertStringContainsString( '<p class="test">ALIAS <span class="span-class">span-content</span></p>', $output );
	}
}
