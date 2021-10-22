<?php

declare(strict_types=1);
/**
 * Base config object.
 *
 * @author Glynn Quelch <glynn.quelch@gmail.com>
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 * @package PinkCrab\Perique
 */

namespace PinkCrab\Perique\Application;

use OutOfBoundsException;

final class App_Config {

	/**@ var string */
	public const POST_META = 'post';

	/**@ var string */
	public const TERM_META = 'term';

	/**@ var string */
	public const USER_META = 'user';

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
	 * Holds all plugin details.
	 *
	 * @var array<string, mixed>
	 */
	protected $plugin = array();

	/**
	 * Holds all taxonomy terms.
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
	 * Holds all the meta keys
	 *
	 * @var array{post:array<string,string>,user:array<string,string>,term:array<string,string>}
	 */
	protected $meta = array(
		self::POST_META => array(),
		self::USER_META => array(),
		self::TERM_META => array(),
	);

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
	 * @param array<string, mixed> $settings
	 * @return void
	 */
	private function set_props( array $settings ): void {
		$this->paths['url']  = $settings['url'];
		$this->paths['path'] = $settings['path'];
		$this->namespaces    = $this->filter_key_value_pair( $settings['namespaces'] );
		$this->plugin        = $settings['plugin'];
		$this->additional    = $settings['additional'];
		$this->db_tables     = $this->filter_key_value_pair( $settings['db_tables'] );
		$this->post_types    = $this->filter_key_value_pair( $settings['post_types'] );
		$this->taxonomies    = $this->filter_key_value_pair( $settings['taxonomies'] );

		$this->set_meta( $settings['meta'] );
	}

	/**
	 * Gets a path with trailing slash.
	 *
	 * @param string|null $path
	 * @param string|null $default The default value to return if not set.
	 * @return array<string, mixed>|string|null
	 */
	public function path( ?string $path = null, ?string $default = null ) {

		if ( is_null( $path ) ) {
			return $this->paths['path'];
		}

		return \array_key_exists( $path, $this->paths['path'] )
			? trailingslashit( $this->paths['path'][ $path ] )
			: $default;
	}

	/**
	 * Gets a path with trailing slash.
	 *
	 * @param string|null $url
	 * @param string|null $default The default value to return if not set.
	 * @return array<string, mixed>|string|null
	 */
	public function url( ?string $url = null, ?string $default = null ) {

		if ( is_null( $url ) ) {
			return $this->paths['url'];
		}

		return \array_key_exists( $url, $this->paths['url'] )
			? trailingslashit( $this->paths['url'][ $url ] )
			: $default;
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
	 * @param string|null $default The default value to return if not set.
	 * @return string|null
	 */
	public function namespace( string $key, ?string $default = null ): ?string {
		return array_key_exists( $key, $this->namespaces )
			? $this->namespaces[ $key ] : $default;
	}

	/**
	 * Return a additional by its key.
	 *
	 * @param string $key
	 * @param string|null $default The default value to return if not set.
	 * @return mixed
	 */
	public function additional( string $key, ?string $default = null ) {
		return array_key_exists( $key, $this->additional )
			? $this->additional[ $key ] : $default;
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
	 * @return string
	 * @throws OutOfBoundsException
	 */
	public function post_types( string $key ) {
		if ( ! array_key_exists( $key, $this->post_types ) ) {
			throw new OutOfBoundsException( 'Post Type not defined.' );
		}

		return $this->post_types[ $key ];
	}

	/**
	 * Returns a valid meta key value, for a defined meta type.
	 *
	 * @param string $key
	 * @param string $type defaults to post
	 * @return string
	 * @throws OutOfBoundsException
	 */
	public function meta( string $key, string $type = self::POST_META ): string {
		// Check meta type.
		if ( ! array_key_exists( $type, $this->meta ) ) {
			throw new OutOfBoundsException( 'Meta Type doesnt exists' );
		}
		// Check key.
		if ( ! array_key_exists( $key, $this->meta[ $type ] ) ) {
			throw new OutOfBoundsException( $type . ' meta key doesnt exists' );
		}

		return $this->meta[ $type ][ $key ];
	}

	/**
	 * Returns the post meta key value
	 * Alias for meta() with type as POST_META
	 *
	 * @param string $key
	 * @return string
	 */
	public function post_meta( string $key ): string {
		return $this->meta( $key, self::POST_META );
	}

	/**
	 * Returns the user meta key value
	 * Alias for meta() with type as USER_META
	 *
	 * @param string $key
	 * @return string
	 */
	public function user_meta( string $key ): string {
		return $this->meta( $key, self::USER_META );
	}

	/**
	 * Returns the tern meta key value
	 * Alias for meta() with type as TERM_META
	 *
	 * @param string $key
	 * @return string
	 */
	public function term_meta( string $key ): string {
		return $this->meta( $key, self::TERM_META );
	}

	/**
	 * Sets the meta data
	 *
	 * @param array<string, array<string,string>> $meta
	 * @return void
	 */
	public function set_meta( array $meta ): void {
		$valid_meta_types = array( self::POST_META, self::USER_META, self::TERM_META );
		foreach ( $meta as $meta_type => $pairs ) {
			if ( ! in_array( $meta_type, $valid_meta_types, true ) ) {
				throw new OutOfBoundsException( 'Valid meta type must be used as key.' );
			}

			// Set all pairs which have both valid key and values.
			$this->meta[ $meta_type ] = $this->filter_key_value_pair( $pairs );
		}
	}

	/**
	 * Returns the key for a taxonomy.
	 *
	 * @param string $key
	 * @return string
	 * @throws OutOfBoundsException
	 */
	public function taxonomies( string $key ): string {
		if ( ! array_key_exists( $key, $this->taxonomies ) ) {
			throw new OutOfBoundsException( 'Taxonomy not defined.' );
		}

		return $this->taxonomies[ $key ];
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
			'meta'       => array(
				self::POST_META => array(),
				self::USER_META => array(),
				self::TERM_META => array(),
			),
			'db_tables'  => array(),
			'namespaces' => array(
				'rest'  => 'pinkcrab',
				'cache' => 'pc_cache',
			),
			'additional' => array(),
		);
	}

	/**
	 * Filters an array to ensure key and value are both valid strings.
	 *
	 * @param array<int|string, mixed> $pairs
	 * @return array<string, string>
	 */
	private function filter_key_value_pair( array $pairs ): array {
		/** @var array<string, string> (as per filter function)*/
		return array_filter(
			$pairs,
			function ( $value, $key ): bool {
				return is_string( $value )
				&& \mb_strlen( $value ) > 0
				&& is_string( $key );
			},
			ARRAY_FILTER_USE_BOTH
		);
	}

	/**
	 * Exports the internal settings array.
	 *
	 * @return array<string, string|int|array<mixed>>
	 */
	public function export_settings(): array {
		return array(
			'path'       => $this->paths['path'],
			'url'        => $this->paths['url'],
			'namespaces' => $this->namespaces,
			'plugin'     => $this->plugin,
			'additional' => $this->additional,
			'db_tables'  => $this->db_tables,
			'post_types' => $this->post_types,
			'taxonomies' => $this->taxonomies,
			'meta'       => $this->meta,
		);
	}
}
