<?php

declare(strict_types=1);
/**
 * Base config object.
 *
 * @since 0.2.0
 * @author Glynn Quelch <glynn.quelch@gmail.com>
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 * @package PinkCrab\Core
 */

namespace PinkCrab\Core\Tests\Application;

use WP_UnitTestCase;
use OutOfBoundsException;
use PinkCrab\Core\Application\App;
use PinkCrab\Core\Application\Config;
use PinkCrab\Core\Application\App_Config;

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
		'post_types' => array(
			'my_cpt' => array(
				// Allows expressions for values.
				'slug' => 'my_slug',
				'meta' => array(
					'meta_1' => 'value_1',
				),
			),
		),
		'taxonomies' => array(
			'tax' => array(
				// Allows expressions for values.
				'slug' => 'my_slug',
				'term' => array(
					'term_1' => 'value_1',
				),
			),
		),
		'db_tables'  => array(
			'db' => 'db_table',
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

	/**
	 * Check excception thrown with unset key.
	 *
	 * @return void
	 */
	public function test_exception_throw_for_unset_posttype_key(): void {
		$this->expectException( OutOfBoundsException::class );
		$app_config = new App_Config( self::SAMPLE_SETTINGS );
		$app_config->post_types( 'invalid' );
	}

	/**
	 * Check excception thrown with unset meta_key.
	 *
	 * @return void
	 */
	public function test_exception_throw_for_unset_posttype_meta_key(): void {
		$this->expectException( OutOfBoundsException::class );
		$app_config = new App_Config( self::SAMPLE_SETTINGS );
		$app_config->post_types( 'my_cpt', 'meta', 'invalid' );
	}

	/**
	 * Test reutns slug.
	 *
	 * @return void
	 */
	public function test_can_get_slug_if_filed_set(): void {
		$app_config = new App_Config( self::SAMPLE_SETTINGS );
		$this->assertEquals( 'my_slug', $app_config->post_types( 'my_cpt', 'slug' ) );
	}

	/**
	 * Test you can return all or a single meta key.
	 *
	 * @return void
	 */
	public function test_returns_meta_values(): void {
		$app_config = new App_Config( self::SAMPLE_SETTINGS );
		$this->assertIsArray( $app_config->post_types( 'my_cpt', 'meta' ) );
		$this->assertEquals( 'value_1', $app_config->post_types( 'my_cpt', 'meta', 'meta_1' ) );
	}

	/**
	 * Test throws exception for missing cpt slug.
	 *
	 * @return void
	 */
	public function test_throws_exception_when_postype_without_slug_set(): void {
		$this->expectException( OutOfBoundsException::class );
		$app_config = new App_Config(
			array(
				'post_types' => array(
					'cpt' => array(
						'_slug' => 'failue',
					),
				),
			)
		);
	}

	/**
	 * Test throws exception for missing cpt meta.
	 *
	 * @return void
	 */
	public function test_throws_exception_when_postype_without_meta_set(): void {
		$this->expectException( OutOfBoundsException::class );
		$app_config = new App_Config(
			array(
				'post_types' => array(
					'cpt' => array(
						'slug'  => 'my_cpt',
						'_meta' => 'failue',

					),
				),
			)
		);
	}

	/**
	 *                                 TAXONMIES
	 */

	/**
	 * Check excception thrown with unset key.
	 *
	 * @return void
	 */
	public function test_exception_throw_for_unset_taxonomy_key(): void {
		$this->expectException( OutOfBoundsException::class );
		$app_config = new App_Config( self::SAMPLE_SETTINGS );
		$app_config->taxonomies( 'invalid' );
	}

	/**
	 * Check excception thrown with unset term_key.
	 *
	 * @return void
	 */
	public function test_exception_throw_for_unset_taxonomy_term_key(): void {
		$this->expectException( OutOfBoundsException::class );
		$app_config = new App_Config( self::SAMPLE_SETTINGS );
		$app_config->taxonomies( 'tax', 'term', 'invalid' );
	}

	/**
	 * Test reutns slug.
	 *
	 * @return void
	 */
	public function test_can_get_slug_if_filed_taxonomy_set(): void {
		$app_config = new App_Config( self::SAMPLE_SETTINGS );
		$this->assertEquals( 'my_slug', $app_config->taxonomies( 'tax', 'slug' ) );
	}

	/**
	 * Test you can return all or a single meta key.
	 *
	 * @return void
	 */
	public function test_returns_taxonomy_term_values(): void {
		$app_config = new App_Config( self::SAMPLE_SETTINGS );
		$this->assertIsArray( $app_config->taxonomies( 'tax', 'term' ) );
		$this->assertEquals( 'value_1', $app_config->taxonomies( 'tax', 'term', 'term_1' ) );
	}

	/**
	 * Test throws exception for missing cpt slug.
	 *
	 * @return void
	 */
	public function test_throws_exception_when_taxonomy_without_slug_set(): void {
		$this->expectException( OutOfBoundsException::class );
		$app_config = new App_Config(
			array(
				'taxonomies' => array(
					'cpt' => array(
						'_slug' => 'failue',
					),
				),
			)
		);
	}

	/**
	 * Test throws exception for missing cpt meta.
	 *
	 * @return void
	 */
	public function test_throws_exception_when_taxonomy_without_meta_set(): void {
		$this->expectException( OutOfBoundsException::class );
		$app_config = new App_Config(
			array(
				'taxonomies' => array(
					'cpt' => array(
						'slug'  => 'my_cpt',
						'_meta' => 'failue',
					),
				),
			)
		);
	}

	/**
	 * Test can get a defined db name
	 */
	public function test_can_get_db_table_name(): void {
		$app_config = new App_Config( self::SAMPLE_SETTINGS );
		$this->assertEquals( 'db_table', $app_config->db_tables( 'db' ) );
	}

	/**
	 * Test throws eception calling unset DB table
	 *
	 * @return void
	 */
	public function test_throws_exception_for_unset_db_table(): void {
		$this->expectException( OutOfBoundsException::class );
		$app_config = new App_Config( self::SAMPLE_SETTINGS );
		$app_config->db_tables( 'failure' );
	}
}
