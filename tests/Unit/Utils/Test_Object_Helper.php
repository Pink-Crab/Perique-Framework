<?php

declare(strict_types=1);

/**
 * Unit tests for the Object_Helper class.
 *
 * @since 2.0.0
 * @author Glynn Quelch <glynn.quelch@gmail.com>
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 * @package PinkCrab\Perique
 */

namespace PinkCrab\Perique\Tests\Unit\Utils;

use PinkCrab\Perique\Utils\Object_Helper;
use PinkCrab\Perique\Utils\App_Config_Path_Helper;
use PinkCrab\Perique\Tests\Fixtures\Mock_Objects\Sample_Class;
use PinkCrab\Perique\Tests\Fixtures\Mock_Objects\Interface_Get;
use PinkCrab\Perique\Tests\Fixtures\Mock_Objects\Parent_Dependency;

/**
 * @group utils
 * @group objects
 * @group unit
 */
class Test_Object_Helper extends \WP_UnitTestCase {

	/**
	 * Data for is_a test.
	 *
	 * @return array<array{0:mixed, 1:boolean}>
	 */
	public function is_a_data(): array {
		return array(
			'✔ VALID :: Class Instance' => array( new Parent_Dependency( new Sample_Class() ), true ),
			'✘ INVALID :: Class Instance' =>  array( new \stdClass(), false ),
			'✘ INVALID :: String' => array( 'string', false ),
			'✘ INVALID :: Int' => array( 1, false ),
			'✘ INVALID :: Float' => array( 1.1, false ),
			'✘ INVALID :: Bool' => array( true, false ),
			'✘ INVALID :: Array' => array( array( 1, 2, 3 ), false ),
			'✘ INVALID :: Null' => array( null, false ),
		);
	}

    /**
     * @testdox It should be possible to check if an object is an instance of a class or interface.
     *
     * @dataProvider is_a_data
     * @param mixed $object
     * @param bool $expected
     * @return void
     */
    public function test_is_a( $object, bool $expected ): void {
        $this->assertEquals( $expected, Object_Helper::is_a( $object, Interface_Get::class ) );
    }
}
