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

namespace PinkCrab\Perique\Tests\Application;

use WP_UnitTestCase;
use OutOfBoundsException;
use PinkCrab\Perique\Application\App;
use PinkCrab\Perique\Application\Config;
use PinkCrab\Perique\Application\App_Config;

class Test_App_Config extends WP_UnitTestCase {

	/**
	 * @method self::unset_app_instance();
	 */
	use App_Helper_Trait;

	public function tearDown(): void {
		self::unset_app_instance();
	}

	public function setup() {
		$this->pre_populated_app_provider()->boot();
	}

	/**
	 * Sample set of settings paths, would be passed on start up.
	 */
	public const SAMPLE_SETTINGS = array(
		'additional' => array(
			'array'  => array( 1, 2, 3, 4 ),
			'string' => 'HI',
		),
		'namespaces' => array( 'rest' => 'fake_rest' ),
		'post_types' => array( 'my_cpt' => 'my_slug' ),
		'taxonomies' => array( 'tax' => 'my_slug' ),
		'db_tables'  => array( 'db' => 'db_table' ),
		'meta'       => array(
			'post' => array( 'post_meta_1' => 'One Post' ),
			'user' => array( 'user_meta_1' => 'One User' ),
			'term' => array( 'term_meta_1' => 'One Term' ),
		),
	);

	/**
	 * Test that an instance of App Config can be created.
	 *
	 * @return void
	 */
	public function test_can_create(): void {
		// With additional settings
		$this->assertInstanceOf( App_Config::class, new App_Config( self::SAMPLE_SETTINGS ) );
		// Without additional settings
		$this->assertInstanceOf( App_Config::class, new App_Config );
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
		$this->assertTrue( is_string( Config::namespace( 'rest' ) ) );
		$this->assertTrue( is_array( Config::path() ) );
		$this->assertTrue( is_array( Config::url() ) );
	}

	/**
	 * Tests that the additional keys can be called out.
	 *
	 * @return void
	 */
	public function test_can_add_additional_keys() {
		$app_config = new App_Config( self::SAMPLE_SETTINGS );

		$this->assertEquals( 'HI', $app_config->string );
		$this->assertEquals( 'HI', $app_config->additional( 'string' ) );

		$this->assertTrue( is_array( $app_config->array ) );
		$this->assertEquals( 1, $app_config->array[0] );
		$this->assertEquals( 3, $app_config->additional( 'array' )[2] );
	}

	/**
	 * Test that defaults defined can be overwritten by the values in the
	 * settings.php file.
	 *
	 * @return void
	 */
	public function test_default_values_can_be_overwritten_via_settings() {
		$no_override   = App::make( App_Config::class );
		$with_override = new App_Config( self::SAMPLE_SETTINGS );

		$this->assertNotEquals(
			$no_override->namespace( 'rest' ),
			$with_override->namespace( 'rest' )
		);
	}

	/**
	 * Ensure the path is returned with a trailing slash.
	 *
	 * @return void
	 */
	public function test_path_returns_path_with_trailing_slash(): void {
		$app_config = new App_Config( self::SAMPLE_SETTINGS );
		$this->assertStringContainsString( '/views/', $app_config->path( 'view' ) );
		$this->assertStringEndsWith( '/', $app_config->path( 'view' ) );
	}

	/**
	 * Test that an invalid path key, returns null
	 *
	 * @return void
	 */
	public function test_path_returns_null_for_invalid_paths() {
		$app_config = new App_Config( self::SAMPLE_SETTINGS );
		$this->assertNull( $app_config->path( 'null' ) );
	}

	/**
	 * Ensure the url is returned with a trailing slash.
	 *
	 * @return void
	 */
	public function test_url_returns_url_with_trailing_slash(): void {
		$app_config = new App_Config( self::SAMPLE_SETTINGS );
		$this->assertStringContainsString( '/views/', $app_config->url( 'view' ) );
		$this->assertStringEndsWith( '/', $app_config->url( 'view' ) );
	}

	/**
	 * Test that an invalid url key, returns null
	 *
	 * @return void
	 */
	public function test_url_returns_null_for_invalid_urls() {
		$app_config = new App_Config( self::SAMPLE_SETTINGS );
		$this->assertNull( $app_config->url( 'null' ) );
	}

	/**
	 * Test returns rest namespace.
	 *
	 * @return void
	 */
	public function test_rest_returns_rest_namespace(): void {
		$app_config = new App_Config( self::SAMPLE_SETTINGS );
		$this->assertEquals( 'fake_rest', $app_config->rest() );
	}

	/**
	 * Test returns cache namespace.
	 *
	 * @return void
	 */
	public function test_cache_returns_cache_namespace(): void {
		$app_config = new App_Config( self::SAMPLE_SETTINGS );
		$this->assertEquals( 'pc_cache', $app_config->cache() );
	}

	/**
	 *                                 POST TYPES
	 */


	/** @testdox Attempting to get the post type key for a post type not defined, an error should be thrown. */
	public function test_exception_throw_for_unset_posttype(): void {
		$this->expectException( OutOfBoundsException::class );
		$app_config = new App_Config( self::SAMPLE_SETTINGS );
		$app_config->post_types( 'invalid' );
	}

	/** @testdox It should be possible to get post type slug from a valid key. */
	public function test_can_get_slug_if_filed_set(): void {
		$app_config = new App_Config( self::SAMPLE_SETTINGS );
		$this->assertEquals( 'my_slug', $app_config->post_types( 'my_cpt' ) );
	}

	/** @testdox When setting post types any key or value which isn't a valid string (string and not empty) will not be set. */
	public function test_filters_post_type_with_none_string_key_value_pairs(): void {
		$this->expectException( OutOfBoundsException::class );
		$app_config = new App_Config(
			array(
				'post_types' => array( 'inv_cpt' => false ),
			)
		);
		$app_config->post_types( 'inv_cpt' );
	}



	/**
	 *                                 TAXONMIES
	 */


	/** @testdox Attempting to get the taxonomuy slug/key for a taxonomy not defined, an error should be thrown. */
	public function test_exception_throw_for_unset_taxonomy_key(): void {
		$this->expectException( OutOfBoundsException::class );
		$app_config = new App_Config( self::SAMPLE_SETTINGS );
		$app_config->taxonomies( 'invalid' );
	}

	/** @testdox It should be possible to get taxonomy slug from a valid key. */
	public function test_can_get_taxonomy_slug(): void {
		$app_config = new App_Config( self::SAMPLE_SETTINGS );
		$this->assertEquals( 'my_slug', $app_config->taxonomies( 'tax' ) );
	}


	/**
	 *                                 META
	 */

	/** @testdox It should not be possible to define meta with an invlaid type. */
	public function test_exception_throw_for_setting_invalid_meta_type(): void {
		$this->expectException( OutOfBoundsException::class );
		$app_config = new App_Config(
			array(
				'meta' => array(
					'invalid' => array( 'slug' => 'my_cpt' ),
				),
			)
		);
	}

	/** @testdox When attempting to get meta data, and incorrect meta type is used, an error should be gnerated */
	public function test_exception_throw_for_calling_invalid_meta_type(): void {
		$this->expectException( OutOfBoundsException::class );
		$app_config = new App_Config( self::SAMPLE_SETTINGS );
		$app_config->meta( 'key', 'invalid_type' );
	}

	/** @testdox When attempting to get a meta key which hasn't been defined, an error should be generated. */
	public function test_exception_throw_for_unset_meta_key(): void {
		$this->expectException( OutOfBoundsException::class );
		$app_config = new App_Config( self::SAMPLE_SETTINGS );
		$app_config->meta( 'invalid_key', 'post' );
	}

	/** @testdox It should be possible to get a meta key value based on its own key and the type. */
	public function test_can_get_meta_with_type():void {
		$app_config = new App_Config( self::SAMPLE_SETTINGS );
		$this->assertEquals( 'One Post', $app_config->meta( 'post_meta_1', 'post' ) );
		$this->assertEquals( 'One User', $app_config->meta( 'user_meta_1', 'user' ) );
		$this->assertEquals( 'One Term', $app_config->meta( 'term_meta_1', 'term' ) );
	}

	/** @testdox It should be possible to get a post meta key, from its own key value. */
	public function test_can_get_post_meta(): void {
		$app_config = new App_Config( self::SAMPLE_SETTINGS );
		$this->assertEquals( 'One Post', $app_config->post_meta( 'post_meta_1' ) );
	}

	/** @testdox It should be possible to get a user meta key, from its own key value. */
	public function test_can_get_user_meta(): void {
		$app_config = new App_Config( self::SAMPLE_SETTINGS );
		$this->assertEquals( 'One User', $app_config->user_meta( 'user_meta_1' ) );
	}

	/** @testdox It should be possible to get a term meta key, from its own key value. */
	public function test_can_get_term_meta(): void {
		$app_config = new App_Config( self::SAMPLE_SETTINGS );
		$this->assertEquals( 'One Term', $app_config->term_meta( 'term_meta_1' ) );
	}

	/**
	 * @testdox Test can get a defined db name
	 */
	public function test_can_get_db_table_name(): void {
		$app_config = new App_Config( self::SAMPLE_SETTINGS );
		$this->assertEquals( 'db_table', $app_config->db_tables( 'db' ) );
	}

	/**
	 * @testdox Test throws eception calling unset DB table
	 *
	 * @return void
	 */
	public function test_throws_exception_for_unset_db_table(): void {
		$this->expectException( OutOfBoundsException::class );
		$app_config = new App_Config( self::SAMPLE_SETTINGS );
		$app_config->db_tables( 'failure' );
	}

	/** @testdox it should be possible to pass in a fallback value when getting additional settings. */
	public function test_uses_default_for_additional(): void {
		$app_config = new App_Config( self::SAMPLE_SETTINGS );
		$this->assertEquals( 'fallback', $app_config->additional( 'missing', 'fallback' ) );
	}

	/** @testdox it should be possible to pass in a fallback value when getting path settings. */
	public function test_uses_default_for_path(): void {
		$app_config = new App_Config( self::SAMPLE_SETTINGS );
		$this->assertEquals( 'fallback', $app_config->path( 'missing', 'fallback' ) );
	}

	/** @testdox it should be possible to pass in a fallback value when getting url settings. */
	public function test_uses_default_for_url(): void {
		$app_config = new App_Config( self::SAMPLE_SETTINGS );
		$this->assertEquals( 'fallback', $app_config->url( 'missing', 'fallback' ) );
	}

	/** @testdox it should be possible to pass in a fallback value when getting namespace settings. */
	public function test_uses_default_for_namespace(): void {
		$app_config = new App_Config( self::SAMPLE_SETTINGS );
		$this->assertEquals( 'fallback', $app_config->namespace( 'missing', 'fallback' ) );
	}

	/** @testdox The export method should export all config settings. */
	public function test_exports_all_settings(): void {
		$app_config = new App_Config( self::SAMPLE_SETTINGS );

		$this->assertEquals( self::SAMPLE_SETTINGS['additional'], $app_config->export_settings()['additional'] );
		$this->assertEquals( self::SAMPLE_SETTINGS['post_types'], $app_config->export_settings()['post_types'] );
		$this->assertEquals( self::SAMPLE_SETTINGS['taxonomies'], $app_config->export_settings()['taxonomies'] );
		$this->assertEquals( self::SAMPLE_SETTINGS['namespaces']['rest'], $app_config->export_settings()['namespaces']['rest'] );
		$this->assertEquals( self::SAMPLE_SETTINGS['db_tables'], $app_config->export_settings()['db_tables'] );
		$this->assertEquals( self::SAMPLE_SETTINGS['meta']['post'], $app_config->export_settings()['meta']['post'] );
		$this->assertEquals( self::SAMPLE_SETTINGS['meta']['user'], $app_config->export_settings()['meta']['user'] );
		$this->assertEquals( self::SAMPLE_SETTINGS['meta']['term'], $app_config->export_settings()['meta']['term'] );
	}
}
