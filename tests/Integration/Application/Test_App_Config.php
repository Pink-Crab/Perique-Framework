<?php

declare(strict_types=1);
/**
 * Base config object.
 *
 * @since 0.2.0
 * @author Glynn Quelch <glynn.quelch@gmail.com>
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 * @package PinkCrab\Perique
 */

namespace PinkCrab\Perique\Tests\Integration\Application;

use WP_UnitTestCase;
use PinkCrab\Perique\Application\Config;
use PinkCrab\Perique\Application\App_Config;
use \PinkCrab\Perique\Tests\Application\App_Helper_Trait;

/**
 * @group integration
 * @group app
 * @group app_config
 */
class Test_App_Config extends WP_UnitTestCase {

	/**
	 * @method self::unset_app_instance();
	 */
	use App_Helper_Trait;

	/**
	 * @return void
	 */
	public function setUp(): void {
		parent::setUp();
		$this->pre_populated_app_provider()->boot();
	}

	public function tearDown(): void {
		self::unset_app_instance();
	}

	/**
	 * Test that the config can be accessed
	 *
	 * This doesnt check the value, only that values are returned.
	 * The values are set in App setup, so cant be checked from here.
	 *
	 * @return void
	 */
	public function test_can_call_with_config_proxy(): void {
		$app_config = new App_Config( include FIXTURES_PATH . '/Application/settings.php' );

		// Paths
		$this->assertEquals( $app_config->path(), Config::path() );
		$this->assertEquals( $app_config->path( 'plugin' ), Config::path( 'plugin' ) );
		$this->assertEquals( $app_config->path( 'view' ), Config::path( 'view' ) );
		$this->assertEquals( $app_config->path( 'assets' ), Config::path( 'assets' ) );
		$this->assertEquals( $app_config->path( 'upload_root' ), Config::path( 'upload_root' ) );
		$this->assertEquals( $app_config->path( 'upload_current' ), Config::path( 'upload_current' ) );

		// Urls
		$this->assertEquals( $app_config->url(), Config::url() );
		$this->assertEquals( $app_config->url( 'plugin' ), Config::url( 'plugin' ) );
		$this->assertEquals( $app_config->url( 'view' ), Config::url( 'view' ) );
		$this->assertEquals( $app_config->url( 'assets' ), Config::url( 'assets' ) );
		$this->assertEquals( $app_config->url( 'upload_root' ), Config::url( 'upload_root' ) );
		$this->assertEquals( $app_config->url( 'upload_current' ), Config::url( 'upload_current' ) );

		// Namespaces
		$this->assertEquals( $app_config->namespace( 'rest' ), Config::namespace( 'rest' ) );
		$this->assertEquals( $app_config->namespace( 'cache' ), Config::namespace( 'cache' ) );
		$this->assertEquals( $app_config->rest(), Config::rest() );
		$this->assertEquals( $app_config->cache(), Config::cache() );

		// Version and additional settings
		$this->assertEquals( $app_config->version(), Config::version() );
		$this->assertEquals( $app_config->additional( 'test_key' ), Config::additional( 'test_key' ) );

		// Post type and post meta
		$this->assertEquals( $app_config->post_types( 'cpt' ), Config::post_types( 'cpt' ) );
		$this->assertEquals( $app_config->post_meta( 'post_meta_1' ), Config::post_meta( 'post_meta_1' ) );

		// Taxonomies and Term Meta
		$this->assertEquals( $app_config->taxonomies( 'tax' ), Config::taxonomies( 'tax' ) );
		$this->assertEquals( $app_config->term_meta( 'term_meta_1' ), Config::term_meta( 'term_meta_1' ) );

		// User Meta
		$this->assertEquals( $app_config->user_meta( 'user_meta_1' ), Config::user_meta( 'user_meta_1' ) );

		// DB Table
		$this->assertEquals( $app_config->db_tables( 'table' ), Config::db_tables( 'table' ) );
	}

}
