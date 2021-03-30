<?php

declare(strict_types=1);
/**
 * Base config object.
 *
 * @author Glynn Quelch <glynn.quelch@gmail.com>
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 * @package PinkCrab\Core
 */

namespace PinkCrab\Core\Application;

use OutOfBoundsException;

final class App_Config {

	/**
	 * Holds the current sites paths & urls
	 *
	 * @var array<string, mixed>
	 */
	protected $paths = array();

	/**
	 * Holds all the namespaces (rest, cache etc).
	 *
	 * @var array<string, mixed>
	 */
	protected $namespaces = array();

	/**
	 * Holds all additiional array details.
	 *
	 * @var array<string, mixed>
	 */
	protected $plugin = array();

	/**
	 * Holds all additiional array details.
	 *
	 * @var array<string, mixed>
	 */
	protected $taxonomies = array();

	/**
	 * Holds the CPT slugs and meta keys.
	 *
	 * @var array<string, mixed>
	 */
	protected $post_types = array();

	/**
	 * Holds an array of table names.
	 *
	 * @var array<string, mixed>
	 */
	protected $db_tables = array();

	/**
	 * Holds all custom settings keys.
	 * Accessed using __get()
	 *
	 * @var array<string, mixed>
	 */
	protected $additional = array();

	/**
	 * @param array<string, mixed> $settings
	 */
	public function __construct( array $settings = array() ) {
		$settings = $this->set_defaults( $settings );
		$this->set_props( $settings );
	}

	/**
	 * Overlays the passed details to the predefined fallbacks.
	 *
	 * @param array<string, mixed> $settings
	 * @return array<string, mixed>
	 */
	private function set_defaults( array $settings ): array {
		return array_replace_recursive( $this->settings_defaults(), $settings );
	}

	/**
	 * Maps the supplied settings array to inner states.
	 *
	 * @param array<string, mixed> $paths
	 * @return void
	 */
	private function set_props( array $paths ): void {
		$this->paths['url']  = $paths['url'];
		$this->paths['path'] = $paths['path'];
		$this->namespaces    = $paths['namespaces'];
		$this->plugin        = $paths['plugin'];
		$this->additional    = $paths['additional'];
		$this->db_tables     = $paths['db_tables'];

		$this->set_post_types( $paths['post_types'] );
		$this->set_taxonomies( $paths['taxonomies'] );
	}

	/**
	 * Gets a path with trailing slash.
	 *
	 * @param string|null $path
	 * @return array<string, mixed>|string|null
	 */
	public function path( ?string $path = null ) {

		if ( is_null( $path ) ) {
			return $this->paths['path'];
		}

		return \array_key_exists( $path, $this->paths['path'] )
			? trailingslashit( $this->paths['path'][ $path ] )
			: null;
	}

	/**
	 * Gets a path with trailing slash.
	 *
	 * @param string|null $url
	 * @return array<string, mixed>|string|null
	 */
	public function url( ?string $url = null ) {

		if ( is_null( $url ) ) {
			return $this->paths['url'];
		}

		return \array_key_exists( $url, $this->paths['url'] )
			? trailingslashit( $this->paths['url'][ $url ] )
			: null;
	}

	/**
	 * Returns the based namespace for all routes.
	 *
	 * @return string
	 */
	public function rest(): string {
		return $this->namespaces['rest'];
	}

	/**
	 * Returns the cache namespace.
	 *
	 * @return string
	 */
	public function cache(): string {
		return $this->namespaces['cache'];
	}

	/**
	 * Return a namespace by its key.
	 *
	 * @param string $key
	 * @return string|null
	 */
	public function namespace( string $key ): ?string {
		return array_key_exists( $key, $this->namespaces )
			? $this->namespaces[ $key ] : null;
	}

	/**
	 * Return a additional by its key.
	 *
	 * @param string $key
	 * @return mixed
	 */
	public function additional( string $key ) {
		return array_key_exists( $key, $this->additional )
			? $this->additional[ $key ] : null;
	}

	/**
	 * Returns the current set plugin version
	 *
	 * @return string
	 */
	public function version(): string {
		return $this->plugin['version'];
	}

	/**
	 * Returns the key for a post type.
	 *
	 * @param string $key
	 * @return string|array<string, mixed>
	 * @throws OutOfBoundsException
	 */
	public function post_types( string $key, string $field = 'slug', ?string $meta_key = null ) {
		if ( ! array_key_exists( $key, $this->post_types ) ) {
			throw new OutOfBoundsException( 'Post Type doesnt exists' );
		}

		if ( $field === 'slug' ) {
			return $this->post_types[ $key ]['slug'];
		}

		if ( $meta_key && ! array_key_exists( $meta_key, $this->post_types[ $key ]['meta'] ) ) {
			throw new OutOfBoundsException( sprintf( 'Meta key doesnt exist for the %s post type in config', $key ) );
		}

		return $meta_key
			? $this->post_types[ $key ]['meta'][ $meta_key ]
			: $this->post_types[ $key ]['meta'];
	}

	/**
	 * Set the defined post types.
	 * Ensures all have valid slug and meta array.
	 *
	 * @param array<string, mixed> $post_types
	 * @return void
	 */
	protected function set_post_types( array $post_types ): void {
		foreach ( $post_types as $label => $post_type ) {
			// Check we have a slug.
			if ( empty( $post_type['slug'] ) ) {
				throw new OutOfBoundsException( 'Post Types must have a defined slug. ' . \wp_json_encode( $post_type, \JSON_PRETTY_PRINT ) );
			}
			// Check we have a meta array, even if empty.
			if ( ! array_key_exists( 'meta', $post_type ) || ! is_array( $post_type['meta'] ) ) {
				throw new OutOfBoundsException( 'Post Types must have a defined meta array, even if empty. ' . \wp_json_encode( $post_type, \JSON_PRETTY_PRINT ) );
			}

			$this->post_types[ $label ] = array(
				'slug' => $post_type['slug'],
				'meta' => $post_type['meta'],
			);
		}
	}

	/**
	 * Returns the key for a post type.
	 *
	 * @param string $key
	 * @return string|array<string, mixed>
	 * @throws OutOfBoundsException
	 */
	public function taxonomies( string $key, string $field = 'slug', ?string $term_key = null ) {
		if ( ! array_key_exists( $key, $this->taxonomies ) ) {
			throw new OutOfBoundsException( 'Taxonomy doesnt exists' );
		}

		if ( $field === 'slug' ) {
			return $this->taxonomies[ $key ]['slug'];
		}

		if ( $term_key && ! array_key_exists( $term_key, $this->taxonomies[ $key ]['term'] ) ) {
			throw new OutOfBoundsException( sprintf( 'Term key doesnt exist for the %s taxonomy in config', $key ) );
		}

		return $term_key
			? $this->taxonomies[ $key ]['term'][ $term_key ]
			: $this->taxonomies[ $key ]['term'];
	}

	/**
	 * Set the definedtaxonomies.
	 * Ensures all have valid slug and term array.
	 *
	 * @param array<string, mixed> $taxonomies
	 * @return void
	 */
	protected function set_taxonomies( array $taxonomies ): void {
		foreach ( $taxonomies as $label => $taxonomy ) {
			// Check we have a slug.
			if ( empty( $taxonomy['slug'] ) ) {
				throw new OutOfBoundsException( 'Taxonomies must have a defined slug. ' . \wp_json_encode( $taxonomy, \JSON_PRETTY_PRINT ) );
			}
			// Check we have a meta array, even if empty.
			if ( ! array_key_exists( 'term', $taxonomy ) || ! is_array( $taxonomy['term'] ) ) {
				throw new OutOfBoundsException( 'Taxonomies must have a defined term array, even if empty. ' . \wp_json_encode( $taxonomy, \JSON_PRETTY_PRINT ) );
			}

			$this->taxonomies[ $label ] = array(
				'slug' => $taxonomy['slug'],
				'term' => $taxonomy['term'],
			);
		}
	}

	/**
	 * Returns a table name based on its key.
	 *
	 * @param string $name
	 * @return string
	 * @throws OutOfBoundsException
	 */
	public function db_tables( string $name ): string {
		if ( ! array_key_exists( $name, $this->db_tables ) ) {
			throw new OutOfBoundsException( 'Table doesnt exist' );
		}
		return $this->db_tables[ $name ];
	}

	/**
	 * Magic getter for values in additional
	 *
	 * @param string $name
	 * @return mixed
	 */
	public function __get( $name ) {
		return $this->additional( $name );
	}

	/**
	 * Returns a base settings array, to ensure all required values are defined.
	 *
	 * @return array<string, mixed>
	 */
	private function settings_defaults(): array {
		$base_path  = \dirname( __DIR__, 2 );
		$plugin_dir = \basename( $base_path );
		$wp_uploads = \wp_upload_dir();

		return array(
			'plugin'     => array(
				'version' => '0.1.0',
			),
			'path'       => array(
				'plugin'         => $base_path,
				'view'           => $base_path . '/views',
				'assets'         => $base_path . '/assets',
				'upload_root'    => $wp_uploads['basedir'],
				'upload_current' => $wp_uploads['path'],
			),
			'url'        => array(
				'plugin'         => plugins_url( $plugin_dir ),
				'view'           => plugins_url( $plugin_dir ) . '/views',
				'assets'         => plugins_url( $plugin_dir ) . '/assets',
				'upload_root'    => $wp_uploads['baseurl'],
				'upload_current' => $wp_uploads['url'],
			),
			'post_types' => array(),
			'taxonomies' => array(),
			'db_tables'  => array(),
			'namespaces' => array(
				'rest'  => 'pinkcrab',
				'cache' => 'pc_cache',
			),
			'additional' => array(),

		);
	}
}
