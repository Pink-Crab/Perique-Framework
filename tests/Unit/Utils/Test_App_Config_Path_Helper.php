<?php

declare(strict_types=1);

/**
 * Tests the default PHP Engine for the view/renderable interface.
 *
 * @since 1.2.0
 * @author Glynn Quelch <glynn.quelch@gmail.com>
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 * @package PinkCrab\Perique
 */

namespace PinkCrab\Perique\Tests\Unit\Utils;

use PinkCrab\Perique\Application\Hooks;
use PinkCrab\Perique\Services\View\View;
use PinkCrab\Perique\Services\View\PHP_Engine;
use PinkCrab\Perique\Utils\App_Config_Path_Helper;
use PinkCrab\Perique\Services\View\Component\Component_Compiler;
use PinkCrab\Perique\Tests\Fixtures\Mock_Objects\View_Components\P;
use PinkCrab\Perique\Tests\Fixtures\Mock_Objects\View_Components\Span;
use PinkCrab\Perique\Tests\Fixtures\Mock_Objects\View_Components\Input;
use PinkCrab\Perique\Tests\Fixtures\Mock_Objects\View_Components\Dot_Notation;
use PinkCrab\Perique\Tests\Fixtures\Mock_Objects\View_Components\P_Tag_Component;
use PinkCrab\Perique\Tests\Fixtures\Mock_Objects\View_Components\Input_Attribute_Path;
use PinkCrab\Perique\Tests\Fixtures\Mock_Objects\View_Components\Input_Template_Method;

/**
 * @group utils
 * @group app_config
 * @group unit
 */
class Test_App_Config_Path_Helper extends \WP_UnitTestCase {

	/** @testdox It should be possible to normalise a path, by stripping any trailing slash, regardless of front ir back */
    public function test_can_normalise_path(): void {
        $this->assertEquals( 'some/path', App_Config_Path_Helper::normalise_path(  'some/path' ) );
        $this->assertEquals( 'some/path', App_Config_Path_Helper::normalise_path(  'some/path/' ) );
        $this->assertEquals( 'some/path', App_Config_Path_Helper::normalise_path(  'some/path\\' ) );
    }
    
    /** @testdox It should be possible to assume the view path, based on the sites base path regardless of the base path path having a trailing slash or not, and regardless of forward or back (based on OS) */
	public function test_can_assume_view_path(): void {
		        
        $this->assertEquals( 'some/path' . '/views', App_Config_Path_Helper::assume_view_path(  'some/path' ) );

        // With a trailing slash
        $this->assertEquals( 'some/path' . '/views', App_Config_Path_Helper::assume_view_path(  'some/path/' ) );

        // With a trailing slash and a backslash
        $this->assertEquals( 'some/path' . '/views', App_Config_Path_Helper::assume_view_path(  'some/path\\' ) );
	}

    /** @testdox It should be possible to assume the base URL from the base path, regardless of the path having a trailing slash or not, and regardless of forward or back (based on OS) */
    public function test_can_get_base_url_from_base_path(): void {
        $this->assertEquals( 'http://example.org/wp-content/plugins/Fixtures', App_Config_Path_Helper::assume_base_url(  \FIXTURES_PATH ) );

        // With a trailing slash
        $this->assertEquals( 'http://example.org/wp-content/plugins/Fixtures', App_Config_Path_Helper::assume_base_url(  \FIXTURES_PATH . '/' ) );

        // With a trailing slash and a backslash
        $this->assertEquals( 'http://example.org/wp-content/plugins/Fixtures', App_Config_Path_Helper::assume_base_url(  \FIXTURES_PATH . '\\' ) );
    }

    /** @testdox It should be possible to assume the view url from the base path, regardless of the path having a trailing slash or not, and regardless of forward or back (based on OS) */
    public function test_can_get_view_url_from_base_path(): void {
        $base_path = \FIXTURES_PATH;
        $views_path = \FIXTURES_PATH . '/views';

        // All combinations of trailing slash and backslash       
        $this->assertEquals( 'http://example.org/wp-content/plugins/Fixtures/views', App_Config_Path_Helper::assume_view_url($base_path, $views_path ) );
        $this->assertEquals( 'http://example.org/wp-content/plugins/Fixtures/views', App_Config_Path_Helper::assume_view_url($base_path, $views_path . '/' ) );
        $this->assertEquals( 'http://example.org/wp-content/plugins/Fixtures/views', App_Config_Path_Helper::assume_view_url($base_path, $views_path . '\\' ) );

        $this->assertEquals( 'http://example.org/wp-content/plugins/Fixtures/views', App_Config_Path_Helper::assume_view_url($base_path . '/', $views_path  ) );
        $this->assertEquals( 'http://example.org/wp-content/plugins/Fixtures/views', App_Config_Path_Helper::assume_view_url($base_path . '/', $views_path . '/' ) );
        $this->assertEquals( 'http://example.org/wp-content/plugins/Fixtures/views', App_Config_Path_Helper::assume_view_url($base_path . '/', $views_path . '\\' ) );
        
        $this->assertEquals( 'http://example.org/wp-content/plugins/Fixtures/views', App_Config_Path_Helper::assume_view_url($base_path . '\\', $views_path  ) );
        $this->assertEquals( 'http://example.org/wp-content/plugins/Fixtures/views', App_Config_Path_Helper::assume_view_url($base_path . '\\', $views_path . '/' ) );
        $this->assertEquals( 'http://example.org/wp-content/plugins/Fixtures/views', App_Config_Path_Helper::assume_view_url($base_path . '\\', $views_path . '\\') );
    }
}
