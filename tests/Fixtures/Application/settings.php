<?php

/**
 * Stub file for testing App settings.
 */

return array(
	'path'       => array(
		'plugin' => FIXTURES_PATH,
		'view'   => FIXTURES_PATH . '/views',
	),
	'url'        => array(
		'plugin' => plugins_url( basename( FIXTURES_PATH ) ),
		'view'   => plugins_url( basename( FIXTURES_PATH ) ) . '/views',
	),
	'additional' => array( 'test_key' => 'test_value' ),
	'meta'       => array(
		'post' => array( 'post_meta_1' => 'One Post' ),
		'user' => array( 'user_meta_1' => 'One User' ),
		'term' => array( 'term_meta_1' => 'One Term' ),
	),
	'post_types' => array( 'cpt' => 'post_type' ),
	'taxonomies' => array( 'tax' => 'taxonomy' ),
	'db_tables'  => array( 'table' => 'db_table' ),
);
