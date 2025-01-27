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

namespace PinkCrab\Perique\Tests\Unit\Application;

use WP_UnitTestCase;
use OutOfBoundsException;
use PinkCrab\Perique\Application\App_Config;
/**
 * @group unit
 * @group app
 * @group app_config
 */
class Test_App_Config extends WP_UnitTestCase {



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
		$this->assertInstanceOf( App_Config::class, new App_Config() );
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
		$no_override   = new App_Config();
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
		// Exception message should contain "invalid is not a defined post type"
		$this->expectExceptionMessage( 'App Config :: "invalid" is not a defined post type' );
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
		$this->expectExceptionMessage( 'App Config :: "inv_cpt" is not a defined post type' );
		$app_config = new App_Config(
			array(
				'post_types' => array( 'inv_cpt' => array( 'a', 'b' ) ),
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
		$this->expectExceptionMessage( 'App Config :: "invalid" is not a defined taxonomy' );
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

	/** @testdox Attempting to set a meta type which is not valid, should result in an OutOfBounds exception being thrown */
	public function test_exception_throw_for_setting_invalid_meta_type_key(): void {
		$this->expectException( OutOfBoundsException::class );
		$this->expectExceptionMessage( 'App Config :: "invalid" is not a valid meta type and cant be defined' );
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
		$this->expectExceptionMessage( 'App Config :: "invalid_type" is not a valid meta type and cant be fetched' );
		$app_config = new App_Config( self::SAMPLE_SETTINGS );
		$app_config->meta( 'key', 'invalid_type' );
	}

	/** @testdox When attempting to get a meta key which hasn't been defined, an error should be generated. */
	public function test_exception_throw_for_unset_meta_key(): void {
		$this->expectException( OutOfBoundsException::class );
		$this->expectExceptionMessage( 'App Config :: "invalid_key" is not a defined postmeta key' );

		$app_config = new App_Config( self::SAMPLE_SETTINGS );
		$app_config->meta( 'invalid_key', 'post' );
	}

	/** @testdox It should be possible to get a meta key value based on its own key and the type. */
	public function test_can_get_meta_with_type():void {
		$app_config = new App_Config( self::SAMPLE_SETTINGS );
		$this->assertEquals( 'One Post', $app_config->meta( 'post_meta_1', 'post' ) );
		$this->assertEquals( 'One User', $app_config->meta( 'user_meta_1', 'user' ) );
		$this->assertEquals( 'One Term', $app_config->meta( 'term_meta_1', 'term' ) );

		// Assumes not passing a type will default to post.
		$this->assertEquals( 'One Post', $app_config->meta( 'post_meta_1' ) );
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
	 * @testdox Test throws exception calling unset DB table
	 *
	 * @return void
	 */
	public function test_throws_exception_for_unset_db_table(): void {
		$this->expectException( OutOfBoundsException::class );
		$this->expectExceptionMessage( 'App Config :: "failure" is not a defined DB table' );
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

	/** @testdox If no settings are defined, defaults will be assumed treating the base as 2 levels above the location of this file. */
	public function test_app_config_defaults(): void {
		// Base paths
		$base_path  = dirname( ABSPATH );
		$plugin_url = \plugins_url( \basename( $base_path ) );
		$wp_uploads = \wp_upload_dir();

		$app_config = new App_Config();
		$defaults   = $app_config->export_settings();

		// Check paths.
		$this->assertEquals( $base_path, $defaults['path']['plugin'] );
		$this->assertEquals( $base_path . '/views', $defaults['path']['view'] );
		$this->assertEquals( $base_path . '/assets', $defaults['path']['assets'] );
		$this->assertEquals( $wp_uploads['basedir'], $defaults['path']['upload_root'] );
		$this->assertEquals( $wp_uploads['path'], $defaults['path']['upload_current'] );

		// Check URLs.
		$this->assertEquals( $plugin_url, $defaults['url']['plugin'] );
		$this->assertEquals( $plugin_url . '/views', $defaults['url']['view'] );
		$this->assertEquals( $plugin_url . '/assets', $defaults['url']['assets'] );
		$this->assertEquals( $wp_uploads['baseurl'], $defaults['url']['upload_root'] );
		$this->assertEquals( $wp_uploads['url'], $defaults['url']['upload_current'] );

		// Namespaces
		$this->assertEquals( 'pinkcrab', $defaults['namespaces']['rest'] );
		$this->assertEquals( 'pc_cache', $defaults['namespaces']['cache'] );
		// $this->assertEquals( 'pc_cache', $config->cache() );

		// Version
		$this->assertEquals( '0.1.0', $defaults['plugin']['version'] );

		// Empty indexes
		$this->assertEquals( array(), $defaults['additional'] );
		$this->assertEquals( array(), $defaults['post_types'] );
		$this->assertEquals( array(), $defaults['taxonomies'] );
		$this->assertEquals( array(), $defaults['db_tables'] );

		// Meta should have 3 indexes
		$this->assertArrayHasKey( 'post', $defaults['meta'] );
		$this->assertArrayHasKey( 'user', $defaults['meta'] );
		$this->assertArrayHasKey( 'term', $defaults['meta'] );

		// Meta should have 3 empty indexes (also checks for the class Constants.)
		$this->assertEquals( array(), $defaults['meta'][ App_Config::POST_META ] );
		$this->assertEquals( array(), $defaults['meta'][ App_Config::TERM_META ] );
		$this->assertEquals( array(), $defaults['meta'][ App_Config::USER_META ] );
	}

	/** @testdox It should be possible to set meta using a valid keys in the array used */
	public function test_set_meta(): void {
		$app_config = new App_Config();
		$app_config->set_meta( array( 'post' => array( 'test' => 'test' ) ) );
		$this->assertEquals( 'test', $app_config->post_meta( 'test' ) );
	}

	/** @testdox Attempting to set meta with an invalid meta type should result in an OutOfBoundException being thrown */
	public function test_set_meta_throws_exception(): void {
		$this->expectException( \OutOfBoundsException::class );
		$app_config = new App_Config();
		$app_config->set_meta( array( 'invalid' => array( 'test' => 'test' ) ) );
	}

	/**
	 * @testdox When settings either db_table, post_types, namespaces or taxonomies, any key of value that is either not a string or empty, will not see them set.
	 * @dataProvider setting_with_invalid_key_or_values
	 */

	public function test_setting_with_invalid_key_or_values( string $setting, array $values ): void {
		$app_config = new App_Config( array( $setting => $values ) );

		// If namespace, the 'rest' and 'cache' should still be set.
		if ( 'namespaces' === $setting ) {
			$this->assertCount( 2, $app_config->export_settings()[ $setting ] );
		} else {
			$this->assertCount( 0, $app_config->export_settings()[ $setting ] );
		}

	}

	/** Data provider for test_setting_with_invalid_key_or_values */
	public function setting_with_invalid_key_or_values(): array {
		return array(
			array( 'db_tables', array( 'test' => '' ) ),
			array( 'post_types', array( 'test' => '' ) ),
			array( 'namespaces', array( '' => 'test' ) ),
			array( 'taxonomies', array( 'test2' ) ),
		);
	}

	/** @testdox It should be possible to access the wpdb prefix */
	public function test_app_config_wpdb_prefix(): void {
		global $wpdb;
		$app_config = new App_Config();
		$this->assertEquals( $wpdb->prefix, $app_config->wpdb_prefix() );

		// With defined prefix.
		$app_config = new App_Config( array( 'plugin' => array( 'wpdb_prefix' => 'test_' ) ) );
		$this->assertEquals( 'test_', $app_config->wpdb_prefix() );
	}
}
